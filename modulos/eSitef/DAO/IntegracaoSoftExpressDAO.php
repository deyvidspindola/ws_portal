<?php
/**
 * @file IntegracaoSoftExpressDAO.php
 * @author marcioferreira
 * @version 09/07/2013 14:44:18
 * @since 09/07/2013 14:44:18
 * @package SASCAR IntegracaoSoftExpressDAO.php 
 */

class IntegracaoSoftExpressDAO{
	
	
	private $conn;
	
	
	// Construtor
	public function __construct($conn) {
	
		$this->conn = $conn;
	}
	
	/**
	 * Recupera dados de acesso ao Web Service da SoftExpress
	 *
	 * @param string $paramConfig
	 * @throws Exception
	 * @return array
	 */
	
	public function getDadosAcessoSoftExpress($paramConfig){

		try{
				
			if($paramConfig != ''){

				$sql =" SELECT pcsioid, pcsidescricao
						FROM parametros_configuracoes_sistemas_itens
						WHERE pcsipcsoid = '".trim($paramConfig)."'
								AND pcsidt_exclusao IS NULL ";

				if (!$rs = pg_query($this->conn, $sql)) {
					throw new Exception('ERRO: <b>Falha ao recuperar dados de acesso ao WebService da SoftExpress.</b>');
				}

				return pg_fetch_all($rs);

			}else{
				return false;
			}
			
		}catch(Exception $e){
			echo $e->getMessage();
			exit;
		}
	}
	
	
	
}