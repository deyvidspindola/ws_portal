<?php

/**
 * FinGeraNfBoletoGraficaDAO.php
 * 
 * Classe de persistência dos dados
 * 
 * @author	Alex Sandro Médice <alex.medice@meta.com.br>
 * @since 07/11/2012
 * @package Financas
 */
class FinGeraNfBoletoGraficaDAO {

    private $conn;
    public $log = array();

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function transactionBegin() {
        pg_query($this->conn, "BEGIN;");
    }

    public function transactionCommit() {
        pg_query($this->conn, "COMMIT");
    }

    public function transactionRollback() {
        pg_query($this->conn, "ROLLBACK;");
    }
    
    /**
     * Recupera as informações da tabela execucao_faturamento verificando se tem processo rodando
     * 
     * @return array
     */
    public function recuperarParametros($finalizado){
    	
    	if(!$finalizado) {
    		$filtro .= " AND eardt_termino IS NULL";
    		$filtro .= " AND earstatus = true ";
    		$filtro .= " AND eartipo_processo IN(13,14,23) ";
    	}else{
    		$filtro .= " AND eartipo_processo IN(13,14,23) ";
    		$filtro .= " AND earstatus = false";
    		$filtro .= " ORDER BY eardt_termino DESC";
    	}
    	
    	$sql = "SELECT
			    	nm_usuario,
			    	usuemail,
			    	earoid serial,
			    	earusuoid,
			    	TO_CHAR(eardt_inicio, 'HH24:MI:SS') as inicio,
			    	TO_CHAR(eardt_termino, 'HH24:MI:SS') as termino,
			    	TO_CHAR(eardt_inicio, 'DD/MM/YYYY HH24:MI:SS') as data_inicio,
			    	TO_CHAR(eardt_termino, 'DD/MM/YYYY HH24:MI:SS') as data_termino,
			    	eartipo_processo,
			    	eardesc_status,
			    	earparametros,
			    	earnomearquivo
			    FROM
			    	execucao_arquivo
			    INNER JOIN usuarios on cd_usuario = earusuoid
			    	$filtro
			    LIMIT 1";

    	if(!$res = pg_query($this->conn, $sql)) {
    		throw new Exception('Falha ao recuperar parâmetros'." ".$sql);
    	}else{
    		$this->MensagemLog("Recuperou parametros");
    	}	
    	return $res;
    }
    
    /**
     * Função para iniciar o controle de geracao de nota e boleto para gráfica
     * @param  $tipo
     */
    public function prepararGeracaoNotaBoleto($usuoid,$tipo,$params="",$nomeArquivo=""){
    	
    	
    	$sql = "INSERT INTO execucao_arquivo(earusuoid, eartipo_processo,earstatus, earparametros,earnomearquivo) 
    	VALUES ($usuoid, $tipo,true,'".$params."','".$nomeArquivo."')";

    	if (!$res = pg_query($this->conn, $sql)) {
    		throw new Exception('Falha ao Gerar Nota e Boleto para Gráfica.');
    	}else{
    		$this->MensagemLog("Preparou Geração Nota e Boleto para Gráfica");
    	}
    	
    
    }
    
    /**
     * Finaliza o processo de geração nota para grafica
     * 
     * @var $resultado
     */
    public function finalizarProcesso($resultado,$tipo){

   $sql = "UPDATE execucao_arquivo
    	SET  eardt_termino=NOW(), earstatus=false, eardesc_status='$resultado'
    	 WHERE eardt_termino is null AND eartipo_processo=$tipo;";

    	$rs = pg_query($this->conn, $sql);
    	
    	if (!$rs) {
    		throw new exception("Falha ao finalizar o processamento concorrente. Contate o administrador de sistemas.",1);
    		$this->MensagemLog("Falha ao finalizar o processamento concorrente. Contate o administrador de sistemas");
    	}
    	
    	
    	$this->MensagemLog("Finaliza o processo de geração nota para grafica");
    
    }
    
    /**
     * Retorna o caminho aonde estão os arquivos zip , csv da grafica
     * @throws Exception
     * @return array
     */
    public function getCaminhoServidor(){
    	
    	$sql = "SELECT
    				valvalor
		    	FROM
		    		valor
    			WHERE 
    				valregoid = 19";
    	
    	if(!$res = pg_query($this->conn, $sql)) {
    	throw new Exception('Falha ao recuperar caminho do servidor arquivos da gráfica'." ".$sql);
    	}else{
    			$this->MensagemLog("Recuperou caminho do servidor arquivos da gráfica");
    	}
   
    	$caminho = array();
    	while ($row = pg_fetch_object($res)) {
    		$caminho[]= $row->valvalor;
    	}
    	return $caminho;
    }
    
    /**
     * Retorna as informações de senha usuario e o ftp
     * @throws Exception
     * @return array
     */
	public function getInformacoesFPT(){

		$sql = " SELECT
					    valvalor
				 FROM
					    dominio
				 INNER JOIN registro ON domoid = regdomoid
				 INNER JOIN valor ON valregoid = regoid
				 WHERE
					   valoid in (45,47,49,52,54)";
		
		$inf = array();
		if(!$res = pg_query($this->conn, $sql)) {
			throw new Exception('Falha ao recuperar informaçoes do ftp arquivos da gráfica'." ".$sql);
			$this->MensagemLog("Falha ao recuperar informações do ftp arquivos da gráfica");
		}else{
    			$this->MensagemLog("Recuperou informações do ftp arquivos da gráfica");
    	}
		while ($row = pg_fetch_object($res)) {
			$inf[]= $row->valvalor;
		}
		return $inf;
	}
	
