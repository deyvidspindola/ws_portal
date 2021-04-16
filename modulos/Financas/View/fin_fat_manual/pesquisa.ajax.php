<div class="mensagem alerta" id="msgalerta2" style="display: none;"></div>

<?php if($notas && count($notas) > 0): ?>
<div class="bloco_titulo">Resultado da pesquisa de notas fiscais</div>
<div class="bloco_conteudo">

	<div class="listagem">

		<table>
			<thead>
				<tr>
					<th><input title="Selecionar Todas as Notas" type="checkbox"
						id="chk_all" name="chk_all" />
							<?php echo "NF/Série";?> 
						</th>
					<th style="text-align: center;"><?php echo "Dt. Referência";?></th>
				<!--  <th style="text-align: center;"><?php echo "Dt. Inclusão";?></th>-->	
					<th style="text-align: center;"><?php echo "Dt. Emissão";?></th>
				<!--  	<th style="text-align: center;"><?php echo "Dt. Envio Gráfica";?></th>-->
					<th style="text-align: center;">Dt. Vencimento</th>
					<th style="text-align: center;">Valor Total</th>
				</tr>
			</thead>
			<tbody>	
					<?php
	
foreach ( $notas as $nota ) :
		$class = $class == '' ? 'par' : '';
		?>						
					<tr class="<?=$class?>">
					<td><input title="Selecionar Nota" type="checkbox" name="chk_oid[]"
						value="<?=$nota['nfloid']?>" />
							<?=$nota['nflno_numero']?>/<?=$nota['nflserie']?>
						</td>
					<td style="text-align: center;"><?=$nota['nfldt_referencia']?></td>
			<!-- 		<td style="text-align: center;"><?=$nota['nfldt_inclusao']?></td> -->
					<td style="text-align: center;"><?=$nota['nfldt_emissao']?></td>
		<!--  			<td style="text-align: center;"><?=$nota['nfldt_envio_grafica']?></td>-->
					<td style="text-align: center;"><?=$nota['nfldt_vencimento']?></td>
					<td style="text-align: right;"><?=number_format($nota['nflvl_total'],2,",",".")?></td>
				</tr>						
					<?php endforeach; ?>
				</tbody>
			<tfoot>
				<tr>
					<td colspan="8" style="text-align: center;">
					<?=count($notas)?> registro(s) encontrado(s).
					<?=(count($notas)==2000 ? " A pesquisa está limitada em 2000 registros." : "")?>
					
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

</div>
<div class="bloco_acoes">
	<button type="button" name="confirmar_fatura" id="confirmar_fatura">Confirmar</button>
</div>

<?php else:  ?>
<div class="mensagem info">Nenhum registro encontrado.</div>
<?php endif;  ?>