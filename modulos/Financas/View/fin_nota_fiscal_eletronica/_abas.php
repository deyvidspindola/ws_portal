<ul class="bloco_opcoes">
	<li class="<?php echo (empty($this->action)) ? 'ativo' : '';?>"><a href="fin_nfe_kernel.php" title="Remessa RPS">Remessa RPS</a></li>
	<li class="<?php echo ($this->action == 'acompanhamento' || $this->action == 'processarRetornoSucessoArquivoRPS' || $this->action == 'processarRetornoErroArquivoRPS') ? 'ativo' : '';?>"><a href="fin_nfe_kernel.php?acao=acompanhamento" title="Acompanhamento de Arquivos">Acompanhamento de Arquivos</a></li>

	<!--Inicio - ORGMKTOTVS-826 - ERP - Alterar tela de geração de remessa para a Prefeitura de Barueri-->
	<?php if(INTEGRACAO_TOTVS_ATIVA == false): ?>
		<li class="<?php echo ($this->action == 'ocorrencias' || $this->action == 'pesquisaOcorrencias') ? 'ativo' : '';?>"><a href="fin_nfe_kernel.php?acao=ocorrencias" title="Ocorrências">Ocorrências</a></li>
	<?php endif; ?>
	<!--Fim - ORGMKTOTVS-826 - ERP - Alterar tela de geração de remessa para a Prefeitura de Barueri-->

</ul>