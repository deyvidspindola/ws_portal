<div  class="mensagem info ">
       Obrigatório informar ao menos um filtro para a pesquisa.
</div>

<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo menor">
            <label id="lbl_ordem_servico" for="ordem_servico">Ordem de Serviço</label>
            <input id="ordem_servico" class="campo numero desabilitado" type="text" maxlength="9" value="" name="ordem_servico" disabled="true">
        </div>

        <div class="campo menor">
            <label id="lbl_stmchave" for="stmchave">Chave de Serviço</label>
            <input id="stmchave" class="campo alfanumerico" type="text" maxlength="15" 
                    value="<?php  echo ($this->view->parametros->stmchave == '') ? '' : $this->view->parametros->stmchave; ?>" name="stmchave">
        </div>

        <div class="campo medio">
            <label id="lbl_stmrepoid" for="stmrepoid">Prestador de Serviço  </label>
            <select id="stmrepoid" class="" name="stmrepoid">
                <option value="">Escolha</option>
                <?php foreach ($this->view->comboRepresentante as $key => $value) : ?>
                     <option value="<?php echo $value->repoid; ?>" <?php  echo ($this->view->parametros->stmrepoid == $value->repoid) ? 'selected="true"' : ''; ?>>
                        <?php echo $value->repnome; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

		<div class="clear"></div>

        <fieldset class="medio opcoes-inline">
            <legend>Local de Atendimento</legend>
            <input id="stmponto_ambos" name="stmponto" value="A" type="radio" class="radio"
                    <?php echo ($this->view->parametros->stmponto == 'A') ? 'checked="checked"' : (empty($this->view->parametros->stmponto) ? 'checked="checked"' :'') ; ?>
                    /><label for="">Ambos</label>
            <input id="stmponto_fixo" name="stmponto" value="F" type="radio" class="radio"
                    <?php echo ($this->view->parametros->stmponto == 'F') ? 'checked="checked"' : ''; ?>
                    /><label for="">Fixo</label>
            <input id="stmponto_movel" name="stmponto" value="M" type="radio" class="radio"
                    <?php echo ($this->view->parametros->stmponto == 'M') ? 'checked="checked""' : ''; ?>
                    /><label for="">Móvel</label>
        </fieldset>

        <div class="clear"></div>
    </div>
</div>

<div class="bloco_acoes">
    <button type="button" id="bt_pesquisar">Pesquisar</button>
    <button type="button" id="bt_novo">Novo</button>
</div>