    /**
     * Mensagens no boleto
     * 
     * @return array
     */
    public function getMensagensBoleto($formaCobranca = null) {

        $retorno = array();

        if($formaCobranca == 84){

            $sql = "SELECT 
                        pcsidescricao
                    FROM 
                        parametros_configuracoes_sistemas
                    INNER JOIN 
                        parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
                    WHERE 
                        pcsipcsoid = 'COBRANCA_REGISTRADA'
                    AND 
                        pcsioid = 'INSTRUCOES_BOLETO' ";

            $rs = pg_query($this->conn, $sql);

            if(pg_num_rows($rs) > 0){
                while($result = pg_fetch_object($rs)){
                    $retorno = explode('</BR>', $result->pcsidescricao);
                }
            }

        }else{

            $retorno [] = 'NÃO RECEBER APÓS 30 DIAS DO VENCIMENTO';
            $retorno [] = 'Após o vencimento cobrar:';
            $retorno [] = 'Multa de 2%';
            $retorno [] = 'Juros de 0,033% ao dia';

        }

        return $retorno;



    }

    /**
     * Notas Fiscais por data de referência
     * 
     * @var FinGeraNfBoletoGraficaVo $voPesquisa
     * @return array
     */
    public function notasFiscaisPorDataReferencia($voPesquisa) {
    	
    	

      $rn = new FinGeraNfBoletoGraficaRN($voPesquisa);

        $sql = "
			SELECT
				nfloid,
		    	nflno_numero, 
				nflserie,
		    	titvl_titulo, 
		    	titdt_vencimento, 
    			clinome,
                clioid,
    			forcnome,
      			( CASE
		           WHEN ( endcep IS NOT NULL ) THEN endcep :: text
		           ELSE clicep_fiscal :: text
        	    END ) AS clicep_com
    		" . $rn->from() . " 
    		" . $rn->where() . " 
    		GROUP BY 
    			nflno_numero, nflserie, titvl_titulo,titdt_vencimento, clinome, clioid, forcnome, clicep_com, nfloid  
    		ORDER BY 
    			clinome, nfloid DESC";


        print "\n::: ".date('Y/m/d H:i')." :::\n";
        print $sql;
        print "\n::: ".date('Y/m/d H:i')." :::\n";


/*$sql="SELECT 
nfloid, 
nflno_numero, 
nflserie, 
titvl_titulo, 
titdt_vencimento, 
clinome, 
clioid, 
forcnome, 
(CASE WHEN (clicep_com IS NOT NULL) THEN clicep_com::text ELSE clino_cep_com::text END) AS clicep_com 
FROM nota_fiscal_item 
INNER JOIN nota_fiscal ON nflno_numero = nfino_numero AND nflserie = nfiserie 
INNER JOIN titulo ON titnfloid = nfloid 
INNER JOIN contrato ON connumero = nficonoid 
INNER JOIN proposta ON prptermo = connumero 
LEFT JOIN tipo_proposta ON tppoid = prptppoid 
INNER JOIN clientes ON clioid = nflclioid 
LEFT JOIN veiculo ON veioid = conveioid 
LEFT JOIN forma_cobranca ON forcoid	= titformacobranca 
LEFT JOIN obrigacao_financeira ON obroid = nfiobroid 
LEFT JOIN endereco	ON endoid = cliend_cobr 
LEFT JOIN estado ON estoid = endestoid 
WHERE nfldt_cancelamento IS NULL AND nflserie = 'A' 
AND nfldt_referencia 
BETWEEN '2014-09-01 00:00:00' AND '2014-09-30 23:59:59' 
AND (concsioid = 1 OR concsioid = 32) 
AND veidt_exclusao IS NULL AND nfldt_envio_grafica IS NULL AND clienvio_grafica = 't' 
AND exists (SELECT nfeknfloid FROM nota_fiscal_eletronica_kernel WHERE nfeknfloid = nfloid) 
GROUP BY nflno_numero, nflserie, titvl_titulo,titdt_vencimento, clinome, clioid, forcnome, clicep_com, nfloid 
ORDER BY clinome, nfloid DESC";*/
        
   
        //anteriormente para o limit na sql acima utilizava-se a método $rn->limit()
 
        $rs = pg_query($this->conn, $sql);

        if (!$rs) {
            throw new Exception('Falha na pesquisa das Notas Fiscais.');
            $this->MensagemLog("Falha na pesquisa das Notas Fiscais.");
        }
	
        $notas = array();
        while ($nota = pg_fetch_object($rs)) {
            $notas[] = $nota;
        }
        $this->MensagemLog("Retornando notas.");
        return $notas;
    }

