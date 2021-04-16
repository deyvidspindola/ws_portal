<table id="resultado" class="tableMoldura resultado_pesquisa" border="1">	
	<tr class="tableSubTitulo">
		<td colspan="6">            
			<h2>Resultados da Pesquisa</h2>                
		</td>
	</tr>
	<tr class="tableTituloColunas">    
		<td align="center" width="120px"><h3>Placa Ve&iacute;culo</h3></td>
		<td align="center"><h3>Equipe</h3></td>
		<td align="center"><h3>Recupera</h3></td>
		<td align="center"><h3>Cidade Acionamento</h3></td>
		<td align="center"><h3>UF Acionamento</h3></td>
		<td align="center"><h3>Aprovado</h3></td>
	</tr>
	
	<?php 
	
	if(count($this->pronta_resposta) > 0):
	
		foreach($this->pronta_resposta as $pronta_resposta):
			$class_red = '';
			if($pronta_resposta['aprovado'] == 'f'){
				$class_red = 'red';
			}
		?>
			<tr class="result">
				<td align="center"><a href="cad_atendimento_pronta_resposta.php?acao=editar&id_atendimento=<?php echo $pronta_resposta['id_atendimento'] ?>"><?php echo $pronta_resposta['placa_veiculo'] ?></a></td>
				<td align="left" class="<?php echo $class_red ?>"><?php echo $pronta_resposta['equipe'] ?></td>
				<td align="center" class="<?php echo $class_red ?>"><?php echo utf8_encode(($pronta_resposta['is_recuperado'] == 't') ? 'SIM' : 'NÃO') ?></td>
				<td align="left" class="<?php echo $class_red ?>"><?php echo $pronta_resposta['cidade'] ?></td>
				<td align="center" class="<?php echo $class_red ?>"><?php echo $pronta_resposta['uf'] ?></td>
				<td align="center"><?php echo ($pronta_resposta['aprovado'] == 't') ? '<img src="images/icones/t1/v.png">' : '' ?></td>
			</tr>        
		<?php 
		endforeach;
		?>
		<tr class="tableTituloColunas">    
			<td colspan="6">&nbsp;</td>
		</tr>
	<?php
	else:	
	?>
	<tr class="tableTituloColunas">    
		<td colspan="6" align="center"><b>Nenhum registro encontrado.</b></td>
	</tr>
	<?php
	endif;
	?>
	
</table>    