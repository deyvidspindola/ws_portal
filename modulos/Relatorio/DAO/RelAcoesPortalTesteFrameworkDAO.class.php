<?php
/**
 * Classe DAO para acesso ao banco de dados - Relatorio Acoes do Portal
 */
class RelAcoesPortalTesteFrameworkDAO extends DAO {
	
	/**
	 * Metodo de consulta à Regiao
	 */
	public function getRegiaoDAO(){	
		
        $sql = "SELECT 
                    estoid, estuf
                FROM 
                    estado 
                WHERE 
                    estexclusao IS NULL 
                ORDER BY 
                    estuf";
        /**
         * Metodo select da classe Zend_Db
         */
		return $this->select($sql);	
		
	}
	
	/**
	 * Metodo de consulta ao Grupo de Menu
	 */
	public function getGrupoMenuDAO(){
        $sql = "SELECT 
                    mpaoid,
                    mpadescricao
                FROM
                    menus_portal_atendimento 
                ORDER BY
                    mpadescricao ASC";

        return $this->select($sql);
	}
	
	/**
	 * Metodo de consulta ao Tipo de Contrato
	 */
	public function getTipoContratoDAO(){
		
        $sql = "-- tipo_contrato = Cliente
                    SELECT 
                        tpcoid, tpcdescricao
                    FROM 
                        tipo_contrato 
                    WHERE 
                        tpcseguradora = false 

                    UNION

                    -- tipo_contrato = Seguradora
                    SELECT 
                        tpcoid, tpcdescricao
                    FROM 
                        tipo_contrato 
                    WHERE 
                        tpcseguradora = true 
                    AND 
                        tpcdescricao NOT ILIKE 'Ex-%' 

                    UNION

                    -- tipo_contrato = Ex-Seguradora
                    SELECT 
                        tpcoid, tpcdescricao
                    FROM 
                        tipo_contrato 
                    WHERE
                        tpcseguradora = true 
                    AND 
                        tpcdescricao ILIKE 'Ex-%' 

                    ORDER BY
                    	tpcdescricao ASC";
                    
        return $this->select($sql);
	}
	
	/**
	 * Metodo de consulta às Cidades com parametro da regiao
	 */
	public function getCidadeDAO($estoid){
        $sql = "SELECT
                    cidoid,
                    ciddescricao 
            	FROM 
                    cidade 
            	WHERE 
                    cidexclusao IS NULL 
            	AND 
                    cidestoid=$estoid";
     
        return $this->select($sql);
    }
	
	/**
	 * Metodo de consulta aos Itens de Menu do portal com parametro do grupo de menu
	 */
	public function getItemMenuDAO($mpaoid){
        $sql = "SELECT
                    impaoid,
                    impadescricao
            	FROM
                    itens_menus_portal_atendimento
            	WHERE
                    impadt_exclusao IS NULL
            	AND
                    impampaoid = $mpaoid";

        return $this->select($sql);
	}
	
	/**
	 * Metodo de consulta ao Cliente com parametro nome
	 */
	public function getClienteDAO($nome){	    
	    $sql = "SELECT 
	                clioid AS cliente_id, 
	                clinome AS cliente_nome 
	            FROM 
	                clientes
	            WHERE 
	                clinome ilike '%$nome%' 
	                AND clidt_exclusao IS NULL
	            ORDER BY 
	                clinome";
	    
	    return $this->select($sql);
	}
	
}