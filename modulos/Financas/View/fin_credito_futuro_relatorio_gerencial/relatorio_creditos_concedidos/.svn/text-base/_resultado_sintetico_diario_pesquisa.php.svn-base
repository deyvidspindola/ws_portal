
<style type="text/css">
.total_bg{
	background: #BAD0E5;
}
</style>
<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="bloco_conteudo">
	<div class="listagem">
		<table>
			<thead>
				<tr>
					<th class="menor">Dt. Emissão</th>
					<th class="maior">Motivo do Crédito</th>
					<th class="maior">Vlr.Itens</th>
					<th class="maior">Vlr.Descto.</th>
					<th class="maior">Vlr.NF</th>
					<th class="maior">% Vlr.Descto. Sobre Vlr.Itens</th>
				</tr>
			</thead>
			<tbody>
				<?php $i = 0; ?>
				<?php foreach ($this->view->dados['descontos'] AS $concedidos) : ?>	
				<?php $class = 'par'; ?>							
					<?php foreach ($concedidos['itens'] as $key => $concedido) : ?>	
						<?php $class = $class == "impar" ? "par" : "impar" ?>	
							<tr class="<?php echo $class ?>">
								<td align="center"><?php echo $concedido->data_emissao_nota ?></td>
								<td align="center"><?php echo $concedido->motivo_credito ?></td>
								<td align="right"><?php  echo 'R$ ' . number_format($concedido->valor_itens , 2, ',', '.') ?></td>
								<td align="right"><?php  echo 'R$ ' . number_format($concedido->valor_desconto , 2, ',', '.') ?></td>
								<td align="right"><?php  echo 'R$ ' . number_format($concedido->valor_nota , 2, ',', '.') ?></td>
								<td align="right"><?php  echo  $concedido->percentual . ' %' ?></td>
							</tr>
							<?php $i++; ?>
						<?php endforeach; ?>
						<tr class="total_linha">
							<td align="right" colspan="2"><b>TOTAL</b></td>
							<td align="right"><b><?php echo 'R$ ' . number_format($concedidos['vl_itens'] , 2, ',', '.') ?></b></td>
							<td align="right"><b><?php echo 'R$ ' . number_format($concedidos['vl_desc'] , 2, ',', '.') ?></b></td>
							<td align="right"><b><?php echo 'R$ ' . number_format($concedidos['vl_nf'] , 2, ',', '.') ?></b></td>
							<td align="right"><b><?php echo $concedidos['vl_percentual'] . ' %' ?></b></td>
						</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<?php $registros = $i == 1 ? $i . ' registro encontrado.' : $i . ' registros encontrados.' ?>
			<tr>
				<td colspan="6"><?php echo $registros ?></td>
			</tr>
		</tfoot>
	</table>
</div>
</div>

<div class="bloco_acoes">
	<button id="btn_gerarXls" data-tipo="S" data-resultado = "d" type="button">Gerar XLS</button>
	<button id="btn_enviarEmail" data-tipo="S" type="button">Enviar E-mail</button>
</div>

<div id="loader_xls" class="carregando invisivel"></div>

<div id="baixarXls" class="invisivel">
	<div class="clear separador"></div>
		<div class="bloco_titulo">Download</div>
		<div class="bloco_conteudo">
			<div class="conteudo centro">
				<a href="#" target="_blank">
					<img src="images/icones/t3/caixa2.jpg">
					<br>
					Relatório Gerencial de Descontos Concedidos-Sintético-<?php echo date('d-m-Y'); ?>
				</a>
			</div>
		</div>
</div>

<?php require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_relatorio_gerencial/relatorio_creditos_concedidos/_box_envio_email.php"; ?>

