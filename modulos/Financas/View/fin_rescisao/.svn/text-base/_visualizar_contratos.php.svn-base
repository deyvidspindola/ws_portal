<? if ($contratos): ?>
    <div class="bloco_titulo">Contratos</div>
    <div class="bloco_conteudo">  
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th>Contrato</th>
                        <th>Placa</th>
                        <th>Meses Falt. Monitoramento</th>
                        <th>% Multa Monitoramento</th>
                        <th>Multa sobre valor monit.</th>
                        <th>Multa sobre valor locação</th>
                        <th>Total multas</th>
                    </tr>
                </thead>
                
                <tbody>
                <? $totalMultaRescisao = 0 ?>
                <? foreach ($contratos as $contrato): ?>                    
                    <?
                        $totalContrato       = $contrato['rescvl_locacao'] + $contrato['rescvl_monitoramento'];
                        $totalMultaRescisao += $totalContrato;
                    ?>
                    <tr>
                        <td><?= $contrato['connumero'] ?></td>
                        <td><?= $contrato['veiplaca'] ?></td>
                        <td align="right"><?= $contrato['rescmeses'] ?></td>
                        <td align="right"><?= toMoney($contrato['rescperc_multa']) ?></td>
                        <td align="right"><?= toMoney($contrato['rescvl_locacao']) ?></td>
                        <td align="right"><?= toMoney($contrato['rescvl_monitoramento']) ?></td>
                        <td align="right"><?= toMoney($totalContrato) ?></td>
                    </tr>
                <? endforeach ?>
                </tbody>
                
                <tfoot>
                    <tr>
                        <td colspan="6"></td>
                        <td align="right">
                            <strong>R$ <?= toMoney($totalMultaRescisao) ?></strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <div class="separador"></div>
<? endif ?>