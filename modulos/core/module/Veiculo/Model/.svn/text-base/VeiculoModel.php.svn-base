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
 * @subpackage Classe Model Veiculo
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
*/
namespace module\Veiculo;

use infra\Helper\Validacao;

class VeiculoModel{
    // Atributos
    private $dao; // Acesso a dados
    
    // Campos inteiros no BD
    private $intFieldList = array('veioid', 'veimlooid', 'veisegoid', 'veino_ano', 'veiusuoid', 'veino_endosso',
    		'veifipe_codigo', 'veifipe_digito', 'veicod_veiculo', 'veiano_fabric', 'veinum_portas', 'veium_eixos', 'veiusuexclusao',
    		'veiusuoid_alteracao', 'veiid_agendamento', 'veiid_recibo', 'veifiloid', 'veiveipoid', 'veipoid', 
    		'veipnumero', 'veipusuoid', 'veipusuoid_excl');
    
    // Campos float no BD
    private $floatFieldList = array();    
	
	/**
	 * Contrutor da classe
	 * 
	 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
	 * @param none
	 * @return none
	 */
    public function __construct() {
        $this->dao = new VeiculoDAO();
    }
    
    /**
     * Busca de dados do veículo pelo ID
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 16/09/2013
     * @param $valKey (valor da chave de busca)
     * @return Array de dado do cliente
     */
    public function getDadosByID($valKey=0) {
        $valKey = (int) $valKey;
        $whereKey = " veioid = $valKey ";
        return $this->dao->getVeiculoByKey($whereKey);
    }
    
    
    /**
     * Busca de dados do veículo pelo PLACA
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 16/09/2013
     * @param $valKey (valor da chave de busca)
     * @return Array de dado do cliente
     */
    public function getDadosByPLACA($valKey='') {
        $valKey = trim($valKey);
        $whereKey = " veiplaca = '$valKey' ";
        return $this->dao->getVeiculoByKey($whereKey);
    }

    /**
     * Busca de dados do veículo pelo RENAVAN
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 16/09/2013
     * @param $valKey (valor da chave de busca)
     * @return Array de dado do cliente
     */
    public function getDadosByRENAVAN($valKey=0) {
        $valKey = (int) $valKey;
        $whereKey = " veino_renavan = $valKey ";
        return $this->dao->getVeiculoByKey($whereKey);
    }
    
    /**
     * Busca de dados do veículo pelo CHASSI
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 16/09/2013
     * @param $valKey (valor da chave de busca)
     * @return Array de dado do cliente
     */
    public function getDadosByCHASSI($valKey=0) {
        $valKey = trim($valKey);
        $whereKey = " veichassi = '$valKey' ";
        return $this->dao->getVeiculoByKey($whereKey);
    }
    
    /**
     * Busca dados do veículo (Tabela veiculo_proprietario)
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 24/09/2013
     * @param int $veiveipoid
     * @return Array com dados | false
     */
    public function getDadosVeiculoProprietario($veiveipoid){
    	if(is_int($veiveipoid)){
    		$resultSet = $this->dao->getDadosVeiculoProprietario($veiveipoid);
    		
    		if(is_array($resultSet)){
    			return $resultSet;
    		} else{
    			return false;
    		}
    	} else{
    		return false;
    	}
    }
    
    /**
     * Grava um registro de veículo.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 24/09/2013
     * @param array $arrayVeiculo
     * @return ID do veículo | false
     */
    public function insertVeiculo($arrayVeiculo){
    	unset($arrayVeiculo['veioid']);
    	
    	$campos		  = '';
    	$valores 	  = '';
    	$arrayVeiculo = $this->dao->applyCast($arrayVeiculo, $this->intFieldList, $this->floatFieldList);
    	 
    	foreach ($arrayVeiculo as $key => $value){
    		if($campos != ''){
    			$campos .= ','.$key;
    		} else{
    			$campos = $key;
    		}
    	
    		if($valores != ''){
    			$valores .= ','.$value;
    		} else{
    			$valores = $value;
    		}
    	}
    	
    	$resultSet = $this->dao->insertVeiculo($campos, $valores);
    	
    	if(!empty($resultSet)){
    		return (int) $resultSet['veioid'];
    	} else{
    		return false;
    	}
    }
    
