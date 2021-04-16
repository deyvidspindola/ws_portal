
<html>
<head>
<title><?=$dadosboleto["identificacao"]?></title>
<meta http-equiv="Content-Type content=text/html charset=UTF-8">

<style type=text/css>

td {
	font: Arial;
}

.margem {
	padding-left: 4px;
}

.borda-lat-esq {
	border-left: 1px black solid;
}

.borda-lat-esq2 {
	border-left: 2px black solid;
}

.borda-base {
	border-bottom: 1px black solid;
}

.borda-base2 {
	border-bottom: 2px black solid;
}

.titulo-menor {
	font-size: 15px;
}

.titulo-maior {
	font-size: 20px;
}

.fonte-menor {
	font-size: 9px;
}

.fonte-media {
	font-size: 10px;
}

.fonte-maior {
	font-size: 20px;
}

.negrito {
	font-weight: bold;
}

</style>

</head>
<body>
	<table cellspacing=0 cellpadding=0 width=666 border=0 class="negrito">
		<tr>
			<td width=150 class="borda-base2">
				<img src="<?=$url?>/images/img_boleto/logohsbc.jpg" width="144" height="30" border="0" />
			</td>
			<td class="borda-lat-esq2 titulo-maior borda-base2" width=58 valign="bottom" align="center">
				<?=$dadosboleto["codigo_banco_com_dv"]?>
			</td>

			<td class="borda-lat-esq2 titulo-menor borda-base2" align="right" width=451 valign="bottom">
				<?=$dadosboleto["linha_digitavel"]?>
			</td>
		</tr>
	</table>
	<table cellspacing=0 cellpadding=0 width=666 border=0>
		<tbody>
			<tr>
				<td width="475" class="borda-lat-esq margem fonte-menor">
					Local de pagamento
				</td>
				<td width="50" class="borda-lat-esq margem fonte-media">
					Parcela
				</td>
				<td width="100" class="borda-lat-esq margem fonte-media">
					Vencimento
				</td>
			</tr>
			<tr>
				<td width="475" class="borda-lat-esq margem fonte-menor borda-base negrito">
					Pagável em qualquer Banco até o vencimento
				</td>
				<td width="50" class="borda-lat-esq margem fonte-media borda-base negrito" align="left">
					<?=$dadosboleto["parcelas"]?>
				</td>
				<td width="100" class="borda-lat-esq margem fonte-media borda-base negrito" align="right">
					<?=$dadosboleto["data_vencimento"]?>
				</td>
			</tr>
			<tr>
				<td width="475" class="borda-lat-esq margem fonte-menor">
					Cedente
				</td>
				<td width="180" class="borda-lat-esq margem fonte-media" colspan="2"> 
					Agência/Código Cedente
				</td>
			</tr>
			<tr>
				<td width="475" class="borda-lat-esq margem fonte-menor borda-base negrito">
					<?=$dadosboleto["cedente"]?>
				</td>
				<td width="180" class="borda-lat-esq margem fonte-media borda-base negrito" align="right" colspan="2">
					<?=$dadosboleto["agencia_codigo"]?>
				</td>
			</tr>
		</tbody>
	</table>
	
	
	
	<table cellspacing=0 cellpadding=0 width=666 border=0>
		<tbody>
			
			<tr>
				<td width="105" class="borda-lat-esq margem fonte-menor">
					Data do documento
				</td>
				<td width="143" class="borda-lat-esq margem fonte-menor">
					No documento
				</td>
				<td width="60" class="borda-lat-esq margem fonte-menor">
					Espécie doc.
				</td>
				<td width="33" class="borda-lat-esq margem fonte-menor">
					Aceite
				</td>
				<td width="86" class="borda-lat-esq margem fonte-menor">
					Data processamento
				</td>
				<td width="180" class="borda-lat-esq margem fonte-menor">
					Nosso número
				</td>
			</tr>
			
			
			<tr>
				<td width="105" class="borda-lat-esq margem fonte-media negrito borda-base">
					<?=$dadosboleto["data_documento"]?>
				</td>
				<td width="143" class="borda-lat-esq margem fonte-media negrito borda-base">
					<?=$dadosboleto["numero_documento"]?>
				</td>
				<td width="60" class="borda-lat-esq margem fonte-media negrito borda-base">
					<?=$dadosboleto["especie_doc"]?>
				</td>
				<td width="33" class="borda-lat-esq margem fonte-media negrito borda-base">
					<?=$dadosboleto["aceite"]?>
				</td>
				<td width="86" class="borda-lat-esq margem fonte-media negrito borda-base">
					<?=$dadosboleto["data_processamento"]?>
				</td>
				<td width="180" class="borda-lat-esq margem fonte-media negrito borda-base"  align="right">
					<?=$dadosboleto["nosso_numero"]?>
				</td>
			</tr>
		
		</tbody>
	</table>
	
	<table cellspacing=0 cellpadding=0 width=666 border=0>
		<tbody>
			
			<tr>
				<td width="105" class="borda-lat-esq margem fonte-menor">
					Uso do banco
				</td>
				<td width="95" class="borda-lat-esq margem fonte-menor">
					Carteira
				</td>
				<td width="36" class="borda-lat-esq margem fonte-menor">
					Espécie
				</td>
				<td width="105" class="borda-lat-esq margem fonte-menor">
					Quantidade
				</td>
				<td width="86" class="borda-lat-esq margem fonte-menor">
					Valor Documento	
				</td>
				<td width="180" class="borda-lat-esq margem fonte-menor">
					(=) Valor documento
				</td>
			</tr>
			
			<tr>
				<td width="105" class="borda-lat-esq margem fonte-media negrito borda-base">

				</td>
				<td width="95" class="borda-lat-esq margem fonte-media negrito borda-base">
					<?=$dadosboleto["carteira"]?>
				</td>
				<td width="36" class="borda-lat-esq margem fonte-media negrito borda-base">
					<?=$dadosboleto["especie"]?>
				</td>
				<td width="105" class="borda-lat-esq margem fonte-media negrito borda-base">
					<?=$dadosboleto["quantidade"]?>
				</td>
				<td width="86" class="borda-lat-esq margem fonte-media negrito borda-base">
					<?=$dadosboleto["valor_unitario"]?>
				</td>
				<td width="180" class="borda-lat-esq margem fonte-media negrito borda-base" align="right">
					<?=$dadosboleto["valor_boleto"]?>
				</td>
			</tr>
		
		</tbody>
	</table>
	
	
	
	<table cellspacing=0 cellpadding=0 width=666 border=0>
		<tbody>
			<tr>
				<td valign=top width="481" rowspan=5 class="borda-lat-esq borda-base">
					<span class="fonte-menor margem">Instruções (Texto de responsabilidade do cedente)</span>
					<br />
					<br />
					<span class="fonte-media negrito margem">
					<?php
						echo htmlentities($dadosboleto["instrucoes1"]);echo "<br />";
						echo htmlentities($dadosboleto["instrucoes2"]);echo "<br />";
						echo htmlentities($dadosboleto["instrucoes3"]);echo "<br />";
						echo htmlentities($dadosboleto["instrucoes4"]);
					?>
					</span>
				</td>
				<td align=right width="180" class="borda-lat-esq fonte-menor" valign=top>
					<table cellspacing="-1" cellpadding="5" border=0 style="padding: 0">
						<tbody>
							<tr>
								<td class="fonte-menor margem borda-base" align="left" height="22" width="181">
									(-) Desconto / Abatimentos
								</td>
							</tr>
							
							<tr>
								<td class="fonte-menor margem borda-base" align="left" height="22" width="181">
									(-) Outras deduções
								</td>
							</tr>
							<tr>
								<td class="fonte-menor margem borda-base" align="left" height="22" width="181">
									(+) Mora / Multa
								</td>
							</tr>
							<tr>
								<td class="fonte-menor margem borda-base" align="left" height="22" width="181">
									(+) Outros acréscimos
								</td>
							</tr>
							<tr>
								<td class="fonte-menor margem borda-base" align="left" height="22" width="181">
									(=) Valor cobrado
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	
	<table cellspacing=0 cellpadding=0 width=666 border=0>
		<tbody>
			<tr>
				<td valign=top width="475" class="borda-lat-esq margem fonte-menor">
					Sacado
				</td>
			</tr>
			<tr>
				<td valign=top width="475" class="borda-lat-esq margem fonte-media negrito">
					<?=htmlentities($dadosboleto["sacado"])?>
				</td>
			</tr>
			
			<tr>
				<td valign=top width="475" class="borda-lat-esq margem fonte-media negrito">
					<?=htmlentities($dadosboleto["endereco1"])?>
				</td>
			</tr>
			
			<tr>
				<td valign=top width="475" class="borda-lat-esq margem fonte-media negrito borda-base">
					<?=htmlentities($dadosboleto["endereco2"])?>
				</td>
				<td valign=top width="180" class="borda-lat-esq margem fonte-media borda-base">
					Cód. Baixa
				</td>
			</tr>
		</tbody>
	</table>
	
	<table cellspacing=0 cellpadding=0 border=0 width=666>
		<tbody>
			<tr>
				<td class="fonte-menor" width=409>
					Sacador/Avalista
				</td>
				<td class="fonte-menor" width=250 align=right>
					Autenticação Mecânica - <span class="negrito">Ficha de Compensação</span>
				</td>
			</tr>
		</tbody>
	</table>
	
	
	<table cellspacing=0 cellpadding=0 width=666 border=0>
		<tbody>
			<tr>
				<td valign=bottom align=left height=50><?=$codbarras?></td>
			</tr>
		</tbody>
	</table>
		
		
	<table cellspacing=0 cellpadding=0 width=666 border=0>
		<tbody>
			<tr>
				<td class="fonte-menor" width=675 align="right">
					Corte na linha pontilhada
				</td>
			</tr>
			<tr>
				<td class="fonte-menor" width=675>
					<img height=1 src="<?=$url?>/images/img_boleto/6.png" width=677 border=0 />
				</td>
			</tr>
		</tbody>
	</table>
</body>
</html>
	
