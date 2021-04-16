<?php

/**
 * @file RelEmbarcadoresSegDAO.class.php
 * @author Diego de Campos Noguês
 * @version 17/06/2013
 * @since 17/06/2013
 * @package SASCAR CadEmbarcadoresDAO.class.php
 */

require_once(_MODULEDIR_."Cadastro/DAO/CadEmbarcadoresDAO.class.php");

/**
 * Acesso a dados para o módulo Tipos de Segmento de Mercado
 */
class RelEmbarcadoresSegDAO {
	
	/**
	 * Conexão com o banco de dados
	 * @var resource
	 */
	private $conn;	
	
	/**
	 * Construtor, recebe a conexão com o banco
	 * @param resource $connection
	 * @throws Exception
	 */
	public function __construct() {        
        global $conn;
        $this->conn = $conn;   
        $this->usuoid = $_SESSION['usuario']['oid'];    

        if($_GET['acao']):
        	$_POST['acao'] = $_GET['acao'];
        endif; 
    }

    public function __set($var, $value) {
        $this->$var = $value;
    }
    
    public function __get($var) {
        return $this->$var;
    }

    public function getGerRisco () {
    	$CadEmbarcadores = new CadEmbarcadoresDAO();
    	return $CadEmbarcadores->getGerRisco();           	
    }
	
	public function getGerRiscoRel() {
    	$CadEmbarcadores = new CadEmbarcadoresDAO();
    	return $CadEmbarcadores->getGerRiscoRel();           	
    }

    public function getSegmentos ($selected = null) {
    	$CadEmbarcadores = new CadEmbarcadoresDAO();
    	return $CadEmbarcadores->getSegmentos();
    }
    
    public function getTransportadoras ($where = '') {
    	$CadEmbarcadores = new CadEmbarcadoresDAO();
    	// return $CadEmbarcadores->getTransportadoras();   	
    	return $CadEmbarcadores->getTransportadoraCliente();   	
    }

    public function getEstados ($selected = null, $uf = false) {
    	$CadEmbarcadores = new CadEmbarcadoresDAO();
    	return $CadEmbarcadores->getEstados(); 	
    }

    
    public function getEmbarcadores() {
    	$CadEmbarcadores = new CadEmbarcadoresDAO();

    	$result = array();

    	$pesquisa = $CadEmbarcadores->pesquisa();    	
    	foreach ($pesquisa as $value):
    		$result[$value['emboid']] = $value['embnome'];
    	endforeach;

    	return $result;
    }

    public function getVeiculosTransportadoras($traoid) {

    		$result = array();
    		foreach ($traoid as $chave => $valor) {

				if($valor == ''){	continue;	}
	    		
				$sql = "SELECT clinome FROM clientes WHERE clioid = $valor ";				
	    		$resultTranome = pg_fetch_object(pg_query($this->conn, $sql));

				$sql = "SELECT 
							count(distinct veioid) AS veiculos
						FROM clientes
							INNER JOIN embarcador_transportadora ON embtclioid=clioid
							LEFT JOIN contrato ON conclioid = clioid and condt_exclusao is null and concsioid = 1 
							LEFT JOIN veiculo ON veioid = conveioid
							LEFT JOIN tipo_contrato ON conno_tipo = tpcoid
						WHERE 
							clioid = $valor AND tpcdescricao not ilike 'ex-%'";

				$resultVeiculos = pg_fetch_object(pg_query($this->conn, $sql));
				$result[] = array('veiculos' => $resultVeiculos->veiculos , 'nome' => $resultTranome->clinome);

    		}
    		return $result;
    }

    public function pesquisa($params) {

		$sql = "
    		SELECT 
				segoid,
				segdescricao, 
				embuf,  
				emboid,	
				embnome, 
				to_char(embdt_alteracao, 'DD/MM/YYYY HH24:MI') as embdt_alteracao,
				(SELECT 
					ARRAY(SELECT gernome 
							FROM gerenciadora 
							JOIN embarcador_gerenciadora 
								ON embggeroid=geroid 
							WHERE embgemboid = emboid ";
				if(count($params['geroid']) > 0 && $params['geroid'][0] != ''){
					$sql .= " AND embggeroid IN(".implode(",", $params['geroid']).")";
				}
			
				$sql .="	ORDER BY gernome)) AS gerenciadoras,
				(SELECT 
					ARRAY( SELECT 
							clioid FROM clientes
								JOIN embarcador_transportadora ON embtclioid=clioid 
								LEFT JOIN contrato ON conclioid = clioid
								LEFT JOIN veiculo ON veioid = conveioid							 
							WHERE
								embtemboid = emboid ";

				if(count($params['traoid']) > 0 && $params['traoid'][0] != '')				
								$sql .=" and embtclioid IN (".implode(",", $params['traoid']).") ";
				$sql.="			
							GROUP BY clioid)) AS transportadoras
			FROM 
				embarcador 
				INNER JOIN 
					segmento 
					ON embsegoid = segoid 
				LEFT JOIN
					embarcador_gerenciadora 
					ON embgemboid = emboid
				LEFT JOIN
					embarcador_transportadora 
					ON embtemboid = emboid 
				WHERE
					emboid IS NOT NULL 
					";
				

		// filtro por segmento
		if(count($params['segoid']) > 0 && $params['segoid'][0] != '')
			$sql .= " AND segoid IN(".implode(',', $params['segoid']).")";

		// filtro por estado
		if(count($params['embuf']) > 0 && $params['embuf'][0] != '')
			$sql .= " AND embuf IN('".implode("','", $params['embuf'])."')";

		// filtro por embarcador
		if(count($params['emboid']) > 0 && $params['emboid'][0] != '')
			$sql .= " AND emboid IN(".implode(",", $params['emboid']).")";

		// filtro por ger. de risco
		if(count($params['geroid']) > 0 && $params['geroid'][0] != '')
			$sql .= " AND embggeroid IN(".implode(",", $params['geroid']).")";

		// filtro por transportadoras
		if(count($params['traoid']) > 0 && $params['traoid'][0] != '')
			$sql .= " AND embtclioid IN(".implode(",", $params['traoid']).")";

		$sql .= "
				GROUP BY
						segoid, embuf, emboid
				ORDER BY 
						segdescricao ";
// print_r($sql);die;

		$rs = pg_query($this->conn, $sql);

		$resultDb = pg_fetch_all($rs);


	
		if(!$resultDb) {
			$result = array();
		} else {
			foreach($resultDb as $value) {		
				$result[$value['segoid']]['label'] = $value['segdescricao'];
				$result[$value['segoid']]['uf'][$value['embuf']][$value['emboid']] = array(
																'embnome' 		  => $value['embnome'],
																'embdt_alteracao' => $value['embdt_alteracao'],
																'gerenciadoras'   => str_getcsv(trim($value['gerenciadoras'], '{}')),
																'transportadoras' => $this->getVeiculosTransportadoras(str_getcsv(trim($value['transportadoras'], '{}')))
															);
			}
		}


        return $result; 
	}
	
}