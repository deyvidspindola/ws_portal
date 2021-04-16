<?php

/**
 * @file CadQuestionarioPosVenda.class.php
 * @author Paulo Henrique da Silva Junior
 * @version 25/06/2013
 * @since 25/06/2013
 * @package SASCAR CadQuestionarioPosVenda.class.php
 */

require_once(_MODULEDIR_."Cadastro/DAO/CadQuestionarioPosVendaDAO.class.php");
/**
 * Action do Cadastro de Questionario Pos Venda
 */
class CadQuestionarioPosVenda {
	
	/**
	 * Acesso a dados do módulo
	 * @var CadQuestionarioPosVenda
	 */
	private $dao;
	
	/**
	 * Construtor
	 */
	public function __construct() {		

		global $conn;
		$this->dao = new CadQuestionarioPosVendaDAO($conn);		

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
	//Exibe o formulario de adicionar questão, que exibe ou esconde informações conforme o tipo item selecionado
	public function formularioQuestao($tipoAtivo = 'padrao', $questionarioid = null, $questaoid = null, $pqioid = null) {
		$pqioidDados = '';
		if ($pqioid != '') {
			$pqioidDados = $this->dao->getQuestionarioItem($pqioid);
			$pqiOpcoes = $this->dao->getItemOpc($pqioid);
			$numOpc = count($pqiOpcoes);
		}
		include _MODULEDIR_.'Cadastro/View/cad_questionario_pos_venda/padrao.php';
	}

	//Retorna todos os Tipo item, com seu correspondente atributo (padrao, checkbox, select, radio)
	public function tipoItem() {
		return $this->dao->getTipoItem();
	}

	//Retorna o tipo da pesquisa do questionário atual
	public function tipoPesquisa($pstoid) {
		return $this->dao->getTipoPesquisa($pstoid);
	}

	//Retorna o tipo da pesquisa do questionário atual
	public function excluirImagens() {
		return $this->dao->excluirImagensAntigas();
	}

	//Retorna todas as ocorrencias não excluidas
	public function ocorrencia() {
		return $this->dao->getOcorrencia();
	}
	//Retorna todas as subopções dos tipos Avaliação multipla, verificação e escolha
	public function questaoOpcoes($pqioid) {
		return $this->dao->getItemOpc($pqioid);
	}

	//Retorna o peso total do questionário
	public function pesoTotal($questaoid) {
		return $this->dao->getPesoTotal($questaoid);
	}


	public function atualizar() {
		// para manter os valores após a tentativa errada de cadastro

		$params = $this->populaValoresPost();

		$resultado = array();		

		$resultado = $this->dao->inserirDados($params);

		echo json_encode($resultado);
		die;

	}
	public function cadastrar() {
		// salvar questão
		$params = $this->populaValoresPost();

		$resultado = array();		

		$resultado = $this->dao->inserirDados($params);

		echo json_encode($resultado);
		die;

	}


	public function populaValoresPost() {
		if(isset($_POST)):
			foreach($_POST as $key => $value):
				$this->$key = $value;
			endforeach;		

			return $_POST;
		endif;
	}

}