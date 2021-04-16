<?php
if ($retornoContratos && count ( $retornoContratos ) > 0) :

	?>

<div class="bloco_titulo">Contratos Para Transferência</div>
<div class="bloco_conteudo">

	<div class="listagem">
		<table>
			<thead>
				<tr>
					<th style="text-align: center;"><?php echo "Cliente";?></th>
					<th style="text-align: center;"><?php echo "Contrato";?></th>
					<th style="text-align: center;"><?php echo "Placa";?></th>
					<th style="text-align: center;"><?php echo "Status";?></th>
				</tr>
			</thead>
			<tbody>	
					<?php
	
	foreach ( $retornoContratos as $row => $key ) :


		$class = $class == '' ? 'par' : '';
		?>						
					<tr class="<?=$class?>">
					<td style="text-align: center;"><?php echo utf8_decode($key['clinome'])?></td>
					<td style="text-align: center;"><?=$key['connumero']?></td>
					<td style="text-align: center;"><?=$key['veiplaca']?></td>
					<td style="text-align: center;" class="<?php echo $key['csidescricao']; ?>"><?php echo $key['csidescricao'];?></td>
				</tr>						
					<?php endforeach; ?>
				</tbody>
			<tfoot>

				<tr class="center">
					<td align="center" colspan="5"></td>
				</tr>

			</tfoot>
		</table>


	</div>

	<table class="tableMoldura">
		<tr>
			<td><label for="cliente">Tefone 1*</label></td>
		</tr>
		<tr>
			<td><input type="text" id="telefone1_titular" name="telefone1_titular" value="<?php echo $retorno['ptrafone_tit_anterior'];?>" /></td>

		</tr>
		<tr>
			<td><label for="cliente">Telefone 2*</label></td>
		</tr>
		<tr>
			<td><input type="text" id="telefone2_titular"
						name="telefone2_titular" value="<?php echo $retorno['ptrafone2_tit_anterior'];?>" /></td>
		</tr>
		<tr>
			<td><label for="pesq_campo1">E-Mail*</label></td>
		</tr>
		<tr>
			<td><input type="text" id="email_titular" name="email_titular"
				value="<?php echo utf8_decode($retorno['ptraemail_tit_anterior']);?>" class="campo" size="50" /></td>
		</tr>
		<tr>
			<td><label for="pesq_campo1"><?php echo "Responsável*";?></label></td>
		</tr>
		<tr>
			<td><input type="text" id="responsavel_titular" name="responsavel_titular"
				value="<?php echo utf8_decode($retorno['ptraresp_tit_anterior']);?>" class="campo" size="50" /></td>
		</tr>
	</table>
<?php else:  ?>
<div class="mensagem info">Não foram encontrados registros</div>
<?php endif;  ?>
</div>
<div class="separador"></div>
<div class="bloco_titulo">Dados Para Contato</div>
<div class="bloco_conteudo">

	<table class="tableMoldura">
	<tr>
	<td colspan="2" align="left">
	
	 	<?php if(isset($retorno['ptramotivo_reprov_analise_credito']) && !empty($retorno['ptramotivo_reprov_analise_credito'])){?>
	 	 <fieldset>
	 	<?php 	echo "Análise de Crédito não aprovado por ".$retorno['nm_usuario']." pelo motivo: ".utf8_decode($retorno['ptramotivo_reprov_analise_credito']); ?>
	 	 </fieldset>
	 	<?php }?>
	
	</td>
	</tr>
		<tr>
			<td><label for="cliente">CPF/CNPJ*</label></td>
			
		</tr>
		<tr>
			<td>
			<?php 
				if(strlen($retorno['ptrano_documento']) == 10) {
					$numeroDocumento = "0".$retorno['ptrano_documento'];
				}else if($retorno['ptrano_documento']== 13){
					$numeroDocumento = "0".$retorno['ptrano_documento'];
				}else{
					$numeroDocumento = $retorno['ptrano_documento'];
				}
			
			?>
			<input type="text" id="cnpjcpf" name="cnpjcpf" value="<?php echo $numeroDocumento;?>" /></td>
		</tr>

		<tr>
			<td><label for="cliente"><?php echo "Nome /Razão Social*";?></label></td>
		</tr>
		<tr>
			<td><input type="text" id="nomerazaosocial" name="nomerazaosocial"
				value="<?php echo utf8_decode($retorno['ptranome']);?>" size="50" /></td>

		</tr>
		<tr>
			<td><label for="cliente">Nome para Contato*</label></td>
		</tr>
		<tr>
			<td><input type="text" id="contato" name="nomerazaosocial" value="<?php echo utf8_decode($retorno['ptranome_contato']);?>"
				size="50" /></td>

		</tr>
		<Tr>
		  <td>
        <spam style="font-size:11"> Telefone para Contato (1)* </spam> &nbsp; &nbsp; &nbsp;
		<spam style="font-size:11">	Telefone para Contato (2)*  </spam> 
  </td>
		</tr>
		<tr>
