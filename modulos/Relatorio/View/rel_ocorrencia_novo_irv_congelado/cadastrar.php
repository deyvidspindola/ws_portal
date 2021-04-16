

<?php require_once _MODULEDIR_ . "Relatorio/View/rel_ocorrencia_novo_irv_congelado/cabecalho.php"; ?>


    
    <!-- Mensagens-->
    <div id="mensagem_info" class="mensagem info">Os campos com * são obrigatórios.</div>
    
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
    <input type="hidden" id="" name="" value="<?php //echo $this->view->parametros->; ?>"/>
    
    
    <?php require_once _MODULEDIR_ . "Relatorio/View/rel_ocorrencia_novo_irv_congelado/formulario_cadastro.php"; ?>
    
    </form>
    
    <?php if (count($this->view->dados) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >jQuery(document).ready(function() {
        showFormErros(<?php echo json_encode($this->view->dados); ?>); 
    });
    </script>
    
    <?php endif; ?>

<?php require_once _MODULEDIR_ . "Relatorio/View/rel_ocorrencia_novo_irv_congelado/rodape.php"; ?>
