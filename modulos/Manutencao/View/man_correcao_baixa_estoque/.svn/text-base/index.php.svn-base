<?php require_once _MODULEDIR_ . "/Manutencao/View/man_correcao_baixa_estoque/cabecalho.php"; ?>
    
<!-- Mensagens-->
<div id="info_principal" class="mensagem info <?php if (!empty($this->view->mensagemRelatorio)): ?>invisivel<?php endif; ?>">
	Campos com * são obrigatórios.
</div>
    
<div id="mensagem_relatorio_andamento" class="mensagem info <?php if (empty($this->view->mensagemRelatorio)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemRelatorio; ?>
</div>

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
    <input type="hidden" id="acao" name="acao" value="pesquisar"/>
	<input type="hidden" id="tipo_csv" name="tipo_csv" value=""/>
	<input type="hidden" id="arrayOS" name="arrayOS" value=""/>
	<input type="hidden" id="totalItens" name="totalItens" value="<?php echo $this->view->totalItens;?>"/> 
	
    <?php require_once _MODULEDIR_ . "/Manutencao/View/man_correcao_baixa_estoque/formulario_pesquisa.php"; ?>
    
    <div id="resultado_pesquisa" >
    
	    <?php 
        if ( $this->view->status && count($this->view->dados) > 0) { 
            require_once 'resultado_pesquisa.php'; 
        } 
        ?>
	    
    </div>
</form>
    
<?php require_once _MODULEDIR_ . "/Manutencao/View/man_correcao_baixa_estoque/rodape.php"; ?>