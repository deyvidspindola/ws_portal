<div class="bloco_titulo">Serviço(s) não faturado(s)</div>

<? if ($multasServicos): ?>
    <div class="bloco_conteudo">  
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th>Contrato</th>
                        <th>Placa</th>
                        <th>Ocorrência</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                
                <tbody>
                <? $totalMulta = 0 ?>
                <? foreach ($multasServicos as $multa): ?>                    
                    <tr>
                        <td><?= $multa['connumero'] ?></td>
                        <td><?= $multa['veiplaca'] ?></td>
                        <td><?= $multa['coaocorrencia'] ?></td>
                        <td>
                            <input type="text" name="" id=""
                                size="5" class="multa-servico mask-money"
                                value="<?= toMoney($multa['coavl_ocorrencia']) ?>" />
                        </td>
                    </tr>
                    <? $totalMulta += $multa['coavl_ocorrencia'] ?>
                <? endforeach ?>
                </tbody>
                
                <tfoot>
                    <tr>
                        <td colspan="3"></td>
                        <td>
                            <input type="text" name="" id=""
                                readonly="readonly" size="5" 
                                class="multa-servico-total"
                                value="<?= toMoney($totalMulta) ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" align="center">
                            <?= count($multasServicos) ?> registros(s) encontrado(s)
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