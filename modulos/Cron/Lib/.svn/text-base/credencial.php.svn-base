<?php
switch ($_SERVER['SERVER_ADDR']) 
{
    case "172.19.14.240": 
		require_once "credencial_producao.php";
        break;
    case "10.1.101.21": 
	# HOMOLOGACAO
        require_once "credencial_homologacao.php";
        break;	
    case "10.1.107.21":
	# TESTE
        require_once "credencial_teste.php";
        break;
    case "172.16.2.57":
	# DESENVOLVIMENTO
        require_once "credencial_desenvolvimento.php";
        break;
    default: 
	# CONFIG LOCAL - ANALISTA DESENVOLVEDOR
        require_once "config_local.php";
}
?>
