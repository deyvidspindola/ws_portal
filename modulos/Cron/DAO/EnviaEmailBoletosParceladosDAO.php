<?php

/**
 * @author 	Leandro Alves Ivanaga
 * @email   leandroivanaga@brq.com
 * @version 19/09/2013
 * @since   19/09/2013
 * 
**/


require_once _SITEDIR_.'boleto_funcoes.php';
use module\BoletoRegistrado\BoletoRegistradoModel;


class EnviaEmailBoletosParceladosDAO {

    private $conn;
    
    public function __construct() {
    
    	global $conn;
    
    	$this->conn = $conn;
    }
    
    /**
     * Busca os titulos na seguintes condições:
     * Devem ser boleto_seco que foram enviados aos clientes
     * E que estão pagos
     * E que foi selecionado mais de uma parcela
     * E que as suas devidas parcelas ainda não foram geradas
     */
    public function boletosSecosPagos() {
        
    	$retorno_boletos = array();
    	
    	$sql = "
			SELECT 
				tr.titoid, tr.titclioid, t.titvl_titulo, tce.tceconoid, pp.ppagadesao_parcela, t.titnfloid, t.titdt_pagamento
			FROM 
				titulo_controle_envio as tce
			JOIN 
    			titulo_retencao tr on tr.titoid = tcetitoid
			JOIN 
    			proposta AS p on p.prptermo = tce.tceconoid 
			JOIN 
    			proposta_pagamento AS pp on pp.ppagprpoid = p.prpoid
    		JOIN 
    			titulo AS t ON t.titoid = tr.titoid
			WHERE 
				tce.tcetipo = 'boleto_seco' 
			AND tce.tcestatus_envio = 't' 
			AND tce.tcedata_envio IS NOT NULL
			AND t.titvl_pagamento >= (t.titvl_titulo-t.titvl_desconto)
			AND (SELECT count(*) FROM titulo_controle_envio WHERE tcetipo = 'titulos_oficiais' AND tceconoid = tce.tceconoid) = 0
			AND pp.ppagadesao_parcela > 1
    	";
        		
		$resul_boletos = pg_query($this->conn,$sql);
        
		$num_rows = pg_num_rows($resul_boletos);
		
		// Se encontrou registros
		if ($num_rows){
			while ($dados = pg_fetch_object($resul_boletos)){
				$retorno_boletos[] = $dados;
			}
		}

        return $retorno_boletos;
    }
    
    
    /**
     * Função responsável por armazenar os titulos oficiais referente ao contrato
     */
    public function salvarParcelas($parcelamento){
    	try{
    		pg_query($this->conn, "BEGIN;");
    		
    		// Loop em todos os titulo do contrato
    		foreach ($parcelamento AS $parcela){
	    		$sql_insert = "
				    INSERT INTO
						titulo
	    			(
	    				titdt_inclusao,
	    				titdt_referencia,
	    				titnfloid,
	    				titdt_vencimento,
	    				titvl_titulo,
	    				titformacobranca,
	    				titclioid,
	    				titno_parcela
	    			)
	    			VALUES (
	    				NOW(),
	    				NOW(),
	    				{$parcela['titnfloid']},
			    		'{$parcela['dt_vencimento']}',
			    		{$parcela['valor_titulo']},
			    		{$parcela['forma_cobranca']},
			    		{$parcela['clioid']},
			    		{$parcela['no_parcela']}
	    			)
	    			returning titoid
	    			;
				";

			    if (!$res_insert = pg_query($this->conn,$sql_insert)){
			    	throw new Exception ("Problema ao gerar o parcelamento dos titulos.");
			    }
			   	
				$parcela['titoid'] = pg_fetch_result($res_insert, 0, "titoid");
				
				$boletoObj = new BoletoRegistradoModel();
				$boletoObj->setTituloId($parcela['titoid']);
				$boletoObj->setCodigoOrigem(BoletoRegistradoModel::CODIGO_ORIGEM_CRON_PARCELAS_SIGGO);
				$boletoObj->setDataVencimento($parcela['dt_vencimento']);
				$boletoObj->setValorFace($parcela['valor_titulo']);
				$boletoObj->setValorDescontoNegociadoDescritivo(0);
				$boletoObj->setValorAbatimentoDescritivo(0);
				$boletoObj->setValorMoraNegociadaDescritivo(0);
				$boletoObj->setValorOutrosAcrescimosDescritivo(0);
				$boletoObj->setValorNominal($parcela['valor_titulo']);

				try {
					$boletoObj->registrarBoletoOnline();
				}catch(\Exception $e){
					die($e->getMessage());
					throw new Exception($e->getMessage());
				}
			    
			    /**
			     *  Gera o NossoNumeroHSBC de acordo com os dados do titulo:
			     *  - Código do titulo
			     *  - Data de vencimento
			     *  - Código do cedente
			     */
			    
			    $data_venc = $parcela['dt_vencimento'];
			    
			    $data_venc = explode("-", $data_venc);
			    $data_venc = array_reverse($data_venc);
			    $data_venc = implode("-", $data_venc);
			    
			    $codigo_cedente= 3471241;
			    $nossonum_com_DV = montaNossoNumeroHSBC($parcela['titoid'], $parcela['dt_vencimento'], $codigo_cedente);
			    
			    // Atualiza o titulo com o seu respectivo NossoNumeroHSCB gerado
			    $sqlTituloNossoNumero = "
				    UPDATE
				    	titulo
				    SET
				    	titnumero_registro_banco = $nossonum_com_DV
				    WHERE titoid = {$parcela['titoid']}
			    ";
			    	
			    if (!$resTituloNossoNumero = pg_query($this->conn, $sqlTituloNossoNumero)) {
			   		throw new Exception ("Problema ao gerar o parcelamento dos titulos.");
				}
			    
				// Insere na tabela titulo_controle_envio para em seguida ser gerado o arquivo e enviado ao cliente
			    $sqlControleEnvio = "
				    INSERT INTO 
				    	titulo_controle_envio
				    (
					    tcetitoid,
					    tceconoid,
					    tcetipo,
					    tcestatus_envio,
					    tcedata_criacao
				    )
				    VALUES (
				    	{$parcela['titoid']},
					    {$parcela['contrato']},
					    'titulos_oficiais',
					    'false',
					    NOW()
			    	)
				";;
			    
			    if (!pg_query($this->conn, $sqlControleEnvio)){
			   		throw new Exception ("Problema ao gerar o parcelamento dos titulos.");
			    }
    		}
    		
    		// Se não houve erro todos os titulos referente ao contrato foram inseridos corretamente
    		pg_query($this->conn, 'COMMIT;');
    		return true;
    	}catch (Execption $e){
    		$msg = $e->getMessage();

    		$retorno = array(
    				'erro'	=>	1,
    				'msg'	=> $msg
    				);
    		
    		pg_query($this->conn, 'ROLLBACK');
    		return $retorno;
    	}
    }
    
    /**
     * Busca os titulos_oficiais que ainda não foram enviados aos clientes
     */
    public function boletosOficiais() {
    
    	$retorno_boletos = array();
    	 
    	$sql = "
			SELECT 
    			tce.tcetitoid, tce.tceconoid, t.titclioid, p.prpoid, c.clinome, t.titvl_titulo
    		FROM 
    			titulo_controle_envio AS tce
    		JOIN
    			titulo AS t ON t.titoid = tce.tcetitoid
    		JOIN 
    			proposta AS p ON p.prptermo = tce.tceconoid
				JOIN
					clientes AS c ON t.titclioid = c.clioid
			WHERE 
    			tce.tcetipo = 'titulos_oficiais'
			AND tce.tcedata_envio IS NULL

    		";
			    
    	$resul_boletos = pg_query($this->conn,$sql);
    
    	$num_rows = pg_num_rows($resul_boletos);
    
    	// Se encontrou registros
    	if ($num_rows){
    		while ($dados = pg_fetch_object($resul_boletos)){
    			$retorno_boletos[] = $dados;
    		}
    	}
    
    	return $retorno_boletos;
    }
}