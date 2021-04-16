<?php

/**
 * SeInterfacePagamento.php
 *
 * Classe para fazer a integração com a softwareexpress, possibilitando acessar os serviços 
 * de pagamentos multi serviços com capacidade de processamento de transações de cartões de crédito,
 * transferência bancária, geração de boletos, integração com opções de mobile payment, entre outros
 * serviços que podem ser facilmente agregados à plataforma.
 *
 * @author Diego C. Ribeiro
 * @email dcribeiro@brq.com
 * @since 25/10/2012
 * @STI 80219
 * @package Softwareexpress
 *
 */
/*
 * VERIFICA SE A CLASSE 'nusoap_base' EXISTE, 
 * APENAS A FUNÇÃO ABAIXO 'class_exists', 
 * NÃO FUNCIONOU COM O CORE
 * 
 * leandroivanaga@brq.com
 */
if (in_array("nusoap_base", get_declared_classes()) === false) {
    require _SITEDIR_ . 'lib/nusoap.php';
}

/** Includes e Dependências. * */
if (!class_exists('nusoap_base')) {
    require _SITEDIR_ . 'lib/nusoap.php';
}

//classe de manipulação de dados no bd 
require_once (_MODULEDIR_ . 'eSitef/DAO/IntegracaoSoftExpressDAO.php');

class IntegracaoSoftExpress {

    // Código da loja no e-Sitef
    public $merchantId;
    // Chave da loja no e-Sitef
    public $merchantKey;
    // Número seqüencial da loja
    public $merchantUSN;
    // Código da autorizadora no e-Sitef.Cada cartão possui um código de identificação
    public $authorizerId;
    // Data de vencimento no formato MMAA
    public $cardExpiryDate;
    // Número do cartão de crédito
    public $cardNumber;
    // Código de segurança
    public $cardSecurityCode;
    // Documento de identidade do comprador 
    public $customerId;

    /**
     * Tipo de financiamento do parcelamento:
     * 	3 = parcelamento com juros da administradora do cartão,
     * 	4 = parcelamento realizado pela loja e sem juros.
     * 	6 = parcelamento com juros da administradora (IATA)
     *  7 = parcelamento realizado pela loja e sem juros (IATA)	 
     */
    public $installmentType;
    // Número de parcelas. 1 = à vista
    public $installments;
    // Identificador da transação no e-SiTef (criptografado) 
    public $nit;
    //define se transação é pagamento ou armazenamento
    public $option;
    //Parâmetros de conexão web service
    public $params;
    // Fornece acesso aos dados no bd
    private $dao;
    //url de acesso ao WS para pagamento recorrente
    public $softExpressRecurrent;
    //url de acesso ao WS para pagamentos (à vista, parcelado)
    public $softExpressPayment;
    //quantidade de tentativas para efetuar o pagamento caso time out
    public $tentativasPagamento;

