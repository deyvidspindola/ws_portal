<?php require_once _MODULEDIR_ . "Relatorio/View/rel_ocorrencia_novo_irv_congelado/cabecalho.php"; ?>

    <style type="text/css">
        @media print {
            /* media-specific rules */
            .formulario, .noprint, .bloco_acoes, .mensagem, .menuPrincipal {
                display: none;
            }

            div.listagem table th, div.listagem table td {
                padding: 5px;
                border: 1px solid #000000;
            }
            div.modulo_conteudo {
                border: 1px solid #FFFFFF;
                margin: 0 0;
                padding: 0 0;
            }
            div.bloco_titulo {
                border: 1px solid #FFFFFF;
                font-size: 12px;
                font-weight: bold;
                height: 25px;
                line-height: 25px;
                margin: 0 0px;
                padding: 0 0px;
                vertical-align: middle;
            }
            div.modulo_titulo {
                border: 1px solid #FFFFFF;
                font-size: 14px;
                font-weight: bold;
                height: 30px;
                line-height: 30px;
                margin: 0 0;
                padding: 0 0;
                vertical-align: middle;
            }
        }
        @media screen {
            .listagem {
                overflow-x: scroll;
                white-space: nowrap;
            }
        }

    </style>
    <!-- Mensagens-->
    <div id="mensagem_info" class="mensagem info">Os campos com * são obrigatórios.</div>

    <div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemErro; ?>
    </div>

    <div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemAlerta; ?>
    </div>

    <div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemSucesso; ?>
    </div>


    <form id="form" method="post" action="rel_ocorrencia_novo_irv_congelado.php" class="noprint">
    <input type="hidden" id="acao" name="acao" value="visualizacaoRelatorioCongelado"/>
    <input type="hidden" id="sub_acao" name="sub_acao" value="pesquisarCongelados"/>
    <input type="hidden" id="congeladosID" name="congeladosID" value="<?php echo $this->view->parametros->congeladosID ?>"/>
        <div class="bloco_titulo">Dados para Pesquisa</div>
            <div class="bloco_conteudo">
                <div class="formulario">

                    <!-- COLUNA DA ESQUERDA -->

                    <div style="float: left">
                        <div class="campo medio">
                            <label for="ococdtipo_relatorio">Tipo</label>
                            <input type="hidden" name="ococdtipo_relatorio" id="ococdtipo_relatorio" value="<?php echo trim($this->view->parametros->ococdtipo_relatorio);?>" />
                            <select id="ococdtipo_relatorio_combo" name="ococdtipo_relatorio_combo" disabled="disabled">
                                <option <?php echo ($this->view->parametros->ococdtipo_relatorio == 'A') ? 'selected="selected"' : ($this->view->parametros->ococdtipo_relatorio != 'A' && !in_array($this->view->parametros->ococdtipo_relatorio, array('P','D','M','S','R')) ? 'selected="selected"' : '') ?> value="A">Analítico</option>
                                <option <?php echo ($this->view->parametros->ococdtipo_relatorio == 'P') ? 'selected="selected"' : '' ?> value="P">Apoio</option>
                                <option <?php echo ($this->view->parametros->ococdtipo_relatorio == 'D') ? 'selected="selected"' : '' ?> value="D">Apoio Detalhado</option>
                                <option <?php echo ($this->view->parametros->ococdtipo_relatorio == 'M') ? 'selected="selected"' : '' ?> value="M">Macro</option>
                                <option <?php echo ($this->view->parametros->ococdtipo_relatorio == 'S') ? 'selected="selected"' : '' ?> value="S">Sintético</option>
                                <option <?php echo ($this->view->parametros->ococdtipo_relatorio == 'R') ? 'selected="selected"' : '' ?> value="R">Sintético Resumido</option>
                            </select>
                        </div>
                        <div class="clear"></div>
                        <div class="campo data periodo">
                            <div class="inicial">
                                <label for="ococdperiodo_inicial">Período *</label>
                                <input id="ococdperiodo_inicial" name="ococdperiodo_inicial" maxlength="10" value="<?php echo $this->view->parametros->ococdperiodo_inicial ?>" class="campo" type="text">
                            </div>
                            <div class="campo label-periodo">a</div>
                            <div class="final">
                                <label for="ococdperiodo_final">&nbsp;</label>
                                <input id="ococdperiodo_final" name="ococdperiodo_final" maxlength="10" value="<?php echo $this->view->parametros->ococdperiodo_final ?>" class="campo" type="text">
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="campo medio">
                            <label for="filtrar_motivo">Motivo</label>
                            <select id="filtrar_motivo" name="filtrar_motivo">
                                <option value="" <?php echo trim($this->view->parametros->filtrar_motivo) == '' ? 'selected="selected"' : '' ?>>Escolha</option>
                                <?php foreach ($this->view->parametros->listarMotivos AS $motivo) : ?>
                                    <option value="<?php echo $motivo['id'] ?>" <?php echo trim($this->view->parametros->filtrar_motivo) == $motivo['id'] ? 'selected="selected"' : '' ?>><?php echo $motivo['label'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="clear"></div>

                        <div class="formularios formulario_analitico formulario_macro">
                            <div class="campo medio">
                                <label for="filtrar_status">Status</label>
                                <select id="filtrar_status" name="filtrar_status">
                                    <option value="" <?php echo trim($this->view->parametros->filtrar_status) == '' ? 'selected="selected"' : ''?>>Todos</option>
                                    <option value="A" <?php echo $this->view->parametros->filtrar_status == 'A' ? 'selected="selected"' : ''?> >Em Andamento</option>
                                    <option value="S" <?php echo $this->view->parametros->filtrar_status == 'S' ? 'selected="selected"' : ''?> >Sem Contato</option>
                                    <option value="P" <?php echo $this->view->parametros->filtrar_status == 'P' ? 'selected="selected"' : ''?> >Pendentes</option>
                                    <option value="R" <?php echo $this->view->parametros->filtrar_status == 'R' ? 'selected="selected"' : ''?> >Recuperado</option>
                                    <option value="N" <?php echo $this->view->parametros->filtrar_status == 'N' ? 'selected="selected"' : ''?> >N&atilde;o Recuperado</option>
                                    <option value="C" <?php echo $this->view->parametros->filtrar_status == 'C' ? 'selected="selected"' : ''?> >Conclu&iacute;dos</option>
                                    <option value="L" <?php echo $this->view->parametros->filtrar_status == 'L' ? 'selected="selected"' : ''?> >Cancelados</option>
                                </select>
                            </div>
                            <div class="clear"></div>
                        </div>


                        <div class="campo menor">
                            <label for="filtrar_tipo_veiculo">Tipo Veículo</label>
                            <select id="filtrar_tipo_veiculo" name="filtrar_tipo_veiculo">
                                <option value=""  <?php echo $this->view->parametros->filtrar_tipo_veiculo == ''  ? 'selected="selected"' : '' ?>>Todos</option>
                                <option value="L" <?php echo $this->view->parametros->filtrar_tipo_veiculo == 'L' ? 'selected="selected"' : '' ?>>Leves</option>
                                <option value="P" <?php echo $this->view->parametros->filtrar_tipo_veiculo == 'P' ? 'selected="selected"' : '' ?>>Pesados</option>
                                <option value="M" <?php echo $this->view->parametros->filtrar_tipo_veiculo == 'M' ? 'selected="selected"' : '' ?>>Motos</option>
                            </select>
                        </div>

                        <div class="campo menor">
                            <label for="filtrar_estado">Estado</label>
                            <select id="filtrar_estado" name="filtrar_estado">
                                <option value=""   <?php echo ($this->view->parametros->filtrar_estado == "") ? "selected='selected'" : '' ?>>Todos</option>
                                <option value="AC" <?php echo ($this->view->parametros->filtrar_estado == "AC") ? "selected='selected'" : '' ?>>AC</option>
                                <option value="AL" <?php echo ($this->view->parametros->filtrar_estado == "AL") ? "selected='selected'" : '' ?>>AL</option>
                                <option value="AM" <?php echo ($this->view->parametros->filtrar_estado == "AM") ? "selected='selected'" : '' ?>>AM</option>
                                <option value="AP" <?php echo ($this->view->parametros->filtrar_estado == "AP") ? "selected='selected'" : '' ?>>AP</option>
                                <option value="BA" <?php echo ($this->view->parametros->filtrar_estado == "BA") ? "selected='selected'" : '' ?>>BA</option>
                                <option value="CE" <?php echo ($this->view->parametros->filtrar_estado == "CE") ? "selected='selected'" : '' ?>>CE</option>
                                <option value="DF" <?php echo ($this->view->parametros->filtrar_estado == "DF") ? "selected='selected'" : '' ?>>DF</option>
                                <option value="ES" <?php echo ($this->view->parametros->filtrar_estado == "ES") ? "selected='selected'" : '' ?>>ES</option>
                                <option value="GO" <?php echo ($this->view->parametros->filtrar_estado == "GO") ? "selected='selected'" : '' ?>>GO</option>
                                <option value="MA" <?php echo ($this->view->parametros->filtrar_estado == "MA") ? "selected='selected'" : '' ?>>MA</option>
                                <option value="MG" <?php echo ($this->view->parametros->filtrar_estado == "MG") ? "selected='selected'" : '' ?>>MG</option>
                                <option value="MS" <?php echo ($this->view->parametros->filtrar_estado == "MS") ? "selected='selected'" : '' ?>>MS</option>
                                <option value="MT" <?php echo ($this->view->parametros->filtrar_estado == "MT") ? "selected='selected'" : '' ?>>MT</option>
                                <option value="PA" <?php echo ($this->view->parametros->filtrar_estado == "PA") ? "selected='selected'" : '' ?>>PA</option>
                                <option value="PB" <?php echo ($this->view->parametros->filtrar_estado == "PB") ? "selected='selected'" : '' ?>>PB</option>
                                <option value="PE" <?php echo ($this->view->parametros->filtrar_estado == "PE") ? "selected='selected'" : '' ?>>PE</option>
                                <option value="PI" <?php echo ($this->view->parametros->filtrar_estado == "PI") ? "selected='selected'" : '' ?>>PI</option>
                                <option value="PR" <?php echo ($this->view->parametros->filtrar_estado == "PR") ? "selected='selected'" : '' ?>>PR</option>
                                <option value="RJ" <?php echo ($this->view->parametros->filtrar_estado == "RJ") ? "selected='selected'" : '' ?>>RJ</option>
                                <option value="RN" <?php echo ($this->view->parametros->filtrar_estado == "RN") ? "selected='selected'" : '' ?>>RN</option>
                                <option value="RO" <?php echo ($this->view->parametros->filtrar_estado == "RO") ? "selected='selected'" : '' ?>>RO</option>
                                <option value="RR" <?php echo ($this->view->parametros->filtrar_estado == "RR") ? "selected='selected'" : '' ?>>RR</option>
                                <option value="RS" <?php echo ($this->view->parametros->filtrar_estado == "RS") ? "selected='selected'" : '' ?>>RS</option>
                                <option value="SC" <?php echo ($this->view->parametros->filtrar_estado == "SC") ? "selected='selected'" : '' ?>>SC</option>
                                <option value="SE" <?php echo ($this->view->parametros->filtrar_estado == "SE") ? "selected='selected'" : '' ?>>SE</option>
                                <option value="SP" <?php echo ($this->view->parametros->filtrar_estado == "SP") ? "selected='selected'" : '' ?>>SP</option>
                                <option value="TO" <?php echo ($this->view->parametros->filtrar_estado == "TO") ? "selected='selected'" : '' ?>>TO</option>
                            </select>
                        </div>
                        <div class="clear"></div>

                        <div class="campo medio">
                            <label for="filtrar_marca">Marca</label>
                            <select id="filtrar_marca" name="filtrar_marca">
                                <option value="" <?php echo trim($this->view->parametros->filtrar_marca) == '' ? 'selected="selected"' : '' ?>>Todas</option>
                                <?php foreach ($this->view->parametros->listarMarcas AS $marca) : ?>
                                    <option value="<?php echo trim($marca->id) ?>" <?php echo trim($this->view->parametros->filtrar_marca) == $marca->id ? 'selected="selected"' : '' ?>><?php echo $marca->label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="clear"></div>

                        <div class="campo medio">
                            <label for="filtrar_modelo">Modelo</label>
                            <select id="filtrar_modelo" name="filtrar_modelo">
                                <option value="" <?php echo trim($this->view->parametros->filtrar_modelo) == '' ? 'selected="selected"' : '' ?>>Todas</option>
                                <?php foreach ($this->view->parametros->listarModelos AS $modelo) : ?>
                                    <option value="<?php echo trim($modelo['label']) ?>" <?php echo trim($this->view->parametros->filtrar_modelo) == $modelo['id'] ? 'selected="selected"' : '' ?>><?php echo $modelo['label'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="clear"></div>


                        <div class="campo maior">
                            <label for="filtrar_seguradora">Seguradora</label>
                            <select id="filtrar_seguradora" name="filtrar_seguradora">
                                <option value="" <?php echo trim($this->view->parametros->filtrar_seguradora) == '' ? 'selected="selected"' : '' ?>>Todas</option>
                                <option value="CLI"  <?php echo trim($this->view->parametros->filtrar_seguradora) == "CLI"  ? 'selected="selected"' : ''; ?>>Cliente - TODOS</option>
                                <option value="SEG"  <?php echo trim($this->view->parametros->filtrar_seguradora) == "SEG"  ? 'selected="selected"' : ''; ?>>Seguradora - TODOS</option>
                                <option value="BRAT" <?php echo trim($this->view->parametros->filtrar_seguradora) == "BRAT" ? 'selected="selected"' : ''; ?>>Bradesco - TODOS</option>
                                <option value="ROD"  <?php echo trim($this->view->parametros->filtrar_seguradora) == "ROD"  ? 'selected="selected"' : ''; ?>>Rodobens - TODOS</option>
                                <option value="RODS" <?php echo trim($this->view->parametros->filtrar_seguradora) == "RODS" ? 'selected="selected"' : ''; ?>>Rodobens - ESPECIAL</option>
                                <option value="SULT" <?php echo trim($this->view->parametros->filtrar_seguradora) == "SULT" ? 'selected="selected"' : ''; ?>>Sulamerica - TODOS</option>
                                <option value="UNBT" <?php echo trim($this->view->parametros->filtrar_seguradora) == "UNBT" ? 'selected="selected"' : ''; ?>>Unibanco - TODOS</option>
                                <?php foreach ($this->view->parametros->listarSeguradorasTipoContrato as $id => $descricao): ?>
                                    <option value="<?php echo trim($descricao); ?>" <?php echo trim($this->view->parametros->filtrar_seguradora) == trim($descricao) ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                <?php endforeach; ?>
                                <?php foreach ($this->view->parametros->listarSeguradorasSeguradora as $id => $descricao): ?>
                                    <option value="<?php echo trim($descricao); ?>" <?php echo trim($this->view->parametros->filtrar_seguradora) == $id ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>


                        <div class="clear"></div>

                        <div class="formularios formulario_analitico formulario_apoio formulario_sintetico formulario_sintetico_resumido">
                            <div class="campo maior">
                                <label for="filtrar_classe_equipamento">Classe do Equipamento</label>
                                <select id="filtrar_classe_equipamento" name="filtrar_classe_equipamento">
                                    <option value="">Escolha</option>
                                    <?php foreach ($this->view->parametros->listarClassesEquipamento as $id => $descricao): ?>
                                        <option value="<?php echo trim($descricao); ?>" <?php echo trim($this->view->parametros->filtrar_classe_equipamento) == trim($descricao) ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="clear"></div>
                            </div>

                            <div class="formularios formulario_analitico formulario_apoio formulario_sintetico formulario_sintetico_resumido">
                            <div class="campo maior">
                                <label for="filtrar_classe_contrato">Classe do Contrato</label>
                                <select id="filtrar_classe_contrato" name="filtrar_classe_contrato">
                                    <option value="">Escolha</option>
                                     <?php foreach ($this->view->parametros->listarClassesEquipamento as $id => $descricao): ?>
                                        <option value="<?php echo trim($descricao); ?>" <?php echo trim($this->view->parametros->filtrar_classe_contrato) == trim($descricao) ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="clear"></div>
                            </div>

                            <div class="campo medio">
                            <label for="filtrar_tipo_ocorrencia">Tipo Ocorrência</label>
                            <select id="filtrar_tipo_ocorrencia" name="filtrar_tipo_ocorrencia">
                                <option value="">Escolha</option>
                                <?php foreach ($this->view->parametros->listarTiposOcorrencia as $id => $descricao): ?>
                                    <option value="<?php echo $id; ?>" <?php echo trim($this->view->parametros->filtrar_tipo_ocorrencia) == $id ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                <?php endforeach; ?>
                            </select>
                            </div>
                            <div class="clear"></div>
                            <div class="campo medio">
                            <label for="filtrar_forma_notificacao">Forma Notificação</label>
                            <select id="filtrar_forma_notificacao" name="filtrar_forma_notificacao">
                                <option value="">Escolha</option>
                                <?php foreach ($this->view->parametros->listarFormaNotificacao as $id => $descricao): ?>
                                    <option value="<?php echo trim($descricao); ?>" <?php echo trim($this->view->parametros->filtrar_forma_notificacao) == trim($descricao) ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                <?php endforeach; ?>
                            </select>
                            </div>
                            <div class="clear"></div>


                            <div class="formularios formulario_macro">

                                <div class="campo menor">
                                    <label for="filtrar_tipo_cidade">Tipo Cidade/UF</label>
                                    <select id="filtrar_tipo_cidade" name="filtrar_tipo_cidade">
                                        <option value="">Escolha</option>
                                        <?php foreach ($this->view->parametros->listarTipoResidencia as $id => $descricao): ?>
                                            <option value="<?php echo $id; ?>" <?php echo trim($this->view->parametros->filtrar_tipo_cidade) == $id ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="campo medio">
                                    <label for="filtrar_cidade">Cidade</label>
                                    <select id="filtrar_cidade" name="filtrar_cidade">
                                        <option value="">Escolha</option>
                                        <?php foreach ($this->view->parametros->listarCidades as $cidade): ?>
                                            <option value="<?php echo $cidade['id']; ?>" <?php echo trim($this->view->parametros->filtrar_cidade) == $cidade['id'] ? 'selected="selected"' : ''; ?>><?php echo $cidade['label']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="clear"></div>

                                <div class="campo medio">
                                    <label for="filtrar_chassi">Chassi</label>
                                    <input id="filtrar_chassi" name="filtrar_chassi" value="<?php echo trim($this->view->parametros->filtrar_chassi) ?>" class="campo" type="text">
                                </div>
                                <div class="clear"></div>

                                <div class="campo menor">
                                    <label for="filtrar_veiculo_carregado">Veículo Carregado</label>
                                    <select id="filtrar_veiculo_carregado" name="filtrar_veiculo_carregado">
                                        <option value="">Todos</option>
                                        <?php foreach ($this->view->parametros->listarVeiculoCarregado as $id => $descricao): ?>
                                            <option value="<?php echo $id; ?>" <?php echo trim($this->view->parametros->filtrar_veiculo_carregado) == $id ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="campo menor">
                                    <label for="filtrar_valor_carga_condicao">Valor da Carga</label>
                                    <select id="filtrar_valor_carga_condicao" name="filtrar_valor_carga_condicao">
                                        <option value="="  <?php echo trim($this->view->parametros->filtrar_valor_carga_condicao) == '='  ? 'selected="selected"' : '' ?>>=</option>
                                        <option value=">=" <?php echo trim($this->view->parametros->filtrar_valor_carga_condicao) == '>=' ? 'selected="selected"' : '' ?>> &gt;= </option>
                                        <option value="<=" <?php echo trim($this->view->parametros->filtrar_valor_carga_condicao) == '<=' ? 'selected="selected"' : '' ?>> &lt;= </option>
                                        <option value=">"  <?php echo trim($this->view->parametros->filtrar_valor_carga_condicao) == '>'  ? 'selected="selected"' : '' ?>> &gt; </option>
                                        <option value="<"  <?php echo trim($this->view->parametros->filtrar_valor_carga_condicao) == '<'  ? 'selected="selected"' : '' ?>> &lt; </option>
                                    </select>
                                </div>
                                <div class="campo menor">
                                    <label for="filtrar_valor_carga">&nbsp;</label>
                                    <?php $this->view->parametros->filtrar_valor_carga = trim($this->view->parametros->filtrar_valor_carga) != '' ? number_format($this->view->parametros->filtrar_valor_carga,2,',','.') : ''; ?>
                                    <input id="filtrar_valor_carga" maxlength="16" name="filtrar_valor_carga" value="<?php echo trim($this->view->parametros->filtrar_valor_carga)?>" class="campo" type="text">
                                </div>
                                <div class="clear"></div>

                                <div class="campo medio">
                                    <label for="filtrar_cpf_cnpj">CPF/CNPJ</label>
                                    <input id="filtrar_cpf_cnpj" name="filtrar_cpf_cnpj" value="<?php echo trim($this->view->parametros->filtrar_cpf_cnpj) ?>" class="campo" type="text">
                                </div>
                                <div class="clear"></div>

                                <div class="campo medio">
                                    <label for="filtrar_instalado_cargo_track">Instalado Cargo Tracck</label>
                                    <select id="filtrar_instalado_cargo_track" name="filtrar_instalado_cargo_track">
                                        <option value="">Todos</option>
                                        <?php foreach ($this->view->parametros->listarInstaladoCargoTrack as $id => $descricao): ?>
                                            <option value="<?php echo $id; ?>" <?php echo trim($this->view->parametros->filtrar_instalado_cargo_track) == $id ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="clear"></div>

                        </div>





                    </div>
                    <!-- FIM COLUNA DA ESQUERDA -->

                    <!-- COLUNA DA DIREITA -->

                    <div style="float: left; margin-left: 100px;">

                        <div class="campo medio">
                            <label for="filtrar_modalidade_contrato">Modalidade de Contrato</label>
                            <select id="filtrar_modalidade_contrato" name="filtrar_modalidade_contrato">
                                <option value="">Escolha</option>
                                <?php foreach ($this->view->parametros->listarModalidadeContrato as $id => $descricao): ?>
                                    <option value="<?php echo $id; ?>" <?php echo trim($this->view->parametros->filtrar_modalidade_contrato) == $id ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="clear"></div>                       

                        <div class="formularios formulario_analitico formulario_macro formulario_sintetico formulario_sintetico_resumido">
                            <div class="campo medio">
                                <label for="filtrar_cliente">Cliente</label>
                                <input id="filtrar_cliente" name="filtrar_cliente" value="<?php echo trim($this->view->parametros->filtrar_cliente) ?>" class="campo" type="text">
                            </div>
                        </div>

                        <div class="campo menor">
                            <label for="filtrar_placa">Placa</label>
                            <input id="filtrar_placa" name="filtrar_placa" value="<?php echo trim($this->view->parametros->filtrar_placa) ?>" class="campo" type="text">
                        </div>
                        <div class="clear"></div>

                        <div class="clear"></div>

                        <div class="formularios formulario_analitico formulario_apoio formulario_sintetico formulario_sintetico_resumido">
                            <div class="campo maior">
                                <label for="filtrar_corretora">Corretora</label>
                                <input id="filtrar_corretora" name="filtrar_corretora" value="<?php echo trim($this->view->parametros->filtrar_corretora) ?>" class="campo" type="text">
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="formularios formulario_analitico formulario_apoio formulario_sintetico formulario_sintetico_resumido">
                            <div class="campo menor">
                                <label for="filtrar_regiao">Região</label>
                                <select id="filtrar_regiao" name="filtrar_regiao">
                                    <option value="">Escolha</option>
                                    <?php foreach ($this->view->parametros->listarRegiao as $id => $descricao): ?>
                                        <option value="<?php echo $id; ?>" <?php echo trim($this->view->parametros->filtrar_regiao) == $id ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="formularios formulario_analitico formulario_apoio formulario_sintetico formulario_sintetico_resumido">
                            <div class="campo menor">
                                <label for="filtrar_classe_grupo">Classe do Grupo</label>
                                <select id="filtrar_classe_grupo" name="filtrar_classe_grupo">
                                    <option value="">Escolha</option>
                                    <?php foreach ($this->view->parametros->listarClasseGrupo as $id => $descricao): ?>
                                        <option value="<?php echo trim($descricao); ?>" <?php echo trim($this->view->parametros->filtrar_classe_grupo) == trim($descricao) ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="formularios formulario_analitico formulario_apoio formulario_sintetico formulario_sintetico_resumido">
                            <div class="campo menor">
                                <label for="filtrar_atendente">Atendente</label>
                                <select id="filtrar_atendente" name="filtrar_atendente">
                                    <option value="">Escolha</option>
                                    <?php foreach ($this->view->parametros->listarAtendentes as $id => $descricao): ?>
                                        <option value="<?php echo trim($descricao); ?>" <?php echo trim($this->view->parametros->filtrar_atendente) == trim($descricao) ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="formularios formulario_apoio">
                            <div class="campo menor">
                                <label for="filtrar_apoio">Apoio</label>
                                <select id="filtrar_apoio" name="filtrar_apoio">
                                    <option value="">Todos</option>
                                    <?php foreach ($this->view->parametros->listarApoio as $id => $descricao): ?>
                                        <option value="<?php echo trim($descricao); ?>" <?php echo trim($this->view->parametros->filtrar_apoio) == trim($descricao) ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="campo medio">
                            <label for="filtrar_equipamento_projeto">Projeto do Equipamento</label>
                            <select id="filtrar_equipamento_projeto" name="filtrar_equipamento_projeto">
                                <option value="">Escolha</option>
                                <?php foreach ($this->view->parametros->listarEquipamentoProjeto as $id => $descricao): ?>
                                    <option value="<?php echo trim($descricao); ?>" <?php echo trim($this->view->parametros->filtrar_equipamento_projeto) == trim($descricao) ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="clear"></div>

                        <div class="campo medio">
                            <label for="filtrar_motivo_equ_sem_contato">Motivo Equipamento S/ Contato</label>
                            <select id="filtrar_motivo_equ_sem_contato" name="filtrar_motivo_equ_sem_contato">
                                <option value="">Escolha</option>
                                <?php foreach ($this->view->parametros->listarMotivoEqupSemContato as $id => $descricao): ?>
                                    <option value="<?php echo trim($descricao); ?>" <?php echo trim($this->view->parametros->filtrar_motivo_equ_sem_contato) == trim($descricao) ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="clear"></div>

                        <div class="formularios formulario_analitico">
                            <fieldset class="medio">
                                <legend>Exibir Endereço do Cliente</legend>
                                <input type="checkbox" value="1" <?php echo $this->view->parametros->filtrar_exibir_endereco == '1' ? 'checked="checked"' : '';  ?> name="filtrar_exibir_endereco" id="filtrar_exibir_endereco">
                                <label for="filtrar_exibir_endereco">Exibir</label>
                            </fieldset>

                            <div class="clear"></div>
                        </div>



                        <div class="formularios formulario_macro">

                            <div class="campo medio">
                                <label for="filtrar_tipo_periodo">Tipo de Período</label>
                                <select id="filtrar_tipo_periodo" name="filtrar_tipo_periodo">
                                    <?php foreach ($this->view->parametros->listarTipoPeriodo as $id => $descricao): ?>
                                        <option value="<?php echo $id; ?>" <?php echo trim($this->view->parametros->filtrar_tipo_periodo) == $id ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="clear"></div>

                            <div class="campo medio">
                                <label for="filtrar_estado_recuperacao">UF Recuperação</label>
                                <select id="filtrar_estado_recuperacao" name="filtrar_estado_recuperacao">
                                    <option value=""   <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "") ? "selected='selected'" : '' ?>>Todos</option>
                                    <option value="AC" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "AC") ? "selected='selected'" : '' ?>>AC</option>
                                    <option value="AL" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "AL") ? "selected='selected'" : '' ?>>AL</option>
                                    <option value="AM" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "AM") ? "selected='selected'" : '' ?>>AM</option>
                                    <option value="AP" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "AP") ? "selected='selected'" : '' ?>>AP</option>
                                    <option value="BA" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "BA") ? "selected='selected'" : '' ?>>BA</option>
                                    <option value="CE" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "CE") ? "selected='selected'" : '' ?>>CE</option>
                                    <option value="DF" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "DF") ? "selected='selected'" : '' ?>>DF</option>
                                    <option value="ES" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "ES") ? "selected='selected'" : '' ?>>ES</option>
                                    <option value="GO" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "GO") ? "selected='selected'" : '' ?>>GO</option>
                                    <option value="MA" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "MA") ? "selected='selected'" : '' ?>>MA</option>
                                    <option value="MG" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "MG") ? "selected='selected'" : '' ?>>MG</option>
                                    <option value="MS" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "MS") ? "selected='selected'" : '' ?>>MS</option>
                                    <option value="MT" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "MT") ? "selected='selected'" : '' ?>>MT</option>
                                    <option value="PA" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "PA") ? "selected='selected'" : '' ?>>PA</option>
                                    <option value="PB" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "PB") ? "selected='selected'" : '' ?>>PB</option>
                                    <option value="PE" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "PE") ? "selected='selected'" : '' ?>>PE</option>
                                    <option value="PI" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "PI") ? "selected='selected'" : '' ?>>PI</option>
                                    <option value="PR" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "PR") ? "selected='selected'" : '' ?>>PR</option>
                                    <option value="RJ" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "RJ") ? "selected='selected'" : '' ?>>RJ</option>
                                    <option value="RN" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "RN") ? "selected='selected'" : '' ?>>RN</option>
                                    <option value="RO" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "RO") ? "selected='selected'" : '' ?>>RO</option>
                                    <option value="RR" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "RR") ? "selected='selected'" : '' ?>>RR</option>
                                    <option value="RS" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "RS") ? "selected='selected'" : '' ?>>RS</option>
                                    <option value="SC" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "SC") ? "selected='selected'" : '' ?>>SC</option>
                                    <option value="SE" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "SE") ? "selected='selected'" : '' ?>>SE</option>
                                    <option value="SP" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "SP") ? "selected='selected'" : '' ?>>SP</option>
                                    <option value="TO" <?php echo ($this->view->parametros->filtrar_estado_recuperacao == "TO") ? "selected='selected'" : '' ?>>TO</option>
                                </select>
                            </div>
                            <div class="clear"></div>

                            <div class="campo medio">
                                <label for="filtrar_recuperado_apoio">Recuperado pelo Apoio</label>
                                <select id="filtrar_recuperado_apoio" name="filtrar_recuperado_apoio">
                                    <option value="">Todos</option>
                                    <?php foreach ($this->view->parametros->listarRecuperadoApoio as $id => $descricao): ?>
                                        <option value="<?php echo $id; ?>" <?php echo trim($this->view->parametros->filtrar_recuperado_apoio) == $id ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="clear"></div>

                            <div class="campo maior">
                                <label for="filtrar_classe">Classe</label>
                                <select id="filtrar_classe" name="filtrar_classe">
                                    <option value="">Todas</option>
                                    <?php foreach ($this->view->parametros->listarClassesEquipamento as $id => $descricao): ?>
                                        <option value="<?php echo trim($descricao); ?>" <?php echo trim($this->view->parametros->filtrar_classe) == trim($descricao) ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="clear"></div>

                            <div class="campo menor">
                                <label for="filtrar_tipo_pessoa">Tipo Pessoa</label>
                                <select id="filtrar_tipo_pessoa" name="filtrar_tipo_pessoa">
                                    <option value="">Todas</option>
                                    <?php foreach ($this->view->parametros->listarTipoPessoa as $id => $descricao): ?>
                                        <option value="<?php echo $id; ?>" <?php echo trim($this->view->parametros->filtrar_tipo_pessoa) == $id ? 'selected="selected"' : ''; ?>><?php echo $descricao; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="clear"></div>

                            <div class="campo maior">
                                <label for="filtrar_bo">Nº B.O.</label>
                                <input id="filtrar_bo" name="filtrar_bo" value="<?php echo trim($this->view->parametros->filtrar_bo) ?>" class="campo" type="text">
                            </div>
                            <div class="clear"></div>

                        </div>

                    </div>

                    <!-- FIM COLUNA DA DIREITA -->

                    <div class="clear"></div>
                </div>
            </div>
            <div class="bloco_acoes">
            	<button id="btn_pesquisa_relatorio_congelado" type="button">Pesquisar</button>
            </div>

    </form>


