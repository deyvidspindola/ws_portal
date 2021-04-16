<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">
        
        <div class="campo menor">
            <label id="lbl_gmaano" for="gmaano">Ano de Referência *</label>
            <select id="gmaano" name="gmaano">
                 <option value="">Escolha</option>
                 <?php if (isset($this->view->listaAnos) && is_array($this->view->listaAnos)) : ?>
                    <?php foreach ($this->view->listaAnos as $ano) : ?>
                        <option value="<?php echo $ano; ?>"
                        <?php if (isset($this->view->parametros->gmaano) && $ano == $this->view->parametros->gmaano) : ?>
                            selected="selected"
                        <?php endif; ?>
                        ><?php echo $ano ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="clear"></div>
        <div class="campo maior">
            <label id="lbl_gmadepoid" for="gmadepoid">Departamento</label>
            <select id="gmadepoid" name="gmadepoid">
                <option value="">Escolha</option>
                 <?php if (isset($this->view->departamentos) && is_array($this->view->departamentos)) : ?>
                    <?php foreach ($this->view->departamentos as $registro) : ?>
                        <option value="<?php echo $registro->depoid; ?>"
                        <?php if (isset($this->view->parametros->gmadepoid) && $registro->depoid == $this->view->parametros->gmadepoid) : ?>
                            selected="selected"
                        <?php endif; ?>
                        ><?php echo $registro->depdescricao; ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="clear"></div>

        <div class="campo maior">
            <label id="lbl_gmaprhoid" for="gmaprhoid">Cargo</label>
            <select id="gmaprhoid" name="gmaprhoid">
                <option value="">Escolha</option>
                 <?php if (isset($this->view->cargos) && is_array($this->view->cargos)) : ?>
                    <?php foreach ($this->view->cargos as $registro) : ?>
                        <option value="<?php echo $registro->prhoid; ?>"
                        <?php if (isset($this->view->parametros->gmaprhoid) && $registro->prhoid == $this->view->parametros->gmaprhoid) : ?>
                            selected="selected"
                        <?php endif; ?>
                        ><?php echo $registro->prhperfil; ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        
        <div class="clear"></div>
        
        <div class="campo maior">
            <label id="lbl_gmafunoid" for="gmafunoid">Funcionário</label>
            <select id="gmafunoid" name="gmafunoid">
                <option value="">Escolha</option>
                  <?php if (isset($this->view->funcionarios) && is_array($this->view->funcionarios)) : ?>
                    <?php foreach ($this->view->funcionarios as $registro) : ?>
                        <option value="<?php echo $registro->funoid; ?>"
                        <?php if (isset($this->view->parametros->gmafunoid) && $registro->funoid == $this->view->parametros->gmafunoid) : ?>
                            selected="selected"
                        <?php endif; ?>
                        ><?php echo $registro->funcionario; ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="clear"></div>
        
        <div class="campo maior">
            <label id="lbl_gmanome" for="gmanome">Nome do Nível</label>
            <input id="gmanome" class="campo" maxlength="30" type="text" value="<?php echo $this->view->parametros->gmanome; ?>" name="gmanome">
        </div>

		<div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
    <button type="button" id="bt_novo">Novo</button>
</div>







