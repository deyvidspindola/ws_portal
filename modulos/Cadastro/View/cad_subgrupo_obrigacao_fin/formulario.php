<!-- Form  -->
<div class="bloco_titulo">Dados principais</div>
<form method="POST" action="cad_subgrupo_obrigacao_fin.php?acao=<?php echo $this->view->acao; ?><?php if(!empty($this->view->subgrupo)): echo '&id='. $this->view->subgrupo->ofsgoid; endif; ?>">
	<div class="bloco_conteudo">
		<div class="formulario">
			<div class="campo maior">
				<label>Descrição</label>
				<input type="text" name="descricao" class="campo" maxlength="50" value="<?php if(!empty($this->view->subgrupo)): echo htmlentities($this->view->subgrupo->ofsgdescricao); endif; ?>">
			</div>
			<div class="clear"></div>
			<fieldset class="maior">
				<legend>Status</legend>
				<input type="radio" id="status_ativo" name="status" value="true" <?php if(!empty($this->view->subgrupo)): echo $this->view->subgrupo->ofsgstatus == 't' ? 'checked' : ''; else: echo 'checked'; endif; ?>>
				<label for="status_ativo">Ativo</label>
				<input type="radio" id="status_inativo" name="status" value="false" <?php if(!empty($this->view->subgrupo)): echo $this->view->subgrupo->ofsgstatus != 't' ? 'checked' : ''; endif; ?>>
				<label for="status_inativo">Inativo</label>
			</fieldset>
			<div class="clear"></div>
		</div>
	</div>
	<div class="bloco_acoes">
		<button type="submit"><?php echo $this->view->acao == 'cadastrar' ? 'Inserir' : 'Alterar'; ?></button>
		<button class="btn-link" data-href="cad_subgrupo_obrigacao_fin.php">Voltar</button>
	</div>
</form>
<br>
