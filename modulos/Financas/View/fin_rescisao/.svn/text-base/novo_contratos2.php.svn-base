<div class="separador"></div>

<div class="bloco_titulo">Contratos</div>

<?php  if ($contratos): ?>
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
                        <th rowspan="2">Desconto</th>
                        <th rowspan="2">Data da Solicitação</th>
                    </tr>
                    <tr>
                        <th>Início</th>
                        <th>Fim</th>
                        <th>Vigência</th>
                        <th>Faltantes</th>
                        <th>% Multa</th>
                       
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($contratos as $contrato): ?>
                    <?php
                    
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
                        $dataInicioVigenciaFidelizacao = null;
                        $dataInicioSubstituicao = null;
                        $dataInicioVigencia = null;
                        
                        //verifica se há fidelizacao
                        if(isset($contrato['hfcdt_fidelizacao'])){
                            $dataInicioVigenciaFidelizacao = $contrato['hfcdt_fidelizacao'];
                        }
                       
                        // verifica se há data de substituicao (transferência/upgrade ou downgrade)
                        if(!empty($contrato['dt_vigencia_ultimo_contrato'])){
                            $dataInicioSubstituicao = $contrato['dt_vigencia_ultimo_contrato'];
                        }
                        
                        if (!empty($dataInicioSubstituicao) && !empty($dataInicioVigenciaFidelizacao)) {
                            if ($dataInicioSubstituicao > $dataInicioVigenciaFidelizacao) {
                                $dataInicioVigencia = $dataInicioSubstituicao;
                            } else {
                                $dataInicioVigencia = $dataInicioVigenciaFidelizacao;
                            }
                        }else if (empty($dataInicioSubstituicao) && !empty($dataInicioVigenciaFidelizacao)) {
                            $dataInicioVigencia = $dataInicioVigenciaFidelizacao;
                                                    
                        }else if (empty($dataInicioVigenciaFidelizacao) && !empty($dataInicioSubstituicao)){
                            $dataInicioVigencia = $dataInicioSubstituicao;
                                    
                        }else{
                            $dataInicioVigencia = $contrato['condt_ini_vigencia'];
                        }
 
                        // Calcula a diferença entre meses faltantes
                        $dataInicio = $dataInicioVigencia;
                        $dataInicio = strtotime($dataInicio);
                        $dataFim = strtotime("+{$mesesVigencia} months", strtotime($dataInicioVigencia));
                        $dataPreRescisao = $contrato['prescadastro'] ? strtotime(str_replace('/', '-', $contrato['prescadastro'])) : strtotime(date('Y-m-d'));

                        $dataInicioD = new DateTime(date('d-m-Y', $dataInicio));
                        $dataFimD = new DateTime(date('d-m-Y', $dataFim));
                        $dataPre = new DateTime(date('d-m-Y', $dataPreRescisao));
            
                        $diffUtilizado =  $dataInicioD->diff($dataPre);
                        $mesesUtilizados = floor($diffUtilizado->days / 30.416666); //(365/12)
                                
                        $mesesFaltantes = 0;
                    
                        if ($mesesVigencia > 0) {
                                        
                            if ($dataFimD > $dataPre) {
                               
                                $diff2 = $dataFimD->diff($dataPre);
                                $mesesFaltantes = ($diff2->days /  30.41666) < 1 ? ceil($diff2->days / 30.41666) : round($diff2->days / 30.41666);
                                
                                $mesesUtilizados = intval($mesesVigencia) - intval($mesesFaltantes); 
                            }
                        } 
                        
                        // Porcentagem da multa
                        $porcentagemMulta = (intval($mesesVigencia) == 12) ? 10 : 25;
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
                        <td class="contrato-multa-data-inicio" data-inicio="<?= date('d/m/Y', strtotime($dataInicioVigencia)) ?>">
                                 <?= date('d/m/Y', strtotime($dataInicioVigencia)) ?>
                        </td>
                         
                        <td class="contrato-multa-data-fim" data-fim="<?= date('d/m/Y', $dataFim) ?>">
                             <?= date('d/m/Y', $dataFim) ?>
                         </td>
                        
                        <td>
                            <input type="text" maxlength="2" size="2"
                                class="contrato-multa-meses contrato-multa-recalcula mask-numbers"
                                value="<?= $mesesVigencia ?>" />
                        </td>
                        <td>
                            <input type="text" maxlength="2" size="2"
                                readonly="readonly"
                                class="contrato-multa-meses-faltantes"
                                value="<?= $mesesFaltantes >= 0 ? $mesesFaltantes : 0 ?>" id="meses-faltantes-<?= $contrato['connumero'] ?>" />
                        </td>
                        <td>
                            <input type="text"
                                size="5" class="contrato-multa-porcentagem contrato-multa-recalcula mask-numbers"
                                value="<?= $porcentagemMulta ?>" id="multa-<?= $contrato['connumero'] ?>" />
                        </td>

                        <td class="">
                            <input type="checkbox" name="incluir_servicos_monitoramento_<?php echo $contrato['connumero'] ?>" id="incluir_servicos_monitoramento_<?php echo $contrato['connumero'] ?>" value="" class="checkbox">Monitoramento
                            <br />
                            <input type="checkbox" name="incluir_servicos_locacao_<?php echo $contrato['connumero'] ?>" id="incluir_servicos_locacao_<?php echo $contrato['connumero'] ?>" value="" class="checkbox">Locação                      
                        </td>
                        <td class="">
                            <input type="checkbox" name="calcular_descontos_<?php echo $contrato['connumero'] ?>" id="calcular_descontos_<?php echo $contrato['connumero'] ?>" value="" class="checkbox">
                        </td>
                        <td class="data">
                            <input id="resmfax-<?php echo $contrato['connumero']; ?>"
                                type="text"
                                maxlength="10"
                                value="<?php echo $contrato['prescadastro'] ?>"
                                class="contrato-data-recisao campo" />
                        </td>

                    </tr>
                <?php endforeach ?>
                </tbody>

                <tfoot>
                    <tr>
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

<?php else: ?>
    <div class="bloco_acoes">
        <p><strong>Nenhum registro encontrado.</strong></p>
    </div>
<?php endif ?>