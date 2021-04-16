<div class="bloco_titulo">Cadastro</div>
<div class="bloco_conteudo">
    <div class="formulario">
        <div class="campo medio">
            <label id="lbl_mcamarca" for="mcamarca">Marca *</label>
            <input id="mcamarca" class="campo" type="text" value="<?php echo $this->view->parametros->mcamarca; ?>" name="mcamarca" maxlength="50">
             <input id="retMarca" type="hidden" name="retMarca" value="<?php echo str_replace("=","%3D",str_replace("&","%26",$this->view->parametros->retMarca)); ?>">
            <input id="url_retorno" type="hidden" name="url_retorno" value="<?php echo trata_retorno($this->view->parametros->retMarca,$this->view->parametros->mcaoid); ?>">
        </div>

         <div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button <?=(!$this->permissao_cadastro_marca) ? "disabled='disabled'" : "" ?> type="button" id="bt_gravar" name="bt_gravar" value="gravar">Gravar</button>
    <button type="button" id="bt_voltar">Voltar</button>

     <?php if ($this->view->parametros->retMarca != '' ):?>
        <button type="button" id="bt_retornar">Sair</button>
    <?php endif; ?>
</div>