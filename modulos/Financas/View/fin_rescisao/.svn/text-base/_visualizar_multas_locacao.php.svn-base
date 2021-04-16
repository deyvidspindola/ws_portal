<? if ($multasLocacao): ?>
    <div class="bloco_titulo">Locações/Mensalidades vencidas até o prazo final do contrato</div>
    <div class="bloco_conteudo">  
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th>NF</th>
                        <th>Vencimento</th>
                        <th>Valor</th>
                        <th>Desconto</th>
                        <th>Observação</th>
                    </tr>
                </thead>
                
                <tbody>
                <? foreach ($multasLocacao as $multa): ?>
                    <tr>
                        <td><?= ($multa['nflno_numero'] . '/' . $multa['nflserie']) ?></td>
                        <td><?= date('d/m/Y', strtotime($multa['vencimento_inicial'])) ?> 
                            até <?= date('d/m/Y', strtotime($multa['vencimento_final'])) ?></td>
                        <td align="right">
                            <?= toMoney($multa['titvl_titulo']) ?> cada
                        </td>
                        <td align="right">
                            <?= toMoney($multa['titvl_desc_rescisao']) ?> cada
                        </td>
                        <td><?= $multa['resciobservacao'] ?></td>
                    </tr>
                <? endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="bloco_acoes">
        <p><?= count($multasLocacao) ?> registro(s) encontrado(s)</p>
    </div>
    
    <div class="separador"></div>
<? endif ?>