<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Rafael Dias <rafael.dias@meta.com.br>
 * @version 07/11/2013
 * @since 07/11/2013
 * @package Core
 * @subpackage Classe Controladora de Ordens de Serviço
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */

namespace module\OrdemServico;

use infra\ComumController,
    infra\Helper\Response,
    infra\Helper\Mascara,
    infra\Helper\Validacao,   
    module\Contrato\ContratoService as Contrato, 
    module\OrdemServico\OrdemServicoModel as Modelo,
    module\Veiculo\VeiculoService as Veiculo,
    module\Cliente\ClienteService as Cliente;

class OrdemServicoController extends ComumController{    
    private $model;
    public $response;
    
    /**
     * Contrutor da classe
     *
      * @author Rafael Dias <rafael.dias@meta.com.br>
      * @version 07/11/2013
     * @param none
     * @return none
     */
    public function __construct(){
        $this->model = new Modelo();
        $this->response = new Response();
    }
    
    /**
     * Processo de geração de OS para um contrato
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 29/11/2013
     * @param int $connumero
     * @param int $usuoid
     * @param array $ordemServicoArray (array associativo tipo chave -> valor, dados da tabela ordem_servico)
     * @param array $ordemServicoArray (array associativo tipo chave -> valor, dados da tabela ordem_servico)
     *   OBS-> campos obrigatórios do $ordemServicoArray[]:
     *     int ordveioid -> Veículo vinculado a O.S
     *     int ordclioid -> Referencia do clioid na tabela clientes, a qual cliente esta direcionada a O.S
     *     int ordstatus -> Status da O.S, faz referência a tabela ordem_servico_status
     *     int ordeqcoid -> Classe do Contrato do Cliente no momento em que foi gerada a O.S
     *   OBS-> campos obrigatórios do $ordemServicoArray[]:
     *     int ordequoid -> Oid do Equipamento Instalado
     *     int ordeveoid -> Versão do equipamento Instalado
     *     int ordrepoid -> Representante Responsável
     *     int orditloid -> Instalador - Dados onde será realizada Instalação/Assistência
     *     boolean ordurgente -> Se a O.S é Urgente
     *     string orddesc_problema -> descrição do problema
     *     string orddescr_motivo -> Motivo da O.S.
     * @return Response $response:
     *     boolean $response->dados (true=OK /false = falha)
     *     string  $response->codigo (código do erro)
     *     string  $response->mensagem (mensagem emitida)
     */
    public function ordemServicoContratoGera($connumero=0, $usuoid=0, $ordemServicoArray=array()){        
        try{
            $connumero = Mascara::inteiro($connumero);
            $usuoid    = Mascara::inteiro($usuoid);
            
            //Definições de ID
            $ordemServicoObj = $this->ordemServicoContratoExiste($connumero);
            
            if ($ordemServicoObj->dados !== false){
                $ordoid = $ordemServicoObj->dados;
            } else{
                $ordemServicoObj = $this->ordemServicoCria($connumero, $usuoid, $ordemServicoArray);
                $ordoid = $ordemServicoObj->dados;
            }
            
            if ($ordoid == false){
                throw new \Exception('Erro ao criar a Ordem de Serviço: '.$ordemServicoObj->mensagem);
            }
                
            //Alteração de status
            $ordemServicoDadosArray = array('ordstatus'=>'4');
            $atualizaObj = $this->ordemServicoAtualiza($ordoid,$usuoid,$ordemServicoDadosArray);
            
            if($atualizaObj->dados === false){
                throw new \Exception($atualizaObj->mensagem);
            }
                            
            //Adicionar os serviços básicos e adicionais cadastrados na proposta
            $listaObj = $this->ordemServicoItensLista($ordoid, $connumero);
            
            if ($listaObj->dados === false) {
                throw new \Exception($listaObj->mensagem);
            }
                
            foreach ($listaObj->dados as $item){
                $itemObj = $this->ordemServicoItemInclui($ordoid, $item);
                
                if ($itemObj->dados === false){
                    throw new \Exception($itemObj->mensagem);
                }
                
                $ordemSituacaoArray = array('orssituacao' => 'Serviço adicionado: '.$item['ositobs']);
                $situacaoOSObj = $this->ordemSituacaoInclui($ordoid, $usuoid, $ordemSituacaoArray);
                
                if ($situacaoOSObj->dados === false){
                    throw new \Exception($situacaoOSObj->mensagem);
                }                
            }
            
            $this->response->setResult(true, '0', 'Ordem de Serviço incluída com sucesso!');
            
        } catch (\Exception $e){
            $this->response->setResult($e, 'EXCEPTION');
        }
        
        return $this->response;
    }
        
