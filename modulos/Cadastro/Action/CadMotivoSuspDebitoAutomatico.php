<?php

/*
 * Persistência de dados
 */
require _MODULEDIR_.'Cadastro/DAO/CadMotivoSuspDebitoAutomaticoDAO.php';


/**
 * CadMotivoSuspDebitoAutomatico.php
 * 
 * Classe para gerenciar requisições para pesquisa de suspensão/exclusão de débito automático
 * 
 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
 * @package Cadastro
 * @since 27/09/2012 
 * 
 */
class CadMotivoSuspDebitoAutomatico
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
		
		/*
		 * Inclui a view
		 */
		include(_MODULEDIR_.'Cadastro/View/cad_motivo_susp_debito_automatico/index.php');
		
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
	public function pesquisar() {
			
		try{
			
			$descricao = (!empty($_POST['descricao_pesquisa'])) ? $_POST['descricao_pesquisa'] : $motivo; 
			
			$motivos = $this->dao->pesquisar(utf8_decode($descricao));
			
			if(empty($motivos)){
				echo json_encode($motivos);
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
			
		}catch (Exception $e){
			
			echo json_encode(array('error' => true, 'message' => utf8_encode($e->getMessage())));
			exit;
		}
			
	}
	
	
	/**
	 * Exclui a descrição do motivo de suspensão
	 *
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function excluir() {
		
		try{
			
			$exclusao = $this->dao->excluir($_POST['id']);
			
			echo json_encode($exclusao);
			exit;	
		
		}catch (Exception $e){
				
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
			
			/*
			 * Pesquisa o motivo de suspensão que o usuario esta tentando cadastrar
			 * OBS: Não pode cadastrar dois motivos iguais
			 */
			$motivo = $this->dao->getByName(utf8_decode($descricao_motivo));
			
			/*
			 * Se a pesquisa retornar algum resultado lança a excessão para não cadastrar igual
			 */
			
			if($motivo > 0){
				throw new Exception('002');
			}
			
			$cadastro_motivo = $this->dao->cadastrar(utf8_decode($descricao_motivo));
			
			echo json_encode($cadastro_motivo);
			exit;
			
		}catch (Exception $e){
			
			echo json_encode(array('error' => true, 'message' => utf8_encode($e->getMessage())));
			exit;
		}
	}
	
	/**
	 * Construtor
	 *
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function CadMotivoSuspDebitoAutomatico(){
	
		global $conn;
	
		$this->dao = new CadMotivoSuspDebitoAutomaticoDAO($conn);
	
	}
		
}