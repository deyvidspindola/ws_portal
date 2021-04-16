<?php require_once _MODULEDIR_ . "Cadastro/View/cad_espelhamento_4gr/cabecalho.php"; ?>
    
    <!-- Mensagens-->
    <div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemErro; ?>
    </div>
    
    <div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemAlerta; ?>
    </div>
    
    <div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemSucesso; ?>
    </div>
    
    <form id="form"  method="post" action="">
  
    <?php require_once _MODULEDIR_ . "Cadastro/View/cad_espelhamento_4gr/formulario_pesquisa.php"; ?>
    
    </form>
    
<?php require_once _MODULEDIR_ . "Cadastro/View/cad_espelhamento_4gr/rodape.php"; ?>