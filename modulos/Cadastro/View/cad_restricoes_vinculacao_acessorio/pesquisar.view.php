<?php
/**
 * @author	Emanuel Pires Ferreira
 * @email	epferreira@brq.com
 * @since	15/02/2013
 */
?>
<tr>
	<td align="center">
		<table class="tableMoldura resultado_pesquisa">
			<tr class="tableSubTitulo"><td colspan="8"><h2>Resultado da pesquisa</h2></td></tr>
			<tr class="tableTituloColunas tab_registro">
				<td align="center"><h3>Projeto</h3></td>
				<td align="center"><h3>Versão</h3></td>
				<td align="center"><h3>Classe</h3></td>
				<td align="center"><h3>Acessório (Produto)</h3></td>
				<td align="center"><h3>Data Cadastro</h3></td>
				<td align="center"><h3>Usuário Cadastro</h3></td>
				<td align="center"></td>
			</tr>
			<?php if(count($view['results']) > 0 ) { ?>
			
				<?php foreach($view['results'] as $linha) { ?>
					<tr class="tr_resultado_ajax" id="restricao<?=$linha['ravoid']?>">
						<td><?=$linha['eprnome']?></td>
                        <td><?=$linha['eveversao']?></td>
                        <td><?=$linha['eqcdescricao']?></td>
                        <td><?=$linha['prdproduto']?></td>
                        <td align="center"><?=$linha['dt_cadastro']?></td>
                        <td align="center"><?=$linha['nm_usuario']?></td>
						<td align="center"><a href="javascript:void(0);" class="excluiRestricao" ravoid="<?=$linha['ravoid']?>"><img src="images/icones/t2/x.jpg" /></a></td>
					</tr>
					
				<?php } //endforeach; ?>
			
				<!--tr class="tableRodapeModelo3">
					<td align="center" colspan="8" id="total_registros"><?=$view['total_registros']?></td>
				</tr-->
			<?php } else { ?>
				<tr class="tableRodapeModelo3">
					<td align="center" colspan="8" id="total_registros">Nenhum registro encontrado!</td>
				</tr>
			<?php } //endif; ?>
		</table>
	</td>
</tr>
