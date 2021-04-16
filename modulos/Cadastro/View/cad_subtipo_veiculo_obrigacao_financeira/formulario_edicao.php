<div class="bloco_titulo">Cadastro</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo">
            <label>Tipo *</label>
            <input type="text" id="tipvdescricao" name="tipvdescricao" class="campo maior desabilitado"
                    value="<?php echo $this->view->parametros->tipvdescricao; ?>">
        </div>
        <div class="clear"></div>


        <div class="campo">
            <label>Subtipo *</label>
            <input type="text" id="vstdescricao" name="vstdescricao" class="campo maior desabilitado"
                    value="<?php echo $this->view->parametros->vstdescricao; ?>">
            <input type="hidden" id="vstoid" name="vstoid[]" class="" value="<?php echo $this->view->parametros->vstoid; ?>">
        </div>

        <div class="clear"></div>

        <div class="campo maior">
            <label for="obroid">Obrigações Financeiras (Acessórios) *</label>
            <select id="obroid" name="obroid[]" multiple="multiple" size="15" class="obrigatorio">
                <?foreach ($this->view->listaObrigacaoFinanceira as $row) { ?>
                    <option value="<?echo $row->obroid;?>" <?php echo ( in_array($row->obroid, $this->view->parametros->lista_obrigacao )) ? "SELECTED" : ""; ?>>
                        <?echo $row->obrobrigacao;?>
                    </option>
                <?}?>
            </select>
        </div>
        <div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="button" id="bt_gravar" name="bt_gravar">Gravar</button>
    <button type="button" id="bt_voltar">Voltar</button>
</div>