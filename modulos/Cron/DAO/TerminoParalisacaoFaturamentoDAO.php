<?php

/**
 * Classe responsável pela persistência de dados
 * @author Marcello Borrmann <marcello.borrmann@meta.com.br>
 * @since 25/03/2015
 * @category Class
 * @package TerminoParalisacaoFaturamentoDAO
 */

class TerminoParalisacaoFaturamentoDAO {   
	
    private $conn;
        
    /**
     * MÉTODO PARA PESQUISAR A QTDE DE DIAS PARAMETRIZADA PARA ENVIO DE EMAIL
     *
     * @param
     * @return int
     * @throws
     */
    public function BuscarDiasEnvio(){
		
		$sql = "
		    	SELECT 
		    		valvalor
		    	FROM 
		    		dominio
		    		INNER JOIN registro ON regdomoid = domoid
		    		INNER JOIN valor ON valregoid = regoid
		    	WHERE
		    		domnome = 'NUMERO DIAS AVISO ENCERRAMENTO PARALISACAO'
		    		AND domativo = 1
		    	;";

		//echo $sql;
		$rs = pg_query($this->conn,$sql);

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_result($rs, 0, 0);
		}
		
		return $retorno;
    	    	 
    }
    
    /**
     * MÉTODO PARA PESQUISAR A DATA DO ÚLTIMO DIA DO MÊS ATUAL
     *
     * @param
     * @return date
     * @throws
     */
    public function BuscarUltimoDia(){
    
    	$sql = "
		    	SELECT 
    				(((TO_CHAR((NOW()::date + interval '1 month'),'YYYYMM')||'01')::date - interval '1 day')::date) AS dt_final
		    	;";
    
    	//echo $sql;
    	$rs = pg_query($this->conn,$sql);
    
    	if (pg_num_rows($rs) > 0){
    		$retorno = pg_fetch_result($rs, 0, 0);
    	}
    
    	return $retorno;
    	 
    }

    /**
     * MÉTODO PARA PESQUISAR A DATA DE ENVIO DO AVISO DE ENCERRAMENTO PARALISACAO
     *
     * @param stdclass 	->dt_ult 	Último dia do mês corrente
     *  				->dias		Qtde de dias parametrizado para envio do e-mail
     * @return date
     * @throws
     */
    public function BuscarDataEnvio(stdclass $dadosEnvio){
    
    	$sql = "
		    	SELECT
    				(('".$dadosEnvio->dt_final."'::date - interval '". $dadosEnvio->dias_env." days')::date) AS dt_envio
		    	;";
    
    	//echo $sql;
    	$rs = pg_query($this->conn,$sql);
    
    	if (pg_num_rows($rs) > 0){
    		$retorno = pg_fetch_result($rs, 0, 0);
    	}
    
    	return $retorno;
    
    }
    
 
	/**
	 * MÉTODO PARA SELECIONAR OS REGISTROS EM QUE A DATA DE INÍCIO DE PARALISAÇÃO, CORRESPONDAM AO DIA 1°, ATUAL
	 *
	 * @param text $assunto Assunto do email
	 * @return stdClass
	 * @throws ErrorException
	 */
	public function pesquisarParametro($dt_final){

		$retorno = array();
		
		$sql = "
				SELECT 					
					TO_CHAR(parfdt_ini_cobranca, 'DD/MM/YYYY') || ' até ' || TO_CHAR(parfdt_fin_cobranca, 'DD/MM/YYYY') AS periodo,
					parfemail_contato, 
					connumero, 
					veioid, 
					veiplaca, 
					clioid, 
					clinome 
				FROM 
					parametros_faturamento 
					INNER JOIN contrato ON connumero = parfconoid 
					INNER JOIN veiculo ON veioid = conveioid 
					INNER JOIN clientes ON clioid = conclioid 
				WHERE 
					parfdt_fin_cobranca::date = '" .$dt_final. "'
					AND parfativo = TRUE 
					AND parftipo = 'IS' 
					AND parfdt_exclusao IS NULL 
				;";

		//echo $sql;
		$rs = pg_query($this->conn,$sql);
		
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
		
		return $retorno;
	}
	
	/**
	 * MÉTODO QUE ATUALIZA FLAG SASWEB NA TABELA DE VEÍCULOS
	 * 
	 * @param int $veiculo ID do veiculo
	 * @return boolean
	 * @throws ErrorException
	 */
	public function atualizarVeiculoSasweb($veiculo) {
		 
		$sql = "
				UPDATE
					veiculo
				SET
					veivisualizacao_sasweb = 't',
					veidt_alteracao = NOW(),
					veiusuoid_alteracao = 2750
				WHERE
					veioid = " .$veiculo. "
				;";
		

		if (!$rs = pg_query($this->conn,$sql)){
			throw new Exception("Houve um erro ao atualizar FLAG SASWEB no veículo.");
		}
	
		return true;
	}
	
	/**
	 * MÉTODO PARA SELECIONAR DADOS DO EMAIL DE AVISO DE INÍCIO DE PARALISAÇÃO
	 *
	 * @param text $assunto Assunto do email
	 * @return stdClass
	 * @throws ErrorException
	 */
	public function pesquisarEmail($assunto){

		$retorno = new stdClass();
		// 'In%cio do Per%odo de Paralisa%o'
		$sql = " SELECT
					seecabecalho,
					seecorpo,
					seeimagem,
					seeimagem_anexo,
					seeremetente
				FROM
					servico_envio_email
				WHERE
					seecabecalho ILIKE '" . $assunto . "'
					AND seedt_exclusao IS NULL
				;";

		//echo $sql;
		$rs = pg_query($this->conn,$sql);

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}

	/**
	 * MÉTODO RESPONSÁVEL POR INSERIR HISTÓRICO DE ENVIO DE EMAIL DE AVISO DE INÍCIO DE PARALISAÇÃO
	 * 
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws 
	 */
	public function inserirHistoricoTermo(stdClass $dados){
	
		$sql = "SELECT
					historico_termo_i(
						". $dados->hitconnumero .",
						". $dados->hitusuoid .",
						'". $dados->hitobs ."'
					); ";
	
		//echo $sql;
		if (!$rs = pg_query($this->conn,$sql)){
			/* Não utilizo exception pois, erro ao inserir histórico, não significa que os 
			emails não foram enviados, portanto não faz sentdo executar rollback */ 
			echo "Houve um erro ao inserir Historico do Termo.";
		}
	
		return true;
	}
	
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function __get($var) {
        return $this->$var;
    }

    /**
     * Abre a transação
     */
    public function begin() {
        pg_query($this->conn, 'BEGIN');
    }

    /**
     * Finaliza um transação
     */
    public function commit() {
        pg_query($this->conn, 'COMMIT');
    }

    /**
     * Aborta uma transação
     */
    public function rollback() {
        pg_query($this->conn, 'ROLLBACK');
    }

}