<div class="bloco_titulo">Dados para Cadastro</div>
<div class="bloco_conteudo">
    <div class="formulario">
        <div class="campo medio">
            <label for="gtroid">Grupo de Trabalho *</label>            
            <select id="gtroid" name="gtroid">
                <option value="">Escolha</option>
                <?php foreach($this->view->parametros->comboGrupoTrabalho as $grupoTrabalho): ?>
                <option <?php echo $this->view->parametros->gtroid == $grupoTrabalho->gtroid ? 'selected="selected"' : ''?> value="<?php echo $grupoTrabalho->gtroid; ?>"><?php echo $grupoTrabalho->gtrnome; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="clear"></div>
        
        <div class="campo medio">
            <label for="motaoid">Tipo Pausa *</label>
            <select id="motaoid" name="motaoid">
                <option value="">Escolha</option>
                <?php foreach($this->view->parametros->comboTipoPausa as $tipoPausa): ?>
                <option <?php echo $this->view->parametros->motaoid == $tipoPausa->motaoid ? 'selected="selected"' : ''?> value="<?php echo $tipoPausa->motaoid; ?>"><?php echo $tipoPausa->motamotivo; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="clear"></div>
        
        <div class="campo medio">
            <label for="hrpexibe_alerta">Exibe Alerta *</label>
            <select id="hrpexibe_alerta" name="hrpexibe_alerta">
                <option value="">Escolha</option>
                <option <?php echo $this->view->parametros->hrpexibe_alerta == 'true' ? 'selected="selected"' : ''?> value="true">Sim</option>
                <option <?php echo $this->view->parametros->hrpexibe_alerta == 'false' ? 'selected="selected"' : ''?> value="false">Não</option>
            </select>
        </div>
        
        <div class="clear"></div>
        
        <div class="campo medio">
            <label for="hrpcadastro_obrigatorio">Cadastro Obrigatório *</label>
            <select id="hrpcadastro_obrigatorio" name="hrpcadastro_obrigatorio">
                <option value="">Escolha</option>
                <option <?php echo $this->view->parametros->hrpcadastro_obrigatorio == 'true' ? 'selected="selected"' : ''?> value="true">Sim</option>
                <option <?php echo $this->view->parametros->hrpcadastro_obrigatorio == 'false' ? 'selected="selected"' : ''?> value="false">Não</option>
            </select>
        </div>
        
        <div class="clear"></div>
        
        <div class="campo medio">
            <label for="hrptolerancia">Tolerância *</label>
            <select id="hrptolerancia" name="hrptolerancia">
                <option value="">Escolha</option>
                <option <?php echo $this->view->parametros->hrptolerancia == '00' ? 'selected="selected"' : ''?> value="00">00</option>
                <option <?php echo $this->view->parametros->hrptolerancia == '05' ? 'selected="selected"' : ''?> value="05">05</option>
                <option <?php echo $this->view->parametros->hrptolerancia == '10' ? 'selected="selected"' : ''?> value="10">10</option>
                <option <?php echo $this->view->parametros->hrptolerancia == '15' ? 'selected="selected"' : ''?> value="15">15</option>
                <option <?php echo $this->view->parametros->hrptolerancia == '20' ? 'selected="selected"' : ''?> value="20">20</option>
                <option <?php echo $this->view->parametros->hrptolerancia == '25' ? 'selected="selected"' : ''?> value="25">25</option>                
                <option <?php echo $this->view->parametros->hrptolerancia == '30' ? 'selected="selected"' : ''?> value="30">30</option>                
            </select>
        </div>
        
        <div class="clear"></div>
        
        <div class="campo medio">
            <label for="hrptempo">Tempo *</label>
            <input type="text" class="campo" id="hrptempo" name="hrptempo" value="<?php echo $this->view->parametros->hrptempo ?>" maxlength="3" />
        </div>
        
        <div class="clear"></div>
    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_confirmar" name="bt_confirmar" value="confirmar">Confirmar</button>    
</div>