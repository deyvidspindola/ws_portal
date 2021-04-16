<?php

/**
 * PrnManutencaoFormaCobrancaCliente.php
 *
 * Classe para fazer alteração da forma de cobrança de um determinado cliente previamente 
 * cadastrado na base de dados.
 *
 *
 * @author	Diego C. Ribeiro
 * @email dcribeiro@brq.com
 * @since 17/10/2012
 * @STI 80219
 * @package Principal
 *
 */
// require para persistência de dados - classe DAO
require _MODULEDIR_ . 'Principal/DAO/PrnManutencaoFormaCobrancaClienteDAO.php';

require_once _MODULEDIR_ . 'eSitef/Action/IntegracaoSoftExpress.class.php';

// classe para gerenciar dados de débito automático
require_once _MODULEDIR_ . 'Principal/Action/PrnDa.php';

//classe para gerenciar dados do cliente
require_once _MODULEDIR_ . 'Principal/Action/PrnCliente.php';

//classe para gerenciar dados de cobrança do cliente
require_once _MODULEDIR_ . 'Principal/Action/PrnDadosCobranca.php';

//classe para gerenciar remoção do cartão de crédito
require_once _MODULEDIR_ . 'eSitef/Action/RemoverCartao.class.php';


require_once _SITEDIR_ . "lib/phpMailer/class.phpmailer.php";
/**
 * Carrega a DAO
 */
require_once _MODULEDIR_ . "/Financas/DAO/FinCreditoFuturoDAO.php";

//carrega VO
require_once _MODULEDIR_ . "/Financas/VO/CreditoFuturoCampanhaPromocionalVO.php";
require_once _MODULEDIR_ . "/Financas/VO/CreditoFuturoMotivoCreditoVO.php";
require_once _MODULEDIR_ . "/Financas/VO/CreditoFuturoMovimentoVO.php";
require_once _MODULEDIR_ . "/Financas/VO/CreditoFuturoParcelaVO.php";
require_once _MODULEDIR_ . "/Financas/VO/CreditoFuturoVO.php";
require_once _MODULEDIR_ . "/Financas/VO/CreditoFuturoHistoricoVO.php";

//carrega BO
require_once _MODULEDIR_ . "/Financas/Action/CreditoFuturo.php";

class PrnManutencaoFormaCobrancaCliente {

    /**
     * Atributo para acesso a persistência de dados
     */
    private $dao;
    private $conn;

    /**
     * Atributo para armazenar o tipo de operação do débito automático
     * */
    private $tipo_operacao;
    public $totalCampanhasVigentes = 0;

    /**
     * Construtor
     */
    public function __construct() {

        global $conn;

        $this->conn = $conn;

        /**
         * Objeto - DAO
         */
        $this->dao = new PrnManutencaoFormaCobrancaClienteDAO($conn);
    }

    /**
     * Pesquisa de Clientes
     * @return	String json com os clientes retornados
     */
    public function pesquisar() {

        $clinome = (isset($_POST['clinome'])) ? $_POST['clinome'] : null;
        $clitipo = (isset($_POST['clitipo'])) ? $_POST['clitipo'] : null;
        $clioid = (isset($_POST['clioid'])) ? $_POST['clioid'] : null;
        $clino_documento = (isset($_POST['clino_documento'])) ? $_POST['clino_documento'] : null;

        $prnCliente = new PrnCliente();

        $clientes = $prnCliente->getClientes($clinome, $clitipo, $clioid, $clino_documento);

        echo json_encode($clientes);
        exit;
    }

    /**
     * Carrega as informações da forma de cobrança disponíveis
     */
    public function carregarInformacoes() {

        $clioid = (isset($_POST['clioid'])) ? $_POST['clioid'] : null;

        $formaCobranca = $this->dao->getInformacoes($clioid);

        echo json_encode($formaCobranca);
        exit;
    }

    /**
     * De acordo com a forma de cobrança selecionada, verifica as datas disponíveis para pagamento
     */
    public function buscarDiaCobranca() {

        $forcoid = (isset($_POST['forcoid'])) ? $_POST['forcoid'] : null;
        $exibeDataVencimento = new stdClass();
        if (is_numeric($forcoid)) {

            if ($this->dao->isCartaoCredito($forcoid)) {
                $exibeDataVencimento->tipo = 'credito';
            }/* else if($this->dao->isDebito($forcoid)){
              $exibeDataVencimento->tipo = 'debito';

              } */ else {
                //$exibeDataVencimento->tipo = null;
                $exibeDataVencimento->tipo = "";
            }

            $prnDadosCobranca = new PrnDadosCobranca();

            $formaCobranca = $prnDadosCobranca->getDiaCobranca($exibeDataVencimento, "");
            echo json_encode($formaCobranca);
            exit();
        } else {
            echo 0;
            exit();
        }
    }

    /**
     * Verifica se a forma de cobrança é do tipo cartão de crédito
     */
    public function verificaFormaCobrancaCartao() {

        $formaCobranca['formaCobrancaCartao'] = $this->dao->verificaFormaCobrancaCartao();
        echo json_encode($formaCobranca);
        exit();
    }

