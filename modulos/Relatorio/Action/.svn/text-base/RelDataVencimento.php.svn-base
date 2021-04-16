<?php
/**
 * @file RelDataVencimento.php
 * @author cassio.bueno
 * @version 19/05/2017 16:50:00
 * @since 19/05/2017 16:50:00
 * @package SASCAR RelDataVencimento.php 
 */


//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/boletagem_massiva_'.date('d-m-Y').'.txt');

//manipula os dados no BD
require(_MODULEDIR_ . "Relatorio/DAO/RelDataVencimentoDAO.php");

//Paginação
require_once _SITEDIR_ . 'lib/Components/Paginacao/PaginacaoComponente.php';

class RelDataVencimento{
	
	//atributos privados
	private $dao;
	
	private $finCalculoDivida;
	
	private $daoManutencaoPolitica;
	
	private $daoFinCalculoDivida;
	
	private $usuarioID ;
	
	private $id_campanha;
	
	private $nome_politica;
	
	private $desconto_politica;
	
	private $pasta_arquivo;
	
	private $nome_arquivo_csv;
	
	private $nome_arquivo_xml;
	
	
	//Objetos para exibicao e dados em telas
	private $view;
	
	/**
	 * Id da tabela execucao arquivo
	 * @var int
	 */
	public $earoid;
	
	
	
	public function __construct() {
	
		global $conn;
		
		if(empty($this->usuarioID)){
			$this->usuarioID = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid']: NULL;
		}
	
		$this->dao = new RelDataVencimentoDAO($conn);		
		
		//Cria objeto da view
		$this->view = new stdClass();
		
		// Ordenção e paginação
		$this->view->ordenacao = null;
		$this->view->paginacao = null;
		$this->view->totalResultados = 0;
		
	}
	
	
	/**
	 * 
	 * 
	 * @param string $param
	 */
	public function index($param=NULL){
		
		if($param['tipo'] == 'alerta'){
			//$tipo = $param['tipo'];
			$msg = $param['msg'];
			$classe = "mensagem alerta";
		}

		if($param['tipo'] == 'erro'){
			//$tipo = $param['tipo'];
			$msg = $param['msg'];
			$classe = 'mensagem erro';
		}
		
		if($param['tipo'] == 'sucesso'){
			//$tipo = $param['tipo'];
			$msg = $param['msg'];
			$classe = 'mensagem sucesso';
		}
		
		$botao = '';
	
		include (_MODULEDIR_ . 'Relatorio/View/rel_data_vencimento/index.php');
	}
		
	/**
	 * 
	 * 
	 * @throws Exception
	 * @return Ambigous <boolean, multitype:>
	 */
	public function pesquisar(){
				
		try {			
			
			//tratamento para os gets da paginação, se o usuário alter algum get, não deixa realizar a pesquisa e retorna ao index
			if(empty($_POST)) {
				if(!isset($_GET['usuario_nome']) || !isset($_GET['filter_cpf_cnpj_gerador']) ||  !isset($_GET['data_ini']) || !isset($_GET['data_fim']) ){
					
					if(isset($_SESSION['paginacao'])) {
						unset($_SESSION['paginacao']);
					}
					
					$this->index();
					exit();
				}
			}
			
			$paginacao = new PaginacaoComponente();			
			
			$pesquisa = new stdClass();
			
			$usuario_nome   = trim(isset($_GET['usuario_nome']) && $_GET['usuario_nome'] != '' ? $_GET['usuario_nome'] : 'NULL');
			$data_ini        = isset($_GET['data_ini']) && $_GET['data_ini'] != '' ? $_GET['data_ini'] : 'NULL';
			$data_fim        = isset($_GET['data_fim']) && $_GET['data_fim'] != '' ? $_GET['data_fim'] : 'NULL';
			$filter_cpf_cnpj_gerador = isset($_GET['filter_cpf_cnpj_gerador']) && $_GET['filter_cpf_cnpj_gerador'] != '' ? $_GET['filter_cpf_cnpj_gerador'] : 'NULL';
						
			
			$pesquisa->usuario_nome   = trim(isset($_POST['usuario_nome']) && $_POST['usuario_nome'] != '' ? $_POST['usuario_nome'] : $usuario_nome);
			$pesquisa->data_ini        = isset($_POST['data_ini']) && $_POST['data_ini'] != '' ? $_POST['data_ini'] : $data_ini;
			$pesquisa->data_fim        = isset($_POST['data_fim']) && $_POST['data_fim'] != '' ? $_POST['data_fim'] : $data_fim;
			$pesquisa->filter_cpf_cnpj_gerador = isset($_POST['filter_cpf_cnpj_gerador']) && $_POST['filter_cpf_cnpj_gerador'] != '' ? $_POST['filter_cpf_cnpj_gerador'] : $filter_cpf_cnpj_gerador;
			
			
			if ($pesquisa->data_ini == 'NULL' ) {
				throw new Exception ( 'A data inicial deve ser informada.' );
			}
			
			if ($pesquisa->data_fim == 'NULL' ) {
				throw new Exception ( 'A data final deve ser informada.' );
			}
			
			$quantPesquisa = $this->dao->getCampanhas($pesquisa);
			
			$this->view->totalResultados = $quantPesquisa[0]['total_registros'];
			
			$campos = array(
					''                  		=> 'Escolha',
					'titoid'       				=> 'Nº do Título',
					'titemissao'        		=> 'Emissão',
					'titvl_titulo'				=> 'Valor do Título',
					'thvdtanterior'				=> 'Vencimento',
					'thvdtposterior'			=> 'Vencimento alterado',
					'tmavdescricao'				=> 'Motivo',
					'nm_usuario' 	 		 	=> 'Nome do Usuário',
					'thvcadastro'				=> 'Data / Hora'
			);					

			if ($paginacao->setarCampos($campos)) {
				$this->view->ordenacao = $paginacao->gerarOrdenacao('titoid');
				$this->view->paginacao = $paginacao->gerarPaginacao($this->view->totalResultados);
			}			
			
			$dadosPesquisa = $this->dao->getCampanhas($pesquisa, $paginacao->buscarPaginacao(), $paginacao->buscarOrdenacao());

			$caminho = $this->pasta_arquivo;			
			
			$this->index();
			
			//$processamento = false;
			
			include (_MODULEDIR_ . 'Relatorio/View/rel_data_vencimento/pesquisar.view.php');
			

		} catch (Exception $e) {
			echo $e->getMessage();
		}	
	}	
}
?>