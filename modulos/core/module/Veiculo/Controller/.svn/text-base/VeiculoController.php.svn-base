<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
 * @version 29/08/2013
 * @since 29/08/2013
 * @package Core
 * @subpackage Classe Controladora de Veiculos
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace module\Veiculo;

use infra\ComumController,
	infra\Helper\Validacao,
	infra\Helper\Mascara,
	infra\Helper\Response;

class VeiculoController extends ComumController{
    
    private $model;
    private $response;
    
	/**
	 * Contrutor da classe
	 * 
	 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
	 * @param none
	 * @return none
     */
    public function __construct(){
        $this->model = new VeiculoModel();
        $this->response = new Response();
    }
    
    /**
     * Busca de dados de veiculo por ID/PL/RE/CH
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
     * @param $valKey (valor da chave de busca)
     * @param $tpKey (tipo da chave de busca ID/PL/RE/CH)
     * @return response ($response->dados = array/false)
     */
    public function getDados($valKey='', $tpKey='ID') { 
    	try {
	    	$valor = false;
	        $tpKey = strtoupper(trim($tpKey));
	        switch ($tpKey) {
	            case 'ID':
	                $valor = $this->model->getDadosByID($valKey);
	            break;
	            case 'PL':
	                $valor = $this->model->getDadosByPLACA($valKey);
	            break;
	            case 'RE':
	                $valor = $this->model->getDadosByRENAVAN($valKey);
	            break;
	            case 'CH':
	                $valor = $this->model->getDadosByCHASSI($valKey);
	            break;
	        }
	        
	        if($valor === false){
	        	$this->response->setResult($valor, 'VEI001');	        	
	        } else{
	        	$this->response->setResult($valor, '0');
	        }	        
	        
    	} catch (\Exception $e) {
    		$this->response->setResult($e, 'EXCEPTION');
    	}

    	return $this->response;
     }
     
    /**
     * Busca dados do proprietário de um veículo.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 24/09/2013
     * @param int $valKey (ID do veículo)
     * @return response ($response->dados = Array/false)
     */
     public function getVeiculoProprietario($valKey) {
     	$valKey = Mascara::inteiro($valKey);
     	
     	if($valKey > 0){
     		$resultSet = $this->model->getDadosByID($valKey);
     		
     		if(is_array($resultSet)){
     			$veiveipoid = Mascara::inteiro($resultSet['veiveipoid']);

     			if($veiveipoid > 0){
     				$resultSet = $this->model->getDadosVeiculoProprietario($veiveipoid);
     				
     				if(is_array($resultSet)){
     					$this->response->setResult($resultSet, '0');
     				} else{
     					$this->response->setResult(false, 'INF002');
     				}
     			} else{
     				$this->response->setResult(false, 'INF002');	
     			}
     		} else{
     			$this->response->setResult(false, 'INF002');	
     		}
     	} else{
     		$this->response->setResult(false, 'INF001');	
     	}
		
     	return $this->response;
     }
     
     /**
      * Grava/Atualiza um registro de veículo
      * 
      * campos obrigatórios: (veioid), veiplaca, veino_renavan, veichassi, veimlooid, veicor, veino_ano
      * 
      * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
      * @version 24/09/2013
      * @param array $arrayVeiculo (array associativo com dados do veículo) 
      * @param string $tpKey (I = Insert; U = Update)
      * @return response ($response->dados = $veioid/false)
      */
     public function veiculoSetDados($arrayVeiculo=array(), $tpKey='I') {
     	$tpKey = trim(strtoupper($tpKey));
     	
     	if(is_array($arrayVeiculo) && !empty($arrayVeiculo)){
     		if($tpKey == 'I'){
     			$obrigatorio = array('veiplaca', 'veichassi', 'veicor', 'veino_ano');
     			$exists  = false;
     			
     			//Verificando se os campos obrigatorios existem
     			$exists = $this->verificaCampos($obrigatorio, $arrayVeiculo);
     			
     			if($exists){
     				$dadosVeiculo = $this->model->getDadosByPLACA($arrayVeiculo['veiplaca']);
     				
     				if(is_array($dadosVeiculo)){
     					$this->response->setResult($dadosVeiculo['veioid'], '0');
     				} else{
     					$veioid = $this->model->insertVeiculo($arrayVeiculo);
     					
     					if(is_int($veioid)){
     						$this->response->setResult($veioid, '0');
     					} else{
     						$this->response->setResult(false, 'VEI002');
     					}
     				}
     			} else{
     				$this->response->setResult(false, 'INF003');
     			}     			
     		} elseif($tpKey == 'U'){
     			//Verifica se existe o ID do veiculo
     			if(array_key_exists('veioid', $arrayVeiculo)){
     				$veioid = $this->model->updateVeiculo($arrayVeiculo);
     				
     				if(is_int($veioid)){
     					$this->response->setResult($veioid, '0');
     				} else{
     					$this->response->setResult(false, 'VEI003');
     				}
     			} else{
     				$this->response->setResult(false, 'INF003');
     			}     			
     		} else{
     			$this->response->setResult(false, 'INF004');
     		}
     	} else{
     		$this->response->setResult(false, 'INF001');
     	}
     	
     	return $this->response;
     }
     
