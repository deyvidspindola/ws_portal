<div class="separador"></div>

<div class="bloco_titulo">Motoristas Logados</div>
<div class="resultado bloco_conteudo">
    <?php
    if (count($this->view->dados->resultadoMotoristas) > 0):
        $classeLinha = "par";
        ?>
        <div class="listagem cabecalho_fixo">
            <table>
                <thead>
                    <tr>
                        <th class="menor" width="50%">Login</th>
                        <th class="menor" width="50%">Nome</th>
                    </tr>

                </thead>
                <tbody>
                    <?php foreach ($this->view->dados->resultadoMotoristas as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "impar") ? "par" : "impar"; ?>
                            <tr class="<?php echo $classeLinha; ?>">
                                <td class="centro" width="50%"><?php echo $resultado->motologin; ?></td>
                                <td class="centro" width="50%"><?php echo $resultado->motonome; ?></td>
                            </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="conteudo"><p>Nehum motorista logado no período</p></div>
        <?php endif; ?>
    </div>
</div>