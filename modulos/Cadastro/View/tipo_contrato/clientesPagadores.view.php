<fieldset class="myFieldset">
<legend><b>Cliente Pagador</b></legend>
<table>
	<tr>
		<td width="5%">
			<label for="cli_monit">Monitoramento:</label>
		</td>
		<td width="30%">
			<input type="hidden" name="cli_id_monit" id="cli_id_monit" value="<?php echo $tpccliente_pagador_monitoramento; ?>" />
			<input type="text" name="cli_monit" id="cli_monit" class="campo1" maxlength="100" value="<?php echo $nomecli_pagador_monitoramento; ?>" />
		</td>
		<td width="30%" align="center" valign="top">
			<fieldset class="myFieldset" style="width:50%">
			<legend><b>Tipo Pessoa</b></legend>
			<?php 
				if ($tipocli_pagador_monitoramento=="F") 
				{ 
					$check_fisica = 'checked="checked"';
					$check_juridica = '';
				} else {
					$check_fisica = '';
					$check_juridica = 'checked="checked"';
				}
			?>
				<input type="radio" name="cli_tipo_monit" class="cli_tipo_monit radio" id="cli_tipo_monit_f" value="F" <?php echo $check_fisica; ?>/>Física&nbsp;&nbsp;
				<input type="radio" name="cli_tipo_monit" class="cli_tipo_monit radio" id="cli_tipo_monit_j" value="J" <?php echo $check_juridica; ?>/>Jurídica
			</fieldset>
		</td>
		<td width="5%">
			<label for="cli_doc_monit">CPF/CNPJ:</label>
		</td>
		<td width="20%">
			<input type="text" name="cli_doc_monit" id="cli_doc_monit" class="campo2" maxlength="20" tabindex="2" value="<?php echo $doccli_pagador_monitoramento; ?>" />
		</td>
		<td width="10%">
			&nbsp;<input type="button" id="bt_cli_monit" name="bt_cli_monit" class="botao" value="Pesquisar" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="cli_monit">Locação:</label>
		</td>
		<td>
			<input type="hidden" name="cli_id_loc" id="cli_id_loc" value="<?php echo $tpccliente_pagador_locacao; ?>" />
			<input type="text" name="cli_loc" id="cli_loc" class="campo1" maxlength="100" tabindex="3" value="<?php echo $nomecli_pagador_locacao; ?>" /> 
		</td>
		<td align="center" valign="top">
			<fieldset class="myFieldset" style="width:50%">
			<legend><b>Tipo Pessoa</b></legend>
			<?php 
				if ($tipocli_pagador_locacao=="F") 
				{
					$check_fisica = 'checked="checked"';
					$check_juridica = '';
				} else {
					$check_fisica = '';
					$check_juridica = 'checked="checked"';
				}
			?>
				<input type="radio" name="cli_tipo_loc" class="cli_tipo_loc radio" id="cli_tipo_loc_f" value="F" <?php echo $check_fisica; ?>/>Física&nbsp;&nbsp;
				<input type="radio" name="cli_tipo_loc" class="cli_tipo_loc radio" id="cli_tipo_loc_j" value="J" <?php echo $check_juridica; ?>/>Jurídica
			</fieldset>
		</td>
		<td>
			<label for="cli_doc_loc">CPF/CNPJ:</label>
		</td>
		<td>
			<input type="text" name="cli_doc_loc" id="cli_doc_loc" class="campo2" maxlength="20" tabindex="4" value="<?php echo $doccli_pagador_locacao; ?>" />
		</td>
		<td>
			&nbsp;<input type="button" id="bt_cli_loc" name="bt_cli_loc" class="botao" value="Pesquisar" />
		</td>
	</tr>
</table>
</fieldset><br /><br />