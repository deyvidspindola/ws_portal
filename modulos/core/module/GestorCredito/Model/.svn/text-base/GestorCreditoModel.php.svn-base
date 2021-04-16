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
namespace module\GestorCredito;

use module\GestorCredito\GestorCreditoDAO as DAO;

class GestorCreditoModel{
	private $dao;
	
	/**
	 * Contrutor da classe
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @version 21/10/2013
	 * @param none
	 * @return none
	 */
	public function __construct() {
		$this->dao = new DAO();
	}
	
	/**
	 * Retorna dados de gestor_credito_parametrizacao.
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @version 28/10/2013
	 * @param int $prptpcoid
	 * @param int $prptppoid
	 * @param string $tipo_pessoa
	 * @param int $prptppoid_sub
	 * @return array / false
	 */
	public function gestorCreditoParametrizacaoGetDados($prptpcoid, $prptppoid, $tipo_pessoa, $prptppoid_sub){		 
		if(is_int($prptpcoid) && is_int($prptppoid) && is_string($tipo_pessoa) && is_int($prptppoid_sub)){
			return $this->dao->gestorCreditoParametrizacaoGetDados($prptpcoid, $prptppoid, $tipo_pessoa, $prptppoid_sub);
		} else{
			return false;
		}
	}
	
	/**
	 * Retorna o total de contratos de um cliente.
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @version 28/10/2013
	 * @param int $clioid
	 * @return int
	 */
	public function clienteGetTotalContratos($clioid){
		return $this->dao->clienteGetTotalContratos($clioid);
	}
	
	/**
	 * Retorna os dados do cliente pagador.
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @version 28/10/2013
	 * @param int $prptpcoid
	 * @return array / false
	 */
	public function clientePagadorGetDados($prptpcoid){
		return $this->dao->clientePagadorGetDados($prptpcoid);
	}

    /**
     * Retorna o maior atraso de um cliente
     * @author Vinicius senna <vsenna@brq.com>
     * @version 02/05/2014
     * @param int $titclioid id do cliente
     * @return string / false
     */
    public function clienteBuscaMaiorAtraso($titclioid){
        return $this->dao->clienteBuscaMaiorAtraso($titclioid);
    }

    /**
     * Retorna dias em atraso deo cliente
     * @author Vinicius senna <vsenna@brq.com>
     * @version 02/05/2014
     * @param int $titclioid id do cliente
     * @return string / false
     */
    public function clienteBuscaDiasAtraso($titclioid){
        return $this->dao->clienteBuscaDiasAtraso($titclioid);
    }
	
	/**
     * Verifica se o cliente pagador possui titulos em atraso há mais de 15 dias.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 18/10/2013
     * @param int $clioid
     * @return boolean
     */
    public function verificaPendenciaTitulosInterna($clioid){
    	return $this->dao->verificaPendenciaTitulosInterna($clioid);
    }
    
    /**
     * Retorna a média da quantidade de dias de atraso do cliente.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 18/10/2013
     * @param int $clioid (ID do Cliente)
     * @return int
     */
    public function getMediaAtrasoCliente($clioid){
    	if(is_int($clioid)){
    		$resultSet = $this->dao->getMediaAtrasoCliente($clioid);
    		
    		if(is_array($resultSet)){
                if(!is_null($resultSet['media'])) {
                    return (int) $resultSet['media'];
                } else{
                    return false;
                }
    			
    		} else{
    			return false;
    		}
    	} else{
    		return false;
    	}
    }
    
    /**
     * Retorna um array com as formas de cobrança.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 30/10/2013
     * @return array
     */
    public function getFormasCobranca(){
    	$resultSet = $this->dao->getFormasCobranca();
    	
    	if(!empty($resultSet)){
    		$result = array();
    		foreach ($resultSet as $row){
    			$result[$row['forcoid']] = $row['forcnome'];
    		}
    		return $result;
    	} else{
    		return array();
    	}
    }
    
