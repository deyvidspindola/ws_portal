<?php //Transferência
$contratosErrors = $data;
if ($contratosErrors && count ( $contratosErrors ) > 0) :

	?>
<div class="bloco_titulo" > <?php echo utf8_encode("CONTRATOS COM ERRO");?> </div>
<div class="bloco_conteudo" >
	<div class="listagem">
		<table>
			<thead>
				<tr>
                    <th style="text-align: center;"> Contrato </th>
                    <th style="text-align: center;"> Erro </th>
				</tr>
			</thead>
			<tbody>	
                <?php
                    foreach ( $contratosErrors as $row => $key ) :

                        $class = $class == '' ? 'par' : '';
		        ?>
                <tr class="<?=$class?>">

					<td style="text-align: center;"><?php echo $row; ?></td>
					<td style="text-align: center;"><?php echo $key;?></td>

                </tr>
					<?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><td colspan="14" style="text-align: center;"><?php echo count($contratosErrors); ?> contrato(s) com erro(s) encontrado(s)</td></tr>
            </tfoot>
		</table>
	</div>

</div>
<?php endif;  ?>



