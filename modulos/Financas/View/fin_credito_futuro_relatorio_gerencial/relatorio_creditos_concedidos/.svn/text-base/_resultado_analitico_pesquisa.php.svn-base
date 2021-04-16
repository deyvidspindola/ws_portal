<div class="separador"></div>

<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
	<div class="listagem">
		<table>
			<thead>
				<tr>
					<th style="width: 50px">Dt. Emissão</th>
					<th style="width: 50px">NF/Série</th>
					<th style="width: 50px">Cliente</th>
					<th style="width: 100px">CNPJ/CPF</th>
					<th style="width: 50px">Cód. Identif. CF</th>
					<th style="width: 150px">Motivo do Crédito</th>
					<th style="width: 50px">Inclusão</th>
					<th style="width: 50px">Protocolo</th>
					<th style="width: 50px">Campanha Promocional</th>
					<th style="width: 100px" >Vlr.Itens</th>
					<th style="width: 100px" >Vlr.Descto.</th>
					<th style="width: 100px" >Vlr.NF</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->view->dados['descontos'] AS $concedido) : ?>
				<?php $class = $class == 'impar' ? 'par' : 'impar'; ?>
				<tr class="<?php echo $class ?>">
					<td align="center"><?php echo $concedido->data_emissao_nota; ?></td>
					<td align="center"><?php echo $concedido->numero_nota . '/' . $concedido->serie_nota; ?></td>
					<td align="left"><?php echo $concedido->cliente; ?></td>
					<td align="center"><?php echo $concedido->cliente_doc; ?></td>
					<td align="center"><?php echo $concedido->credito_futuro_id; ?></td>
					<td align="left"><?php echo $concedido->motivo_credito; ?></td>
					<td align="center"><?php echo $concedido->forma_inclusao; ?></td>
					<td align="right"><?php echo $concedido->protocolo; ?></td>
					<td align="left"><?php echo $concedido->campanha_promocional; ?></td>
					<td align="right"><?php echo 'R$ ' . $concedido->valor_itens; ?></td>
					<td align="right"><?php echo 'R$ ' . $concedido->valor_desconto; ?></td>
					<td align="right"><?php echo 'R$ ' . $concedido->valor_nota; ?></td>
				</tr>

			<?php endforeach; ?>
			<tr class="total_linha">
				<td align="right" colspan="9"><b>TOTAL</b></td>
				<td align="right"><b>R$ <?php echo number_format($this->view->dados['total_valor_itens'], 2, ',', '.'); ?></b></td>
				<td align="right"><b>R$ <?php echo number_format($this->view->dados['total_valor_desconto'], 2, ',', '.'); ?></b></td>
				<td align="right"><b>R$ <?php echo number_format($this->view->dados['total_valor_notas'], 2, ',', '.'); ?></b></td>
			</tr>
		</tbody>
		<tfoot>
			<?php $registros = count($this->view->dados['descontos']) > 1 ? count($this->view->dados['descontos']) . ' registros encontrados.' : '1 registro encontrado.'; ?>
			<tr>
				<td colspan="12">
					<?php echo $registros ?>
				</td>
			</tr>
		</tfoot>
	</table>
</div>
</div>
<!-- BARRA DE AÃ‡Ã•ES -->
<div class="bloco_acoes">
	<button id="btn_gerarXls" data-tipo="A" data-resultado = "NULL" type="button">Gerar XLS</button>
	<button id="btn_enviarEmail" data-tipo="A" type="button">Enviar E-mail</button>
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
					Relatório Gerencial de Descontos Concedidos-<?php echo date('d-m-Y'); ?>
				</a>
			</div>
		</div>
</div>


<?php require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_relatorio_gerencial/relatorio_creditos_concedidos/_box_envio_email.php"; ?>