<?php if (trim($_POST['sub_acao'])=='gerarPdf'): ?>

     <div class="resultado">

        <div class="separador"></div>

        <div class="bloco_titulo resultado">Download</div>
        <div class="bloco_conteudo">

            <div class="conteudo centro">
                <a href="download.php?arquivo=<?php echo $this->view->parametros->arquivo_pdf ?>" target="_blank">
                    <img src="images/icones/t3/caixa2.jpg"><br><?php echo basename($this->view->parametros->arquivo_pdf) ?>
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <form id="form_resultado"  method="post" action="rel_ocorrencia_novo_irv_congelado.php?acao=pesquisar">
       <?php
            if ($this->view->dados instanceof PgDataList){
               $viewRelatorio = $this->view->tiposRelatorio[$this->view->parametros->ococdtipo_relatorio];
               $arquivo_resultado =  "resultado_pesquisa_".$viewRelatorio.".php";
               include_once($arquivo_resultado);
              /*   if($this->view->parametros->ococdtipo_relatorio && count($this->view->dados) > 0) {
        			$viewRelatorio = $this->view->tiposRelatorio[$this->view->parametros->ococdtipo_relatorio];
                    $arquivo_resultado =  "resultado_pesquisa_".$viewRelatorio.".php";
                    include_once($arquivo_resultado);
                } */
            }
        ?>
    </form>
<?php endif; ?>
</div>
    <?php if (count($this->view->dados) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >jQuery(document).ready(function() {
        showFormErros(<?php echo json_encode($this->view->dados); ?>);
    });
    </script>

    <?php endif; ?>

<?php require_once _MODULEDIR_ . "Relatorio/View/rel_ocorrencia_novo_irv_congelado/rodape.php"; ?>
