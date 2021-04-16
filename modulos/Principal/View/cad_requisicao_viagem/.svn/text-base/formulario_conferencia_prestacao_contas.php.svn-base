<div class="separador"></div>

<div class="bloco_titulo">Conferência de Prestação de Contas</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo medio">
            <label for="statusConferencia">Status da Conferência *</label>
            <select id="statusConferencia" name="statusConferencia">
                <option value="">Escolha</option>
                <option value="A" <?php echo (isset($this->view->parametrosConferencia->statusConferencia) && $this->view->parametrosConferencia->statusConferencia == 'A' ? 'selected="selected"' : '') ?>>Aprovado</option>
                <option value="R" <?php echo (isset($this->view->parametrosConferencia->statusConferencia) && $this->view->parametrosConferencia->statusConferencia == 'R' ? 'selected="selected"' : '') ?>>Reprovado</option>
            </select>
        </div>
        <div class="clear"></div>

        <div class="campo data" style="white-space: nowrap;">
            <label for="dataChegadaRelatorio">Data da chegada do relatório *</label>
            <input id="dataChegadaRelatorio" class="campo" type="text" value="<?php echo (isset($this->view->parametrosConferencia->dataChegadaRelatorio) ? $this->view->parametrosConferencia->dataChegadaRelatorio : '') ?>" name="dataChegadaRelatorio">
        </div>
        <div class="clear"></div>

        <?php if($this->view->parametros->dadosDespesa->valorDespesas < $this->view->parametros->valorAdiantamento) : ?>
        <div class="campo menor" style="white-space: nowrap;">
            <label for="valorDevolucao">Valor de Devolução *</label>
            <input style="text-align: right;" id="valorDevolucao" maxlength="7" class="campo moeda" type="text" value="<?php echo (isset($this->view->parametrosConferencia->valorDevolucao) ? number_format($this->view->parametrosConferencia->valorDevolucao, 2, ',', '') : number_format(floatval($this->view->parametros->valorAdiantamento - $this->view->parametros->dadosDespesa->valorDespesas), 2, ',', '') ) ?>" name="valorDevolucao">
        </div>
        <div class="clear"></div>
        <?php endif; ?>

        <?php if($this->view->parametros->dadosDespesa->valorDespesas > $this->view->parametros->valorAdiantamento) : ?>
        <div class="campo menor" style="white-space: nowrap;">
            <label for="valorReembolso">Valor de Reembolso *</label>
            <input style="text-align: right;" id="valorReembolso" maxlength="7" class="campo moeda" type="text" value="<?php echo (isset($this->view->parametrosConferencia->valorReembolso) ? number_format($this->view->parametrosConferencia->valorReembolso, 2, ',', '') : number_format(floatval($this->view->parametros->dadosDespesa->valorDespesas - $this->view->parametros->valorAdiantamento), 2, ',', '') ) ?>" name="valorReembolso">
        </div>
        <div class="clear"></div>
        <?php endif; ?>

        <div class="campo medio">
            <label for="justificativaConferencia">Justificativa *</label>
            <textarea id="justificativaConferencia" name="justificativaConferencia" rows="3"><?php echo (isset($this->view->parametrosConferencia->justificativaConferencia) ? $this->view->parametrosConferencia->justificativaConferencia : '') ?></textarea>
        </div>
        <div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="button" id="bt_confirmarConferencia" name="bt_confirmarConferencia">Confirmar</button>
</div>