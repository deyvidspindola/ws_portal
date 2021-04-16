<?php

/**
 * Classe de persistência de dados
 * 
 * @author Vanessa Rabelo <Vanessa.rabelo@meta.com.br>
 * 
 */
class RelIntegracaoMasterDAO {


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
    
	
	
	
		
	public function pesquisarIntegracaoMaster($filtros = array()){
	
	
	    	try {
    		$filtro = "";  
    
    


    		
    		if (isset($filtros['forfornecedor']) && !empty($filtros['forfornecedor'])) {
    			$filtro .= " AND  forfornecedor ILIKE '".$filtros['forfornecedor']."%' ";
    		}
    		 

    		if (isset($filtros['data_inicio_pesquisa']) && !empty($filtros['data_inicio_pesquisa'])    		
    				) {
    			$filtro .= " AND  lsvdt_chamada::date >= ('".$filtros['data_inicio_pesquisa']."')::date ";
    		}
    		
    		 
	    	if (isset($filtros['data_fim_pesquisa']) && !empty($filtros['data_fim_pesquisa'])	    	 
	    		){
    			$filtro .= " AND  lsvdt_chamada::date <=  ('".$filtros['data_fim_pesquisa']."')::date ";
    		}

    		

    	
    		 
    		if (isset($filtros['numero_solicitacao']) && strlen($filtros['numero_solicitacao'])) {
    			$filtro .= " AND lsvnumero_solicitacao = {$filtros['numero_solicitacao']} ";
    		}
    

    		
    		$a=array();
    		
    		if (isset($filtros['webservice']) && !empty($filtros['webservice'])) {
    		
    			$a[]= " lsvmotivo = 'WI' ";
    		
    		}
    		
    		if (isset($filtros['semadto']) && !empty($filtros['semadto'])) {
    		
    			$a[]= " lsvmotivo = 'SA' ";
    		
    		}
    		
    		if (isset($filtros['semreemb']) && !empty($filtros['semreemb'])) {
    		
    			$a[]= " lsvmotivo = 'SR' ";
    		
    		}
    		
    		if (count($a)>0)$filtro .= " and (".implode("or", $a).")";
    		

    		
    		$sql = "
    			SELECT
    				TO_CHAR(lsvdt_chamada,'DD/MM/YYYY') AS data_cad,
    				lsvnumero_solicitacao,
    				forfornecedor, 
    				lsvmotivo,
    				CASE
                        WHEN lsvmotivo='WI' THEN 
                            'WebService Indisponível'
                         WHEN lsvmotivo='SA' THEN 
                            'Sem Adiantamento'
                         WHEN lsvmotivo='SR' THEN 
                            'Sem Reembolso'                       
                         ELSE 'Outros Tipos'
                    END AS motivo
    			FROM
    				log_solicita_viagem
    				LEFT JOIN fornecedores ON lsvdocumento::text = fordocto 
    				LEFT JOIN solicitacao_viagem ON solvnumero_solicitacao = lsvnumero_solicitacao
    				AND solicitacao_viagem.solvchave_solicitacao = log_solicita_viagem.lsvchave_solicitacao 
    				
    			WHERE	
    				true
    				and solvprocessado IS NOT TRUE
    				    
    		$filtro    		 
    			order by lsvdt_chamada desc
    		";


    		
    		$rs = pg_query($this->conn, $sql);
    		 
    		if (!$rs) {
    			throw new Exception('Houve um erro ao realizar a pesquisa.');
    		}
    
    			return array(
    				"erro" => 0,
    		"resultado" => $rs
    		);
    	}
		catch(Exception $e){				
				return array(
				"erro" => 1,
				"mensagem" => $e->getMessage()
		);
		}
	
	}
	

}








