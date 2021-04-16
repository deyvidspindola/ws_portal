 <?php require_once '_header.php'; 
$control = new FinGeraNfBoletoGrafica(true);
$view =  $control->setRetornaView();
$retorno = $control->verificarProcesso(false);
if ($retorno['codigo'] == 2) {
	$disabled = 'disabled = "disabled"';
}

?>  
   <script language="Javascript" type="text/javascript" src="modulos/web/js/fin_gera_nf_boleto_grafica.js"></script> 
    <script language="Javascript" type="text/javascript">
		var SITEURL 		= '<?php echo _SITEURL_; ?>';
		var MASK_CPF 		= '<?php echo FinGeraNfBoletoGraficaUtil::MASK_CPF; ?>';
		var MASK_CNPJ 		= '<?php echo FinGeraNfBoletoGraficaUtil::MASK_CNPJ; ?>';
		var MAX_CONNUMERO 	= '<?php echo $view->MAX_CONNUMERO; ?>';
		
		var MSG_VALIDATE_REFERENCIA 	= '<?php echo $view->MSG_VALIDATE_REFERENCIA; ?>';
		var MSG_VALIDATE_TIPO 			= '<?php echo $view->MSG_VALIDATE_TIPO; ?>';
		var MSG_VALIDATE_MAX_CONNUMERO 	= '<?php echo $view->MSG_VALIDATE_MAX_CONNUMERO; ?>';
		var MSG_VALIDATE_MIN_CONNUMERO 	= '<?php echo $view->MSG_VALIDATE_MIN_CONNUMERO; ?>';
		var MSG_CONFIRMA_GERACAO 		= '<?php echo $view->MSG_CONFIRMA_GERACAO; ?>';
		var MSG_FALHA_PESQUISA 			= '<?php echo $view->MSG_FALHA_PESQUISA; ?>';
		var MSG_NENHUMA_NF_SELECIONADA 	= '<?php echo $view->MSG_NENHUMA_NF_SELECIONADA; ?>';
    </script>

 <div id="wrapper" align="center">
        <table width="100%" class="tableMoldura" align="center">
            <tr class="tableTitulo">
                <td><h1>Geração de NF e boleto para a gráfica</h1>
                </td>
            </tr>
              <tr id="msg">
                <td>
	                <span id="div_msg" class="msg"><?php if(!empty($retorno['msg']) || $retorno['msg'] != '') echo $retorno['msg']; ?></span>
                </td>
            </tr>
            <tr>
                <td height="18" id="msg" class="msg" style="left: 20px;">
	                <span id="div_msg"><?php echo $view->msg; ?></span>
	                <span id="div_msg_alidacao" style="display: none;"><?php echo $view->msgValidacao; ?></span>
                </td>
            </tr>
            <tr>
                <td align="center"><br />
                
					<form name="frm" id="frm" method="post" action="">
                        <table width="98%" class="tableMoldura">
                            <tr class="tableSubTitulo">
                                <td><h2>Dados para a pesquisa:</h2></td>
                            </tr>                            
                            <tr>
                                <td>
                                    <table>
                                        <tr>
                                            <td style="width:220;">
                                                <label for="frm_data"><b>Data de Referência: *</b></label>
                                            </td>
                                            <td colspan="3">
                                                <input tabindex="1" type="text" name="frm_data" id="frm_data" value="<?php echo FinGeraNfBoletoGraficaUtil::dateToView($view->voPesquisa->frm_data); ?>" size="10" maxlength="10" style="width:120px;" tabindex="1" onblur="revalidar(this,'@@/@@/@@@@','data');">
                                                <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.forms[0].frm_data,'dd/mm/yyyy',this)" align="absmiddle" alt="Calendário">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="frm_doc">CPF/CNPJ:</label>
                                            </td>
                                            <td style="width:280px;">
                                                <input tabindex="2" type="text" id="frm_doc" name="frm_doc" value="<?php echo FinGeraNfBoletoGraficaUtil::docToView($view->voPesquisa->frm_doc, $view->voPesquisa->frm_tipo); ?>" maxlength="18" style="width:200px;">
                                            </td>
                                            <td align="right">
                                            	<table>
                                            		<tr>
                                            			<td style="width:50px;"><label for="frm_tipo">Tipo:</label></td>
	                                            		<td><input tabindex="3" type="radio" name="frm_tipo" id="frm_tipo_f" class="frm_tipo" value="F" <?php if ($view->voPesquisa->frm_tipo == 'F'): ?>checked="checked"<?php endif; ?> /></td>
	                                            		<td><label for="frm_tipo_f">Física</label></td>
	                                            		<td><input tabindex="4" type="radio" name="frm_tipo" id="frm_tipo_j" class="frm_tipo" value="J" <?php if ($view->voPesquisa->frm_tipo == 'J'): ?>checked="checked"<?php endif; ?> /></td>
	                                            		<td><label for="frm_tipo_j"> Jurídica</label></td>
                                            		</tr>
                                            	</table>
                                            </td>
                                        </tr>	
                                        <tr>
                                            <td>
                                                <label for="frm_cliente">Nome do Cliente:</label>
                                            </td>
                                            <td colspan="2">
                                                <input tabindex="5" type="text" id="frm_cliente" name="frm_cliente" value="<?php echo $view->voPesquisa->frm_cliente; ?>" maxlength="300" style="width:380px;">
                                            </td>
                                        </tr>	                        
                                        <tr>
                                            <td>
                                                <label for="frm_tipo_contrato">Tipo Contrato:</label>
                                            </td>
                                            <td colspan="2">
                                                <select tabindex="6" id="frm_tipo_contrato" name="frm_tipo_contrato" style="width:100%;">
                                                    <option value="">- Selecione -</option>
                                                	<?php foreach ($view->tiposContrato as $tipoContrato): ?>
                                                    <option value="<?php echo $tipoContrato->key; ?>" <?php if ($tipoContrato->key == $view->voPesquisa->frm_tipo_contrato): ?>selected="selected"<?php endif; ?>><?php echo $tipoContrato->value; ?></option>
                                                	<?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="frm_contrato">No. Termo:</label>
                                            </td>
                                            <td>
                                                <input tabindex="7" type="text" id="frm_contrato" name="frm_contrato" value="<?php echo $view->voPesquisa->frm_contrato; ?>" maxlength="10" style="width:200px;">
                                            </td>
                                            <td align="right">
                                            	<table>
                                            		<tr>
                                            			<td style="width:50px;"><label for="frm_placa">Placa:</label></td>
	                                            		<td><input tabindex="8" type="text" id="frm_placa" name="frm_placa" value="<?php echo $view->voPesquisa->frm_placa; ?>" maxlength="7" style="width:130px;"></td>
                                            		</tr>
                                            	</table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <br /><label>(*) Campos com preenchimento obrigatório</label><br />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <fieldset id="mensagens_boleto" >
                                    	<legend>Mensagens no boleto</legend>
                                    	
                                    	<?php foreach ($view->mensagensBoleto as $chave => $mensagemBoleto): ?>
                                    	<p class="mensagem_boleto" id="mensagem_boleto_<?php echo $chave; ?>"><?php echo $mensagemBoleto; ?></p>
                                    	<?php endforeach; ?>
                                    </fieldset>
                                </td>
                            </tr>
							<tr>
								<td>&nbsp;</td>
							</tr>
                            <tr class="tableRodapeModelo1">
                                <td align="center">
                                <input type="hidden" name="acao" id="acao" value="prepararPrevia" />
