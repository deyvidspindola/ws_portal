<form>
                <div class="bloco_conteudo">
                    <div class="conteudo" style="margin:0 !important; padding:0 !important">
                        <table width="100%">
                            <tbody>
                                <tr>
                                    <td id="td_nome" class="label">Representante</td>
                                    <td colspan="3"><?php echo utf8_encode($representante->nome); ?></td>
                                    <td id="td_documento" class="label menor">CNPJ</td>
                                    <td><?php echo $representante->cnpj; ?></td>
                                </tr>
                                <tr>
                                    <td id="td_nome" class="label" nowrap="nowrap">Nome Fantasia</td>
                                    <td colspan="3"><?php echo utf8_encode($representante->nomefantasia); ?></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td id="td_endereco" class="label"><?php echo utf8_encode('Endereço'); ?></td>
                                    <td colspan="5">
                                        <?php echo utf8_encode($representante->endereco); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td id="td_cidade" class="label">Cidade</td>
                                    <td><?php echo utf8_encode($representante->cidade); ?></td>
                                    <td id="td_estado" class="label menor">Estado</td>
                                    <td class="menor"><?php echo utf8_encode($representante->estado); ?></td>
                                    <td id="td_telefone_cliente" class="label menor">Telefone</td>
                                    <td class="medio"><?php echo $representante->telefone; ?></td>
                                </tr>

                                <tr>
                                    <td id="td_contato" class="label">Contato</td>
                                    <td><?php echo utf8_encode($representante->contato); ?></td>
                                    <td id="td_telefone_contato" class="label">Telefone</td>
                                    <td><?php echo $representante->contatotelefone; ?></td>
                                    <td id="td_email" class="label">E-mail</td>
                                    <td><?php echo utf8_encode($representante->email); ?></td>
                                </tr>
                                <tr>
                                    <td id="td_funcoes" class="label">Tipo O.S.</td>
                                    <td colspan="2"><?php echo utf8_encode($this->param->tipoOs); ?></td>
                                    <td class="label"></td>
                                    <td id="td_email" class="label">Classe do Cliente</td>
                                    <td><?php echo utf8_encode($this->param->classe);?></td>
                                </tr>
                                <tr>
                                    <td id="td_funcoes" class="label"><?php echo utf8_encode('Nº O.S.'); ?></td>
                                    <td colspan="2"><?php echo $this->param->num_os; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <?
                    /**
                     * STI-86783 Verificar se a solicitação foi gerada pelo agendamento unitário, caso sim,
                     * trazer todos checkbox marcados e inabilitados para edição e ocultar o botão <atender>.
                     */
                    $checked = "";
                    $blocked = "";
                    $blocked_sup = "marcardesmarcar();";

                    if ($solicitacao->flag_agendamento == "t"){
                        $checked = "checked";
                        $blocked = "this.checked='checked'";
                        $blocked_sup = $blocked;
                    }
                    ?>

                    <div class="listagem">
                        <table>
                            <thead>
                                <tr>
                                    <th id="thtodos">
                                        <?php foreach($itens as $item) :?>
                                            <?php if($item->status == 'Pendente') : ?>
                                                <input type="checkbox" id="todos" name="todos" onclick="<?=$blocked_sup?>" <?=$checked?>>
                                                <?php break; ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </th>
                                    <th style="width: 20px !important">Status</th>
                                    <th>Motivo</th>
                                    <th>Produto</th>
                                    <th class="medio">Permite Enviar Produto Similar</th>
                                    <th class="menor">Qtd.</th>
                                </tr>
                            </thead>

                            <tbody id="tabela_solicitacao_produto">
                                <?php  $itensPendentes = 0; ?>
                                <?php foreach($itens as $indice => $item) :?>
                                    <tr class="<?php echo ($indice + 1) % 2 == 0 ? 'par mouse-pointer' : 'impar mouse-pointer'; ?>">
                                        <?php if($item->status == 'Pendente'):?>
                                            <td id="checkbox_<?php echo $item->saioid; ?>"class="centro">
                                                <input class="checkbox" type="checkbox" id="c_1" name="produto[]" value="<?php echo $item->saioid; ?>" onclick="<?=$blocked?>" <?=$checked?>>
                                            </td>
                                        <?php else: ?>
                                            <td class="centro"></td>
                                        <?php endif; ?>
                                        <?php
                                            switch ($item->status) {
                                                case 'Pendente':
                                                    $itensPendentes++;
                                                    echo '<td id="status_'.$item->saioid.'" class="centro laranja">'.utf8_encode($item->status).'</td>';
                                                    break;
                                                case 'Atendido':
                                                    echo '<td id="status_'.$item->saioid.'" class="centro verde">'.utf8_encode($item->status).'</td>';
                                                    break;
                                                case 'Recusado':
                                                    echo '<td id="status_'.$item->saioid.'" class="centro vermelho">'.utf8_encode($item->status).'</td>';
                                                    break;
                                                default:
                                                    echo '<td class="centro">'.$item->status.'</td>';
                                            }
                                        ?>
                                        <td><?php echo utf8_encode($item->recusa); ?></td>
                                        <td><?php echo utf8_encode($item->produto); ?></td>
                                        <td class="centro">
                                            <?php if($item->permite_similar == 'NÃO') : ?>
                                                <img class="img" src="./images/Agendamento/unavailable.png" height="15">
                                            <?php else: ?>
                                                <img class="img" src="./images/Agendamento/check.png" height="15">
                                            <?php endif; ?>
                                        </td>
                                        <td class="direita"><?php echo $item->qtd; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>

                        </table>
                    </div>
                    <div style="padding:5px">
                        <b><?php echo utf8_encode('Observação:'); ?></b>
                        &nbsp;
                        <?php echo utf8_encode($solicitacao->sagobservacao); ?>
                    </div>
                </div>
                <input type='hidden' id="qtd_itens_pendentes" name="qtd_itens_pendentes" value="<?php echo $itensPendentes; ?>">
            </form>
        </div>

        <div id="recusar-produtos-form" style="display:none" title="Recusar Produto">
            <form>
                <div class="campo maior">
                    <label>Justificativa *</label>
                    <textarea id="justificativa" maxlength="300" class="maior" rows="5" cols="10"></textarea>
                </div>
            </form>
        </div>

        <div class="conteudo">
            <fieldset>
                <legend style="cursor: default;">Legenda</legend>
                <ul class="legenda">
                    <li><img class="img" src="./images/Agendamento/check.png" height="15"> <?php echo utf8_encode('- Sim'); ?></li>
                    <li><img class="img" src="./images/Agendamento/unavailable.png" height="15"> <?php echo utf8_encode('- Não'); ?></li>
                </ul>
            </fieldset>
        </div>
