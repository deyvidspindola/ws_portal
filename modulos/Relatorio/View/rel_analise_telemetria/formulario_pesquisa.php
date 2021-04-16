<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">
        <div class="campo menor">
            <label id="lbl_placa" for="placa">Placa:</label>
            <input id="placa" class="campo descricao" type="text" value="<?=@$this->view->filtros->placa;?>" name="placa" maxlength="100" data-msg-error='Informe pelo menos 4 caracteres da placa.'>
        </div>
        <div class="clear"></div>
        <?php
            // Período Padrão
            $data_inicial = date('d/m/Y', strtotime(date('Y-m-d H:i:s') . ' -2 days'));
            $data_final = date('d/m/Y');
        ?>
        <div class="campo data periodo">
            <div class="inicial">
                <label for="periodo_data_inicial" style="cursor: default; ">Período:</label>
                <input id="periodo_data_inicial" type="text" name="periodo_data_inicial" maxlength="10" value="<?php echo (isset($this->view->filtros->periodo_data_inicial)) ? $this->view->filtros->periodo_data_inicial : $data_inicial; ?>" class="campo" readonly>
            </div>
            <div class="campo label-periodo">a</div>
            <div class="final">
                <label for="periodo_data_final" style="cursor: default; ">&nbsp;</label>
                <input id="periodo_data_final" type="text" name="periodo_data_final" maxlength="10" value="<?php echo isset($this->view->filtros->periodo_data_final) ? $this->view->filtros->periodo_data_final : $data_final; ?>" class="campo" readonly>
            </div>
            <div class="clear"></div>
            <label for="" style="margin-top: 5px; cursor: default; ">Período máximo de 2 dias para pesquisar</label>
        </div>
		<div class="clear"></div>
    </div>
</div>
<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
</div>