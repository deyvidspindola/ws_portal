<?php
/**
 * @author	Gabriel Luiz Pereira
 * @email	gabriel.pereira@meta.com.br
 * @since	06/09/2012
 */
$indicador = 0;
$zebra = '';
$comissaoPaga = 0;
$comissaoAPagar = 0;
$totalComissaoPaga = 0;
$totalComissaoAPagar = 0;
?>
<table>
	<tr>
		<td nowrap align="left">
                &nbsp;&nbsp;&nbsp;
                <label>
                	Legenda:
                </label>
                            	
                <img alt="Pendente geração" src="images/icones/ap2/ap10.jpg">
                Pendente geração
                &nbsp;&nbsp;
                <img alt="Comissão gerada" src="images/icones/ap2/ap8.jpg">
                Comissão gerada
                &nbsp;&nbsp;
                <img alt="Comissão paga" src="images/icones/ap2/ap1.jpg">
            	Comissão paga
       </td>
   </tr>
   <tr>
   	<td>
   		&nbsp;
   	</td>
   </tr>
</table>
<center>
<table width="98%" class="tableMoldura">
	<tbody>
    	<tr class="tableSubTitulo">
        	<td><h2>Resultados da Pesquisa</h2></td>
        </tr>
        <?php if ($rsPesquisaNotasFiscais != null && pg_num_rows($rsPesquisaNotasFiscais) > 0): ?>
		<tr>
        	<td>
            	<table width="100%" style=" font-size: 9pt;" id="resultado_pesquisa">
	            	<thead>
	                	<tr class="tableTituloColunas">
	                    	<td nowrap="nowrap"><h3></h3></td>
	                        <td ><h3>Data Faturamento</h3></td>
	                        <td nowrap="nowrap"><h3>NF / Série</h3></td>
	                        <td width="40%"><h3>Cliente</h3></td>
	                        <td><h3>UF</h3></td>
	                        <td width="20%"><h3>Cidade</h3></td>
	                        <td><h3>Contrato</h3></td>
	                        <td><h3>Veículo</h3></td>
	                        <td width="40%"><h3>Item</h3></td>
	                        <td nowrap="nowrap"><h3>Valor NF</h3></td>                                            
	                        <td><h3>Valor Comissão</h3></td>
	                        <td><h3>Data Cálculo Comissão</h3></td>
	                        <td><h3>Data Pagamento</h3></td>                                            
	                        <td><h3>Títulos Vencidos</h3></td>                                            
	                        <td><h3>Situação Comissão</h3></td>                                            
	                    </tr>
	                </thead>
	                <!-- RESULTADOS -->
	                <tbody>
	                	<?php while ($notaFiscal = pg_fetch_object($rsPesquisaNotasFiscais)): ?>
	                		<?php if ($indicador != $notaFiscal->corroid): ?>
	                		
	                			<!-- SUB TOTAIS -->
	                			<?php if ($indicador != 0):?>
	                			<tr class="tableTituloColunas">
									<td colspan="16" style="padding-left: 4px;" style=" font-size: 9pt;">
										<div style="width: 500px; float: left">
										SUB-TOTAL COMISSÃO PAGA:
										<?php echo $comissaoPaga == 0 ? '' : number_format($comissaoPaga,2, ',', '.'); ?>
									</div>
									<div style="float: left">
										SUB-TOTAL COMISSÃO A PAGAR:
										<?php echo $comissaoAPagar == 0 ? '' : number_format($comissaoAPagar,2, ',', '.'); ?>
									</div>
									</td>
								</tr>
								<?php 
									$comissaoPaga 			= 0;
									$comissaoAPagar 	= 0;
								?>
	                			<?php endif;?>
		                		<?php 
		                			$indicador 				= $notaFiscal->corroid; 
		                		?>
		                		
							<tr class="tableTituloColunas">
								<td colspan="16" style="padding-left: 4px;" style=" font-size: 9pt;">
									<b>Indicador Negócio:
									<?php echo $notaFiscal->corrnome; ?>
									</b>
								</td>
							</tr>
							<?php endif;?>
							<!-- Calculo de SubTotais e Totais -->
	                		<?php
	                		if ($notaFiscal->status_comissao == 'PAGA') {
	                			$comissaoPaga 		+= $notaFiscal->coinvl_comissao;
	                			$totalComissaoPaga 	+= $notaFiscal->coinvl_comissao;
	                		}
	                		else {
	                			$comissaoAPagar += $notaFiscal->coinvl_comissao != '' ? $notaFiscal->coinvl_comissao : 0;
	                			$totalComissaoAPagar += $notaFiscal->coinvl_comissao != '' ? $notaFiscal->coinvl_comissao : 0;
	                		}
	                		
	                		$zebra = $zebra == '#FFFFFF' ? '#E6EAEE' : '#FFFFFF';
	                		$valorComissao = $notaFiscal->coinvl_comissao != '' ? number_format($notaFiscal->coinvl_comissao,2, ',', '.') : '';
	                		?>
		                	<tr bgcolor="<?php echo $zebra?>">
		                		<td>
		                			<!-- Check Box Comissionáveis -->
		                			<?php if ($notaFiscal->pciitem_comissao == 't' && $notaFiscal->status_comissao != 'PAGA'):?>
		                				<input type="checkbox" id="comissionavel" name="comissionavel[]" 
		                				class="nota_indicador <?php 
		                				if ($notaFiscal->status_comissao == 'PENDENTE') echo 'nota_indicador_pendente'; 
		                				elseif ($notaFiscal->status_comissao == 'GERADA') echo 'nota_indicador_gerada';
		                				?>"
		                				value="<?php 	$valor = $notaFiscal->status_comissao;
	                									$valor .= "|";
	                									$valor .= $notaFiscal->nflno_numero.trim($notaFiscal->nflserie).$notaFiscal->connumero;
	                									$valor .= "|";
	                									$valor .= $notaFiscal->nfivl_item;
	                									$valor .= "|";
	                									$valor .= $notaFiscal->eqcoid; 
	                									$valor .= "|";
	                									$valor .= $notaFiscal->connumero; 
	                									$valor .= "|";
	                									$valor .= $notaFiscal->obroid; 
	                									$valor .= "|";
	                									$valor .= $notaFiscal->corroid; 
	                									$valor .= "|";
	                									$valor .= $notaFiscal->nflno_numero;
	                									$valor .= "|";
	                									$valor .= $notaFiscal->nflserie;
	                									$valor .= "|";
	                									$valor .= $notaFiscal->coinvl_comissao;
	                									
	                									echo $valor;
	                									
	                									?>"
	                					<?php 
	                					if (is_array($_POST['comissionavel'])) {
		                					if (in_array($valor, $_POST['comissionavel'])) {
		                						echo 'checked="checked"';
		                					}
										} ?> />
		                			<?php endIf;?>
		                		</td>
		                		<td align="center"><?php echo $notaFiscal->nfldt_faturamento; ?></td>
		                		<td align="left"><?php echo $notaFiscal->nflno_numero,' / ',$notaFiscal->nflserie;  ?></td>
		                		<td align="left"><?php echo $notaFiscal->clinome;  ?></td>
		                		<td align="left"><?php echo $notaFiscal->uf;  ?></td>
		                		<td align="left"><?php echo $notaFiscal->cidade;  ?></td>
		                		<td><?php echo $notaFiscal->connumero;  ?></td>
		                		<td><?php echo $notaFiscal->veiplaca;  ?></td>
		                		<td><?php echo $notaFiscal->obrobrigacao;  ?></td>
		                		<td align="right"><?php echo number_format($notaFiscal->nfivl_item,2, ',', '.');  ?></td>
		                		<td align="right"><?php echo $valorComissao;  ?></td> <!-- Valor comissão -->
		                		<td align="center"><?php echo $notaFiscal->coindt_cadastro;  ?></td> <!-- Dt calculo comissao -->
		                		<td align="center"><?php echo $notaFiscal->coindt_pagamento;  ?></td> <!-- Data Pagamento -->
		                		<td align="center"><?php echo $notaFiscal->titulo_vencido;  ?></td>
		                		<td align="center">
		                			<?php 
		                			switch ($notaFiscal->status_comissao){ 
		                			 		case 'PENDENTE': ?>
		                					<img alt="Pendente geração" src="images/icones/ap2/ap10.jpg">
		                				<?php 	break;
		                					case 'GERADA':?>
		                					<img alt="Comissão gerada" src="images/icones/ap2/ap8.jpg">
		                				<?php 	break;
		                					case 'PAGA':?>
		                					<img alt="Comissão paga" src="images/icones/ap2/ap1.jpg">
		                				<?php 	break;
		                					default:
		                						break;
		                			}?>
		                		</td> <!-- Situação Comissão -->
		                	</tr>
	                	<?php endwhile; ?>
	                		<tr class="tableTituloColunas">
								<td colspan="16" style="padding-left: 4px;" style=" font-size: 9pt;">
									<div style="width: 500px; float: left">
										SUB-TOTAL COMISSÃO PAGA:
										<?php echo $comissaoPaga == 0 ? '' : number_format($comissaoPaga,2, ',', '.'); ?>
									</div>
									<div style="float: left">
										SUB-TOTAL COMISSÃO A PAGAR:
										<?php echo $comissaoAPagar == 0 ? '' : number_format($comissaoAPagar,2, ',', '.'); ?>
									</div>
								</td>
							</tr>
							<tr class="tableTituloColunas">
								<td colspan="16" style="padding-left: 4px;" style=" font-size: 9pt;">
									<div style="width: 500px; float: left">
										<b>TOTAL COMISSÃO PAGAR:
										<?php echo $totalComissaoPaga == 0 ? '' : number_format($totalComissaoPaga,2, ',', '.'); ?>
										</b>
									</div>
									<div style="float: left">
										<b>TOTAL COMISSÃO A PAGAR:
										<?php echo $totalComissaoAPagar == 0 ? '' : number_format($totalComissaoAPagar,2, ',', '.'); ?>
										</b>
									</div>
								</td>
							</tr>
	                </tbody>
	                <tfoot>
	                	<tr class="tableSubTitulo">
	                    	<td colspan="18">
	                        	<label>
	                            	<input type="checkbox" id="selecionar_todas" name="selecionar_todas" value="selecionar_todas">
	                                Selecionar todas
	                            </label>
	                            &nbsp;&nbsp;&nbsp;
	                            <label>
	                            	<input type="checkbox" id="selecionar_pendentes" name="selecionar_pendentes" value="selecionar_pendentes">
	                                Selecionar somente notas pendentes de geração de comissão
	                            </label>
	                        	<span style="float: right; margin-right: 50px">
		                        	<input class="botao" type="button" id="gerarComissao" name="gerarComissao" value="Gerar Comissão">&nbsp;
		                      
		                        	<input class="botao" type="button" id="efetuarPagamento" name="efetuarPagamento" value="Efetuar Pagamento">&nbsp;
		                        
		                        	<input class="botao" type="button" id="excluirComissao" name="excluirComissao" value="Excluir Comissão">
	                        	</span>
	                        </td>
	                    </tr>
	               </tfoot>
              </table>
           </td>
         </tr>
         <?php else:?>
         <tr>
         	<td style="padding: 4px;">
         		Nenhum resultado encontrato.
         	</td>
         </tr>
         <?php endIf;?>
     </tbody>
</table>
</center>