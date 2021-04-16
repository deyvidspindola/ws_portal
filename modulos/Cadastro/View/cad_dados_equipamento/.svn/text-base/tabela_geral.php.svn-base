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
                                Outros Grupos Equipamentos
                            </th>
                        </tr>
                        <tr>
                            <th class="menor">Placa</th>
                            <!--<th class="medio">Chassi</th>-->
                            <th class="medio">Cliente</th>

                            <th class="menor">Eq Esn</th>
                            <th class="menor">Eq Versão</th>
                            <th class="menor">Eq Projeto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($this->view->dados) > 0):
                            $classeLinha = "par";
                            ?>

                            <?php foreach ($this->view->dados['OUTROS'] as $resultado) : ?>
                                <?php $classeLinha = ($classeLinha == "impar") ? "par" : "impar"; ?>
                                    <tr class="<?php echo $classeLinha; ?>" data-equesn="<?php echo $resultado->equesn; ?>" >
                                        
                                        <td class="centro"><?php echo $resultado->veiplaca; ?></td>
                                        <td class="centro"><?php echo $resultado->clinome; ?></td>
                                        <td class="centro"><?php echo $resultado->equesn; ?></td>
                                        <td class="centro"><?php echo $resultado->eveversao; ?></td>
                                        <td class="centro eprnome"><?php echo $resultado->eprnome; ?></td>
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