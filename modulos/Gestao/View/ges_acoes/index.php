<?php require_once $this->view->caminho.'cabecalho.php'; ?>

<div id="mensagem_info" class="mensagem info">Os campos com * são obrigatórios.</div>

<div id="div_mensagem_geral" class="mensagem invisivel"></div>

<?php if (!empty($this->view->mensagem->erro)) : ?>
    <div class="mensagem erro"><?php echo $this->view->mensagem->erro; ?></div>
<?php endif; ?>

<?php if (!empty($this->view->mensagem->sucesso)) : ?>
    <div class="mensagem sucesso"><?php echo $this->view->mensagem->sucesso; ?></div>
<?php endif; ?>

<?php if (!empty($this->view->mensagem->alerta) && is_array($this->view->mensagem->alerta)) : ?>
    <?php foreach($this->view->mensagem->alerta as $key => $msg) : ?>
        <div class="mensagem alerta"><?php echo $msg; ?></div>
    <?php endForEach; ?>
<?php elseif (!empty($this->view->mensagem->alerta)) : ?>
    <div class="mensagem alerta"><?php echo $this->view->mensagem->alerta; ?></div>
<?php endif; ?>

<?php if ( ( isset($this->recarregarArvore) && $this->recarregarArvore == true ) || (isset($this->view->recarregarArvore) && $this->view->recarregarArvore == true) ) :?>
<script type="text/javascript">
    parent.recarregaArvore();
</script>
<?php endif; ?>
    
<?php if (count($this->view->destaque) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >jQuery(document).ready(function() {
            showFormErros(<?php echo json_encode($this->view->destaque); ?>);
        });
    </script>
<?php endif; ?>

<form name="form" id="form">
<input type="hidden" name="acao" id="acao" value="<?php echo $this->param->acao ?>" />
<input type="hidden" name="id_meta_selecionada" id="id_meta_selecionada" value="<?php echo $this->param->meta ?>" />
<input type="hidden" name="id_plano_selecionado" id="id_plano_selecionado" value="<?php echo $this->param->plano ?>" />
<input type="hidden" name="ano" id="ano" value="<?php echo $this->param->ano ?>" />

<?php 

	require_once $this->view->caminho."/formulario_cadastro.php"; 

?>

</form>

<?php require_once $this->view->caminho."/rodape.php"; ?>

