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
 *
 * REVISIONS:
 * - 02/05/2014 Vinicius Senna <vsenna@brq.com>
 *      Método  "analisaCredito(...)" -> Adicionado mais itens no array de retorno, [STI 8.....]
 */
namespace module\GestorCredito;

use infra\ComumController,
	infra\Helper\Response,
    infra\Helper\Mascara,
    infra\Helper\Validacao,
    module\GestorCredito\GestorCreditoModel as Modelo,
	module\Contrato\ContratoService as Contrato,
	module\Cliente\ClienteService as Cliente;


//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/log_gestor_credito_'.date('d-m-Y').'.txt');


class GestorCreditoController extends ComumController{
	
	private $wsdlSerasaH = 'https://gw-homologa.serasa.com.br/wsgestordecisao/wsgestordecisao.asmx?wsdl';
	private $wsdlSerasaP = 'https://sitenet05.serasa.com.br/wsgestordecisao/wsgestordecisao.asmx?wsdl';
	
	private $model = '';
	private $objSerasa = '';
	private $response;
		
	/**
	 * Construtor da classe
	 * 
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 17/10/2013
     * @param none
     * @return none
	 */
	public function __construct(){
		$this->model = new Modelo();
		$this->response = new Response();
    }   
    
    /**
     * Realiza consulta (parametrizada) em uma única chamada.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 25/10/2013
     * @param string $cpf_cnpj (CPF ou CNPJ do cliente)
     * @param array associativo $dadosAnalise
     *    OBS-> campos obrigatórios do $dadosAnalise[]: 
     *      int formaPagamento -> forma de pagamento (forcoid)
     *      char(1) tipoPessoa -> tipo pessoa ('F'/'J')
     *      int tipoProposta -> Tipo de Proposta (tppoid)
     *      int tipoContrato -> Tipo de Contrato (tpcoid)
     *      int qtdEquipamentos -> Quantidade de equipamentos
     *      float valorTotalCompra -> valor total da compra/proposta
     *    OBS-> campos NÃO obrigatórios do $dadosAnalise[]: 
     *      int subTipoProposta ->  Subtipo de Proposta (tppoid_sub)
     * @param char(1) $opAmbSerasa (Ambiente de consulta ao serasa)
     * @return array dados da consulta crédito / false
     */
    public function analisaCredito($cpf_cnpj='', $dadosAnalise=array(), $opAmbSerasa='H'){
    	$cpf_cnpj = trim($cpf_cnpj);

    	if($cpf_cnpj != '' && !empty($dadosAnalise)){
    		$cpf_cnpj = Mascara::somenteNumeros($cpf_cnpj);
            $result = Validacao::validaCpfCnpj($cpf_cnpj);

            // Total de contratos
            $totalContratos = 0;
            // Cliente desde
            $clienteDesde = '';
            // Experiencia cliente
            $expCliente = '';
            // Valor medio da parcela
            $valorMedioParcela = 0.0;
            // Maior atraso;
            $maiorAtraso = '0';
            // Dias atraso
            $diasAtraso = 0;
            // Pendencia financeira
            $pendenciaFinanceira = false;
            // Soma contratos ativos
            $somaContratosAtivos = 0;
            // Id Pagador
            $clientePagador = 0;

    		    		 
    		if(Validacao::validaCpfCnpj($cpf_cnpj)){
    			$obrigatorio = array('formaPagamento', 'tipoPessoa', 'tipoProposta', 'tipoContrato', 'qtdEquipamentos', 'valorTotalCompra');
    			$exists = false;
    
    			//Verificando se os campos obrigatorios existem
    			$exists = $this->verificaCampos($obrigatorio, $dadosAnalise);
                
    			if($exists){
    				//Parâmetros
    				$prptpcoid = Mascara::inteiro($dadosAnalise['tipoContrato']);
    				$prptppoid = Mascara::inteiro($dadosAnalise['tipoProposta']);
    				$subTipoProposta = Mascara::inteiro($dadosAnalise['subTipoProposta']);
    				$tipo_pessoa = $dadosAnalise['tipoPessoa'];
    				    				
    				$vCreditoParametrizacao = $this->gestorCreditoParametrizacaoBuscaDados($prptpcoid, $prptppoid, $tipo_pessoa, $subTipoProposta);
    				
    				$objCliente = Cliente::clienteGetDados($cpf_cnpj, 'DOC');    				
    				$vCliente = $objCliente->dados;
                   
    				//Parâmetros
    				$limiteContratos  = Mascara::inteiro($vCreditoParametrizacao['gcpconlimite']);
    				$gcpindica_gestor = strtolower(trim($vCreditoParametrizacao['gcpindica_gestor']));
    				
    				$verificaInterna = false;
    				    				
    				$objClientePagador = $this->clienteVerificaPagador($prptpcoid);
    				$vClientePagador = $objClientePagador->dados;
    				
    				if(is_array($vClientePagador) && !empty($vClientePagador)){
    					$clioidPagador = $vClientePagador['clioid_pagador'];
                        $clientePagador = $vClientePagador['clioid_pagador'];
    					$verificaInterna = true;

    				} else{
    					if(is_array($vCliente)){
	    					$clioidPagador = $vCliente['clioid'];
	    					$verificaInterna = true;
    					} else{
    						$verificaInterna = false;
    					}
    				}

                    $clioidPagador = Mascara::inteiro($clioidPagador);

                    if($clioidPagador) {
                        // Cliente desde
                        $clienteDesde = $this->model->clienteBuscaMenorDataContrato($clioidPagador,$clientePagador);
                        // Experiencia do cliente
                        $expCliente = $this->clienteMediaAtraso($clioidPagador, false);
                        $expCliente = utf8_encode($expCliente->dados['classificacao']);
                        // Valor médio da parcela
                        $valorMedioParcela = $this->model->getValorMedioTitulos($clioidPagador);
                        // Dias atraso
                        $diasAtraso = $this->model->clienteBuscaDiasAtraso($clioidPagador);
                        $diasAtraso = Mascara::inteiro($diasAtraso['dias_atraso']);
                        // Maior atraso
                        $maiorAtraso = $this->model->clienteBuscaMaiorAtraso($clioidPagador);
                        $maiorAtraso = Mascara::inteiro($maiorAtraso['dias_atraso']);
                        if($maiorAtraso) {
                            if($maiorAtraso == 1){
                                $maiorAtraso .= ' dia';
                            } else{
                                $maiorAtraso .= ' dias';
                            }
                        }
                        else{
                            $maiorAtraso = (string) $maiorAtraso;
                        }
                        // pendencia financeira
                        $pendenciaFinanceira = $this->model->clienteVerificaInadimplencia($clioidPagador);
                        // Soma contratos
                        $somaContratosAtivos = $this->model->clienteSomarTotalcontratosAtivos($clioidPagador, $clientePagador);

                    } else{
                        $expCliente = 'Cliente Novo';
                    } 

    				if($verificaInterna === true){
    					$retornoAnalise = array();
    					$objAnalisaCredito = $this->sascarAnalisaCredito($clioidPagador);
    					$resultSet = $objAnalisaCredito->dados;
                        

    					if($resultSet === false){
    						$retornoAnalise['prppsfoid'] = 1;
                       		$retornoAnalise['prppsfoidgestor'] = 3;
                       		$retornoAnalise['prpstatus_aprovacao'] = 1;
	                        $retornoAnalise['prpusuoid_aprovacao_fin'] = 'null';
	                        $retornoAnalise['prpresultado_str'] = 'Análise Interna: Crédito Não Aprovado - Cliente inadimplente.';
	                        $retornoAnalise['prpobservacao_financeiro'] = 'Análise Interna: Crédito Não Aprovado - Cliente inadimplente.';    									
    						
                            $retornoAnalise['cliente_desde'] = $clienteDesde;
                            $retornoAnalise['total_contratos'] = $somaContratosAtivos;
                            $retornoAnalise['experiencia_cliente'] = $expCliente;
                            $retornoAnalise['valor_medio_parcela'] = $valorMedioParcela;
                            $retornoAnalise['pendencia_financeira'] = $pendenciaFinanceira;
                            $retornoAnalise['dias_atraso'] = $diasAtraso;
                            $retornoAnalise['maior_atraso'] = $maiorAtraso;

	                	    $this->response->setResult($retornoAnalise, '0');
	                	    return $this->response;
    					}
    					
    					if($limiteContratos > 0){
	    					$totalContratos  = $this->clienteBuscaTotalContratos($clioidPagador);
	    					$qtdEquipamentos = Mascara::inteiro($dadosAnalise['qtdEquipamentos']);
	    					$totalContratos  = $totalContratos + $qtdEquipamentos;
	    								
	    					if($totalContratos > $limiteContratos){
	    						$retornoAnalise['prppsfoid'] = 1;
	    						$retornoAnalise['prppsfoidgestor'] = 3;
	    						$retornoAnalise['prpstatus_aprovacao'] = 1;
	    						$retornoAnalise['prpusuoid_aprovacao_fin'] = 'null';
	    						$retornoAnalise['prpresultado_str'] = 'Análise Interna: Crédito Não Aprovado - Limite de contratos excedido.';
	    						$retornoAnalise['prpobservacao_financeiro'] = 'Análise Interna: Crédito Não Aprovado - Limite de contratos excedido.';
	    						
                                $retornoAnalise['cliente_desde'] = $clienteDesde;
                                $retornoAnalise['total_contratos'] = $somaContratosAtivos;
                                $retornoAnalise['experiencia_cliente'] = $expCliente;
                                $retornoAnalise['valor_medio_parcela'] = $valorMedioParcela;
                                $retornoAnalise['pendencia_financeira'] = $pendenciaFinanceira;
                                $retornoAnalise['dias_atraso'] = $diasAtraso;
                                $retornoAnalise['maior_atraso'] = $maiorAtraso;

	    						$this->response->setResult($retornoAnalise, '0');
	                	    	return $this->response;
	    					}
    					}

    				}

    				if($gcpindica_gestor == 't'){
    					$objSerasaCredito = $this->serasaAnalisaCredito($cpf_cnpj, $dadosAnalise, $opAmbSerasa);
    					$resultSet = $objSerasaCredito->dados;
    					
                        $resultSet['pendencia_financeira'] = $pendenciaFinanceira;
                        $resultSet['cliente_desde'] = $clienteDesde;
                        $resultSet['total_contratos'] = $somaContratosAtivos;
                        $resultSet['experiencia_cliente'] = $expCliente;
                        $resultSet['valor_medio_parcela'] = $valorMedioParcela;
                        $resultSet['dias_atraso'] = $diasAtraso;
                        $resultSet['maior_atraso'] = $maiorAtraso;

    					if(is_array($resultSet)){
    						$this->response->setResult($resultSet, '0', 'Retorno SERASA');
    					} else{
    						$this->response->setResult(false, '0', 'Retorno SERASA');
    					}
    				} else{
    					$retornoAnalise['prppsfoid'] = 1;
    					$retornoAnalise['prppsfoidgestor'] = 1;
    					$retornoAnalise['prpstatus_aprovacao'] = 0;
    					$retornoAnalise['prpusuoid_aprovacao_fin'] = 'null';
    					$retornoAnalise['prpresultado_str'] = 'Crédito Aprovado!';
    					$retornoAnalise['prpobservacao_financeiro'] = '';

                        $retornoAnalise['cliente_desde'] = $clienteDesde;
                        $retornoAnalise['total_contratos'] = $somaContratosAtivos;
                        $retornoAnalise['experiencia_cliente'] = $expCliente;
                        $retornoAnalise['valor_medio_parcela'] = $valorMedioParcela;
                        $retornoAnalise['pendencia_financeira'] = $pendenciaFinanceira;
    					$retornoAnalise['dias_atraso'] = $diasAtraso;
                        $retornoAnalise['maior_atraso'] = $maiorAtraso;

    					$this->response->setResult($retornoAnalise, '0');

    				}
    			} else{
    				$this->response->setResult(false, 'INF003');
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
     * Realiza consulta no SERASA.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 18/10/2013
     * @param string $cpf_cnpj (CPF ou CNPJ do cliente)
     * @param array associativo $dadosAnalise
     *    OBS-> campos obrigatórios do $dadosAnalise[]: 
     *      int formaPagamento -> forma de pagamento (forcoid)
     *      char(1) tipoPessoa -> tipo pessoa ('F'/'J')
     *      int tipoProposta -> Tipo de Proposta (tppoid)
     *      int tipoContrato -> Tipo de Contrato (tpcoid)
     *      int qtdEquipamentos -> Quantidade de equipamentos
     *      float valorTotalCompra -> valor total da compra/proposta
     * @param char(1) $opAmbSerasa (Ambiente de consulta ao serasa)
     * @return array dados da consulta/crédito
     */
    public function serasaAnalisaCredito($cpf_cnpj=0, $dadosAnalise=array(), $opAmbSerasa='H'){
    	$cpf_cnpj = Mascara::somenteNumeros($cpf_cnpj);
    	$result = Validacao::validaCpfCnpj($cpf_cnpj);    	
    	
    	if($result === true && is_array($dadosAnalise)){
    		$obrigatorio = array('formaPagamento', 'tipoPessoa', 'tipoProposta', 'tipoContrato', 'qtdEquipamentos', 'valorTotalCompra');
    		$exists = false;
    		
    		//Verificando se os campos obrigatorios existem
    		$exists = $this->verificaCampos($obrigatorio, $dadosAnalise);
    		
    		if($exists){
    			
    			$objParametros = new \stdClass();
    			
    			if($opAmbSerasa=='H'){
	    			//Dados sascar homologação
	    			$objParametros->sCNPJ 	 = '03112879000151';
	    			$objParametros->sUsrGC 	 = 'SASCAR';
	    			$objParametros->sPassGC  = 'serasa';
	    			$objParametros->sUsrSer  = '06886475';
	    			$objParametros->sPassSer = '10203040';
	    			
    			}else{
    				//Dados sascar produção
    				$objParametros->sCNPJ 	 = '03112879000151';
    				$objParametros->sUsrGC 	 = 'sascar';
    				$objParametros->sPassGC  = 'serasa';
    				$objParametros->sUsrSer  = '89027253';
    				$objParametros->sPassSer = 'gestor@3';
    			}
    			
    			//Parâmetros
    			$objParametros->sDoc = $cpf_cnpj;
    			$objParametros->VrCompra = $dadosAnalise['valorTotalCompra'];
    			$objParametros->sScore  = '    ';
    			$objParametros->bSerasa = true;
    			$objParametros->bAtualizar = false;   			
    			$objParametros->sOnLine = utf8_encode($this->montarDadosOnline($cpf_cnpj, $dadosAnalise));

    			$fp = fopen("/tmp/log_cargo_track_salesforce_GESTOR_CREDITO_SERASA_CORE.txt","w+");
    			chmod("/tmp/log_cargo_track_salesforce_GESTOR_CREDITO_SERASA_CORE.txt", 0777);
    			
    			fwrite ($fp,' $objParametros->  =>  '.serialize($opAmbSerasa).PHP_EOL.PHP_EOL);
    			
    			   			
    			//Chama funcao do WS Serasa
    			if($opAmbSerasa=='H'){
    			    $this->objSerasa = new \SoapClient($this->wsdlSerasaH, array('trace' => 1, 'exceptions' => 1, 'soap_version' => SOAP_1_1));
    			}else{
    			    $this->objSerasa = new \SoapClient($this->wsdlSerasaP, array('trace' => 1, 'exceptions' => 1, 'soap_version' => SOAP_1_1));
    			}
    			
    			fwrite ($fp,' $this->objSerasa   ->  =>  '.serialize($this->objSerasa).PHP_EOL.PHP_EOL);
    			
    			
    			$this->objSerasa->AnalisarCredito($objParametros);
    			
    			$objXML = new \SimpleXMLElement($this->objSerasa->__getLastResponse());
    			    			   			
    	
    			fwrite ($fp,'$objXML  ->  =>  '.serialize($this->objSerasa->__getLastResponse()).PHP_EOL.PHP_EOL);
    			
    			fclose($fp);
    			
    			
    			$envelope = $objXML->xpath('//soap:Envelope');
    			$envelope = reset($envelope);
    			
    			$body = $envelope->xpath('soap:Body');
    			$body = reset($body);
    			
                $objRetorno = str_replace("\n", "(#)", $body->AnalisarCreditoResponse->AnalisarCreditoResult[0]);
                $retorno = explode('(#)', $objRetorno);
                                
                $vRetorno = array();
                $arrayRetorno = array();

                foreach($retorno as $v){    

                    $valor = explode('=', $v);

                    $valor[0] = trim($valor[0]);
                    $valor[1] = trim($valor[1]);

                    if($arrayRetorno[$valor[0]]=='MSGE_TIPO'){
                        $arrayRetorno[$valor[0]] = utf8_decode($valor[1]);
                    }else{
                        $arrayRetorno[$valor[0]] = $valor[1];
                    }

                }      			  			
    			
    			if($arrayRetorno['MSGE_TIPO'] == "NAO APROVADO" || Mascara::removeAcento(utf8_decode($arrayRetorno['MSGE_TIPO'])) == "NAO APROVADO"){
    				$vRetorno['prppsfoid'] = 3;
    				$vRetorno['prppsfoidgestor'] = 3;
                    $vRetorno['prpstatus_aprovacao'] = 1;
    				$vRetorno['prpresultado_aciap'] = "Análise Gestor Serasa: Crédito Não Aprovado.";
    				$vRetorno['prpobservacao_financeiro'] = "Análise Gestor Serasa: Crédito Não Aprovado.";
    			} elseif($arrayRetorno['MSGE_TIPO'] == "ANALISE NA RETAGUARDA"){
    				$vRetorno['prppsfoid'] = 1;
    				$vRetorno['prppsfoidgestor'] = 1;
                    $vRetorno['prpstatus_aprovacao'] = 2;
    				$vRetorno['prpusuoid_aprovacao_fin'] = 'null';
    				$vRetorno['prpresultado_aciap'] = "Análise Gestor Serasa: Aguardando Análise.";
    				$vRetorno['prpobservacao_financeiro'] = "Análise Gestor Serasa: Aguardando Análise.";
    			} elseif($arrayRetorno['MSGE_TIPO'] == "INSUFICIENCIA DE DADOS" || $arrayRetorno['CHAVE'] == '0' || empty($arrayRetorno['MSGE_TIPO'])){
    				$vRetorno['prppsfoid'] = 1;
    				$vRetorno['prppsfoidgestor'] = 1;
                    $vRetorno['prpstatus_aprovacao'] = 2;
    				$vRetorno['prpusuoid_aprovacao_fin'] = 'null';
    				$vRetorno['prpresultado_aciap'] = "Falha ao realizar análise de crédito.";
    				$vRetorno['prpobservacao_financeiro'] = "Falha ao realizar análise de crédito.";
    			} elseif($arrayRetorno['ERRO'] != ''){
    				$vRetorno['prpobservacao_financeiro'] .= ' '.$arrayRetorno['ERRO'];
    				$vRetorno['prpresultado_aciap'] .= ' '.$arrayRetorno['ERRO'];
                    $vRetorno['prpstatus_aprovacao'] = 2;
    			} else{
    				$vRetorno['prppsfoid'] = 1;
    				$vRetorno['prppsfoidgestor'] = 1;
                    $vRetorno['prpstatus_aprovacao'] = 0;
    				$vRetorno['prpobservacao_financeiro'] = "Análise Gestor SERASA: Crédito Aprovado.";
    			}
    			
    			$vRetorno['prpresultado_aciap'] = utf8_encode($vRetorno['prpresultado_aciap']);
    			$vRetorno['prpresultado_aciap'] .= "\n".$body->AnalisarCreditoResponse->AnalisarCreditoResult[0];    			
    			
    			foreach ($vRetorno as $chave => $valor) {
    				$vRetorno[$chave] = Mascara::removeAcento(utf8_decode($valor));
    			}
    			
    			$this->response->setResult($vRetorno, '0', 'Retorno SERASA');
    		} else{
    			$this->response->setResult(false, 'INF005');
    		}   		
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Verifica se o cliente pagador possui titulos em atraso há mais de 15 dias.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 18/10/2013
     * @param int $clioid
     * @return response ($response->dados = true/false)
     */
    public function sascarAnalisaCredito($clioid){
    	$clioid = Mascara::inteiro($clioid);
    	if($this->model->verificaPendenciaTitulosInterna($clioid)){
    	    $this->response->setResult(false, '0', 'Possui titulos em atraso a mais de 15 dias.');
        }else{
            $this->response->setResult(true, '0', 'Não possui pendências.');
        }
    	return $this->response;
    }
    
    /**
     * Calcula a média de atraso do cliente e estabelece uma classificação textual.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 23/10/2013
     * @param int $clioid (ID do Cliente)
     * @param boolean $mDias (Passe TRUE para retornar a media de atraso em dias)
     * @return response  ($response->dados = array / int / false)
     */
    public function clienteMediaAtraso($clioid=0, $mDias=false){
    	$clioid = Mascara::inteiro($clioid);
    	$mediaAtraso   = 0;
    	$classificacao = '';
    	$tipoCliente   = '';
    	
    	if($clioid > 0){
            $mediaAtraso = $this->model->getMediaAtrasoCliente($clioid);

    		if(!$mDias){			
    			$tipoCliente = 'BASE';

                if($mediaAtraso === false){
                    $classificacao = "Cliente Novo";
                } else{
                    $mediaAtraso = Mascara::inteiro($mediaAtraso);

                    if($mediaAtraso <= 5){
                        $classificacao = "Ótimo";               
                    } elseif($mediaAtraso <= 15){
                        $classificacao = "Bom";             
                    } elseif($mediaAtraso <= 45){
                        $classificacao = "Regular";             
                    } elseif($mediaAtraso > 45){
                        $classificacao = "Ruim";            
                    } else{
                        $classificacao = "Cliente Novo";
                    }
                }
    			
    				    			 
    			$mediaAtraso = array('classificacao' => $classificacao, 'tipoCliente' => $tipoCliente);
    			$this->response->setResult($mediaAtraso, '0');
    		} else{
    			$this->response->setResult($mediaAtraso, '0');
    		}
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Retorna os dados do cliente pagador.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 28/10/2013
     * @param int $prptpcoid
     * @return response ($response->response = array/false)
     */
    public function clienteVerificaPagador($prptpcoid=0){
    	$prptpcoid = Mascara::inteiro($prptpcoid);
    	
    	if($prptpcoid >= 0){// OBS: tipo 0 (ZERO) = cliente
    		$resultSet = $this->model->clientePagadorGetDados($prptpcoid);
    		
    		if(is_array($resultSet) && !empty($resultSet)){
    			$this->response->setResult($resultSet, '0');
    		} else{
    			$this->response->setResult(false, '0');
    		}
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Método retorna o valor médio dos títulos do cliente
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 04/11/2013
     * @param int $clioid (ID do cliente)
     * @return response ($response->dados = média/false)
     */
    public function clienteValorMedioTitulosAtivos($clioid=0){
    	$clioid = Mascara::inteiro($clioid);
    	
    	if($clioid > 0){
    		$result = $this->model->getValorMedioTitulosAtivos($clioid);
    		$this->response->setResult($result, '0');
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Método retorna o NÚMERO total de títulos ativos do cliente
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 04/11/2013
     * @param int $clioid (ID do cliente)
     * @return response ($response->dados = Número de títulos)
     */
    public function clienteNumeroTotalTitulosAtivos($clioid=0){
    	$clioid = Mascara::inteiro($clioid);
    	if($clioid > 0){
    		$result = $this->model->getNumeroTotalTitulosAtivos($clioid);
    		$this->response->setResult($result, '0');
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	
    	return $this->response;
    }
    
    /**
     * Método retorna o VALOR total de títulos ativos do cliente
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 24/10/2013
     * @param int $clioid (ID do cliente)
     * @return response ($response->dados = valor total de títulos)
     */
    public function clienteValorTotalTitulosAtivos($clioid=0){
    	$clioid = Mascara::inteiro($clioid);
    	if($clioid > 0){
    		$result = $this->model->getValorTotalTitulosAtivos($clioid);
    		$this->response->setResult($result, '0');
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    	return $this->response;
    }
    
    /**
     * Retorna o total de contratos de um cliente.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 28/10/2013
     * @param int $clioid
     * @return int / false
     */
    private function clienteBuscaTotalContratos($clioid=0){
    	$clioid = Mascara::inteiro($clioid);
    	 
    	if($clioid > 0){
    		return $this->model->clienteGetTotalContratos($clioid);
    	} else{
    		return false;
    	}
    }
    
    /**
     * Retorna o maior atraso de um cliente
     * @author Vinicius senna <vsenna@brq.com>
     * @version 02/05/2014
     * @param int $titclioid id do cliente
     * @return string / false
     */
    private function clienteBuscaMaiorAtraso($titclioid=0){
        $titclioid = Mascara::inteiro($titclioid);

        if($titclioid > 0){
            return $this->model->clienteBuscaMaiorAtraso($titclioid);
        } else{
            return false;
        }
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
    private function gestorCreditoParametrizacaoBuscaDados($prptpcoid=0, $prptppoid=0, $tipo_pessoa='', $prptppoid_sub=0){
    	$prptpcoid = Mascara::inteiro($prptpcoid);
    	$prptppoid = Mascara::inteiro($prptppoid);
    	$tipo_pessoa  = strtoupper(trim($tipo_pessoa));
    	$prptppoid_sub = Mascara::inteiro($prptppoid_sub);
    	 
    	if($prptppoid > 0 && $tipo_pessoa != ''){
    		return $this->model->gestorCreditoParametrizacaoGetDados($prptpcoid, $prptppoid, $tipo_pessoa, $prptppoid_sub);
    	} else{
    		return false;
    	}
    }
    
    /**
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 29/10/2013
     * @param string $cpf_cnpj (CPF ou CNPJ do cliente)
     * @param array associativo $dadosAnalise
     *    OBS-> campos obrigatórios do $dadosAnalise[]: 
     *      int formaPagamento -> forma de pagamento (forcoid)
     *      char(1) tipoPessoa -> tipo pessoa ('F'/'J')
     *      int tipoProposta -> Tipo de Proposta (tppoid)
     *      int tipoContrato -> Tipo de Contrato (tpcoid)
     *      int qtdEquipamentos -> Quantidade de equipamentos
     *      float valorTotalCompra -> valor total da compra/proposta
     * @return array / false
     */
    private function montarDadosOnline($cpf_cnpj, $dadosAnalise=array()){
    	$cpf_cnpj = Mascara::somenteNumeros($cpf_cnpj);
    	$result = Validacao::validaCpfCnpj($cpf_cnpj);
    	
    	if($result === true && !empty($dadosAnalise)){
    		$prptpcoid  = $dadosAnalise['tipoProposta'];
    		$prpforcoid = $dadosAnalise['formaPagamento'];
    		$prpnum_veiculos = $dadosAnalise['qtdEquipamentos'];
    			
    		$objClientePagador = $this->clienteVerificaPagador($prptpcoid);
    		$vClientePagador = $objClientePagador->dados;

    		$clioid  = Mascara::inteiro($vClientePagador['clioid_pagador']);
    		$pagador = true;

    		if($clioid == 0){
    			$objCliente = Cliente::clienteGetDados($cpf_cnpj, 'DOC');
    			$vCliente = $objCliente->dados;
                
    			if(is_array($vCliente) && !empty($vCliente)){
    				$clioid  = Mascara::inteiro($vCliente['clioid']);
    				$pagador = false;
    			}
    		}
    		//Parâmetros
    		$objMedia = $this->clienteMediaAtraso($clioid);
    		$vMedia = $objMedia->dados;
    		
    		$objAnalisaCredito = $this->sascarAnalisaCredito($clioid);
    		$status = $objAnalisaCredito->dados;
    		
    		$vFormaCobranca = $this->buscaFormasCobranca();
    			
    		if($status){
    			$status = 'ADIMPLENTE';
    		} else{
    			$status = 'INADIMPLENTE';
    		}
    			
    		if($clioid == 0){
    			$tipo = 'NOVO';
    		} else{
    			$tipo = $vMedia['tipoCliente'];
    		}
    		
    		$objMedia = $this->clienteMediaAtraso($clioid, true);
    		$vMedia = $objMedia->dados;

    		//RETORNO
    		$resultSet = array();
    		$resultSet[] = "FORMA PAGAMENTO@".$vFormaCobranca[$prpforcoid];
    		$resultSet[] = "MEDIA ATRASO@".$vMedia;
    		$resultSet[] = "QTDE EQUIPAMENTOS BASE@".$this->buscaTotalContratosAtivos($clioid, $pagador);
    		$resultSet[] = "QTDE EQUIPAMENTOS PROPOSTA@".$prpnum_veiculos;
    		$resultSet[] = "TEMPO RELACIONAMENTO@".$this->buscaTempoRelacionamentoCliente($clioid);
    		$resultSet[] = "TIPO CLIENTE@".$tipo;
    		$resultSet[] = "RESTRICAO INTERNA@".$this->buscaValorTitulosAtrasados($clioid);
    		$resultSet[] = "DO STATUS@".$status;
    	    			
    		return implode("|", $resultSet);
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
    private function buscaFormasCobranca(){
    	return $this->model->getFormasCobranca();
    }
    
    /**
     * Soma todos os contratos ativos do cliente pagador.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 30/10/2013
     * @param int $clioid (ID do cliente)
     * @param boolean $pagador (Informa se o cliente é ou não o pagador)
     * @return int
     */
    private function buscaTotalContratosAtivos($clioid=0, $pagador=false){
    	$clioid = Mascara::inteiro($clioid);
    	
    	if($clioid > 0){
    		return $this->model->getTotalContratosAtivos($clioid, $pagador);
    	} else{
    		return 0;
    	}
    }
    
    /**
     * Retorna o tempo de relacionamento do cliente (Em meses).
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 30/10/2013 
     * @param int $clioid
     * @return float/int
     */
    private function buscaTempoRelacionamentoCliente($clioid=0){
    	$clioid = Mascara::inteiro($clioid);
    	
    	if($clioid > 0){
    		return $this->model->getTempoRelacionamentoCliente($clioid);
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
    private function buscaValorTitulosAtrasados($clioid=0){
    	$clioid = Mascara::inteiro($clioid);
    	 
    	if($clioid > 0){
    		return $this->model->getValorTitulosAtrasados($clioid);
    	} else{
    		return 0;
    	}
    }
}
