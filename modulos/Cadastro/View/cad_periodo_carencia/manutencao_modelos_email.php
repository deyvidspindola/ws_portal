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

<form name="frm_manutencao_modelos_email" id="frm_manutencao_modelos_email" method="POST" action="cad_periodo_carencia.php">
	<input type="hidden" name="acao" id="acao" value="manutencaoModelosEmail" />
	<input type="hidden" name="idModelo" id="idModelo" value="" />
	<div align="center">
    	<table class="tableMoldura">
			<tr class="tableTitulo">
                <td><h1>Modelos de E-Mail - Reativação de Cobrança de Monitoramento</h1></td>
            </tr>
            <tr>
                <td>
                	<br />
                    <span id="msg" class="msg"><? echo @$mensagemInformativa; ?></span>
                    <input type="hidden" id='excluirEmail' name='excluirEmail' value="<?php echo @$excluir; ?>" />
                </td>
            </tr>
            <tr>
                <td align="center">
					<table class="tableMoldura" align="center">
						<tr class="tableTitulo">
							<td colspan='2'><h1>Modelo de E-mail</h1></td>
						</tr>
						<tr><td><br /></td><td><br /></td></tr>
						<tr>
							<td><label for="fim_carencia_email">Fim da carência em: (*)</label></td>
							<td align="left" width="85%">
								<input type="text" name="fim_carencia_email" id="fim_carencia_email" size="10" maxlength="3" />
								<span style="margin-top: 30px;">&nbsp;dias</span></td>
							</td>
						</tr>
						<tr>
							<td><label for="assunto_email">Assunto: (*)</label></td>
							<td align="left" width="85%">
								<input type="text" name="assunto_email" id="assunto_email" size="95" maxLength="98" />
							</td>
						</tr>
						<tr>
							<td><label for="modelo_email">Modelo de E-mail: (*)</label></td>
							<td rowspan='5'><textarea name="modelo_email" id="modelo_email" cols="95" rows="8"></textarea></td>
						</tr>
						<tr>
							<td colspan='2'><br /></td>
						</tr>
						<tr><td><br />&nbsp;</td></tr>
						<tr><td><br />&nbsp;</td></tr>
						<tr><td><br />&nbsp;</td></tr>
						<tr>
							<td></td>
							<td>
								<br />
								<p>** IMPORTANTE: Você pode utilizar as tags [CLIENTE] e [VEICULO] (em letra maiúscula e entre colchetes) no modelo </p> 
								<p>do e-mail. No ato do envio o sistema as substituirá pelo nome do cliente e placa do veículo em período de carência.</p>
							</td>
						</tr>
						<tr><td><br />&nbsp;</td></tr>
						<tr><td><br />&nbsp;</td></tr>
						<tr><td colspan='2' style="padding-left:12px;padding-bottom:10px;">Campos com (*) são de preenchimento obrigatório</td></tr>
						<tr class="tableRodapeModelo2" style="height:23px;">
							<td align="center" colspan="2">
								<input type="button" class="botao" name="bt_confirmar_modelo" value="Confirmar" id="bt_confirmar_modelo" align='right' />
								<input type="button" class="botao" name="bt_limpar_modelo" value="Limpar" id="bt_limpar_modelo" align='right' />
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="center">
					<table class="tableMoldura" align="center">
						<tr class="tableTitulo">
							<td colspan='5'><h1>Lista de Modelos</h1></td>
						</tr>
						<tr class="tableTituloColunas">
							<td><span style="margin-left: 5px;"><b>Fim da Carência em</b></span></td>
							<td><span style="margin-left: 5px;"><b>Assunto</b></span></td>
							<td><span style="margin-left: 5px;"><b>Última alteração por</b></span></td>
							<td><span style="margin-left: 5px;"><b>Data Última Alteração</b></span></td>
							<td><span style="margin-left: 5px;"><b></b></span></td>
						</tr>
						<?php echo @$tabela; ?>
					</table>
				</td>
			</tr>
		</table>
	</div>

</form>