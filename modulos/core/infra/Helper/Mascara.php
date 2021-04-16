<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Bruno B. Affonso
 * @version 04/09/2013
 * @since 04/09/2013
 * @package Core
 * @subpackage Mascaras
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace infra\Helper;

class Mascara{
	
	/**
	 * Aplica máscara para CPF - xxx.xxx.xxx-xx
	 * 
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @param string $string (somente números)
	 * @return string
	 */
	public static function mascaraCpf($string){
		if(!empty($string)){			
			if(preg_match("/^[0-9]{11}$/", $string)){
				return substr($string, 0, 3).'.'.substr($string, 3, 3).'.'.substr($string, 6, 3).'-'.substr($string, 9, 2);
			} else{
				return '';
			}
		} else{
			return '';
		}
	}
	
	/**
	 * Aplica máscara para CNPJ - xx.xxx.xxx/xxxx-xx
	 * 
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @param string $string (somente números)
	 * @return
	 */
	public static function mascaraCnpj($string){
		if(!empty($string)){			
			if(preg_match("/^[0-9]{14}$/", $string)){				
				return substr($string, 0, 2).'.'.substr($string, 2, 3).'.'.substr($string, 5, 3).'/'.substr($string, 8, 4).'-'.substr($string, 12, 2);
			} else{
				return '';
			}
		} else{
			return '';
		}
	}
	
	/**
	 * Aplica máscara para telefone (abrange o 9º dígito) - (xx) xxxx-xxxx
	 * 
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @param string $string (somente números)
	 * @return
	 */
	public static function mascaraTelefone($string){
		if(!empty($string)){			
			if(preg_match("/^[0-9]{10,11}$/", $string)){
				if(strlen($string) == 10){
					return '('.substr($string, 0, 2).') '.substr($string, 2, 4).'-'.substr($string, 6, 4);
				} elseif(strlen($string) == 11){
					return '('.substr($string, 0, 2).') '.substr($string, 2, 5).'-'.substr($string, 7, 4);
				}		
			} else{
				return '';
			}
		} else{
			return '';
		}
	}
	
	/**
	 * Aplica máscara para CEP - xx.xxx-xxx
	 * 
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @param string $string (somente números)
	 * @return
	 */
	public static function mascaraCep($string){
		if(!empty($string)){			
			if(preg_match("/^[0-9]{8}$/", $string)){				
				return substr($string, 0, 2).'.'.substr($string, 2, 3).'-'.substr($string, 5, 3);
			} else{
				return '';
			}
		} else{
			return '';
		}
	}
	
	/**
	 * Remove letras e caracteres especiais
	 * retornando somente os números.
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @param string $string
	 * @return
	 */
	public static function somenteNumeros($string){
		return trim(preg_replace("/[a-zA-Z\=*+-.\/]/", '', $string));
	}
	
	/**
	 * Remove ameaças de segurança como SQL injection
	 *
	 * @author Fabio Andrei Lorentz <fabio.lorentz@ewave.com.br>
	 * @param string $strValor
	 * @return string $strValor
	 */
	public static function removeSQLInjection($strValor=''){
		$remover = array("--","'"," OR ");
		$strValor = str_ireplace($remover, "", $strValor);
		return $strValor;
	}
	
	/**
	 * Remove caracteres não numéricos e aplica cast de inteiro
	 *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
 	 * @param string $strValor
	 * @return int $intRetorno (valor formatado para inteiro)
	 */
	public static function inteiro($strValor=''){
	    $intRetorno = trim(preg_replace("/[a-zA-Z\=*+-.\/]/", '', $strValor));
	    if(strlen($intRetorno) <= 8){
	        return (int) $intRetorno;
	    } else{
	        return $intRetorno;
	    }
	}
	
	/**
	 * Formata valor para inteiro.
	 * 
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @param string $valor
	 * @param string $tipo (IN = Retorna formato do BD; OUT = Retorna formato moeda R$)
	 * @return int / false
	 */
	public static function formataInteiro($valor, $tipo){
		$tipo = trim(strtoupper($tipo));		
		if($tipo == 'IN'){
			$valor = str_replace('.', '', trim($valor));
			$valor = str_replace(',', '.', $valor);
			return (int) $valor;
		} elseif($tipo == 'OUT'){
			$valor = str_replace('.', ',', trim($valor));
			return (int) $valor;
		} else{
			return false;
		}
	}
	
