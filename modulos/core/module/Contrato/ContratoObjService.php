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
 * @subpackage Classe Core de Contrato
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */

namespace module\Contrato;
use module\Contrato\ContratoController as Controlador;

class ContratoObjService{
    
    // MÉTODOS RELACIONADOS A PROPOSTA
    
    /**
     * Método para criar uma proposta com status Z = em elaboração
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 16/09/2013
     * @param int $prptppoid (modalidade)
     * @param int $prptpcoid (tipo de contrato, tabela tipo_contrato)
     * @param int $prpusuoid (usuário que criou a proposta)
     * @return Response $response:
     *     mixed $response->dados ($prpoid=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaCria($prptppoid=0, $prptpcoid=0, $prpusuoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaCria($prptppoid, $prptpcoid, $prpusuoid);
    }
    
    /** 
     * Insere registro no histórico de proposta
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 17/09/2013 
     * @param int $prphprpoid (ID da proposta)
     * @param int $prphusuoid (ID do usuário)
     * @param string $prphobs (Observação)
     * @return Response $response:
     *     boolean $response->dados (true=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaGravaHistorico($prphprpoid=0, $prphusuoid=0, $prphobs='') {
        $contrato = new Controlador();
        return $contrato->propostaGravaHistorico($prphprpoid, $prphusuoid, $prphobs);
    }
    
    /**
     * Atualiza dados de uma proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 18/09/2013 
     * @param int $prpoid (ID da proposta)
     * @param array $propostaArray array associativo com dados da proposta
     *     AVISO: a seleção dos pares chave => valor devem estar de acordo com a tabela proposta
     *     e a validação de consistencia dos dados atualizados é de responsabilidade da camada de negócio.
     * @return Response $response:
     *     mixed $response->dados ($prpoid=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaAtualiza($prpoid=0, $propostaArray=array()) {
        $contrato = new Controlador();
        return $contrato->propostaAtualiza($prpoid, $propostaArray);
    }
    
    /**
     * Apenas verifica se uma prpoid existe.
     * 
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 18/09/2013 
     * @param int $prpoid (ID da proposta)
     * @return Response $response:
     *     boolean $response->dados (true=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaExiste($prpoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaExiste($prpoid);
    }
    
    /**
     * Liga cliente a proposta
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 19/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $clioid (ID do cliente)
     * @return Response $response:
     *     mixed $response->dados ($prpclioid=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaSetaCliente($prpoid=0, $clioid=0) {
        $contrato = new Controlador();
        return $contrato->propostaSetaCliente($prpoid, $clioid);
    }
    
    /**
     * Inclui um item de proposta
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 19/09/2013 
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (ID do usuario que incluiu o item)
     * @param array $propostaItemArray (array associativo tipo chave -> valor, dados da tabela proposta_item)
     *     OBS-> campos obrigatórios do $propostaItemArray[]: 
     *       int pritobjeto -> ID do objeto rastreavel
     *       char(1) prittipo -> Tipo (V=veículo C=Carga)
     *       int pritquantidade -> quantidade de itens (por default = 1)
     *       float pritvl_parcelamonitoramento -> valor da parcela de monitoramento
     *       float pritvl_parcelalocacao -> valor da parcela de locação
     * @return Response $response:
     *     mixed $response->dados ($pritoid=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaItemInclui($prpoid=0, $usuoid=0, $propostaItemArray=array()) {
        $contrato = new Controlador();
        return $contrato->propostaItemInclui($prpoid, $usuoid, $propostaItemArray);
    }
    
    /**
     * Atualiza dados de um item de proposta
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 19/09/2013 
     * @param int $pritoid (ID do ITEM da proposta)
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (ID do usuario que incluiu o item)
     * @param array $propostaItemArray (array associativo tipo chave -> valor, dados da tabela proposta_item)
     *     OBS-> campos obrigatórios do $propostaItemArray[]: 
     *       int pritobjeto
     *       char(1) prittipo
     *       int pritquantidade
     *       float pritvl_parcelamonitoramento -> valor da parcela de monitoramento
     *       float pritvl_parcelalocacao -> valor da parcela de locação
     * @return Response $response:
     *     mixed $response->dados ($pritoid=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaItemAtualiza($pritoid=0, $prpoid=0, $usuoid=0, $propostaItemArray) {
        $contrato = new Controlador();
        return $contrato->propostaItemAtualiza($pritoid, $prpoid, $usuoid, $propostaItemArray);
    }


    /**
     * Busca Lista de itens da Proposta
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 19/09/2013
     * @param int $prpoid (ID da proposta)
     * @return Response $response:
     *     mixed $response->dados (array de ITENS da proposta=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaItemLista($prpoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaItemLista($prpoid);
    }    
    
    /**
     * Busca dados de uma proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @return Response $response:
     *     mixed $response->dados (array com dados da proposta=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaBuscaDados($prpoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaBuscaDados($prpoid);
    }
    
    /**
     * Vincula o produto(classe de contrato) a proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (usuário)
     * @return Response $response:
     *     mixed $response->dados ($prpoid=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaSetaProduto($prpoid=0, $usuoid=0, $propostaProdutoArray=array()) {
        $contrato = new Controlador();
        return $contrato->propostaSetaProduto($prpoid, $usuoid, $propostaProdutoArray);
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
     *     int prpforcoid -> forma de cobrança
     *     int prpdia_vcto -> dia do vencimento
     *     int cpvoid => parcelamento (ID da tabela cond_pgto_venda)
     *     int obroid_servico => obrigação financeira (CLASSE CONTRATADA)
     *     float vl_servico => valor parcela locacao
     *     float prppercentual_desconto_locacao -> percentual desconto locação
     *     float vl_monitoramento -> valor do monitoramento
     *     int prpprazo_contrato -> vigência do contrato
     *     float prpagmulta_rescissoria -> valor multa resisória
     *     
     *     OBS: campos float formato numeric(12,2) com "." como separador de decimais
     *          ex: 200.25
     * @return Response $response:
     *     mixed $response->dados ($prpoid=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaSetaPagamento($prpoid=0, $usuoid=0, $propostaPagamentoArray=array()) {
        $contrato = new Controlador();
        return $contrato->propostaSetaPagamento($prpoid, $usuoid, $propostaPagamentoArray);
    }
   
    
    /**
     * Inclui um acessório na proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (usuário)
     * @param mixed $prospritoid (item ao qual o acessório é adicionado, caso valor 't' adiciona em todos os itens da proposta)
     * @param array $propostaAcessorioArray (array com dados)
     *     OBS-> campos obrigatórios do $propostaAcessorioArray[]: 
     *     int prosobroid -> ID da obrigação financeira do serviço/acessório a ser adicionado
     *     string prossituacao -> Situação : "L" Locação, "C" Cortesia , "D" Demonstação, "L" Cliente, "B" Básico e "M" COMODATO. 
     *     float prosvalor => Valor que o Serviço foi Negociado com o Cliente
     *     boolean prosinstalar => true/false
     *     string prosmotivo_naoinstalar => caso prosinstalar = false, qual o motivo
     *     int prosqtde -> quantidade do item adicionado
     *     
     *     OBS: campos float formato numeric(12,2) com "." como separador de decimais
     *          ex: 200.25
     * @return Response $response:
     *     mixed $response->dados (array com prosoid's=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaAcessorioInclui($prpoid=0, $usuoid=0, $prospritoid='t', $propostaAcessorioArray=array()) {
        $contrato = new Controlador();
        return $contrato->propostaAcessorioInclui($prpoid, $usuoid, $prospritoid, $propostaAcessorioArray);
    }
    
    /**
     * Exclui/remove um acessório da proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prosoid (ID da proposta_servico)
     * @param int $usuoid (usuário)
     * @return Response $response:
     *     boolean $response->dados (true=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaAcessorioExclui($prosoid=0, $usuoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaAcessorioExclui($prosoid, $usuoid);
    }
    
    /**
     * Busca a lista de acessórios da proposta.
     *     OBS: busca todos os serviços onde prossituacao = M
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     *     OBS: busca todos os serviços onde prossituacao != M e prossituacao != B
     * @return Response $response:
     *     mixed $response->dados (array com todos os acessórios da proposta=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaAcessorioLista($prpoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaAcessorioLista($prpoid);
    }
    
    
    /**
     * Busca a lista de acessórios da de um ITEM de proposta.
     *     OBS: busca todos os do ITEM serviços onde prossituacao = M
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $pritoid (ID do ITEM)
     * @return array array com todos os dados de acessórios da proposta
     *     OBS: busca todos os serviços onde prossituacao != M e prossituacao != B
     * @return Response $response:
     *     mixed $response->dados (array com todos os acessórios do ITEM da proposta=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaItemAcessorioLista($prpoid=0, $pritoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaItemAcessorioLista($prpoid, $pritoid);
    }
    
    /**
     * Inclui um opcional na proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (usuário)
     * @param mixed $prospritoid (item ao qual o acessório é adicionado, caso valor 't' adiciona em todos os itens da proposta)
     * @param array $propostaOpcionalArray (array com dados do item opcional)
     *     OBS-> campos obrigatórios do $propostaOpcionalArray[]: 
     *     int prosobroid -> ID da obrugação financeira do serviço/acessório a ser adicionado
     *     float prosvalor -> Valor que o Serviço foi Negociado com o Cliente
     *     boolean prosvalor_agregado_monitoramento -> indica que o valor é diluido no valor do monitoramento
     * @return Response $response:
     *     mixed $response->dados (array com prosoid's=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaOpcionalInclui($prpoid=0, $usuoid=0, $prospritoid='t', $propostaOpcionalArray=array()) {
        $contrato = new Controlador();
        return $contrato->propostaOpcionalInclui($prpoid, $usuoid, $prospritoid, $propostaOpcionalArray);
    }
    
    /**
     * Exclui/remove um opcional da proposta/item.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $prosoid (ID da proposta_servico)
     * @param int $usuoid (usuário)
     * @return Response $response:
     *     boolean $response->dados (true=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaOpcionalExclui($prpoid=0, $prosoid=0, $usuoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaOpcionalExclui($prpoid, $prosoid, $usuoid);
    }

    
    /**
     * Busca lista de opcionais da proposta.
     *     OBS: busca todos os serviços da proposta onde prossituacao = M
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @return Response $response:
     *     mixed $response->dados (array com prosoid's da PROPOSTA=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaOpcionalLista($prpoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaOpcionalLista($prpoid);
    }


    /**
     * Busca lista de opcionais do ITEM da proposta.
     *     OBS: busca todos os serviços mensais do item onde prossituacao = M
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $pritoid (ID do ITEM)
     * @return Response $response:
     *     mixed $response->dados (array com prosoid's do ITEM da proposta=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaItemOpcionalLista($prpoid=0, $pritoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaItemOpcionalLista($prpoid, $pritoid);
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
     *         int execcontas (ID do funcionario/representante)
     *         int prpregcoid (ID região comercial)
     *         int prprczoid (ID zona comercial)
     *         int telemkt (ID televendas)
     *         int prpcorroid -> (ID corretor)
     * @return Response $response:
     *     mixed $response->dados ($prpoid=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaSetaComercial($prpoid=0, $usuoid=0, $propostaComercialArray=array()) {
        $contrato = new Controlador();
        return $contrato->propostaSetaComercial($prpoid, $usuoid, $propostaComercialArray);
    }


    /**
     * Busca dados comerciais da proposta.
     *     OBS: retorna uma matriz completa com dados do comercial
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @return Response $response:
     *     mixed $response->dados (array com dados comerciais=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaComercialBuscaDados($prpoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaComercialBuscaDados($prpoid);
    }
    

    /**
     * Inclui uma gerenciadora.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (usuário)
     * @param int $prggeroid (ID da GERENCIADORA)
     * @param int $prgsequencia (Sequência da gerenciadora)
     * @return Response $response:
     *     mixed $response->dados ($prgoid=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaGerenciadoraInclui($prpoid=0, $usuoid=0, $prggeroid=0, $prgsequencia=0) {
        $contrato = new Controlador();
        return $contrato->propostaGerenciadoraInclui($prpoid, $usuoid, $prggeroid, $prgsequencia);
    }
    
    /**
     * Exclui/remove uma gerenciadora.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $prgoid (ID da gerenciadora)
     * @return Response $response:
     *     boolean $response->dados (true=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaGerenciadoraExclui($prpoid=0, $prgoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaGerenciadoraExclui($prpoid, $prgoid);
    }
    
    /**
     * Retorna array com lista de dados das gerenciadoras vinculadas na proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @return Response $response:
     *     mixed $response->dados (array com dados de gerenciadoras=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaGerenciadoraLista($prpoid) {
        $contrato = new Controlador();
        return $contrato->propostaGerenciadoraLista($prpoid);
    }
    
   /**
     * Grava status e demais informações do financeiro na proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (ID do usuário que inseriu a informação)
     * @param int $prppsfoid (ID do Status Financeiro conforme tabela proposta_status_financeiro)
     * @param strint $prpobservacao_financeiro (Observação referente a condição/financeira)
     * @param string $prpresultado_aciap (Strint contendo resultado da consulta)
     * @return Response $response:
     *     mixed $response->dados ($prpoid=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaSetaFinanceiro($prpoid=0, $usuoid=0, $prppsfoid=0, $prpobservacao_financeiro='', $prpresultado_aciap='') {
        $contrato = new Controlador();
        return $contrato->propostaSetaFinanceiro($prpoid, $usuoid, $prppsfoid, $prpobservacao_financeiro, $prpresultado_aciap);
    }
    
    /**
     * Busca dados financeiros da proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @return Response $response:
     *     mixed $response->dados (array com dados financeiros=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaFinanceiroBuscaDados($prpoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaFinanceiroBuscaDados($prpoid);
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
     *         string prcrg (RG do contato)
     *         string prcfone_res (fone residencial)
     *         string prcfone_com (fone comercial)
     *         string prcobs (observação)
     * @return Response $response:
     *     mixed $response->dados ($prcoid=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaContatoInclui($prpoid=0, $propostaContatoArray=array()) {
        $contrato = new Controlador();
        return $contrato->propostaContatoInclui($prpoid, $propostaContatoArray);
    }
    
    /**
     * Exclui/remove um registro de contato.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 14/10/2013
     * @param int $prpoid (ID da proposta)
     * @param int $prcoid (ID do contato)
     * @return Response $response:
     *     mixed $response->dados (true=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaContatoExclui($prpoid=0, $prcoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaContatoExclui($prpoid, $prcoid);
    }
    
    /**
     * Retorna array com lista de contatos de um tipo .
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 14/10/2013
     * @param int $prpoid (ID da proposta)
     * @param char $prctipo (tipo do contato 'A'/'E'/'I')
     * @return Response $response:
     *     mixed $response->dados (array com lista de contatos=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaContatoLista($prpoid=0, $prctipo='A') {
        $contrato = new Controlador();
        return $contrato->propostaContatoLista($prpoid, $prctipo);
    }
    
    /**
     * Grava/seta o status da proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 14/10/2013
     * @param int $prpoid (ID da proposta)
     * @param char $prpstatus (status da proposta: P=Pendente,R=Aguardando Retorno,C=Concluído,E=Cancelado)
     * @return Response $response:
     *     mixed $response->dados ($prpstatus=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaSetaStatus($prpoid=0, $prpstatus='P') {
        $contrato = new Controlador();
        return $contrato->propostaSetaStatus($prpoid, $prpstatus);
    }
    
    /**
     * Retorna status da proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 14/10/2013
     * @param int $prpoid (ID da proposta)
     * @return Response $response:
     *     mixed $response->dados ($prpstatus=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaBuscaStatus($prpoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaBuscaStatus($prpoid);
    }
    
    
    /**
     * Verifica pendências da proposta.
     *     OBS: realiza uma série de verificações e retorna true caso passe em todas as etapas
     *     ou uma lista de códigos onde cada código representa uma falha/pendência.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 14/10/2013
     * @param int $prpoid (ID da proposta)
     * @return Response $response:
     *     mixed $response->dados (true=OK /array de códigos de pendências = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function propostaVerificaPendencias($prpoid=0) {
        $contrato = new Controlador();
        //return $contrato->propostaVerificaPendencias($prpoid);
        return true;
    }
  
/*
      MÉTODOS RELATIVOS A CONTRATO 
*/

    
    /**
     * Gera todos os contratos a partir de uma proposta, baseados no proposta ítem.
     *     OBSs: - retorna pelo menos 1 (um) contrato;
     *           - controle de transações é parametrizável;
     *           - geração de O.S. é parametrizável.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 14/11/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (ID do usuário executou a geração dos contratos)
     * @param boolean $controlaTransacao (true/false determina se transfere o controle de transações para o core)
     * @param boolean $geraOS (true/false determina de gera ou não Ordem de Serviço)
     * @return Response $response:
     *     mixed $response->dados (array com lista de connumeros=OK /false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public function contratoGera($prpoid=0, $usuoid=0, $controlaTransacao=false, $geraOS=true) {
        $contrato = new Controlador();
        return $contrato->contratoGera($prpoid, $usuoid, $controlaTransacao, $geraOS);
    }
    
    public function contratoLista($prpoid=0) {
        return true;
    }


    
}