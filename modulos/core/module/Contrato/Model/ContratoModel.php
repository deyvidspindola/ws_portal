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
 * @subpackage Classe Model de Contrato
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
*/
namespace module\Contrato;

use infra\Validacao,
    module\Contrato\PropostaDAO,
    infra\Helper\Mascara,
    module\Contrato\ContratoDAO;

class ContratoModel{
    // Atributos
    public $prpDAO; // Acesso a dados da Proposta
    public $cttDAO; // Acesso a dados do Contrato
    
    // Campos no BD (proposta)
    private $prpIntFieldList = array('prpoid', 'prptipo_pessoa', 'prpno_endereco1', 'prpno_cep1', 'prpno_endereco2', 'prpno_cep2',
            'prpno_ano', 'prpno_serie', 'prpno_fone_board', 'prpeqcoid', 'prpusuoid_excl', 'prpvendedoroid', 'prpno_endosso',
    		'prptermo_original', 'prpnum_parcela', 'prpcartao_codseg', 'prpprazo', 'prpsusep', 'prpfrota_veiculo',
    		'prpdias_demonstracao', 'prpusuoid_viaoriginal', 'prpdia_vcto_boleto', 'prpprazo_contrato', 'prpgarantia', 'prpclioid', 'prptermo');
    
    private $prpFloatFieldList = array('prploc_anual', 'prprenov_anual', 'prpvalvula', 'prpsleep', 'prpconversor'); // Campos float no BD (proposta)
	
    private $prpFkList = array('prpusuoid', 'prpusuoid_concl', 'prpusuoid_analise', 'prptppoid', 'prpautorizacao_alcada',
    		'prpcampcoid', 'prpclicloid', 'prpclifunoid', 'prpcorroid', 'prpendoid_inst', 'prpforcoid', 'prpgctoid', 'prpgeroid',
    		'prpgrcoid', 'prpindicadoroid', 'prplocioid', 'prpmcaoid', 'prpmlooid', 'prpmsuboid', 'prppecoid', 'prppednoid',
    		'prppmsoid', 'prpprcoid', 'prppromoid', 'prpprpcoid', 'prppsfoid', 'prppsfoidgestor', 'prprczoid', 'prpregcoid',
    		'prprelroid', 'prprelroid_instalacao', 'prprepcentraloid', 'prprepoid_instalacao', 'prptermo_contingente',
    		'prptexto_contrato', 'prpusu_proposta_gerada', 'prpusuoid_aprovacao_fin', 'prpusuoid_autorizacao',
    		'prpusuoid_autorizacao_tecnica', 'prpusuoid_liberacao'); // FOREIGN KEY QUE ACEITA VALOR: NULL
	
    // Campos no BD (Proposta servico)
    private $prpServicoIntFieldList = array('prosoid', 'prosprpoid', 'prosusuoid', 'prosobroid', 'prosusuoid_excl', 'pritusuoid_excl',
    		'prosqtde');
    
    private $prpServicoFloatFieldList = array('prosvalor_tabela', 'prosvalor', 'prosdesconto');
    
    private $prpServicoFkList = array('prosalioid', 'prosendoid_gerenciador', 'prosusuoid_excl'); // FOREIGN KEY QUE ACEITA VALOR: NULL
    
    // Campos no BD (Item proposta)
    private $prpItemIntFieldList = array('pritoid', 'pritprpoid', 'pritprptermo', 'pritobjeto', 'pritusuoid', 'pritusuoid_excl');    
    private $prpItemFloatFieldList = array();
    
    // Campos no BD (proposta_pagamento)
    private $prpPagamentoIntFieldList = array('ppagoid', 'ppagprpoid', 'ppagnum_parcela', 'ppagcartao_codseg', 'ppagusuoid',
    		'ppagpri_vencimento', 'ppagadesao_parcela', 'ppagforcoid_adesao',
    		'ppagno_autorizacao', 'ppagcartao_codseg_adesao', 'ppagno_cheque', 'ppagno_autorizacao_adesao',
    		'ppagdia_vencimento','adesao_parcela','forcoid_adesao');
    
    private $prpPagamentoFloatFieldList = array('ppaghabilitacao', 'ppagmonitoramento', 'ppaganuidade', 'ppagrenovacao',
    		'ppagvalvula', 'ppagsleep', 'ppagconversor', 'ppagmulta_rescissoria', 'ppagadesao', 'ppagvl_servico', 'ppagvl_desconto_servico',
    		'ppagvl_tabela_servico', 'ppagdeslocamento', 'ppagpedagio', 'ppagvl_negociado_adesao', 'ppagvl_tabela_adesao', 'ppagvl_desconto_adesao',
    		'ppagvl_deslocamento', 'ppagtvltx_instalacao');
    
    private $prpPagamentoFkList = array('ppagbancodigo_adesao', 'ppagbancodigo', 'ppagclioid_adesao', 'ppagconnumero_roubado', 'ppagcpvoid',
    		'ppagforcoid', 'adesao', 'ppagobroid_servico'); // FOREIGN KEY QUE ACEITA VALOR: NULL
    
    // Campos no BD (proposta_comissao)
    private $prpComercialIntFieldList = array('pcomoid');
    
    private $prpComercialFloatFieldList = array();
    
    private $prpComercialFkList = array('pcomfunoid', 'pcomitloid', 'pcomprpoid', 'pcomrepoid', 'pcomrtcoid', 'pcomtlmoid', 'pcomusuoid_excl',
    		'pcomusuoid'); // FOREIGN KEY QUE ACEITA VALOR: NULL
    
    // Campos no BD (proposta_contato)
    private $prpContatoIntFieldList = array('prcoid', 'prcsequencia', 'prcfone_array');
    private $prpContatoFloatFieldList = array('');
    private $prpContatoFkList = array('prcprpoid'); // FOREIGN KEY QUE ACEITA VALOR: NULL
    
    
    /*** CAMPOS REFERENTE A CONTRATO ***/
     
    // valores do tipo Integer (que não sejam FKs)
    private $cttIntFieldList = array('conno_dia_vencimento',  'conbanco', 
            'conagencia', 'conconta', 'conporta_panico_qdt',
            'conprazo_contrato', 'congarantia');
    
    // campos do tipo Float
    private $cttFloatFieldList = array('convl_mensalidade');
    
    // chaves estrangeiras (FKs) que aceitam NULL
    private $cttFkList = array('conclioid', 'conequoid', 'conveioid','conno_tipo', 'conitloid', 'convenitloid',
        'conrevcstoid', 'conrepcstoid', 'conrepcentraloid', 'conrepcomissaooid', 'conrepvendedoroid',
        'conordoid', 'coneqcoid', 'conusuoid', 'congeroid', 'concmcoid', 'congrcoid','conusuoid_exclusao',
        'conregcoid','conrczoid','conprazo_contrato','concampcoid');

