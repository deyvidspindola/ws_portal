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
 * @subpackage Classe de Acesso a Dados de Contrato
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace module\Contrato;

use infra\ComumDAO,
    infra\Helper\Mascara;

class PropostaDAO extends ComumDAO{
    
    public function __construct(){
        parent::__construct();
    }
    
    /**
     * Insere/cria uma porposta com status = Z em elaboração
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 16/09/2013
     * @param int $prptppoid (modalidade ID tabela tipo proposta)
     * @param string $prptipo_proposta (modalidade CODIGO tabela tipo proposta)
     * @param int $prptpcoid (tipo de contrato, tabela tipo_contrato)
     * @param int $prpusuoid (ID do usuario)
     * @return mixed $prpoid = ok / null = falha
    */
    public function insert($prptppoid, $prptipo_proposta, $prptpcoid, $prpusuoid){
        $sqlString = "INSERT INTO proposta (prptppoid, prptipo_proposta, prptpcoid, prpusuoid, prpstatus) 
                      VALUES (". $prptppoid . ", '". $prptipo_proposta . "' , " . $prptpcoid. " , " . $prpusuoid. " , 'Z') RETURNING prpoid;";
        $this->queryExec($sqlString);
        if($this->getNumRows() > 0){
            return $this->getCell();
        } else{
            return false;
        }
    }
    
    /**
     * Busca o código do tipo de proposta pelo ID do tipo
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 16/09/2013
     * @param int $tppoid (ID do tipo de proposta)
     * @return int $tppcodigo (código do tipo = prptipo_proposta)
     */
    public function getTipoCodigo($tppoid=0){
        $sqlString = "SELECT tppcodigo FROM tipo_proposta 
                      WHERE tppoid = $tppoid";
        $this->queryExec($sqlString);
        if($this->getNumRows() > 0){
            return $this->getCell();
        } else{
            return false;
        }
    }
    
    /**
     * Insere um registro de histórico de manutenções da proposta
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 17/09/2013
     * @param int $prphprpoid (ID da proposta)
     * @param int $prphusuoid (ID do usuário)
     * @param string $prphobs (Observação)
     * @return mixed true=gravação ok / false gravação nok
     */
    public function historicoInsert($prphprpoid, $prphusuoid, $prphobs){
        $sqlString = "INSERT INTO proposta_historico (prphprpoid, prphusuoid, prphobs)
                      VALUES (". $prphprpoid . " , " . $prphusuoid. " , '" . $prphobs. "');";
        return $this->queryExec($sqlString);
     }
    
    /**
     * Atualiza a proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 17/09/2013
     * @paramint int $prpoid
     * @param sitring $dados
     * @return boolean true=gravação ok / false gravação nok
     */
    public function update($prpoid, $dados){
        $sqlString = "UPDATE proposta SET $dados WHERE prpoid = $prpoid RETURNING prpoid;";
        $this->queryExec($sqlString);
         
        if($this->getAffectedRows() > 0){
            return $this->getAssoc();
        } else{
            return false;
        }
    }
    
    /**
     * Apenas verifica se uma prpoid existe.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 18/09/2013
     * @param int $prpoid (ID da proposta)
     * @return int numRows (Número de registros)
     */
    public function exists($prpoid){
        $sqlString = "SELECT prpoid FROM proposta WHERE prpoid = $prpoid;";                
        $this->queryExec($sqlString);
        return $this->getNumRows();
    }
    
    /**
     * Inclui um item da proposta.
     * 
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 19/09/2013
     * @param string $campos
     * @param string $valores
     * @return mixed array/false
     */
    public function itemInsert($campos, $valores){
        $sqlString = "INSERT INTO proposta_item (".$campos.") VALUES (".$valores.") RETURNING pritoid;";        
        $this->queryExec($sqlString);
         if($this->getAffectedRows() > 0){
            return $this->getAssoc();
        } else{
            return false;
        }
    }
    
    /**
     * Atualiza um item de proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 20/09/2013
     * @param string $dados
     * @param int $pritoid
     * @return mixed array/false
     */
    public function itemUpdate($dados, $pritoid){
        $sqlString = "UPDATE proposta_item SET $dados WHERE pritoid = $pritoid RETURNING pritoid;";
        $this->queryExec($sqlString);
        
        if($this->getAffectedRows() > 0){
            return $this->getAssoc();
        } else{
            return false;
        }
    }

