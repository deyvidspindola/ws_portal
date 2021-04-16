<?php

Class CadSubgrupoObrigacaoFinanceiraHistoricoDAO {

	const ERRO_QUERY_SELECT = 'Falha ao obter informações do histórico do banco de dados';
	const ERRO_QUERY_INSERT = 'Falha ao inserir as informações de histórico do banco de dados';
	const ERRO_QUERY_UPDATE = 'Falha ao atualizar as informações de histórico do banco de dados';
	const ACAO_INCLUSAO = 'Inclusão';
	const ACAO_ALTERACAO = 'Alteração';
	const ACAO_INATIVACAO = 'Inativação';
	const ACAO_REATIVACAO = 'Reativação';
	
	private $conn;
	public $usuoid;
	public $acao;

	public function __construct($conn){
		$this->conn = $conn;
	}

	public function inserirHistorico($subgrupoId, $acao, $descricaoAntiga, $descricaoNova){
		$sql = "
			INSERT INTO
				obrigacao_financeira_sub_grupo_historico
				(ofsghofsgoid, 
				ofsghusuoid, 
				ofsghacao, 
				ofsghdescricao_antiga, 
				ofsghdescricao_nova, 
				ofsghdt_alteracao)
			VALUES
				($subgrupoId, 
				$this->usuoid, 
				'$acao', 
				'$descricaoAntiga', 
				'$descricaoNova', 
				NOW())
		";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new Exception(self::ERRO_QUERY_INSERT);
		}

		return true;
	}

	public function getHistorico(){
		$sql = "
			SELECT 
				ofsghoid, 
				ofsghofsgoid, 
				ofsghusuoid, 
				ofsghacao, 
				ofsghdescricao_antiga, 
				ofsghdescricao_nova, 
				ofsghdt_alteracao, 
				usuarios.ds_login 
			FROM 
				obrigacao_financeira_sub_grupo_historico 
			INNER JOIN 
				usuarios ON ofsghusuoid=cd_usuario 
			ORDER BY ofsghdt_alteracao DESC
		";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new Exception(self::ERRO_QUERY_SELECT);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}		
		return $retorno;
	}
}