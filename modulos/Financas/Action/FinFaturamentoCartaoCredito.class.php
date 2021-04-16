<?php

/**
 * @author	Emanuel Pires Ferreira
 * @email	epferreira@brq.com
 * @since	10/12/2012
 * 
 * Alterado por Márcio Sampaio em 08/02/2013
 * STI 80835
 * 
 * */
require_once (_MODULEDIR_ . 'Financas/DAO/FinFaturamentoCartaoCreditoDAO.class.php');
require_once (_MODULEDIR_ . 'eSitef/Action/IntegracaoSoftExpress.class.php');

//classe reponsável em enviar os e-mails
require_once _SITEDIR_ . 'modulos/Principal/Action/ServicoEnvioEmail.php';

/**
 * Trata requisições do módulo financeiro para efetuar pagamentos 
 * de títulos com forma de cobrança 'cartão de crédito' 
 */
class FinFaturamentoCartaoCredito {

    /**
     * Fornece acesso aos dados necessarios para o módulo
     * @property FinFaturamentoCartaoCreditoDAO
     */
    private $dao;

    /**
     * Fornece acesso aos objetos do WebService
     * @property IntegracaoSoftExpress
     */
    private $ws;

    /**
     * Fornece acesso aos objetos do WebService Parcelado
     * @property IntegracaoSoftExpress
     */
    private $ws2;

    /**
     * Fornece acesso aos objetos do PHPMailer
     * @property PHPMailer
     */
    private $mail;
    private $file;

    /**
     * Construtor, configura acesso a dados e parâmetros iniciais do módulo
     */
    public function __construct() {
        global $conn;

        $this->dao = new FinFaturamentoCartaoCreditoDAO($conn);
    }

    /**
     * Função responsável chamar a DAO que calcula o próximo dia útil
     * @return date
     */
    public function retornaProximoDiaUtil() {
        return $this->dao->retornaProximoDiaUtil();
    }

    /**
     * Função responsável por chamar a DAO que devolve todas 
     * as formas de cobrança que são cartão de crédito
     * 
     * @return Array
     */
    public function formasCobrancaCartaoCredito() {
        return $this->dao->retornaFormasCobrancaCartaoCredito();
    }

    /**
     * Função responsável por chamar a DAO que devolve todos os títulos 
     * abertos de acordo com a forma de cobrança e vencimento
     * 
     * @param Array $formasCobranca - array de todas as formas de cobrança que são cartões de crédito
     * @return Array
     */
    public function buscaTitulosAbertos($formasCobranca) {
        //instancia a classe de pagamento de cartão de crédito
        $this->ws = new IntegracaoSoftExpress('payment');

        $tentativasPagamento = $this->ws->tentativasPagamento;

        return $this->dao->buscaTitulosAbertos($formasCobranca, $tentativasPagamento);
    }

    /**
     * Retorna a quantidade de transações efetuadas na data corrente:
     * -> Não enviadas
     * -> Pendentes de pagamento
     * -> Recebidas
     *  
     * @return array
     */
    public function retornarTransacoes() {

        return $this->dao->retornarTransacoes();
    }

