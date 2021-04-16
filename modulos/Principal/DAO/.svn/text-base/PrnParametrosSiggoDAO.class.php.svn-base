<?php
/**
* @author   Leandro Alves Ivanaga
* @email    leandroivanaga@brq.com
* @since    26/08/2013
* */

/**
 * Trata requisições de ações relacionadas a tabela parametros_gerais
 */
class PrnParametrosSiggoDAO {

    /**
     * Link de conexão com o banco
     * @property resource
     */
    public $conn;


    /**
     * Construtor
     * @param resource $conn - Link de conexão com o banco
     */
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getValorParametros($params = array()) {
    		
    	$where = array();
    	if (!empty($params['id_tipo_proposta']) || strlen($params['id_tipo_proposta'])){
    		$where[] = " parstppoid = {$params['id_tipo_proposta']} ";	
    	}

    	if (!empty($params['id_subtipo_proposta']) || strlen($params['id_subtipo_proposta'])){
    		$where[] = " parssubtipo_tppoid = {$params['id_subtipo_proposta']} ";
    	}

    	if (!empty($params['id_tipo_contrato']) || strlen($params['id_tipo_contrato'])){
    		$where[] = " parstpcoid = {$params['id_tipo_contrato']} ";
    		
    	}

    	if (!empty($params['id_equipamento_classe']) || strlen($params['id_equipamento_classe'])){
    		$where[] = " parseqcoid = {$params['id_equipamento_classe']} ";
    		 
    	}

    	if (!empty($params['nome_parametro']) || strlen($params['nome_parametro'])){
    		$where[] = " parsnome ilike '{$params['nome_parametro']}' ";
    		 
    	}
    	
    	$sqlWhere = implode($where, " AND ");
    	

    	$sqlBusca =
    		"SELECT
    			parsoid, parstppoid, parssubtipo_tppoid, parstpcoid, parseqcoid, parsnome, parsvalor
    		FROM parametros_siggo
    		";
    		
    	if ($sqlWhere) {
    		$sqlBusca .= " WHERE " . $sqlWhere;
    	}
    	    		
    	
    	$sqlBusca .= "LIMIT 1;";

    	$query = pg_query($this->conn, $sqlBusca);
    	    	 
    	$result = pg_fetch_object($query);
    	
    	return $result;
    }
}