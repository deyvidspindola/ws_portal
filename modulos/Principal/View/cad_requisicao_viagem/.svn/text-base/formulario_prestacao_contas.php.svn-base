<div class="separador"></div>

<div id="mensagem_alerta_prestacao" class="mensagem alerta invisivel"></div>
<div id="mensagem_sucesso_prestacao" class="mensagem sucesso invisivel"></div>

<?php 
	$idRequisicao = (isset($_GET['idRequisicao']) && !empty($_GET['idRequisicao'])) ? $_GET['idRequisicao'] : $this->view->parametros->idRequisicao;
        $classe = ($_SESSION['prestacao_contas'][$idRequisicao]['mensagem_prestacao_de_contas']) ? '' : 'invisivel';
        $msg = ($_SESSION['prestacao_contas'][$idRequisicao]['mensagem_prestacao_de_contas']) ? $_SESSION['prestacao_contas'][$idRequisicao]['mensagem_prestacao_de_contas'] : '';
 ?>
<div id="alerta_prestacao" class="mensagem sucesso <?php echo $classe ?>">
<?php 
    echo $msg; 
    $_SESSION['prestacao_contas'][$idRequisicao]['mensagem_prestacao_de_contas'] = "";

?>
</div>

<div class="bloco_titulo">Prestação de Contas</div>
	<input type="hidden" name="adioid" id="adioid" value="<?php echo $idRequisicao ?>">
    <input type="hidden" name="reembolso" id="reembolso" value="<?php echo isset($_GET['reembolso']) ? $_GET['reembolso'] : '' ?>">
	
    <div class="bloco_conteudo">
        <div class="formulario">
                <div class="campo data">
                    <label for="adigdt_despesa">Data da Despesa *</label>
                    <input <?php echo $this->view->parametros->desabilitarCamposPrestacaoContas;?> id="adigdt_despesa" name="adigdt_despesa" maxlength="10" value="" class="campo" />
                </div>
                <div class="clear"></div>

                <div class="campo medio">
                    <label for="adigtdpoid">Tipo da Despesa *</label>
                    <select <?php echo $this->view->parametros->desabilitarCamposPrestacaoContas;?> id="adigtdpoid" name="adigtdpoid">
                        <option value="">Escolha</option>
                         <?php foreach ($this->view->parametros->tipoDespesa as $despesa) : ?>
                        <option value="<?php echo $despesa->tdpoid . "|" . $despesa->tdpdescricao ?>">
                            <?php echo $despesa->tdpdescricao ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                </div>
                <div class="clear"></div>

                <div class="campo menor">
                    <label for="adigvalor_unitario">Valor *</label>
                    <input style="text-align: right;" maxlength="9" <?php echo $this->view->parametros->desabilitarCamposPrestacaoContas;?> id="adigvalor_unitario" name="adigvalor_unitario" value="" class="campo" type="text" />
                </div>
                <div class="clear"></div>

                <div class="campo menor">
                    <label for="adignota">Número da Nota *</label>
                    <input maxlength="9" <?php echo $this->view->parametros->desabilitarCamposPrestacaoContas;?> id="adignota" name="adignota" value="" class="campo" type="text" />
                </div>
                <div class="clear"></div>

                <div class="campo maior">
                    <label for="adigobs" id="obs_despesa">Observações</label>
                    <textarea <?php echo $this->view->parametros->desabilitarCamposPrestacaoContas;?> id="adigobs" name="adigobs" rows="5"></textarea>
                </div>
                <div class="clear"></div>
        </div>
    </div>
    <div class="bloco_acoes">
    	<?php if ($_SESSION['prestacao_contas'][$idRequisicao]['registro_inserido']) $disabled = "disabled='disabled'"; ?>
        <button type="button" <?php echo $this->view->parametros->desabilitarCamposPrestacaoContas;?> class="<?php echo $disabled; ?>" id="bt_adicionar" name="bt_adicionar">Adicionar</button>
        <button type="button" <?php echo $this->view->parametros->desabilitarCamposPrestacaoContas;?> class="<?php echo $disabled; ?>" id="bt_limpar_prestacao" name="bt_limpar_prestacao" class="botao" />Limpar</button>
    </div>

