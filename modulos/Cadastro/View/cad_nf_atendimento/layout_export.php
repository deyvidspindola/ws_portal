<table border="1">
	<tr>
		<th align="center"><b>Período Faturado</b></th>
		<th align="center"><b>Valor Fixo</b></th>
		<th align="center"><b>Valor Por Unidade Recuperada</b></th>
		<th align="center"><b>Quantidade Recuparada</b></th>
		<th align="center"><b>Total Recuperado</b></th>
		<th align="center"><b>Valor Por Unidade Não Recuperada</b></th>
		<th align="center"><b>Quantidade Não Recuparada</b></th>
		<th align="center"><b>Total Não Recuperado</b></th>
		<th align="center"><b>Valor Variável</b></th>
		<th align="center"><b>Quantidade De Acionamentos Excedentes</b></th>
		<th align="center"><b>Valor Por Unidade Excedente</b></th>
		<th align="center"><b>Total Fatura</b></th>
                <th align="center"><b>Previsão De Pagamento</b></th>
	</tr>
	
	<?php 
	
	foreach($this->valores as $valor_resultado):
	?>
	<tr>
		<td align="center"><?php echo $valor_resultado['periodo'] ?></td>
		<td align="center"><?php echo $valor_resultado['valor_fixo'] ?></td>
		<td align="center"><?php echo $valor_resultado['valor_unidade_recuperada'] ?></td>
		<td align="center"><?php echo $valor_resultado['quantidade_recuperada'] ?></td>
		<td align="center"><?php echo $valor_resultado['total_recuperado'] ?></td>
		<td align="center"><?php echo $valor_resultado['valor_unidade_nao_recuperada'] ?></td>
		<td align="center"><?php echo $valor_resultado['quantidade_nao_recuperada'] ?></td>
		<td align="center"><?php echo $valor_resultado['total_nao_recuperado'] ?></td>
		<td align="center"><?php echo $valor_resultado['valor_variavel'] ?></td>
		<td align="center"><?php echo $valor_resultado['quantidade_acionamentos_excedentes'] ?></td>
		<td align="center"><?php echo $valor_resultado['valor_unidade_excedentes'] ?></td>
		<td align="center"><?php echo $valor_resultado['total_fatura'] ?></td>
                <td align="center"><?php echo $valor_resultado['previsao_pagamento'] ?></td>
	</tr>	
	<?php 
	endforeach;
	?>		
</table>
<br />
<table border="1">
	<tr>
		<th align="center"><b>Data Acionamento</b></th>
		<th align="center"><b>Veículo</b></th>
		<th align="center"><b>Aprovar</b></th>
	</tr>
	
	<?php 
		
	if(count($this->acionamentos) > 0):

		foreach($this->acionamentos as $acionamento):
		?>
		<tr>
			<td align="center"><?php echo $acionamento['data_acionamento'] ?></td>
			<td align="center"><?php echo $acionamento['veiculo'] ?></td>
			<td align="center"><?php echo $acionamento['aprovar'] ?></td>
		</tr>	
		<?php 
		endforeach;
		
	endif;
	?>
</table>