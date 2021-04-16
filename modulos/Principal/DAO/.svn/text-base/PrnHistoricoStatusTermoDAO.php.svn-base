<?php

/**
 * Classe de persistência de dados
 * 
 * @author Angelo Frizzo Junior<angelo.frizzo@meta.com.br>
 * 
 */
class PrnHistoricoStatusTermoDAO {


    private $conn;
        
    function __construct($conn) {

        $this->conn = $conn;        
    }
    
    public function begin() {
    	pg_query($this->conn, "BEGIN;");
    }
    
    
    public function commit() {
    	pg_query($this->conn, "COMMIT");
    }
    
    
    public function rollback() {
    	pg_query($this->conn, "ROLLBACK;");
    }
    
	public function pesquisarStatusTermo($filtros = array()){
	
    		$filtro = "";  
    		
    		if (isset($filtros['contrato']) && strlen($filtros['contrato'])) {
    			
    			/*
    			 * Verifica se contrato existe na tabela contrato 
    			 */
				$sql = "
					SELECT
						connumero
					FROM
						contrato
					WHERE
						connumero = {$filtros['contrato']} 
				";
				
				$rs = pg_query($this->conn, $sql);
				
				if (!$rs) {
					throw new Exception('Houve um erro ao realizar a pesquisa de Contrato.');
				}
				
				$linhas_contrato = pg_num_rows($rs);
				
				if (empty($linhas_contrato)) {
					throw new Exception('Esse número de Contrato não existe no sistema.');
				}
				
				/*
				 * Busca historicos utilizando número de contrato selecionado
				 */
				$filtro = " AND hitconnumero = {$filtros['contrato']} ";
    			
    			$modulo_contrato = $filtros['contrato'] % 10;

	    		$sql = "
	    			SELECT
	    				TO_CHAR(hitdt_acionamento,'DD/MM/YYYY HH24:MI') AS data_acionamento,
	    				nm_usuario AS usuario,
	    				csidescricao AS status 
	    			FROM
	    				historico_termo$modulo_contrato
	    				INNER JOIN contrato_situacao ON csioid = hitconcsioid
	    				INNER JOIN usuarios ON cd_usuario = hitusuoid 
	    			WHERE 
	    				hitconcsioid > 0  	
		    			$filtro    		 
	    			ORDER BY hitdt_acionamento DESC
	    		";
	    		$rs = pg_query($this->conn, $sql);
	    		 
	    		if (!$rs) {
	    			throw new Exception('Houve um erro ao realizar a pesquisa de Histórico.');
	    		}
	    		
	    		return $rs;
    		} 
    		//else {
    		//	throw new Exception('O campo "Contrato" deve ser preenchido.');
    		//}
	
	}

}