<?php

/**
 * @file RelPaginaFuncaoUser.php
 * @author Allan Cleyton
 * @version 22/03/2018
 * @since 22/03/2018
 * @package SASCAR RelPaginaFuncaoUser.php 
 */

require (_MODULEDIR_ . "Relatorio/DAO/RelPaginaFuncaoUserDAO.php");


class RelPaginaFuncaoUser {
    
    /*
    * Constantes
    */
     const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";

    private $dao;
    private $retorno;
    private $cd_usuario;

    
    public function __construct() {
    	$this->dao = new RelPaginaFuncaoUserDao();
    }
    
    public function listar() {
        
    }
    
    public function index() {
    	
    	//$pagoid=14;
    	if ($_SERVER['REQUEST_METHOD'] =='POST')
    	{
    		//print_r($_POST);
    		$tipo = $_POST['tipo'];
    		if ($_POST['tipo'] == 'P')
    		{
    			$param['options'] = $this->dao->getPagina();
    		}	
    		else 
    		{
    			$param['options'] = $this->dao->getFuncao();
    		}
    	}
    			
//	print_r($param['comboPaginaUser']);
		include _MODULEDIR_.'Relatorio/View/rel_pagina_funcao_user/index.php';
  	
    }
    
    public function gerar() 
    {
    	
    	if ($_POST['tipo_acao'] == 'F')
    	{
    		$this->dao->getFuncaoPorUser($_POST['options'] , $_POST['tipo']);
       	}
    	else 
    	{
    		$this->dao->getPaginaPorUser($_POST['options'] , $_POST['tipo']);
    	}
    }
    
     
}