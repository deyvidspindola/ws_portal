<div class="bloco_titulo">Cadastro</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <fieldset class="medio opcoes-inline">
            <legend>Local de Atendimento</legend>
            <input id="stmponto_fixo" name="stmponto" value="F" type="radio" class="radio chave"
             <?php echo ($this->view->parametros->stmponto_movel != 'M') ? 'checked="checked"' : ''; ?>/><label for="">Fixo</label>
            <input id="stmponto_movel" name="stmponto" value="M" type="radio" class="radio chave"
             <?php echo ($this->view->parametros->stmponto == 'M') ? 'checked="checked"' : ''; ?>/><label for="">Móvel</label>
        </fieldset>

        <div class="clear"></div>

        <div class="campo medio">
            <label id="lbl_ostgrupo" for="ostgrupo">Tipo de O.S.*</label>
            <select id="ostgrupo" class="obrigatorio chave" name="ostgrupo">
                <option value="">Escolha</option>
                <?php foreach ($this->view->comboTipoOrdem as $key => $value) : ?>
                     <option value="<?php echo $value->ostgrupo; ?>" <?php  echo ($this->view->parametros->ostgrupo == $value->ostgrupo) ? 'selected="true"' : ''; ?>>
                        <?php echo $value->ostdescricao; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo medio">
            <label id="lbl_agccodigo" for="agccodigo">Agrupamento Classe *</label>
            <select id="agccodigo" class="obrigatorio chave" name="agccodigo">
                <option value="">Escolha</option>
                <?php foreach ($this->view->comboAgrupamentoClasse as $key => $value) : ?>
                     <option value="<?php echo $value->agccodigo; ?>" <?php  echo ($this->view->parametros->agccodigo == $value->agccodigo) ? 'selected="true"' : ''; ?>>
                        <?php echo $value->agcdescricao; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="clear"></div>

        <div class="campo medio">
            <label id="lbl_peso" for="peso">Peso *</label>
            <input id="peso" class="campo numero obrigatorio chave" type="text" maxlength="3"
                value="<?php  echo ($this->view->parametros->peso == '') ? '' : $this->view->parametros->peso; ?>" name="peso">
        </div>

        <div class="campo medio">
            <label id="lbl_stmchave" for="stmchave">Chave de Serviço Gerada</label>
            <input id="stmchave" class="campo desabilitado" type="text" disabled="true"
                    value="<?php  echo ($this->view->parametros->stmchave == '') ? '' : $this->view->parametros->stmchave; ?>" name="stmchave">
        </div>

        <div class="clear"></div>

        <div class="campo grande">
            <label id="lbl_stmrepoid" for="stmrepoid">Prestador de Serviço *</label>
            <select id="stmrepoid" class="obrigatorio" name="stmrepoid">
                <option value="">Escolha</option>
                <?php foreach ($this->view->comboRepresentante as $key => $value) : ?>
                     <option value="<?php echo $value->repoid; ?>" <?php  echo ($this->view->parametros->stmrepoid == $value->repoid) ? 'selected="true"' : ''; ?>>
                        <?php echo $value->repnome; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

         <div class="campo medio">
            <label id="lbl_stmtempo_minimo" for="stmtempo_minimo">Duração Mínima (min) *</label>
            <input id="stmtempo_minimo" class="campo numero obrigatorio" type="text" maxlength="3"
                value="<?php  echo ($this->view->parametros->stmtempo_minimo == '') ? '' : $this->view->parametros->stmtempo_minimo; ?>" name="stmtempo_minimo">
            <input id="stmtempo_minimo_original" class="" type="hidden"
                value="<?php  echo ($this->view->parametros->stmtempo_minimo == '') ? '' : $this->view->parametros->stmtempo_minimo; ?>" name="stmtempo_minimo_original">
        </div>
        <div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="button" id="bt_inserir" name="bt_inserir" value="gravar">Gravar</button>
    <button type="button" id="bt_voltar">Voltar</button>
</div>