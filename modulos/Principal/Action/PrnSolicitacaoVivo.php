<?php
require _MODULEDIR_ . 'Principal/DAO/PrnSolicitacaoVivoDAO.php';
require 'lib/Components/CampoBuscaX.class.php';


/* 
 * @author Dyorg Almeida
 * @email dyorg.almeida@meta.com.br
 * @since 23/10/2012
 * @description Módulo que permite a inclusão de Solicitação Vivo para Atendimento offline.
 */
class PrnSolicitacaoVivo {

	private $dao;
	
	/*
	 * @description lista de status
	 */
	public $status;
	
	/*
	 * @description lista de motivos
	 */
	public $motivos;
	
	/*
	 * @description componente para campo de busca
	 */
	public $component_busca_cliente;
	
	/*
	 * @description dados de retorno da solicitação
	 */
	public $solicitacao;
	
	/*
	 * @description resultado da pesquisa de solicitações
	 */
	public $listasolicitacoes = array();
	
	/*
	 * @description lista de detalhes de tratativa cadastrados na solicitação
	*/
	public $listadetalhes = array();
	
	/*
	 * @description mensagem de sucesso ou aviso na tela
	 */
	public $feedback_mensagem;
	
	/*
	 * @description mensagem de erro na tela
	*/
	public $feedback_erro;
	
	
	public function __construct() {
		
		try {

			$this->dao = new PrnSolicitacaoVivoDAO();
		
			/*
			 * carrega lista para preenchimento de combobox
			 */
			$this->status = $this->dao->listarStatus();
			$this->motivos = $this->dao->listarMotivos();
			
			/*
			 * preenche dao com dados do cabeçalho da requisição
			 */
			$this->dao->slpoid = addslashes($_REQUEST['slpoid']);
			$this->dao->slpdt_cadastro = $_POST['slpdt_cadastro'];
			$this->dao->slpclioid = $_POST['cpx_valor_cliente_nome'];
			$this->dao->slpdescricao = addslashes($_POST['slpdescricao']);
			$this->dao->slpprotocolo_vivo = $_POST['slpprotocolo_vivo'];
			$this->dao->slpslpmoid = $_POST['slpslpmoid'];
			$this->dao->slpslpsoid = $_POST['slpslpsoid'];
			$this->dao->dt_inicial_informada = $_POST['data_inicial'];
			$this->dao->dt_final_informada = $_POST['data_final'];
			$this->dao->telefone = addslashes($_POST['ddd'].$_POST['telefone']);
			$this->dao->cpf_cnpj = addslashes($_POST['cpf_cnpj']);		
		
		} catch (Exception $e) {
		
			$this->feedback_erro = $e->getMessage();
		}
	}
	
	/*
	 * válida se é possível realizar edição
	 */
	private function validarCondicaoParaRealizarEdicao(){
		
		/*
		 * verificar se solcitação esta na fase edição para prosseguir
		 */
		if (empty($this->dao->slpoid) ) return;
		
		/*
		 * busca dados da solicitação
		 */
		$solicitacao = $this->dao->buscarSolicitacao($this->dao->slpoid);
		
		/*
		 * valida se a solicitção foi encontrada
		 * caso status da solicitação seja concluído lança uma exceção para a tela
		 */
		if ($solicitacao) {
			if ($solicitacao['slpslpsoid'] == 3) throw new Exception('A solicitação não pode ser editada após status alterado para concluído');
		} 
		
	}
	
	/* 
 	 * @author Dyorg Almeida
 	 * @description Carrega tela com filtro de pesquisa e, se houver resultado de pesquisa, a lista de pesquisa.
 	 */
	public function pesquisar() {
		
		try {

			$this->listasolicitacoes = $this->dao->pesquisarSolicitacoes();
			
			if (!$this->listasolicitacoes) throw new Exception('Nenhum resultado encontrado.');
			
		} catch (Exception $e) {
			
			$this->feedback_erro = $e->getMessage();
		}
	}
	
	/*
	 * @author Dyorg Almeida
	 * @description Carregar tela para inserir ou editar solicitação
	 */
	public function cadastro() {
		
		try {
			
			$params = array(
					'id' 			=> 'cliente_id',
					'name'			=> 'cliente_nome',
					'btnFind'   	=> true,
					'btnFindText'	=> 'Pesquisar',
					'data'			=> array(
							'table'           => 'clientes',
							'fieldFindByText' => 'clinome',
							'fieldFindById'   => 'clioid',
							'fieldLabel'      => 'clinome',
							'fieldReturn'     => 'clioid'
					)
			);
			
			/*
			 * Componente resposável por gerar o campo de pesquisa por cliente
			* */
			$this->component_busca_cliente = new CampoBuscaX($params);
		
			if (!empty($this->dao->slpoid)) {
					
				$this->solicitacao = $this->dao->buscarSolicitacao($this->dao->slpoid);	
				
				if (!$this->solicitacao) throw new Exception('Não foi encontrado registro para o id informado');
				
				$this->listadetalhes = $this->dao->buscarDetalhes($this->dao->slpoid);
			} 
			
		} catch (Exception $e) {
				
			$this->feedback_erro = $e->getMessage();
		}
	}	
	
	/*
	 * @author Dyorg Almeida
	 */
	public function salvarSolicitacao() {
	
		try {
			
			/*
			 * salva dados da solicitação
			 */
			$this->dao->salvarSolicitacao();
			
			$this->feedback_mensagem = 'Solicitação salva com sucesso.';
			
		} catch (Exception $e) {
		
			$this->feedback_erro = $e->getMessage();
		}
		
		$this->cadastro();
	}
	
	public function salvarTratativa() {

		try {
			
			$this->validarCondicaoParaRealizarEdicao();

			$descricao = $_POST['slpddescricao'];

			if ($_SESSION['usuario']['vivo']['token']) {
				$descricao = $_POST['slpddescricao'] . " Atendente VIVO: " . $_SESSION['usuario']['vivo']['atendente'];
			} 
			
			$this->dao->salvarDetalhe(addslashes($descricao), $this->dao->slpoid, $this->dao->slpslpsoid);
		
			$this->feedback_mensagem = 'Detalhamento da tratativa salva com sucesso.';
			
		} catch (Exception $e) {
		
			$this->feedback_erro = $e->getMessage();
		}
		
		$this->cadastro();
	}
	
	/*
	 * @author Dyorg Almeida
	 */	
	public function salvarMotivo() {

		try {
		
			$descricao = addslashes($_POST['slpmdescricao']);
		
			if($this->dao->buscarMotivoPorDescricao($descricao) !== false) throw new Exception('Este Motivo já consta cadastrado.');
				
			/*
			 * salva motivo e atualiza lista de motivos
			 */
			$this->dao->salvarMotivo($descricao);
			$this->motivos = $this->dao->listarMotivos();
	
			
			$this->feedback_mensagem = 'Motivo salvo com sucesso.';
			
		} catch (Exception $e) {
		
			$this->feedback_erro = $e->getMessage();
		}
	}
	
	/*
	 * @author Dyorg Almeida
	 */
	public function excluirMotivo() {
	
		try {
		
			/*
			 * remove motivo
			 */
			$idmotivo = $_POST['combobox_cadastro_motivo'];
			$this->dao->removerMotivo($idmotivo);
			
			/*
			 * atualiza lista de motivos
			 */
			$this->motivos = $this->dao->listarMotivos();
			
			$this->feedback_mensagem = 'Motivo excluído com sucesso.';
		
		} catch (Exception $e) {
		
			$this->feedback_erro = $e->getMessage();
		}
		
	}	
	
}