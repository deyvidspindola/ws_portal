<div class="bloco_titulo">Cadastro</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo maior">
            <label id="lbl_bonrerepoid" for="bonrerepoid">Representante *</label>
            <select id="bonrerepoid" name="bonrerepoid">
                <option value="">Escolha</option>
                <?php foreach($this->view->representantes as $representante) : ?>
                    <option value="<?php echo $representante->repoid; ?>" <?php echo ($representante->repoid == $this->view->parametros->bonrerepoid ? 'selected="selected"' : ''); ?> >
                        <?php echo $representante->repnome; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="clear"></div>

        <div class="campo mes_ano">
            <label id="lbl_bonredt_bonificacao" for="bonredt_bonificacao">Mês/Ano *</label>
            <input id="bonredt_bonificacao" type="text" name="bonredt_bonificacao" maxlength="10" value="<?php echo $this->view->parametros->bonredt_bonificacao; ?>" class="campo" />
        </div>

        <div class="campo medio">
            <label id="lbl_bonrebonrecatoid" for="bonrebonrecatoid">Categoria *</label>
            <select id="bonrebonrecatoid" name="bonrebonrecatoid">
                <option value="">Escolha</option>
                <?php foreach($this->view->categorias as $categoria) : ?>
                    <option value="<?php echo $categoria->bonrecatoid; ?>" <?php echo ($categoria->bonrecatoid == $this->view->parametros->bonrebonrecatoid ? 'selected="selected"' : ''); ?> >
                        <?php echo $categoria->bonrecatnome; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="clear"></div>

        <div class="campo menor">
            <label id="lbl_bonrevalor_bonificacao" for="bonrevalor_bonificacao">Custo de Eficiência Operacional *</label>
            <input id="bonrevalor_bonificacao" class="campo" maxlength="10" type="text" value="<?php echo ((float) $this->view->parametros->bonrevalor_bonificacao > 0) ? number_format($this->view->parametros->bonrevalor_bonificacao, 2, ',', '.') : ''; ?>" name="bonrevalor_bonificacao">
        </div>

        <div class="clear"></div>

        <div class="campo menor">
            <label id="lbl_bonreqtd_min_os" for="bonreqtd_min_os">Qtde Mínima O.S. *</label>
            <input id="bonreqtd_min_os" class="campo" maxlength="6" type="text" value="<?php echo ((int) $this->view->parametros->bonreqtd_min_os > 0) ? $this->view->parametros->bonreqtd_min_os : ''; ?>" name="bonreqtd_min_os">
        </div>

        <div class="clear"></div>

        <div class="campo medio">
            <label id="lbl_bonrestatus" for="bonrestatus">Status: <?php echo ( isset($this->view->parametros->status_formatado) ? $this->view->parametros->status_formatado : "Aberto"); ?></label>
        </div>

        <div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <?php if ( empty($this->view->parametros->bonrestatus) || $this->view->parametros->bonrestatus == 'A' ) : ?>
        <button type="submit" id="bt_gravar" name="bt_gravar" value="gravar">Salvar</button>
    <?php endif; ?>
    <?php if ( in_array($this->view->parametros->bonrestatus, array('A','R')) ) : ?>
        <button type="button" onclick="javascript: return cancelar('<?php echo $this->view->parametros->bonreoid; ?>');" name="bt_cancelar" value="gravar">Cancelar Custo de Eficiência Operacional</button>
    <?php endif; ?>
    <button type="button" id="bt_voltar">Voltar</button>
</div>