<div class="bloco_titulo">Dados para Cadastro</div>
<div class="bloco_conteudo">
	<div class="formulario ui-sortable">
		<div class="campo maior">
			<label for="gminome">Nome do Indicador *</label>
			<input id="gminome" name="gminome" value="<?php echo ($this->view->paramatrosCadastro->gminome) ? $this->view->paramatrosCadastro->gminome : ''?>" class="campo" type="text">
		</div>

		<div class="clear"></div>

		<div class="campo medio">
			<label for="gmitipo">Tipo *</label>
			<select id="gmitipo" name="gmitipo" <?php echo isset($this->view->paramatrosCadastro->editar) ? 'disabled="disabled"' : '' ?>>
				<?php
				foreach ($this->view->comboTipo as $chave => $tipo): 

					if (isset($this->view->paramatrosCadastro->gmitipo) && $this->view->paramatrosCadastro->gmitipo != "") {
						$selected = ($this->view->paramatrosCadastro->gmitipo == $chave) ? 'selected="selected"' : '';
					} else {
						$selected = ($chave == 'M') ? 'selected="selected"' : '';
					} ?>
					<option value="<?php echo $chave ?>" <?php echo $selected ?>><?php echo $tipo ?></option>
				<?php
				endforeach; ?>

			</select>
        <?php if(isset($this->view->paramatrosCadastro->editar)) : ?>
            <input name="gmitipo" value="<?php echo ($this->view->paramatrosCadastro->gmitipo) ? $this->view->paramatrosCadastro->gmitipo : '' ?>" type="hidden" />
        <?php endif; ?>
		</div>

		<div class="clear"></div>

		<div class="campo medio">
			<label for="gmitipo_indicador">Tipo Indicador *</label>
			<select id="gmitipo_indicador" name="gmitipo_indicador">
				<?php
				foreach ($this->view->comboTipoIndicador as $chave => $tipoIndicador): 

					$selected = ($chave == 'I') ? 'selected="selected"' : '';
					if (isset($this->view->paramatrosCadastro->gmitipo_indicador)) {
						$selected = ($this->view->paramatrosCadastro->gmitipo_indicador == $chave) ? 'selected="selected"' : '';
					} ?>
					<option value="<?php echo $chave ?>" <?php echo $selected ?>><?php echo $tipoIndicador ?></option>
				<?php
				endforeach; ?>
			</select>
		</div>

		<div class="clear"></div>

		<div class="campo medio">
			<label for="gmimetrica">Métrica *</label>
			<select id="gmimetrica" name="gmimetrica">
				<?php
				foreach ($this->view->comboMetrica as $chave => $metrica): 

					$selected = ($chave == 'P') ? 'selected="selected"' : '';
					if (isset($this->view->paramatrosCadastro->gmimetrica) && $this->view->paramatrosCadastro->gmimetrica != "") {
						$selected = ($this->view->paramatrosCadastro->gmimetrica == $chave) ? 'selected="selected"' : '';
					} ?>
					<option value="<?php echo $chave ?>" <?php echo $selected ?>><?php echo $metrica ?></option>
				<?php
				endforeach; ?>
			</select>
		</div>

		<div class="clear"></div>

		<div class="campo menor">
			<label for="gmicodigo">Código *</label>
			<input id="gmicodigo" name="gmicodigo" value="<?php echo ($this->view->paramatrosCadastro->gmicodigo) ? $this->view->paramatrosCadastro->gmicodigo : ''?>" class="campo" type="text" <?php echo isset($this->view->paramatrosCadastro->editar) ? 'disabled="disabled"' : '' ?> />
            <?php if(isset($this->view->paramatrosCadastro->editar)) : ?>
    			<input name="gmicodigo" value="<?php echo ($this->view->paramatrosCadastro->gmicodigo) ? $this->view->paramatrosCadastro->gmicodigo : ''?>" type="hidden" />
            <?php endif; ?>
		</div>

		<div class="campo menor">
			<label for="gmiprecisao">Precisão</label>
			<input id="gmiprecisao" maxlength="1" name="gmiprecisao" value="<?php echo ($this->view->paramatrosCadastro->gmiprecisao) ? $this->view->paramatrosCadastro->gmiprecisao : ''?>" class="campo" type="text" />
		</div>

		<div class="clear"></div>
	</div>
</div>

<div class="bloco_acoes">
	<button type="button" id="bt_confirmar">Confirmar</button>
	<button type="button" id="bt_voltar">Voltar</button>
</div>