	/**
	 * Formata valor para float.
	 * 
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @param string $valor
	 * @param string $tipo (IN = Retorna formato do BD; OUT = Retorna formato moeda R$)
	 * @return float / false
	 */
	public static function formataReal($valor, $tipo){
		$tipo = trim(strtoupper($tipo));		
		if($tipo == 'IN'){
			$valor = str_replace('.', '', trim($valor));
			$valor = str_replace(',', '.', $valor);
			return (float) $valor;
		} elseif($tipo == 'OUT'){
			$valor = str_replace('.', ',', trim($valor));
			return (float) $valor;
		} else{
			return false;
		}
	}
	
	/**
	 * Remove acento.
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @param string $string
	 * @return string
	 */
	public static function removeAcento($string){
		$string = trim($string);		
		$string = preg_replace("[^a-z A-Z 0-9.,/()]", "", strtr($string, 'áàãâéêíìóôõúüçÁÀÃÂÉÊÍÌÓÔÕÚÜÇ','aaaaeeiiooouucAAAAEEIIOOUUC'));
		
		return $string;
	}
	
	/**
	 * Seta valor NULL para variáveis conforme tipo.
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @param string $valor
	 * @param string $tipo (I = Inteiro; D = Data, V = valor ponto flutuante)
	 * @return int / false
	 */
	public static function setDefaultNull($valor=0, $tipo='I'){
	    $tipo = trim(strtoupper($tipo));
	    $valor = trim($valor);
	    if($tipo == 'I'){// inteiro
	        $valor = self::inteiro($valor);
	        if($valor == 0){
	            return 'NULL';
	        }
	    } elseif($tipo == 'D'){// data 
	    	if($valor == ''){
	            return 'NULL';
	        }
	        $valor = "'" . $valor . "'"; 
	    } elseif($tipo == 'V'){// valor campo flutuante
	        $valor = (float) $valor;
	    	if($valor == 0){
	            return 'NULL';
	        }
	    }
	    return $valor;
	}

	/**
	 * Converte data do formato do Brasil para formato do Banco e vice versa.
	 * 
	 * @author Fabio Andrei Lorentz [fabio.lorentz@sascar.com.br]
	 * @param string $valor
	 * @param string $tipo (IN = Retorna formato do BD; OUT = Retorna formato data BR)
	 * @return string
	 */
	public static function formataData($valor, $tipo) {
		$tipo = trim(strtoupper($tipo));

		if($valor == '') {
			return false;
		}

		if($tipo == 'IN') {
			$dataBR = explode("/", $valor);
			$dataBRR = array_reverse($dataBR);
			$dataBD = implode("-", $dataBRR);
			return $dataBD;
		} elseif($tipo == 'OUT') {
			$dataBD = explode("-", $valor);
			$dataBDR = array_reverse($dataBD);
			$dataBR = implode("/", $dataBDR);
			return $dataBR;
		} else {
			return false;
		}
	}

	/**
	 * Converte data e hora do formato do Brasil para formato do Banco e vice versa.
	 * 
	 * @author Fabio Andrei Lorentz [fabio.lorentz@sascar.com.br]
	 * @param string $valor
	 * @param string $tipo (IN = Retorna formato do BD; OUT = Retorna formato data BR)
	 * @return string
	 */
	public static function formataDataHora($valor, $tipo) {
		$tipo = trim(strtoupper($tipo));

		if($valor == '') {
			return false;
		}	

		if($tipo != 'IN' && $tipo != 'OUT') {
			return false;
		}

		$tmp = explode(" ", $valor);
		$data = $tmp[0];
		$hora = $tmp[1];
		$dataF = self::formataData($data, $tipo);
		$dataHora = $dataF . " " . $hora;

		return $dataHora;
	}
	
}