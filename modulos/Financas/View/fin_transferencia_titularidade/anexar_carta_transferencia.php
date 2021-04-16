<div class="mensagem alerta" id="msgalerta2" style="display: none;"></div>
<div class="modulo_titulo"><?php echo "Carta Anexo"; ?></div>
<div class="bloco_conteudo">
<div class="mensagem alerta" id="msgalertaaddarquivocarta" style="display: none;"></div>
	<form id="formularioAnexoCarta" name="formularioAnexoCarta" method="post" enctype="multipart/form-data">

	<table class="tableMoldura">

		<tr>
			<td ><label>Arquivo*:</label></td>
		</tr>
		<tr>
			<td><input type="file" value="nenhum arquivo selecionado" size="20" name="arqAnexoReqCarta" id="arqAnexoReqCarta" class="form_field"></td>
			<td>
		</tr>
		<Tr>
			<td><label><?php echo "Descrição*";?></label></td>
		</tr>
		<tr>

			<td>
			
			<input type="text" name="arqAnexoReqDescricaoCarta" id="arqAnexoReqDescricaoCarta"  size="40" maxlength="100"></td>
			
		</tr>

		<tr>
			<td colspan="4">
			<?php if(trim($retorno['ptrastatus_conclusao_proposta']) != 'CA' && trim($retorno['ptrastatus_conclusao_proposta']) != 'C' && trim($retorno['ptrastatus_conclusao_proposta']) != 'F') {?>
				<input class="botao"
				type="button" value="Adicionar" name="bt_add_arquivos_carta" id="bt_add_arquivos_carta"/>
			<?php }?>
			</td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
	</table>

	</form>
	<div class="listagem" id="listaCartas"> </div>
	<div class="listagem" id="anexosCartas">
	<?php
	$respostaCarta = $control->listaAnexosCarta($id);
	
	if(count($respostaCarta) > 0 && !empty($respostaCarta) || $respostaCarta != null) {?>

	<table>
	<thead>
	<tr>
				<th style='text-align: center';>Arquivo</th>
				<th style='text-align: center';>Descrição</th>
				<th style='text-align: center';>Data</th>
				<th style='text-align: center';>Usuário</th>
					<th style="text-align: center;"><?php echo 'Ações';?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
		
				$class = '';
				foreach ($respostaCarta as $row) :
				$descricao = utf8_decode($row['ptadescricao']);
				$nome = utf8_decode($row['nm_usuario']);
				
				$class = $class == '' ? 'par' : '';
				?>
				<tr class="<?=$class?>">
					<input type="hidden" id="idCarta" name="idCarta" value="<?php echo $row[ptaoid];?>" />
					<td style="text-align: center;"><a title=Downloads target="_blank" href="download.php?arquivo=<?php echo _SITEDIR_ ."faturamento/transferencia_titularidade/".$row['ptanm_arquivo']; ?>"><?=utf8_decode($row['ptanm_arquivo'])?></a></td>
	                <td style="text-align: center;"><?=$descricao?></td>
	                <td style="text-align: center;"><?=$row['data']?></td>
					<td style="text-align: center;"><?=$nome?></td>

					<td class="acao centro">
					<?php if(trim($retorno['ptrastatus_conclusao_proposta']) != 'CA' && trim($retorno['ptrastatus_conclusao_proposta']) != 'C' && trim($retorno['ptrastatus_conclusao_proposta']) != 'F') {?>
		             <a title=Excluir rel="<?php  echo utf8_decode($row['ptanm_arquivo']); ?>" id="btn_excluir_carta" href="javascript:void(0);">
		           		<IMG class=icone alt=Excluir src="images/icon_error.png"></a>
		           		<?php }?>
					</td>
				</tr>

			
			    <?php endforeach;  ?>
			    
				<tfoot>
			<tr class='center'>
			<td align='center' colspan='5'>
			</td>
			</tr>
			</tfoot>
		</table>
			<?php  } ?>
			   
	</div>
	</div>
