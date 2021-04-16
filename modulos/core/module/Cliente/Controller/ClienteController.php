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
 * @subpackage Classe Controladora de Clientes
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace module\Cliente;

use infra\ComumController,
	infra\Helper\Response,
	module\Cliente\ClienteModel as Modelo,
	infra\Helper\Validacao as Validacao,    
	infra\Helper\Mascara as Mascara;

class ClienteController extends ComumController{
    
    private $model;
    public $response;
    
	/**
	 * Contrutor da classe
	 * 
	 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
	 * @param none
	 * @return none
     */
    public function __construct(){
        $this->model = new Modelo();
        $this->response = new Response();
    }
    
    /**
     * Busca de dados de cliente
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
     * @param valor da chave ($valKey), tipo da chave ($tpKey=ID/DOC)
     * @return response ($response->dados = Array com dados do cliente/false)
     */
    public function getDados($valKey='', $tpKey='ID') {
    	$tpKey = trim(strtoupper($tpKey));
    	
        if($tpKey == 'ID'){
            $resultSet = $this->model->getDadosByID($valKey);
            
            if(is_array($resultSet) && !empty($resultSet)){
            	$this->response->setResult($resultSet, '0');
            } else{
            	$this->response->setResult(false, 'INF002');
            }
        } elseif($tpKey == 'DOC'){
        	if(Validacao::validaCpfCnpj($valKey)){
        		$valKey = Mascara::somenteNumeros($valKey);
        		$length = strlen($valKey);
        		
        		if($length == 11){
        			$resultSet = $this->model->getDadosByCPF($valKey);
        			
        			if(is_array($resultSet) && !empty($resultSet)){
        				$this->response->setResult($resultSet, '0');
        			} else{
        				$this->response->setResult(false, 'INF002');
        			}
        		} elseif ($length == 14){
        			$resultSet = $this->model->getDadosByCNPJ($valKey);
        			
        			if(is_array($resultSet) && !empty($resultSet)){
        				$this->response->setResult($resultSet, '0');
        			} else{
        				$this->response->setResult(false, 'INF002');
        			}
        		} else{
        			$this->response->setResult(false, 'INF006');
        		}
        	} else{
        		$this->response->setResult(false, 'INF006');
        	}
        } else{
        	$this->response->setResult(false, 'INF006');
        }
        
        return $this->response;
    }
    
    /**
     * Grava dados de cliente
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
     * @param $arrayCliente, $acao
     * @return response ($response->dados = $clioid | false)
     */
    public function setDados($arrayCliente=array(), $acao='I') {
        if($acao == 'I'){
        	if($arrayCliente['clitipo'] == 'F'){
        		$cpfCnpj = Mascara::somenteNumeros($arrayCliente['clino_cpf']);
        		$arrayCliente['clino_cpf'] = $cpfCnpj;
        	} else{
        		$cpfCnpj = Mascara::somenteNumeros($arrayCliente['clino_cgc']);
        		$arrayCliente['clino_cgc'] = $cpfCnpj;
        	}
        	
        	if(Validacao::validaCpfCnpj($cpfCnpj)){
        		$objCliente = $this->getDados($cpfCnpj, 'DOC');                
        		$dadosCliente = $objCliente->dados;
        		
        		// CLIENTE JA EXISTE
        		if(is_array($dadosCliente) && !empty($dadosCliente)){
                    $this->response->setResult(false, 'CLI006');
        		} else{
                    //INSERE CLIENTE
        			$clioid = $this->model->insertCliente($arrayCliente);
        			
        			if(is_int($clioid)){
        				$this->response->setResult($clioid, '0');
        			} else{
        				$this->response->setResult($clioid, 'CLI001');
        			}
        		}
        	} else{
        		$this->response->setResult(false, 'INF006');
        	}            
        } elseif($acao == 'U'){
        	if($arrayCliente['clioid'] > 0){
        		if(array_key_exists('clitipo', $arrayCliente)){
        			$cpfCnpj = Mascara::somenteNumeros($arrayCliente['clino_cpf']);
        			$arrayCliente['clino_cpf'] = $cpfCnpj;
        		}
        		
        		if(array_key_exists('clino_cgc', $arrayCliente)){
        			$cpfCnpj = Mascara::somenteNumeros($arrayCliente['clino_cgc']);
        			$arrayCliente['clino_cgc'] = $cpfCnpj;
        		}
        		
        		$clioid = $this->model->updateCliente($arrayCliente);
        		
        		if(is_int($clioid)){
        			$this->response->setResult($clioid, '0');
        		} else{
        			$this->response->setResult($clioid, 'CLI002');
        		} 
        	} else{
        		$this->response->setResult($clioid, 'INF006');
        	}            
        } else{
        	$this->response->setResult(false, 'INF004');
        }
        
        return $this->response;
    }
    
