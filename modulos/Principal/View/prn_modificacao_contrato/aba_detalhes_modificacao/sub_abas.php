	<ul class="bloco_opcoes">
		<li class="<?php echo ($this->view->sub_tela == 'aba_dados_principais') ? 'ativo' : '' ?>">
			<a href="?acao=detalhar&sub_tela=aba_dados_principais&mdfoid=<?php echo $this->view->parametros->mdfoid ?>">Dados Principais</a>
		</li>
		<li class="<?php echo ($this->view->sub_tela == 'aba_itens') ? 'ativo' : '' ?>">
			<a href="?acao=detalhar&sub_tela=aba_itens&mdfoid=<?php echo $this->view->parametros->mdfoid ?>">Itens</a>
		</li>
        <li class="<?php echo ($this->view->sub_tela == 'aba_acessorios') ? 'ativo' : '' ?>">
            <a href="?acao=detalhar&sub_tela=aba_acessorios&mdfoid=<?php echo $this->view->parametros->mdfoid ?>">Acessórios</a>
        </li>
         <li class="<?php echo ($this->view->sub_tela == 'aba_historico') ? 'ativo' : '' ?>">
            <a href="?acao=detalhar&sub_tela=aba_historico&mdfoid=<?php echo $this->view->parametros->mdfoid ?>">Histórico</a>
        </li>
	</ul>