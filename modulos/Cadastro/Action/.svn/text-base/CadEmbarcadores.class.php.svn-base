<?php
/**
 * @file CadEmbarcadores.class.php
 * @author Diego de Campos Noguês - diegocn@brq.com
 * @version 17/06/2013
 * @since 17/06/2013
 * @package SASCAR CadEmbarcadores.class.php 
 */

require_once(_MODULEDIR_."Cadastro/DAO/CadEmbarcadoresDAO.class.php");
require_once(_SITEDIR_ . "includes/classes/Formulario.class.php");

/**
 * Action do Cadastro de Embarcadores
 */
class CadEmbarcadores {
	
	private $dao;

	public function __construct() {		

		global $conn;
		$this->dao = new CadEmbarcadoresDAO($conn);		
	}
    
    public function __set($var, $value) {
        $this->$var = $value;
    }
    
    public function __get($var) {
        return $this->$var;
    }	

	public function index($acao = 'index', $resultadoPesquisa = array(), $segdescricao = '', $mensagem = '') {
	
		cabecalho();

		$this->comboSegmentos = $this->dao->getSegmentos($this->embsegoid);
		$this->comboEstados   = $this->dao->getEstados($this->embuf, true);	

		$this->formPesquisaObj = $this->formPesquisa();

		include _MODULEDIR_.'Cadastro/View/cad_embarcadores/index.php';
	}

	public function novo($acao, $mensagem = '') {

		cabecalho();

		$this->comboSegmentos 		= $this->dao->getSegmentos($this->embsegoid);
		$this->comboEstados   		= $this->dao->getEstados($this->embuf, true);
		$this->comboTransportadoras	= array();
		$this->comboGerRisco  		= array();
		$this->exibeCabecalhoListas = '0';

		// popula array JS com os labels das opções escolhidas
		if($this->gerRiscoSelAdd != '') {
			$this->gerRiscoSelAddLabel = $this->dao->getGerRisco(" AND geroid IN(".$this->gerRiscoSelAdd.")");
			$this->exibeCabecalhoListas = '1';
		}else
			$this->gerRiscoSelAddLabel   = array();

		if($this->transpSelAdd != '') {die('->Testar<-');
			$this->transpSelAddLabel   = $this->dao->getTransportadoras(" AND traoid IN(".$this->transpSelAdd.")");
			$this->comboTranspCliente	= $this->dao->getTransportadoraCliente();
			$this->exibeCabecalhoListas = '1';
		} else
			$this->transpSelAddLabel   = array();
		
		include _MODULEDIR_.'Cadastro/View/cad_embarcadores/form.php';
	}
	
	public function editar($acao = 'editar', $mensagem = '') {	

		cabecalho();

		// pega post para passar o parametro de id
		$params = $this->populaValoresPost();

		// se deu erro no 'atualizar' volta com os campos preenchidos
		if($params['acao'] == 'editar')
			$result = $this->dao->getEmbarcador($params['emboid']);		
		else
			$result = $params;	

		$this->exibeCabecalhoListas = '1';	

		// limpa campos e seta os novos valores do form
		$this->populaValoresPost(true);
		$this->populaValoresPost(false, $result);	

		// seta variaveis para popular campos hidden e fazer o loop de valores na view
		$this->gerRiscoSelAdd = implode(',', $this->gerRiscoSelAddArr = $this->dao->getGerenciadorasPorEmbarcador($params['emboid'], true));
		$this->transpSelAdd   = implode(',', $this->transpSelAddArr   = $this->dao->getTransportadorasPorEmbarcador($params['emboid'], true));

		$this->gerRiscoSelAddLabel = $this->dao->getGerenciadorasPorEmbarcador($params['emboid'], true, true);
		$this->transpSelAddLabel   = $this->dao->getTransportadorasPorEmbarcador($params['emboid'], true, true);

		// carrega combos
		$this->comboSegmentos 		= $this->dao->getSegmentos($this->embsegoid);
		$this->comboEstados   		= $this->dao->getEstados($this->embuf, true);
		$this->comboTransportadoras = array();
		$this->comboGerRisco  		= array();
		
		include _MODULEDIR_.'Cadastro/View/cad_embarcadores/form.php';
	}
	
	public function atualizar($acao, $mensagem = '') {

		$params    = $this->populaValoresPost();
		$resultado = $this->dao->atualizaDados($params);
		$this->populaValoresPost(true);

		if ($resultado['acao'] == 'index') {
			$this->index('index', '', '', $resultado['mensagem']);
		} else {
			$this->editar('editar', $resultado['mensagem']);
		}
	}
	