    /**
     * Função responsável por efetuar todos os passos necessários para o pagamento de um título
     * 
     * @param integer $clioid      - id do cliente
     * @param integer $titoid      - id do título
     * @param float   $valort      - valor do título
     * @param date    $diaCobranca - data que será feita a cobrança do título
     * 
     * @return Array $reporte
     */
    public function processaPagamento($clioid, $titoid, $valort, $diaCobranca, $acao = 'NULL', $modo = null) {
        ob_start();

        //instancia a classe com a opção do modo de pagamento de cartão de crédito
        $this->ws = new IntegracaoSoftExpress('payment');

        //usuário AUTOMATICO para processos onde não existe autenticação
        $cd_usuario = 2750;

        //verifica se realiza uma nova transação
        $novaTransacao = false;

        //condição de parada para o while das 3 tentativas de pagamento caso aconteça timeout
        $continuaProcesso = false;

        //armazena quantidade de consultas realizadas pelo método getStatus
        $tentativasPagamento = 0;

        //transactionStatus permitidos para baixar o título no bd
        # CON -> Pagamento confirmado pela instituição financeira.
        $statusPermitidosBaixa = array('CON');

        //pesquisa dados do cliente para enviar por email
        $dadosCliente = $this->dao->buscaDadosCliente($clioid);


        try {

            //pesquisar o NIT e se ctcsucesso IS FALSE
            $consultaNit = $this->dao->pesquisarNit($clioid, $titoid);

            //verifica se tem nit e se o transactionStatus é vazio
            //consumir o getStatus SOMENTE SE NÃO OBTIVER RESPOSTA do método doHashPayment
            if (!empty($consultaNit) && $consultaNit['status'] === '') {

                //verifica o status do pagamento no método getStatus no WEBSERVICE
                $retNit = $this->ws->getStatus($consultaNit['nit']);

                $r = fopen(_SITEDIR_ . 'arq_financeiro/log_cartao.txt', 'a+');
                $cartao = $retNit->paymentResponse->responseCode . ' -- moduledir:' . _MODULEDIR_;
                fwrite($r, $cartao);
                fclose($r);

                //se a transação está ok e transactionStatus é permitido, baixa o título
                if ($retNit->paymentResponse->responseCode == 0 && in_array($retNit->paymentResponse->transactionStatus, $statusPermitidosBaixa)) {

                    //busca dados do cartao atual
                    $dadosCartao = $this->dao->buscaDadosCartao($clioid);

                    //se houver cartão cadastrado
                    if ($dadosCartao) {

                        //efetua a baixa no título no bd
                        $reporte = $this->baixarTitulo($clioid, $dadosCartao, $titoid, $retNit, $valort, $cd_usuario, $diaCobranca, $consultaNit);
                    } else {
                        //HOMOLOGACAO
                        print "Cliente sem cartão de crédito cadastrado!<br />";

                        $reporte['titulo'] = $titoid;
                        $reporte['dadosClienteSemCartao'] = $dadosCliente;
                        $reporte['acao'] = 'Cliente sem cartão de crédito cadastrado!';
                        $reporte['code'] = 3;

                        $this->dao->incluirTransacaoCartaoErro($consultaNit['idTransacao'], $reporte['acao']);
                    }
                } else {
                    //efetua nova transação
                    $novaTransacao = true;
                }
            } else {
                #realiza uma nova transação com um novo NIT
                $novaTransacao = true;
            }


            # Um nova transação é permitida, quando :
            # - na pesquisa do nit ($retornoNit) não houver retorno e o trasactionStatus não está vazio
            # - se houver retorno na pesquisa do nit ($retNit) mas a resposta da transação não for OK, é realizada uma nova transação

            if ($novaTransacao) {

                //inicia transação do banco de dados
                $this->dao->begin();

                //inicia transação
                $idTransacao = $this->dao->incluirTransacaoCartao($clioid, $titoid, 0, false, 0);

                //converte valor para centavos
                $valorc = $this->_converteValor($valort);

                //inicia processo de pagamento com webservice
                $retBegin = $this->ws->beginTransaction($valorc, $titoid, $idTransacao);

                $r = fopen(_SITEDIR_ . 'arq_financeiro/log_cartao3.txt', 'a+');
                $cartao = '$valorc:' . $valorc . ' titoid:' . $titoid . ' idTransacao:' . $idTransacao;
                fwrite($r, $cartao);
                fclose($r);

                //se houve retorno da conexao
                if (is_object($retBegin)) {

                    //se houve sucesso na transação
                    if ($retBegin->transactionResponse->responseCode == 0) {

                        $nit = $retBegin->transactionResponse->nit;

                        $r = fopen(_SITEDIR_ . 'arq_financeiro/log_cartao2.txt', 'a+');
                        $cartao = 'nit:' . $nit . '';
                        fwrite($r, $cartao);
                        fclose($r);

                        //busca dados do cartao atual
                        $dadosCartao = $this->dao->buscaDadosCartao($clioid);

                        //se houver cartão cadastrado
                        if ($dadosCartao) {

                            //tenta pagar um vez, se não retornar nenhuma resposta, começa a efetuar as tentativas
                            //verificando dentro do loop com o método getStatus se o pagamento foi concluído transactionStatus == 'CON'
                            do {

                                //inicia a contagem do tempo para o timeout
                                $iniciaTempoTimeOut = time();
                                $r = fopen(_SITEDIR_ . 'arq_financeiro/log_cartao2.txt', 'a+');
                                $cartao = $retNit->paymentResponse->responseCode . ' -- moduledir:' . _MODULEDIR_;
                                fwrite($r, $cartao);
                                fclose($r);

                                //tenta efetuar pagamento
                                $retPayment = $this->ws->doHashPayment($nit, $dadosCartao['hashcartao'], $dadosCartao['autorizadora'], 1, 4, true, $clioid, $dadosCartao['nome_cartao']);

                                //finaliza e contabiliza o tempo de timeout
                                $verificaTempoTimeOut = $this->verificaTempoTimeOut($iniciaTempoTimeOut);

                                //se não houver resposta do doHashPayment, então verifica o getStatus
                                if ($verificaTempoTimeOut === true || !isset($retPayment->paymentResponse)) {

                                    //verifica os status do pagamento
                                    $verificaNit = $this->ws->getStatus($nit);

                                    // consulta se já foi pago no WEBSERVICE
                                    if ($verificaNit->paymentResponse->responseCode == 0 && in_array($verificaNit->paymentResponse->transactionStatus, $statusPermitidosBaixa)) {

                                        $consultaNit['idTransacao'] = $idTransacao;
                                        $consultaNit['nit'] = $nit;
                                        //efetua a baixa no título no bd caso a transação foi concluída no WEBSERVICE
                                        $reporte = $this->baixarTitulo($clioid, $dadosCartao, $titoid, $verificaNit, $valort, $cd_usuario, $diaCobranca, $consultaNit);

                                        //interrompe o loop de tentativas
                                        $continuaProcesso = false;
                                    } elseif (!isset($verificaNit->paymentResponse->transactionStatus)) {

                                        //continua com o processo de tentativas, caso transactionStatus retorne vazio
                                        $continuaProcesso = true;
                                    } else {

                                        $reporte['acao'] = $verificaNit->paymentResponse->message;
                                        $reporte['code'] = $verificaNit->paymentResponse->responseCode;

                                        $continuaProcesso = false;
                                    }
                                } else {

                                    // se o retorno do WEBSERVICE for OK e transactionStatus for == 'CON', então efetiva o pagamento no bd
                                    if ($retPayment->paymentResponse->responseCode == 0 && in_array($retPayment->paymentResponse->transactionStatus, $statusPermitidosBaixa)) {

                                        //efetua o pagamento
                                        $reporte = $this->efetuarPagamentoTitulo($clioid, $dadosCartao, $titoid, $retPayment, $valorc, $cd_usuario, $diaCobranca, $idTransacao, $nit);

                                        $continuaProcesso === false;
                                    } else {

                                        //registra o erro na tabela controle_transacao_cartao
                                        $this->dao->incluirTransacaoCartaoErro($idTransacao, $retPayment->paymentResponse->message, $nit, $retPayment->paymentResponse->transactionStatus);

                                        //inclui histório de pagamento
                                        $ccchoid = $this->dao->incluiHistoricoPagamento($clioid, $dadosCartao['cccoid'], $titoid, $retPayment->paymentResponse);

                                        //HOMOLOGACAO
                                        print "Falha na cobrança do título " . $titoid . "!<br />";

                                        //retorna para registro das atividades
                                        $reporte['acao'] = $retPayment->paymentResponse->message;
                                        $reporte['code'] = $retPayment->paymentResponse->responseCode;

                                        $continuaProcesso === false;
                                    }
                                }

                                //incrementa a contagem de tentativas
                                $tentativasPagamento++;
                            } while ($tentativasPagamento < 3 && $verificaTempoTimeOut === true && $continuaProcesso === true);


                            # se foram efetuadas 3 tentativas de consulta dispara email para Administrador
                            if ($tentativasPagamento === 3) {

                                //HOMOLOGACAO
                                print "Tempo Excedido apos 3 tentativas !<br />";

                                $this->enviaAdmEmailTimeOut($clioid, $titoid, $dadosCliente);

                                //registra o erro na tabela controle_transacao_cartao
                                $this->dao->incluirTransacaoCartaoErro($idTransacao, $retPayment, $nit);

                                $reporte['acao'] = 'Tempo Excedido após 3 tentativas ! ';
                                $reporte['code'] = 100;
                            }
                        } else {
                            //HOMOLOGACAO
                            print "Cliente sem cartão de crédito cadastrado!<br />";

                            $reporte['titulo'] = $titoid;
                            $reporte['dadosClienteSemCartao'] = $dadosCliente;
                            $reporte['acao'] = 'Cliente sem cartão de crédito cadastrado !';
                            $reporte['code'] = 3;

                            $this->dao->incluirTransacaoCartaoErro($idTransacao, $reporte['acao'], $nit);
                        }
                    } else {
                        //registra o erro na tabela controle_transacao_cartao
                        $this->dao->incluirTransacaoCartaoErro($idTransacao, $retBegin->transactionResponse->message);

                        //HOMOLOGACAO
                        print "Erro: " . $retBegin->transactionResponse->message . "!<br />";

                        //retorna para registro das atividades
                        $reporte['acao'] = $retBegin->transactionResponse->message;
                        $reporte['code'] = $retBegin->transactionResponse->responseCode;
                    }
                } else {
                    //qualquer erro referente a comunicação com o e-Sitef
                    $this->dao->incluirTransacaoCartaoErro($idTransacao, $retBegin);

                    //HOMOLOGACAO
                    print "Erro: " . $retBegin . "!<br />";

                    $reporte['acao'] = $retBegin;
                    $reporte['code'] = 0;
                }

                $this->dao->commit();
            }
        } catch (Exception $e) {

            $this->dao->rollback();

            $reporte['acao'] = 'Erro no processamento - Sascar';
            $reporte['code'] = 0;
        }


        # se o processamento está vindo do Cron, então, imprime todos os prints na tela
        # se não, limpa o buffer e não manda os prints para o browser.
        if ($acao === 'cron') {
            ob_end_flush();
        } else {
            ob_end_clean();
        }

        return $reporte;
    }

