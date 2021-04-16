<?php

/**
 * Classe CadParametrizacaoRsCalculoRepasseDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   Antoneli Tokarski <antoneli.tokarski@meta.com.br>
 *
 */
class CadRetencaoImpostosDAO {

	/**
	 * Conexão com o banco de dados
	 * @var resource
	 */
	private $conn;

	/**
	 * Mensagem de erro para o processamentos dos dados
	 * @const String
	 */
	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";


	public function __construct($conn) {
		//Seta a conexão na classe
		$this->conn = $conn;
	}

	/**
	 * Método para realizar a pesquisa de varios registros
	 * @param stdClass $parametros Filtros da pesquisa
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisarHistorico(stdClass $parametros){

		$retorno = array();

		$sql = "
				SELECT
					hrsriiss,
					hrsripis,
					hrsricofins,
					hrsrivalor_chip,
					hrsridt_cadastro,
					hrsriusuoid_cadastro,
					nm_usuario AS usuario
				FROM
					historico_rs_retencao_impostos
				INNER JOIN
					usuarios ON cd_usuario = hrsriusuoid_cadastro
				ORDER BY
					hrsrioid DESC,
					hrsridt_cadastro
				";



		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($row = pg_fetch_object($rs)){
			$retorno[] = $row;
		}

		return $retorno;
	}

	/**
	 * Método para realizar a pesquisa de varios registros
	 * @param stdClass $parametros Filtros da pesquisa
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisar(stdClass $parametros){

		$retorno = array();

		$sql = "
				SELECT
					*
				FROM
					parametrizacao_rs_retencao_impostos
				";



		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($row = pg_fetch_object($rs)){
			$retorno[] = $row;
		}

		return $retorno;
	}

	/**
	 * Método para realizar a pesquisa de varios registros
	 * @param stdClass $parametros Filtros da pesquisa
	 * @return array
	 * @throws ErrorException
	 */
	public function buscarDataUltimoHistorico(){

		$row = '';

		$sql = "
				SELECT
					MAX(hrsridt_cadastro) AS hrsridt_cadastro
				FROM
					historico_rs_retencao_impostos
				";



		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if(pg_num_rows($rs) > 0) {
			$row = pg_fetch_object($rs);
		}

		return empty($row) ? '' : $row->hrsridt_cadastro;
	}

	/**
	 * Método para realizar a pesquisa de apenas um registro.
	 *
	 * @param int $id Identificador único do registro
	 * @return stdClass
	 * @throws ErrorException
	 */
	public function pesquisarPorID($id){

		$retorno = new stdClass();

		$sql = "
				SELECT
					*
				FROM
					parametrizacao_rs_retencao_impostos
				WHERE
					 prsrioid =" . intval( $id ) . "";

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}

	/**
	 * Responsável para inserir um registro no banco de dados.
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function inserir(stdClass $dados){

		$dados->prsriiss = str_replace(',', '.', $dados->prsriiss);
		$dados->prsripis = str_replace(',', '.', $dados->prsripis);
		$dados->prsricofins = str_replace(',', '.', $dados->prsricofins);
		$dados->prsrivalor_chip = str_replace(',', '.', str_replace('.', '', $dados->prsrivalor_chip));

		$sql = "INSERT INTO
							parametrizacao_rs_retencao_impostos
							(
								prsriiss,
								prsripis,
								prsricofins,
								prsrivalor_chip
							)
						VALUES
							(
								".$dados->prsriiss.",
								".$dados->prsripis.",
								".$dados->prsricofins.",
								".$dados->prsrivalor_chip."

							)
							RETURNING prsrioid";

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$dados->prscroid = pg_fetch_result($rs, 0, 'prsrioid');

		$this->inserirHistorico($dados);

		return true;
	}

	/**
	 * Responsável para inserir um registro no banco de dados.
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function inserirHistorico(stdClass $dados){

		$sql = "INSERT INTO
							historico_rs_retencao_impostos
							(
								hrsriiss,
								hrsripis,
								hrsricofins,
								hrsrivalor_chip,
								hrsridt_cadastro,
								hrsriusuoid_cadastro
							)
						VALUES
							(
								".$dados->prsriiss.",
								".$dados->prsripis.",
								".$dados->prsricofins.",
								".$dados->prsrivalor_chip.",
								NOW(),
								".$dados->usuoid."
							)";

		if (!pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		return true;
	}

	/**
	 * Responsável por atualizar os registros
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function atualizar(stdClass $dados){

		$dados->prsriiss = str_replace(',', '.', $dados->prsriiss);
		$dados->prsripis = str_replace(',', '.', $dados->prsripis);
		$dados->prsricofins = str_replace(',', '.', $dados->prsricofins);
		$dados->prsrivalor_chip = str_replace(',', '.', str_replace('.', '', $dados->prsrivalor_chip));

		$sql = "
				UPDATE
					parametrizacao_rs_retencao_impostos
				SET
					prsriiss = ".$dados->prsriiss.",
					prsripis = ".$dados->prsripis.",
					prsricofins = ".$dados->prsricofins.",
					prsrivalor_chip = ".$dados->prsrivalor_chip."
				WHERE
				 	prsrioid = " . $dados->prsrioid . "";

		if (!pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$this->inserirHistorico($dados);

		return true;
	}

	/**
	 * Abre a transação
	 */
	public function begin(){
		pg_query($this->conn, 'BEGIN');
	}

	/**
	 * Finaliza um transação
	 */
	public function commit(){
		pg_query($this->conn, 'COMMIT');
	}

	/**
	 * Aborta uma transação
	 */
	public function rollback(){
		pg_query($this->conn, 'ROLLBACK');
	}


}

