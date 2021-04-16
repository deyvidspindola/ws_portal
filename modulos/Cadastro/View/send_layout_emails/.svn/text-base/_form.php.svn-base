<script type="text/javascript" src="modulos/web/js/send_layout_emails.js"></script>   
<script type="text/javascript">
jQuery(document).ready(function() {

	//removeAlerta();
	<?php if($this->hasFlashMessage()): ?>
		criaAlerta('<?=$this->flashMessage()?>');
		jQuery('#bt_enviar').attr('disabled', 'disabled');
	<?php endif; ?>
	
});
</script>

<center><br />
	<table class="tableMoldura">
		<tr class="tableTitulo">
	        <td><h1>Tela de Edição de Layout de Emails</h1></td>
	    </tr>	    
	    <tr height="20">
			<td>
				<span id="div_msg" class="msg">
					<? //echo ($this->hasFlashMessage()) ? $this->flashMessage() : '' ?>
				</span>
			</td>
		</tr>
		<tr>
	        <td align="center">
	            <form method="post" id="editar_layout_emails" enctype="multipart/form-data" action="send_layout_emails.php?acao=confirmarEmail" >
	           		<input type="hidden" name="editavel" id="editavel" value="f">
	            	        
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
		                	<td width="15%"><label>Título do email: *</label></td>	      
		                    <td>
		                    	<select name="seeoid" id="seeoid" >
		                    		<option  value="">Escolha...</option>
		                    		
		                    		<?php 
		                    		//$form['seeoid']=26;
		                    		foreach ($listLayouts as $layout): ?>
		                    		<option value="<?=$layout['seeoid']?>" <?=($this->_getIdLayout()==$layout['seeoid'] ? 'selected="selected"' : '')?> ><?=$layout['seecabecalho']?></option>
		                    		<?php endforeach; ?>
		                    	</select>
		                    	<!-- input type="text" readonly="readonly" name="seecabecalho" id="seecabecalho" size="50" maxlength="50" value="<?= $form['seecabecalho'] ?>" -->
		                    </td>  
		                </tr>		                
		                <tr id="seecorpoo_readonly" style="display: none;" >
		                	<td width="15%" valign="top" nowrap><label>Conteúdo: * 
		                		<br />(máximo 3 mil caracteres)</label>
		                	</td>	      
		                    <td>
		                    	<iframe style="overflow: auto; border-style: solid;border-width: 1px;height: 400px;width: 750px;"  id="seecorpo1" ></iframe>
		                    </td>  
		                </tr>
		                <tr id="seecorpoo_edit" style="display: none;" >
		                	<td width="15%" valign="top"><label>Conteúdo: * 
		                		<br />(máximo 3 mil caracteres)</label>
		                	</td>	      
		                    <td>
		                    	<textarea name="seecorpo" id="seecorpo" maxlength="50" class="editor"><?php //= $form['seecorpo'] ?></textarea>
		                    </td>  
		                </tr>	                
		                <tr>
		                	<td width="15%"></td>	      
		                    <td>
		                    	<div style="text-align: left; width: 700px;">Você ainda pode digitar <span id="char-count">3000</span> caracteres.</div>
		                    </td>  
		                </tr>
                        <tr>
							<td colspan="2">
							<label for="ancdt_cadastro_ini">(*) Campos de preenchimento obrigatório.</label>
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
