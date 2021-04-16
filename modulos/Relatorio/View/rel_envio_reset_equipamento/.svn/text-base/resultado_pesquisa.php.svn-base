    <div class="bloco_titulo">Resultado da Pesquisa</div>
    <div class="bloco_conteudo">
        <?php echo $this->view->ordenacao; ?>
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="medio centro">Placa</th>
                    	<th class="menor centro">Cliente</th>
                        <th class="menor centro">Equipamento</th>
                        <th class="medio centro">Data de envio do comando</th>
                    	<th class="medio centro">Status Posição</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($this->view->dados as $dados):
                        $cor = ($cor=="par") ? "impar" : "par";
                        ?>
                        <tr class="<?php echo $cor; ?>">
                            <td class="centro"><?php echo $dados->veiplaca; ?></td>
                            <td class="esquerda"><?php echo $dados->clinome ?></td>
                            <td class="esquerda"><?php echo $dados->eprnome; ?></td>
                            <td class="centro"><?php echo $dados->data_envio_comando; ?></td>
                            <td class="centro"><?php echo $dados->status_posicao; ?></td>
                        </tr>
                    <?php
                        endForeach;
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="10">
                            <?php
                                if($this->view->totalResultados == "1"){
                                    echo "1 registro encontrado.";
                                } else{
                                    echo $this->view->totalResultados . " registros encontrados.";
                                }
                            ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <?php echo ($this->view->totalResultados > 10) ? $this->view->paginacao : ''; ?>
        </div>
    </div>