    /**
     * Itens das Notas Fiscais por data de referência
     * 
     * @var FinGeraNfBoletoGraficaVo $voPesquisa
     * @return array
     */
    public function itensNotasFiscaisPorDataReferencia(FinGeraNfBoletoGraficaVo $voPesquisa) {

        $rn = new FinGeraNfBoletoGraficaRN($voPesquisa);

        $sql = "SELECT nfloid,
				       nflno_numero,
				       nflserie,
				       nflvlr_imposto,
				       nflaliquota_imposto,
				       nfldt_referencia,
				       nfldt_emissao,
				       nflvl_desconto,
				       nfivl_item,
				       clinome,
				       clioid,
				       clitipo,
				       clino_doc,
				       clienvio_grafica,
				       titdt_vencimento,
				       titoid,
                       titformacobranca,
				       titvl_titulo,
				       tppoid,
				       tppoid_supertipo,
				       obrobrigacao,
				       obrobs_boleto,
				       veiplaca,
				       COALESCE(clirua_fiscal,'') || COALESCE(', ' || clino_fiscal,'') || COALESCE(' - ' || clicompl_fiscal,'') as log_fiscal,
				       COALESCE(clibairro_fiscal,'')as bairro_fiscal, 
				       COALESCE(clicep_fiscal,'') as cep_fiscal, 
				       COALESCE(clicidade_fiscal,'') || COALESCE(' - ' || cliuf_fiscal,'') as cidade_fiscal,
				       cliend_cobr as end_cor,  
				       COALESCE(endlogradouro,'') || COALESCE(', ' || endno_numero,'') || COALESCE(' - ' || endcomplemento,'') as log_cor,
				       COALESCE(endbairro,'') as bairro_cor, 
				       COALESCE(endcep,'') as cep_cor, 
				       COALESCE(endcidade,'') || COALESCE(' - ' || enduf,'') as cidade_cor                
    		" . $rn->from() . " 
    		" . $rn->where() . " 
    		ORDER BY 
    			nfloid            
    	";
        
 

        $rs = pg_query($this->conn, $sql);

        if (!$rs) {
            throw new Exception('Falha na pesquisa das Notas Fiscais.');
        }
        
        return $rs;        
        
    }
    
    public function contarNotas(FinGeraNfBoletoGraficaVo $voPesquisa) {
        
        $rn = new FinGeraNfBoletoGraficaRN($voPesquisa);
        
        $sql = "SELECT
                    COUNT(1) as qtd,
                    tipo
                FROM(
                        SELECT
                            COUNT(1),
                            CASE
                                WHEN 
                                    tppoid_supertipo = 12 OR tppoid = 12 
                                THEN
                                    'VAREJO'
                                ELSE
                                'SASCAR'
                            END AS tipo	    	
                            " . $rn->from() . " 
                        LEFT JOIN 
                            config_banco ON cfbbanco = forccfbbanco     
                        " . $rn->where() . "                            
                        AND
                            forcoid IS NOT NULL
                        AND
                            endoid IS NOT NULL                
                        GROUP BY 
                            tipo, nfloid
                ) AS sql
                GROUP BY tipo
            ";
 
        $rs = pg_query($this->conn, $sql);
        $retorno = array();
        
        while($result =  pg_fetch_object($rs)){
            $retorno[$result->tipo] = $result->qtd;
        }
        
        return $retorno;
        
    }


    public function verificarNota($row) {
        
        $sql = "SELECT
                    --dados de forma de cobrança
                    forma_cobranca.forcoid AS dados_forma_cobranca,

                    --dados bancarios referente a forma de cobrança
                    config_banco.cfbbanco AS banco,
                    config_banco.cfbagencia,
                    config_banco.cfbconta_corrente,

                    --endereço do cliente
                    endereco.endoid AS endereco_cliente,

                    --dados boleto gerado
                    (select tbrecd_barras from titulo_boleto_registro where tbretitoid=titulo.titoid and tbrecd_movimento in ('2','14','27') order by tbreoid asc limit 1) as codigo_barras,
                    (select tbrelinha_digitavel from titulo_boleto_registro where tbretitoid=titulo.titoid and tbrecd_movimento in ('2','14','27') order by tbreoid asc limit 1) as linha_digitavel,
                    (select tbrenosso_numero from titulo_boleto_registro where tbretitoid=titulo.titoid and tbrecd_movimento in ('2','14','27') order by tbreoid asc limit 1) as nosso_numero
                FROM
                    clientes
                LEFT JOIN titulo 			  ON titclioid 	      = clioid
                LEFT JOIN forma_cobranca 	  ON titformacobranca = forcoid
                LEFT JOIN config_banco 		  ON cfbbanco	      = forccfbbanco
                LEFT JOIN nota_fiscal 		  ON titnfloid		  = nfloid
                LEFT JOIN endereco 			  ON cliend_cobr	  = endoid
                LEFT JOIN motivo_inadimplente ON titmotioid 	  = motioid
                WHERE titoid = " . $row->titoid . "
                LIMIT 1";

        $rsVerifica = pg_query($this->conn, $sql);

        $itemOk = true;
        
        $verifica = new stdClass();
        
        $verifica->item = pg_fetch_object($rsVerifica);

        if (is_null($verifica->item->dados_forma_cobranca)) {

            $this->log[] = array(
                'cliente_id' => $row->clioid,
                'cliente' => $row->clinome,
                'nota' => $row->nflno_numero . ' ' . $row->nflserie,
                'msg' => 'Falha ao recuperar dados de forma de cobrança.'
            );
            
            $itemOk = false;
        }

        if (is_null($verifica->item->banco) && !is_null($verifica->item->dados_forma_cobranca)  && in_array($verifica->item->dados_forma_cobranca, array('73','74'))) {

            $this->log[] = array(
                'cliente_id' => $row->clioid,
                'cliente' => $row->clinome,
                'nota' => $row->nflno_numero . ' ' . $row->nflserie,
                'msg' => 'Falha ao recuperar dados bancários referentes à forma de cobrança.'
            );

            $itemOk = false;
        }

        if (is_null($verifica->item->endereco_cliente)) {

            $this->log[] = array(
                'cliente_id' => $row->clioid,
                'cliente' => $row->clinome,
                'nota' => $row->nflno_numero . ' ' . $row->nflserie,
                'msg' => 'Falha ao recuperar dados do endereço do cliente.'
            );

            $itemOk = false;
        }


        if ($verifica->item->banco == '341') {

            $digito = "";
            $conta_corrente = $verifica->item->cfbconta_corrente;

            if (strpos($conta_corrente, "-") !== false) {

                $posicao_digito = strrpos($conta_corrente, "-");
                $digito = substr($conta_corrente, $posicao_digito + 1, strlen($conta_corrente) - $posicao_digito);
                $conta_corrente = substr($conta_corrente, 0, $posicao_digito);

                if (strlen($conta_corrente) > 5) {

                    $this->log[] = array(
                        'cliente' => $row->clinome,
                        'nota' => $row->nflno_numero . ' ' . $row->nflserie,
                        'msg ' => 'Conta não pode ultrapassar 5 caracteres: ' . $verifica->item->cfbconta_corrente
                    );

                    $itemOk = false;
                }
            }
        }
        
        return array(
            'itemOK' => $itemOk,
            'item' => $verifica->item
        );
        
    }

