<div class="bloco_titulo">Dados Principais</div>
<div class="bloco_conteudo">
    <div class="formulario">
        
        <div class="campo maior">
            <label id="lbl_tipvdescricao" for="tipvdescricao">Descrição *</label>
            <input id="tipvdescricao" class="campo descricao" type="text" value="<?=$this->view->parametros->tipvdescricao;?>" name="tipvdescricao" maxlength="100">
        </div>

        <div class="campo menor">
            <label id="lbl_tipvcategoria" for="tipvcategoria">Tipo Veículo *</label>
            <select id="tipvcategoria" name="tipvcategoria">
                <option value="">Escolha</option>
                <option value="L" <?echo ($this->view->parametros->tipvcategoria == 'L' ? 'selected' : '');?> >Leve</option>
                <option value="P" <?echo ($this->view->parametros->tipvcategoria == 'P' ? 'selected' : '');?>>Pesado</option>
            </select>
        </div>

        <div class="campo medio inline">
            <input id="tipvcarreta" type="checkbox" name="tipvcarreta" <?echo ($this->view->parametros->tipvcarreta == 't' ? 'checked="checked"' : '');?>>
            <label id="lbl_tipvcarreta" for="tipvcarreta">Veículo Tipo Carreta </label>
        </div>

		<div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_gravar" name="bt_gravar" value="gravar">Salvar</button>
    <button type="button" id="bt_voltar">Voltar</button>
</div>