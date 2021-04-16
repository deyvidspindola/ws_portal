<div class="separador"></div>
<!-- <div class="resultado bloco_titulo">Resultado da Pesquisa</div> -->
<div class="resultado bloco_conteudo">
    <div id="bloco_itens" class="listagem">
        <table>
            <?php
                if (count($this->view->consolidadoCliente)) : ?>
                    <thead>
                        <tr>
                            <th class="esquerda" colspan="20">
                                Consolidado Cliente
                            </th>
                        </tr>
                        <tr>
                            <th class="menor">Cliente</th>
                            <th class="medio">Total Consumo (Kb)</th>
                            <th class="menor">Periodo Apura&ccedil;&atilde;o</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($this->view->consolidadoCliente) > 0):
                            $classeLinha = "par";
                            ?>

                            <?php foreach ($this->view->consolidadoCliente as $resultado) : ?>
                                <?php $classeLinha = ($classeLinha == "impar") ? "par" : "impar"; ?>
                                    <tr class="<?php echo $classeLinha; ?>" data-clioid="<?php echo $resultado->clioid; ?>" >
                                        
                                        <td class="centro nome"><?php echo $resultado->clinome; ?></td>
                                        <td class="centro total"><?php echo $resultado->total; ?></td>
                                        <td class="centro apuracao"><?php echo $resultado->min_apuracao; ?> &agrave; <?php echo $resultado->max_apuracao; ?></td>
                                    </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                <?php
                endif;
            ?>
        </table>
    </div>
</div>