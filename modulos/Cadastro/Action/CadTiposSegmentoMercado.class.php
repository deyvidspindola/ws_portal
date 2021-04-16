<?php

/**
 * @file CadTiposSegmentoMercado.class.php
 * @author Paulo Henrique da Silva Junior
 * @version 14/06/2013
 * @since 14/06/2013
 * @package SASCAR CadTiposSegmentoMercado.class.php
 */

require_once(_MODULEDIR_."Cadastro/DAO/CadTiposSegmentoMercadoDAO.class.php");

/**
 * Action do Cadastro de Tipos de Segmento de Mercado
 */
class CadTiposSegmentoMercado {
	
	/**
	 * Acesso a dados do módulo
	 * @var CadTiposSegmentoMercado
	 */
	private $dao;
	
	/**
	 * Construtor
	 */




	public function __construct() {
		
		global $conn;

        // global $conn;
        // $this->conn = $conn;   
        // $this->usuoid = $_SESSION['usuario']['oid'];     
		$this->dao = new CadTiposSegmentoMercadoDAO($conn);
	}
    
    public function __set($var, $value) {
        $this->$var = $value;
    }
    
    public function __get($var) {
        return $this->$var;
    }	
 
	/**
	 * Acesso inicial do módulo
	 */
	public function index($acao = 'index', $resultadoPesquisa = array(), $segdescricao = '', $mensagem = '') {
		include _MODULEDIR_.'Cadastro/View/cad_tipos_segmento_mercado/index.php';
	}

	public function novo($acao, $mensagem = '') {
		include _MODULEDIR_.'Cadastro/View/cad_tipos_segmento_mercado/form.php';
	}
	
	public function editar($acao = 'editar', $mensagem = '') {
		$segoid = isset($_POST['segoid']) ? $_POST['segoid'] : '';
		$resultadoPesquisa = $this->dao->getTipos('', $segoid);
		$segdescricao = $resultadoPesquisa[0]['segdescricao'];	
		include _MODULEDIR_.'Cadastro/View/cad_tipos_segmento_mercado/form.php';
	}
	
	public function atualizar($acao, $mensagem = '') {
		$resultado = array();
		$segoid = isset($_POST['segoid']) ? $_POST['segoid'] : '';
		$segdescricao = trim(isset($_POST['segdescricao'])) ? trim($_POST['segdescricao']) : '';
		$resultado = $this->dao->atualizaDados($segdescricao, $segoid);
		if ($resultado['acao'] == 'index') {
			$this->index('index', '', '', $resultado['mensagem']);
		} else {
			$this->editar('editar', $resultado['mensagem']);
		}
	}
	
	public function excluir($acao, $mensagem = '') {
		$resultado = array();
		$segoid = isset($_POST['segoid']) ? $_POST['segoid'] : '';
		$resultado = $this->dao->excluirDados($segoid);
		$this->index('index', '', '', $resultado['mensagem']);
	}
	


	public function cadastrar($acao) {
		$resultado = array();
		// $segdescricao = isset($_POST['segdescricao']) ? strtoupper(strtr($_POST['segdescricao'],"áéíóúâêôãõàèìòùç","ÁÉÍÓÚÂÊÔÃÕÀÈÌÒÙÇ")) : '';
		$segdescricao = trim(isset($_POST['segdescricao'])) ? trim($_POST['segdescricao']) : '';
		$resultado = $this->dao->inserirDados($segdescricao);
		if ($resultado['acao'] == 'index') {
			$this->index('index', '', '', $resultado['mensagem']);
		} else {
			$this->novo('novo', $resultado['mensagem']);
		}
	}

	public function comboSegmento() {
		return $this->dao->getTipos('', '', '');	
	}	


	/**
	 * Gera o arquivo XLS
	 */
	public function pesquisar() {
		$segdescricao = trim(isset($_POST['segdescricao'])) ? trim($_POST['segdescricao']) : '';
		$resultadoPesquisa = $this->dao->getTipos($segdescricao);	
		$this->index('pesquisar', $resultadoPesquisa, $segdescricao);
	}
	
    


}