     /**
      * Grava um registro de proprietário em veiculo_proprietario
      * OBS: atualiza também os dados do proprietário na tabela veiculo
      *
      * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
      * @version 24/09/2013
      * @param array $arrayProprietario (array associativo com dados do Proprietario) campos obrigatórios:
      * @param string $tpKey Tipo da ação (I = Insert; U = Update)
      * @return response ($response->dados = $veipoid/false)
      */
     public function veiculoProprietarioSetDados($arrayProprietario=array(), $tpKey='I'){
     	$tpKey = trim(strtoupper($tpKey));
     	
     	if(is_array($arrayProprietario) && !empty($arrayProprietario)){
     		if($tpKey == 'I'){
     			$obrigatorio = array('veiptipopessoa','veipnome','veipcnpjcpf','veipusuoid_cadastro');
     			$exists = false;
     			
     			//Verificando se os campos obrigatorios existem
     			$exists = $this->verificaCampos($obrigatorio, $arrayProprietario);
     			
     			if($exists){
     				$veipoid = $this->model->insertVeiculoProprietario($arrayProprietario);
     				
     				if(is_int($veipoid)){
     					$this->response->setResult($veipoid, '0');
     				} else{
     					$this->response->setResult(false, 'VEI004');
     				}
     			} else{
     				$this->response->setResult(false, 'INF003');
     			}
     			
     		} elseif($tpKey == 'U'){
     			$resultSet = $this->model->updateVeiculoProprietario($arrayProprietario);
     			//Verificando se excluiu logicamente o veiculo.
     			if($resultSet !== false){
     				//Inserindo o veiculo novo.
     				$resultSet = $this->veiculoProprietarioSetDados($arrayProprietario, 'I');     				
     				
     				if($resultSet->dados !== false){
     					$veipoid = $resultSet->dados;
     					$veioid = $arrayProprietario['veipveioid'];
     					$dados  = array('veiveipoid' => $veipoid, 'veioid' => $veioid);
     					//Atualizando a FK do veiculo_proprietario na tabela veiculo.
     					return $this->veiculoSetDados($dados, 'U');
     				} else{
     					$this->response->setResult(false, 'VEI004');
     				}
     			} else{
     				$this->response->setResult(false, 'VEI005');
     			}
     		} else{
     			$this->response->setResult(false, 'INF004');
     		}
     	} else{
     		$this->response->setResult(false, 'INF001');
     	}
     	
     	return $this->response;
     }
     
     /**
      * Exclusão lógica de um registro de proprietário
      *
      * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
      * @version 20/09/2013
      * @param int $veipoid (ID do Proprietario)
      * @param int $usuoid (ID do usuário que esta realizando a exclusão)
      * @return response ($response->dados = true/false)
      */
     public function veiculoProprietarioDelete($veipoid=0, $usuoid=0){
     	$veipoid = Mascara::inteiro($veipoid);
     	$usuoid  = Mascara::inteiro($usuoid);
     	
     	if($veipoid > 0 && $usuoid > 0){
     		$resultSet = $this->model->veiculoProprietarioDelete($veipoid, $usuoid);
     		
     		if($resultSet){
     			$this->response->setResult(true, 'VEI006');
     		} else{
     			$this->response->setResult(false, 'VEI007');
     		}
     	} else{
     		$this->response->setResult(false, 'INF001');
     	}
     	
     	return $this->response;
     }
     
     /**
      * Exclusão lógica de registro de veículo
      *
      * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
      * @version 26/09/2013
      * @param int $veioid (ID do veículo)
      * @param int $usuoid (ID do usuário que esta realizando a exclusao)
      * @return response ($response->dados = true/false)
      */
     public function veiculoDelete($veioid=0, $usuoid=0){
     	$veioid = Mascara::inteiro($veioid);
     	$usuoid = Mascara::inteiro($usuoid);
     	
     	if($veioid > 0 && $usuoid > 0){
     		$resultSet = $this->model->veiculoDelete($veioid, $usuoid);
     		
     		if(is_array($resultSet) && !empty($resultSet)){
     			$this->response->setResult(true, 'VEI008');
     		} else{
     			$this->response->setResult(false, 'VEI009');
     		}
     	} else{
     		$this->response->setResult(false, 'INF001');
     	}
     	
     	return $this->response;
     }
}
