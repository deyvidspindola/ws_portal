    <div class="separador"></div>
    <div class="bloco_titulo">Resultado da Pesquisa</div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="centro">Representante</th>
                        <th class="centro">Classe do Cliente</th>
                        <th class="centro">Produto</th>
                        <th class="centro">Permite Enviar Produto Similar</th>
                        <th class="centro">Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $representante = 0; ?>
                    <?php $total         = 0; ?>
                    <?php foreach($this->view->dados->pesquisa as $indice => $registro) : ?>
                        <tr class="<?php echo ($indice + 1) % 2 == 0 ? 'par' : 'impar'; ?>">
                            <?php if ($registro->repoid != $representante) : ?>
                                <?php $total++; ?>
                                <?php $estilo        = $total % 2 == 0 ? '#dee6f6' : '#ffffff'; ?>
                                <?php $representante = $registro->repoid; ?>
                                <td rowspan="<?php echo $registro->replinha; ?>" style="background: <?php echo $estilo; ?>;">
                                    <?php echo $registro->reprazao; ?>
                                </td>
                            <?php endif; ?>
                            <td><?php echo $registro->eqcdescricao; ?></td>
                            <td><?php echo $registro->prdproduto; ?></td>
                            <td class="centro">
                                <?php if($registro->permite_similar == 'NÃO') : ?>
                                    <img class="img" src="./images/Agendamento/unavailable.png" height="15">
                                <?php else: ?>
                                    <img class="img" src="./images/Agendamento/check.png" height="15">
                                <?php endif; ?>
                            </td>
                            <td class="direita">
                                <?php echo $registro->sagtotal; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">
                            <?php if ($total == 1) : ?>
                                1 registro encontrado.
                            <?php else : ?>
                                <?php echo $total; ?> registros encontrados.
                            <?php endif; ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="conteudo">
        <fieldset>
            <legend style="cursor: default;">Legenda</legend>
            <ul class="legenda">
                <li><img class="img" src="./images/Agendamento/check.png" height="15"> - Sim</li>
                <li><img class="img" src="./images/Agendamento/unavailable.png" height="15"> - Não</li>
            </ul>
        </fieldset>
    </div>
