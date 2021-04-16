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
 * @subpackage Superclasse Controller
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace infra;

abstract class ComumController{
    /**
     * Contrutor da classe
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
     * @param none
     * @return none
    */
    public function __construct() {
        //....
    }
    
    /**
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 15/10/2013
     * @param array $arrayObrigatorio array com campos obrigatórios.
     * @param array $arrayDados array associativo (CHAVE => VALOR) a ser validado.
     * @return TRUE se contem todos os campos / FALSE caso esteja faltando algum campo
     */
    protected function verificaCampos($arrayObrigatorio, $arrayDados){
    	$exists = false;
    	
    	//Verificando se os campos obrigatorios existem
    	foreach($arrayObrigatorio as $row){
    		if(array_key_exists($row, $arrayDados)){
    			$exists = true;
    		} else{
    			$exists = false;
    			break;
    		}
    	}
     	return $exists;
    }
}