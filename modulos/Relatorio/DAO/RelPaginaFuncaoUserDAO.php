<?php

/**
 * @file RelPaginaFuncaoUserDAO.php
 * @author Allan Cleyton
 * @version 22/03/2018
 * @since 22/03/2018
 * @package SASCAR RelPaginaFuncaoUserDAO.php 
 */

class RelPaginaFuncaoUserDAO {
    
    private $conn;  
    private $cd_usuario;
    
    public function __construct() 
    {        
        global $conn;
        $this->conn = $conn;   
        $this->cd_usuario = $_SESSION['usuario']['oid'];    

    }
	
    public function getPaginaPorUser($pagoid,$tipo) {
        
    	$id_pagina = null;
    	foreach ($pagoid as $v)
    	{
    	$id_pagina .= $v.',';
    	}
        $sql = "
                SELECT depdescricao AS Departamento,
				       to_ascii (prhperfil) AS Cargo,
				       to_ascii (nm_usuario) AS Usuario,
				       ppcpagoid as id_pagina,
				       pagtitulo as titulo_pagina,
				       to_ascii (pagdescricao) AS pagina,
				       pagurl as url			     
				FROM pagina_permissao_cargo
				INNER JOIN perfil_rh ON prhoid = ppccargooid
				INNER JOIN usuarios ON usucargooid = prhoid
				LEFT JOIN departamento ON depoid = usudepoid
				LEFT JOIN pagina ON ppcpagoid = pagoid
				WHERE ppcpagoid in (".substr($id_pagina,0,strlen($id_pagina)-1).")
				  AND dt_exclusao IS NULL
				  AND prhexclusao IS NULL
				ORDER BY pagdescricao 
            ";
        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        if(pg_num_rows($rs) > 0 ){
           $result = pg_fetch_all($rs);
        }
        $this->getRelatorioCsv($result);
        // return $result;
    }

    public function getFuncaoPorUser($funcoid,$tipo) {
    
    	$id_funcao = null;
    	foreach ($funcoid as $v)
    	{
    		$id_funcao .= $v.',';
    	}
    	$sql = "
    	SELECT depdescricao AS Departamento,
		       to_ascii (prhperfil) AS Cargo,
		       to_ascii (nm_usuario) AS Usuario,
		       fpcfuncoid as id_funcao,
		       to_ascii (funcnome) AS nome_funcao,
		       to_ascii (funcdescricao) AS descricao_funcao
		FROM funcao_permissao_cargo
		INNER JOIN perfil_rh ON prhoid = fpccargooid
		INNER JOIN funcao ON fpcfuncoid = funcoid
		INNER JOIN usuarios ON usucargooid = prhoid
		LEFT JOIN departamento ON depoid = usudepoid
		WHERE funcoid in (".substr($id_funcao,0,strlen($id_funcao)-1).")
		  AND dt_exclusao IS NULL
		  AND funcexclusao IS NULL
		ORDER BY funcdescricao
    	";

    	$rs = pg_query($this->conn, $sql);
    
    	$result = array();
    
    	if(pg_num_rows($rs) > 0 ){
    		$result = pg_fetch_all($rs);
    	}
    $this->getRelatorioCsv($result);
    	// return $result;
    }

   public function getPagina() 
   {
   	$sql = 'select * from pagina where pagexclusao is null ORDER BY pagdescricao';
   	$rs = pg_query($this->conn, $sql);
   	$result = array();
   	if (pg_num_rows($rs) > 0 ) 
   	{
   		$result = pg_fetch_all($rs);
   	}
   	return $result;
   }
   
   public function getFuncao()
   {
   	$sql = 'select * from funcao where funcexclusao is null ORDER BY funcdescricao';
   	$rs = pg_query($this->conn, $sql);
   	$result = array();
   	if (pg_num_rows($rs) > 0 )
   	{
   		$result = pg_fetch_all($rs);
   	}
   	return $result;
   }
   
   /**
    * Retorna o resultado com as paginas ou funcoes em csv;
    */
   public function getRelatorioCsv($relatorio)
   {
   	
   	$this->exportCsvDownload($relatorio, "permissao_por_user");
   	
   }
   
   /*
    * exporta informações para csv
    */
   public function exportCsvDownload($array, $filename = null, $delimiter=";")
   {
   	 
   
   	header('Content-Type: application/csv; charset=UTF-8');
   	header('Content-Disposition: attachment; filename="'.$filename.date("dmY-His").'.csv";');
   
   	$f = fopen('php://output', 'w');
   
   	foreach ($array as $line) {
   		fputcsv($f, $line, $delimiter);
   	}
   	exit;
   	 
   }
    
}

