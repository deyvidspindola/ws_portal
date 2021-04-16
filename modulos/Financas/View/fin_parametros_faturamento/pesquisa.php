<!-- CSS -->
<link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
<link type="text/css" rel="stylesheet" href="includes/css/calendar.css">
<link type="text/css" rel="stylesheet" href="modulos/web/css/fin_parametros_faturamento.css">

<!--[if IE 9]>
<style>
.period_fat{
	margin-left: 48px;
}

.field_tpPessoa{
	margin-left: 4px;
}

#ativo_faturamento{
	margin-left: -1px;
}

#isento_cobranca{
	margin-left: -1px;
}

</style>

<![endif]-->

<!-- JAVASCRIPT -->
<script type="text/javascript" src="includes/js/calendar.js"></script>
<script type="text/javascript" src="includes/js/mascaras.js"></script>
<script type="text/javascript" src="includes/js/auxiliares.js"></script>  
<script type="text/javascript" src="includes/js/validacoes.js"></script>

<!-- jQuery -->
<script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>

<!-- jQuery UI -->
<link type="text/css" rel="stylesheet" href="modulos/web/js/lib/jQueryUI/themes/base/jquery.ui.all.css">        
<script src="modulos/web/js/lib/jQueryUI/ui/jquery.effects.core.js"></script>
<script src="modulos/web/js/lib/jQueryUI/ui/jquery.effects.highlight.js"></script>

