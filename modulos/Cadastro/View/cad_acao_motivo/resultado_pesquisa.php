		

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultados da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="menor">Tipo</th>
                    <th>Descrição</th>
                    <th class="menor">Cadastro</th>
                    <th class="menor">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->dados) > 0):
                    $classeLinha = "par";
                    $totalRegistros = 0;
                    ?>
                    <!-- novo --->    
                    <!--loop de motivos ações-->
                    <?php foreach ($this->view->dados as $key => $acao) : ?>

                        <!--Printo os dados da ação-->

                        <tr class="par">
                            <td><?php echo $acao['tipo']; ?></td>
                            <td><?php echo $acao['acao']; ?></td>
                            <td class="centro"><?php echo $acao['cadastro']; ?></td>
                            <td class="centro">
                                <a class="editar" href="#" rel="<?php echo $key; ?>"><img src="images/edit.png" title="Editar" class="icone" /></a>
                                <?php if (count($acao['motivos']) == 0) : ?>
                                    <a class="deletar" href="#" rel="<?php echo $key; ?>"><img src='images/icon_error.png' title='Excluir' class="icone" /></a>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <!--Verifico se a ação possui motivos-->
                        <?php if (count($acao['motivos']) > 0) : ?>

                            <!--Printo o primeiro registro-->

                            <tr class="">
                                <td rowspan="<?php echo count($acao['motivos']) ?>"><?php echo $acao['motivos'][0]['tipo']; ?></td>
                                <td><?php echo $acao['motivos'][0]['motivo']; ?></td>
                                <td class="centro"><?php echo $acao['motivos'][0]['cadastro']; ?></td>
                                <td class="centro">
                                    <a class="deletarMotivo" href="#" rel="<?php echo $acao['motivos'][0]['motivoID']; ?>"><img src='images/icon_error.png' title='Excluir' class="icone" /></a>
                                </td>
                            </tr>
                            <?php $totalRegistros++; ?>

                            <?php unset($acao['motivos'][0]); ?>
                            <!-- Se possui motivos, loop mottivos-->

                            <?php if (count($acao['motivos']) > 0) : ?>

                                <?php foreach ($acao['motivos'] as $key => $motivo) : ?>

                                    <!--Printo os dados dos motivo-->
                                    <tr class="">
                                        <td colspan="1"><?php echo $motivo['motivo']; ?></td>
                                        <td class="centro"><?php echo $motivo['cadastro']; ?></td>
                                        <td class="centro">
                                            <a class="deletarMotivo" href="#" rel="<?php echo $motivo['motivoID']; ?>"><img src='images/icon_error.png' title='Excluir' class="icone" /></a>
                                        </td>
                                    </tr>
                                    <?php $totalRegistros++; ?>
                                <?php endforeach; ?>

                            <?php endif; ?>

                        <?php endif; ?>

                        <?php $totalRegistros++; ?>            
                    <?php endforeach; ?>
                <?php endif; ?>

            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="centro">
                        <?php
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>