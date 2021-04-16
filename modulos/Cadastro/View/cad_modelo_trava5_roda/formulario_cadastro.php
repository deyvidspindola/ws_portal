<div class="bloco_titulo">Cadastro</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo medio">
            <label id="lbl_tmodescricao" for="tmodescricao">Modelo *</label>
            <input id="tmodescricao" class="campo" type="text" value="<?=$this->view->parametros->tmodescricao?>" name="tmodescricao">
        </div>
	<div class="clear"></div>        
        
        <div class="campo grande">
            <label for="tmoprdoid">Produto *</label>
            <select id="tmoprdoid" name="tmoprdoid">
                <option value="">Escolha</option>
                <?php if (isset($this->view->parametros->produtos)) {
                    foreach ($this->view->parametros->produtos as $produto) { ?>
                    <option value="<?=$produto['prdoid'];?>" <?=($this->view->parametros->tmoprdoid == $produto['prdoid'] ? "SELECTED" : "") ?>><?=utf8_decode($produto['prdproduto']);?></option>
                <?php  }
                }?>
            </select>            
        </div>
	<div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_gravar" name="bt_gravar" value="gravar">Gravar</button>
    <button type="button" id="bt_voltar">Voltar</button>
</div>