<?php foreach($registros as $indice => $registro) : ?>
	<tr class="<?php echo ($indice + 1) % 2 == 0 ? 'par' : 'impar' ?>">
		<td class="centro"><?php echo $registro->data; ?></td>
		<td class="direita"><?php echo number_format($registro->valor, 1, ',', ''); ?></td>
		<td class="centro" style="width: 100px;">
			<a href="cad_inpc.php?acao=alterar&data=<?php echo str_replace('/', '-', $registro->data); ?>" title="Editar"><img alt="Editar" src="images/edit.png" class="icone" /></a>
			<a href="#" title="Excluir" onclick="javascript: return excluir('<?php echo str_replace('/', '-', $registro->data); ?>');"><img alt="Excluir" src="images/icon_error.png" class="icone" /></a>
		</td>
	</tr>
<?php endforeach;?>