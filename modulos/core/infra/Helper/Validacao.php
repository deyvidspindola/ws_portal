<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Jorge A. D. Kautzmann
 * @version 29/08/2013
 * @since 29/08/2013
 * @package Core
 * @subpackage Validações
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace infra\Helper;

class Validacao{
    
    /**
     * Verifica se CPF/CNPJ é válido.
     *
     * @author Bruno B. Affonso - <bruno.bonfim@sascar.com.br>
     * @param string $string
     * @return boolean
     */
    public static function validaCpfCnpj($string){
        $string    = trim($string);
        $arrayCpf  = array('0' => '00000000000', '1' => '11111111111', '2' => '22222222222', '3' => '33333333333',
                           '4' => '44444444444', '5' => '55555555555', '6' => '66666666666', '7' => '77777777777',
                           '8' => '88888888888', '9' => '99999999999');
        $arrayCnpj = array('0' => '00000000000000', '1' => '11111111111111', '2' => '22222222222222', '3' => '33333333333333',
                           '4' => '44444444444444', '5' => '55555555555555', '6' => '66666666666666', '7' => '77777777777777',
                           '8' => '88888888888888', '9' => '99999999999999');
        
        if(!empty($string)){
            $string = preg_replace("/[' '-. \/ \t]/", '', $string);
            $length = strlen($string);
          
            if($length != 11 && $length != 14){
                return false;
            }
            
            if(in_array($string, $arrayCpf)){ 
                return false;
            }
            
            if(in_array($string, $arrayCnpj)){
                return false;
            }
            
            $numero = substr($string, 0, ($length - 2));
            $digito = substr($string, ($length - 2), 2);
            
            $arrayNumero = array();
            $arrayNumero = str_split($numero);
            
            //Verificando o primeiro numero do Digito.
            $vDigito = 0;
            
            for($i = 0; $i < ($length - 2); $i++){
                if($length == 14){ //CNPJ
                    $vDigito += $arrayNumero[11 - $i] * (2 + ($i % 8));
                } else{ //CPF
                    $vDigito += $arrayNumero[$i] * (10 - $i);
                }
            }
           
            if($vDigito == 0){
                return false;
            }
            
            $vDigito = 11 - ($vDigito % 11);
            
            if($vDigito > 9){
                $vDigito = 0;            
            }
            
            if($digito[0] != $vDigito){
                return false;
            }
            
            //Verificando o segundo numero do Digito.
            $vDigito *= 2;
            
            for($i = 0; $i < ($length - 2); $i++){
                if($length == 14){ //CNPJ
                    $vDigito += $arrayNumero[11 - $i] * (2 + (($i + 1) % 8));
                } else{ //CPF
                    $vDigito += $arrayNumero[$i] * (11 - $i);
                }
            }

            $vDigito = 11 - ($vDigito % 11);
            
            if($vDigito > 9){
                $vDigito = 0;
            }
            
            if($digito[1] != $vDigito){
                return false;
            }
            
            return true;
        } else{
            return false;
        }
    }
    
    /**
     * Verifica se o CEP é válido.
     *
     * @author Bruno B. Affonso - <bruno.bonfim@sascar.com.br>
     * @param string $string
     * @return boolean
     */
    public static function validaCep($string){
        $string = trim(preg_replace("/[-.]/", '', $string));
        $string = preg_match("/[0-9]{8}$/", $string);
        
        if(!$string){            
            return false;
        } else{
            return true;
        }
    }
    
    /**
     * Verifica se o telefone é válido.
     * Telefone: (99) 9999-9999 / (99) 99999-9999
     *
     * @author Bruno B. Affonso - <bruno.bonfim@sascar.com.br>
     * @param string $string
     * @return boolean
     */
    public static function validaTelefone($string){
        $string = trim(preg_replace("/[' '()-.]/", '', $string));
        $string = preg_match("/^[0-9]{10,11}$/", $string);
        
        if(!$string){            
            return false;
        } else{
            return true;            
        }
    }
    
    /**     
     * Verifica se e-mail é valido.
     *
     * @author Bruno B. Affonso - <bruno.bonfim@sascar.com.br>
     * @param string $string
     * @return boolean
     */
    public static function validaEmail($string){        
        $string = trim($string);
        $string = preg_match("/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/", $string);
        
        if(!$string){            
            return false;
        } else{
            return true;            
        }
    }
    
    /**     
     * Verifica se é uma data valida.
     *
     * @author Bruno B. Affonso - <bruno.bonfim@sascar.com.br>
     * @param string $string - dd/mm/yyyy
     * @return boolean
     */
    public static function validaData($string){
        $string = trim($string);
        
        if(!empty($string)){
            $string = preg_match("/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/", $string, $entries);
            
            if($string){
            	//mes, dia, ano            
                if(!checkdate($entries[2], $entries[1], $entries[3])){            
                    return false;
                } else{
                    return true;            
                }
            } else{
                return false;
            }            
        } else{
            return false;
        }
    }
    
    /**
     * Verifica se é um horário válido.
     * 
     * @author Bruno B. Affonso - <bruno.bonfim@sascar.com.br>
     * @param string $string - HH:mm:ss
     * @return boolean
     */
    public static function validaHorario($string){
    	$string = trim($string);
    	
    	if(!empty($string)){
    		$string = preg_match("/^([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $string);
    		
    		if($string){
    			return true;
    		} else{
    			return false;
    		}
    	} else{
    		return false;
    	}
    }
    
    /**
     * Verifica se é uma data/hora válida.
     * 
     * @author Bruno B. Affonso - <bruno.bonfim@sascar.com.br>
     * @param string $string - dd/mm/yyyy HH:mm:ss
     * @return boolean 
     */
    public static function validaDataHora($string){
    	$string = trim($string);
    	 
    	if(!empty($string)){
    		$string = preg_match("/^([0-9]{2})\/([0-9]{2})\/([0-9]{4}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $string, $entries);
    		
			if($string){
				//mes, dia, ano
				if(!checkdate($entries[2], $entries[1], $entries[3])){
					return false;
				} else{
					return true;
				}   				
			} else{
				return false;
			}
    	} else{
    		return false;
    	}
    }
}