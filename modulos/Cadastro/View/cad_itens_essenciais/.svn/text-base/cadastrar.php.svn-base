

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_itens_essenciais/cabecalho.php"; ?>


    
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

    <div id="mensagem_excluir" class="invisivel">
        Deseja realmente excluir este registro?
    </div>
    
    
    <form id="form_cadastrar"  method="POST" action="" enctype="multipart/form-data">
    <input type="hidden" id="acao" name="acao" value="cadastrar"/>
    <input type="hidden" id="iesoid" name="iesoid" value="<?php echo $this->view->parametros->iesoid; ?>"/>
    
    
    <?php require_once _MODULEDIR_ . "Cadastro/View/cad_itens_essenciais/formulario_cadastro.php"; ?>
    
    </form>
    
    <?php if (count($this->view->validacao) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >jQuery(document).ready(function() {
        showFormErros(<?php echo json_encode($this->view->validacao); ?>); 
    });
    </script>
    
    <?php endif; ?>

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_itens_essenciais/rodape.php"; ?>
