<div class="bloco_titulo">Multas sobre o valor do monitoramento </div>

<?php if ($contratosMultasLocacao): ?>
    <div class="bloco_conteudo">  
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th>Contrato</th>
                        <th>% Multa</th>
                        <th>Meses restantes</th>
                        <th>Valor do contrato (Monitoramento)</th>
                        <th>Valor da multa</th>
                     </tr>
                </thead>

                <tbody>
                    <?php
                        foreach ($contratosMultasLocacao as $contrato):
                            
                            if (intval($contrato['mesesFaltantes']) == 0 && floatval($contrato['descontoProRataMonitoramento']) <= 0) {
                                continue;
                            }

                            $style = "";
                            if (intval($contrato['mesesFaltantes']) == 0 && floatval($contrato['descontoProRataMonitoramento']) > 0) {
                                $style = 'style="display:none"';
                            }
                            ?>
                        <tr <?= $style ?>>
                            <td>
                            <input type="hidden" value="<?= $contrato['connumero']; ?>" class="contrato-multa-monitoramento" />
                            <input type="hidden" value="<?= $contrato['percentualMulta'] ?>" class="multa-monitoramento-percentual-multa" />
                            <input type="hidden" value="<?= $contrato['mesesFaltantes'] ?>" class="multa-monitoramento-meses-faltantes" />
                            <input type="hidden" value="<?= toMoney($contrato['valor_monitoramento']) ?>" class="multa-monitoramento-valor-monitoramento"/>
                            <input type="hidden" value="<?= toMoney($contrato['totalMultaMonitoramento']) ?>" class="multa-monitoramento-total-multa-monitoramento" />
                            <input type="hidden" value="<?= toMoney($contrato['descontoProRataMonitoramento']) ?>" class="multa-monitoramento-desconto-pro-rata" />
                            <?= $contrato['connumero'] ?></td>
                            <td><?= $contrato['percentualMulta'] ?></td>
                            <td><?= $contrato['mesesFaltantes'] ?></td>
                            <td><?= toMoney($contrato['valor_monitoramento']) ?></td>
                            <td><?= toMoney($contrato['totalMultaMonitoramento']) ?></td>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?> 
    <div class="bloco_acoes">
        <p><strong>Nenhum registro encontrado.</strong></p>
    </div>
<?php endif ?> 