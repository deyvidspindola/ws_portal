<div class="bloco_titulo">Cadastro de Plano de Ação</div>
<div class="bloco_conteudo">
	<div class="formulario">

		<?php if ($this->param->codigo): ?>
			<div class="campo menor">
				<label for="codigo">Código</label>
				<input id="codigo" name="codigo" value="<?php echo $this->param->codigo;?>" class="campo desabilitado" type="text" readOnly="readonly">
			</div>

			<div class="clear"></div>
		<?php endif; ?>

		<div class="campo maior">
			<label for="titulo">Título *</label>
			<input id="titulo" maxlength="100" name="titulo" value="<?php echo $this->param->titulo ;?>" class="campo" type="text">
		</div>

		<div class="clear"></div>

		<div class="campo maior">
			<label for="meta">Meta *</label>

			<?php if ($this->layout->superUsuario) { ?>
				<select id="meta" name="meta">
					<option value="">Escolha</option>

					<?php
					foreach ($this->view->metas as $meta) :
						$selected = ($meta->gmeoid == $this->param->meta) ? 'selected="selected"' : ''; ?>
						<option value="<?php echo $meta->gmeoid ?>" <?php echo $selected ?>><?php echo $meta->gmenome ?></option>
					<?php endforeach; ?>

				</select>
			<?php } else {  ?>
				<select disabled="disabled">
					<?php
					foreach ($this->view->metas as $meta) :
						$selected = ($meta->gmeoid == $this->param->meta) ? 'selected="selected"' : ''; ?>
						<option value="<?php echo $meta->gmeoid ?>" <?php echo $selected ?>><?php echo $meta->gmenome ?></option>
					<?php endforeach; ?>
				</select>
				<input type="hidden" id="meta" name="meta" value="<?php echo $this->param->meta; ?>" />
			<?php } ?>

		</div>

		<div class="clear"></div>
		<div class="campo maior">
			<label for="combo">Responsável *</label>
			<select id="responsavel" name="responsavel">
				<option value="">Escolha</option>
				<?php
					foreach ($this->view->responsaveis as $responsavel) :
						if (intval($this->param->meta) > 0) {
							$selected = ($responsavel->funoid == $responsavel->gmefunoid_responsavel) ? 'selected="selected"' : '';
						}
						if ($this->param->acao == 'editar') {
							$selected = ($responsavel->funoid == $this->param->responsavel) ? 'selected="selected"' : '';
						} ?>
						<option value="<?php echo $responsavel->funoid ?>" <?php echo $selected ?>><?php echo $responsavel->funnome ?></option>
					<?php endforeach; ?>
			</select>
		</div>

		<div class="clear"></div>

		<fieldset class="menor opcoes-inline">
			<legend>Compartilhar</legend>
			<?php $checked = ($this->param->compartilhar) ? 'checked="checked"' : '' ;?>
			<input id="compartilhar" name="compartilhar" value="1" type="checkbox" <?php echo $checked ?>>
			<label for="compartilhar">Compartilhar</label>
		</fieldset>

		<div class="campo data periodo">
	        <div class="inicial">
	            <label for="data_inicio">Data Início *</label>
	            <input type="text" id="data_inicio" name="data_inicio" value="<?php echo ($this->param->data_inicio) ? $this->param->data_inicio : '';?>" class="campo" />
	        </div>
	        <div class="campo label-periodo">a</div>
	        <div class="final">
	            <label for="data_fim">Data Fim *</label>
	            <input type="text" id="data_fim" name="data_fim" value="<?php echo ($this->param->data_fim) ? $this->param->data_fim : '';?>" class="campo" />
	        </div>
	    </div>

		<div class="clear"></div>

		<div class="campo medio">
			<label for="status">Status *</label>
			<select id="status" name="status">
				<option value="">Escolha</option>
				<?php
					foreach ($this->view->arrComboStatus as $chave => $status) :

						$selected = ($chave == 'I') ? 'selected="selected"' : '';
						if ($this->param->acao == 'editar') {
							$selected = ($chave == $this->param->status) ? 'selected="selected"' : '';
						}  ?>
						<option value="<?php echo $chave ?>" <?php echo $selected ?>><?php echo $status ?></option>
					<?php endforeach; ?>
			</select>
		</div>

		<div class="clear"></div>

	</div>
</div>
<div class="bloco_acoes">
	<button type="button" id="confirmar">Confirmar</button>
	<?php if ($this->param->acao == 'editar') :?>
		<button type="button" id="retornar">Retornar</button>
	<?php endif; ?>
</div>
