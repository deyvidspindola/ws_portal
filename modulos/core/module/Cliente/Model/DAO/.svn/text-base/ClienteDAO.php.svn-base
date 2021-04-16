<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Jorge A. D. kautzmann
 * @version 29/08/2013
 * @since 29/08/2013
 * @package Core
 * @subpackage Classe de Acesso a Dados de Cliente
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace module\Cliente;

use infra\ComumDAO;

class ClienteDAO extends ComumDAO{
    
    public function __construct() {
        parent::__construct();
    }

    public function getById($id){

        $sql = "
            SELECT 
                clitipo as cliente_tipo,
                clinome as cliente_nome,
                clino_cpf as cliente_cpf,
                clino_cgc as cliente_cnpj,
                endlogradouro as endereco_cobranca_logradouro,
                endno_numero as endereco_cobranca_numero,
                endcomplemento as endereco_cobranca_complemento,
                endbairro as endereco_cobranca_bairro,
                endcidade as endereco_cobranca_cidade,
                enduf as endereco_cobranca_uf,
                endcep as endereco_cobranca_cep
            FROM
                clientes
            JOIN
                endereco ON endoid = cliend_cobr
            WHERE
                clioid = $id
            LIMIT 1;
        ";

        $this->queryExec($sql);

        return $this->getNumRows() > 0 ? $this->getObject(0) : null;

    }
    
    /**
     * Busca de dados de cliente
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
     * @param string $whereKey (condição de comparação da chave)
     * @return Array de dados do cliente
     */
    public function getClienteByKey($whereKey = '') {
     	$sqlString = "
        		SELECT
                    *
         		FROM
					clientes
                LEFT JOIN endereco
                ON endoid = cliend_cobr
                WHERE " .
                    $whereKey . " 
                AND
                    clidt_exclusao IS NULL;";
				    	
        $this->queryExec($sqlString);
        
        if($this->getNumRows() > 0){
            return $this->getAssoc();
        } else{
            return array();
        }
    }
     
    /**
     * Insere novo Cliente
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 10/09/2013
     * @param String $campos
     * @param String $valores
     * @return array
     */
    public function insertCliente($campos, $valores){
    	$sqlString = "INSERT INTO clientes (".$campos.") VALUES (".$valores.") RETURNING clioid;"; 
        $this->setDebug(true);        
        $this->queryExec($sqlString);
        
        if($this->getNumRows() > 0){
        	return $this->getAssoc();
        } else{
        	return array();
    	}
    }
    
    /**
     * Atualiza Cliente
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 10/09/2013
     * @param String $dados
     * @param int $clioid
     * @return array
     */
    public function updateCliente($dados, $clioid){
    	$sqlString = "UPDATE clientes SET $dados WHERE clioid = $clioid RETURNING clioid;";
    	$this->queryExec($sqlString);
    	if($this->getAffectedRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return array();
    	}
    }
    
    /**
     * Busca o endereço do cliente pelo ID
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 11/09/2013
     * @param $endoid (ID do endereço)
     * @return Array com dados do endereço
     */
    public function getEnderecoByID($endoid){
    	$sqlString = "
    		SELECT 
			    *
			FROM
    			endereco
    		WHERE
    			endoid = $endoid;";
    	
    	$this->queryExec($sqlString);
    	 
    	if($this->getNumRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return array();
    	}
    }
    
    /**
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 12/09/2013
     * @param int $clioid
     * @return boolean
     */
    public function verificaClienteByKey($whereKey = ''){
        $sqlString = "
    		SELECT
    			clioid
    	    FROM
    			clientes
            WHERE " .
                $whereKey . ";";
        
     	$this->queryExec($sqlString);
    	
    	if($this->getNumRows() > 0){
    		return true;
    	} else{
    		return false;
    	}
    }
    
    /**
     * Método para inserir registro de endereço do Cliente (tabela endereco)
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 23/09/2013
     * @param String $campos
     * @param String $valores
     * @return array
     */
    public function clienteEnderecoInsert($campos, $valores){
    	$sqlString = "INSERT INTO endereco (".$campos.") VALUES (".$valores.") RETURNING endoid;";    	
    	$this->queryExec($sqlString);
    	
    	if($this->getNumRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return array();
    	}
    }
    
    /**
     * Atualiza registro de endereço do Cliente (tabela Endereço)
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 27/09/2013
     * @param string $dados
     * @param int $endoid
     * @return array
     */
    public function clienteEnderecoUpdate($dados, $endoid){
    	$sqlString = "UPDATE endereco SET $dados WHERE endoid = $endoid RETURNING endoid;";
    	$this->queryExec($sqlString);
    	 
    	if($this->getAffectedRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return array();
    	}
    }
    
    /**
     * Método para inserir registro de forma de cobrança do Cliente (tabela cliente_cobranca)
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 03/02/2014
     * @param String $campos
     * @param String $valores
     * @return array
     */
    public function formaCobrancaInsert($campos, $valores){
    	$sqlString = "INSERT INTO cliente_cobranca (".$campos.") VALUES (".$valores.") RETURNING clicoid;";
    	$sqlString = str_replace("'NULL'", "NULL", $sqlString);
    	$sqlString = str_replace(",0,", ",NULL,", $sqlString);
    	$this->queryExec($sqlString);
    	 
    	if($this->getNumRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return array();
    	}
    } 
       
    /**
     * Exclui todos os registros de forma de cobrança do Cliente (tabela cliente_cobranca)
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 03/02/2014
     * @param String $dados
     * @param int $clioid
     * @return boolean
     */
    public function formaCobrancaDeleteAll($clioid, $usuoid){
    	$sqlString = "UPDATE cliente_cobranca SET clicexclusao = now(), clicusuoid = $usuoid WHERE clicclioid = $clioid;";
    	$this->queryExec($sqlString);
    
    	if($this->getAffectedRows() > 0){
    		return true;
    	} else{
    		return false;
    	}
    } 
    
    /**
     * Busca dados de forma de cobrança do cliente
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 03/02/2014
     * @param $clioid (ID do cliente)
     * @return Array de dado de forma de cobrança
     */
    public function getFormaCobranca($clioid){
    	$sqlString = "
	    	SELECT
	    		*
	    	FROM
	    		cliente_cobranca
	    	WHERE
	    		clicclioid = $clioid
    			AND clicexclusao IS NULL
    		ORDER BY
    			clicoid DESC
    		LIMIT 1;";
    	 
    	$this->queryExec($sqlString);
    
    	if($this->getNumRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return array();
    	}
    }
    
    /**
     * Exclui um cliente.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 27/09/2013
     * @param String $dados
     * @param int $clioid
     * @return boolean
     */
    public function exclui($clioid, $usuoid){
    	$sqlString = "UPDATE clientes SET clidt_exclusao = now(), cliusuoid_alteracao = $usuoid WHERE clioid = $clioid;";
    	$this->queryExec($sqlString);
    	 
    	if($this->getAffectedRows() > 0){
    		return true;
    	} else{
    		return false;
    	}
    }
}