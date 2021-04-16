<div class="bloco_titulo">Dados Principais</div>
<div class="bloco_conteudo">
    <div class="formulario">
        <div class="campo maior">
            <label for="lbl_tpcoid">Cancelar OS quando o Tipo do Contrato for alterado de:</label>
            <select id="pcaotipos_de" name="pcaotipos_de[]" multiple="multiple" size="12">
                <?php if (isset($this->view->tiposContrato) && count($this->view->tiposContrato) > 0) : ?>
                    <?php foreach ($this->view->tiposContrato as $item) : ?>
                        <option <?php echo (is_array($this->view->dados->pcaotipos_de) && in_array($item->tpcoid, $this->view->dados->pcaotipos_de)) ? 'selected="selected"' : '' ?> value="<?php echo $item->tpcoid ?>"><?php echo $item->tpcdescricao ?></option>                    
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div> 
        <div class="campo maior">
            <label for="lbl_tpcoid">Para:</label>
            <select id="pcaotipos_para" name="pcaotipos_para[]" multiple="multiple" size="12">
                <?php if (isset($this->view->tiposContrato) && count($this->view->tiposContrato) > 0) : ?>
                    <?php foreach ($this->view->tiposContrato as $item) : ?>
                        <option <?php echo (is_array($this->view->dados->pcaotipos_para) && in_array($item->tpcoid, $this->view->dados->pcaotipos_para)) ? 'selected="selected"' : '' ?> value="<?php echo $item->tpcoid ?>"><?php echo $item->tpcdescricao ?></option>                    
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div> 
        <div class="campo maior">
            <label for="lbl_csioid">Cancelar OS quando o Status do Contrato for alterado de:</label>
            <select id="pcaostatus_de" name="pcaostatus_de[]" multiple="multiple" size="12">
                <?php if (isset($this->view->contratoSituacao) && count($this->view->contratoSituacao) > 0) : ?>
                    <?php foreach ($this->view->contratoSituacao as $item) : ?>
                        <option <?php echo (is_array($this->view->dados->pcaostatus_de) && in_array($item->csioid, $this->view->dados->pcaostatus_de)) ? 'selected="selected"' : '' ?> value="<?php echo $item->csioid ?>"><?php echo $item->csidescricao ?></option>                    
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="campo maior">
            <label for="lbl_csioid">Para:</label>
            <select id="pcaostatus_para" name="pcaostatus_para[]" multiple="multiple" size="12">
                <?php if (isset($this->view->contratoSituacao) && count($this->view->contratoSituacao) > 0) : ?>
                    <?php foreach ($this->view->contratoSituacao as $item) : ?>
                        <option <?php echo (is_array($this->view->dados->pcaostatus_para) && in_array($item->csioid, $this->view->dados->pcaostatus_para)) ? 'selected="selected"' : '' ?> value="<?php echo $item->csioid ?>"><?php echo $item->csidescricao ?></option>                    
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="bloco_acoes">
    <button type="submit" id="bt_salvar">Salvar</button>
</div>







