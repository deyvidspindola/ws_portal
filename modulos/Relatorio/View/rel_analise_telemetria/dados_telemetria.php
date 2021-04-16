<div class="separador"></div>
<!-- <div class="resultado bloco_titulo">Resultado da Pesquisa</div> -->
<div class="resultado bloco_titulo">Dados Telemetria</div>
<div class="resultado bloco_conteudo">

    <div class="listagem cabecalho_fixo">
        <table>
            <thead>
                <tr>
                    <th>Data Posição</th>
                    <th>Horímetro</th>
                    <th>Hodômetro</th>
                    <th>Velocidade</th>
                    <th>RPM</th>
                    <th>Motor Ligado</th>
                    <th>Login Motorista</th>
                </tr>

            </thead>
            <tbody>
                <?php
                if (count($this->view->dados->resultadoTelemetria) > 0):
                    $classeLinha = "par";
                    foreach ($this->view->dados->resultadoTelemetria as $resultado) :
                        $classeLinha = ($classeLinha == "impar") ? "par" : "impar"; ?>
                        <tr class="<?php echo $classeLinha; ?>">
                            <td class="centro"><?php echo $resultado->dadtdt_pacote; ?></td>
                            <td class="centro"><?php echo $resultado->dadthorimetro; ?></td>
                            <td class="centro"><?php echo ($resultado->dadtodometro != '0') ? substr_replace($resultado->dadtodometro, ',', -1, 0) : '0' ?></td>
                            <td class="centro"><?php echo $resultado->dadtvelocidade; ?></td>
                            <td class="centro"><?php echo $resultado->dadtrpm; ?></td>
                            <td class="centro"><?php echo (($resultado->dadtmotor_funcionando == '1') ? 'Sim' : 'Não'); ?></td>
                            <td class="centro"><?php echo ($resultado->dadtmotooid == '0') ? 'Sem Motorista' : $resultado->dadtmotooid; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="resultado bloco_mensagens">
    <p>
        Total Dados no Período: <?php echo count($this->view->dados->resultadoTelemetria); ?>
    </p>
</div>