<form id="form_disponivel" method="post" action="prn_cockpit_agendamento_os.php?ordoid=<?php echo $this->param->ordoid ?>&repoid=<?php echo $this->param->repoid ?>">
    <input type="hidden" id="acao" name="acao" value=""/>
    <input type="hidden" name="id_agendamento" value="<?php echo $this->view->id_reserva_agendamento ?>">
    <input type="hidden" id="repoid" name="repoid" value="<?php echo $this->param->repoid ?>"/>
    <input type="hidden" id="ordoid" name="ordoid" value="<?php echo $this->param->ordoid ?>"/>
    <input type="hidden" id="connumero" name="connumero" value="<?php echo $this->param->connumero ?>"/>

    <?php
    $cor = 'impar';
    $tipo = '';

    if (isset($this->view->parametros->disponivel)) {
        $disponivel = $this->view->parametros->disponivel;
    } else {
        $disponivel = new stdClass();
    }
    ?>

    <div class="separador"></div>
    <div class="bloco_titulo">Produtos a Reservar</div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table id="lista_reservar">
                <thead>
                    <th class="esquerda">Produto</th>
                    <th class="menor">Em Estoque</th>
                    <th class="menor">Em Trânsito</th>
                    <th class="menor">Ação</th>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
    <div class="bloco_acoes">
        <button id="btn_salvar_reserva" disabled="disabled" type="button">Salvar Reserva</button>
    </div>

    <?php if (count($disponivel->equipamento) || count($disponivel->outro)) : ?>
        <div class="separador"></div>
        <div class="bloco_titulo">Produtos Disponíveis</div>
        <div class="bloco_acoes">
            <button class="bt_reservar_produtos" type="button">Reservar Produtos</button>
        </div>
        <div class="bloco_conteudo">
            <div class="listagem">
                <table>
                    <thead>
                        <tr>
                            <th class="medio"></th>
                            <th>Produto</th>
                            <th class="medio" colspan="2">Disponível</th>
                            <th class="medio" colspan="2">Em Trânsito</th>
                            <th class="menor">Retirada</th>
                            <th class="menor">Reservado Estoque</th>
                            <th class="menor">Reservado Trânsito</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>
                                Qtde.
                                <img src="images/icon_info.png" title="Quantidade disponível em estoque." class="icone meio" />
                            </th>
                            <th class="checkbox" >
                                <input type="checkbox" class="chb_disponivel_estoque_todos" />
                            </th>
                            <!-- th>
                                Qtde.
                                <img src="images/icon_info.png" title="Quantidade a ser solicitada ou reservada em estoque." class="icone meio" />
                            </th -->
                            <th>
                                Qtde.
                                <img src="images/icon_info.png" title="Quantidade disponível em trânsito (Em remessas)." class="icone meio" />
                            </th>
                            <th class="checkbox" >
                                <input type="checkbox" class="chb_disponivel_transito_todos"/>
                            </th>
                            <!-- th>
                                Qtde.
                                <img src="images/icon_info.png" title="Quantidade a ser solicitada ou reservada em trânsito." class="icone meio" />
                            </th -->
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($disponivel->equipamento)) : ?>
                            <?php foreach ($disponivel->equipamento as $indice => $equipamento) : ?>
                                <tr class="<?php echo $cor; ?>" id="<?php echo $equipamento->oid ?>">
                                    <?php //$reservadoEstoque = $this->view->produtos->sessao[$equipamento->oid][quantidade_disponivel] > 0 ? $this->view->produtos->sessao[$equipamento->oid][quantidade_disponivel] : 0; ?>
                                    <?php //$reservadoTransito = $this->view->produtos->sessao[$equipamento->oid][quantidade_transito] > 0 ? $this->view->produtos->sessao[$equipamento->oid][quantidade_transito] : 0; ?>
                                    <?php 
                                        $reservadosRepresentante = $this->dao->buscaQuantidadeReservada($this->param->repoid, $equipamento->oid); 
                                        $reservadoEstoque = 0;
                                        $reservadoTransito = 0;
                                        if ($reservadosRepresentante != null) {
                                            $reservadoEstoque = $reservadosRepresentante->disponivel;
                                            $reservadoTransito = $reservadosRepresentante->transito;
                                        }
                                    ?>
                                    <?php if ($indice == 0) : ?>
                                        <td rowspan="<?php echo count($disponivel->equipamento); ?>" class="agrupamento">Equipamento</td>
                                    <?php endif; ?>
                                    <td><?php echo $equipamento->nome; ?></td>
                                    <td class="centro" id="max_estoque_<?php echo $equipamento->oid ?>">
                                        <span id="qtde_<?php echo $equipamento->oid ?>"><?php echo $equipamento->disponivel; ?></span>
                                        <input type="hidden"
                                               id="produto[<?php echo $equipamento->oid ?>][qtdeInicial]"
                                               name="produto[<?php echo $equipamento->oid ?>][qtdeInicial]"
                                               value="<?php echo $equipamento->qtdeInicial; ?>"
                                               class ="qtdeInicial_<?php echo $equipamento->oid; ?>"/>
                                    </td>
                                    <td class="centro">
                                    	<input type="checkbox" name="checkbox[<?php echo $equipamento->oid ?>][checkedDisponivel]" data-produtoId="<?php echo $equipamento->oid ?>" data-nome="<?php echo $equipamento->nome ?>" id="equipamento_disponivel_<?php echo $equipamento->oid ?>" class="chb_disponivel_estoque produtos somente_disponivel" data-tipo="estoque"/>
                                    	<input type="hidden"   value="0" name="produto[<?php echo $equipamento->oid ?>][disponivel]" class="campo direita mini desabilitado somenteNumero equipamento_disponivel_<?php echo $equipamento->oid ?> disponivel_estoque_<?php echo $equipamento->oid ?>" />
                                    </td>
                                    <!--<td class="centro"><a href="#" id="max_transito_<?php echo $equipamento->oid ?>"><?php echo $equipamento->transito; ?></a></td>-->
                                    <td class="centro" id="max_estoque_transito_<?php echo $equipamento->oid ?>">
                                            <span id="qtde_transito_<?php echo $equipamento->oid ?>"><?php echo $equipamento->transito; ?></span>
                                            <input type="hidden"
                                               id="produto_transito[<?php echo $equipamento->oid ?>][qtdeInicial]"
                                               value="<?php echo $equipamento->transito - $reservadoTransito; ?>"
                                               class ="qtdeInicial_transito_<?php echo $equipamento->oid; ?>"/>
                                    </td>
                                    <td class="centro">
                                    	<input type="checkbox" name="checkbox[<?php echo $equipamento->oid ?>][checkedTransito]"
                                               data-produtoId="<?php echo $equipamento->oid ?>"
                                               data-nome="<?php echo $equipamento->nome ?>"
                                               id="equipamento_transito_<?php echo $equipamento->oid ?>"
                                               class="chb_disponivel_transito produtos"
                                               data-tipo="transito"
                                               />
                                    	<input type="hidden" value="0" name="produto[<?php echo $equipamento->oid ?>][transito]" class="campo direita mini desabilitado somenteNumero equipamento_transito_<?php echo $equipamento->oid ?> disponivel_transito_<?php echo $equipamento->oid ?>" />
                                    </td>
                                    <td class="centro"><?php echo $equipamento->retirada; ?></td>
                                    <td class="centro"><?php echo $reservadoEstoque; ?></td>
                                    <td class="centro"><?php echo $reservadoTransito; ?></td>
                                </tr>
                                <?php $cor = $cor == 'impar' ? 'par' : 'impar'; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if (isset($disponivel->outro)) : ?>
                            <?php $i = 0; ?>
                            <?php foreach ($disponivel->outro as $indice => $outro) : ?>
                                <?php //$reservadoEstoque = $this->view->produtos->sessao[$outro->oid][quantidade_disponivel] > 0 ? $this->view->produtos->sessao[$outro->oid][quantidade_disponivel] : 0; ?>
                                <?php //$reservadoTransito = $this->view->produtos->sessao[$outro->oid][quantidade_transito] > 0 ? $this->view->produtos->sessao[$outro->oid][quantidade_transito] : 0; ?>
                                <?php 
                                        $reservadosRepresentante = $this->dao->buscaQuantidadeReservada($this->param->repoid, $outro->oid); 
                                        $reservadoEstoque = 0;
                                        $reservadoTransito = 0;
                                        if ($reservadosRepresentante != null) {
                                            $reservadoEstoque = $reservadosRepresentante->disponivel;
                                            $reservadoTransito = $reservadosRepresentante->transito;
                                        }
                                    ?>
                                <tr class="<?php echo $cor; ?>" id="<?php echo $outro->oid ?>">
                                    <?php if ($tipo != $outro->tipo) : ?>
                                        <?php $tipo = $outro->tipo; ?>
                                        <td rowspan="<?php echo $outro->quantidade; ?>" class="agrupamento"><?php echo $outro->tipo; ?></td>
                                    <?php endif; ?>
                                    <td><?php echo $outro->nome; ?></td>
                                    <td class="centro" id="max_estoque_<?php echo $outro->oid ?>" >
                                        <span id="qtde_<?php echo $outro->oid ?>"><?php echo $outro->disponivel; ?></span>
                                        <input type="hidden"
                                               id="produto[<?php echo $outro->oid ?>][qtdeInicial]"
                                               name="produto[<?php echo $outro->oid ?>][qtdeInicial]"
                                               value="<?php echo $outro->qtdeInicial; ?>"
                                                class ="qtdeInicial_<?php echo $outro->oid ?>"/>
                                    </td>
                                    <td class="centro">
                                    	<input type="checkbox" name="checkbox[<?php echo $outro->oid ?>][checkedDisponivel]" data-produtoId="<?php echo $outro->oid ?>" data-nome="<?php echo $outro->nome ?>" id="outro_<?php echo $i ?>_disponivel_<?php echo $outro->oid ?>" class="chb_disponivel_estoque produtos somente_disponivel"/>
                                    	<input type="hidden" value="0" name="produto[<?php echo $outro->oid ?>][disponivel]" class="campo direita desabilitado somenteNumero mini outro_<?php echo $i ?>_disponivel_<?php echo $outro->oid ?> disponivel_estoque_<?php echo $outro->oid ?>" />
                                    </td>
                                    <!--<td class="centro"><a href="#" id="max_transito_<?php echo $outro->oid ?>"><?php echo $outro->transito; ?></a></td>-->
                                    <td class="centro" id="max_estoque_transito_<?php echo $outro->oid ?>">
                                        <span id="qtde_transito_<?php echo $outro->oid ?>"><?php echo $outro->transito; ?></span>
                                    </td>
                                    <td class="centro">
                                    	<input type="checkbox" name="checkbox[<?php echo $outro->oid ?>][checkedTransito]"
                                               data-produtoId="<?php echo $outro->oid ?>"
                                               data-nome="<?php echo $outro->nome ?>"
                                               id="outro_<?php echo $i ?>_transito_<?php echo $outro->oid ?>"
                                               class="chb_disponivel_transito produtos"
                                               />
                                    	<input type="hidden" value="0" name="produto[<?php echo $outro->oid ?>][transito]" class="campo direita desabilitado somenteNumero mini outro_<?php echo $i ?>_transito_<?php echo $outro->oid ?> disponivel_transito_<?php echo $outro->oid ?>" />
                                    </td>
                                    <td class="centro"><?php echo $outro->retirada; ?></td>
                                    <td class="centro"><?php echo $reservadoEstoque; ?></td>
                                    <td class="centro"><?php echo $reservadoTransito; ?></td>
                                </tr>
                                <?php $cor = $cor == 'impar' ? 'par' : 'impar'; ?>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bloco_acoes">
            <button id="btn_solicitar" disabled="disabled" type="button">Solicitar Produtos</button>
            <!--<button id="btn_reservar" disabled="disabled" type="button">Reservar Produtos</button>-->
            <button class="bt_reservar_produtos" type="button">Reservar Produtos</button>
            <button type="button" class="invisivel">Reservar Produtos da Classe</button>
        </div>
    <?php endif; ?>

