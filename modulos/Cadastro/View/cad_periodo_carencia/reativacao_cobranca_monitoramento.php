<?php
cabecalho();
include("calendar/calendar.js");
include "lib/funcoes_masc.php";
include "lib/funcoes.js";
?>
<link rel="stylesheet" href="calendar/calendar.css" type="text/css"  />
<link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
<link rel="stylesheet" href="modulos/web/css/fin_estrategia_cobranca.css" type="text/css"  />
<link rel="stylesheet" href="modulos/web/css/lib/loading.css" type="text/css"  />
<!-- JAVASCRIPT -->
<script type="text/javascript" src="includes/js/calendar.js"></script>
<script type="text/javascript" src="includes/js/mascaras.js"></script>
<script type="text/javascript" src="includes/js/auxiliares.js"></script>
<script type="text/javascript" src="includes/js/validacoes.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jQueryUI/js/jquery-ui-1.8.24.custom.min.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>
<script type="text/javascript" src="modulos/web/js/cad_periodo_carencia.js"></script>

<form name="frm_reativacao_cobranca" id="frm_reativacao_cobranca" method="POST" action="cad_periodo_carencia.php">
    <input type="hidden" name="acao" id="acao" value="reativacaoCobrancaMonitoramento" />
    <div align="center">
    	<table class="tableMoldura">
			<tr class="tableTitulo">
                <td><h1>Período de Carência para Reativação da Cobrança de Monitoramento</h1>
	                <input name="vigencia" type="hidden" value="<?php echo @$vigencia; ?>" />
					<input name="periodo" type="hidden" value="<?php echo @$periodo; ?>" />
				</td>
            </tr>
			<tr>
                <td>
                	<br />
                    <span id="msg" class="msg"><? echo @$mensagemInformativa; ?></span>
                </td>
            </tr>

            <tr>
                <td align="center">
					<table class="tableMoldura" align="center">
						<tr class="tableTitulo">
							<td colspan='2'><h1>Período de Carência</h1></td>
						</tr>
						<tr><td><br /></td><td><br /></td></tr>
						<tr>
							<td><label for="departamento">Início de Vigência: (*)</label></td>
							<td align="left" width="85%">
								<input type="text" name="data_vigencia" id="data_vigencia" size="10" maxlength="10" onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@','data');" value="<?php echo date(@$vigencia); ?>" />&nbsp;
                                <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.forms[0].data_vigencia,'dd/mm/yyyy',this)" align="absmiddle" border="0" alt="Calendário..." style="margin-top:2px;">
							</td>
						</tr>
						<tr><td colspan='2'><br /></td></tr>
						<tr>
							<td><label for="periodo">Período: (*)</label></td>
							<td><input type="text" name="periodo_vigencia" id="periodo_vigencia" size="10" maxlength="3" value="<?php echo @$periodo; ?>" />
							<span style="margin-top: 30px;">&nbsp;dias</span></td>
						</tr>
						<tr>
							<td colspan='2'><br /></td>
						</tr>
						<tr>
							<td colspan='2'><label style="margin-left: 16px;margin-bottom: 20px;">Campos com (*) são de preenchimento obrigatório</label></td>
						</tr>
						<tr class="tableRodapeModelo2" style="height:23px;">
							<td align="center" colspan="2">
								<input type="button" class="botao" name="bt_confirmar_reativacao" value="Confirmar" id="bt_confirmar_reativacao" align='right' />
								<input type="button" class="botao" name="bt_limpar_reativacao" value="Limpar" id="bt_limpar_reativacao" align='right' />
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td><br /></td>
			</tr>
			<tr>
				<td align="center">
					<table class="tableMoldura" align="center">
						<tr class="tableTitulo">
							<td colspan='3'><h1>Histórico de alterações</h1></td>
						</tr>
						<tr class="tableTituloColunas">
							<td><span style="margin-left: 5px;"><b>Data</b></span></td>
							<td><span style="margin-left: 5px;"><b>Usuário</b></span></td>
							<td><span style="margin-left: 5px;"><b>Observação</b></span></td>
						</tr>
						<?php echo @$tabela; ?>
					</table>
				</td>
			</tr>
		</table>
    </div>
</form>