    /**
     * Atualiza um registro de veículo.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 24/09/2013
     * @param array $arrayVeiculo
     * @return ID do veículo | false
     */
    public function updateVeiculo($arrayVeiculo){
    	$dados 		  = '';
    	$arrayVeiculo = $this->dao->applyCast($arrayVeiculo, $this->intFieldList, $this->floatFieldList);
    	 
    	foreach ($arrayVeiculo as $key => $value){
    		if($key == 'veioid'){
    			$veioid = $value;
    		} else{
    			if($dados != ''){
    				$dados .= ','.$key.' = '.$value;
    			} else{
    				$dados = $key.' = '.$value;
    			}
    		}
    	}
    	 
    	$resultSet = $this->dao->updateVeiculo($dados, $veioid);
    	 
    	if(!empty($resultSet)){
    		return (int) $resultSet['veioid'];
    	} else{
    		return false;
    	}
    }
    
    /**
     * Grava um registro de proprietário em veiculo_proprietario
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 24/09/2013
     * @param array $arrayProprietario
     * @return $veipoid | false
     */
    public function insertVeiculoProprietario($arrayProprietario){
    	unset($arrayProprietario['veipoid']);
    	    	 
    	$campos  = '';
    	$valores = '';
    	$arrayProprietario = $this->dao->applyCast($arrayProprietario, $this->intFieldList, $this->floatFieldList);
    	
    	foreach ($arrayProprietario as $key => $value){
    		if($campos != ''){
    			$campos .= ','.$key;
    		} else{
    			$campos = $key;
    		}
    		 
    		if($valores != ''){
    			$valores .= ','.$value;
    		} else{
    			$valores = $value;
    		}
    	}
    	 
    	$resultSet = $this->dao->insertVeiculoProprietario($campos, $valores);
    	 
    	if(!empty($resultSet)){
    		return (int) $resultSet['veipoid'];
    	} else{
    		return false;
    	}
    }
    
    /**
     * Atualiza um registro de proprietário em veiculo_proprietario
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 24/09/2013
     * @param array $arrayProprietario
     * @return $veipoid | false
     */
    public function updateVeiculoProprietario($arrayProprietario){
    	$dados = '';
    	$arrayProprietario = $this->dao->applyCast($arrayProprietario, $this->intFieldList, $this->floatFieldList);
    	
    	$veipoid = (int) $arrayProprietario['veipoid'];
    	$usuoid  = (int) $arrayProprietario['veipusuoid'];
    	
    	if($veipoid > 0 && $usuoid > 0){
    		$resultSet = $this->dao->updateVeiculoProprietario($veipoid, $usuoid);
    	} else{
    		$resultSet = array();
    	}
    	
    	if(!empty($resultSet)){
    		return (int) $resultSet['veipoid'];
    	} else{
    		return false;
    	}
    }
    
    /**
     * Exclusão lógica de um registro de proprietário
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $veipoid (ID do Proprietario)
     * @param int $usuoid (ID do usuário que esta realizando a exclusão)
     * @return boolean true/false
     */
    public function veiculoProprietarioDelete($veipoid, $usuoid){
    	$resultSet = $this->dao->updateVeiculoProprietario($veipoid, $usuoid);
    	
    	if(!empty($resultSet)){
    		return true;
    	} else{
    		return false;
    	}
    }
    
    /**
     * Exclusão lógica de registro de veículo
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 26/09/2013
     * @param int $veioid (ID do veículo)
     * @param int $usuoid (ID do usuário que esta realizando a exclusão)
     * @return boolean true/false
     */
    public function veiculoDelete($veioid, $usuoid){    
    	if(is_int($veioid) && is_int($usuoid)){
    		return $this->dao->veiculoDelete($veioid, $usuoid);
    	} else{
    		return false;
    	}
    }
}