<?php include_once '_header.php';?>
<head>
<style type="text/css">
.exclui_item {
	background-image: url('images/del.gif');
	background-repeat: no-repeat !important;
	width: 15px !important;
	height: 15px !important;
	border: none;
}
</style>
<!-- script type="text/javascript" src="modulos/web/js/fin_fat_manual_editar.js"></script -->
</head>

<div class="mensagem sucesso" id="msgsucesso">Nota Fiscal gerada com
	sucesso!</div>

<div class="modulo_titulo">Faturamento Manual</div>
<div class="modulo_conteudo">

	<div class="listagem">

		<div class="bloco_titulo">Dados da nota fiscal.</div>
		<div class="bloco_conteudo">
					<?php foreach($listNotas as $nota): ?>	
					<div class="listagem">
				<table>
					<!-- thead>
								<tr>
									<th colspan="2" style="width: 50%">Dados da Nota Fiscal de Monitoramento</th>
								</tr>
							</thead -->
					<tbody>
						<tr>
							<td>Número:</td>
							<td><?=$nota['nflno_numero']?></td>
						</tr>
						<tr class="par">
							<td>Série:</td>
							<td><?=$nota['nflserie']?></td>
						</tr>
						<tr>
							<td>Natureza:</td>
							<td><?=$nota['nflnatureza']?></td>
						</tr>
						<tr class="par">
							<td>Transporte:</td>
							<td><?=$nota['nfltransporte']?></td>
						</tr>
						<tr>
							<td>Emissão:</td>
							<td><?=$nota['nfldt_emissao']?></td>
						</tr>
						<tr class="par">
							<td>Vencimento:</td>
							<td><?=$nota['nfldt_vencimento']?></td>
						</tr>
						<tr>
							<td>Nome do Cliente:</td>
							<td><?=$this->vo->cliente['clinome']?></td>
						</tr>
						<tr class="par">
							<td>Fone:</td>
							<td>
									<?php
						$fones = array ();
						if (trim ( $this->vo->cliente ['clifone_res'] ) != "")
							$fones [] = "Res.: " . $this->vo->cliente ['clifone_res'];
						if (trim ( $this->vo->cliente ['clifone_com'] ) != "")
							$fones [] = "Com.: " . $this->vo->cliente ['clifone_com'];
						if (trim ( $this->vo->cliente ['clifone_cel'] ) != "")
							$fones [] = "Cel.: " . $this->vo->cliente ['clifone_cel'];
						
						echo implode ( " / ", $fones );
						?></td>
						</tr>
						<tr>
							<td>Valor Nota:</td>
							<td><?=number_format($nota['nflvl_total'],2,",",".")?></td>
						</tr>
						<tr class="par">
							<td>Valor Desconto:</td>
							<td><?=number_format($nota['nflvl_desconto'],2,",",".")?></td>
						</tr>
						<tr>
							<td>Valor IR:</td>
							<td><?=number_format($nota['nflvlr_ir'],2,",",".")?></td>
						</tr>
						<tr class="par">
							<td>Valor ISS:</td>
							<td><?=number_format($nota['nflvlr_iss'],2,",",".")?></td>
						</tr>
						<tr>
							<td>Valor PIS:</td>
							<td><?=number_format($nota['nflvlr_pis'],2,",",".")?></td>
						</tr>
						<tr class="par">
							<td>Valor COFINS:</td>
							<td><?=number_format($nota['nflvlr_cofins'],2,",",".")?></td>
						</tr>
						<tr>
							<td>Valor CSLL:</td>
							<td><?=number_format($nota['nflvlr_csll'],2,",",".")?></td>
						</tr>
						<tr class="par">
							<td>Informações Complementares na NF-e:</td><td><?=$nota['nflinfcomp'];?></td>
						</tr>
					</tbody>
				</table>
			</div>	 
					<?php endforeach; ?> 
			</div>

	</div>

	<div class="separador"></div>

	<div class="listagem">

		<div class="bloco_titulo">Itens da nota fiscal</div>
		<div class="bloco_conteudo">
			<div class="listagem">
				<table>
					<thead>
						<tr>
							<th style="text-align: center;">Contrato</th>
							<th style="text-align: center;">Tp. Contrato</th>
							<th style="text-align: center;">Obrigação Financeira</th>
							<th style="text-align: center;">Tp. Item</th>
							<th style="text-align: center;">Valor Unit.</th>
							<th style="text-align: center;">Desconto</th>
							<th style="text-align: center;">Valor Total</th>
						</tr>
					</thead>

					<tbody>
							<?php foreach ($this->itensNota as  $item) : ?>
							<?php $class = $class == 'par' ? 'impar' : 'par'; ?>
							<tr class="<?php echo $class ?>">
							<td><?php echo $item['connumero']?></td>
							<td><?php echo $item['tpcdescricao']?></td>
							<td><?php echo $item['obrobrigacao']?></td>
							<td><?php echo ($item['nfitipo']== "L" ? "Locação" :  ($item['nfitipo']=="M" ? "Monit./Serviços" : "") ) ?></td>
							<td class="direita">
									<?php
								if (isset ( $item ['item_desconto'] ) && $item ['item_desconto']) {
									echo ' - ' . number_format ( $item ['desconto_aplicado'], 2, ",", "." );
								} else {
									echo number_format ( $item ['nfivl_item'], 2, ",", "." );
								}
								
								?>
								</td>
							<td class="direita"><?=number_format($item['nfidesconto'],2,",",".")?></td>
							<td class="direita">
									<?php
								if (isset ( $item ['item_desconto'] ) && $item ['item_desconto']) {
									echo ' - ' . number_format ( $item ['desconto_aplicado'], 2, ",", "." );
								} else {
									echo number_format ( $item ['nfivl_item'] - $item ['nfidesconto'], 2, ",", "." );
								}
								
								?>
								</td>
						</tr>

							<?php
								if (isset ( $item ['item_desconto'] ) && $item ['item_desconto']) {
									$valorTotal += '-' . $item ['desconto_aplicado'];
								} else {
									$valorTotal += ($item ['nfivl_item'] - $item ['nfidesconto']);
								}
								
								?>
							<?php endforeach; ?>
						</tbody>

					<tfoot>
						<tr>
							<td class="direita" colspan="6">Valor Total</td>
							<td class="direita"><?php echo number_format($valorTotal,2,',','.') ?></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>

	</div>

</div>
<div class="clear"></div>

</div>
<div class="bloco_acoes">
	<button type="button" id="bt_retorna_oinf"
		onclick="window.location.href='fin_fat_manual.php'">Retornar</button>
</div>
