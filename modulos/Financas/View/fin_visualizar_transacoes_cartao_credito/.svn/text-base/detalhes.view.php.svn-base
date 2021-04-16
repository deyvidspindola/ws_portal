<?php
/**
 * @author	Gabriel Luiz Pereira
 * @email	gabriel.pereira@meta.com.br
 * @since	06/09/2012
 */


$titulo     = (!isset($_POST['titoid']))?null:$_POST['titoid'];
$vencimento = (!isset($_POST['titdt_vencimento']))?null:$_POST['titdt_vencimento'];
 
?>

<tr>
	<td align="center">
		<table class="tableMoldura resultado_pesquisa">
			<tr class="tableSubTitulo"><td colspan="8"><h2>Detalhes</h2></td></tr>
			<tr>
				<td width="40"><h2>Título: </h2></td>
				<td><?=$titulo?></td>
			</tr>
			<tr>
				<td width="40"><h2>Data de Vencimento: </h2></td>
				<td><?=implode("/",array_reverse(explode("-",$vencimento)))?></td>
				
			</tr>
		</table>
	</td>
	<!--td><strong>Título: </strong></td-->
</tr>

<tr>

	<td align="center">
		<?php if($view['detalhes']) { ?>
			<table class="tableMoldura resultado_pesquisa">
				<tr class="tableSubTitulo"><td colspan="8"><h2>Retorno da SoftExpress</h2></td></tr>
				<tr class="tableTituloColunas tab_registro">
					<!--td width="6%" align="center"><h3>Título</h3></td-->
					<td width="34%" align="center"><h3>Cópia do Cupom da Transação</h3></td>
					<td width="8%" align="center"><h3>NSU</h3></td>
					<!--td width="7%" align="center"><h3>Autorizadora</h3></td-->
					<td width="9%" align="center"><h3>Tipo do Pagamento</h3></td>
					<td width="9%" align="center"><h3>Nº Autorização</h3></td>
					<td width="6%" align="center"><h3>Status</h3></td>
					<td width="9%" align="center"><h3>Valor Total</h3></td>
					<td width="16%" align="center"><h3>Msg. Retorno</h3></td>
					<td width="9%" align="center"><h3>Data de Pagamento</h3></td>
				</tr>

				<?php foreach($view['detalhes'] as $linha) { ?>
					<tr class="tr_resultado_ajax">
						<!--td align="center"><?=$linha['ccchtitoid']?></td-->
						<td align="center"><?=$linha['ccchcupom_cliente']?></td>
						<td align="center"><?=$linha['ccchnsu_autorizadora']?></td>
						<!--td align="center"><?=$linha['ccchautorizadora']?></td-->
						<td align="center"><?=$linha['ccchtipopagamento']?></td>
						<td align="center"><?=$linha['ccchnumero_autorizacao']?></td>
						<td align="center"><?=$linha['ccchstatustransacao']?></td>
						<td align="center"><?=number_format($linha['ccchvalortransacao'],2,',','.')?></td>
						<td align="center"><?=$linha['ccchmensagem']?></td>
						<td align="center"><?=implode("/",array_reverse(explode("-",$linha['ccchdt_resposta'])))?></td>
					</tr>
				<?php } //endforeach ?>
			</table>
		<?php } else { ?>
			<table class="tableMoldura resultado_pesquisa">
				<tr class="tableSubTitulo"><td colspan="9"><h2>Retorno da SoftExpress</h2></td></tr>
				<tr class="tr_resultado_ajax">
					<td colspan="9" align="center">Nenhum detalhe referente ao título selecionado</td>
				</tr>
			</table>
		<?php } //endif ?>
		
		<?php if($view['historico']) { ?>
			<table class="tableMoldura resultado_pesquisa">
				<tr class="tableSubTitulo"><td colspan="9"><h2>Histórico de Envios</h2></td></tr>
				<tr class="tableTituloColunas tab_registro">
					<td width="15%"><h3>Data Envio</h3></td>
					<td width="15%"><h3>Status</h3></td>
					<td width="70%"><h3>Motivo</h3></td>
				</tr>

				<?php foreach($view['historico'] as $linha) { ?>
					<tr class="tr_resultado_ajax">
						<?php $data = explode(" ",$linha['ctcdt_inclusao']);?>
						<td><?=implode("/",array_reverse(explode("-",$data[0])))?></td>
						<td><?=($linha['ctcccchoid'] != "")?'OK':'Não Enviado'?></td>
						<td><?=$linha['ctcmotivo']?></td>
					</tr>
				<?php } //endforeach ?>
			</table>
		<?php } else { ?>
			<table class="tableMoldura resultado_pesquisa">
				<tr class="tableSubTitulo"><td colspan="9"><h2>Histórico de Envios</h2></td></tr>
				<tr class="tr_resultado_ajax">
					<td colspan="9" align="center">Nenhum histórico de envio referente ao título selecionado</td>
				</tr>
			</table>
		<?php } //endif ?>
	</td>
</tr>
