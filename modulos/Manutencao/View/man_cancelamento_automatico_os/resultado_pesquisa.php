		

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th id="th_motivo">Motivo</th>
                    <th id="th_status" class="menor">Status</th>
                    <th id="th_acoes" class="acao">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->dados) > 0):
                    $classeLinha = "par";
                    ?>

                    <?php foreach ($this->view->dados as $indice => $resultado) : ?>
                        <?php $classeLinha = ($indice + 1) % 2 == 0 ? "par" : "impar"; ?>
                        <tr class="<?php echo $classeLinha; ?>">
                            <td class="esquerda"><?php echo htmlentities($resultado->osmc_motivo); ?></td>
                            <td class="esquerda"><?php echo ($resultado->osmc_status == 'A') ? 'Ativo' : 'Invativo'; ?></td>
                            <td class="centro">
                                <a href="cad_motivo_cancelamento_o_s.php?acao=editar&osmcoid=<?php echo $resultado->osmcoid; ?>" onclick="('<?php echo $resultado->osmcoid; ?>');">
                                    <img alt="Alterar" class="editar" src="/sistemaWeb/images/icones/file.gif">
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="centro">
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