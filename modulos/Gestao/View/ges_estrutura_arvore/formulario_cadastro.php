<div class="bloco_titulo">Cadastro</div>
<div class="bloco_conteudo">
    <div class="formulario">
        
        <div class="campo menor">
            <label id="lbl_gmaano" for="gmaano">Ano de Referência *</label>
            <select id="gmaano" name="gmaano" <?php echo (isset($this->view->parametros->editar) && $this->view->parametros->editar) ? 'disabled="disabled" class="desabilitado"' : ''  ?> >
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
      
         <div class="campo maior">
            <label id="lbl_gmadepoid" for="gmadepoid">Departamento *</label>
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
            <label id="lbl_gmaprhoid" for="gmaprhoid">Cargo *</label>
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
        
         <div class="campo maior">
            <label id="lbl_gmafunoid" for="gmafunoid">Funcionário *</label>
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
        
        <div class="campo menor">
            <label id="lbl_gmanivel" for="gmanivel">Nível *</label>
            <select id="gmanivel" name="gmanivel" <?php echo (isset($this->view->parametros->editar) && $this->view->parametros->editar) ? 'disabled="disabled" class="desabilitado"' : ''  ?>>
                <option value="">Escolha</option>
                <?php for($x = 1; $x < 21; $x++) :?>
                    <option value="<?php echo $x ?>"
                    <?php if($this->view->parametros->gmanivel == $x) : ?>
                        selected 
                    <?php endif; ?>><?php echo $x ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="campo menor">
            <label id="lbl_gmasubnivel" for="gmasubnivel">Subnível *</label>
            <select id="gmasubnivel" name="gmasubnivel" <?php echo (isset($this->view->parametros->editar) && $this->view->parametros->editar) ? 'disabled="disabled" class="desabilitado"' : ''  ?>>
                <option value="">Escolha</option>
                <?php for($x = 1; $x < 21; $x++) :?>
                    <option value="<?php echo $x ?>"
                    <?php if($this->view->parametros->gmasubnivel == $x) : ?>
                        selected 
                    <?php endif; ?>><?php echo $x ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="clear"></div>

          <div class="campo maior">
            <label id="lbl_gmafunoid_superior" for="gmafunoid_superior">Superior imediato</label>
            <select id="gmafunoid_superior" name="gmafunoid_superior" <?php echo (isset($this->view->parametros->editar) && $this->view->parametros->editar) ? 'disabled="disabled" class="desabilitado"' : ''  ?> >
                 <option value="">Escolha</option>
                  <?php if (isset($this->view->superior) && is_array($this->view->superior)) : ?>
                    <?php foreach ($this->view->superior as $registro) : ?>
                        <option value="<?php echo $registro->funoid; ?>"
                        <?php if (isset($this->view->parametros->gmafunoid_superior) && $registro->funoid == $this->view->parametros->gmafunoid_superior) : ?>
                            selected="selected"
                        <?php endif; ?>
                        ><?php echo $registro->funcionario; ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="campo maior">
            <label id="lbl_gmanome" for="gmanome">Nome do Nível *</label>
            <input id="gmanome" class="campo <?php echo (isset($this->view->parametros->editar) && $this->view->parametros->editar) ? 'desabilitado' : ''  ?>" maxlength="30" type="text" value="<?php echo $this->view->parametros->gmanome; ?>" name="gmanome" <?php echo (isset($this->view->parametros->editar) && $this->view->parametros->editar) ? 'disabled="disabled"' : ''  ?>>
        </div>
        
      
		<div class="clear"></div>


    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_gravar" name="bt_gravar" value="gravar">Confirmar</button>
    <button type="button" id="bt_voltar">Retornar</button>
</div>