    /**
     * Retorna o numero de contratos ativos do cliente pagador.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 30/10/2013
     * @param int $clioid (ID do cliente)
     * @param boolean $pagador (Informa se o cliente é ou não o pagador)
     * @return int
     */
    public function getTotalContratosAtivos($clioid, $pagador){    	 
    	return $this->dao->getTotalContratosAtivos($clioid, $pagador);
    }
    
    /**
     * Retorna o tempo de relacionamento do cliente (Em meses)
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 30/10/2013
     * @param int $clioid
     * @return float / int
     */
    public function getTempoRelacionamentoCliente($clioid){
    	$resultSet = $this->dao->getTempoRelacionamentoCliente($clioid);
    	if(is_array($resultSet)){
    		return (float) $resultSet['meses'];
    	} else{
    		return 0;
    	}
    }
    
    /**
     * Soma os títulos em atraso do cliente pagador.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 30/10/2013
     * @param int $clioid
     * @return float / false
     */
    public function getValorTitulosAtrasados($clioid){
    	$resultSet = $this->dao->getValorTitulosAtrasados($clioid);
    	if(is_array($resultSet)){
    		return (float) $resultSet['total'];
    	} else{
    		return 0;
    	}
    }
    
    /**
     * Método retorna o valor médio dos títulos do cliente
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 04/11/2013
     * @param int $clioid (ID do cliente)
     * @return mixed média/false
     */
    public function getValorMedioTitulosAtivos($clioid){
    	$resultSet = $this->dao->getValorMedioTitulosAtivos($clioid);
    	return (float) $resultSet['media'];
    }
    
    /**
     * Método retorna o NÚMERO total de títulos ativos do cliente
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 04/11/2013
     * @param int $clioid (ID do cliente)
     * @return int Número de títulos
     */
    public function getNumeroTotalTitulosAtivos($clioid){
    	return $this->dao->getNumeroTotalTitulosAtivos($clioid);
    }
    
    /**
     * Método retorna o VALOR total de títulos ativos do cliente
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 24/10/2013
     * @param int $clioid (ID do cliente)
     * @return float valor total de títulos
     */
    public function getValorTotalTitulosAtivos($clioid){
    	$resultSet = $this->dao->getValorTotalTitulosAtivos($clioid);
    	return (float) $resultSet['total'];
    }

    /**
     * Método retorna o Valor Medio de títulos ativos do cliente
     *
     * @author Vinicius Senna <vsenna@brq.com>
     * @version 13/05/2014
     * @param int $clioid (ID do cliente)
     * @return float valor medio de titulos
     */
    public function getValorMedioTitulos($clioid){
        $resultSet = $this->dao->getValorMedioTitulos($clioid);
        return (float) $resultSet['media'];
    }

    /**
     * Método retorna a menor data de contrato do cliente
     * @author Vinicius Senna <vsenna@brq.com>
     * @version 13/05/2014
     * @param int $clioid (ID do cliente)
     * @param int/false $pagador
     * @return string menor data contrato
     */
    public function clienteBuscaMenorDataContrato($clioid, $pagador){
        $resultSet = $this->dao->clienteBuscaMenorDataContrato($clioid, $pagador);
        return (string) $resultSet['data'];
    }

    /**
     * Método retorna se existe pendencia financeira
     * @author Vinicius Senna <vsenna@brq.com>
     * @version 13/05/2014
     * @param int $clioid (ID do cliente)
     * @return boolean 
     */
    public function clienteVerificaInadimplencia($clioid){
        $resultSet = $this->dao->clienteVerificaInadimplencia($clioid);
        return (boolean) $resultSet['dias_atraso'];
    }

    /**
     * Método retorna a soma dos contratos ativos do cliente
     * @author Vinicius Senna <vsenna@brq.com>
     * @version 13/05/2014
     * @param int $clioid (ID do cliente)
     * @param int/false $pagador
     * @return string total de contratos ativos
     */
    public function clienteSomarTotalcontratosAtivos($clioid, $pagador){
        $resultSet = $this->dao->clienteSomarTotalcontratosAtivos($clioid, $pagador);
        return (int) $resultSet['total'];
    }
}