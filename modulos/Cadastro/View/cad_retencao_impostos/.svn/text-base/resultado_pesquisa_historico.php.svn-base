<div class="resultado bloco_titulo">Histórico de Manutenções na Tabela de Retenção de Impostos</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th>Data Registro</th>
                    <th>Usuário</th>
                    <th>ISS</th>
                    <th>PIS</th>
                    <th>COFINS</th>
                    <th>Valor Chip</th>
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
                                <td class="centro"><?php echo date('d/m/Y H:i:s', strtotime($resultado->hrsridt_cadastro)) ?></td>
                                <td class=""><?php echo $resultado->usuario ?></td>
                                <td class="direita"><?php echo number_format($resultado->hrsriiss, 2, ',', '.') ?> %</td>
                                <td class="direita"><?php echo number_format($resultado->hrsripis, 2, ',', '.') ?> %</td>
                                <td class="direita"><?php echo number_format($resultado->hrsricofins, 2, ',', '.') ?> %</td>
                                <td class="direita">R$<?php echo number_format($resultado->hrsrivalor_chip, 2, ',', '.') ?></td>
							</tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6 class="centro">
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
<div class="separador"></div>