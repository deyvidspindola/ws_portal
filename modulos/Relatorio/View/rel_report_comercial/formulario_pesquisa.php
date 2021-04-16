<?php
if ($this->view->status) {
    $rpcdt_referencia = '';
    $rpcdrczoid       = array();
    $rpcclioid        = '';
    $rpcclinome       = '';
} else {
    $rpcdt_referencia = isset($this->param->rpcdt_referencia) ? $this->param->rpcdt_referencia : '';
    $rpcdrczoid       = isset($this->param->rpcdrczoid) && is_array($this->param->rpcdrczoid) ? $this->param->rpcdrczoid : array();
    $rpcclioid        = isset($this->param->rpcclioid) ? $this->param->rpcclioid : '';
    $rpcclinome       = isset($this->param->rpcclinome) ? $this->param->rpcclinome : '';

    if (!$rpcclioid) {
        $rpcclinome = '';
    }
}
?>
<form id="form_formulario_pesquisa" method="post">
    <input id="acao" type="hidden" name="acao" value="salvarReportComercial" />
    <input id="rpcclioid" type="hidden" name="rpcclioid" value="<?php echo $rpcclioid; ?>" />
    <div class="bloco_titulo">Dados para Pesquisa</div>
    <div class="bloco_conteudo">
        <div class="formulario">
            <div class="campo mes_ano">
                <label id="lbl_rpcdt_referencia" for="rpcdt_referencia">Período de Referência *</label>
                <input id="rpcdt_referencia" type="text" maxlength="10" name="rpcdt_referencia" value="<?php echo $rpcdt_referencia; ?>" class="campo" />
            </div>
            <div class="clear"></div>
            <div class="campo medio">
                <label id="lbl_rpcdrczoid" for="rpcdrczoid">DMV *</label>
                <select id="rpcdrczoid" multiple="multiple" name="rpcdrczoid[]">
                    <option value="">Marcar todos</option>
                    <?php if (isset($this->view->dados->regiaoComercialZona) && is_array($this->view->dados->regiaoComercialZona)) : ?>
                        <?php foreach ($this->view->dados->regiaoComercialZona as $registro) : ?>
                            <option value="<?php echo $registro->rczoid; ?>"
                                <?php echo in_array($registro->rczoid, $rpcdrczoid) ? 'selected="selected"' : ''; ?>
                            ><?php echo $registro->rczcd_zona; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="clear"></div>
            <div class="campo medio">
                <label id="lbl_rpcclinome" for="rpcclinome">Cliente</label>
                <input id="rpcclinome" type="text" name="rpcclinome" value="<?php echo $rpcclinome; ?>" class="campo" />
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="bloco_acoes">
        <button id="bt_pesquisar" type="submit">Pesquisar</button>
    </div>
</form>