<?php
/**
 * @author	Gabriel Luiz Pereira
 * @email	gabriel.pereira@meta.com.br
 * @since	06/09/2012
 */

 ?>
<tr>
	<td align="center">
		<table class="tableMoldura resultado_pesquisa">
			<tr class="tableSubTitulo"><td colspan="8"><h2>Reenvio de Transação</h2></td></tr>
			<th width='85%' style='background:#A2B5CD; font-size:12px; font-family:Arial'>Status</th>
			<th width='15%' style='background:#A2B5CD; font-size:12px; font-family:Arial' align='center'>Quantidade</th>
<?php
			$i=0;
			foreach ($view as $status => $quant) { 
				$cor = ($i%2 == 0)?"#FFFFFF":"#CCCCCC"; ?>
				<tr style='font-size:12px; font-family:Arial; background-color: <?=$cor?>'><td><?=str_replace(":"," - ",$status)?></td>
				<?php foreach($quant as $key => $qtd) { ?>
					<td align='center'><?=$qtd?></td>
				<?php } ?>
				</tr>
				<?php $i++;
			} ?>
	</table>
	<p>
		<strong>Faturamento  Cart&atilde;o de Cr&eacute;dito<br />Data: </strong><?=date('d/m/Y')?> <br />
		<strong>Hora</strong>: <?=date('H:i:s')?> 
	</p>
</div>