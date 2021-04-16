<?php require_once _MODULEDIR_ . "Cadastro/View/cad_gerenciadora/cabecalho.php"; ?>
	<div>
		<center>
	    <form id="endpoint_form" name="endpoint_form" method="post" >
			<input type="hidden" id="endpoint_url_functions" name="endpoint_url_functions" value=<?php echo "modulos/Cadastro/View/cad_gerenciadora/cad_gerenciadora_endpoint.php";?> >
	    	<input type="hidden" id="endpoint_acao" name="endpoint_acao" value="index"/>
	    	<input type="hidden" id="endpoint_id" name="endpoint_id" value=""/>
			<input type="hidden" id="risk_manager_id" name="risk_manager_id" value=<?php echo $geroid; ?> >
			<input type="hidden" id="gerenciadora_acao" name="gerenciadora_acao" value=<?php echo $acao; ?> >
				
			<table class="tableMoldura">
				<tr class="tableSubTitulo">
					<td colspan="2"><h2>Cadastro do Endpoint</h2></td>
                </tr>
				<tr id="tr_mensagem_aviso" name="tr_mensagem_aviso">
					<td colspan="2"><div id="mensagem_aviso" name="mensagem_aviso" class="msg"></div></td>
				</tr>
				<tr id="tr_mensagem_alerta" name="tr_mensagem_alerta"> 					
					<td colspan="2"><div id="mensagem_alerta" name="mensagem_alerta" class="msg"></div></td> 
				</tr>
				<tr id="tr_mensagem_firewall_espaco"  name="tr_mensagem_firewall_espaco">
					<td colspan="2">&nbsp;</td>
				</tr>				
				<tr id="tr_mensagem_firewall" name="tr_mensagem_firewall">
					<td colspan="2"><div id="mensagem_firewall" name="mensagem_firewall"></div></td>
				</tr>
				<tr id="tr_ger_endpoint_protocolo" name="tr_ger_endpoint_protocolo"> 						
					<td class="label" id="lbl_ger_endpoint_protocolo" name="lbl_ger_endpoint_protocolo"width="15%" nowrap >Protocolo:</td>
					<td width="85%">
						<select id="ger_endpoint_protocolo" name="ger_endpoint_protocolo">
							<option value="nenhum">Nenhum</option>
							<option value="caessat">CAESSAT</option>
							<option value="wirsolut">WIRSOLUT</option>
						</select>
					</td>
				</tr>
				<tr id="tr_ger_endpoint_ip" name="tr_ger_endpoint_ip">							
					<td class="label" id="lbl_ger_endpoint_ip" name="lbl_ger_endpoint_ip" width="15%" nowrap >IP:</td>
					<td width="85%">
						<input  type="text" id="ger_endpoint_ip" name="ger_endpoint_ip" maxlength="120" value="" obrigatorio="1">
					</td>
				</tr>
				<tr id="tr_ger_endpoint_porta" name="tr_ger_endpoint_porta">
					<td class="label" id="lbl_ger_endpoint_ip" name="lbl_ger_endpoint_ip" width="15%" nowrap >Porta:</td>
					<td width="85%">
						<input  type="text" id="ger_endpoint_porta" name="ger_endpoint_porta" maxlength="60" value="" obrigatorio="1">
					</td>
				</tr>				
				<tr>
					<td colspan="2" class="tableRodapeModelo1" align="center">
						<input type="button" class="botao" id="bt_gravar" name="bt_gravar" value="Atualizar Endpoint">
					</td>
				</tr>
				
			</table>	 
				
	    </form>
    
<?php require_once _MODULEDIR_ . "Cadastro/View/cad_gerenciadora/rodape.php"; ?>
