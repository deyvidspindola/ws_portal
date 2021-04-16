	<ul class="bloco_opcoes">
		<?php if ($this->view->parametros->acao == 'cadastrar'):  ?>
			<li class="<?php echo ($this->view->tela == 'cadastro') ? 'ativo' : '' ?>">				
				<a href="?acao=cadastrar">Cadastro Modificação</a>				
			</li>
		<?php endIf; ?>
		<?php if (!empty($this->view->parametros->mdfoid)): ?>
			<li class="<?php echo ($this->view->tela == 'detalhes') ? 'ativo' : '' ?>" >
				<a href="?acao=detalhar&mdfoid=<?php echo $this->view->parametros->mdfoid ?>">Detalhes da Modificação</a>
			</li>
		<?php endIf; ?>		
	</ul>