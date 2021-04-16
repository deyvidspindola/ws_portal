<?php

/**
 * @author Dyorg Almeida <dyorg.almeida@meta.com.br>
 */
class RelConsumoSatelitalDAO {
	
	private $conn;
	
	public function RelConsumoSatelitalDAO() {
	
		global $conn;
	
		$this->conn = $conn;
	}
	
	/**
	 * Busca provedores para preencher combobox
	 *
	 * @author Dyorg Almeida <dyorg.almeida@meta.com.br>
	 * @since 28/12/2012
	 */
	public function listarProvedoresEmPares() {
	
		$sql = "SELECT
					aspioid as id,
					aspidescricao as nome
				FROM
					antena_satelital_provedor_inf
				WHERE
					aspiexclusao IS NULL
				ORDER BY
					aspidescricao";
	
		if(!$rs = pg_query($this->conn, $sql)){
			throw new Exception('ERRO: Falha de conexão ao tentar busca os provedores.');
		}
	
		return pg_fetch_all($rs);
	
	}	

	/**
	 * Busca modelos de equipamento para preencher combobox
	 *
	 * @author Dyorg Almeida <dyorg.almeida@meta.com.br>
	 */
	public function listarModelosEquipamentoEmPares() {
	
		$sql = "SELECT
					asmoid as id,
					asmdescricao as descricao
				FROM
					antena_satelital_modelo
				ORDER BY
					asmdescricao";
		
		if(!$rs = pg_query($this->conn, $sql)){
			throw new Exception('ERRO: Falha de conexão ao tentar busca os modelos de equipamento.');
		}
		
		return pg_fetch_all($rs);
	}
	
}