    /**
     * Exclui um item da proposta
     *
     * @author Fabio Andrei Lorentz <fabio.lorentz@ewave.com.br>
     * @version 20/05/2014
     * @param  integer $pritoid (ID do ITEM da proposta)
     * @param  integer $usuoid  (ID do usuario que excluiu o item)
     * @return mixed array/false
     */
    public function itemDelete($pritoid, $usuoid) {
        $sqlString = "UPDATE proposta_item 
                      SET pritdt_exclusao = NOW(),
                          pritusuoid_exclusao =  $usuoid
                      WHERE pritoid = $pritoid
                      RETURNING pritoid";
        $this->queryExec($sqlString);

        if($this->getAffectedRows() > 0){
            return $this->getAssoc();
        } else{
            return false;
        }                      
    }

    /**
     * Exclui todos itens da proposta
     *
     * @author Fabio Andrei Lorentz <fabio.lorentz@ewave.com.br>
     * @version 20/05/2014
     * @param  integer $prpoid  (ID da proposta)
     * @param  integer $usuoid  (ID do usuario que excluiu todos os itens)
     * @return boolean
     */
    public function itensDelete($prpoid, $usuoid) {
        $sqlString = "UPDATE proposta_item 
                      SET pritdt_exclusao = NOW(),
                          pritusuoid_exclusao =  $usuoid
                      WHERE pritprpoid = $prpoid";
        $response = $this->queryExec($sqlString);

        return $response;
    }
    
    /**
     * Liga cliente a proposta
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 19/09/2013
     * @param string $dados (campo = valor, campo = valor, ...)
     * @param int $prpoid (ID da proposta)
     * @return mixed prpclioid/false
     */
    public function setCliente($dados, $prpoid){
        $sqlString = "UPDATE proposta SET $dados WHERE prpoid = $prpoid RETURNING prpclioid;";
        $this->queryExec($sqlString);
         
        if($this->getAffectedRows() > 0){
            return $this->getAssoc();
        } else{
            return false;
        }
    }
    
    /**
     * Busca dados de uma proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 23/09/2013
     * @param $prpoid (ID da proposta)
     * @return array com os dados da proposta|false
     */
    public function getDados($prpoid){
        $sqlString = "
            SELECT
                *
            FROM
                proposta
            WHERE
                prpoid = $prpoid
        	AND
        		prpdt_exclusao IS NULL;";
        
        $this->queryExec($sqlString);
        
        if($this->getNumRows() > 0){
            return $this->getAssoc();
        } else{
            return false;
        }
    }
    
    /**
     * Retorna as informações de Equipamento Classe da Proposta.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 30/09/2013
     * @param int $prpeqcoid
     * @return array / false
     */
    public function getEquipamentoClasseDados($prpeqcoid){
        $sqlString = "
            SELECT
                eqcoid,
                eqcdescricao,
                eqcvlr_mens,
                eqcvlr_resgate,
                eqcecgoid,
                eqcobroid,
                eqcnao_altera_classe,
                eqcinativo,
                eqccategoria,
                eqcobroid_servico,
                eqcproj_245,
                eqcprazo_inst,
                eqcmovel,
                eqcgera_os,
                eqcaltera_placa_veiculo,
                eqcnivel,
                eqchoras_inst,
                eqccodigo_servico_denatran,
                eqcservico_sbtec,
                eqcvlr_minimo_mens,
                eqcvlr_maximo_mens,
                eqcobroid_revenda,
                eqcde_para_sbtec,
                eqcpermite_telemetria,
                eqcplano_satelital,
                eqcpermite_satelital,
                eqcversao_satelital,
                eqclimite_acessorios
            FROM
                equipamento_classe
            WHERE
                eqcoid = $prpeqcoid;";
        
        $this->queryExec($sqlString);
        
        if($this->getNumRows() > 0){
            return $this->getAssoc();
        } else{
            return false;
        }
    }
    
