
<div class="modulo_titulo">Pessoas Autorizadas</div>

<div class="bloco_conteudo">
<div class="mensagem alerta" id="msgalertaPessoasAut" style="display: none;"></div>
	<table class="tableMoldura">

		<tr>
			<td width="18%"><label>Nome*:</label></td>
			<td width="18%"><label>CPF*:</label></td>
			<td width="18%"><label>RG*:</label></td>
		</tr>
		<tr>
			<td><input type="text" name="prtcnome_aut" id="prcnome_aut" size="30"
				 value="" />
				
			<input type="hidden" name="ptpaoid_pessoa_aut" id="ptpaoid_pessoa_aut" value="" />
			<input type="hidden" name="id_prop_pessoaAut" id="id_prop_pessoaAut" value="" />	
			</td>
			<td><input type="text" name="prtcpf_aut" id="prccpf_aut" size="14"
				maxlength="11" value=""></td>
			<td><input type="text" name="prtrg_aut" id="prcrg_aut" size="14"
				maxlength="12" value=""></td>
		</tr>
		<Tr>
			<td><label>Fone residencial*:</label></td>
			<td><label>Fone Comercial:</label></td>
		</tr>
		<tr>

			<td><input type="text" name="prtfone_res_aut" id="prcfone_res_aut"
				size="15" maxLength="14"></td>
			<td><input type="text" name="prtfone_com_aut" id="prcfone_com_aut"
				size="15" maxLength="14"></td>
		</tr>
		<tr>
			<td><label>Fone celular:</label></td>
			<td><label>ID Nextel:</label></td>
		</tr>
		<tr>
			<td><input type="text" name="prtfone_cel_aut" id="prcfone_cel_aut"
				size="15" maxLength="14"></td>
			<td><input type="text" name="prtid_nextel_aut" id="prcid_nextel_aut"
				size="14" maxLength="14"></td>
		</tr>
		<tr>
			<td colspan="4"> 
	<?php if(trim($retorno['ptrastatus_conclusao_proposta']) != 'CA' && trim($retorno['ptrastatus_conclusao_proposta']) != 'C' && trim($retorno['ptrastatus_conclusao_proposta']) != 'F') {?>
			<input class="botao" type="button" value="Adicionar" name="bt_add_pessoas_auto" id="bt_add_pessoas_auto"/>
			<input class="botao" type="button" value="Atualizar" name="bt_atualizar_pessoas_auto" id="bt_atualizar_pessoas_auto"/>
			<input class="botao" type="button" value="Cancelar" name="bt_cancelar_pessoas_auto" id="bt_cancelar_pessoas_auto"/>
   <?php }?>
		</td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
	</table>
	<div class="listagem" id="adicionaAutorizaPessoa"> </div>
	<div class="listagem" id="autorizaPessoa">
	<?php
	$resposta = $control->listaPessoa($id);

	if(count($resposta) > 0 && !empty($resposta) || $resposta != null) {?>
		<table>
			<thead>
				<tr>
					<th style="text-align: center;"><?php echo "Nome";?></th>
					<th style="text-align: center;"><?php echo "CPF";?></th>
					<th style="text-align: center;"><?php echo "RG";?></th>
					<th style="text-align: center;"><?php echo "Fone Residencial";?></th>
					<th style="text-align: center;"><?php echo "Fone Comercial";?></th>
					<th style="text-align: center;"><?php echo "Fone Fone Celular";?></th>
					<th style="text-align: center;"><?php echo "ID Nextel";?></th>
					<th style="text-align: center;">Ações</th>
				</tr>
			</thead>
			<tbody>
				<?php 
		
		
				foreach ($resposta as $row) :
				$class = $class == '' ? 'par' : '';
				$nome = utf8_decode($row['ptpanome']);
				?>
				<tr class="<?=$class?>">
				<input type="hidden" id="idContPessoaAut" name="idContPessoaAut" value="<?php echo $row['ptpaptraoid'];?>" />
					<td style="text-align: center;"><?=$nome?></td>
	                <td style="text-align: center;"><?=$row['ptpacpf']?></td>
					<td style="text-align: center;"><?=$row['ptparg']?></td>
	                <td style="text-align: center;"><?=$row['ptpafone_residencial']?></td>
	                <td style="text-align: center;"><?=$row['ptpafone_comercial']?></td>
					<td style="text-align: center;"><?=$row['ptpafone_celular']?></td>
					<td style="text-align: center;"><?=$row['ptpaidnextel']?></td>

					<td class="acao centro">
		<?php if(trim($retorno['ptrastatus_conclusao_proposta']) != 'CA' && trim($retorno['ptrastatus_conclusao_proposta']) != 'C' && trim($retorno['ptrastatus_conclusao_proposta']) != 'F') {?>
		             <a title=Excluir rel="<?php  echo $row['ptpaoid']; ?>" id="btn_excluir_pessoas_aut" href="javascript:void(0);">
		           		<IMG class=icone alt=Excluir src="images/icon_error.png"></a>
		           		<a title=Editar rel="<?php  echo $row['ptpaoid']; ?>" id="btn_editar_pessoas_aut" href="javascript:void(0);">
		           		<IMG class=icone alt=Editar src="images/icon_editar.gif"></a>
	   <?php }?>
					</td>
				</tr>

				    <?php endforeach;  ?>
					<tfoot>
			<tr class='center'>
			<td align='center' colspan='8'>
			</td>
			</tr>
			</tfoot>
			</table>
			<?php  } ?>
	</div>

</div>