

<?php require_once _MODULEDIR_ . "Principal/View/prn_regra_abertura_os/cabecalho.php"; ?>


    
    <!-- Mensagens-->
    <div id="mensagem_info" class="mensagem info">Os campos com * s?o obrigat?rios.</div>
    
    <div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemErro; ?>
    </div>
    
    <div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemAlerta; ?>
    </div>
    
    <div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemSucesso; ?>
    </div>
    
    
    <form id="form_cadastrar"  method="post" action="">
    <input type="hidden" id="acao" name="acao" value="cadastrar"/>
    <input type="hidden" id="ordraoid" name="ordraoid" value="<?php echo $this->view->parametros->ordraoid; ?>"/>
    
    
    <?php require_once _MODULEDIR_ . "Principal/View/prn_regra_abertura_os/formulario_cadastro.php"; ?>
    
    </form>
    
    <?php if (count($this->view->dados) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >jQuery(document).ready(function() {
        showFormErros(<?php echo json_encode($this->view->dados); ?>); 
    });
    </script>
    
    <?php endif; ?>

<?php require_once _MODULEDIR_ . "Principal/View/prn_regra_abertura_os/rodape.php"; ?>
