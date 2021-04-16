<!-- RESULTADO DA PESQUISA - ANALÍTICO -->
<table class="tableMoldura" width="98%" >
	<thead>
		<tr class="tableSubTitulo">
			<td colspan="19">
				<h2>Resultado da Pesquisa</h2> 
			</td>
		</tr>
	</thead>
	<tbody>
		<col width="20px" />
	    <col width="30px" />
	    <col width="40px" />
	    <col width="20px" />
	    <col width="20px" />
	    <col width="20px" />
	    <col width="20px" />
	    <col width="20px" />
	    <col width="20px" />
	    <col width="20px" />
	    <col width="20px" />
	    <col width="20px" />
	    <col width="20px" />
	    <col width="20px" />
	    <col width="20px" />
	    <col width="20px" />
		<tr class="tableTituloColunas">
			<td colspan="9">&nbsp;</td>
			<td align="center" colspan="2"><h3>Contrato</h3></td>
			<td align="center" colspan="2"><h3>Equipamento</h3></td>
			<td align="center" colspan="2"><h3>Defeito</h3></td>
			<td>&nbsp;</td>
			<td align="center" colspan="2"><h3>Responsável</h3></td>
			<td>&nbsp;</td>
		</tr>
		<tr class="tableTituloColunas">
			<td><h3>Data</h3></td>
			<td><h3>Nº Ordem</h3></td>
			<td><h3>Cliente</h3></td>
			<td><h3>Placa</h3></td>
			<td><h3>Recorrência</h3></td>
			<td><h3>Status</h3></td>
			<td><h3>Item</h3></td>
			<td><h3>Tipo</h3></td>
			<td><h3>Motivo</h3></td>
			<td><h3>Tipo</h3></td>
			<td><h3>Classe</h3></td>
			<td><h3>Modelo</h3></td>
			<td><h3>Versão</h3></td>
			<td><h3>Alegado</h3></td>
			<td><h3>Constatado</h3></td>
			<td><h3>Abertura</h3></td>
			<td><h3>Autorização</h3></td>
			<td><h3>Cancelamento</h3></td>
			<td><h3>Conclusão</h3></td>
		</tr>
		
		<?php $ordem_servico = ""; ?>
		<?php $os_total      = 0; ?>
		<?php if ($relatorio !== null && is_resource($relatorio) && $relatorio !== false): ?>
			<?php $zebra = ''; ?>
			<?php while($row = pg_fetch_object($relatorio)):?>
			<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'; ?>
			<tr class="<?php echo $zebra;?>">
				<?php if($ordem_servico == $row->ordem_servico) : ?>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				<?php else : ?>
					<?php $ordem_servico = $row->ordem_servico; ?>
					<?php $os_total++; ?>
					<td><?php echo $row->data;?></td>
					<td><a href="javascript: void(0);" onclick="javascript: window.open('prn_ordem_servico.php?ESTADO=cadastrando&acao=editar&ordoid=<?php echo $row->ordem_servico;?>#div_1');"><?php echo $row->ordem_servico;?></a></td>
					<td><?php echo $row->cliente;?></td>
					<td><?php echo $row->placa;?></td>
				<?php endif; ?>
				<td><?php echo ($row->status == 'Cancelado') ? '' : $row->recorrencia;?></td>
				<td><?php echo $row->status;?></td>
				<td><?php echo $row->item;?></td>
				<td><?php echo $row->tipo;?></td>
				<td><?php echo $row->motivo;?></td>
				<td><?php echo $row->tipo_contrato;?></td>
				<td><?php echo $row->classe_contrato;?></td>
				<td><?php echo $row->modelo;?></td>
				<td><?php echo $row->versao;?></td>
				<td><?php echo $row->defeito_alegado;?></td>
				<td><?php echo $row->defeito_constatado;?></td>
				<td><?php echo $row->responsavel_abertura;?></td>
				<td><?php echo $row->responsavel_autorizacao;?></td>
				<td><?php echo $row->responsavel_cancelamento;?></td>
				<td><?php echo $row->responsavel_conclusao;?></td>
			</tr>
			<?php endwhile;?>
			<tr>
			<tr>
				<td align="center" class="tableRodapeModelo3" colspan="19">
					<b>Total de Registros: <?php echo /* $os_total */ pg_num_rows($relatorio); ?></b>
				</td>
			</tr>
		</tr>
		<?php else:?>
		<!-- Sem resultado na consulta -->
		<tr>
			<td align="center" class="tableRodapeModelo3" colspan="19">
				<b>Nenhum resultado encontrado</b>
			</td>
		</tr>
		<?php endif;?>
	</tbody>
</table>