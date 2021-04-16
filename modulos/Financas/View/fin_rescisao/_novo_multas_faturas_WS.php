<div class="bloco_titulo">Faturas do(s) contrato(s) selecionado(s)</div>

<?php 
    if (count($retorno) > 0): 
        
        $monitoramentos = 0;
        //Verifica se existe algum monitoramentos para os contratos
        foreach($retorno as $contratoRetorno){
            $monitoramentos += count($contratoRetorno->return->cobrancaMonitoramento);
        }

        $totalMonitoramento = 0;

        //Totais da multa de monitoramento
        $totaisMonitoramento = array();
        
        if ($monitoramentos > 0) :
    
    
?>
    <div class="bloco_conteudo">  
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" class="selecionar-todos-multa-fatura" checked="checked"/> Cobrar</th>
                        <th>Dias atraso</th>
                        <th>Documento</th>
                        <th>Vencimento</th>
                        <th>Valor Total</th>
                        <th>Valor Deste Termo</th>
                        <th>Desconto rescisão</th>
                        <th>Situação</th>
                        <!--th>Observação</th-->
                    </tr>
                </thead>
                
                <tbody>
                <?php
                    
                    foreach($retorno as $contratoRetorno):
                        
                        $contratoRescisao = $contratoRetorno->return;
                        


                        if (count($contratoRescisao->cobrancaMonitoramento) == 0){
                            continue;
                        }

                        $totaisMonitoramento[$contratoRescisao->termo] = $contratoRescisao->valorMultaMensalidade;
                        

                        foreach ($contratoRescisao->cobrancaMonitoramento as $cobrancaMonitoramento):
                            $totalMonitoramento++;
                    ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="multa_fatura_cobravel"
                                class="multa-fatura" checked="checked"
                                value="<?php echo $cobrancaMonitoramento->titulo;?>" />
                        </td>
                        <td><?php echo $cobrancaMonitoramento->diasAtraso;?></td>
                        <td class="multaFaturaDocto"><?php echo $cobrancaMonitoramento->numeroNotaMonitoramento;?>/<?php echo $cobrancaMonitoramento->siglaCobrancaMonitoramento;?></td>
                        <td class="multaFaturaVecto"><?php echo  $cobrancaMonitoramento->dataVencimentoMonitoramento; ?></td>
                        <td>
                            <input type="text" name="" id=""
                                readonly="readonly" size="5" class="multa-fatura-valor-total"
                                value="<?php echo toMoney($cobrancaMonitoramento->valorTotalMonitoramento); ?>" />
                        </td>
                        <td>
                            <input type="text" name="" id=""
                                readonly="readonly" size="5" class="multa-fatura-valor <?php echo $contratoRescisao->termo; ?>"
                                value="<?php echo toMoney($cobrancaMonitoramento->valorRealMonitoramento); ?>" />
                        </td>
                        <td>
                            <input type="text"
                                size="5" class="multa-fatura-desconto mask-money <?php echo $contratoRescisao->termo; ?>"
                                value="<?php echo toMoney($cobrancaMonitoramento->valorPagoIndevidoMonitoramento); ?>" />
                                
                            <!-- Campo hidden para guardar valor original do desconto -->
                            <input type="hidden" class="multa-fatura-desconto-padrao "
                                value="<?php echo toMoney($cobrancaMonitoramento->valorPagoIndevidoMonitoramento); ?>" />
                        </td>
                        <td><?php echo $cobrancaMonitoramento->statusMonitoramento  ?></td>
                        <!--td>
                            <input type="checkbox" class="multa-faturas-observacao" />
                            <input type="text" class="multa-faturas-locacao-observacao-text"
                                name="multa_faturas_observacao[]" size="40"
                                value="" />
                        </td-->
                    </tr>
                <? endforeach ?>
                <? endforeach ?>
                </tbody>
                
                <tfoot>
                    <tr>
                        <!--td>
                            <input type="checkbox" class="selecionar-todos-multa-fatura"
                                checked="checked" />
                        </td-->
                        <!--td colspan="8"></td-->
                        <!--td>
                            <input type="checkbox" class="selecionar-todos-multa-faturas-observacao" />
                            Alterar obs.:
                            <select class="change-multa-faturas-observacao">
                                <option>Nota baixada pela Sascar</option>
                                <option>A vencer</option>
                                <option>Vencida</option>
                                <option>Baixado</option>
                            </select>
                        </td-->
                    </tr>
                    <tr>
                        <td colspan="9" align="center">
                            <?= $totalMonitoramento ?> registros(s) encontrado(s)
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php
        if (count($totaisMonitoramento) > 0) {
            foreach($totaisMonitoramento as $connumero => $valor) : ?>
            <input type="hidden" value="<?php echo toMoney($valor); ?>" id="contrato-multa-total-<?php echo $connumero;?>" />
        <?php endforeach;?>
        <?php } ?>                   
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