    /**
     * Controle de transações de confirmação da nova forma de pagamento selecionada
     */
    public function confirmarFormaPagamento($transacao = true) {
        //instância da classe de atualização de dados de cobrança
        $prnDadosCobranca = new PrnDadosCobranca();

        # recupera informações do formulário

        $dadosConfirma = new stdClass();

        /* BEGIN DUM 81608 - 002556 */

        $dadosConfirma->conreajuste = isset($_POST['conreajuste']) && trim($_POST['conreajuste']) != '' ? trim($_POST['conreajuste']) : '';

        /* END DUM 81608 - 002556 */


        // Informacoes do quadro Dados de Pagamento na tela de Contrato
        $dadosConfirma->contrato = !empty($_POST['contrato']) ? $_POST['contrato'] : 0;
        $dadosConfirma->anuidade = !empty($_POST['cpaganuidade']) ? $_POST['cpaganuidade'] : 0;
        $dadosConfirma->renovacao = !empty($_POST['cpagrenovacao']) ? $_POST['cpagrenovacao'] : 0;
        $dadosConfirma->valvula = !empty($_POST['cpagvalvula']) ? $_POST['cpagvalvula'] : 0;
        $dadosConfirma->sleep = !empty($_POST['cpagsleep']) ? $_POST['cpagsleep'] : 0;
        $dadosConfirma->conversor = !empty($_POST['cpagconversor']) ? $_POST['cpagconversor'] : 0;
        $dadosConfirma->monitoramento = !empty($_POST['cpagmonitoramento']) ? $_POST['cpagmonitoramento'] : 0;
        $dadosConfirma->transf_titularidade = !empty($_POST['cpagtransf_titularidade']) ? $_POST['cpagtransf_titularidade'] : '';
        $dadosConfirma->habilitacao = !empty($_POST['cpaghabilitacao']) ? $_POST['cpaghabilitacao'] : 0;
        $dadosConfirma->numero_parcela = !empty($_POST['cpagnum_parcela']) ? $_POST['cpagnum_parcela'] : 'null';

        //id do cliente
        $dadosConfirma->id_cliente = (isset($_POST['clioid'])) ? $_POST['clioid'] : 'null';
        //nova forma de cobrança
        $dadosConfirma->forma_cobranca_posterior = (isset($_POST['forcoid'])) ? $_POST['forcoid'] : 'null';
        //nova data de vencimento
        $dadosConfirma->idDataVencimento = (isset($_POST['forma_pagamento_clidia_vcto'])) ? $_POST['forma_pagamento_clidia_vcto'] : 'null';
        //permissao de alteração da data de venc
        $dadosConfirma->dataVencimentoPermissao = (isset($_POST['forma_pagamento_clidia_vcto_alterar'])) ? true : false;
        //dados cartão de crédito
        $dadosConfirma->numeroCartao = (isset($_POST['numero_cartao'])) ? $_POST['numero_cartao'] : 'null';
        $dadosConfirma->nomePortador = (isset($_POST['nome_portador'])) ? $this->formata($_POST['nome_portador']) : 'null';
        $dadosConfirma->dataValidade = (isset($_POST['mes_ano'])) ? $_POST['mes_ano'] : 'null';
        //dados débito automático
        $dadosConfirma->motivoAlteraDebito = (isset($_POST['motivo_alterar_debito'])) ? $_POST['motivo_alterar_debito'] : 'null';
        $dadosConfirma->protocolo = (isset($_POST['protocolo'])) ? $_POST['protocolo'] : 'null';
        $dadosConfirma->debitoBanco = (isset($_POST['debito_banco'])) ? $_POST['debito_banco'] : 'null';
        $dadosConfirma->debitoAgencia = (isset($_POST['debito_agencia'])) ? $_POST['debito_agencia'] : 'null';
        $dadosConfirma->debitoConta = (isset($_POST['debito_conta'])) ? $_POST['debito_conta'] : 'null';
        $dadosConfirma->obsContratoPagamento = (isset($_POST['info_debito'])) ? $_POST['info_debito'] : null;
        //dados de cobranca
        $dadosConfirma->emailCobranca = (isset($_POST['cobranca_email'])) ? $_POST['cobranca_email'] : 'null';
        $dadosConfirma->email = (isset($_POST['cliente_email'])) ? $_POST['cliente_email'] : 'null';
        $dadosConfirma->emailNfe = (isset($_POST['cobranca_email_nfe'])) ? $_POST['cobranca_email_nfe'] : 'null';
        $dadosConfirma->telefoneDdd = (isset($_POST['cobranca_telefone_ddd'])) ? $_POST['cobranca_telefone_ddd'] : 'null';
        $dadosConfirma->telefone = (isset($_POST['cobranca_telefone_fone'])) ? $_POST['cobranca_telefone_fone'] : 'null';
        $dadosConfirma->cep = (isset($_POST['cobranca_cep'])) ? $_POST['cobranca_cep'] : 'null';
        $dadosConfirma->pais = (isset($_POST['cobranca_pais'])) ? $_POST['cobranca_pais'] : 'null';
        $dadosConfirma->estado = (isset($_POST['cobranca_estado'])) ? $_POST['cobranca_estado'] : 'null';
        $dadosConfirma->cidade = (isset($_POST['cobranca_cidade'])) ? $_POST['cobranca_cidade'] : 'null';
        $dadosConfirma->bairro = (isset($_POST['cobranca_bairro'])) ? $_POST['cobranca_bairro'] : 'null';
        $dadosConfirma->logradouro = (isset($_POST['cobranca_logradouro'])) ? $_POST['cobranca_logradouro'] : 'null';
        $dadosConfirma->numero = (isset($_POST['cobranca_num'])) ? $_POST['cobranca_num'] : 'null';
        $dadosConfirma->complemento = (isset($_POST['cobranca_complemento'])) ? $_POST['cobranca_complemento'] : 'null';
        //dados do usuário
        $dadosConfirma->id_usuario = (isset($_SESSION['usuario']['oid'])) ? $_SESSION['usuario']['oid'] : $_POST['cod_usu'];

        //Canal de Entrada do histórico: I = Intranet; P = Portal
        $dadosConfirma->entrada = (isset($_POST['entrada'])) ? $_POST['entrada'] : 'null';
        // Origem da chamada
        //  FC - Tela Manutenção Forma de Cobrança Clientes
        //	CF - Tela Contrato Financeiro
        //	CS - Tela Contrato
        //	DA - Tela Débito Automático
        //	PC - Tela Pré-Cadastro
        //  C1 - Conector 1 (WS1 - ws1_integracao_ebs.php)
        //  VC - Validade Cartão Crédito
        //  CT - CARGO TRACCK
        $dadosConfirma->origem_chamada = (isset($_POST['origem_chamada'])) ? $_POST['origem_chamada'] : 'null';
        $dadosConfirma->cliente_novo = (isset($_POST['cliente_novo'])) ? $_POST['cliente_novo'] : 'null';

        $dadosConfirma->enviaEmail = false;
        //controle para remoção do cartão
        $removeCartao = false;

        //varíáveis para controlar a alteração de forma de conbrança do cliente caso o cartão de crédito
        //seja negado pela SE
        $msgErro = "";
        $alteraCobrancaBoleto = false;


        //devido a muitas transações abertas no pré-cadastro que interfere na remoção do cartão,
        //é necessário abrir e fechar um nova transação para garantir a integridade dados dos logs
        //esse módulo não permite transações de bd abertas antes de processa a alteração da forma de cobrança 
        if ($dadosConfirma->origem_chamada != 'CORE' && $transacao) {
            $rs = pg_query($this->conn, "BEGIN;");
            $rs = pg_query($this->conn, "COMMIT;");
        }

        if ($dadosConfirma->origem_chamada == "CT") {
            // CAMPOS UTILIZADOS NO FORMULARIO DE CADASTRO CLIENTE CARGO TRACCK
            $dadosConfirma->nomeTitular = (!empty($_POST['clictitular_conta'])) ? $_POST['clictitular_conta'] : '';
            $dadosConfirma->tipoConta = (!empty($_POST['clitipo'])) ? $_POST['clitipo'] : '';
            $dadosConfirma->nomeCartao = (!empty($_POST['cccnome_cartao'])) ? $_POST['cccnome_cartao'] : '';
            $dadosConfirma->diaMes = (!empty($_POST['clicdia_mes'])) ? $_POST['clicdia_mes'] : 'null';
            $dadosConfirma->diasPrazo = (!empty($_POST['clicdias_prazo'])) ? $_POST['clicdias_prazo'] : 'null';
            $dadosConfirma->diasUteis = (!empty($_POST['clicdias_uteis'])) ? $_POST['clicdias_uteis'] : 'false';
            $dadosConfirma->diaSemana = (!empty($_POST['clicdia_semana'])) ? $_POST['clicdia_semana'] : '';
        } else {
            $dadosClienteCargoTracck = $prnDadosCobranca->getDadosClienteCargoTracck($dadosConfirma);
            // CAMPOS UTILIZADOS NO FORMULARIO DE CADASTRO CLIENTE CARGO TRACCK
            $dadosConfirma->nomeTitular = (!empty($dadosClienteCargoTracck->clictitular_conta)) ? $dadosClienteCargoTracck->clictitular_conta : '';
            $dadosConfirma->tipoConta = (!empty($dadosClienteCargoTracck->clitipo)) ? $dadosClienteCargoTracck->clitipo : '';
            $dadosConfirma->nomeCartao = (!empty($dadosClienteCargoTracck->clicdia_mes)) ? $dadosClienteCargoTracck->clicdia_mes : '';
            $dadosConfirma->diaMes = (!empty($dadosClienteCargoTracck->clicdia_mes)) ? $dadosClienteCargoTracck->clicdia_mes : 'null';
            $dadosConfirma->diasPrazo = (!empty($dadosClienteCargoTracck->clicdias_prazo)) ? $dadosClienteCargoTracck->clicdias_prazo : 'null';
            $dadosConfirma->diasUteis = (!empty($dadosClienteCargoTracck->clicdias_uteis)) ? $dadosClienteCargoTracck->clicdias_uteis : 'false';
            $dadosConfirma->diaSemana = (!empty($dadosClienteCargoTracck->clicdia_semana)) ? $dadosClienteCargoTracck->clicdia_semana : '';
            $dadosConfirma->nomeCartao = ''; //(!empty($dadosClienteCargoTracck->cccnome_cartao)) ? $dadosClienteCargoTracck->cccnome_cartao : '';
        }

        //formulários pemitidos para alteração de dados de cobrança do cliente referente a $dadosConfirma->origem_chamada
        $formPermitidos = array("FC");

        try {

            //verifica se o id do cliente foi informado
            if ($dadosConfirma->id_cliente === 'null') {
                throw new Exception(' O codigo do cliente deve ser informado.');
            }

            //verifica se foi informado a nova forma de cobrança
            if ($dadosConfirma->forma_cobranca_posterior === 'null') {
                throw new Exception(' A forma de cobranca deve ser informada.');
            }

            //verifica se o canal de entrada foi informado
            if ($dadosConfirma->entrada === 'null') {
                //Canal de Entrada do histórico: I = Intranet; P = Portal
                throw new Exception(' O canal de entrada deve ser informado.');
            }

            //verifica se a origem de chamada foi informada
            if ($dadosConfirma->origem_chamada === 'null') {
                throw new Exception(' A origem da chamada deve ser informada.');
            }

            //veriica se o usuário foi informado
            if ($dadosConfirma->id_usuario === 'null') {
                throw new Exception(' O usuario deve ser informado.');
            }

            //verifica se a origem_chamada vem do WS191_submeterPermissaoDebitoAutomatico.php com valor 'DA' = Débito Automático para buscar a data de vencimento do bd
            if ($dadosConfirma->origem_chamada === 'DA') {

                //instancia a classe de dados de cobrança
                $prnDadosCobranca = new PrnDadosCobranca();

                //confirma se a nova forma é débito automático para buscar a data de vencimento
                if ($this->dao->isDebito($dadosConfirma->forma_cobranca_posterior)) {

                    $exibeDataVencimento->tipo = 'debito';
                    //busca o id da data de vencimento
                    $formaCobranca = $prnDadosCobranca->getDiaCobranca($exibeDataVencimento, "");
                    //seta o id da data de vencimento
                    $dadosConfirma->idDataVencimento = $formaCobranca[0]['codigo'];

                    //se for uma exclusão o WS191_submeterPermissaoDebitoAutomatico.php seta a forma de cobrança para 74 (Cobrança registrada HSBC)
                } else if ($dadosConfirma->forma_cobranca_posterior === '74') {

                    //id do dia fixo, já que não é informado o dia de vencimento no WS
                    $dadosConfirma->idDataVencimento = 2; // dia 7
                } else {
                    $exibeDataVencimento->tipo === 'null';
                }

                //verifica se a data de vencimento foi encontrada
                if ($exibeDataVencimento->tipo === 'null') {
                    throw new Exception(' A data de vencimento nao foi encontrada.');
                }
            } else {
                //verifica para a outras origens de chamada se a data de vencimento foi informada
                if ($dadosConfirma->forma_cobranca_posterior !== 'null' && !empty($dadosConfirma->forma_cobranca_posterior)) {
                    if ($dadosConfirma->idDataVencimento === 'null') {
                        throw new Exception(' A data de vencimento deve ser informada.');
                    }
                } else {
                    $dadosConfirma->forma_cobranca_posterior = 0;
                }
            }

            // instância da classe de Integração com e-Sitef
            $ws = new IntegracaoSoftExpress();
            //instância da classe cliente para atualização e recuperação de dados
            $prnCliente = new PrnCliente();
            //instância da classe de débito automático
            $prnDa = new PrnDa();

            //grava log de erro se o pagamento for cartao de crédito antes do BEGIN, pois se gerar alguma exceção não grava os dados 
            //na tabela devido ao rollback
            if ($this->dao->isCartaoCredito($dadosConfirma->forma_cobranca_posterior)) {
                $this->dao->incluirTransacaoCartao($dadosConfirma->id_cliente, false);
            }

            // consulta a forma de cobrança atual do cliente
            $formaAtual = $prnDadosCobranca->getFormaCobrancaAtual($dadosConfirma->id_cliente);


            //inicia transação do tipo remoção de cartão de crédito
            //$transacaoRemocaoID = $removerCartao->iniciarTransacaoRemocaoCartaoCredito($dadosConfirma->id_cliente);
            //}
            //inicia transação do banco de dados
            //usar para que, em qualquer msg de erro, seja dado rollback e não seja alterada a consistência do banco
            if ($dadosConfirma->origem_chamada != 'CORE' && $transacao) {
                $rs = pg_query($this->conn, "BEGIN;");
            }

            // Se a forma atual for cartão de crédito, executa os métodos
            // referentes a inclusão/remoção do cartão de crédito
            if ($formaAtual->forccobranca_cartao_credito == 't') {

                $removeCartao = false;

                if ($dadosConfirma->numeroCartao != 'null' && $dadosConfirma->numeroCartao != '' && $dadosConfirma->dataValidade != 'null' && $dadosConfirma->dataValidade != '') {
                    $removeCartao = true;
                }

                if (!$this->dao->isCartaoCredito($dadosConfirma->forma_cobranca_posterior)) {
                    $removeCartao = true;
                }

                if ($removeCartao) {

                    //remove o cartão de crédito no bd
                    if (!$this->dao->removerCobrancaClienteCredito($dadosConfirma->id_cliente)) {
                        throw new Exception(" Falha ao remover cartao de credito.");
                    }
                }
            }

            // Se a nova forma de Pagamento for Cartão de Crédito e tiver número do cartão e data da validade
            if ($this->dao->isCartaoCredito($dadosConfirma->forma_cobranca_posterior) && ($dadosConfirma->numeroCartao != 'null' && $dadosConfirma->numeroCartao != '') && ( $dadosConfirma->dataValidade != 'null' && $dadosConfirma->dataValidade != '' )) {

                //validação da data de validade do cartão de crédito
                if (!$dataValidadeCartao = $this->validaData($dadosConfirma->dataValidade)) {
                    throw new Exception(" Data de validade do cartao de credito invalida.");
                }
                // Executa o método "store" do WebService, que armazenará as informações do cartão.
                //$store['merchantId']     = "1";
                $store['cardNumber'] = $dadosConfirma->numeroCartao;
                $store['cardExpiryDate'] = $this->formataDataCartao($dadosConfirma->dataValidade);
//                $store['authorizerId'] = 2; //$this->dao->buscaAutorizadora($dadosConfirma->forma_cobranca_posterior );
//                $store['merchantUSN'] = 388683; //$this->dao->getIdTransacao();
		$store['authorizerId']   = $this->dao->buscaAutorizadora($dadosConfirma->forma_cobranca_posterior );
		$store['merchantUSN']    = $this->dao->getIdTransacao();
                $store['customerId'] = $dadosConfirma->id_cliente;
                $store['additionalInfo'] = "";

                //envia os dados para o WebService
                $retStore = $ws->store($store);
                //caso a softexpress esteja offline
                if (strstr($retStore->storeResponse->message, 'offline')) {
                    $msgErro = "Sistema de pagamento indisponivel. ";
                    //se foi solicitação de remoção de cartão e apresentou erro, então, fará a troca de forma de cobrança,
                    //se não, lança o exception
                    if ($removeCartao) {
                        $alteraCobrancaBoleto = true;
                    } else {
                        throw new Exception($msgErro);
                    }
                }

                //trata retorno da transação de pagamento com a e-SiTef
                if ($retStore->storeResponse->status == 'EXP') {

                    $msgErro = " Armazenamento expirou devido a tempo excessivo sem atualizacao por parte da loja (sem conexao com o usuario/comprador), favor selecionar outra forma de pagamento. ";

                    //se foi solicitação de remoção de cartão e apresentou erro, então, fará a troca de forma de cobrança,
                    //se não, lança o exception
                    if ($removeCartao) {
                        $alteraCobrancaBoleto = true;
                    } else {
                        throw new Exception($msgErro);
                    }
                } elseif ($retStore->storeResponse->status == 'NEG') {

                    $msgErro = " Armazenamento negado. Cartao invalido, favor selecionar outra forma de pagamento. ";

                    //se foi solicitação de remoção de cartão e apresentou erro, então, fará a troca de forma de cobrança,
                    //se não, lança o exception
                    if ($removeCartao) {
                        $alteraCobrancaBoleto = true;
                    } else {
                        throw new Exception($msgErro);
                    }
                } elseif ($retStore->storeResponse->status == 'CAN') {

                    $msgErro = " Armazenamento do cartao cancelado pelo cliente. Dados removidos do e-SiTef a pedidos da loja, favor selecionar outra forma de pagamento. ";

                    //se foi solicitação de remoção de cartão e apresentou erro, então, fará a troca de forma de cobrança,
                    //se não, lança o exception
                    if ($removeCartao) {
                        $alteraCobrancaBoleto = true;
                    } else {
                        throw new Exception($msgErro);
                    }
                } elseif ($retStore->storeResponse->status == 'DEL') {

                    $msgErro = " Dados do cartao foram removidos do e-SiTef a pedidos do dono do cartao, favor selecionar outra forma de pagamento. ";

                    //se foi solicitação de remoção de cartão e apresentou erro, então, fará a troca de forma de cobrança,
                    //se não, lança o exception
                    if ($removeCartao) {
                        $alteraCobrancaBoleto = true;
                    } else {
                        throw new Exception($msgErro);
                    }
                } elseif ($retStore->storeResponse->status == 'BLQ') {

                    $msgErro = " Armazenamento bloqueado por possivel tentativa de fraude, favor selecionar outra forma de pagamento. ";

                    //se foi solicitação de remoção de cartão e apresentou erro, então, fará a troca de forma de cobrança,
                    //se não, lança o exception
                    if ($removeCartao) {
                        $alteraCobrancaBoleto = true;
                    } else {
                        throw new Exception($msgErro);
                    }
                } elseif (empty($retStore->storeResponse->cardHash)) {


                    $msgErro = " O cartao de credito informado nao foi aceito, favor selecionar outra forma de pagamento. ";

                    //se foi solicitação de remoção de cartão e apresentou erro, então, fará a troca de forma de cobrança,
                    //se não, lança o exception
                    if ($removeCartao) {
                        $alteraCobrancaBoleto = true;
                    } else {
                        throw new Exception($msgErro); 
                    }
                } elseif ($retStore->storeResponse->status == 'CON' || $retStore->storeResponse->status == 'DUP') {
                    //cadastra dados do cartão atual na tabela "cliente_cobranca_credito".
                    if (!$this->dao->incluirCobrancaClienteCredito($retStore, $dadosConfirma, $dataValidadeCartao)) {
                        throw new Exception(" Falha ao incluir cobranca de cartao de credito. ");
                    }
                } else {

                    $msgErro = $retStore->storeResponse->message;
                    //se foi solicitação de remoção de cartão e apresentou erro, então, fará a troca de forma de cobrança,
                    //se não, lança o exception
                    if ($removeCartao) {
                        $alteraCobrancaBoleto = true;
                    } else {
                        throw new Exception($msgErro);
                    }
                }
                if ($alteraCobrancaBoleto) {
                    //inclui histório da transação de alteração para boleto
                    if (!$this->dao->incluirTransacaoCartao($dadosConfirma->id_cliente, false, $retStore, 'Alterada forma de cobrança para boleto.')) {
                        throw new Exception(" Falha ao incluir historico de alteracao para boleto.");
                    }
                } else {
                    //finaliza transação de inclusão de cartão
                    if (!$this->dao->incluirTransacaoCartao($dadosConfirma->id_cliente, true, $retStore)) {
                        throw new Exception(" Falha ao finalizar transacao de cartao");
                    }
                }
            }

            // caso o cartão do cliente seja negado pela SE pelos motivos tratados acima,
            // a forma de cobrança do cliente é alterada para boleto somente em casos que solicitarem remoção do cartão, ou seja,
            // a forma de cobrança atual do cliente seja cartão de crédito
            if ($alteraCobrancaBoleto) {
                $dadosConfirma->forma_cobranca_posterior = 1; //Boleto
            }

            //trata os dados bancários, quando a nova forma não for débito automático
            //apaga os dados por segurança, pois pode vir dados do cache do browser
            if (!$this->dao->isDebito($dadosConfirma->forma_cobranca_posterior)) {
                unset($dadosConfirma->debitoBanco);
                unset($dadosConfirma->debitoAgencia);
                unset($dadosConfirma->debitoConta);

            }

            // Busca os dados anteriores de cobranca do cliente antes de efetuar as alterações
            $dados_cobranca_anterior = $prnDadosCobranca->getFormaCobrancaAnterior($dadosConfirma->id_cliente);
            if (empty($dados_cobranca_anterior->situacao_visualizacao) || $dados_cobranca_anterior->situacao_visualizacao == ' ') {
                $dadosConfirma->visualizacao_anterior = 'V';
            } else {
                $dadosConfirma->visualizacao_anterior = $dados_cobranca_anterior->situacao_visualizacao;
            }
            # EXCLUSÃO DE DA
            //se a forma atual for débito automático e a nova forma for qualquer outra,
            // inicia o processo de EXCLUSÃO
            if ($formaAtual->forcdebito_conta == 't' && !$this->dao->isDebito($dadosConfirma->forma_cobranca_posterior)) {
                //dados bancários anteriores
                $removeDa = new stdClass();
                $removeDa->forma_cobranca_anterior = $dados_cobranca_anterior->forma_cobranca;
                $removeDa->descricao_forma_cobranca_anterior = $dados_cobranca_anterior->descricao_forma_cobranca;
                $removeDa->banco_anterior = $dados_cobranca_anterior->banco;
                $removeDa->nome_banco_anterior = $dados_cobranca_anterior->nome_banco;
                $removeDa->agencia_anterior = $dados_cobranca_anterior->agencia;
                $removeDa->conta_corrente_anterior = $dados_cobranca_anterior->conta_corrente;
                //para verificação dos demais métodos, já que serão usados os mesmos
                $removeDa->tipo_operacao = $this->tipo_operacao = 'E'; //Exclusão de DA
                //método para remoção de DA
                $remocaoDebito = $prnDa->removerDebitoAutomatico($removeDa, $dadosConfirma);

                if (!empty($remocaoDebito)) {
                    if ($remocaoDebito != 1) {
                        throw new Exception($remocaoDebito);
                    }
                }

                //envia e-mail informando a exclusão do débito automático
                $dadosConfirma->enviaEmail = true;
            }

            # INCLUSÃO DE DA
            //se a nova forma escolhida for débito automático e a forma anterior não seja débito automático
            // inicia o processo de INCLUSÃO de débito automático
            if ($this->dao->isDebito($dadosConfirma->forma_cobranca_posterior) && $formaAtual->forcdebito_conta == 'f') {
                $insereDa = new stdClass();
                //dados anteriores de cobranca do cliente
                $insereDa->forma_cobranca_anterior = $dados_cobranca_anterior->forma_cobranca;

                //busca o nome do banco 
                $nome_banco_posterior = $this->getNomeBanco($dadosConfirma->debitoBanco, $dadosConfirma->forma_cobranca_posterior);
                $insereDa->nome_banco_posterior = $nome_banco_posterior;

                $insereDa->banco_posterior = $dadosConfirma->debitoBanco;
                $insereDa->agencia_posterior = $dadosConfirma->debitoAgencia;
                $insereDa->conta_corrente_posterior = $dadosConfirma->debitoConta;
                $insereDa->nomeTitular = $dadosConfirma->nomeTitular;

                //para verificação dos demais métodos, já que serão usados os mesmos
                $insereDa->tipo_operacao = $this->tipo_operacao = 'I'; //Inclusão de DA
                //método para inserção de DA
                if (!$prnDa->insereDebitoAutomatico($insereDa, $dadosConfirma)) {
                    throw new Exception(" Falha ao inserir debito automatico.");
                }

                //envia e-mail com o termo informando a inclusão do débito automático
                $dadosConfirma->enviaEmail = true;
            }

            # ALTERAÇÃO DE DA
            //se a forma atual for débito automático e a nova forma também é débito automático,
            // faz o processo de ALTERAÇÃO
            if ($formaAtual->forcdebito_conta == 't' && $this->dao->isDebito($dadosConfirma->forma_cobranca_posterior)) {
                $alteraDa = new stdClass();
                // dados bancários anteriores de cobrança do cliente
                $alteraDa->forma_cobranca_anterior = $dados_cobranca_anterior->forma_cobranca;
                $alteraDa->descricao_forma_cobranca_anterior = $dados_cobranca_anterior->descricao_forma_cobranca;
                $alteraDa->banco_anterior = $dados_cobranca_anterior->banco;
                $alteraDa->nome_banco_anterior = $dados_cobranca_anterior->nome_banco;
                $alteraDa->agencia_anterior = $dados_cobranca_anterior->agencia;
                $alteraDa->conta_corrente_anterior = $dados_cobranca_anterior->conta_corrente;

                //novos dados bancários
                $nome_banco_posterior = $this->getNomeBanco($dadosConfirma->debitoBanco, $dadosConfirma->forma_cobranca_posterior);
                $alteraDa->nome_banco_posterior = $nome_banco_posterior;
                $alteraDa->banco_posterior = $dadosConfirma->debitoBanco;
                $alteraDa->agencia_posterior = $dadosConfirma->debitoAgencia;
                $alteraDa->conta_corrente_posterior = $dadosConfirma->debitoConta;
                $alteraDa->nomeTitular = $dadosConfirma->nomeTitular;
                //para verificação dos demais métodos, já que serão usados os mesmos
                $alteraDa->tipo_operacao = $this->tipo_operacao = 'A'; //Alteração de DA
                //método para alteração de DA
                if (!$prnDa->alterarDebitoAutomatico($alteraDa, $dadosConfirma)) {
                    throw new Exception(" Falha ao alterar debito automatico.");
                }
            }

            //verifica os formulários permitidos que alteram dados de cobrança do cliente
            if (in_array($dadosConfirma->origem_chamada, $formPermitidos)) {

                //atualiza dados do cliente
                if (!$prnCliente->atualizarCliente($dadosConfirma)) {
                    throw new Exception(" Falha ao atualizar dados do cliente.");
                }

                if ($prnDadosCobranca->getEnderecoCobrancaPorCliente($dadosConfirma->id_cliente)) {
                    //atualiza endereço de cobrança
                    if (!$prnDadosCobranca->atualizarEnderecoCobranca($dadosConfirma)) {
                        throw new Exception(" Falha ao atualizar dados endereco de cobranca.");
                    }
                } else {
                    if (!$prnDadosCobranca->inserirEnderecoCobranca($dadosConfirma)) {
                        throw new Exception(" Falha ao atualizar dados endereco de cobranca.");
                    }
                }
            }

            //busca a data do vencimento para inserir na tabela de proposta
            $diaVencimentoProposta = $prnDadosCobranca->getDiaCobranca("", $dadosConfirma->idDataVencimento);
            $dadosConfirma->diaVencimentoProposta = $diaVencimentoProposta[0]['dia_pagamento'];

            //atualiza as propostas
            if (!$this->atualizarPropostas($dadosConfirma)) {
                throw new Exception(" Falha ao atualizar propostas.");
            }

            //atualiza contratos da forma de pagamento
            if (!$this->atualizarContratosPagamento($dadosConfirma)) {
                throw new Exception(" Falha ao atualizar contratos de pagamento.");
            }

            if ($dadosConfirma->origem_chamada == 'CS') {
                if (!$this->atualizaDadosPagamentoContrato($dadosConfirma)) {
                    throw new Exception(" Falha ao atualizar dados de pagamento referente ao contrato.");
                }
            }
            //atualiza forma de pagamento das propostas
            if (!$this->atualizarPropostasPagamento($dadosConfirma)) {
                throw new Exception(" Falha ao atualizar forma de pagamento da proposta.");
            }

            //recupera os títulos do cliente para efetuar as alterações
            $titulos = $this->dao->buscaTitulosAlterar($dadosConfirma);

            // Se houver títulos em aberto, altera a data de vencimento de acordo com a nova forma de pagamento
            if ($titulos) {
                $permissaoAlteraDataVencimento = $this->verificaPermissaoVencimento();
                foreach ($titulos as $titulo) {
                    //traz a nova data de vencimento de acordo as regras do método
                    $dt_vencimento = $this->dao->retornaDataVencimento($titulo['titven'], $dadosConfirma->idDataVencimento);
                    //efetua as alterações dos títulos
                    if (!$this->dao->alterarTitulos($titulo['titoid'], $dt_vencimento, $dadosConfirma, $permissaoAlteraDataVencimento)) {
                        throw new Exception(" Falha ao alterar titulos.");
                    }
                }
            }

            //Atualiza dados da cobrança relacionada ao cliente
            if (!$prnDadosCobranca->atualizarCobranca($dadosConfirma)) {
                throw new Exception(" Falha ao atualizar dados de cobranca.");
            }

            //envia e-mail se a forma anterior ou posterior for débito automático
            if ($dadosConfirma->enviaEmail) {
                //  Verifica se o cliente possui pelo menos um contrato ativo 
                $is_contrato_ativo = $prnCliente->contratoAtivoCliente($dadosConfirma->id_cliente);

                if ($is_contrato_ativo) {
                    //envia e-mail utilizando método da classe de débito automático
                    $prnDa->enviarEmail($dadosConfirma, $dados_cobranca_anterior, $this->tipo_operacao);
                }
            }

            //confirma alterações no banco caso não tenha erros
            if ($dadosConfirma->origem_chamada != 'CORE' && $transacao) {
                $rs = pg_query($this->conn, "COMMIT;");
            }

            if ($rs) {
                $this->verificarCreditoFuturoFormaCobranca($dadosConfirma->id_cliente, intval($dados_cobranca_anterior->forma_cobranca), intval($dadosConfirma->forma_cobranca_posterior), $dadosConfirma->cliente_novo);
            }

            //se entrada vier do portal ou origem_chamada for do local, retorna um array, se não, retorna jason para chamada do jquery
            if ($dadosConfirma->entrada === 'P' || $dadosConfirma->origem_chamada === 'PC' || $dadosConfirma->origem_chamada === 'C1' || $dadosConfirma->origem_chamada === 'VC' || $dadosConfirma->origem_chamada === 'CORE') {
                //exibe mensagem de erro caso altere a forma de cobrança para boleto
                if ($alteraCobrancaBoleto) {
                    $errorMsg = 'Falha na transação de remoção e inserção de cartão de crédito.<br/><br/>Motivo ->' . $msgErro . '<br/><br/> A forma de cobrança foi alterada para Boleto.';
                    return array('error' => true, 'message' => $errorMsg);
                }
                return array('error' => false);
            } else {
                echo json_encode("OK");
                exit();
            }
        } catch (Exception $e) {
            //é desfeita as alterações no banco se uma Exception for lançada
            if ($dadosConfirma->origem_chamada != 'CORE' && $transacao) {
                $rs = pg_query($this->conn, "ROLLBACK;");
            }
            //se entrada vier do portal ou origem_chamada for do pré-cadastro, retorna um array para o WS, se não, retorna jason para chamada do jquery
            if ($dadosConfirma->entrada === 'P' || $dadosConfirma->origem_chamada === 'PC' || $dadosConfirma->origem_chamada === 'CORE') {
                return array('error' => true, 'message' => $e->getMessage());
            } else {
                echo json_encode(utf8_encode($e->getMessage()));
                exit();
            }
        }
    }

