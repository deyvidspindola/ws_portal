<?php

/**
 * Classe responsável pelo CRUD do controle de falhas de acessórios 
 * @author andre.zilz <andre.zilz@meta.com.br>
 * @package Cadastro 
 * @since 07/06/2013
 */

class CadControleFalhasAcessoriosDAO {
	
	protected $conn;
	
	/**
	 * Construtor da Classe
	 * @param $conn
	 */
	public function __construct($conn) {
		 $this->conn = $conn;
	}
	
	/**
	 * Inicia uma transação com o banco de dados
	 */
	public function abrirTransacao() {
		pg_query($this->conn, "BEGIN;");
	}
	
	/**
	 * Comita uma transação com o banco de dados
	 */
	public function fecharTransacao() {
		pg_query($this->conn, "COMMIT");
	}
	
	/**
	 * Aborta uma transação com o banco de dados
	 */
	public function abortarTransacao() {
		pg_query($this->conn, "ROLLBACK;");
	}
	
	/**
	 * Executa uma consulta (query)
	 *
	 * @param string $sql	  
	 * @return recordset
	 */
	public function executarQuery($sql) {
		
		$rs = pg_query($this->conn, $sql);
	
		//Implantar se quiser retorno de erro do banco
		/*
		if (!$rs) {
			throw new Exception("Falha de banco de dados");
		}
		*/
		 
		return $rs;
	}
	
	
	/**
	 * Realiza a busca de dados referentes ao o item de acao laboratório
	 * @param int $codigo
	 * @param string $descricao
	 * @return array:
	 */
	public function pesquisarItemAcao($codigo, $descricao=''){
		
		$codigo = (int)$codigo;
		$descricao = pg_escape_string($descricao);
		$listaItens = array();
		$i = 0;
		
		$sql = "SELECT 		
							ifaoid AS codigo, 
							ifadescricao AS descricao
				FROM 		
							item_falha_acao
				WHERE 		
							ifadt_exclusao IS NULL
				AND 		
							ifaimotoid = ".$codigo."
				AND 		
							TRIM(ifadescricao) ILIKE '%".$descricao."%'
				ORDER BY	
							ifaoid		
				";
	
		$rs = $this->executarQuery($sql);
		
		while($row = pg_fetch_object($rs)) {
				
			$listaItens[$i]['codigo'] 		= $row->codigo;			
			$listaItens[$i]['descricao']	= $row->descricao;
			
			$i++;
		}
		
		return $listaItens;		
	}
	
	/**
	 * Realiza a busca de dados referentes ao o item de Componente afetado
	 * @param int $codigo
	 * @param string $descricao
	 * @return array:
	 */
	public function pesquisarItemComponente($codigo, $descricao=''){
	
		$codigo = (int)$codigo;
		$descricao = pg_escape_string($descricao);
		$listaItens = array();
		$i = 0;
	
		$sql = "SELECT 		
							ifcoid AS codigo, 
							ifcdescricao AS descricao
				FROM 		
							item_falha_componente
				WHERE 		
							ifcdt_exclusao IS NULL
				AND 		
							ifcimotoid = ".$codigo."
				AND 		
							TRIM(ifcdescricao) ILIKE '%".$descricao."%'
				ORDER BY	
							ifcoid
				";
		
		$rs = $this->executarQuery($sql);
	
		while($row = pg_fetch_object($rs)) {
		
			$listaItens[$i]['codigo']		= $row->codigo;			
			$listaItens[$i]['descricao'] 	= $row->descricao;			
				
			$i++;
		}

		return $listaItens;
	}	
	
	/**
	 * Realiza a busca de dados referentes ao o item de defeito laboratório
	 * @param int $codigo
	 * @param string $descricao
	 * @return array:
	 */
	public function pesquisarItemDefeito($codigo, $descricao=''){
	
		$codigo = (int)$codigo;
		$descricao = pg_escape_string($descricao);
		$listaItens = array();
		$i = 0;
	
		$sql = "SELECT 		
							ifdoid AS codigo, 
							ifddescricao AS descricao
				FROM 		
							item_falha_defeito
				WHERE 		
							ifddt_exclusao IS NULL
				AND 		
							ifdimotoid = ".$codigo."
				AND 		
							TRIM(ifddescricao) ILIKE '%".$descricao."%'
				ORDER BY	
							ifdoid
				";
	
		$rs = $this->executarQuery($sql);
	
		while($row = pg_fetch_object($rs)) {
			
			$listaItens[$i]['codigo'] 		= $row->codigo;			
			$listaItens[$i]['descricao'] 	= $row->descricao;			
				
			$i++;
		}
	
		return $listaItens;
	}	
	 
