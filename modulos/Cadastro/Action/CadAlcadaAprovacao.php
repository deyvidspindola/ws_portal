<?php

class CadAlcadaAprovacao {

	private $dao;
	private $dao_validade;
	private $dao_intervado;
	private $dao_emergencia;
	private $dao_ciclo;
	public  $usuarioAprovador;
	
	private $combos;
	
	public function __construct() {		

		global $conn;
		$this->dao = new CadAlcadaAprovacaoDAO($conn);		
	}
    
    public function view($action, $resultadoPesquisa = array(), $layoutCompleto = true, $abas = true) {
		if($layoutCompleto)
    		include _MODULEDIR_.'Cadastro/View/cad_alcada_aprovacao/header.php';
    	
    	if($abas)
    		include _MODULEDIR_.'Cadastro/View/cad_alcada_aprovacao/abas.php';

    	if($action == 'pesquisar')
    		include _MODULEDIR_.'Cadastro/View/cad_alcada_aprovacao/index.php';
    
    	
    	    include _MODULEDIR_.'Cadastro/View/cad_alcada_aprovacao/'.$action.'.php';
    
    	if($layoutCompleto)
    		include _MODULEDIR_.'Cadastro/View/cad_alcada_aprovacao/footer.php';
    	
    }

    public function index() {
    	
        $param = array();
		$this->usuarioAprovador = $this->dao->getUsuarioAprovador();
    	$this->view('index', $param, true, false);
    }

    public function pesquisar() {
    	
    	$this->usuarioAprovador = $this->dao->getUsuarioAprovador();
    	$param = $this->populaValoresPost();
    	$this->filters = $param;
    	$this->dados = $this->dao->pesquisar($param);

    	$this->view('index', $param, true, false);

	}

	public function cadastrar(){

		$this->usuarioAprovador = $this->dao->getUsuarioAprovador();
		$this->view('cadastrar', $param, true, false);		

	}

	public function editar(){

		$alcoid = $_GET['alcoid'];
		$this->usuarioAprovador = $this->dao->getUsuarioAprovador();
		$this->dados = array();
		if($alcoid != ""){
			$this->dados = $this->dao->pesquisaById($alcoid);
		}

		$this->view('editar', '', true, false);		

	}

	public function cadastrarAlcada(){
		$param = $this->populaValoresPost();

		$this->retorno = $this->dao->cadastrarAlcada($param);

		$this->usuarioAprovador = $this->dao->getUsuarioAprovador();
		
		$this->view('index', $param, true, false);	
	}

	public function excluirAlcada(){

		$param = $this->populaValoresPost();
		
		$this->retorno = $this->dao->excluirAlcada($param['alcoid']);
		
		$this->usuarioAprovador = $this->dao->getUsuarioAprovador();
		
		$this->view('index', $param, true, false);	
	}

	public function atualizarAlcada(){
		
		$param = $this->populaValoresPost();
		
		$this->retorno = $this->dao->atualizarAlcada($param);
		
		$this->usuarioAprovador = $this->dao->getUsuarioAprovador();

		$this->view('index', $param, true, false);	
	}
	

	public function populaValoresPost($clearPost = false, $param = null) {
    	if(!is_null($param)):
    		$data = $param;
    	else:
    		$data = $_POST;
    	endif;
    
    	foreach($data as $key => $value):
    	if($clearPost === false) {
    		$this->$key = (is_string($value))?trim($value):$value;
    	} else
    		unset($this->$key);
    	endforeach;
    
    	return $data;
    }

}
