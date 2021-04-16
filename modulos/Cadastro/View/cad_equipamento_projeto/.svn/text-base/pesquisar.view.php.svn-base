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
				<td align="center"><h3>ID</h3></td>
				<td align="center"><h3>Projeto</h3></td>
				<td align="center"><h3>Tipo Portal</h3></td>
				<td align="center"><h3>Teste Portal</h3></td>
				<td align="center"><h3>Quadriband</h3></td>
				<td align="center"><h3>Data Cadastro</h3></td>
				<td align="center"><h3>Usuário Cadastro</h3></td>
				<td align="center"><h3>Editar</h3></td>
			</tr>
			<?php if(count($view['equipamentos']) > 0 ) { ?>
			
				<?php foreach($view['equipamentos'] as $linha) { ?>
					<tr class="tr_resultado_ajax">
					    <td align="center"><?=$linha['eproid']?></td>
						<td><?=$linha['eprnome']?></td>
                        <td align="center"><?=($linha['eprtipo']!="")?($linha['eprtipo'] == "CG")?"Carga":"Casco":""?></td>
                        <td align="center"><?=($linha['eprteste_portal']=='t')?"Sim":"Não"?></td>
						<td align="center"><?=($linha['eprquadriband']=='t')?"Sim":"Não"?></td>
						<?php $arrData = explode(" ",$linha['eprcadastro']);?>
						<td align="center"><?=implode("/",array_reverse(explode("-",$arrData[0])))?></td>
						<td><?=$linha['nm_usuario']?></td>
						<td align="center">
                            <form action="" method="post" id="editaEquipamento<?=$linha['eproid']?>">
                                <input type="hidden" name="acao" id="acao" value="editar" />
                                <input type="hidden" name="eproid" id="eproid" value="<?=$linha['eproid']?>" />
                                <a href="javascript:void(0);" onclick="$('#editaEquipamento<?=$linha['eproid']?>').submit();"><img src="images/icones/t2/file.jpg" /></a>
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
