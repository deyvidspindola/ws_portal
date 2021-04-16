<table width="98%" class="tableMoldura" id="tableResultReport">
	<?php if($acao != 'exportar'): ?>
	<tr class="tableSubTitulo">
	   	<td colspan="7"><h2>Resultado da pesquisa</h2></td>
	</tr>  
<?php endif; ?>
    <?php 
   	if(count($resultadoPesquisa) > 0):
    	foreach ($resultadoPesquisa as $rs): ?>
    	<tr <?php if($acao == 'exportar'):?>style="background-color: #bcbcbc"<?php else:?> class="tableTituloColunas" <?php endif; ?>>
    		<td width="70px">Segmento:</td>
    		<td colspan="6"><b><?php echo $rs['label']?></b></td>
    	</tr>
    	<?php 
    		foreach ($rs['uf'] as $label => $values): 
    			//var_dump($values);
    			?>
    	<tr <?php if($acao == 'exportar'):?>style="background-color: #eeeeee"<?php else:?> class="tde" <?php endif; ?>>
    		<td>&nbsp;</td>
    		<td width="50px">Estado:</td>
    		<td colspan="5"><b><?php echo $label?></b></td>
    	</tr>
    	<?php foreach ($values as $value): ?>
    	<tr>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>
    		<td>Embarcador: <b><?php echo $value['embnome']?></b></td>
    		<td colspan="3"  <?php if($acao == 'exportar'):?>align="right"<?php endif; ?>>Dt. Atualização: <?php echo $value['embdt_alteracao']?></td>
    	</tr>	
    	<tr>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>    		
    		<td width="150px" colspan="4">
    			<table>	 
    				<tr>
    					<td width="20px">&nbsp;</td>
    					<td colspan="2">Gerenciadoras:</td>
    				</tr>    				
    				<?php foreach($value['gerenciadoras'] as $gr): ?>	    				
    				<tr>
    					<td width="20px">&nbsp;</td>
    					<td width="20px">&nbsp;</td>
    					<td><b><?php echo $gr;?></b></td>
    				</tr>
    				<?php endforeach;?>
    			</table>    			
    		</td>
    	</tr>	
    	<tr>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>    		
    		<td width="150px" colspan="3">
    			<table>	 
    				<tr>
    					<td width="20px">&nbsp;</td>
    					<td colspan="2">Transportadoras:</td>                        
    				</tr>    				
    				<?php
                     foreach($value['transportadoras'] as $chave => $valor): 
                    ?>	    				
    				<tr>    					
    					<td>&nbsp;</td>  
                        <td width="20px">&nbsp;</td>  
                        <td style="padding: 2px;"><b><?php echo $valor['nome'];?></b></td>
    				</tr>
    				<?php endforeach;?>
    			</table>
    		</td>
            <td>
                <table>  
                    <tr>
                        <td width="20px">&nbsp;</td>
                        <td colspan="2">Qtd. de Veiculos:</td>
                    </tr>
                    <?php 
                        $qtdTotal = 0;
                        foreach($value['transportadoras'] as $chave => $valor): 
                            $qtdTotal += $valor['veiculos'];
                    ?>
                    <tr>                        
                        <td width="20px">&nbsp;</td>  
                        <td align="right" style="padding: 2px;"><b><?php echo $valor['veiculos'];?></b></td>                                                                      
                    </tr>
                    <?php endforeach;?>                   
                </table>
            </td> 
    	</tr>
        <tr>
            <td colspan="5">&nbsp;</td>
            <td>
                <table>
                    <tr>
                        <td width="20px">&nbsp;</td>  
                        <td style="background-color: #CECECE; padding: 2px; width: 94px;">
                            <span style="float: left;"><b>Total:</b></span> 
                            <span style="float: right;"><b><?php echo $qtdTotal;?></b></span>
                        </td>            
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="6">&nbsp;</td>
        </tr>
    	<?php endforeach; ?>
    	<?php endforeach; ?>
		<?php endforeach; ?>
		<tr class="tableRodapeModelo1">
			<td colspan="7" align="center">
				<input type="button" name="exportar" id="exportar" value="Exportar em Planilha" class="botao">
			</td>
		</tr>
    <?php
    else: ?>
    	<tr class="tableRodapeModelo3">
    		<td colspan="7" align="center"><b>Não foram encontrados registros que satisfaçam os filtros</b></td>
    	</tr>
    <?php endif; ?>
</table>
<style>
    #exportar {padding: 1px 10px;}
</style>