    /**
     * Atualiza dados das propostas, relacionadas aos contratos ativos do cliente
     * - agencia
     * - conta corrente
     *
     */
    public function atualizarPropostas($dadosConfirma) {

        //instância da classe dados de cobrança
        $prnDadosCobranca = new PrnDadosCobranca();

        $agencia = ($dadosConfirma->debitoAgencia) ? $dadosConfirma->debitoAgencia : 'null';
        $conta_corrente = ($dadosConfirma->debitoConta) ? $dadosConfirma->debitoConta : 'null';

        // Busca os dados anteriores de cobranca do cliente
        $dados_cobranca_anterior = $prnDadosCobranca->getFormaCobrancaAnterior($dadosConfirma->id_cliente);

        $historicoProposta = new stdClass();
        //dados bancários anteriores
        $historicoProposta->descricao_forma_cobranca_anterior = $dados_cobranca_anterior->descricao_forma_cobranca;
        $historicoProposta->nome_banco_anterior = $dados_cobranca_anterior->nome_banco;
        $historicoProposta->agencia_anterior = $dados_cobranca_anterior->agencia;
        $historicoProposta->conta_corrente_anterior = $dados_cobranca_anterior->conta_corrente;

        //novos dados bancários
        $nome_banco_posterior = $this->getNomeBanco($dadosConfirma->debitoBanco, $dadosConfirma->forma_cobranca_posterior);
        $historicoProposta->nome_banco_posterior = $nome_banco_posterior;
        $historicoProposta->agencia_posterior = $dadosConfirma->debitoAgencia;
        $historicoProposta->conta_corrente_posterior = $dadosConfirma->debitoConta;

        // Busca dados da forma de cobranca posterior
        $dados_posteriores = $prnDadosCobranca->getDadosFormaCobranca($dadosConfirma->forma_cobranca_posterior);
        $descricao_forma_cobranca_posterior = $dados_posteriores->descricao_forma_cobranca;

        // Texto para inserir no historico da proposta de: para:
        $texto_alteracao = "Alteração: forma de cobrança de: $historicoProposta->descricao_forma_cobranca_anterior para: $descricao_forma_cobranca_posterior ";
        $texto_alteracao.= "banco de: $historicoProposta->nome_banco_anterior para: $historicoProposta->nome_banco_posterior ";
        $texto_alteracao.= "agência de: $historicoProposta->agencia_anterior para:  $historicoProposta->agencia_posterior ";
        $texto_alteracao.= "conta corrente de: $historicoProposta->conta_corrente_anterior para: $historicoProposta->conta_corrente_posterior ";

        $historicoProposta->texto_alteracao = $texto_alteracao;

        return $this->dao->atualizarPropostas($historicoProposta, $dadosConfirma, $agencia, $conta_corrente);
    }

