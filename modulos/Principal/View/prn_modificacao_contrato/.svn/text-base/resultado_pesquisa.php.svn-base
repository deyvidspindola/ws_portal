    <div class="bloco_titulo">Resultado da Pesquisa</div>
    <div class="bloco_conteudo">
        <?php echo $this->view->ordenacao; ?>
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="medio centro">Data</th>
                    	<th class="menor centro">Nº</th>
                        <th class="medio centro">Status Modificação</th>
                        <th class="medio centro">Status Financeiro</th>
                    	<th class="medio centro">Grupo Modificação</th>
                    	<th class="medio centro">Tipo Modificação</th>
                    	<th class="medio centro">Cliente</th>
                    	<th class="menor centro">Contrato</th>
                    	<th class="menor centro">O.S.</th>
                    	<th class="medio centro">Status O.S.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($this->view->dados as $dados):
                        $cor = ($cor=="par") ? "impar" : "par";
                    ?>
                        <tr class="<?php echo $cor; ?>">
                            <td class="centro"><?php echo $dados->data_cadastro; ?></td>
                            <td class="direita">
                                <a href="prn_modificacao_contrato.php?acao=detalhar&sub_tela=aba_dados_principais&mdfoid=<?php echo $dados->mdfoid; ?>">
                                    <?php echo $dados->mdfoid; ?>
                                </a>
                            </td>
                            <td class="esquerda"><?php echo $this->view->legenda_status[$dados->mdfstatus]; ?></td>
                            <td class="esquerda"><?php echo $this->view->legenda_status_financeiro[$dados->mdfstatus_financeiro]; ?></td>
                            <td class="esquerda"><?php echo $dados->grupo; ?></td>
                            <td class="esquerda"><?php echo $dados->tipo; ?></td>
                            <td class="esquerda"><?php echo $dados->cliente; ?></td>
                            <td class="direita">
                                <a href="prn_modificacao_contrato.php?acao=listarContratos&connumero=<?php echo $dados->connumero; ?>" target="_NEW">
                                    <?php echo $dados->connumero; ?>
                                </a>
                            </td>
                            <td class="direita"><?php echo $dados->cmfordoid; ?></td>
                            <td class="esquerda"><?php echo $dados->ordstatus; ?></td>
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