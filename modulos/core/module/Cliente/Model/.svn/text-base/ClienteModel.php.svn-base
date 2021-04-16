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
 * @subpackage Classe Model de Cliente
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
*/
namespace module\Cliente;

use module\Cliente\ClienteDAO as DAO,
	infra\Helper\Validacao as Validacao,
    infra\Helper\Mascara as Mascara;

class ClienteModel{
    // Atributos
    private $dao; // Acesso a dados
    private $intFieldList 	= array('clioid', 'clino_rg', 'clino_cep_res', 'clino_res',
    		'clino_cep_com', 'clino_com', 'cliformacobranca', 'clidia_vcto', 'cliusuoid',
    		'cligeroid', 'cliend_cobr', 'clianalista', 'clicteroid', 'clipaisoid', 'cliusuoid_alteracao',
    		'cliendoid', 'clidia_vcto_loc', 'cliclioid_matriz', 'clinr_dias_protesto_cobr_registrada',
    		'clifunoid', 'cliclicloid', 'endddd'); // Campos inteiros no BD
    
    private $floatFieldList = array('clivl_beneficio', 'cliret_iss_perc', 'cliret_pis_perc', 'cliret_cofins_perc',
    		'cliret_csll_perc', 'cliret_irf_perc'); // Campos float no BD
	/**
	 * Contrutor da classe
	 * 
	 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
	 * @param none
	 * @return none
	 */
    public function __construct() {
        $this->dao = new DAO();
    }

    public function getById($id){
        return $this->dao->getById($id);
    }
    
    /**
     * Busca de dados de cliente pelo ID
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
     * @param (int) $clioid
     * @return Array de dado do cliente
     */
    public function getDadosByID($clioid=0) {
        $valKey   = Mascara::inteiro($clioid);
        $whereKey = " clioid = $valKey ";
        return $this->dao->getClienteByKey($whereKey);
    }
    
    /**
     * Busca de dados de cliente por CPF
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 10/09/2013
     * @param int $clino_cpf
     * @return Array de dado do cliente
     */
    public function getDadosByCPF($clino_cpf=0){
        $whereKey = " clino_cpf = $clino_cpf ";
        return $this->dao->getClienteByKey($whereKey);
    }
    
    /**
     * Busca de dados de cliente por CNPJ
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 10/09/2013
     * @param int $clino_cgc (CNPJ)
     * @return Array de dado do cliente
     */
    public function getDadosByCNPJ($clino_cgc=0) {
        $whereKey = " clino_cgc = $clino_cgc ";
        return $this->dao->getClienteByKey($whereKey);
    }