    /**
     * Buscar uma lista de endereços do cliente
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 11/09/2013
     * @param int $clioid
     * @return response ($response->dados = Array associativo/false)
     */
    public function getEnderecos($clioid=0){
    	$clioid = Mascara::inteiro($clioid);
    	
    	if($clioid > 0){
    		$resultSet = $this->model->getEnderecosByID($clioid);
    		
    		if(is_array($resultSet) && !empty($resultSet)){
    			$this->response->setResult($resultSet, '0');
    		} else{
    			$this->response->setResult(false, 'INF002');
    		}
    	} else{
    		$this->response->setResult(false, 'INF006');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Buscar um endereço do cliente pelo ID
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 11/09/2013
     * @param int $clioid
     * @param int $clioid
     * @return response ($response->dados = Array associativo/false)
     */
    public function getEndereco($clioid=0, $tipo='P'){
        $clioid = Mascara::inteiro($clioid);
        $tipo   = trim(strtoupper($tipo));
        $clienteDados = array();
        
        if($clioid > 0){
            $clienteDados = $this->model->getDadosByID($clioid);
            $endoid = 0;
            
            switch ($tipo){
                case 'P':
                   $endoid = Mascara::inteiro($clienteDados['cliendoid']);
                break;
                case 'C':
                   $endoid = Mascara::inteiro($clienteDados['cliend_cobr']);
                break;
                case 'I':
                   $endoid = Mascara::inteiro($clienteDados['cliendoid_instalacao']);
                break;
            }
            
            $resultSet = $this->model->getEnderecoByID($endoid);
            
        	if(is_array($resultSet) && !empty($resultSet)){
    			$this->response->setResult($resultSet, '0');
    		} else{
    			$this->response->setResult(false, 'INF002');
    		}
        } else{
            $this->response->setResult(false, 'INF006');
        }
        
        return $this->response;
    }
    
    /**
     * Verifica se cliente existe
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 11/09/2013
     * @param Valor de busca ($valKey)
     * @param String $tpKey ('ID'/'CPF'/'CNPJ')
     * @return response ($response->dados = boolean)
     */
    public function clienteExiste($valKey='', $tpKey='ID'){
    	if(trim($valKey) != ''){
    		if($tpKey == 'ID'){
                $this->response->setResult($this->model->verificaClienteByID($valKey), '0');
    		} else{
    			if(Validacao::validaCpfCnpj($valKey)){
    				$valKey = Mascara::somenteNumeros($valKey);
    				if($tpKey == 'CPF'){
                        $this->response->setResult($this->model->verificaClienteByCPF($valKey), '0');
    				} elseif($tpKey == 'CNPJ'){
    					$this->response->setResult($this->model->verificaClienteByCNPJ($valKey), '0');
    				} else{
    					$this->response->setResult(false, 'INF004');
    				}
    			} else{
                    $this->response->setResult(false, 'INF001');
    			}
    		}    		
    	} else{
    		$this->response->setResult(false, 'INF006');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Inclui um endereço de cliente
     * 
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 23/09/2013
     * @param $clioid (ID do cliente)
     * @param int $usuoid (ID do usuario que esta realizando a inclusao)
     * @param $arrayEndereco (Array com dados de um registro de endereço do cliente)
     * @param $tipo (Tipo do endereço: P = Principal; C = Cobrança; I = Instalação)
     * @return response ($response->dados = ID do cliente | ID do registro de endereço | false)
     */
    public function clienteEnderecoInclui($clioid=0, $usuoid=0, $arrayEndereco=array(), $tipo='P') {
        $clienteDados = array();
        $clienteDados['clioid'] = Mascara::inteiro($clioid);
        $tipo = trim(strtoupper($tipo));
        $result = false;
        
    	if(!empty($arrayEndereco)){   		
    	    switch ($tipo){
    	        case 'P':    	        	 
    			     $clienteDados['cliendoid'] = Mascara::inteiro($this->model->clienteEnderecoInsert($arrayEndereco));
    			     
    			     // atualizar endereço na propria tabela
    			     if($clienteDados['cliendoid'] > 0){
    			     	$objCliente = $this->getDados($clioid, 'ID');
    			     	$dadosCliente = $objCliente->dados;
    			     	
    			     	if($dadosCliente['clitipo'] == 'F'){
    			     		$clienteDados['clino_cep_res'] = $arrayEndereco['endno_cep'];
    			     		$clienteDados['cliuf_res'] 	   = $arrayEndereco['enduf'];
    			     		$clienteDados['clicidade_res'] = $arrayEndereco['endcidade'];
    			     		$clienteDados['clirua_res']    = $arrayEndereco['endlogradouro'];
    			     		$clienteDados['clino_res']     = $arrayEndereco['endno_numero'];
    			     		$clienteDados['clicompl_res']  = $arrayEndereco['endcomplemento'];
    			     		$clienteDados['clibairro_res'] = $arrayEndereco['endbairro'];
    			     		$clienteDados['clifone_res']   = $arrayEndereco['endfone'];
    			     		$clienteDados['clicep_res']    = $arrayEndereco['endno_cep'];
    			     		if (Mascara::inteiro($usuoid) > 0) {
    			     			$clienteDados['cliusuoid_alteracao'] = Mascara::inteiro($usuoid);
    			     		}
    			     		    			     		
    			     	} elseif($dadosCliente['clitipo'] == 'J'){
    			     		$clienteDados['clino_cep_com'] = $arrayEndereco['endno_cep'];
    			     		$clienteDados['cliuf_com'] 	   = $arrayEndereco['enduf'];
    			     		$clienteDados['clicidade_com'] = $arrayEndereco['endcidade'];
    			     		$clienteDados['clirua_com']    = $arrayEndereco['endlogradouro'];
    			     		$clienteDados['clino_com']     = $arrayEndereco['endno_numero'];
    			     		$clienteDados['clicompl_com']  = $arrayEndereco['endcomplemento'];
    			     		$clienteDados['clibairro_com'] = $arrayEndereco['endbairro'];
    			     		$clienteDados['clifone_com']   = $arrayEndereco['endfone'];
    			     		$clienteDados['clicep_com']    = $arrayEndereco['endno_cep'];
    			     		if (Mascara::inteiro($usuoid) > 0) {
    			     			$clienteDados['cliusuoid_alteracao'] = Mascara::inteiro($usuoid);
    			     		}
    			     	}
    			     	
    			     	$result = $this->model->updateCliente($clienteDados);   			     	
    	             }
    	        break;
    	        case 'C':
    	    		 $clienteDados['cliend_cobr'] = Mascara::inteiro($this->model->clienteEnderecoInsert($arrayEndereco));
    			     if($clienteDados['cliend_cobr'] > 0){
    			         $result = $this->model->updateCliente($clienteDados);    			         
    	             }
    	        break;
    	        case 'I':
        	    	 $clienteDados['cliendoid_instalacao'] = Mascara::inteiro($this->model->clienteEnderecoInsert($arrayEndereco));
    			     if($clienteDados['cliendoid_instalacao'] > 0){
    			         $result = $this->model->updateCliente($clienteDados);
    	             }
    	        break;
    	        default:
    	        	$result = false;
    	        break;
    	    }
    	    
    	    if(is_int($result)){
    	    	$this->response->setResult($result, '0');
    	    } else{
    	    	$this->response->setResult(false, 'CLI003');
    	    }
     	} else{
    		$this->response->setResult(false, 'INF006');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Atualiza um endereço de cliente
     * 
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 23/09/2013
     * @param $clioid (ID do cliente)
     * @param int $usuoid (ID do usuario que esta realizando a alteracao)
     * @param $endoid (ID do endereço)
     * @param $arrayEndereco (Array com dados de um registro de endereço do cliente)
     * @param $tipo (Tipo do endereço: P = Principal; C = Cobrança; I = Instalação)
     * @return response ($response->dados = ID do cliente|ID do registro de endereço|false)
     */
    public function clienteEnderecoAtualiza($clioid=0, $usuoid=0, $endoid=0, $arrayEndereco=array(), $tipo='P') {
        $clienteDados = array();
        $clienteDados['clioid'] = $clioid;
        $result = false;
        
    	if(!empty($arrayEndereco)){
    	    switch ($tipo){
    	        case 'P':
    			     $clienteDados['cliendoid'] = Mascara::inteiro($this->model->clienteEnderecoUpdate($endoid, $arrayEndereco));
    			     
    			     if($clienteDados['cliendoid'] > 0){
    			     	$objCliente = $this->getDados($clioid, 'ID');
    			     	$dadosCliente = $objCliente->dados;
    			     		
    			     	if($dadosCliente['clitipo'] == 'F'){
    			     		$clienteDados['clino_cep_res'] = $arrayEndereco['endno_cep'];
    			     		$clienteDados['cliuf_res'] 	   = $arrayEndereco['enduf'];
    			     		$clienteDados['clicidade_res'] = $arrayEndereco['endcidade'];
    			     		$clienteDados['clirua_res']    = $arrayEndereco['endlogradouro'];
    			     		$clienteDados['clino_res']     = $arrayEndereco['endno_numero'];
    			     		$clienteDados['clicompl_res']  = $arrayEndereco['endcomplemento'];
    			     		$clienteDados['clibairro_res'] = $arrayEndereco['endbairro'];
    			     		$clienteDados['clifone_res']   = $arrayEndereco['endfone'];
    			     		$clienteDados['clicep_res']    = $arrayEndereco['endno_cep'];
    			     		if (Mascara::inteiro($usuoid) > 0) {
    			     			$clienteDados['cliusuoid_alteracao'] = Mascara::inteiro($usuoid);
    			     		}
    			     	
    			     	} elseif($dadosCliente['clitipo'] == 'J'){
    			     		$clienteDados['clino_cep_com'] = $arrayEndereco['endno_cep'];
    			     		$clienteDados['cliuf_com'] 	   = $arrayEndereco['enduf'];
    			     		$clienteDados['clicidade_com'] = $arrayEndereco['endcidade'];
    			     		$clienteDados['clirua_com']    = $arrayEndereco['endlogradouro'];
    			     		$clienteDados['clino_com']     = $arrayEndereco['endno_numero'];
    			     		$clienteDados['clicompl_com']  = $arrayEndereco['endcomplemento'];
    			     		$clienteDados['clibairro_com'] = $arrayEndereco['endbairro'];
    			     		$clienteDados['clifone_com']   = $arrayEndereco['endfone'];
    			     		$clienteDados['clicep_com']    = $arrayEndereco['endno_cep'];
    			     		if (Mascara::inteiro($usuoid) > 0) {
    			     			$clienteDados['cliusuoid_alteracao'] = Mascara::inteiro($usuoid);
    			     		}
    			     	}
    			     	
    			     	$result = $this->model->updateCliente($clienteDados);
    	             } else{
    	             	$result = false;
    	             }
    	        break;
    	        case 'C':
    	    		 $clienteDados['cliend_cobr'] = Mascara::inteiro($this->model->clienteEnderecoUpdate($endoid, $arrayEndereco));
    			     if($clienteDados['cliend_cobr'] > 0){
    			         $result = $this->model->updateCliente($clienteDados);
    	             } else{
    	                 $result = false;
    	             }
    	        break;
    	        case 'I':
        	    	 $clienteDados['cliendoid_instalacao'] = Mascara::inteiro($this->model->clienteEnderecoUpdate($endoid, $arrayEndereco));
    			     if($clienteDados['cliendoid_instalacao'] > 0){
    			         $result = $this->model->updateCliente($clienteDados);
    	             } else{
    	                 $result = false;
    	             }
    	        break;
    	    }
    	    
    	    if(is_int($result)){
    	    	$this->response->setResult($result, '0');
    	    } else{
    	    	$this->response->setResult(false, 'CLI004');
    	    }
     	} else{
    		$this->response->setResult(false, 'INF006');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Método para excluir um registro de cliente
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 27/09/2013
     * @param int $clioid (ID do cliente a ser excluido)
     * @param int $usuoid (ID do usuario que esta realizando a exclusao)
     * @return response ($response->dados = boolean)
     */
    public function clienteExclui($clioid=0, $usuoid=0){
    	$clioid = Mascara::inteiro($clioid);
    	$usuoid = Mascara::inteiro($usuoid);
    	
    	if($clioid > 0 && $usuoid > 0){
    		$resultSet = $this->model->exclui($clioid, $usuoid);
    		
    		if($resultSet){
    			$this->response->setResult(true, '0');
    		} else{
    			$this->response->setResult(false, 'CLI005');
    		}
    	} else{
    		$this->response->setResult(false, 'INF006');
    	}
    	
    	return $this->response;
    }        

    /**
     * Inclui uma nova forma de cobrança ao cliente, excluindo as anteriores
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 31/01/2014
     * @param $clioid (ID do cliente)
     * @param int $usuoid (ID do usuario que esta realizando a inclusao)
     * @param $arrayFormaCobranca (Array com dados de forma de cobrança do cliente)
     * @return response (true | false)
     */
    public function clienteFormaCobrancaInclui($clioid=0, $usuoid=0, $arrayFormaCobranca=array()) {
    	
    	try {
    	
	    	$result = false;
	    
	    	if(!empty($arrayFormaCobranca)){
	    		
	    		//Reserva valores atuais
	    		$formaCobrancaAtual = $this->model->getFormaCobranca($clioid, $usuoid);
	    		
	    		//Monta array
	    		if (is_array($formaCobrancaAtual) && count($formaCobrancaAtual) > 0){
	    			foreach ($formaCobrancaAtual AS $key => $val) {
	    				$inteiro = Mascara::inteiro(str_replace("'","",$val));
	    				if ($inteiro > 0){
		    				$val = Mascara::setDefaultNull(((!empty($arrayFormaCobranca[$key])) ? $arrayFormaCobranca[$key] : $inteiro),'I');
	    				} else{
	    					$val = Mascara::setDefaultNull(((!empty($arrayFormaCobranca[$key])) ? $arrayFormaCobranca[$key] : $val),'D');
	    				}
		    			$val = ($val === 0) ? 'NULL' : $val;
		    			$arrayFormaCobranca[$key] = $val;
		    		}
	    		}
	    		$arrayFormaCobranca['clicformacobranca'] = Mascara::inteiro(Mascara::somenteNumeros(($arrayFormaCobranca['clicformacobranca'])));
	    		$arrayFormaCobranca['clicclioid'] = Mascara::inteiro(Mascara::somenteNumeros(($clioid)));
	    		$arrayFormaCobranca['clicusuoid'] = Mascara::inteiro(Mascara::somenteNumeros(($usuoid)));
	    		
	    		//Reseta valores
	    		$reset = $this->model->clienteFormaCobrancaDeleteAll($clioid, $usuoid);
	    		
	    		if ($reset) {
	    		
	    			//Insere nova forma de cobrança
		    		$result = $this->model->clienteFormaCobrancaInsert($arrayFormaCobranca);
		    		
		    		if($result){
		    			// 2014/09/09 -> gravação do forma cobrança na tabela cliente
		    			$arrayCliente = array('clioid' => $clioid, 'cliusuoid_alteracao' => $usuoid, 'cliformacobranca' => $arrayFormaCobranca['clicformacobranca']);
		    			$result = $this->model->updateCliente($arrayCliente);
		    			
		    			$this->response->setResult($result, '0');
		    		} else{
		    			$this->response->setResult(false, 'CLI003');
		    		}
	    		} else{
	    			throw new \Exception('Erro ao atualizar forma de cobrança');
	    		}
	    	} else{
	    		$this->response->setResult(false, 'INF006');
	    	}
	    	
    	} catch (\Exception $e){
    		$this->response->setResult($e, 'EXCEPTION');
    	}
    	
    	return $this->response;
    }
}