    /**
     * Busca todos os registros ativos de tipos de contrato
     * 
     * @author	Alex S. Médice <alex.medice@meta.com.br>
     * @return array
     */
    public function tiposContratosAtivos() {

        $sql = "SELECT
                    tpcoid, 
                    tpcdescricao
                FROM
                    tipo_contrato
                WHERE
                    tpcativo IS TRUE
                ORDER BY
                    tpcdescricao";

        $rs = pg_query($this->conn, $sql);

        if (!$rs) {
            throw new Exception('Falha na pesquisa dos tipos de contrato.');
        }

        $tipos = array();
        while ($tipo = pg_fetch_object($rs)) {
            $voTipo = new stdClass();
            $voTipo->key = $tipo->tpcoid;
            $voTipo->value = $tipo->tpcdescricao;

            $tipos[] = $voTipo;
        }

        return $tipos;
    }

    /**
     * Verifica se o contrato existe
     * 
     * @var string $connumero
     * @return boolean
     */
    public function isContratoNaoExiste($connumero) {

        $sql = "SELECT connumero FROM contrato WHERE connumero = " . $connumero;
        $rs = pg_query($this->conn, $sql);

        if (!$rs) {
            throw new Exception('Falha na verificação se contrato existe: ' . $connumero);
        }

        return (pg_num_rows($rs) > 0) ? false : true;
    }

    /**
     * Verifica se o contrato está inativo
     * 
     * @var string $connumero
     * @return boolean
     */
    public function isContratoInativo($connumero) {

        $sql = "SELECT concsioid FROM contrato WHERE connumero = " . $connumero . " AND (concsioid = 1 OR concsioid = 32)"; // ativo ou retenção
        $rs = pg_query($this->conn, $sql);

        if (!$rs) {
            throw new Exception('Falha na verificação de contrato inativo: ' . $connumero);
        }

        return (pg_num_rows($rs) > 0) ? false : true;
    }

    /**
     * Verifica se o veículo está inativo
     * 
     * @var string $placa
     * @return boolean
     */
    public function isVeiculoInativo($placa) {

        $sql = "SELECT veioid FROM veiculo WHERE veiplaca='" . $placa . "' AND veidt_exclusao IS NOT NULL";
        $rs = pg_query($this->conn, $sql);

        if (!$rs) {
            throw new Exception('Falha na pesquisa do veículo: ' . $placa);
        }

        return (pg_num_rows($rs) > 0) ? true : false;
    }

    /**
     * Verifica nome nova marca
     * 
     * @var string $identificador
     * @return string
     */
    public function nomeParametrizacao($identificador) {

        $sql = "SELECT 
    				pcsidescricao
				FROM 
					parametros_configuracoes_sistemas_itens
				WHERE 
					pcsioid = 'NOME'
				AND 
					pcsipcsoid = '$identificador'";
        $rs = pg_query($this->conn, $sql);

        if (!$rs) {
            throw new Exception('Falha na pesquisa do paramentro: ' . $identificador);
        }

        return (pg_fetch_result($rs, 0, 'pcsidescricao'));
    }

    /**
     * ATUALIZA O titulo com o nosso_numero gerado
     * 
     * @param string $nosso_numero
     * @param int $titulo_id
     * @return void
     */
    public function atualizarNossoNumero($nosso_numero, $titulo_id) {
        $sql = "UPDATE titulo SET titnumero_registro_banco = '" . $nosso_numero . "' WHERE titoid = " . $titulo_id;

        $rs = pg_query($this->conn, $sql);

        $sql2 = "UPDATE titulo_venda SET titnumero_registro_banco = '" . $nosso_numero . "' WHERE titoid = " . $titulo_id;

        $rs2 = pg_query($this->conn, $sql2);

        if (!$rs or !$rs2) {
            throw new Exception('Falha ao atualizar o título com o Nosso Número');
        }
    }