    /**
     * Função responsável por efetuar todos os passos necessários para o PAGAMENTO PARCELADO
     * usando a função doPayment do webservice de integração com a Softexpress
     * 
     * @param integer $clioid       - id do cliente
     * @param integer $titoid       - id do título
     * @param float   $valort       - valor do título
     * @param date    $diaCobranca  - data que será feita a cobrança do título
     * @param int     $parcelas     - quantidade de parcelas 
     * @param int     $authorizedId - autorizadora (master, visa, ou outra) 
     * @param string  $numeroCartao - número do cartão de crédito
     * @param date    $dataExpiracaoCartao - data de vencimento do cartão
     * @param int     $codigoSeguranca - número com 3 dígitos (atualmente é obrigatório apenas para pagamento parcelado, não é gravado em banco)
     * @param string  $acao - controla exibição de dados na tela 
     * 
     * @return Array $reporte
     */
    public function processarPagamentoParcelado($clioid, $titoid, $valort, $diaCobranca, $parcelas, $authorizedId, $numeroCartao, $dataExpiracaoCartao, $codigoSeguranca, $acao = 'NULL', $origem = NULL, $nomePortador = '') {

        ob_start();

        //instancia a classe com a opção do modo de parcelamento no cartão de crédito
        $this->ws2 = new IntegracaoSoftExpress('parcelado');

        //usuário AUTOMATICO para processos onde não existe autenticação
        $cd_usuario = 2750;

        //verifica se realiza uma nova transação
        //$novaTransacao = false;
        //condição de parada para o while das 3 tentativas de pagamento caso aconteça timeout
        $continuaProcesso = false;

        //armazena quantidade de consultas realizadas pelo método getStatus
        $tentativasPagamento = 0;

        //transactionStatus permitidos para baixar o título no bd
        # CON -> Pagamento confirmado pela instituição financeira.
        $statusPermitidosBaixa = array('CON');

        try {

            //inicia transação
            $idTransacao = $this->dao->incluirTransacaoCartao($clioid, $titoid, 0, false, 0);

            if ($origem != 'CORE') {
                //inicia transação do banco de dados
                $this->dao->begin();
            }

            //converte valor para centavos
            $valorc = $this->_converteValor($valort);

            $retBegin = $this->ws2->beginTransaction($valorc, $titoid, $idTransacao);

            //se houve retorno da conexao
            if (is_object($retBegin)) {

                //se houve sucesso na transação
                if ($retBegin->transactionResponse->responseCode == 0) {

                    $nit = $retBegin->transactionResponse->nit;

                    //busca dados do cartao atual
                    $dadosCartao = $this->dao->buscaDadosCartao($clioid);

                    //se houver cartão cadastrado
                    if ($dadosCartao) {

                        //tenta pagar um vez, se não retornar nenhuma resposta, começa a efetuar as tentativas
                        //verificando dentro do loop com o método getStatus se o pagamento foi concluído transactionStatus == 'CON'
                        do {

                            //inicia a contagem do tempo para o timeout
                            $iniciaTempoTimeOut = time();

                            //tenta efetuar pagamento
                            $retPayment = $this->ws2->doPayment($nit, $authorizedId, true, $numeroCartao, $dataExpiracaoCartao, $codigoSeguranca, $clioid, 4, $parcelas, $nomePortador);

                            //finaliza e contabiliza o tempo de timeout
                            $verificaTempoTimeOut = $this->verificaTempoTimeOut($iniciaTempoTimeOut);

                            //se não houver resposta do doPayment, então verifica o getStatus
                            if ($verificaTempoTimeOut === true || !isset($retPayment->paymentResponse)) {

                                //verifica os status do pagamento
                                $verificaNit = $this->ws2->getStatus($nit);

                                // consulta se já foi pago no WEBSERVICE
                                if ($verificaNit->paymentResponse->responseCode == 0 && in_array($verificaNit->paymentResponse->transactionStatus, $statusPermitidosBaixa)) {

                                    $consultaNit['idTransacao'] = $idTransacao;
                                    $consultaNit['nit'] = $nit;

                                    //efetua a baixa no título no bd caso a transação foi concluída no WEBSERVICE
                                    $reporte = $this->baixarTitulo($clioid, $dadosCartao, $titoid, $verificaNit, $valort, $cd_usuario, $diaCobranca, $consultaNit, "parcelado", $parcelas, $origem);

                                    //interrompe o loop de tentativas
                                    $continuaProcesso = false;
                                } elseif (!isset($verificaNit->paymentResponse->transactionStatus)) {

                                    //continua com o processo de tentativas, caso transactionStatus retorne vazio
                                    $continuaProcesso = true;
                                }
                            } else {

                                // se o retorno do WEBSERVICE for OK e transactionStatus for == 'CON', então efetiva o pagamento no bd
                                if ($retPayment->paymentResponse->responseCode == 0 && in_array($retPayment->paymentResponse->transactionStatus, $statusPermitidosBaixa)) {

                                    //efetua o pagamento
                                    $reporte = $this->efetuarPagamentoTitulo($clioid, $dadosCartao, $titoid, $retPayment, $valorc, $cd_usuario, $diaCobranca, $idTransacao, $nit, 'parcelado', $parcelas, $origem);

                                    $continuaProcesso === false;
                                } else {

                                    //registra o erro na tabela controle_transacao_cartao
                                    $this->dao->incluirTransacaoCartaoErro($idTransacao, $retPayment->paymentResponse->message, $nit, $retPayment->paymentResponse->transactionStatus);

                                    //inclui histório de pagamento
                                    $ccchoid = $this->dao->incluiHistoricoPagamento($clioid, $dadosCartao['cccoid'], $titoid, $retPayment->paymentResponse);

                                    //HOMOLOGACAO
                                    print "Falha na cobrança do título " . $titoid . "!<br />";

                                    ## trata retorno da transação (caso erro) de pagamento com a e-SiTef ##
                                    //caso a softexpress esteja offline
                                    if (strstr($retPayment->paymentResponse->message, 'offline')) {
                                        $reporte['acao'] = "Sistema de pagamento indisponível. ";
                                    }

                                    if ($retPayment->paymentResponse->transactionStatus == 'INV') {
                                        $reporte['acao'] = "Transação Inválida. Falha ao inicar transação.";
                                    } elseif ($retPayment->paymentResponse->transactionStatus == 'PPC') {
                                        $reporte['acao'] = "Pagamento pendente de confirmação";
                                    } elseif ($retPayment->paymentResponse->transactionStatus == 'PPN') {
                                        $reporte['acao'] = "Pagamento pendente não confirmado (cancelado).";
                                    } elseif ($retPayment->paymentResponse->transactionStatus == 'NEG') {
                                        $reporte['acao'] = "Pagamento negado pela Instituição financeira.";
                                    } elseif ($retPayment->paymentResponse->transactionStatus == 'CAN') {
                                        $reporte['acao'] = "Pagamento cancelado (não efetuado) por falha na comunicação com o SiTef";
                                    } elseif ($retPayment->paymentResponse->transactionStatus == 'ERR') {
                                        $reporte['acao'] = "Erro na comunicação com a autorizadora. Tente novamente.";
                                    } elseif ($retPayment->paymentResponse->transactionStatus == 'BLQ') {
                                        $reporte['acao'] = "A transação será bloqueada após várias tentativas de consulta de cartão.";
                                    } elseif ($retPayment->paymentResponse->transactionStatus == 'EXP') {
                                        $reporte['acao'] = "Transação expirada.";
                                    } elseif ($retPayment->paymentResponse->transactionStatus == 'TNE') {
                                        $reporte['acao'] = "Erro na transação.";
                                    } else {
                                        $reporte['acao'] = $retPayment->paymentResponse->message;
                                    }

                                    //retorna para registro das atividades
                                    $reporte['code'] = $retPayment->paymentResponse->responseCode;

                                    $continuaProcesso === false;
                                }
                            }

                            //incrementa a contagem de tentativas
                            $tentativasPagamento++;
                        } while ($tentativasPagamento < 3 && $verificaTempoTimeOut === true && $continuaProcesso === true);


                        # se foram efetuadas 3 tentativas de consulta dispara email para Administrador
                        if ($tentativasPagamento === 3) {

                            //HOMOLOGACAO
                            print "Tempo Excedido apos 3 tentativas !<br />";

                            //pesquisa dados do cliente para enviar por email
                            $dadosCliente = $this->dao->buscaDadosCliente($clioid);

                            $this->enviaAdmEmailTimeOut($clioid, $titoid, $dadosCliente);

                            //registra o erro na tabela controle_transacao_cartao
                            $this->dao->incluirTransacaoCartaoErro($idTransacao, $retPayment, $nit);

                            $reporte['acao'] = 'Tempo Excedido após 3 tentativas ! ';
                            $reporte['code'] = 100;
                        }
                    } else {
                        //HOMOLOGACAO
                        print "Cliente sem cartão de crédito cadastrado!<br />";

                        $reporte['acao'] = 'Cliente sem cartão de crédito cadastrado !';
                        $reporte['code'] = 0;

                        $this->dao->incluirTransacaoCartaoErro($idTransacao, $reporte['acao'], $nit);
                    }
                } else {
                    //registra o erro na tabela controle_transacao_cartao
                    $this->dao->incluirTransacaoCartaoErro($idTransacao, $retBegin->transactionResponse->message);

                    //HOMOLOGACAO
                    print "Erro: " . $retBegin->transactionResponse->message . "!<br />";

                    //retorna para registro das atividades
                    $reporte['acao'] = $retBegin->transactionResponse->message;
                    $reporte['code'] = $retBegin->transactionResponse->responseCode;
                }
            } else {
                //qualquer erro referente a comunicação com o e-Sitef
                $this->dao->incluirTransacaoCartaoErro($idTransacao, $retBegin);

                //HOMOLOGACAO
                print "Erro: " . $retBegin . "!<br />";

                $reporte['acao'] = $retBegin;
                $reporte['code'] = 0;
            }

            if ($origem != 'CORE') {
                $this->dao->commit();
            }
        } catch (Exception $e) {

            if ($origem != 'CORE') {
                $this->dao->rollback();
            }

            $reporte['acao'] = 'Erro no processamento - Sascar';
            $reporte['code'] = 0;
        }

        # se o processamento está vindo do Cron, então, imprime todos os prints na tela
        # se não, limpa o buffer e não manda os prints para o browser.
        if ($acao === 'cron') {
            ob_end_flush();
        } else {
            ob_end_clean();
        }

        return $reporte;
    }

