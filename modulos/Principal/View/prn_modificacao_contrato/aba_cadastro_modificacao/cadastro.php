        <?php  require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/aba_cadastro_modificacao/sub_abas.php"; ?>
        <div class="bloco_titulo"></div>
        <div class="bloco_conteudo">
            <form action="" id="form_cadastro_modificacao" method="post" enctype="multipart/form-data">
                <input id="mdfoid" name="mdfoid" value="<?php echo $this->view->parametros->mdfoid;?>" type="hidden">
                <input id="mdfstatus" name="mdfstatus" value="<?php echo $this->view->parametros->mdfstatus;?>" type="hidden">
                <input id="acao" name="acao" value="cadastrar" type="hidden">
                <input id="sub_tela" name="sub_tela" type="hidden" value="<?php echo  $this->parametros->view->sub_tela; ?>">

                <div class="separador"></div>
                <div class="mensagem info">Campos com (*) são obrigatórios.</div>
                <div class="bloco_titulo">Cadastro Modificação</div>
                <div class="bloco_conteudo">
                    <div class="formulario">
                            <div class="campo medio">
                                <label for="cmtoid">Tipo Modificação *</label>
                                <select id="cmtoid" name="cmtoid">
                                    <option value="" data-lote="" data-troca="" data-grupo="" data-siggo=""
                                            data-arquivo="" data-financeiro="">Escolha</option>
                                    <?php foreach ($this->view->comboTipoModificacao as $dados): ?>
                                            <option value="<?php echo $dados->cmtoid; ?>"
                                                    <?php echo ($this->view->parametros->cmtoid == $dados->cmtoid) ? ' selected="true"' : '' ; ?>
                                                    data-analise="<?php echo $dados->cmptanalise_credito; ?>"
                                                    data-lote="<?php echo $dados->cmpgmodificacao_lote; ?>"
                                                    data-troca="<?php echo $dados->cmpttroca_cliente; ?>"
                                                    data-grupo="<?php echo $dados->cmtcmgoid; ?>"
                                                    data-siggo="<?php echo $dados->produto_siggo; ?>"
                                                    data-seguro="<?php echo $dados->produto_siggo_seguro; ?>"
                                                    data-arquivo="<?php echo $dados->cmptleitura_arquivo; ?>"
                                                    data-financeiro="<?php echo $dados->cmptrecebe_dados_financeiro; ?>"
                                                    data-taxa="<?php echo $dados->cmpttaxa; ?>"
                                                    data-obroid="<?php echo $dados->cmpxobroid; ?>">
                                                <?php echo $dados->cmtdescricao; ?>
                                            </option>
                                    <?php endForeach; ?>
                                </select>
                                <img id="img_cmtoid" class="carregando invisivel" src="images/ajax-loader-circle.gif">
                            </div>
                            <input type="hidden" id="produto_siggo" name="produto_siggo" value="<?php echo $this->view->parametros->produto_siggo;?>">
                            <input type="hidden" id="cmptleitura_arquivo" name="cmptleitura_arquivo" value="<?php echo $this->view->parametros->cmptleitura_arquivo;?>">
                            <input type="hidden" id="exibir_cadastro" name="exibir_cadastro" value="<?php echo $this->view->parametros->exibir_cadastro;?>">
                            <input type="hidden" id="cmptrecebe_dados_financeiro" name="cmptrecebe_dados_financeiro" value="<?php echo $this->view->parametros->cmptrecebe_dados_financeiro;?>">
                            <input type="hidden" id="cmpttaxa" name="cmpttaxa" value="<?php echo $this->view->parametros->cmpttaxa;?>">
                            <input type="hidden" id="cmtcmgoid" name="cmtcmgoid" value="<?php echo $this->view->parametros->cmtcmgoid;?>">

                             <div  id="bloco_migrar_para" class="campo medio <?php echo ($this->view->parametros->id_migracao_ex != '') ? '' : 'invisivel' ;?>">
                                <label for="migrar_para">Migrar Para *</label>
                                <select id="migrar_para" name="migrar_para">
                                    <option value="" data-analise="" data-pagador="">Escolha</option>
                                </select>
                                <img id="img_migrar_para" class="carregando invisivel" src="images/ajax-loader-circle.gif">
                                <input type="hidden" id="migrar_para_recarga_tela" value="<?php echo $this->view->parametros->migrar_para;?>" />
                                <input type="hidden" id="acoes_lote_migracao" name="acoes_lote_migracao" value="<?php echo $this->view->parametros->acoes_lote_migracao; ?>">
                                <input type="hidden" id="cliente_pagador" name="cliente_pagador" value="<?php echo $this->view->parametros->cliente_pagador; ?>">
                            </div>

                            <div id="combo_msuboid" class="campo medio <?php echo empty($this->view->comboMotivoSubstituicao) ? ' invisivel' : '' ?>">
                                <label for="msuboid">Motivo Substituição *</label>
                                <select id="msuboid" name="msuboid">
                                    <option value="" data-eqcoid="" data-troca="">Escolha</option>
                                     <?php foreach ($this->view->comboMotivoSubstituicao as $dados): ?>
                                            <option value="<?php echo $dados->msuboid; ?>"
                                                    data-eqcoid="<?php echo $dados->msubeqcoid;?>"
                                                    data-troca="<?php echo $dados->msubtrocaveiculo;?>"
                                                    <?php echo ($this->view->parametros->msuboid == $dados->msuboid) ? ' selected="true"' : '' ; ?>>
                                            <?php echo $dados->msubdescricao; ?>
                                        </option>
                                    <?php endForeach; ?>
                                </select>
                                <img id="img_msuboid" class="carregando invisivel" src="images/ajax-loader-circle.gif">
                                <input type="hidden" id="is_combo_motivo_visivel" name="is_combo_motivo_visivel" value="N">
                                <input type="hidden" id="troca_veiculo" name="troca_veiculo" value="<?php echo $this->view->parametros->troca_veiculo;?>">
                                <input type="hidden" id="msubeqcoid" name="msubeqcoid" value="<?php echo $this->view->parametros->msubeqcoid;?>">
                            </div>
                            <div class="clear"></div>

                            <div id="campos_analise_credito" class="">
                                <div id="bloco_cpf_cnpj" class="<?php echo ($this->view->parametros->esconder_cpf_cnpj == 't') ? 'invisivel' : ''; ?>">
                                    <div class="campo medio">
                                        <label for="tipo_pessoa">Tipo Pessoa *</label>
                                        <select id="tipo_pessoa" name="tipo_pessoa" <?php echo (empty($this->view->parametros->cmtoid)) ? 'disabled="true"' : '' ;?>>
                                            <option value="">Escolha</option>
                                            <option value="F" <?php echo ($this->view->parametros->tipo_pessoa == 'F') ? ' selected="true"' : '' ; ?>>
                                                Física
                                            </option>
                                            <option value="J" <?php echo ($this->view->parametros->tipo_pessoa == 'J') ? ' selected="true"' : '' ; ?>>
                                                Jurídica
                                            </option>
                                        </select>
                                    </div>

                                    <div class="campo medio">
                                        <label for="cpf_cnpj">CPF/CNPJ *</label>
                                        <input id="cpf_cnpj" name="cpf_cnpj" value="<?php echo $this->view->parametros->cpf_cnpj;?>"
                                                class="campo <?php echo (empty($this->view->parametros->tipo_pessoa)) ? ' desabilitado' : '';?>"
                                                type="text" <?php echo (empty($this->view->parametros->tipo_pessoa)) ? ' disabled="true"' : '';?> />
                                        <input id="cmfclioid_destino" name="cmfclioid_destino" value="<?php echo $this->view->parametros->cmfclioid_destino;?>" type="hidden">
                                        <input id="clinome" name="clinome" value="<?php echo $this->view->parametros->clinome;?>" type="hidden">
                                    </div>
                                     <div class="clear"></div>
                                </div>
                                <input type="hidden" id="esconder_cpf_cnpj" name="esconder_cpf_cnpj" value="<?php echo $this->view->parametros->esconder_cpf_cnpj;?>">
                                <div class="clear"></div>
                            </div>
                             <div class="clear"></div>

                            <div class="campo maior">
                                <label for="observacao">Observação</label>
                                <textarea id="observacao" name="observacao"><?php echo $this->view->parametros->observacao;?></textarea>
                                <input type="hidden" id="observacao_serasa" name="observacao_serasa" value="<?php echo $this->view->parametros->observacao_serasa;?>">
                            </div>
                            <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div id="bloco_cadastro" class="<?php echo ($this->view->parametros->exibir_cadastro == 't') ? '' : 'invisivel' ;?>">

                    <div id="bloco_dados_contratuais" class="<?php echo ($this->view->parametros->id_migracao_ex != '') ? 'invisivel' : '' ;?>">
                        <div class="separador"></div>
                         <div class="mensagem alerta invisivel" id="msg_alerta_dados_contratuais"></div>
                        <div class="bloco_titulo">Dados Contratuais</div>
                        <div class="bloco_conteudo">
                                <div class="formulario">
                                   <fieldset class="menor opcoes-inline  <?php echo ($this->view->parametros->exibir_lote) ? '' : 'invisivel' ;?>">
                                        <legend>Ações em Lote</legend>
                                        <input id="acoes_lote" name="acoes_lote" value="<?php echo $this->view->parametros->acoes_lote;?>" type="checkbox" <?php echo ($this->view->parametros->acoes_lote != 't' ) ? '' : ' checked="true"' ;?>><label for="resultado_tela">Sim</label>
                                        <input type="hidden" id="cmpgmodificacao_lote" name="cmpgmodificacao_lote" value="<?php echo $this->view->parametros->cmpgmodificacao_lote; ?>">
                                    </fieldset>
                                    <fieldset class="menor opcoes-inline  <?php echo ($this->view->parametros->exibir_lote != 't') ? '' : 'invisivel' ;?>">
                                        <legend>Anexar Arquivo(s)</legend>
                                        <input id="anexar_arquivo" name="anexar_arquivo" value="<?php echo $this->view->parametros->anexar_arquivo;?>" type="checkbox" <?php echo ($this->view->parametros->anexar_arquivo != 't' ) ? '' : ' checked="true"' ;?>><label for="resultado_tela">Sim</label>
                                    </fieldset>
                                    <div class="clear"></div>

                                     <div class="campo medio">
                                        <label for="cmfconnumero">Nº Contrato *</label>
                                        <input id="cmfconnumero" name="cmfconnumero" value="<?php echo $this->view->parametros->cmfconnumero;?>"
                                        class="campo numerico <?php echo ($this->view->parametros->acoes_lote !='t') ? '' : ' desabilitado' ;?>"
                                        <?php echo ($this->view->parametros->acoes_lote!='t') ? '' : ' disabled="true"' ;?>
                                        type="text">
                                        <img id="img_cmfconnumero" class="carregando-input invisivel" src="images/ajax-loader-circle.gif">
                                    </div>

                                    <div class="campo medio">
                                        <label for="cmftpcoid_destino">Tipo Contrato</label>
                                        <select id="cmftpcoid_destino" name="cmftpcoid_destino">
                                            <option value="">Escolha</option>
                                            <?php foreach ($this->view->comboTipoContrato as $dados): ?>
                                                <option value="<?php echo $dados->tpcoid; ?>"
                                                    <?php echo ($this->view->parametros->cmftpcoid_destino == $dados->tpcoid) ? ' selected="true"' : '' ; ?>>
                                                    <?php echo $dados->tpcdescricao; ?>
                                                </option>
                                            <?php endForeach; ?>
                                        </select>
                                    </div>
                                     <div class="campo medio">
                                        <label for="cmfeqcoid_destino">Classe Contrato *</label>
                                        <select id="cmfeqcoid_destino" name="cmfeqcoid_destino">
                                            <option value="">Escolha</option>
                                            <?php foreach ($this->view->comboClasseContrato as $dados): ?>
                                                <option value="<?php echo $dados->eqcoid; ?>" <?php echo ($this->view->parametros->cmfeqcoid_destino == $dados->eqcoid) ? ' selected="true"' : '' ; ?>>
                                                    <?php echo $dados->eqcdescricao; ?>
                                                </option>
                                            <?php endForeach; ?>
                                        </select>
                                    </div>
                                    <div class="clear"></div>

                                     <div class="campo medio">
                                        <label for="cmfcvgoid">Vigência *</label>
                                        <select id="cmfcvgoid" name="cmfcvgoid" <?php echo ($this->view->parametros->produto_siggo == 't') ? 'disabled="true"' : '';?>>
                                            <option value="">Escolha</option>
                                            <?php foreach ($this->view->comboVigencia as $dados): ?>
                                                <option value="<?php echo $dados->cvgoid; ?>" <?php echo ($this->view->parametros->cmfcvgoid == $dados->cvgoid) ? ' selected="true"' : '' ; ?>>
                                                    <?php echo $dados->cvgvigencia; ?>
                                                </option>
                                            <?php endForeach; ?>
                                        </select>
                                        <input type="hidden" id="cmfcvgoid_aux" name="cmfcvgoid_aux" value="<?php echo $this->view->parametros->cmfcvgoid;?>"/>
                                    </div>

                                     <div class="campo medio">
                                        <label for="cmffunoid_executivo">Executivo</label>
                                        <select id="cmffunoid_executivo" name="cmffunoid_executivo">
                                            <option value="" data-dmv="">Escolha</option>
                                            <?php foreach ($this->view->comboExecutivo as $dados): ?>
                                                <option value="<?php echo $dados->funoid; ?>"
                                                    <?php echo ($this->view->parametros->cmffunoid_executivo == $dados->funoid) ? ' selected="true"' : '' ; ?>
                                                    data-dmv="<?php echo $dados->funrczoid; ?>">
                                                    <?php echo $dados->funnome; ?>
                                                </option>
                                            <?php endForeach; ?>
                                        </select>
                                        <input type="hidden" id="cmfrczoid" name="cmfrczoid" value="<?php echo $this->view->parametros->cmfrczoid;?>"/>
                                    </div>
                                    <div class="clear"></div>

                                     <fieldset class="field_maior" id="bloco_dados_adicionais">
                                    <legend>Dados Adicionais</legend>
                                         <div class="conteudo">
                                           <div id="link_nome_cliente" class="label">Cliente: &nbsp;</div><a href="cad_cliente.php?acao=principal&clioid=<?php echo $this->view->parametros->cmfclioid_destino;?>" target="_blank"><?php echo $this->view->parametros->clinome;?></a>
                                                <div class="clear"></div>
                                            <div id="bloco_veiculo">
                                                <div id="link_placa" class="label">Veiculo: &nbsp;</div><a href="veiculo.php?veioid=<?php echo $this->view->parametros->cmfveioid;?>" target="_blank"><?php echo $this->view->parametros->veiplaca;?></a>
                                            </div>

                                            <div id="bloco_novo_veiculo" class="<?php echo ($this->view->parametros->exibir_novo_veiculo == 't') ? '' : 'invisivel' ;?>">
                                                --- <a href="veiculo.php" target="_blank"> [Novo Veículo]</a>
                                                <div class="clear"></div>
                                                <div class="campo menor">
                                                    <label for="veiplaca_novo">Placa Novo Veículo*</label>
                                                    <input type="text" id="veiplaca_novo" name="veiplaca_novo" value="<?php echo $this->view->parametros->veiplaca_novo;?>" class="campo">
                                                </div>
                                                <input type="hidden" id="cmfveioid_novo" name="cmfveioid_novo" value="<?php echo $this->view->parametros->cmfveioid_novo;?>">
                                            </div>
                                            <input type="hidden" id="exibir_novo_veiculo" name="exibir_novo_veiculo" value="<?php echo $this->view->parametros->exibir_novo_veiculo;?>">
                                            <input type="hidden" id="veiplaca" name="veiplaca" value="<?php echo $this->view->parametros->veiplaca;?>">
                                            <input type="hidden" id="cmfveioid" name="cmfveioid" value="<?php echo $this->view->parametros->cmfveioid;?>">
                                        </div>
                                    </fieldset>
                                    <div class="clear"></div>

                                    <fieldset id="bloco_proposta_siggo" class="<?php echo ($this->view->parametros->produto_siggo != 't') ? 'invisivel' : ''; ?>">
                                        <legend>Proposta Siggo</legend>
                                         <div class="campo medio">
                                             <label for="cmftppoid">Tipo *</label>
                                            <select id="cmftppoid" name="cmftppoid">
                                                <option value="">Escolha</option>
                                                <?php foreach ($this->view->comboTipoProposta as $dados): ?>
                                                <option value="<?php echo $dados->tppoid; ?>"
                                                    <?php echo ($this->view->parametros->cmftppoid == $dados->tppoid) ? ' selected="true"' : '' ; ?>>
                                                    <?php echo $dados->tppdescricao; ?>
                                                </option>
                                                <?php endForeach; ?>
                                            </select>
                                        </div>
                                        <div class="clear"></div>
                                        <div class="campo medio">
                                             <label for="cmftppoid_subtitpo">Sub-tipo *</label>
                                            <select id="cmftppoid_subtitpo" name="cmftppoid_subtitpo">
                                                <option value="">Escolha</option>
                                                <?php foreach ($this->view->comboSubTipo as $dados): ?>
                                                    <option value="<?php echo $dados->tppoid; ?>"
                                                            <?php echo ($this->view->parametros->cmftppoid_subtitpo == $dados->tppoid) ? ' selected="true"' : '' ; ?>>
                                                    <?php echo $dados->tppdescricao; ?>
                                                </option>
                                            <?php endForeach; ?>
                                            </select>
                                            </select>
                                        <img id="img_cmftppoid_subtitpo" class="carregando invisivel" src="images/ajax-loader-circle.gif">
                                        <input type="hidden" id="cmftppoid_subtitpo_recarga_tela" value="<?php echo $this->view->parametros->cmftppoid_subtitpo;?>" />
                                        </div>
                                    </fieldset>
                                 <div class="clear"></div>
                                </div>
                        </div>
                    </div>

                     <div id="bloco_faturamento" class="<?php echo ($this->view->parametros->cmptrecebe_dados_financeiro == 'f') ? 'invisivel' : ''; ?>">
                        <div class="separador"></div>
                        <div class="mensagem alerta invisivel" id="msg_alerta_bloco_faturamento"></div>
                        <div class="bloco_titulo">Faturamento</div>
                        <div class="bloco_conteudo">
                             <div class="formulario">
                                <input type="hidden" id="analise_credito" name="analise_credito" value="<?php echo $this->view->parametros->analise_credito; ?>">
                                <div class="campo menor">
                                    <label for="cmdfpcpvoid">Parcelamento *</label>
                                    <select id="cmdfpcpvoid" name="cmdfpcpvoid" <?php echo (!empty($this->view->parametros->bloquear_parcela)) ? ' disabled="disabled"' : '' ; ?>>
                                        <option value="" data-parcelas="">Escolha</option>
                                         <?php foreach ($this->view->comboParcelamento as $dados): ?>
                                            <option  data-parcelas="<?php echo $dados->cpvparcela; ?>"
                                                value="<?php echo $dados->cpvoid; ?>"
                                                <?php echo ($this->view->parametros->cmdfpcpvoid == $dados->cpvoid) ? ' selected="true"' : '' ; ?>>
                                                <?php echo $dados->cpvdescricao; ?>
                                            </option>
                                        <?php endForeach; ?>
                                    </select>
                                    <input type="hidden" id="cmdfpnum_parcela" name="cmdfpnum_parcela" value="<?php echo $this->view->parametros->cmdfpnum_parcela; ?>" />
                                    <input type="hidden" id="cmdfpcpvoid_aux" name="cmdfpcpvoid_aux" value="<?php echo $this->view->parametros->cmdfpcpvoid; ?>" />
                                </div>

                                <div class="campo medio">
                                    <label for="cmdfpforcoid">Forma de Pagamento *</label>
                                    <select id="cmdfpforcoid" name="cmdfpforcoid" class="desabilitado" disabled="disabled">
                                        <option value="" data-forma="">Escolha</option>
                                         <?php foreach ($this->view->comboFormaPagamento as $dados): ?>
                                            <option data-forma="<?php echo $dados->forma; ?>"
                                                value="<?php echo $dados->forcoid; ?>" <?php echo ($this->view->parametros->cmdfpforcoid == $dados->forcoid) ? ' selected="true"' : '' ; ?>>
                                                <?php echo $dados->forcnome; ?>
                                            </option>
                                        <?php endForeach; ?>
                                    </select>
                                    <input type="hidden" id="cmdfpforcoid_aux" name="cmdfpforcoid" value="<?php echo $this->view->parametros->cmdfpforcoid; ?>">
                                    <input type="hidden" id="forma_pgto" name="forma_pgto" value="<?php echo $this->view->parametros->forma_pgto; ?>">
                                </div>

                                <div class="campo menor">
                                    <label for="cmdfpvencimento_fatura">Data Vencimento *</label>
                                    <select id="cmdfpvencimento_fatura" name="cmdfpvencimento_fatura" disabled="true">
                                        <option value="" data-parcelas="">Escolha</option>
                                         <?php foreach ($this->view->comboDataVencimento as $dados): ?>
                                            <option  value="<?php echo $dados->cdvoid; ?>"
                                                <?php echo ($this->view->parametros->cmdfpvencimento_fatura == $dados->cdvoid) ? ' selected="true"' : '' ; ?>>
                                                <?php echo $dados->cdvdia; ?>
                                            </option>
                                        <?php endForeach; ?>
                                    </select>
                                    <input type="hidden" id="cmdfpvencimento_fatura_aux" name="cmdfpvencimento_fatura" value="<?php echo $this->view->parametros->cmdfpvencimento_fatura; ?>">
                                </div>
                                <div class="clear"></div>

                                 <div class="campo menor">
                                    <label for="cmdfpvlr_monitoramento_negociado">Monitoramento *</label>
                                    <input id="cmdfpvlr_monitoramento_negociado" name="cmdfpvlr_monitoramento_negociado" value="<?php echo $this->view->parametros->cmdfpvlr_monitoramento_negociado; ?>" class="campo moeda" type="text">
                                    <input type="hidden" id="eqcvlr_minimo_mens" name="eqcvlr_minimo_mens" value="<?php echo $this->view->parametros->eqcvlr_minimo_mens; ?>" />
                                    <input type="hidden" id="eqcvlr_maximo_mens" name="eqcvlr_maximo_mens" value="<?php echo $this->view->parametros->eqcvlr_maximo_mens; ?>" />
                                    <input type="hidden" id="cmdfpvlr_monitoramento_tabela" name="cmdfpvlr_monitoramento_tabela" value="<?php echo $this->view->parametros->cmdfpvlr_monitoramento_tabela; ?>" />
                                </div>

                                <div class="campo menor">
                                    <label for="cmdfpvlr_locacao_negociado">Locação *</label>
                                    <input type="text" id="cmdfpvlr_locacao_negociado" name="cmdfpvlr_locacao_negociado"
                                            value="<?php echo ($this->view->parametros->cmdfpisencao_locacao == 't') ? '' : $this->view->parametros->cmdfpvlr_locacao_negociado; ?>"
                                            class="campo moeda <?php echo ($this->view->parametros->cmdfpisencao_locacao == 't') ? ' desabilitado' : '' ;?>"
                                            <?php echo ($this->view->parametros->cmdfpisencao_locacao == 't') ? 'disabled="true"' : '' ;?>>
                                    <input type="hidden" id="tpivalor_minimo" name="tpivalor_minimo" value="<?php echo $this->view->parametros->tpivalor_minimo; ?>" />
                                    <input type="hidden" id="cmdfpvlr_locacao_tabela" name="cmdfpvlr_locacao_tabela" value="<?php echo $this->view->parametros->cmdfpvlr_locacao_tabela; ?>" />
                                </div>

                                <div class="campo">
                                    <label>&nbsp;</label>
                                    <input id="cmdfpisencao_locacao" name="cmdfpisencao_locacao" value="t" class="campo" type="checkbox"
                                            <?php echo ($this->view->parametros->cmdfpisencao_locacao == 't') ? 'checked="true"' : '' ; ?>>
                                    <label for="cmdfpisencao_locacao" class="campo">&nbsp; Isentar Valor de Locação</label>
                                </div>
                                 <div class="clear"></div>

                                 <fieldset class="field_maior">
                                    <legend>Taxa</legend>
                                    <div class="campo medio">
                                        <label for="cmdfpobroid_taxa">Taxa *</label>
                                        <select id="cmdfpobroid_taxa" name="cmdfpobroid_taxa">
                                            <option value="">Escolha</option>
                                            <?php foreach ($this->view->comboTaxas as $dados): ?>
                                                <option  data-valor="<?php echo $dados->obrvl_obrigacao; ?>"
                                                        value="<?php echo $dados->obroid; ?>" <?php echo ($this->view->parametros->cmdfpobroid_taxa == $dados->obroid) ? ' selected="true"' : '' ; ?>>
                                                    <?php echo $dados->obrobrigacao; ?>
                                                </option>
                                            <?php endForeach; ?>
                                        </select>
                                        <input type="hidden" id="cmdfpvlr_taxa_tabela" name="cmdfpvlr_taxa_tabela" value="<?php echo $this->view->parametros->cmdfpvlr_taxa_tabela; ?>" />
                                    </div>

                                    <div class="campo menor">
                                        <label for="cmdfpvlr_taxa_negociado">Valor da Taxa *</label>
                                        <input id="cmdfpvlr_taxa_negociado" name="cmdfpvlr_taxa_negociado"
                                                value="<?php echo ($this->view->parametros->cmdfpisencao_taxa == 't') ? '' : $this->view->parametros->cmdfpvlr_taxa_negociado;?>"
                                                class="campo moeda  <?php echo ($this->view->parametros->cmdfpisencao_taxa == 't') ? ' desabilitado' : '' ;?>" type="text"
                                                <?php echo ($this->view->parametros->cmdfpisencao_taxa == 't') ? 'disabled="true"' : '' ;?>>
                                    </div>
                                     <div class="clear"></div>

                                    <div class="campo">
                                        <label>&nbsp;</label>
                                        <input id="cmdfpisencao_taxa" name="cmdfpisencao_taxa" value="t" class="campo" type="checkbox"
                                                <?php echo ($this->view->parametros->cmdfpisencao_taxa == 't') ? 'checked="true"' : '' ; ?>>
                                        <label for="cmdfpisencao_taxa" class="campo">&nbsp; Isentar Valor de Taxa</label>
                                    </div>
                                     <div id="bloco_pgto_cartao" class="campo <?php echo ($this->view->parametros->exibir_pagar_cartao != 't') ? 'invisivel' : ''; ?>">
                                        <label>&nbsp;</label>
                                        <input id="cmdfppagar_cartao" name="cmdfppagar_cartao" value="t" class="campo" type="checkbox"
                                                <?php echo ($this->view->parametros->cmdfppagar_cartao == 't') ? 'checked="true"' : '' ; ?>>
                                        <label for="cmdfppagar_cartao" class="campo">&nbsp; Pagar com Cartão de Crédito</label>
                                    </div>
                                 </fieldset>
                                 <div class="clear"></div>

                                <div id="bloco_credito" class="<?php echo ($this->view->parametros->exibir_bloco_credito == 't') ? '' : 'invisivel' ; ?>">
									<fieldset class="field_maior">
										<legend>Cartão de Crédito</legend>
										<div class="campo medio">
											<label for="cmdfpcartao">Nº Cartão *</label>
											<input id="cmdfpcartao" name="cmdfpcartao"
												value="<?php echo $this->view->parametros->cmdfpcartao;?>"
												class="campo numerico" type="text" maxlength="16">
										</div>
										<div class="campo mes_ano">
											<label for="cmdfpcartao_vencimento">Vencimento *</label>
											<input id="cmdfpcartao_vencimento" name="cmdfpcartao_vencimento" class="campo" type="text"
												value="<?php echo $this->view->parametros->cmdfpcartao_vencimento;?>">
										</div>
										<div class="clear"></div>
										<div class="campo medio">
											<label for="cmdfpnome_portador">Nome do Portador *</label>
											<input id="cmdfpnome_portador" name="cmdfpnome_portador"
												value="<?php echo $this->view->parametros->cmdfpnome_portador;?>"
												class="campo alpha" type="text" maxlength="30">
										</div>
										<div class="clear"></div>
										<div class="campo maior">
											<label>Conforme Impresso no Cartão</label>
										</div>
										<div class="clear"></div>
									</fieldset>
                                    <div class="clear"></div>
                                </div>

                                 <div id="bloco_debito" class="<?php  echo ($this->view->parametros->forma_pgto == 'debito') ? '' : 'invisivel' ;?>">
                                    <fieldset class="field_maior">
                                        <legend>Débito Automático</legend>
                                         <div class="campo maior">
                                             <label for="cmdfpdebito_banoid">Banco *</label>
                                            <select id="cmdfpdebito_banoid" name="cmdfpdebito_banoid">
                                                <option value="">Escolha</option>
                                            </select>
                                            <img id="img_cmdfpdebito_banoid" class="carregando invisivel" src="images/ajax-loader-circle.gif">
                                            <input type="hidden" id="cmdfpdebito_banoid_recarga_tela" value="<?php echo $this->view->parametros->cmdfpdebito_banoid;?>" />
                                        </div>
                                        <div class="clear"></div>
                                        <div class="campo menor">
                                            <label for="cmdfpdebito_agencia">Agência *</label>
                                           <input id="cmdfpdebito_agencia" name="cmdfpdebito_agencia" class="campo numerico" type="text"
                                                value="<?php echo $this->view->parametros->cmdfpdebito_agencia;?>">
                                        </div>
                                        <div class="campo medio">
                                            <label for="cmdfpdebito_cc">Conta *</label>
                                           <input id="cmdfpdebito_cc" name="cmdfpdebito_cc" class="campo numerico" type="text"
                                                value="<?php echo $this->view->parametros->cmdfpdebito_cc;?>">
                                        </div>
                                     </fieldset>
                                     <div class="clear"></div>
                                 </div>

                                  <div class="clear"></div>
                             </div>
                        </div>
                    </div>

                     <div id="bloco_dados_acessorios" class="<?php echo ($this->view->parametros->cmptrecebe_dados_financeiro == 'f') ? 'invisivel' : ''; ?>">
                        <?php  require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/aba_cadastro_modificacao/acessorios.php"; ?>
                    </div>

                    <?php  require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/aba_cadastro_modificacao/cadastro_contato.php"; ?>

                 </form>
                 <div class="separador"></div>
            </div>
            <div class="separador"></div>
        </div>

        <div class="bloco_acoes">
            <button type="button" id="btn_confirmar_mofificacao" class="<?php echo (($this->view->parametros->anexar_arquivo != 't') && ($this->view->parametros->acoes_lote !='t'))  ? '' : ' invisivel' ;?>">Confirmar</button>
            <button type="button" id="btn_prosseguir_lote" class="<?php echo (($this->view->parametros->anexar_arquivo != 't') && ($this->view->parametros->acoes_lote =='t')) ? '' : 'invisivel' ;?>">Prosseguir</button>
            <button type="button" id="btn_prosseguir_anexos" class="<?php echo (($this->view->parametros->anexar_arquivo == 't') && ($this->view->parametros->acoes_lote !='t')) ? '' : 'invisivel' ;?>">Prosseguir</button>
            <button type="button" id="btn_voltar">Voltar</button>
            <button type="button" id="btn_teste">Teste</button>
        </div>