    /**
     * O sistema deve atualizar a nota fiscal com o status de arquivo gerado
     * 
     * @param string $nota
     * @return void
     */
    public function atualizarStatusArquivoGerado($nota) {
        if (!empty($nota)){
            $sql = "UPDATE 
                           nota_fiscal 
                       SET nfldt_envio_grafica = NOW() 
                   WHERE 
                       nfloid IN ( " . implode(",", $nota) .  " )";

           $rs = pg_query($this->conn, $sql);

           if (!$rs) {
               throw new Exception('Falha ao atualizar a data de envio do arquivo para gráfica');
           } 
        }
    }
    
    public function verificarNaTituloRetencao($titoid) {
        
        $sql = "SELECT * from titulo_retencao WHERE titoid = " . $titoid;

        if (!$rs = executa($this->conn, $sql)) {
            throw new Exception("Erro ao verificar titulo_retencao. Tente novamente.");
        }
        
        return pg_num_rows($rs);
        
    }
    
     /**
     * STI 83807
     * Notas Fiscais para calculo do imposto
     * 
     * @var FinGeraNfBoletoGraficaVo $voPesquisa
     * @return array
     */
    public function notasFiscaisCalculoImposto(FinGeraNfBoletoGraficaVo $voPesquisa) {

        $rn = new FinGeraNfBoletoGraficaRN($voPesquisa);

        $sql = "
            SELECT
                nfloid
            " . $rn->from() . " 
            " . $rn->where() . "  
            ORDER BY 
                nfloid";

 
        $rs = pg_query($this->conn, $sql);

        if (!$rs) {
            throw new Exception('Falha na pesquisa das Notas Fiscais.');
		} 
    
        $notas = array();
        while ($nota = pg_fetch_object($rs)) {
            $notas[] = $nota;
        }

        return $notas;
    }
    
    public function MensagemLog($msg){
    	$hora_atual    = date("H:i:s)");
    	$data_processamento = date("Ymd");
    	$fp = fopen(_SITEDIR_."faturamento/log_geracao_nf_boleto_grafica","a");
    	chmod(_SITEDIR_."faturamento/log_geracao_nf_boleto_grafica", 0777);
    	fputs ($fp,"$hora_atual - $msg\n");
    	fclose($fp);
    }
    
} 
    
class FinGeraNfBoletoGraficaRN implements FinGeraNfBoletoGraficaRNCriteria {

    /**
     * @var FinGeraNfBoletoGraficaVo
     */
    private $vo;

    public function __construct(FinGeraNfBoletoGraficaVo $vo) {
        $this->vo = $vo;
    }

    public function from() {
        $from[] = 'FROM 		nota_fiscal_item';
        $from[] = "INNER JOIN 	nota_fiscal 			ON nflno_numero = nfino_numero AND nflserie = nfiserie";
        $from[] = 'INNER JOIN 	titulo 					ON titnfloid 	= nfloid';
        $from[] = 'INNER JOIN 	contrato 				ON connumero 	= nficonoid';
		$from[] = 'LEFT JOIN    proposta                ON prptermo     = connumero';
        $from[] = 'LEFT  JOIN 	tipo_proposta 			ON tppoid 		= prptppoid';
        
        $from[] = "INNER JOIN ( SELECT cliend_cobr,
				      clinome,
				          clioid,
				          clitipo,
				          clirua_res AS clirua_fiscal,
				          clino_res AS clino_fiscal,
				          clicompl_res AS clicompl_fiscal,
				          clibairro_res AS clibairro_fiscal,
				          COALESCE ( clicep_res , clino_cep_res::text) AS clicep_fiscal,
				          clicidade_res AS clicidade_fiscal,
				          cliuf_res AS cliuf_fiscal,
				          LPAD(clino_cpf::text, 11, '0') AS clino_doc,
				          clienvio_grafica
				        FROM clientes
				        WHERE clitipo = 'F'
				        
				        UNION ALL
				
				        SELECT
				          cliend_cobr,
				          clinome,
				      clioid,
				          clitipo,
				          clirua_com AS clirua_fiscal,
				          clino_com AS clino_fiscal,
				          clicompl_com AS clicompl_fiscal,      
				          clibairro_com AS clibairro_fiscal,
				          clicep_com AS clicep_fiscal,
				          clicidade_com AS clicidade_fiscal,
				          cliuf_com AS cliuf_fiscal,
				          LPAD(clino_cgc::text, 14, '0')  AS clino_doc,
				          clienvio_grafica      
				        FROM clientes
				        WHERE clitipo = 'J') clientes ON clioid = nflclioid"; 
		$from[] = 'LEFT JOIN 	veiculo 				ON veioid 		= conveioid';
        $from[] = 'LEFT JOIN 	forma_cobranca 			ON forcoid		= titformacobranca';
        $from[] = 'LEFT JOIN 	obrigacao_financeira 	ON obroid 		= nfiobroid';
        $from[] = 'LEFT JOIN 	endereco			 	ON endoid 		= cliend_cobr';


