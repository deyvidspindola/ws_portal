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
 * @subpackage Classe Model da Ordem de Serviço
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */

namespace module\OrdemServico;

use infra\Helper\Mascara,
	module\OrdemServico\OrdemServicoDAO as DAO;

class OrdemServicoModel {
	public $dao;
	// Campos no BD (OS);	
	private $ordIntFieldList;	
	private $ordFloatFieldList;	
	private $ordFkList;
	// Campos no BD (Item OS)	
	private $ositItemIntFieldList;
	private $ositItemFloatFieldList;
	
	public function __construct() {
		$this->dao = new DAO();
		
		$this->ordIntFieldList = array('ordclioid', 'ordveioid', 'ordconnumero', 'ordstatus', 'ordequoid', 'ordeqcoid', 'ordeveoid', 'ordusuoid', 'ordmtioid', 'ordotsoid');
		$this->ordFloatFieldList = array();
		$this->ordFkList = array();
		
		$this->ositItemIntFieldList = array('ositoid', 'ositotioid', 'ositobs', 'ositeqcoid');
		$this->ositItemFloatFieldList = array();
		$this->ositFkList = array();
	}
			
	/**
	 * Inclui uma OS
	 *
	 * @author Bruno B. Affonso
	 * @version 09/12/2013
	 * @param int $connumero
	 * @param int $usuoid
	 * @param array $ordemServicoArray (array associativo tipo chave -> valor, dados da tabela ordem_servico)
	 * @return mixed $ordoid/false
	 */
	public function ordemServicoInsert($connumero, $usuoid, $ordemServicoArray) {
		if(is_array($ordemServicoArray) && !empty($ordemServicoArray)){
			$campos  = '';
			$valores = '';
			$strSeparador = '';
			
			//Tratando os parâmetros
			$ordemServicoArray['ordusuoid'] = Mascara::setDefaultNull($usuoid, 'I');
			$ordemServicoArray['ordconnumero'] = Mascara::setDefaultNull($connumero, 'I');
			$ordemServicoArray['ordclioid'] = Mascara::setDefaultNull(Mascara::inteiro($ordemServicoArray['ordclioid']), 'I');
			$ordemServicoArray['ordveioid'] = Mascara::setDefaultNull(Mascara::inteiro($ordemServicoArray['ordveioid']), 'I');
			$ordemServicoArray['ordstatus'] = Mascara::setDefaultNull(Mascara::inteiro($ordemServicoArray['ordstatus']), 'I');
			$ordemServicoArray['ordeqcoid'] = Mascara::setDefaultNull(Mascara::inteiro($ordemServicoArray['ordeqcoid']), 'I');
			$ordemServicoArray['ordequoid'] = Mascara::setDefaultNull(Mascara::inteiro($ordemServicoArray['ordequoid']), 'I');
			$ordemServicoArray['ordeveoid'] = Mascara::setDefaultNull(Mascara::inteiro($ordemServicoArray['ordeveoid']), 'I');
			$ordemServicoArray['ordmtioid'] = Mascara::setDefaultNull(Mascara::inteiro($ordemServicoArray['ordmtioid']), 'I');
			$ordemServicoArray['ordotsoid'] = Mascara::setDefaultNull(Mascara::inteiro($ordemServicoArray['ordotsoid']), 'I');
	    	$ordemServicoArray['ordurgente'] = (strlen($ordemServicoArray['ordurgente']) > 0) ? "'".$ordemServicoArray['ordurgente']."'" : "'f'";
	    	$ordemServicoArray['orddesc_problema'] = "'".trim($ordemServicoArray['orddesc_problema'])."'";
	    	$ordemServicoArray['orddescr_motivo']  = "'".trim($ordemServicoArray['orddescr_motivo'])."'";
	    	
	    	//Montando INSERT
			foreach ($ordemServicoArray as $key => $value){
				$campos .= $strSeparador . $key;
				$valores .= $strSeparador . $value;
				$strSeparador = ',';
			}
			
			$vOrdemServico = $this->dao->ordemServicoInsert($campos, $valores);
			
			if(is_array($vOrdemServico)){
				return Mascara::inteiro($vOrdemServico['ordoid']);
			} else{
				return false;
			}
		} else{
			return false;
		}
	}
	
	/**
	 * Inclui uma OS
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @version 09/12/2013
	 * @param int $ordoid
	 * @param int $usuoid
	 * @param array $ordemServicoArray (array associativo tipo chave -> valor, dados da tabela ordem_servico)
	 * @return mixed $ordoid/false
	 */
	public function ordemServicoUpdate($ordoid, $usuoid, $ordemServicoArray) {
		$dados = '';
        $strSeparador = '';
        $ordemServicoArray = $this->dao->applyCast($ordemServicoArray, $this->ordIntFieldList, $this->ordFloatFieldList, $this->ordFkList);
        
        foreach ($ordemServicoArray as $key => $value){
            $dados .= $strSeparador . $key.' = '.$value;
            $strSeparador = ', ';
        }
        
        if(!empty($dados)){
            $vOrdemServico = $this->dao->ordemServicoUpdate($dados, $ordoid);
            
            if(is_array($vOrdemServico)){
                return Mascara::inteiro($vOrdemServico['ordoid']);
            } else{
                return false;
            }
        } else{
            return false;
        }
	}	
	
