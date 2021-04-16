<?php
/** Classes dependencias */
require 'modulos/Manutencao/View/MANInsereTextoContratoView.php';
require 'modulos/Manutencao/DAO/ManInsereTextoContratoDAO.class.php';

/**
 * description: Classe respons�vel por control
 * a tela ti_verifica_obrigacao_duplicada.php
 * 
 * @author denilson.sousa
 *
 */
class MANInsereTextoContrato {
	/** @var MANInsereTextoContratoDAO */
	private $dao;
	
	/** @view MANInsereTextoContratoView */
	private $view;
		
	public function __construct() {
		$this->dao = new ManInsereTextoContratoDAO();
		$this->view = new MANInsereTextoContratoView();
		$this->cd_usuario_intra = 0;
	}
	
	public function receberContratosAction($contratos) {
		$this->view->receberContratosView($contratos);
		
	}
	
	public function pesquisarContratosAction() {
		$resultado = $this->dao->pesquisarContratos($_POST['contratos']);
		
		$this->view->pesquisarContratosView($resultado);
		
	}
	
	public function inserirHistoricoContratosAction($contratos,$texto_historico) {
		$resultado = $this->dao->inserirHistorico($contratos,$texto_historico);
		
		$this->view->receberContratosView($resultado);
	}
	
}

?>