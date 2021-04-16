<?php

/**
 * @file CadObrigacao.class.php
 * @author Rafael Mitsuo Moriya 
 * @version 06/09/2013
 * @since 06/09/2013
 * @package SASCAR CadObrigacaoDAO.class.php 
 */

class CadObrigacaoDAO {
    
    private $conn;  
    private $cd_usuario;
    
    public function __construct() {        
        global $conn;
        $this->conn = $conn;   
        $this->cd_usuario = $_SESSION['usuario']['oid'];  
    }
    
    /**
     * Busca os clientes por nome
     */
    public function getClientes($params) {
    	 
    	$sql = "
	    	SELECT
	    		clioid,
    	        clinome
	    	FROM
	    		clientes
	    	WHERE
	    	";
    	 
    	if ($params['cliente_busca'] != "") {
    		$sql .= " clinome ILIKE '%". $params['cliente_busca'] ."%'";
    	}
    	 
    	$sql .= " ORDER BY clinome ASC";

    	$rs = pg_query($this->conn, $sql);
    	 
    	while( $result = pg_fetch_array($rs) ){
    		$array[] = array( 'clioid' => $result['clioid'], 'clinome' => utf8_encode($result['clinome']));
    	}
    	 
    	return $array;
    }

    public function listaClientesExcecao($array){

        if(sizeof($array) > 0){
            $sql = "
                SELECT
                    clioid,
                    clinome
                FROM
                    clientes
                WHERE
                    clioid IN (
                ";

            $x = 1;
            foreach($array as $clioid){
                $sql .= $clioid[0];

                if($x < sizeof($array)){
                    $sql .= ",";
                }
                $x++;
            }
             
            $sql .= ") ORDER BY clinome ASC";
            
            $rs = pg_query($this->conn, $sql);

            $x = 0;

            while($result = pg_fetch_array($rs)){
                $retorno[] = array('clioid' => $result['clioid'],'clinome' => $result['clinome'], 'cliexobrtipo' => $array[$x][1]);
                $x++;
            }
  
            return $retorno;
        }else{
            return array();
        }
    }


    public function listaClientesCadastrados($obroid){

        $sql = "SELECT 
                    cliexobrclioid,
                    cliexobrtipo
                FROM 
                    cliente_excecao_obrigacao
                WHERE
                    cliexobrobroid = '" . $obroid . "'
                    AND cliexobrdt_exclusao IS NULL
                ";

        $rs = pg_query($this->conn, $sql);

        if(pg_num_rows($rs) > 0){
            return pg_fetch_all($rs);   
        }else{
            return array();
        }
    }

    public function insereClienteExcecao($obroid,$arrayClientes){
        
        try{
            foreach($arrayClientes as $cliente){
                $sql = "SELECT 
                            cliexobroid 
                        FROM 
                            cliente_excecao_obrigacao 
                        WHERE 
                            cliexobrobroid = '" . $obroid . "'
                            AND cliexobrclioid = '" . $cliente[0] . "'";

                $rs = pg_query($this->conn, $sql);

                if(pg_num_rows($rs) > 0){
                    $cliexobroid = pg_fetch_result($rs, 0, 'cliexobroid');

                    $sql = "UPDATE 
                                cliente_excecao_obrigacao 
                            SET 
                                cliexobrdt_exclusao = null,
                                cliexobrtipo = '" . $cliente[1] . "'
                            WHERE 
                                cliexobroid = '" .  $cliexobroid . "'";

                    $rs = pg_query($this->conn, $sql);

                }else{

                    $sql = "INSERT INTO cliente_excecao_obrigacao 
                                (cliexobrobroid,cliexobrclioid,cliexobrtipo) 
                            VALUES 
                                ('" . $obroid . "','" . $cliente[0] . "','" . $cliente[1] . "')";

                    $rs = pg_query($this->conn, $sql);
                }
            }
        }catch(Exception $e){
            throw new Exception("Erro ao inserir cliente na exceção.");
        }
    }

    public function deletarClienteExcecao($obroid){
        $sql = "UPDATE 
                    cliente_excecao_obrigacao 
                SET 
                    cliexobrdt_exclusao = NOW() 
                WHERE cliexobrobroid = '" . $obroid . "'";

        $rs = pg_query($this->conn, $sql);
    }
    
    /**
     * Vincula à conclusão da O.S um ou mais tipos de obrigação financeira.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @param int $ostofostoid - Id do tipo da O.S
     * @param int $obroid - Id da obrigacao financeira
     * @param int $usuoid - Id do usuário que está realizando a operação.
     */
    public function vincularConclusao($ostofostoid, $obroid, $usuoid){
        $sql = "INSERT INTO os_tipo_obrigacao_financeira
                    (ostofostoid, ostofobroid, ostofusuoid_inclusao, ostofdt_cadastro)
                VALUES
                    ($ostofostoid, $obroid, $usuoid, NOW());";
                    
        pg_query($this->conn, $sql);
    }
    
    /**
     * Desvincula à conclusão da O.S de um ou mais tipos de obrigação financeira.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @param int $obroid - Id da obrigacao financeira
     */
    public function desvincularConclusao($obroid){
        $sql = "DELETE FROM
                    os_tipo_obrigacao_financeira
                WHERE
                    ostofobroid = $obroid;";
        
        pg_query($this->conn, $sql);
    }
}