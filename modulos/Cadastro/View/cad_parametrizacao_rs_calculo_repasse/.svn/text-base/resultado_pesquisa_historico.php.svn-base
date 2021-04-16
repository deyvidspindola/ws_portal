

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Usuário</th>
                    <th>Código</th>
                    <th>Faixa Inicial Parque Médio</th>
                    <th>Faixa Final Parque Médio</th>
                    <th>Revenue Share VIVO</th>
                    <th>Revenue Share SASCAR</th>
                    <th>Preço Mínimo</th>
                    <th>Incremento Valor</th>
                    <th>Acão</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->dados) > 0):
                    $classeLinha = "par";
                    ?>

                    <?php foreach ($this->view->dados as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
							<tr class="<?php echo $classeLinha; ?>">
                                <td class="centro"><?php echo date('d/m/Y H:i:s', strtotime($resultado->hrscrdt_cadastro)) ?></td>
                                <td class=""><?php echo $resultado->usuario ?></td>
                                <td class="direita"><?php echo $resultado->prscroid ?></td>
                                <td class="direita"><?php echo number_format($resultado->hrscrfaixa_inicial, 2, ',', '.') ?></td>
                                <td class="direita"><?php echo number_format($resultado->hrscrfaixa_final, 2, ',', '.') ?></td>
                                <td class="direita"><?php echo number_format($resultado->hrscrrevenue_share_vivo, 2, ',', '.') ?> %</td>
                                <td class="direita"><?php echo number_format($resultado->hrscrrevenue_share_sascar, 2, ',', '.') ?> %</td>
                                <td class="direita">R$ <?php echo number_format($resultado->hrscrpreco_minimo, 2, ',', '.') ?></td>
                                <td class="direita">R$ <?php echo number_format($resultado->hrscrincremento_valor, 2, ',', '.') ?></td>
                                <td>
                                    <?php echo $resultado->acao ?>
                                </td>
							</tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10 class="centro">
                        <?php
                        $totalRegistros = count($this->view->dados);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>