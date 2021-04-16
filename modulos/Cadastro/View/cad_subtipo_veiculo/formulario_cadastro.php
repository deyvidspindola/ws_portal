<div class="bloco_titulo">Cadastro</div>
<div class="bloco_conteudo">
    <div class="formulario">
        <div class="campo medio">
            <label id="lbl_vstdescricao" for="vstdescricao">Subtipo *</label>
            <input id="vstdescricao" class="campo" type="text" value="<?php echo $this->view->parametros->vstdescricao; ?>" name="vstdescricao" maxlength="50">
        </div>

        <div class="campo">
            <label for="tipvoid">Tipo *</label>
            <select id="tipvoid" name="tipvoid" class="medio">
                <option value="">Escolha</option>
                <?foreach ($this->view->listaTipo as $row) { ?>
                    <option value="<?echo $row->tipvoid;?>" <?php echo ($this->view->parametros->tipvoid == $row->tipvoid ? "SELECTED" : "") ?>>
                        <?echo $row->tipvdescricao ;?>
                    </option>
                <?}?>
            </select>
        </div>
        <div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="button" id="bt_gravar" name="bt_gravar" value="gravar">Gravar</button>
    <button type="button" id="bt_voltar">Voltar</button>
</div>