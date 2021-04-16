<div class="campo menor ">
    <label for="valor_tipo_desconto">Valor (R$) *</label>
    <input class="campo moeda" maxlength="12" type="text" name="cadastro[cfovalor]"  id="valor_tipo_desconto" value="<?php echo isset($this->view->parametros->cadastro['cfovalor']) ? $this->view->parametros->cadastro['cfovalor'] : trim($_SESSION['credito_futuro']['step_2']['valor']) ?>" style="text-align: right" /> 
</div>

<div class="campo medio"></div>	

<div class="clear"></div>

<?php
 $integral_checado = isset($this->view->parametros->cadastro['cfoforma_aplicacao']) && $this->view->parametros->cadastro['cfoforma_aplicacao'] == '1' ? 'checked="checked"' : (isset($this->view->parametros->cadastro['cfoforma_aplicacao']) || $this->view->parametros->cadastro['cfoforma_aplicacao'] != '1'  ? 'checked="checked"' : '');
 $parcelas_checado = isset($this->view->parametros->cadastro['cfoforma_aplicacao']) && $this->view->parametros->cadastro['cfoforma_aplicacao'] == '2' ? 'checked="checked"' : '';
?>
<fieldset class="medio">
    <legend>Aplicação *</legend>
    <input type="radio" class="cfoforma_aplicacao naoValidar" id="cfoforma_aplicacao_1" name="cadastro[cfoforma_aplicacao]" value="1" <?php echo $integral_checado ?> />
    <label for="cfoforma_aplicacao_1">Integral</label>
    <input type="radio"class="cfoforma_aplicacao naoValidar" id="cfoforma_aplicacao_2" name="cadastro[cfoforma_aplicacao]" value="2" <?php echo $parcelas_checado ?> />
    <label for="cfoforma_aplicacao_2">Parcelas</label>
</fieldset>

<?php
$parcela_invisivel = isset($this->view->parametros->cadastro['cfoforma_aplicacao']) && $this->view->parametros->cadastro['cfoforma_aplicacao'] == '2' ? '' : 'invisivel';
?>
<div class="campo menor valor_parcela <?php echo $parcela_invisivel ?>">
    <label class="<?php echo $parcela_invisivel ?>" for="valor_aplicacao">Qtde. Parcelas *</label>
    <input class="campo <?php echo $parcela_invisivel ?> campo_parcela" type="text" name="cadastro[cfoqtde_parcelas]" value="<?php echo isset($this->view->parametros->cadastro['cfoqtde_parcelas'])  ? $this->view->parametros->cadastro['cfoqtde_parcelas'] : '1' ?>" id="cfoqtde_parcelas"  /> 
</div>

<div class="clear"></div>

<?php
 $monitoramento_checado = isset($this->view->parametros->cadastro['cfoaplicar_desconto']) && $this->view->parametros->cadastro['cfoaplicar_desconto'] == '1' ? 'checked="checked"' : (isset($this->view->parametros->cadastro['cfoaplicar_desconto']) || $this->view->parametros->cadastro['cfoaplicar_desconto'] != '1'  ? 'checked="checked"' : '');
 $locacao_checado = isset($this->view->parametros->cadastro['cfoaplicar_desconto']) && $this->view->parametros->cadastro['cfoaplicar_desconto'] == '2' ? 'checked="checked"' : '';
?>
<fieldset class="maior invisivel">
    <legend>Aplicar o desconto sobre o valor total de *</legend>
    <input type="radio" id="cfoaplicar_desconto_1" class="naoValidar" name="cadastro[cfoaplicar_desconto]" value="1" <?php echo $monitoramento_checado ?> />
    <label for="cfoaplicar_desconto_1">Monitoramento</label>
    <input type="radio" id="cfoaplicar_desconto_2" class="naoValidar" name="cadastro[cfoaplicar_desconto]" value="2" <?php echo $locacao_checado ?> />
    <label for="cfoaplicar_desconto_2">Locação</label>
</fieldset>

<div class="clear"></div>

<?php
$obrigacaofinanceiraDesconto = isset($this->view->parametros->cadastro['cfoobroid_desconto']) ? trim($this->view->parametros->cadastro['cfoobroid_desconto']) : $this->view->parametros->parametracaoCreditoFuturo->cfeaobroid_contestacao; 
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

<div class="clear"></div>

<div class="campo maior">
    <label for="cfoobservacao">
        Observação
    </label>
    <textarea name="cadastro[cfoobservacao]" maxlength="500" rows="5"><?php echo isset($this->view->parametros->cadastro['cfoobservacao']) ? $this->view->parametros->cadastro['cfoobservacao'] : '' ?></textarea>
</div>