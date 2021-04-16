<div class="bloco_titulo">Dados Principais</div>
<div class="bloco_conteudo">
    <div class="formulario">
        
        <div class="campo menor">
            <label id="lbl_agccodigo" for="agccodigo">Código *</label>
            <input id="agccodigo" class="campo codigo" type="text" value="<?php echo $this->view->parametros->agccodigo;?>" name="agccodigo" maxlength="10">
        </div>

        <div class="campo maior">
            <label id="lbl_agcdescricao" for="agcdescricao">Descrição *</label>
            <input id="agcdescricao" class="campo descricao" type="text" value="<?php echo $this->view->parametros->agcdescricao;?>" name="agcdescricao" maxlength="100">
        </div>

		<div class="clear"></div>


    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_gravar" name="bt_gravar" value="gravar">Salvar</button>
    <button type="button" id="bt_voltar">Voltar</button>
</div>