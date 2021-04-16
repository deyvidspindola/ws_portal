<div class="separador"></div>
<div class="resultado bloco_titulo">Dados Delta</div>
<div class="resultado bloco_conteudo">

    <div class="listagem cabecalho_fixo">
        <table>
            <thead>
                <tr>
                    <th>Inicio Delta</th>
                    <th>Hodômetro Inicio</th>
                    <th>Fim Delta</th>
                    <th>Hodômetro Fim</th>
                    <th>Movimento (Segundos)</th>
                    <th>Parado (Segundos)</th>
                    <th>Giro Motor (Segundos)</th>
                    <th>Consumo (Litros)</th>
                    <th>Tipo Delta</th>
                    <th>Login Motorista</th>
                </tr>

            </thead>
            <tbody>
                <?php
                if (count($this->view->dados->resultadoDelta) > 0):
                    $classeLinha = "par";
                    foreach ($this->view->dados->resultadoDelta as $resultado) :
                        $classeLinha = ($classeLinha == "impar") ? "par" : "impar"; ?>
                        <tr class="<?php echo $classeLinha; ?>">
                            <td class="centro"><?php echo $resultado->dt_inicio_jornada; ?></td>
                            <td class="centro"><?php echo ($resultado->odometro_inicial != '0') ? substr_replace($resultado->odometro_inicial, ',', -1, 0) : '0' ?></td>

                            <td class="centro"><?php echo $resultado->dt_final_delta; ?></td>
                            <td class="centro"><?php echo ($resultado->odometro_final != '0') ? substr_replace($resultado->odometro_final, ',', -1, 0) : '0' ?></td>

                            <td class="centro"><?php echo $resultado->deldt_movimento; ?></td>
                            <td class="centro"><?php echo $resultado->deldt_parado; ?></td>
                            <td class="centro"><?php echo $resultado->deldt_motor_giro; ?></td>
                            <td class="centro"><?php echo $resultado->delconsumo_combustivel; ?></td>
                            <td class="centro"><?php echo $resultado->tipo; ?></td>
                            <td class="centro"><?php echo ($resultado->delmotooid == '0') ? 'Sem Motorista' : $resultado->delmotooid; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="resultado bloco_mensagens">
    <p>
        Total Dados no Período: <?php echo count($this->view->dados->resultadoDelta); ?>
    </p>
</div>
