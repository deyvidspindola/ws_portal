
<div class="mensagem alerta" id="msgalerta2" style="display: none;"></div>
<div class="modulo_titulo"><?php echo "Contato de Emergência"; ?></div>
<div class="bloco_conteudo">
<div class="mensagem alerta" id="msgalertaPessoaEmergencia" style="display: none;"></div>
	<table class="tableMoldura">

		<tr>
			<td ><label>Nome*:</label></td>
		</tr>
		<tr>
			<td><input type="text" name="prcnome_cont_emerg" id="prcnome_cont_emerg" size="30"  value="" >
			<input type="hidden" name="ptceoid_cont_emerg" id="ptceoid_cont_emerg" value="" >
			<input type="hidden" name="id_prop_contEmerg" id="id_prop_contEmerg" value="" >
			</td>
			<td>
		</tr>
		<Tr>
			<td><label>Fone residencial*:</label></td>
			<td><label>Fone Comercial:</label></td>
		</tr>
		<tr>

			<td><input type="text" name="prcfone_res_cont_emerg" id="prcfone_res_cont_emerg"
				size="15" maxLength="14"></td>
			<td><input type="text" name="prcfone_com_cont_emerg" id="prcfone_com_cont_emerg"
				size="15" maxLength="14"></td>
		</tr>
		<tr>


			<td><label>Fone celular:</label></td>
			<td><label>ID Nextel:</label></td>

		</tr>
		<tr>
			<td><input type="text" name="prcfone_cel_cont_emerg" id="prcfone_cel_cont_emerg"
				size="15" maxLength="14"></td>
			<td><input type="text" name="prcid_nextel_cont_emerg" id="prcid_nextel_cont_emerg"
				size="14" maxLength="14"></td>
		</tr>

		<tr>
			<td colspan="4"> 
	<?php if(trim($retorno['ptrastatus_conclusao_proposta']) != 'CA' && trim($retorno['ptrastatus_conclusao_proposta']) != 'C' && trim($retorno['ptrastatus_conclusao_proposta']) != 'F') {?>
			<input class="botao" type="button" value="Adicionar" name="bt_add_cont_emerg" id="bt_add_cont_emerg"/>
			<input class="botao" type="button" value="Atualizar" name="bt_atualizar_cont_emerg" id="bt_atualizar_cont_emerg"/>
			<input class="botao" type="button" value="Cancelar" name="bt_cancelar_cont_emerg" id="bt_cancelar_cont_emerg"/>
	<?php }?>
		</td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
	</table>
	<div class="listagem" id="adicionaContatoEmergencia"> </div>
	<div class="listagem" id="contatoEmergencia">
	<?php
	$resposta = $control->listaContatoEmergencia($id);
	if(count($resposta) > 0 && !empty($resposta) || $resposta != null) {?>
		<table>
			<thead>
				<tr>
					<th style="text-align: center;"><?php echo "Nome";?></th>
					<th style="text-align: center;"><?php echo "Fone Residencial";?></th>
					<th style="text-align: center;"><?php echo "Fone Comercial";?></th>
					<th style="text-align: center;"><?php echo "Fone Fone Celular";?></th>
					<th style="text-align: center;"><?php echo "ID Nextel";?></th>
					<th style="text-align: center;"><?php echo 'Ações';?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
		
			
				foreach ($resposta as $row) :
				$class = $class == '' ? 'par' : '';
				$nome = utf8_decode($row['ptcenome']);
				?>
				<tr class="<?=$class?>">
				<input type="hidden" id="idContEmerg" name="idContEmerg" value="<?php echo $row[ptceptraoid ];?>" />
					<td style="text-align: center;"><?=$nome?></td>
	                <td style="text-align: center;"><?=$row['ptcefone_residencial']?></td>
	                <td style="text-align: center;"><?=$row['ptcefone_celular']?></td>
					<td style="text-align: center;"><?=$row['ptcefone_comercial']?></td>
					<td style="text-align: center;"><?=$row['ptceidnextel']?></td>

					<td class="acao centro">
		<?php if(trim($retorno['ptrastatus_conclusao_proposta']) != 'CA' && trim($retorno['ptrastatus_conclusao_proposta']) != 'C' && trim($retorno['ptrastatus_conclusao_proposta']) != 'F') {?>			
		             <a title=Excluir rel="<?php  echo $row['ptceoid']; ?>" id="btn_excluir_contemerg" href="javascript:void(0);">
		           		<IMG class=icone alt=Excluir src="images/icon_error.png"></a>
		           		<a title=Editar rel="<?php  echo $row['ptceoid']; ?>" id="btn_editar_contemerg" href="javascript:void(0);">
		           		<IMG class=icone alt=Editar src="images/icon_editar.gif"></a>
		<?php }?>
					</td>
				</tr>

				    <?php endforeach;  ?>
					<tfoot>
			<tr class='center'>
			<td align='center' colspan='6'>
			</td>
			</tr>
			</tfoot>
			</table>
			<?php  } ?>
	</div>
</div>