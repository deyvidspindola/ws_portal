<?php require_once $this->view->caminho.'cabecalho.php'; ?>

<div id="mensagem_info" class="mensagem info">Os campos com * são obrigatórios.</div>

<div id="div_mensagem_geral" class="mensagem invisivel"></div>

<?php if (!empty($this->view->mensagem->erro)) : ?>
    <div class="mensagem erro"><?php echo $this->view->mensagem->erro; ?></div>
<?php endif; ?>

<?php if (!empty($this->view->mensagem->alerta)) : ?>
    <div class="mensagem alerta"><?php echo $this->view->mensagem->alerta; ?></div>
<?php endif; ?>

<?php if (!empty($this->view->mensagem->sucesso)) : ?>
    <div class="mensagem sucesso"><?php echo $this->view->mensagem->sucesso; ?></div>
<?php endif; ?>

<form enctype="multipart/form-data" id="form"  method="post" action="">
<input type="hidden" id="acao" name="acao" value=""/>
<?php if(!$this->view->erro) : ?>
	<?php if($this->view->permissao) :?>
		<?php require_once $this->view->caminho."/indicadores_previstos.php"; ?>

		<div class="separador"></div>

		<?php require_once $this->view->caminho.'/indicadores_realizados.php'; ?>
		
		<div class="separador"></div>
		
		<?php require_once $this->view->caminho.'/acao.php'; ?>
	<?php else : ?>
		<?php require_once $this->view->caminho.'/indicadores_realizados.php'; ?>
	<?php endif; ?>
<?php endif; ?>

</form>

<?php if (count($this->view->destaque) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >
        jQuery(document).ready(function() {
            showFormErros(<?php echo json_encode($this->view->destaque); ?>);
        });
    </script>
<?php endif; ?>

<?php require_once $this->view->caminho."/rodape.php"; ?>

