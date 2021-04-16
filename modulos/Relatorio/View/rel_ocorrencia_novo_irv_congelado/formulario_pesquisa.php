<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">

    <div class="campo medio">
        <label for="ococdtipo_relatorio">Tipo</label>
        <select id="ococdtipo_relatorio" name="ococdtipo_relatorio">
            <option <?php echo ($this->view->parametros->ococdtipo_relatorio == '') ? 'selected="selected"' : '' ?> value="">Todos</option>
            <option <?php echo ($this->view->parametros->ococdtipo_relatorio == 'A') ? 'selected="selected"' : '' ?> value="A">Analítico</option>
            <option <?php echo ($this->view->parametros->ococdtipo_relatorio == 'P') ? 'selected="selected"' : '' ?> value="P">Apoio</option>
            <option <?php echo ($this->view->parametros->ococdtipo_relatorio == 'D') ? 'selected="selected"' : '' ?> value="D">Apoio Detalhado</option>
            <option <?php echo ($this->view->parametros->ococdtipo_relatorio == 'M') ? 'selected="selected"' : '' ?> value="M">Macro</option>
            <option <?php echo ($this->view->parametros->ococdtipo_relatorio == 'S') ? 'selected="selected"' : '' ?> value="S">Sintético</option>
            <option <?php echo ($this->view->parametros->ococdtipo_relatorio == 'R') ? 'selected="selected"' : '' ?> value="R">Sintético Resumido</option>
            <option <?php echo ($this->view->parametros->ococdtipo_relatorio == 'EX') ? 'selected="selected"' : '' ?> value="EX">Excluídos</option>
        </select>
    </div>
    <div class="clear"></div>
    <div class="campo data periodo">
        <div class="inicial">
            <label for="ococdperiodo_inicial">Período</label>
            <input id="ococdperiodo_inicial" name="ococdperiodo_inicial" maxlength="10" value="<?php echo $this->view->parametros->ococdperiodo_inicial ?>" class="campo" type="text">
        </div>
        <div class="campo label-periodo">a</div>
        <div class="final">
            <label for="ococdperiodo_final">&nbsp;</label>
            <input id="ococdperiodo_final" name="ococdperiodo_final" maxlength="10" value="<?php echo $this->view->parametros->ococdperiodo_final ?>" class="campo" type="text">
        </div>
    </div>
    <div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
</div>







