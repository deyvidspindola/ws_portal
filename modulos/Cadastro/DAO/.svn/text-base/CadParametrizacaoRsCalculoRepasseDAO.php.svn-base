<?php

/**
 * Classe CadParametrizacaoRsCalculoRepasseDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   Antoneli Tokarski <antoneli.tokarski@meta.com.br>
 *
 */
class CadParametrizacaoRsCalculoRepasseDAO {

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

		$and = !empty($parametros->prscroid) ? " AND hrscrprscroid = ".$parametros->prscroid."" : '';

		$sql = "
				SELECT
					hrscrfaixa_inicial,
					hrscrfaixa_final,
					hrscrrevenue_share_vivo,
					hrscrrevenue_share_sascar,
					hrscrpreco_minimo,
					hrscrincremento_valor,
					hrscrprscroid as prscroid,
					CASE
						WHEN hrscracao = 'I' THEN
							'Inclusão'
						WHEN hrscracao = 'E' THEN
							'Exclusão'
						ELSE
							'Alteração'
					END AS acao,
					hrscrdt_cadastro,
					nm_usuario AS usuario
				FROM
					historico_rs_calculo_repasse
				INNER JOIN
					usuarios ON cd_usuario = hrscrusuoid_acao
				WHERE
					hrscrdt_cadastro BETWEEN '".$parametros->data_inicial." 00:00:00' AND '".$parametros->data_final." 23:59:59'
				".$and."
				ORDER BY
					hrscroid,
					hrscrdt_cadastro
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
					parametrizacao_rs_calculo_repasse
				WHERE
					prscrdt_exclusao IS NULL
				ORDER BY
					prscrfaixa_inicial;
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
					MAX(hrscrdt_cadastro) AS hrscrdt_cadastro
				FROM
					historico_rs_calculo_repasse
				WHERE
					hrscracao = 'I'
				";



		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if(pg_num_rows($rs) > 0) {
			$row = pg_fetch_object($rs);
		}

		return empty($row) ? '' : $row->hrscrdt_cadastro;
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
					parametrizacao_rs_calculo_repasse
				WHERE
					 prscroid =" . intval( $id ) . "";

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}

	/**
	 * Método para realizar a pesquisa apenas do ultimo registro
	 *
	 * @param int $id Identificador único do registro
	 * @return stdClass
	 * @throws ErrorException
	 */
	public function buscarUltimoRegistro(){

		$retorno = new stdClass();

		$sql = "
				SELECT
					*
				FROM
					parametrizacao_rs_calculo_repasse
				WHERE
					prscrdt_exclusao IS NULL
				ORDER BY
					prscroid DESC
				LIMIT 1
				";

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

		$dados->prscrfaixa_inicial = str_replace(',', '.', str_replace('.', '', $dados->prscrfaixa_inicial));
		$dados->prscrfaixa_final = str_replace(',', '.', str_replace('.', '', $dados->prscrfaixa_final));
		$dados->prscrrevenue_share_vivo = str_replace(',', '.', $dados->prscrrevenue_share_vivo);
		$dados->prscrrevenue_share_sascar = str_replace(',', '.', $dados->prscrrevenue_share_sascar);
		$dados->prscrpreco_minimo = str_replace(',', '.', str_replace('.', '', $dados->prscrpreco_minimo));
		$dados->prscrincremento_valor = str_replace(',', '.', str_replace('.', '', $dados->prscrincremento_valor));

		$sql = "INSERT INTO
							parametrizacao_rs_calculo_repasse
							(
								prscrfaixa_inicial,
								prscrfaixa_final,
								prscrrevenue_share_vivo,
								prscrrevenue_share_sascar,
								prscrpreco_minimo,
								prscrincremento_valor,
								prscrdt_cadastro,
								prscrusuoid_cadastro
							)
						VALUES
							(
								".$dados->prscrfaixa_inicial.",
								".$dados->prscrfaixa_final.",
								".$dados->prscrrevenue_share_vivo.",
								".$dados->prscrrevenue_share_sascar.",
								".$dados->prscrpreco_minimo.",
								".$dados->prscrincremento_valor.",
								NOW(),
								".$dados->usuoid."

							)
							RETURNING prscroid";

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$dados->prscroid = pg_fetch_result($rs, 0, 'prscroid');

		$this->inserirHistorico($dados, 'I');

		return true;
	}

	/**
	 * Responsável para inserir um registro no banco de dados.
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function inserirHistorico(stdClass $dados, $operacao){

		$sql = "INSERT INTO
							historico_rs_calculo_repasse
							(
								hrscrfaixa_inicial,
								hrscrfaixa_final,
								hrscrrevenue_share_vivo,
								hrscrrevenue_share_sascar,
								hrscrpreco_minimo,
								hrscrincremento_valor,
								hrscrprscroid,
								hrscracao,
								hrscrdt_cadastro,
								hrscrusuoid_acao
							)
						VALUES
							(
								".$dados->prscrfaixa_inicial.",
								".$dados->prscrfaixa_final.",
								".$dados->prscrrevenue_share_vivo.",
								".$dados->prscrrevenue_share_sascar.",
								".$dados->prscrpreco_minimo.",
								".$dados->prscrincremento_valor.",
								".$dados->prscroid.",
								'".$operacao."',
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

		$dados->prscrfaixa_inicial = str_replace(',', '.', str_replace('.', '', $dados->prscrfaixa_inicial));
		$dados->prscrfaixa_final = str_replace(',', '.', str_replace('.', '', $dados->prscrfaixa_final));
		$dados->prscrrevenue_share_vivo = str_replace(',', '.', $dados->prscrrevenue_share_vivo);
		$dados->prscrrevenue_share_sascar = str_replace(',', '.', $dados->prscrrevenue_share_sascar);
		$dados->prscrpreco_minimo = str_replace(',', '.', str_replace('.', '', $dados->prscrpreco_minimo));
		$dados->prscrincremento_valor = str_replace(',', '.', str_replace('.', '', $dados->prscrincremento_valor));

		$sql = "UPDATE
							parametrizacao_rs_calculo_repasse
						SET
							prscrfaixa_inicial = ".$dados->prscrfaixa_inicial.",
							prscrfaixa_final = ".$dados->prscrfaixa_final.",
							prscrrevenue_share_vivo = ".$dados->prscrrevenue_share_vivo.",
							prscrrevenue_share_sascar = ".$dados->prscrrevenue_share_sascar.",
							prscrpreco_minimo = ".$dados->prscrpreco_minimo.",
							prscrincremento_valor = ".$dados->prscrincremento_valor."

						WHERE
							 prscroid = " . $dados->prscroid . "";

		if (!pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$this->inserirHistorico($dados, 'A');

		return true;
	}

	/**
	 * Exclui (UPDATE) um registro da base de dados.
	 * @param int $id Identificador do registro
	 * @return boolean
	 * @throws ErrorException
	 */
	public function excluir($id, $dados){

		$sql = "
				UPDATE
					parametrizacao_rs_calculo_repasse
				SET
					 prscrdt_exclusao = NOW(),
					 prscrusuoid_exclusao = ".$dados->usuoid."
				WHERE
					 prscroid = " . intval( $id ) . "";

		if (!pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$this->inserirHistorico($dados, 'E');

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

