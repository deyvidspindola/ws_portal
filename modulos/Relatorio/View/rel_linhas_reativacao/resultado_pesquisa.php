    <div class="bloco_titulo">Resultado da Pesquisa</div>
    <div class="bloco_conteudo">
        <?php echo $this->view->ordenacao; ?>
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="medio centro">Data</th>
                    	<th class="menor centro">Cliente</th>
                        <th class="menor centro">Linha</th>
                        <th class="menor centro">Status da Linha</th>
                        <th class="medio centro">Status do Contrato</th>
                    	<th class="medio centro">Tipo do Contrato</th>
                    	<th class="medio centro">Placa</th>
                    	<th class="menor centro">Contrato</th>
                    	<th class="medio centro"><?=utf8_decode('Usuário')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($this->view->dados as $dados):
                        $cor = ($cor=="par") ? "impar" : "par";
                    ?>
                        <tr class="<?php echo $cor; ?>">
                            <td class="centro"><?php echo $dados->data_cadastro; ?></td>
                            <td class="esquerda"><?php echo $dados->cliente ?></td>
                            <td class="esquerda"><?php echo $dados->ddd; ?> <?php echo $dados->linha; ?></td>
                            <td class="esquerda"><?php echo $dados->statuslinha; ?></td>
                            <td class="esquerda"><?php echo $dados->statuscontrato; ?></td>
                            <td class="esquerda"><?php echo $dados->tipocontrato; ?></td>
                            <td class="esquerda"><?php echo $dados->placa; ?></td>
                            <td class="esquerda"><?php echo $dados->contrato; ?></td>
                            <td class="esquerda"><?php echo $dados->usuario; ?></td>
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