	/**
	 * Inclui um item de OS.
	 *
	 * @author Rafael Dias <rafael.dias@meta.com.br>
	 * @version 02/12/2013
	 * @param array $ordemServicoItemArray (array associativo tipo chave -> valor, dados da tabela ordem_servico_item)
	 * @return mixed $ositoid/false
	 */
	public function ordemServicoItemInsert($ordemServicoItemArray){
		//utilizando function (ordem_servico_item_i)
		$vItem = $this->dao->itemInsert($ordemServicoItemArray);
	
		if($vItem !== false){
			return Mascara::inteiro($vItem['ositoid']);
		} else{
			return false;
		}
	}
	
	/**
	 * Atualiza um item de OS
	 *
	 * @author Rafael Dias <rafael.dias@meta.com.br>
	 * @version 24/01/2014
	 * @param int $ositoid
	 * @param array $ordemServicoItemArray (array associativo tipo chave -> valor, dados da tabela ordem_servico_item)
	 * @return mixed $ositoid/false
	 */
	public function ordemServicoItemUpdate($ositoid, $ordemServicoItemArray) {
		$dados = '';
		$strSeparador = '';
		$IntFieldList = array('ositotioid','ositeqcoid','ositordoid','osittmooid','osittmoioid','ositosdfoid_alegado','ositosdfoid_analisado','ositalioid','ositotcoid','ositotooid','ositotaoid','ositotsoid');
		$FloatFieldList = array();
		$FkList = array();
		$ordemServicoItemArray = $this->dao->applyCast($ordemServicoItemArray, $IntFieldList, $FloatFieldList, $FkList);
	
		foreach ($ordemServicoItemArray as $key => $value){
			$dados .= $strSeparador . $key.' = '.$value;
			$strSeparador = ', ';
		}
	
		if(!empty($dados)){
			$vOrdemServicoItem = $this->dao->ItemUpdate($dados, $ositoid);
	
			if(is_array($vOrdemServicoItem)){
				return Mascara::inteiro($vOrdemServicoItem['ositoid']);
			} else{
				return false;
			}
		} else{
			return false;
		}
	}
	
	/**
	 * Verifica se existe uma OS para um contrato
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @version 09/12/2013
	 * @param int $connumero
	 * @return mixed ordoid/false
	 */
	public function ordemServicoContratoGet($connumero){
		$vOrdemServico = $this->dao->ordemServicoContratoGet($connumero);
		
		if($vOrdemServico !== false){
			return Mascara::inteiro($vOrdemServico['ordoid']);
		} else{
			return false;
		}
	}
	
	/**
	 * Busca serviços cadastrados na proposta
	 *
	 * @author Rafael Dias <rafael.dias@meta.com.br>
	 * @version 02/12/2013
	 * @param int $prpoid (ID da proposta)
	 * @param int $ordoid (ID da OS)
	 * @return mixed array/false
	 */
	public function ordemServicoItensPropostaListGet($prpoid=0, $ordoid=0){
		$prpoid = Mascara::inteiro($prpoid);
		$ordoid = Mascara::inteiro($ordoid);
		
		if($prpoid > 0){
			$lista = $this->dao->ordemServicoItensPropostaListGet($prpoid);
			$retorno = array();
						
			if($lista !== false){
				foreach ($lista as $item){
					for($i=0; $i < $item['qtd']; $i++){
						unset($item['qtd']);
						$item['ositordoid'] = $ordoid;
						array_push($retorno, $item);
					}
				}
				
				return $retorno;
			} else{
				return false;	
			}
		} else{
			return false;
		}
	}
	
	/**
	 * Busca serviço de instalacao de equipamento
	 *
	 * @author Rafael Dias <rafael.dias@meta.com.br>
	 * @version 23/01/2014
	 * @param int $prpoid (ID da proposta)
	 * @param int $ordoid (ID da OS)
	 * @return mixed array/false
	 */
	public function ordemServicoItemEquipamentoListGet($prpoid,$ordoid){
		$prpoid = Mascara::inteiro($prpoid);
		$ordoid = Mascara::inteiro($ordoid);
	
		if($prpoid > 0){
			$lista = $this->dao->ordemServicoItemEquipamentoListGet($prpoid);
			$retorno = array();
	
			if($lista !== false){
				foreach ($lista as $item){
					for($i=0; $i < $item['qtd']; $i++){
						unset($item['qtd']);
						$item['ositordoid'] = $ordoid;
						array_push($retorno, $item);
					}
				}
	
				return $retorno;
			} else{
				return false;
			}
		} else{
			return false;
		}
	}
	
	/**
	 * Busca serviços cadastrados na OS.
	 * 
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @version 09/12/2013
	 * @param int $ordoid
	 * @return mixed array/false
	 */
	public function ordemServicoItensListGet($ordoid){
		$ordoid = Mascara::inteiro($ordoid);
		
		if($ordoid > 0){
			$resultSet = $this->dao->ordemServicoItensListGet($ordoid);
			$retorno = array();
			
			if($resultSet !== false){
				foreach ($resultSet as $item){
					for($i=0; $i < $item['qtd']; $i++){
						unset($item['qtd']);
						$item['ositordoid'] = $ordoid;
						array_push($retorno, $item);
					}
				}
			
				return $retorno;
			} else{
				return false;
			}
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
		$retorno = $this->dao->ordemSituacaoInsert($ordemSituacaoArray);
		return $retorno;
	}	
}