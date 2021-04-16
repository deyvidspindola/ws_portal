<?php
	
	/**
	 * @author	Felipe F. de Souza Carvalho
	 * @email	fscarvalho@brq.com
	 * @since	14/01/2013
	 * */

	require_once (_MODULEDIR_ . 'Cadastro/DAO/CadTesteObrigacaoFinanceiraDAO.php');

	class CadTesteObrigacaoFin {

		private $dao;
		
		public function __construct() {
			$this->dao = new CadTesteObrigacaoFinDAO();
		}
		
		public function index(){
			
			cabecalho();
			
			require_once _MODULEDIR_ . 'Cadastro/View/cad_teste_obrigacao_fin/index.php';
		}
		
		public function listarTestesCadastrados(){
			try {
				
				$response = $this->dao->listarTestesCadastrados();
				
			} catch(Exception $e) {
				$response = array('error' => true, 'message' => utf8_encode($e->getMessage()));
			}
						
			echo json_encode($response);
		}
		
		
		public function listarObrigacoesCadastradas(){
			try {
		
				$response = $this->dao->listarObrigacoesCadastradas();
		
			} catch(Exception $e) {
				$response = array('error' => true, 'message' => utf8_encode($e->getMessage()));
			}
		
			echo json_encode($response);
		}
		
		public function pesquisar(){
			try {
				
				//Extrai filtros da pesquisa e chama função da classe dao
				$idTeste 	 = (isset($_POST['cb_pesq_teste'])) ? $_POST['cb_pesq_teste'] : '0';
				$idObrigacao = (isset($_POST['cb_pesq_obrigacao'])) ? $_POST['cb_pesq_obrigacao'] : '0';
				
				$response = $this->dao->pesquisar($idTeste, $idObrigacao);
											
			} catch (Exception $e) {
				$response = array('error' => true, 'message' => utf8_encode($e->getMessage()));
			}
			
			echo json_encode($response);
		}
			
		public function salvar(){
			try {
		
				//Extrai filtros da pesquisa e chama função da classe dao
				$idTeste 	 = isset($_POST['cb_pesq_teste'])     ? $_POST['cb_pesq_teste']     : '0';
				$idObrigacao = isset($_POST['cb_pesq_obrigacao']) ? $_POST['cb_pesq_obrigacao'] : '0';
		
				//Extrai id do usuário que efetuou a exclusão
				$idUsuario = $_SESSION['usuario']['oid'];
				
				$eptotoid = $this->dao->salvar($idTeste, $idObrigacao, $idUsuario);
		
				$response = array("eptotoid" => $eptotoid);
							
			} catch (Exception $e) {
				$response = array('error' => true, 'message' => utf8_encode($e->getMessage()));
			}
			
			echo json_encode($response);
		}

		
		public function excluirObrigacaoTeste(){
			try {

				//Extrai id para exclusão 
				$eptotoid = isset($_POST['eptotoid']) ? $_POST['eptotoid'] : '0';

				//Extrai id do usuário que efetuou a exclusão
				$idUsuario = $_SESSION['usuario']['oid'];
				
				//Chama método DAO para exclusão
				$status = $this->dao->excluirObrigacaoTeste($eptotoid, $idUsuario);
				
				if($status){
					$response = array("status" => "1");
				} else {
					$response = array("status" => "0");
				}
				
			} catch(Exception $e) {
				$response = array('error' => true, 'message' => utf8_encode($e->getMessage()));
			}
			
			echo json_encode($response);
		}
		
	}

?>
