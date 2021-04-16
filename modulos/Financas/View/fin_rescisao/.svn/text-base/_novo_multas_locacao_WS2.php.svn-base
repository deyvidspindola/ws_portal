<?php
ini_set('display_errors', 0);
/* **************************************************
 * IMPORTANTE:
 *
 * Reordena todos os registros para facilitar a apre-
 * sentação agrupada por nota fiscal / contrato.
 * ************************************************** */
$contador = new stdClass();
$contador->contratos = 0;
$contador->notas     = 0;
$contador->titulos   = 0;

$quantidade = new stdClass();
$quantidade->total = 0;
$quantidade->contratos = array();
$quantidade->notas     = array();

$total = new stdClass();
$total->porNota = array();
$total->geral->valorContrato = 0;
$total->geral->valorMulta    = 0;
$total->geral->valorTitulo   = 0;

$dados     = array();
$contratos = array();
$notas     = array();
$titulos   = array();

if (is_array($retorno)) {
    
    // STI 84189
    $i=0;
    // FIM STI 84189

    foreach ($retorno as $contrato) {
        $contrato = $contrato->return;

        // STI 84189
        $arrContratos[$i]['termo'] = $contrato->termo;
        $arrContratos[$i]['totalMensalidadeEquipamento'] = $contrato->totalMensalidadeEquipamento;
        $arrContratos[$i]['valorMultaMensalidade'] = $contrato->valorMultaMensalidade;
        $arrContratos[$i]['valorMultaMensalidadeFaltante'] = $contrato->valorMultaMensalidadeFaltante;
        $arrContratos[$i]['totalMensalidadeIndevido'] = $contrato->totalMensalidadeIndevido;
        $arrContratos[$i]['valorPagoIndevidoMonitoramentoTotal'] = $contrato->valorPagoIndevidoMonitoramentoTotal;
        $arrContratos[$i]['totalDiferencaIndevido'] = $contrato->totalDiferencaIndevido;
        $arrContratos[$i]['dataRescisao'] = $contrato->dataRecisao;
        $arrContratos[$i]['mesesRestantes'] = (int)$contrato->mesesRestantes;
        $arrContratos[$i]['percentualMulta'] = $contrato->percentualMulta;
        $i++;
        // FIM STI 84189

        if (is_array($contrato->cobrancaEquipamentos)) {
            foreach ($contrato->cobrancaEquipamentos as $equipamento) {
                $notaFiscal = $equipamento->numeroNotaEquipamento.'/'.$equipamento->siglaNotaEquipamento;
                $posicao    = new stdClass();

                if (!in_array($notaFiscal, $notas)) {
                    $dados[$contador->notas]->numero    = $notaFiscal;
                    $dados[$contador->notas]->contratos = array();

                    $contratos[$contador->notas] = array();
                    $notas[$contador->notas]     = $notaFiscal;

                    $contador->contratos = 0;
                    $contador->titulos   = 0;
                    $contador->notas++;

                    $quantidade->notas[$notaFiscal] = 0;

                    $total->porNota[$notaFiscal]->valorContrato = 0;
                    $total->porNota[$notaFiscal]->valorMulta    = 0;
                    $total->porNota[$notaFiscal]->valorTitulo   = 0;
                }

                $posicao->nota = array_search($notaFiscal, $notas);

                if (!in_array($contrato->termo, $contratos[$posicao->nota])) {
                    $temp = new stdClass();
                    $temp->termo           = $contrato->termo;
                    $temp->dataRecisao     = $contrato->dataRecisao;
                    $temp->percentualMulta = $contrato->percentualMulta;

                    $dados[$posicao->nota]->contratos[$contador->contratos] = $temp;
                    $dados[$posicao->nota]->contratos[$contador->contratos]->titulos = array();

                    $contratos[$posicao->nota][$contador->contratos] = $contrato->termo;
                    $titulos[$posicao->nota][$contador->contratos]   = array();

                    $contador->contratos++;
                    $contador->titulos = 0;

                    $quantidade->contratos[$contrato->termo] = 0;

                    unset($temp);
                }

                $posicao->contrato = array_search($contrato->termo, $contratos[$posicao->nota]);

                if (!in_array($equipamento->titulo, $titulos[$posicao->nota][$posicao->contrato])) {
                    $valorMulta = round(((floatval($contrato->percentualMulta) * $equipamento->valorUnitarioEquipamento) / 100), 2);

                    $temp = new stdClass();
                    $temp->codigo           = $equipamento->titulo;
                    $temp->dataPagamento    = $equipamento->dataPagamento;
                    $temp->dataVencimento   = $equipamento->dataVencimentoEquipamento;
                    $temp->diasAtraso       = $equipamento->diasAtraso;
                    $temp->pagamentoExtorno = $equipamento->pagamentoExtornoEquipamento;
                    $temp->status           = $equipamento->statusEquipamento;
                    $temp->valorTotal       = $equipamento->valorTotalEquipamento;
                    $temp->valorUnitario    = $equipamento->valorUnitarioEquipamento;
                    $temp->valorMulta       = $valorMulta;
                    $temp->dataRescisao     = $contrato->dataRecisao;
                    $temp->connumero        = $contrato->termo;

                    $dados[$posicao->nota]->contratos[$posicao->contrato]->titulos[$contador->titulos] = $temp;

                    $titulos[$posicao->nota][$posicao->contrato][$contador->titulos] = $equipamento->titulo;

                    $contador->titulos++;

                    $quantidade->total++;
                    $quantidade->contratos[$contrato->termo]++;
                    $quantidade->notas[$notaFiscal]++;

                    $total->geral->valorContrato += $equipamento->valorUnitarioEquipamento;
                    $total->geral->valorMulta    += $valorMulta;
                    $total->geral->valorTitulo   += $equipamento->valorTotalEquipamento;
                    $total->porNota[$notaFiscal]->valorContrato = $contrato->totalizadorEquipamentoParcela;
                    $total->porNota[$notaFiscal]->valorMulta    += $valorMulta;
                    $total->porNota[$notaFiscal]->valorTitulo   += $equipamento->valorTotalEquipamento;

                    unset($temp);
                }
            }
        }

        // Soma dos totalizadores
        $total->geral->valorContratoGeral += $total->porNota[$notaFiscal]->valorContrato;
        $totalGeralMulta += ($total->porNota[$notaFiscal]->valorContrato * ($contrato->percentualMulta / 100));
    }
}

