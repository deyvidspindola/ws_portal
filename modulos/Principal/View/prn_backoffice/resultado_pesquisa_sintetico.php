<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">

        <!-- POR MOTIVO -->
        <table>
            <thead>
                <tr>
                    <th class="centro">Motivo</th>
                    <th class="menor centro">Quantidade</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->dados['motivo']) > 0):
                    $classeLinha = "par";
                        $total=0;
                        foreach ($this->view->dados['motivo'] as $resultado) :
                            $total+=$resultado->solicitacoes;
                    ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
							<tr id="<?php echo $resultado->bacoid ?>" class="<?php echo $classeLinha; ?>">
								<td class="esquerda"><?php echo $resultado->motivo; ?></td>
								<td class="direita"><?php echo $resultado->solicitacoes; ?></td>
                            </tr>
                    <?php endforeach; ?>
            <?php
                endif; ?>
            </tbody>
            <tfoot>
                    <tr>
                        <td class="esquerda">Total</td>
                        <td class="direita"><?php echo $total; ?></td>
                    </tr>
            </tfoot>
        </table>

        <!-- POR ATENDENTE -->
        <table>
            <thead>
                <tr>
                    <th class="centro">Tempo</th>
                    <th class="menor centro">Qtde. em Andamento</th>
                    <th class="medio centro">Qtde. Concluído</th>
                    <th class="menor centro">Qtde. Pendente</th>
                    <th class="menor centro">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $arrTempo = array(
                                      '9_maior1mes'=>'Maior que 1 mês',
                                      '8_menor1mes'=>'Até 1 Mês',
                                      '7_menor5dias'=>'Até 5 dias',
                                      '6_menor3dias'=>'Até 3 dias',
                                      '5_menor2dias'=>'Até 2 dias',
                                      '4_menor1dia'=>'Até 1 dia',
                                      '3_menor12horas'=>'Até 12 horas',
                                      '2_menor6horas'=>'Até 6 horas',
                                      '1_menor2horas'=>'Até 2 horas'
                                  );
                    ksort($arrTempo);

                if (count($this->view->dados['tempo']) > 0):
                        $classeLinha = "par";
                        $total=0;
                        $total_concluido=0;
                        $total_andamento=0;
                        $total_pendente=0;
                        foreach ($arrTempo as $chave => $tempo) :
                            $linha = (isset($this->view->dados['tempo'][$chave]) ? $this->view->dados['tempo'][$chave] : array());
                            $Subtotal_linha = intval($linha['P'])+intval($linha['A'])+intval($linha['C']);

                            $total_concluido+=intval($linha['C']);
                            $total_andamento+=intval($linha['A']);
                            $total_pendente+=intval($linha['P']);

                            $total+=$Subtotal_linha;
                    ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                            <tr id="us_<?php echo $resultado->cd_usuario ?>" class="<?php echo $classeLinha; ?>">
                                <td class="esquerda"><?php echo $tempo ?></td>
                                <td class="direita"><?php echo (isset($linha['A']) ? $linha['A']  : '0' ); ; ?></td>
                                <td class="direita"><?php echo (isset($linha['C']) ? $linha['C']  : '0' );  ?></td>
                                <td class="direita"><?php echo (isset($linha['P']) ? $linha['P']  : '0' ); ?></td>
                                <td class="direita"><?php echo ($Subtotal_linha ? $Subtotal_linha  : '0' ); ?></td>
                            </tr>
                    <?php endforeach; ?>
            <?php
                endif; ?>
            </tbody>
            <tfoot>
                    <tr>
                        <td class="esquerda">Total</td>
                        <td class="direita"><?php echo $total_andamento; ?></td>
                        <td class="direita"><?php echo $total_concluido; ?></td>
                        <td class="direita"><?php echo $total_pendente; ?></td>
                        <td class="direita"><?php echo $total; ?></td>
                    </tr>
            </tfoot>
        </table>

        <!-- POR ATENDENTE -->
        <table>
            <thead>
                <tr>
                    <th class="centro">Atendente</th>
                    <th class="menor centro">Qtde. em Andamento</th>
                    <th class="medio centro">Qtde. Concluído</th>
                    <th class="menor centro">Qtde. Pendente</th>
                    <th class="menor centro">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->dados['atendente']) > 0):
                    $classeLinha = "par";
                    ?>

                    <?php
                        $total=0;
                        $total_concluido=0;
                        $total_andamento=0;
                        $total_pendente=0;
                        foreach ($this->view->dados['atendente'] as $resultado) :
                            $Subtotal_linha = $resultado->concluido+$resultado->andamento+$resultado->pendente;
                            $total_concluido+=$resultado->concluido;
                            $total_andamento+=$resultado->andamento;
                            $total_pendente+=$resultado->pendente;
                            $total+=$Subtotal_linha;
                    ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                            <tr id="us_<?php echo $resultado->cd_usuario ?>" class="<?php echo $classeLinha; ?>">
                                <td class="esquerda"><?php echo $resultado->nm_usuario; ?></td>
                                <td class="direita"><?php echo $resultado->andamento; ?></td>
                                <td class="direita"><?php echo $resultado->concluido; ?></td>
                                <td class="direita"><?php echo $resultado->pendente; ?></td>
                                <td class="direita"><?php echo $Subtotal_linha; ?></td>
                            </tr>
                    <?php endforeach; ?>
            <?php
                endif; ?>
            </tbody>
            <tfoot>
                    <tr>
                        <td class="esquerda">Total</td>
                        <td class="direita"><?php echo $total_andamento; ?></td>
                        <td class="direita"><?php echo $total_concluido; ?></td>
                        <td class="direita"><?php echo $total_pendente; ?></td>
                        <td class="direita"><?php echo $total; ?></td>
                    </tr>
            </tfoot>
        </table>
    </div>
</div>