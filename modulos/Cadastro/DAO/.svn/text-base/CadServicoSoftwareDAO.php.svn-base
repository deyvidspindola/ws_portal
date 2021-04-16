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
class CadServicoSoftwareDAO
{
	
	private $conn;
	
	/**
	 * Efetua a pesquisa do relatório 
	 *
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function pesquisar($descricao){
		
		$sql = "SELECT
					srsoid as id,
					srsdescricao as descricao,
					srsdt_cadastro as data_cadastro
				FROM
					servico_software
				WHERE
					srsdt_exclusao IS NULL
				AND
					srsdescricao ILIKE '%$descricao%'
				ORDER BY
					srsdescricao";
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
	public function getByName($descricao){
		
		$descricao = pg_escape_string(stripslashes($descricao));
		
		$sql = "SELECT
					srsoid as id,
					srsdescricao as descricao,
					srsdt_cadastro as data_cadastro
				FROM
					servico_software
				WHERE
					srsdt_exclusao IS NULL
				AND
					LOWER(srsdescricao) = LOWER('$descricao')";
		if(!($rs = pg_query($this->conn, $sql))){
			throw new Exception('001');
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
					servico_software
				SET	
					srsdt_exclusao = NOW()
				WHERE
					srsoid = $id";
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
					servico_software (srsdescricao, srsdt_cadastro)
				VALUES 
					('$servico', NOW())
				RETURNING srsoid";
		if(!$qr = pg_query($this->conn, $sql)){
			throw new Exception('001');
		}
		
		$id = pg_fetch_result($qr, 0, 'srsoid');
		
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
	public function CadServicoSoftwareDAO($conn){
	
		$this->conn = $conn;
	
	}
}