<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">
    
        <div class="campo maior">
            <label id="lbl_tipvdescricao" for="tipvdescricao">Descrição</label>
            <input id="tipvdescricao" class="campo descricao" type="text" value="<?=$this->view->filtros->tipvdescricao;?>" name="tipvdescricao" maxlength="100">
        </div>

        <div class="campo menor">
            <label id="lbl_tipvcategoria" for="tipvcategoria">Tipo Veículo</label>
            <select id="tipvcategoria" name="tipvcategoria">
                <option value="">Escolha</option>
                <option value="L" <?echo ($this->view->filtros->tipvcategoria == 'L' ? 'selected' : '');?> >Leve</option>
                <option value="P" <?echo ($this->view->filtros->tipvcategoria == 'P' ? 'selected' : '');?>>Pesado</option>
            </select>
        </div>

		<div class="clear"></div>


    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
    <button type="button" id="bt_novo">Novo</button>
</div>







