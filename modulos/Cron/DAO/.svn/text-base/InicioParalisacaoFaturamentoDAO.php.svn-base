<?php

/**
 * Classe responsável pela persistência de dados
 * @author Marcello Borrmann <marcello.borrmann@meta.com.br>
 * @since 25/03/2015
 * @category Class
 * @package InicioParalisacaoFaturamentoDAO
 */

class InicioParalisacaoFaturamentoDAO {   
	
    private $conn;
 
	/**
	 * MÉTODO PARA SELECIONAR OS REGISTROS EM QUE A DATA DE INÍCIO DE PARALISAÇÃO, CORRESPONDAM AO DIA 1°, ATUAL
	 *
	 * @param text $assunto Assunto do email
	 * @return stdClass
	 * @throws ErrorException
	 */
	public function pesquisarParametro(){

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
					parfdt_ini_cobranca::date = NOW()::date 
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
					veivisualizacao_sasweb = 'f',
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