	public function excluir($acao, $mensagem = '') {
		$resultado = array();
		$this->populaValoresPost();
		$resultado = $this->dao->excluirDados($this->emboid);
		$this->populaValoresPost(true);
		$this->index('index', '', '', $resultado['mensagem']);		
	}

	public function salvar($acao) {
		// para manter os valores após a tentativa errada de cadastro
		$params = $this->populaValoresPost();

		$resultado = $this->dao->inserirDados($params);

		if ($resultado['acao'] == 'index') {
			// limpa os campos para não aparecer os valores na pesquisa
			$this->populaValoresPost(true);
			$this->index('index', '', '', $resultado['mensagem']);
		} else {
			$this->novo('novo', $resultado['mensagem']);
		}
	}

	public function cancelar($acao) {
		// se cancelar, volta para a listagem
		if ($acao == 'cancelar') {
			$this->index('index', '', '', 'Operação cancelada');
		}
	}

	public function getEmbarcadores() {
		return $this->dao->pesquisa($this->populaValoresPost());
	}
	
	public function pesquisar() {
		// para manter os valores após a busca
		$params = $this->populaValoresPost();
		$resultadoPesquisa = $this->dao->pesquisa($params);	
		$this->index('pesquisar', $resultadoPesquisa);
	}

	public function populaValoresPost($clearPost = false, $params = null) {	
		if(!is_null($params)):
			$data = $params;
		else:
			$data = $_POST;
		endif;
		
		foreach($data as $key => $value):
			if($clearPost === false) {
				// TODO: alterar strtoupper para mb_strtoupper assim que estiver habilitado o mb_string
				$this->$key = (is_string($value))?strtoupper($value):$value;
			} else
				unset($this->$key);
		endforeach;		
		
		return $data;		
	}

	public function form($acao = null) {

		$form = new Formulario("form");

		$form->adicionarHidden("emboid", $this->emboid);
		$form->adicionarCampo("embnome", "text", "Nome*:", "Nome do Cliente", $this->embnome, true, 80);
		$form->adicionarCampo("embcnpj", "cnpj", "CNPJ:", "CNPJ do Cliente", $this->embcnpj, false, 20, 18);
		$form->adicionarCampo("embrua", "text", "Rua:", null, $this->embrua, false, 80);
		$form->adicionarCampo("embnumero", "text", "Número:", null, $this->embnumero, false, 15);
		$form->adicionarCampo("embcomplemento", "text", "Compl.:", null, $this->embcomplemento, false, 53);
		$form->agruparCampos("embnumero,embcomplemento", false);
		$form->adicionarCampo("embbairro", "text", "Bairro:", null, $this->embbairro, false, 53);
		$form->adicionarCampo("embcidade", "text", "Cidade:", null, $this->embcidade, false, 53);
		$form->adicionarSelect("embuf", "Estado:", "Estado", $this->embuf, $this->comboEstados, false, 'Selecione');
		$form->adicionarCampo("embcep", "text", "CEP:", null, $this->embcep, false, 15, 15);
		$form->agruparCampos("embuf,embcep", false);
		$form->adicionarCampo("embcontato", "text", "Contato:", null, $this->embcontato, false, 20);
		$form->adicionarCampo("embtelefone1", "text", "Fone:", null, $this->embtelefone1, false, 20, 15);
		$form->adicionarCampo("embtelefone2", "text", "Fone 2:", null, $this->embtelefone2, false, 20, 15);
		$form->adicionarCampo("embtelefone3", "text", "Fone 3:", null, $this->embtelefone3, false, 20, 15);
		$form->adicionarCampo("embemail", "text", "E-mail:", null, $this->embemail, false, 53);

		if(is_array($this->embfrota))
			$this->embfrota = $this->embfrota[1];

		if(strtoupper($this->embfrota) == 'F' || $this->embfrota == null)
			$this->embfrota = array(array('F', null, false));
		else
			$this->embfrota = array(array('T', null, true));

		$form->adicionarCheckBox("embfrota", "Possui frota própria:", $this->embfrota, false);
		$form->adicionarSelect("embsegoid", "Segmento*:", "Segmento", $this->embsegoid, $this->comboSegmentos, true, 'Selecione');
		$form->adicionarTextArea("embobservacao", "Observações:", $this->embobservacao, false, 45, 3);

		$form->adicionarCampo("embdt_alteracao", "text", "Data última atualização:", null, $this->embdt_alteracao, false, 53);

		//ADICIONANDO SUBTITULO
		$form->adicionarSubTitulo("Gerenciadoras de Risco");
		$form->adicionarCampo("gerencGet", "text", "Gerenciadoras:", null, '', false, 70, 100, 
									'<input type="button" value="Pesquisar" onclick="getGerencRisco()" style="font-size:8pt;background-image: url(\'images/fd_tr_principal.gif\');cursor:pointer;" />');
		$form->adicionarSelect("gerRiscoSel", "", null, null, $this->comboGerRisco, false, 'Selecione', '98%');
		$form->adicionarHidden("gerRiscoSelAdd", $this->gerRiscoSelAdd);
		$form->adicionarHidden("gerRiscoSelRem",'');

		//ADICIONANDO SUBTITULO
		$form->adicionarSubTitulo("Transportadoras (Clientes)");
		$form->adicionarCampo("transpCli", "text", "Transportadora Cliente: ", null, '', false, 70, 100, 
									'<input type="button" value="Pesquisar" onclick="getTranspCli()" style="font-size:8pt;background-image: url(\'images/fd_tr_principal.gif\');cursor:pointer;" />');
		$form->adicionarSelect("transpSel", "", null, null, $this->comboTransportadoras, false, 'Selecione', '98%');
		$form->adicionarHidden("transpSelAdd", $this->transpSelAdd);
		$form->adicionarHidden("transpSelRem",'');

		$acao = ($acao == 'novo') ? 'salvar' : 'atualizar';

		$form->adicionarHidden("acao", $acao);

		$form->adicionarQuadro("quadro1","Dados Principais");	

		$form->adicionarButton($acao, ucfirst($acao));
		$form->adicionarCampoAcao($acao, "onclick","valida(document.form,'".$acao."',true);");		

		$form->adicionarButton('cancelar', 'Cancelar');

		$form->adicionarQuadroButton("quadro1",$acao);
		$form->adicionarQuadroButton("quadro1","cancelar");

		if($acao == 'atualizar') {
			$form->adicionarButton('excluir', 'Excluir');
			$form->adicionarQuadroButton("quadro1","excluir");
		}

		$form->util->incluiCssJavascript();
		$form->util->abreQuadro("Cadastro de Embarcadores");
		$form->desenhar();
		$form->util->fechaQuadro();

		return $form;
	}

