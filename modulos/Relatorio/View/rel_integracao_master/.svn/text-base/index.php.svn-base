<form name="frm_pesquisa_integracao" id="frm_pesquisa_integracao" method="POST" action="rel_integracao_master.php">
    <input type="hidden" name="acao" id="acao" />    
    <center>
        <table class="tableMoldura">
            <tr class="tableTitulo">
                <td><h1>Registros Não Importados Na Integração</h1></td>
            </tr>
            <tr height="20">
                <td>
                    <span id="div_msg_pesquisar" class="msg">
                    <? if ($acao == "pesquisaIntegracao"):?>
                    
	                    <?php if($rs['erro'] == 1):?>
                   	    <?php echo $rs['mensagem'] ?> 
	                    <?php endif;?> 
                                        
                    <?php endif;?>
                    </span>
                </td>               
                
            </tr>	
            <tr>
                <td align="center">
                    <table class="tableMoldura">
                        <tr class="tableSubTitulo">
                            <td colspan="4"><h2>Dados para pesquisa</h2></td>
                        </tr> 
                        <tr height="5">
                            <td colspan="4"></td>
                        </tr>			
                        <tr>
                            <td width="15%"><label for="data_inicio_pesquisa">Período integração:</</label></td>
                            <td width="40%">
                                <input type="text" name="data_inicio_pesquisa" id="data_inicio_pesquisa" size="10" maxlength="10"  onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@','data');" value="<?php echo $data_inicial; ?>" />
                                <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.forms[0].data_inicio_pesquisa,'dd/mm/yyyy',this)" align="absmiddle" border="0" alt="Calendário...">
                                à
                                <input type="text" name="data_fim_pesquisa" id="data_fim_pesquisa" size="10" maxlength="10" onkeyup="formatar(this, '@@/@@/@@@@')" onBlur="revalidar(this,'@@/@@/@@@@','data');" value="<?php echo $data_final; ?>" /> 
                                <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.forms[0].data_fim_pesquisa,'dd/mm/yyyy',this)" align="absmiddle" border="0" alt="Calendário...">
                            </td> 
                        </tr>
                        <tr height="25">
                            <td><label for="nome_cliente">Nome do viajante:</label></td>
                            <td>   
                                <input type="text" name="forfornecedor" id="forfornecedor"  maxlength="50" style="width:400px;" value="<?echo $forfornecedor?>">
                            </td>				    
                        </tr>
                        <tr height="25">
                			<td><label>Solicitação:</label></td>
                			<td> 
                				<input type="text" id="numero_solicitacao"  name="numero_solicitacao" size="10" maxlength="10" value="<?=$numero_solicitacao?>">
              				</td>
            			</tr>           			
            			
            						
            			<tr height="25"> 
              				<td><label>WebService indisponível:</label></td>
              				<td>
                			<input type="checkbox" class="checkbox" name="webservice" id="webservice" value="t" <?if($webservice=="t"){ echo "CHECKED"; }?>/>
              							</td>
            			</tr>
            			<tr height="25"> 
              				<td><label>Sem adiantamento:</label></td>
              				<td>
                				<input type="checkbox" class="checkbox" name="semadto" id="semadto" value="t" <?if($semadto=="t"){ echo "CHECKED"; }?>/>
              				</td>
            			</tr>
            			<tr height="25"> 
              				<td><label>Sem reembolso:</label></td>
              				<td>
                				<input type="checkbox" class="checkbox" name="semreemb" id="semreemb" value="t" <?if($semreemb=="t"){ echo "CHECKED"; }?>/>
              				</td>
            			</tr>   
                        <tr height="5">
                            <td colspan="5"></td>
                        </tr>
                        <tr class="tableRodapeModelo1" style="height:23px;">
                            <td align="center" colspan="5">
                                <input type="button" name="bt_pesquisar" id="bt_pesquisar" value="Pesquisar" class="botao" style="width:90px;" >
                           </td>
                        </tr>
                    </table>		                    
                </td>
            </tr> 
    
            	<? if ($acao == "pesquisaIntegracao"):?>

						<? if ($this->numeroLinhas >0):?>					   
			<tr>
				<td align="center">
					<div id="tabs">
						<div id="fragment-1">
							<table class="tableMoldura
							">   
								<tr class="tableSubTitulo">
		                            <td colspan="20"><h2>Resultado da pesquisa</h2></td>
		                        </tr>                    
								<tr class="tableTituloColunas">

					                <td align="center"><h3>Data</h3></td>
					                <td align="center" width="180"><h3>Solicitação</h3></td>
					                <td align="center"><h3>Viajante</h3></td>
					                <td align="center"><h3>Motivo</h3></td>					               				                
					           </tr>
												
						             <?php  //if ($this->quantidade_arquivo_serasa > 0): ?>
							        	 <?php foreach($this->regra AS $linha):?>
					                                    
						           			  <?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'; ?>
					
					            <tr class="<?php echo $zebra;?>">
									<td align="center"><?php echo $linha['data_cad'];?></td>
									<td align="right"><?php echo $linha['lsvnumero_solicitacao'];?></td>			                                    		
					                <td align="left"><?php echo $linha['forfornecedor'];?></td>					                
					                <td align="left"><?php echo $linha['motivo'];?></td>		              
					             </tr>
					
                                        <?php endforeach;?>
                                <tr>
									<td align="center" class="tableRodapeModelo3" colspan="9">
                                     <b>A pesquisa retornou  <?php echo $this->numeroLinhas; ?> registro(s).</b>
                                    </td>
                                </tr>		                                        
                                <?php else:?>
                                    <!-- Sem resultado na consulta -->
                                <tr>
                                   <td align="center" class="tableRodapeModelo3" colspan="9">
                                   <?php if($rs['erro'] != 1):?>
                                   <b>Nenhum resultado encontrado.</b>
                                   <?php endif;?>                                     
                                   </td>
                                </tr>                                

							<?php endif;?>							
                            </table>						
                        </div> 
                    </div>                    
             </td>
       </tr>       
		<?php endif;?>
   	 </div>
  </table>
    </center>
</form>