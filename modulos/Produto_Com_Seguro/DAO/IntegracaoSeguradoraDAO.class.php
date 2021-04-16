<?php
/**
 * @file IntegracaoSeguradoraDAO.class.php
 * @author marcioferreira
 * @version 01/11/2013 14:18:59
 * @since 01/11/2013 14:18:59
 * @package SASCAR IntegracaoSeguradoraDAO.class.php 
 */

//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/log_produto_seguro_'.date('d-m-Y').'.txt');

class IntegracaoSeguradoraDAO{

	/**
	 * Link de conexão com o banco
	 * @property resource
	 */
	private $connSeguradora;


	/**
	 * Construtor
	 * @param resource $conn - Link de conexão com o banco
	 */
	public function __construct($connSiggo){

		$this->connSeguradora = $connSiggo;

	}
	
	
	public function getDadosAcessoSeguradora($idSeguradora, $filtroAmbiente){
		
		try{
		
			if(empty($idSeguradora)){
				throw new Exception('O código da seguradora para buscar os dados do Web Service deve ser informado');
			}
			
			if(empty($filtroAmbiente)){
				throw new Exception('O código do parâmetro para buscar os dados do Web Service deve ser informado');
			}
		
			$sql = " SELECT pcsioid AS nome_param, 
			                pcsidescricao AS valor_param
					   FROM parametros_configuracoes_sistemas_itens    
			     INNER JOIN parametros_configuracoes_sistemas ON pcsoid = pcsipcsoid  
					  WHERE pcsipcsoid = '$filtroAmbiente' 
						AND pcsivinculo = '$idSeguradora' 
					    AND pcsidt_exclusao IS NULL
						AND pcsdt_exclusao IS NULL";
			
			if (!$result = pg_query($this->connSeguradora, $sql)) {
				throw new Exception("Falha ao recuperar dados da url para Web Service da seguradora");
			}

			if (pg_num_rows($result) > 0) {
				return pg_fetch_all($result);
			}
			
			return false;

		}catch (Exception $e){
			echo $e->getMessage();
			exit();
		}
		
	}
	

}