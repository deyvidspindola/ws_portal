<div class="bloco_titulo">Faturas do(s) contrato(s) selecionado(s)</div>

<? if ($multasFaturas): ?>
    <div class="bloco_conteudo">  
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th>Cobrar</th>
                        <th>Dias atraso</th>
                        <th>Docto</th>
                        <th>Fatura</th>
                        <th>Vcto</th>
                        <th>Valor</th>
                        <th>Desconto rescisão</th>
                        <th>Terceirizada</th>
                        <th>Observação</th>
                    </tr>
                </thead>
                
                <tbody>
                <? foreach ($multasFaturas as $multa): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="multa_fatura_cobravel"
                                class="multa-fatura" checked="checked"
                                value="<?= $multa['titoid'] ?>" />
                        </td>
                        <td><?= $multa['atraso']?></td>
                        <td><?= $multa['nota'] ?></td>
                        <td><?= ($multa['fatura'] . '/' . $multa['finalidade']) ?></td>
                        <td><?= $multa['titdt_vencimento'] ?></td>
                        <td>
                            <input type="text" name="" id=""
                                readonly="readonly" size="5" class="multa-fatura-valor"
                                value="<?= toMoney($multa['titvl_titulo']) ?>" />
                        </td>
                        <td>
                            <input type="text"
                                size="5" class="multa-fatura-desconto mask-money"
                                value="<?= toMoney($multa['desconto_automatico']) ?>" />
                                
                            <!-- Campo hidden para guardar valor original do desconto -->
                            <input type="hidden" class="multa-fatura-desconto-padrao"
                                value="<?= toMoney($multa['desconto_automatico']) ?>" />
                        </td>
                        <td><?= (isset($multa['terceirizada']) ? 'Sim' : 'Não') ?></td>
                        <td>
                            <input type="checkbox" class="multa-faturas-observacao" />
                            <input type="text" class="multa-faturas-locacao-observacao-text"
                                name="multa_faturas_observacao[]" size="40"
                                value="<?= $multa['observacao'] ?>" />
                        </td>
                    </tr>
                <? endforeach ?>
                </tbody>
                
                <tfoot>
                    <tr>
                        <td>
                            <input type="checkbox" class="selecionar-todos-multa-fatura"
                                checked="checked" />
                        </td>
                        <td colspan="7"></td>
                        <td>
                            <input type="checkbox" class="selecionar-todos-multa-faturas-observacao" />
                            Alterar obs.:
                            <select class="change-multa-faturas-observacao">
                                <option>Nota baixada pela Sascar</option>
                                <option>A vencer</option>
                                <option>Vencida</option>
                                <option>Baixado</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="9" align="center">
                            <?= count($multasFaturas) ?> registros(s) encontrado(s)
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