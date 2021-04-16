<?php
@cabecalho();
@require_once ("lib/funcoes.js");
@require_once ("lib/funcoes.php");

?>

<head>            

    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css" />
<!--[if IE]>
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style-ie.css">
<![endif]-->    
    <link href="modulos/web/css/cad_cliente.css" type="text/css" rel="stylesheet" />
    <!-- jQuery UI Layout  1.1.0 -->
    <script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script>
    <script src="js/jquery.maskMoney.js"></script>
    <script src="js/jquery.maskedinput.js"></script>

    <script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>
    <script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>


    <script type="text/javascript" src="modulos/web/js/lib/jquery.ajaxfileupload.js"></script>
    <!-- Validações Novo -->
    <script type="text/javascript" src="modulos/web/js/lib/validacao.js"></script> 
    
    <script type="text/javascript" src="includes/js/validacoes.js"></script> 
    <script type="text/javascript" src="includes/js/auxiliares.js"></script>
    
    
    <script type="text/javascript">
    	// Endereço do script, para AJAX
    	var ACTION = '<?= $_SERVER['SCRIPT_NAME']?>';
    	var diaVcto = '<?= $this->dadosCobranca['clientes']['clidia_vcto']; ?>';
    	// variaves de controle de acesso
    	var cadastra_cliente              = '<?= $this->fn_cadastra_cliente?>';
    	var cliente_dados_cobranca        = '<?= $this->fn_cliente_dados_cobranca?>';
    	var acess_quad_obr_financ_cliente = '<?= $this->fn_acess_quad_obr_financ_cliente?>';
    	var cliente_dados_fiscais         = '<?= $this->fn_cliente_dados_fiscais?>';
    </script>
    <script type="text/javascript" src="modulos/web/js/cad_cliente.js"></script>    
</head>
<div class="modulo_titulo">Cadastro de Cliente</div>
	<div class="modulo_conteudo">
		<div id="mensagem" class="mensagem <?php echo ($this->retorno['status'] != '') ? $this->retorno['status'] : '' ?>"><?php echo ($this->retorno['mensagem'] != '') ? $this->retorno['mensagem'] : '' ?></div>