</form>

<?php
unset($disponivel);
?>

<script>
<?php if (isset($_SESSION['funcao']['solicita_unidades_estoque']) && $_SESSION['funcao']['solicita_unidades_estoque'] == '1') : ?>
        var solicita_unidades_estoque = true;
<?php else: ?>
        var solicita_unidades_estoque = false;
<?php endif; ?>
</script>

<div id="solicitar-produtos-form" style="display:none" title="SOLICITAR PRODUTOS">
    <form >
        <input type="hidden" name="ordoid" value="<?php echo $this->param->ordoid; ?>" id="solicitar_produtos_ordoid">
        <input type="hidden" name="repoid" value="<?php echo $this->param->repoid; ?>" id="solicitar_produtos_repoid">

        <div class="separador"></div>

        <div class="bloco_conteudo">
            <div class="listagem">
                <table>
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Qtd.</th>
                        </tr>
                    </thead>
                    <tbody id="tabela_solicitacao_produto"></tbody>
                </table>
            </div>
        </div>

        <div class="separador"></div>

        <div class="campo medio" style="margin-left: 23px;">
            <label id="lbl_observacao" for="lbl_observacao" style="cursor: default;">Observação *</label>
            <textarea style="width:284px" id="solicitar-observacao" name="observacao"></textarea>
        </div>

        <div class="separador"></div>
    </form>
</div>
