<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo">
            <label for="tipvoid">Tipo</label>
            <select id="tipvoid" name="tipvoid" class="medio">
                <option value="">Escolha</option>
                <?foreach ($this->view->listaTipo as $row) { ?>
                    <option value="<?echo $row->tipvoid;?>" <?php echo ($this->view->parametros->tipvoid == $row->tipvoid ? "SELECTED" : "") ?>>
                        <?echo $row->tipvdescricao ;?>
                    </option>
                <?}?>
            </select>
        </div>

        <div class="campo">
            <label for="vstoid">Subtipo</label>
            <select id="vstoid" name="vstoid" class="medio">
                <option value="">Escolha</option>
                <?foreach ($this->view->listaSubTipo as $row) { ?>
                    <option value="<?echo $row->vstoid;?>" <?php echo ($this->view->parametros->vstoid == $row->vstoid ? "SELECTED" : "") ?>>
                        <?echo $row->vstdescricao ;?>
                    </option>
                <?}?>
            </select>
        </div>

        <div class="campo">
            <label for="obroid">Obrigação Financeira</label>
            <select id="obroid" name="obroid" class="maior">
                <option value="">Escolha</option>
                 <?foreach ($this->view->listaObrigacaoFinanceira as $row) { ?>
                    <option value="<?echo $row->obroid;?>" <?php echo ($this->view->parametros->obroid == $row->obroid ? "SELECTED" : "") ?>>
                        <?echo $row->obrobrigacao ;?>
                    </option>
                <?}?>
            </select>
        </div>

		<div class="clear"></div>


    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
    <button type="button" id="bt_novo">Novo</button>
</div>







