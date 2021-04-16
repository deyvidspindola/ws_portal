<?php require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_relatorio_gerencial/cabecalho.php"; ?>

<div class="bloco_titulo">Campanha(s) Promocional(ais) Vigente(s)</div>
<div class="bloco_conteudo">
	
	<div class="formulario">

		<div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
			<?php echo $this->view->mensagemErro; ?>
		</div>

		<div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
			<?php echo $this->view->mensagemAlerta; ?>
		</div>

		<?php if (count($this->view->dados) > 0): ?>
		<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
		<div class="resultado bloco_conteudo">
			<div class="listagem">
				<table>
					<thead>
						<tr>
							<th style="width: 55px" class="menor">Cód. Identif.</th>
							<th style="width: 170px" class="menor">Período de Vigência</th>
							<th style="width: 170px" class="menor">Tipo de Campanha Promocional</th>
							<th style="width: 170px" class="menor">Motivo do Crédito</th>
							<th style="width: 80px" class="menor">Tipo do Desconto</th>
							<th style="width: 150px" class="menor">Valor / %</th>
							<th style="width: 80px" class="menor">Forma de Aplicação</th>
							<th style="width: 55px" class="menor">Qtde. de Parcelas</th>
							<th style="width: 170px" class="menor">Usuário</th>							
						</tr>
					</thead>
					<tbody>
						<?php
						if (count($this->view->dados) > 0):
							$classeLinha = "par";
						?>

						<?php foreach ($this->view->dados as $resultado) : ?>
						<?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
						<tr class="<?php echo $classeLinha; ?>">
							<td class="centro"><?php echo $resultado->id; ?></td>
							<td class="esquerda"><?php echo $resultado->periodo; ?></td>
							<td class="esquerda"><?php echo $resultado->tipo_campanha; ?></td>
							<td class="esquerda"><?php echo $resultado->motivo_credito; ?></td>							
							<td class="esquerda"><?php echo $resultado->tipo_desconto; ?></td>
							<td class="direita"><?php echo $resultado->valor; ?></td>
							<td class="esquerda"><?php echo $resultado->forma_aplicacao; ?></td>
							<td class="direita"><?php echo $resultado->qtd_parcelas; ?></td>
							<td class="esquerda"><?php echo $resultado->usuario; ?></td>							
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="9" class="centro">
						<?php
						$totalRegistros = count($this->view->dados);
						echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
						?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
<?php endif; ?>
</div>
</div>

<?php require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_relatorio_gerencial/rodape.php"; ?>