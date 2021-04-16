<?php
/**
 * @author	Emanuel Pires Ferreira
 * @email	epferreira@brq.com
 * @since	11/01/2013
 */
?>
<tr>
	<td align="center">
		<table class="tableMoldura resultado_pesquisa">
			<tr class="tableSubTitulo"><td colspan="8"><h2>Resultado da pesquisa</h2></td></tr>
			<tr class="tableTituloColunas tab_registro">
				<td align="center"><h3>Grupo</h3></td>
				<td align="center"><h3>Projeto Equipamento</h3></td>
				<td align="center"><h3>Classe Equipamento</h3></td>
				<td align="center"><h3>Versão Equipamento</h3></td>
				<td align="center"><h3>Comando</h3></td>
				<td align="center"><h3>Editar</h3></td>
			</tr>
			<?php if(count($view['results']) > 0 ) { ?>
			
				<?php foreach($view['results'] as $linha) { ?>
					<tr class="tr_resultado_ajax">
						<td><?=$linha['eptcfdescricao']?></td>
                        <td><?=$linha['eprnome']?></td>
                        <td><?=$linha['eqcdescricao']?></td>
                        <td><?=$linha['eveversao']?></td>
                        <td><?=$linha['cmdcomando']?></td>
						<td align="center">
                            <form action="" method="post" id="editaConfiguracao<?=$linha['eptcfoid']?>">
                                <input type="hidden" name="acao" id="acao" value="editar" />
                                <input type="hidden" name="eptcfoid" id="eptcfoid" value="<?=$linha['eptcfoid']?>" />
                                <a href="javascript:void(0);" onclick="$('#editaConfiguracao<?=$linha['eptcfoid']?>').submit();"><img src="images/icones/t2/file.jpg" /></a>
                            </form>
                        </td>
					</tr>
					
				<?php } //endforeach; ?>
			
				<tr class="tableRodapeModelo3">
					<td align="center" colspan="8" id="total_registros"><?=$view['total_registros']?></td>
				</tr>
			<?php } else { ?>
				<tr class="tableRodapeModelo3">
					<td align="center" colspan="8" id="total_registros">Nenhum registro encontrado!</td>
				</tr>
			<?php } //endif; ?>
		</table>
	</td>
</tr>
