<!-- RESULTADO DA PESQUISA - SINTÉTICO -->
<table class="tableMoldura" width="98%" >
	<thead>
		<tr class="tableSubTitulo">
			<td colspan="19">
				<h2>Resultado da Pesquisa</h2> 
			</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td style="padding: 0" align="center">
				<br />
				<?php $zebra = 'tde';?>
				<!-- OS POR STATUS -->
				<table class="tableMoldura" width="100%" >
					<tbody>
						<tr class="tableTituloColunas titulo_sintetico">
							<td width="70%"><h3>O.S. Geradas por Status</h3></td>
							<td><h3>Quantidade</h3></td>
						</tr>
						<?php if ($totalPorStatus > 0): ?>
							<?php foreach ($grupoPorStatus as $status):?>
							<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'?>
							<tr class="<?php echo $zebra;?>">
								<td><?php echo $status['nome']?></td>
								<td><?php echo $status['quantidade']?></td>
							</tr>
							<?php endforeach;?>
							<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'?>
							<tr class="<?php echo $zebra;?>">
								<td><b>Total</b></td>
								<td><b><?php echo $totalPorStatus;?></b></td>
							</tr>
						<?php else:?>
							<tr class="<?php echo $zebra;?>">
								<td><b>Total</b></td>
								<td><b>0</b></td>
							</tr>
						<?php endif;?>
					</tbody>
				</table>
				
				<?php $zebra = 'tde';?>
				<!-- OS POR CLASSE DE EQUIPAMENTO -->
				<table class="tableMoldura" width="100%" >
					<tbody>
						<tr class="tableTituloColunas titulo_sintetico">
							<td width="70%"><h3>O.S. Geradas por Classe de Equipamento</h3></td>
							<td><h3>Quantidade</h3></td>
						</tr>
						<?php if ($totalPorClasseEquipamento > 0): ?>
							<?php foreach ($grupoPorClasseEquipamento as $classeEquipamento):?>
							<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'?>
							<tr class="<?php echo $zebra;?>">
								<td><?php echo $classeEquipamento['nome']?></td>
								<td><?php echo $classeEquipamento['quantidade']?></td>
							</tr>
							<?php endforeach;?>
							<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'?>
							<tr class="<?php echo $zebra;?>">
								<td><b>Total</b></td>
								<td><b><?php echo $totalPorClasseEquipamento;?></b></td>
							</tr>
						<?php else:?>
							<tr class="<?php echo $zebra;?>">
								<td><b>Total</b></td>
								<td><b>0</b></td>
							</tr>
						<?php endif;?>
					</tbody>
				</table>
				
				<?php $zebra = 'tde';?>
				<!-- OS POR VERSÃO DE EQUIPAMENTO -->
				<table class="tableMoldura" width="100%" >
					<tbody>
						<tr class="tableTituloColunas titulo_sintetico">
							<td width="70%"><h3>O.S. Geradas por Versão de Equipamento</h3></td>
							<td><h3>Quantidade</h3></td>
						</tr>
						<?php if ($totalPorVersaoEquipamento > 0): ?>
							<?php foreach ($grupoPorVersaoEquipamento as $versaoEquipamento):?>
							<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'?>
							<tr class="<?php echo $zebra;?>">
								<td><?php echo $versaoEquipamento['nome']?></td>
								<td><?php echo $versaoEquipamento['quantidade']?></td>
							</tr>
							<?php endforeach;?>
							<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'?>
							<tr class="<?php echo $zebra;?>">
								<td><b>Total</b></td>
								<td><b><?php echo $totalPorVersaoEquipamento;?></b></td>
							</tr>
						<?php else:?>
							<tr class="<?php echo $zebra;?>">
								<td><b>Total</b></td>
								<td><b>0</b></td>
							</tr>
						<?php endif;?>
					</tbody>
				</table>
				
				<!-- OS POR DEFEITO ALEGADO -->
				<table class="tableMoldura" width="100%" >
					<tbody>
						<tr class="tableTituloColunas titulo_sintetico">
							<td width="70%"><h3>O.S. Geradas por Defeito Alegado</h3></td>
							<td><h3>Quantidade</h3></td>
						</tr>
						<?php if ($totalPorDefeitoAlegado > 0): ?>
							<?php foreach ($grupoPorDefeitoAlegado as $defeitoAlegado):?>
							<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'?>
							<tr class="<?php echo $zebra;?>">
								<td><?php echo $defeitoAlegado['nome']?></td>
								<td><?php echo $defeitoAlegado['quantidade']?></td>
							</tr>
							<?php endforeach;?>
							<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'?>
							<tr class="<?php echo $zebra;?>">
								<td><b>Total</b></td>
								<td><b><?php echo $totalPorDefeitoAlegado;?></b></td>
							</tr>
						<?php else:?>
							<tr class="<?php echo $zebra;?>">
								<td><b>Total</b></td>
								<td><b>0</b></td>
							</tr>
						<?php endif;?>
					</tbody>
				</table>
				
				<!-- OS POR DEFEITO CONSTATADO -->
				<table class="tableMoldura" width="100%" >
					<tbody>
						<tr class="tableTituloColunas titulo_sintetico">
							<td width="70%"><h3>O.S. Geradas por Defeito Constatado</h3></td>
							<td><h3>Quantidade</h3></td>
						</tr>
						<?php if ($totalPorDefeitoConstatado > 0): ?>
							<?php foreach ($grupoPorDefeitoConstatado as $defeitoConstatado):?>
							<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'?>
							<tr class="<?php echo $zebra;?>">
								<td><?php echo $defeitoConstatado['nome']?></td>
								<td><?php echo $defeitoConstatado['quantidade']?></td>
							</tr>
							<?php endforeach;?>
							<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'?>
							<tr class="<?php echo $zebra;?>">
								<td><b>Total</b></td>
								<td><b><?php echo $totalPorDefeitoConstatado;?></b></td>
							</tr>
						<?php else:?>
							<tr class="<?php echo $zebra;?>">
								<td><b>Total</b></td>
								<td><b>0</b></td>
							</tr>
						<?php endif;?>
					</tbody>
				</table>
				
				
				<!-- OS POR TIPO -->
				<table class="tableMoldura" width="100%" >
					<tbody>
						<tr class="tableTituloColunas titulo_sintetico">
							<td width="70%"><h3>O.S. Geradas por Tipo</h3></td>
							<td><h3>Quantidade</h3></td>
						</tr>
						<?php if ($totalPorTipo > 0): ?>
							<?php foreach ($grupoPorTipo as $tipo):?>
							<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'?>
							<tr class="<?php echo $zebra;?>">
								<td><?php echo $tipo['nome']?></td>
								<td><?php echo $tipo['quantidade']?></td>
							</tr>
							<?php endforeach;?>
							<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'?>
							<tr class="<?php echo $zebra;?>">
								<td><b>Total</b></td>
								<td><b><?php echo $totalPorTipo;?></b></td>
							</tr>
						<?php else:?>
							<tr class="<?php echo $zebra;?>">
								<td><b>Total</b></td>
								<td><b>0</b></td>
							</tr>
						<?php endif;?>
					</tbody>
				</table>
				
				<!-- OS POR MOTIVO -->
				<table class="tableMoldura" width="100%" >
					<tbody>
						<tr class="tableTituloColunas titulo_sintetico">
							<td width="70%"><h3>O.S. Geradas por Motivo</h3></td>
							<td><h3>Quantidade</h3></td>
						</tr>
						<?php if ($totalPorMotivo > 0): ?>
							<?php foreach ($grupoPorMotivo as $motivo):?>
							<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'?>
							<tr class="<?php echo $zebra;?>">
								<td><?php echo $motivo['nome']?></td>
								<td><?php echo $motivo['quantidade']?></td>
							</tr>
							<?php endforeach;?>
							<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'?>
							<tr class="<?php echo $zebra;?>">
								<td><b>Total</b></td>
								<td><b><?php echo $totalPorMotivo;?></b></td>
							</tr>
						<?php else:?>
							<tr class="<?php echo $zebra;?>">
								<td><b>Total</b></td>
								<td><b>0</b></td>
							</tr>
						<?php endif;?>
					</tbody>
				</table>
				
				<!-- OS POR TIPO DE SOLICITAÇÃO  -->
				<table class="tableMoldura" width="100%" >
					<tbody>
						<tr class="tableTituloColunas titulo_sintetico">
							<td width="70%"><h3>O.S. Geradas por Tipo de Solicitação</h3></td>
							<td><h3>Quantidade</h3></td>
						</tr>
						<?php if ($totalPorTipoSolicitacao > 0): ?>
							<?php foreach ($grupoPorTipoSolicitacao as $tipoSolicitacao):?>
							<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'?>
							<tr class="<?php echo $zebra;?>">
								<td><?php echo $tipoSolicitacao['nome']?></td>
								<td><?php echo $tipoSolicitacao['quantidade']?></td>
							</tr>
							<?php endforeach;?>
							<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'?>
							<tr class="<?php echo $zebra;?>">
								<td><b>Total</b></td>
								<td><b><?php echo $totalPorTipoSolicitacao;?></b></td>
							</tr>
						<?php else:?>
							<tr class="<?php echo $zebra;?>">
								<td><b>Total</b></td>
								<td><b>0</b></td>
							</tr>
						<?php endif;?>
					</tbody>
				</table>
			</td>
		</tr>
		
	</tbody>
</table>