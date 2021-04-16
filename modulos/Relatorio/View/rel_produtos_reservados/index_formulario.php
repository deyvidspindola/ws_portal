<form id="form_pesquisa" method="post">
    <input id="acao" type="hidden" name="acao" value="" />
    <div class="bloco_titulo">Dados para Pesquisa</div>
    <div class="bloco_conteudo">
        <div class="formulario">
            <fieldset class="medio opcoes-inline">
                <legend>Pesquisar Por *</legend>
                <input id="reldt_tipo_agenda" type="radio" name="reldt_tipo" value="agenda"
                    <?php if (empty($this->param->reldt_tipo) || $this->param->reldt_tipo == 'agenda') : ?>
                        checked="checked"
                    <?php endif; ?>
                />
                <label id="lbl_reldt_tipo_agenda" for="reldt_tipo_agenda">Data Agenda</label>
                <input id="reldt_tipo_reserva" type="radio" name="reldt_tipo" value="reserva"
                    <?php if (isset($this->param->reldt_tipo) && $this->param->reldt_tipo == 'reserva') : ?>
                        checked="checked"
                    <?php endif; ?>
                />
                <label id="lbl_reldt_tipo_reserva" for="reldt_tipo_reserva">Data Reserva</label>
            </fieldset>
            <div class="clear"></div>
            <div class="campo data periodo">
                <div class="inicial">
                    <label id="lbl_reldt_inicial" for="reldt_inicial">Período *</label>
                    <input id="reldt_inicial" type="text" name="reldt_inicial" maxlength="10"
                        value="<?php echo isset($this->param->reldt_inicial) ? $this->param->reldt_inicial : '' ?>"
                    class="campo" />
                </div>
                <div class="campo label-periodo">a</div>
                <div class="final">
                    <label id="lbl_reldt_final" for="reldt_final">&nbsp;</label>
                    <input id="reldt_final" type="text" name="reldt_final" maxlength="10"
                        value="<?php echo isset($this->param->reldt_final) ? $this->param->reldt_final : '' ?>"
                    class="campo" />
                </div>
            </div>
            <div class="clear"></div>

            <div class="campo maior">
                <label id="lbl_repoid" for="repoid">Representante</label>
                <select id="repoid" name="repoid" <?php echo ($this->isDeptoTecnico) ? 'disabled="true"' : '';?> >
                    <option value="">Escolha</option>
                    <?php if (isset($this->view->dados->representante) && is_array($this->view->dados->representante)) : ?>
                        <?php foreach ($this->view->dados->representante as $registro) : ?>
                            <option value="<?php echo $registro->oid; ?>"
                                <?php if (isset($this->param->repoid) && $registro->oid == $this->param->repoid) : ?>
                                    selected="selected"
                                <?php endif; ?>
                            ><?php echo $registro->descricao; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="campo maior">
                <label id="lbl_itloid" for="itloid">Instalador</label>
                <select id="itloid" name="itloid">
                    <option value="">Escolha</option>
                </select>
                <input id="itloid_select" name="itloid_select" value="<?php echo $this->param->itloid;?>" type="hidden">
            </div>
            <div class="clear"></div>

            <div class="campo menor">
                <label id="lbl_ufuf" for="ufuf">UF</label>
                <select id="ufuf" name="ufuf">
                    <option value="">Escolha</option>
                    <?php if (isset($this->view->dados->estado) && is_array($this->view->dados->estado)) : ?>
                        <?php foreach ($this->view->dados->estado as $registro) : ?>
                            <option value="<?php echo $registro->descricao; ?>"
                                <?php if (isset($this->param->ufuf) && $registro->descricao == $this->param->ufuf) : ?>
                                    selected="selected"
                                <?php endif; ?>
                            ><?php echo $registro->descricao; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="campo medio">
                <label id="lbl_ciddescricao" for="ciddescricao">Cidade</label>
                <select id="ciddescricao" name="ciddescricao">
                    <option value="">Escolha</option>
                    <?php if (isset($this->view->dados->cidade) && is_array($this->view->dados->cidade)) : ?>
                        <?php foreach ($this->view->dados->cidade as $registro) : ?>
                            <option value="<?php echo $registro->descricao; ?>"
                                <?php if (isset($this->param->ciddescricao) && $registro->descricao == $this->param->ciddescricao) : ?>
                                    selected="selected"
                                <?php endif; ?>
                            ><?php echo $registro->descricao; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="clear"></div>

            <div class="campo maior">
                <label id="lbl_eqcoid" for="eqcoid">Classe do Contrato</label>
                <select id="eqcoid" name="eqcoid">
                    <option value="">Escolha</option>
                    <?php if (isset($this->view->dados->classe) && is_array($this->view->dados->classe)) : ?>
                        <?php foreach ($this->view->dados->classe as $registro) : ?>
                            <option value="<?php echo $registro->oid; ?>"
                                <?php if (isset($this->param->eqcoid) && $registro->oid == $this->param->eqcoid) : ?>
                                    selected="selected"
                                <?php endif; ?>
                            ><?php echo $registro->descricao; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="campo medio">
                <label id="lbl_ostoid" for="ostoid">Tipo O.S.</label>
                <select id="ostoid" name="ostoid">
                    <option value="">Escolha</option>
                    <?php if (isset($this->view->dados->tipo) && is_array($this->view->dados->tipo)) : ?>
                        <?php foreach ($this->view->dados->tipo as $registro) : ?>
                            <option value="<?php echo $registro->oid; ?>"
                                <?php if (isset($this->param->ostoid) && $registro->oid == $this->param->ostoid) : ?>
                                    selected="selected"
                                <?php endif; ?>
                            ><?php echo $registro->descricao; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="campo menor">
                <label id="lbl_ordoid" for="ordoid">O.S.</label>
                <input id="ordoid" name="ordoid" type="text" maxlength="9" class="numerico campo" value="<?php echo $this->param->ordoid; ?>">
            </div>
            <div class="clear"></div>

            <div class="campo maior">
                <label id="lbl_rasoid" for="rasoid">Status</label>
                <select id="rasoid" name="rasoid">
                    <option value="">Escolha</option>
                    <?php if (isset($this->view->dados->status) && is_array($this->view->dados->status)) : ?>
                        <?php foreach ($this->view->dados->status as $registro) : ?>
                            <option value="<?php echo $registro->oid; ?>"
                                <?php if (isset($this->param->rasoid) && $registro->oid == $this->param->rasoid) : ?>
                                    selected="selected"
                                <?php endif; ?>
                            ><?php echo $registro->descricao; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="campo maior">
                <label id="lbl_prdoid" for="prdoid">Produto</label>
                <select id="prdoid" name="prdoid">
                    <option value="">Escolha</option>
                    <?php if (isset($this->view->dados->status) && is_array($this->view->dados->status)) : ?>
                        <?php foreach ($this->view->dados->produtos as $registro) : ?>
                            <option value="<?php echo $registro->prdoid; ?>"
                                <?php if (isset($this->param->prdoid) && $registro->prdoid == $this->param->prdoid) : ?>
                                    selected="selected"
                                <?php endif; ?>
                            ><?php echo $registro->prdproduto; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="clear"></div>

            <fieldset class="maior opcoes-inline">
                <legend>Incluir</legend>
                <input id="reserva_remessa" type="checkbox" name="reserva_remessa" value="1"
                    <?php if (isset($this->param->reserva_remessa) && $this->param->reserva_remessa == '1') : ?>
                        checked="checked"
                    <?php endif; ?>
                />
                <label id="lbl_reserva_remessa" for="reserva_remessa">Produtos Reservados em Remessas</label>
            </fieldset>
            <div class="clear"></div>
        </div>
    </div>
    <div class="bloco_acoes">
        <button id="bt_pesquisar" type="button">Pesquisar</button>
        <button id="bt_exportar" type="button">Exportar XLS</button>
    </div>
</form>