    /**
     * Função responsável por baixar título no banco
     * caso o retorno do webservice seja OK
     *
     * @param inteiro $clioid - código do cliente
     * @param Array $dadosCartao - dados do cartão de crédito do cliente 
     * @param inteiro $titoid - id do título
     * @param Array $retNit -  informações do nit do título
     * @param inteiro $valort - valor do título
     * @param inteiro $cd_usuario - código de usuário automático
     * @param date $diaCobranca - data da nova cobrança
     * @param Array $consultaNit - dados no nit existente
     *
     * @return Array $reporte
     */
    private function baixarTitulo($clioid, $dadosCartao, $titoid, $retNit, $valort, $cd_usuario, $diaCobranca, $consultaNit, $modo = NULL, $quant_parcelas = NULL, $origem = NULL) {

        try {

            if ($origem != 'CORE') {
                //inicia transação do banco de dados
                $this->dao->begin();
            }

            //inclui histório de pagamento
            $ccchoid = $this->dao->incluiHistoricoPagamento($clioid, $dadosCartao['cccoid'], $titoid, $retNit->paymentResponse);

            //confirma Pagamento
            $this->dao->confirmaPagamento($titoid, $ccchoid, $valort, $cd_usuario);

            //busca nova data do título de substituição
            $novaData = $this->_dataNovoTitulo($diaCobranca);

            //inclui título de substituição
            $this->dao->insereTituloCredito($titoid, $novaData, $modo, $quant_parcelas);

            //conclui transação executando update
            $this->dao->incluirTransacaoCartao($clioid, $titoid, $consultaNit['idTransacao'], true, $ccchoid, $consultaNit['nit'], $retNit->paymentResponse->transactionStatus);

            //insere linha no arquivo cado não seja o ambiente de produção
            if ($_SESSION['servidor_teste'] != 0) {
                //insere uma linha no arquivo
                //$cabecalho = array("0","Titulo","Cod Estabelecimento","Numero Resumo","Numero Comprovante","",                             "NSU","CARTAO","Valor Bruto","Total Parcelas","Valor Pago","Valor Liquido","Data Credito","","Numero Parcela","","","","","","","","Taxa Administrativa");
                $linha = array("10", $titoid, '1111', '12121', "1234", "", $retNit->paymentResponse->sitefUSN, "123456", $valort, $quant_parcelas, $valort, $valort, date('Ymd'), "", "inserir manual", "", "", "", "", "", "", "", "inserir manual");
                fputcsv($this->file, $linha, ';');
            }

            //HOMOLOGACAO
            print "Título " . $titoid . " pago com sucesso !<br />";

            //retorna para registro das atividades
            $reporte['acao'] = $retNit->paymentResponse->message;
            $reporte['code'] = $retNit->paymentResponse->responseCode;
            $reporte['resposta'] = 'OK';

            if ($origem != 'CORE') {
                $this->dao->commit();
            }
        } catch (Exception $e) {

            if ($origem != 'CORE') {
                $this->dao->rollback();
            }

            $reporte['acao'] = 'Erro no processamento - Sascar';
            $reporte['code'] = 0;
        }

        return $reporte;
    }

