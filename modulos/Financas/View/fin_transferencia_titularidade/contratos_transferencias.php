<div class="mensagem alerta" id="msgalerta2" style="display: none;"></div> 
<?php
if ($transferencia && count ( $transferencia ) > 0) :

	?>
<div class="bloco_titulo"><?=utf8_encode("Contratos Para Transferência")?></div>
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
                    <th style="text-align: center;"><?=utf8_encode("Inicio de Vigência")?></th>
                    <!--<th style="text-align: center;"><?php echo "Meses Restantes";?></th>-->
					<th style="text-align: center;"><?php echo "Placa";?></th>
                    <th style="text-align: center;"><?php echo "Tipo do Contrato";?></th>
                    <th style="text-align: center;"><?php echo "Classe do Contrato";?></th>
                    <!--<th style="text-align: center;"><?=utf8_encode("Locação")?></th>-->
                    <!--<th style="text-align: center;"><?=utf8_encode("Acessórios")?></th>-->
                    <!--<th style="text-align: center;"><?php echo "Monitoramento";?></th>-->
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
                    <td>
                    <input title="Selecionar Nota" type="checkbox" name="chk_oid[]" id="chk_oid" data-idstatus="<?php echo $key['concsioid']?>" value="<?php echo $key['connumero'];?>" />
                    </td>
					<td style="text-align: center;"><?=utf8_encode($key['clinome'])?></td>
					<td style="text-align: center;"><?=$key['connumero']?></td>
					<td style="text-align: center;"><?=$key['inicio_vigencia']?></td>
					<!--<td style="text-align: center;"><?php echo $mesesTerminoVigencia; ?>/<?=$key['conprazo_contrato']?></td>-->
					<td style="text-align: center;"><?=$key['veiplaca']?></td>
					<td style="text-align: center;"><?=utf8_encode($key['tipo_contrato'])?></td>
					<td style="text-align: center;"><?=utf8_encode($key['classe_contrato'])?></td>
                    <!--<td style="text-align: center;"><?=number_format($key['locacao'],2,",",".");?></td>-->
                    <!--<td style="text-align: center;"><?=number_format($key['acessorios'],2,",",".");?></td>-->
                    <!--<td style="text-align: center;"><?=number_format($key['monitoramento'],2,",",".");?></td>-->
                    <!--<td style="text-align: center;"><?=number_format($key['total'],2,",",".");?></td>-->
                    <td style="text-align: center;" class="<?php echo utf8_encode ($key['csidescricao']) ?>"><?php echo utf8_encode ($key['csidescricao'])?></td>
                </tr>
					<?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><td colspan="13" style="text-align: center;"><?php echo count($transferencia); ?> registro(s) encontrado(s)</td></tr>
            </tfoot>
		</table>
	</div>
    <input type="hidden" id="telefone1_titular" name="telefone1_titular" value="<?php echo $telefone1;?>" maxlength="20" />
    <input type="hidden" id="telefone2_titular"
           name="telefone2_titular" value="<?php echo $telefone2;?>" maxlength="20" />
    <input type="hidden" id="email_titular" name="email_titular"
           value="<?php echo $email?>" class="campo" size="50" maxlength="100" />
    <input type="hidden" id="responsavel_titular" name="responsavel_titular"
           value="Responsavel Felipe" class="campo" size="50" maxlength="100" />
    <!--
	<table class="tableMoldura">
	<tr><td></td></tr>
				<tr>
			<td>
			<fieldset>
				<?php echo utf8_encode("Taxa de transferência:<br />
					De 1 a 9 veículos  R$100,00 para cada veí­culo;<br />
					De 10 a 15 Única taxa de R$350,00;<br />	 
					E acima de 15 Única taxa de R$500,00;<br />");?>	
			
			</fieldset>
			</td>
		</tr>
		<tr>
			<td><label for="cliente">Tefone 1*</label></td>
		</tr>
		
		<tr>
			<td><input type="hidden" id="telefone1_titular" name="telefone1_titular" value="<?php echo $telefone1;?>" maxlength="20" /></td>

		</tr>
		<tr>
			<td><label for="cliente">Telefone 2*</label></td>
		</tr>
		<tr>
			<td><input type="hidden" id="telefone2_titular"
						name="telefone2_titular" value="<?php echo $telefone2;?>" maxlength="20" /></td>
		</tr>
		<tr>
			<td><label for="pesq_campo1">E-Mail*</label></td>
		</tr>
		<tr>
			<td><input type="hidden" id="email_titular" name="email_titular"
				value="<?php echo $email?>" class="campo" size="50" maxlength="100" /></td>
		</tr>
		<tr>
			<td><label for="pesq_campo1"><?php echo utf8_encode("Responsável*");?></label></td>
		</tr>
		<tr>
			<td><input type="hidden" id="responsavel_titular" name="responsavel_titular"
				value="Responsavel Felipe" class="campo" size="50" maxlength="100" /></td>
		</tr>
	</table>
	-->
<?php else:  ?>
<div class="mensagem info"><?php echo utf8_encode("Não foram encontrados registros");?></div>
<?php endif;  ?>
</div>
<div class="bloco_acoes">
    <button type="button" name="confirmar_transferencia"
		id="confirmar_transferencia" > <?php echo utf8_encode('Selecionar Contratos Para Transferência')?> </button>
    <button type="button" name="download_Planilha"
            id="download_Planilha" >Gerar CSV</button>

    <!-- <a href="fin_transferencia_titularidade.php?acao=novo"><button type="button" name="confirmar_transferencia"
		id="confirmar_transferencia" >Transferir Selecionados</button></a> -->
</div>
