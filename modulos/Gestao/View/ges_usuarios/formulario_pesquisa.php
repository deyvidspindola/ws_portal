<form id="form_pesquisa" method="post">
    <input id="acao" type="hidden" name="acao" value="" />
    <div class="bloco_titulo" style="cursor: default; ">Dados para Pesquisa</div>
    <div class="bloco_conteudo">
        <div class="formulario">
           <div class="campo maior">
                <label for="depoid" style="cursor: default; ">Departamento *</label>
                <select id="depoid" name="depoid">
                    <option value="">Escolha</option>
                    <option value="todos" 
                    <?php if($this->param->depoid == 'todos') :?> 
                        selected 
                    <?php endif; ?>>Todos</option>
                    <?php if (isset($this->view->dados->departamentos) && is_array($this->view->dados->departamentos)) : ?>
                        <?php foreach ($this->view->dados->departamentos as $registro) : ?>
                            <option value="<?php echo $registro->depoid; ?>"
                            <?php if (isset($this->param->depoid) && $registro->depoid == $this->param->depoid) : ?>
                                selected="selected"
                            <?php endif; ?>
                            ><?php echo $registro->depdescricao; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="clear"></div>
            <div class="campo maior">
                <label for="prhoid" style="cursor: default; ">Cargo *</label>
                <select id="prhoid" name="prhoid">
                    <option value="">Escolha</option>
                    <option value="todos" 
                    <?php if($this->param->prhoid == 'todos') :?> 
                        selected 
                    <?php endif; ?>>Todos</option>
                    <?php if (isset($this->view->dados->cargos) && is_array($this->view->dados->cargos)) : ?>
                        <?php foreach ($this->view->dados->cargos as $registro) : ?>
                            <option value="<?php echo $registro->prhoid; ?>"
                            <?php if (isset($this->param->prhoid) && $registro->prhoid == $this->param->prhoid) : ?>
                                selected="selected"
                            <?php endif; ?>
                            ><?php echo $registro->prhperfil; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="bloco_acoes">
        <button id="bt_pesquisar" type="button" style="cursor: default; ">Pesquisar</button>
    </div>
    <div class="separador"></div>
</form>