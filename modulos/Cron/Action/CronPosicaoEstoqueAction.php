<?php

/**
 * STI - 85394 Relatório Posição Estoque - CLASSE GERA POSIÇÃO ESTOQUE DIÁRIA
 * @author Bruno Luiz Kumagai Aldana - <bruno.aldana.ext@sascar.com.br>
 * @since 09/06/2015
 * @category Class
 * @package CronPosicaoEstoqueAction
 */

require_once _MODULEDIR_ . 'Cron/DAO/CronPosicaoEstoqueDAO.php';

class CronPosicaoEstoqueAction {

    private $dao;
	
	/**
	 * Inicia o método para deletar registros de 3 meses anteriores e na sequência gera a Posição Estoque Diária
	 * 
	 * @param
	 * @return boolean
	 */	
	public function iniciarPosicaoEstoque() {
		try{
			// Inicia transação
			$this->dao->begin();
			
			// Gera Posição Estoque Diária  
			$data = date("Y-m-d"); 
			
			$filtros['data_posicao'] = $data;
			
			$resultado = $this->dao->getDataPosicaoEstoque($filtros);
		 
			//verifica se já existe registro para data do insert 
			if($resultado){ 
				echo "<br />Registros de posição de estoque diária já foram inseridos na data de hoje<br />";
			}else{
				$insertPosicaoEstoqueDaria = $this->dao->insertPosicaoEstoqueDaria();
				if (!$insertPosicaoEstoqueDaria) {
					throw new Exception('Erro ao gerar posição de estoque diária tabela posicao_estoque_trimestral .') ;
				}else{
					echo "<br />Registros de posição estoque diários inseridos com sucesso<br />";
				} 
			}
			 
			// Finaliza transação
			$this->dao->commit();
			$retorno = 1;
		}
		
		catch(Exception $e) {
			// Reverte ações na transação
    		$this->dao->rollback();
            echo $e->getMessage();
			$retorno = 0; 
    	}
		
		return $retorno;
		
	}
	/**
	 * Inicia o método para deletar registros de 3 meses anteriores e na sequência gera a Posição Estoque Diária
	 *
	 * @param
	 * @return boolean
	 */
	public function deletarPosicaoEstoque() {
		try{
			// Inicia transação
			$this->dao->begin();
				
			// Deleta Estoque Trimestral
			$deletePosicaoEstoqueTrimestral = $this->dao->deletePosicaoEstoqueTrimestral();
			if (!deletePosicaoEstoqueTrimestral) {
				throw new Exception('Erro ao deletar registros de 3 meses anteriores tabela posicao_estoque_trimestral .') ;
			}
			 
			// Finaliza transação
			$this->dao->commit();
			$retorno = 1;
		}
	
		catch(Exception $e) {
			// Reverte ações na transação
			$this->dao->rollback();
			echo $e->getMessage();
			$retorno = 0;
	
		}
	
		return $retorno;
	
	}
 
    /**
     * Metodo Construtor
     */
    public function __construct() {
        $this->dao = new CronPosicaoEstoqueDAO();
    }

}