 <div class="separador"></div>
    <div class="bloco_titulo">Resultado da Pesquisa</div>
    <div class="bloco_conteudo">
        <div class="listagem">

            <table>
                <thead>
                    <tr>
                        <th class="medio">Atendente</th>
                        <th class="medio">Representante</th>
                        <th class="medio">Instalador</th>
                        <th class="menor">Data Cadastro</th>
                        <th class="maior">Motivo</th>
                        <th class="acao">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($this->view->dados as $registro) :
                            $cor = ($cor=="par") ? "impar" : "par";
                    ?>
                        <tr class="<?php echo $cor; ?>">
                            <td><?php echo $registro->atendente; ?></td>
                            <td><?php echo $registro->representante; ?></td>
                            <td><?php echo $registro->instalador; ?></td>
                            <td class="centro"><?php echo $registro->data_cadastro; ?></td>
                            <td><?php echo $registro->motivo; ?></td>
                            <td class="centro">
                                <?php if($registro->status == 'A') : ?>
                                    <img id="excluir_<?php echo $registro->aproid; ?>" data-aproid="<?php echo $registro->aproid; ?>" class="icone hand excluir" src="images/icon_error.png" title="Excluir" />
                                <?php endIf;?>
                            </td>
                        </tr>
                    <?php endForeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6">
                            <?php if($this->view->dados[0]->total_registros == "1"){
                                echo "1 registro encontrado.";
                            } else{
                                echo $this->view->dados[0]->total_registros . " registros encontrados.";
                            }
                            ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>