        /**
         * Restringe o JOIN com parametros_faturamento pelo obrigação financeira.
         * Esta restrição foi retirada a pedido do cliente:
         * 
         * AND									   obroid		= parfobroid
         */
        return implode(' ', $from);
    }

    public function where() {

    	
        list($ano, $mes, $dia) = explode('-', $this->vo->frm_data);

        $ultimoDiaMes = $this->ultimoDiaMes($ano, $mes);
        $dataFimBusca = $ano . '-' . $mes . '-' . $ultimoDiaMes;

        $where[] = "WHERE 	nfldt_cancelamento IS NULL"; // Não considerar nota fiscal cancelada.
		
        $backtrace = debug_backtrace();
        $sbtec = false;

        foreach ($backtrace as $arquivo) {
            if (strstr($arquivo["file"], 'sbtec.')) {
                $sbtec = true;
            }
        }

  		if($sbtec){
			$where[] = "AND 	nflserie = 'SB'"; // Mantis 7064 - Desvincular o envio para gráfica do Delphi.
		}else{
			$where[] = "AND 	nflserie = 'A'"; // RN2
		}
		
        $where[] = "AND     nfldt_referencia BETWEEN '" . $this->vo->frm_data . " 00:00:00' AND '" . $dataFimBusca . " 23:59:59'"; // Não considerar nota fiscal onde a data de emissão seja diferente da data de referência informada na tela.
        $where[] = "AND 	(concsioid = 1 OR concsioid = 32)"; // contratos com status ativo ou retenção
		$where[] = "AND 	veidt_exclusao IS NULL";
        $where[] = "AND 	nfldt_envio_grafica IS NULL";
        // $where[] = "AND		(parfenvia_grafica IS NULL OR parfenvia_grafica = 't')";
        //$where[] = "AND		titformacobranca IN (73,74, 1)";
        $where[] = "AND 	clienvio_grafica = 't'"; // RN2
        //Deve resgatar somente registros que já tenham sido enviados para kernel
        //$where[] = "AND nfloid IN (SELECT nfeknfloid FROM nota_fiscal_eletronica_kernel WHERE nfeknfloid = nfloid)";
        $where[] = "AND exists (SELECT nfeknfloid FROM nota_fiscal_eletronica_kernel WHERE nfeknfloid = nfloid)";


        if (!empty($this->vo->frm_doc)) {
            $where[] = "AND 	clitipo = '" . $this->vo->frm_tipo . "'";

            switch ($this->vo->frm_tipo) {
                case 'F':
                    $where[] = "AND 	clino_cpf = '" . $this->vo->frm_doc . "'";
                    break;
                case 'J':
                    $where[] = "AND 	clino_cgc = '" . $this->vo->frm_doc . "'";
                    break;
            }
        }

        if (!empty($this->vo->frm_cliente)) {
            $where[] = "AND 	clinome ILIKE '%" . $this->vo->frm_cliente . "%'";
        }

        if (!empty($this->vo->frm_tipo_contrato)) {
            $where[] = "AND 	conno_tipo = '" . $this->vo->frm_tipo_contrato . "'";
        }

        if (!empty($this->vo->frm_contrato)) {
            $where[] = "AND 	connumero = '" . $this->vo->frm_contrato . "'";
        }

        if (!empty($this->vo->frm_placa)) {
            $where[] = "AND 	veiplaca = '" . $this->vo->frm_placa . "'";
        }

        if (!empty($this->vo->notas_ignoradas)) {
            $in = implode(',', $this->vo->notas_ignoradas);

            $where[] = "AND 	nfloid IN(" . $in . ")";
        }

        return implode(' ', $where);
    }

    public function limit() {
        if (!empty($this->vo->frm_resultados)) {
            $limit = "LIMIT 	" . $this->vo->frm_resultados;
        }

        return $limit;
    }

    /*
     * Metodo para retornar o ultimo dia do mes passado como parametro de acordo com o ano
     */
    public function ultimoDiaMes($ano, $mes){ 

	

       if (((fmod($ano,4)==0) and (fmod($ano,100)!=0)) or (fmod($ano,400)==0)) { 
           $dias_fevereiro = 29; 
       } else { 
           $dias_fevereiro = 28; 
       } 
    

        switch(intval($mes)) { 
            case '01': 
                return 31; 
            break; 
            case '02': 
                return $dias_fevereiro; 
            break; 
            case '03': 
                return 31; 
            break; 
            case '04': 
                return 30; 
            break; 
            case '05': 
                return 31; 
            break; 
            case '06': 
                return 30; 
            break; 
            case '07': 
                return 31; 
            break; 
            case '08': 
                return 31;
            break; 
            case '09': 
                return 30;
            break; 
            case '10':
                return 31; 
            break; 
            case '11': 
                return 30; 
            break; 
            case '12': 
                return 31; 
            break; 
       } 
    }
}

class FinGeraNfBoletoGraficaVo {

    public $frm_data;
    public $frm_doc;
    public $frm_tipo = 'J';
    public $frm_cliente;
    public $frm_tipo_contrato;
    public $frm_contrato;
    public $frm_placa;
    public $frm_resultados = '';
    public $frm_grafica = 'hsbc';
    public $notas_ignoradas = array();
    public $frm_mensagens = array();

    public function __construct($data) {

        foreach ($this as $key => $val) {
            if (isset($data[$key])) {

                $this->$key = is_string($data[$key]) ? trim($data[$key]) : $data[$key];

                if ($key =='frm_data') {
                    $this->$key = $this->$key;
                }
                if ($key == 'frm_doc') {
                    $this->$key = FinGeraNfBoletoGraficaUtil::sanitizeInteger($this->$key);
                }
                if ($key == 'frm_cliente') {
                    $this->$key = strtoupper($this->$key);
                }
            }
        }

        $this->notas_ignoradas = ($data['notas_ignoradas']) ? $data['notas_ignoradas'] : array();
        $this->frm_mensagens = ($data['frm_mensagens']) ? $data['frm_mensagens'] : array();
    }

}

