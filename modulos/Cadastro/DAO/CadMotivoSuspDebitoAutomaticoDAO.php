<?php

/**
 * CadMotivoSuspDebitoAutomaticoDAO.php
 *
 * Classe para persistencia de dados do cadastro de motivos de suspensão
 *
 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
 * @package Cadastro
 * @since 27/09/2012
 *
 */
class CadMotivoSuspDebitoAutomaticoDAO
{
	
	private $conn;
	
	/**
	 * Efetua a pesquisa do relatório 
	 *
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function pesquisar($descricao){
		
		$sql = "SELECT
					msdaoid as id,
					msdadescricao as descricao,
					msdadt_cadastro as data_cadastro
				FROM
					motivo_susp_debito_automatico
				WHERE
					msdadt_exclusao IS NULL
				AND
					msdadescricao ILIKE '%$descricao%'";
		if(!$rs = pg_query($this->conn, $sql)){
			throw new Exception('ERRO: Falha de conexão ao tentar realizar a pesquisa.');
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
	public function getByName($descricao){
	
		$sql = "SELECT
					msdaoid as id,
					msdadescricao as descricao,
					msdadt_cadastro as data_cadastro
				FROM
					motivo_susp_debito_automatico
				WHERE
					msdadt_exclusao IS NULL
				AND
					msdadescricao = '$descricao'";
		if(!($rs = pg_query($this->conn, $sql))){
			throw new Exception('ERRO: Falha de conexão ao tentar realizar a pesquisa.');
		}
		
		return pg_num_rows($rs);
	}
	
	/**
	 * Exclui o motivo de suspensão selecionado 
	 *
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function excluir($id){
		
		$sql = "UPDATE
					motivo_susp_debito_automatico
				SET	
					msdadt_exclusao = NOW()
				WHERE
					msdaoid = $id";
		if(!pg_query($this->conn, $sql)){
			throw new Exception('ERRO: Falha de conexão ao excluir o registro.');
		}
		
		return array('error' => false);
		
	}
	
	/**
	 * Cadastra o motivo de suspensão
	 *
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function cadastrar($motivo){
		
		$sql = "INSERT INTO 
					motivo_susp_debito_automatico (msdadescricao, msdadt_cadastro)
				VALUES 
					('$motivo', NOW())";
		if(!pg_query($this->conn, $sql)){
			throw new Exception('ERRO: Falha de conexão ao inserir o registro.');
		}
		
		return array('error' => false);
	}
	
	/**
	 * Construtor
	 *
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function CadMotivoSuspDebitoAutomaticoDAO($conn){
	
		$this->conn = $conn;
	
	}
}