<?php
@cabecalho();
?>         
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css">
    <!--[if IE]>
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style-ie.css">
    <![endif]-->    
    <link type="text/css" rel="stylesheet" href="calendar/calendar.css" />
    <link type="text/css" rel="stylesheet"  href="modulos/web/css/cad_configuracao_equipamento.css"/>
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css"> 
    
    <script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script> 
    <script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>  
    <script type="text/javascript" src="includes/js/auxiliares.js"></script>  
    
    <script type="text/javascript">
    	// Endereço do script, para AJAX
    	var ACTION = '<?= $_SERVER['SCRIPT_NAME']?>';
    </script>
    <script type="text/javascript" src="modulos/web/js/lib/validacao.js"></script>    
    <script type="text/javascript" src="modulos/web/js/cad_configuracao_equipamento.js"></script>

    <style type="text/css">
       .img_loader {                  
                    background: url(images/ajax-loader-circle.gif) no-repeat 100%;                   
                }
        #excluir_cliente{
            cursor: pointer;
        }
    </style>  

<div class="modulo_titulo">Cadastro de Configuração de Equipamentos
 
</div>
    <div class="modulo_conteudo">        
        <div id="mensagem" class="mensagem <?php echo ($this->retorno != '') ? $this->retorno['status'] : '' ?>"><?php echo ($this->retorno != '') ? $this->retorno['mensagem'] : '' ?></div>
        <?php if(count($this->retorno['informativos']) > 0) : ?>
        <div id="informativos" class="mensagem <?php echo ($this->retorno['informativos'] != '') ? $this->retorno['informativos']['status'] : '' ?>">
            <?php echo ($this->retorno['informativos'] != '') ? $this->retorno['informativos']['mensagem'] : '' ?>
        </div>
        <?php endif;?>
        