<div class="bloco_titulo">Cadastro</div>
<div class="bloco_conteudo">
    <div class="formulario">
        <input type="hidden" id="dataAtual" value="<?php echo date('d/m/Y'); ?>">

        <div class="campo maior">
            <label for="solicitante">Solicitante *</label>
            <?php if($this->view->parametros->permissaoEdicao == 'Solicitante' && ( $this->view->parametros->statusRequisicao == 'P') || !isset($this->view->parametros->statusRequisicao) ) : ?>
            <select id="solicitante" name="solicitante">
                <option value="">Escolha</option>
                <?php foreach ($this->view->parametros->solicitantes as $solicitante) : ?>
                    <option value="<?php echo $solicitante->cd_usuario ?>" <?php echo (isset($this->view->parametros->solicitante) && ($this->view->parametros->solicitante == $solicitante->cd_usuario)) ? 'selected="selected"' : '' ?>>
                        <?php echo $solicitante->nm_usuario ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php else : ?>
            <input readonly="readonly" class="campo" type="text" value="<?php echo isset($this->view->parametros->nomeSolicitante) ? $this->view->parametros->nomeSolicitante : '' ?>">
            <input type="hidden" value="<?php echo isset($this->view->parametros->solicitante) ? $this->view->parametros->solicitante : '' ?>" name="solicitante" id="solicitante">
        <?php endif; ?>
        </div>
        <div class="clear"></div>

        <div class="campo maior">
            <label for="empresa">Empresa *</label>
        <?php if($this->view->parametros->permissaoEdicao == 'Solicitante' && ( $this->view->parametros->statusRequisicao == 'P') || !isset($this->view->parametros->statusRequisicao) ) : ?>
            <select id="empresa" name="empresa">
                <option value="">Escolha</option>
                <?php foreach ($this->view->parametros->todasEmpresas as $empresa) : ?>
                    <option value="<?php echo $empresa->tecoid ?>" <?php echo (isset($this->view->parametros->empresa) && ($this->view->parametros->empresa == $empresa->tecoid)) ? 'selected="selected"' : '' ?>>
                        <?php echo $empresa->tecrazao ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php else : ?>
            <?php foreach ($this->view->parametros->todasEmpresas as $empresa) : ?>
                <?php if($empresa->tecoid == $this->view->parametros->empresa) : ?>
                        <input readonly="readonly" class="campo" type="text" value="<?php echo $empresa->tecrazao ?>">
                <?php endif; ?>
            <?php endforeach; ?>
            <input type="hidden" value="<?php echo isset($this->view->parametros->empresa) ? $this->view->parametros->empresa : '' ?>" name="empresa" id="empresa">
        <?php endif; ?>
        </div>
        <div class="clear"></div>

        <div class="campo maior">
            <label for="centroCusto">Centro de Custo *</label>
        <?php if($this->view->parametros->permissaoEdicao == 'Solicitante' && ( $this->view->parametros->statusRequisicao == 'P') || !isset($this->view->parametros->statusRequisicao) ) : ?>
            <select id="centroCusto" name="centroCusto">
                <option value="">Escolha</option>
                <!-- Opções deste campo serão preenchidos por ajax -->
            </select>
            <input type="hidden" value="<?php echo isset($this->view->parametros->centroCusto) ? $this->view->parametros->centroCusto : '' ?>" id="postCentroCusto">
        <?php else : ?>
            <input readonly="readonly" class="campo" type="text" value="<?php echo isset($this->view->parametros->nomeCentroCusto) ? $this->view->parametros->nomeCentroCusto : '' ?>">
            <input type="hidden" value="<?php echo isset($this->view->parametros->centroCusto) ? $this->view->parametros->centroCusto : '' ?>" name="centroCusto" id="centroCusto">
        <?php endif; ?>
        </div>
        <div class="clear"></div>

        <div class="campo maior">
            <label for="justificativa">Justificativa *</label>
        <?php if($this->view->parametros->permissaoEdicao == 'Solicitante' && ( $this->view->parametros->statusRequisicao == 'P') || !isset($this->view->parametros->statusRequisicao) ) : ?>
            <textarea id="justificativa" name="justificativa" rows="5"><?php echo isset($this->view->parametros->justificativa) ? $this->view->parametros->justificativa : '' ?></textarea>
        <?php else : ?>
            <textarea readonly="readonly" id="justificativa" name="justificativa" rows="5"><?php echo isset($this->view->parametros->justificativa) ? $this->view->parametros->justificativa : '' ?></textarea>
        <?php endif; ?>
        </div>
        <div class="clear"></div>

        <div class="campo medio">
            <label for="tipoRequisicao">Tipo da Requisição *</label>
        <?php if($this->view->parametros->permissaoEdicao == 'Solicitante' && ( $this->view->parametros->statusRequisicao == 'P') || !isset($this->view->parametros->statusRequisicao) ) : ?>
            <select id="tipoRequisicao" name="tipoRequisicao">
                <option value="">Escolha</option>
                <option value="C" <?php echo (isset($this->view->parametros->tipoRequisicao) && $this->view->parametros->tipoRequisicao == 'C') ? 'selected="selected"' : '' ?>>Combustível - ticket car</option>
                <option value="A" <?php echo (isset($this->view->parametros->tipoRequisicao) && $this->view->parametros->tipoRequisicao == 'A') ? 'selected="selected"' : '' ?>>Adiantamento</option>
                <option value="L" <?php echo (isset($this->view->parametros->tipoRequisicao) && $this->view->parametros->tipoRequisicao  == 'L') ? 'selected="selected"' : '' ?>>Reembolso</option>
            </select>
        <?php else : 

            switch($this->view->parametros->tipoRequisicao) {
                case 'C':
                    $tipoRequisicao = 'Combustível - ticket car';
                    break;
                case 'A':
                    $tipoRequisicao = 'Adiantamento';
                    break;
                case 'L':
                    $tipoRequisicao = 'Reembolso';
                    break;
            }
        ?>
            <input readonly="readonly" class="campo" type="text" value="<?php echo $tipoRequisicao ?>" />
            <input id="tipoRequisicao" type="hidden" value="<?php echo isset($this->view->parametros->tipoRequisicao) ? $this->view->parametros->tipoRequisicao : '' ?>" name="tipoRequisicao" />
        <?php endif; ?>
        </div>
        <div class="clear"></div>

        <div id="blocoTiposRequisicao" style="display: none;">
            
            <div id="blocoCombustivel">
                <div class="campo medio">
                    <label for="projeto">Projeto</label>
                <?php if($this->view->parametros->permissaoEdicao == 'Solicitante' && ( $this->view->parametros->statusRequisicao == 'P') || !isset($this->view->parametros->statusRequisicao) ) : ?>
                    <select id="projeto" name="projeto">
                        <option value="">Escolha</option>
                        <?php foreach ($this->view->parametros->todosProjetos as $projeto) : ?>
                            <option value="<?php echo $projeto->rproid ?>" <?php echo (isset($this->view->parametros->projeto) && ($this->view->parametros->projeto == $projeto->rproid)) ? 'selected="selected"' : '' ?>>
                                <?php echo $projeto->rprnome ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else : ?>
                    <?php foreach ($this->view->parametros->todosProjetos as $projeto) : ?>
                        <?php if($projeto->rproid == $this->view->parametros->projeto) : ?>
                                <input readonly="readonly" class="campo" type="text" value="<?php echo $projeto->rprnome ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <input type="hidden" value="<?php echo isset($this->view->parametros->projeto) ? $this->view->parametros->projeto : '' ?>" name="projeto">
                <?php endif; ?>
                </div>
                <div class="clear"></div>

                <div class="campo medio">
                    <label for="idaVolta">Ida/Volta *</label>
                <?php if($this->view->parametros->permissaoEdicao == 'Solicitante' && ( $this->view->parametros->statusRequisicao == 'P') || !isset($this->view->parametros->statusRequisicao) ) : ?>
                    <select id="idaVolta" name="idaVolta">
                        <option value="">Escolha</option>
                        <option value="A" <?php echo (isset($this->view->parametros->idaVolta) && $this->view->parametros->idaVolta == 'A') ? 'selected="selected"' : '' ?>>Somente Ida</option>
                        <option value="I" <?php echo (isset($this->view->parametros->idaVolta) && $this->view->parametros->idaVolta == 'I') ? 'selected="selected"' : '' ?>>Ida e Volta</option>
                    </select>
                <?php else : ?>
                    <input readonly="readonly" class="campo" type="text" value="<?php echo isset($this->view->parametros->idaVolta) ? ($this->view->parametros->idaVolta == 'A' ? 'Somente Ida' : 'Ida e Volta') : '' ?>">
                    <input id="idaVolta" type="hidden" value="<?php echo isset($this->view->parametros->idaVolta) ? $this->view->parametros->idaVolta : '' ?>" name="idaVolta">
                <?php endif; ?>
                </div>
                <div class="clear"></div>

            <?php if($this->view->parametros->permissaoEdicao == 'Solicitante' && ( $this->view->parametros->statusRequisicao == 'P') || !isset($this->view->parametros->statusRequisicao) ) : ?>
                <div class="campo data periodo">
            <?php else : ?>
                <div class="campo periodo">
            <?php endif; ?>
                    <div class="inicial">
                        <label for="dtPartida">Data Partida *</label>
                <?php if($this->view->parametros->permissaoEdicao == 'Solicitante' && ( $this->view->parametros->statusRequisicao == 'P') || !isset($this->view->parametros->statusRequisicao) ) : ?>
                        <input id="dtPartida" class="campo" type="text" value="<?php echo isset($this->view->parametros->dtPartida) ? $this->view->parametros->dtPartida : '' ?>" name="dtPartida">
                <?php else : ?>
                        <input style="width: 90px !important;" readonly="readonly" class="campo" type="text" value="<?php echo isset($this->view->parametros->dtPartida) ? $this->view->parametros->dtPartida : '' ?>" name="dtPartida">
                <?php endif; ?>
                    </div>
                    <div id="divDataRetorno" class="final">
                        <label for="dtRetorno">Data Retorno *</label>
                <?php if($this->view->parametros->permissaoEdicao == 'Solicitante' && ( $this->view->parametros->statusRequisicao == 'P') || !isset($this->view->parametros->statusRequisicao) ) : ?>
                        <input id="dtRetorno" class="campo" type="text" value="<?php echo isset($this->view->parametros->dtRetorno) ? $this->view->parametros->dtRetorno : '' ?>" name="dtRetorno">
                <?php else : ?>
                        <input style="margin-left: 9px; width: 90px !important;" readonly="readonly" class="campo" type="text" value="<?php echo isset($this->view->parametros->dtRetorno) ? $this->view->parametros->dtRetorno : '' ?>" name="dtRetorno">
                <?php endif; ?>
                    </div>
                </div>
                <div class="clear"></div>

                <div class="campo menor">
                    <label for="placaVeiculo">Placa do Veículo *</label>
                <?php if($this->view->parametros->permissaoEdicao == 'Solicitante' && ( $this->view->parametros->statusRequisicao == 'P') || !isset($this->view->parametros->statusRequisicao) ) : ?>
                    <input id="placaVeiculo" maxlength="30" class="campo" type="text" value="<?php echo isset($this->view->parametros->placaVeiculo) ? $this->view->parametros->placaVeiculo : '' ?>" name="placaVeiculo">
                <?php else : ?>
                    <input readonly="readonly" id="placaVeiculo" maxlength="30" class="campo" type="text" value="<?php echo isset($this->view->parametros->placaVeiculo) ? $this->view->parametros->placaVeiculo : '' ?>" name="placaVeiculo">
                <?php endif; ?>
                </div>
                <div class="clear"></div>

                <div class="campo menor">
                    <label for="estadoOrigem">Estado Origem *</label>
                <?php if($this->view->parametros->permissaoEdicao == 'Solicitante' && ( $this->view->parametros->statusRequisicao == 'P') || !isset($this->view->parametros->statusRequisicao) ) : ?>
                    <select id="estadoOrigem" name="estadoOrigem">
                        <option value="">Escolha</option>
                        <?php foreach ($this->view->parametros->todosEstados as $estado) : ?>
                            <option value="<?php echo $estado->estoid ?>" <?php echo (isset($this->view->parametros->estadoOrigem) && $this->view->parametros->estadoOrigem == $estado->estoid) ? 'selected="selected"' : '' ?>>
                                <?php echo $estado->estnome ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else : ?>
                    <?php foreach ($this->view->parametros->todosEstados as $estado) : ?>
                        <?php if($estado->estoid == $this->view->parametros->estadoOrigem) : ?>
                                <input readonly="readonly" class="campo" type="text" value="<?php echo $estado->estnome ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <input id="estadoOrigem" type="hidden" value="<?php echo isset($this->view->parametros->estadoOrigem) ? $this->view->parametros->estadoOrigem : '' ?>" name="estadoOrigem">
                <?php endif; ?>
                </div>
                <div class="campo medio">
                    <label for="cidadeOrigem">Cidade Origem *</label>
                <?php if($this->view->parametros->permissaoEdicao == 'Solicitante' && ( $this->view->parametros->statusRequisicao == 'P') || !isset($this->view->parametros->statusRequisicao) ) : ?>
                    <select id="cidadeOrigem" name="cidadeOrigem">
                        <option value="">Escolha</option>
                        <!-- Opções deste campo serão preenchidos por ajax -->
                    </select>
                    <input type="hidden" value="<?php echo isset($this->view->parametros->cidadeOrigem) ? $this->view->parametros->cidadeOrigem : '' ?>" id="postCidadeOrigem">
                <?php else : ?>
                    <input readonly="readonly" class="campo" type="text" value="<?php echo isset($this->view->parametros->nomeCidadeOrigem) ? $this->view->parametros->nomeCidadeOrigem : '' ?>">
                    <input type="hidden" value="<?php echo isset($this->view->parametros->cidadeOrigem) ? $this->view->parametros->cidadeOrigem : '' ?>" name="cidadeOrigem">
                <?php endif; ?>
                </div>
                <div class="clear"></div>

                <div class="campo menor">
                    <label for="estadoDestino">Estado Destino *</label>
                <?php if($this->view->parametros->permissaoEdicao == 'Solicitante' && ( $this->view->parametros->statusRequisicao == 'P') || !isset($this->view->parametros->statusRequisicao) ) : ?>
                    <select id="estadoDestino" name="estadoDestino">
                        <option value="">Escolha</option>
                        <?php foreach ($this->view->parametros->todosEstados as $estado) : ?>
                            <option value="<?php echo $estado->estoid ?>" <?php echo (isset($this->view->parametros->estadoDestino) && $this->view->parametros->estadoDestino == $estado->estoid) ? 'selected="selected"' : '' ?>>
                                <?php echo $estado->estnome ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else : ?>
                    <?php foreach ($this->view->parametros->todosEstados as $estado) : ?>
                        <?php if($estado->estoid == $this->view->parametros->estadoDestino) : ?>
                                <input readonly="readonly" class="campo" type="text" value="<?php echo $estado->estnome ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <input id="estadoDestino" type="hidden" value="<?php echo isset($this->view->parametros->estadoDestino) ? $this->view->parametros->estadoDestino : '' ?>" name="estadoDestino">
                <?php endif; ?>
                </div>
                <div class="campo medio">
                    <label for="cidadeDestino">Cidade Destino *</label>
                <?php if($this->view->parametros->permissaoEdicao == 'Solicitante' && ( $this->view->parametros->statusRequisicao == 'P') || !isset($this->view->parametros->statusRequisicao) ) : ?>
                    <select id="cidadeDestino" name="cidadeDestino">
                        <option value="">Escolha</option>
                        <!-- Opções deste campo serão preenchidos por ajax -->
                    </select>
                    <input type="hidden" value="<?php echo isset($this->view->parametros->cidadeDestino) ? $this->view->parametros->cidadeDestino : '' ?>" id="postCidadeDestino">
                <?php else : ?>
                    <input readonly="readonly" class="campo" type="text" value="<?php echo isset($this->view->parametros->nomeCidadeDestino) ? $this->view->parametros->nomeCidadeDestino : '' ?>">
                    <input type="hidden" value="<?php echo isset($this->view->parametros->cidadeDestino) ? $this->view->parametros->cidadeDestino : '' ?>" name="cidadeDestino">
                <?php endif; ?>
                </div>
                <div class="clear"></div>

                <div class="campo menor">
                    <label for="distancia">Distância (em KM) *</label>
                <?php if($this->view->parametros->permissaoEdicao == 'Solicitante' && ( $this->view->parametros->statusRequisicao == 'P') || !isset($this->view->parametros->statusRequisicao) ) : ?>
                    <input id="distancia" maxlength="6" class="campo" type="text" value="<?php echo isset($this->view->parametros->distancia) ? $this->view->parametros->distancia : '' ?>" name="distancia">
                <?php else : ?>
                    <input readonly="readonly" id="distancia" maxlength="6" class="campo" type="text" value="<?php echo isset($this->view->parametros->distancia) ? $this->view->parametros->distancia : '' ?>" name="distancia">
                <?php endif; ?>
                </div>
                <div class="clear"></div>

                <div style="font-size: 11px;">
                    <p>Litros: <span id="litrosDistancia">100</span></p>
                    <p>Crédito: R$ <span id="creditoDistancia"> 100,00</span></p>
                </div>
                <div class="clear"></div>

            </div>

            <div id="blocoAdiantamento">
                
                <div class="campo menor">
                    <label for="valorAdiantamento">Valor *</label>
                <?php if($this->view->parametros->permissaoEdicao == 'Solicitante' && ( $this->view->parametros->statusRequisicao == 'P') || !isset($this->view->parametros->statusRequisicao) ) : ?>
                    <input style="text-align: right;" name="valorAdiantamento" id="valorAdiantamento" maxlength="7" class="campo moeda" type="text" value="<?php echo isset($this->view->parametros->valorAdiantamento) ? number_format(floatval($this->view->parametros->valorAdiantamento), 2, ',', '.') : '0,00' ?>">
                <?php else : ?>
                    <input readonly="readonly" style="text-align: right;" name="valorAdiantamento" maxlength="7" class="campo" type="text" value="<?php echo isset($this->view->parametros->valorAdiantamento) ? number_format(floatval($this->view->parametros->valorAdiantamento), 2, ',', '.') : '0,00' ?>">
                <?php endif; ?>
                </div>
                <div class="clear"></div>

            <?php if( ( ($this->view->parametros->permissaoEdicao == 'Solicitante' || $this->view->parametros->permissaoEdicao == 'Aprovador') && $this->view->parametros->statusRequisicao == 'P') || (!isset($this->view->parametros->statusRequisicao) ) ) : ?>
                <div class="campo data" style="white-space: nowrap;">
            <?php else : ?>
                <div class="campo" style="white-space: nowrap;">
            <?php endif; ?>
                    <label for="dataCredito">Solicitar Crédito Para *</label>
                <?php if( ( ($this->view->parametros->permissaoEdicao == 'Solicitante' || $this->view->parametros->permissaoEdicao == 'Aprovador') && $this->view->parametros->statusRequisicao == 'P') || (!isset($this->view->parametros->statusRequisicao) ) ) : ?>
                    <input id="dataCredito" class="campo" type="text" value="<?php echo isset($this->view->parametros->dataCredito) ? $this->view->parametros->dataCredito : '' ?>" name="dataCredito">
                <?php else : ?>
                    <input style="width: 90px !important;" readonly="readonly" class="campo" type="text" value="<?php echo isset($this->view->parametros->dataCredito) ? $this->view->parametros->dataCredito : '' ?>" name="dataCredito">
                <?php endif; ?>
                </div>
                <div class="clear"></div>
            </div>

            <div class="campo medio">
                <label for="aprovador">Solicitar Aprovação Para *</label>
            <?php if($this->view->parametros->permissaoEdicao == 'Solicitante' && ( $this->view->parametros->statusRequisicao == 'P') || !isset($this->view->parametros->statusRequisicao) ) : ?>
                <select id="aprovador" name="aprovador">
                    <option value="">Escolha</option>
                    <!-- Opções deste campo serão preenchidos por ajax -->
                </select>
                <input type="hidden" value="<?php echo isset($this->view->parametros->aprovador) ? $this->view->parametros->aprovador : '' ?>" id="postAprovador">
            <?php else : ?>
                <input readonly="readonly" class="campo" type="text" value="<?php echo isset($this->view->parametros->nomeAprovador) ? $this->view->parametros->nomeAprovador : '' ?>">
                <input type="hidden" value="<?php echo isset($this->view->parametros->aprovador) ? $this->view->parametros->aprovador : '' ?>" name="aprovador">
            <?php endif; ?>
            </div>
            <div class="clear"></div>

        </div>

    </div>
</div>

<div class="bloco_acoes">
<?php if( $this->view->parametros->statusRequisicao == 'P' || !isset($this->view->parametros->statusRequisicao) ) : ?>
    <button type="button" id="bt_confirmar" name="bt_confirmar">Confirmar</button>
    <?php if($this->view->parametros->permissaoEdicao == 'Solicitante') : ?>
        <button type="button" id="bt_limparCadastro">Limpar</button>
    <?php else : ?>
        <button readonly="readonly" type="button">Limpar</button>
    <?php endif; ?>
<?php else : ?>
    <button readonly="readonly" type="button">Confirmar</button>
    <button readonly="readonly" type="button">Limpar</button>
<?php endif; ?>
    <button type="button" id="bt_voltar">Voltar</button>
</div>