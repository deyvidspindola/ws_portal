
    <div class="bloco_titulo">Resultado da Pesquisa</div>
    <div class="bloco_conteudo">
        <?php echo $this->view->ordenacao; ?>
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="medio centro">Data</th>
                    	<th class="menor centro">Nº Contrato</th>
                        <th class="medio centro">Vencimento</th>
                    	<th class="medio centro">Tipo Contrato</th>
                    	<th class="medio centro">Cliente</th>
                    	<th class="medio centro">Classe</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach($this->view->dados as $dados):
                        $cor = ($cor=="par") ? "impar" : "par";
                    ?>
                        <tr class="<?php echo $cor; ?>">
                            <td class="centro"><?php echo $dados->inicio_vigencia; ?></td>
                            <td class="direita">
                                <a href="contrato_servicos.php?connumero=<?php echo $dados->connumero; ?>" target="_NEW">
                                    <?php echo $dados->connumero; ?>
                                </a>
                            </td>
                            <td class="centro"><?php echo $dados->fim_vigencia; ?></td>
                            <td class="esquerda"><?php echo $dados->tipo_contrato; ?></td>
                            <td class="esquerda"><?php echo $dados->cliente; ?></td>
                            <td class="esquerda"><?php echo $dados->eqcdescricao; ?></td>
                        </tr>
                    <?php
                        endForeach;
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6">
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