<!-- Script JS -->
<script type="text/javascript" src="modulos/web/js/fin_parametros_faturamento.js"></script>
<div style="text-align: center;">
<form name="filtro" id="filtro" method="post">
 <input type="hidden" name="acao" value="" />
 <input type="hidden" name="parfoid" value="" />
 <input type="hidden" name="pesquisa_automatica" value="<?php echo isset($_POST['pesquisa_automatica']) ? $_POST['pesquisa_automatica'] : ''; ?>" />
  <table class="tableMoldura" style="margin: 0 auto;">
	<tr class="tableTitulo">
		<td><h1>Parâmetros do Faturamento</h1></td>
	</tr>
    <tr>
    	<td>
    		&nbsp;<span id="msg" class="msg"></span>
    	</td>
   	</tr>
	<tr>
	 <td align="center">
		<table class="tableMoldura" border="1">
			<tr class="tableSubTitulo">
				<td colspan="9"><h2>Dados para Pesquisa</h2></td>
			</tr>
			<tr>
				<td nowrap="nowrap"><label for="contrato">Contrato:</label></td>
				<td nowrap="nowrap"><input type="text" id="contrato" name="contrato" size="10" maxlength="30" /></td>
				<td nowrap="nowrap" rowspan="2">
					<fieldset class="field_tpPessoa" style="margin-bottom: 2px;">
						<legend>Tipo Pessoa</legend>
						<label>
							<input type="radio" name="tipo_pessoa" value="PF" class="radio" />
							Física
						</label>
						<div class="clear"></div>
						<label>
							<input type="radio" name="tipo_pessoa" value="PJ" class="radio" checked="checked" />
							Jurídica
						</label>
					</fieldset>
				</td>
				<td nowrap="nowrap" rowspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td nowrap="nowrap" colspan="2">&nbsp;</td>
				<td nowrap="nowrap" align="right"><label for="isento_cobranca">Isento Cobrança:</label></td>
				<td nowrap="nowrap" style="width: 80px;"><input type="checkbox" id="isento_cobranca" name="isento_cobranca" class="checkbox" /></td>
				<td rowspan="6" style="width: 100%;"></td>
			</tr>
			<tr>
				<td nowrap="nowrap"><label for="cliente">Cliente:</label></td>
				<td nowrap="nowrap"><input type="text" id="cliente" name="cliente" size="25" maxlength="50" /></td>
				<!-- MESMO CAMPO -->
				<td nowrap="nowrap" class="field_cnpj td_cnpj"><label for="cnpj">CNPJ:</label></td>
				<td nowrap="nowrap" class="field_cpf td_cpf"><label for="cpf">CPF:</label></td>
				<td nowrap="nowrap" class="field_cnpj"><input type="text" name="cnpj" id="cnpj" size="30" maxlength="30" class="cnpj" /></td>
				<td nowrap="nowrap" class="field_cpf"><input type="text" name="cpf" id="cpf" size="30" maxlength="30" class="cpf" /></td>
				<!-- /MESMO CAMPO -->
				<td nowrap="nowrap" align="right"><label for="ativo_faturamento">Ativo p/ faturam.:</label></td>
				<td nowrap="nowrap"><input type="checkbox" id="ativo_faturamento" name="ativo_faturamento" class="checkbox" /></td>
			</tr>
			<tr>
				<td nowrap="nowrap"><label for="tipo_contrato">Tipo de Contrato:</label></td>
				<td nowrap="nowrap" colspan="2">
					<select id="tipo_contrato" name="tipo_contrato">
						<option value="">Selecione</option>
					</select>
				</td>
				<td nowrap="nowrap" rowspan="2" colspan="4" valign="top">
					<fieldset class="field_nivel">
						<legend>Nível</legend>
						<label>
							<input type="checkbox" name="nivel[]" value="1" class="checkbox" />
							Contrato
						</label>
						<label>
							<input type="checkbox" name="nivel[]"  value="2" class="checkbox" />
							Cliente
						</label>
						<label>
							<input type="checkbox" name="nivel[]"  value="3" class="checkbox" />
							Tipo Contrato
						</label>
						<label>
							<input type="checkbox" name="nivel[]"  value="4" class="checkbox" />
							Cliente e Tipo Contrato
						</label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td nowrap="nowrap" class="label_obr"><label for="obrigacao_financeira">Obrigação Financeira:</label></td>
				<td nowrap="nowrap" colspan="2">
					<select id="obrigacao_financeira" name="obrigacao_financeira">
						<option value="">Selecione</option>
					</select>
				</td>
			</tr>
			<tr>
				<td nowrap="nowrap" class="period_fat"><label for="periodicidade_faturamento">Periodicidade do Faturamento: &nbsp;</label></td>
				<td nowrap="nowrap" colspan="2" class="period_fat">
					<select id="periodicidade_faturamento" name="periodicidade_faturamento">
						<option value="">Selecione</option>
					</select>
				</td>
				<?php /*
				<td nowrap="nowrap" rowspan="2" colspan="4" valign="top">
					<fieldset class="field_nivel" style="width: 150px;">
						<legend>Enviar fatura p/ gráfica</legend>
						<label>
							<input type="checkbox" name="envia_grafica[]" value="'t'" class="checkbox" />
							Sim
						</label>
						<label>
							<input type="checkbox" name="envia_grafica[]"  value="'f'" class="checkbox" />
							Não
						</label>
					</fieldset>
				</td>
				*/ ?>
			</tr>
			<tr>
				<td colspan="3" style="height: 25px;"></td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr class="tableRodapeModelo1" style="height: 23px;">
				<td colspan="9" align="center">
					<input type="button" id="btn_pesquisar" name="btn_pesquisar" value="Pesquisar" class="botao" />
					<input type="button" id="btn_novo" name="btn_novo" value="Novo" class="botao" />
				</td>
			</tr>
		</table>
		 </td>
	</tr>
    <tr align="center">
		<td>
			<table class="tableMoldura resultado_pesquisa" style="display: none; margin: 0 auto;">
			<!-- Aqui recebe o resultado da pequisa retornado por Ajax -->				        
		    </table>
		</td>
	</tr>
	<tr>
		<td align="center">
			<div class="processando" style="display: none;">
			    <center>
			    	<img src="images/loading.gif" alt="" style="margin: 0 auto;"/>
			    </center>
			</div>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>
</form>
</div>