    /***********************************/
    
    /**
     * Contrutor da classe
     * 
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
     * @param none
     * @return none
     */
    public function __construct() {
        $this->prpDAO = new PropostaDAO();
        $this->cttDAO = new ContratoDAO();
    }
    
    // MÉTODOS REFERENTES A PROPOSTA
    
    /**
     * Insere/cria uma porposta com status = Z em elaboração
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 16/09/2013
     * @param int $prptppoid (modalidade)
     * @param int $prptpcoid (tipo de contrato, tabela tipo_contrato)
     * @param int $prpusuoid (usuário que criou a proposta)
     * @return $prpoid = ok / null = falha
    */
    public function propostaInsert($prptppoid, $prptpcoid, $prpusuoid) {
        $prptipo_proposta = $this->prpDAO->getTipoCodigo($prptppoid);
        return $this->prpDAO->insert($prptppoid, $prptipo_proposta, $prptpcoid, $prpusuoid);
    }
    
    /**
     * Insere um registro de histórico da proposta
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 17/09/2013
     * @param $prphprpoid (ID da proposta)
     * @param $prphusuoid (ID do usuário)
     * @param $prphobs (Observação)
     * @return true=gravação ok / false gravação nok
     */
    public function propostaHistoricoInsert($prphprpoid, $prphusuoid, $prphobs) {
        return $this->prpDAO->historicoInsert($prphprpoid, $prphusuoid, $prphobs);
    }
    
    /**
     * Atualiza dados de uma proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 18/09/2013
     * @param int $prpoid (ID da proposta)
     * @param array $propostaArray array associativo contendo os campos e seus valores
     * @return mixed $prpoid/false
     */
    public function propostaUpdate($prpoid, $propostaArray) {
        $dados = '';
        $strSeparador = '';
        $propostaArray = $this->prpDAO->applyCast($propostaArray, $this->prpIntFieldList, $this->prpFloatFieldList, $this->prpFkList);
        
        foreach ($propostaArray as $key => $value){
            $dados .= $strSeparador . $key.' = '.$value;
            $strSeparador = ', ';
        }
        
        if(!empty($dados)){
            $resultSet = $this->prpDAO->update($prpoid, $dados);
            
            if(!empty($resultSet)){
                return Mascara::inteiro($resultSet['prpoid']);
            } else{
                return false;
            }
        } else{
            return false;
        }        
    }
    
    /**
     * Apenas verifica se uma prpoid existe.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 18/09/2013
     * @param $prpoid (ID da proposta)
     * @return true/false
     */
    public function propostaExists($prpoid=0) {
        if(!empty($prpoid)){
            $resultSet = $this->prpDAO->exists($prpoid);
            
            if($resultSet > 0){
                return true;
            } else{
                return false;
            }
        } else{
            return false;
        }  
    }
    
    /**
     * Inclui um item de proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 19/09/2013
     * @param array $propostaItemArray (array associativo tipo chave -> valor, dados da tabela proposta_item)
     * @return mixed $pritoid/false
     */
    public function propostaItemInsert($propostaItemArray){
        $campos  = '';
        $valores = '';
        $strSeparador = '';
        
        $propostaItemArray = $this->prpDAO->applyCast($propostaItemArray, $this->prpItemIntFieldList, $this->prpItemFloatFieldList);
        if($propostaItemArray['pritquantidade'] == 0){
            $propostaItemArray['pritquantidade'] = 1;
        } 
        foreach ($propostaItemArray as $key => $value){
            $campos .= $strSeparador . $key;
            $valores .= $strSeparador . $value;
            $strSeparador = ',';
        }
        $resultSet = $this->prpDAO->itemInsert($campos, $valores);
            
        if(!empty($resultSet)){
            // Solução temporária para atender o projeto siggo
            $vProposta = $this->propostaGetDados($propostaItemArray['pritprpoid']);
            
            if(is_array($vProposta)){
            	$connumero = Mascara::inteiro($vProposta['prptermo']);
            	
            	if($connumero == 0){
            		$this->prpDAO->termoAtualiza($propostaItemArray['pritprpoid'], $propostaItemArray['pritprptermo']);
            	}
            }
            
            return Mascara::inteiro($resultSet['pritoid']);
        } else{
            return false;
        }      
    }
    
