<table class="tableMoldura" id="tabela_equipamento_projeto" style="width:100%;height:100%;margin:0">		                                
    <tr class="tableTituloColunas" id="cabecalho_tabela_equipamento_projeto">
    	<td width="100px" align="center"><h3>Excluir</h3></td>
    	<td><h3>Projetos Cadastrados</h3></td>
    </tr>
    <? 
    	$i = 0;
    	foreach ($equipamento_restricao as $eproid => $eprnome) { 
    		$classe =  ($i % 2 == 0) ? "tdc" : "tde";
    ?>
    <tr class="<?=$classe?> equipamento_restricao">
    	<td align="center" class="equipamento_restricao_td"><input type='checkbox' name='equipamento_restricao[]' class="equipamento_restricao_chk" value='<?=$eproid?>'></td>
    	<td><?=$eprnome?></td>
    </tr>
    <?
    		$i++;
		} 
	?>
	<tr class="tableTituloColunas">
    	<td colspan="2" align="center"><input type="button" value="Excluir" class="botao" style="padding:3px 30px" onclick="delRestricaoEquipamentoProjeto(xajax.getFormValues('form'))"></td>
    </tr>
</table>