<div class="separador"></div>
<!-- <div class="resultado bloco_titulo">Resultado da Pesquisa</div> -->
<div class="resultado bloco_conteudo">
    <div id="bloco_itens" class="listagem">
        <table style="font-size: 9px;">
            <?php

                if (count($this->view->dados['OUTROS'])) : ?>
                    <thead>
                        <tr>
                            <th class="esquerda" colspan="20">
                                Dados por antena satelital
                            </th>
                        </tr>
                        <tr>
                            <th class="menor">Antena</th>
                            <th class="medio">Cliente</th>
                            <th class="medio">Contrato</th>
                            <th class="menor">Consumo (Kb)</th>
                            <th class="menor">Data apura&ccedil;&atilde;o</th>
                            <th class="menor">Placa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($this->view->dados) > 0):
                            $classeLinha = "par";
                            ?>

                            <?php foreach ($this->view->dados['OUTROS'] as $resultado) : ?>
                                <?php $classeLinha = ($classeLinha == "impar") ? "par" : "impar"; ?>
                                    <tr class="<?php echo $classeLinha; ?>" >
                                        
                                        <td class="centro"><?php echo $resultado->acsasatno_serie; ?></td>
                                        <td class="centro"><?php echo $resultado->acsclinome; ?></td>
                                        <td class="centro"><?php echo $resultado->acsconnumero; ?></td>
                                        <td class="centro"><?php echo $resultado->acsconsumo_operadora; ?></td>
                                        <td class="centro"><?php echo $resultado->acsdata_apuracao; ?></td>
                                        <td class="centro"><?php echo $resultado->acsveiplaca; ?></td>
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