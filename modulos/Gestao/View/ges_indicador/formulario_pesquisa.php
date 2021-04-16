<form id="form_pesquisa" method="post" action="ges_indicador.php">
    <input id="acao" type="hidden" name="acao" value="pesquisar" />
    <input id="gmioid" type="hidden" name="gmioid" value="" />
    <div class="bloco_titulo">Dados para Pesquisa</div>
    <div class="bloco_conteudo">
        <div class="formulario">
           <div class="campo maior">
                <label for="gmioid_nome">Nome</label>
                <select id="gmioid_nome" name="gmioid_nome">
                    <option value="">Escolha</option>
                   <?php foreach ($this->view->dados->comboNome as $option):?>
                         <option value="<?php echo $option->gmioid; ?>"
                                <?php if (isset($this->param->gmioid_nome) && $option->gmioid == $this->param->gmioid_nome) : ?>
                                    selected="selected"
                                <?php endif; ?>
                                 >
                             <?php echo $option->gminome; ?>
                         </option>
                   <?php endforeach; ?>
                </select>
            </div>
            <div class="clear"></div>
            <div class="campo medio">
                <label for="gmicodigo">Código</label>
                <select id="gmicodigo" name="gmicodigo">
                    <option value="">Escolha</option>
                    <?php foreach ($this->view->dados->comboCodigo as $option):?>
                         <option value="<?php echo $option->gmioid; ?>"
                                <?php if (isset($this->param->gmicodigo) && $option->gmioid == $this->param->gmicodigo) : ?>
                                    selected="selected"
                                <?php endif; ?>
                                 >
                             <?php echo $option->gmicodigo; ?>
                         </option>
                   <?php endforeach; ?>
                </select>
            </div>
            <div class="clear"></div>
            <div class="campo medio">
                <label for="gmistatus">Status</label>
                <select id="gmistatus" name="gmistatus">
                    <option value="">Escolha</option>
                    <option value="A"
                            <?php if (isset($this->param->gmistatus) && ("A" == $this->param->gmistatus)) : ?>
                                selected="selected"
                            <?php endif; ?>
                            >
                        Ativo
                    </option>
                    <option value="I"
                             <?php if (isset($this->param->gmistatus) && ("I" == $this->param->gmistatus)) : ?>
                                selected="selected"
                            <?php endif; ?>
                            >
                        Inativo
                    </option>
                </select>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="bloco_acoes">
        <button id="bt_pesquisar" type="button">Pesquisar</button>
        <button id="bt_novo" type="button">Novo</button>
    </div>
    <div class="separador"></div>
</form>
<div id="loader" class="carregando invisivel"></div>