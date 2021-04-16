<? if ($multasServicos): ?>
    <div class="bloco_titulo">Serviços Utilizados (Mês Atual)</div>
    <div class="bloco_conteudo">  
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th>Placa</th>
                        <th>Descrição</th>
                        <th>Quantidade</th>
                        <th>Valor</th>
                        <th>Total</th>
                    </tr>
                </thead>
                
                <tbody>
                <?
                    $totalValorServicos = 0;
                    $totalValorUso      = 0;
                ?>
                <? foreach ($multasServicos as $multa): ?>
                    <tr>
                        <td><?= $multa['veiplaca'] ?></td>
                        <td><?= $multa['risdescr'] ?></td>
                        <td align="right">
                            <?= $multa['risqtde'] ?> cada
                        </td>
                        <td align="right">
                            <?= toMoney($multa['risvalor']) ?> cada
                        </td>
                        <td align="right">
                            <?= toMoney($multa['risqtde'] * $multa['risvalor']) ?>
                        </td>
                    </tr>
                    
                    <?
                        $totalValorServicos += $multa['risvalor'];
                        $totalValorUso      += ($multa['risqtde'] * $multa['risvalor']);
                    ?>
                <? endforeach ?>
                </tbody>
            </table>
                
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                    <td align="right">
                        <strong>R$ <?= toMoney($totalValorServicos) ?></strong>
                    </td>
                    <td align="right">
                        <strong>R$ <?= toMoney($totalValorUso) ?></strong>
                    </td>
                </tr>
            </tfoot>
        </div>
    </div>
    <div class="bloco_acoes">
        <p><?= count($multasServicos) ?> serviço(s) encontrad(s)</p>
    </div>

    <div class="separador"></div>
<? endif ?>