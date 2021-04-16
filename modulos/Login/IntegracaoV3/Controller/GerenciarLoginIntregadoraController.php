<?php

require_once _MODULEDIR_ . 'Login/IntegracaoV3/Action/LoginIntegracaoV3Action.php';
require_once _MODULEDIR_ . 'Login/Commun/Controller/AbstractController.php';

class GerenciarLoginIntregadoraController extends AbstractController {
	
	public $software;
	public $tipo;
	public $integracao;
	public $permissaoAcessoLog;
	public $permissaoAcessoLinkNovaGerenciadora;
	
	public $idDepartamentosAcessoLog;
	public $idDepartamentosAcessoLinkNovaGerenciadora;
	
	public $resultadoPesquisa;
	
	public $listCadastroGerenciadora;
	
	
	
	public function __construct() {
		parent::__construct();
		
		$this->listCadastroGerenciadora = _PROTOCOLO_ . _SITEURL_ . "cad_gerenciadora.php";
		/** Definir regra acesso página */
		$this->idDepartamentosAcessoPagina    = array(543,606);
		$this->idDepartamentosAcessoLog       = array(543,606);
		$this->idDepartamentosAcessoLinkNovaGerenciadora = array(543,606);
		
		$this->verificarPermissaoDeAcesso();
		$this->resultadoPesquisa = 0;
		
	}
	
	public function index($acao = 'index',$resultadoPesquisa = array()) {
		if($acao == "principal" || $acao == "pesquisar"){
			$this->software 		= $this->comboSoftware();
			$this->tipo				= $this->comboTipo();
			$this->integracao 		= $this->comboPossuiIntegracaoV3();
		}
		return $this->view($acao);
	}
	
	public function principal(){
		return $this->index('principal');
	}
	
	public function pesquisar() {
		$loginIntegracaoV3Action = new LoginIntegracaoV3Action();
		
		$this->resultadoPesquisa = $loginIntegracaoV3Action->buscarGerenciadoraLista(
				$this->params['ger_nome'],
				$this->params['busca_gersoftware'],
				$this->params['busca_tipo'], 
				$this->params['possuiIntegracao']
		);
		return $this->index('principal', $this->resultadoPesquisa);
	}
	
	public function gerarAcessoIntegradora(){
		$loginIntegracaoV3Action = new LoginIntegracaoV3Action($_POST['geroid'], $this->autorAcao);
		$retorno = $loginIntegracaoV3Action->gerarAcessoIntegradora();
		
		if($retorno->retorno){
			$this->mensagemSucesso = $retorno->mensagem . "<br/>
					PARA A GERENCIADORA: " . $retorno->objeto->intnome . "<br/>" .
					"Login: " . $retorno->objeto->intlogin . "<br/> Senha: sascar"
			;
		}
		
		if($retorno->retorno == FALSE){
			$this->mensagemErro = $retorno->mensagem;
		}
		
		return $this->index('principal');
	}
	
	public function	resetarSenhaIntegradora(){
		
	    $loginIntegracaoV3Action = new LoginIntegracaoV3Action($_POST['geroid'], $this->autorAcao);
		$retorno = $loginIntegracaoV3Action->resetarSenhaIntegradora();
		
		if($retorno->retorno){
			$this->mensagemSucesso = $retorno->mensagem . "<br/>
					PARA A GERENCIADORA: " . $retorno->objeto->intnome . "<br/>" .
					"Login: " . $retorno->objeto->intlogin . "<br/> Senha: sascar";
		}
		if($retorno->retorno == FALSE){
			$this->mensagemErro = $retorno->mensagem;
		}
		$this->index('principal');
	}
	
	private function comboSoftware(){
		 $array = array
		(
			array('id_software' => '', 'nome_software' => 'Todos'),
			array('id_software' => 'S', 'nome_software' => 'SASGC'),
			array('id_software' => 'I', 'nome_software' => 'INTEGRAÇÃO'),
			array('id_software' => 'SS', 'nome_software' => 'Sem Software'),
			array('id_software' => 'SI', 'nome_software' => 'Software Indiferente')
		);
		 		
		return $array;
	}
	
	private function comboTipo(){
		 return array(
			array('tipoSoftware' => '', 'softwareNome' => 'Todos'),
			array('tipoSoftware' => '1', 'softwareNome' => 'CLIENTE'),
			array('tipoSoftware' => '6', 'softwareNome' => 'FROTAS'),
			array('tipoSoftware' => '3', 'softwareNome' => 'PA'),
			array('tipoSoftware' => '4', 'softwareNome' => 'SUPORTE SASCAR'),
			array('tipoSoftware' => '2', 'softwareNome' => 'TEGMA'),
			array('tipoSoftware' => '5', 'softwareNome' => 'TELEMETRIA')
		);
	}
	
	private function comboPossuiIntegracaoV3(){
		return array(
			array('buscaIntegracao' => "TODOS", 'descricao' => "Todos"),
			array('buscaIntegracao' => "NAO", 'descricao' => "Não"),
			array('buscaIntegracao' => "SIM", 'descricao' => "Sim"),
		);
	}
	
	
	
	private function verificarAcessoLog($idDepartamento){
	    if (in_array($idDepartamento, $this->idDepartamentosAcessoLog)){
	        return $this->permissaoAcessoLog = TRUE;
	    }
	    return $this->permissaoAcessoLog = FALSE;
	}
	
	private function verificarAcessoLinkNovaGerenciadora($idDepartamento){
	    if (in_array($idDepartamento, $this->idDepartamentosAcessoLinkNovaGerenciadora)){
	        return $this->permissaoAcessoLinkNovaGerenciadora = TRUE;
	    }
	    return $this->permissaoAcessoLinkNovaGerenciadora = FALSE;
	}
	
    protected function verificarPermissaoDeAcesso(){
        $this->verificarAcessoPagina($this->autorAcao->departamento);
        $this->verificarAcessoLog($this->autorAcao->departamento);
        $this->verificarAcessoLinkNovaGerenciadora($this->autorAcao->departamento);
    }
    
}