<div class="bloco_titulo">Dados Principais</div>
<div class="bloco_conteudo">
    <div class="formulario">
        
        <div class="campo maior">
            <label id="lbl_modedescricao" for="modedescricao">Descrição *</label>
            <input id="modedescricao" class="campo descricao" type="text" value="<?=$this->view->parametros->modedescricao;?>" name="modedescricao" maxlength="100">
        </div>

        <div class="campo medio">
            <label id="lbl_modemmeoid" for="modemmeoid">Marca *</label>
            <select id="modemmeoid" name="modemmeoid">
                <option value="">Escolha</option>
                <?php if(count($this->view->marcas) > 0) :?>
                    <?php foreach ($this->view->marcas as $marcasChave => $marcasValor) :?>
                            <option value="<?php echo  $marcasValor->mmeoid?>"  <?php echo ($marcasValor->mmeoid == $this->view->parametros->modemmeoid ? 'selected' : '');?> ><?php echo  $marcasValor->mmedescricao?></option>
                    <?php endforeach;?>
                <?php endif;?>
            </select>
        </div>

        <div class="campo maior">
            <label id="lbl_modeobroid" for="modeobroid">Chicote designado para Marca/Modelo * <img class="btn-help" src="images/help10.gif" style="cursor: pointer" onclick="mostrarHelpComment(this,'Mínimo três letras para a auto pesquisa.','D' , '');"></label>
            <input id="modeobroid_autocomplete" class="campo" type="text" value="<?php echo $this->view->parametros->modeobroid_autocomplete; ?>" name="cmp_cliente_autocomplete">
            <input id="modeobroid" type="hidden" value="<?php echo $this->view->parametros->modeobroid; ?>" class="validar" name="modeobroid">
        </div>

		<div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_gravar" name="bt_gravar" value="gravar">Salvar</button>
    <button type="button" id="bt_voltar">Voltar</button>
</div>