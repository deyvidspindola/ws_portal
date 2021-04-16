<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo">
            <label for="mlomcaoid">Marca *</label>
            <select id="mlomcaoid" name="mlomcaoid" class="medio obrigatorio">
                <option value="">Escolha</option>
                <?foreach ($this->view->listaMarcas as $row) { ?>
                    <option value="<?echo $row->mcaoid;?>" <?echo ($this->view->parametros->mlomcaoid == $row->mcaoid ? "SELECTED" : "") ?>>
                        <?echo $row->mcamarca;?>
                    </option>
                <?}?>
            </select>
        </div>

         <div class="campo">
            <label>&nbsp;</label>
            <input id="marca_inativa" name="marca_inativa" type="checkbox" class="campo" value="I"
            <?php echo ($this->view->parametros->marca_inativa == 'I') ? 'checked="true"' : '' ;?>>
            <label for="marca_inativa" class="medio">&nbsp;Somente Marcas inativas</label>
        </div >

        <div class="clear"></div>

        <div class="campo medio">
            <label for="mlooid">Modelo</label>
            <select id="mlooid" name="mlooid">
                <option value="">Escolha</option>
                 <?foreach ($this->view->listaModelos as $row) { ?>
                    <option value="<?echo $row->mlooid;?>" <?echo ($this->view->parametros->mlooid == $row->mlooid ? "SELECTED" : "") ?>>
                        <?echo $row->mlomodelo;?>
                    </option>
                <?}?>
            </select>
         </div>

         <div class="campo">
            <label>&nbsp;</label>
            <input id="modelo_inativo" name="modelo_inativo" type="checkbox" class="campo" value="I"
            <?php echo ($this->view->parametros->modelo_inativo == 'I') ? 'checked="true"' : '' ;?>>
            <label for="modelo_inativo" class="medio">&nbsp;Somente Modelos inativos</label>
        </div >

        <div class="clear"></div>

        <input id="retModelo" type="hidden" name="retModelo" value="<?php echo str_replace("=","%3D",str_replace("&","%26",$this->view->parametros->retModelo)); ?>">
        <input id="url_retorno" type="hidden" name="url_retorno" value="<?php echo trata_retorno($this->view->parametros->retModelo,$this->view->parametros->mcaoid); ?>">
    </div>
</div>

<div class="bloco_acoes">
    <button type="button" id="bt_pesquisar">Pesquisar</button>
    <button type="button" id="bt_novo"  <?php echo ( $this->permissaoCadastro ) ? '' : "disabled='disabled'"; ?> >Novo</button>

     <?php if ($this->view->parametros->retModelo != '' ):?>
        <button type="button" id="bt_retornar">Sair</button>
    <?php endif; ?>
</div>







