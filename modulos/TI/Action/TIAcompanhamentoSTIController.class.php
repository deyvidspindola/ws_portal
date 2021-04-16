<?php
/** 
 * SASCAR (http://www.sascar.com.br/)
 * 
 * @author Jorge A. D. Kautzmann <jorge.kautzmann@sascar.com.br>
 * @description	Acompanhamento de STI - Ações de Controle
 * @version 10/10/2012 [0.0.1]
 * @package SASCAR Intranet
*/

/**
 * @category Controller Principal
 * @package SASCAR Intranet
 */
class TIAcompanhamentoSTIController {
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
		$this->dao = new TIAcompanhamentoSTIDAO();
		$this->view = new TIAcompanhamentoSTIView();
	}

	/**
	 * headerAction()
	 *
	 * @param none
	 * @return none
	 */
	public function headerAction($strAjaxPrint='') {
		$this->view->header($strAjaxPrint);
	}
	
	/**
	  * getPesquisaResult()
	  *
	  * @param none
	  * @return none
	*/
	public function pesquisarAction() {
		$vPesquisa = array();
		$_SESSION['aba_ativa'] = 1;
		$origem = isset($_POST['origem_req'])? trim($_POST['origem_req']) : 'menu';
		if($origem == 'form-submit'){
			$vPesquisa = $this->dao->getPesquisaResult();
			$this->view->pesquisaFormResult($vPesquisa);
		}else{
			$this->view->pesquisaForm();
		}
	}
	
	/**
	  * pesquisarSolicitanteAction()
	  *
	  * @param none
	  * @return none
	*/
	public function pesquisarSolicitanteAction() {
		$this->view->pesquisarSolicitante();
	}
	

	/**
	 * getDetalheSTIRelacao()
	 *
	 * @param $reqioid
	 * @return none
	 */
	public function getDetalheSTIRelacao($reqioid = 0) {
		$reqioid = (int) $reqioid;
		$divID = 'iDetRel_' . $reqioid;
		$objResponse = new xajaxResponse();
		$objResponse->AddAssign($divID, 'innerHTML', $this->view->getDetalheSTIRelacao($this->dao->getDetalheSTIRelacao($reqioid)));
		return $objResponse->getXML();
	}
	
	
	
	/**
	 * getComboBoxListSubtipo()
	 *
	 * @param $reqtoid (ID do tipo)
	 * @return none
	 */
	public function getComboBoxSubtipo($reqtoid = 0) {
		$reqtoid = (int) $reqtoid;
		$objResponse = new xajaxResponse();
		$objResponse->AddAssign('combo_subtipo', 'innerHTML', $this->view->getComboBoxListSubtipo($this->dao->getComboBoxListSubtipo($reqtoid)));
		return $objResponse->getXML();
	}
	
	/**
	  * exibirDadosAction()
	  *
	  * @param none
	  * @return none
	*/
	public function exibirDadosAction() {
	    $_SESSION['aba_ativa'] = 1;
		$vData = array();
		$reqioid = (int) $_GET['reqioid'];
		$vData = $this->dao->getDadosSTI($reqioid);
		$this->view->dadosSTI($vData);
	}
	
	/**
	  * confirmarClassificacaoAction()
	  *
	  * @param none
	  * @return none
	*/
	public function confirmarClassificacaoAction() {
		$_SESSION['aba_ativa'] = 1;
	    $vData = array();
		$vData = $this->dao->setDadosSTI();
		if($vData['action_st'] == 'ok'){
			$vData = $this->dao->getDadosSTI($vData['sti']);
			$vData['action_msg'] = 'Dados da STI atualizados com sucesso!';
		}
		$this->view->dadosSTI($vData);
	}
	
	/**
	 * incluirPlanejamentoFase()
	 *
	 * @param none
	 * @return none
	 */
	public function incluirPlanejamentoFaseAction(){
		$_SESSION['aba_ativa'] = 1;
	    $vData = array();
		$vData = $this->dao->setPlanejamentoFase();
		if($vData['action_st'] == 'ok'){
			$vData = $this->dao->getDadosSTI($vData['sti']);
			$vData['action_msg_pfase'] = 'Planejamento de fase realizado com sucesso!';
		}
		$this->view->dadosSTI($vData);
	}



	/**
	 * getComboBoxUsuario()
	 *
	 * @param $reqiexoid (ID da função)
	 * @return none
	 */
	public function getComboBoxUsuario($reqifureqifcoid = 0) {
		$reqifureqifcoid = (int) $reqifureqifcoid;
		$objResponse = new xajaxResponse();
		$objResponse->AddAssign('combo_usuario', 'innerHTML', $this->view->getComboBoxUsuario($this->dao->getComboBoxUsuario($reqifureqifcoid)));
		return $objResponse->getXML();
	}
	
	
	/**
	 * getComboBoxRecursoFase()
	 *
	 * @param $reqiexoid (ID do tipo)
	 * @return none
	 */
	public function getComboBoxRecursoFase($reqiexoid = 0) {
		$reqiexoid = (int) $reqiexoid;
		$objResponse = new xajaxResponse();
		$objResponse->AddAssign('combo_recurso', 'innerHTML', $this->view->getComboBoxListRecursoFase($this->dao->getComboBoxListRecursoFase($reqiexoid)));
		return $objResponse->getXML();
	}


	/**
	 * getRelacaoRecursoFase()
	 *
	 * @param $reqiexoid (ID do tipo)
	 * @return none
	 */
	public function getRelacaoRecursoFase($reqiexoid = 0) {
		$reqiexoid = (int) $reqiexoid;
		$objResponse = new xajaxResponse();
		$objResponse->AddAssign('fase_bt_confirmar','style.display', 'none');
		$objResponse->AddAssign('fase_bt_incluir','style.display', 'block');
		$objResponse->AddAssign('plan_fases_relacao', 'innerHTML', $this->view->getListRecursoFase($this->dao->getListRecursoFase($reqiexoid)));
		return $objResponse->getXML();
	}
	
	/**
	 * excluirPlanejamentoFase()
	 *
	 * @param none
	 * @return none
	 */
	public function excluirPlanejamentoFaseAction() {
		$_SESSION['aba_ativa'] = 1;
	    $vData = array();
		$vData = $this->dao->unsetPlanejamentoFase();
		if($vData['action_st'] == 'ok'){
			$vData = $this->dao->getDadosSTI($vData['sti']);
			$vData['action_msg_pfase'] = 'Exclusão de planejamento de fase realizado com sucesso!';
		}
		$this->view->dadosSTI($vData);
	}

	/**
	 * preparaFormAlteracaoRecursoFase()
	 *
	 * @param $reqieroid (ID do tipo)
	 * @return none
	 */
	public function preparaFormAlteracaoRecursoFase($reqieroid = 0) {
		$reqieroid = (int) $reqieroid;
		$objResponse = new xajaxResponse();
		$vData = $this->dao->getDadosRecursoFase($reqieroid);
		$objResponse->AddAssign('sti_fase_inicio','value', $vData['reqierdt_previsao_inicio']);
		$objResponse->AddAssign('sti_fase_final','value', $vData['reqierdt_previsao_fim']);
		$objResponse->AddAssign('sti_fase_horas','value', $vData['reqierhoras_estimadas']);
		$objResponse->AddAssign('reqieroid','value', $reqieroid);
		$objResponse->AddAssign('combo_recurso', 'innerHTML', $this->view->getComboBoxListRecursoFase($this->dao->getComboBoxListRecursoFase($vData['reqierreqiexoid']), $vData['reqierusuoid_executor']));
		$objResponse->AddAssign('fase_bt_incluir','style.display', 'none');
		$objResponse->AddAssign('fase_bt_confirmar','style.display', 'block');
		return $objResponse->getXML();
	}
	
	
	/**
	 * alterarPlanejamentoFase()
	 *
	 * @param none
	 * @return none
	 */
	public function alterarPlanejamentoFaseAction() {
		$_SESSION['aba_ativa'] = 1;
	    $vData = array();
		$vData = $this->dao->updatePlanejamentoFase();
		if($vData['action_st'] == 'ok'){
			$vData = $this->dao->getDadosSTI($vData['sti']);
			$vData['action_msg_pfase'] = 'Planejamento de fase alterado com sucesso!';
		}
		$this->view->dadosSTI($vData);
	}

	/**
	 * getFasesAbas()
	 *
	 * @param $fluxo_fase_lancamento (fluxo + lancamento, faseAtiva, fAtualizaDivRel)
	 * @return none
	 */
	public function getFasesAbas($fluxo_fase_lancamento = '', $faseAtiva=0) {
		$vAbas = array();
		$objResponse = new xajaxResponse();
		$vAbas = $this->dao->getFasesAbas($fluxo_fase_lancamento);
		if($vAbas['qtd_itens'] > 0){
			$objResponse->AddAssign('fases_abas', 'innerHTML', $this->view->getFasesAbas($vAbas, $faseAtiva));
		}else{
			$strHtmlForm = '<table width="98%" align="left"><tr height="80"><td><span>&nbsp;&nbsp;&nbsp;&nbsp; Selecione um fluxo.</span></td></tr></table>';
			$objResponse->AddAssign('fases_abas', 'innerHTML', $strHtmlForm);
		}
		return $objResponse->getXML();
	}
	
	/**
	 * getFasesAbasForm()
	 *
	 * @param $fase_lancamento_sti (combinação de fase+lancamento+sti)
	 * @return none
	 */
	public function getFasesAbasForm($fase_lancamento_sti = '') {
	    $vAbasForm = array();
		$vAbasAnexos = array();
		$vAbasHistorico = array();
		$objResponse = new xajaxResponse();
		$vAbasForm = $this->dao->getFasesAbasForm($fase_lancamento_sti);
		if($vAbasForm['qtd_itens'] > 0){
		    $objResponse->AddAssign('fases_form', 'innerHTML', $this->view->getFasesAbasForm($vAbasForm));
		    $vAbasAnexos = $this->dao->getFasesAnexos($vAbasForm['relacao'][0]['reqieroid']);
		    $objResponse->AddAssign('recurso_lista_anexos', 'innerHTML', $this->view->getFasesAnexos($vAbasAnexos));
		    $vAbasHistorico = $this->dao->getFasesHistorico($vAbasForm['relacao'][0]['reqieroid']);
		    $objResponse->AddAssign('recurso_historico', 'innerHTML', $this->view->getFasesHistorico($vAbasHistorico));
		}else{
			$strHtmlForm = '<table class="tableMoldura" id="form_config_fase" width="98%" align="center"><tr height="80"><td><span>&nbsp;&nbsp;&nbsp;&nbsp;A fase não possui recursos programados.</span></td></tr></table>';
			$objResponse->AddAssign('fases_form', 'innerHTML', $strHtmlForm);
		}
		return $objResponse->getXML();
	}

	/**
	 * getFasesAbasFormRecurso()
	 *
	 * @param $reqieroid (ID do recurso + fase)
	 * @return none
	 */
	public function getFasesAbasFormRecurso($reqieroid = 0) {
	    $vAbasForm = array();
	    $vAbasAnexos = array();
	    $vAbasHistorico = array();
	    $objResponse = new xajaxResponse();
	    $vAbasForm = $this->dao->getFasesAbasFormRecurso($reqieroid);
	    if($vAbasForm['qtd_itens'] > 0){
	        $objResponse->AddAssign('fases_form_area_recurso', 'innerHTML', $this->view->getFasesAbasFormRecurso($vAbasForm));
	        $vAbasAnexos = $this->dao->getFasesAnexos($reqieroid);
	        $objResponse->AddAssign('recurso_lista_anexos', 'innerHTML', $this->view->getFasesAnexos($vAbasAnexos));
	        $vAbasHistorico = $this->dao->getFasesHistorico($reqieroid);
	        $objResponse->AddAssign('recurso_historico', 'innerHTML', $this->view->getFasesHistorico($vAbasHistorico));
	    }else{
	        $strHtmlForm = '<table width="98%" align="center"><tr height="80"><td><span>&nbsp;&nbsp;&nbsp;&nbsp;Recurso não possui dados.</span></td></tr></table>';
	        $objResponse->AddAssign('fases_form_area_recurso', 'innerHTML', $strHtmlForm);
	        $objResponse->AddAssign('recurso_lista_anexos', 'innerHTML', '');
	        $objResponse->AddAssign('recurso_historico', 'innerHTML', '');
	    }
	    return $objResponse->getXML();
	}
	
	
	
	/**
	 * setFaseExecucaoRecurso()
	 *
	 * @param $vFormData
	 * @return none
	 */
	public function setFaseExecucaoRecurso($vFormData = array()) {
		$vOper = array();
		$vAbasAnexos = array();
		$vAbasHistorico = array();
		$objResponse = new xajaxResponse();
		$vOper = $this->dao->setFaseExecucaoRecurso($vFormData);

		if((int)$vOper['reqierprogresso'] == 100) {
			$retornoDefeito = $this->dao->atualizarDefeitosAbaFase($vFormData);
		}
		
		$objResponse->AddAssign('fases_form_area_recurso_msg', 'innerHTML', $vOper['action_msg']);
		if($vOper['conclusao_st'] == 'S'){
			$objResponse->AddAssign('cbx_concluir_execucao', 'checked', true);
		}
		if($vOper['inicio_st'] == 'S'){
			$objResponse->AddAssign('cbx_iniciar_execucao', 'checked', true);
		}
		// Chama tabela anexos
		$vAbasAnexos = $this->dao->getFasesAnexos($vFormData['reqieroid_sel']);
		$objResponse->AddAssign('recurso_lista_anexos', 'innerHTML', $this->view->getFasesAnexos($vAbasAnexos));
        // Atualização de campos
		$objResponse->AddAssign('reqierprogresso', 'value', $vOper['reqierprogresso']);
		$objResponse->AddAssign('reqierdt_inicio', 'value', $vOper['reqierdt_inicio']);
		$objResponse->AddAssign('reqierdt_conclusao', 'value', $vOper['reqierdt_conclusao']);
		if (isset($vOper["qtd_bugs_mantis"])) {
			$objResponse->AddAssign('defeito_testes', 'value', $vOper['qtd_bugs_mantis']);
			$objResponse->AddAssign('defeito_testes_anterior', 'value', $vOper['qtd_bugs_mantis']);
		}
		
		
		// Chama tabela do histórico
		$vAbasHistorico = $this->dao->getFasesHistorico($vFormData['reqieroid_sel']);
		$objResponse->AddAssign('recurso_historico', 'innerHTML', $this->view->getFasesHistorico($vAbasHistorico));
		return $objResponse->getXML();
		
	}

	public function atualizarDefeitosAbaFase($vFormData = array())
	{
		$vOper = array();
		$objResponse = new xajaxResponse();
		$vOper = $this->dao->atualizarDefeitosAbaFase($vFormData);

		$objResponse->AddAssign('fases_form_area_recurso_msg', 'innerHTML', $vOper['action_msg']);

		return $objResponse->getXML();
	}


	/**
	 * enviarArquivoAnexoAction()
	 *
	 * @param none 
	 * @return none
	 */
	public function enviarArquivoAnexoAction() {
		$vOper = array();
		$strScript = '';
		$vOper = $this->dao->setArquivoAnexo();
		$strScript .= '<script type="text/javascript">';
		$vOper['action_msg'] = utf8_encode($vOper['action_msg']);
		$strScript .= "parent.retornoArquivoAnexo('" . $vOper['action_msg'] . "', '" . $vOper['action_st'] . "'," . $vOper['reqieroid'] . ")";
		$strScript .= '</script>';
		echo $strScript;
		return true;
	}

	/**
	 * getFasesAnexos()
	 *
	 * @param $reqieroid (ID_execucao_recurso)
	 * @return none
	 */
	public function getFasesAnexos($reqieroid = '') {
		$vAbasAnexos = array();
		$objResponse = new xajaxResponse();
		$vAbasAnexos = $this->dao->getFasesAnexos($reqieroid);
		$objResponse->AddAssign('recurso_lista_anexos', 'innerHTML', $this->view->getFasesAnexos($vAbasAnexos));
		return $objResponse->getXML();
	}
	
	/**
	 * getFasesHistorico()
	 *
	 * @param $reqieroid (ID_execucao_recurso)
	 * @return none
	 */
	public function getFasesHistorico($reqieroid = '') {
		$vAbasHistorico = array();
		$objResponse = new xajaxResponse();
		$vAbasHistorico = $this->dao->getFasesHistorico($reqieroid);
		$objResponse->AddAssign('recurso_historico', 'innerHTML', $this->view->getFasesHistorico($vAbasHistorico));
		return $objResponse->getXML();
	}


	/**
	 * excluiAnexo()
	 *
	 * @param $reqioid (ID_sti), $reqieroid (ID_execucao_recurso), $riaoid (ID_anexo)
	 * @return objResponse
	 */
	public function excluiAnexo($reqioid=0, $reqieroid=0, $riaoid=0) {
		$vOper = array();
		$objResponse = new xajaxResponse();
		$vOper = $this->dao->excluiAnexo($reqioid, $reqieroid, $riaoid);
		$objResponse->AddAssign('fases_form_area_recurso_msg', 'innerHTML', '<span class="msg"> ' . $vOper['action_msg'] . '</span>');
		if($vOper['action_st'] == 'ok'){
    		// Chama tabela anexos
    		$vAbasAnexos = $this->dao->getFasesAnexos($reqieroid);
    		$objResponse->AddAssign('recurso_lista_anexos', 'innerHTML', $this->view->getFasesAnexos($vAbasAnexos));
    		// Chama tabela do histórico
    		$vAbasHistorico = $this->dao->getFasesHistorico($reqieroid);
    		$objResponse->AddAssign('recurso_historico', 'innerHTML', $this->view->getFasesHistorico($vAbasHistorico));
    	}
		return $objResponse->getXML();
	}
	

	/**
	 * gerenciarFluxosAction()		
	 *
	 * @param none
	 * @return none
	 */
	public function gerenciarFluxosAction() {
	    $_SESSION['aba_ativa'] = 2;
	    $reqifoid = (int) $_POST['reqifoid'];
	    $vData = array();
	    if($reqifoid > 0){
	        $vData = array_merge($_POST, $this->dao->getDadosFluxo($reqifoid));
        }else{
	        $vData = array_merge($vData, $_POST);
	        $vData['reqifdescricao'] =  '';
	        $vData['reqifusuoid_responsavel'] =  '';
        }
        $this->view->getDadosFluxo($vData);
	}


	/**
	 * confirmarNovoFluxoAction()
	 *
	 * @param none
	 * @return none
	 */
	public function confirmarNovoFluxoAction(){
	    $_SESSION['aba_ativa'] = 2;
	    $vData = array();
	    $vData = $this->dao->confirmarNovoFluxo();
	    $this->view->getDadosFluxo($vData);
	}

	/**
	 * excluirFluxoAction()
	 *
	 * @param none
	 * @return none
	 */
	public function excluirFluxoAction(){
	    $_SESSION['aba_ativa'] = 2;
	    $vData = array();
	    $vData = $this->dao->excluirFluxo();
	    $this->view->getDadosFluxo($vData);
	}
	

	/**
	 * adicionarFaseFluxoAction()
	 *
	 * @param none
	 * @return none
	 */
	public function adicionarFaseFluxoAction(){
	    $_SESSION['aba_ativa'] = 2;
	    $vData = array();
	    $vData = $this->dao->adicionarFaseFluxo();
	    $this->view->getDadosFluxo($vData);
	}
	
	/**
	 * excluirFaseFluxoAction()
	 *
	 * @param none
	 * @return none
	 */
	public function excluirFaseFluxoAction(){
	    $_SESSION['aba_ativa'] = 2;
	    $vData = array();
	    $vData = array_merge($this->dao->excluirFaseFluxo(), $_POST);
	    $this->view->getDadosFluxo($vData);
	}


	/**
	 * gerenciarFasesAction()
	 *
	 * @param none
	 * @return none
	 */
	public function gerenciarFasesAction() {
	    $_SESSION['aba_ativa'] = 3;
	    $reqifsoid = (int) $_POST['reqifsoid'];
	    $vData = array();
	    if($reqifsoid > 0){
	        $vData = array_merge($_POST, $this->dao->getDadosFase($reqifsoid));
	    }else{
	        $vData = array_merge($vData, $_POST);
	        $vData['reqifsdescricao'] = '';
	    }
	    $this->view->getDadosFase($vData);
	}
	
	
	/**
	 * confirmarNovaFaseAction()
	 *
	 * @param none
	 * @return none
	 */
	public function confirmarNovaFaseAction(){
	    $_SESSION['aba_ativa'] = 3;
	    $vData = array();
	    $vData = $this->dao->confirmarNovaFase();
	    $this->view->getDadosFase($vData);
	}
	
	/**
	 * excluirFluxoAction()
	 *
	 * @param none
	 * @return none
	 */
	public function excluirFaseAction(){
	    $_SESSION['aba_ativa'] = 3;
	    $vData = array();
	    $vData = $this->dao->excluirFase();
	    $this->view->getDadosFase($vData);
	}

	/**
	 * atualizarFaseAction()
	 * @param none
	 * @return none
	 */
	public function atualizarFaseAction(){
		$_SESSION['aba_ativa'] = 3;
	    $vData = array();
	    $vData = $this->dao->atualizarFase();
	    $this->view->getDadosFase($vData);
	}
	//=======================================================


	/**
	 * gerenciarFuncoesAction()
	 *
	 * @param none
	 * @return none
	 */
	public function gerenciarFuncoesAction() {
	    $_SESSION['aba_ativa'] = 4;
	    $reqifcoid = (int) $_POST['reqifcoid'];
	    $vData = array();
	    if($reqifcoid > 0){
	        $vData = array_merge($_POST, $this->dao->getDadosFuncao($reqifcoid));
	    }else{
	        $vData = array_merge($vData, $_POST);
	        $vData['reqifcdescricao'] = '';
	    }
	    $this->view->getDadosFuncao($vData);
	}
	
	
	/**
	 * confirmarNovaFuncaoAction()
	 *
	 * @param none
	 * @return none
	 */
	public function confirmarNovaFuncaoAction(){
	    $_SESSION['aba_ativa'] = 4;
	    $vData = array();
	    $vData = $this->dao->confirmarNovaFuncao();
	    $this->view->getDadosFuncao($vData);
	}
	
	/**
	 * excluirFuncaoAction()
	 *
	 * @param none
	 * @return none
	 */
	public function excluirFuncaoAction(){
	    $_SESSION['aba_ativa'] = 4;
	    $vData = array();
	    $vData = $this->dao->excluirFuncao();
	    $this->view->getDadosFuncao($vData);
	}
	
	
	/**
	 * adicionarFuncaoUsuarioAction()
	 *
	 * @param none
	 * @return none
	 */
	public function adicionarFuncaoUsuarioAction(){
	    $_SESSION['aba_ativa'] = 4;
	    $vData = array();
	    $vData = $this->dao->adicionarFuncaoUsuario();
	    $this->view->getDadosFuncao($vData);
	}
	
	/**
	 * excluirFuncaoUsuarioAction()
	 *
	 * @param none
	 * @return none
	 */
	public function excluirFuncaoUsuarioAction(){
	    $_SESSION['aba_ativa'] = 4;
	    $vData = array();
	    $vData = array_merge($this->dao->excluirFuncaoUsuario(), $_POST);
	    $this->view->getDadosFuncao($vData);
	}

	/**
	 * acompanhamentoAction()
	 *
	 * @param none
	 * @return none
	 */
	public function acompanhamentoAction() {
	    $vPesquisa = array();
	    $_SESSION['aba_ativa'] = 5;
	    // $vPesquisa = $this->dao->getAcompanhamentoResult();
	    $this->view->acompanhamentoFormResult($vPesquisa);
	}
	
    public function ajaxDesenhaDetalhesApontamentos() {
        $sti = $_GET['sti'];
        $fase = $_GET['fase'];
        $lancamento = $_GET['lancamento'];

        $apontamentos = $this->dao->getDetalhesApontamentos($sti, $fase, $lancamento);

        $html = $this->view->desenhaDetalhesApontamentos($apontamentos);

        echo $html;
        exit();
    }
}