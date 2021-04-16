
<div class="bloco_titulo">
    <?php if($this->view->parametros->ripramal) { ?>
    Alteração 
    <?php } else { ?>
    Cadastro
    <?php } ?>
</div>

<div class="bloco_conteudo">

    <div class="formulario">

        <div class="campo menor">
            <label id="lbl_ripramal" for="ripramal">Ramal *</label>
            <input id="ripramal" class="campo ramal" type="text" value="<?php echo $this->view->parametros->ripramal;?>" name="ripramal" maxlength="10">
        </div>

        <div class="campo menor">
            <label id="lbl_ripip" for="ripip">IP *</label>
            <input id="ripip" class="campo ip" type="text" value="<?php echo $this->view->parametros->ripip;?>" name="ripip" maxlength="15">
        </div>

        <div class="campo maior">
            <label id="lbl_ripdescricao" for="ripdescricao">Descrição *</label>
            <input id="ripdescricao" class="campo descricao" type="text" value="<?php echo $this->view->parametros->ripdescricao;?>" name="ripdescricao" maxlength="150">
        </div>

        <div class="campo menor">
            <label id="lbl_ripponto_roteamento" for="ripponto_roteamento">Roteamento *</label>
            <select name="ripponto_roteamento" id="ripponto_roteamento" class="campo roteamento">
                <?php if($this->view->parametros->ripponto_roteamento == 't') { ?>
                <option value="1" selected>SIM</option> 
                <?php } else { ?>
                <option value="1">SIM</option> 
                <?php } ?>
                <?php if($this->view->parametros->ripponto_roteamento == 'f') { ?>
                <option value="f" selected>NÃO</option> 
                <?php } else { ?>
                <option value="f">NÃO</option> 
                <?php } ?>
            </select>
        </div>

		<div class="clear"></div>

    </div>

</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_gravar" name="bt_gravar" value="gravar">Salvar</button>
    <button type="button" id="bt_voltar">Voltar</button>
</div>