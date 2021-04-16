<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <?php
            if($this->view->parametros->acao == 'cadastrar') {
                $this->view->parametros->mcamarca = '';
            }
        ?>

        <div class="campo medio">
            <label id="lbl_mcamarca" for="mcamarca">Marca</label>
            <input id="mcamarca" class="campo" type="text" value="<?php echo $this->view->parametros->mcamarca; ?>" name="mcamarca">
            <input id="retMarca" type="hidden" name="retMarca" value="<?php echo str_replace("=","%3D",str_replace("&","%26",$this->view->parametros->retMarca)); ?>">
            <input id="url_retorno" type="hidden" name="url_retorno" value="<?php echo trata_retorno($this->view->parametros->retMarca,$this->view->parametros->mcaoid); ?>">
        </div>
         <div class="clear"></div>
    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
    <?php if ($this->permissao_cadastro_marca) : ?>
        <button type="button" id="bt_novo">Novo</button>
    <?php endif; ?>

     <?php if ($this->view->parametros->retMarca != '' ):?>
        <button type="button" id="bt_retornar">Sair</button>
    <?php endif; ?>
</div>







