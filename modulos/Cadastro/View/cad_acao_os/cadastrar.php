

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_acao_os/cabecalho.php"; ?>


    
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
    
    <div id="mensagem_excluir" class="invisivel">Deseja realmente excluir este registro?</div>

    <form id="form_cadastrar"  method="post" action="">
    <input type="hidden" id="acao" name="acao" value="cadastrar"/>
    <input type="hidden" id="mhcoid" name="mhcoid" value="<?php echo $this->view->parametros->mhcoid; ?>"/>
    
    
    <?php require_once _MODULEDIR_ . "Cadastro/View/cad_acao_os/formulario_cadastro.php"; ?>
    
    </form>
    
    <?php if (count($this->view->dados) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >jQuery(document).ready(function() {
        showFormErros(<?php echo json_encode($this->view->dados); ?>); 
    });
    </script>
    <?php endif; ?>

    <?php if (count($this->view->acoes) > 0) : ?>
    <div id="resultado_pesquisa" >
    
        <?php 
        if ( $this->view->status && count($this->view->acoes) > 0) { 
            require_once 'resultado_pesquisa.php'; 
        } 
        ?>
        
    </div>
    <?php endif; ?>

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_acao_os/rodape.php"; ?>
