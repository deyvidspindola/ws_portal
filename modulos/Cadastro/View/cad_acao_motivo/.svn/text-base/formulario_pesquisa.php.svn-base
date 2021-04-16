<style>
    div.listagem table th {  
        text-align: center!important;  
    }
</style>
<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">
        <div class="campo medio">
            <label for="aoamoid">Ação</label>
            <select name="aoamoid" id="aoamoid">
                <option value="">Selecione</option>
                <?php if (count($this->view->parametros->acoes) > 0) : ?>
                    <?php foreach ($this->view->parametros->acoes as $acao): ?>
                        <option <?php echo ($this->view->parametros->aoamoid == $acao->aoamoid) ? 'selected="selected"' : '' ?> value="<?php echo $acao->aoamoid; ?> "><?php echo $acao->aoamdescricao; ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>            
        </div>
        <div class="clear"></div>
        <div class="campo medio">
            <label for="aoamoid_motivo">Motivo</label>

            <select name="aoamoid_motivo" id="aoamoid_motivo">
                <option value="">Selecione</option>
                <?php if (count($this->view->parametros->motivos) > 0) : ?>
                    <?php foreach ($this->view->parametros->motivos as $motivo): ?>
                        <option <?php echo (intval($this->view->parametros->aoamoid_motivo) == $motivo->aoamoid) ? 'selected="selected"' : '' ?> value="<?php echo $motivo->aoamoid; ?> "><?php echo $motivo->aoamdescricao; ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <img class="carregando" id="loading-motivos" style="display: none;" src="<?php echo _PROTOCOLO_ . _SITEURL_ . 'modulos/web/images/ajax-loader-circle.gif' ?>" />
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
    <button type="button" id="bt_novo">Novo</button>
</div>





