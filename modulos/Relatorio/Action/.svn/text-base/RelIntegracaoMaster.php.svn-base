<?php


/**
 * Classe de persistência de dados 
 */
require (_MODULEDIR_ . "Relatorio/DAO/RelIntegracaoMasterDAO.php");

/**
 * RelIntegracaoMaster.php
 * 
 * Classe Action para o Relatório de Integração MASTER Turismo
 * 
 * @author Vanessa Rabelo <Vanessa.rabelo@meta.com.br>
 * @package Relatório
 * @since 22/02/2013
 * 
 */


class RelIntegracaoMaster {

    private $dao;
    
    
    
    /**
     * Construtor
     *
     * @author Vanessa Rabelo <Vanessa.rabelo@meta.com.br>
     */
    public function __construct() {
    
    	global $conn;
    
    	$this->dao = new RelIntegracaoMasterDAO($conn);
    	$this->id_usuario = $_SESSION['usuario']['oid'];
    }
    
    
    
    /**
     * Método principal
     * Chama a view do relatório
     * 
     * @author Vanessa Rabelo <Vanessa.rabelo@meta.com.br
     */
    public function index() {

        cabecalho();
        
        $data_inicial         = (isset($_POST['data_inicio_pesquisa'])) ? $_POST['data_inicio_pesquisa'] : date('d/m/Y');
        $data_final           = (isset($_POST['data_fim_pesquisa'])) ? $_POST['data_fim_pesquisa'] : date('d/m/Y');
        
        include(_MODULEDIR_ . 'Relatorio/View/rel_integracao_master/index.php');
    }


    /**
     * 
     * Método de pesquisa do relatório Integração MASTER Turismo
     * 
     * @author Vanessa Rabelo <vanessa.rabelo@meta.com.br>
     */
    public function pesquisaIntegracao() {
    
    	cabecalho();
    
    	$acao ='pesquisaIntegracao';
    	
    
    	
    	$data_inicial         = (isset($_POST['data_inicio_pesquisa'])) ? $_POST['data_inicio_pesquisa'] : date('d/m/Y');
    	$data_final           = (isset($_POST['data_fim_pesquisa'])) ? $_POST['data_fim_pesquisa'] : date('d/m/Y');
    	$forfornecedor        = (isset($_POST['forfornecedor'])) ? $_POST['forfornecedor'] : null;
    	$numero_solicitacao   = (isset($_POST['numero_solicitacao'])) ? $_POST['numero_solicitacao'] : null;
        $webservice 	 	  = (isset($_POST['webservice'])) ? $_POST['webservice'] : '';
    	$semadto   			  = (isset($_POST['semadto'])) ? $_POST['semadto'] : '';
    	$semreemb    	      = (isset($_POST['semreemb'])) ? $_POST['semreemb'] : '';
    	
    	
    	
    	
  

    
    	$this->regra = array();
    	$filtros = array(
    			"forfornecedor" => $forfornecedor,
    			"data_inicio_pesquisa" => $data_inicial,
    			"data_fim_pesquisa" => $data_final,
    			"numero_solicitacao" => $numero_solicitacao,    			
    			"webservice" => $webservice,
    			"semadto" => $semadto,
    			"semreemb" => $semreemb,
    		
    			

    			 
    	);
    	 
    
    	$rs = $this->dao->pesquisarIntegracaoMaster($filtros);
    
    	if ($rs['erro']===0) {
    		$this->numeroLinhas = pg_num_rows($rs['resultado']);
    		 
    		$this->regra = pg_fetch_all($rs['resultado']);
    	}
    
    	include(_MODULEDIR_ . 'Relatorio/View/rel_integracao_master/index.php');
    }
         
  


}