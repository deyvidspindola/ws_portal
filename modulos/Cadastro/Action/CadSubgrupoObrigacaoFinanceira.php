<?php

Class CadSubgrupoObrigacaoFinanceira {

	private $conn;
	private $dao;
	private $daoHistorico;
	private $usuoid;

	public $view;

	public function __construct($dao){

		$this->dao = $dao;
		$this->usuoid = isset($_SESSION[usuario][oid]) ? $_SESSION[usuario][oid] : 2750;

        $this->view = new stdClass();
        // $this->view->mensagemErro = '';
        // $this->view->mensagemAlerta = '';

	}

	public function setDaoHistorico($daoHistorico){
		$this->daoHistorico = $daoHistorico;
	}

	public function index(){
		$this->view->subgrupos = $this->dao->getSubgruposObrigacaoFinanceira(null);
		$this->view->historico = $this->daoHistorico->getHistorico();
		require_once _MODULEDIR_ . "Cadastro/View/cad_subgrupo_obrigacao_fin/index.php";
		
	}

	public function cadastrar(){

		try{

			$this->view->acao = 'cadastrar';

			if ($_SERVER['REQUEST_METHOD'] === 'POST'){

				if(empty($_POST['descricao'])){
					throw new Exception("O campo <b>descrição</b> é obrigatório");
				}

				$status = !empty($_POST['status']) ? $_POST['status'] : 'true';
				$subgrupo = $this->dao->inserirSubgrupoObrigacaoFinanceira($_POST['descricao'], $status, $this->usuoid);
				if($subgrupo){
					$this->view->msgSucesso = 'Subgrupo cadastrado com sucesso';					
					$subgrupo = $this->dao->getSubgruposObrigacaoFinanceiraById($subgrupo->ofsgoid);
					$this->gerarHistorico($subgrupo);
				}

			}

		}catch(Exception $e){
			$this->view->msgErro = $e->getMessage();
		}

		require_once _MODULEDIR_ . "Cadastro/View/cad_subgrupo_obrigacao_fin/cadastrar.php";
		
	}

	public function editar(){

		try{

			$this->view->acao = 'editar';

			if(empty($_GET['id']) || !$subgrupo = $this->dao->getSubgruposObrigacaoFinanceiraById($_GET['id'])){
				header('Location: cad_subgrupo_obrigacao_fin.php');
				exit;
			}

			if ($_SERVER['REQUEST_METHOD'] === 'POST'){

				if(empty($_POST['descricao'])){
					throw new Exception("O campo <b>descrição</b> é obrigatório");
				}

				$status = !empty($_POST['status']) ? $_POST['status'] : 'true';

				if($this->dao->atualizarSubgrupoObrigacaoFinanceira($_GET['id'], $_POST['descricao'], $status)){
					$this->view->msgSucesso = 'Subgrupo alterado com sucesso';
					$subgrupoAntigo = $subgrupo;
					$subgrupo = $this->dao->getSubgruposObrigacaoFinanceiraById($_GET['id']);
					$this->gerarHistorico($subgrupo,$subgrupoAntigo);
				}

			}

			$this->view->subgrupo = $subgrupo;

		}catch(Exception $e){
			$this->view->msgErro = $e->getMessage();
		}

		require_once _MODULEDIR_ . "Cadastro/View/cad_subgrupo_obrigacao_fin/editar.php";		

	}

	public function pesquisar(){
		$limite = 10;
		$this->view->subgrupos = $this->dao->getSubgruposObrigacaoFinanceira($_GET["descricao"]);
		$this->view->historico = $this->daoHistorico->getHistorico();
		require_once _MODULEDIR_ . "Cadastro/View/cad_subgrupo_obrigacao_fin/index.php";
		
	}


	public function gerarHistorico($subgrupoNovo,$subgrupoAntigo = null){
		$this->daoHistorico->usuoid = $this->usuoid;
		if (!$subgrupoAntigo) {
			if (!$this->daoHistorico->inserirHistorico($subgrupoNovo->ofsgoid, CadSubgrupoObrigacaoFinanceiraHistoricoDAO::ACAO_INCLUSAO, null, $subgrupoNovo->ofsgdescricao)){
				$this->view->msgErro = CadSubgrupoObrigacaoFinanceiraHistoricoDAO::ERRO_QUERY_INSERT;
			}
		} else {
			if ($subgrupoAntigo->ofsgdescricao != $subgrupoNovo->ofsgdescricao) {
				$acao = CadSubgrupoObrigacaoFinanceiraHistoricoDAO::ACAO_ALTERACAO;
				if (!$this->daoHistorico->inserirHistorico($subgrupoNovo->ofsgoid, $acao, $subgrupoAntigo->ofsgdescricao, $subgrupoNovo->ofsgdescricao)){
					$this->view->msgErro = CadSubgrupoObrigacaoFinanceiraHistoricoDAO::ERRO_QUERY_UPDATE;
				}
			}

			if ($subgrupoAntigo->ofsgstatus == 't' && $subgrupoNovo->ofsgstatus == 'f') {
				$acao = CadSubgrupoObrigacaoFinanceiraHistoricoDAO::ACAO_INATIVACAO;
				if (!$this->daoHistorico->inserirHistorico($subgrupoNovo->ofsgoid, $acao, $subgrupoNovo->ofsgdescricao, $subgrupoNovo->ofsgdescricao)){
					$this->view->msgErro = CadSubgrupoObrigacaoFinanceiraHistoricoDAO::ERRO_QUERY_UPDATE;
				}
			} else if ($subgrupoAntigo->ofsgstatus == 'f' && $subgrupoNovo->ofsgstatus == 't') {
				$acao = CadSubgrupoObrigacaoFinanceiraHistoricoDAO::ACAO_REATIVACAO;
				if (!$this->daoHistorico->inserirHistorico($subgrupoNovo->ofsgoid, $acao, $subgrupoNovo->ofsgdescricao, $subgrupoNovo->ofsgdescricao)){
					$this->view->msgErro = CadSubgrupoObrigacaoFinanceiraHistoricoDAO::ERRO_QUERY_UPDATE;
				}
			}
		}
	}
}