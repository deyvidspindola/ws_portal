<?php
@cabecalho();
@require_once ("lib/funcoes.js");
?>

<head>            	
	
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css"/>
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css"/>
    <script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.maskedinput.min.js"></script>
    <script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>
    <script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>
    <script type="text/javascript" src="modulos/web/js/lib/validacao.js"></script>
    <!--[if IE]>
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style-ie.css">
    <![endif]-->  
    <script type="text/javascript">
    	// Endere�o do script, para AJAX
    	var ACTION = '<?= $_SERVER['SCRIPT_NAME']?>';
    </script>
    <script type="text/javascript" src="modulos/web/js/prn_bem_consumo.js"></script>
    
    <style type="text/css">
        .periodo .inicial {
            margin-right: 20px !important ;
        }
    
    </style>
    
</head>
<div class="modulo_titulo">Bem de Consumo</div>
	<div class="modulo_conteudo">
		<div id="mensagem" class="mensagem <?php echo ($this->retorno['status'] != '') ? $this->retorno['status'] : '' ?>"><?php echo ($this->retorno['mensagem'] != '') ? $this->retorno['mensagem'] : '' ?></div>