unset($contador);
unset($contratos);
unset($notas);
unset($titulos);

?>

<div class="bloco_titulo">Multas sobre o valor de locação 2 </div>

<?php
if ($quantidade->total) :
    $atual = new stdClass();
    $atual->contrato   = '';
    $atual->notaFiscal = '';
                              
?>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="centro">Nota</th>
                        <th class="centro">Contrato</th>
                        <th class="centro">% Multa</th>
                        <th class="centro">Data vencimento</th>
                        <th class="centro">Valor do contrato</th>
                        <th class="centro">Valor do título</th>
                        <th class="centro">Valor da multa</th>
                        <!--th class="centro">Estornar</th>
                        <th class="centro">Observação</th-->
                        <th class="centro">Ação</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td class="direita" colspan="4">Total Geral</td>
                        <td class="direita">R$ <?php echo toMoney($total->geral->valorContratoGeral); ?></td>
                        <td class="direita">R$ <?php echo toMoney($total->geral->valorTitulo); ?></td>
                        <td class="direita">
                            R$
                            <input type="text"
                                readonly="readonly"
                                size="5"
                                value="<?php echo toMoney($totalGeralMulta); ?>"
                                class="multa-locacao-soma-geral" />
                        </td>
                        <!--td>&nbsp;</td>
                        <td class="centro">
                            <input type="checkbox" class="multa-locacao-observacao-selecionar-geral" />
                            Alterar obs.:
                            <select class="change-multa-locacao-observacao">
                                <option>Nota baixada pela Sascar</option>
                                <option>A vencer</option>
                                <option>Vencida</option>
                                <option>Baixado</option>
                            </select>
                        </td-->
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="10" class="centro">
                            <?php if ($quantidade->total == 1) : ?>
                                1 registro encontrado
                            <?php else : ?>
                                <?php echo $quantidade->total; ?> registros encontrados
                            <?php endif; ?>
                        </td>
                    </tr>
                </tfoot>
                <?php foreach ($dados as $notaFiscal) : ?>
                    <tbody>
                        <?php foreach ($notaFiscal->contratos as $contrato) : ?>
                            <?php $i=0;?>
                            <?php foreach ($contrato->titulos as $titulo) : ?>
                                <tr>
                                    <?php
                                    if ($notaFiscal->numero != $atual->notaFiscal) :
                                        $atual->notaFiscal = $notaFiscal->numero;
                                    ?>
                                        <td rowspan="<?php echo $quantidade->notas[$notaFiscal->numero]; ?>" class="agrupamento">
                                            <?php echo $notaFiscal->numero; ?>
                                            <input type="hidden" value="<?php echo $notaFiscal->numero; ?>" class="notas-fiscais" />
                                        </td>
                                    <?php
                                    endif;

                                    if ($contrato->termo != $atual->contrato) :
                                        $atual->contrato = $contrato->termo;
                                    ?>
                                    <?php endif; ?>

                                    <?php if($i==0) : ?>
                                        <td rowspan="<?=count($contrato->titulos); ?>" class="agrupamento"><?php echo $contrato->termo; ?></td>
                                    <?php endif; ?>

                                    <td class="direita">
                                        <input type="hidden"
                                            value="<?php echo $contrato->termo; ?>"
                                            class="contrato-multa-locacao" />
                                        <input type="hidden"
                                            value="<?php echo $titulo->pagamentoExtorno == true ? "S" : "N"; ?>"
                                            class="parcela-locacao-pago" />
                                        <?php if ($titulo->pagamentoExtorno) : ?>
                                            <input id="" type="text"
                                                maxlength="3"
                                                readonly="readonly"
                                                size="3"
                                                value="<?php echo $contrato->percentualMulta; ?>"
                                                class="mask-numbers multa-locacao-porcentagem-pago" />
                                        <?php else : ?>
                                            <input id="" type="text"
                                                maxlength="3"
                                                size="3"
                                                value="<?php echo $contrato->percentualMulta; ?>"
                                                class="multa-locacao-porcentagem mask-numbers" 
                                                readOnly="readonly" />
                                        <?php endif; ?>
                                    </td>
                                    <td class="centro">
                                        <?php echo $titulo->dataVencimento; ?>
                                    </td>
                                    <td class="direita">
                                        <input id="" type="text"
                                            name=""
                                            readonly="readonly"
                                            size="5"
                                            value="<?php echo toMoney($titulo->valorUnitario); ?>"
                                            class="multa-locacao-valor" />
                                    </td>
                                    <td class="direita">
                                        <input id="" type="text"
                                            name=""
                                            readonly="readonly"
                                            size="5"
                                            value="<?php echo toMoney($titulo->valorTotal); ?>"
                                            class="multa-locacao-porcentagem-contrato" />
                                    </td>
                                    <td class="direita">
                                        <input id="" type="text"
                                            name=""
                                            readonly="readonly"
                                            size="5"
                                            value="<?php echo toMoney($titulo->valorMulta); ?>"
                                            class="multa-locacao-total" />
                                    </td>
                                    <!--td>
                                        <?php //echo $titulo->pagamentoExtorno == true ? "Sim" : "Não"; ?>
                                    </td>
                                    <td class="centro">
                                        <input type="checkbox" class="multa-locacao-observacao-check" />
                                        <input type="text"
                                            name="multa_locacao_observacao[]"
                                            size="30"
                                            value="<?php /* echo $equipamento->status; */ ?>"
                                            class="multa-locacao-observacao" />
                                    </td-->
                                    <td class="centro">
                                        <a id="botaoZerar_<?=substr($notaFiscal->numero, 0, strpos($notaFiscal->numero, '/'))?>" class="botao zerar-multa-locacao">Zerar multa</a>
                                    </td>
                                </tr>
                                <?$i++;?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="direita" colspan="4">Total Nota</td>
                            <td class="direita">R$ <?php echo toMoney($total->porNota[$notaFiscal->numero]->valorContrato); ?></td>
                            <td class="direita">R$ <?php echo toMoney($total->porNota[$notaFiscal->numero]->valorTitulo); ?></td>
                            <td class="direita">
                                R$
                                <input type="text"
                                    readonly="readonly"
                                    size="5"
                                    value="<?php echo toMoney($total->porNota[$notaFiscal->numero]->valorContrato * ($contrato->percentualMulta / 100)); ?>"
                                    class="multa-locacao-soma-pornota" 
                                    id="valorTotalMulta_<?=substr($notaFiscal->numero, 0, strpos($notaFiscal->numero, '/'))?>" />
                            </td>
                            <!--td>&nbsp;</td>
                            <td class="centro">
                                <input type="checkbox" class="multa-locacao-observacao-selecionar-pornota" />
                                Alterar obs.:
                                <select class="change-multa-locacao-observacao">
                                    <option>Nota baixada pela Sascar</option>
                                    <option>A vencer</option>
                                    <option>Vencida</option>
                                    <option>Baixado</option>
                                </select>
                            </td-->
                            <td>&nbsp;</td>
                        </tr>
                    </tfoot>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
<?php else : ?>
    <div class="bloco_acoes">
        <p><strong>Nenhum registro encontrado.</strong></p>
    </div>
<?php endif; ?>

<!-- STI 84189 -->
<?php
    $notasFiscaisBaixa = json_encode($dados, JSON_FORCE_OBJECT);
    $arrContratos      = json_encode($arrContratos);    
?>
<script type="text/javascript">
    var notasFiscaisBaixa = jQuery.parseJSON('<?=$notasFiscaisBaixa?>');
    var arrContratos      = jQuery.parseJSON('<?=$arrContratos?>');
</script>
<!-- FIM STI 84189 -->