<?php 

if ($_SESSION['prestacao_contas'][$idRequisicao]['itens']) { ?>
<div id="items_adicionados">
	<input type="hidden" name="chave" id="chave">
	<input type="hidden" name="flag_registro_bd" id="flag_registro_bd" value="<?php echo $_SESSION['prestacao_contas'][$idRequisicao]['flag_registro_bd']?>">
    <div class="resultado bloco_conteudo">
            <div class="listagem">
                <table>
                    <thead>
                        <tr>
                            <th class="medio">Data</th>
                            <th class="maior">Tipo de Despesa</th>
                            <th class="medio">Número da Nota</th>
                            <th class="medio">Valor</th>
                            <th class="menor">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                    	 <?php
                            if ($_SESSION['prestacao_contas'][$idRequisicao]['itens']) : 
                            	foreach ($_SESSION['prestacao_contas'][$idRequisicao]['itens'] as $chave => $itens) :

									if($class=="impar"){
                                    	$class="par";
                                    	$path = 'tf2';
                                    }else{
                                    	$class="impar";
                                    	$path = 't2';
                                    }
                         ?>

						<tr class="<?php echo $class ?>">
                        	<td class="centro">
                            	<?php echo $itens['data_despesa']; ?>
                                	<input type="hidden" name="in_data_despesa_<?php echo $chave ?>" id="in_data_despesa_<?php echo $chave ?>" value="<?php echo $itens['data_despesa'] ?>" />
                            </td>
                            <td>
								<?php echo $itens['tipo_despesa'] ?>
                                <input type="hidden" name="in_tipo_despesa_<?php echo $chave ?>" id="in_tipo_despesa_<?php echo $chave ?>" value="<?php echo $itens['chave_tipo_despesa'] ?>" />
                            </td>
                            <td class="direita">
                            	<?php echo $itens['numero_nota'] ?>
                                <input type="hidden" name="in_numero_nota_<?php echo $chave ?>" id="in_numero_nota_<?php echo $chave ?>" value="<?php echo $itens['numero_nota'] ?>" />
                            </td>
                            <td class="direita">
                            	<?php echo number_format($itens['valor_despesa'], 2, ',', '.'); ?>
                                <input type="hidden" name="in_valor_despesa_<?php echo $chave ?>" id="in_valor_despesa_<?php echo $chave ?>" value="<?php echo number_format($itens['valor_despesa'], 2, ',', '.'); ?>" />
                            </td>
                            	<td style="display: none;" width="8%" align="center">
                                	<input type="hidden" name="in_observacao_prestacao_contas_<?php echo $chave ?>" id="in_observacao_prestacao_contas_<?php echo $chave ?>" value="<?php echo utf8_decode($itens['observacao_prestacao_contas']) ?>" />
                                </td>
                            <td class="centro">
                            	<?php
                                	if (!isset($this->view->parametros->statusRequisicao) || ($this->view->parametros->statusRequisicao == 'S' && $this->view->parametros->permissaoEdicao == 'Solicitante') || ($_SESSION['funcao']['visualizar_requisicao_viagem'] == 1 && $this->view->parametros->statusRequisicao == 'F')) : ?>
                                    	<a title="Editar despesa" href="javascript:void(0)" class="editarItem" id-item="<?php echo $chave ?>">
                                        	<img style="border: none !important;" alt="Editar despesa" src="images/edit.png" width="20" /></a>
                                            &nbsp;
                                            <a title="Excluir despesa" href="javascript:void(0)" class="excluirItem" id-item="<?php echo $chave ?>">
                                            <img style="border: none !important;" alt="Excluir despesa" src="images/icones/<?php echo $path ?>/error.jpg" width="20" /></a>
                                            <?php
                                     endif; ?>
                            </td>
                     	</tr>
						<?php
                        	endforeach; ?>
                       <tfoot>
                       		<tr>
                            	<td colspan="5" class="rodape">
								<center>
                                    	Valor do Adiantamento: R$
                                        	<span id="div_total_adiantamento">
                                            	<?php echo number_format($_SESSION['prestacao_contas'][$idRequisicao]['valor_total_adiantamento'], 2, ',', '.'); ?>
                                            </span>
                                            <input type="hidden" value="<?php echo $_SESSION['prestacao_contas'][$idRequisicao]['valor_total_adiantamento'] ?>" name="total_adiantamento" id="total_adiantamento" />
                                    
									 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    	Total das Despesas: R$
                                        	<span id="div_total_despesas">
                                            	<?php echo number_format($_SESSION['prestacao_contas'][$idRequisicao]['valor_total_despesas'], 2, ',', '.'); ?>
                                            </span>
                                            <input type="hidden" value="<?php echo $_SESSION['prestacao_contas'][$idRequisicao]['valor_total_despesas'] ?>" name="total_despesas" id="total_despesas" />
                                    
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    	Total à Receber: R$
										<span id="div_total_receber">
                                        	<?php echo number_format($_SESSION['prestacao_contas'][$idRequisicao]['valor_total_receber'], 2, ',', '.'); ?>
                                       	</span>
                                        <input type="hidden" value="<?php echo $_SESSION['prestacao_contas'][$idRequisicao]['valor_total_receber'] ?>" name="total_receber" id="total_receber" />
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    	Total à Devolver: R$
                                        	<span id="div_total_devolver">
                                            	<?php echo number_format($_SESSION['prestacao_contas'][$idRequisicao]['valor_total_devolver'], 2, ',', '.'); ?>
                                           	</span>
                                            <input type="hidden" value="<?php echo $_SESSION['prestacao_contas'][$idRequisicao]['total_devolver'] ?>" name="total_devolver" id="total_devolver" />
                                </center>  	
                                </td>
                             </tr>
                         </tfoot>
						<?php
                        endif; ?>
                </table>
            </div>

            <?php if ($_SESSION['prestacao_contas'][$idRequisicao]['valor_total_despesas'] > $_SESSION['prestacao_contas'][$idRequisicao]['valor_total_adiantamento']) : ?>
            
                <?php if ($this->view->parametros->statusRequisicao != 'F'): ?>

                    <div class="formulario solicitarAprovacaoPara">
                        <div class="campo medio">
                           <label for="solicitar_reembolso_para">Solicitar Aprovação Para *</label>
                           <select <?php echo $this->view->parametros->desabilitarCamposPrestacaoContas;?> id="solicitar_reembolso_para" name="solicitar_reembolso_para">
                               <option value="">Escolha</option>
                               <?php 
                               if ($this->view->parametros->aprovadores) { 
        							
        							foreach($this->view->parametros->aprovadores as $aprovador) {
        						?>
                               		<option value="<?php echo $aprovador->cd_usuario . '|' . $aprovador->usuemail ?>"><?php echo $aprovador->nm_usuario ?></option>
                               <?php 
        							} 
        						}?>
                           </select>
                       </div>
                       <div class="clear"></div>
                    </div>

                <?php endif; ?>

            <?php endif; ?>
    </div>
    <div class="bloco_acoes">
         <button <?php echo $this->view->parametros->desabilitarCamposPrestacaoContas;?> type="button" id="bt_confirmar_prestacao" name="bt_confirmar_prestacao">Confirmar</button>
         <?php $disab = ($_SESSION['prestacao_contas'][$idRequisicao]['registro_inserido'] == 1) ? '' : "disabled='disabled'";?>
         <button type="button" id="bt_imprimir_prestacao" name="bt_imprimir_prestacao" <?php echo $disab ?>>Imprimir</button>
    </div>
</div>
<center>
<div class="carregando_item" style="display: none;">
	<img src="modulos/web/images/loading.gif" />
</div>
</center>
<?php } ?>