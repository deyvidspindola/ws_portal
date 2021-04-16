
            <div class="bloco_titulo">Dados para pesquisa</div>
            <div class="bloco_conteudo">

                <div class="formulario">

                    <input type="hidden" name="cliente_id" id="cliente_id">

                    <!-- TIPO DE RELATÓRIO -->
                    <div class="campo medio">
                        <label id="lbl_tipo">Tipo</label>
                        <select id="tipo_relatorio" name="tipo_relatorio">
                            <option <?php echo $this->view->parametros->tipo_relatorio == "A" ? 'selected="selected"' : ($this->view->parametros->tipo_relatorio != "S" ? 'selected="selected"' : '') ?> value="A">Analítico</option>
                            <option <?php echo $this->view->parametros->tipo_relatorio == "S" ? 'selected="selected"' : ''?> value="S">Sintético</option>
                        </select>
                    </div>

                    <!-- MOTIVO DE CRÉDITO -->
                    <div class="campo maior">
                        <label id="lbl_motivo_credito">Motivo do Crédito</label>
                        <select id="motivo_credito" name="motivo_credito">
                            <?php foreach ($this->view->parametros->listarMotivoCredito AS $motivo) : ?>
                                <?php $selected = $this->view->parametros->motivo_credito == $motivo->cfmcoid ? 'selected="selected"' : ''; ?>
                                <option <?php echo $selected ?> value="<?php echo $motivo->cfmcoid  ?>"><?php echo $motivo->cfmcdescricao ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="clear"></div>

                    <!-- PERÍODO DE INCLUSÃO -->
                    <div class="campo data periodo">
                        <div class="inicial">
                            <label for="periodo_inclusao_ini">Período *</label>
                            <input type="text" id="periodo_inclusao_ini" name="periodo_inclusao_ini" value="<?php echo $this->view->parametros->periodo_inclusao_ini ?>" class="campo" />
                        </div>                          
                        <div class="campo label-periodo">a</div>
                        <div class="final">
                            <label for="periodo_inclusao_fim">&nbsp;</label>
                            <input type="text" id="periodo_inclusao_fim" name="periodo_inclusao_fim" value="<?php echo $this->view->parametros->periodo_inclusao_fim ?>" class="campo" />
                        </div>                            
                    </div>

                    <!-- TIPO CAMPANHA PROMOCIONAL -->
                    <div class="campo maior">
                        <label id="lbl_tipo_campanha_promocional">Tipo de Campanha Promocional</label>
                        <select id="tipo_campanha_promocional" name="tipo_campanha_promocional">
                            <?php foreach ($this->view->parametros->listarTipoCampanha AS $tipoCampanhas) : ?>
                                <?php $selected = $this->view->parametros->tipo_campanha_promocional == $tipoCampanhas->cftpoid ? 'selected="selected"' : ''; ?>
                                <option <?php echo $selected ?> value="<?php echo $tipoCampanhas->cftpoid  ?>"><?php echo $tipoCampanhas->cftpdescricao ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                                            
                    <div class="clear"></div>

                    <style type="text/css">
                        .fix_radio{
                             margin-left: -5px;
                        }
                    </style>

                    <!-- INCLUSÃO -->   
                    <fieldset class="medio">
                        <legend id="lgd_forma_inclusao">Inclusão</legend>
                        <input type="radio" id="forma_inclusao_1" name="forma_inclusao" value="1" <?php echo $this->view->parametros->forma_inclusao == '1' ? 'checked="checked"' : ''; ?> />
                        <label class="fix_radio" for="forma_inclusao_1">Manual</label>
                        <input type="radio" id="forma_inclusao_2" name="forma_inclusao" value="2" <?php echo $this->view->parametros->forma_inclusao == '2' ? 'checked="checked"' : ''; ?> />
                        <label class="fix_radio" for="forma_inclusao_2">Automática</label> 
                        <input type="radio" id="forma_inclusao_3" name="forma_inclusao" value="-1" <?php echo $this->view->parametros->forma_inclusao == '-1' ? 'checked="checked"' : ($this->view->parametros->forma_inclusao =! '1' && $this->view->parametros->forma_inclusao != '2' ? 'checked="checked"' : ''); ?>/>
                        <label class="fix_radio" for="forma_inclusao_3">Todas</label> 
                    </fieldset>

                    <div class="clear"></div>
                    <div class="analitico" <?php echo $this->view->parametros->tipo_relatorio == "A" ? 'style="display: block"' : ($this->view->parametros->tipo_relatorio != "S" ? 'style="display: block"' : 'style="display: none"') ?>>

                        <div class="clear"></div>
                        
                        
                        <!-- PESQUISA CLIENTE INDICADOR -->             
                        <div class="campo medio" style="position: relative;">
                            <label>Nome do Cliente</label>
                            <input id="nome_cliente" type="text" class="campo"  maxlength="50" name="nome_cliente" value="<?php echo $this->view->parametros->nome_cliente ?>"  />
                        </div>

                        <div class="clear"></div>

                        <fieldset class="medio">
                            <legend id="lgd_tipo_pessoa">Tipo Pessoa</legend>
                            <input type="radio" value="F" class="componente_tipo_pessoa" id="tipo_pessoa_1" name="tipo_pessoa"  <?php echo $this->view->parametros->tipo_pessoa == 'F' ? 'checked="checked"' : '' ?>/>
                            <label for="tipo_pessoa_1" >Física</label>
                            <input type="radio" value="J" class="componente_tipo_pessoa" id="tipo_pessoa_2" name="tipo_pessoa"  <?php echo $this->view->parametros->tipo_pessoa == 'J' ? 'checked="checked"' : ($this->view->parametros->tipo_pessoa != 'F' ? 'checked="checked"' : '') ?> />
                            <label for="tipo_pessoa_2" >Jurídica</label> 
                        </fieldset>

                        <div class="clear"></div>

                        <div id="doc_J" class="campo medio <?php echo $this->view->parametros->tipo_pessoa == 'J' ? 'visivel' : ($this->view->parametros->tipo_pessoa != 'F' ? 'visivel' : 'invisivel') ?>" >
                            <label for="cliente_doc_J" >CNPJ</label>
                            <input type="text" value="<?php echo $this->view->parametros->cliente_doc_J ?>" id="cliente_doc_J" name="cliente_doc_J" class="campo mask_cnpj "  />
                        </div> 

                        <div id="doc_F" class="campo medio <?php echo $this->view->parametros->tipo_pessoa == 'F' ? 'visivel' : 'invisivel' ?>" >
                            <label for="cliente_doc_F" >CPF</label>
                            <input type="text" value="<?php echo $this->view->parametros->cliente_doc_F ?>" id="cliente_doc_F" name="cliente_doc_F" class="campo mask_cnpj "  />
                        </div>

                        <div class="clear"></div>

                        <!-- NOTA FISCAL -->
                        <div class="campo medio">
                            <label for="lbl_numero_nf" >Número NF</label>
                            <input type="text" class="campo" id="numero_nf" name="numero_nf" maxlength="10"  value="<?php echo $this->view->parametros->numero_nf ?>" />
                        </div>

                        <div class="campo menor">
                            <label lbl="lbl_serie_nf" >Série NF</label>
                            <select id="serie_nf" name="serie_nf">
                                <?php foreach ($this->view->parametros->listarSerieNota AS $serie) : ?>
                                    <?php $selected = trim($this->view->parametros->serie_nf) == trim($serie->nfsserie) ? 'selected="selected"' : ''; ?>
                                    <option <?php echo $selected ?> value="<?php echo trim($serie->nfsserie)  ?>"><?php echo trim($serie->nfsserie) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="sintetico" <?php echo $this->view->parametros->tipo_relatorio == "S" ? 'style="display: block"' : 'style="display: none"'?>>
                        <!-- RESULTADO -->  
                        <fieldset class="medio">
                            <legend id="lgd_resultado">Resultado</legend>
                            <input type="radio" id="tipo_resultado_d" name="tipo_resultado" value="d" <?php echo $this->view->parametros->tipo_resultado == 'd' ? 'checked="checked"' : ($this->view->parametros->tipo_resultado != 'm' ? 'checked="checked"' : '') ?> />
                            <label for="tipo_resultado_d">Diário</label>
                            <input type="radio" id="tipo_resultado_m" name="tipo_resultado" value="m" <?php echo $this->view->parametros->tipo_resultado == 'm' ? 'checked="checked"' : '' ?>/>
                            <label for="tipo_resultado_m">Mensal</label> 
                        </fieldset>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            <!-- BARRA DE AÇÕES -->
            <div class="bloco_acoes">
                <button id="btn_pesquisar" data-pesquisa="pesquisarConcedidos" type="button">Pesquisar</button>
                <button id="btn_retornar" type="button">Retornar</button>
            </div>