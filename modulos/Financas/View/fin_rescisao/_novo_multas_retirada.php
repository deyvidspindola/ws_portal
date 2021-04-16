<?php
if ($isClienteSiggo):
?>
    <div class="bloco_titulo">Equipamentos</div>
<?php
else:
?>
    <div class="bloco_titulo">Taxa de retirada de equipamentos</div>
<?php
endif
?>

<?php 
    // STI 84189
    $retiradaEquipamentos = array();
    // FIM STI 84189
    
    if (count($retorno) > 0): 
        
        $taxas = 0;
        //Verifica se existe algum monitoramentos para os contratos
        
        $totaltaxas = 0;

        foreach($retorno as $contratoRetorno){
            $taxas += count($contratoRetorno->return->equipamentos);
        }
        
        if ($taxas > 0) :
    
?>
    <div class="bloco_conteudo">  
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <?php if (!$isClienteSiggo): ?>
                            <th width="10">Cobrar valor retirada</th>
                        <?php endif ?>
                        <th width="10">Cobrar valor multa</th>
                        <th>Contrato</th>
                        <th>Serviço</th>
                        <?php if (!$isClienteSiggo): ?>
                            <th>Valor</th>
                            <th>Valor Retirada</th>
                        <?php endif ?>
                        <th>Valor Multa por não Devolução</th>
                    </tr>
                </thead>
                
                <tbody>
                <?php
                    $i = 0;
                    foreach($retorno as $contratoRetorno):
                        
                        $contratoRescisao = $contratoRetorno->return;
                        
                        if (count($contratoRescisao->equipamentos) == 0){
                            continue;
                        }
                        
                        foreach ($contratoRescisao->equipamentos as $equipamento):
                            $totaltaxas++;

                            // STI 84189
                            $retiradaEquipamentos[$i]['contrato']       = $contratoRescisao->termo;
                            $retiradaEquipamentos[$i]['item']           = utf8_encode($equipamento->item);
                            $retiradaEquipamentos[$i]['valor']          = $equipamento->valor;
                            $retiradaEquipamentos[$i]['valorRetirada']  = $equipamento->valorRetirada;
                            $retiradaEquipamentos[$i]['obroidretirada'] = $equipamento->obroidretirada;
                            // FIM STI 84189
                        ?>
                        <tr>
                            <?php if (!$isClienteSiggo): ?>
                                <td>
                                    <input type="checkbox" id="" name=""
                                        checked="checked" class="multa-retirada"
                                        value="<?php echo $contratoRescisao->termo; ?>" />
                                    <input type="hidden" class="multa-retirada-valorRetirada" value="<?=$equipamento->valorRetirada?>"/>
                                    <input type="hidden" class="multa-retirada-termo" value="<?=$contratoRescisao->termo?>"/>
                                    <input type="hidden" class="multa-retirada-item" value="<?=$equipamento->item?>"/>
                                    <input type="hidden" class="multa-retirada-obroidretirada" value="<?=$equipamento->obroidretirada?>"/>
                                </td>
                            <?php  endif ?>
                            <td>
                                <input type="checkbox" id="" name=""
                                    class="multa-nao-retirada"
                                    value="<?php echo $contratoRescisao->termo; ?>" />
                                <input type="hidden" class="multa-nao-retirada-termo" value="<?=$contratoRescisao->termo?>"/>
                                <input type="hidden" class="multa-nao-retirada-item" value="<?=$equipamento->item?>"/>
                                <?php if (!$isClienteSiggo): ?>
                                    <input type="hidden" class="multa-nao-retirada-valorRetirada" value="<?=$equipamento->valorRetirada?>"/>
                                <?php endif ?>
                                <input type="hidden" class="multa-nao-retirada-obroidretirada" value="<?=$equipamento->obroidretirada?>"/>
                            </td>
                            <td><?php echo $contratoRescisao->termo; ?></td>
                            <td><?php echo $equipamento->item; ?></td>
                            <?php if (!$isClienteSiggo): ?>
                                <td><?php echo toMoney($equipamento->valor); ?></td>
                                <td>
                                    <input type="text" name="" id=""
                                        size="5" class="multa-retirada-valor mask-money"
                                        value="<?php echo toMoney($equipamento->valorRetirada); ?>" />
                                </td>
                            <?php  endif ?>
                            <td>
                                <input type="text" size="5" class="multa-nao-retirada-valor mask-money"
                                value="<?php echo toMoney(0.00); ?>" />
                            </td>
                        </tr>
                        <?php $i++;?>
                        <? endforeach ?>
                    <? endforeach ?>
                </tbody>
                
                <tfoot>
                    <tr>
                        <td colspan="7" align="center">
                            <?= $totaltaxas ?> registros(s) encontrado(s)
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<? else: ?> 
    <div class="bloco_acoes">
        <p><strong>Nenhum registro encontrado.</strong></p>
    </div>
<? endif ?> 
<? else: ?> 
    <div class="bloco_acoes">
        <p><strong>Nenhum registro encontrado.</strong></p>
    </div>
<? endif ?> 

<!-- STI 84189 -->
<?php $retiradaEquipamentos = json_encode($retiradaEquipamentos);?>
<script type="text/javascript">
    var retiradaEquipamentos = jQuery.parseJSON('<?=$retiradaEquipamentos?>');
</script>
<!-- FIM STI 84189 -->