    /**
     * Atualizar dados das propostas de pagamento, relacionadas as propostas relacionadas aos contratos ativos do cliente
     * - banco
     * - agencia
     * - conta corrente
     *
     */
    public function atualizarPropostasPagamento($dadosConfirma) {
        $banco = (!empty($dadosConfirma->debitoBanco)) ? $dadosConfirma->debitoBanco : 'null';
        $agencia = (!empty($dadosConfirma->debitoAgencia)) ? $dadosConfirma->debitoAgencia : 'null';
        $conta_corrente = (!empty($dadosConfirma->debitoConta)) ? $dadosConfirma->debitoConta : 'null';

        return $this->dao->atualizarPropostasPagamento($dadosConfirma, $banco, $agencia, $conta_corrente);
    }

    public function atualizaDadosPagamentoContrato($dados) {
        return $this->dao->atualizarDadosPagamentoContrato($dados);
    }

    public function buscarAutorizadora($forcoid) {
        return $this->dao->buscaAutorizadora($forcoid);
    }

    /**
     * Atualiza dados dos contratos de pagamento relacionados aos contratos ativos do cliente
     * - banco
     * - agencia
     * - conta corrente
     *
     */
    public function atualizarContratosPagamento($dadosConfirma) {
        $banco = (!empty($dadosConfirma->debitoBanco)) ? $dadosConfirma->debitoBanco : 'null';
        $agencia = (!empty($dadosConfirma->debitoAgencia)) ? $dadosConfirma->debitoAgencia : 'null';
        $conta_corrente = (!empty($dadosConfirma->debitoConta)) ? $dadosConfirma->debitoConta : 'null';

        return $this->dao->atualizarContratosPagamento($dadosConfirma, $banco, $agencia, $conta_corrente);
    }