	/**
	 * Inativa um itemd e acao laboratório
	 * @param int $codigo
	 */
	public function inativarItemAcao($codigo){
		
		if(empty($codigo)){
			return false;
		}
		
		$codigo = (int)$codigo;
		
		$sql = "UPDATE 	
						item_falha_acao
				SET 	
						ifadt_exclusao = NOW()
				WHERE 	
						ifaoid = ".$codigo ."
				";
		
		if($rs = $this->executarQuery($sql)){
			
			if (pg_affected_rows($rs) > 0){ 
				$retorno = true;
			}
		}
		
		return $retorno;
		
	}	
	
	/**
	 * Inativa um itemd e acao laboratório
	 * @param int $codigo
	 */
	public function inativarItemComponente($codigo){
	
		if(empty($codigo)){
			return false;
		}
		
		$codigo = (int)$codigo;
	
		$sql = "UPDATE 	
						item_falha_componente
				SET 	
						ifcdt_exclusao = NOW()
				WHERE 	
						ifcoid = ".$codigo ."
				";

		if($rs = $this->executarQuery($sql)){
			
			if (pg_affected_rows($rs) > 0){ 
				$retorno = true;
			}
		}
		
		return $retorno;
	
	}
	
	/**
	 * Inativa um itemd e acao laboratório
	 * @param int $codigo
	 */
	public function inativarItemDefeito($codigo){
			
		if(empty($codigo)){
			return false;
		}
		
		$codigo = (int)$codigo;
	
		$sql = "UPDATE 	
						item_falha_defeito
				SET 	
						ifddt_exclusao = NOW()
				WHERE 	
						ifdoid = ".$codigo ."
				";
	
		if($rs = $this->executarQuery($sql)){
			
			if (pg_affected_rows($rs) > 0){ 
				$retorno = true;
			}
		}
		
		return $retorno;
	
	}
	
	/**
	 * Insere no banco um novo item acao laboratório
	 * @param int $codigo
	 * @param string $descricao
	 * @return boolean
	 */
	public function inserirItemAcao($codigo, $descricao){
	
		if(empty($codigo) || empty($descricao)){
			return false;
		}
		
		$descricao 	= pg_escape_string($descricao);
		$codigo		= (int)$codigo;
		
		$sql = "
			INSERT INTO	 
						item_falha_acao 
										(
											ifaoid, 
											ifaeproid, 
											ifadescricao, 
											ifadt_cadastro, 
											ifadt_exclusao, 
											ifaimotoid
										)
						VALUES 
										(
											DEFAULT, 
											NULL, 
											'".$descricao."', 
											NOW(),
											NULL, 
											".$codigo."
										)
			";

		if($rs = $this->executarQuery($sql)){
			
			if (pg_affected_rows($rs) > 0){ 
				$retorno = true;
			}
		}
		
		return $retorno;
		
	}
	
	/**
	 * Insere no banco um novo item componente
	 * @param int $codigo
	 * @param string $descricao
	 * @return boolean
	 */
	public function inserirItemComponente($codigo, $descricao){
	
		if(empty($codigo) || empty($descricao)){
			return false;
		}
	
		$descricao 	= pg_escape_string($descricao);
		$codigo		= (int)$codigo;
	
		$sql = "
			INSERT INTO	 
						item_falha_componente
											(
												ifcoid, 
												ifceproid, 
												ifcdescricao, 
												ifcdt_cadastro, 
												ifcdt_exclusao, 
												ifcimotoid
											)
						VALUES
											(
												DEFAULT, 
												NULL, 
												'".$descricao."', 
												NOW(),
												NULL, 
												".$codigo."
											)
			";
	
		if($rs = $this->executarQuery($sql)){
			
			if (pg_affected_rows($rs) > 0){ 
				$retorno = true;
			}
		}
		
		return $retorno;
	
	}
	
	/**
	 * Insere no banco um novo item defeito laboratório
	 * @param int $codigo
	 * @param string $descricao
	 * @return boolean
	 */
	public function inserirItemDefeito($codigo, $descricao){
	
		if(empty($codigo) || empty($descricao)){
			return false;
		}
	
		$descricao 	= pg_escape_string($descricao);
		$codigo		= (int)$codigo;
	
		$sql = "
			INSERT INTO	 
						item_falha_defeito
										(
											ifdoid, 
											ifdeproid, 
											ifddescricao, 
											ifddt_cadastro, 
											ifddt_exclusao, 
											ifdflag, 
											ifdimotoid)
						VALUES
										(
											DEFAULT, 
											NULL, 
											'".$descricao."', 
											NOW(),
											NULL, 
											DEFAULT,
											".$codigo."
										)
			";

		if($rs = $this->executarQuery($sql)){
			
			if (pg_affected_rows($rs) > 0){ 
				$retorno = true;
			}
		}

		return $retorno;

	}
	
}