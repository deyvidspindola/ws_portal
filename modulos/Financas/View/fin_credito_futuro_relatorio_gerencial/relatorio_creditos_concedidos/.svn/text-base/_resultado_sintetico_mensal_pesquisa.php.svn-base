
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
					<th class="menor">Mês/Ano</th>
					<th class="maior">Vlr.Itens</th>
					<th class="maior">Vlr.Descto.</th>
					<th class="maior">Vlr.NF</th>
					<th class="maior">% Vlr.Descto. Sobre Vlr.Itens</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->view->dados['descontos'] AS $concedidos) : ?>
					<?php $class = $class == 'impar' ? 'par' : 'impar' ?>
					<tr class="<?php echo $class  ?>">
						<td class="agrupamento" align="center"><?php echo $concedidos->data_emissao_nota ?></td>
						<td align="right"><?php echo 'R$ ' . number_format($concedidos->valor_itens , 2, ',', '.'); ?></td>
						<td align="right"><?php echo 'R$ ' . number_format($concedidos->valor_desconto , 2, ',', '.'); ?></td>
						<td align="right"><?php echo 'R$ ' . number_format($concedidos->valor_nota , 2, ',', '.'); ?></td>
						<td align="right"><?php echo $concedidos->percentual . ' %'; ?></td>
					</tr>
				<?php endforeach; ?>
					<?php $class = $class == 'impar' ? 'par' : 'impar' ?>
					<tr class="<?php echo $class  ?>">
						<td class="agrupamento" align="center">Total</td>
						<td align="right"><strong><?php echo 'R$ ' . number_format($this->view->dados['valor_itens_total'] , 2, ',', '.'); ?></strong></td>
						<td align="right"><strong><?php echo 'R$ ' . number_format($this->view->dados['valor_descontos_total'] , 2, ',', '.'); ?></strong></td>
						<td align="right"><strong><?php echo 'R$ ' . number_format($this->view->dados['valor_nota_total'] , 2, ',', '.'); ?></strong></td>
						<td align="right"><strong><?php echo $this->view->dados['percentual_descontos_total'] . ' %'; ?></strong></td>
					</tr>
			</tbody>

		</table>
	</div>
</div> 

<div class="bloco_acoes">
	<button id="btn_gerarXls" data-tipo="S" data-resultado = "m" type="button">Gerar XLS</button>
	<button id="btn_enviarEmail" data-tipo="S" type="button">Enviar E-mail</button>
</div>

<?php if (isset($this->view->graficoSinteticoMensal) && trim($this->view->graficoSinteticoMensal) != '') : ?>
<div class="clear separador"></div>

<div class="bloco_titulo">Gráfico</div>
<div class="bloco_conteudo">
	<img style="width: 100%" src="<?php echo $this->view->graficoSinteticoMensal ?>">
</div>
<?php endif; ?>

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
