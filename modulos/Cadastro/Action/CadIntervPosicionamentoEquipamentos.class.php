<?php
/**
 * @file CadIntervPosicionamentoEquipamentos.class.php
 * @author Diego de Campos Noguês - diegocn@brq.com
 * @version 06/08/2013
 * @since 06/08/2013
 * @package SASCAR CadValidadesEquipamentos.class.php  
 */

require_once(_MODULEDIR_."Cadastro/DAO/CadIntervPosicionamentoEquipamentosDAO.class.php");

/**
 * Action do Cadastro de Intervalos de Posicionamento de Equipamentos
 */
class CadIntervPosicionamentoEquipamentos {
	
	private $dao;

	public function __construct() {		

		global $conn;
		$this->dao = new CadIntervPosicionamentoEquipamentosDAO($conn);

	}
    
    public function __set($var, $value) {
        $this->$var = $value;
    }
    
    public function __get($var) {
        return $this->$var;
    }	

	public function index($acao = 'index', $resultadoPesquisa = array(), $mensagem = '') {	

		// exibe mensagem quando existir
		if($mensagem != '')
			$this->retorno = $mensagem;

		// passa variaveis para a view
		$this->acao = $acao;
		$this->resultadoPesquisa = $resultadoPesquisa;

		// chama a view
		$this->view($acao);
	}

	public function novo($acao, $mensagem = '') {
		if($mensagem != '')
			$this->retorno = $mensagem;

		// chama a view
		$this->view($acao);
	}
	
	public function editar($acao = 'editar', $mensagem = '') {	
		if($mensagem != '')
			$this->retorno = $mensagem;

		// pega post para passar o parametro de id
		$params = $this->populaValoresPost();

		// se deu erro no 'atualizar' volta com os campos preenchidos
		if($params['acao'] == 'editar')
			$result = $this->dao->getInterPosicionamentoEquipamento($params['iposeqpoid']);		
		else
			$result = $params;	

		// limpa campos e seta os novos valores do form
		$this->populaValoresPost(true);
		$this->populaValoresPost(false, $result);	

		// chama a view
		$this->view($acao);		
		
	}
	
	public function atualizar($acao, $mensagem = '') {

		$params    = $this->populaValoresPost();
		$resultado = $this->dao->atualizaDados($params);
		$this->populaValoresPost(true);

		if ($resultado['acao'] == 'index') {
			$this->index('index', '', $resultado);
		} else {
			$this->editar('editar', $resultado);
		}
	}
	
	public function excluir($acao, $mensagem = '') {
		$resultado = array();
		$this->populaValoresPost();
		$resultado = $this->dao->excluirDados($this->iposeqpoid);
		$this->populaValoresPost(true);
		$this->index('index', '', $resultado);		
	}

	public function salvar($acao) {
		// para manter os valores após a tentativa errada de cadastro
		$params = $this->populaValoresPost();

		$resultado = $this->dao->inserirDados($params);		

		if ($resultado['acao'] == 'index') {
			// limpa os campos para não aparecer os valores na pesquisa
			$this->populaValoresPost(true);
			$this->index('index', '', $resultado);
		} else {
			$this->novo('novo', $resultado);
		}
	}

	public function cancelar($acao) {
		// se cancelar, volta para a listagem
		if ($acao == 'cancelar') {
			$this->index('cancelar', '', array('status' => 'alerta', 'mensagem' => 'Operação Cancelada'));
		}
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

	public function view($action, $layoutCompleto = true) {
		if($layoutCompleto)
			include _MODULEDIR_.'Cadastro/View/cad_interv_posicionamento_equipamentos/header.php';
		
		include _MODULEDIR_.'Cadastro/View/cad_interv_posicionamento_equipamentos/'.$action.'.php';
		
		if($layoutCompleto)
			include _MODULEDIR_.'Cadastro/View/cad_interv_posicionamento_equipamentos/footer.php';
	}
	
	/**
	 * mensagem de total de registros apresentados em grid
	 */
	public function getMensagemTotalRegistros($total){
		$arrMsg = array("0"=>"Nenhum registro encontrado", "1"=>"registro encontrado", "2"=>"registros encontrados");
		if($total == 0){
			return $arrMsg['0'];
		}elseif($total == 1){
			return $total." ".$arrMsg['1'];
		}elseif($total > 1){
			return $total." ".$arrMsg['2'];
		}
	}

}