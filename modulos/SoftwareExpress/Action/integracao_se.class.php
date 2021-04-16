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

ini_set('display_errors', 1);
//error_reporting(~E_WARNING);

// teste
// require_once '/var/www/html/sistemaWeb/lib/config.php';

/** Includes e Dependências. **/
require _SITEDIR_ .'lib/nusoap.php';

// Ambiente de HOMOLOGAÇÃO da SOFTEXPRESS  (e-Sitef)
define('_SOFTEXPRESS_'      , "https://esitef-homologacao.softwareexpress.com.br/e-sitef/Recurrent?wsdl");

// Ambiente de PRODUÇÃO da SOFTEXPRESS  (e-Sitef)
// define('_SOFTEXPRESS_'      , "https://esitef-homologacao.softwareexpress.com.br/e-sitef/Recurrent?wsdl");

class IntegracaoSE {
	
	// Código da loja no e-Sitef
	public $merchantId;	

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
	
	public function __construct(){	
            	
            $this->store();
            // $this->callStatus();
            // $this->callStatusByOrderId();
	}
        
        /**
	 * Método responsável pela chamada do webservice retornando o objeto instanciado 
	 */
	public function startWebService(){            
		                
            $client = new SoapClient(_SOFTEXPRESS_,
                            array(  'trace' => 1,
                                    'exceptions' => 1,
                                    'soap_version' => SOAP_1_1,
                                    'proxy_host' => "10.2.57.200",
                                    'proxy_port' => 3128));

            return $client;
	}
        
        public function callStatus(){
            
            $ws = $this->startWebService();
            
            $nita = "????";

            $statusRequest = array('nita' => $nita);
            
            $ws->callStatus($statusRequest);
            
            print '<pre>';
            print_r($ws);	
            print '</pre>';
            
            
        }
        
        public function callStatusByOrderId(){
                          
            $ws = $this->startWebService();
            $statusByOrderIdRequest = array(                                                    
                                        'merchantUSN' 		=> "2",
                                        'merchantKey' 		=> "6F4B71F18F2ECB8BCB038154C893D7BA83D6C29256CBED03"
                                    );
            
            $ws->callStatusByOrderId($statusByOrderIdRequest);
            
            print '<pre>';
            print_r($ws);	
            print '</pre>';
                        
        }

        /**
         * O método store será utilizado no armazenamento do cartão. 
         */
	public function store(){
            
            $ws = $this->startWebService();
            
            // additionalInfo   => campo  reservado  para  uso  futuro,  previsto  para  uma  eventual  necessidade  do lado do e-SiTef. 
            // authorizerId     => Código da autorizadora (instituição financeira) no e-SiTef (ver Apêndice A).
            // cardExpiryDate   => Data  de  vencimento  do  cartão.  Ex:  0912  caso  a  data  de  vencimento  seja 09/2012 
            // cardNumber       => Número do cartão de crédito.
            // customerId       => Código de  identificação para cada  cliente, criado pela  loja.
            // merchantId       => Parâmetro(Código da loja no e-SiTef) para uso na interface Web Service; 
            // merchantUSN      => Parâmetro para uso na Consulta de Status ou Consulta Armazenamento ou Remoção do Cartão Armazenado.            
		
            $storeRequest = array('storeRequest' => array
                                            (
                                                'additionalInfo'        => "",
                                                'authorizerId' 		=> "2",
                                                'cardExpiryDate'        => "0913",
                                                'cardNumber' 		=> "5555666677778886",
                                                'customerId' 		=> "13",                                                    
                                                'merchantId' 		=> "sascar", 
                                                'merchantUSN' 		=> "2"
                                            ));
                                   
            $storeResponse = $ws->store($storeRequest);
            
            
            print '<pre>';
            print_r($storeResponse);	
            print '</pre>';
		
	}		
}
?>