    /**
     * Verifica se tem uma Ordem de Serviço para um determinado contrato
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 09/12/2013
     * @param int $connumero
     * @return response ($response->dados = ordoid/false)
     */
    public function ordemServicoContratoExiste($connumero=0){
        $connumero = Mascara::inteiro($connumero);
        
        if($connumero > 0){
            $ordoid = $this->model->ordemServicoContratoGet($connumero);
        
            if($ordoid !== false){
                $this->response->setResult($ordoid, '0');
            } else{
                $this->response->setResult(false, '0', 'Ordem de Serviço não localizada.');
            }
        } else{
            $this->response->setResult(false, 'INF006');
        }
        
        return $this->response;
    }
    
    /**
     * Cria Ordem de Serviço
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 09/12/2013
     * @param int $connumero
     * @param int $usuoid
     * @param array $ordemServicoArray
     *   OBS-> campos obrigatórios do $ordemServicoArray[]:
     *     int ordveioid -> Veículo vinculado a O.S
     *     int ordclioid -> Referencia do clioid na tabela clientes, a qual cliente esta direcionada a O.S
     *     int ordstatus -> Status da O.S, faz referência a tabela ordem_servico_status
     *     int ordeqcoid -> Classe do Contrato do Cliente no momento em que foi gerada a O.S
     *   OBS-> campos obrigatórios do $ordemServicoArray[]:
     *     int ordequoid -> Oid do Equipamento Instalado
     *     int ordeveoid -> Versão do equipamento Instalado
     *     int ordrepoid -> Representante Responsável
     *     int orditloid -> Instalador - Dados onde será realizada Instalação/Assistência
     *     boolean ordurgente -> Se a O.S é Urgente
     *     string orddesc_problema -> descrição do problema
     *     string orddescr_motivo -> Motivo da O.S.
     * @return response ($response->dados = ordoid/false)
     */
    public function ordemServicoCria($connumero=0, $usuoid=0, $ordemServicoArray=array()){
        $connumero = Mascara::inteiro($connumero);
        $usuoid    = Mascara::inteiro($usuoid);        
        $exists    = false;
        $obrigatorio = array('ordclioid', 'ordveioid', 'ordstatus', 'ordeqcoid');
        
        //Verificando se os campos obrigatorios existem
        $exists = $this->verificaCampos($obrigatorio, $ordemServicoArray);
        
        if($exists){        
            $ordoid = $this->model->ordemServicoInsert($connumero, $usuoid, $ordemServicoArray);
            
            if($ordoid > 0){
                $this->response->setResult($ordoid, '0');
            } else{
                $this->response->setResult(false, 'INF001');
            }
        } else{
            $this->response->setResult(false, 'INF003');
        }
        
        return $this->response;
    }
        
    /**
     * Atualiza a Ordem de Serviço
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 09/12/2013
     * @param int $ordoid
     * @param int $usuoid
     * @param array $ordemServicoArray
     * @return response ($response->dados = ordoid/false)
     */
    public function ordemServicoAtualiza($ordoid=0, $usuoid=0, $ordemServicoArray=array()){
        $ordoid = Mascara::inteiro($ordoid);
        $usuoid = Mascara::inteiro($usuoid);
        
        if($ordoid > 0 && $usuoid > 0 && !empty($ordemServicoArray)){
            $ordoid = $this->model->ordemServicoUpdate($ordoid, $usuoid, $ordemServicoArray);

            if($ordoid !== false){
                $this->response->setResult($ordoid, '0');
            } else{
                $this->response->setResult(false, 'INF001');
            }
        } else{
            $this->response->setResult(false, 'INF006');
        }
    
        return $this->response;
    }
        
