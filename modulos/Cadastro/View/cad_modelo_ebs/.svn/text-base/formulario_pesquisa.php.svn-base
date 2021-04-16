<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">
    
        <div class="campo maior">
            <label id="lbl_modedescricao" for="modedescricao">Descrição</label>
            <input id="modedescricao" class="campo descricao" type="text" value="<?=$this->view->filtros->modedescricao;?>" name="modedescricao" maxlength="100">
        </div>
        <div class="campo medio">
            <label id="lbl_modemmeoid" for="modemmeoid">Marca</label>
            <select id="modemmeoid" name="modemmeoid">
                <option value="">Escolha</option>
                <?php if(count($this->view->marcas) > 0) :?>
                    <?php foreach ($this->view->marcas as $marcasChave => $marcasValor) :?>
                            <option value="<?php echo  $marcasValor->mmeoid?>"  <?php echo ($marcasValor->mmedescricao == $this->view->filtros->mmedescricao ? 'selected' : '');?> ><?php echo  $marcasValor->mmedescricao?></option>
                    <?php endforeach;?>
                <?php endif;?>
            </select>
        </div>

		<div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
    <button type="button" id="bt_novo">Novo</button>
    <button type="button" id="bt_novo_marca">Nova Marca</button>
</div>







