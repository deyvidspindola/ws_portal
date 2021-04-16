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
 * 
 *     OBS: todos os par�metros definidos nas assinaturas de m�todos s�o requeridos
 */

namespace module\Contrato;
use module\Contrato\ContratoController as Controlador;

class ContratoService{
    
    // M�TODOS RELACIONADOS A PROPOSTA
    
    /**
     * M�todo est�tico para criar uma proposta com status Z = em elabora��o
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 16/09/2013
     * @param int $prptppoid (modalidade)
     * @param int $prptpcoid (tipo de contrato, tabela tipo_contrato)
     * @param int $prpusuoid (usu�rio que criou a proposta)
     * @return Response $response:
     *     mixed $response->dados ($prpoid=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaCria($prptppoid=0, $prptpcoid=0, $prpusuoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaCria($prptppoid, $prptpcoid, $prpusuoid);
    }
    
    /** 
     * Insere registro no hist�rico de proposta
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 17/09/2013 
     * @param int $prphprpoid (ID da proposta)
     * @param int $prphusuoid (ID do usu�rio)
     * @param string $prphobs (Observa��o)
     * @return Response $response:
     *     boolean $response->dados (true=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaGravaHistorico($prphprpoid=0, $prphusuoid=0, $prphobs='') {
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
     *     AVISO: a sele��o dos pares chave => valor devem estar de acordo com a tabela proposta
     *     e a valida��o de consist�ncia dos dados atualizados � de responsabilidade da camada de neg�cio.
     *     Entradas obrigat�rias conforme necessidades neg�cio.
     * @return Response $response:
     *     mixed $response->dados ($prpoid=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaAtualiza($prpoid=0, $propostaArray=array()) {
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
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaExiste($prpoid=0) {
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
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaSetaCliente($prpoid=0, $clioid=0) {
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
     *     OBS-> campos obrigat�rios do $propostaItemArray[]: 
     *       int pritobjeto -> ID do objeto rastre�vel
     *       char(1) prittipo -> Tipo (V=ve�culo C=Carga)
     *       int pritquantidade -> quantidade de itens (por default = 1)
     *       float pritvl_parcelamonitoramento -> valor da parcela de monitoramento
     *       float pritvl_parcelalocacao -> valor da parcela de loca��o
     *     OBS-> campos N�O obrigat�rios do $propostaItemArray[]: 
     *       N/A
     * @return Response $response:
     *     mixed $response->dados ($pritoid=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaItemInclui($prpoid=0, $usuoid=0, $propostaItemArray=array()) {
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
     *     OBS-> campos obrigat�rios do $propostaItemArray[]: 
     *       int pritobjeto
     *       char(1) prittipo
     *       int pritquantidade
     *       float pritvl_parcelamonitoramento -> valor da parcela de monitoramento
     *       float pritvl_parcelalocacao -> valor da parcela de loca��o
     *     OBS-> campos N�O obrigat�rios do $propostaItemArray[]: 
     *       N/A
     * @return Response $response:
     *     mixed $response->dados ($pritoid=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaItemAtualiza($pritoid=0, $prpoid=0, $usuoid=0, $propostaItemArray) {
        $contrato = new Controlador();
        return $contrato->propostaItemAtualiza($pritoid, $prpoid, $usuoid, $propostaItemArray);
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
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
     */
    public static function propostaItemExclui($pritoid=0, $usuoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaItemExclui($pritoid, $usuoid);
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
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
     */
    public static function propostaItensExclui($prpoid=0, $usuoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaItensExclui($prpoid, $usuoid);
    }

    /**
     * Busca Lista de itens da Proposta
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 19/09/2013
     * @param int $prpoid (ID da proposta)
     * @return Response $response:
     *     mixed $response->dados (array de ITENS da proposta=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaItemLista($prpoid=0) {
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
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaBuscaDados($prpoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaBuscaDados($prpoid);
    }
    
    /**
     * Vincula o produto(classe de contrato) a proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (usu�rio)
     * @param array $propostaProdutoArray (array de dados do produto)
     *   OBS-> campos obrigat�rios do $propostaProdutoArray[]: 
     *     int prpeqcoid -> classe de equipamento/produto
     *   OBS-> campos N�O obrigat�rios do $propostaProdutoArray[]: 
     *       N/A
     * @return Response $response:
     *     mixed $response->dados ($prpoid=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaSetaProduto($prpoid=0, $usuoid=0, $propostaProdutoArray=array()) {
        $contrato = new Controlador();
        return $contrato->propostaSetaProduto($prpoid, $usuoid, $propostaProdutoArray);
    }
    
    /**
     * Vincula dado do pagamento a proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (usu�rio)
     * @param array $propostaPagamentoArray (array com dados)
     *   OBS-> campos obrigat�rios do $propostaPagamentoArray[]: 
     *     int prpforcoid -> forma de cobran�a
     *     int prpdia_vcto -> dia do vencimento
     *     int cpvoid => parcelamento (ID da tabela cond_pgto_venda)
     *     int obroid_servico => obriga��o financeira (CLASSE CONTRATADA)
     *     float vl_servico => valor parcela locacao
     *     float prppercentual_desconto_locacao -> percentual desconto loca��o
     *     float vl_monitoramento -> valor do monitoramento
     *     int prpprazo_contrato -> vig�ncia do contrato
     *     float prpagmulta_rescissoria -> valor multa resis�ria
     *   OBS-> campos N�O obrigat�rios do $propostaPagamentoArray[]: 
     *     float adesao -> Valor da taxa de ades�o
     *     int adesao_parcela -> N�mero de parcelas para pagamento da taxa de ades�o
     *     int forcoid_adesao -> ID forma de cobran�a da taxa de ades�o, chave estrangeira com a tabela forma_cobran�a FOREIGN KEY
     *     float ppagtvltx_instalacao -> valor da taxa de instala��o
     *     
     *   OBS: campos float formato numeric(12,2) com "." como separador de decimais
     *          ex: 200.25
     * @return Response $response:
     *     mixed $response->dados ($prpoid=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaSetaPagamento($prpoid=0, $usuoid=0, $propostaPagamentoArray=array()) {
        $contrato = new Controlador();
        return $contrato->propostaSetaPagamento($prpoid, $usuoid, $propostaPagamentoArray);
    }
   
    
    /**
     * Inclui um acess�rio na proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (usu�rio)
     * @param mixed $prospritoid (item ao qual o acess�rio � adicionado, caso valor 't' adiciona em todos os itens da proposta)
     * @param array $propostaAcessorioArray (array com dados)
     *     OBS-> campos obrigat�rios do $propostaAcessorioArray[]: 
     *     int prosobroid -> ID da obriga��o financeira do servi�o/acess�rio a ser adicionado
     *     string prossituacao -> Situa��o : "L" Loca��o, "C" Cortesia , "D" Demonsta��o, "L" Cliente, "B" B�sico e "M" COMODATO. 
     *     float prosvalor => Valor que o Servi�o foi Negociado com o Cliente
     *     boolean prosinstalar => true/false
     *     string prosmotivo_naoinstalar => caso prosinstalar = false, qual o motivo
     *     int prosqtde -> quantidade do item adicionado
     *     OBS-> campos N�O obrigat�rios do $propostaAcessorioArray[]: 
     *         N/A
     *     OBS: campos float formato numeric(12,2) com "." como separador de decimais
     *          ex: 200.25
     * @return Response $response:
     *     mixed $response->dados (array com prosoid's=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaAcessorioInclui($prpoid=0, $usuoid=0, $prospritoid='t', $propostaAcessorioArray=array()) {
        $contrato = new Controlador();
        return $contrato->propostaAcessorioInclui($prpoid, $usuoid, $prospritoid, $propostaAcessorioArray);
    }
    
    /**
     * Exclui/remove um acess�rio da proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prosoid (ID da proposta_servico)
     * @param int $usuoid (usu�rio)
     * @return Response $response:
     *     boolean $response->dados (true=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaAcessorioExclui($prosoid=0, $usuoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaAcessorioExclui($prosoid, $usuoid);
    }
    
    /**
     * Busca a lista de acess�rios da proposta.
     *     OBS: busca todos os servi�os onde prossituacao = M
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     *     OBS: busca todos os servi�os onde prossituacao != M e prossituacao != B
     * @return Response $response:
     *     mixed $response->dados (array com todos os acess�rios da proposta=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaAcessorioLista($prpoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaAcessorioLista($prpoid);
    }
    
    
    /**
     * Busca a lista de acess�rios da de um ITEM de proposta.
     *     OBS: busca todos os do ITEM servi�os onde prossituacao = M
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $pritoid (ID do ITEM)
     * @return array array com todos os dados de acess�rios da proposta
     *     OBS: busca todos os servi�os onde prossituacao != M e prossituacao != B
     * @return Response $response:
     *     mixed $response->dados (array com todos os acess�rios do ITEM da proposta=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaItemAcessorioLista($prpoid=0, $pritoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaItemAcessorioLista($prpoid, $pritoid);
    }
    
    /**
     * Inclui um opcional na proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (usu�rio)
     * @param mixed $prospritoid (item ao qual o acess�rio � adicionado, caso valor 't' adiciona em todos os itens da proposta)
     * @param array $propostaOpcionalArray (array com dados do item opcional)
     *     OBS-> campos obrigat�rios do $propostaOpcionalArray[]: 
     *     int prosobroid -> ID da obruga��o financeira do servi�o/acess�rio a ser adicionado
     *     float prosvalor -> Valor que o Servi�o foi Negociado com o Cliente
     *     boolean prosvalor_agregado_monitoramento -> indica que o valor � diluido no valor do monitoramento
     *     OBS-> campos N�O obrigat�rios do $propostaOpcionalArray[]: 
     *         N/A
     * @return Response $response:
     *     mixed $response->dados (array com prosoid's=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaOpcionalInclui($prpoid=0, $usuoid=0, $prospritoid='t', $propostaOpcionalArray=array()) {
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
     * @param int $usuoid (usu�rio)
     * @return Response $response:
     *     boolean $response->dados (true=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaOpcionalExclui($prpoid=0, $prosoid=0, $usuoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaOpcionalExclui($prpoid, $prosoid, $usuoid);
    }

    
    /**
     * Busca lista de opcionais da proposta.
     *     OBS: busca todos os servi�os da proposta onde prossituacao = M
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @return Response $response:
     *     mixed $response->dados (array com prosoid's da PROPOSTA=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaOpcionalLista($prpoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaOpcionalLista($prpoid);
    }


    /**
     * Busca lista de opcionais do ITEM da proposta.
     *     OBS: busca todos os servi�os mensais do item onde prossituacao = M
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $pritoid (ID do ITEM)
     * @return Response $response:
     *     mixed $response->dados (array com prosoid's do ITEM da proposta=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaItemOpcionalLista($prpoid=0, $pritoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaItemOpcionalLista($prpoid, $pritoid);
    }
    
    
    /**
     * Grava/atualiza dados comerciais da proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (usu�rio)
     * @param array $propostaComercialArray (array com dados comercial)
     *     OBS-> campos obrigat�rios do $propostaComercialArray[]: 
     *         N/A
     *     OBS-> campos opcionais do $propostaComercialArray[]: 
     *         int execcontas (ID do funcionario/representante)
     *         int prpregcoid (ID regi�o comercial)
     *         int prprczoid (ID zona comercial)
     *         int telemkt (ID televendas)
     *         int prpcorroid -> (ID corretor)
     * @return Response $response:
     *     mixed $response->dados ($prpoid=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaSetaComercial($prpoid=0, $usuoid=0, $propostaComercialArray=array()) {
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
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaComercialBuscaDados($prpoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaComercialBuscaDados($prpoid);
    }
    

    /**
     * Inclui uma gerenciadora.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (usu�rio)
     * @param int $prggeroid (ID da GERENCIADORA)
     * @param int $prgsequencia (Sequ�ncia da gerenciadora)
     * @return Response $response:
     *     mixed $response->dados ($prgoid=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaGerenciadoraInclui($prpoid=0, $usuoid=0, $prggeroid=0, $prgsequencia=0) {
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
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaGerenciadoraExclui($prpoid=0, $prgoid=0) {
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
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaGerenciadoraLista($prpoid) {
        $contrato = new Controlador();
        return $contrato->propostaGerenciadoraLista($prpoid);
    }
    
   /**
     * Grava status e demais informa��es do financeiro na proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 20/09/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (ID do usu�rio que inseriu a informa��o)
     * @param int $prppsfoid (ID do Status Financeiro conforme tabela proposta_status_financeiro)
     * @param strint $prpobservacao_financeiro (Observa��o referente a condi��o/financeira)
     * @param string $prpresultado_aciap (Strint contendo resultado da consulta)
     * @return Response $response:
     *     mixed $response->dados ($prpoid=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaSetaFinanceiro($prpoid=0, $usuoid=0, $prppsfoid=0, $prpobservacao_financeiro='', $prpresultado_aciap='') {
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
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaFinanceiroBuscaDados($prpoid=0) {
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
     *     OBS-> campos obrigat�rios do $propostaContatoArray[]: 
     *         char prctipo (tipo do contato: (A)utorizados , (E)mergencia , (I)nstalacao)
     *         string prcnome (nome do contato)
     *         string prccpf (CPF do contato)
     *         string prcfone_cel (Telefone Celular)
     *         
     *     OBS-> campos opcionais do $propostaContatoArray[]: 
     *         string prcrg (RG do contato)
     *         string prcfone_res (fone residencial)
     *         string prcfone_com (fone comercial)
     *         string prcobs (observa��o)
     * @return Response $response:
     *     mixed $response->dados ($prcoid=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaContatoInclui($prpoid=0, $propostaContatoArray=array()) {
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
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaContatoExclui($prpoid=0, $prcoid=0) {
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
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaContatoLista($prpoid=0, $prctipo='A') {
        $contrato = new Controlador();
        return $contrato->propostaContatoLista($prpoid, $prctipo);
    }
    
    /**
     * Grava/seta o status da proposta.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 14/10/2013
     * @param int $prpoid (ID da proposta)
     * @param char $prpstatus (status da proposta: P=Pendente,R=Aguardando Retorno,C=Conclu�do,E=Cancelado,L=Aguardando An�lise Financeira,T=Aguardando An�lise T�cnica)
     * @return Response $response:
     *     mixed $response->dados ($prpstatus=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaSetaStatus($prpoid=0, $prpstatus='P') {
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
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaBuscaStatus($prpoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaBuscaStatus($prpoid);
    }
    
    
    /**
     * Verifica pend�ncias da proposta.
     *     OBS: realiza uma s�rie de verifica��es e retorna true caso passe em todas as etapas
     *     ou uma lista de c�digos onde cada c�digo representa uma falha/pend�ncia.
     *     $retorno->dados['P01'] = 'Modalidade de proposta n�o informada.';
     *     $retorno->dados['P02'] = 'Tipo de contrato n�o informado.';
     *     $retorno->dados['P03'] = 'Cliente n�o informado.';
     *     $retorno->dados['P04'] = 'Classe/Produto n�o informado.';
     *     ....
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 14/10/2013
     * @param int $prpoid (ID da proposta)
     * @return Response $response:
     *     mixed $response->dados (true=OK /c�digo de erro = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function propostaVerificaPendencias($prpoid=0) {
        $contrato = new Controlador();
        return $contrato->propostaVerificaPendencias($prpoid);
    }
    
    /**
     * Verifica se a classe/produto informado n�o � nulo e se faz
     * parte dos produtos ativos da Sascar.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 09/01/2014
     * @param int $prpeqcoid (ID Equipamento Classe)
     * @return Response $response:
     *     mixed $response->dados (true=OK /c�digo de erro = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
     */
    public static function propostaValidaClasse($prpeqcoid=0){
        $contrato = new Controlador();
        return $contrato->propostaValidaClasse($prpeqcoid);
    }

    /**
     * Vincula o n�mero externo que vem do SalesForce a proposta.
     *
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 10/01/2014
     * @param int $prpoid (ID Proposta)
     * @param int $prpnumero_externo (N�mero externo que vem do SalesForce)
     * @return Response $response:
     *     mixed $response->dados (prpoid=OK /c�digo de erro = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
     */
    public static function propostaSetaReferenciaExterna($prpoid=0, $prpnumero_externo=0){
        $contrato = new Controlador();
        return $contrato->propostaSetaReferenciaExterna($prpoid, $prpnumero_externo);
    }


    /**
     * Atualiza corretor indicador
     * @author Vinicius Senna <vsenna@brq.com>
     * @version 2/4/2014
     * @param int $prpoid (ID da proposta)
     * @param int $$prpcorroid (ID do corretor)
     * @return Response $response:
     *     mixed $response->dados (prpoid=OK /c�digo de erro = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
     */
    public function propostaGravaCorretorIndicador($prpoid=0, $prprcorroid=0) {
        $contrato = new Controlador();
        return $contrato->propostaGravaCorretorIndicador($prpoid, $prprcorroid);
    }
  
    /*
    * M�TODOS RELATIVOS A CONTRATO ***********************************************
    */

    
    /**
     * Gera todos os contratos a partir de uma proposta, baseados no proposta �tem.
     *     OBSs: - retorna pelo menos 1 (um) contrato;
     *           - controle de transa��es � parametriz�vel;
     *           - gera��o de O.S. � parametriz�vel.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 14/11/2013
     * @param int $prpoid (ID da proposta)
     * @param int $usuoid (ID do usu�rio executou a gera��o dos contratos)
     * @param boolean $controlaTransacao (true/false determina se transfere o controle de transa��es para o core)
     * @param boolean $geraOS (true/false determina de gera ou n�o Ordem de Servi�o)
     * @return Response $response:
     *     mixed $response->dados (array com lista de connumeros=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function contratoGera($prpoid=0, $usuoid=0, $controlaTransacao=false, $geraOS=true) {
        $contrato = new Controlador();
        return $contrato->contratoGera($prpoid, $usuoid, $controlaTransacao, $geraOS);
    }
    
    
   /**
     * Retorna a lista de 1 ou + contratos gerados a partir de uma proposta.
     * 
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 07/12/2013
     * @param int $prpoid (ID da proposta)
     * @return Response $response:
     *     mixed $response->dados (array com lista de connumeros=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function contratoLista($prpoid=0) {
        $contrato = new Controlador();
        return $contrato->contratoLista($prpoid);
    }

	/**
	 * Recebe n�mero da proposta que gerou o contrato
	 * 
	 * @author Rafael Dias <rafael.dias@meta.com.br>
	 * @param int $connumero (n�mero do contrato)
     * @return Response $response:
     *     mixed $response->dados (prpoid=OK /false = falha)
     *     string $response->codigo (c�digo do erro)
     *     string $response->mensagem (mensagem emitida)
	 */
	public static function contratoPropostaBusca($connumero=0){
		$contrato = new Controlador();
		return $contrato->contratoPropostaBusca($connumero);
	}
    
}