    /**
     * Verifica se o usuário possui permissão para alterar a data de vencimento dos títulos
     *  
     * @return boolean
     */
    public static function verificaPermissaoVencimento() {

        if (isset($_SESSION['funcao']['Alterar vencimento'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verifica se a forma de cobrança é do tipo cartão de crédito
     * chamada pelo jquery
     */
    public function isCartaoCredito() {
        $forcoid = (isset($_POST['forcoid'])) ? $_POST['forcoid'] : null;
        $formaCobranca['formaCobrancaCartao'] = $this->dao->isCartaoCredito($forcoid);
        echo json_encode($formaCobranca);
        exit();
    }

    /**
     * Verifica se a forma de cobrança é do tipo débito automático
     * chamada pelo jquery.
     */
    public function isDebito() {
        $forcoid = (isset($_POST['forcoid'])) ? $_POST['forcoid'] : null;
        $formaCobranca['formaCobrancaDebito'] = $this->dao->isDebito($forcoid);
        echo json_encode($formaCobranca);
        exit();
    }

    /**
     * Retorna os dados de usuário
     * @autor Márcio Sampaio Ferreira
     * @email marcioferreira@brq.com
     *
     * @param $id_usuario
     *
     * @return objeto
     */
    public function getDadosUsuario($id_usuario) {

        return $this->dao->getDadosUsuario($id_usuario);
    }

    /**
     * Retorna os dados de banco
     * @autor Márcio Sampaio Ferreira
     * @email marcioferreira@brq.com
     *
     * @param $banco inteiro
     *
     * @return objeto
     */
    public function getDadosBanco($banco = null) {

        //pega o $ajax do post
        $ajax = (isset($_POST['ajax'])) ? $_POST['ajax'] : null;

        //se a chamada vier do ajax do js, então recebe dados do post e retorna o jason
        if ($ajax) {

            $codBanco = (isset($_POST['codBanco'])) ? $_POST['codBanco'] : null;

            $resul = $this->dao->getDadosBanco($codBanco);
            $resultado['bancodigo'] = $resul[0]['bancodigo'];
            $resultado['nome_banco'] = $resul[0]['nome_banco'];

            echo json_encode($resultado);
            exit();
        } else {
            return $this->dao->getDadosBanco($banco);
        }
    }

    /**
     * Retorna nome do banco
     * @autor Márcio Sampaio Ferreira
     * @email marcioferreira@brq.com
     *
     * @param $banco
     *
     * @return objeto
     */
    public function getNomeBanco($banco, $novaFormaCobranca) {

        if (!empty($banco)) {

            $dados_banco = $this->getDadosBanco($banco);
            $nomeBanco = $dados_banco[0]['nome_banco'];
        } else {

            $prnDadosCobranca = new PrnDadosCobranca();
            $dados_cobranca = $prnDadosCobranca->getBancoPorFormaCobranca($novaFormaCobranca);

            if (count($dados_cobranca) > 0) {
                $nomeBanco = $dados_cobranca[0]['nome_banco'];
            }
        }

        return $nomeBanco;
    }

    /**
     * Retorna as formas de cobrança do banco informado
     * chamada pelo jquery.
     */
    public function getBancoPorFormaCobranca() {

        $prnDadosCobranca = new PrnDadosCobranca();

        $forcoid = (isset($_POST['forcoid'])) ? $_POST['forcoid'] : null;

        $resultado = $prnDadosCobranca->getBancoPorFormaCobranca($forcoid);
        echo json_encode($resultado);
        exit();
    }

    /**
     * Retorna os dados de endereço do cep informado
     * chamada pelo jquery.
     */
    public function getEnderecoCep() {

        $prnDadosCobranca = new PrnDadosCobranca();

        $cep = (isset($_POST['cep'])) ? $_POST['cep'] : null;

        $resultado = $prnDadosCobranca->getEnderecoCep($cep);


        echo json_encode($resultado);
        exit();
    }

    /**
     * Retorna lista de países
     * chamada pelo jquery.
     */
    public function listarPaises() {

        $prnDadosCobranca = new PrnDadosCobranca();

        $resultado = $prnDadosCobranca->getDadosPaises();
        echo json_encode($resultado);
        exit();
    }

    /**
     * Retorna lista de motivos de troca de débito automático 
     * para qualquer outra forma de pagamento
     * chamada pelo jquery.
     */
    public function getDadosMotivos() {

        $prnDa = new PrnDa();

        $resultado = $prnDa->getDadosMotivos();
        echo json_encode($resultado);
        exit();
    }

    /**
     * Retorna lista de estados do país informado
     * chamada pelo jquery.
     */
    public function listarEstados() {
        $prnDadosCobranca = new PrnDadosCobranca();

        $pais = (isset($_POST['pais'])) ? $_POST['pais'] : null;

        $resultado = $prnDadosCobranca->listarEstados($pais);
        echo json_encode($resultado);
        exit();
    }

    /**
     * Retorna lista de cidade do estado informado
     * chamada pelo jquery.
     */
    public function listarCidades() {

        $prnDadosCobranca = new PrnDadosCobranca();

        $estado = (isset($_POST['estado'])) ? $_POST['estado'] : null;

        $resultado = $prnDadosCobranca->listarCidades($estado);
        echo json_encode($resultado);
        exit();
    }

    /**
     * Retorna lista de bairros do estado e cidade informados
     * chamada pelo jquery.
     */
    public function listarBairros() {

        $prnDadosCobranca = new PrnDadosCobranca();

        $estado = (isset($_POST['estado'])) ? $_POST['estado'] : null;
        $cidade = (isset($_POST['cidade'])) ? $_POST['cidade'] : null;

        $resultado = $prnDadosCobranca->listarBairros($estado, $cidade);

        echo json_encode($resultado);
        exit();
    }

    /**
     * @author	Willian Ouchi
     * @email	willian.ouchi@meta.com.br
     * @return	String json com a validação de e-mail
     * */
    public function validarEmail() {

        $email = (isset($_POST['email'])) ? $_POST['email'] : null;

        $informacoes = true;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $informacoes = false;
        }

        echo json_encode($informacoes);
        exit;
    }

    /**
     * Formata data de validade do cartao
     *
     * -> MM/AA
     * <- MMAA
     */
    public function formataDataCartao($data) {
        $date = explode("/", $data);

        $mesP = $date[0];
        $anoP = $date[1];
        $saida = $mesP . $anoP;

        return $saida;
    }

    /**
     * Função: 	Para validar data de vencimento do cartão de crédito 
     * Autor:	Márcio Sampaio Ferreira
     * Data:    12/04/2013
     */
    public function validaData($data) {

        $anoAtual = 0;
        //tem que estar no formato mm/aa
        if (strlen($data) != 5) {
            return false;
        }

        //separa mês e ano da validade 
        $dataValidade = explode("/", $data);
        $mesValidade = $dataValidade[0];
        $anoValidade = $dataValidade[1];

        //pega o último dia do mês de acordo o mês e ano de validade informado pelo usuário 
        $ultimoDiaMesValidade = date("d", mktime(0, 0, 0, (date($mesValidade) + 1), 0, date($anoValidade)));
        $dataValidadeCartao = $ultimoDiaMesValidade . "/" . $data;

        $valData = explode("/", $dataValidadeCartao);
        $dia = is_numeric($valData[0]) ? $valData[0] : 0;
        $mes = is_numeric($valData[1]) ? $valData[1] : 0;
        $ano = is_numeric($valData[2]) && strlen($valData[2]) == 2 ? $valData[2] : 0;

        //verifica se a data de validade do cartão é realmente uma data válida
        if (!checkdate($mes, $dia, $ano)) {
            return false;
        }
        //separa a data em array mês e ano
        $dataV = explode("/", $data);

        //verifica se o mês informado é válido
        if ($dataV[0] == 0 || $dataV[0] > 12) {
            return false;
        }

        //pega somente o dois últimos dígitos do ano
        $anoAtual = date("y");

        //verifica se o mês de validade informada já passou do ano atual
        if ($dataV[0] < date("m") && $dataV[1] == $anoAtual) {
            return false;
        }

        //calcula o ano atual mais 5 anos para validar o ano da validade do cartão de crédito
        //$anoValidade = $anoAtual + 5;
        /*
          if ($dataV[1] < $anoAtual || $dataV[1] > $anoValidade) {
          return false;
          }


          if ($dataV[1] < $anoAtual) {
          return false;
          }
         */

        if ($dataV[1] < $anoAtual) {
            return false;
        }
        /*
          // Validar mês validade
          if ($dataV[0] > date("m") && $dataV[1] == $anoValidade){
          return false;
          }
         */
        return $dataValidadeCartao;
    }

    /**
     * Busca Cobranças Promocionais vigentes
     */
    public function buscarCobrancasPromocionais() {

        $forma_cobranca_cliente = isset($_GET['forma']) ? $_GET['forma'] : '0';

        $tipo_forma = $this->dao->verificaFormaCobrancaBoleto($forma_cobranca_cliente);

        $this->totalCampanhasVigentes = $this->dao->buscarTotalCampanhasVigentes();

        return $this->dao->buscarCobrancasPromocionais($tipo_forma);
    }

    /**
     * Verifica se deve ser criado um crédito futuro para a nova forma de cobrança do cliente
     *
     * @param int $idCliente              Id do cliente
     * @param int $formaCobrancaAnterior  Id da forma de cobrança antes da alteração
     * @param int $formaCobrancaPosterior Id da forma de cobrança depois da alteração
     * 
     * @return boolean Verdadeiro se foi criado crédito futuro
     */
    public function verificarCreditoFuturoFormaCobranca($idCliente, $formaCobrancaAnterior, $formaCobrancaPosterior, $cliente_novo = null) {

        /*
         * Conversa com Dani Leite via Skype (21/11/2014):
         *
         * 
          [11:21:45] Dani Leite: existem duas formas de aplicar o crédito futuro referente a forma de pagamento
          [11:22:08] Dani Leite: Primeiro: Na adesão de um novo contrato, então se o cliente optar pelo débito ou cartão, ele ganha o desconto
          [11:22:44] Dani Leite: Segundo: Na troca da forma de pagamento, então se ele paga por boleto e mudar para cartão ou débito ganha, assim como, se voltar para boleto antes de terminar o desconto, perde

         * Com isso concluo que o trecho abaixo (cliente_novo) não deve existir, pois não encontrei nada na documentação referente a isso.
         * 
         */

        //if ($cliente_novo === true){
        //	return false;
        //}

        $dadosFormaCobrancaAnterior = $this->dao->buscarDadosFormaCobranca($formaCobrancaAnterior);
        $dadosFormaCobrancaPosterior = $this->dao->buscarDadosFormaCobranca($formaCobrancaPosterior);

        if ($dadosFormaCobrancaPosterior->tipoCredito == 0) {
            return false;
        }

        //Instancia a DAO
        $finCreditoFuturoDao = new FinCreditoFuturoDAO($this->conn);

        //Instacia a Action
        $creditoFuturoBo = new CreditoFuturo($finCreditoFuturoDao);

        $campanhaPromocional = $this->dao->buscarCampanhaVigente($dadosFormaCobrancaPosterior->tipoCredito);
        if ($campanhaPromocional) {

            if ($this->dao->verificarLimiteSeisMeses($idCliente)) {
                $informacoesHistoricoCliente = new stdClass();
                $informacoesHistoricoCliente->cliente = $idCliente;
                $informacoesHistoricoCliente->usuarioInclusao = $this->dao->buscarIdUsuarioAutomatico();

                $textoObservacao = "Não foi possível conceder o crédito futuro referente à campanha promocional " .
                        "Cód.Identificação " .
                        $campanhaPromocional->cfcpoid . " " .
                        "com Tipo de campanha promocional " .
                        $campanhaPromocional->descricao_tipo_campanha . " " .
                        "devido ao período de 6 meses.";
                $finCreditoFuturoDao->incluirHistoricoCliente($informacoesHistoricoCliente, $textoObservacao);
                unset($informacoesHistoricoCliente);
                return false;
            }

            $ultimaFormaPagamento = $this->dao->buscarUltimaFormaPagamento($idCliente);
            if ($ultimaFormaPagamento->tipoCredito == $dadosFormaCobrancaPosterior->tipoCredito) {
                return false;
            }

            $idCreditoFuturoAtivoSemMovimentacao = $this->dao->buscarCreditoFuturoAtivoSemMovimentacao($idCliente);
            if ($idCreditoFuturoAtivoSemMovimentacao) {
                $creditoFuturoVo = new CreditoFuturoVO();

                $creditoFuturoVo->id = $idCreditoFuturoAtivoSemMovimentacao;

                $creditoFuturoVo->usuarioExclusao = $this->dao->buscarIdUsuarioAutomatico();

                $creditoFuturoVo->observacao = "Crédito futuro excluído por motivo de alteração na forma de cobrança. " .
                        "De: " . $dadosFormaCobrancaAnterior->forcnome . " " .
                        "Para: " . $dadosFormaCobrancaPosterior->forcnome . ". Outro crédito futuro foi criado.";
                $creditoFuturoVo->origem = 4;

                $creditoFuturoBo->excluir($creditoFuturoVo);
                unset($creditoFuturoVo);
            }

            $creditoFuturoMotivoCreditoVo = $this->dao->buscarMotivoCredito($campanhaPromocional->cfcpcfmccoid);
            $creditoFuturoVo = $this->prepararCreditoFuturoVo($idCliente, $campanhaPromocional, $creditoFuturoMotivoCreditoVo);

            if ($creditoFuturoBo->incluir($creditoFuturoVo)) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Prepara creditoFuturoVo convertendo valores da campanha promocional para inclusão de crédito futuro
     *
     * @param int    $idCliente                                           Id do cliente
     * @param object $campanhaPromocional                                 Dados da campanha promocional
     * @param \CreditoFuturoMotivoCreditoVo $creditoFuturoMotivoCreditoVo Dados do motivo do crédito da campanha promocional
     *
     * @return \CreditoFuturoVO
     */
    private function prepararCreditoFuturoVo($idCliente, $campanhaPromocional, $creditoFuturoMotivoCreditoVo) {

        $creditoFuturoVo = new CreditoFuturoVO();

        $creditoFuturoVo->id = 'NULL';

        $creditoFuturoVo->cliente = $idCliente;

        if ($campanhaPromocional->cfcptipo_desconto == 'P') {
            $creditoFuturoVo->tipoDesconto = 1;
        } else if ($campanhaPromocional->cfcptipo_desconto == 'V') {
            $creditoFuturoVo->tipoDesconto = 2;
        }

        $creditoFuturoVo->status = ''; //vazio default

        $creditoFuturoVo->valor = $campanhaPromocional->cfcpdesconto;

        if ($campanhaPromocional->cfcpaplicacao == 'I') {
            $creditoFuturoVo->formaAplicacao = 1;
        } else if ($campanhaPromocional->cfcpaplicacao == 'P') {
            $creditoFuturoVo->formaAplicacao = 2;
        }

        $creditoFuturoVo->saldo = ''; //vazio por default

        if ($campanhaPromocional->cfcpaplicar_sobre == 'M') {
            $creditoFuturoVo->aplicarDescontoSobre = 1;
        } else if ($campanhaPromocional->cfcpaplicar_sobre == 'L') {
            $creditoFuturoVo->aplicarDescontoSobre = 2;
        }

        $creditoFuturoVo->observacao = $campanhaPromocional->cfcpobservacao;

        $creditoFuturoVo->dataInclusao = ''; //vazio default

        $creditoFuturoVo->dataExclusao = ''; //vazio default

        $creditoFuturoVo->dataEncerramento = ''; //vazio default

        $creditoFuturoVo->dataAvaliacao = ''; //vazio default

        $creditoFuturoVo->usuarioInclusao = $this->dao->buscarIdUsuarioAutomatico();

        $creditoFuturoVo->usuarioExclusao = ''; //vazio default

        $creditoFuturoVo->usuarioEncerramento = ''; //vazio default

        $creditoFuturoVo->usuarioAvaliador = ''; //vazio default

        $creditoFuturoVo->protocolo = 'NULL';

        $creditoFuturoVo->contratoIndicado = 'NULL';

        $creditoFuturoVo->obrigacaoFinanceiraDesconto = $campanhaPromocional->cfcpobroid;

        $creditoFuturoVo->formaInclusao = 2; // 1- manual / 2- automatica

        $creditoFuturoVo->qtdParcelas = $campanhaPromocional->cfcpqtde_parcelas;

        $creditoFuturoVo->MotivoCredito = $creditoFuturoMotivoCreditoVo;

        $creditoFuturoVo->origem = 4;

        $creditoFuturoVo->CampanhaPromocional = $campanhaPromocional;

        return $creditoFuturoVo;
    }

    /**
     * Ajusta String Nome Portador Cartão de Crédito
     */
    private function trataNomePortador($nomePortador) {

        $nomePortador = preg_replace('/[^a-zA-Z0-9 ]/', '', $nomePortador);

        $nomePortador = strtoupper($nomePortador);

        return $nomePortador;
    }

    private function formata($str) {
        $busca = array("à", "á", "ã", "â", "ä", "è", "é", "ê", "ë", "ì", "í", "î", "ï", "ò", "ó", "õ", "ô", "ö", "ù", "ú", "û", "ü", "ç", "-", ".", "'");
        $substitui = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c", " ", " ", " ");
        $str = str_replace($busca, $substitui, $str);

        $busca = array("À", "Á", "Ã", "Â", "Ä", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï", "Ò", "Ó", "Õ", "Ô", "Ö", "Ù", "Ú", "Û", "Ü", "Ç");
        $substitui = array("A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C");
        $str = str_replace($busca, $substitui, $str);

        $str = preg_replace('/[^a-zA-Z0-9 ]/', '', $str);

        return trim(strtoupper($str));
    }

}
//fim arquivo
?>