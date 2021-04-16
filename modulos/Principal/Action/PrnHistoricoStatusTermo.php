<?php


/**
 * Classe de persistência de dados 
 */
require (_MODULEDIR_ . "Principal/DAO/PrnHistoricoStatusTermoDAO.php");

/**
 * PrnHistoricoStatusTermo.php
 * 
 * Classe Action para Histórico Status Termo
 * 
 * @author Angelo Frizzo Jr <angelo.frizzo@meta.com.br>
 * @package Principal
 * @since 07/03/2013
 * 
 */

class PrnHistoricoStatusTermo {

    private $dao;
    
    /*
     * Construtor
     */
    public function __construct() {
    
    	global $conn;
    
    	$this->dao = new PrnHistoricoStatusTermoDAO($conn);
    	$this->id_usuario = $_SESSION['usuario']['oid'];
    }
    
    /*
     * Método principal
     */
    public function index() {

        cabecalho();

        include(_MODULEDIR_ . 'Principal/View/prn_historico_status_termo/index.php');
    }

    /*
     * Método de pesquisa do Histórico Status Termo
     */
    public function pesquisaStatusTermo() {
    
    	cabecalho();
    
    	try{
	    	$acao ='pesquisaStatusTermo';
	    	
	    	$contrato   = (isset($_POST['contrato'])) ? $_POST['contrato'] : null;
	    	$this->regra = array();
	    	$filtros = array(
				"contrato" => $contrato    			
	    	);
	    
	    	$rs = $this->dao->pesquisarStatusTermo($filtros);
	    	
    		$this->numeroLinhas = pg_num_rows($rs);
    		$this->regra = pg_fetch_all($rs);
	    	
   	    }
		catch(Exception $e){
			
			$msg = $e->getMessage();
			 
		}
	    	    		
        include(_MODULEDIR_ . 'Principal/View/prn_historico_status_termo/index.php');

    }
}