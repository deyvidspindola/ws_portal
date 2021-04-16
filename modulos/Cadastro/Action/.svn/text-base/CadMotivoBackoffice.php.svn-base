<?php

/*
 * Persistência de dados
 */
require _MODULEDIR_.'Cadastro/DAO/CadMotivoBackofficeDAO.php';


/**
 * CadServicoSoftware.php
 * 
 * Classe para gerenciar requisições para pesquisa de suspensão/exclusão de débito automático
 * 
 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
 * @package Cadastro
 * @since 09/01/2013 
 * 
 */
class CadMotivoBackoffice
{
	private $dao;
	
	/**
	 * Método principal
	 * Inclui a view necessária para pesquisar, cadastrar e excluir
	 * 
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function index(){
		
		/**
		 * Cabecalho da pagina (menus)
		 */
		cabecalho();
		
		$this->view->motivos = $this->pesquisar(true);
		
		/*
		 * Inclui a view
		 */
		include(_MODULEDIR_.'Cadastro/View/cad_motivo_backoffice/index.php');
		
	}
	
	/**
	 * Pesquisa os motivos de suspensão
	 * 
	 * $motivo:
	 * 		Variável usada pelo método de cadastro para verificar duplicidade no banco de dados
	 * 
	 * Para efetuar a pesquisa pela tela não precisa passar parâmtros
	 *
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function pesquisar($index = false) {
			
		try{
			
			$id_motivo = (!empty($_POST['motivo_pesquisa'])) ? $_POST['motivo_pesquisa'] : null; 
			
			$motivos = $this->dao->pesquisar(utf8_decode($id_motivo));
			
			$arrMotivos = array();
			
			if ($index) {
				if (count($motivos) > 0) {
					foreach($motivos as $motivo) {
						$arrMotivos[] = array(
								'id' => $motivo['id'],
								'descricao' => utf8_encode($motivo['descricao']),
								'data_cadastro' => $motivo['data_cadastro']
						);
					}
				}
				
				return $arrMotivos;
			} else {
			
				if(empty($motivos)){
					echo json_encode($arrMotivos);
					exit;
				}
				
				foreach($motivos as $motivo) {
					$arrMotivos[] = array(
						'id' => $motivo['id'],
						'descricao' => utf8_encode($motivo['descricao']),
						'data_cadastro' => $motivo['data_cadastro']
					);
				}
			
				echo json_encode($arrMotivos);
				exit;
			}
			
		}catch (Exception $e){
			
			echo json_encode(array('error' => true, 'message' => utf8_encode($e->getMessage())));
			exit;
		}
			
	}
	
	/**
	 * Atualizar combo
	 */
	public function atualizarCombo(){
		
		$arrMotivos = array();
		
		$motivos = $this->dao->pesquisar(null);
		
		if (count($motivos) > 0) {
			foreach($motivos as $motivo) {
				$arrMotivos[] = array(
					'id' => $motivo['id'],
					'descricao' => utf8_encode($motivo['descricao'])
				);
			}
		}
		
		echo json_encode($arrMotivos);
		exit;
	}
	
	
	/**
	 * Exclui a descrição do motivo de suspensão
	 *
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function excluir() {
		
		try{
			
			pg_query($this->conn, 'BEGIN');
			
			$exclusao = $this->dao->excluir($_POST['id']);
			
			pg_query($this->conn, 'COMMIT');
			
			echo json_encode($exclusao);
			exit;	
		
		}catch (Exception $e){
			
			pg_query($this->conn, 'ABORT');
				
			echo json_encode(array('error' => true, 'message' => utf8_encode($e->getMessage())));
			exit;
		}
	}
	
	/**
	 * Cadastra a descrição do motivo de suspensão
	 * 
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function cadastrar() {
		
		try{
			
			$descricao_motivo = trim($_POST['descricao']);
			$id_motivo = trim($_POST['id']);
			
			/*
			 * Verifica se o serviço que esta tentando cadastrar ja não existe na base
			 * OBS: Não pode cadastrar dois motivos iguais
			 */
			if (empty($id_motivo)) {
				$motivo = $this->dao->getByName(utf8_decode($descricao_motivo), 'C');
			} else {
				$motivo = $this->dao->getByName(utf8_decode($descricao_motivo), 'A');
			}
			/*
			 * Se a pesquisa retornar algum resultado lança a excessão para não cadastrar igual
			 */
			if($motivo > 0){
				throw new Exception('002');
			}
			
			pg_query($this->conn, 'BEGIN');
			
			if (empty($id_motivo)) {
				$retorno = $this->dao->cadastrar(utf8_decode($descricao_motivo));
			} else {
				$retorno = $this->dao->alterar(utf8_decode($descricao_motivo), $id_motivo);
			}
			
			pg_query($this->conn, 'COMMIT');
			
			echo json_encode($retorno);
			exit;
			
		}catch (Exception $e){
			
			pg_query($this->conn, 'ABORT');
			
			echo json_encode(array('error' => true, 'message' => utf8_encode($e->getMessage())));
			exit;
		}
	}
	
	/**
	 * Construtor
	 *
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function CadMotivoBackoffice(){
	
		global $conn;
		
		$this->conn = $conn;
	
		$this->dao = new CadMotivoBackofficeDAO($conn);
	
	}
		
}