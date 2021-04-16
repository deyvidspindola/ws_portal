
<div class="separador"></div>
<div class="bloco_titulo">Resultado da Pesquisa</div>
<div class="bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="centro">Data<br>Atendimento</th>
                    <th class="centro">Representante</th>
                    <th class="centro">Técnico</th>
                    <th class="centro" title="Sequência dos atendimentos realizados pelo técnico">Seq.</th>
                    <th class="centro">Horário<br>Atendimento</th>
                    <th class="centro">Visita<br>Improdutiva</th>
                    <th class="centro">Ordem de<br>Serviço</th>
                    <th class="centro">Endereço do<br>Atendimento</th>
                    <th class="centro">Valor<br>KM (R$)</th>
                    <th class="centro">Abrangência<br>(KM)</th>
                    <th class="centro" title="Valor já descontado Abrangência">Total<br>Deslocamento<br>(KM)</th>
                    <th class="centro">Total<br>Pedágio<br>(R$)</th>
                    <th class="centro" title="(Valor KM * Total Deslocamento) + Total Pedágio">Valor<br>Total<br>(R$)</th>
                </tr>
            </thead>
            <tbody>
                <?php $i_atendimentos   = 0; ?>
                <?php $nr_linha_tecnico = 0; ?>
                <?php foreach ($this->view->dados->pesquisa as $data => $representantes): ?>
                    <?php foreach ($representantes as $id_representante => $representante): ?>
                        <?php $total_representante = 0; ?>
                        <?php foreach ($representante as $id_tecnico => $atendimentos): ?>
                            <?php foreach ($atendimentos as $chave => $atendimento): ?>

                                <?php if ($chave == 0) : ?>
                                     <tr class="<?=(($nr_linha_tecnico % 2) ? "fundo_par" : "fundo_impar")?> linha_invisivel">
                                        <td class="centro" rowspan="<?=count($atendimentos)+3?>"><?=$atendimento->data_atendimento?></td>
                                        <td class="esquerda" rowspan="<?=count($atendimentos)+3?>"><?=$atendimento->representante?></td>
                                        <td class="esquerda" rowspan="<?=count($atendimentos)+3?>"><?=$atendimento->tecnico?></td>
                                        <td class="centro" colspan="10"></td>
                                    </tr>
                                    <tr class="<?=(($nr_linha_tecnico % 2) ? "fundo_par" : "fundo_impar")?>">
                                        <td class="centro" colspan="4">Saída</td>
                                        <td class="esquerda"><?=$atendimento->endereco_saida?></td>
                                        <td class="centro"></td>
                                        <td class="centro"></td>
                                        <td class="centro"></td>
                                        <td class="centro"></td>
                                        <td class="centro"></td>
                                    </tr>
                                <?php endif; ?>

                                <tr class="<?=(($nr_linha_tecnico % 2) ? "fundo_par" : "fundo_impar")?>">
                                    <td class="centro"><?=$chave+1?>º</td>
                                    <td class="centro"><?=$atendimento->hora_atendimento?></td>
                                    <td class="centro"><?=($atendimento->tipo == 'VI' ? "SIM":"")?></td>
                                    <td class="centro"><a target="_blank" href="<?php echo _PROTOCOLO_ . _SITEURL_; ?>prn_ordem_servico.php?ESTADO=cadastrando&acao=editar&ordoid=<?php echo $atendimento->ordoid; ?>"><?=$atendimento->ordoid?></a></td>
                                    <td class="esquerda">
                                        <?php if($atendimento->os_direcionada == 'f'): ?>
                                        <?=$atendimento->logradouro?><br>
                                        <?=$atendimento->bairro?><br>
                                        <?=$atendimento->cidade?> - <?=$atendimento->uf?><br>
                                        <?=$atendimento->cep?>
                                        <?php else:
                                                echo "### O.S. Direcionada ###";
                                              endif;
                                         ?>
                                    </td>
                                    <td class="centro"><?=number_format($atendimento->valor_km, 2, ',', '.')?></td>
                                    <td class="centro"><?=$atendimento->abrangencia?></td>

                                    <?php if ($atendimento->status_deslocamento > 1): ?>
                                        <td class="centro"><img class="img" src="./images/Agendamento/unavailable.png" title="<?=$this->view->dados->mensagens_cron[$atendimento->status_deslocamento]?>"></td>
                                        <td class="centro"><img class="img" src="./images/Agendamento/unavailable.png" title="<?=$this->view->dados->mensagens_cron[$atendimento->status_deslocamento]?>"></td>
                                        <td class="centro"><img class="img" src="./images/Agendamento/unavailable.png" title="<?=$this->view->dados->mensagens_cron[$atendimento->status_deslocamento]?>"></td>
                                    <?php else : ?>
                                        <?php if ($chave == count($atendimentos)-1): ?>
                                            <td class="centro"><?php
                                                $valorKmChegada = ($atendimento->total_km - $atendimento->retorno_km);
                                                if( $valorKmChegada < 0 ) {
                                                    echo '0';
                                                } else {
                                                    echo $valorKmChegada;
                                                }
                                                ?>
                                            </td>
                                            <td class="centro"><?php
                                                    $valorPedagioChegada = ($atendimento->total_pedagio - $atendimento->retorno_pedagio);
                                                    if( $valorPedagioChegada < 0 ){
                                                        echo '0.00';
                                                    } else {
                                                        echo  number_format($atendimento->total_pedagio - $atendimento->retorno_pedagio, 2, ',', '.');
                                                    }

                                                    ?>
                                            </td>
                                            <td class="centro"><?php
                                                    $valorDeslocChegada = ($atendimento->valor_total - $atendimento->valor_total_retorno);

                                                    if( $valorDeslocChegada < 0){
                                                        echo '0,00';
                                                    } else {
                                                        echo number_format($valorDeslocChegada, 2, ',', '.');
                                                    }
                                                    ?>
                                            </td>
                                        <?php else : ?>
                                            <td class="centro"><?=$atendimento->total_km?></td>
                                        <td class="centro"><?=number_format($atendimento->total_pedagio, 2, ',', '.')?></td>
                                        <td class="centro"><?=number_format($atendimento->valor_total, 2, ',', '.')?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr>

                                <?php if ($chave == count($atendimentos)-1): ?>
                                    <tr class="<?=(($nr_linha_tecnico % 2) ? "fundo_par" : "fundo_impar")?>">
                                        <td class="centro" colspan="4">Chegada</td>
                                        <td class="esquerda"><?=$atendimento->endereco_chegada?></td>
                                        <td class="centro"><?=number_format($atendimento->valor_km, 2, ',', '.')?></td>
                                        <td class="centro"><?=$atendimento->abrangencia?></td>

                                        <?php if ($atendimento->status_deslocamento_chegada > 1): ?>
                                            <td class="centro"><img class="img" src="./images/Agendamento/unavailable.png" title="<?=$this->view->dados->mensagens_cron[$atendimento->status_deslocamento_chegada]?>"></td>
                                            <td class="centro"><img class="img" src="./images/Agendamento/unavailable.png" title="<?=$this->view->dados->mensagens_cron[$atendimento->status_deslocamento_chegada]?>"></td>
                                            <td class="centro"><img class="img" src="./images/Agendamento/unavailable.png" title="<?=$this->view->dados->mensagens_cron[$atendimento->status_deslocamento_chegada]?>"></td>
                                        <?php else : ?>
                                            <td class="centro"><?=number_format($atendimento->retorno_km, 0, ',' , '.')?></td>
                                            <td class="centro"><?=number_format($atendimento->retorno_pedagio, 2, ',', '.')?></td>
                                            <td class="centro"><?=number_format($atendimento->valor_total_retorno  , 2, ',', '.')?></td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endif; ?>

                                <?php $i_atendimentos++; ?>
                                <?php $total_representante += $atendimento->valor_total; ?>
                                <?php $nome_representante = $atendimento->representante; ?>
                                                            <?php endforeach; ?>
                            <?php $nr_linha_tecnico++; ?>
                            <tr>
                                <td class="" colspan="13"></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td class="fundo_total" colspan="13">Sub-Total Representante <?=$nome_representante?>: R$ <?=number_format($total_representante, 2, ',', '.')?></td>
                        </tr>
  
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="13"><?=$i_atendimentos?> registro<?=($i_atendimentos > 1 ? "s" : "")?> encontrado<?=($i_atendimentos > 1 ? "s" : "")?>.</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>