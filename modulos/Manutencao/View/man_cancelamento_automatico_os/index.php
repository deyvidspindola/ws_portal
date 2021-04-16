<?php require_once _MODULEDIR_ . "Manutencao/View/man_cancelamento_automatico_os/cabecalho.php"; ?>

<!-- Mensagens-->
<div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemErro; ?>
</div>

<div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemAlerta; ?>
</div>

<div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemSucesso; ?>
</div>


<form id="form"  method="post" action="man_cancelamento_automatico_os.php">
    <input type="hidden" id="acao" name="acao" value="gravar"/>
    <input type="hidden" id="pcaooid" name="pcaooid" value=""/>

    <?php require_once _MODULEDIR_ . "Manutencao/View/man_cancelamento_automatico_os/formulario_parametrizacao.php"; ?>

</form>

<?php require_once _MODULEDIR_ . "Manutencao/View/man_cancelamento_automatico_os/rodape.php"; ?>
