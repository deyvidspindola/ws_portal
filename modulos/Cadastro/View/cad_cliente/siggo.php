<div class="bloco_titulo">Particularidades Cliente Siggo</div>
<form action="" name="cad_cliente_siggo" id="cad_cliente_siggo" method="post">
	<input type="hidden" name="acao" id="acao" value="persistirParticularidadeSiggo" />
	<input type="hidden" name="clioid" id="clioid" value="<?php echo $this->clioid; ?>" />
	<div class="bloco_conteudo">
		<div class="conteudo">
			<div class="campo maior">
				<label for="clippessoa_politicamente_exposta1">Pessoa Politicamente Exposta 1:</label>
				<select name="clippessoa_politicamente_exposta1" id="clippessoa_politicamente_exposta1">
					<option value="">Selecione</option>
					<option value="true" <?php echo ($this->siggo->clippessoa_politicamente_exposta1 == 't') ? ' selected="true"' : '' ; ?>>
						Sim
					</option>
					<option value="false" <?php echo ($this->siggo->clippessoa_politicamente_exposta1 == 'f') ? ' selected="true"' : '' ; ?>>
						Não
					</option>
				</select>
			</div>
			<div class="clear"></div>

			<div class="campo maior">
				<label for="clippessoa_politicamente_exposta2">Pessoa Politicamente Exposta 2:</label>
				<select name="clippessoa_politicamente_exposta2" id="clippessoa_politicamente_exposta2">
					<option value="">Selecione</option>
					<option value="true" <?php echo ($this->siggo->clippessoa_politicamente_exposta2 == 't') ? ' selected="true"' : '' ; ?>>
						Sim
					</option>
					<option value="false" <?php echo ($this->siggo->clippessoa_politicamente_exposta2 == 'f') ? ' selected="true"' : '' ; ?>>
						Não
					</option>
				</select>
			</div>
			<div class="clear"></div>

			<div class="campo maior">
				<label for="clippspsoid">Profissão:</label>
				<select name="clippspsoid" id="clippspsoid">
					<option value="">Selecione</option>
					<?php foreach ( $this->siggo->combo_profissao as $profissao) : ?>
						<option value="<?php  echo  $profissao->pspsoid;?>" 
							<?php echo ($this->siggo->clippspsoid == $profissao->pspsoid) ? ' selected="true"' : '' ; ?>>
							<?php  echo  $profissao->pspsprofdesc;?>
						</option>
					<?php endForeach; ?>
				</select>
			</div>
			<div class="clear"></div>
			<div class="campo maior">
				<label for="cliptipo_segurado">Tipo Segurado:</label>
				<select name="cliptipo_segurado" id="cliptipo_segurado">
					<option value="">Selecione</option>
					<option value="1" <?php echo ($this->siggo->cliptipo_segurado == '1') ? ' selected="true"' : '' ; ?>>
						Segurado é Proprietário
					</option>
					<option value="0" <?php echo ($this->siggo->cliptipo_segurado == '0') ? ' selected="true"' : '' ; ?>>
						Segurado Não é Proprietário
					</option>
				</select>
			</div>
			<div class="clear"></div>

		</div>
	</div>
	<div class="bloco_acoes">
		<button  type="submit" id="btn_confirmar_siggo" disabled="true">Confirmar</button>
		<button type="button" id="btn_voltar_siggo" onclick="window.location.href='cad_cliente.php'">Voltar</button>
	</div>
</form>
<div class="separador"></div>