	public function formPesquisa($acao = null) {
		
		$form = new Formulario("form");

		$form->adicionarHidden("emboid", $this->emboid);
		$form->adicionarCampo("embnome", "text", "Nome:", "Nome do Cliente", $this->embnome, true, 50, 50);
		$form->adicionarCampo("embcnpj", "cnpj", "CNPJ:", "CNPJ do Cliente", $this->embcnpj, false, 30, 20);		
		$form->adicionarSelect("embsegoid", "Segmento:", "Segmento", $this->embsegoid, $this->comboSegmentos, false, 'Selecione', 362);
		$form->adicionarSelect("embuf", "Estado:", "Estado", $this->embuf, $this->comboEstados, false, 'Selecione', 90);		
		$form->adicionarCampo("embcidade", "text", "Cidade:", null, $this->embcidade, false, 50);

		if(!isset($this->embfrota))
			$arrCheck[] = array('0', null, false); 
		else
			$arrCheck[] = array('1', null, true);
		
		$form->adicionarCheckBox("embfrota", "Possui frota própria:", $arrCheck, false);
		
		$form->adicionarHidden("acao", 'pesquisar');
		$form->adicionarButton('pesquisar', 'Pesquisar');
		$form->adicionarButton('novo', 'Novo');	

		$form->util->incluiCssJavascript();	

		return $form;
	}

	public function transpCliente(){
		
		$retorno = array();
		
		$params = $this->populaValoresPost();
		$getClientes = $this->dao->getTransportadoraCliente($params['transpCli']);		
		
		if(count($getClientes) > 0){
			$retorno = $getClientes;
			echo json_encode($retorno);
		}else{
			echo 0;
		}

		exit;
	}

	public function gerencRisco(){
		
		$retorno = array();
		
		$params    = $this->populaValoresPost();
		
		$getGerenc = $this->dao->getGerRisco($params['gerencSel']);

		if(count($getGerenc) > 0){
			$retorno = $getGerenc;
			echo json_encode($retorno);
		}else{
			echo 0;
		}

		exit;
	}
}