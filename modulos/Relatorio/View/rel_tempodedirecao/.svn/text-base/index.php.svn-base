<form name="frm" id="frm" method="POST" action="rel_tempodedirecao.php">
    <input type="hidden" name="gerar_csv" id="gerar_csv" />
    <input type="hidden" name="exportdata" id="exportdata" />
    <input type="hidden" name="acao" id="acao" />
    <input type="hidden" name="limite_resultados" id="limite_resultados" value="<?php echo($this->getLimiteResultados()); ?>" />
    <center>
        <table class="tableMoldura">
            <tr class="tableTitulo">
                <td><h1>Relat&oacute;rio de Tempo de Dire&ccedil;&atilde;o</h1></td>
            </tr>
            <tr height="20">
                <td>
                    <span id="div_msg_pesquisar" class="msg"></span>
                </td>
            </tr>	
            <tr height="25">
               	<td align="center">
                    <table class="tableMoldura">
                        <tr class="tableSubTitulo">
                            <td colspan="4"><h2>Dados para pesquisa</h2></td>
                        </tr>			
                        <tr style="height: 50px !important;">
                            <td width="15%"><label for="data_inicio_pesquisa">Per&iacute;odo: (*)</label></td>
                            <td>
                                <input type="text" name="data_inicio_pesquisa" id="data_inicio_pesquisa" size="10" maxlength="10"  onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@','data');" value="<?php echo date('d/m/Y'); ?>" style="float:left; margin-top:8px;margin-right:5px;" />
                                <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.forms[0].data_inicio_pesquisa,'dd/mm/yyyy',this)" align="absmiddle" border="0" alt="Calend�rio..." style="float:left;margin-top:8px;margin-right:5px;">
                                <span style="float:left;margin-top:15px;margin-right:5px;">&agrave;</span>
                                <input type="text" name="data_fim_pesquisa" id="data_fim_pesquisa" size="10" maxlength="10" onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@','data');" value="<?php echo date('d/m/Y'); ?>" style="float:left; margin-top:8px;margin-right:5px;" /> 
                                <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.forms[0].data_fim_pesquisa,'dd/mm/yyyy',this)" align="absmiddle" border="0" alt="Calend�rio..." style="float:left; margin-top:8px;margin-right:5px;">
                            </td>
                        </tr>
                       	<tr height="25">
                            <td><label for="nome_cliente">Cliente (*)</label></td>
                            <td>
                                <input type="text" name="nome_cliente" id="nome_cliente" value="" maxlength="50" style="width:396px;">
				<input type="hidden" id="clioid" name="clioid" value="">
				<div class="mensagem alerta invisivel" id="msg_alerta_autocomplete"></div>
                            </td>
                        </tr>
			<tr>
			    <td>
                                <label for="motivo">Placa (*)</label>
                            </td>
                            <td>
                                <input type="text" name="placa" id="placa" value="" maxlength="50" style="width:100px;">
                            </td>
			</tr>
                        <tr>
                            <td><label for="motorista">Motorista</label></td>
                            <td colspan="3">
                                <input type="text" name="motorista" id="motorista" value="" maxlength="50" style="width:396px;">
                            </td>
                        </tr>					
                        <tr height="24">
                            <td colspan="2"><label>(*) Campos com preenchimento obrigat&oacute;rio</label></td>
                        </tr>
                        <tr class="tableRodapeModelo1" style="height:23px;">
                            <td align="center" colspan="2">
                                <input type="button" name="bt_pesquisar" id="bt_pesquisar" value="Pesquisar" class="botao" style="width:90px;">
                                <input type="button" name="bt_cancelar" id="bt_cancelar" value="Cancelar pesquisa" class="botao" style="width:120px; display: none;">
                            </td>
                        </tr>
                    </table>		
                    <div class="processando" id="processando" style="display: none;"><img src="images/loading.gif" alt="" /></div>
                </td>
            </tr>
            
            <tr>
                <td align="center">
                    <div id="resultado_cabecalho_fixo" class="listagem cabecalho_fixo" style="display: none;">
			<table class="tableMoldura resultado_pesquisa" style=" display: none;">
                    	</table>
		    </div>
		    <div id="resultado_bloco_mensagens" class="bloco_mensagens" style="display: none;">
	   	    </div>
		    
                </td>
            </tr>
            
            <tr>
                <td align="center">
                    <div class="processando_csv" style="display:none;"><img src="images/loading.gif" alt="" /></div>
                </td>
            </tr>
        </table>
    </center>
</form>
