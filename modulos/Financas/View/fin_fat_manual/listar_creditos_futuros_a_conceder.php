
<?php if (!empty($this->creditosFuturo)) : ?>

<?php $isAjax = $_SERVER['HTTP_X_REQUESTED_WITH'] && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'; 
?>
<div class="bloco_titulo"><?php echo utf8_decode("CrÃ©ditos a conceder");?></div>
<div class="bloco_conteudo">
	<div class="listagem">
		<table id="credito_futuro_cliente">
			<thead>
				<tr>
					<th>	
						<?php "Motivo do Crédito";?>
					</th>
					<th>							
						<?php echo "Obrigação Financeira";?>
					</th>
					<th>							
						Aplicado sobre
					</th>
					<th>
						Valor
					</th>
				</tr>
			</thead>
			<tbody>	
				<?php foreach ($this->creditosFuturo as $key => $item) : ?>

				<?php $class = $class == 'par' ? 'impar' : 'par' ?>

				<tr class="<?php echo $class ?> creditos_futuro  credito_tipo_desconto_<?php echo $item['cfotipo_desconto'] == '1' ? 'P' : 'V'  ?>" data-porcentagem="<?php echo $item['porcentagem_desconto'] ?>" data-creditoId = "<?php echo $item['credito_id'] ?>" id="credito_futuro_<?php echo $item['credito_id'] ?>"  data-aplicarDescontoSobre="<?php echo $item['cfoaplicar_desconto'] ?>">
					<td><?php echo $item['cfmcdescricao'] ?></td>
					<td><?php echo $item['obrobrigacao'] ?></td>
					<td><?php echo $item['aplicar_desconto_descricao'] ?></td>
				<td class="direita valor"><?php echo  $item['valor_formatado'] ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
			<tfoot>
			</table>
		</div>
	</div>

<?php endif; ?>