<div class="modulo_conteudo">
	<div id="mensagem_info" class="mensagem info">Campos com * s&atilde;o obrigat&oacute;rios.</div>			 
	<div class="bloco_titulo">Dados Principais</div>
	<div class="bloco_conteudo">
		<div class="formulario">
			<div class="campo menor">
				<label id="lbl_periodo_isencao" for="periodo_isencao">Per&iacute;odo Isen&ccedil;&atilde;o *</label>
				<select id="periodo_isencao" name="periodo_isencao">
					<option value="30"  <?php if ($this->view->parametros->periodo_isencao == 1 || $this->view->parametros->periodo_isencao == '') echo "selected='SELECTED'"; ?> > 30 dias</option>
					<option value="60"  <?php if ($this->view->parametros->periodo_isencao == 2) echo "selected='SELECTED'"; ?> > 60 dias</option>
					<option value="90"  <?php if ($this->view->parametros->periodo_isencao == 3) echo "selected='SELECTED'"; ?> > 90 dias</option>
					<option value="120" <?php if ($this->view->parametros->periodo_isencao == 4) echo "selected='SELECTED'"; ?> >120 dias</option>
				</select>
			</div>
			<div class="campo maior">
				<label id="lbl_parfemail_contato" for="parfemail_contato">E-mail *</label>
				<input id="parfemail_contato" name="parfemail_contato" value="<?php echo $this->view->parametros->parfemail_contato; ?>" class="campo" type="text">
			</div>
			<div class="clear"></div>
			<?php 
			if ($this->view->parametros->opcoes_cancelar != "") { 
				?>
				<fieldset class="medio opcoes-display-block">
					<legend>Cancelar Isen&ccedil;&atilde;o *</legend>
						<?php 
						echo $this->view->parametros->opcoes_cancelar;
						?>
				</fieldset>
				<div class="clear"></div>
				<?php 
			}
			?>
		</div>
	</div>
</div> 
