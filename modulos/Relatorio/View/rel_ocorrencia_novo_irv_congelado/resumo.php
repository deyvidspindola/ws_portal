             <?php

                //Variáveis de controle
                $totalComContato = 0;
                $totalSemContato = 0;

                foreach ($this->view->dadosResumo->recuperacoes as $ocorrencia) {
                    if($ocorrencia->tipo == 'recuperados' && $ocorrencia->motivo_ocorrencia == '') {
                        $totalComContato += intval($ocorrencia->veiculos);
                    } else if ($ocorrencia->tipo == 'recuperados') {
                        $totalSemContato += intval($ocorrencia->veiculos);
                    }
                }

                ?>

                <tr  class="">
                    <td class="maior">Equipamentos Instalados</td>
                    <td class="menor direita">
                         <?php echo $this->view->dadosResumo->total_equipamentos; ?>
                    </td>
                </tr>
                <tr class="par">
                    <td class="maior negrito">Ocorrências em Andamento</td>
                    <td class="menor direita negrito">
                         <?php echo $this->view->dadosResumo->total_andamento; ?>
                    </td>
                </tr>
                <tr class="">
                    <td class="maior">Ocorrências Atendidas (no período) </td>
                    <td class="menor direita">
                         <?php echo $this->view->dadosResumo->atendidas_periodo; ?>
                    </td>
                </tr>
                <tr class="">
                    <td class="maior">Ocorrências Anteriores Recuperadas no período</td>
                    <td class="menor direita">
                         <?php echo $this->view->dadosResumo->recuperadas_anterior; ?>
                    </td>
                </tr>
                <tr>
                    <td class="maior">Total de Ocorrências Anteriores Recuperadas e as Atendidas no período </td>
                    <td class="menor direita">
                        <?php echo (intval($this->view->dadosResumo->atendidas_periodo) + intval($this->view->dadosResumo->recuperadas_anterior)); ?>
                    </td>
                </tr>

                <tr class="par">
                    <td class="menor negrito">Veículos Recuperados</td>
                    <td class="menor direita negrito">
                         <?php
                            echo $totalRecuperados = $this->view->dadosResumo->total_veiculos_recuperados;
                            //tratamento divisão por zero
                            if($totalRecuperados == 0){
                                $divisorRecuperados = 1;
                            } else {
                                $divisorRecuperados = $totalRecuperados;
                            }
                        ?>
                    </td>
                </tr>
				<tr class="">
					<td class="maior">Com Contato</td>
					<td class="menor direita">
                        <?php
                            echo $totalComContato . " (";
                            echo number_format((intval(((intval($totalComContato *100)) / $divisorRecuperados)*100)/100),2,',','.');
                            echo "%)";

                        ?>
                    </td>
				</tr>
				<tr class="">
					<td class="maior">Sem Contato</td>
                    <td class="menor direita">
                        <?php
                            echo $totalSemContato . " (";
                            echo number_format((intval(((intval($totalSemContato *100)) / $divisorRecuperados)*100)/100),2,',','.');
                            echo "%)";
                        ?>
                    </td>
				</tr>

                 <?php foreach($this->view->dadosResumo->recuperacoes as $ocorrencia): ?>
                    <?php if(($ocorrencia->tipo == 'recuperados') && ($ocorrencia->motivo_ocorrencia != '')):?>
                    <tr class="">
                        <td class="maior">
                             <?php echo $ocorrencia->motivo_ocorrencia; ?>
                        </td>
                        <td class="menor direita">
                            <?php
                                echo $ocorrencia->veiculos . " (";
                                echo number_format((intval(((intval($ocorrencia->veiculos *100)) / $divisorRecuperados)*100)/100),2,',','.');
                                echo "%)";
                            ?>
                        </td>
                    </tr>
                     <?php endif;?>
                <?php endForEach;?>

                <?php

                //Variáveis de controle
                $totalComContato = 0;
                $totalSemContato = 0;

                foreach ($this->view->dadosResumo->recuperacoes as $ocorrencia) {
                    if($ocorrencia->tipo == 'nao_recuperados' && $ocorrencia->motivo_ocorrencia == '') {
                        $totalComContato += intval($ocorrencia->veiculos);
                    } else if ($ocorrencia->tipo == 'nao_recuperados') {
                        $totalSemContato += intval($ocorrencia->veiculos);
                    }
                }

                ?>

                <tr class="par">
                    <td class="menor negrito">Veículos Não Recuperados</td>
                    <td class="menor direita negrito">
                         <?php
                            echo $totalNaoRecuperados = $this->view->dadosResumo->total_veiculos_nao_recuperados;
                            //tratamento divisão por zero
                            if($totalNaoRecuperados == 0){
                                $divisorNaoRecuperados = 1;
                            } else {
                                $divisorNaoRecuperados = $totalNaoRecuperados;
                            }
                        ?>
                    </td>
                </tr>
				<tr class="">
					<td class="maior">Equipamento Com Contato</td>
					<td class="menor direita">
                        <?php
                            echo $totalComContato . " (";
                            echo number_format((intval(((intval($totalComContato *100)) / $divisorNaoRecuperados)*100)/100),2,',','.');
                            echo "%)";
                        ?>
                    </td>
				</tr>
                <tr class="">
					<td class="maior">Equipamento Sem Contato</td>
					<td class="menor direita">
                        <?php
                            echo $totalSemContato . " (";
                            echo number_format((intval(((intval($totalSemContato *100)) / $divisorNaoRecuperados)*100)/100),2,',','.');
                            echo "%)";
                        ?>
                    </td>
				</tr>

                 <?php foreach($this->view->dadosResumo->recuperacoes as $ocorrencia): ?>
                    <?php if(($ocorrencia->tipo == 'nao_recuperados') && ($ocorrencia->motivo_ocorrencia != '')):?>
                    <tr class="">
                        <td class="maior">
                             <?php echo $ocorrencia->motivo_ocorrencia; ?>
                        </td>
                        <td class="menor direita">
                            <?php
                                echo $ocorrencia->veiculos . " (";
                                echo number_format((intval(((intval($ocorrencia->veiculos *100)) / $divisorNaoRecuperados)*100)/100),2,',','.');
                                echo "%)";
                            ?>
                        </td>
                    </tr>
                     <?php endif;?>
                <?php endForEach;?>

				<tr class="par">
					<td class="maior">Índice Percentual de Recuperação</td>
					<td class="menor direita">
                        <?php
                            echo number_format((($totalRecuperados *100) / $this->view->dadosResumo->atendidas_periodo),1,',','.');
                            echo "%";
                        ?>
                    </td>
				</tr>
				<tr class="">
					<td class="maior">Índice Percentual de Não Recuperação</td>
					<td class="menor direita">
                          <?php
                                echo number_format((($totalNaoRecuperados *100) / $this->view->dadosResumo->total_ocorrencias),1,',','.');
                                echo "%";
                          ?>
                    </td>
				</tr>

                <?php
                    $pesados = 0;
                    $leves = 0;
                    $motos = 0;

                    foreach($this->view->dadosResumo->tipo_veiculo as $ocorrencia) {

                        switch ($ocorrencia->tipo_veiculo) {
                            case 'Pesado':
                                $pesados = $ocorrencia->total;
                                break;
                            case 'Leve':
                                $leves = $ocorrencia->total;
                                break;
                            case 'Moto':
                                $motos = $ocorrencia->total;
                                break;
                        }

                    }
                ?>

				<tr class="par">
					<td class="maior">Veículos Pesados Recuperados</td>
					<td class="menor direita">
                        <?php echo $pesados; ?>
                    </td>
				</tr>
				<tr class="">
					<td class="maior">Veículos Leves Recuperados</td>
					<td class="menor direita">
                         <?php echo $leves; ?>
                    </td>
				</tr>
                <tr class="par">
					<td class="maior">Veículos Motos Recuperados</td>
					<td class="menor direita">
                         <?php echo $motos; ?>
                    </td>
				</tr>
				<tr class="">
					<td class="maior">Informado que havia rastreador</td>
					<td class="menor direita">
                        <?php
                            echo $this->view->dadosResumo->total_rastreador . " (";
                            echo number_format((($this->view->dadosResumo->total_rastreador *100) / $totalRecuperados),1,',','.');
                            echo "%)";
                        ?>
                    </td>
				</tr>