    /**
     * Grava Dados Cliente
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 10/09/2013
     * @param $arrayCliente
     * @return ID do cliente ($clioid)
     */    
    public function insertCliente($arrayCliente=array()){
    	unset($arrayCliente['clioid']);
    	
    	$campos		  = '';
    	$valores 	  = '';   	
    	$arrayCliente = $this->dao->applyCast($arrayCliente, $this->intFieldList, $this->floatFieldList);
    	
    	foreach ($arrayCliente as $key => $value){
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
    	    	
    	$resultSet = $this->dao->insertCliente($campos, $valores);
    	    	
    	if(!empty($resultSet)){
    		return Mascara::inteiro($resultSet['clioid']);
    	} else{
    		return false;
    	}
    }
    
    /**
     * Atualiza Dados Cliente
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 10/09/2013
     * @param $arrayCliente
     * @return $clioid = ID do cliente / false
     */
    public function updateCliente($arrayCliente=array()){
    	$dados 		  = '';
    	if (empty($arrayCliente['cliusuoid_alteracao'])){
    		unset($arrayCliente['cliusuoid_alteracao']);
    	}
    	$arrayCliente = $this->dao->applyCast($arrayCliente, $this->intFieldList, $this->floatFieldList);
    	
    	foreach ($arrayCliente as $key => $value){
    		if($key == 'clioid'){
    			$clioid = $value;
    		} else{
    			if($dados != ''){
    				$dados .= ','.$key.' = '.$value;
    			} else{
    				$dados = $key.' = '.$value;
    			}
			}
		}
		
		if (!strlen($dados)){
			return false;
		}
    	
    	$resultSet = $this->dao->updateCliente($dados, $clioid);
    	
    	if(!empty($resultSet)){
    		return Mascara::inteiro($resultSet['clioid']);
    	} else{
    		return false;
    	}
    }
    
    /**
     * Busca o endereço do cliente pelo ID.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 27/09/2013
     * @param int $endoid
     * @return array | false
     */
    public function getEnderecoByID($endoid){
    	if(is_int($endoid)){
    		$resultSet = $this->dao->getEnderecoByID($endoid);
    		 
    		if(!empty($resultSet)){
    			return $resultSet;
    		} else{
    			return false;
    		}  		
    	} else{
    		return false;
    	}    	
    }
	
    /**
     * Busca uma lista de endereços do cliente por ID.
     * 
     * @param int $clioid
     * @return Array/false
     */
    public function getEnderecosByID($clioid=0){
    	$arrayList = array();
    	$valKey = Mascara::inteiro($clioid);
    	$whereKey = " clioid = $valKey ";
    	$resultSet = $this->dao->getClienteByKey($whereKey);
    	 
    	if(!empty($resultSet)){
    		
    		$cliendoid   = Mascara::inteiro($resultSet['cliendoid']);
    		$cliend_cobr = Mascara::inteiro($resultSet['cliend_cobr']);
    		$cliendoid_instalacao = Mascara::inteiro($resultSet['cliendoid_instalacao']);
    		// Endereço principal
    		if($cliendoid > 0){
    			$resultSet = $this->dao->getEnderecoByID($cliendoid);
    			
    			if(!empty($resultSet)){
    			    $arrayList['principal'] = $resultSet;
    			}
    		}
    		// Endereço de Cobrança
    		if($cliend_cobr > 0){
    			$resultSet = $this->dao->getEnderecoByID($cliend_cobr);
    			
    			if(!empty($resultSet)){
    				$arrayList['cobranca']  = $resultSet;
    			}
    		}
    		// Endereço de Instalação
    		if($cliendoid_instalacao > 0){
    		    $resultSet = $this->dao->getEnderecoByID($cliendoid_instalacao);
    		     
    		    if(!empty($resultSet)){
    				$arrayList['instalacao']  = $resultSet;
    		    }
    		}
    		return $arrayList;
    	} else{
    		return false;
    	}
    }
    
    /**
     * Busca dados de forma de cobrança do cliente
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 03/02/2014
     * @param (int) $clioid
     * @return Array de dado de forma de cobrança
     */
    public function getFormaCobranca($clioid=0){	
    	$resultSet = $this->dao->getFormaCobranca($clioid);
    	if (is_array($resultSet)) {
    		return $resultSet;
    	} else {
    		return array();
    	}
    }
    
    /** 
     * @param int $clioid
     * @return boolean
     */
    public function verificaClienteByID($clioid=0){
     	$valKey = Mascara::inteiro($clioid);
    	$whereKey = " clioid = $valKey ";
    	return $this->dao->verificaClienteByKey($whereKey);
    }
    
    /**
     * @param int $cpf
     * @return boolean
     */
    public function verificaClienteByCPF($cpf=0){
     	$valKey = Mascara::inteiro($cpf);
    	$whereKey = " clino_cpf = $valKey ";
    	return $this->dao->verificaClienteByKey($whereKey);
    }
    
    /**
     * @param int $cnpj
     * @return boolean
     */
    public function verificaClienteByCNPJ($cnpj=0){
     	$valKey = Mascara::inteiro($cnpj);
    	$whereKey = " clino_cgc = $valKey ";
    	return $this->dao->verificaClienteByKey($whereKey);
    }
    
    /**
     *  Método para inserir registro de endereço do Cliente (tabela Endereços)
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 23/09/2013
     * @param $arrayEnderecoCliente (Array com dados de um registro de endereço do cliente)
     * @return ID do registro de endereço|false
     */
    public function clienteEnderecoInsert($arrayEnderecoCliente){
    	if(is_array($arrayEnderecoCliente)){
    		$campos  = '';
    		$valores = '';
    		$arrayEnderecoCliente = $this->dao->applyCast($arrayEnderecoCliente, $this->intFieldList, $this->floatFieldList);
    		
	    	foreach ($arrayEnderecoCliente as $key => $value){
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
    		 
    		$resultSet = $this->dao->clienteEnderecoInsert($campos, $valores);
    		 
    		if(!empty($resultSet)){
    			return Mascara::inteiro($resultSet['endoid']);
    		} else{
    			return false;
    		}
    	} else{
    		return false;
    	}
    }
    
    /**
     *  Método para atualizar registro de endereço do Cliente (tabela Endereços)
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 27/09/2013
     * @param int $endoid
     * @param array $arrayEnderecoCliente (Array com dados de um registro de endereço do cliente)
     * @return ID do registro de endereço|false
     */
    public function clienteEnderecoUpdate($endoid, $arrayEndereco){
    	$dados = '';
    	$arrayEndereco['endoid'] = $endoid;
    	$arrayEndereco = $this->dao->applyCast($arrayEndereco, $this->intFieldList, $this->floatFieldList);
    	 
    	foreach ($arrayEndereco as $key => $value){
    		if($key == 'endoid'){
    			$endoid = $value;
    		} else{
    			if($dados != ''){
    				$dados .= ','.$key.' = '.$value;
    			} else{
    				$dados = $key.' = '.$value;
    			}
    		}
    	}
    	 
    	$resultSet = $this->dao->clienteEnderecoUpdate($dados, $endoid);
    	 
    	if(!empty($resultSet)){
    		return Mascara::inteiro($resultSet['endoid']);
    	} else{
    		return false;
    	}
    }
    
    /**
     * Método para excluir um registro de cliente
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 27/09/2013
     * @param int $clioid (ID do cliente a ser excluido)
     * @param int $usuoid (ID do usuario que esta realizando a exclusao)
     * @return boolean
     */
    public function exclui($clioid, $usuoid){    	 
    	if(is_int($clioid) && is_int($usuoid)){
    		return $this->dao->exclui($clioid, $usuoid);
    	} else{
    		return false;
    	}
    }

    /**
     *  Método para inserir registro de forma de cobrança do Cliente (tabela cliente_cobranca)
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 31/01/2014
     * @param $arrayFormaCobranca (Array com dados de forma e cobranca do cliente)
     * @return true|false
     */
    public function clienteFormaCobrancaInsert($arrayFormaCobranca){
    	if(is_array($arrayFormaCobranca)){
    		$campos  = '';
    		$valores = '';
    		$intFieldList = array('clicclioid','clicusuoid','clicformacobranca','clicagencia','clicconta','clicmscoid','clicdias_prazo','clicdia_mes','clicdt_inicial','clicdt_final');
    		$floatFieldList = array();
    		    		
    		$arrayFormaCobranca = $this->dao->applyCast($arrayFormaCobranca, $intFieldList, $floatFieldList);
    
    		foreach ($arrayFormaCobranca as $key => $value){
    			if ($key != 'clicoid'){
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
    		}
    		 
    		$resultSet = $this->dao->formaCobrancaInsert($campos, $valores);
    		 
    		if(!empty($resultSet)){
    			return true;
    		} else{
    			return false;
    		}
    	} else{
    		return false;
    	}
    }

    /**
     * Excluí logicamente todos os registro de cliente_cobranca.
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 03/02/2014
     * @param int $clioid
     * @param int $usuoid
     * @return boolean
     */
    public function clienteFormaCobrancaDeleteAll($clioid, $usuoid){
    	if(is_int($clioid) && is_int($usuoid)){
    		return $this->dao->formaCobrancaDeleteAll($clioid, $usuoid);
    	} else{
    		return false;
    	}
    }
    
    
}