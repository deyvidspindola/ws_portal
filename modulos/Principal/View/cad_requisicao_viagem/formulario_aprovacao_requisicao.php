<div class="separador"></div>

<div class="bloco_titulo">Aprovação</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo menor">
            <label for="valorAprovacaoRequisicao">Valor Liberado *</label>
            <input style="text-align: right;" id="valorAprovacaoRequisicao" maxlength="7" class="campo moeda" type="text" value="<?php echo isset($this->view->parametrosAprovacao->valorAprovacaoRequisicao) ? number_format(floatval($this->view->parametrosAprovacao->valorAprovacaoRequisicao), 2, ',', '.') : '0,00' ?>" name="valorAprovacaoRequisicao">
        </div>
        <div class="clear"></div>

        <div class="campo medio">
            <label for="observacoesAprovacaoRequisicao">Observações *</label>
            <textarea id="observacoesAprovacaoRequisicao" name="observacoesAprovacaoRequisicao" rows="3"><?php echo isset($this->view->parametrosAprovacao->observacoesAprovacaoRequisicao) ? $this->view->parametrosAprovacao->observacoesAprovacaoRequisicao : '' ?></textarea>
        </div>
        <div class="clear"></div>

        <div class="campo medio">
            <label for="statusAprovacaoRequisicao">Status Aprovação *</label>
            <select id="statusAprovacaoRequisicao" name="statusAprovacaoRequisicao">
                <option value="">Escolha</option>
                <option value="A" <?php echo (isset($this->view->parametrosAprovacao->statusAprovacaoRequisicao) && $this->view->parametrosAprovacao->statusAprovacaoRequisicao == 'A') ? 'selected="selected"' : '' ?>>Aprovado</option>
                <option value="R" <?php echo (isset($this->view->parametrosAprovacao->statusAprovacaoRequisicao) && $this->view->parametrosAprovacao->statusAprovacaoRequisicao == 'R') ? 'selected="selected"' : '' ?>>Reprovado</option>
            </select>
        </div>
        <div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="button" id="bt_confirmarAprovacaoRequisicao" name="bt_confirmarAprovacaoRequisicao">Confirmar</button>
</div>