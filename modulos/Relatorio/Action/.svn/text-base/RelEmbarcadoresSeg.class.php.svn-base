<?php

/**
 * @file RelEmbarcadoresSeg.class.php
 * @author Diego de Campos Noguês - diegocn@brq.com
 * @version 17/06/2013
 * @since 17/06/2013
 * @package SASCAR RelEmbarcadoresSeg.class.php 
 */

require_once(_MODULEDIR_ ."Relatorio/DAO/RelEmbarcadoresSegDAO.class.php");
require_once(_SITEDIR_ . "includes/classes/Formulario.class.php");
/**
 * Action do Cadastro de Tipos de Segmento de Mercado
 */
class RelEmbarcadoresSeg {
	
	/**
	 * Acesso a dados do módulo
	 * @var RelEmbarcadoresSeg
	 */
	private $dao;
	public $formPesquisaObj;
	
	/**
	 * Construtor
	 */
	public function __construct() {		

		global $conn;
		$this->dao = new RelEmbarcadoresSegDAO($conn);		
		
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
		$this->formPesquisaObj = $this->formPesquisa();		

		include _MODULEDIR_.'Relatorio/View/rel_embarcadores_seg/index.php';
	}
	
	public function pesquisar() {
		// para manter os valores após a busca
		$params = $this->populaValoresPost();
		
		$resultadoPesquisa = $this->dao->pesquisa($params);	
		$this->index('pesquisar', $resultadoPesquisa);
	}

	public function exportar($acao) {

		$params = $this->populaValoresPost();

		$resultadoPesquisa = $this->dao->pesquisa($params);	

		$file = "rel_embarcadores_seg.xls";		
		header('Pragma: public');
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");                  // Date in the past
		header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1
		header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1
		header("Pragma: no-cache");
		header("Expires: 0");
		header('Content-Transfer-Encoding: none');
		header('Content-Type: application/vnd.ms-excel;');                 // This should work for IE &amp; Opera
		header("Content-type: application/x-msexcel");                    // This should work for the rest
		header('Content-Disposition: attachment; filename="'.$file.'"');
		include _MODULEDIR_.'Relatorio/View/rel_embarcadores_seg/result.php';
		exit;
	}

	public function populaValoresPost($clearPost = false, $params = null) {	

		if(!is_null($params)):
			$data = $params;
		else:
			$data = $_POST;
		endif;
		
		foreach($data as $key => $value):
			if($clearPost === false)
				$this->$key = $value;
			else
				unset($this->$key);
		endforeach;	

		return $data;		
	}	

	public function formPesquisa($acao = null) {

		$form = new Formulario('form');		

		$form->adicionarSelect('segoid[]','Segmentos de Mercado:','Segmentos de Mercado',$this->segoid,
												$this->dao->getSegmentos(),false,'TODOS',350,1,true);

		$form->adicionarSelect('embuf[]','Estados:','Estados',$this->embuf,
												$this->dao->getEstados(null, true),false,'TODOS',100,1,true);

		$form->adicionarSelect('emboid[]','Embarcadores:','Embarcadores',$this->emboid,
												$this->dao->getEmbarcadores(),false,'TODOS',350,1,true);

		$form->adicionarSelect('geroid[]','Gerenciadoras de Riscos:','Gerenciadoras de Riscos',$this->geroid,
												$this->dao->getGerRiscoRel(),false,'TODOS',475,1,true);

		$form->adicionarSelect('traoid[]','Transportadoras (Clientes):','Transportadoras',$this->traoid,
												$this->dao->getTransportadoras(),false,'TODOS',350,1,true);

		$form->adicionarButton('pesquisar', 'Pesquisar');

		$form->util->incluiCssJavascript();	

		return $form;
	}

}