<? require_once '_header.php' ?>

<script type="text/javascript" src="modulos/web/js/send_layout_emails.js"></script>   

<center><br />
	<table class="tableMoldura">
		<tr class="tableTitulo">
	        <td><h1>Tela de Edição de Layout de Emails</h1></td>
	    </tr>	    
	    <tr height="20">
			<td>
				<span id="div_msg" class="msg"></span>
			</td>
		</tr>
		<tr>
	        <td align="center">
	            <form method="post" id="editar_layout_emails" enctype="multipart/form-data" action="send_layout_emails.php?acao=enviaEmailOcorrencia" >
	           		<input type="hidden" name="editavel" id="editavel" value="f">
					<input type="hidden" name="ocorrencias" id="ocorrencias" value="<?=$_GET['oco']?>">
					<textarea name="seecorpo" id="seecorpo" maxlength="10" style="display:none;"></textarea>

	            	<table class="tableMoldura">	            		
		                <tr class="tableSubTitulo">
		                    <td colspan="3"><h2>Dados principais</h2></td>
		                </tr>	 
		                <tr>
		                	<td colspan="2">
		                	   <center>
		                	   		<br />
									<table class="tableMoldura">
								        <tr class="tableSubTitulo">
									    	<td colspan="2"><h2>TAGs</h2></td>
	                					</tr>		                
								        <tr class="tdc">
									    	<td width="15%">[NOMECLIENTE]</td>
									    	<td>Nome do cliente</td>
	                					</tr>  
								        <tr class="tde">
									    	<td width="15%">[PLACA]</td>
									    	<td>Placa do veículo</td>
	                					</tr>	                
								        <tr class="tdc">
									    	<td width="15%">[MARCA]</td>
									    	<td>Marca do veículo</td>
	                					</tr>		                
								        <tr class="tde">
									    	<td width="15%">[MODELO]</td>
									    	<td>Modelo do veículo</td>
	                					</tr>		                
								        <tr class="tdc">
									    	<td width="15%">[CHASSI]</td>
									    	<td>Chassi do veículo</td>
	                					</tr>			                
								        <tr class="tde">
									    	<td width="15%">[IMAGEM]</td>
									    	<td>Imagem a ser enviada</td>
	                					</tr>	
									</table>
								</center>
		                	</td>
		                </tr>               
		                <tr>
		                	<td width="15%"><label>Título do Layout: *</label></td>	      
		                    <td>
		                    	<select name="seeoid" id="seeoid" >
									<? for($for=0; $for<count($seeoidView); $for++){
										echo "<option  value='".$seeoidView[$for]['seetoid']."'>".$seeoidView[$for]['seetdescricao']."</option>";
									   }
									?>
								</select>
		                    </td>  
		                </tr>
						<tr>
							<td align="center" colspan="2">
								<div id="conteudoLayoutEmail" class="tableMoldura" style="padding:15px; margin:10px; width:90%;"></div>
							</td>
						</tr>
		                <tr class="tableRodapeModelo1" style="height:23px;">
		                    <td align="center" colspan="2">
		                        <input type="button" name="bt_enviar"   id="bt_enviar"   value="Enviar"   class="botao" style="width: 100px;" disabled="disabled" />
		                        <input type="button" name="bt_cancelar" id="bt_cancelar" value="Cancelar" class="botao" style="width: 100px;" onclick="javascript:window.close()" />
		                    </td>
		                </tr>
	            </table>
				<div id="loading"></div>
	            </form>
	        </td>
	    </tr>
	</table>
</center>

<? @include_once "lib/rodape.php" ?>