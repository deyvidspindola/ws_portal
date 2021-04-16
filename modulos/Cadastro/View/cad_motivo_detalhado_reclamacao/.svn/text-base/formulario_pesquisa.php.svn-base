<div class="bloco_titulo">Detalhamento do Motivo</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo maior">
            <label for="motivo_geral">Motivo Geral</label>
            <select id="motivo_geral" name="motivo_geral">
                <option value="">Escolha</option>
                <?php foreach($this->view->parametros->motivos_geral as $motivo) : ?>
                    <option value="<?php echo $motivo->mtroid ?>" <?php echo (isset($this->view->parametros->motivo_geral) && ($this->view->parametros->motivo_geral == $motivo->mtroid)) ? 'selected="selected"' : '' ?>>
                        <?php echo $motivo->mtrdescricao; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="clear"></div>

        <div class="campo maior">
            <label for="detalhamento_motivo">Detalhamento do Motivo</label>
            <select id="detalhamento_motivo" name="detalhamento_motivo">
                <option value="">Escolha</option>
                <?php foreach($this->view->parametros->detalhamentos_motivo as $motivo_detalhado) : ?>
                    <option value="<?php echo $motivo_detalhado->mdroid ?>" <?php echo (isset($this->view->parametros->detalhamento_motivo) && ($this->view->parametros->detalhamento_motivo == $motivo_detalhado->mdroid)) ? 'selected="selected"' : '' ?>>
                        <?php echo $motivo_detalhado->mdrdescricao ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
    <button type="button" id="bt_novo">Novo</button>
</div>







