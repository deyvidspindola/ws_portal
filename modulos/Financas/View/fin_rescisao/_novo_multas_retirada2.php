<div class="bloco_titulo">Taxa de retirada(s) de equipamento(s)</div>

<?php if ($contratosMultasLocacao && $contratosMultasLocacao['TRC_faturado'] !== true):?>

<div class="bloco_conteudo">  
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th width="10">Cobrar valor retirada</th>
                    <th width="10">Cobrar valor multa</th>
                    <th>Contrato</th>
                    <th>Serviço</th>
                    <th>Valor</th>
                    <th>Valor Retirada</th>
                    <th>Valor Multa por não Devolução</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($contratosMultasLocacao as $contrato):
                    if(!$contrato['TRC_faturado']):
                ?>
                <tr>
                    <td>
                        <input type="checkbox" id="" name="" checked="checked" class="multa-retirada" value="<?php echo $contrato['connumero'] ?>" />
                        <input type="hidden" class="multa-retirada-valorRetirada" value="<?= $contrato['taxa_retirada'] ?>"/>
                        <input type="hidden" class="multa-retirada-termo" value="<?= $contrato['connumero'] ?>"/>
                        <input type="hidden" class="multa-retirada-item" value="<?= $contrato['eqcdescricao'] ?>"/>
                        <input type="hidden" class="multa-retirada-obroidretirada" value="<?= $contrato['obrobroid_retirada'] ?>"/>
                        <input type="hidden" class="multa-retirada-eqcobroid" value="<?= $contrato['eqcobroid'] ?>"/>
                    </td>

                    <td>
                        <input type="checkbox" id="" name="" class="multa-nao-retirada" value="<?php echo $contrato['connumero']; ?>" />
                        <input type="hidden" class="multa-nao-retirada-termo" value="<?= $contrato['connumero'] ?>"/>
                        <input type="hidden" class="multa-nao-retirada-item" value="<?= $contrato['eqcdescricao'] ?>"/>
                        <input type="hidden" class="multa-nao-retirada-valorRetirada" value="<?= '0' ?>"/>
                        <input type="hidden" class="multa-nao-retirada-obroidretirada" value="<?= $contrato['obrobroid_retirada'] ?>"/>
                        <input type="hidden" class="multa-retirada-eqcobroid" value="<?= $contrato['eqcobroid'] ?>"/>
                    </td>

                    <td><?php echo $contrato['connumero'] ?></td>

                    <td><?php echo $contrato['eqcdescricao'] ?></td>

                    <td><?php echo toMoney($contrato['taxa_retirada']); ?></td>
                    <td>
                        <input type="text" name="" id="" size="5" class="multa-retirada-valor mask-money"  value="<?php echo toMoney($contrato['taxa_retirada']); ?>" />
                    </td>

                    <td>
                        <input type="text" size="5" class="multa-nao-retirada-valor mask-money" value="<?php echo toMoney(0.00); ?>" />
                    </td>
                </tr>

                <?php endif; endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?> 
<div class="bloco_acoes">
    <p><strong>Nenhum registro encontrado.</strong></p>
</div>
<?php endif ?> 

