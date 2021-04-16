<ul class="bloco_opcoes">

    <li class="<?php echo ($this->view->parametros->acao == 'cadastrar' || trim($this->view->parametros->acao) == '') ?  'ativo' : '' ?>" id="aba_cadastrar" 
        <?php echo ($this->view->parametros->acao == 'cadastrar' || trim($this->view->parametros->acao) == '') ?  '' : 'style="background: url(images/fundo.gif);"' ?>>
        <a href="cad_acao_os.php">Cadastrar</a>
    </li>

    <li class="<?php echo ($this->view->parametros->acao == 'vincular') ?  'ativo' : '' ?>" id="aba_vincular" 
        <?php echo ($this->view->parametros->acao == 'vincular') ?  '' : 'style="background: url(images/fundo.gif);"' ?>>
        <a href="cad_acao_os.php?acao=vincular">Vincular Departamento</a>
    </li>

</ul>
<div class="bloco_titulo">Dados Principais</div>
<div class="bloco_conteudo">
    <div class="formulario">
        
        <div class="campo maior">
            <label id="lbl_mhcdescricao" for="mhcdescricao">Descrição *</label>
            <input id="mhcdescricao" class="campo obrigatorio acaoinput" type="text" value="<?php echo (!empty($this->view->mensagemAlerta)) ? $this->view->parametros->mhcdescricao : '' ?>" name="mhcdescricao" maxlength="100">
        </div>

        <div class="clear"></div>
    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_gravar" name="bt_gravar" value="gravar">Cadastrar</button>
</div>