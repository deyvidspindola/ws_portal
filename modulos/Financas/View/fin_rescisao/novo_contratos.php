<div class="separador"></div>

<div class="bloco_titulo">Contratos</div>

<? if ($contratos): ?>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2"><input type="checkbox" class="selecionar-todos-contratos"checked="checked" /></th>
                        <th rowspan="2">Contrato</th>
                        <th rowspan="2">Modelo de Contrato</th>
                        <th rowspan="2">Veículo</th>
                        <th rowspan="2">Classe equip.</th>
                        <th rowspan="2">Tipo</th>
                        <th colspan="2" class="col-center">Vigência</th>
                        <th colspan="2" class="col-center">Meses</th>
                        <th colspan="1" class="col-center">Monitoramento</th>
                        <th rowspan="2">Isenção</th>
                        <th rowspan="2">Data da Solicitação</th>
                    </tr>
                    <tr>
                        <th>Início</th>
                        <th>Fim</th>
                        <th>Vigência</th>
                        <th>Faltantes</th>
                        <th>% Multa</th>
                        <?php
                        /*
                         *  Ocultar os valores da rescisão, pois serão buscados no WebService
                         *
                         <th>Valor</th>
                        <th>Multa</th>
                        <th>Desconto</th>
                        <th>Total</th>*/?>
                    </tr>
                </thead>

                <tbody>
                <? foreach ($contratos as $contrato): ?>
                    <?
                        /* $tituloMonitoramento = $this->_dao->findTituloMonitoramentoContrato(
                            $contrato['prescadastro'], $contrato['connumero']
                        ); */

                        // Calcula meses do contrato
                        if (isset($contrato['meses_aditivo'])
                                && $contrato['meses_aditivo'] > 0)
                        {
                            $mesesVigencia = $contrato['meses_aditivo'];
                        }
                        else
                        {
                            $mesesVigencia = $contrato['conprazo_contrato'];
                        }


                        //verifica se há fidelizacao
                        if(isset($contrato['hfcdt_fidelizacao'])){
                            $dataInicio = $contrato['hfcdt_fidelizacao'];
                        }else{
                            $dataInicio = $contrato['condt_ini_vigencia'];
                        }

                        $dataFax = $contrato['prescadastro'] ? strtotime(str_replace('/', '-', $contrato['prescadastro'])) : strtotime(date('Y-m-d'));
                        $dataFim = strtotime("+{$mesesVigencia} months", strtotime($dataInicio));
                        $dataInicio = strtotime($dataInicio);

                        // Calcula a diferença entre meses faltantes
                        if ($mesesVigencia > 0 && ($dataFim > $dataFax)) {

                            $dataInicio = new DateTime(date('d-m-Y', $dataInicio));
                            $dataFim    = new DateTime(date('d-m-Y', $dataFim));
                            $dataAtual  = new DateTime(date('d-m-Y', $dataFax));
                            $diff = $dataInicio->diff($dataAtual);

                            //igualando o calculo feito no webservice Java
                            $mesesFaltantes = round($mesesVigencia - ($diff->days / 30.41667));
                        }
                        else
                        {
                            $mesesFaltantes = 0;
                        }

                        // Porcentagem da multa
                        $porcentagemMulta = (intval($mesesVigencia) == 12) ? 10 : 25;

                        // Calcula a multa de monitoramento
                        /* $valorMonitoramento = $tituloMonitoramento['valor_monitoramento'] * $mesesFaltantes;
                        $valorMultaMonitoramento = $porcentagemMulta * $valorMonitoramento / 100;

                        // Cálculo do total
                        $totalMulta = $valorMultaMonitoramento - $tituloMonitoramento['desconto_multa_monitoramento'];
                        $totalMulta = ($totalMulta > 0) ? $totalMulta : 0;*/
                    ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="" id=""
                                class="contrato-cliente" checked="checked"
                                value="<?= $contrato['connumero'] ?>" />
                        </td>
                        <td>
                            <?= $contrato['connumero'] ?>
                        </td>
                        <td><?php 
                  				$dados = $contrato['convl_modelo_contrato'];
		                        if ($dados == 1) {
		                        	print "Contrato padrão 2015";
		                        }elseif ($dados == 2){
									print "Contrato padrão anterior a 2015";
								}elseif($dados == 3){
									print "Minuta diferenciada";
								}?>
						</td>
                        <td>
                            <?= $contrato['veiplaca'] ?>
                        </td>
                        <td>
                            <?= $contrato['eqcdescricao'] ?>
                        </td>
                        <td>
                            <?= ($contrato['tpcseguradora'] == 't') ? 'Seguradora' : 'Cliente' ?>
                        </td>
                        <? if(isset($contrato['hfcdt_fidelizacao'])): ?>
                            <td class="contrato-multa-data-inicio" data-inicio="<?= date('d/m/Y', strtotime($contrato['hfcdt_fidelizacao'])) ?>">
                                <strike><?= date('d/m/Y', strtotime($contrato['condt_ini_vigencia'])) ?></strike><br>
                                <?= date('d/m/Y', strtotime($contrato['hfcdt_fidelizacao'])) ?>
                            </td>
                        <? else: ?>
                            <td class="contrato-multa-data-inicio" data-inicio="<?= date('d/m/Y', strtotime($contrato['condt_ini_vigencia'])) ?>">
                                <?= date('d/m/Y', strtotime($contrato['condt_ini_vigencia'])) ?>
                            </td>
                        <? endif; ?>
                        <? if(isset($contrato['hfcdt_fidelizacao'])): ?>
                            <td class="contrato-multa-data-fim" data-fim="<?= date('d/m/Y', strtotime("+{$mesesVigencia} month", strtotime($contrato['hfcdt_fidelizacao']))) ?>">
                                <strike><?= date('d/m/Y', strtotime("+{$mesesVigencia} month", strtotime($contrato['condt_ini_vigencia']))) ?></strike><br>
                                <?= date('d/m/Y', strtotime("+{$mesesVigencia} month", strtotime($contrato['hfcdt_fidelizacao']))) ?>
                            </td>
                        <? else: ?>
                            <td class="contrato-multa-data-fim" data-fim="<?= date('d/m/Y', strtotime("+{$mesesVigencia} month", strtotime($contrato['condt_ini_vigencia']))) ?>">
                                <?= date('d/m/Y', strtotime("+{$mesesVigencia} month", strtotime($contrato['condt_ini_vigencia']))) ?>
                            </td>
                        <? endif; ?>
                        <td>
                            <input type="text" maxlength="2" size="2"
                                class="contrato-multa-meses contrato-multa-recalcula mask-numbers"
                                value="<?= $mesesVigencia ?>" />
                        </td>
                        <td>
                            <input type="text" maxlength="2" size="2"
                                readonly="readonly"
                                class="contrato-multa-meses-faltantes"
                                value="<?= $mesesFaltantes >= 0 ? $mesesFaltantes : 0 ?>" />
                        </td>
                        <td>
                            <input type="text"
                                size="5" class="contrato-multa-porcentagem contrato-multa-recalcula mask-numbers"
                                value="<?= $porcentagemMulta ?>" id="multa-<?= $contrato['connumero'] ?>" />
                        </td>
                        <?php
                        /*
                         *  Ocultar os valores da rescisão, pois serão buscados no WebService
                         *

                        <td>
                            <input type="hidden"
                                readonly="readonly" size="5"
                                class="contrato-multa-obrigacao"
                                value="<?= toMoney($tituloMonitoramento['valor_monitoramento']) ?>" />

                            <input type="text"
                                readonly="readonly" size="5"
                                class="contrato-multa-monitoramento"
                                value="<?= toMoney($valorMonitoramento) ?>" />
                        </td>
                        <td>
                            <input type="text"
                                readonly="readonly" size="5"
                                class="contrato-multa-valor"
                                value="<?= toMoney($valorMultaMonitoramento) ?>" />
                        </td>
                        <td>
                            <input type="text"
                                readonly="readonly" size="5"
                                class="contrato-multa-desconto"
                                value="<?= toMoney($tituloMonitoramento['desconto_multa_monitoramento']) ?>" />
                        </td>
                        <td>
                            <input type="text"
                                readonly="readonly" size="5"
                                class="contrato-multa-total"
                                value="<?= toMoney($totalMulta) ?>" />
                        </td>
                        <td><a class="botao zerar-multa-contrato">Zerar multa</a></td>
                        */?>
                        <?php ?>
                        <td class="">
                            <input type="checkbox" name="incluir_servicos_monitoramento_<?php echo $contrato['connumero'] ?>" id="incluir_servicos_monitoramento_<?php echo $contrato['connumero'] ?>" value="" class="checkbox">Monitoramento
                            <br />
                            <input type="checkbox" name="incluir_servicos_locacao_<?php echo $contrato['connumero'] ?>" id="incluir_servicos_locacao_<?php echo $contrato['connumero'] ?>" value="" class="checkbox">Locação                      
                        </td>
                        <td class="data">
                            <input id="resmfax-<?php echo $contrato['connumero']; ?>"
                                type="text"
                                maxlength="10"
                                value="<?php echo $contrato['prescadastro']/* ? $contrato['prescadastro'] : date('d/m/Y');*/ ?>"
                                class="contrato-data-recisao campo" />
                        </td>

                    </tr>
                <? endforeach ?>
                </tbody>

                <tfoot>
                    <tr>
                        <!--td><input type="checkbox" class="selecionar-todos-contratos"
                                checked="checked" />
                        </td-->
                        <td colspan="16" align="center">
                            <?= count($contratos) ?> contratos(s) encontrado(s)
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="bloco_acoes">
        <a class="botao pesquisar-multas">Calcular</a>
    </div>
<? else: ?>
    <div class="bloco_acoes">
        <p><strong>Nenhum registro encontrado.</strong></p>
    </div>
<? endif ?>