<!--                                    <input tabindex="9" type="submit" class="botao" name="btn_pesquisar" id="btn_pesquisar" value="Pesquisar">-->
                                <input <?php echo $disabled;?> tabindex="9" type="submit" class="botao" name="btn_pesquisar" id="btn_pesquisar" value="Gerar Prévia">
                                <input <?php echo $disabled;?> tabindex="12" type="submit" id="btn_gerar" name="btn_gerar" value="Gerar arquivo" class="botao" />&nbsp;
                                <input <?php echo $disabled;?> tabindex="12" type="button" id="btn_visualizar" name="btn_visualizar" value="Visualizar Arquivos" class="botao" />&nbsp;
                                </td>
                            </tr>
                        </table>
						
						<?php if ($view->arquivo): ?>
                        <br><center><a onclick='' target="_blank" href='download.php?arquivo=<?php echo $view->arquivo ?>'><img src='images/icones/t3/caixa2.jpg'><br>Download do arquivo ZIP</a></center><br/>
						<?php endif ?>
						
                    <!-- Se existir arquivo de prévia-->
                    <?php if ($view->pathArquivoPrevia): ?>
                        <br><center><a onclick='' target="_blank" href='download.php?arquivo=<?php echo $view->pathArquivoPrevia ?>'><img src='images/icones/t3/caixa2.jpg'><br>Download do arquivo CSV</a></center><br/>
                    <?php endif ?>
						
                    
                    
                </td>
            </tr>
            <tr>
            	<td>
            	<div id="resultado" >
            		<?php require_once 'resultado_arquivos.php'; ?>
            	</div>
            	
            	</td>
            </tr>
            
            </form>
        </table>
        
         
    </div>
    
    