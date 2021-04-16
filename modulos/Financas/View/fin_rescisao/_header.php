<?php
@cabecalho();
@require_once ("lib/funcoes.js");

if (strstr($_SERVER["REQUEST_URI"], 'fin_rescisao2')){
     $script = '<script type="text/javascript" src="modulos/web/js/fin_rescisao2.js"></script>';
}else{
     $script = '<script type="text/javascript" src="modulos/web/js/fin_rescisao.js"></script>';
}
?>

<html>
	<head>
		<meta charset="ISO-8859-1">
		
		<!-- CSS -->
        <link type="text/css" rel="stylesheet" href="lib/css/style.css"/>
        <link type="text/css" rel="stylesheet" href="lib/css/cupertino/jquery-ui-1.10.0.custom.min.css"/>
        <link type="text/css" rel="stylesheet" href="modulos/web/css/fin_rescisao.css" />

        <!-- JAVASCRIPT -->    
        <script type="text/javascript" src="includes/js/mascaras.js"></script>
        <script type="text/javascript" src="includes/js/auxiliares.js"></script>
        <script type="text/javascript" src="includes/js/validacoes.js"></script>    
        <script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
        <script type="text/javascript" src="lib/js/jquery-ui-1.10.0.custom.min.js"></script>
        <script type="text/javascript" src="lib/js/bootstrap.js"></script>    
        <script type="text/javascript" src="js/jquery.maskMoney.js"></script>
        <script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>
          
        <?= $script ?>
	</head>
	<body>
        <? if ($this->hasFlashMessage()): ?>
            <div class="mensagem sucesso"><?= $this->flashMessage() ?></div>
        <? else: ?>
            <div class="mensagem invisivel"></div>
        <? endif ?>
            
		<div class="modulo_titulo">Rescisão</div>        
		<div class="modulo_conteudo">