    /**
     * Função responsável por efetuar o pagamento no WEBSERVICE
     *
     * @param inteiro $clioid - código do cliente
     * @param Array $dadosCartao - dados do cartão de crédito do cliente
     * @param inteiro $titoid - id do título
     * @param Array $retPayment -  informações do retorno do pagamento
     * @param inteiro $valorc - valor do título
     * @param inteiro $cd_usuario - código de usuário automático
     * @param date $diaCobranca - data da nova cobrança
     * @param inteiro $idTransacao - id da transação do banco para identificação no histórico de pagamentos
     * @param string $nit - código da transação de pagamento
     *
     * @return Array $reporte
     */
    private function efetuarPagamentoTitulo($clioid, $dadosCartao, $titoid, $retPayment, $valorc, $cd_usuario, $diaCobranca, $idTransacao, $nit, $modo = NULL, $quant_parcelas = NULL, $origem = NULL) {

        try {

            if ($origem != 'CORE') {
                //inicia transação do banco de dados
                $this->dao->begin();
            }

            //inclui histório de pagamento
            $ccchoid = $this->dao->incluiHistoricoPagamento($clioid, $dadosCartao['cccoid'], $titoid, $retPayment->paymentResponse);

            // Convertido valor
            $valort = $this->_converteIntFloat($valorc);

            //confirma Pagamento
            $this->dao->confirmaPagamento($titoid, $ccchoid, $valort, $cd_usuario);

            //busca nova data do título de substituição
            $novaData = $this->_dataNovoTitulo($diaCobranca);

            //inclui título de substituição
            $this->dao->insereTituloCredito($titoid, $novaData, $modo, $quant_parcelas);

            //conclui transação
            $this->dao->incluirTransacaoCartao($clioid, $titoid, $idTransacao, true, $ccchoid, $nit, $retPayment->paymentResponse->transactionStatus);

            //insere linha no arquivo cado não seja o ambiente de produção
            if ($_SESSION['servidor_teste'] != 0) {
                //insere uma linha no arquivo
                $testeTaxa = $valorc / 100 * 3;
                $trataTaxa = number_format($testeTaxa, 0, '', '');
                $valorl = $valorc - $trataTaxa;
                //$cabecalho = array("0","Titulo","Cod Estabelecimento","Numero Resumo","Numero Comprovante","",                             "NSU","CARTAO","Valor Bruto","Total Parcelas","Valor Pago","Valor Liquido","Data Credito","","Numero Parcela","","","","","","","","Taxa Administrativa");
                $linha = array("10", $titoid, '1111', '12121', "1234", "", $retNit->paymentResponse->sitefUSN, "123456", $valorc, $quant_parcelas, $valort, $valorl, date('Ymd'), "", "inserir manual", "", "", "", "", "", "", "", $trataTaxa);
                fputcsv($this->file, $linha, ';');
            }

            //HOMOLOGACAO
            print "Título " . $titoid . " pago com sucesso!<br />";

            //retorna para registro das atividades
            $reporte['acao'] = $retPayment->paymentResponse->message;
            $reporte['code'] = $retPayment->paymentResponse->responseCode;
            $reporte['resposta'] = 'OK';

            if ($origem != 'CORE') {
                $this->dao->commit();
            }
        } catch (Exception $e) {

            if ($origem != 'CORE') {
                $this->dao->rollback();
            }

            $reporte['acao'] = 'Erro no processamento - Sascar';
            $reporte['code'] = 0;
        }

        return $reporte;
    }