    /**
     * Retorna as informações da Obrigação Financeira.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 01/10/2013
     * @param int $eqcobroid
     * @return array com itens da obrigacao financeira | array vazio
     */
    public function getObrigacaoFinanceiraItens($eqcobroid){
        $sqlString = "
            SELECT
                ofioid,
                ofiobroid,
                ofiservico,
                ofivalor,
                ofitipo
            FROM
                obrigacao_financeira_item
            WHERE
                ofiobroid = $eqcobroid
            AND
                ofiusuoid_excl IS NULL;";
        $this->queryExec($sqlString);
        
        if($this->getNumRows() > 0){
            return $this->getAll();
        } else{
            return array();
        }
    }
    
    /**
     * Vincula dado do pagamento a proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid
     * @param int $usuoid
     * @param string $campos
     * @param string $valores
     * @return array / false
     */
    public function pagamentoInsert($prpoid, $usuoid, $campos, $valores){
    	$sqlString = "INSERT INTO proposta_pagamento (".$campos.", ppagprpoid, ppagusuoid)
    					VALUES (".$valores.", $prpoid, $usuoid) RETURNING ppagoid;";
    	$this->queryExec($sqlString);
    	
    	if($this->getAffectedRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Insere uma proposta serviço.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 07/10/2013
     * @param int $prpoid
     * @param int $usuoid
     * @param string $campos
     * @param string $valores
     * @return array / false
     */
    public function servicoInsert($prpoid, $usuoid, $campos, $valores){
    	$sqlString = "INSERT INTO proposta_servico (".$campos.", prosprpoid, prosusuoid)
    					VALUES (".$valores.", $prpoid, $usuoid) RETURNING prosoid;";
    	$this->queryExec($sqlString);
    	 
    	if($this->getAffectedRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Exclui todos os registros
     * da tabela proposta_pagamento onde ppagprpoid = $prpoid 
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 09/10/2013
     * @param int $prpoid
     * @param int $usuoid
     * @return boolean
     */
    public function pagamentoDelete($prpoid, $usuoid){
    	$sqlString = "DELETE FROM proposta_pagamento WHERE ppagprpoid = $prpoid;";
    	$this->queryExec($sqlString);
    	 
    	if($this->getAffectedRows() > 0){
    		return true;
    	} else{
    		return false;
    	}
    }
    
    /**
     * Excluí logicamente um registro da proposta_servico.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 10/10/2013
     * @param int $prosoid
     * @param int $usuoid
     * @return boolean
     */
    public function servicoDelete($prosoid, $usuoid){
    	$sqlString = "UPDATE proposta_servico SET prosiexclusao = now(), prosusuoid_excl = $usuoid WHERE prosoid = $prosoid;";
    	$this->queryExec($sqlString);
    	 
    	if($this->getAffectedRows() > 0){
    		return true;
    	} else{
    		return false;
    	}
    }
    
    /**
     * Retorna os dados da proposta_servico
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 10/10/2013
     * @param int $prosoid
     * @return array / false
     */
    public function servicoGetDados($prosoid){
    	$sqlString = "
            SELECT
                *
            FROM
                proposta_servico
            WHERE
                prosoid = $prosoid
            AND
                prosusuoid_excl IS NULL;";
    	
        $this->queryExec($sqlString);
        
        if($this->getNumRows() > 0){
            return $this->getAssoc();
        } else{
            return false;
        }
    }
    
    /**
     * Busca dados da Obrigação Financeira
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 11/10/2013
     * @param int $prosobroid
     * @return array / false
     */
    public function obrigacaoFinanceiraGetDados($prosobroid){
    	$sqlString = "
    		SELECT
    			*
    		FROM
    			obrigacao_financeira
    		WHERE
    			obroid = $prosobroid
    		AND
    			obrdt_exclusao IS NULL;";
    	 
    	$this->queryExec($sqlString);
    	
    	if($this->getNumRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Busca a lista de acessórios da proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 11/10/2013
     * @param int $prpoid (ID da proposta)
     * @return array array com todos os dados de acessórios da proposta
     *     OBS: busca todos os serviços onde prossituacao != M e prossituacao != B
     */
    public function acessorioGetList($prpoid){
    	$sqlString = "
	    	SELECT
	    		*
	    	FROM
	    		proposta_servico
	    	WHERE
	    		prosprpoid = $prpoid
	    	AND
	    		prossituacao != 'M'
    		AND
    			prossituacao != 'B'
    		AND
    			prosusuoid_excl IS NULL;";
    	
    	$this->queryExec($sqlString);
    	 
    	if($this->getNumRows() > 0){
    		return $this->getAll();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Busca a lista de acessórios de um ITEM de proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 16/10/2013
     * @param int $prpoid (ID da proposta)
     * @param int $pritoid (ID do ITEM)
     * @return array array com todos os dados de acessórios da proposta
     *     OBS: busca todos os serviços onde prossituacao != M e prossituacao != B
     */
    public function acessorioGetItemList($prpoid, $pritoid) {
    	$sqlString = "
	    	SELECT
	    		*
	    	FROM
	    		proposta_servico
	    	WHERE
	    		prosprpoid = $prpoid
	    	AND
	    		prospritoid = $pritoid
	    	AND
	    		prossituacao != 'M'
    		AND
    			prossituacao != 'B'
    		AND
    			prosusuoid_excl IS NULL;";
    	
    	$this->queryExec($sqlString);
    	 
    	if($this->getNumRows() > 0){
    		return $this->getAll();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Busca lista de opcionais do ITEM da proposta.
     *     OBS: busca todos os serviços mensais do item onde prossituacao = M
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 16/10/2013
     * @param int $prpoid (ID da proposta)
     * @param int $pritoid (ID do ITEM)
     * @return array / false
     */
    public function acessorioOpcionalItemGetList($prpoid, $pritoid){
    	$sqlString = "
	    	SELECT
	    		*
	    	FROM
	    		proposta_servico
	    	WHERE
	    		prosprpoid = $prpoid
	    	AND
	    		prospritoid = $pritoid
	    	AND
	    		prossituacao = 'M'
    		AND
    			prosusuoid_excl IS NULL;";
    	
    	$this->queryExec($sqlString);
    	 
    	if($this->getNumRows() > 0){
    		return $this->getAll();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Insere registro na proposta_comissao.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 11/10/2013
     * @param string $campos
     * @param string $valores
     * @return array / false
     */
    public function comissaoInsert($campos, $valores){
    	$sqlString = "INSERT INTO proposta_comissao (".$campos.")
    					VALUES (".$valores.") RETURNING pcomoid;";
    	$this->queryExec($sqlString);
    	
    	if($this->getAffectedRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Busca dados comerciais da proposta.
     *     OBS: retorna uma matriz completa com dados do comercial
     *
     * @author Bruno B. Affonso [bruno.bonfim@sacar.com.br]
     * @version 14/10/2013
     * @param int $prcoid (ID da proposta comercial)
     * @return array / false
     */
    public function comercialGetDados($prcoid){
    	$sqlString = "
	    	SELECT
	    		*
	    	FROM
	    		proposta_comercial
	    	WHERE
	    		prcoid = $prcoid
	    	AND
	    		prcexclusao IS NULL;";
    	 
    	$this->queryExec($sqlString);
    	
    	if($this->getNumRows() > 0){
    		return $this->getAll();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Retorna a quantidade de registros de gerenciadoras que estão vinculadas a uma proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sacar.com.br]
     * @version 14/10/2013
     * @param int $prpoid
     * @return int
     */
    public function gerenciadoraGetNumero($prpoid){
    	$sqlString = "
	    	SELECT
	    		prgoid
	    	FROM
	    		proposta_gerenciadora
	    	WHERE
	    		prgprpoid = $prpoid;";
    	 
    	$this->queryExec($sqlString);    	
    	return Mascara::inteiro($this->getNumRows());
    }
    
    /**
     * Inclui uma gerenciadora a uma proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sacar.com.br]
     * @version 14/10/2013
     * @param int $prpoid
     * @param int $prggeroid
     * @param int $prgsequencia
     * @return mixed $prgoid / false
     */
    public function gerenciadoraInsert($prpoid, $prggeroid, $prgsequencia){
    	$sqlString = "INSERT INTO proposta_gerenciadora (prgprpoid, prggeroid, prgsequencia, prgperiodo_ind) 
    					VALUES ($prpoid, $prggeroid, $prgsequencia, 't') RETURNING prgoid;";
    	$this->queryExec($sqlString);
    	 
    	if($this->getAffectedRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Exclui/remove uma gerenciadora.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 14/10/2013
     * @param int $prgoid (ID da gerenciadora)
     * @return boolean true/false
     */
    public function gerenciadoraDelete($prgoid){
    	$sqlString = "DELETE FROM proposta_gerenciadora WHERE prgoid = $prgoid;";
    	$this->queryExec($sqlString);
    	
    	if($this->getAffectedRows() > 0){
    		return true;
    	} else{
    		return false;
    	}
    }
    
    /**
     * Retorna array com lista de dados das gerenciadoras vinculadas na proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 14/10/2013
     * @param int $prpoid (ID da proposta)
     * @return mixed array/false
     */
    public function gerenciadoraGetList($prpoid){
    	$sqlString = "
	    	SELECT
	    		*
	    	FROM
	    		proposta_gerenciadora
	    	WHERE
	    		prgprpoid = $prpoid;";
    	 
    	$this->queryExec($sqlString);
    	   	
    	if($this->getNumRows() > 0){
    		return $this->getAll();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Retorna array com lista de dados da proposta_status_financeiro.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 15/10/2013
     * @param int $prppsfoid (ID da proposta_status_financeiro)
     * @return mixed array/false
     */
    public function statusFinanceiroGetDados($prppsfoid){
    	$sqlString = "
	    	SELECT
	    		*
	    	FROM
	    		proposta_status_financeiro
	    	WHERE
	    		psfoid = $prppsfoid;";
    	 
    	$this->queryExec($sqlString);
    	   	
    	if($this->getNumRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Busca dados financeiros da proposta (tabela proposta).
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 15/10/2013
     * @param int $prpoid (ID da proposta)
     * @return array/false
     */
    public function financeiroGetDados($prpoid){
    	$sqlString = "
	    	SELECT
	    		prppsfoid,
	    		prpusuoid_aprovacao_fin,
	    		prpobservacao_financeiro,
	    		prpresultado_aciap,
	    		prpdt_aprovacao_fin
	    	FROM
	    		proposta
	    	WHERE
	    		prpoid = $prpoid;";
    	 
    	$this->queryExec($sqlString);
    	   	
    	if($this->getNumRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Inclui um registro de contato.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 15/10/2013
     * @param string $campos
     * @param string $valores
     * @return mixed $prcoid/false
     */
    public function contatoInsert($campos, $valores){
    	$sqlString = "INSERT INTO proposta_contato (".$campos.") VALUES (".$valores.") RETURNING prcoid;";
    	$this->queryExec($sqlString);
    	
    	if($this->getAffectedRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Exclui/remove um registro de contato.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 15/10/2013
     * @param int $prpoid (ID da proposta)
     * @param int $prcoid (ID do contato)
     * @return boolean true/false
     */
    public function contatoDelete($prpoid, $prcoid){
    	$sqlString = "DELETE FROM proposta_contato WHERE prcprpoid = $prpoid AND prcoid = $prcoid;";
    	$this->queryExec($sqlString);
    	 
    	if($this->getAffectedRows() > 0){
    		return true;
    	} else{
    		return false;
    	}
    }
    
    /**
     * Retorna array com lista de contatos de um tipo .
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 15/10/2013
     * @param int $prpoid (ID da proposta)
     * @param string $prctipo (tipo do contato 'A'/'E'/'I')
     * @return mixed array/false
     */
    public function contatoGetList($prpoid, $prctipo){
    	$sqlString = "
	    	SELECT
	    		*
	    	FROM
	    		proposta_contato
	    	WHERE
	    		prcprpoid = $prpoid
    		AND
    			prctipo = '$prctipo';";
    	
    	$this->queryExec($sqlString);
    		
    	if($this->getNumRows() > 0){
    		return $this->getAll();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Grava/seta o status da proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 16/10/2013
     * @param int $prpoid (ID da proposta)
     * @param char $prpstatus (status da proposta: P=Pendente,R=Aguardando Retorno,C=Concluído,E=Cancelado,L=Aguardando Análise Financeira,T=Aguardando Análise Técnica)
     * @return char $prpstatus/false
     */
    public function statusUpdate($prpoid, $prpstatus){
    	$sqlString = "UPDATE proposta SET prpstatus = '$prpstatus' WHERE prpoid = $prpoid RETURNING prpstatus;";
        $this->queryExec($sqlString);
         
        if($this->getAffectedRows() > 0){
            return $this->getAssoc();
        } else{
            return false;
        }
    }
    
    /**
     * Retorna status da proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 16/10/2013
     * @param int $prpoid (ID da proposta)
     * @return char $prpstatus / false
     */
    public function statusGet($prpoid){
    	$sqlString = "
	    	SELECT
	    		prpstatus
	    	FROM
	    		proposta
	    	WHERE
    			prpoid = $prpoid;";
    	
    	$this->queryExec($sqlString);
    		
    	if($this->getNumRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Inclui um opcional na proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 06/11/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (usuário)
     * @param mixed $prospritoid (item ao qual o acessório é adicionado, caso valor 't' adiciona em todos os itens da proposta)
     * @param array $propostaOpcionalArray (array com dados do item opcional)
     *     OBS-> campos obrigatórios do $propostaOpcionalArray[]:
     *     int prosobroid -> ID da obrugação financeira do serviço/acessório a ser adicionado
     *     float prosvalor -> Valor que o Serviço foi Negociado com o Cliente
     *     boolean prosvalor_agregado_monitoramento -> indica que o valor é diluido no valor do monitoramento
     *
     * @return mixed array/false
     */
    public function propostaOpcionalInsert($campos, $valores, $prpoid, $usuoid, $prospritoid) {
    	$sqlString = "INSERT INTO proposta_servico (".$campos.", prosprpoid, prosusuoid, prospritoid)
    					VALUES (".$valores.", $prpoid, $usuoid, $prospritoid) RETURNING prosoid;";
    	$this->queryExec($sqlString);
    	
    	if($this->getAffectedRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Retorna todos os itens de uma proposta.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 06/11/2013
     * @param int $prpoid
     * @return array / false
     */
    public function propostaItemGetList($prpoid){
    	$sqlString = "
	    	SELECT
	    		*
	    	FROM
	    		proposta_item
	    	WHERE
	    		pritprpoid = $prpoid;";
    	 
    	$this->queryExec($sqlString);
    	
    	if($this->getNumRows() > 0){
    		return $this->getAll();
    	} else{
    		return false;
    	}
    }
    
    /**
     * Exclui/remove um opcional da proposta/item.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 07/11/2013
     * @param int $prpoid (ID da proposta)
     * @param int $prosoid (ID da proposta_servico)
     * @param int $usuoid (usuário)
     * @return boolean true/false
     */
    public function propostaOpcionalDelete($prpoid, $prosoid, $usuoid) {
    	$sqlString = "UPDATE proposta_servico
    					SET prosiexclusao = NOW(), prosusuoid_excl = $usuoid WHERE prosprpoid = $prpoid AND prosoid = $prosoid;";
        $this->queryExec($sqlString);
         
        if($this->getAffectedRows() > 0){
            return true;
        } else{
            return false;
        }
    }
    
    /**
     * Busca lista de opcionais da proposta.
     *     OBS: busca todos os serviços da proposta onde prossituacao = M
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 07/11/2013
     * @param int $prpoid (ID da proposta)
     * @return boolean array/false
     */
    public function propostaOpcionalGetList($prpoid) {
    	$sqlString = "
	    	SELECT
	    		*
	    	FROM
	    		proposta_servico
	    	WHERE
	    		prosprpoid = $prpoid
	    	AND
	    		prossituacao = 'M'
    		AND
    			prosusuoid_excl IS NULL;";
    	
    	$this->queryExec($sqlString);
    	 
    	if($this->getNumRows() > 0){
    		return $this->getAll();
    	} else{
    		return false;
    	}
    }
    

    /**
     * Busca lista de servicos do item da proposta
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 22/11/2013
     * @param int $prpoid (ID da proposta)
     * @param int $pritoid (ID do ITEM)
     * @return array / false
     */
    public function servicosItemGetList($prpoid, $pritoid){
        $sqlString = "
            SELECT
	            prossituacao,
	            prosobroid,
                obrtipo_obrigacao,
				prosqtde,
				prosvalor_tabela,
				prosvalor,
	            prosdesconto,
				prosinstalar,
				prosmotivo_naoinstalar,
				prosalioid,
				prosendoid_gerenciador
	       	FROM
                proposta_servico
            INNER JOIN
                obrigacao_financeira 
              ON prosobroid = obroid
            WHERE
                prosprpoid = $prpoid 
              AND
                (prossituacao = 'B' OR prospritoid = $pritoid)
              AND
                prosusuoid_excl IS NULL;";
        $this->queryExec($sqlString);
        if($this->getNumRows() > 0){
            return $this->getAll();
        } else{
            return false;
        }
    }
    

    /**
     * Verifica compatibilidade de acessório com modelo de veículo
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 22/11/2013
     * @param int $tpcoid (ID tipo contrato)
     * @param int $mlooid (ID modelo veículo)
     * @param int $obroid (ID obrigação financeira)
     * @param int $prpno_ano (ano do veículo)
     * @return array / false
     */
    public function verificaAcessorioModeloVeiculo($tpcoid, $mlooid, $obroid, $prpno_ano){
        $sqlString = "
            SELECT verifica_acessorio_modelo_veiculo(
                        " . $tpcoid . ",
                        " . $mlooid . ",
                        '" . $obroid . "',
                        " . $prpno_ano . ") AS retorno;";
        $this->queryExec($sqlString);
        if($this->getAssoc() > 0){
            return true;
        } else{
            return false;
        }
    }
    
    /**
     * Atualiza número do termo na proposta.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 02/12/2013
     * @param int $prpoid
     * @param int $connumero
     * @return boolean
     */
    public function termoAtualiza($prpoid, $connumero){
    	$sqlString = "UPDATE proposta SET prptermo = $connumero WHERE prpoid = $prpoid AND prptermo IS NULL;";
    	$this->queryExec($sqlString);
    	 
    	if($this->getAffectedRows() > 0){
    		return true;
    	} else{
    		return false;
    	}
    }
    
    /**
     * Retorna dados da proposta pagamento
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 19/11/2013
     * @param int $prpoid (ID da proposta)
     * @return mixed array/false
     */
    public function propostaPagamentoGet($prpoid){
    	$sqlString = "
    		SELECT
		    	*
		    FROM
		    	proposta_pagamento
		    WHERE
		    	ppagprpoid = $prpoid;";
    	
    	$this->queryExec($sqlString);
    	
    	if($this->getNumRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return false;
    	}
    }

    /**
     * Verifica se a classe/produto informado não é nulo e se faz
     * parte dos produtos ativos da Sascar.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 09/01/2014
     * @param int $prpeqcoid (ID Equipamento Classe)
     * @return mixed eqcinativo/false
     */
    public function propostaValidaClasse($prpeqcoid){
        $sqlString = "
    		SELECT
		    	eqcinativo
		    FROM
		    	equipamento_classe
		    WHERE
		    	eqcoid = $prpeqcoid;";
    	
    	$this->queryExec($sqlString);
    	
    	if($this->getNumRows() > 0){
    		return $this->getAssoc();
    	} else{
    		return false;
    	}
    }

    /**
     * Vincula o número externo que vem do SalesForce a proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 10/01/2014
     * @param int $prpoid (ID Proposta)
     * @param int $prpnumero_externo
     * @return mixed $prpoid/false
     */
    public function propostaReferenciaExternaUpdate($prpoid, $prpnumero_externo){
        $sqlString = "UPDATE proposta SET prpnumero_externo = $prpnumero_externo
                        WHERE prpoid = $prpoid RETURNING prpoid;";

        $this->queryExec($sqlString);
         
        if($this->getAffectedRows() > 0){
            return $this->getAssoc();
        } else{
            return false;
        }
    }
}