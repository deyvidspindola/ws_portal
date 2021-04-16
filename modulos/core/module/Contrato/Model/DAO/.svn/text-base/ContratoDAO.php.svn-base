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

class ContratoDAO extends ComumDAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Cria um contrato e retorna o número do mesmo.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 19/09/2013
     * @return $connumero|null
     */
    public function getConnumero(){
        $sqlString = "INSERT INTO contrato(condt_cadastro) VALUES (now()) RETURNING connumero;";
        $this->queryExec($sqlString);
         
        if($this->getNumRows() > 0){
            $resultSet = $this->getAssoc();
            return Mascara::inteiro($resultSet['connumero']);
        } else{
            return false;
        }
    }
    
    /**
     * Atualiza dados do Contrato 
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 18/11/2013
     * @param integer $connumero
     * @param String $dados
     * @return mixed int/false
     */
    public function update($connumero, $dados){
         $sqlString = "UPDATE contrato SET $dados WHERE connumero = $connumero;";
         if($this->queryExec($sqlString)){
             return true;
         } else{
             return false;
         }
    }
    

    /**
     * Inclui um item de Contrato Servico.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 25/11/2013
     * @param string $stringSqlInsert
     * @return mixed $consoid/false
     */
    public function servicoInsert($stringSqlInsert){
        $sqlString = "INSERT INTO contrato_servico " . $stringSqlInsert . " RETURNING consoid;";
        $this->queryExec($sqlString);
        if($this->getAffectedRows() > 0){
            return $this->getAssoc();
        } else{
            return false;
        }
    }
    

    /**
     * Verifica se o cliente já possui Semestralidade SASGC.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 25/11/2013
     * @param int $clioid (ID do cliente)
     * @param int $endoid_gerenciador (ID do endereço gerenciador)
     * @param int $obroidSASGC (ID da obrigacao financeira do SASGC)
     * @return boolean true=sim/false=não
     */
    public function clienteSemestralidadeExists($clioid, $endoid_gerenciador, $obroidSASGC){
        $sqlString = "
            SELECT cliooid
	        FROM cliente_obrigacao_financeira
	        WHERE clioclioid = " . $clioid . "
	            AND clioobroid = " . $obroidSASGC . "
	            AND cliodt_termino IS NULL
	            AND clioendoid_sasgerenciador = " . $endoid_gerenciador . "; ";
        $this->queryExec($sqlString);
        if($this->getNumRows() > 0){
            return true;
        } else{
            return false;
        }
    }

    /**
     * Verifica se o cliente já possui Semestralidade SASGC.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 25/11/2013
     * @param int $prpoid (ID do cliente)
     * @param int $obroidSASGC (ID da obrigacao financeira do SASGC)
     * @return mixed tpivalor=ok/false=erro
     */
    public function obrigacaoFinanceiraGetValor($prpoid, $obroidSASGC){
        $sqlString = "
            SELECT tpivalor AS valor
	        FROM tabela_preco
	        INNER JOIN tabela_preco_item ON tproid = tpitproid
	        INNER JOIN proposta_pagamento ON tpicpvoid = ppagcpvoid
	        WHERE tprstatus = 'A'
	           AND tpiexclusao IS NULL
	           AND tpiobroid = " . $obroidSASGC . "
	           AND ppagprpoid = " . $prpoid;
        $this->queryExec($sqlString);
        if($this->getNumRows() > 0){
            return $this->getAssoc();
        } else{
            return false;
        }
    }

    /**
     * Inclui obrigação financeira de Semestralidade para cliente.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 25/11/2013
     * @param string $stringSqlInsert
     * @return mixed $cliooid/false
     */
    public function clienteObrigacaoFinanceiraInsert($stringSqlInsert){
        $sqlString = "INSERT INTO cliente_obrigacao_financeira " . $stringSqlInsert . " RETURNING cliooid;";
        $this->queryExec($sqlString);
        if($this->getAffectedRows() > 0){
            return $this->getAssoc();
        } else{
            return false;
        }
    }
    
    /**
     * Exporta contatos para cliente.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 25/11/2013
     * @param int $prpoid (ID da proposta)
     * @param int $connumero (ID do contrato)
     * @param boolean $prpclioid (ID do cliente)
     * @param int $usuoid (ID do usuario)
     * @return boolean true/false
     */
    public function contatosTransf($prpoid, $connumero, $prpclioid, $usuoid){
        $sqlString = "SELECT proposta_exporta_contatos('  \"" . $prpoid . "\"
	                             \"" . $connumero . "\"
	                             \"" . $prpclioid . "\"
	                             \"" . $usuoid . "\"') AS retorno;";
        $this->queryExec($sqlString);
    	if($this->getNumRows() > 0){
     	    $result = $this->getAssoc();
    	    if($result['retorno'] == 0){
    	        return true;
    	    } else{
    	        return false;
    	    }
    	}else{
    	    return false;
    	}
    }


    /**
     * Transfere dados de pagamento.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 25/11/2013
     * @param int $prpoid  (ID proposta)
     * @param int $connumero (ID contrato)
     * @param boolean $prpcorretor_recebe_comissao (flag de comissão)
     * @param int $usuoid (ID do usuario)
     * @return boolean true/false
     */
    public function pagamentoTransfer($prpoid, $connumero, $prpcorretor_recebe_comissao, $usuoid){
        // Query para transferir os dados de pagemento
        $sqlString = "INSERT INTO contrato_pagamento (cpagconoid, cpagforcoid, cpagrenovacao, cpagusuoid, cpagforcoid_adesao,
	                    cpagobroid_servico, cpagvl_servico, cpagvl_desconto_servico, cpagvl_tabela_servico, cpagcartao,
	                    cpagcartao_validade, cpagcartao_codseg, cpagdebito_agencia, cpagdebito_cc, cpagbancodigo,
	                    cpagno_autorizacao, cpagbancodigo_adesao, cpagdebito_agencia_adesao, cpagdebito_cc_adesao, cpagcartao_adesao,
	                    cpagcartao_validade_adesao, cpagcartao_codseg_adesao, cpagclioid_adesao, cpagsituacao, cpagno_cheque,
	                    cpagcpvoid, cpagdeslocamento, cpagcorretor_recebe_comissao, cpagpedagio, cpagconnumero_roubado,
	                    cpagadesao, cpagadesao_parcela, cpagvl_tabela_instalacao, cpagvl_negociado_instalacao, cpagvl_desconto_instalacao, cpagmonitoramento,
						cpagvl_tabela_adesao, cpagvl_negociado_adesao, cpagvl_desconto_adesao, cpaglocal_inst, cpagvl_deslocamento, cpagmulta_rescissoria)
                		SELECT " . $connumero . ", ppagforcoid, ppagrenovacao, $usuoid, ppagforcoid_adesao,
	                    ppagobroid_servico, ppagvl_servico, ppagvl_desconto_servico, ppagvl_tabela_servico, ppagcartao,
	                    ppagcartao_validade, ppagcartao_codseg, ppagdebito_agencia, ppagdebito_cc, ppagbancodigo,
	                    ppagno_autorizacao, ppagbancodigo_adesao, ppagdebito_agencia_adesao, ppagdebito_cc_adesao, ppagcartao_adesao,
	                    ppagcartao_validade_adesao, ppagcartao_codseg_adesao, ppagclioid_adesao, ppagsituacao, ppagno_cheque,
	                    ppagcpvoid, ppagvl_deslocamento, '" . $prpcorretor_recebe_comissao . "', ppagpedagio, ppagconnumero_roubado,
	                    ppagadesao, ppagadesao_parcela, ppagvl_tabela_adesao, ppagvl_negociado_adesao, ppagvl_desconto_adesao, ppagmonitoramento,
						ppagvl_tabela_adesao, ppagvl_negociado_adesao, ppagvl_desconto_adesao, ppaglocal_inst, ppagvl_deslocamento, ppagmulta_rescissoria
	                    FROM proposta_pagamento
	                    WHERE ppagprpoid = $prpoid;";
         if($this->queryExec($sqlString)){
            return true;
        } else{
            return false;
        }
    }
    

    /**
     * Transfere Benefícios de Opcionais.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 28/11/2013
     * @param int $connumero (ID contrato)
     * @param char $tipoBeneficio (tipo de beneficio 'A=Assistência','P=Pacote')
     * @return boolean true/false
     */
    public function opcionaisBeneficioTransfer($connumero=0, $tipoBeneficio='A'){
        if($tipoBeneficio=='A'){
            $sqlString = "INSERT INTO cliente_beneficio(clbbstoid,clbclioid,clbebtoid)
                (SELECT  1,conclioid,obrebtoid
                FROM
                contrato,
                clientes,
                contrato_servico,
                obrigacao_financeira
                WHERE
                condt_exclusao IS NULL
                AND consiexclusao IS NULL
                AND obrebtoid>0
                AND conclioid=clioid
                AND consconoid=connumero
                AND consobroid=obroid
                AND conclioid NOT IN
                (SELECT clbclioid FROM cliente_beneficio WHERE clbdt_exclusao IS NULL AND clbclioid=conclioid AND clbebtoid=obrebtoid)
                AND connumero=$connumero);";
        }else{
            $sqlString = "INSERT INTO cliente_beneficio(clbbstoid,clbclioid,clbebtoid)
                (SELECT  1,conclioid,obrebtoid
                FROM
                contrato,
                clientes,
                contrato_servico,
                obrigacao_financeira_item,
                obrigacao_financeira
                WHERE
                ofiservico = obroid
                AND ofiexclusao IS NULL
                AND condt_exclusao IS NULL
                AND consiexclusao IS NULL
                AND obrebtoid>0
                AND conclioid=clioid
                AND consconoid=connumero
                AND consobroid=ofiobroid
                AND conclioid NOT IN
                (SELECT clbclioid FROM cliente_beneficio WHERE clbdt_exclusao IS NULL AND clbclioid=conclioid AND clbebtoid=obrebtoid)
                AND connumero=$connumero);";
        }
        // Pode não ter registro para inserir
        // nesse caso só verifica se não houve erro
        if($this->queryExec($sqlString)){
            return true;
        } else{
            return false;
        }
    }


    /**
     * Transfere dados de pagamento.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 25/11/2013
     * @param int $prpoid  (ID proposta)
     * @param int $connumero (ID contrato)
     * @return boolean true/false
     */
    public function comissaoTransfer($prpoid, $connumero){
        // Query para transferir os dados de pagemento
        $sqlString = "INSERT INTO contrato_comissao (ccomconoid, ccomrtcoid, ccomrepoid, ccomusuoid,
	                    ccomtlmoid, ccomfunoid, ccomitloid, ccomresponsavel_venda_meta)
	                    SELECT " . $connumero . " AS contrato, pcomrtcoid, pcomrepoid, pcomusuoid,
	                    pcomtlmoid, pcomfunoid, pcomitloid, pcomresponsavel_venda_meta
	                    FROM proposta_comissao
	                    WHERE pcomdt_exclusao IS NULL
	                    AND pcomprpoid = $prpoid
	                    GROUP BY contrato, pcomrtcoid, pcomrepoid, pcomusuoid, pcomtlmoid, pcomfunoid, pcomitloid, pcomresponsavel_venda_meta;";
        
        // Pode não ter registro para inserir
        // nesse caso só verifica se não houve erro
        if($this->queryExec($sqlString)){
            return true;
        } else{
            return false;
        }
    }
    
    /**
     * Busca a proposta que gerou um determinado contrato
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 02/12/2013
     * @param int $connumero (ID contrato)
     * @return int $prpoid  (ID proposta)
     */
    public function propostaGet($connumero){    		
    	$sqlString = '
    				SELECT
    					prpoid 
    				FROM
    					proposta 
    				WHERE
    					prpdt_exclusao IS NULL 
    				AND
    					prpoid > 0
    				AND
    					prptermo ='.$connumero;
    	
    	$this->queryExec($sqlString);
    	    
    	if($this->getNumRows() > 0){
    		$resultSet = $this->getAssoc();
            return Mascara::inteiro($resultSet['prpoid']);
    	} else{
    		return false;
    	}
    }
       
    
    /**
     * Transfere dados de pagamento.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 25/11/2013
     * @param int $connumero (ID contrato)
     * @param int $vProposta (array com dados da Proposta)
     * @return boolean true/false
     */
    public function zonaComercialTransfer($connumero, $vProposta){
        // Query para transferir os dados de pagemento
        $sqlString = "UPDATE contrato
	                  SET conregcoid = " . $vProposta['prpregcoid'] . ",
	                        conrczoid = " . $vProposta['prprczoid'] . ",
	                        concampcoid = " . $vProposta['prpcampcoid'] . ",
	                        conprazo_contrato = " . $vProposta['prpprazo_contrato'] . "
	                  WHERE connumero =" . $connumero . "
                     ";
        // Pode não ter registro para inserir
        // nesse caso só verifica se não houve erro
        if($this->queryExec($sqlString)){
            return true;
        } else{
            return false;
        }
    }
    
    /**
     * Retorna todos os PRPTERMO de uma proposta de acordo com a proposta_item.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 19/12/2013
     * @param int $prpoid
     * @return array/false
     */
    public function contratoListGet($prpoid){
    	$sqlString = "
    				SELECT
    					pritprptermo
    				FROM
    					proposta_item
    				WHERE
    					pritprpoid = $prpoid
    				AND
    					pritdt_exclusao IS NULL;";
    	 
    	$this->queryExec($sqlString);
    		
    	if($this->getNumRows() > 0){
    		return $this->getAll();
    	} else{
    		return false;
    	}
    }    
}