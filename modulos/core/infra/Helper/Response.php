<?php
/**
  * Sascar - Sistema Corporativo
  *
  * LICENSE
  *
  * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
  *
  * @author Rafael Dias <rafael.dias@meta.com.br>
  * @version 07/11/2013
  * @since 07/11/2013
  * @package Core
  * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
  */

namespace infra\Helper;

use infra\ResponseDAO as DAO;

class Response {
	public $dados = '';
	public $codigo = '';
	public $mensagem = '';
	
	public function __construct(){
		
	}
	
	/**
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 11/11/2013
	 * @param mixed $dados
	 * @param string $codigo
	 * @param string $mensagem
	 * @return object
	 */
	public function setResult($dados='', $codigo='', $mensagem=''){		
		//Parâmetros
		$codigo = trim(strtoupper($codigo));
		$mensagem = trim($mensagem);
		
		$this->dados = $dados;
		
		if($codigo == 'EXCEPTION'){
			$this->dados = false;
			$this->mensagem = $dados->getMessage();
			$this->codigo = $dados->getCode();
		} else{
			$this->codigo = $codigo;
				
			if($mensagem != ''){
				$this->mensagem = $mensagem;
			} else{
				$this->mensagem = $this->getMessage($codigo);
			}
		}		
	}
	
	private function getMessage($codigo='OK') {
		$dao = new DAO();
		return $dao->getMessage($codigo);
	}
}
