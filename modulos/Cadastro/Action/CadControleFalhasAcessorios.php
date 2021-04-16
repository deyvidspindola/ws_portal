<?php

require _MODULEDIR_ . 'Cadastro/DAO/CadControleFalhasAcessoriosDAO.php';

/**
 * Classe responsável pelas regras de negócio do controle de falhas de acessórios
 * @author marcelo.fuchs <marcelo.fuchs@meta.com.br>
 * @package Cadastro
 * @since 07/06/2013
 */

class CadControleFalhasAcessorios {
	
	const ITEM_ACAO				= 1;
	const ITEM_COMPONENTE		= 2;
	const ITEM_DEFEITO			= 3;
	
	/**
	 * Método Construtor
	 */
	public function __construct() {	
		global $conn;
		$this->dao = new CadControleFalhasAcessoriosDAO($conn);
	}
	
	public function index(){
		include(_MODULEDIR_ . 'Cadastro/View/cad_controle_falhas_acessorios/index.php');
	}
	
	
	/**
	 * Aciona a busca no banco de dados da pesquisa
	 */
	public function buscarRegistro(){
		
		$acessorio 	= isset($_POST['acessorio']) 	? $_POST['acessorio'] 	: 0;
		$itemFalha 	= isset($_POST['item_falha']) 	? $_POST['item_falha'] 	: 0;
		$descricao 	= isset($_POST['descricao']) 	? $_POST['descricao'] 	: '';
		$descricao 	= $this->tratarDescricaoPesquisa($descricao);
		$resultado 	= array();
		$mensagem 	= "";	
		$totalRegistros = 0;

		try {

			switch ($itemFalha){
				
				case $this::ITEM_ACAO :
					$resultado = $this->dao->pesquisarItemAcao($acessorio, $descricao);
					break;
					
				case $this::ITEM_COMPONENTE:
					$resultado = $this->dao->pesquisarItemComponente($acessorio, $descricao);
					break;
					
				case $this::ITEM_DEFEITO:
					$resultado = $this->dao->pesquisarItemDefeito($acessorio, $descricao);
					break;			
			}
			
			$totalRegistros = (count($resultado));
			$_SESSION['descricao_acessorio'] = $this->recuperaDescricaoFalha($itemFalha); 
			
			
			include(_MODULEDIR_ . 'Cadastro/View/cad_controle_falhas_acessorios/index.php');
		} catch (Exception $e) {
			//$mensagem = $e->getMessage();
		}

	}
	
	/**
	 * Inativa o registro no banco de dados
	 */
	public function inativarRegistro(){

		$itens		= isset($_POST['chk_codigo']) 		? $_POST['chk_codigo'] 	: array();
		$itemFalha 	= isset($_POST['item_falha']) 		? $_POST['item_falha'] 	: 0;
		$acessorio 	= isset($_POST['$acessorio']) 		? $_POST['$acessorio'] 	: 0;
		$resultado 	= false;
		$mensagem 	= "";
		$itemFalha 	= (int)$itemFalha;		

		try {
		
			$this->dao->abrirTransacao();
			
			switch ($itemFalha){
					
				case $this::ITEM_ACAO :
					
					foreach ($itens as $ifaoid ){
					
						$resultado = $this->dao->inativarItemAcao($ifaoid);
					}										
					break;
		
				case $this::ITEM_COMPONENTE:
					
					foreach ($itens as $ifcoid ){
				
						$resultado = $this->dao->inativarItemComponente($ifcoid);
					}	
					break;
		
				case $this::ITEM_DEFEITO:
					
					foreach ($itens as $ifdoid ){
					
						$resultado = $this->dao->inativarItemDefeito($ifdoid);
					}	
					break;
			}
			
			$this->dao->fecharTransacao();
			$_SESSION['flash_message'] = 'Registro Excluído.';
			$_SESSION['descricao_acessorio'] = $this->recuperaDescricaoFalha($itemFalha);
			
			$this->buscarRegistro();
		
		
		} catch (Exception $e) {
		
			$this->dao->abortarTransacao();
			//$mensagem = $e->getMessage();
		}

	}
	
	/**
	 * Insere novo registro no banco de dados
	 */
	public function inserirRegistro(){		
		
		$acessorio = isset($_POST['acessorio']) 	? $_POST['acessorio'] 	: 0;
		$itemFalha = isset($_POST['item_falha']) 	? $_POST['item_falha'] 	: 0;
		$descricao = isset($_POST['descricao']) 	? $_POST['descricao'] 	: '';	
		$descricao = trim($descricao);
		$resultado = false;
		$mensagem = "";
		$itemFalha = (int)$itemFalha;
				
		try {
		
			$this->dao->abrirTransacao();
			
			switch ($itemFalha){
					
				case $this::ITEM_ACAO :
					$resultado = $this->dao->inserirItemAcao($acessorio, $descricao);
					break;
		
				case $this::ITEM_COMPONENTE:
					$resultado = $this->dao->inserirItemComponente($acessorio, $descricao);
					break;
		
				case $this::ITEM_DEFEITO:
					$resultado = $this->dao->inserirItemDefeito($acessorio, $descricao);
					break;
			}
			
			$this->dao->fecharTransacao();	
			$_SESSION['flash_message'] = 'Registro Incluído.';
			$_SESSION['descricao_acessorio'] = $this->recuperaDescricaoFalha($itemFalha);
			$this->buscarRegistro();			
			
			
		} catch (Exception $e) {
		
			$this->dao->abortarTransacao();
			//$mensagem = $e->getMessage();
		}

	}	
	
	
	/**
	 * Substitui caracteres especiais e espaçosde forma a adequar para a busca em banco
	 * @param string $descricao
	 * @return string
	 */
	private function tratarDescricaoPesquisa($descricao){
	
		$descricao = trim($descricao);
	
		$texto = preg_replace("[^a-z A-Z 0-9.,/()]", "", strtr($descricao, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ", "__________________________"));
	
		$texto = str_replace(' ', '%', $texto);
	
		return $texto;
	
	}	
	
	/**
	 * Recupera a descrição da combo Item Acessorio
	 * @param int $codigo
	 * @return string
	 */
	public function recuperaDescricaoFalha($codigo){
		
		$codigo = (int)$codigo;
		
		switch ($codigo){
			
			case $this::ITEM_ACAO:
				$texto = "Ação Lab.";
				break;
			case $this::ITEM_COMPONENTE:
				$texto = "Componente Afetado";
				break;
			case $this::ITEM_DEFEITO:
				$texto = "Defeito Lab.";
				break;						
		}
		
		return $texto;
		
	}
	
	
}