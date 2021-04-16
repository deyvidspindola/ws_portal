<div class="bloco_titulo">Cadastro</div>
<div class="bloco_conteudo">
    <div class="formulario">
        <div class="campo medio">
            <label id="lbl_osmc_motivo" for="osmc_motivo">Motivo: *</label>
            <input type="text" id="osmc_motivo" name="osmc_motivo" class="campo" maxLength="50" value="<?php echo htmlentities((isset($this->view->parametros->osmcoid) && !empty($this->view->parametros->osmcoid)) ? trim($this->view->parametros->osmc_motivo) : '' ) ?>"/>
        </div> 

        <?php if (isset($this->view->parametros->osmc_status) && !empty($this->view->parametros->osmc_status)): ?>
            <div class="campo menor">
                <label for="lbl_osmc_status">Status:</label>
                <select id="osmc_status" name="osmc_status" >
                    <option value="A"<?php echo (isset($this->view->parametros->osmc_status) && !empty($this->view->parametros->osmc_status) != '' && $this->view->parametros->osmc_status === "A") ? 'selected="selected"' : '' ?>>Ativo</option>
                    <option value="I"<?php echo (isset($this->view->parametros->osmc_status) && !empty($this->view->parametros->osmc_status) != '' && $this->view->parametros->osmc_status === "I") ? 'selected="selected"' : '' ?>>Inativo</option>
                </select>
            <?php endif; ?>
        </div>
        <div class="clear"></div>
        <div class="separador"></div>
    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_confirmar" name="bt_confirmar" value="gravar">Confirmar</button>
    <button type="button" id="bt_voltar">Voltar</button>
</div>
