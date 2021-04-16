<?php
require_once 'modulos/Relatorio/DAO/RelConsumoSatelitalDAO.php';

/**
 * @author Dyorg Almeida <dyorg.almeida@meta.com.br>
 */
class RelConsumoSatelital {
	
	private $dao;
	
	private $error;
	
	/**
	 * @author Dyorg Almeida <dyorg.almeida@meta.com.br>
	 */
	public function RelConsumoSatelital() {

		$this->dao = new RelConsumoSatelitalDAO();
	}
	
	/**
	 * Lista de provedores de equipamentos
	 * 
	 * @author Dyorg Almeida <dyorg.almeida@meta.com.br>
	 * @since 28/12/2012
	 */
	public function listarProvedoresEmPares() {
		
		try {

			$lista = $this->dao->listarProvedoresEmPares();
			
			return $lista;
			
		} catch (Exception $e) {
			
			$this->error = 'Erro ao listar fornecedores de equipamento';
		}
		
	}
	
	/**
	 * Lista de modelos de equipamentos
	 *
	 * @author Dyorg Almeida <dyorg.almeida@meta.com.br>
	 */
	public function listarModelosEquipamentoEmPares() {
	
		try {
	
			$lista = $this->dao->listarModelosEquipamentoEmPares();
				
			return $lista;
				
		} catch (Exception $e) {
				
			$this->error = 'Erro ao listar modelos de equipamento';
		}
	
	}	
	
}