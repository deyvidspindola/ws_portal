<center>
	<table class="tableMoldura">
		<tr id = "layoutEmails" class="tableTitulo" >
	        <td><h1>Layout de Emails </h1></td>
	    </tr>

	    <tr id = "layoutSms"  name="layoutSms"class="tableTitulo" >
	        <td><h1>Layout de Emails/SMS </h1></td>
	    </tr>

	    <tr height="20">
			<td><span id="div_msg" class="msg">
				<? echo ($this->hasFlashMessage()) ? $this->flashMessage() : '' ?>
			</span></td>
		</tr>


		<tr>
	        <td align="center">
	            <table class="tableMoldura">
	            	<form method="post" id="editar_layout_emails" enctype="multipart/form-data"
	            		action="<?= $_SERVER['PHP_SELF'] . '?acao=' . $_GET['acao'] ?>">

	            		<input type="hidden" name="seeoid" id="seeoid" value="<?= $form['seeoid'] ?>" />

		                <tr class="tableSubTitulo">
		                    <td colspan="4"><h2>Dados principais</h2></td>
		                </tr>

		            	<tr>
		            		<td><br></td>
		            	</tr>

		            	<tr >
		            	    <td width="15%"><label>Tipo de Envio: *</label></td>
						   <td >

						       <?
                      			 $selected = ' selected="selected"';
								?>
						        <select name="seetipo" id="seetipo">
							         <option value="E" <?if($form['seetipo'] == "E") echo $selected;?>>Email</option>
							         <option value="S" <?if($form['seetipo'] == "S") echo $selected;?>>SMS</option>
						        </select>
					     	</td>
					    </tr>

	         			</tr>
			                <tr >
		                    <td width="15%"><label>Funcionalidade: *</label></td>
		                    <td>
		                    	<select name="seeseefoid" id="seeseefoid">
		                    		<option value="0">Escolha</option>
		                    		<? foreach ($funcionalidades as $item): ?>
		                    			<option value="<?= $item['seefoid'] ?>" <?= ($form['seeseefoid'] === $item['seefoid']) ? 'selected="selected"' : '' ?>>
		                    				<?= $item['seefdescricao']?>
		                    			</option>
		                    		<? endforeach ?>
		                    	</select>
		                    </td>

		                    <td id="remetente" width="15%"><label>Remetente:</label></td>
		                    <td>
		                    	<input type="text" name="seeremetente" id="seeremetente" size="50" maxlength="50" value="<?= $form['seeremetente'] ?>" />
		                    </td>
		                </tr>

		                <tr>
		                	<td width="15%"><label for="seeseetoid">Título do Layout: *</label></td>
		                    <td>
		                    	<!-- OLD <input type="text" name="seecabecalho" size="50" value="<?= $form['seedescricao'] ?>" maxlength="50" />-->
		                    	<input type="hidden" name="titulo_layout_select" id="titulo_layout_select" value="<?= ($form['seeseetoid'] ? $form['seeseetoid'] : ""); ?>">
		                    	<select name=seeseetoid id="seeseetoid" class="titulo_layout">
		                    		<option value="">--Escolha A Funcionalidade--</option>
		                    	</select><img src="images/progress4.gif" id="loading_titulo" style="display:none;" />
		                    </td>

		                    <td width="15%"><label>Tipo de Proposta:</label></td>
		                    <td>
		                    	<select style="width: 350" name="tppoid" id="tppoid">
		                    		<option value="" >Selecione</option>
		                    		<? foreach ($tipoProposta as $item): ?>
		                    			<option value="<?= $item['tppoid'] ?>" <?= ($form['seetppoid'] === $item['tppoid']) ? 'selected="selected"' : '' ?>>
		                    				<?= $item['tppdescricao']?>
		                    			</option>
		                    		<? endforeach ?>
		                    	</select>
		                    </td>
		                </tr>

		                <tr>
		                    <td width="15%"><label>Objetivo: *</label></td>
		                    <td>
		                    	<input type="text" name="seeobjetivo" id="seeobjetivo" size="50" maxlength="50" value="<?= $form['seeobjetivo'] ?>" />
		                    </td>

		                    <td width="15%"><label for="lconftppoid_sub"  class="sub_tipo_pro" style="display:none;">Subtipo de Proposta:</label></td>
		                    <td>
		                    	<input type="hidden" name="sub_tipo_pro_select" id="sub_tipo_pro_select" value="<?= ($form['seetlconftppoid_sub'] ? $form['seetlconftppoid_sub'] : ""); ?>">
		                    	<select name="lconftppoid_sub" id="lconftppoid_sub" class="sub_tipo_pro" style="display:none;">
		                    		<option value=""></option>
		                    	</select><img src="images/progress4.gif" id="loading_tipo" style="display:none;" />
		                    </td>
		                </tr>

		                <tr >
		                	<td width="15%"><label name="assunto" id="assunto" >Assunto do E-mail: *</label></td>
		                    <td>
		                    	<input name="seecabecalho" id="seecabecalho" type="text"  size="50" maxlength="100" value="<?= $form['seecabecalho'] ?>" />
		                    </td>

		                    <td width="15%" ><label>Tipo de Contrato:</label></td>
		                    <td  >
		                    	<select  style="width: 350" name="tpcoid" id="tpcoid" >
		                    		<option  value="" >Selecione</option>
		                    		<? foreach ($tipoContrato as $item): ?>
		                    			<option value="<?= $item['tpcoid'] ?>" <?= ($form['seetpcoid'] === $item['tpcoid']) ? 'selected="selected"' : '' ?>>
		                    				<?= $item['tpcdescricao']?>
		                    			</option>
		                    		<? endforeach ?>
		                    	</select>
		                    </td>

		                </tr>

		                <tr>
		                	<td colspan="2"  style="width: 650">
		                	<label>Deseja que este seja o layout padrão para funcionalidade escolhida?</label>
		                    	<input  type="radio" name="seepadrao" value="t" id="seepadrao1"
		                    		<?= ($form['seepadrao'] == 't') ? 'checked="checked"' : '' ?> ></input>
		                    	<label for="seepadrao1">Sim</label>

		                    	<input type="radio" name="seepadrao" value="f" id="seepadrao2"
		                    		<?= ($form['seepadrao'] == 'f') ? 'checked="checked"' : ((!$form['seeoid']) ? 'checked="checked"' : ''); ?> />
		                    	<label for="seepadrao2">Não</label>
		                    </td>

		                    <td width="15%"><label name="servidor" id="servidor">Servidor: *</label></td>
		                    <td>
		                    	<select name="srvoid" id="srvoid">
		                    		<option value="">Selecione</option>
		                    		<? foreach ($servidores as $item): ?>
		                    			<option value="<?= $item['srvoid'] ?>" <?= ($form['seesrvoid'] === $item['srvoid']) ? 'selected="selected"' : '' ?>>
		                    				<?= $item['srvdescricao']?>
		                    			</option>
		                    		<? endforeach ?>
		                    	</select>
		                    </td>
		                </tr>

		                <tr>
		                	<td width="15%"><label id="importar" name="importar">Importar imagem</label></td>
		                    <td colspan="3">
		                    	<input type="file" id="seeimagem" name="seeimagem" value="" />
		                    </td>
		                </tr>

		                <tr>
		                	<td width="15%"></td>
		                    <td colspan="3">
		                    	<input type="checkbox" id="seeimagem_anexo" name="seeimagem_anexo" value="t"
		                    		<?= ($form['seeimagem_anexo'] == 't') ? 'checked="checked"' : '' ?> />
		                    	<label for="seeimagem_anexo" id="seeimagem_anexo_label">Incluir como anexo</label>
		                    </td>
		                </tr>

                        <? if (strlen($form['seeimagem'])): ?>
                        <tr class="imagem-atual">
		                	<td width="15%">Imagem atual</td>
                            <td colspan="3">
                                <a href="<?= ('images/layout_email/' . $form['seeimagem']) ?>" target="_blank">
                                    <?= $form['seeimagem'] ?>
                                </a>

                                <span class="excluir-imagem">
                                    [<a data-seeoid="<?= $form['seeoid'] ?>">X</a>]
                                </span>
		                    </td>
		                </tr>
                        <? endif ?>




                       <tr id="corpoSms">
		                	<td  style="width: 400"valign="top"><label>Texto padrão: * (máximo   120 caracteres)</label></td>

		                    <td colspan="3" >
		                    	<textarea  name="seecorpoSms" id="seecorpoSms" maxlength="120" rows="5"  style="text-align: left; width: 380px; padding-top: 10px; padding-bottom: 10px;  font-size: 11px; "><?= $form['seecorpo'] ?></textarea>
		                    </td>
		                </tr>

		                <tr id="char-countSms">
		                	<td width="15%"></td>
		                    <td colspan="3">
		                    	<div style="text-align: right; width: 380; padding-top: 10px; padding-bottom: 10px;">Você ainda pode digitar <span id="countSms">120</span> caracteres.</div>
		                    </td>
		                </tr>



		               <tr id="seecorpoId">
		                	<td width="15%" valign="top"><label>Texto padrão: * (máximo 3 mil caracteres)</label></td>
		                    <td colspan="3">
		                    	<textarea name="seecorpo" id="seecorpo" maxlength="50" class="editor"><?= $form['seecorpo'] ?></textarea>
		                    </td>
		                </tr>

		                <tr>
		                	<td width="15%"></td>
		                    <td colspan="3">
		                    	<div id="msg_texto" style="text-align: left; width: 700px; padding-top: 20px; padding-bottom: 20px; font-weight: bold; font-size: 9px; display: none;"></div>
		                    </td>
		                </tr>



		                <tr id="char-countId">
		                	<td width="15%"></td>
		                    <td colspan="3">
		                    	<div style="text-align: right; width: 700px; padding-top: 10px; padding-bottom: 10px;">Você ainda pode digitar <span id="char-count">3000</span> caracteres.</div>
		                    </td>
		                </tr>



		                <tr id="legSms">
		                	<td width="15%"><label>Legenda:</label></td>
		                </tr>
		                <tr id="osSms">
		                	<td width="15%" colspan="3"><label >[ordem_servico]:número da Ordem de Serviço </label></td>
		                </tr>
		                <tr id="placaSms">
		                	<td width="15%" colspan="3"><label >[placa]:número da placa do veículo </label></td>
		                </tr>
		                <tr id="dtAgendamentoSms">
		                	<td width="15%" colspan="3"><label>[dataos]:data de agendamento da Ordem de Serviço </label></td>
		                </tr>
		                <tr id="hmSms">
		                	<td width="15%" colspan="3"><label>[hora]:hora e minutos do agendamento da Ordem de Serviço </label></td>
		                </tr>
		                <tr id="legendaNumeroContrato">
		                	<td width="15%" colspan="3"><label>[contrato]:número do contrato </label></td>
		                </tr>
		                <tr>
		                	<td width="15%" colspan="3"><br></td>
		                </tr>


		                <tr class="tableRodapeModelo1" style="height:23px;">
		                    <td align="center" colspan="4">

                                    <input type="submit" name="bt_novo" id="bt_novo" value="Confirmar" class="botao" >

                                <? if (isset($form['seeoid'])): ?>
                                    <a id="bt_visualizar" class="botao" target="_blank"
                                        href="cad_layout_emails.php?acao=visualizar&seeoid=<?= $form['seeoid'] ?>">
                                        Visualizar
                                    </a>
                                <? endif ?>

		                        <a id="bt_retornar" class="botao" href="<?= $_SERVER['SCRIPT_NAME'] ?> ">Retornar</a>
		                    </td>
		                </tr>
		            </form>
	            </table>
	        </td>
	    </tr>
	</table>
</center>

<?php if ( isset($form['seetipo']) && $form['seetipo']	 != '' ) :?>
<script type="text/javascript">
	alterarLayout('<?php echo $form['seetipo'];?>');
</script>
<?php endif;?>
