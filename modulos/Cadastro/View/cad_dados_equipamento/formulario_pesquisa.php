<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">
    
        <div class="campo menor">
            <label id="lbl_placa" for="placa">Placa:</label>
            <input id="placa" class="campo descricao" type="text" value="<?=@$this->view->filtros->placa;?>" name="placa" maxlength="100" data-msg-error='Informe pelo menos 4 caracteres da placa.'>
        </div>

        <div class="campo medio">
            <label id="lbl_chassi" for="chassi">Chassi:</label>
            <input id="chassi" class="campo descricao" type="text" value="<?=@$this->view->filtros->chassi;?>" name="chassi" maxlength="100">
        </div>
        
        <div class="campo medio">
            <label id="lbl_contrato" for="contrato">Contrato:</label>
            <input id="contrato" class="campo descricao" type="text" value="<?=@$this->view->filtros->contrato;?>" name="contrato" maxlength="100">
        </div>

        <div class="clear"></div>
        
        <div class="campo maior">
            <input id="clioid" class="campo" type="hidden" value="<?=@$this->view->filtros->clioid;?>" name="clioid" maxlength="100">

            <label id="lbl_cliente" for="cliente">Cliente:</label>
            <input id="cliente" class="campo descricao" type="text" value="<?=@$this->view->filtros->cliente;?>" name="cliente" maxlength="100">
        </div>
        <!--
        <div class="campo maior">
            <label id="" for="">&nbsp;</label>
            <button type="submit" id="bt_pesquisar_cliente">Pesquisar Cliente</button>
        </div>
        -->
        <input type="hidden" id="equipamentos" name="equipamentos">
		<div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar" class="desabilitado" disabled="" title="Carregando, aguarde!">Pesquisar</button>
    <button name="acao" value="exportarCSV" id="bt_exportar" disabled="" class="desabilitado" style="display: none;" title="Carregando, aguarde!">Exportar CSV</button>
</div>