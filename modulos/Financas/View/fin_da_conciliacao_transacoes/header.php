<?php
@cabecalho();
@require_once ("lib/funcoes.js");
@require_once ("lib/funcoes.php");

?>
<title> Conciliação de Transações de Débito Automático</title>
<head>            

     <!-- CSS -->
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
    <link type="text/css" rel="stylesheet" href="calendar/calendar.css"/>
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css" />
     	
	<!--[if IE]>
	    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style-ie.css">
	<![endif]--> 
    
    <!-- JAVASCRIPT -->
    <script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.maskedinput.js"></script>
    <script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>
    <script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>
    <script type="text/javascript" src="includes/js/validacoes.js"></script>    
    <script type="text/javascript" src="includes/js/calendar.js"></script>
    <script type="text/javascript" src="modulos/web/js/fin_da_conciliacao_transacoes.js?rand=<?=rand(1, 9999);?>"></script>   
</head>
		
