<?php //Transferência
$contratosTransferidos = $data;
if ($contratosTransferidos && count ( $contratosTransferidos ) > 0) :

?>
<div class="bloco_titulo" > <?php echo utf8_encode("CONTRATOS TRANSFERIDOS COM SUCESSO");?> </div>
<div class="bloco_conteudo" >
	<div class="listagem">
		<table>
			<thead>
				<tr>
                    <th style="text-align: center;"> Termo Original</th>
                    <th style="text-align: center;"> Novo Termo</th>
                    <th style="text-align: center;"> Novo Titular </th>
                     <th style="text-align: center;"> Mensagem </th>
				</tr>
			</thead>
			<tbody>	
                <?php
                    foreach ( $contratosTransferidos as $row ) :

                        $class = $class == '' ? 'par' : '';
		        ?>
                <tr class="<?=$class?>">

					<td style="text-align: center;"><?php echo $row['prptermo_original']; ?></td>
					<td style="text-align: center;"><?php echo $row['prptermo']; ?></td>
					<td style="text-align: center;"><?php echo $row['novo_clinome'] .' - '.$docLabel. $row['novo_clino_documento'];?></td>
					<td style="text-align: center;"><?php echo $row['status_message']; ?></td>

                </tr>
					<?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><td colspan="14" style="text-align: center;"><?php echo count($contratosTransferidos); ?> contrato(s) transferido(s) com sucesso</td></tr>
            </tfoot>
		</table>
	</div>

</div>
<?php endif;  ?>



