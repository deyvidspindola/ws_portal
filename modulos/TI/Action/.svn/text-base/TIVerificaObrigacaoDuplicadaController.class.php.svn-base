<?php

/** Classes dependencias */
require 'modulos/TI/View/TIVerificaObrigacaoDuplicadaView.php';
require 'modulos/TI/DAO/TIVerificaObrigacaoDuplicadaDAO.class.php';

/** Lib de envio de email */
include "/lib/phpMailer/class.phpmailer.php";

/**
 * description: Classe respons�vel por controlar
 * a tela ti_verifica_obrigacao_duplicada.php
 * 
 * @author denilson.sousa
 *
 */
class TIVerificaObrigacaoDuplicada {
	
	/** @var TIVerificaObrigacaoDuplicadaDAO */
	private $dao;
	
	/** @view TIVerificaObrigacaoDuplicadaView */
	private $view;
		
	public function __construct() {
		$this->dao = new TIVerificaObrigacaoDuplicadaDAO();
		$this->view = new TIVerificaObrigacaoDuplicadaView();
	}
	
	public function pesquisarAction() {
		$this->view->pesquisarForm();
	}
	
	public function pesquisarObrigacoes() {
		$resultado = $this->dao->pesquisar($_POST['connumero']);
		
		$this->view->pesquisaResult($resultado);
	}
	
	/**
	 * Corrige as obrigacoes duplicadas
	 * 
	 */
	public function corrigeObrigacao($obrigacoes = null) {
		try {
			$resultado = $this->dao->corrigirObrigacao($_POST['connumero']);
                	
			$this->view->mostrarCorrecao($resultado);			

		} catch(Exception $e) {
			echo $e->getMessage();		
		}
	}
	
}