class FinGeraNfBoletoGraficaNotaFiscalVo {

    public $IdNF;
    public $NumeroNF;
    public $SerieNF;
    public $NomeSacado;
    public $Endereco;
    public $Bairro;
    public $Cidade;
    public $CEP;
    public $Endereco_NF;
    public $Bairro_NF;
    public $Cidade_NF;
    public $CEP_NF;
    public $CNPJ;
    public $NumeroContrato = ''; // Pode deixar vazio por enquanto ???;
    public $Referencia;
    public $DataEmissao;
    public $Impressao = 'ANALITICO';
    public $EnviaCliente = 'SIM';
    public $Vencimento;
    public $NossoNumero = ''; // Dica: Deve ser calculado de acordo com o banco para o qual será impresso o título. ????;
    public $NumeroDocumento;
    public $ValorPagar;
    public $TipoMensagem = '0';
    public $ValorFatura;
    public $ValorTitulo;
    public $ValorDesconto;
    public $ValorDescontoNF;
    public $ValorOutrasDeducoes = ''; // ???;
    public $Boleto = 'SIM';
    public $LinhaDigitavel = ''; // Calculado de acordo com o banco para o qual será impresso o título.;
    public $CodigoBarras = ''; // Calculado de acordo com o banco para o qual será impresso o título.;
    public $TipoEmpenho = 'N';
    public $Empenho = ''; // ???;
    public $FormaPagamento = ''; // ???;
    public $Mensagem1 = 'NAO RECEBER APOS 30 DIAS DO VENCIMENTO';
    public $Mensagem2 = 'Apos o vencimento cobrar:';
    public $Mensagem3 = 'Multa de 2%';
    public $Mensagem4 = 'Juros de 0,033% ao dia              Parc.:1';
    public $Mensagem5 = '';
    public $Mensagem6 = 'MONITORAMENTO-A cobrança é antecipada e o vencimento é sempre dia 16 de cada mês. Ex.: você paga dia 16 de outubro a  monitoramento referen-';
    public $Mensagem7 = ' te ao período de 01/10 a 30/10. Mas atenção, no 1º monitoramento pode haver cobrança de Pro-rata, dependendo da data de instalação do SASCAR.';
    public $Mensagem8 = 'PRO-RATA-Cobrança dos dias do mês anterior ao mês atual cobrado, ou seja, dos dias referente ao mês de instalação. Ex. foi instalado  dia';
    public $Mensagem9 = ' 21/09/02, é cobrado os dias de monitoramento 21/09 a 30/09/02, mais o mês de outubro/02. (10 dias + mensalidade)';
    public $Mensagem10 = 'ROAMING-Deslocamento de um telefone celular, para fora da área de cobertura da operadora do celular, instalado no veículo. O deslocamento';
    public $Mensagem11 = ' é cobrado pela operadora e repassado ao cliente SASCAR, conforme previsto na cláusula 4 do contrato em vigor.';

    public function __construct(stdClass $row) {
        $map = new FinGeraNfBoletoGraficaNotaFiscalVoMap();

        foreach ($this as $key => $value) {
            $column = property_exists($map, $key) ? $map->$key : $key;

            if (property_exists($row, $column)) {
                $this->$key = $row->$column;
            }
        }
    }

    public function setFinBoleto(stdClass $voBoleto) {
        $this->NossoNumero = $voBoleto->nossoNumeroDv;
        $this->NossoNumeroSemDv = $voBoleto->nossoNumero;
        $this->LinhaDigitavel = $voBoleto->linhaDigitavel;
        $this->CodigoBarras = $voBoleto->codigoBarras;
    }

}

class FinGeraNfBoletoGraficaNotaFiscalVoMap {

    public $IdNF = 'nfloid';
    public $NumeroNF = 'nflno_numero';
    public $SerieNF = 'nflserie';
    public $NomeSacado = 'clinome';
    public $Endereco = 'clirua_com';
    public $Bairro = 'clibairro_com';
    public $Cidade = 'clicidade_com';
    public $CEP = 'clicep_com';
    public $Endereco_NF = 'clirua_com';
    public $Bairro_NF = 'clibairro_com';
    public $Cidade_NF = 'clicidade_com';
    public $CEP_NF = 'clicep_com';
    public $CNPJ = 'clino_doc'; // Dica: clientes.clino_cpf (quando pessoa física) ou clientes.clino_cgc (quando pessoa jurídica);
    public $Referencia = 'nfldt_referencia';
    public $DataEmissao = 'nfldt_emissao';
    public $Vencimento = 'titdt_vencimento';
    public $NumeroDocumento = 'titoid';
    public $ValorPagar = 'titvl_titulo';
    public $ValorFatura = 'titvl_titulo';
    public $ValorTitulo = 'titvl_titulo';
    public $ValorDesconto = 'nflvl_desconto';
    public $ValorDescontoNF = 'nflvl_desconto';

}

class FinGeraNfBoletoGraficaNotaFiscalVoType {

