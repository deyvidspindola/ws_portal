<!-- CSS -->
<link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
<link type="text/css" rel="stylesheet" href="includes/css/calendar.css">
<link type="text/css" rel="stylesheet" href="modulos/web/js/lib/jqlot/jquery.jqplot.min.css" />

<!-- JAVASCRIPT -->
<script type="text/javascript" src="includes/js/calendar.js"></script>
<script type="text/javascript" src="includes/js/mascaras.js"></script>
<script type="text/javascript" src="includes/js/auxiliares.js"></script>  
<script type="text/javascript" src="includes/js/validacoes.js"></script>
<script type="text/javascript" src="js/jquery-1.7.1.js"></script>

<script language="javascript" type="text/javascript" src="modulos/web/js/lib/jqlot/excanvas.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jqlot/jquery.min.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jqlot/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jqlot/plugins/jqplot.barRenderer.min.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jqlot/plugins/jqplot.categoryAxisRenderer.min.js"></script>

<script type="text/javascript" src="modulos/web/js/prn_historico_status_termo.js"></script>

<form name="frm_pesquisa_status_termo" id="frm_pesquisa_status_termo" method="POST" action="prn_historico_status_termo.php">
    <input type="hidden" name="acao" id="acao" />    
    <center>
        <table class="tableMoldura">
            <tr class="tableTitulo">
                <td><h1>Histórico de Status dos Termos</h1></td>
            </tr>
            <tr height="20">
                <td>
                    <span id="div_msg_pesquisar" class="msg"><?php echo $msg?></span>
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
                        <tr height="25">
                			<td><label>Contrato: *</label></td>
                			<td> 
                				<input type="text" id="contrato"  name="contrato" size="20" maxlength="12" value="<?=$contrato?>">
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
           
    
            	<? if ($acao == "pesquisaStatusTermo"):?>

						<? if ($this->numeroLinhas >0):?>					   
			<tr>
				<td align="center">
					<div id="tabs">
						<div id="fragment-1">
							<table class="tableMoldura
							">   
								<tr class="tableSubTitulo">
		                            <td colspan="20"><h2>Resultado da Pesquisa</h2></td>
		                        </tr>                    
								<tr class="tableTituloColunas">
					                <td align="center" width="33%"><h3>Data Alteração</h3></td>
					                <td align="center" width="33%"><h3>Status Termo</h3></td>
					                <td align="center" width="33%"><h3>Usuário</h3></td>
					           </tr>
												
				        	 <?php foreach($this->regra AS $linha):?>
			           			  <?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'; ?>
					            <tr class="<?php echo $zebra;?>">
									<td align="left"><?php echo $linha['data_acionamento'];?></td>
									<td align="left"><?php echo utf8_decode($linha['status']);?></td>			                                    		
					                <td align="left"><?php echo $linha['usuario'];?></td>					                
					             </tr>
                             <?php endforeach;?>
                                <tr>
									<td align="center" class="tableRodapeModelo3" colspan="9">
									<?php $letraS = (($this->numeroLinhas)>1) ? "s" : null; ?>
                                     <b>A pesquisa retornou  <?php echo $this->numeroLinhas; ?> registro<?php echo $letraS;?>.</b>
                                    </td>
                                </tr>		                                        
						<?php else:?>
							<?php //if ($rs['erro'] === 0):?>
                                    <!-- Sem resultado na consulta -->
                                <tr>
                                   <td align="center" class="tableRodapeModelo3" colspan="9">
                                     <b>Nenhum resultado encontrado.</b>
                                   </td>
                                </tr>                                
							<?php //endif;?>
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