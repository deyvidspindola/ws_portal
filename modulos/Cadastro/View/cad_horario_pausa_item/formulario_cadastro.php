<div class="bloco_titulo">Dados Principais</div>
<div class="bloco_conteudo">
	<div class="formulario">
		<div class="campo medio">
			<label id="lbl_gtroid" for="gtroid">Grupo de Trabalho *</label>
			<select id="gtroid" name="gtroid" <?php echo (!$this->view->acesso) ? 'disabled="disabled"' : '' ;?>>
				<option value="">Escolha</option>
                <?php foreach($this->view->parametros->comboGrupoTrabalho as $grupoTrabalho): ?>
                <option <?php echo $this->view->parametros->gtroid == $grupoTrabalho->gtroid ? 'selected="selected"' : ''?> value="<?php echo $grupoTrabalho->gtroid; ?>"><?php echo $grupoTrabalho->gtrnome; ?></option>
                <?php endforeach; ?>
			</select>
		</div>
		<div class="clear"></div>

		<div class="campo medio">
			<label id="lbl_motaoid" for="motaoid">Tipo de Pausa *</label>
			<select id="motaoid" name="motaoid" <?php echo (!$this->view->acesso) ? 'disabled="disabled"' : '' ;?>>
				<option value="">Escolha</option>
			</select>
		</div>
		<div class="clear"></div>

		<div class="campo medio">
			<label id="lbl_hrpiatendente" for="hrpiatendente">Atendente *</label>
			<select id="hrpiatendente" name="hrpiatendente"  <?php echo (!$this->view->acesso) ? 'disabled="disabled"' : '' ;?>>
				<option value="">Escolha</option>
			</select>
		</div>
		<div class="clear"></div>

        <div class="campo menor">
			<label id="lbl_horario_inicial" for="horario_inicial">Horário *</label>
			<input id="horario_inicial" name="horario_inicial" value="" class="campo" type="text" <?php echo (!$this->view->acesso) ? 'disabled="disabled"' : '' ;?> />
		</div>
		
		<?php /*
		<div class="campo menor">
			<label id="lbl_horario_final" for="horario_final">à *</label>
			<input id="horario_final" name="horario_final" value="" class="campo" type="text" <?php echo (!$this->view->acesso) ? 'disabled="disabled"' : '' ;?> />
		</div>
		<div class="clear"></div>
		*/ ?>

		<fieldset class="medio opcoes-inline invisivel">
			<legend id="lgd_filtro_de_horario">Filtro de Horário</legend>

			<input id="intervalo" name="filtro_horario" value="I" type="radio" <?php echo (!$this->view->acesso) ? 'disabled="disabled"' : '' ;?> checked="checked"/>
				<label id="lbl_intervalo" for="intervalo">Intervalo</label>

			<input id="exato" name="filtro_horario" value="E" type="radio" <?php echo (!$this->view->acesso) ? 'disabled="disabled"' : '' ;?> />
				<label id="lbl_exato" for="exato">Exato</label>

		</fieldset>

        <div class="clear"></div>

		<div class="campo menor" id="div_tempo">
			<label id="lbl_hrpitempo" for="hrpitempo">Tempo *</label>
			<input id="hrpitempo" name="hrpitempo" value="" class="campo" type="text"  maxlength="3" <?php echo (!$this->view->acesso) ? 'disabled="disabled"' : '' ;?> />
		</div>
		<div class="clear"></div>

        <div class="campo menor">
			<label id="lbl_tolerancia" for="tolerancia">Tolerância *</label>
			<select id="tolerancia" name="tolerancia" <?php echo (!$this->view->acesso) ? 'disabled="disabled"' : '' ;?>>
				<option value="">Escolha</option>
				<option value="00">00</option>
				<option value="05">05</option>
				<option value="10">10</option>
				<option value="15">15</option>
				<option value="20">20</option>
				<option value="25">25</option>
				<option value="30">30</option>
			</select>
		</div>

		<div class="clear"></div>

	</div>
</div>
<div class="bloco_acoes">
    <?php if ($this->view->acesso): ?>
	<button type="button" id="bt_confirmar" name = "bt_confirmar">Lançar Pausa</button>
    <?php endif; ?>
</div>