    private $IdNF = 'int';
    private $NumeroNF = 'int';
    private $SerieNF = 'text';
    private $NumeroContrato = 'int';
    private $DataEmissao = 'date';
    private $Vencimento = 'date';
    private $ValorPagar = 'dec';
    private $TipoMensagem = 'int';
    private $ValorFatura = 'dec';
    private $ValorTitulo = 'dec';
    private $ValorDesconto = 'dec';
    private $ValorDescontoNF = 'dec';
    private $ValorOutrasDeducoes = 'dec';

    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        return 'str';
    }

}

class FinGeraNfBoletoGraficaNotaFiscalItemVo {

    public $Qtde = 1;
    public $Placa;
    public $Descricao;
    public $Valor;
    public $Tipo;
    public $SuperTipo;

    public function __construct(stdClass $row) {
        $map = new FinGeraNfBoletoGraficaNotaFiscalItemVoMap();

        foreach ($this as $key => $value) {
            $column = property_exists($map, $key) ? $map->$key : $key;

            if (property_exists($row, $column)) {
                $this->$key = $row->$column;
            }
        }
    }

}

class FinGeraNfBoletoGraficaNotaFiscalItemVoMap {

    public $Placa = 'veiplaca';
    public $Descricao = 'obrobrigacao';
    public $Valor = 'nfivl_item';
    public $Tipo = 'tppoid';
    public $SuperTipo = 'tppoid_supertipo';

}

class FinGeraNfBoletoGraficaNotaFiscalItemVoType {

    private $Qtde = 'str';
    private $Placa = 'str';
    private $Descricao = 'str';
    private $Valor = 'dec';
    private $Tipo = 'int';
    private $SuperTipo = 'int';

    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        return 'str';
    }

}

class FinGeraNfBoletoGraficaAction {

    /**
     * Informações do $_REQUEST
     * @property $request
     */
    protected $request;

    /**
     * Link de conexão com o banco de dados
     * @var mixed $conn
     */
    protected $conn;

    /**
     * Ação atual
     * @var string $action
     */
    protected $action;

    /**
     * Atributo para acesso a persistência de dados
     * @property FinGeraNfBoletoGraficaDAO
     */
    protected $dao;

    /**
     * Informações para View
     * @property View
     */
    protected $view;

    /*
     * @var array $request
     * @return void
     */

    public function __construct() {

        global $conn;

        $this->request = $request;
        $this->conn = $conn;
        $this->view = new FinFaturamentoView();
        $this->action = ($this->request['acao']) ? $this->request['acao'] : 'index';

        $this->view->acao = $this->action;
        $this->view->msg = ($this->request['msg']) ? $this->request['msg'] : '';
    }

}

class FinGeraNfBoletoGraficaUtil {

    const MASK_CPF = '999.999.999-99';
    const MASK_CNPJ = '99.999.999/9999-99';

    public static function dateToDb($date, $format = 'Y-m-d') {
        if (empty($date)) {
            return '';
        }

        $tmp = explode('/', $date);
        $date = $tmp['2'] . '-' . $tmp['1'] . '-' . $tmp['0'];

        return $date;
    }

    public static function dateToView($date, $format = 'd/m/Y') {
        if (empty($date)) {
            return '';
        }

        return date($format, strtotime($date));
    }

    public static function docToView($value, $type) {

        if (empty($value)) {
            return '';
        }

        switch ($type) {
            case 'F':
                $mask = self::MASK_CPF;
                break;
            case 'J':
            default:
                $mask = self::MASK_CNPJ;
                break;
        }

        return self::applyMask($value, $mask);
    }

    /**
     * Aplica mascara a qualquer numero de acordo com a mascara
     * 
     * @example FinGeraNfBoletoGraficaUtil::applyMask('12345678901234', 99.999.999/9999-99); Mascara para CNPJ retornará 12.345.678/9012-34
     * @param integer $value
     * @param string $mask
     * @param string $pad_string
     * @param string $pad_type
     * @return string
     */
    public static function applyMask($value, $mask, $pad_string = 0, $pad_type = STR_PAD_LEFT) {

        preg_match_all('/[^0-9]/', $mask, $matches, PREG_OFFSET_CAPTURE); // pega qualquer caracter que não seja numérico na máscara
        $matches = current($matches);

        if ($matches) {
            $length = (strlen($mask) - count($matches));
            $value = str_pad($value, $length, $pad_string, $pad_type); // garante que o valor tem o mesmo tamanho da mascara e preenche com 0 a esquerda caso seja menor 

            foreach ($matches as $matche) { // percorre todos caracteres especias
                list($accent, $pos) = $matche; // pega o caracter especial e sua posição

                $newValue = substr($value, 0, $pos); // pega o valor até a posição do caracter especial 
                $newValue .= $accent; // adiciona o acento no valor
                $newValue .= substr($value, $pos); // pega o valor depois da posição do caracter especial 

                $value = $newValue;
            }
        }

        return $value;
    }

    /**
     * Limpa uma string retirando qualquer caracter que não seja numerico
     * 
     * @param string $value
     * @return integer
     */
    public static function sanitizeInteger($value) {
        return preg_replace('/[^0-9]/', '', $value);
    }

}

class FinFaturamentoView {
    
}

interface FinGeraNfBoletoGraficaRNCriteria {

    public function __construct(FinGeraNfBoletoGraficaVo $vo);

    public function where();

}

class ExceptionValidation extends Exception {
    
}


