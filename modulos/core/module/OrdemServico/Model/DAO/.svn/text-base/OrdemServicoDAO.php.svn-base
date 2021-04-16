<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Rafael Dias <rafael.dias@meta.com.br>
 * @version 08/11/2013
 * @since 08/11/2013
 * @package Core
 * @subpackage Classe de Acesso a Dados de OS
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */

namespace module\OrdemServico;

use infra\ComumDAO;

class OrdemServicoDAO extends ComumDAO{
    
    public function __construct() {
        parent::__construct();
    }
     
    /**
     * Grava dados de OS
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 09/12/2013
     * @param string $campos
     * @param string $valores
     * @return mixed array/false
     */
    public function ordemServicoInsert($campos, $valores) {
    	$sqlString = "
        		INSERT INTO ordem_servico
	        		($campos)
    			VALUES
    				($valores)
    			RETURNING
    				ordoid;";
    	$this->queryExec($sqlString);	    	   	
    
    	if($this->getNumRows() > 0){
    		return $this->getAssoc();	    		
    	} else{
    		return false;
    	}
    }

    /**
     * Atualiza dados da OS
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 09/12/2013
     * @param string $dados
     * @param integer $ordoid    
     * @return mixed int/false
     */
    public function ordemServicoUpdate($dados, $ordoid){
    	$sqlString = "UPDATE ordem_servico SET $dados WHERE ordoid = $ordoid RETURNING ordoid;";        
    	$this->queryExec($sqlString);
        
        if($this->getAffectedRows() > 0){
            return $this->getAssoc();
        } else{
            return false;
        }
    }
    
    /**
     * Retorna lista de itens gravados na proposta
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 02/12/2013
     * @param int $prpoid
     * @return mixed array/false
     */
    public function ordemServicoItensPropostaListGet($prpoid){
    	$sqlString = "
			SELECT 
				otioid AS ositotioid, 
				prpeqcoid AS ositeqcoid,
				concatena('ACESSORIOS'||'-'||formata_str(ostdescricao)||'-'||formata_str(otidescricao)) AS ositobs,
				prosqtde AS qtd
			FROM 
				os_tipo_item
			INNER JOIN
				os_tipo ON ostoid = otiostoid
			INNER JOIN
				proposta_servico ON otiobroid = prosobroid
			INNER JOIN
				proposta ON prosprpoid = prpoid
			WHERE 
				prosinstalar IS TRUE
			AND
				otiostoid = 1
			AND
				otidt_exclusao IS NULL
			AND
				prosiexclusao IS NULL
			AND
				prpoid = $prpoid
			GROUP BY 
				otioid, prpeqcoid, prosqtde;";
    	
    	$this->queryExec($sqlString);
    	
    	if($this->getNumRows() > 0){
    		return $this->getAll();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Busca serviço de instalacao de equipamento.
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 23/01/2014
     * @param int $ordoid
     * @return mixed array/false
     */
    public function ordemServicoItemEquipamentoListGet($prpoid){
    	$sqlString = "
    		SELECT 
				otioid AS ositotioid, 
    			prpeqcoid AS ositeqcoid,
				concatena('EQUIPAMENTO'||'-'||formata_str(ostdescricao)||'-'||formata_str(otidescricao)) AS ositobs,
				1 AS qtd
			FROM os_tipo_item
				INNER JOIN os_tipo ON ostoid = otiostoid
    			LEFT JOIN proposta ON prpoid = $prpoid
			WHERE TRUE
				AND otioid = 3
				AND ostoid = 1
				AND otidt_exclusao IS NULL
			GROUP BY 
				otioid, prpeqcoid;
    	";
    	 
    	$this->queryExec($sqlString);
    	 
    	if($this->getNumRows() > 0){
    		return $this->getAll();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Verifica se um contrato possui OS ativa.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 09/12/2013
     * @param (int) $connumero
     * @return mixed ordoid/false
     */
    public function ordemServicoContratoGet($connumero){
    	$sqlString = "
    		SELECT
    			ordoid
		  	FROM
		  		ordem_servico
		  	WHERE
		  		ordconnumero = $connumero
		  	AND
		  		ordstatus IN (1,4)
		  	ORDER BY
		  		orddt_ordem DESC
		  	LIMIT 1;";
    	 	
    	$this->queryExec($sqlString);
    	   	
    	if($this->getNumRows() > 0){
            return $this->getAssoc();
        } else{
            return false;
        }
    }
    
    /**
     * Inclui um item da OS.
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 02/12/2013
     * @return mixed array/false
     */
    public function itemInsert($itemArray){
    	$sqlString = "
    		SELECT
    			ordem_servico_item_i('{\"" .$itemArray['ositotioid']."\"
    								   \"" .$itemArray['ositordoid']."\"
    								   \"\"
    								   \"" .$itemArray['ositeqcoid']."\"
    								   \"" .$itemArray['ositobs']."\"
    								   \"P\"
    								   \"NULL\" }') as ositoid;";
    	$this->queryExec($sqlString);
    	
    	if($this->getNumRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Atualiza dados do item da OS
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 24/01/2014
     * @param string $dados
     * @param integer $ositoid
     * @return mixed int/false
     */
    public function ItemUpdate($dados, $ositoid){
    	$sqlString = "UPDATE ordem_servico_item SET $dados WHERE ositoid = $ositoid RETURNING ositoid;";
    	$this->queryExec($sqlString);
    
    	if($this->getAffectedRows() > 0){
    		return $this->getAssoc();
    	} else{
	    	return false;
	    }
    }

    /**
     * Inclui registro em ordem_situacao
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 03/12/2013
     * @param array $ordemSituacaoArray
     * @return boolean
     */
    public function ordemSituacaoInsert($ordemSituacaoArray){
    	$sqlString = "
    		INSERT INTO ordem_situacao
    			(orsordoid, orssituacao, orsusuoid, orsstatus)
			VALUES
    			(".$ordemSituacaoArray['orsordoid'].", '".$ordemSituacaoArray['orssituacao']."', ".$ordemSituacaoArray['orsusuoid'].", NULL);";
    	
    	$this->queryExec($sqlString);
    	
    	if($this->getAffectedRows() > 0){
    		return true;
    	} else{
    		return false;
    	}
    }
    
    /**
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 10/12/2013
     * @param int $ordoid
     * @return array/false
     */
    public function ordemServicoItensListGet($ordoid){
    	$sqlString = "
    		SELECT 
    			ositoid,
				ositotioid, 
				ositeqcoid,
				ositobs,
				1 AS qtd
			FROM 
				ordem_servico
				INNER JOIN ordem_servico_item ON ositordoid = ordoid
			WHERE 
				TRUE
				AND ordstatus != 9
				AND ordoid = $ordoid";
    	
    	$this->queryExec($sqlString);
    	
    	if($this->getNumRows() > 0){
    		return $this->getAll();
    	} else{
    		return false;
    	}
    }
}