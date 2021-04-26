<?php

/**
 * Cliente teste de nusoap.
 *
 * Módulo responsável pelo envio de XML com os dados da consulta. 
 *
 * @file    webservice/cliente_teste.php
 * @author  Angelo Frizzo Jr
 * @since   24/05/2012
 * @version 24/05/2012
 * @package /var/www/html/intranet/sascar/webservice/cliente_teste.php
 *
 */

/**
 * Includes e Dependências.
 */
include '../lib/nusoap-0.9.5/lib/nusoap.php';
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
//ini_set("display_errors", 1);

include '../includes/functions.php';

/**
 * Caso o Web Service esteja em fase de testes, onde o serviço possa sofrer alterações, é necessário 
 * evitar que se faça caching na execução do cliente PHP. Para isso devemos inserir a seguinte instrução:
 */
ini_set("soap.wsdl_cache_enabled", "0"); //Limpa o cache

/**
 * Início do protocólo SOAP (cliente) que irá consumir o server.
 */

//$client = new SoapClient('http://172.18.0.1:8000/autenticacao.php?wsdl', array('trace' => 1, 'exceptions' => 1, 'soap_version' => SOAP_1_1));
//$client = new SoapClient('http://172.18.0.1:8000/recuperarTesteAutomaticoRp.php?wsdl', array('trace' => 1, 'exceptions' => 1, 'soap_version' => SOAP_1_1));
//$client = new SoapClient('http://172.18.0.1:8000/WS166_consultarResumoConfiguracao.php?wsdl', array('trace' => 1, 'exceptions' => 1, 'soap_version' => SOAP_1_1));
//$client = new SoapClient('http://172.18.0.1:8000/WS168_reenviarConfiguracao.php?wsdl', array('trace' => 1, 'exceptions' => 1, 'soap_version' => SOAP_1_1));
$client = new SoapClient('http://172.18.0.1:8000/WS163_submeterKmOdometro.php?wsdl', array('trace' => 1, 'exceptions' => 1, 'soap_version' => SOAP_1_1));
//$client = new SoapClient('https://desenvolvimento.sascar.com.br/sistemaWeb/WS_Portal_v2.0/autenticacao.php?wsdl', array('trace' => true, 'exceptions' => true, 'soap_version' => SOAP_1_1));
//$client = new SoapClient('http://172.18.0.1:8000/validaCpfInstaladorRp.php?wsdl', array('trace' => 1, 'exceptions' => 1, 'soap_version' => SOAP_1_1));

try {

    //$repoid, $cntioid, $cpf, $usuario
    $result = $client->ReenviarConfiguracao('1911', '7516', '93104944920', '39411754', 'MSC0003');

//    $result = $client->RecuperarTesteAutomaticoRp('1911', '39411754', '93104944920', 'N', '7516');
//    $result = $client->ConsultarResumoConfiguracao('1911', '7516', '93104944920', '39411754' );

//    $result = $client->Autenticacao('ADMIN.DESENV', 'ADMIN.DESENV', 'fa5a9404a2c5ae41cffb65195bbde3cf', 'RT', 'TZv-jmaKpBcxKJahv79a9RY2Zz3SLoXwZZENvWrXtLX8tG5xav17!1220148135!1614883481226');

//    $result = $client->ValidaCpfInstaladorRp(
//        $repoid = '1911',
//        $ordoid = '39411754',
//        $cpf = '93104944920',
//        $origem = 'T'
//    );
//
//    echo "<pre>\n";
//    echo "Cabeçalho da Chamada:\n";
//    echo $client->__getLastRequestHeaders();
//    echo "</pre>";
//
//    echo "<pre>\n";
//    echo "Request:\n";
//    echo $client->__getLastRequest();
//    echo "</pre>";
    header('Content-type:text/xml');
    echo $client->__getLastResponse();

}catch (SoapFault $e){
    
    echo "<hr>";
    
    echo "<pre>\n";
    echo "Cabeçalho da Chamada:\n";
    echo htmlspecialchars($client->__getLastRequestHeaders());
    echo "</pre>";
    echo "Chamada:<br>";
    echo htmlspecialchars($client->__getLastRequest());

    echo "<hr>";
        
    echo "<pre>\n";
    echo "Cabeçalho de Retorno:\n";
    echo htmlspecialchars($client->__getLastResponseHeaders());
    echo "</pre>";
    echo "Retorno:<br>";
    echo $client->__getLastResponse();    
    
    echo "<pre>";
    print_r($e);
    echo "</pre>";    

    echo "<br>FALHOU! SOAP Fault: ".$e->getMessage()."<br>";
    
} ?>