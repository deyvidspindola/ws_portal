<?php 
if (isset($editar) && $editar === true) : ?>
    <div class="bloco_titulo">Dados do item</div>
<?php else : ?>
    <div class="bloco_titulo">Dados do novo item</div>
<?php endif; ?>
<div class="bloco_conteudo">

    <div class="formulario">
        <div class="campo medio">
            <label for="item_connumero">Contrato *</label>
            <input readonly="readonly" type="text" id="item_connumero" name="item[connumero]" value="<?= ($_POST['connumero'] != "SC" ? $_POST['connumero'] : "") ?>" class="campo" <?= ($_POST['connumero'] == "SC" ? "disabled=\"disabled\"" : "") ?> />
        </div>
        <div class="clear"></div>

        <div class="campo pesquisaMaior">
            <label for="item_obrobrigacao"><?php echo "Obrigação Financeira *";?></label>
            <input type="hidden" id="item_nfiobroid" name="item[obroid]" value="<?php echo (isset($editar) && $editar === true) ? $itemEdicao['obroid'] : '' ?>" />
            <input readonly="readonly" id="item_obrobrigacao" name="item[obrobrigacao]" value="<?php echo (isset($editar) && $editar === true) ? $itemEdicao['obrobrigacao'] : '' ?>" class="campo" type="text">
            <button type="button" id="bt_pesquisar_obrfin">Pesquisar</button>
        </div>
        <div class="clear"></div>

        <fieldset class="maior" id="field_tipo">
            <legend>Tipo de Item *</legend>
            <input type="radio" value="L" name="item[nfitipo]" <?php echo (isset($editar) && $editar === true && $itemEdicao['nfitipo'] === 'L') ? 'checked=checked' : '' ?> id="opcao_1_1">
            <label for="opcao_1_1"><?php echo "Locação";?></label>
            <input type="radio" value="M" name="item[nfitipo]" <?php echo (isset($editar) && $editar === true && $itemEdicao['nfitipo'] === 'M') ? 'checked=checked' : '' ?> id="opcao_2_1">
            <label for="opcao_2_1"><?php echo "Monitoramento (Serviços)";?></label>
        </fieldset>
        <div class="clear"></div>

        <div class="campo menor">
            <label for="item_nfivl_item"><?php echo "Valor Unitário *";?></label>
            <input type="text" id="item_nfivl_item" name="item[nfivl_item]" value="<?php echo (isset($editar) && $editar === true) ?  $row_altnfi['nfivl_item']  : '0.00' ?>" class="campo_dinheiro"  onKeyup="jQuery.fn.formatarDinheiro();somaItem();" onBlur="jQuery.fn.formatarDinheiro();somaItem();jQuery.fn.atualizaCampo();" maxlength="10" />
             <input type="hidden" id="item_nfidesconto" name="item[nfidesconto]" value="<?php echo (isset($editar) && $editar === true) ? number_format($itemEdicao['nfidesconto'], 2, '.', '') : '0.00' ?>" class="campo_dinheiro"  onKeyup="jQuery.fn.formatarDinheiro();somaItem();" onBlur="jQuery.fn.formatarDinheiro();somaItem(); jQuery.fn.atualizaCampo();"  maxlength="10" />
        </div>
        <div class="clear"></div>
<!-- 
        <div class="campo menor">
            <label for="item_nfidesconto">Desconto</label>
            <input type="text" id="item_nfidesconto" name="item[nfidesconto]" value="<?php echo (isset($editar) && $editar === true) ? number_format($itemEdicao['nfidesconto'], 2, '.', '') : '0.00' ?>" class="campo_dinheiro"  onKeyup="jQuery.fn.formatarDinheiro();somaItem();" onBlur="jQuery.fn.formatarDinheiro();somaItem(); jQuery.fn.atualizaCampo();"  maxlength="10" />
        </div>
        <div class="clear"></div>
 -->
        <div class="campo menor">
            <label for="item_total">Valor Total</label>
            <input  type="text" readonly="readonly" disabled="disabled" id="item_total" name="item[total]" value="0.00" class="campo_dinheiro" onBlur="jQuery.fn.formatarDinheiro();"/>
        </div>
        <div class="clear"></div>
 		
        <?php if (!isset($editar) && isset($_POST['ids_notas']) && $_POST['ids_notas'] === 'null') : ?>
        <div class="campo menor">
            <label style="width: 170px;" for="qtd_replicacoes">Replicar esse item 'n' vezes *</label>
            <input type="text" id="qtd_replicacoes" name="qtd_replicacoes" value="1" maxlength="4" class="campo" />
        </div>
        <div class="clear"></div>
        <?php endif; ?>

    </div>
</div>		
<div class="bloco_acoes">
    <?php if (isset($editar) && $editar === true) : ?>
        <button type="button" id="bt_altera_item" rel="<?php echo (isset($editar) && $editar === true) ? $_POST['notaFiscalItem'] : '' ?>">OK, alterar este item</button>
    <?php else : ?>
        <button type="button" id="bt_inclui_item">OK, incluir este item</button>
    <?php endif; ?>
</div>	
<div class="separador"></div>	
<div id="load_Save" style="display: none;"></div>