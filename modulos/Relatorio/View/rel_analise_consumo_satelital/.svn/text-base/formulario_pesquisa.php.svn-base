<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo medio">
            <label id="lbl_mes" for="mes">Mes:</label>
            <input id="mes" class="campo descricao" type="text" value="<?=@$this->view->filtros->mes;?>" name="mes" maxlength="2">
        </div>

        <div class="campo medio">
            <label id="lbl_ano" for="ano">Ano:</label>
            <input id="ano" class="campo descricao" type="text" value="<?=@$this->view->filtros->ano;?>" name="ano" maxlength="4">
        </div>
        
        <div class="clear"></div>

        <div class="campo medio">
            <label id="lbl_contrato" for="contrato">Contrato:</label>
            <input id="contrato" class="campo descricao" type="text" value="<?=@$this->view->filtros->contrato;?>" name="contrato" maxlength="100">
        </div>
        <div class="clear"></div>

        <div class="campo medio">
            <label id="lbl_antena" for="antena">Antena:</label>
            <input id="antena" class="campo descricao" type="text" value="<?=@$this->view->filtros->antena;?>" name="antena" maxlength="100">
        </div>
        
        <div class="clear"></div>
        
        <div class="campo maior">
            <input id="clioid" class="campo" type="hidden" value="<?=@$this->view->filtros->clioid;?>" name="clioid" maxlength="100">

            <label id="lbl_cliente" for="cliente">Cliente:</label>
            <input id="cliente" class="campo descricao" type="text" value="<?=@$this->view->filtros->cliente;?>" name="cliente" maxlength="100">
        </div>
        <input type="hidden" id="equipamentos" name="equipamentos">
		<div class="clear"></div>

        <div class="clear"></div>
        
        <div class="campo maior">
            <label id="lbl_cliente" for="cliente">Tipo de pesquisa:</label>
            <select name="tipopesquisa">
                <option <?=($this->view->filtros->tipopesquisa == 'sintetico') ? "selected='selected'": '';?> value="sintetico">Sintetico</option>
                <option <?=($this->view->filtros->tipopesquisa == 'analitico') ? "selected='selected'": '';?> value="analitico">Analitico</option>
            </select>
        </div>
        <input type="hidden" id="equipamentos" name="equipamentos">
		<div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
    <button name="acao" value="exportarCSV" id="bt_exportar" title="Exportar o resultado da pesquisa completa para CSV!">Exportar CSV</button>
</div>