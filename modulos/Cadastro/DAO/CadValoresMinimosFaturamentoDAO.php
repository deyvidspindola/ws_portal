<?php 
 
class CadValoresMinimosFaturamentoDAO {
	private $conn;
	
	public $vmfoid;
	public $vmfvl_acionamento;
	public $vmfqtd_min_acionamento;
	public $vmfqtd_max_acionamento; 
	public $vmfvl_localizacao_web;
	public $vmfvl_localizacao_solicitada;
	public $vmfvl_bloqueio_solicitado;
	public $vmfvl_faturamento_minimo;
	public $vmfusuoid_cadastro;

	
	public function CadValoresMinimosFaturamentoDAO($conn) {
		$this->conn = $conn;
	}

	/**
	 * Retorna os valores referentes ao regitro 
	 * 
	 * @param unknown_type $valores
	 * @return array
	 */
	public function recuperar() {
		$retorno = array();
		
		$sql = "SELECT 
					*
				FROM  
					valores_minimos_faturamento
				WHERE
					vmfdt_exclusao IS NULL
				LIMIT 1";
		 
		if (!$res = pg_query($this->conn,$sql)) { 	
			throw new Exception("Falha ao pesquisar valores",1);
		}
		
		 return $retorno = pg_fetch_assoc($res);
	} 
		 
	public function salvar() {
		
		if (!empty($this->vmfoid) && $this->vmfoid > 0) {
			$sqlUpdate ="	UPDATE 
								valores_minimos_faturamento
							SET  
								 vmfdt_exclusao = now(),
	     					 	 vmfusuoid_exclusao = ".$this->vmfusuoid_cadastro."
							 WHERE
							 	 vmfoid = ".$this->vmfoid;
			
		
		
			if (!$resUpdate = pg_query($this->conn, $sqlUpdate)) {
				throw new Exception("Falha ao salvar valores",1);
			}
		}
	
	 $sqlInsert = "	INSERT INTO valores_minimos_faturamento (
							vmfvl_acionamento,
							vmfqtd_min_acionamento,
							vmfqtd_max_acionamento,
							vmfvl_localizacao_web,
							vmfvl_localizacao_solicitada,
							vmfvl_bloqueio_solicitado,
							vmfvl_faturamento_minimo,
							vmfusuoid_cadastro) VALUES (
							".$this->vmfvl_acionamento.",
							".$this->vmfqtd_min_acionamento.",
							".$this->vmfqtd_max_acionamento.",
							".$this->vmfvl_localizacao_web.",
							".$this->vmfvl_localizacao_solicitada.",
					        ".$this->vmfvl_bloqueio_solicitado.",
					        ".$this->vmfvl_faturamento_minimo.",
					        ".$this->vmfusuoid_cadastro."
				)";
		
		if (!$resInsert = pg_query($this->conn, $sqlInsert)) {
			throw new Exception("Falha ao salvar valores".pg_last_error($resInsert),1);
		}
			
	}	

	
}

?>