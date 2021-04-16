<div class="mensagem alerta" id="msgalerta2" style="display: none;"></div> 
<?php

if ($transferencia && count ( $transferencia ) > 0) :

	?>
<div class="bloco_titulo" ><?php echo utf8_encode("Contratos Para Transferência - Itens selecionados");?></div>
<div class="bloco_conteudo" >
	<div class="listagem">
            <table id="dadosTransferencia">
			<thead>
				<tr>
                    <th style="text-align: center;"><?php echo "Cliente";?></th>
                    <th style="text-align: center;"><?php echo "Contrato";?> </th>
                    <!--<th style="text-align: center;"><?php echo "Novo Contrato";?></th>-->
                    <th style="text-align: center;"><?php echo utf8_encode("Inicio de Vigência");?></th>
                    <!--<th style="text-align: center;"><?php echo "Meses Restantes";?></th>-->
                    <!--<th style="text-align: center;"><?php echo utf8_encode("Nova Vigência");?></th>-->
					<th style="text-align: center;"><?php echo "Placa";?></th>
                    <th style="text-align: center;"><?php echo "Tipo do Contrato";?></th>
                    <th style="text-align: center;"><?php echo "Classe do Contrato";?></th>
                    <th style="text-align: center;"><?php echo utf8_encode("Locação");?></th>
                    <!--<th style="text-align: center;"><?php echo utf8_encode("Acessórios");?></th>-->
                    <th style="text-align: center;"><?php echo "Monitoramento";?></th>
                    <!--<th style="text-align: center;"><?php echo "Valor Total";?></th>-->
					<th style="text-align: center;"><?php echo "Status";?></th>

				</tr>
			</thead>
			<tbody>	
                <?php
                    foreach ( $transferencia as $row => $key ) :
                        $telefone1 = $key ['telefone1'];
                        $telefone2 = $key ['telefone2'];
                        $email = $key ['cliemail'];
                        $class = $class == '' ? 'par' : '';
		        ?>
                <tr class="<?=$class?>">

					<td style="text-align: center;"><?=utf8_encode($key['clinome'])?></td>
                    <td style="text-align: center;"><?=$key['connumero']?><input name="contrato" type="hidden" value="<?=$key['connumero']?>"</td>
					<!--<td style="text-align: center;">-</td>-->
					<td style="text-align: center;"><?=$key['inicio_vigencia']?> <input type="hidden" id="dataInicioVigencia"  name="dataInicioVigencia" value="<?=$key['inicio_vigencia']?>" /> </td>
					<!--<td style="text-align: center;"><?php echo $mesesTerminoVigencia; ?>/<?=$key['conprazo_contrato']?></td>-->
					<!--<td style="text-align: center;">12/06/2019</td>-->
					<td style="text-align: center;"><?=$key['veiplaca']?></td>
					<td style="text-align: center;"><?=utf8_encode($key['tipo_contrato'])?></td>
					<td style="text-align: center;"><?=utf8_encode($key['classe_contrato'])?></td>
					<td style="text-align: center;"><input id="valorLocacao" type="text"  onkeyup="jQuery(this).maskMoney({thousands:'.', decimal:','});" value="<?php echo number_format($key['locacao'],2,",","."); ?>" name="valorLocacao" size="9" maxlength='11'></td>
                    <!--<td style="text-align: center;"> <?php echo number_format($key['acessorios'],2,",","."); ?><input id="valorAcessorios" type="hidden" maxlength="7" onkeyup="jQuery(this).maskMoney({thousands:'.', decimal:','});" value="<?php echo number_format($key['acessorios'],2,",","."); ?>" name="valorAcessorios" size="3"></td>-->
					<td style="text-align: center;"><input id="valorMonitoramento" type="text"  onkeyup="jQuery(this).maskMoney({thousands:'.', decimal:','});" value="<?php echo number_format($key['valor_monitoramento'],2,",","."); ?>" name="valorMonitoramento" size="9" maxlength='11'></td>
					<!--<td style="text-align: center;"> <?php echo number_format($key['total'],2,",","."); ?></td>-->
					<td style="text-align: center;" class="<?php echo utf8_encode ($key['csidescricao']) ?>"><?php echo utf8_encode ($key['csidescricao'])?></td>
                </tr>
					<?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><td colspan="14" style="text-align: center;"><?php echo count($transferencia); ?> registro(s) encontrado(s)</td></tr>
            </tfoot>
		</table>
	</div>

<?php else:  ?>
<div class="mensagem info"><?php echo utf8_encode("Nenhum item selecionado para transferência de contrato");?></div>
<?php endif;  ?>
</div>

