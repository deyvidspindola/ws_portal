        <?php  require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/aba_detalhes_modificacao/sub_abas.php"; ?>
        <div class="bloco_titulo">Detalhes da Modificação</div>
        <div class="bloco_conteudo">
            <div id="msg_confirmar_cancelar_modificacao" class="invisivel">Deseja realmente cancelar esta modificação?</div>
            <div id="msg_confirmar_nao_autorizar" class="invisivel">A não autorização implica no cancelamento desta modificação, deseja prosseguir?</div>
            <div class="separador"></div>
            <div class="bloco_titulo">Dados Principais</div>
            <div class="bloco_conteudo">
            	<div class="formulario">
	            	<div class="conteudo">
	            		<div class="label coluna-a">Nº Modificação:</div><span class="medio"><?php echo $this->view->parametros->mdfoid; ?></span>
	            		<div class="label coluna-a">Data:</div><span><?php echo $this->view->parametros->data_modificacao; ?></span>
	            			<div class="clear"></div>
	            		<div class="label coluna-a">Tipo Modificação:</div><span class="medio"><?php echo $this->view->parametros->tipo_modificacao; ?></span>
	            		<div class="label coluna-a">Motivo Substituição:</div><span class="medio"><?php echo $this->view->parametros->substituicao_descricao; ?></span>
	            			<div class="clear"></div>
	            		<div class="label coluna-a">Motivo:</div>
                            <span class="medio">
                                <?php echo empty($this->view->parametros->motivo_modificacao) ? '&nbsp;&nbsp;' : $this->view->parametros->motivo_modificacao ; ?>
                            </span>
                        <div class="label coluna-a">Status Modificação:</div><span><?php echo $this->view->legenda_status[$this->view->parametros->mdfstatus]; ?></span>
	            			<div class="clear"></div>
            			<div class="label coluna-a">Usuário:</div><span class="medio"><?php echo $this->view->parametros->usuario_modificacao; ?></span>
                        <div class="label coluna-a">Status Financeiro:</div>
                            <span class="<?php echo ($this->view->parametros->mdfstatus_financeiro == 'A') ? 'text_green' : 'text_red';?>">
                                <?php echo $this->view->legenda_status_financeiro[$this->view->parametros->mdfstatus_financeiro]; ?>
                            </span>
                            <div class="clear"></div>
                        <div class="label coluna-a">Cliente:</div><span>
                        <?php
                            if(empty($this->view->parametros->cliente_modificacao)) {
                                echo 'Diversos';
                            } else {
                               echo '<a href="cad_cliente.php?acao=principal&clioid=' . $this->view->parametros->mdfclioid .'" target="_blank">';
                               echo $this->view->parametros->cliente_modificacao . '</a>';
                            }
                        ?>
                        </span>
	            			<div class="clear"></div>
	            	</div>
            	</div>
            </div>
            <div class="separador"></div>

            <div class="bloco_titulo">Dados Contratuais</div>
            <div class="bloco_conteudo">
                <div class="formulario">
                    <div class="conteudo">
                            <div class="clear"></div>
                        <div class="label coluna-c">Tipo Contrato:</div><span class="medio"><?php echo $this->view->parametros->tipo_contrato_novo; ?></span>
                            <div class="clear"></div>
                        <div class="label coluna-c">Vigência:</div><span class="medio"><?php echo $this->view->parametros->vigencia; ?> meses</span>
                            <div class="clear"></div>
                        <div class="label coluna-c">Executivo / DMV:</div>
                            <span class="medio"><?php echo $this->view->parametros->executivo .' / '.   $this->view->parametros->dmv; ?></span>
                            <div class="clear"></div>
                    </div>
                </div>
            </div>
            <div class="separador"></div>

            <div class="bloco_titulo">Faturamento</div>
            <div class="bloco_conteudo">
                <div class="formulario">
                    <div class="conteudo">
                        <div class="label coluna-c">Forma de Pagamento:</div><span class="medio"><?php echo $this->view->parametros->forma_pgto; ?></span>
                            <div class="clear"></div>
                        <div class="label coluna-c">Valor de Monitoramento:</div><span>R$ <?php echo $this->view->parametros->monitoramento; ?></span>
                            <div class="clear"></div>
                        <div class="label coluna-c">Valor de Locação:</div><span class="medio">R$ <?php echo $this->view->parametros->locacao; ?></span>
                            <div class="clear"></div>
                        <div class="label coluna-c">Taxa:</div><span class="medio">
                            <?php echo empty($this->view->parametros->taxa_descricao) ? 'Sem taxa' : $this->view->parametros->taxa_descricao; ?>
                        </span>
                            <div class="clear"></div>
                        <div class="label coluna-c">Valor:</div><span class="medio">
                            <?php echo ($this->view->parametros->taxa_isencao == 't') ? 'Isento' : ('R$ ' . $this->view->parametros->taxa); ?>
                        </span>
                            <div class="clear"></div>

                        <form id="form_gerar_contratos" action="" method="post" class="<?php echo ($this->view->parametros->cmdfppagar_cartao == 't') ? '' : 'invisivel'; ?>">
                            <div class="label coluna-c">Código de Segurança *</div>
                            <input id="cartao_codigo" name="cartao_codigo" type="password" value="" maxlength="3" class="campo numerico">
                            <input type="hidden" id="mdfoid" name="mdfoid" value="<?php echo $this->view->parametros->mdfoid; ?>">
                            <input type="hidden" id="mdfcmtoid" name="mdfcmtoid" value="<?php echo $this->view->parametros->mdfcmtoid; ?>">
                            <input type="hidden" id="cmtoid" name="cmtoid" value="<?php echo $this->view->parametros->mdfcmtoid; ?>">
                            <input type="hidden" id="acao" name="acao" value="gerarContratos">
                            <input type="hidden" id="mdfstatus" name="mdfstatus" value="<?php echo $this->view->parametros->mdfstatus; ?>">
                            <input type="hidden" id="cmdfppagar_cartao" name="cmdfppagar_cartao" value="<?php echo $this->view->parametros->cmdfppagar_cartao; ?>">
                            <div class="clear"></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="separador"></div>

           <?php if ($this->view->parametros->mdfstatus == 'P' || $this->view->parametros->mdfstatus == 'A'):  ?>
                <form id="form_cancelar" method="post" action="">
                   <input type="hidden" id="is_nao_autorizar" name="is_nao_autorizar" value="f">
                   <div class="bloco_titulo">Ações</div>
                    <div class="bloco_conteudo">
                        <div class="formulario">
                            <fieldset class="medio">
                                <legend>Cancelar Modificação</legend>
                                <input type="radio" value="S" id="cancelar_sim" name="cancelar">
                                <label for="cancelar_sim">Sim</label>
                                <input type="radio" value="N" id="cancelar_nao" name="cancelar" checked="true">
                                <label for="cancelar_nao">Não</label>
                            </fieldset>
                             <div class="clear"></div>
                            <div id="campo_observacao_cancelar" class="campo maior invisivel">
                                <label for="observacao_cancelar">Observação *</label>
                                <textarea id="observacao_cancelar" name="observacao_cancelar"><?php echo $this->view->parametros->observacao_cancelar;?></textarea>
                            </div>
                            <div class="clear"></div>
                             <?php if( ($this->view->parametros->mdfstatus == 'A') && ($this->permisaoAutorizarTecnico) ) : ?>
                                 <fieldset class="medio">
                                    <legend>Autorização Técnica</legend>
                                    <input type="radio" value="S" id="autorizar_sim" name="autorizacao">
                                    <label for="autorizar_sim">Autorizar</label>
                                    <input type="radio" value="N" id="autorizar_nao" name="autorizacao" checked="true">
                                    <label for="autorizar_nao">Não Autorizar</label>
                                </fieldset>
                                <div class="clear"></div>
                            <?php endIf; ?>
                            <input type="hidden" id="acao" name="acao" value="cancelarModificacao">
                            <input type="hidden" id="mdfstatus" name="mdfstatus" value="<?php echo $this->view->parametros->mdfstatus; ?>">
                            <input type="hidden" id="mdfoid" name="mdfoid" value="<?php echo $this->view->parametros->mdfoid; ?>">
                        </div>
                    </div>
                </form>
                <form id="form_autorizar" action="" method="post" class="invisivel">
                    <input type="hidden" id="mdfoid" name="mdfoid" value="<?php echo $this->view->parametros->mdfoid; ?>">
                    <input type="hidden" id="acao" name="acao" value="efetivarAutorizacaoTecnica">
                    <input type="hidden" id="autorizar" name="autorizar" value="">

                </form>
            <?php endIf; ?>
            <div class="separador"></div>
       </div>
        <div class="bloco_acoes">
            <?php if ( ($this->view->parametros->mdfstatus == 'P') && ($this->view->parametros->mdfstatus_financeiro == 'A') ):  ?>
                <button type="button" id="btn_gerar_contrato">Gerar Contrato(s)</button>
            <?php endIf; ?>
            <?php if ( ($this->view->parametros->mdfstatus == 'P' || $this->view->parametros->mdfstatus == 'A') ):  ?>
                <button type="button" id="btn_confirmar" class="desabilitado">Confirmar</button>
            <?php endIf; ?>
        </div>
    </div>
     <div class="bloco_acoes">
            <button type="button" id="btn_voltar">Voltar</button>
    </div>