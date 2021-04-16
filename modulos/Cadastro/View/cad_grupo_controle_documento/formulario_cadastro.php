<div class="bloco_titulo">Grupo do Documento</div>
<div class="bloco_conteudo">
    <div class="formulario">        
        
        <input id="itseoid" type="hidden" class="campo" type="text" value="<?php echo $this->view->parametros->itseoid ?>" name="itseoid">

        <div class="campo medio">
            <label id="lbl_itsedescricao" for="itsedescricao">Grupo do Documento *</label>
            <input id="itsedescricao" class="campo" type="text" value="<?php echo $this->view->parametros->itsedescricao ?>" name="itsedescricao">
        </div>

		<div class="clear"></div>


    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_gravar" name="bt_gravar" value="gravar">Confirmar</button>
    <button type="button" id="bt_voltar">Voltar</button>
</div>