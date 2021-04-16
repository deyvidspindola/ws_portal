<?php


//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/var/www/log/log_ebs_fox_'.date('d-m-Y').'.txt');

$fp = fopen("/var/www/log/log_webservice_fox.txt","a");

global $client;

// Inicializa a conexão com o canal de integração
try {
            
    // Em produção, usar esse endereço
    //$client = new SoapClient("http://canalintegracao.ebs.com.br/fox/IntegracaoFoxService.svc?wsdl");

    // Em Homologação, usar esse endereço
    //$client = new SoapClient("http://canalintegracao.ebs.com.br/foxhomolog/IntegracaoFoxService.svc?wsdl");

 	// Array de configuração da chamada SOAP...
	$params = array(  	
		'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
		'trace' => 1,
		'exceptions' => 1,
		'soap_version' => SOAP_1_1,
		'style' => SOAP_DOCUMENT,
		'use' => SOAP_LITERAL,
		'encoding' => 'UTF-8',
		'proxy_host' => '172.16.2.77',
		'proxy_port' => 80,
		'connection_timeout' => 90
  	);
	$client = new SoapClient("http://gvtsvdw-fnt01/Service/EBS.IntegracaoFoxService.IntegracaoFoxService.svc?wsdl", $params);
	//$client = new SoapClient("http://gvtsvdw-fnt01/Sevice/EBS.IntegracaoFoxService.IntegracaoFoxService.svc?wsdl", $params);
	//$client = new SoapClient("http://gvtsvdw-fnt01/Servico_Teste/EBS.IntegracaoFoxService.IntegracaoFoxService.svc?wsdl");

	
	
	fwrite ($fp,' retorno webservice  $client  =>  '.serialize($client).PHP_EOL.PHP_EOL);
		
	
	
	
    /**
     * @todo Dentro da BRQ precisamos utilizar o proxy para conseguir conectar,
     * devemos retirar o proxy quando subir a última versão para produção
     
    $client = new SoapClient("http://canalintegracao.ebs.com.br/foxhomolog/IntegracaoFoxService.svc?wsdl",
        array('proxy_host' => "10.2.8.200",
        		'proxy_port' => 3128)
    );*/

// Caso não consiga fechar a conexão, $client = null    
} catch (SoapFault $exc) {
	
	echo '<pre>';
	var_dump($exc->faultcode, $exc->faultstring, $exc->faultactor, $exc->detail, $exc->_name, $exc->headerfault);
	
	fwrite ($fp,' retorno webservice Exception $client  =>  '.serialize($exc->faultcode, $exc->faultstring, $exc->faultactor, $exc->detail, $exc->_name, $exc->headerfault).PHP_EOL.PHP_EOL);
	
    $client = null;
}

fclose($fp);

/**
 * Função para Requisição de envio
 *
 * @return StdClass
 */
function req (){
    $req = new StdClass();
    $req->credenciais = new StdClass();
    $req->credenciais->Usuario = "SASCAR";
    $req->credenciais->Senha = "69038F1D-2BF8-4C84-81DB-E1FC788B0E80";
    $req->chaveIntegracao = "B43DEC1508C644CD9171488461A6FC65";
    return $req;
}

// base de barra funda
function reqct (){
	$req = new StdClass();
	$req->credenciais = new StdClass();
	$req->credenciais->Usuario = "SASCAR";
	$req->credenciais->Senha = "69038F1D-2BF8-4C84-81DB-E1FC788B0E80";
	$req->chaveIntegracao = "51AA8698FF24479AB7C548882106F8E5";
	return $req;
}