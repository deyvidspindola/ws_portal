
<div class="mensagem alerta" id="msgalerta2" style="display: none;"></div>
<div class="modulo_titulo"><?php echo "Contatos Para Instalação/Assistência"; ?></div>
<div class="bloco_conteudo">
<div class="mensagem alerta" id="msgalertaInstalacaoAssistencia" style="display: none;"></div>
	<table class="tableMoldura">

		<tr>
			<td ><label>Nome*:</label></td>
		</tr>
		<tr>
			<td><input type="text" name="prcnome_cont_assist" id="prcnome_cont_assist" size="30"  value="" >
			<input type="hidden" name="ptcioid_cont_assist" id="ptcioid_cont_assist" value="" >
			<input type="hidden" name="id_prop_InstalAssis" id="id_prop_InstalAssis" value="" >
			</td>
			<td>
		</tr>
		<Tr>
			<td><label>Fone residencial*:</label></td>
			<td><label>Fone Comercial:</label></td>
		</tr>
		<tr>

			<td><input type="text" name="prcfone_res_cont_assist" id="prcfone_res_cont_assist"
				size="15" maxLength="14"></td>
			<td><input type="text" name="prcfone_com_cont_assist" id="prcfone_com_cont_assist"
				size="15" maxLength="14"></td>
		</tr>
		<tr>


			<td><label>Fone celular:</label></td>
			<td><label>ID Nextel:</label></td>

		</tr>
		<tr>
			<td><input type="text" name="prcfone_cel_cont_assist" id="prcfone_cel_cont_assist"
				size="15" maxLength="14"></td>
			<td><input type="text" name="prcid_nextel_cont_assist" id="prcid_nextel_cont_assist"
				size="14" maxLength="14" ></td>
		</tr>

		<tr style="width: 5px;">
			<td >
		<?php if(trim($retorno['ptrastatus_conclusao_proposta']) != 'CA' && trim($retorno['ptrastatus_conclusao_proposta']) != 'C' && trim($retorno['ptrastatus_conclusao_proposta']) != 'F') {?>	
					<input class="botao" type="button"
					value="Adicionar" name="bt_add_cont_inst" id="bt_add_cont_inst"/>
					<input class="botao" type="button"
					value="Atualizar" name="bt_atualizar_cont_inst" id="bt_atualizar_cont_inst"/>
						<input class="botao" type="button"
					value="Cancelar" name="bt_cancelar_cont_inst" id="bt_cancelar_cont_inst"/>
		<?php }?>
			</td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
	</table>
	<div class="listagem" id="adicionaContatoAssistencia"> </div>
	<div class="listagem" id="contatoAssistencia">
	<?php
	$resposta = $control->listaContatoInstalacao($id);
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
				$nome = utf8_decode($row['ptcinome']);
				?>
				<tr class="<?=$class?>">
				<input type="hidden" id="idInstalAssis" name="idInstalAssis" value="<?php echo $row[ptciptraoid ];?>" />
					<td style="text-align: center;"><?=$nome?></td>
	                <td style="text-align: center;"><?=$row['ptcifone_residencial']?></td>
	                <td style="text-align: center;"><?=$row['ptcifone_comercial']?></td>
					<td style="text-align: center;"><?=$row['ptcifone_celular']?></td>
					<td style="text-align: center;"><?=$row['ptcidnextel']?></td>

					<td class="acao centro">
			<?php if(trim($retorno['ptrastatus_conclusao_proposta']) != 'CA' && trim($retorno['ptrastatus_conclusao_proposta']) != 'C' && trim($retorno['ptrastatus_conclusao_proposta']) != 'F') {?>
		             <a title=Excluir rel="<?php  echo $row['ptcioid']; ?>" id="btn_excluir_instAssistencia" href="javascript:void(0);">
		           		<IMG class=icone alt=Excluir src="images/icon_error.png"></a>
		           		<a title=Editar rel="<?php  echo $row['ptcioid']; ?>" id="btn_editar_instAssistencia" href="javascript:void(0);">
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