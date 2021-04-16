<?php
	$debito_automatico = new RelDebitoAutomatico();
	$motivos = $debito_automatico->getMotivos();
?>
<form name="frm" id="frm" method="POST" action="rel_debito_automatico.php">
    <input type="hidden" name="gerar_csv" id="gerar_csv" />
    <input type="hidden" name="exportdata" id="exportdata" />
    <input type="hidden" name="acao" id="acao" />
    <center>
        <table class="tableMoldura">
            <tr class="tableTitulo">
                <td><h1>Relatório de Débito Automático</h1></td>
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
                        <tr style="line-height: 10px;">
                            <td width="15%"><label>Tipo</label></td>
                            <td width="40%">
                                <select id="tipo_relatorio" name="tipo_relatorio" style="width: 300px" >
                                    <option value="analitico">Analítico</option>
                                    <option value="sintetico">Sintético</option>
                                </select>
							</td>
                            <td  width="15%" align="right">
                                <label for="tipo_operacao">Tipo de Operação</label>
                            </td>
                            <td align="left">
                                <select id="tipo_operacao" name="tipo_operacao" style="width: 300px" >
									<option value="">Todos</option>
									<option value="A">Alteração</option>
									<option value="E">Exclusão</option>
									<option value="I">Inclusão</option>
                                </select>
                            </td>
                        </tr>				
                        <tr style="height: 50px !important;">
                            <td width="15%"><label for="data_inicio_pesquisa">Período: (*)</label></td>
                            <td width="25%">
                                <input type="text" name="data_inicio_pesquisa" id="data_inicio_pesquisa" size="10" maxlength="10"  onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@','data');" value="<?php echo date('d/m/Y'); ?>" style="float:left; margin-top:8px;margin-right:5px;" />
                                <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.forms[0].data_inicio_pesquisa,'dd/mm/yyyy',this)" align="absmiddle" border="0" alt="Calendário..." style="float:left;margin-top:8px;margin-right:5px;">
                                <span style="float:left;margin-top:15px;margin-right:5px;">à</span>
                                <input type="text" name="data_fim_pesquisa" id="data_fim_pesquisa" size="10" maxlength="10" onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@','data');" value="<?php echo date('d/m/Y'); ?>" style="float:left; margin-top:8px;margin-right:5px;" /> 
                                <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.forms[0].data_fim_pesquisa,'dd/mm/yyyy',this)" align="absmiddle" border="0" alt="Calendário..." style="float:left; margin-top:8px;margin-right:5px;">
			             		<fieldset id="trResultado" style="background-color: #FFF; border: 1px solid #CCCCCC; width: 150px; margin-left: 8px; text-align:center;display:none;">
                        			<legend style="margin-left: 10px;">Resultado</legend>
                        			<input type="radio" name="resultado" value="D" checked />Diário
                        			<input type="radio" name="resultado" value="M" />Mensal
                        		</fieldset>
                            </td>
                            <td  width="15%" align="right">
                                <label for="canal_entrada">Canal de Entrada</label>
                            </td>
                            <td align="left">
                                <select id="canal_entrada" name="canal_entrada" style="width: 300px" >
									<option value="">Todos</option>
									<option value="I">Intranet</option>
									<option value="P">Portal</option>
                                </select>
                            </td>
                        </tr>
                       	<tr height="25">
                            <td><label for="nome_cliente">Cliente</label></td>
                            <td>
                                <input type="text" name="nome_cliente" id="nome_cliente" value="" maxlength="50" style="width:396px;"> <?= desenhaHelpComment('Digite parte do nome do cliente para busca.'); ?>
                            </td>
                            <td width="15%" align="right">
                                <label for="motivo">Motivo</label>
                            </td>
                            <td align="left">
                                <select id="motivo" name="motivo" style="width: 300px">
                                    <option value="">Todos</option>
									<?php
										foreach($motivos as $row) { 
											echo "<option value='".$row['id']."'>".$row['descricao']."</option>";
										}
									?> 
                                </select>
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="5">

							</td>
                        </tr>					
                        <tr height="24">
                            <td colspan="4"><label>(*) Campos com preenchimento obrigatório</label></td>
                        </tr>
                        <tr class="tableRodapeModelo1" style="height:23px;">
                            <td align="center" colspan="4">
                                <input type="button" name="bt_pesquisar" id="bt_pesquisar" value="Pesquisar" class="botao" style="width:90px;">
                                <input type="button" name="bt_cancelar" id="bt_cancelar" value="Cancelar pesquisa" class="botao" style="width:120px; display: none;">
                            </td>
                        </tr>
                    </table>		
                    <div class="processando" style="display: none;"><img src="images/loading.gif" alt="" /></div>
                </td>
            </tr>
            
            <tr>
                <td align="center">
                    <table class="tableMoldura resultado_pesquisa" style="width: 1210px; display: none;">
                    </table>
                </td>
            </tr>
            
            <tr>
                <td align="center">
                    <table class="tableMoldura grafico_pesquisa" style="width: 1210px; display: none;">
                        
                        <tr class="tableSubTitulo">
                            <td colspan="4"><h2>Gráfico</h2></td>
                        </tr>
                        
                        <tr>
                            <td>
                                <div style="width: 1210px; overflow-x: scroll;">
                                    <div id="grafico"></div>
                                </div>
                            </td>
                        </tr>
                        
                    </table>
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