    public function __construct($opcao = NULL) {
        try {

            global $conn;

            $this->dao = new IntegracaoSoftExpressDAO($conn);

            $this->option = $opcao;

            //AMBIENTE DE TESTES
            if ($_SESSION['servidor_teste'] === 1) {

                if ($this->option == 'payment') {

                    //seta variável para recuperar os dados de acesso ao Web Service na SoftExpress
                    $dadosWsRecorrente = $this->dao->getDadosAcessoSoftExpress('SOFTEXPRESS_PAGAMENTO_RECORRENTE_TESTES');

                    if (!is_array($dadosWsRecorrente)) {
                        throw new Exception('Falha ao pesquisar dados de acesso a SoftExpress Recorrente');
                    }

                    //percorre e atribui os dados de acesso nos atributos
                    foreach ($dadosWsRecorrente as $dados) {
                        $this->atribuirDadosAcessoSoftExpress($dados);
                    }
                } elseif ($this->option == 'parcelado') {

                    //seta variável para recuperar os dados de acesso do instalação parcelado
                    $dadosWsParcelado = $this->dao->getDadosAcessoSoftExpress('SOFTEXPRESS_PAGAMENTO_PARCELADO_TESTES');

                    if (!is_array($dadosWsParcelado)) {
                        throw new Exception('Falha ao pesquisar dados de acesso a SoftExpress Parcelado');
                    }

                    //percorre e atribui os dados de acesso nos atributos
                    foreach ($dadosWsParcelado as $dados) {
                        $this->atribuirDadosAcessoSoftExpress($dados);
                    }
                } else {

                    //seta variável para recuperar os dados de acesso ao Web Service na SoftExpress
                    $dadosWsRecorrente = $this->dao->getDadosAcessoSoftExpress('SOFTEXPRESS_PAGAMENTO_RECORRENTE_TESTES');

                    if (!is_array($dadosWsRecorrente)) {
                        throw new Exception('Falha ao pesquisar dados de acesso a SoftExpress Recorrente');
                    }

                    //percorre e atribui os dados de acesso nos atributos
                    foreach ($dadosWsRecorrente as $dados) {
                        $this->atribuirDadosAcessoSoftExpress($dados);
                    }
                }


                //PRODUÇÃO
            } else {

                if ($this->option == 'payment') {

                    //seta variável para recuperar os dados de acesso ao Web Service na SoftExpress
                    $dadosWsRecorrente = $this->dao->getDadosAcessoSoftExpress('SOFTEXPRESS_PAGAMENTO_RECORRENTE_PRODUCAO');

                    if (!is_array($dadosWsRecorrente)) {
                        throw new Exception('Falha ao pesquisar dados de acesso a SoftExpress Recorrente');
                    }

                    //percorre e atribui os dados de acesso nos atributos
                    foreach ($dadosWsRecorrente as $dados) {
                        $this->atribuirDadosAcessoSoftExpress($dados);
                    }
                } elseif ($this->option == 'parcelado') {

                    //seta variável para recuperar os dados de acesso do instalação parcelado
                    $dadosWsParcelado = $this->dao->getDadosAcessoSoftExpress('SOFTEXPRESS_PAGAMENTO_PARCELADO_PRODUCAO');

                    if (!is_array($dadosWsParcelado)) {
                        throw new Exception('Falha ao pesquisar dados de acesso a SoftExpress Parcelado');
                    }

                    //percorre e atribui os dados de acesso nos atributos
                    foreach ($dadosWsParcelado as $dados) {
                        $this->atribuirDadosAcessoSoftExpress($dados);
                    }
                } else {

                    //seta variável para recuperar os dados de acesso ao Web Service na SoftExpress
                    $dadosWsRecorrente = $this->dao->getDadosAcessoSoftExpress('SOFTEXPRESS_PAGAMENTO_RECORRENTE_PRODUCAO');

                    if (!is_array($dadosWsRecorrente)) {
                        throw new Exception('Falha ao pesquisar dados de acesso a SoftExpress Recorrente');
                    }

                    //percorre e atribui os dados de acesso nos atributos
                    foreach ($dadosWsRecorrente as $dados) {
                        $this->atribuirDadosAcessoSoftExpress($dados);
                    }
                }
            }


            //caso ambiente local, seta proxy para saída de conexão
//            if ($_SERVER['HTTP_HOST'] == '192.168.56.101') {
            if ($_SERVER['HTTP_HOST'] == 'localhost') {

                $this->params = array('trace' => 1,
                    'exceptions' => 1,
                    'soap_version' => SOAP_1_1,
//                    'proxy_host' => "10.2.57.200",
//                    'proxy_port' => 3128,
                    'proxy_host' => "172.19.10.26",
                    'proxy_port' => 8080, 
                    'connection_timeout' => 90);
            } else {

                $this->params = array('trace' => 1,
                    'exceptions' => 1,
                    'soap_version' => SOAP_1_1,
                    'connection_timeout' => 90);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * Resposável em atribuir os dados de acordo a descrição do array de dados recebido por parâmetro 
     * 
     * @param array $dados
     */
    private function atribuirDadosAcessoSoftExpress($dados) {


        if ($dados['pcsioid'] === '_SOFTEXPRESS_') {
            $this->softExpressRecurrent = trim($dados['pcsidescricao']);
        }

        if ($dados['pcsioid'] === '_SOFTEXPRESSPAYMENT_') {
            $this->softExpressPayment = trim($dados['pcsidescricao']);
        }

        if ($dados['pcsioid'] === '_MERCHANTKEY_') {
            $this->merchantKey = trim($dados['pcsidescricao']);
        }

        if ($dados['pcsioid'] === '_MERCHANTID_') {
            $this->merchantId = trim($dados['pcsidescricao']);
        }

        if ($dados['pcsioid'] === '_TENTATIVAS_PAGAMENTO_') {
            $this->tentativasPagamento = trim($dados['pcsidescricao']);
        }
    }

    /**
     * Método responsável pela chamada do webservice retornando o objeto instanciado 
     */
    public function startWebService() {

        switch ($this->option) {
            case '':
                $client = new SoapClient($this->softExpressRecurrent, $this->params);
                break;
            case 'payment':
                $client = new SoapClient($this->softExpressPayment, $this->params);
                break;
            case 'parcelado':
                $client = new SoapClient($this->softExpressPayment, $this->params);
                break;
        }
        return $client;
    }

    /**
     * O método store será utilizado no armazenamento do cartão. 
     * Ex Retorno
     *     [storeResponse] => stdClass Object
      (
      [authorizerId] => 2
      [cardHash] => -JGCGbaVWGxoGn3KNwV0IUlsDlT06OQQEJBrnrFURiaU5kMduVb3cni8mnc4bQUMoPJ5+y-UJofIE06MT8EEzA==
      [cardSuffix] => 8886
      [customerId] => 13
      [merchantUSN] => 2
      [message] => Armazenamento efetuado com sucesso!
      [nita] => Z8f86c06d65e052ce155eae00c21e51903c7ff31e5cde0eca60c10bc8e6b297e1
      [nsua] => 12112700003950A
      [status] => DUP
      )
     */
    public function store($store) {

        $ws = $this->startWebService();

        // additionalInfo   => campo  reservado  para  uso  futuro,  previsto  para  uma  eventual  necessidade  do lado do e-SiTef. 
        // authorizerId     => Código da autorizadora (instituição financeira) no e-SiTef (ver Apêndice A).
        // -- cardExpiryDate   => Data  de  vencimento  do  cartão.  Ex:  0912  caso  a  data  de  vencimento  seja 09/2012 
        // -- cardNumber       => Número do cartão de crédito.
        // -- customerId       => Código de  identificação para cada  cliente, criado pela  loja.
        // merchantId       => Parâmetro(Código da loja no e-SiTef) para uso na interface Web Service; 
        // merchantUSN      => Parâmetro para uso na Consulta de Status ou Consulta Armazenamento ou Remoção do Cartão Armazenado.            

        $storeRequest = array('storeRequest' => array
                (
                'additionalInfo' => $store['additionalInfo'],
                'authorizerId' => $store['authorizerId'],
                'cardExpiryDate' => $store['cardExpiryDate'],
                'cardNumber' => $store['cardNumber'],
                'customerId' => $store['customerId'],
                'merchantId' => $this->merchantId, //$store['merchantId'], 
                'merchantUSN' => $store['merchantUSN']
        ));

        $storeResponse = $ws->store($storeRequest);

        return $storeResponse;
    }

    public function beginRemoveStoredCard($hashCartao) {

        $ws = $this->startWebService();

        $removeStoreCardRequest = array(
            'merchantUSN' => "2",
            'merchantKey' => $this->merchantKey,
            'cardHASH' => $hashCartao
        );

        $retorno = $ws->beginRemoveStoredCard($removeStoreCardRequest);

        if ($retorno) {
            return $retorno;
        } else {
            return FALSE;
        }
    }

    public function doRemoveStoredCard($hashCartao, $nita) {
        $ws = $this->startWebService();

        $removeStoreCardRequest = array(
            'merchantKey' => $this->merchantKey,
            'cardHASH' => $hashCartao,
            'nita' => $nita
        );



        $retorno = $ws->doRemoveStoredCard($removeStoreCardRequest);
    }

    public function beginTransaction($amount, $orderId, $merchantUSN) {
        try {
            $ws = $this->startWebService();

            $beginTransactionParams = array(
                'transactionRequest' => array(
                    'merchantId' => $this->merchantId,
                    'amount' => $amount,
                    'orderId' => $orderId,
                    'merchantUSN' => $merchantUSN
                )
            );

            return $ws->beginTransaction($beginTransactionParams);
        } catch (Exception $e) {
            return "Falha ao iniciar transação beginTransaction";
        }
    }

    public function getStatus($nit) {
        try {
            $ws = $this->startWebService();

            $getStatus = array(
                'merchantKey' => $this->merchantKey,
                'nit' => $nit
            );
            return $ws->getStatus($getStatus);
        } catch (Exception $e) {
            return "Falha ao consultar dados getStatus";
        }
    }

    public function doHashPayment($nit, $cardHash, $authorizerId, $installments, $installmentType, $autoConfirmation, $customerId, $cardholder = '') {
        $ws = $this->startWebService();

        $doHashPaymentParams = array(
            'hashPaymentRequest' => array(
                'nit' => $nit,
                'cardHash' => $cardHash,
                'authorizerId' => $authorizerId,
                'installments' => $installments,
                'installmentType' => $installmentType,
                'autoConfirmation' => $autoConfirmation,
                'customerId' => $customerId
            )
        );

        //adiciona elemento CardHolder no array
        if (trim($cardholder) != '') {
            $arrCardholder = 'CARDHOLDER:' . $cardholder; //Ex: 'CARDHOLDER:FULANO TESTE'
            $doPaymentParams['paymentRequest']['extraField'] = $arrCardholder;
        }
        try {
            return $ws->doHashPayment($doHashPaymentParams);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function doPayment($nit, $authorizerId, $autoConfirmation, $cardNumber, $cardExpiryDate, $cardSecurityCode, $customerId, $installmentType, $installments, $cardholder = '') {
        $ws = $this->startWebService();

        $doPaymentParams = array(
            'paymentRequest' => array(
                'nit' => $nit,
                'authorizerId' => $authorizerId,
                'autoConfirmation' => $autoConfirmation,
                'cardNumber' => $cardNumber,
                'cardExpiryDate' => $cardExpiryDate,
                'cardSecurityCode' => $cardSecurityCode,
                'customerId' => $customerId,
                'installmentType' => $installmentType,
                'installments' => $installments
            )
        );

        //adiciona elemento CardHolder no array
        if (trim($cardholder) != '') {
            $arrCardholder = 'CARDHOLDER:' . $cardholder; //Ex: 'CARDHOLDER:FULANO TESTE'
            $doPaymentParams['paymentRequest']['extraField'] = $arrCardholder;
        }
        try {
            $x = $ws->doPayment($doPaymentParams);
            return $x;
        } catch (Exception $e) {

            return $e->getMessage();
        }
    }

}

//fim arquivo
?>
