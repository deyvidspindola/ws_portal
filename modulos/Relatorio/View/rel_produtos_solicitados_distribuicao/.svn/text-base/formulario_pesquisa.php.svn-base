<form id="form_pesquisa" method="post">
    <input id="acao" type="hidden" name="acao" value="" />
    <div class="bloco_titulo">Dados para Pesquisa</div>
    <div class="bloco_conteudo">
        <div class="formulario">
            <div class="campo data periodo">
                <div class="inicial">
                    <label for="data_1" style="cursor: default; ">Período inclusão *</label>
                    <input id="sagdt_cadastro_inicial" type="text" name="sagdt_cadastro_inicial" maxlength="10" value="<?php echo (isset($this->param->sagdt_cadastro_inicial)) ? $this->param->sagdt_cadastro_inicial : ''; ?>" class="campo">
                </div>
                <div class="campo label-periodo">a</div>
                <div class="final">
                    <label for="data_2" style="cursor: default; ">&nbsp;</label>
                    <input id="sagdt_cadastro_final" type="text" name="sagdt_cadastro_final" maxlength="10" value="<?php echo isset($this->param->sagdt_cadastro_final) ? $this->param->sagdt_cadastro_final : ''; ?>" class="campo">
                </div>
            </div>
            <div class="campo menor">&nbsp;</div>
            <div class="campo medio">
                <label id="lbl_tprel" for="tprel">Tipo de Relatório *</label>
                <select id="tprel" name="tprel">
                    <option value="">Escolha</option>
                    <option value="A"<?php if (isset($this->param->tprel) and $this->param->tprel == 'A') : ?> selected="selected"<?php endif; ?>>Analítico</option>
                    <option value="S"<?php if (isset($this->param->tprel) and $this->param->tprel == 'S') : ?> selected="selected"<?php endif; ?>>Sintético</option>
                </select>
            </div>
            <div class="clear"></div>
            <div class="campo maior">
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
            <div class="campo medio">
                <label for="lbl_saisoid" style="cursor: default; ">Status</label>
                <select id="saisoid" name="saisoid">
                    <option value="">Escolha</option>
                    <?php if (isset($this->view->dados->status) && is_array($this->view->dados->status)) : ?>
                        <?php foreach ($this->view->dados->status as $registro) : ?>
                            <option value="<?php echo $registro->saisoid; ?>"
                            <?php if (isset($this->param->saisoid) && $registro->saisoid == $this->param->saisoid) : ?>
                                selected="selected"
                            <?php endif; ?>
                            ><?php echo $registro->descricao; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>   
                </select>
            </div>
            <div class="ufuf clear"></div>
            <div class="campo menor">
                <label id="lbl_ufuf" for="ufuf">UF</label>
                <select id="ufuf" name="ufuf">
                    <option value="">Escolha</option>
                    <?php if (isset($this->view->dados->estados) && is_array($this->view->dados->estados)) : ?>
                        <?php foreach ($this->view->dados->estados as $registro) : ?>
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
                    <?php if (isset($this->view->dados->cidades) && is_array($this->view->dados->cidades)) : ?>
                        <?php foreach ($this->view->dados->cidades as $registro) : ?>
                            <option value="<?php echo $registro->descricao; ?>"
                            <?php if (isset($this->param->ciddescricao) && $registro->descricao == $this->param->ciddescricao) : ?>
                                selected="selected"
                            <?php endif; ?>
                            ><?php echo $registro->descricao; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="campo maior">
                <label id="lbl_repoid" for="repoid">Representante</label>
                <select id="repoid" name="repoid">
                    <option value="">Escolha</option>
                    <?php if (isset($this->view->dados->representantes) && is_array($this->view->dados->representantes)) : ?>
                        <?php foreach ($this->view->dados->representantes as $registro) : ?>
                            <option value="<?php echo $registro->oid; ?>"
                            <?php if (isset($this->param->repoid) && $registro->oid == $this->param->repoid) : ?>
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
            <div class="clear"></div>
        </div>
    </div>
</form>
    <div class="bloco_acoes">
        <button id="bt_pesquisar" type="button" style="cursor: default; ">Pesquisar</button>
        <button id="bt_exportar" type="button" style="<?php if ((isset($this->param->tprel) and $this->param->tprel != 'A') or !isset($this->param->tprel)) : ?>display:none; <?php endif; ?>cursor: default; ">Exportar XLS</button>
    </div>

   





