<?php
/** 
 * SASCAR (http://www.sascar.com.br/)
 * 
 * @author Jorge A. D. Kautzmann <jorge.kautzmann@sascar.com.br>
 * @description	Cadastro de Planos Satelitais - Ações de Controle
 * @version 28/03/2013 [1.0]
 * @package SASCAR Intranet
*/

/**
 * @category Controller Principal
 * @package SASCAR Intranet
 */
class CadPlanoSatelitalController {
	private $dao;
	private $view;
	
	/**
	  * __construct()
	  *
	  * @param none
	  * @return none
	  * @description	Método construtor da classe
	*/
	public function __construct() {
		$this->dao = new CadPlanoSatelitalDAO();
		$this->view = new CadPlanoSatelitalView();
	}

	/**
	 * headerAction()
	 *
	 * @param none
	 * @return none
	 */
	public function headerAction() {
		$this->view->header();
	}
	

	/**
	 * gerenciarPlanosAction()
	 *
	 * @param none
	 * @return none
	 */
	public function gerenciarPlanosAction() {
	    $asapoid = (int) $_POST['asapoid'];
	    $vData = array();
	    if($asapoid > 0){
	        $vData = array_merge($_POST, $this->dao->getDadosPlano($asapoid));
	    }else{
	        $vData = array_merge($vData, $_POST);
	        $vData['asapdescricao'] = '';
	    }
	    $this->view->getDadosPlano($vData);
	}
	
	
	/**
	 * confirmarNovoPlanoAction()
	 *
	 * @param none
	 * @return none
	 */
	public function confirmarNovoPlanoAction(){
	    $vData = array();
	    $vData = $this->dao->confirmarNovoPlano();
	    $vData['asapdescricao'] = '';
	    $this->view->getDadosPlano($vData);
	}
	
	/**
	 * excluirPlanoAction()
	 *
	 * @param none
	 * @return none
	 */
	public function excluirPlanoAction(){
	    $vData = array();
	    $vData = $this->dao->excluirPlano();
	    $this->view->getDadosPlano($vData);
	}
	
}