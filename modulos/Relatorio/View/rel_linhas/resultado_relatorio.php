<!-- RESULTADO DA PESQUISA -->
<?php
	$registrosEncontrados = 0;
	if ($relatorio != false) {
		$registrosEncontrados = pg_num_rows($relatorio);
	} 
?>
<?php if ($registrosEncontrados <= 1000): ?>
<table class="tableMoldura resultado_relatorio" width="98%">
	<tbody>
		<tr class="tableTituloColunas">
			<td align="center"><h3>CID/ICCID</h3></td>
			<td align="center"><h3>Status CID/ICCID</h3></td>
			<td align="center" width="105px"><h3>Linha</h3></td>
			<td align="center"><h3>Operadora</h3></td>			
			<td align="center"><h3>Status Linha</h3></td>
			<td align="center"><h3>BAN</h3></td>
			<td align="center"><h3>Data Habilitação</h3></td>
			<td align="center"><h3>Nº Série Equipamento</h3></td>
			<td align="center"><h3>Status Equipamento</h3></td>
			<td align="center"><h3>Termo</h3></td>
			<td align="center"><h3>Status Termo</h3></td>
			<td align="center"><h3>Antena Satelital</h3></td>
			<td align="center"><h3>Plano Satelital</h3></td>
			<td align="center"><h3>Status Fornecedor Antena Satelital</h3></td>
			<td align="center"><h3>Data de Cancelamento</h3></td>
			<td align="center"><h3>DT de Alteração Status NTC</h3></td>
			<td align="center"><h3>Classe do Contrato</h3></td>
			<td align="center"><h3>Tipo do Contrato</h3></td>
			<td align="center"><h3>Nome do Cliente</h3></td>
			<td align="center"><h3>Placa</h3></td>
			<td align="center"><h3>Versão Equipamento</h3></td>
		</tr>
		<?php if ($relatorio !== null && is_resource($relatorio) && $relatorio !== false): ?>
			<?php while($row = pg_fetch_object($relatorio)):?>
			<?php 
				$zebra	= $zebra == 'tdc' ? 'tde' : 'tdc'; 
				
				$cid				= empty($row->cid) 				? '-' : $row->cid;
				$statusCid			= empty($row->status_cid) 		? '-' : $row->status_cid;
				$status				= empty($row->status)			? '-' : $row->status;
				$ban				= empty($row->ban)				? '-' : $row->ban;
				$dataHabilitacao	= empty($row->data_habilitacao)	? '-' : $row->data_habilitacao;
				$serieEquipamento	= empty($row->serie_equipamento)? '-' : $row->serie_equipamento;
				$statusEquipamento	= empty($row->status_equipamento)? '-': $row->status_equipamento;
				$contrato			= empty($row->contrato)			? '-' : $row->contrato;
				$statusContrato		= empty($row->status_contrato)	? '-' : $row->status_contrato;
				$serialAntena		= empty($row->serial_antena)	? '-' : $row->serial_antena;
				$fornecedorAntena	= empty($row->fornecedor_antena)? '-' : $row->fornecedor_antena;
				$planoAntena		= empty($row->plano)? '-' : $row->plano;
				$operadora			= empty($row->oploperadora)? '-' : $row->oploperadora;
				$dataCancelamento	= empty($row->linbloqueado)? '-' : $row->linbloqueado;
				$dataAlteracao		= empty($row->lindt_alteracaontc)? '-' : $row->lindt_alteracaontc;
				$classe_contrato 	= empty($row->classe_contrato)		? '-' : $row->classe_contrato;
				$conno_tipo 		= empty($row->conno_tipo)			? '-' : $row->conno_tipo;
				$clinome 			= empty($row->clinome)				? '-' : $row->clinome;
				$veiplaca 			= empty($row->veiplaca)				? '-' : $row->veiplaca;
				$versao_eqpto 		= empty($row->versao_eqpto)			? '-' : $row->versao_eqpto;
			?>
			<tr class="<?php echo $zebra;?>" style="font-size: 7px !important" >
				<td align="left"><?php echo $cid ?></td>
				<td align="center"><?php echo $statusCid ?></td>
				<td align="center"><?php echo formatar_fone_nono_digito($row->numero) ?></td>
				<td align="center"><?php echo $operadora ?></td>
				<td align="center"><?php echo $status ?></td>
				<td align="center"><?php echo $ban ?></td>
				<td align="center"><?php echo $dataHabilitacao ?></td>
				<td align="center"><?php echo $serieEquipamento ?></td>
				<td align="center"><?php echo $statusEquipamento ?></td>
				<td align="center"><?php echo $contrato ?></td>
				<td align="center"><?php echo $statusContrato ?></td>
				<td align="center"><?php echo $serialAntena ?></td>
				<td align="center"><?php echo $planoAntena ?></td>
				<td align="center"><?php echo $fornecedorAntena ?></td>
				<td align="center"><?php echo $dataCancelamento ?></td>
				<td align="center"><?php echo $dataAlteracao ?></td>
				<td align="center"><?php echo $classe_contrato ?></td>
				<td align="center"><?php echo $conno_tipo ?></td>
				<td align="center"><?php echo $clinome ?></td>
				<td align="center"><?php echo $veiplaca ?></td>
				<td align="center"><?php echo $versao_eqpto ?></td>
			</tr>
			<?php endwhile;?>
			<tr class="tableRodapeModelo1">
				<td align="center" colspan="21">
					<span style="line-height: 1.9em; margin-right: 20px"><b><?php echo $registrosEncontrados ?> Registros encontrados</b></span> 
				</td>
			</tr>
			<tr>
				<td><br/></td>
			</tr>
			<tr class="tableRodapeModelo1">
				<td align="center" colspan="21">
					<input class="botao" type="button" value="Exportar" name="bt_exportar" />
				</td>
			</tr>

		<?php else:?>
		<!-- Sem resultado na consulta -->
		<tr>
			<td align="center" class="tableRodapeModelo3" colspan="24">
				<b>Nenhum resultado encontrado</b>
			</td>
		</tr>
		<?php endif;?>
	</tbody>
</table>
<?php else: ?>
<table class="tableMoldura resultado_relatorio" width="98%">
	<thead>
		<tr class="tableSubTitulo">
			<td colspan="19">
				<h3>Download</h3>
			</td>
		</tr>
	</thead>
	<tbody>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td align="center">
				<a href="downloads.php?arquivo=<?php echo $arquivoPlanilha; ?>" target="_blank">
					<div>
						<img alt="Download" src="images/icones/t3/caixa2.jpg"><br/>
						Relatório de linhas
					</div>
				</a>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
	</tbody>
</table>
<?php endif;?>

<!-- LOG DE LINHA NÃO ENCONTRADAS -->
<?php if (!empty($arquivoLog)): ?>
<table class="tableMoldura resultado_relatorio" width="98%">
	<thead>
		<tr class="tableSubTitulo">
			<td colspan="24">
				<h3>Download</h3>
			</td>
		</tr>
	</thead>
	<tbody>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td align="center">
				<a href="downloads.php?arquivo=<?php echo $arquivoLog?>" target="_blank">
					<div>
						<img alt="Download" src="images/icones/t3/caixa2.jpg"><br/>
						Baixar arquivo de log
					</div>
				</a>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
	</tbody>
</table>
<?php endif; ?>