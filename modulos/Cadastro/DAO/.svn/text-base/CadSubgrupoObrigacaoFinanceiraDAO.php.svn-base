<?php

Class CadSubgrupoObrigacaoFinanceiraDAO {

	const ERRO_QUERY_SELECT = 'Falha ao obter informações do banco de dados';
	const ERRO_QUERY_INSERT = 'Falha ao inserir as informações do banco de dados';
	const ERRO_QUERY_UPDATE = 'Falha ao atualizar as informações do banco de dados';

	private $conn;

	public function __construct($conn){
		$this->conn = $conn;
	}

	public function getSubgruposObrigacaoFinanceira($descricao = NULL){
		$offset = $limit * $offset;
		$descricao = preg_replace("/[^a-zA-Z0-9áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ\-\/ ]/", "", $descricao);
		$sql = "
			SELECT
				ofsgoid,
				ofsgdescricao,
				ofsgdt_cadastro,
				ofsgusuoid_cadastro,
				ofsgdt_exclusao,
				ofsgusuoid_exclusao,
				ofsgstatus
			FROM
				obrigacao_financeira_sub_grupo
			WHERE ofsgdescricao ILIKE '%".pg_escape_string($descricao)."%'
			ORDER BY ofsgoid ASC";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new Exception(self::ERRO_QUERY_SELECT);
		}
		
		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}
		
		return $retorno;
	}

	public function getSubgruposObrigacaoFinanceiraById($id){

		$sql = "
			SELECT
				ofsgoid,
				ofsgdescricao,
				ofsgdt_cadastro,
				ofsgusuoid_cadastro,
				ofsgdt_exclusao,
				ofsgusuoid_exclusao,
				ofsgstatus
			FROM
				obrigacao_financeira_sub_grupo
			WHERE
				ofsgoid = $id
			LIMIT 1
		";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new Exception(self::ERRO_QUERY_SELECT);
		}

		return pg_num_rows($rs) > 0 ? pg_fetch_object($rs) : false;

	}

	public function inserirSubgrupoObrigacaoFinanceira($descricao, $status, $usuario){
		$descricao = preg_replace("/[^a-zA-Z0-9áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ\-\/ ]/", "", $descricao);
		$sql = "
			INSERT INTO
				obrigacao_financeira_sub_grupo
				(ofsgdescricao, ofsgstatus, ofsgusuoid_cadastro)
			VALUES
				($1, $status, $usuario)
			RETURNING ofsgoid
		";

		$result = pg_prepare($this->conn, "query_descricao", $sql);
		if(!$query_descricao = pg_execute($this->conn,"query_descricao", array(html_entity_decode($descricao)))){
			throw new Exception(self::ERRO_QUERY_INSERT);
        }

		return pg_fetch_object($query_descricao);

	}

	public function atualizarSubgrupoObrigacaoFinanceira($id, $descricao, $status){
		$descricao = preg_replace("/[^a-zA-Z0-9áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ\-\/ ]/", "", $descricao);
		$sql = "
			UPDATE
				obrigacao_financeira_sub_grupo
			SET
				ofsgdescricao = $1,
				ofsgstatus = $status
			WHERE
				ofsgoid = $id
		";

		$result = pg_prepare($this->conn, "query_descricao", $sql);
		if(!$query_descricao = pg_execute($this->conn,"query_descricao", array(html_entity_decode($descricao)))){
			throw new Exception(self::ERRO_QUERY_UPDATE);
        }

		return true;

	}
	
}