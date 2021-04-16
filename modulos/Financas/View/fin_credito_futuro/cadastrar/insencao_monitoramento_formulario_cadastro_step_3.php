<div class="campo menor valor_parcela ">
    <label class="" for="valor_aplicacao">Qtde. Parcelas *</label>
    <input class="campo  campo_parcela" type="text" name="cadastro[cfoqtde_parcelas]" value="<?php echo isset($this->view->parametros->cadastro['cfoqtde_parcelas'])  ? $this->view->parametros->cadastro['cfoqtde_parcelas'] : '1' ?>" id="cfoqtde_parcelas"  /> 
</div>

<div class="campo medio"></div>	

<div class="campo maior">
    <label>
        Nome do Cliente
    </label>
    <input readonly class="campo" id="" type="text" value="<?php echo !empty($_SESSION['credito_futuro']['step_1']['razao_social']) ? $this->verificarTipoRequisicao(trim($_SESSION['credito_futuro']['step_1']['razao_social'])) : '' ?>" >
</div>

<div class="clear"></div>

<?php
$obrigacaofinanceiraDesconto = isset($this->view->parametros->cadastro['cfoobroid_desconto']) && trim($this->view->parametros->cadastro['cfoobroid_desconto']) != '' ? trim($this->view->parametros->cadastro['cfoobroid_desconto']) : $this->view->parametros->parametracaoCreditoFuturo->cfeaobroid_contas; 
?>
<div class="campo maior">
    <label for="status">Obrigação Financeira de Desconto *</label>
    <select id="cfoobroid_desconto" name="cadastro[cfoobroid_desconto]" >
        <option value="">SELECIONE</option>
        <?php if (isset($this->view->parametros->obrigacaoFinanceiraDesconto) && count($this->view->parametros->obrigacaoFinanceiraDesconto) > 0) : ?>
            <?php foreach ($this->view->parametros->obrigacaoFinanceiraDesconto as $item) : ?>
                <?php if ($obrigacaofinanceiraDesconto == $item->obroid) : ?>
                    <option selected="selected" value="<?php echo $item->obroid ?>"><?php echo $item->obrobrigacao ?></option>
                <?php else: ?>
                    <option value="<?php echo $item->obroid ?>"><?php echo $item->obrobrigacao ?></option>
                <?php endif; ?>                        
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>

<div class="campo maior">
    <label>
        Motivo do Crédito
    </label>
    <input readonly class="campo" id="" type="text" value="<?php echo trim($_SESSION['credito_futuro']['step_2']['motivo_descricao']); ?>" >
</div>

<div class="clear"></div>

<div class="campo maior">
    <label for="cfoobservacao">
        Observação
    </label>
    <textarea name="cadastro[cfoobservacao]" maxlength="500" rows="5"><?php echo isset($this->view->parametros->cadastro['cfoobservacao']) ? $this->view->parametros->cadastro['cfoobservacao'] : '' ?></textarea>
</div>