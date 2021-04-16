<?php
/**
 * @author	Emanuel Pires Ferreira
 * @email	epferreira@brq.com
 * @since	13/03/2013
 */
?>
<tr>
	<td align="center">
		<?php if(count($view['results']) > 0 ) { ?>
			<?php foreach($view['results'] as $empresa => $linha) { ?>
			    <table class="tableMoldura resultado_pesquisa">
				    <tr class="tableSubTitulo"><td colspan="10"><h2><?=$empresa?></h2></td></tr>
				    <tr class="tableTituloColunas tab_registro">
                        <td align="center"><h3>Codigo</h3></td>
                        <td align="center"><h3>Descrição</h3></td>
                        <td align="center"><h3>Convênio</h3></td>
                        <td align="center"><h3>Conta Contábil</h3></td>
                        <td align="center"><h3>Limite</h3></td>
                        <td align="center"><h3>Agência</h3></td>
                        <td align="center"><h3>Conta Corrente</h3></td>
                        <td align="center"><h3>Tipo</h3></td>
                        <td align="center"><h3>Agência Convênio</h3></td>
                        <td align="center"><h3>Conta Corrente Convênio</h3></td>
                    </tr>
                    <?php $qtd = 0;?>
				    <?php foreach($linha as $row) { ?>
    					<tr class="tr_resultado_ajax">
    						<td>
    						    <form action="" method="post" id="editaBanco<?=$row['cfbbanco']?>">
                                    <input type="hidden" name="acao" id="acao" value="editar" />
                                    <input type="hidden" name="cfbbanco" id="cfbbanco" value="<?=$row['cfbbanco']?>" />
                                    <a href="javascript:void(0);" onclick="$('#editaBanco<?=$row['cfbbanco']?>').submit();"><?=$row['cfbbanco']?></a>
                                </form>
						    </td>
                            <td><?=$row['cfbnome']?></td>
                            <td><?=$row['cfbconvenio']?></td>
                            <td><?=$row['plcdescricao']?></td>
                            <td align="right">R$ <?=number_format($row['cfblimite'],2,",",".")?></td>
    						<td><?=$row['cfbagencia']?></td>
                            <td><?=$row['cfbconta_corrente']?></td>
                            <td><?=$row['cfbtipo']?></td>
                            <td><?=$row['cfbagencia_convenio']?></td>
                            <td><?=$row['cfbconta_corrente_convenio']?></td>
    					</tr>
    					<?php $qtd++;?>
					<?php } ?>
					<tr class="tableRodapeModelo3">
                        <td align="center" colspan="10" class="total_registros">A pesquisa retornou <?=$qtd?> registro(s).</td>
                    </tr>
				</table>
			<?php } //endforeach; ?>
			
		<?php } else { ?>
			<tr class="tableRodapeModelo3">
				<td align="center" colspan="10" id="total_registros">Nenhum registro encontrado!</td>
			</tr>
		<?php } //endif; ?>
	</td>
</tr>
