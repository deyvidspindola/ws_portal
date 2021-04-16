<?php $token = md5(uniqid("")); ?>
<script type="text/javascript">
                    jQuery(".<?php echo $token; ?>").ajaxfileupload({
                        params: {
                                'uploadarquivo' : '<?php echo $token; ?>'
                            },
                        action: 'cad_questionario_pos_venda.php',
                        onComplete: function(response) {
	                        	if (response == null) 
	                        	{
	                        		var response = jQuery.parseJSON('{"status":"ERRO", "message":"Arquivo Inválido"}');
	                        	} 
	                        	var tipoResposta = response.status;
	                        	var resposta = response.message;
	                        	if (tipoResposta == 'SUCESSO') {
	                        		jQuery(".<?php echo $token; ?>").parent('.questelemento-padrao').children(".arquivoresposta").html('<a href="/arq_questionario/' + resposta + '" target="_blank" >' + resposta + '</a>');	
	                        		jQuery(".<?php echo $token; ?>").parent('.questelemento-padrao').children(".arquivo").val(resposta);	
	                        		jQuery(".<?php echo $token; ?>").closest('.padrao').find(".salvar_questao").removeAttr('disabled');
	                        	} else if (tipoResposta == 'ERRO' || tipoResposta == false) {
	                        		jQuery(".<?php echo $token; ?>").parent('.questelemento-padrao').children(".arquivoresposta").html('<span class="erroarquivo">' + resposta + '</span>');
	                        		jQuery(".<?php echo $token; ?>").parent('.questelemento-padrao').children(".arquivo").val('');	
	                        		jQuery(".<?php echo $token; ?>").closest('.padrao').find(".salvar_questao").attr('disabled', 'disabled');
	                        	}
                        	jQuery(".<?php echo $token; ?>").closest('.questelemento-padrao').find(".loadingtipo").hide();
                        },
                        onStart: function() {
                        	jQuery(".<?php echo $token; ?>").closest('.questelemento-padrao').find(".loadingtipo").show();
                        	jQuery(".<?php echo $token; ?>").closest('.padrao').find(".salvar_questao").attr('disabled', 'disabled');
                        },
                        submit_button:  null
                    });
	
        var elemento = jQuery(".<?php echo $token; ?>").closest('.padrao');
        var tipo = jQuery(".<?php echo $token; ?>").closest('.padrao').find('.pqitipo_item');
        var todosTipos = ["padrao", "radio", "checkbox", "select"];
        var peso = jQuery(':selected', jQuery(tipo)).attr("peso");
        if (peso == 'sempeso') {
            jQuery(elemento).find('.pqiavalia_representante').attr('disabled', 'disabled').removeAttr('checked');
            jQuery(elemento).find('.peso').attr('disabled', 'disabled').val('0'); 
        } else {
            jQuery(elemento).find('.pqiavalia_representante').removeAttr('disabled'); 
            jQuery(elemento).find('.peso').removeAttr('disabled').val(''); 
        }
        jQuery(elemento).show();