    /**
     * Inclui um item de Ordem de Serviço
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 03/12/2013
     * @param int $ordoid
     * @param int $usuoid
     * @param array $ordemServicoItemArray
     * @return response ($response->dados = $ositoid/false)
     */
    public function ordemServicoItemInclui($ordoid=0, $ordemServicoItemArray=array()){
        if(is_array($ordemServicoItemArray) && !empty($ordemServicoItemArray)){    
            $ordemServicoItemArray['ositordoid'] = Mascara::inteiro($ordoid);
            $exists = false;
            $obrigatorio = array('ositotioid', 'ositordoid', 'ositeqcoid', 'ositobs');
            
            //Verificando se os campos obrigatorios existem
            $exists = $this->verificaCampos($obrigatorio, $ordemServicoItemArray);
            
            if($exists){    
                $ositoid = $this->model->ordemServicoItemInsert($ordemServicoItemArray);
        
                if($ositoid > 0){
                    $this->response->setResult($ositoid, '0');
                } else{
                    $this->response->setResult(false, 'ORD010', 'Erro ao inserir item de ordem de serviço.');
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
     * Atualiza um item de Ordem de Serviço
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 24/01/2014
     * @param int $ositoid
     * @param array $ordemServicoItemArray
     * @return response ($response->dados = $ositoid/false)
     */
    public function ordemServicoItemAtualiza($ositoid=0, $ordemServicoItemArray=array()){
    	if(is_array($ordemServicoItemArray) && !empty($ordemServicoItemArray)){
    		    		
    		$ositoid = $this->model->ordemServicoItemUpdate($ositoid,$ordemServicoItemArray);
    
    		if($ositoid > 0){
    			$this->response->setResult($ositoid, '0');
    		} else{
    			$this->response->setResult(false, 'ORD010', 'Erro ao atualizar item de ordem de serviço.');
    		}
    		
    	} else{
    		$this->response->setResult(false, 'INF005');
    	}
    
    	return $this->response;
    }  
        
    /**
     * Lista itens de Ordem de Serviço
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 03/12/2013
     * @param int $ordoid
     * @param int $connumero
     * @throws \Exception
     * @return response ($response->dados = array/false)
     */
    public function ordemServicoItensLista($ordoid=0, $connumero=0){        
        try {
            $ordoid = Mascara::inteiro($ordoid);
            $connumero = Mascara::inteiro($connumero);
            $lista = array();
            
            if ($connumero > 0){                
                $propostaObj = Contrato::contratoPropostaBusca($connumero);
                $prpoid = $propostaObj->dados;
                
                if ($prpoid === false){
                    throw new \Exception($propostaObj->mensagem);
                }
                
                //serviços básicos e adicionais cadastrados na proposta
                $lista1 = $this->model->ordemServicoItensPropostaListGet($prpoid, $ordoid);
                
                //serviço de instalação de equipamento
                $lista2 = $this->model->ordemServicoItemEquipamentoListGet($prpoid, $ordoid);
                
                if ($lista1 === false && $lista2 === false){
                	throw new \Exception('Erro ao consultar itens');
                }
                
                if (is_array($lista1) && is_array($lista2)) {
                	$lista = array_merge($lista1,$lista2);
                } elseif (is_array($lista1)){
                	$lista = $lista1;
                } elseif (is_array($lista2)){
                	$lista = $lista2;
                }
                
                if (count($lista) === 0){
                    throw new \Exception('Nenhum ítem retornado');
                }
            } else{
                //serviços básicos e adicionais cadastrados na OS
                $lista = $this->model->ordemServicoItensListGet($ordoid);
                
                if ($lista === false){
                    throw new \Exception('Erro ao consultar itens da ordem de serviço');
                }               
                                
                if (count($lista) === 0){
                    throw new \Exception('Nenhum ítem retornado');
                }
            }
            
            $this->response->setResult($lista, '0');
            
        } catch (\Exception $e){
            $this->response->setResult($e, 'EXCEPTION');
        }
    
        return $this->response;
    }
    
    /**
     * Inclui registro em ordem_situacao
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 03/12/2013
     * @param int $ordoid
     * @param int $usuoid
     * @param array $ordemSituacaoArray
     * @return response ($response->dados)
     */
    public function ordemSituacaoInclui($ordoid=0, $usuoid=0, $ordemSituacaoArray=array()){
        if(is_array($ordemSituacaoArray) && !empty($ordemSituacaoArray)){                    
            $ordemSituacaoArray['orsordoid'] = Mascara::inteiro($ordoid);
            $ordemSituacaoArray['orsusuoid'] = Mascara::inteiro($usuoid);
            
            $exists = false;
            $obrigatorio = array('orsordoid', 'orssituacao');
                
            //Verificando se os campos obrigatorios existem
            $exists = $this->verificaCampos($obrigatorio, $ordemSituacaoArray);
            
            if($exists){    
                $gravou = $this->model->ordemSituacaoInsert($ordemSituacaoArray);
        
                if($gravou){
                    $this->response->setResult($gravou, '0');
                } else{
                    $this->response->setResult(false, 'ORD010');
                }
            } else{
                $this->response->setResult(false, 'INF003');
            }
        } else{
            $this->response->setResult(false, 'INF005');
        }
        
        return $this->response;
    }

}