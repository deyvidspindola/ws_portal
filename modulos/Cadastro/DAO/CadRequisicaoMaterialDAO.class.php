<?php

/**
 * @file CadRequisicaoMaterialDAO.class.php
 * @author Danilo Carvalho de França
 * @version 16/12/2013 11:00:28
 * @since 16/12/2013 11:00:28
 * @package SASCAR CadRequisicaoMaterialDAO.class.php 
 */

class CadRequisicaoMaterialDAO {
    
    private $conn;  
    private $cd_usuario;
    
    public function __construct() {        
        global $conn;
        $this->conn = $conn;   
        $this->cd_usuario = $_SESSION['usuario']['oid'];    

    }

    public function getEstabelecimento($empresa) {

        $sql = "SELECT
                    etboid, etbdescricao 
                FROM 
                    estabelecimento 
                WHERE
                    etbtecoid = ".$empresa." 
                    and etbexclusao is null
                ORDER BY etbdescricao";

    	$rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        if(pg_num_rows($rs) > 0) {
            
            $result = pg_fetch_all($rs);
            foreach($result as $row){
                $resultado[] = array_map('utf8_encode',$row);
            }
        }
        
        return $resultado;
    }


    function getRepresentante($etboid){
        global $autenticacaoSistemaUsuarios;
        $sql = "SELECT
                    relroid
                FROM
                    relacionamento_representante,
                    estabelecimento,
                    tectran
                WHERE
                    relrrep_terceirooid = etbrepoid_recebedor
                AND etbtecoid = tecoid
                AND etboid = ".$etboid."
                AND tecurl_sistema = '$autenticacaoSistemaUsuarios'";

        $rs = pg_query($this->conn, $sql);

        if(pg_num_rows($rs) > 0 ){
            $relroid = pg_fetch_result($rs, 0, 'relroid');
        }
        return $relroid;
    }

    
}

