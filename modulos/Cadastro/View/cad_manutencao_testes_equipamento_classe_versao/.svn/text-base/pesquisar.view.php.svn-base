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
				<td align="center"><h3>Editar</h3></td>
				<td align="center"><h3>Equipamento</h3></td>
				<td align="center"><h3>Classe</h3></td>
				<td align="center"><h3>Versão</h3></td>
				<td align="center"><h3>Excluir</h3></td>
			</tr>
			<?php if(count($view['equipamentos']) > 0 ) { ?>
			
				<?php foreach($view['equipamentos'] as $linha) { ?>
					<tr class="tr_resultado_ajax">
						<td align="center">
						    <form action="" method="post" id="editaTeste<?=$linha['epcvoid']?>">
						        <input type="hidden" name="acao" id="acao" value="editar" />
						        <input type="hidden" name="epcvoid" id="epcvoid" value="<?=$linha['epcvoid']?>" />
						        <a href="javascript:void(0);" onclick="$('#editaTeste<?=$linha['epcvoid']?>').submit();"><img src="images/icones/t2/file.jpg" /></a>
						    </form>
					    </td>
						<td><?=$linha['eprnome']?></td>
						<td><?=$linha['eqcdescricao']?></td>
						<td><?=$linha['eveversao']?></td>
						<td align="center"><a href="javascript:void(0);" class="excluiTeste" epcvoid="<?=$linha['epcvoid']?>" pesquisa="true"><img src="images/icones/t2/x.jpg" /></a></td>
					</tr>
					
				<?php } //endforeach; ?>
			
				<tr class="tableRodapeModelo3">
					<td align="center" colspan="5" id="total_registros"><?=$view['total_registros']?></td>
				</tr>
			<?php } else { ?>
				<tr class="tableRodapeModelo3">
					<td align="center" colspan="5" id="total_registros">Nenhum registro encontrado!</td>
				</tr>
			<?php } //endif; ?>
		</table>
	</td>
</tr>


<form id="excluirTesteTelaEditar" method="post">
    <input type="hidden" name="mensagem" id="mensagem" value="" /> 
</form>
