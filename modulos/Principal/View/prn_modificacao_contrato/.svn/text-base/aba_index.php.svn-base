<ul class="bloco_opcoes">
	<li class="<?php echo ($this->view->tela == 'pesquisa') ? 'ativo' : '' ?>" >
		<a href="?acao=index">Pesquisa</a>
	</li>
	<li class="<?php echo ($this->view->tela == 'contratos_vencer') ? 'ativo' : '' ?>" >
		<a href="?acao=pesquisar&tela_pesquisa=contratos_vencer">Contratos a Vencer</a>
	</li>
	<?php if($this->permisaoAnaliseCredito): ?>
		<li class="<?php echo ($this->view->tela == 'analise_credito') ? 'ativo' : '' ?>" >
			<a href="?acao=recuperarAnaliseCredito">Análise de Crédito</a>
		</li>
	<?php endIf ;?>
</ul>