<div class="bloco_titulo">Cadastro</div>
<div class="bloco_conteudo">
    <div id="form-cad" class="formulario">

        <div class="campo">
            <label for="tipvoid">Tipo *</label>
            <select id="tipvoid" name="tipvoid" class="maior obrigatorio">
                <option value="">Escolha</option>
                <?foreach ($this->view->listaTipoNovo as $row) { ?>
                    <option value="<?echo $row->tipvoid;?>">
                        <?echo $row->tipvdescricao ;?>
                    </option>
                <?}?>
            </select>
        </div>
        <div class="clear"></div>

        <div class="campo maior">
            <label for="vstoid">Subtipo *</label>
            <select id="vstoid" name="vstoid[]" multiple="multiple" size="15" class="obrigatorio">
                <?foreach ($this->view->listaSubTipoNovo as $row) { ?>
                    <option value="<?echo $row->vstoid;?>">
                        <?echo $row->vstdescricao;?>
                    </option>
                <?}?>
            </select>
        </div>

        <div class="campo maior">
            <label for="obroid">Obrigações Financeiras (Acessórios) *</label>
            <select id="obroid" name="obroid[]" multiple="multiple" size="15" class="obrigatorio">
                <?foreach ($this->view->listaObrigacaoFinanceira as $row) { ?>
                    <option value="<?echo $row->obroid;?>">
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