<? if ($faturas): ?>
    <div class="bloco_titulo">Faturas</div>
    <div class="bloco_conteudo">  
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th>Fatura</th>
                        <th>Valor</th>
                        <th>Desconto</th>
                        <th>Vencimento</th>
                        <th>Observação</th>
                    </tr>
                </thead>
                
                <tbody>
                <?
                    $totalFaturas         = 0;
                    $totalDescontoFaturas = 0;
                ?>
                <? foreach ($faturas as $fatura): ?>
                    <tr>
                        <td><?= ($fatura['nflno_numero'] . '/' . $fatura['nflserie']) ?></td>
                        <td align="right"><?= toMoney($fatura['titvl_titulo'] + $fatura['titvl_desc_rescisao']) ?></td>
                        <td align="right"><?= toMoney($fatura['titvl_desc_rescisao']) ?></td>
                        <td><?= date('d/m/Y', strtotime($fatura['titdt_vencimento'])) ?></td>
                        <td><?= $fatura['resciobservacao'] ?></td>
                    </tr>
                    
                    <?
                        $totalFaturas         += $fatura['titvl_titulo'] + $fatura['titvl_desc_rescisao'];
                        $totalDescontoFaturas += $fatura['titvl_desc_rescisao'];               
                    ?>
                <? endforeach ?>
                </tbody>
                
                <tfoot>
                    <tr>
                        <td></td>
                        <td align="right"><strong>R$ <?= toMoney($totalFaturas) ?></strong></td>
                        <td align="right"><strong>R$ <?= toMoney($totalDescontoFaturas) ?></strong></td>
                        <td><strong>Total faturas: R$ <?= toMoney($totalFaturas - $totalDescontoFaturas) ?></strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="bloco_acoes">
        <p><?= count($faturas) ?> fatura(s) encontrada(s)</p>
    </div>
    
    <div class="separador"></div>
<? endif ?>