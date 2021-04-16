<?php
require_once 'xajax/xajax.inc.php';
$xajax = new xajax();
$xajax->setCharEncoding("ISO-8859-1");

if($listaClientesNovo != '' || $listaClientesNovo != null || $listaClientesNovo > 0) {

	$cep = $listaClientesNovo['ptendcep'];
	$cidade = $listaClientesNovo['ptendcidade'];
	$bairro = $listaClientesNovo['ptendbairro'];
	$logradouro = $listaClientesNovo['ptendlogradouro'];
	$numero = $listaClientesNovo['ptendnumero'];
	$complemento = $listaClientesNovo['ptendcomplemento'];
	$fone = $listaClientesNovo['ptendfone'];
	$fone2 = $listaClientesNovo['ptendfone2'];
	$fone3 = $listaClientesNovo['ptendfone3'];
	$email = $listaClientesNovo['ptendemail'];
	$emailnf = $listaClientesNovo['ptendemailnf'];
    $paisend = $listaClientesNovo['ptendpaisoid'];
    $estadoend = $listaClientesNovo['ptendestoid'];

	
}else if($listaClienteExistente != '' || $listaClienteExistente != null || $listaClienteExistente > 0){
	$resultidEstado = $control->buscaEstadoID($listaClienteExistente['enduf']);
	$estadoend = $resultidEstado['estoid'];
	$cep = $listaClienteExistente['endno_cep'];
	$cidade = $listaClienteExistente['endcidade'];
	$bairro = $listaClienteExistente['endbairro'];
	$logradouro = $listaClienteExistente['endlogradouro'];
	$numero = $listaClienteExistente['endno_numero'];
	$complemento = $listaClienteExistente['endcomplemento'];
	$fone = $listaClienteExistente['fone1'];
	$fone2 = $listaClienteExistente['fone2'];
	$fone3 = $listaClienteExistente['fone3'];
	$email = $listaClienteExistente['cliemail'];
	$emailnf = $listaClienteExistente['cliemail_nfe'];
	$paisend = $listaClienteExistente['clipaisoid'];

}

?>

<div class="mensagem alerta" id="msgalerta2" style="display: none;"></div>
<div class="modulo_titulo"><?php echo "Endereço"; ?></div>
<div class="bloco_conteudo">

	<div id="editarEndereco"></div>
	<table border="0" cellspacing="1" cellpadding="0" class="tableMoldura">
		<tr id="msg">
			<td></td>
		</tr>
		<tr>
			<td colspan="8" align="center"><br>
				<table class="tableMoldura">
					<tr>
						<td><label for="cliente">CEP*</label></td>
					</tr>
					<tr>
						<td><input type="text" id="prpend_cep" name="prpend_cep"
							value="<?=$cep?>" size="20" maxlength="10" />
							<div id="imgCEP_endereco"></div>
						</td>
					

					</tr>

					<tr>
						<td><label for="cliente"><?php echo "País*"; ?></label></td>
					</tr>
					<tr>
						<td><select name="prpend_pais" id="prpend_pais">
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
						<td><select name="prpend_est" id="prpend_est">
								<option value="">UF</option>
								<?php foreach ($estado as $row) : ?>
								<option value="<?php echo $row['estoid']; ?>" rel="<?php echo $row['estuf']; ?>" <? if ($estadoend==$row['estoid']) echo " SELECTED"; ?>><?php echo $row['estuf'];?></option>
								<?php endforeach;?>
						</select></td>

					</tr>



					<tr>
						<td><label for="cliente">Cidade*</label></td>
					</tr>
					<tr>

						<td>
						<div id="cidadesInput">
						<input type="text" id="prpend_cid" name="prpend_cid" value="<?=utf8_decode($cidade);?>"
							class="campo" maxlength="100" />
						</div>
							<div id="cidadesSelect"></div>
						</td>

					</tr>



					<tr>
						<td><label for="cliente">Bairro*</label></td>
					</tr>
					<tr>
						<td>
						<div id="bairrosInput">
						<input type="text" id="prpend_bairro" name="prpend_bairro" value="<?=utf8_decode($bairro);?>"
							class="campo" />
						</div>
							<div id="bairrosSelect"></div>
							<input type="button" value="Cadastre Manual" id="cad_manual" />
						</td>

					</tr>


					<tr>
						<td><label for="cliente">Logradouro*</label></td>
						<td><label for="cliente">Numero*</label></td>
					</tr>
					<tr>
						<td><input type="text" id="prpend_log" name="prpend_log" value="<?=utf8_decode($logradouro);?>"
							class="campo" size="50" maxlength="100" /></td>
						<td><input type="text" id="prpend_num" name="prpend_num" value="<?=$numero?>"
							class="campo" size="10" maxlength="5" /></td>

					</tr>

					<tr>
						<td><label for="cliente">Complemento</label></td>
					</tr>
					<tr>
						<td><input type="text" id="prpend_compl" name="prpend_compl" value="<?=utf8_decode($complemento);?>"
							class="campo" size="50" maxlength="100" /></td>
					</tr>

					<Tr>
						<td><label>Fone (1)*</label></td>
						<td><label>Fone (2)*</label></td>
					</tr>
					<tr>

						<td><input type="text" name="prcfone_cont" id="prcfone_cont"
							size="15" maxLength="16" value="<?=$fone?>"></td>
						<td><input type="text" name="prcfone_cont2" id="prcfone_cont2"
							size="15" maxLength="16" value="<?=$fone2?>"></td>
					</tr>

					<tr>
						<td><label for="cliente">Fone(3)*</label></td>

					</tr>
					<tr>
						<td><input type="text" name="prcfone_cont3" id="prcfone_cont3"
							size="15" maxLength="16" value="<?=$fone3?>"></td>
					</tr>

					<tr>
						<td><label for="cliente">E-Mail*</label></td>

					</tr>
					<tr>
						<td><input type="text" id="prpend_email" name="prpend_email" value="<?=$email?>"
							class="campo" size="50" maxlength="100" /></td>
					</tr>

					<tr>
						<td><label for="cliente">E-Mail NFe*</label></td>

					</tr>
					<tr>
						<td><input type="text" id="prpend_emailnf" name="prpend_emailnf" value="<?=$emailnf?>"
							class="campo" size="50" maxlength="100" /></td>
					</tr>
				</table></td>
		</tr>

	</table>
</div>