<td><input type="text" maxlength="14" size="15" id="contato1" name="contato1" value="<?php echo $retorno['ptrafone_contato1'];?>"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  
<input type="text" id="contato2" maxlength="14" size="15" name="contato2" value="<?php echo $retorno['ptrafone_contato2'];?>"></td>
		</tr>
		<tr>
			<td><label for="cliente">E-Mail para Contato*</label></td>
		</tr>
		<tr>
			<td><input type="text" id="email" name="email" value="<?php echo utf8_decode($retorno['ptracontato_email']);?>" size="50" /></td>

		</tr>
		<tr>
			<td><label for="cliente">Resultado Serasa</label></td>
		</tr>
		<tr>
			<td><textarea name="resultado_serasa" id="resultado_serasa" rows="10" cols="80"><?php echo utf8_decode($retorno['ptraresultado_serasa']);?></textarea></td>

		</tr>
	</table>

</div>

<?php
if ($dados && count ( $dados ) > 0) :
	?>
<div class="separador"></div>
<div class="bloco_titulo"><?php echo utf8_encode("Titulos Pendentes dos Contratos Selecionados");?></div>
<div class="bloco_conteudo">
	<div class="listagem">
		<table>
			<thead>
				<tr>
					<th style="text-align: center;"><?php echo "Nota";?></th>
					<th style="text-align: center;"><?php echo "Contrato";?></th>
					<th style="text-align: center;"><?php echo "Vencimento";?></th>
					<th style="text-align: center;"><?php echo "valor da NF/Parcela";?></th>
					<th style="text-align: center;"><?php echo "Valor Do Contrato";?></th>
				</tr>
			</thead>
			<tbody>	
					<?php
	
	foreach ( $dados as $row => $key ) :
		
		$class = $class == '' ? 'par' : '';
		?>						
					<tr class="<?=$class?>">
					<td style="text-align: center;"><?=$key['nflno_numero']?>/<?=$key['nfiserie']?></td>
					<td style="text-align: center;"><?=$key['connumero']?></td>
					<td style="text-align: center;"><?=$key['titdt_vencimento']?></td>
					<td style="text-align: center;"><?=$key['nflvl_total']?></td>
					<td style="text-align: center;"><?=$key['valor_contrato']?></td>
				</tr>						
					<?php endforeach; ?>
				</tbody>
			    <tfoot>
        
                    <tr class="center">
                        <td align="center" colspan="5">
                              
                        </td>
                    </tr>
                     
                </tfoot>
		</table>
	</div>
	<div class="separador"></div>


	<table class="tableMoldura">
		<tr><td>
		 	<?php if(isset($retorno['ptramotivo_reprov_analise_divida']) && !empty($retorno['ptramotivo_reprov_analise_divida'])){
	 		?>
	 		<fieldset>
	 		<?php echo "Transferência de divida não autorizada por ".$retorno['nm_usuario']." com a seguinte observação: ".utf8_decode($retorno['ptramotivo_reprov_analise_divida']);?>
	 		 </fieldset>
	 	<?php }?>
		</td></tr>
		<tr>
			<td><label for="cliente">Motivo Transferência*</label></td>
		</tr>
		<tr>
			<td><textarea name="motivo_Transferencia" id="motivo_Transferencia" rows="10" cols="80"><?php echo utf8_decode($retorno['ptramotivo_trans']);?></textarea></td>

		</tr>

	</table>
	
</div>
	
	<?php endif;  ?>