    /**
     * Helper para calcular o tempo consumido em um processo 
     *
     * @param int $iniciaTempoTimeOut - valor (em segundos) em formato inteiro
     *
     * @return boolean
     */
    public function verificaTempoTimeOut($iniciaTempoTimeOut) {

        //calcula o tempo da transação    
        $tempoFinal = time() - $iniciaTempoTimeOut;

        // testar com 2 sleep(5) entre as chamadas dos métodos: iniciaTempoTimeOut e finalizaTempoTimeOut
        if ($tempoFinal >= 90) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Helper que converte o valor do título de Float para Int
     * 
     * @param float $entrada - valor em formato float
     * 
     * @return integer $valor - valor convertido em formato Int
     */
    private function _converteValor($entrada) {
        return number_format($entrada, 2, "", "");
    }

    /**
     * Helper que converte o valor do título de Int para Float
     *
     * @param int $valor - valor em formato int
     *
     * @return float $valor - valor convertido em formato Float
     */
    private function _converteIntFloat($valor) {
        return $valor / 100;
    }

    /**
     * Helper que adiciona 30 dias a data de cobrança do título
     * 
     * @param date $data - data de pagamento
     * 
     * @return date 
     */
    private function _dataNovoTitulo($data) {
        list($dia, $mes, $ano) = explode("/", $data);

        return date('d/m/Y', mktime(0, 0, 0, $mes, $dia + 30, $ano));
    }

    /**
     * Retorna dados da forma de cobrança cartão de crédito por cliente informado
     * @autor Márcio Sampaio Ferreira
     * @email marcioferreira@brq.com
     *
     * @param $clioid
     *
     * @return array
     */
    public function getDadosCartao($clioid) {

        return $this->dao->buscaDadosCartao($clioid);
    }

    /**
     * Função responsável por enviar o e-mail de retorno
     * dos processos de pagamento.
     * 
     * @param Array $informações - dados de retorno das transações
     * 
     * @return boolean
     */
    public function enviaEmail($informacoes, $dadosSemCartao) {
        //novo objeto genérico
        $htmlEmail = new stdClass();

        //instância de classe de configurações de servidores para envio de email
        $servicoEnvioEmail = new ServicoEnvioEmail();

        //pesquisa o layout de e-mail relatório geral
        $dadosLayoutEmailRelGeral = $this->dao->getDadosCorpoEmail('Envia relatorio de transacoes por cartao de credito');
        //se houver informações de transações

        if (is_array($informacoes)) {

            #corpo e-mail relatório geral
            //não enviadas
            $htmlEmail->corpo_email_geral = str_replace('[naoEnviadas]', ($informacoes['naoEnviadas'] ? $informacoes['naoEnviadas'] : 0), $dadosLayoutEmailRelGeral->corpo_email);

            //pendentes de pagamento
            $htmlEmail->corpo_email_geral = str_replace('[pendentePagamento]', ($informacoes['pendentePagamento'] ? $informacoes['pendentePagamento'] : 0), $htmlEmail->corpo_email_geral);

            //recebidas
            $htmlEmail->corpo_email_geral = str_replace('[recebidas]', ($informacoes['recebidas'] ? $informacoes['recebidas'] : 0), $htmlEmail->corpo_email_geral);

            //clientes sem cartão de crédito cadastrado
            $htmlEmail->corpo_email_geral = str_replace('[semCartao]', ($informacoes[0]['semCartao'] ? $informacoes[0]['semCartao'] : 0), $htmlEmail->corpo_email_geral);


            //verifica se há dados de cliente que não possuem cadastro de cartão de crédito
            if (is_array($dadosSemCartao)) {

                //pesquisa o layout de e-mail de clientes sem cartão de crédito cadastrado
                $dadosLayoutEmailClienteSemCartao = $this->dao->getDadosCorpoEmail('Relatorio de clientes sem cartao de credito cadastrado');

                //pesquisa o layout de e-mail da linha de clientes sem cartão de crédito cadastrado
                $dadosLayoutEmailLinhaCliente = $this->dao->getDadosCorpoEmail('Linha dados clientes');

                //separa as colunas através do parâmetro  FIM_COLUNA
                $colunaDadosClientes = explode('[FIM_COLUNA]', $dadosLayoutEmailLinhaCliente->corpo_email);

                $pattern = "@<tr(.*?)</tr>@si";
                preg_match_all($pattern, $dadosLayoutEmailClienteSemCartao->corpo_email, $matches);
                $modelo = $matches[0][2];
                $colunas = '';

                $coluna1 = '';
                $coluna2 = '';

                foreach ($dadosSemCartao as $dadosCliente) {

                    $colunas .= $modelo;

                    if ($dadosCliente['clitipo'] == 'F') {
                        $label = 'CPF';
                        $documento = $dadosCliente['clino_cpf'];
                    } elseif ($dadosCliente['clitipo'] == 'J') {
                        $label = 'CNPJ';
                        $documento = $dadosCliente['clino_cgc'];
                    }

                    //nome cliente
                    $coluna1 = str_replace('[clinome]', $dadosCliente['clinome'], $colunaDadosClientes[0]);

                    //label documento (cpf ou cnpj)
                    $coluna1 = str_replace('[label]', $label, $coluna1);

                    //documento cliente
                    $coluna1 = str_replace('[documento]', $documento, $coluna1);

                    //título
                    $coluna2 = str_replace('[titulo]', $dadosCliente['titulo'], $colunaDadosClientes[1]);

                    $colunas = str_replace('[COLUNA1_DADOS_CLIENTES]', $coluna1, $colunas);
                    $colunas = str_replace('[COLUNA2_DADOS_CLIENTES]', $coluna2, $colunas);
                }
            }

            $htmlEmail->corpo_email_sem_cartao = str_replace($modelo, $colunas, $dadosLayoutEmailClienteSemCartao->corpo_email);

            //data atual
            $htmlEmail->corpo_email_geral = str_replace('[dataHoje]', $dadosLayoutEmailRelGeral->data_atual, $htmlEmail->corpo_email_geral);

            //hora atual
            $htmlEmail->corpo_email_geral = str_replace('[horaAgora]', $dadosLayoutEmailRelGeral->hora_atual, $htmlEmail->corpo_email_geral);

            //concatena o html
            $mensagem = $htmlEmail->corpo_email_geral . $htmlEmail->corpo_email_sem_cartao;
        } else {
            //envia layout de que não houve dados para exibir
            //pesquisa o layout de e-mail não houve dados para exibir
            $dadosLayoutEmailRelSemDados = $this->dao->getDadosCorpoEmail('Nao ha transacoes por cartao de credito para exibir');

            //data atual
            $htmlEmail->corpo_email = str_replace('[dataHoje]', $dadosLayoutEmailRelSemDados->data_atual, $dadosLayoutEmailRelSemDados->corpo_email);

            //hora atual
            $htmlEmail->corpo_email = str_replace('[horaAgora]', $dadosLayoutEmailRelSemDados->hora_atual, $htmlEmail->corpo_email);

            $mensagem = $htmlEmail->corpo_email;
        }


        //e-mail de destino do relatório
        $emailUsuario = $this->dao->getEmailEnvioRelatorio();

        $lista_email_envio = $emailUsuario->pcsidescricao;

        //separa os e-mails
        $email_envio = explode(';', $lista_email_envio);

        //envia o email para a lista
        foreach ($email_envio as $email) {

            $email_envio = trim($email);

            //recupera e-mail de testes
            if ($_SESSION['servidor_teste'] == 1) {

                //limpa a variável com os e-mails
                $email_envio = "";

                //recupera email de testes da tabela parametros_configuracoes_sistemas_itens
                $emailTeste = $this->dao->getEmailTeste();

                if (!is_object($emailTeste)) {
                    throw new exception('E necessario informar um e-mail de teste em ambiente de testes.');
                }

                $email_envio = $emailTeste->pcsidescricao;
            }

            //envia o email
            $envio_email = $servicoEnvioEmail->enviarEmail(
                    $email_envio, $dadosLayoutEmailRelGeral->assunto_email, $mensagem, $arquivo_anexo = null, $email_copia = null, $email_copia_oculta = null, $dadosLayoutEmailRelGeral->servidor, $emailTeste->pcsidescricao//$email_desenvolvedor = null
            );

            if (!empty($envio_email['erro'])) {
                throw new exception($envio_email['msg']);
            }
        }

        //imprime email que será enviado para o cliente em ambiente de testes
        if ($_SESSION['servidor_teste'] == 1) {
            print($mensagem);
            print('<br/><br/>');
        }


        return true;
    }

    /**
     * Função responsável por enviar o e-mail de retorno para o Administrador
     * caso exceda o timeout e 3 tentativas.
     *
     * @return boolean
     */
    public function enviaAdmEmailTimeOut($clioid, $titoid, $dadosCliente) {

        if ($dadosCliente['clitipo'] == 'F') {
            $label = 'CPF';
            $documento = $dadosCliente['clino_cpf'];
        } elseif ($dadosCliente['clitipo'] == 'J') {
            $label = 'CNPJ';
            $documento = $dadosCliente['clino_cgc'];
        }

        $mensagem = "
            <div style='font-size:12px;font-family:Arial;color: #000;text-align: center;padding-top: 20px;'>
                <p><strong>Relat&oacute;rio das Transa&ccedil;&otilde;es com TimeOut excedido ap&oacute;s 3 tentativas. </strong></p>
                <table style='margin: auto;width: 600px;text-align: left;border: 1px solid black;font-size:12px;font-family:Arial'>
                    <tbody>
                        <tr>
                            <th width='50%' style='background:#A2B5CD; font-size:12px; font-family:Arial'>Nome Cliente</th>
    						<th width='25%' style='background:#A2B5CD; font-size:12px; font-family:Arial'>Documento</th>
    						<th width='25%' style='background:#A2B5CD; font-size:12px; font-family:Arial'>Título</th>
                        </tr>";

        $cor = ($i % 2 == 0) ? "#FFFFFF" : "#CCCCCC";
        $mensagem.="<tr style='font-size:12px; font-family:Arial; background-color: " . $cor . "'>";
        $mensagem.="<td>" . $dadosCliente['clinome'] . "</td>";
        $mensagem.="<td>$label: " . $documento . "</td>";
        $mensagem.="<td align='center'>" . str_replace(":", " - ", $titoid) . "</td>";
        $mensagem.="</tr>";

        $mensagem.="</tbody>
                </table>
                <p>
                    <strong>Faturamento  Cart&atilde;o de Cr&eacute;dito<br />Data: </strong>" . date('d/m/Y') . " <br />
                    <strong>Hora</strong>: " . date('H:i:s') . "
                </p>
            </div>";

        $this->mail->IsSMTP();
        $this->mail->From = "sascar@sascar.com.br";
        $this->mail->FromName = "SASCAR";
        $this->mail->Subject = 'Tempo Excedido';
        $this->mail->MsgHTML($mensagem);
        $this->mail->ClearAllRecipients();

        //produção
        if ($_SESSION['servidor_teste'] == 0) {
            // Usuarios da SASCAR que irão receber os emails
            $this->mail->AddAddress('lucas.mendes@sascar.com.br', 'Lucas Mendes');

            //outros locais   
        } else {
            //usuários de teste
            $this->mail->AddAddress('marcioferreira@brq.com', 'Márcio');
            //$this->mail->AddAddress('dcribeiro@brq.com','Diego');
        }

        // Envia o email
        $this->mail->Send();

        /* if($this->mail->Send()) { return "/n Email - OK /n"; } else { return "/n Email - Erro /n"; } */

        // Limpa os destinatários e os anexos
        $this->mail->ClearAllRecipients();
        $this->mail->ClearAttachments();

        return true;
    }

    public function criaArquivo() {
        $this->file = fopen(_SITEDIR_ . 'eSitef/arquivos_conciliacao/usar_' . date('dmY His') . '.csv', 'a+');
        $cabecalho = array("0", "Titulo", "Cod Estabelecimento", "Numero Resumo", "Numero Comprovante", "", "NSU", "CARTAO", "Valor Bruto", "Total Parcelas", "Valor Pago", "Valor Liquido", "Data Credito", "", "Numero Parcela", "", "", "", "", "", "", "", "Taxa Administrativa");

        fputcsv($this->file, $cabecalho, ';');
    }

    public function fechaArquivo() {
        fclose($this->file);
    }

}
