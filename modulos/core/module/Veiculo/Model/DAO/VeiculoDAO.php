<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Jorge A. D. kautzmann
 * @version 16/09/2013
 * @since 16/09/2013
 * @package Core
 * @subpackage Classe de Acesso a Dados de Veículo
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace module\Veiculo;

use infra\ComumDAO;

class VeiculoDAO extends ComumDAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Busca um registro de veículo com base em uma condição de comparação
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 23/09/2013
     * @param $whereKey (condição de comparação da chave)
     * @return Array de dados do cliente
     */
    public function getVeiculoByKey($whereKey = '') {
    	$sqlString = "
                SELECT
                      *
                FROM
                    veiculo
                WHERE " .
                    $whereKey . " 
                AND
                    veidt_exclusao IS NULL;";
    	
        $this->queryExec($sqlString);
        
        if($this->getNumRows() > 0){
            return $this->getAssoc();
        } else{
            return false;
        }
    }
    
    /**
     * Busca dados do veículo na tabela veiculo_proprietario.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 24/09/2013
     * @param int $veiveipoid
     * @return Array com dados | false
     */
    public function getDadosVeiculoProprietario($veiveipoid){
    	$sqlString = "
        	SELECT
        		*                   
           	FROM
                veiculo_proprietario
            WHERE
    			veipoid = $veiveipoid
    		AND
    			veipdt_exclusao IS NULL;";
    	 
    	$this->queryExec($sqlString);
    	
    	if($this->getNumRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Grava um registro de veículo.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 24/09/2013
     * @param string $campos
     * @param string $valores
     * @return array
     */
    public function insertVeiculo($campos, $valores){
    	$sqlString = "INSERT INTO veiculo (".$campos.") VALUES (".$valores.") RETURNING veioid;";    	
    	$this->queryExec($sqlString);
    	
    	if($this->getNumRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return array();
    	}
    }
    
    /**
     * Atualiza um registro de veículo.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 24/09/2013
     * @param string $dados
     * @param int $veioid
     * @return array
     */
    public function updateVeiculo($dados, $veioid){
    	$sqlString = "UPDATE veiculo SET $dados WHERE veioid = $veioid RETURNING veioid;";    	
    	$this->queryExec($sqlString);
    	 
    	if($this->getAffectedRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return array();
    	}
    }
    
    /**
     * Grava um registro de veículo em veiculo_proprietario.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 24/09/2013
     * @param string $campos
     * @param string $valores
     * @return array
     */
    public function insertVeiculoProprietario($campos, $valores){
    	$sqlString = "INSERT INTO veiculo_proprietario (".$campos.") VALUES (".$valores.") RETURNING veipoid;";
    	$this->queryExec($sqlString);
    	 
    	if($this->getNumRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return array();
    	}
    }
    
    /**
     * Realiza a exclusao logica um registro de veículo em veiculo_proprietario.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 24/09/2013
     * @param int $veipoid
     * @param int $usuoid
     * @return array
     */
    public function updateVeiculoProprietario($veipoid, $usuoid){    	
    	$sqlString = "
    		UPDATE
    			veiculo_proprietario
    		SET
    			veipdt_exclusao = now(),
    			veipusuoid_excl = $usuoid
    		WHERE
    			veipoid = $veipoid
    		RETURNING
    			veipoid;";
    	
    	$this->queryExec($sqlString);
    
    	if($this->getAffectedRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return array();
    	}
    }
    
    /**
     * Exclusão lógica de registro de veículo
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 26/09/2013
     * @param int $veioid
     * @param int $usuoid
     * @return array
     */
    public function veiculoDelete($veioid, $usuoid){
    	$sqlString = "
    		UPDATE
    			veiculo
    		SET
    			veidt_exclusao = now(),
    			veiusuexclusao = $usuoid
    		WHERE
    			veioid = $veioid
    		RETURNING
    			veioid;";
    	 
    	$this->queryExec($sqlString);
    
    	if($this->getAffectedRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return array();
    	}
    }
 
}