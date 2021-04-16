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
 * @subpackage Classe Controladora de Contratos
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace module\Contrato;

use infra\ComumController,
	infra\Helper\Response,
    infra\Helper\Mascara,
    infra\Helper\Validacao,    
    module\Contrato\ContratoModel as Modelo,
    module\Veiculo\VeiculoService as Veiculo,
    module\Cliente\ClienteService as Cliente,
    module\OrdemServico\OrdemServicoService as OrdemServico;

class ContratoController extends ComumController{
    
    protected $model;
    private $response;
    // valores padrão para testes/controle de fluxo
    private $obroidSASGC; // Obrigação financeira FIXA do SASGC
    
    
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
        $this->obroidSASGC = 270;
    }
    
    // MÉTODOS RELACIONADOS A PROPOSTA
    
    /**
     * Insere/cria uma proposta nova
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 16/09/2013 
     * @param int $prptppoid (modalidade)
     * @param int $prptpcoid (tipo de contrato, tabela tipo_contrato)
     * @param int $prpusuoid (usuário que criou a proposta)
     * @return response $response ($response->dados: $prpoid/false)
    */
    public function propostaCria($prptppoid, $prptpcoid, $prpusuoid) {
    	try{
        	$prptppoid = Mascara::inteiro($prptppoid);
        	$prptpcoid = Mascara::inteiro($prptpcoid);
        	$prpusuoid = Mascara::inteiro($prpusuoid);
            if(($prptppoid > 0) && ($prpusuoid)){
            	$prpoid = $this->model->propostaInsert($prptppoid, $prptpcoid, $prpusuoid);
            	
            	if($prpoid !== false){
            		$this->response->setResult($prpoid, 'PRP001');
            	} else{
            		$this->response->setResult(false, 'PRP002');
            	}
            } else{
            	$this->response->setResult(false, 'INF005');
            }
        } catch (\Exception $e) {
            $this->response->setResult($e, 'EXCEPTION');
        }
        
        return $this->response;
     }
    
    /**
     * Insere registro no histórico
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 17/09/2013 
     * @param int $prphprpoid (ID da proposta)
     * @param int $prphusuoid (ID do usuário)
     * @param string $prphobs (Observação)
     * @return response $response ($response->dados = true/false)
     */
    public function propostaGravaHistorico($prphprpoid, $prphusuoid, $prphobs) {
        try{
            $prphobs = trim($prphobs);
            $prphprpoid = Mascara::inteiro($prphprpoid);
            $prphusuoid = Mascara::inteiro($prphusuoid);
            if($prphobs != ''){
            	$resultSet = $this->model->propostaHistoricoInsert($prphprpoid, $prphusuoid, $prphobs);
            	
            	if($resultSet){
            		$this->response->setResult(true, 'PRP003');
            	} else{
            		$this->response->setResult(false, 'PRP004');
            	}            	
            } else{
                 $this->response->setResult(false, 'INF005');
            }
        } catch (\Exception $e) {
            $this->response->setResult($e, 'EXCEPTION');
        }
        
        return $this->response;
    }
    
    /**
     * Atualiza dados de uma proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 18/09/2013 
     * @param int $prpoid (ID da proposta)
     * @param array $propostaArray (array associativo tabela proposta)
     * @return response ($response->dados = $prpoid/false)
     */
    public function propostaAtualiza($prpoid=0, $propostaArray=array()) {
        $prpoid = Mascara::inteiro($prpoid);
        
        if(!empty($propostaArray) && $prpoid > 0){
            $prpoid = $this->model->propostaUpdate($prpoid, $propostaArray);
            
            if(is_int($prpoid)){
            	$this->response->setResult($prpoid, 'PRP005');
            } else{
            	$this->response->setResult(false, 'PRP006');
            }
        } else{
            $this->response->setResult(false, 'INF005');
        }

        return $this->response;
    }
    
    /**
     * Apenas verifica se uma prpoid existe.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 18/09/2013
     * @param int $prpoid (ID da proposta)
     * @return response ($response->dados = true/false)
     */
    public function propostaExiste($prpoid=0) {
        $prpoid = Mascara::inteiro($prpoid);
        if($prpoid > 0){
            $prpoid = $this->model->propostaExists($prpoid);
            
            if($prpoid){
            	$this->response->setResult(true, 'PRP007');
            } else{
            	$this->response->setResult(false, 'PRP008');
            }
        } else{
        	$this->response->setResult(false, 'INF005');
        }
     	return $this->response;
    }
    
    /**
     * Inclui um item de proposta.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 19/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (ID do usuario que incluiu o item)
     * @param array $propostaItemArray (array associativo tipo chave -> valor, dados da tabela proposta_item)
     * @return response ($response->dados = $pritoid/false)
     */
    public function propostaItemInclui($prpoid=0, $usuoid=0, $propostaItemArray=array()){        
        if(is_array($propostaItemArray) && !empty($propostaItemArray)){
            $obrigatorio = array('pritobjeto', 'prittipo','pritprptermo');
            $exists      = false;            
            $objContrato = $this->contratoGetConnumero();
            
            if($objContrato->dados != false){
                $propostaItemArray['pritprptermo'] = $objContrato->dados;
            } else{
                return $objContrato;
            }
            
            //Verificando se os campos obrigatorios existem
            $exists = $this->verificaCampos($obrigatorio, $propostaItemArray);
            
            if($exists){
            	$propostaItemArray['pritprpoid'] = Mascara::inteiro($prpoid);
            	$propostaItemArray['pritusuoid_cadastro'] = Mascara::inteiro($usuoid);
            	
                $pritoid = $this->model->propostaItemInsert($propostaItemArray);
                if(is_int($pritoid)){
                	$this->response->setResult($pritoid, 'PRP009');
                } else{
                	$this->response->setResult(false, 'PRP010');
                }
            } else{
                $this->response->setResult(false, 'INF003');
            }            
        } else{
            $this->response->setResult(false, 'INF005');
        }
        
        return $this->response;
    }
    
    /**
     * Atualiza dados de um item de proposta
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 20/09/2013 
     * @param int $pritoid (ID de um ITEM da proposta)
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (ID do usuario que incluiu o item)
     * @param array $propostaItemArray (array associativo tipo chave -> valor, dados da tabela proposta_item)
     * @return response ($response->dados = $pritoid/false)
     */
    public function propostaItemAtualiza($pritoid, $prpoid, $usuoid, $propostaItemArray=array()){
        if(is_array($propostaItemArray) && !empty($propostaItemArray)){
        	$propostaItemArray['pritoid'] = Mascara::inteiro($pritoid);
        	$propostaItemArray['pritprpoid'] = Mascara::inteiro($prpoid);
        	$propostaItemArray['pritusuoid'] = Mascara::inteiro($usuoid);
        	
            if($propostaItemArray['pritoid'] > 0){
                $pritoid = $this->model->propostaItemUpdate($propostaItemArray);
                
                if(is_int($pritoid)){
                	$this->response->setResult(false, 'PRP011');
                } else{
                	$this->response->setResult(false, 'PRP012');
                }
            } else{
            	$this->response->setResult(false, 'INF001');
            }
        } else{
        	$this->response->setResult(false, 'INF005');
        }
        
        return $this->response;
    }

    /**
     * Exclui um item da proposta
     *
     * @author Fabio Andrei Lorentz <fabio.lorentz@ewave.com.br>
     * @version 20/05/2014
     * @param  integer $pritoid (ID do ITEM da proposta)
     * @param  integer $usuoid  (ID do usuario que excluiu o item)
     * @return Response $response:
     *     mixed $response->dados ($pritoid=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
     */
    public function propostaItemExclui($pritoid, $usuoid) {
        $pritoid = Mascara::inteiro($pritoid);
        $usuoid = Mascara::inteiro($usuoid);
            
        if($pritoid > 0 && $usuoid > 0) {
            $pritoid = $this->model->propostaItemDelete($pritoid, $usuoid);
                
            if(is_int($pritoid)){
                $this->response->setResult(false, 'PRP042');
            } else{
                $this->response->setResult(false, 'PRP043');
            }
        } else {
            $this->response->setResult(false, 'INF005');
        }
        
        return $this->response;
    }

    /**
     * Exclui todos itens da proposta
     *
     * @author Fabio Andrei Lorentz <fabio.lorentz@ewave.com.br>
     * @version 20/05/2014
     * @param  integer $prpoid  (ID da proposta)
     * @param  integer $usuoid  (ID do usuario que excluiu todos os itens)
     * @return Response $response:
     *     mixed $response->dados ($pritoid=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
     */
    public function propostaItensExclui($prpoid, $usuoid) {
        $prpoid = Mascara::inteiro($prpoid);
        $usuoid = Mascara::inteiro($usuoid);
            
        if($prpoid > 0 && $usuoid > 0) {
            $response = $this->model->propostaItensDelete($prpoid, $usuoid);
                
            if($response){
                $this->response->setResult(false, 'PRP044');
            } else{
                $this->response->setResult(false, 'PRP045');
            }
        } else {
            $this->response->setResult(false, 'INF005');
        }
        
        return $this->response;
    }
    
    /**
     * Liga cliente a proposta
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 19/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $clioid (ID do cliente)
     * @return response ($response->dados = $prpclioid/false)
     */
    public function propostaSetaCliente($prpoid=0, $clioid=0) {
        $prpoid = Mascara::inteiro($prpoid);
        $clioid = Mascara::inteiro($clioid);
                
        if($prpoid > 0 && $clioid > 0){
            $objCliente = Cliente::clienteGetDados($clioid, 'ID');
            $dadosCliente = $objCliente->dados;
            
            if(!empty($dadosCliente)){
            	$objProposta = $this->propostaExiste($prpoid);
            	
                if($objProposta->dados){
                    $prpclioid = $this->model->propostaClienteSet($dadosCliente, $prpoid);
                    
                    if(is_int($prpclioid)){
                    	$this->response->setResult($prpclioid, 'PRP013');
                    } else{
                    	$this->response->setResult(false, 'PRP014');
                    }
                } else{
                	$this->response->setResult(false, 'INF001');
                }
            } else{
            	$this->response->setResult(false, 'INF005');
            }
        } else{
            $this->response->setResult(false, 'INF005');
        }
        
        return $this->response;
    }
    
    /**
     * Busca dados de uma proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 20/09/2013
     * @param $prpoid (ID da proposta)
     * @return response ($response->dados = array/false)
     */
    public function propostaBuscaDados($prpoid=0) {
        $prpoid = Mascara::inteiro($prpoid);
        
        if($prpoid > 0){
            $dadosProposta = $this->model->propostaGetDados($prpoid);
            
            if(is_array($dadosProposta)){
            	$this->response->setResult($dadosProposta, '0');
            } else{
            	$this->response->setResult(false, 'INF002');
            }
        } else{
            $this->response->setResult(false, 'INF005');
        }
        
        return $this->response;
    }
    
    /**
     * Vincula o produto(classe de contrato) a proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (usuário)
     * @param array $propostaProdutoArray (array com dados)
     *     OBS-> campos obrigatórios do $propostaProdutoArray[]:
     *     prpeqcoid -> Classe de contrato/equipamento
     * @return response ($response->dados = $prpoid/false)
     */
    public function propostaSetaProduto($prpoid=0, $usuoid=0, $propostaProdutoArray=array()) {
    	$prpoid = Mascara::inteiro($prpoid);
    	$usuoid = Mascara::inteiro($usuoid);
    	$dadosEquipamentoClasse = array();
    	$dadosObrigacaoFinanceiraItens = array();
    	$arrayPropostaServico = array();
    	
    	if($prpoid > 0 && $usuoid > 0 && !empty($propostaProdutoArray)){
    		$obrigatorio = array(0 => 'prpeqcoid');
    		$exists = false;
    		
    		//Verificando se os campos obrigatorios existem
    		$exists = $this->verificaCampos($obrigatorio, $propostaProdutoArray);
    		
    		if($exists){
    			$prpeqcoid = Mascara::inteiro($propostaProdutoArray['prpeqcoid']);

    			if($prpeqcoid > 0){
    				$dadosEquipamentoClasse = $this->model->getEquipamentoClasseDados($prpeqcoid);
    				
    				if(is_array($dadosEquipamentoClasse)){
    					$eqcobroid = Mascara::inteiro($dadosEquipamentoClasse['eqcobroid']);
    					if($eqcobroid > 0){
    						$dadosObrigacaoFinanceiraItens = $this->model->getObrigacaoFinanceiraItens($eqcobroid);
    						
    						if(is_array($dadosObrigacaoFinanceiraItens)){
    							$arrayPropostaServico['prossituacao'] = 'B';							
    							$arrayPropostaServico['prosvalor_tabela'] = 0;
    							$arrayPropostaServico['prosvalor'] 	  = 0;
    							$arrayPropostaServico['prosdesconto'] = 0;
    							$arrayPropostaServico['prosqtde'] 	  = 1;
    							
    							// insere proposta_servico
    							foreach ($dadosObrigacaoFinanceiraItens as $row){
    								$arrayPropostaServico['prosobroid'] = $row['ofiservico'];
    								$this->model->propostaServicoInsert($prpoid, $usuoid, $arrayPropostaServico);
    							}
    							
    							// Atualizar proposta
      							return $this->propostaAtualiza($prpoid, $propostaProdutoArray);
    							
    						} else{
    							$this->response->setResult(false, 'INF006');
    						}
    					} else{
    						$this->response->setResult(false, 'INF006');
    					}
    				} else{
    					$this->response->setResult(false, 'INF006');
    				}
    			} else{
    				$this->response->setResult(false, 'INF001');
    			}
    			
    		} else{
    			$this->response->setResult(false, 'INF003');
    		}
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
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
     *     prpforcoid;
     *     prpdia_vcto;
     *     cpvoid;
     *     obroid_servico;
     *     vl_servico;
     *     prppercentual_desconto_locacao;
     *     vl_monitoramento;
     *     prpprazo_contrato;
     *     prpagmulta_rescissoria;
     *     ppagtvltx_instalacao (taxa de instalação);
     * @return response ($response->dados = $prpoid/false)
     */
    public function propostaSetaPagamento($prpoid=0, $usuoid=0, $propostaPagamentoArray=array()) {
    	$prpoid = Mascara::inteiro($prpoid);
    	$usuoid = Mascara::inteiro($usuoid);
    	$resultSet = '';
    	$dadosProposta = array();
    	$dadosPropostaPagamento = array();    	
    	
    	if($prpoid > 0 && $usuoid > 0 && !empty($propostaPagamentoArray)){
    		$exists = false;    		    		
    		$obrigatorio = array('prpforcoid', 'prpdia_vcto', 'cpvoid', 'obroid_servico', 'vl_servico',
    		        'prppercentual_desconto_locacao', 'vl_monitoramento', 'prpprazo_contrato',
    				'prpagmulta_rescissoria');
    		//Verificando se TODOS os campos obrigatorios existem
    		$exists = $this->verificaCampos($obrigatorio, $propostaPagamentoArray);
    		
    		//Verifica se todos os campos obrigatorios foram enviados
    		
    		if($exists){
    			//Criando campos para inserção
    			$dadosPropostaPagamento['ppagstatus']  			   = 'P';
    			$dadosPropostaPagamento['ppagforcoid'] 			   = $propostaPagamentoArray['prpforcoid'];
    			$dadosPropostaPagamento['ppagmonitoramento'] 	   = $propostaPagamentoArray['vl_monitoramento'];    			
    			$dadosPropostaPagamento['ppagmulta_rescissoria']   = $propostaPagamentoArray['prpagmulta_rescissoria'];
    			$dadosPropostaPagamento['ppagobroid_servico'] 	   = $propostaPagamentoArray['obroid_servico'];
    			$dadosPropostaPagamento['ppagvl_servico']		   = $propostaPagamentoArray['vl_servico'];
    			$dadosPropostaPagamento['ppagvl_desconto_servico'] = $propostaPagamentoArray['prppercentual_desconto_locacao'];
    			$dadosPropostaPagamento['ppagcpvoid']			   = $propostaPagamentoArray['cpvoid'];
    			$dadosPropostaPagamento['ppagadesao']              = $propostaPagamentoArray['adesao'];
    			$dadosPropostaPagamento['ppagadesao_parcela']      = $propostaPagamentoArray['adesao_parcela'];
    			$dadosPropostaPagamento['ppagforcoid_adesao']      = $propostaPagamentoArray['forcoid_adesao'];
    			
    			$dadosPropostaPagamento['ppagtvltx_instalacao']      = $propostaPagamentoArray['ppagtvltx_instalacao'];
    			
    			
     			
    			//Exclusão dos registros
    			$this->model->propostaPagamentoDelete($prpoid, $usuoid);
     			//Inserindo uma proposta pagamento   			    			
    			$ppagoid = $this->model->propostaPagamentoSet($prpoid, $usuoid, $dadosPropostaPagamento);  			
    			 
    			if(is_int($ppagoid)){   	    			    
    				//Criando campos para atualizar
					$dadosProposta['prpdia_vcto_boleto'] 			 = $propostaPagamentoArray['prpdia_vcto'];
					$dadosProposta['prpprazo_contrato'] 			 = $propostaPagamentoArray['prpprazo_contrato'];
					$dadosProposta['prppercentual_desconto_locacao'] = $propostaPagamentoArray['prppercentual_desconto_locacao'];
					
					//Atualizando a proposta
					$prpoid = $this->model->propostaUpdate($prpoid, $dadosProposta);
					
					if(is_int($prpoid)){
						$objProposta = $this->propostaBuscaDados($prpoid);
						$vProposta 	 = $objProposta->dados;
						
						$dadosCliente = array();
						$dadosCliente['clidia_vcto'] = $propostaPagamentoArray['prpdia_vcto'];
						
						if($vProposta !== false){
							$clioid = Mascara::inteiro($vProposta['prpclioid']);
						} else{
							$clioid = 0;
						}
						
						if($clioid > 0){
							Cliente::clienteAtualiza($clioid, $usuoid, $dadosCliente);
							
							if($clioid > 0){
								$this->response->setResult($prpoid, 'PRP015');
							} else{
								$this->response->setResult(false, 'PRP016');
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
    		} else{
     		    $this->response->setResult(false, 'INF003');
    		}
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Retorna os dados da proposta_servico
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 10/10/2013
     * @param int $prosoid
     * @return response ($response->dados = array / false)
     */
    public function propostaServicoGetDados($prosoid){
    	$prosoid = Mascara::inteiro($prosoid);
    	 
    	if($prosoid > 0){
    		$resultSet = $this->model->propostaServicoGetDados($prosoid);
    		
    		if(is_array($resultSet)){
    			$this->response->setResult($resultSet, '0');
    		} else{
    			$this->response->setResult(false, 'INF002');
    		}
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Excluí logicamente um registro da proposta_servico.
     * Somente serviços onde a Situação é diferente de "B" podem ser excluídos.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 10/10/2013
     * @param int $prosoid
     * @param int $usuoid
     * @return response ($response->dados = boolean)
     */
    public function propostaServicoDelete($prosoid=0, $usuoid=0){
    	$prosoid = Mascara::inteiro($prosoid);
    	$usuoid  = Mascara::inteiro($usuoid);
    	
    	if($prosoid > 0 && $usuoid > 0){
    		$objPropostaServico = $this->propostaServicoGetDados($prosoid);
    		$vPropostaServico   = $objPropostaServico->dados;
    		
    		if(is_array($vPropostaServico)){
    			$prosituacao = strtoupper(trim($vPropostaServico['prosituacao']));
    			
    			if($prosituacao != 'B' && $prosituacao != ''){
    				$resultSet = $this->model->propostaServicoDelete($prosoid, $usuoid);
    				
    				if($resultSet){
    					$this->response->setResult(true, 'PRP017');
    				} else{
    					$this->response->setResult(false, 'PRP018');
    				}
    			} else{
    				$this->response->setResult(false, 'PRP019');	
    			}    			
    		} else{
    			$this->response->setResult(false, 'INF006');
    		}    		
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /** 
     * Inclui um acessório na proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 10/10/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (usuário)
     * @param mixed $prospritoid (item ao qual o acessório é adicionado, caso valor 't' adiciona em todos os itens da proposta)
     * @param response ($response->dados = array $propostaAcessorioArray/false)
     */
    public function propostaAcessorioInclui($prpoid=0, $usuoid=0, $prospritoid='t', $propostaAcessorioArray=array()){
    	$prpoid = Mascara::inteiro($prpoid);
    	$usuoid = Mascara::inteiro($usuoid);
    	$prospritoid = strtolower(trim($prospritoid));
    	$obrigatorio = array();
    	$exists 	 = false;
    	$obrigacaoFinanceira = array();
    	$desconto = 0;
    	
    	if($prpoid > 0 && $usuoid > 0 && $prospritoid != '' && !empty($propostaAcessorioArray)){
    		$obrigatorio = array('prosobroid', 'prossituacao', 'prosvalor', 'prosqtde');
    		
    		//Verificando se os campos obrigatorios existem
    		$exists = $this->verificaCampos($obrigatorio, $propostaAcessorioArray);
    		
    		if($exists){
    			$obrigacaoFinanceira = $this->model->propostaObrigacaoFinanceiraGetDados($propostaAcessorioArray['prosobroid']);
    			$obrvl_obrigacao = 0;
    			$prosdesconto = 0;
    			$desconto = 0;
    			
    			if(is_array($obrigacaoFinanceira)){
    				$obrvl_obrigacao = (float) $obrigacaoFinanceira['obrvl_obrigacao'];
    				$prosdesconto	 = (float) $propostaAcessorioArray['prosvalor'];
    				$desconto 		 = $obrvl_obrigacao - $prosdesconto;
    			}
    			
    			if($desconto < 0){
    				$desconto = 0;
    			}
    			
    			$propostaAcessorioArray['prosdesconto'] = $desconto;
    			$propostaAcessorioArray['prosvalor_tabela'] = $obrvl_obrigacao;
    			 
    			$resultSet = $this->model->propostaAcessorioInsert($prpoid, $usuoid, $prospritoid, $propostaAcessorioArray);
    			
    			if($resultSet !== false){
    				$this->response->setResult($resultSet, 'PRP021');
    			} else{
    				$this->response->setResult(false, 'PRP020');
    			}    			
    		} else{
    			$this->response->setResult(false, 'INF003');
    		}    		
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Exclui/remove um acessório da proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 10/10/2013
     * @param int $prosoid (ID da proposta_servico)
     * @param int $usuoid (usuário)
     * @return response ($response->dados = true/false)
     */
    public function propostaAcessorioExclui($prosoid=0, $usuoid=0) {
    	$prosoid = Mascara::inteiro($prosoid);
    	$usuoid  = Mascara::inteiro($usuoid);
    	
    	if($prosoid > 0 && $usuoid > 0){
    		$resultSet = $this->model->propostaServicoDelete($prosoid, $usuoid);
    		
    		if($resultSet){
    			$this->response->setResult(true, 'PRP022');
    		} else{
    			$this->response->setResult(false, 'PRP023');
    		}
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Busca a lista de acessórios da proposta.
     *     OBS: busca todos os serviços onde prossituacao = M
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 11/10/2013
     * @param int $prpoid (ID da proposta)
     * @return response ($response->dados = array/false)
     *     OBS: busca todos os serviços onde prossituacao != M e prossituacao != B
     */
    public function propostaAcessorioLista($prpoid=0) {
    	$prpoid = Mascara::inteiro($prpoid);
    	
    	if($prpoid > 0){
    		$resultSet = $this->model->propostaAcessorioGetList($prpoid);
    		
    		if(is_array($resultSet)){
    			$this->response->setResult($resultSet, '0');
    		} else{
    			$this->response->setResult(false, 'INF002');
    		}
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Busca a lista de acessórios da de um ITEM de proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 16/10/2013
     * @param int $prpoid (ID da proposta)
     * @param int $pritoid (ID do ITEM)
     * @return response ($response->dados = array/false)
     *     OBS: busca todos os serviços onde prossituacao != M e prossituacao != B
     */
    public function propostaItemAcessorioLista($prpoid=0, $pritoid=0){
    	$prpoid  = Mascara::inteiro($prpoid);
    	$pritoid = Mascara::inteiro($pritoid);
    	
    	if($prpoid > 0 && $pritoid > 0){
    		$resultSet = $this->model->propostaAcessorioGetItemList($prpoid, $pritoid);
    		
    		if(is_array($resultSet)){
    			$this->response->setResult($resultSet, '0');
    		} else{
    			$this->response->setResult(false, 'INF002');
    		}
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Busca lista de opcionais do ITEM da proposta.
     *     OBS: busca todos os serviços mensais do item onde prossituacao = M
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 16/10/2013
     * @param int $prpoid (ID da proposta)
     * @param int $pritoid (ID do ITEM)
     * @return response ($response->dados = array / false)
     */
    public function propostaItemOpcionalLista($prpoid=0, $pritoid=0){
    	$prpoid  = Mascara::inteiro($prpoid);
    	$pritoid = Mascara::inteiro($pritoid);
    	
    	if($prpoid > 0 && $pritoid > 0){
    		$resultSet = $this->model->acessorioOpcionalItemGetList($prpoid, $pritoid);
    		
    		if(is_array($resultSet)){
    			$this->response->setResult($resultSet, '0');
    		} else{
    			$this->response->setResult(false, 'INF002');
    		}
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Grava/atualiza dados comerciais da proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (usuário)
     * @param array $propostaComercialArray (array com dados comercial)
     *     OBS-> campos obrigatórios do $propostaComercialArray[]:
     *         N/A
     *     OBS-> campos opcionais do $propostaComercialArray[]:
     *     	   int execcontas (ID do funcionario/representante)
     *         int prpregcoid (ID região comercial)
     *         int prprczoid (ID zona comercial)
     *         int telemkt (ID televendas)
     *         int prpcorroid -> (ID corretor)
     * @return response ($response->dados = $prpoid/false)
     */
    public function propostaSetaComercial($prpoid=0, $usuoid=0, $propostaComercialArray=array()) {
    	$prpoid = Mascara::inteiro($prpoid);
    	$usuoid = Mascara::inteiro($usuoid);
    	$dadosProposta = array();
    	
    	if($prpoid > 0 && $usuoid > 0){
    		//$resultSet = $this->model->propostaComissaoInsert($prpoid, $usuoid, $propostaComercialArray);
    		// verificar gravação de COMISSÃO	
    		//if($resultSet > 0){
    		if(true){
    			$dadosProposta['prpregcoid'] = $propostaComercialArray['prpregcoid'];
    			$dadosProposta['prprczoid']  = $propostaComercialArray['prprczoid'];
    			$dadosProposta['prpcorroid'] = $propostaComercialArray['prpcorroid'];
   				return $this->propostaAtualiza($prpoid, $dadosProposta);
   			} else{
   				$this->response->setResult(false, 'PRP024');
   			}
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Busca dados comerciais da proposta.
     *     OBS: retorna uma matriz completa com dados do comercial
     *
     * @author Bruno B. Affonso [bruno.bonfim@sacar.com.br]
     * @version 14/10/2013
     * @param int $prpoid (ID da proposta)
     * @return response ($response->dados = array / false)
     */
    public function propostaComercialBuscaDados($prpoid=0){
    	$prpoid = Mascara::inteiro($prpoid);
    	
    	if($prpoid > 0){
    		$objProposta = $this->propostaBuscaDados($prpoid);
    		$vProposta   = $objProposta->dados;
    		
    		if(is_array($vProposta)){
    			$prcoid = Mascara::inteiro($vProposta['prpprcoid']);
    			
    			if($prcoid > 0){
    				$resultSet = $this->model->propostaComercialGetDados($prcoid);
    				
    				if(is_array($resultSet)){
    					$this->response->setResult($resultSet, '0');
    				} else{
    					$this->response->setResult(false, 'INF002');
    				}
    			} else{
    				$this->response->setResult(false, 'INF005');
    			}
    		} else{
    			$this->response->setResult(false, 'INF002');
    		}    		
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Inclui uma gerenciadora.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 14/10/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (usuário)
     * @param int $prggeroid (ID da GERENCIADORA)
     * @param int $prgsequencia (Sequência da gerenciadora)
     * @return response ($response->dados = $prgoid/false)
     */
    public function propostaGerenciadoraInclui($prpoid=0, $usuoid=0, $prggeroid=0, $prgsequencia=0){
    	$prpoid = Mascara::inteiro($prpoid);
    	$usuoid = Mascara::inteiro($usuoid);
    	$prggeroid = Mascara::inteiro($prggeroid);
    	$prgsequencia = Mascara::inteiro($prgsequencia);
    	$nGerenciadoras = 0;
    	
    	if($prpoid > 0 && $usuoid > 0 && $prggeroid > 0 && $prgsequencia > 0){
    		//Numero de gerenciadoras vinculadas a proposta
    		$nGerenciadoras = $this->model->propostaGerenciadoraGetNumero($prpoid);
    		
			if($nGerenciadoras < 3){
				$resultSet = $this->model->propostaGerenciadoraInsert($prpoid, $prggeroid, $prgsequencia);
				
				if(is_array($resultSet)){
					$prgoid = $resultSet['prgoid'];
					$this->response->setResult($prgoid, 'PRP026');
				} else{
					$this->response->setResult(false, 'PRP027');
				}
			} else{
				$this->response->setResult(false, 'PRP025');
			}   	
    	} else{
    		$this->response->setResult(false, 'INF006');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Exclui/remove uma gerenciadora.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 14/10/2013
     * @param int $prpoid (ID da proposta)
     * @param int $prgoid (ID da gerenciadora)
     * @return response ($response->dados = true/false)
     */
    public function propostaGerenciadoraExclui($prpoid=0, $prgoid=0) {
    	$prpoid = Mascara::inteiro($prpoid);
    	$prgoid = Mascara::inteiro($prgoid);
    	
    	if($prpoid > 0 && $prgoid > 0){
    		$resultSet = $this->model->propostaGerenciadoraDelete($prpoid, $prgoid);
    		if($resultSet){
    			$this->response->setResult(true, 'PRP028');
    		} else{
    			$this->response->setResult(false, 'PRP029');
    		}    		
    	} else{
    		$this->response->setResult(false, 'INF006');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Retorna array com lista de dados das gerenciadoras vinculadas na proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 14/10/2013
     * @param int $prpoid (ID da proposta)
     * @return response ($response->dados = array/false)
     */
    public function propostaGerenciadoraLista($prpoid=0) {
    	$prpoid = Mascara::inteiro($prpoid);
    	
    	if($prpoid > 0){
    		$resultSet = $this->model->propostaGerenciadoraGetList($prpoid);
    		
    		if(is_array($resultSet)){
    			$this->response->setResult($resultSet, '0');
    		} else{
    			$this->response->setResult(false, 'INF002');
    		}
    	} else{
    		$this->response->setResult(false, 'INF001');
    	}
    	
    	return $this->response;
    }  
    
    /**
     * Grava status e demais informações do financeiro na proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 14/10/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (ID do usuário que inseriu a informação)
     * @param int $prppsfoid (ID do Status Financeiro conforme tabela proposta_status_financeiro)
     * @param string $prpobservacao_financeiro (Observação referente a condição/financeira)
     * @param string $prpresultado_aciap (Strint contendo resultado da consulta)
     * @return response ($response->dados = array/false)
     */
    public function propostaSetaFinanceiro($prpoid=0, $usuoid=0, $prppsfoid=0, $prpobservacao_financeiro='', $prpresultado_aciap=''){
    	$prpoid = Mascara::inteiro($prpoid);
    	$usuoid = Mascara::inteiro($usuoid);
    	$prppsfoid = Mascara::inteiro($prppsfoid);
    	$prpobservacao_financeiro = trim($prpobservacao_financeiro);
    	$prpresultado_aciap = trim($prpresultado_aciap);
    	$propostaArray = array();
    	
    	if($prpoid > 0 && $usuoid > 0 && $prppsfoid > 0){
    		$objStatusFinanceiro = $this->propostaStatusFinanceiroGetDados($prppsfoid);
    		$resultSet = $objStatusFinanceiro->dados;
    		
    		if(is_array($resultSet)){
    			$psfindica_aprovacao = $resultSet['psfindica_aprovacao'];
    			
    			if($psfindica_aprovacao == 't'){
    				$propostaArray['prppsfoid'] = $prppsfoid;
    				$propostaArray['prpusuoid_aprovacao_fin']  = $usuoid;
    				$propostaArray['prpobservacao_financeiro'] = $prpobservacao_financeiro;
    				$propostaArray['prpresultado_aciap']  = $prpresultado_aciap;
    				$propostaArray['prpdt_aprovacao_fin'] = 'NOW()';    				
    			} else{
    				$propostaArray['prppsfoid'] = $prppsfoid;
    				$propostaArray['prpobservacao_financeiro'] = $prpobservacao_financeiro;
    				$propostaArray['prpresultado_aciap'] = $prpresultado_aciap;
    			}
    			
    			return $this->propostaAtualiza($prpoid, $propostaArray);
    		} else{
    			$this->response->setResult(false, 'INF006');
    		}    		
    	} else{
    		$this->response->setResult(false, 'INF001');
    	}
    	
    	return $this->response;
    }
    
	/**
     * Retorna array com lista de dados da proposta_status_financeiro.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 15/10/2013
     * @param int $prppsfoid (ID da proposta_status_financeiro)
     * @return response ($response->dados = array/false)
     */
    public function propostaStatusFinanceiroGetDados($prppsfoid){
    	$prppsfoid = Mascara::inteiro($prppsfoid);
    	
    	if($prppsfoid > 0){
    		$resultSet = $this->model->propostaStatusFinanceiroGetDados($prppsfoid);
    		
    		if(is_array($resultSet)){
    			$this->response->setResult($resultSet, '0');
    		} else{
    			$this->response->setResult(false, 'INF002');
    		}
    	} else{
    		$this->response->setResult(false, 'INF001');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Busca dados financeiros da proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 15/10/2013
     * @param int $prpoid (ID da proposta)
     * @return response ($response->dados = array/false)
     */
    public function propostaFinanceiroBuscaDados($prpoid=0){
    	$prpoid = Mascara::inteiro($prpoid);
    	
    	if($prpoid > 0){
    		return $this->model->propostaFinanceiroGetDados($prpoid);
    	} else{
    		$this->response->setResult(false, 'INF001');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Inclui um registro de contato.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param array $propostaContatoArray (array com dados do contato)
     *     OBS-> campos obrigatórios do $propostaContatoArray[]:
     *         char prctipo (tipo do contato: (A)utorizados , (E)mergencia , (I)nstalacao)
     *         string prcnome (nome do contato)
     *         string prccpf (CPF do contato)         
     *         string prcfone_cel (Telefone Celular)
     *
     *     OBS-> campos opcionais do $propostaContatoArray[]:
     *     	   string prcrg (RG do contato)
     *         string prcfone_res (fone residencial)
     *         string prcfone_com (fone comercial)
     *         string prcobs (observação)
     * @return response ($response->dados = $prcoid/false)
     */
    public function propostaContatoInclui($prpoid=0, $propostaContatoArray=array()){
    	$prpoid = Mascara::inteiro($prpoid);
    	$obrigatorio = array();
    	    	
    	if($prpoid > 0 && !empty($propostaContatoArray)){
    		$obrigatorio = array('prctipo', 'prcnome', 'prccpf', 'prcfone_cel');
    		$exists      = false;
    		    		
    		//Verificando se os campos obrigatorios existem
    		$exists = $this->verificaCampos($obrigatorio, $propostaContatoArray);
    		
    		if($exists){
    			$prcoid = $this->model->propostaContatoInsert($prpoid, $propostaContatoArray);
    			
    			if($prcoid !== false){
    				$this->response->setResult($prcoid, 'PRP030');
    			} else{
    				$this->response->setResult(false, 'PRP031');
    			}
    		} else{
    			$this->response->setResult(false, 'INF003');
    		}
    		
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Exclui/remove um registro de contato.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 15/10/2013
     * @param int $prpoid (ID da proposta)
     * @param int $prcoid (ID do contato)
     * @return response ($response->dados = true/false)
     */
    public function propostaContatoExclui($prpoid=0, $prcoid=0){
    	$prpoid = Mascara::inteiro($prpoid);
    	$prcoid = Mascara::inteiro($prcoid);
    	
    	if($prpoid > 0 && $prcoid > 0){
    		$resultSet = $this->model->propostaContatoDelete($prpoid, $prcoid);
    		
    		if($resultSet){
    			$this->response->setResult(true, 'PRP032');
    		} else{
    			$this->response->setResult(false, 'PRP033');
    		}
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Retorna array com lista de contatos de um tipo .
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 15/10/2013
     * @param int $prpoid (ID da proposta)
     * @param string $prctipo (tipo do contato 'A'/'E'/'I')
     * @return response ($response->dados = array/false)
     */
    public function propostaContatoLista($prpoid=0, $prctipo='A'){
    	$prpoid  = Mascara::inteiro($prpoid);
    	$prctipo = strtoupper(trim($prctipo));
    	
    	if($prpoid > 0 && $prctipo != ''){
    		$resultSet = $this->model->propostaContatoGetList($prpoid, $prctipo);
    		
    		if($resultSet){
    			$this->response->setResult($resultSet, '0');
    		} else{
    			$this->response->setResult(false, 'INF002');
    		}
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Grava/seta o status da proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 16/10/2013
     * @param int $prpoid (ID da proposta)
     * @param char $prpstatus (status da proposta: P=Pendente,R=Aguardando Retorno,C=Concluído,E=Cancelado,L=Aguardando Análise Financeira,T=Aguardando Análise Técnica)
     * @return response ($response->dados = $prpstatus/false)
     */
    public function propostaSetaStatus($prpoid=0, $prpstatus='P'){
    	$prpoid = Mascara::inteiro($prpoid);
    	$prpstatus = strtoupper(trim($prpstatus));
    	
    	if($prpoid > 0 && $prpstatus != ''){
    		$resultSet = $this->model->propostaStatusUpdate($prpoid, $prpstatus);
    		
    		if($resultSet){
    			$this->response->setResult($resultSet, 'PRP034');
    		} else{
    			$this->response->setResult(false, 'PRP035');
    		}
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Retorna status da proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 16/10/2013
     * @param int $prpoid (ID da proposta)
     * @return response ($response->dados = $prpstatus / false)
     */
    public function propostaBuscaStatus($prpoid=0) {
    	$prpoid = Mascara::inteiro($prpoid);
    	    	
    	if($prpoid > 0){
    		$resultSet = $this->model->propostaStatusGet($prpoid);
    		
    		if($resultSet){
    			$this->response->setResult($resultSet, '0');
    		} else{
    			$this->response->setResult(false, 'INF002');
    		}
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
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
     * @return response ($response->dados = $prosoid/false)
     */
    public function propostaOpcionalInclui($prpoid=0, $usuoid=0, $prospritoid='t', $propostaOpcionalArray=array()) {
    	$prpoid = Mascara::inteiro($prpoid);
    	$usuoid = Mascara::inteiro($usuoid);
    	$prospritoid = strtolower(trim($prospritoid));
    	
    	if($prpoid > 0 && $usuoid > 0 && $prospritoid != '' && !empty($propostaOpcionalArray)){
    		$obrigatorio = array('prosobroid', 'prosvalor', 'prosvalor_agregado_monitoramento');
    		$exists      = false;
    		
    		//Verificando se os campos obrigatorios existem
    		$exists = $this->verificaCampos($obrigatorio, $propostaOpcionalArray);
    		
    		if($exists){
    			$obrigacaoFinanceira = $this->model->propostaObrigacaoFinanceiraGetDados($propostaOpcionalArray['prosobroid']);
    			$obrvl_obrigacao = 0;
    			$prosvalor = (float) $propostaOpcionalArray['prosvalor'];
    			
    			if(is_array($obrigacaoFinanceira)){    				
    				$obrvl_obrigacao = (float) $obrigacaoFinanceira['obrvl_obrigacao']; 				   				
    			}
    			
    			if($propostaOpcionalArray['prosvalor_agregado_monitoramento'] === true){
    				$prosdesconto = $obrvl_obrigacao;
    				$prosvalor = 0;
    				$propostaOpcionalArray['prosvalor_agregado_monitoramento'] = 't';
    			} else{
    				$prosdesconto = $obrvl_obrigacao - $prosvalor;
    				$propostaOpcionalArray['prosvalor_agregado_monitoramento'] = 'f';
    			}
    			
    			if($prosdesconto < 0){
    				$prosdesconto = 0;
    			}
    			    			
    			$propostaOpcionalArray['prossituacao'] = 'M';
    			$propostaOpcionalArray['prosqtde'] = 1;
    			$propostaOpcionalArray['prosvalor_tabela'] = $obrvl_obrigacao;
    			$propostaOpcionalArray['prosdesconto'] = $prosdesconto;
    			$propostaOpcionalArray['prosinstalar'] = 'f';
    			$propostaOpcionalArray['prosvalor'] = $prosvalor;
    			
    			$resultSet = $this->model->propostaOpcionalInsert($prpoid, $usuoid, $prospritoid, $propostaOpcionalArray);
    			
    			if($resultSet !== false){
    				$this->response->setResult($resultSet, 'PRP037');
    			} else{
    				$this->response->setResult(false, 'PRP036');
    			}
    		} else{
    			$this->response->setResult(false, 'INF003');
    		}
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Retorna todos os itens de uma proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 06/11/2013
     * @param int $prpoid
     * @return response ($response->dados = array / false)
     */
    public function propostaItemLista($prpoid=0){
    	$prpoid = Mascara::inteiro($prpoid);
    	  	
    	if($prpoid > 0){
    		$resultSet = $this->model->propostaItemGetList($prpoid);
    		
    		if(is_array($resultSet)){
    			$this->response->setResult($resultSet, '0');
    		} else{
    			$this->response->setResult(false, 'INF002');
    		}
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Exclui/remove um opcional da proposta/item.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 07/11/2013
     * @param int $prpoid (ID da proposta)
     * @param int $prosoid (ID da proposta_servico)
     * @param int $usuoid (usuário)
     * @return response ($response->dados = true/false)
     */
    public function propostaOpcionalExclui($prpoid=0, $prosoid=0, $usuoid=0) {
    	$prpoid  = Mascara::inteiro($prpoid);
    	$prosoid = Mascara::inteiro($prosoid);
    	$usuoid  = Mascara::inteiro($usuoid);
    	
    	if($prpoid > 0 && $prosoid > 0 && $usuoid > 0){
    		$resultSet = $this->model->propostaOpcionalDelete($prpoid, $prosoid, $usuoid);
    		
    		if($resultSet){
    			$this->response->setResult(true, 'PRP038');
    		} else{
    			$this->response->setResult(false, 'PRP039');
    		}
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Busca lista de opcionais da proposta.
     *     OBS: busca todos os serviços da proposta onde prossituacao = M
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 07/11/2013
     * @param int $prpoid (ID da proposta)
     * @return response ($response->dados = array/false)
     */
    public function propostaOpcionalLista($prpoid=0) {
    	$prpoid = Mascara::inteiro($prpoid);
    	
    	if($prpoid > 0){
    		$resultSet = $this->model->propostaOpcionalGetList($prpoid);
    		
    		if(is_array($resultSet)){
    			$this->response->setResult($resultSet, '0');
    		} else{
    			$this->response->setResult(false, 'INF002');
    		}
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }

    /**
     * Verifica e retorna a lista (array) de pendências de uma proposta.
     *     OBS: cada código da lista representa uma pendência
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 14/11/2013
     * @param int $prpoid (ID da proposta)
     * @return mixed response ($response->dados = true=sem pendências/array de pendências)
     */
    public function propostaVerificaPendencias($prpoid=0) {
        $prpoid = Mascara::inteiro($prpoid);
         
        if($prpoid > 0){
        	$objProposta = $this->propostaBuscaDados($prpoid);
        	
        	if(is_array($objProposta->dados)){
        		$clioid 	= Mascara::inteiro($vProposta['prpclioid']);        		
        		$eqcoid 	= Mascara::inteiro($vProposta['prpeqcoid']);
        		$prpforcoid = Mascara::inteiro($vProposta['prpforcoid']);
        		        		
        		$prpdia_vcto_boleto = Mascara::inteiro($vProposta['prpdia_vcto_boleto']);        		
        		$vPropostaItens 	= $this->model->propostaItemGetList($prpoid);
        		        		
        		$vPropostaPagamento = $this->model->propostaPagamentoGet($prpoid);
        		$prpcpvoid  		= Mascara::inteiro($vPropostaPagamento['ppagcpvoid']);
        		$ppagmonitoramento  = trim($vPropostaPagamento['ppagmonitoramento']);
        		$ppagvl_servico     = trim($vPropostaPagamento['ppagvl_servico']);
        		
        		if($clioid == 0){
        			$this->response->setResult(false, 'PEP001');
        		} elseif($vPropostaItens == false){
        			$this->response->setResult(false, 'PEP002');
        		} elseif($eqcoid == 0){
        			$this->response->setResult(false, 'PEP003');
        		} elseif($prpforcoid == 0 || $prpdia_vcto_boleto == 0 || $prpcpvoid == 0){
        			$this->response->setResult(false, 'PEP004');
        		} elseif($ppagmonitoramento == '' || $ppagvl_servico == ''){
        			$this->response->setResult(false, 'PEP005');
        		} elseif(false){
        			$this->response->setResult(false, 'PEP006');
        		} else{
        			$this->response->setResult(true, '0', 'Proposta sem pendências.');
        		}
        	} else{
        		$this->response->setResult($objProposta->dados, $objProposta->codigo);
        	}
        } else{
            $this->response->setResult(false, 'INF005');
        }
        
        return $this->response;
    }
    
    /**
     * Verifica se a classe/produto informado não é nulo e se faz
     * parte dos produtos ativos da Sascar.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 09/01/2014
     * @param int $prpeqcoid (ID Equipamento Classe)
     * @return response ($response->dados = true/false)
     */
    public function propostaValidaClasse($prpeqcoid=0){
        $prpeqcoid = Mascara::inteiro($prpeqcoid);
        
        if($prpeqcoid > 0){
            if($this->model->propostaValidaClasse($prpeqcoid)){
                $this->response->setResult(true, '0');
            } else{
                $this->response->setResult(false, 'PRP040');
            }
        } else{
            $this->response->setResult(false, 'INF005');
        }
        
        return $this->response;
    }

    /**
     * Vincula o número externo que vem do SalesForce a proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 10/01/2014
     * @param int $prpoid (ID Proposta)
     * @param int $prpnumero_externo
     * @return response ($response->dados = $prpoid/false)
     */
    public function propostaSetaReferenciaExterna($prpoid=0, $prpnumero_externo=0){
        $prpoid = Mascara::inteiro($prpoid);
        $prpnumero_externo = Mascara::inteiro($prpnumero_externo);

        if($prpoid > 0 && $prpnumero_externo > 0){
            $resultSet = $this->model->propostaReferenciaExternaUpdate($prpoid, $prpnumero_externo);

            if($resultSet != false){
                $prpoid = $resultSet;
                $this->response->setResult($prpoid, '0', 'Número externo vinculado a proposta.');
            } else{
                $this->response->setResult(false, 'PRP041');
            }
        } else{
            $this->response->setResult(false, 'INF005');
        }

        return $this->response;
    }
    


    /**
     * Atualiza corretor da proposta
     * @author Vinicius Senna <vsenna@brq.com>
     * @version 2/4/2014
     * @param int $prphprpoid (ID da proposta)
     * @param int $prpcorroid (ID do corretor)
     * @return response $response ($response->dados: $prpoid/false)
     */ 
    public function propostaGravaCorretorIndicador($prphprpoid,$prpcorroid) {
        
        $prphprpoid = Mascara::inteiro($prphprpoid);
        $prpcorroid = Mascara::inteiro($prpcorroid);

        try{
            if($prphprpoid > 0 && $prpcorroid > 0){

                $propostaArray = array ('prpcorroid' => $prpcorroid);

                $prpoid = $this->model->propostaUpdate($prphprpoid, $propostaArray);
                
                if(is_int($prpoid)){
                    $this->response->setResult(true, '');
                } else{
                    $this->response->setResult(false, '');
                }
            }
        } catch (\Exception $e) {
            $this->response->setResult($e, 'EXCEPTION');
        }

        return $this->response;
    }
    
    // MÉTODOS REFERENTES A CONTRATO
        
    /**
     * Cria um contrato e retorna o número do mesmo.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 19/09/2013
     * @return response ($response->dados = $connumero|false)
     */
    private function contratoGetConnumero(){
        $connumero = $this->model->contratoGetConnumero();
        
        if($connumero != null){
            if($connumero > 0){
                $this->response->setResult($connumero, 'CTT001');
            } else{
                $this->response->setResult(false, 'CTT002');
            }
        } else{
            $this->response->setResult(false, 'INF005');
        }
        
        return $this->response;
    }
    

    /**
     * Gera contrato(s) a partir da proposta.
     *     OBS: geração baseada nos itens da proposta
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 14/11/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (ID do usuário executou a geração dos contratos)
     * @param boolean $controlaTransacao (true/false determina se transfere o controle de transações para o core)
     * @param boolean $geraOS (true/false determina de gera ou não Ordem de Serviço)
     * @return Response (mixed $response->dados, string $response->codigo, string $response->mensagem)
     */
    public function contratoGera($prpoid=0, $usuoid=0, $controlaTransacao=false, $geraOS=true) {
        $prpoid = Mascara::inteiro($prpoid);
        $usuoid = Mascara::inteiro($usuoid);
        $servicoCompativel = false;
        $teveErro = false;
        $vProposta = array();
        $vPropostaItens = array();
        $vPropostaItem = array();
        $vEquipamentoClasse = array();
        $vServicos = array();
        $objVeiculo = '';
        $vVeiculo = array();
        $sqlString = '';
        $sqlArray = array();
        $vGerenciadoras = array();
        $vGerenciadora = array();
        $vConnumero = array();
        if(($prpoid > 0) && ($usuoid > 0)){
            $vProposta = $this->model->propostaGetDados($prpoid);
            if($controlaTransacao){
                // Start Transaction
                $this->model->cttDAO->startTransaction();
            }
            // processo de geração do(s) contrato(s)
            // busca dados básicos da proposta
            if(is_array($vProposta)){
                // verificar presença de TAXA DE INSTALACAO *** PENDENTE
                
                // Busca configuração da Classe/Equipamento (prpeqcoid)
                $vEquipamentoClasse = $this->model->cttDAO->getDataRecordByID('equipamento_classe','eqcoid',$vProposta['prpeqcoid']);
                // Loop proposta_item (INICIO)
                // Transfere etapa 01
                $vPropostaItens = $this->model->propostaItemGetList($prpoid);
                if(is_array($vPropostaItens)){
                    foreach ($vPropostaItens as $vPropostaItem){
                        $vContrato = array();
                        // Busca dados do veículo/ITEM
                        $objVeiculo = Veiculo::veiculoGetDados($vPropostaItem['pritobjeto'], 'ID');
                        $vVeiculo = $objVeiculo->dados;
                        
                        
                        // Migra dados proposta->contrato fase 01
                        $connumero = Mascara::inteiro($vPropostaItem['pritprptermo']);
                        $pritoid = Mascara::inteiro($vPropostaItem['pritoid']);
                        
                        $vContrato = array_merge($vProposta, $vPropostaItem);
                                                
                        if($this->model->contratoTransfereDados($connumero, $usuoid, $vContrato, '01')){
                            // Fluxo de transfêrencia de dados porposta->contrato
                            // verifica se gera OS e se é 245
                            if($vEquipamentoClasse['eqcgera_os'] == 't'){
                                // processa geração de O.S.
                                if($geraOS){
                                	$ordemServicoArray = array();
                                	$ordemServicoArray['ordclioid'] = $vContrato['prpclioid'];                             	
                                	$ordemServicoArray['ordeqcoid'] = $vContrato['prpeqcoid'];
                                	$ordemServicoArray['ordveioid'] = $vVeiculo['veioid'];
                                	$ordemServicoArray['ordstatus'] = 1;

                                	OrdemServico::ordemServicoContratoGera($connumero, $usuoid, $ordemServicoArray);
                                }
                            }
                            // Migra SERVIÇOS(básico+opcionais+acessorios)
                            $vServicos = $this->model->propostaServicosItemGetList($prpoid, $pritoid);
                            
                            if(is_array($vServicos)){
                                
                                // deleção LÓGICA de TODOS os serviços previamente inseridos para o contrato (LIMPA TABELA)
                                $sqlArray = array();
                                $sqlArray['consusuoid_excl'] = $usuoid;
                                $sqlArray['consiexclusao'] = 'NOW()';
                                $this->model->cttDAO->pgUpdate('contrato_servico', "consconoid=$connumero AND consiexclusao IS NULL", 'consoid', $sqlArray);
                                
                                foreach ($vServicos as $vServico){
                                    $sqlArray = array();
                                    // INÍCIO VERIFICA SE ACESSÓRIO É COMPATÍVEL COM MODELO/ANO DO VEÍCULO/TIPO CONTRATO
                                    // utiliza função DB: verifica_acessorio_modelo_veiculo(...)
                                    $servicoCompativel = $this->model->verificaAcessorioModeloVeiculo($vContrato['prptpcoid'], $vVeiculo['veimlooid'], $vServico['prosobroid'], $vVeiculo['veino_ano']);
                                    // Teste adicional -> sobrepõe regra de compatibilidade
                                    // Os casos abaixo sempre são compativeis (SERVIÇOS OPCIONAIS)
                                    if($vServico['obrtipo_obrigacao'] == 'A' || $vServico['obrtipo_obrigacao'] == 'V' || $vServico['obrtipo_obrigacao'] == 'P' ){
                                        $servicoCompativel = true;
                                    }
                                    
                                    // Montando array de INSERT
                                    $sqlArray['consconoid'] = $connumero;
                                    $sqlArray['conssituacao'] = "'" . $vServico['prossituacao'] . "'";
                                    $sqlArray['consusuoid'] = $usuoid;
                                    $sqlArray['consobroid'] = $vServico['prosobroid'];
                                    $sqlArray['consqtde'] = 1;
                                    $sqlArray['consvalor_tabela'] = $vServico['prosvalor_tabela'];
                                    $sqlArray['consvalor'] = $vServico['prosvalor'];
                                    $sqlArray['consdesconto'] = $vServico['prosdesconto'];
                                    $sqlArray['consinstalar'] = "'" . $vServico['prosinstalar'] . "'";
                                    $sqlArray['consmotivo_naoinstalar'] = "'" . $vServico['prosmotivo_naoinstalar'] . "'";
                                    $sqlArray['consalioid'] = $vServico['prosalioid'];
                                    
                                    if ($sqlArray['consalioid'] == '') {
                                        $sqlArray['consalioid'] = "NULL";
                                    }
                                    
                                    // Se for software grava como instalado
                                    if($vServico['obrtipo_obrigacao'] == 'S') { 
                                        $sqlArray['consinstalacao'] = 'NOW()';
                                    }
                                    // COMPATIBILIDADE
                                    if(!$servicoCompativel){
                                        $sqlArray['consiexclusao'] = 'NOW()';
                                    }
                                    // gera INSERT do contrato_servico
                                    if(is_array($this->model->contratoServicoInsert($this->model->cttDAO->getSQLInsertByArray($sqlArray)))){
                                        // Erro ao Migrar dados da proposta fase 01
                                        $this->response->setResult(false, 'CTT007');
                                        $teveErro = true;
                                        break(2);
                                        
                                    }
                                    // ajusta gerenciador
                                    if ($vServico['prosendoid_gerenciador'] == '') {
                                        $vServico['prosendoid_gerenciador'] = 'NULL';
                                    }
                                    // Verifica obrigação tipo SASGC (semestralidade de software)
                                    if ($vServico['prosobroid'] == $this->obroidSASGC) {
                                        $vServico['obroidSASGC'] = $this->obroidSASGC;
                                        $this->model->contratoVerifSemestSoftware($usuoid, $vProposta, $vServico);
                                    }
                                }
                                
                                
                            }
                           
                            // Migra Contatos
                            // chama função DB: proposta_exporta_contatos(...)
                            if(!$this->model->contratoTransfereContatosCliente($prpoid, $connumero, $vProposta['prpclioid'], $usuoid)){
                                // Erro ao transferir contatos
                                $this->response->setResult(false, 'CTT008');
                                $teveErro = true;
                                break;
                            }
                            // Migra Pagamento
                            if(!$this->model->contratoTransferePagamento($prpoid, $connumero, $vProposta['prpcorretor_recebe_comissao'], $usuoid)){
                                // Erro ao transferir contatos
                                $this->response->setResult(false, 'CTT009');
                                $teveErro = true;
                                break;
                            }
                            // TRANSFERIR OPCIONAIS
                            //(1) Assistência
                            if(!$this->model->contratoTransfereOpcionaisBeneficio($connumero, 'A')){
                                $this->response->setResult(false, 'CTT010');
                                $teveErro = true;
                                break;
                            }       
                            //(2) Pacote
                            if(!$this->model->contratoTransfereOpcionaisBeneficio($connumero, 'P')){
                                $this->response->setResult(false, 'CTT011');
                                $teveErro = true;
                                break;
                            }
                            // MIGRAR COMISSÃO
                            if(!$this->model->contratoTransfereComissao($prpoid, $connumero)){
                                $this->response->setResult(false, 'CTT012');
                                $teveErro = true;
                                break;
                            }
                            // ZONA E REGIÃO COMERCIAL
                            if(!$this->model->contratoTransfereZonaComercial($connumero, $vProposta)){
                                $this->response->setResult(false, 'CTT013');
                                $teveErro = true;
                                break;
                            }
                            // GERENCIADORAS
                            $vGerenciadoras = $this->model->propostaGerenciadoraGetList($prpoid);
                            if(is_array($vGerenciadoras)){
                                foreach ($vGerenciadoras as $vGerenciadora){
                                    if(!$this->model->contratoTransfereGerenciadora($connumero, $vGerenciadora)){
                                        $this->response->setResult(false, 'CTT014');
                                        $teveErro = true;
                                        break(2);
                                    }
                                }
                            }
                             // Migra/atualiza data de vencimento do boleto/fatura do cliente
                            if(!$this->model->contratoTransfereVencimentoFaturaCliente($vProposta['prpclioid'], $usuoid, $vProposta['prpdia_vcto_boleto'])){
                                $this->response->setResult(false, 'CTT015');
                                $teveErro = true;
                                break;
                            }
                            
                            // TRATAMENTO ESPECIAL TELEMETRIA                            
                            // Tranfere dados proposta->contato fase 02
                            if($this->model->contratoTransfereDados($connumero, $usuoid, $vContrato, '02')){
                                //array de connumero
                                $vConnumero[] = $connumero;                                
                            } else{
                                $this->response->setResult(false, 'CTT017');
                                $teveErro = true;
                                break;
                            }
                        } else{
                            // Erro ao Migrar dados da proposta fase 01
                            $this->response->setResult(false, 'CTT004');
                            $teveErro = true;
                            break;
                        }
                    }
                    // Loop proposta_item (FIM)
                     
                } else{
                    // Proposta não localizada
                    $this->response->setResult(false, 'CTT006');
                    $teveErro = true;
                    
                }
                
                if(!$teveErro){
                    // Atualiza status da proposta para C
                    $this->model->propostaStatusUpdate($prpoid, 'C');
                    $this->response->setResult($connumero, 'CTT005');
                }
                
            } else{
                // Proposta não localizada
                $this->response->setResult(false, 'CTT003');
                $teveErro = true;
            }
            
            if($controlaTransacao){
            	
                if($teveErro){
                    // Rollback Transaction
                    $this->model->cttDAO->rollbackTransaction();
                    // Código e mensagem de retorno
                } else{
                    // Commit Transaction
                    $this->model->cttDAO->commitTransaction();
                    
                    // Código e mensagem de retorno
                    $this->response->setResult($vConnumero,'CTT001');
                }
            }
        } else{
            $this->response->setResult(false, 'INF005');
        }
        return $this->response;
    }

    /**
     * Retorna a lista de contratos gerados por uma proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 19/12/2013
     * @param int $prpoid (ID da proposta)
     * @return response ($response->dados = array de connumeros / false)
     */
    public function contratoLista($prpoid=0){
        $prpoid = Mascara::inteiro($prpoid);
        
        if($prpoid > 0){
        	$resultSet = $this->model->contratoListGet($prpoid);
        	
        	if($resultSet !== false){
        		$this->response->setResult($resultSet, '0');        		
        	} else{
        		$this->response->setResult(false, 'INF002');
        	}           
        } else{
            $this->response->setResult(false, 'INF005');
        }
        
        return $this->response;
    }
    
    
    /**
     * Recebe número da proposta que gerou o contrato
     * 
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @param int $connumero
     * @return Response (mixed $response->dados, string $response->codigo, string $response->mensagem)
     */
    public function contratoPropostaBusca($connumero=0){
    	try {    	
	    	if($connumero > 0){
	    		$prpoid = $this->model->contratoPropostaGet($connumero);
	    		if ($prpoid > 0) {
	    			$this->response->setResult($prpoid,'0');
	    		} else{
	    			$this->response->setResult(false, 'CTT051');
	    		}	    		
	    	} else{
	    		$this->response->setResult(false, 'INF005');
	    	}
	    	
    	} catch (\Exception $e) {
    		$this->response->setResult($e, 'EXCEPTION');
    	}
    	return $this->response;
    }
    
}