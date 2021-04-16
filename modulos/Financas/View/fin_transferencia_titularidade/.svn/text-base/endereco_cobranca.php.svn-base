<?php
if($listaClientesNovo != '' || $listaClientesNovo != null || $listaClientesNovo > 0) {
	
	$cep = $listaClientesNovo['ptendcobcep'];
	$cidade = $listaClientesNovo['ptendcobcidade'];
	$bairro = $listaClientesNovo['ptendcobbairro'];
	$logradouro = $listaClientesNovo['ptendcoblogradouro'];
	$numero = $listaClientesNovo['ptendcobnumero'];
	$complemento = $listaClientesNovo['ptendcobcomplemento'];
	$paisend = $listaClientesNovo['ptendcobpaisoid'];
	$estadoend = $listaClientesNovo['ptendcobestoid'];

	
}else if($listaClienteExistente != '' || $listaClienteExistente != null || $listaClienteExistente > 0){

	$cep = $listaClienteExistente['endcepcobr'];
	$cidade = $listaClienteExistente['endcidcobr'];
	$bairro = $listaClienteExistente['endbairrocobr'];
	$logradouro = $listaClienteExistente['endlogradcobr'];
	$numero = $listaClienteExistente['endno_numero'];
	$complemento = $listaClienteExistente['endcomplcobr'];
    $paisend = $listaClienteExistente['endpaisoidcobr'];  
    $resultidEstado = $control->buscaEstadoID($listaClienteExistente['endufcobr']);
    $estadoend = $resultidEstado['estoid'];


}

?>
<div class="mensagem alerta" id="msgalerta2" style="display: none;"></div>
<div class="modulo_titulo"><?php echo "Endereço Cobrança";?></div>
<div class="bloco_conteudo">

	<table border="0" cellspacing="1" cellpadding="0" class="tableMoldura">
		<tr id="msg">
			<td></td>
		</tr>
		<tr>
			<td><INPUT TYPE="checkbox" NAME="end_cobranca" VALUE="1" id="end_cobranca"> o mesmo</td>
		</tr>
		<tr>
			<td colspan="8" align="center"><br>
				<table class="tableMoldura">
					<tr>
						<td><label for="cliente">CEP*</label></td>
					</tr>
					<tr>
						<td><input type="text" id="prpendcob_cep" name="prpendcob_cep"
							value="<?=$cep?>" size="20"  maxlength="10" />
						<div id="imgCEP_enderecoCob"></div>	
						</td>

					</tr>

						<tr>
						<td><label for="cliente"><?php echo "País*"; ?></label></td>
					</tr>
					<tr>
						<td><select name="prpendcob_pais" id="prpendcob_pais">
						 <option value=''>--Escolha--</option>
							<?php foreach ($pais as $row) : ?>
							<option value="<?php echo $row['paisoid']; ?>" <? if ($paisend==$row['paisoid']){echo " SELECTED";}else if($row['paisnome'] == 'Brasil'){echo " SELECTED";}; ?>><?php echo $row['paisnome'];?></option>
							<?php endforeach;?>
						</select></td>

					</tr>

					<tr>
						<td><label for="cliente">Estado*</label></td>
					</tr>
					<tr>
						<td><select name="prpendcob_est" id="prpendcob_est">
								<option value="">UF</option>
								<?php foreach ($estado as $row) : ?>
								<option value="<?php echo $row['estoid']; ?>"  <? if ($estadoend==$row['estoid']) echo " SELECTED"; ?>><?php echo $row['estuf'];?></option>
								<?php endforeach;?>
						</select></td>

					</tr>


					<tr>
						<td><label for="cliente">Cidade*</label></td>
					</tr>
					<tr>
						<td>
						<div id="cidadesCobrInput">
							<input type="text" id="prpendcob_cid" name="prpendcob_cid" value="<?=utf8_decode($cidade);?>"
							class="campo" maxlength="100" />
						</div>
							<div id="cidadesCobrSelect"></div>
						</td>

					</tr>

					<tr>
						<td><label for="cliente">Bairro*</label></td>
					</tr>
					<tr>
	
						<td>
							<div id="bairrosInputCobr">
						<input type="text" id="prpendcob_bairro" name="prpendcob_bairro" value="<?=utf8_decode($bairro);?>"
							class="campo" />
						</div>
							<div id="bairrosSelectCobr"></div>
							<input type="button" value="Cadastre Manual" id="cad_manual_end_cob" />
						</td>
					
					
					<tr>
						<td><label for="cliente">Logradouro*</label></td>
						<td><label for="cliente">Numero*</label></td>
					</tr>
					<tr>
						<td><input type="text" id="prpendcob_log" name="prpendcob_log" value="<?=utf8_decode($logradouro);?>"
							class="campo" size="50" maxlength="100" /></td>
						<td><input type="text" id="prpendcob_num" name="prpendcob_num" value="<?=$numero?>"
							class="campo" size="10" maxlength="5" /></td>

					</tr>

					<tr>
						<td><label for="cliente">Complemento</label></td>
					</tr>
					<tr>
						<td><input type="text" id="prpendcob_compl" name="prpendcob_compl" value="<?=utf8_decode($complemento);?>"
							class="campo" size="50" maxlength="100" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
				</table></td>
		</tr>

	</table>
</div>
