<?php require_once _MODULEDIR_ . "Principal/View/prn_agendamento_unitario/cabecalho.php"; ?>
<?php
    $retirada_reinstalacao = ($this->view->parametros->retirada_reinstalacao) ? '1' : '0';

    $bloqueio = false;

    if (in_array($parametros->etapa, array('agenda', 'salvar'))) {
        $bloqueio = true;
    }

    $exibirOsAdicional = isset($this->view->parametros->checkRetiradaReinstalacao['checked']) &&
                         $this->view->parametros->checkRetiradaReinstalacao['checked'];
	$exibirOsAdicional = ($exibirOsAdicional) ? $this->view->parametros->retirada_reinstalacao : true;
?>

    <!-- Mensagens-->
    <div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemErro; ?>
    </div>

    <div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemAlerta; ?>
    </div>

    <div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemSucesso; ?>
    </div>
    <input type="hidden" id="id_os_principal" name="id_os_principal" value="<?php echo $this->view->parametros->id; ?>" />

    <div class="mensagem info">(*) Campos de preenchimento obrigatório.</div>

    <?php foreach ($this->view->ordemServicos as $chave => $ordemServico): ?>
    <div style="<?php echo (!isset($ordemServico['principal']) && !$exibirOsAdicional) ? 'display: none;' : '';?>" id="<?php echo (!isset($ordemServico['principal'])) ? 'os-adicional' : '';?>">
        <div class="bloco_titulo">Dados da Ordem de Serviço</div>
        <div class="bloco_conteudo">
            <div class="formulario">
                <div class="campo menor">
                    <label>Ordem de Serviço</label>
                    <input id="cmp_ordem_servico_<?php echo $chave; ?>" class="campo" disabled="disabled" type="text" value="<?php echo $ordemServico['ordoid']; ?>" name="cmp_ordem_servico[]">
                </div>

                <div class="campo menor">
                    <label>Tipo</label>
                    <input id="cmp_tipo_<?php echo $chave; ?>" class="campo" readonly="readonly" type="text" value="<?php echo $ordemServico['ostdescricao']; ?>" name="cmp_tipo[]">
                </div>

                <div class="campo menor">
                    <label>Status</label>
                    <input id="cmp_status_<?php echo $chave; ?>" class="campo" readonly="readonly" type="text" value="<?php echo $ordemServico['ossdescricao']; ?>" name="cmp_status[]">
                </div>

                <div class="campo menor">
                    <label>Prioridade</label>
                    <input id="cmp_prioridade_<?php echo $chave; ?>" class="campo" readonly="readonly" type="text" value="<?php echo $ordemServico['ordurgente']; ?>" name="cmp_prioridade[]">
                </div>

                <div class="campo medio">
                    <label>Data de Cadastro</label>
                    <input id="cmp_data_cadastro_<?php echo $chave; ?>" class="campo" readonly="readonly" type="text" value="<?php echo $ordemServico['orddt_ordem']; ?>" name="cmp_data_cadastro[]">
                </div>

                <div class="clear"></div>

                <div class="campo menor">
                    <label>Contrato</label>
                    <input id="cmp_contrato_<?php echo $chave; ?>" class="campo" type="text" readonly="readonly" value="<?php echo $ordemServico['ordconnumero']; ?>" name="cmp_contrato[]">
                </div>

                <div class="campo maior">
                    <label>Cliente</label>
                    <input id="cmp_cliente_<?php echo $chave; ?>" class="campo" type="text" readonly="readonly" value="<?php echo $ordemServico['clinome']; ?>" name="cmp_cliente[]">
                </div>

                <div class="campo menor">
                    <label>UF</label>
                    <input id="cmp_uf_<?php echo $chave; ?>" class="campo" type="text" readonly="readonly" value="<?php echo $ordemServico['uf']; ?>" name="cmp_uf[]">
                </div>

                <div class="campo menor">
                    <label>Cidade</label>
                    <input id="cmp_cidade_<?php echo $chave; ?>" class="campo" type="text" readonly="readonly" value="<?php echo $ordemServico['cidade']; ?>" name="cmp_cidade[]">
                </div>

                <div class="clear"></div>

                <div class="campo menor">
                    <label>Marca</label>
                    <input id="cmp_marca_<?php echo $chave; ?>" class="campo" type="text" readonly="readonly" value="<?php echo $ordemServico['mcamarca']; ?>" name="cmp_marca[]">
                </div>

                <div class="campo medio">
                    <label>Modelo</label>
                    <input id="cmp_modelo_<?php echo $chave; ?>" class="campo" type="text" readonly="readonly" value="<?php echo $ordemServico['mlomodelo']; ?>" name="cmp_modelo[]">
                </div>

                <div class="campo menor">
                    <label>Ano</label>
                    <input id="cmp_ano_<?php echo $chave; ?>" class="campo" type="text" readonly="readonly" value="<?php echo $ordemServico['veino_ano']; ?>" name="cmp_ano[]">
                </div>

                <div class="campo menor">
                    <label>Cor</label>
                    <input id="cmp_cor_<?php echo $chave; ?>" class="campo" type="text" readonly="readonly" value="<?php echo $ordemServico['veicor']; ?>" name="cmp_cor[]">
                </div>

                <div class="campo menor">
                    <label>Placa</label>
                    <input id="cmp_placa_<?php echo $chave; ?>" class="campo" type="text" readonly="readonly" value="<?php echo $ordemServico['veiplaca']; ?>" name="cmp_placa[]">
                </div>

                <div class="clear"></div>

                <div class="campo medio">
                    <label>Classe do Equipamento</label>
                    <input id="cmp_classe_<?php echo $chave; ?>" class="campo" type="text" readonly="readonly" value="<?php echo $ordemServico['eqcdescricao']; ?>" name="cmp_classe[]">
                </div>

                <div class="campo medio">
                    <label>Chassi</label>
                    <input id="cmp_chassi_<?php echo $chave; ?>" class="campo" type="text" readonly="readonly" value="<?php echo $ordemServico['veichassi']; ?>" name="cmp_chassi[]">
                </div>

                <div class="clear"></div>

            </div>

            <div class="bloco_titulo">Serviços</div>

            <div class="bloco_conteudo">
                <div class="listagem">
                    <table>
                        <thead>
                            <tr>
                                <th class="menor">Item</th>
                                <th class="menor">Tipo</th>
                                <th class="maior">Motivo</th>
                                <th class="medio">Def. Alegado</th>
                                <th class="menor">Status</th>
                            </tr>
                        </thead>
                        <?php if (count($ordemServico['servicos'])): ?>
                        <tbody>
                            <?php foreach ($ordemServico['servicos'] as $chave => $valor): ?>
                            <tr class="<?php echo ($chave % 2) ? 'par' : 'inpar'; ?>">
                                <td class="esquerda"><?php echo $valor['item']; ?></td>
                                <td class="esquerda"><?php echo $valor['tipo']; ?></td>
                                <td class="esquerda"><?php echo $valor['motivo']; ?></td>
                                <td class="esquerda"><?php echo $valor['defeito_alegado']; ?></td>
                                <td class="esquerda"><?php echo $valor['status']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <?php else: ?>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="centro">Nenhum serviço cadastrado para a ordem de serviço foi encontrado.</td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>

                    </table>
                </div>
            </div>
            <div class="separador"></div>
        </div>
        <div class="separador"></div>
    </div>
    <?php endforeach; ?>

    <?php if (!empty($this->view->ordemServicos[$this->view->chaveOSPrincipal]['id_relacao_representante'])): ?>
        <?php if (!in_array($parametros->etapa, array('agenda', 'salvar'))): ?>
            <div class="direcionamento">
                <div class="bloco_titulo">Direcionamento</div>
                <div class="bloco_conteudo">
                    <div class="separador"></div>
                    <div class="bloco_conteudo">
                        <div class="listagem">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="medio">Prestador de Serviço</th>
                                        <th class="acao">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="impar">
                                        <td class="centro"><?php echo $ordemServico['representante']; ?></td>
                                        <td class="centro">
                                            <?php if ($_SESSION['funcao']['direcionar_ordem_servico'] == 1): ?>
                                            <span>
                                                <a href="javascript:void(0);">
                                                    <img class="icone excluir-redirecionamento" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/icon_error.png" title="Excluir Direcionamento">
                                                </a>
                                            </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="separador"></div>
                </div>
                <div class="separador"></div>
            </div>

            <div id="box-confirmacao" title="Confirmação" class="invisivel">
                <p class="confirmacao-mensagem">Deseja realmente cancelar o direcionamento de agendamento feito?</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <form action="<?php echo _PROTOCOLO_ . _SITEURL_; ?>prn_agendamento_unitario.php?acao=detalhe&operacao=<?php echo $this->view->parametros->operacao; ?>&id=<?php echo $this->view->parametros->id; ?>&etapa=agenda#visualizar" method="post" id="formEndereco">
        <div class="bloco_titulo">Endereço de Atendimento</div>
        <div class="bloco_conteudo">
            <div class="formulario">
                <?php if ($this->view->parametros->checkRetiradaReinstalacao['isRetiradaReinstalacao'] || $this->view->parametros->retirada_reinstalacao): ?>
                <fieldset class="maior">
                    <?php
                        $checked = $this->view->parametros->checkRetiradaReinstalacao['checked'] ? 'checked="checked"' : '';
                        $readonly = $this->view->parametros->checkRetiradaReinstalacao['readonly'] ? ' onclick="return false;" class="desabilitado"' : '';

                        if (empty($checked) && isset($this->view->parametros->retirada_reinstalacao) && $this->view->parametros->retirada_reinstalacao):
                            $checked = 'checked="checked"';
                        endif;
                    ?>
                    <input type="checkbox" value="1" name="retirada_reinstalacao" id="retirada_reinstalacao"
                        class="<?php echo ($bloqueio) ? ' desabilitado' : '' ?>"
                        <?php echo ($bloqueio) ? ' onclick="return false;"' : '' ;?>
                        <?php echo $checked . ' ' . $readonly?>>
                    <label for="retirada_reinstalacao">Retirada e reinstalação no mesmo dia</label>
                </fieldset>
                <?php endif; ?>

                <?php
                    $tipoOS = 0;
                    foreach ($this->view->ordemServicos as $key => $os) {
                        if($os['principal'] == 1) {
                            $tipoOS = $os['ostoid'];
                        }
                    }
                ?>
                <?php if ($_SESSION['funcao']['agendamento_emergencial'] && $tipoOS == 4): ?>
                <fieldset class="maior">
                    <input type="checkbox" value="1" name="atendimento_emergencial" id="atendimento_emergencial"
                        class="<?php echo ($bloqueio) ? ' desabilitado' : '' ?>"
                        <?php echo ($bloqueio) ? 'onclick="return false;"' : '' ;?>
                        <?php echo (isset($this->view->parametros->atendimento_emergencial)) ? 'checked="checked"' : ''?>>
                    <label for="atendimento_emergencial">Agendamento emergencial</label>
                </fieldset>
                <?php endif; ?>

                <div class="clear"></div>

                <?php
                    $comp_end = unserialize($_SESSION['agendamentos'][$_GET['id']]);

                    $campos = array();
                    if(is_a($comp_end, 'AgendamentoVO') && isset($_GET['etapa'])) {
                        $campos = array(
                            'comp_end_pais'            => isset($this->view->parametros->comp_end_pais) ? $this->view->parametros->comp_end_pais : 'BRASIL',
                            'comp_end_id_pais'         => isset($this->view->parametros->comp_end_id_pais) ? $this->view->parametros->comp_end_id_pais : '1',
                            'comp_end_cep'             => isset($this->view->parametros->comp_end_cep) ? $this->view->parametros->comp_end_cep : $comp_end->getCEP(),
                            'comp_end_estado'          => isset($this->view->parametros->comp_end_estado) ? $this->view->parametros->comp_end_estado : $comp_end->getCodigoEstado(),
                            'comp_end_id_estado'       => isset($this->view->parametros->comp_end_id_estado) ? $this->view->parametros->comp_end_id_estado : $comp_end->getIdEstado(),
                            'comp_end_cidade'          => isset($this->view->parametros->comp_end_cidade) ? $this->view->parametros->comp_end_cidade : $comp_end->getCidade(),
                            'comp_end_id_cidade'       => isset($this->view->parametros->comp_end_id_cidade) ? $this->view->parametros->comp_end_id_cidade : (int)$comp_end->getCodigoCidade(),
                            'comp_end_bairro'          => isset($this->view->parametros->comp_end_bairro) ? $this->view->parametros->comp_end_bairro : $comp_end->getBairro(),
                            'comp_end_id_bairro'       => isset($this->view->parametros->comp_end_id_bairro) ? $this->view->parametros->comp_end_id_bairro : $comp_end->getIdBairro(),
                            'comp_end_logradouro'      => isset($this->view->parametros->comp_end_logradouro) ? $this->view->parametros->comp_end_logradouro : $comp_end->getLogradouro(),
                            'comp_end_numero'          => isset($this->view->parametros->comp_end_numero) ? $this->view->parametros->comp_end_numero : $comp_end->getNumero(),
                            'comp_end_complemento'     => isset($this->view->parametros->comp_end_complemento) ? $this->view->parametros->comp_end_complemento : $comp_end->getComplemento()
                        );


                        $this->view->enderecosAction->isModoEdicao(true);

                        $this->view->enderecosAction->isModoBloqueio($bloqueio);

                    }
                    //renderiza o componente de CEP
                    $this->view->enderecosAction->plotarComponente($campos);
                ?>
                <div class="campo maior">
                    <label for="comp_end_referencia">Ponto de referência</label>
                    <input id="comp_end_referencia" name="comp_end_referencia"
                        class="campo validar <?php echo ($bloqueio) ? ' desabilitado' : '' ?>"
                        <?php echo ($bloqueio) ? 'disabled="true"' : '' ;?>
                        type="text" maxlength="100" value="<?=($this->view->parametros->comp_end_referencia) ? $this->view->parametros->comp_end_referencia : ((is_a($comp_end, 'AgendamentoVO') && isset($_GET['etapa'])) ? $comp_end->getReferencia() : '')?>">
                    <input type="hidden" name="cmp_disponibilidade" value="1">
                </div>

                <div class="clear"></div>
            </div>
        </div>
        <?php if ($parametros->etapa == 'info'): ?>
        <div class="bloco_acoes">
            <button type="submit" id="bt_pesquisar">Pesquisar Agenda</button>
            <button type="button" id="bt_limpar_endereco">Limpar</button>

            <?php if (isset($this->view->requisicaoBackoffice)): ?>
            <button type="button" id="bt_backoffice" onclick="window.location.href='<?php echo _PROTOCOLO_ . _SITEURL_; ?>prn_agendamento_unitario.php?acao=analiseBackoffice&id=<?php echo $this->view->parametros->id; ?>'">Solicitar Análise Backoffice</button>
            <?php endif; ?>

            <?php if (isset($this->view->notificarOS)): ?>
            <button type="button" id="bt_notificarOS" onclick="window.location.href='<?php echo _PROTOCOLO_ . _SITEURL_; ?>prn_agendamento_unitario.php?acao=notificarOS&id=<?php echo $this->view->parametros->id; ?>&marcar=<?php echo empty($checked) ? 0 : 1; ?>'">Notificar O.S</button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </form>

    <?php if (in_array($parametros->etapa, array('agenda', 'salvar'))): ?>
    <form id="formSalvarAgendamento" action="<?php echo _PROTOCOLO_ . _SITEURL_; ?>prn_agendamento_unitario.php?acao=detalhe&operacao=<?php echo $this->view->parametros->operacao; ?>&id=<?php echo $this->view->parametros->id; ?>&etapa=salvar&pagina=<?php echo $this->view->pagina; ?>#visualizar" method="post">
        <div class="separador"></div>
        <div class="bloco_titulo">Agenda<a name="visualizar"></a></div>
        <div class="bloco_conteudo">
            <div class="separador"></div>
            <div id="mensagem_erro_agenda" class="mensagem erro msg-agenda <?php if (empty($this->view->mensagemErroAgenda)): ?>invisivel<?php endif;?>">
                <?php echo $this->view->mensagemErroAgenda; ?>
            </div>
            <div id="mensagem_alerta_agenda" class="mensagem alerta <?php if (empty($this->view->mensagemAlertaAgenda)): ?>invisivel<?php endif;?>">
                <?php echo $this->view->mensagemAlertaAgenda; ?>
            </div>
            <div class="mensagem info msg-agenda">Duração do atendimento estimado em <?php echo $this->view->tempoAtividade; ?> hora(s).</div>

            <div class="conteudo">
                <fieldset>
                    <legend>Legenda</legend>
                    <ul>
                        <li><img alt="Exemplo 1" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/Agendamento/star.png" height="16" width="16"> Melhor dia</li>
                        <li><img alt="Exemplo 2" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/Agendamento/available.png" height="16" width="16"> Disponível</li>
                        <li><img alt="Exemplo 3" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/Agendamento/check.png" height="16" width="16"> Selecionado</li>
                        <li><img alt="Exemplo 4" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/Agendamento/unavailable.png" height="16" width="16"> Indisponível</li>
                    </ul>
                </fieldset>
            </div>

            <div class="conteudo">
                <div class="listagem">
                    <table id="listagem-datas">
                        <thead>
                            <tr>
                                <?php foreach ($this->view->diasSemana as $dia): ?>
                                <th class="medio"><?php echo $dia; ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <?php
                            $meses = array(
                                1 => 'JAN',
                                2 => 'FEV',
                                3 => 'MAR',
                                4 => 'ABR',
                                5 => 'MAI',
                                6 => 'JUN',
                                7 => 'JUL',
                                8 => 'AGO',
                                9 => 'SET',
                                10 => 'OUT',
                                11 => 'NOV',
                                12 => 'DEZ',
                            );
                        ?>
                        <tbody class="agenda-icones">
                            <?php foreach ($this->view->agenda as $semana): ?>
                            <tr class="impar">
                                <?php foreach ($semana as $dia => $dados): ?>
                                <td class="centro">
                                    <div>
                                        <?php if (isset($dados['melhor_data']) && $dados['melhor_data']): ?>
                                        <a href="javascript:void(0);" class="agenda-disponivel" data-dia="<?php echo $dados['data']->format('d-') . $meses[$dados['data']->format('n')] . $dados['data']->format('-Y'); ?>">
                                            <img src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/Agendamento/star.png" class="melhor-dia" title="Melhor dia">
                                        </a>
                                        <?php elseif (isset($dados['permite_agendamento']) && $dados['permite_agendamento']): ?>
                                        <a href="javascript:void(0);" class="agenda-disponivel" data-dia="<?php echo $dados['data']->format('d-') . $meses[$dados['data']->format('n')] . $dados['data']->format('-Y'); ?>">
                                            <img src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/Agendamento/available.png" title="Disponível">
                                        </a>
                                        <?php else: ?>
                                        <img src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/Agendamento/unavailable.png" title="Indisponível">
                                        <?php endif; ?>
                                    </div>
                                    <div><?php echo $dados['data']->format('d-') . $meses[$dados['data']->format('n')] . $dados['data']->format('-Y'); ?></div>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <?php if (!isset($this->view->parametros->atendimento_emergencial)): ?>
                                <td colspan="7">
                                    <?php if ($this->view->pagina > 1): ?>
                                        <a href="<?php echo _PROTOCOLO_ . _SITEURL_; ?>prn_agendamento_unitario.php?acao=detalhe&operacao=<?php echo $this->view->parametros->operacao; ?>&id=<?php echo $this->view->parametros->id; ?>&etapa=agenda&retirada_reinstalacao=<?php echo $retirada_reinstalacao ;?>&pagina=<?php echo ($this->view->pagina - 1); ?>#visualizar"><< ANTERIOR</a>
                                    <?php else: ?>
                                        << ANTERIOR
                                    <?php endif; ?> -
                                    <a href="<?php echo _PROTOCOLO_ . _SITEURL_; ?>prn_agendamento_unitario.php?acao=detalhe&operacao=<?php echo $this->view->parametros->operacao; ?>&id=<?php echo $this->view->parametros->id; ?>&etapa=agenda&retirada_reinstalacao=<?php echo $retirada_reinstalacao ;?>&pagina=<?php echo ($this->view->pagina + 1); ?>#visualizar">SEGUINTE >></a>
                                </td>
                                <?php else: ?>
                                <td colspan="7"></td>
                                <?php endif; ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div style="display: none;">
                    <img id="icone-selecionado" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/Agendamento/check.png" title="Selecionado">
                    <img id="icone-disponivel" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/Agendamento/available.png" title="Disponível">
                    <img id="icone-melhor-dia" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/Agendamento/star.png" title="Melhor Dia">
                </div>
            </div>

            <div class="separador"></div>

            <div id="alerta_time_slot" class="mensagem alerta invisivel">
                Nenhum horário selecionado.
            </div>

            <div class="conteudo">
                <div class="listagem">
                    <table style="width: 85%; display: none;" align="center" id="listagem-slots">
                        <thead>
                            <tr>
                                <th class="selecao">Selecionar</th>
                                <th class="menor">Tipo de PS</th>
                                <th class="menor">Horário</th>
                                <th class="maior">Detalhes</th>
                            </tr>
                        </thead>
                        <?php
                        foreach ($this->view->agenda as $semana):
                        foreach ($semana as $dia => $dados):

                        // Verifica se o dia tem slots disponíveis para agendamento
                        if (!isset($dados['permite_agendamento']) || !$dados['permite_agendamento']):
                            continue;
                        endif;
                        ?>
                        <tbody id="slots-<?php echo $dados['data']->format('d-') . $meses[$dados['data']->format('n')] . $dados['data']->format('-Y'); ?>" style="display: none;">
                            <?php
                            $contadorSlots = 0;
                            $prestadores = array();
                            foreach ($dados['slots'] as $chave => $slot):

                            // Verifica se o slot permite agendamento
                            if (!$slot['permite_agendamento']):
                                continue;
                            endif;

                            // Usado para limitar a exibição de apenas um horário por representante
                            if (isset($prestadores[$slot['representante']][$slot['ponto']][$slot['time_slot_agendamento']])) {
                                continue;
                            }
                            $prestadores[$slot['representante']][$slot['ponto']][$slot['time_slot_agendamento']] = true;

                            $contadorSlots++;
                            ?>
                            <tr class="<?php echo $contadorSlots % 2 ? 'inpar' : 'par'; ?>">
                                <td class="centro"><input type="radio" name="cmp_time_slot" value="<?php echo $slot['data_slot'] . '/' . $slot['time_slot_agendamento'] . '/' . $slot['representante'] . '/' . $slot['categoria'] . '/' . $slot['ponto']; ?>" /></td>
                                <td class="centro"><?php echo $slot['ponto']; ?></td>
                                <? $slot_agendamento_ofsc = explode('-',$slot['agendamento'][0]['slot_agendamento_ofsc']) ?>
                                <td class="centro"><?php echo $slot_agendamento_ofsc[0].':00 - '.$slot_agendamento_ofsc[1].':00'; ?></td>
                                <td class="centro">
                                    <?php

                                    if( isset($this->view->parametros->atendimento_emergencial) || ($slot['ponto'] == 'FIXO') ) {

                                        echo '<span class="nome">' . $slot['info_prestador']['repnome'] . '</span><br />';

                                        if(empty($slot['info_prestador']['endvfone'])) {
                                            echo '<span class="">Contato Representação:</span><br />';
                                        } else{
                                            echo '<span class="">Contato Representação: (' . $slot['info_prestador']['endvddd'] . ') ' .
                                                $slot['info_prestador']['endvfone'] . '</span><br />';
                                        }

                                        if(empty( $slot['info_prestador']['repcontato_fone'])) {

                                             echo '<span class="">Contato Emergencial: ' . $slot['info_prestador']['repcontato'] . '</span><br />';

                                        } else {

                                            echo '<span class="">Contato Emergencial: ' . $slot['info_prestador']['repcontato'] . ' (' .
                                                $slot['info_prestador']['repcontato_ddd'] . ') ' . $slot['info_prestador']['repcontato_fone'] . '</span><br />';

                                        }

                                        echo '<span class="endereco">' . $slot['info_prestador']['endvrua'] . ', ' .
                                            $slot['info_prestador']['endvnumero'] . ' - ' .
                                            $slot['info_prestador']['endvbairro'] . '<br />';
                                        echo $slot['info_prestador']['clcnome'] . ' - ' .
                                            $slot['info_prestador']['clcuf_sg'] . '</span><br />';
                                        echo '<span class="complemento">' . $slot['info_prestador']['endvponto_referencia'] . '</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <?php endforeach; ?>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <div class="separador"></div>
        </div>

        <div class="separador"></div>
        <div id="mensagem_alerta_contato" class="mensagem alerta <?php if (empty($this->view->mensagemAlertaContato)): ?>invisivel<?php endif;?>">
                <?php echo $this->view->mensagemAlertaContato; ?>
        </div>
        <div id="bloco_alerta_contato">
            <div id="alerta_contato" class="mensagem alerta invisivel">
                Existem campos obrigatórios não preenchidos.
            </div>
            <div id="alerta_contato_telefone" class="mensagem alerta invisivel">
                Telefone informado inválido.
            </div>
            <div id="alerta_contato_email" class="mensagem alerta invisivel">
                E-mail informado inválido.
            </div>
        </div>


        <div class="bloco_titulo">Contato</div>
        <div class="bloco_conteudo">
            <div class="formulario">

                <fieldset class="maior">
                    <legend>Solicitante</legend>
                     <div class="campo maior">
                        <label id="lbl_contato" for="cmp_contato">Nome da pessoa que está solicitando o agendamento</label>
                        <input id="cmp_contato" class="campo" type="text" value="" name="cmp_contato" maxlength="100">
                    </div>

                    <div class="campo medio">
                        <label id="lbl_contato_celular" for="cmp_contato_celular">Telefone celular da pessoa que está solicitando o agendamento</label>
                        <input id="cmp_contato_celular" class="campo" type="text" value="" name="cmp_contato_celular">
                    </div>

                    <div class="campo medio">
                        <label id="lbl_contato_email" for="cmp_contato_email">E-mail da pessoa que está solicitando o agendamento</label>
                        <input id="cmp_contato_email" class="campo" type="text" value="<?php echo $this->view->ordemServicos[0]['cliemail']; ?>" maxlength="100" name="cmp_contato_email">
                    </div>

                </fieldset>

                <fieldset class="maior">
                    <legend>Responsável Acompanhamento</legend>
                        <input type="hidden" name="retirada_reinstalacao" value="<?php echo (empty($checked)) ? '0' : '1' ?>" />
                        <div class="campo maior">
                            <label id="lbl_responsavel" for="cmp_responsavel">Nome do responsável que irá acompanhar o serviço</label>
                            <input id="cmp_responsavel" class="campo" type="text" value="" name="cmp_responsavel" maxlength="100">
                        </div>

                        <div class="campo medio">
                            <label id="lbl_responsavel_celular" for="cmp_responsavel_celular">Telefone celular do responsável que irá acompanhar o serviço</label>
                            <input id="cmp_responsavel_celular" class="campo" type="text" value="" name="cmp_responsavel_celular" maxlength="100">
                        </div>
                </fieldset>
                <div class="clear"></div>
            </div>
        </div>


        <div class="separador"></div>
        <div class="bloco_titulo">Resumo e Observações</div>
        <div class="bloco_conteudo">
            <div class="separador"></div>
            <div class="conteudo">
                <div>Data: <strong id="resumo-data"></strong></div>
                <div>Horário: <strong id="resumo-hora"></strong></div>
                <div>Tempo de Atendimento: <strong><?php echo $this->view->tempoAtividade; ?></strong></div>
                <div>Tipo de PS: <strong id="resumo-tipo"></strong></div>
                <div>Local: <strong id="resumo-local"></strong></div>
                <div>Endereço: <strong id="resumo-endereco"></strong></div>
            </div>
            <div class="formulario">
                <div class="campo maior">
                    <label id="lbl_observacoes" for="cmp_observacoes">Observações</label>
                    <textarea rows="5" id="cmp_observacoes" name="cmp_observacoes"></textarea>
                </div>

                <div class="clear"></div>
            </div>
        </div>

        <div class="bloco_acoes">
            <button type="submit" id="bt_agendar">Agendar</button>
            <button type="button" id="bt_limpar">Limpar</button>
        </div>
    </form>
    <?php endif; ?>

<?php require_once _MODULEDIR_ . "Principal/View/prn_agendamento_unitario/rodape.php"; ?>