</script>
<table class="padrao" width="100%" cellspacing="0" cellpadding="0" style="display:none">
	<input type="hidden" name="token" value="<?php echo $token;?>" class="token">
	<input type="hidden" name="questaoid" value="<?php echo $questaoid;?>" class="questaoid">
	<input type="hidden" name="questionarioid" value="<?php echo $questionarioid;?>" class="questionarioid">
	<input type="hidden" name="pqioid" value="<?php echo $pqioidDados->pqioid;?>" class="pqioid">
	<tbody>
		<tr>
			<td class="titulo" colspan="7">
				<?php echo ($pqioidDados->pqioid != '') ? 'Alterar' : 'Adicionar';?> Questão
			<div class="alertas"></div> </td>
		</tr>
		<tr>
			<td class="form_label"> Tipo Item: </td>
			<td>
				<select class="pqitipo_item" name="pqitipo_item">
					<?php
					foreach ($this->tipoItem() as $chave => $valor) {
					?>
					<option value="<?php echo $chave ?>" <?php echo ($chave == $pqioidDados->pqitipo_item) ? 'selected' : ''?> tipo="<?php echo $valor[1] ?>" peso="<?php echo $valor[2] ?>"><?php echo $valor[0] ?></option>
					<?php
					}
					?>
				</select>
			</td>
			<td class="form_label"> Tipo Ocorrência: </td>
			<td>
				<select class="pqirhtoid" name="pqirhtoid">
                    <option value="">--Escolha--</option>
					<?php
					foreach ($this->ocorrencia() as $chave => $valor) {
					?>
						<option value="<?php echo $chave ?>" <?php echo ($chave == $pqioidDados->pqirhtoid) ? 'selected' : ''?>><?php echo $valor ?></option>
					<?php
					}
					?>
    			</select>
			</td>
			<td class="form_label"> Peso: </td>
			<td>
				<div style="width:143px">
					<input class="peso" type="text" size="3" maxlength="3" value="<?php echo $pqioidDados->pqipeso; ?>" name="pqipeso">
					<span class="pesoerro" style="float:left"></span>
					<span class="subtitulo">Ordem:</span> <input class="pqiitem_ordem" type="text" size="2" maxlength="2" value="<?php echo $pqioidDados->pqiitem_ordem; ?>" name="pqiitem_ordem">
				</div>
			</td>
			<td rowspan="2">
				<input class="botao salvar_questao" type="button" name="<?php echo ($pqioidDados->pqioid != '') ? 'atualizar' : 'adicionar';?>" style="width:60px" value=" <?php echo ($pqioidDados->pqioid != '') ? 'Salvar' : 'Adicionar';?> ">
				<br/>
				<input class="botao cancelar_<?php echo ($pqioidDados->pqioid != '') ? 'altera' : 'questao';?>" type="button" name="cancelar" style="width:60px" value=" Cancelar ">
			</td>
		</tr>
		<tr>
			<td class="form_label"> Descrição: </td>
			<td>
				<textarea cols="50" class="pqiitem_topico" name="pqiitem_topico"><?php echo $pqioidDados->pqiitem_topico; ?></textarea>
			</td>
			<td class="form_label" >
				<div class="questelemento-padrao" <?php echo ($tipoAtivo != 'padrao') ? 'style="display:none"' : '' ?>>
					<input type="checkbox" value="true" <?php echo ($pqioidDados->pqiavalia_representante == 't') ? 'checked' : ''?> class="pqiavalia_representante" name="pqiavalia_representante">
					Avalia Representante
				</div>
				<div  class="addcampoelement"  <?php echo ($tipoAtivo != 'radio' && $tipoAtivo != 'checkbox' && $tipoAtivo != 'select') ? 'style="display:none"' : '' ?>>
					Adicionar Campo:
				</div>
			</td>
			<td class="">
				<div class="questelemento-padrao" <?php echo ($tipoAtivo != 'padrao') ? 'style="display:none"' : '' ?>>
					<textarea cols="50" class="pqidescricao_ocorrencia" name="pqidescricao_ocorrencia"><?php echo $pqioidDados->pqidescricao_ocorrencia; ?></textarea>
				</div>
				<div class="questelemento-radio" <?php echo ($tipoAtivo != 'radio') ? 'style="display:none"' : '' ?>>
					<input type="hidden" name="numradio" class="numradio" value="<?php echo ($numOpc > 0 && $tipoAtivo == 'radio') ? $numOpc : '1'; ?>">
					<input class="botao addradio" type="button" style="width:35px;float: left;margin-top: 9px;" value="+" <?php echo ($pqioidDados->pqioid != '') ? 'disabled' : '';?>>
					<div class="mostra_radios">
						<ul class="lista_radios">
							<?php
							if ($numOpc > 0  && $tipoAtivo == 'radio') {
							foreach ($pqiOpcoes as $chave => $valor) {
								?>
								<li>
									<?php echo $valor ?><br/>
									<input type="radio" name="mostraradio">
								</li>
								<?php 	
								}
							} else {
								?>
								<li>
									1<br/>
									<input type="radio" name="mostraradio">
								</li>
							<?php
							}
							?>
							
						</ul>
					</div>
				</div>
				<div class="questelemento-checkbox" <?php echo ($tipoAtivo != 'checkbox') ? 'style="display:none"' : '' ?>>
					<input type="text" class="textocheck">
					<input class="botao addcheck" type="button" style="width:35px" value="+" <?php echo ($pqioidDados->pqioid != '') ? 'disabled' : '';?>>
					<div class="mostra_checks">
						<ul class="lista_checks">
						<?php 
						if ($numOpc > 0) { 
								foreach ($pqiOpcoes as $chave => $valor) {
								?>
								<li>
									<input type="checkbox" name="mostracheck"><?php echo $valor ?>
								</li>
						<?php
								}
							}
						?>							
						</ul>
					</div>
					<?php if ($numOpc > 0) { 
							foreach ($pqiOpcoes as $chave => $valor) {
							?>
							<input class="numcheck" type="hidden" value="<?php echo $valor ?>" name="numcheck[]">
					<?php
							}
						}
					?>					
				</div>
				<div class="questelemento-select" <?php echo ($tipoAtivo != 'select') ? 'style="display:none"' : '' ?>>
					<input type="text" class="textoselect">
					<input class="botao addselect" type="button" style="width:35px" value="+" <?php echo ($pqioidDados->pqioid != '') ? 'disabled' : '';?>>
					<div class="mostra_selects">
						<select name="mostrarselect" class="lista_selects" <?php echo ($tipoAtivo == 'select' && $numOpc > 0) ? 'size="'.$numOpc.'"' : ''; ?>>
						<?php 
						if ($numOpc > 0) { 
								foreach ($pqiOpcoes as $chave => $valor) {
								?>
								<option value=""><?php echo $valor ?></option>
						<?php
								}
							}
						?>									
						</select>
					</div>
					<?php if ($numOpc > 0) { 
							foreach ($pqiOpcoes as $chave => $valor) {
							?>
							<input class="numselect" type="hidden" value="<?php echo $valor ?>" name="numselect[]">
					<?php
							}
						}
					?>					
				</div>
			</td>
			<td class="form_label" style="vertical-align: top; padding-top: 8px;">
				<div class="questelemento-padrao" <?php echo ($tipoAtivo != 'padrao') ? 'style="display:none"' : '' ?>>
					Imagem: 
				</div>
			</td>
			<td>
				<div class="questelemento-padrao contemarquivo" <?php echo ($tipoAtivo != 'padrao') ? 'style="display:none"' : '' ?>>
					<input type="file" name="arquivo" class="<?php echo $token; ?>">
<br/>
					<img src="images/progress4.gif" class="loadingtipo" style="display:none" />
					<span class="arquivoresposta"><?php echo ($pqioidDados->pqinome_imagem != '') ? '<a href="/arq_questionario/'.$pqioidDados->pqinome_imagem.'" target="_blank" >'.$pqioidDados->pqinome_imagem.'</a>' : '';?></span>
					<input type="hidden" value="" class="arquivo">
				</div>
			</td>
		</tr>
	</tbody>
</table>