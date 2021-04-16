<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo mes_ano">
            <label id="lbl_bonredt_bonificacao" for="bonredt_bonificacao">Mês/Ano</label>
            <input id="bonredt_bonificacao" type="text" name="bonredt_bonificacao" maxlength="10" value="<?php echo $this->view->parametros->bonredt_bonificacao; ?>" class="campo" />
        </div>

        <div class="clear"></div>

        <div class="campo maior">
            <label id="lbl_bonrerepoid" for="bonrerepoid">Representante</label>
            <select id="bonrerepoid" name="bonrerepoid">
                <option value="">Escolha</option>
                <?php foreach($this->view->representantes as $representante) : ?>
                    <option value="<?php echo $representante->repoid; ?>" <?php echo ($representante->repoid == $this->view->parametros->bonrerepoid ? 'selected="selected"' : ''); ?>>
                        <?php echo $representante->repnome; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="clear"></div>
        
        <div class="campo medio">
            <label id="lbl_bonrebonrecatoid" for="bonrebonrecatoid">Categoria</label>
            <select id="bonrebonrecatoid" name="bonrebonrecatoid">
                <option value="">Escolha</option>
                <?php foreach($this->view->categorias as $categoria) : ?>
                    <option value="<?php echo $categoria->bonrecatoid; ?>" <?php echo ($categoria->bonrecatoid == $this->view->parametros->bonrebonrecatoid ? 'selected="selected"' : ''); ?>>
                        <?php echo $categoria->bonrecatnome; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo menor">
            <label id="lbl_bonrestatus" for="bonrestatus">Status</label>
            <select id="bonrestatus" name="bonrestatus">
                <option value="">Escolha</option>
                <option <?php echo ($this->view->parametros->bonrestatus == 'A' ? 'selected="selected"' : ''); ?> value="A">Aberto</option>
                <option <?php echo ($this->view->parametros->bonrestatus == 'C' ? 'selected="selected"' : ''); ?> value="C">Cancelado</option>
                <option <?php echo ($this->view->parametros->bonrestatus == 'R' ? 'selected="selected"' : ''); ?> value="R">Rateado</option>
            </select>
        </div>

        <div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
    <?php if ( $this->view->permissao ) : ?>
        <button type="button" id="bt_novo">Novo</button>
    <?php endif; ?>
</div>