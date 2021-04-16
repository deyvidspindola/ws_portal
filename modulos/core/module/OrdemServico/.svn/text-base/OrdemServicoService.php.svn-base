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
 * @subpackage Classe Core de Ordem de Servico
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */

namespace module\OrdemServico;
use module\OrdemServico\OrdemServicoController as Controlador;

class OrdemServicoService {
            
    /**
     * Processo de geração de OS para um contrato
     *  OBS: É utilizado somente dentro do método Contrato::contratoGera(...)
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 29/11/2013
     * @param int $connumero
     * @param int $usuoid
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
    public static function ordemServicoContratoGera($connumero=0, $usuoid=0, $ordemServicoArray=array()){
        $controlador = new Controlador();
        return $controlador->ordemServicoContratoGera($connumero, $usuoid, $ordemServicoArray);
    }
    
    /**
     * Verifica se tem uma Ordem de Serviço para um determinado contrato
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 03/12/2013
     * @param int $connumero
     * @return Response $response:
     *     boolean $response->dados (true=OK /false = falha)
     *     string  $response->codigo (código do erro)
     *     string  $response->mensagem (mensagem emitida)
     */
    public static function ordemServicoContratoExiste($connumero=0){
        $controlador = new Controlador();
        return $controlador->ordemServicoContratoExiste($connumero);
    }
    
    /**
     * Cria Ordem de Serviço
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 09/12/2013
     * @param int $connumero
     * @param int $usuoid
     * @param array $ordemServicoArray(array associativo tipo chave -> valor, dados da tabela ordem_servico)
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
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
     */
    public static function ordemServicoCria($connumero=0, $usuoid=0, $ordemServicoArray=array()){
        $controlador = new Controlador();
        return $controlador->ordemServicoCria($connumero, $usuoid, $ordemServicoArray);
    }
    
    /**
     * Atualiza a Ordem de Serviço
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 03/12/2013
     * @param int $ordoid
     * @param int $usuoid
     * @param array $ordemServicoArray
     * @param array $ordemServicoArray(array associativo tipo chave -> valor, dados da tabela ordem_servico)
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
    public static function ordemServicoAtualiza($ordoid=0, $usuoid=0, $ordemServicoArray=array()){
        $controlador = new Controlador();
        return $controlador->ordemServicoAtualiza($ordoid, $usuoid, $ordemServicoArray);
    }
    
    /**
     * Inclui um item de Ordem de Serviço (function DB: ordem_servico_item_i(text))
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 03/12/2013
     * @param int $ordoid
     * @param array $ordemServicoItemArray
     *  
     * OBS-> campos obrigatórios do $ordemServicoItemArray[]:
     *     int ositotioid -> Motivo
     *     int ositordoid -> Ordem
     *     int ositeqcoid -> Classe
     *     string ositobs -> Observação
     *     
     * @return Response $response:
     *     boolean $response->dados (true=OK /false = falha)
     *     string  $response->codigo (código do erro)
     *     string  $response->mensagem (mensagem emitida)
     */
    public static function ordemServicoItemInclui($ordoid=0, $ordemServicoItemArray=array()){
        $controlador = new Controlador();
        return $controlador->ordemServicoItemInclui($ordoid, $ordemServicoItemArray);
    }
    
    /**
     * Atualiza um item de Ordem de Serviço
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 24/01/2014
     * @param int $ositoid
     * @param array $ordemServicoItemArray
     *
     * OBS-> campos obrigatórios do $ordemServicoItemArray[]:
     *     int ositotioid -> Motivo
     *     int ositordoid -> Ordem
     *     int ositeqcoid -> Classe
     *     string ositobs -> Observação
     *
     * @return Response $response:
     *     boolean $response->dados (true=OK /false = falha)
     *     string  $response->codigo (código do erro)
     *     string  $response->mensagem (mensagem emitida)
     */
    public static function ordemServicoItemAtualiza($ositoid=0, $ordemServicoItemArray=array()){
    	$controlador = new Controlador();
    	return $controlador->ordemServicoItemAtualiza($ositoid, $ordemServicoItemArray);
    }    
    
    /**
     * Lista itens de Ordem de Serviço
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 03/12/2013
     * @param int $ordoid
     * @param int $connumero
     * @throws \Exception
     * @return Response $response:
     *     boolean $response->dados (true=OK /false = falha)
     *     string  $response->codigo (código do erro)
     *     string  $response->mensagem (mensagem emitida)
     */
    public static function ordemServicoItensLista($ordoid=0, $connumero=0){
        $controlador = new Controlador();
        return $controlador->ordemServicoItensLista($ordoid, $connumero);
    }
    
    /**
     * Inclui registro em ordem_situacao
     *
     * @author Rafael Dias <rafael.dias@meta.com.br>
     * @version 03/12/2013
     * @param int $ordoid
     * @param int $usuoid
     * @param array $ordemSituacaoArray
     * OBS-> campos obrigatórios do $ordemServicoItemArray[]:
     *     int orsordoid
     *     string orssituacao
     *     
     * @return Response $response:
     *     boolean $response->dados (true=OK /false = falha)
     *     string  $response->codigo (código do erro)
     *     string  $response->mensagem (mensagem emitida)
     */
    public static function ordemSituacaoInclui($ordoid=0, $usuoid=0, $ordemSituacaoArray=array()){
        $controlador = new Controlador();
        return $controlador->ordemSituacaoInclui($ordoid, $usuoid, $ordemSituacaoArray);
    }
}