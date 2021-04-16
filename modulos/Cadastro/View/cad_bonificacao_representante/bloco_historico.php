        

<div class="separador"></div>
<div class="resultado bloco_titulo">Histórico de Alterações</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="menor">Horario</th>
                    <th class="menor">Data</th>
                    <th>Mensagem</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->parametros) > 0):
                    $classeLinha = "par";
                    ?>

                    <?php foreach ($this->view->parametros->historico as $registro) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                            <tr class="<?php echo $classeLinha; ?>">
                                <td class="centro"><?php echo $registro->horario; ?></td>
                                <td class="centro"><?php echo $registro->data; ?></td>
                                <td class="esquerda"><?php echo wordwrap($registro->mensagem,100,"<br />", true); ?></td>
                            </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="centro">
                        <?php
                        $totalRegistros = count($this->view->parametros->historico);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>