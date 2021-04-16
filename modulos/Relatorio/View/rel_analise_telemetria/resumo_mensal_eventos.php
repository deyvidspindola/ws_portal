<div class="separador"></div>

<div class="bloco_titulo">Resumo Mensal (Eventos)</div>
<div class="resultado bloco_conteudo">
    <div class="listagem cabecalho_fixo">
        <table>
            <thead>
                <tr>
                    <th width="50%">Evento</th>
                    <th width="50%">Qtde</th>
                </tr>

            </thead>
            <tbody>
                <?php
                if (count($this->view->dados->resumoMensalEventos) > 0):
                    $classeLinha = "par";
                    ?>
                    <?php foreach ($this->view->dados->resumoMensalEventos as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "impar") ? "par" : "impar"; ?>
                            <tr class="<?php echo $classeLinha; ?>">
                                <td class="centro" width="50%"><?php echo $resultado->evetdescr; ?></td>
                                <td class="centro" width="50%"><?php echo $resultado->qtd; ?></td>
                            </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>