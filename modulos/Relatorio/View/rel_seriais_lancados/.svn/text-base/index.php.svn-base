<?php require_once _MODULEDIR_ . "Relatorio/View/rel_seriais_lancados/cabecalho.php"; ?>
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

<?php if(!count($this->view->dados) && !isset($this->view->parametros->acao)): ?>
	<div id="mensagem_info" class="mensagem info <?php if (empty($this->view->mensagemInfo)): ?>invisivel<?php endif;?>">
    	<?php echo $this->view->mensagemInfo; ?>
	</div>
<?php endif; ?>

<?php require_once _MODULEDIR_ . "Relatorio/View/rel_seriais_lancados/formulario_pesquisa.php"; ?>
<?php 
    // Resultado pesquisa
    if ($this->view->parametros->acao == 'pesquisar' && $this->view->status && count($this->view->dados) > 0) { 
        require_once 'resultado_pesquisa.php'; 
    } else if($this->view->parametros->acao == 'gerar-csv' && is_array($this->view->arquivoCSV)){
        require_once 'resultado_csv.php';
    }
?>
<?php require_once _MODULEDIR_ . "Relatorio/View/rel_seriais_lancados/rodape.php"; ?>
