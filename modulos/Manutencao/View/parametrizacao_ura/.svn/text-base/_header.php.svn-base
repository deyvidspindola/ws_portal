<?php
cabecalho();
require_once ("lib/funcoes.js");
?>

             <link type="text/css" rel="stylesheet" href="css/style.css"/>
             <!-- CSS -->
        <link type="text/css" rel="stylesheet" href="lib/css/style.css"/>
        <link type="text/css" rel="stylesheet" href="lib/css/cupertino/jquery-ui-1.10.0.custom.min.css"/>
        <link type="text/css" rel="stylesheet" href="modulos/web/css/man_parametrizacao_ura.css" />

        <!-- JAVASCRIPT -->    
        <script type="text/javascript" src="includes/js/mascaras.js"></script>
        <script type="text/javascript" src="includes/js/auxiliares.js"></script>
        <script type="text/javascript" src="includes/js/validacoes.js"></script>    
        <script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
        <script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>
        <script type="text/javascript" src="lib/js/jquery-ui-1.10.0.custom.min.js"></script>
        <script type="text/javascript" src="lib/js/bootstrap.js"></script>    
        
        <style>
        
        .active {
             border-bottom: 1px solid #94ADC2 !important;
             background-image: url("images/fundo_over3.jpg") !important;
        }
        
        </style>
        

        <?php 
  
        $aba_panico_atual = "";
        if($this->getAba() == 'panico'){
        	$aba_panico_atual = 'class="active"';
        }
             
        $aba_estatistica_atual = "";
        if($this->getAba() == 'estatistica'){
        	$aba_estatistica_atual = 'class="active"';
        }
             
        $aba_assistencia_atual = "";
        if($this->getAba() == 'assistencia'){
        	$aba_assistencia_atual = 'class="active"';
        }
        
        $aba_cron_atual = "";
        if($this->getAba() == 'cron'){
        	$aba_cron_atual = 'class="active"';
        }
        
        if ($this->hasFlashMessage()): ?>
            <div class="mensagem sucesso"><?= $this->flashMessage() ?></div>
        <? else: ?>
            <div class="mensagem invisivel"></div>
        <? endif ?>
            
             <div class="modulo_titulo">Parametrização</div>  
                 
             <div class="modulo_conteudo">
             
        <div id="msg_inclusao_manual" class="msg"></div>  
                    <ul class="bloco_opcoes">             
                    
                           <?if($_SESSION['funcao']['parametro_ura_aba_panico'] == 1 ){?>     
                           <li <?php echo $aba_panico_atual; ?>><a href="man_parametrizacao_ura.php?acao=panico">Atend. Pânicos</a></li>
                           <?}?>                           
                           
                           <? if($_SESSION['funcao']['parametro_ura_aba_estatistica'] == 1  ){?>                       
                           <li <?php echo $aba_estatistica_atual; ?>><a href="man_parametrizacao_ura.php?acao=estatistica">Atend. Estatística</a></li>
                           <?}?>                           
                          
                           <? if($_SESSION['funcao']['parametro_ura_aba_assistencia'] == 1){ ?>     
			                <li <?php echo $aba_assistencia_atual; ?>><a href="man_parametrizacao_ura.php?acao=assistencia">Atend. Assistência</a></li>
			                <?}?>    
    
			                <? if($_SESSION['funcao']['parametro_ura_aba_cron'] == 1){ ?>     
			                <li <?php echo $aba_cron_atual; ?>><a href="man_parametrizacao_ura.php?acao=cron">Cron</a></li>
			                <?}?>
                    </ul>
