<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <?php
            if($this->view->parametros->acao == 'cadastrar') {
                $this->view->parametros->mtpdescricao = '';
            }
        ?>

        <div class="campo medio">
            <label id="lbl_mtpdescricao" for="mtpdescricao">Descrição</label>
            <input id="mtpdescricao" class="campo" type="text" value="<?php echo $this->view->parametros->mtpdescricao; ?>" name="mtpdescricao">
        </div>
         <div class="clear"></div>
    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
    <button type="button" id="bt_novo">Novo</button>
</div>







