<?php

/**
 * CadServicoSoftwareDAO.php
 *
 * Classe para persistencia de dados do cadastro serviços de softwares
 *
 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
 * @package Cadastro
 * @since 27/09/2012
 *
 */
class CadMotivoBackofficeDAO
{
	
	private $conn;
	
	/**
	 * Efetua a pesquisa do relatório 
	 *
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function pesquisar($id){
		
		$sql = "SELECT
					bmsoid as id,
					bmsdescricao as descricao,
					bmsdt_cadastro as data_cadastro
				FROM
					backoffice_motivo_solicitacao
				WHERE
					bmsdt_exclusao IS NULL ";
	
		if (!empty($id)) {
			$sql .= 
				"AND
					bmsoid = " . intval($id);
		}
		
		$sql .="
				ORDER BY
					bmsdescricao";
		
		if(!$rs = pg_query($this->conn, $sql)){
			throw new Exception('001');
		}
		
		if (pg_num_rows($rs) > 0) {
			return pg_fetch_all($rs);
		}
		
		return array();				
	}
	
	
	/**
	 * Efetua a pesquisa do relatório
	 *
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function getByName($descricao, $tipoProcessamento){
		
		$descricao = pg_escape_string(stripslashes($descricao));
		
		//Cadastrar
		if ($tipoProcessamento == 'C') {
			$where = " LOWER(bmsdescricao) = LOWER('$descricao')";
		} else if ($tipoProcessamento == 'A') { //Alterar
			$where = " bmsdescricao = '$descricao'";
		}
		
		$sql = "SELECT
					bmsoid as id,
					bmsdescricao as descricao,
					bmsdt_cadastro as data_cadastro
				FROM
					backoffice_motivo_solicitacao
				WHERE
					bmsdt_exclusao IS NULL
				AND
					$where";
		if(!($rs = pg_query($this->conn, $sql))){
			throw new Exception('001');
		}
		
		return pg_num_rows($rs);
	}
	
	/**
	 * Altera o motivo de suspensão selecionado
	 *
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function alterar($descricao, $id){
		
		$descricao = pg_escape_string(stripslashes($descricao));
		
		$sql = "UPDATE
					backoffice_motivo_solicitacao
				SET
					bmsdescricao = '". $descricao ."'
				WHERE
					bmsoid = $id";
		if(!pg_query($this->conn, $sql)){
			throw new Exception('001');
		}
	
		return array('error' => false);
	
	}
	
	/**
	 * Exclui o motivo de suspensão selecionado 
	 *
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function excluir($id){
		
		$sql = "UPDATE
					backoffice_motivo_solicitacao
				SET	
					bmsdt_exclusao = NOW(),
					bmsusuoid_exclusao = ".intval($_SESSION['usuario']['oid'])."
				WHERE
					bmsoid = $id";
		if(!pg_query($this->conn, $sql)){
			throw new Exception('001');
		}
		
		return array('error' => false);
		
	}
	
	/**
	 * Cadastra o motivo de suspensão
	 *
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function cadastrar($servico){
		
		$servico = pg_escape_string(stripslashes($servico));
		
		$sql = "INSERT INTO 
					backoffice_motivo_solicitacao (bmsdescricao, bmsdt_cadastro, bmsusuoid_cadastro)
				VALUES 
					('$servico', NOW(), ".intval($_SESSION['usuario'][oid]).")
				RETURNING bmsoid";
		if(!$qr = pg_query($this->conn, $sql)){
			throw new Exception('001');
		}
		
		$id = pg_fetch_result($qr, 0, 'bmsoid');
		
		if($id == 0){
			throw new Exception('001');
		}
		
		return array('error' => false, 'id' => $id);
		
	}
	
	/**
	 * Construtor
	 *
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function CadMotivoBackofficeDAO($conn){
	
		$this->conn = $conn;
	
	}
}