    /**
     * Atualiza dados de um item de proposta
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 20/09/2013
     * @param array $propostaItemArray (array associativo tipo chave -> valor, dados da tabela proposta_item)
     * @return mixed $pritoid/false
     */
    public function propostaItemUpdate($propostaItemArray){
        $dados = '';
        $strSeparador = '';
        $pritoid = $propostaItemArray['pritoid'];
        
        $propostaItemArray = $this->prpDAO->applyCast($propostaItemArray, $this->prpItemIntFieldList, $this->prpItemFloatFieldList);
        if($propostaItemArray['pritquantidade'] == 0){
            $propostaItemArray['pritquantidade'] = 1;
        }
        foreach ($propostaItemArray as $key => $value){
            $dados .= $strSeparador . $key.' = '.$value;
            $strSeparador = ', ';
        }
         
        if(!empty($dados)){
            $resultSet = $this->prpDAO->itemUpdate($dados, $pritoid);
        
            if(!empty($resultSet)){
                return Mascara::inteiro($resultSet['pritoid']);
            } else{
                return false;
            }
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
     * @return mixed $pritoid/false
     */
    public function propostaItemDelete($pritoid, $usuoid) {
        $resultSet = $this->prpDAO->itemDelete($pritoid, $usuoid);
        
        if(!empty($resultSet)){
            return Mascara::inteiro($resultSet['pritoid']);
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
    public function propostaItensDelete($prpoid, $usuoid) {
        $resultSet = $this->prpDAO->itensDelete($prpoid, $usuoid);

        if($resultSet !== false){
            return true;
        } else{
            return false;
        }    
    }

    /**
     * Liga cliente a proposta
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 19/09/2013
     * @param string $dadosCliente
     * @param int $prpoid (ID da PROPOSTA)
     * @return mixed $prpclioid/false
     */
    public function propostaClienteSet($dadosCliente, $prpoid){
        //Pessoa Fisica ou Juridica
        if($dadosCliente['clitipo'] == 'F'){
            $prptipo_pessoa = 1;
            $prpno_cpf_cgc  = $dadosCliente['clino_cpf']; //CPF
        } else{
            $prptipo_pessoa = 2;
            $prpno_cpf_cgc  = $dadosCliente['clino_cgc']; //CNPJ
        }
        
        //Dados do cliente pra atualizar a proposta
        $dadosPropostaCliente = array('prptipo_pessoa' => $prptipo_pessoa, 'prplocatario' => $dadosCliente['clinome'],
                'prpno_cpf_cgc' => $prpno_cpf_cgc, 'prpclioid' => $dadosCliente['clioid']);
        
        //Aplicando CAST
        $dadosPropostaCliente = $this->prpDAO->applyCast($dadosPropostaCliente, $this->prpIntFieldList, $this->prpFloatFieldList, $this->prpFkList);
        $dados = '';
        $strSeparador = '';
        
        foreach ($dadosPropostaCliente as $key => $value){
            $dados .= $strSeparador . $key.' = '.$value;
            $strSeparador = ', ';
        }
        $resultSet = $this->prpDAO->setCliente($dados, $prpoid);

        if(!empty($resultSet)){
            return Mascara::inteiro($resultSet['prpclioid']);
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
     * @return Array com os dados da proposta|false
     */
    public function propostaGetDados($prpoid){ 
        if(is_int($prpoid)){
            return $this->prpDAO->getDados($prpoid);
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
     * @return array com os dados equipamento classe | false
     */
    public function getEquipamentoClasseDados($prpeqcoid){
        $vClasse = array();
    	if(is_int($prpeqcoid)){
    		$vClasse = $this->prpDAO->getEquipamentoClasseDados($prpeqcoid);
    		// Solicitação projeto SIGGO (CONTORNAR PROBLEMA eqcobroid_servico UNIQUE)
    		$vClasse['eqcobroid_servico'] = Mascara::inteiro($vClasse['eqcobroid_servico']);
    		if($vClasse['eqcobroid_servico'] == 0){
    		    $vClasse['eqcobroid_servico'] = 1;
    		}
    		return $vClasse;
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
     * @return array com os dados equipamento classe | false
     */
    public function getObrigacaoFinanceiraItens($eqcobroid){
    	if(is_int($eqcobroid)){
    		return $this->prpDAO->getObrigacaoFinanceiraItens($eqcobroid);
    	} else{
    		return false;
    	}
    }
    
    
    /**
     * Vincula dado do pagamento a proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (usuário)
     * @param array $propostaPagamentoArray (array com dados)
     *     OBS-> campos obrigatórios do $propostaPagamentoArray[]:
     *     prpforcoid -> forma de cobrança
     *     prpdia_vcto_boleto -> dia do vencimento
     *     prpcpvoid => parcelamento
     *     prpvl_servico => valor parcela locacao
     *     prppercentual_desconto_locacao -> percentual desconto locação
     *     prpvl_monitoramento -> valor do monitoramento
     *     prpprazo_contrato -> vigência do contrato
     *     prpagmulta_rescissoria -> valor multa resisória
     *     ppagtvltx_instalacao -> Taxa de instalação
     * @return mixed $prpoid/false
     */
    public function propostaPagamentoSet($prpoid, $usuoid, $propostaPagamentoArray){
    	if(is_int($prpoid) && is_int($usuoid) && is_array($propostaPagamentoArray)){
    	    $campos = '';
    	    $valores = '';
    	    $strSeparador = '';    		   	    
    	    $propostaPagamentoArray = $this->prpDAO->applyCast($propostaPagamentoArray, $this->prpPagamentoIntFieldList, $this->prpPagamentoFloatFieldList, $this->prpPagamentoFkList);
    		
	    	foreach ($propostaPagamentoArray as $key => $value){
	    		$campos  .= $strSeparador . $key;
	    		$valores .= $strSeparador . $value;
	    		$strSeparador = ',';
	    	}	
	            		
    		$resultSet = $this->prpDAO->pagamentoInsert($prpoid, $usuoid, $campos, $valores);
    		
    		if(is_array($resultSet)){
    			return Mascara::inteiro($resultSet['ppagoid']);
    		} else{
    			return false;
    		}
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
     * @param array $arrayPropostaServico
     * @return mixed $ppagoid / false
     */
    public function propostaServicoInsert($prpoid, $usuoid, $arrayPropostaServico){
    	$campos  = '';
    	$valores = '';
    	$prpoid  = Mascara::inteiro($prpoid);
    	$usuoid  = Mascara::inteiro($usuoid);
        $strSeparador = '';
    	$arrayPropostaServico = $this->prpDAO->applyCast($arrayPropostaServico, $this->prpServicoIntFieldList, $this->prpServicoFloatFieldList, $this->prpServicoFkList);
    	
    	foreach ($arrayPropostaServico as $key => $value){
    		$campos  .= $strSeparador . $key;
    		$valores .= $strSeparador . $value;
    		$strSeparador = ',';
    	}
    	
    	$resultSet = $this->prpDAO->servicoInsert($prpoid, $usuoid, $campos, $valores);

    	if(is_array($resultSet)){
    		return Mascara::inteiro($resultSet['prosoid']);
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
     * @param int $prpoid (PK da tabela proposta)
     * @param int $usuoid (ID do usuário que está realizando a exclusão)
     * @return boolean
     */
    public function propostaPagamentoDelete($prpoid, $usuoid){
    	return $this->prpDAO->pagamentoDelete($prpoid, $usuoid);
    }
    
    /**
     * Retorna os dados da proposta_servico
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 10/10/2013
     * @param int $prosoid
     * @return array / false
     */
    public function propostaServicoGetDados($prosoid){    
    	if(is_int($prosoid)){
    		return $this->prpDAO->servicoGetDados($prosoid);
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
    public function propostaServicoDelete($prosoid, $usuoid){    	 
    	if(is_int($prosoid) && is_int($usuoid)){
    		return $this->prpDAO->servicoDelete($prosoid, $usuoid);
    	} else{
    		return false;
    	}
    }
    
    /**
     * Inclui um acessório na proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 10/10/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (usuário)
     * @param mixed $prospritoid (item ao qual o acessório é adicionado, caso valor 't' adiciona em todos os itens da proposta)
     * @param array $propostaAcessorioArray (array com dados)
     */
    public function propostaAcessorioInsert($prpoid, $usuoid, $prospritoid, $propostaAcessorioArray){
    	if(is_int($prpoid) && is_int($usuoid) && $prospritoid != '' && is_array($propostaAcessorioArray)){
    		$propostaAcessorioArray = $this->prpDAO->applyCast($propostaAcessorioArray, $this->prpServicoIntFieldList, $this->prpServicoFloatFieldList, $this->prpServicoFkList);
    		
    		//Se == t , incluir para todos os itens.
    		if($prospritoid == 't'){
    			$propostaItemList = array();
    			$propostaItemList = $this->propostaItemGetList($prpoid);
    			$result = array();
    			
    			if(is_array($propostaItemList)){
	    			foreach ($propostaItemList as $row){
	    				$propostaAcessorioArray['prospritoid'] = $row['pritoid'];
	    				$prosoid = $this->propostaServicoInsert($prpoid, $usuoid, $propostaAcessorioArray);
	    				
	    				if(is_int($prosoid)){
	    					$result[] = $prosoid;
	    				} else{
	    					$result = false;
	    					break;
	    				}	    					    				
	    			}
	    			
	    			return $result;
    			} else{
    				return false;
    			}
    		} else{
    			$propostaAcessorioArray['prospritoid'] = Mascara::inteiro($prospritoid);
    			return $this->propostaServicoInsert($prpoid, $usuoid, $propostaAcessorioArray);
    		}
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
    public function propostaObrigacaoFinanceiraGetDados($prosobroid){
    	$prosobroid = Mascara::inteiro($prosobroid);
    	
    	if($prosobroid > 0){
    		return $this->prpDAO->obrigacaoFinanceiraGetDados($prosobroid);
    	} else{
    		return false;
    	}
    }
    
    /**
     * Busca a lista de acessórios da proposta.
     *     OBS: busca todos os serviços onde prossituacao = M
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 11/10/2013
     * @param int $prpoid (ID da proposta)
     * @return array array com todos os dados de acessórios da proposta
     *     OBS: busca todos os serviços onde prossituacao != M e prossituacao != B
     */
    public function propostaAcessorioGetList($prpoid){    	 
    	if(is_int($prpoid)){
    		return $this->prpDAO->acessorioGetList($prpoid);
    	} else{
    		return false;
    	}
    }
    
    /**
     * Busca a lista de acessórios da de um ITEM de proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 16/10/2013
     * @param int $prpoid (ID da proposta)
     * @param int $pritoid (ID do ITEM)
     * @return array array com todos os dados de acessórios da proposta
     *     OBS: busca todos os serviços onde prossituacao != M e prossituacao != B
     */
    public function propostaAcessorioGetItemList($prpoid, $pritoid){
        if(is_int($prpoid) && is_int($pritoid)){
    		return $this->prpDAO->acessorioGetItemList($prpoid, $pritoid);
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
    	if(is_int($prpoid) && is_int($pritoid)){
    		return $this->prpDAO->acessorioOpcionalItemGetList($prpoid, $pritoid);
    	} else{
    		return false;
    	}
    }
    
    /**
     * Insere registro na proposta_comissao.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 11/10/2013
     * @param int $prpoid
     * @param int $usuoid
     * @param array $propostaComercialArray
     * @return $pcomoid / false
     */
    public function propostaComissaoInsert($prpoid, $usuoid, $propostaComercialArray){
    	if(is_int($prpoid) && is_int($usuoid) && is_array($propostaComercialArray)){
    		$campos  = '';
    		$valores = '';
    		$strSeparador = '';
    		$dadosComissao = array();
    		
    		//Definindo os dados para insercao
    		$dadosComissao['pcomrepoid'] = $propostaComercialArray['execcontas'];
    		$dadosComissao['pcomusuoid'] = $usuoid;
    		$dadosComissao['pcomprpoid'] = $prpoid;
    		$dadosComissao['pcomfunoid'] = $propostaComercialArray['telemkt'];
    		$dadosComissao['pcomresponsavel_venda_meta'] = 'f';    		
    		
    		//Aplicando CAST
    		$propostaComercialArray = $this->prpDAO->applyCast($dadosComissao, $this->prpComercialIntFieldList, $this->prpComercialFloatFieldList, $this->prpComercialFkList);
    		 
    		foreach ($propostaComercialArray as $key => $value){
    			$campos  .= $strSeparador . $key;
    			$valores .= $strSeparador . $value;
    			$strSeparador = ',';
    		}
    		
    		$resultSet = $this->prpDAO->comissaoInsert($campos, $valores);
    		
    		if(is_array($resultSet)){
    			return Mascara::inteiro($resultSet['pcomoid']);
    		} else{
    			return false;
    		}
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
    public function propostaComercialGetDados($prcoid){    	 
    	if(is_int($prcoid)){
    		return $this->prpDAO->comercialGetDados($prcoid);
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
    public function propostaGerenciadoraGetNumero($prpoid){
    	$prpoid = Mascara::inteiro($prpoid);
    	return $this->prpDAO->gerenciadoraGetNumero($prpoid);
    }    
    
    /**
     * Inclui uma gerenciadora a uma proposta.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sacar.com.br]
     * @version 14/10/2013
     * @param int $prpoid
     * @param int $prggeroid
     * @param int $prgsequencia
     * @return mixed $prgoid/false
     */
    public function propostaGerenciadoraInsert($prpoid, $prggeroid, $prgsequencia){
    	if(is_int($prpoid) && is_int($prggeroid) && is_int($prgsequencia)){
    		return $this->prpDAO->gerenciadoraInsert($prpoid, $prggeroid, $prgsequencia);
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
    public function propostaGerenciadoraDelete($prgoid) {    	 
    	if(is_int($prgoid)){
    		return $this->prpDAO->gerenciadoraDelete($prgoid);
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
     * @return mixed array
     */
    public function propostaGerenciadoraGetList($prpoid){	 
    	if(is_int($prpoid)){
    		return $this->prpDAO->gerenciadoraGetList($prpoid);
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
    public function propostaStatusFinanceiroGetDados($prppsfoid){
    	if(is_int($prppsfoid)){
    		return $this->prpDAO->statusFinanceiroGetDados($prppsfoid);
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
    public function propostaFinanceiroGetDados($prpoid){
    	if(is_int($prpoid)){
    		return $this->prpDAO->financeiroGetDados($prpoid);
    	} else{
    		return false;
    	}
    }
    
    /**
     * Inclui um registro de contato.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 15/10/2013
     * @param int $prpoid (ID da proposta)
     * @param array $propostaContatoArray (array com dados do contato)
     * @return mixed $prcoid/false
     */
    public function propostaContatoInsert($prpoid, $propostaContatoArray){
    	if(is_int($prpoid) && is_array($propostaContatoArray)){
    		$campos  = '';
    		$valores = '';
    		$strSeparador = '';
    		$propostaContatoArray['prcprpoid'] = $prpoid;    		
    		
    		//Aplicando CAST
    		$propostaContatoArray = $this->prpDAO->applyCast($propostaContatoArray, $this->prpContatoIntFieldList, $this->prpContatoFloatFieldList, $this->prpContatoFkList);
    		 
    		foreach ($propostaContatoArray as $key => $value){
    			$campos  .= $strSeparador . $key;
    			$valores .= $strSeparador . $value;
    			$strSeparador = ',';
    		}
    		  		
    		$resultSet = $this->prpDAO->contatoInsert($campos, $valores);
    		
    		if(is_array($resultSet)){
    			return Mascara::inteiro($resultSet['prcoid']);
    		} else{
    			return false;
    		}
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
    public function propostaContatoDelete($prpoid, $prcoid){    	 
    	if(is_int($prpoid) && is_int($prcoid)){
    		return $this->prpDAO->contatoDelete($prpoid, $prcoid);
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
    public function propostaContatoGetList($prpoid, $prctipo){    	 
    	if(is_int($prpoid) && is_string($prctipo)){
    		if($prctipo != 'A' && $prctipo != 'E' && $prctipo != 'I'){
    			return false;
    		} else{
    			return $this->prpDAO->contatoGetList($prpoid, $prctipo);
    		}    		
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
    public function propostaStatusUpdate($prpoid, $prpstatus){  	 
    	if(is_int($prpoid) && is_string($prpstatus)){
            if($prpstatus != 'P' && $prpstatus != 'R' && $prpstatus != 'C' && $prpstatus != 'E' && $prpstatus != 'L' && $prpstatus != 'T'){
    			return false;
    		} else{
    			//Atualizando o status
    			$resultSet = $this->prpDAO->statusUpdate($prpoid, $prpstatus);
    			
    			if(is_array($resultSet)){
    				return $resultSet['prpstatus'];
    			} else{
    				return false;
    			}
    		}    		
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
    public function propostaStatusGet($prpoid){   
    	if(is_int($prpoid)){
    		$resultSet = $this->prpDAO->statusGet($prpoid);
    			
    		if(is_array($resultSet)){
    			return $resultSet['prpstatus'];
    		} else{
    			return false;
    		}
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
     * @return mixed $prosoid/false
     */
    public function propostaOpcionalInsert($prpoid, $usuoid, $prospritoid, $propostaOpcionalArray) {
    	if(is_int($prpoid) && is_int($usuoid) > 0 && $prospritoid != '' && is_array($propostaOpcionalArray)){
    		$campos  = '';
    		$valores = '';
    		$strSeparador = '';
    		
    		//Aplicando CAST
    		$propostaOpcionalArray = $this->prpDAO->applyCast($propostaOpcionalArray, $this->prpServicoIntFieldList, $this->prpServicoFloatFieldList, $this->prpServicoFkList);
    		
    		foreach ($propostaOpcionalArray as $key => $value){
    			$campos  .= $strSeparador . $key;
    			$valores .= $strSeparador . $value;
    			$strSeparador = ',';
    		}
    		
    		//Se == t , incluir para todos os itens.
    		if($prospritoid == 't'){
    			$propostaItemList = array();
    			$propostaItemList = $this->propostaItemGetList($prpoid);
    			$result = array();
    			 
    			if(is_array($propostaItemList)){
    				foreach ($propostaItemList as $row){
    					$prospritoid = $row['pritoid'];
    					$resultSet = $this->prpDAO->propostaOpcionalInsert($campos, $valores, $prpoid, $usuoid, $prospritoid);
    					    					
    					if(is_array($resultSet)){
    						$result[] = Mascara::inteiro($resultSet['prosoid']);
    					} else{
    						$result = false;
    						break;
    					}
    				}
    				
    				return $result;
    			} else{
    				return false;
    			}
    		} else{
    			$prospritoid = Mascara::inteiro($prospritoid);
    			$resultSet = $this->prpDAO->propostaOpcionalInsert($campos, $valores, $prpoid, $usuoid, $prospritoid);
    			
    			if(is_array($resultSet)){
    				return Mascara::inteiro($resultSet['prosoid']);
    			} else{
    				return false;
    			}
    		}
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
    	if(is_int($prpoid)){
    		return $this->prpDAO->propostaItemGetList($prpoid);
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
    	if(is_int($prpoid) && is_int($prosoid) && is_int($usuoid)){
    		return $this->prpDAO->propostaOpcionalDelete($prpoid, $prosoid, $usuoid);
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
    	if(is_int($prpoid)){
    		return $this->prpDAO->propostaOpcionalGetList($prpoid);
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
    	if(is_int($prpoid)){
    		return $this->prpDAO->propostaPagamentoGet($prpoid);
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
     * @return boolean
     */
    public function propostaValidaClasse($prpeqcoid){
        $resultSet = $this->prpDAO->propostaValidaClasse($prpeqcoid);
        
        if($resultSet === false){
            return false;
        } else{
            $eqcinativo = $resultSet['eqcinativo'];
            
            if(empty($eqcinativo)){
                return true;
            } else{
                return false;
            }
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
        $resultSet = $this->prpDAO->propostaReferenciaExternaUpdate($prpoid, $prpnumero_externo);
        
        if($resultSet === false){
            return false;
        } else{
            return $resultSet['prpoid'];
        }
    }
    
    // MÉTODOS REFERENTES A CONTRATO
    
    /**
     * Cria um contrato e retorna o número sequencial do mesmo.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 19/09/2013
     * @return $connumero/null
     */
    public function contratoGetConnumero(){
        return $this->cttDAO->getConnumero();
    }
    
    /**
     * Atualiza dados de uma proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 18/11/2013
     * @param int $connumero (ID do contrato)
     * @param int $usuoid (ID da proposta)
     * @param array $contratoArray array contendo dados para gerar contrato contrato
     * @param string $fase (fase de transferência de dados)
     * @return mixed $connumero/false
     */
    public function contratoTransfereDados($connumero=0, $usuoid=0, $contratoArray, $fase='01') {        
        $contratoArraySQL = array();
        $strSeparador = '';
        $contratoSQLString = '';
        
        // Monta array de dados para update
        if($fase == '01'){
            
            // OBS: para usar o método getSQLUpdateByArray 
            // Segmentação do contrato
            $contratoArraySQL['consegmentacao_contrato'] = "''"; // *** PENDENTE
            // Modalidade
            $contratoArraySQL['conmodalidade'] = "'" . $this->contratoModalidadeGet($contratoArray['prptipo_proposta']) . "'";
            // Data de solicitação
            if(empty($contratoArray['prpdt_solicitacao'])){
                $contratoArraySQL['condt_solicitacao'] = 'NULL';
            }else{
                $contratoArraySQL['condt_solicitacao'] = "'" . $contratoArray['prpdt_solicitacao'] . "'";
            }
            // Garantia
            if(empty($contratoArray['prpgarantia'])){
                $contratoArraySQL['congarantia'] = 'NULL';
            }else{
                $contratoArraySQL['congarantia'] = $contratoArray['prpgarantia'];
            }
            // corretor
            if(empty($contratoArray['prpcorroid'])){
                $contratoArraySQL['concorroid'] = 'NULL';
            }else{
                $contratoArraySQL['concorroid'] = $contratoArray['prpcorroid'];
            }
            $contratoArraySQL['condt_geracao_contrato'] = 'NOW()';
            $contratoArraySQL['condt_cadastro'] = 'NOW()';
            // *** PENDENTE
            $contratoArraySQL['confamilia_produto'] = "'" . $contratoArray['prpfamilia_produto'] . "'";
            $contratoArraySQL['conusuoid'] = $usuoid;
            $contratoArraySQL['conveioid'] = $contratoArray['pritobjeto'];
            $contratoArraySQL['conclioid'] = $contratoArray['prpclioid'];
            $contratoArraySQL['conno_tipo'] = $contratoArray['prptpcoid'];
            // classe equipamento/contrato
            if(empty($contratoArray['prpeqcoid'])){
                $contratoArraySQL['coneqcoid'] = 'NULL';
            }else{
                $contratoArraySQL['coneqcoid'] = $contratoArray['prpeqcoid'];
            }
            $contratoArraySQL['consolicitante'] = "'" . $contratoArray['prpgerente_neg'] . "'";
            
            $contratoSQLString = $this->cttDAO->getSQLUpdateByArray($contratoArraySQL);
            
        }else{// fase 02        	        	  
            $contratoArraySQL['confrota'] = "'".trim($contratoArray['prpfrota'])."'";            
            $contratoArraySQL['consem_custo'] = "'".trim($contratoArray['prpsemcusto'])."'";
            $contratoArraySQL['conresponsavel_venda_meta'] = "'".trim($contratoArray['prpresponsavel_venda_meta'])."'";
            
            if(!empty($contratoArray['repoid_representante'])){
            	$contratoArraySQL['conrepcomissaooid'] = $contratoArray['repoid_representante']; //PENDENTE
            } else{
            	$contratoArraySQL['conrepcomissaooid'] = 'NULL'; //PENDENTE
            }
            
            if(!empty($contratoArray['repoid_revenda'])){
            	$contratoArraySQL['conrepvendedoroid'] = $contratoArray['repoid_revenda']; //PENDENTE
            } else{
            	$contratoArraySQL['conrepvendedoroid'] = 'NULL'; //PENDENTE
            }
            
            if(!empty($contratoArray['prpvendedoroid'])){
            	$contratoArraySQL['convenitloid'] = $contratoArray['prpvendedoroid'];
            } else{
            	$contratoArraySQL['convenitloid'] = 'NULL';
            }
            
            if(!empty($contratoArray['prppanico'])){
            	$contratoArraySQL['conpanico_inst'] = "'".$contratoArray['prppanico']."'";
            } else{
            	$contratoArraySQL['conpanico_inst'] = 'NULL';
            }
            
            if(!empty($contratoArray['prpbloqueio'])){
            	$contratoArraySQL['conbloqueio_inst'] = "'".$contratoArray['prpbloqueio']."'";
            } else{
            	$contratoArraySQL['conbloqueio_inst'] = 'NULL';
            }
            
            if(!empty($contratoArray['prpescuta'])){
            	$contratoArraySQL['conescuta_inst'] = "'".$contratoArray['prpescuta']."'";
            } else{
            	$contratoArraySQL['conescuta_inst'] = 'NULL';
            }
            
            if(!empty($contratoArray['prpgrcoid'])){
            	$contratoArraySQL['congrcoid'] = $contratoArray['prpgrcoid'];
            } else{
            	$contratoArraySQL['congrcoid'] = 'NULL';
            }
            
            if(!empty($contratoArray['prpeqcoid'])){
            	$contratoArraySQL['coneqcoid'] = $contratoArray['prpeqcoid'];
            } else{
            	$contratoArraySQL['coneqcoid'] = 'NULL';
            }
            if(!empty($contratoArray['prptpcoid'])){
            	$contratoArraySQL['conno_tipo'] = $contratoArray['prptpcoid'];
            } else{
            	$contratoArraySQL['conno_tipo'] = 0;
            }
            
            if(!empty($contratoArray['prppednoid'])){
            	$contratoArraySQL['conpednoid'] = $contratoArray['prppednoid'];
            } else{
            	$contratoArraySQL['conpednoid'] = 'NULL';
            }
            
            if(!empty($contratoArray['prpindicadoroid'])){
            	$contratoArraySQL['conindicadoroid'] = $contratoArray['prpindicadoroid'];
            } else{
            	$contratoArraySQL['conindicadoroid'] = 'NULL';
            }
            
            if(!empty($contratoArray['prptermo_contingente'])){
            	$contratoArraySQL['conindicadoroid'] = $contratoArray['prptermo_contingente'];
            } else{
            	$contratoArraySQL['conindicadoroid'] = 'NULL';
            }
            
            if(!empty($contratoArray['prprepcentraloid'])){
            	$contratoArraySQL['conrepcentraloid'] = $contratoArray['prprepcentraloid'];
            } else{
            	$contratoArraySQL['conrepcentraloid'] = 'NULL';
            }
            
            if(!empty($contratoArray['prpdias_demonstracao'])){
            	$contratoArraySQL['condias_demonstracao'] = $contratoArray['prpdias_demonstracao'];
            } else{
            	$contratoArraySQL['condias_demonstracao'] = 'NULL';
            }
                        
            $contratoSQLString = $this->cttDAO->getSQLUpdateByArray($contratoArraySQL);
        }
        
        if(strlen($contratoSQLString) > 0){
            return $this->cttDAO->update($connumero, $contratoSQLString);
        } else{
            return false;
        }
    }
    

    /**
     * Retorna a modalidade de contrato com base no tipo de proposta
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 18/11/2013
     * @param int $prptipo_proposta (tipo da proposta)
     * @return array com os dados equipamento classe | false
     */
    public function contratoModalidadeGet($prptipo_proposta='L'){
        switch($prptipo_proposta){
            case 'L':
                return 'L';
            break;
            case 'R':
                return 'V';
            break;
            default:
                return '';
        }
    }
    

    /**
     * Busca serviços do item da proposta (Básico + Acessórios + Opcionais).
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 21/11/2013
     * @param int $prpoid (ID da proposta)
     * @param int $pritoid (ID do item)
     * @return mixed $servicosArray (array contendo os itens de serviço)/false
     */
    public function propostaServicosItemGetList($prpoid=0, $pritoid=0) {
        $prpoid = Mascara::inteiro($prpoid);
        $pritoid = Mascara::inteiro($pritoid);
        if(($prpoid > 0) && ($pritoid > 0)){
            return $this->prpDAO->servicosItemGetList($prpoid, $pritoid);
        } else{
            return false;
        }
    }
    

    /**
     * Verifica compatibilidade de acessório com modelo veículo
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 21/11/2013
     * @param int $tpcoid (ID tipo contrato)
     * @param int $mlooid (ID modelo veículo)
     * @param int $obroid (ID obrigação financeira)
     * @param int $prpno_ano (ano do veículo)
     * @return boolean true/false
     */
     public function verificaAcessorioModeloVeiculo($tpcoid=0, $mlooid=0, $obroid=0, $prpno_ano=0) {
        $tpcoid = Mascara::inteiro($tpcoid);
        $mlooid = Mascara::inteiro($mlooid);
        $obroid = Mascara::inteiro($obroid);
        $prpno_ano = Mascara::inteiro($prpno_ano);
        if(($mlooid > 0) && ($obroid > 0) && ($prpno_ano > 0)){
            return $this->prpDAO->verificaAcessorioModeloVeiculo($tpcoid, $mlooid, $obroid, $prpno_ano);
        } else{
            return false;
        }
    }

    /**
     * Insere um item na contrato serviço.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 21/11/2013
     * @param string $stringSqlInsert
     * @return mixed $consoid/false
     */
    public function contratoServicoInsert($stringSqlInsert=''){
        
        $resultSet = $this->cttDAO->servicoInsert($stringSqlInsert);
        if(is_array($resultSet)){
            return Mascara::inteiro($resultSet['consoid']);
        } else{
            return false;
        }
    }
    

    /**
     * Insere Obrig. Fin. de Semestralidade de Software (SASGC), caso não exista.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 25/11/2013
     * @param int $usuoid (usuário executor da ação)
     * @param array $vProposta (dados da proposta)
     * @param array $vServico (dado do serviço)
     * @return boolean true=OK/false=Erro
     */
    public function contratoVerifSemestSoftware($usuoid=0, $vProposta=array(), $vServico=array()){
        $sqlArray = array();
        $sqlStringInsert = '';
        
        $sqlArray['clioclioid'] = $vProposta['prpclioid'];
        $sqlArray['clioobroid'] = $vServico['obroidSASGC'];
        $sqlArray['cliovl_obrigacao'] = 0;
        $sqlArray['cliodt_inicio'] = 'NOW()';
        $sqlArray['cliodt_termino'] = 'NULL';
        $sqlArray['cliono_periodo_mes'] = 1;
        $sqlArray['cliodemonstracao'] = "'t'";
        $sqlArray['cliodemonst_aprov'] = $usuoid;
        $sqlArray['cliodemonst_validade'] = "(SELECT NOW() + INTERVAL '30 day')";
        $sqlArray['cliosoftware_principal'] = 2;
        $sqlArray['clioendoid_sasgerenciador'] = $vServico['prosendoid_gerenciador'];
        
        if(is_array($vProposta)){
            // verifica se já existe o serviço para o cliente
            if(!$this->cttDAO->clienteSemestralidadeExists($vProposta['prpclioid'], $vServico['prosendoid_gerenciador'], $vServico['obroidSASGC'])){
                // Verificar necessidade de chamar o método abaixo
                //$this->cttDAO->obrigacaoFinanceiraGetValor($vProposta['prpoid'], $vServico['obroidSASGC']);
                // Insere registro para o cliente
                 
                switch ($vProposta['prptipo_proposta']) {
                    case 'D':
                        $sqlArray['clioclioid'] = $vProposta['prpclioid'];
                        $sqlArray['clioobroid'] = $vServico['obroidSASGC'];
                        $sqlArray['cliovl_obrigacao'] = 0;
                        $sqlArray['cliodt_inicio'] = 'NOW()';
                        $sqlArray['cliodt_termino'] = 'NULL';
                        $sqlArray['cliono_periodo_mes'] = 1;
                        $sqlArray['cliodemonstracao'] = "'t'";
                        $sqlArray['cliodemonst_aprov'] = $usuoid;
                        $sqlArray['cliodemonst_validade'] = "(SELECT now() + INTERVAL '30 day')";
                        $sqlArray['cliosoftware_principal'] = 2;
                        $sqlArray['clioendoid_sasgerenciador'] = $vServico['prosendoid_gerenciador'];
                        // Monta string associativa 
                        $sqlStringInsert = $this->cttDAO->getSQLInsertByArray($sqlArray);
                     break;
                     default:
                         $sqlArray['clioclioid'] = $vProposta['prpclioid'];
                         $sqlArray['clioobroid'] = $vServico['obroidSASGC'];
                         $sqlArray['cliovl_obrigacao'] = 0;
                         $sqlArray['cliodt_inicio'] = 'NOW()';
                         $sqlArray['cliodt_termino'] = 'NULL';
                         $sqlArray['cliono_periodo_mes'] = 1;
                         $sqlArray['cliosoftware_principal'] = 2;
                         $sqlArray['clioendoid_sasgerenciador'] = $vServico['prosendoid_gerenciador'];
                         // Monta string associativa
                         $sqlStringInsert = $this->cttDAO->getSQLInsertByArray($sqlArray);
                }
                // Grava obrigação financeira do cliente->semestralidade
                $this->cttDAO->clienteObrigacaoFinanceiraInsert($sqlStringInsert);
            }
        } else{
            return false;
        }
    }


    /**
     * Transfere contatos da proposta para o cliente
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 21/11/2013
     * @param int $prpoid (ID proposta)
     * @param int $connumero (ID do contrato)
     * @param int $prpclioid (ID do cliente)
     * @param int $usuoid (ID do usuário)
     * @return boolean true/false
     */
    public function contratoTransfereContatosCliente($prpoid=0, $connumero=0, $prpclioid=0, $usuoid=0) {
        $prpoid = Mascara::inteiro($prpoid);
        $connumero = Mascara::inteiro($connumero);
        $prpclioid = Mascara::inteiro($prpclioid);
        $usuoid = Mascara::inteiro($usuoid);
        if(($prpoid > 0) && ($connumero > 0) && ($prpclioid > 0) && ($usuoid > 0)){
            return $this->cttDAO->contatosTransf($prpoid, $connumero, $prpclioid, $usuoid);
        } else{
            return false;
        }
    }

    /**
     * Transfere dados de Pagamento
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 21/11/2013
     * @param int $prpoid (ID proposta)
     * @param int $connumero (ID do contrato)
     * @param boolean $prpcorretor_recebe_comissao (corretor para comissão)
     * @param int $usuoid (ID do usuário)
     * @return boolean true/false
     */
    public function contratoTransferePagamento($prpoid=0, $connumero=0, $prpcorretor_recebe_comissao=false, $usuoid=0) {
        $prpoid = Mascara::inteiro($prpoid);
        $connumero = Mascara::inteiro($connumero);
        if(trim($prpcorretor_recebe_comissao) == ''){
            $prpcorretor_recebe_comissao = 'f';
        }
        $usuoid = Mascara::inteiro($usuoid);
        if(($prpoid > 0) && ($connumero > 0) && ($usuoid > 0)){
            return $this->cttDAO->pagamentoTransfer($prpoid, $connumero, $prpcorretor_recebe_comissao, $usuoid);
        } else{
            return false;
        }
    }

    /**
     * Transfere benefícios de opcionais.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/11/2013
     * @param int $connumero (ID contrato)
     * @param char $tipoBeneficio (tipo de beneficio 'A=Assistência','P=Pacote')
     * @return boolean true/false
     */
    public function contratoTransfereOpcionaisBeneficio($connumero=0, $tipoBeneficio='A') {
        $connumero = Mascara::inteiro($connumero);
        if($connumero > 0){
            return $this->cttDAO->opcionaisBeneficioTransfer($connumero, $tipoBeneficio);
        } else{
            return false;
        }
    }

    /**
     * Transfere dados de comissão.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/11/2013
     * @param int $prpoid (ID proposta)
     * @param int $connumero (ID contrato)
     * @return boolean true/false
     */
    public function contratoTransfereComissao($prpoid=0, $connumero=0) {
        $whereClause = '';
        $prpoid = Mascara::inteiro($prpoid);
        $connumero = Mascara::inteiro($connumero);
        if(($prpoid > 0) && ($connumero > 0)){
            $whereClause = "
                         pcomprpoid = $prpoid
                         AND pcomdt_exclusao IS NULL
                         AND pcomrtcoid IS NOT NULL
                         AND pcomusuoid IS NOT NULL
                       ";
            if($this->cttDAO->pgExists('proposta_comissao', $whereClause)){
                return $this->cttDAO->comissaoTransfer($prpoid, $connumero);
            }else{
                return true;
            }
        } else{
            return false;
        }
    }
    
    
    /**
     * Recebe número da proposta que gerou o contrato
	 * 
	 * @author Rafael Dias <rafael.dias@meta.com.br>
     * @param int $connumero
     * @return mixed $prpoid/false
     */
    public function contratoPropostaGet($connumero=0){
    	if ($connumero > 0){    		
    		$prpoid = $this->cttDAO->propostaGet($connumero);
    		if($prpoid > 0){
    		    return $prpoid;
    		}
    	}
    	return false;
    }

    /**
     * Transfere dados da Zona e Região comercial.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 02/12/2013
     * @param int $connumero (número do contrato)
     * @param array $vProposta (dados da proposta)
     * @return boolean true=OK/false=Erro
     */
    public function contratoTransfereZonaComercial($connumero=0, $vProposta=array()){
        if((is_array($vProposta)) && ($connumero > 0)){
            $vProposta['prpregcoid'] = Mascara::inteiro($vProposta['prpregcoid']);
            $vProposta['prprczoid'] = Mascara::inteiro($vProposta['prprczoid']);
            $vProposta['prpcampcoid'] = Mascara::inteiro($vProposta['prpcampcoid']);
            $vProposta['prpprazo_contrato'] = Mascara::inteiro($vProposta['prpprazo_contrato']);
            //  
            if(trim($vProposta['prpregcoid']) == 0){
                $vProposta['prpregcoid'] = 'NULL';
            }
            if(trim($vProposta['prprczoid']) == 0){
                $vProposta['prprczoid'] = 'NULL';
            }
            if(trim($vProposta['prpcampcoid']) == 0){
                $vProposta['prpcampcoid'] = 'NULL';
            }
            if(trim($vProposta['prpprazo_contrato']) == 0){
                $vProposta['prpprazo_contrato'] = 'NULL';
            }
            // Grava obrigação financeira do cliente->semestralidade
            return $this->cttDAO->zonaComercialTransfer($connumero, $vProposta);
        } else{
            return false;
        }
    }
    

    /**
     * Transfere dados de gerenciadoras
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 02/12/2013
     * @param int $connumero (número do contrato)
     * @param array $vGerenciadora (dados da gerenciadora)
     * @return boolean true=OK/false=Erro
     */
    public function contratoTransfereGerenciadora($connumero=0, $vGerenciadora=array()){
        $dataArray = array();
        $whereClause = '';
        $prgsequencia = Mascara::inteiro($vGerenciadora['prgsequencia']);
        if($prgsequencia > 0){
            $vGerenciadora['prggeroid'] = Mascara::setDefaultNull($vGerenciadora['prggeroid'], 'I');
            $vGerenciadora['prgperiodo_ind'] = trim($vGerenciadora['prgperiodo_ind']);
            if($vGerenciadora['prgperiodo_ind'] == ''){
                $vGerenciadora['prgperiodo_ind'] = "'f'";
            }else{
                $vGerenciadora['prgperiodo_ind'] = "'" . $vGerenciadora['prgperiodo_ind'] . "'";
            }
            $vGerenciadora['prgdata_limite'] = Mascara::setDefaultNull($vGerenciadora['prgdata_limite'], 'D');
            $vGerenciadora['prghora_limite'] = Mascara::setDefaultNull($vGerenciadora['prghora_limite'], 'D');
            
            $whereClause = "congconnumero = $connumero";
            
            $dataArray['conggeroid' . $prgsequencia] = $vGerenciadora['prggeroid'];
            $dataArray['congperiodo_ind' . $prgsequencia] = $vGerenciadora['prgperiodo_ind'];
            $dataArray['congdt_limite' . $prgsequencia] = $vGerenciadora['prgdata_limite'];
            $dataArray['conghr_limite' . $prgsequencia] = $vGerenciadora['prghora_limite'];
            
            return $this->cttDAO->pgUpdate('contrato_gerenciadora', $whereClause, 'congconnumero', $dataArray);
        }else{
            return true;
        }
    }
        
    /**
     * Transfere dados de gerenciadoras
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 03/12/2013
     * @param int $clioid (ID do cliente)
     * @param int $usuoid (ID do usuário)
     * @param int $prpdia_vcto_boleto (dia do vencimento da fatura/boleto)
     * @return boolean true=OK/false=Erro
     */
    public function contratoTransfereVencimentoFaturaCliente($clioid=0, $usuoid=0, $prpdia_vcto_boleto=1){
        $dataArray = array();
        $whereClause = '';
        $clioid = Mascara::inteiro($clioid);
        $usuoid = Mascara::inteiro($usuoid);
        $prpdia_vcto_boleto = Mascara::inteiro($prpdia_vcto_boleto);
        if(($clioid > 0) && ($usuoid > 0)){
            $whereClause = "clioid = $clioid";
            $dataArray['clidia_vcto'] = $prpdia_vcto_boleto;
            $dataArray['clidt_alteracao'] = 'NOW()';
            $dataArray['cliusuoid_alteracao'] = $usuoid;
            return $this->cttDAO->pgUpdate('clientes', $whereClause, 'clioid', $dataArray);
        }else{
            return true;
        }
    }
    
    /**
     * Retorna a lista de contratos gerados por uma proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 19/12/2013
     * @param int $prpoid (ID da proposta)
     * @return mixed array/false
     */
    public function contratoListGet($prpoid){
    	$resultSet = $this->cttDAO->contratoListGet($prpoid);
    	
    	if(is_array($resultSet)){
    		return $resultSet;
    	} else{
    		return false;
    	}
    }
    
}