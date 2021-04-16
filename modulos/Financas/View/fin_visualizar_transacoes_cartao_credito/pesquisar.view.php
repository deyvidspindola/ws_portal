<?php
/**
 * @author	Gabriel Luiz Pereira
 * @email	gabriel.pereira@meta.com.br
 * @since	06/09/2012
 */
?>
<tr>
	<td align="center">
		<table class="tableMoldura resultado_pesquisa">
			<tr class="tableSubTitulo"><td colspan="8"><h2>Resultado da pesquisa</h2></td></tr>
			<tr class="tableTituloColunas tab_registro">
				<td align="center"><h3>Título</h3></td>
				<td><h3>Cliente</h3></td>
				<td align="right"><h3>Valor Total</h3></td>
				<td align="center"><h3>Data de Vencimento</h3></td>
				<td align="center"><h3>Data da Transação</h3></td>
				<td align="center"><h3>Forma de Cobrança</h3></td>
				<td align="center"><h3>Situação</h3></td>
				<td align="center"><h3>Ação</h3></td>
			</tr>
			<?php 
			
			$total = 0;
			
			if(count($view['titulos']) > 0 ) { ?>
			
				<?php foreach($view['titulos'] as $linha) { ?>
					<tr class="tr_resultado_ajax">
						<td align="center">
						    <form id="form_detalhes<?=$linha['ctctitoid']?>" method="post" class="detalhesForm">
                                <input type="hidden" name="acao" id="acao" value="detalhes" />
                                <input type="hidden" name="titoid" id="titoid" value="<?=$linha['ctctitoid']?>" />
                                <input type="hidden" name="titdt_vencimento" id="titdt_vencimento" value="<?=$linha['titdt_vencimento']?>" />
                                <input type="hidden" name="clinome" id="clinome" value="<?=$_POST['clinome']?>" />
                                <input type="hidden" name="clitipo" id="clitipo" value="<?=$_POST['clitipo']?>" />
                                <input type="hidden" name="clino_documento" id="clino_documento" value="<?=$_POST['clino_documento']?>" />
                                <input type="hidden" name="titdt_vencimento_inicio" id="titdt_vencimento_inicio" value="<?=$_POST['titdt_vencimento_inicio']?>" />
                                <input type="hidden" name="titdt_vencimento_fim" id="titdt_vencimento_fim" value="<?=$_POST['titdt_vencimento_fim']?>" />
                                <input type="hidden" name="titdt_transacao_inicio" id="titdt_transacao_inicio" value="<?=$_POST['titdt_transacao_inicio']?>" />
                                <input type="hidden" name="titdt_transacao_fim" id="titdt_transacao_fim" value="<?=$_POST['titdt_transacao_fim']?>" />
                                <input type="hidden" name="situacao" id="situacao" value="<?=$_POST['situacao']?>" />
                                <a href="javascript:void(0);" onclick="jQuery('#form_detalhes<?=$linha['ctctitoid']?>').submit();"><?=$linha['ctctitoid']?></a>
                            </form>
					    </td>
						<td><?=$linha['clinome']?></td>
						<td align="right"><?=number_format($linha['titvl_titulo'],2,',','.')?></td>
						<td align="center"><?=implode("/",array_reverse(explode("-",$linha['titdt_vencimento'])))?></td>
						<td align="center"><?=implode("/",array_reverse(explode("-",$linha['dt_transacao'])))?></td>
						<td align="center"><?=$linha['forcnome']?></td>
						<td align="center">
							<?php if($linha['ctcccchoid'] == 0 && $linha['status'] == "") { ?>
								Não Enviada
							<?php } elseif ($linha['titdt_pagamento'] == "" &&  $linha['titdt_credito'] == "" && $linha['status'] != 'CON') { ?>
								Pendente de Pagamento
							<?php } elseif (($linha['titdt_pagamento'] != "" && $linha['titdt_credito'] != "" && $linha['status'] === 'CON') || ($linha['ctcccchoid'] != 0 && $linha['status'] == '') ) { ?>
								Recebida
							<?php } ?>
						</td>
						<td align="center">
							<?php if($linha['ctcccchoid'] == 0 || $linha['ctcccchoid'] == "") { ?>
								<form id="form_reenviar<?=$linha['ctctitoid']?>" method="post" class="reenvioForm">
									<input type="hidden" name="acao" id="acao" value="reenviar" />
									<input type="hidden" name="titoid" id="titoid" value="<?=$linha['ctctitoid']?>" />
									<input type="hidden" name="clioid" id="clioid" value="<?=$linha['clioid']?>" />
									<input type="hidden" name="valort" id="valort" value="<?=$linha['titvl_titulo']?>" />
									<input type="hidden" name="clinome" id="clinome" value="<?=$_POST['clinome']?>" />
									<input type="hidden" name="clitipo" id="clitipo" value="<?=$_POST['clitipo']?>" />
									<input type="hidden" name="clino_documento" id="clino_documento" value="<?=$_POST['clino_documento']?>" />
									<input type="hidden" name="titdt_vencimento_inicio" id="titdt_vencimento_inicio" value="<?=$_POST['titdt_vencimento_inicio']?>" />
									<input type="hidden" name="titdt_vencimento_fim" id="titdt_vencimento_fim" value="<?=$_POST['titdt_vencimento_fim']?>" />
									<input type="hidden" name="titdt_transacao_inicio" id="titdt_transacao_inicio" value="<?=$_POST['titdt_transacao_inicio']?>" />
                                    <input type="hidden" name="titdt_transacao_fim" id="titdt_transacao_fim" value="<?=$_POST['titdt_transacao_fim']?>" />
									<input type="hidden" name="situacao" id="situacao" value="<?=$_POST['situacao']?>" />
									
									<?php if($visualizarTransacoes->verificarFormaPagamentoAtualCliente($linha['clioid']) 
											  && $visualizarTransacoes->verificarTituloPagamentoCredito($linha['titformacobranca'])){?>
											  
										<a href="javascript:void(0);" onclick="jQuery('#form_reenviar<?=$linha['ctctitoid']?>').submit();">Reenviar</a>
									
									<?php }?>
									
								</form>
							<?php } ?>
						</td>
					</tr>
					
				<?php $total+=$linha["titvl_titulo"]; } //endforeach; ?>
			
				<tr class="tableRodapeModelo2">
					<td align="right" colspan="2"><b>Total&nbsp;</b></td>
					<td align="right" colspan="1"><b><?=number_format($total,2,",",".")?>&nbsp;</b></td>
					<td align="right" colspan="5"></td>
				</tr>
			
				<tr class="tableRodapeModelo3">
					<td align="center" colspan="8" id="total_registros"><?=$view['total_registros']?></td>
				</tr>
				<tr class="tableRodapeModelo3">
					<td align="center" colspan="8" id="total_registros">
						<input type="button" class="botao" id="botao_gerarcsv" value="Gerar CSV" />
					</td>
				</tr>
			<?php } else { ?>
				<tr class="tableRodapeModelo3">
					<td align="center" colspan="8" id="total_registros">Nenhum registro encontrado!</td>
				</tr>
			<?php } //endif; ?>
		</table>
	</td>
</tr>
