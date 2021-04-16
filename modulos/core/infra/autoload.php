<?php

function __autoload($classname) {	    
    $classname = ltrim($classname, '\\');    
    $root = '';
    $path = '';
    $namespace = '';
    
	
    if ($lastnspos = strripos($classname, '\\')) {
    	$namespace = substr($classname, 0, $lastnspos);
    	$classname = substr($classname, $lastnspos + 1);
    	$root = str_replace('\\', '/', $namespace) . '/';
    }
    
	if ($classname == "ComumDAO" || $classname == "ComumVO" || $classname == "ResponseDAO" || $classname == "ParametroDAO") {
    	$path = 'infra/Model/';
    } elseif ($classname == "ComumController") {
		$path = 'infra/Controller/';
    } elseif ($classname == "BoletoAbstract") {
		$path = $root . '/Project/src/OpenBoleto/';
    } elseif ($classname == "Agente") {
		$path = $root . '/Project/src/OpenBoleto/';
    } elseif ($classname == "Santander") {
		$path = $root . '/Project/src/OpenBoleto/Banco/';
	} elseif ($classname == "Itau") {
    	$path = $root . '/Project/src/OpenBoleto/Banco/';
    } elseif ($classname == "Hsbc") {
    	$path = $root . '/Project/src/OpenBoleto/Banco/';
    } elseif ($classname == "Caixa") {
    	$path = $root . '/Project/src/OpenBoleto/Banco/';
    } elseif ($classname == "Bradesco") {
    	$path = $root . '/Project/src/OpenBoleto/Banco/';
    } elseif ($classname == "Exception") {
    	$path = $root . '/Project/src/OpenBoleto/';
    } elseif ($classname == "DateTime") {
    	$path = $root . '/Project/src/OpenBoleto/';
    } elseif ($classname == "BoletoService") {
    	$path = 'module/Boleto/';
	} elseif ($classname == "LeitorRemessaCNABSantanderModel"){
		$path = 'module/LeitorRemessaCNABSantander/Model/';	
    } elseif (substr($classname,-10) == "Controller") {
    	$path = $root . '/Controller/';
    } elseif (substr($classname,-5) == "Model") {
		$path = $root . '/Model/';
    } elseif (substr($classname,-3) == "DAO") {
       $path = $root . '/Model/DAO/';
    } elseif ($classname == "ParametroCobrancaRegistrada"){
       $path = 'module/Parametro/';
    } elseif ($classname == "IntegracaoProtheusTotvs"){
       $path = 'module/WSProtheus/';
    } elseif ($classname == "BoletoRegistradoModel"){
        $path = $root + 'Model/';
    } elseif ($classname == "BoletoRegistradoDAO"){
       $path = $root + 'DAO/';
	} else {
    	$path = $root;
    }
    
    $filename = $path . str_replace('_', '/', $classname) . '.php';
    require_once _MODULEDIR_.'core/'.$filename;
}

?>