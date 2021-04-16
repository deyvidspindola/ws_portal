<div class="mensagem alerta" id="msgalerta2" style="display: none;"></div>
<?php
if ($transferencia && count ( $transferencia ) > 0) :

	?>
<div class="bloco_titulo"><?php echo 'Hist&oacute;rico'; ?></div>
<div class="bloco_conteudo">
	<div class="listagem">
		<table>
			<thead>
				<tr>
                    <th><input title="Selecionar Todas as tranferencias"
                               type="checkbox" id="chk_all" name="chk_all" />
                        <?php echo "";?>
                    </th>
                    <th style="text-align: center;"><?php echo "Cliente";?></th>
                    <th style="text-align: center;"><?php echo "Contrato";?></th>
                    <th style="text-align: center;"><?php echo "Inicio de Vig&ecirc;cia";?></th>
					<th style="text-align: center;"><?php echo "Placa";?></th>
                    <th style="text-align: center;"><?php echo "Tipo do Contrato";?></th>
                    <th style="text-align: center;"><?php echo "Classe do Contrato";?></th>
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
                    <td>
                    <input title="Selecionar Nota" type="checkbox" name="chk_oid[]" id="chk_oid" data-idstatus="<?php echo $key['concsioid']?>" value="<?php echo $key['connumero'];?>" />
                    </td>
					<td style="text-align: center;"><?=$key['clinome']?></td>
					<td style="text-align: center;"><?=$key['connumero']?></td>
					<td style="text-align: center;"><?=$key['inicio_vigencia']?></td>
					<td style="text-align: center;"><?=$key['veiplaca']?></td>
					<td style="text-align: center;"><?php echo utf8_encode($key['tipo_contrato']); ?></td>
					<td style="text-align: center;"><?php echo utf8_encode($key['classe_contrato']); ?></td>
					<td style="text-align: center;" class="<?php echo utf8_encode ($key['csidescricao']) ?>"><?php echo utf8_encode ($key['csidescricao'])?></td>
                </tr>
					<?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><td colspan="8" style="text-align: center;"><?php echo count($transferencia); ?> registro(s) encontrado(s)</td></tr>
            </tfoot>
		</table>
	</div>
    
    
<?php else:  ?>
<div class="mensagem info"><?php echo utf8_encode("Não foram encontrados registros");?></div>
<?php endif;  ?>
</div>

