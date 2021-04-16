<? if ($taxasRetirada): ?>
    <div class="bloco_titulo">Taxa de Retirada de Equipamentos</div>
    <div class="bloco_conteudo">  
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th>Serviço</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                
                <tbody>
                <?
                    $totalTaxas = 0;
                ?>
                <? foreach ($taxasRetirada as $taxa): ?>
                    <tr>
                        <td><?= (strlen($taxa['obrobrigacao']))
                                  ? $taxa['obrobrigacao']
                                  : "Taxa de retirada contrato {$taxa['rescrconoid']}" ?>
                        </td>
                        <td align="right">
                            <?= toMoney($taxa['rescrvl_retirada']) ?>
                        </td>
                    </tr>
                    
                    <?
                        $totalTaxas += $taxa['rescrvl_retirada'];
                    ?>
                <? endforeach ?>
                </tbody>
                
                <tfoot>
                    <tr>
                        <td></td>
                        <td align="right"><strong>R$ <?= toMoney($totalTaxas) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="bloco_acoes">
        <p><?= count($taxasRetirada) ?> taxa(s) encontrada(s)</p>
    </div>

    <div class="separador"></div>
<? endif ?>