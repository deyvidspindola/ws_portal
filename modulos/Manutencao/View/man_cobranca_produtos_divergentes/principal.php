    <div class="bloco_titulo">Produtos</div>
    <div class="bloco_conteudo">
        <div id="bloco_produtos_divergentes" class="listagem">
            <form action="man_cobranca_produtos_divergentes.php" id="form_cobrar_produtos" method="post">
                <input id="invoid" name="invoid" type="hidden" value="<?php echo  $this->view->parametros->invoid; ?>">
                <input id="produtos" name="produtos" type="hidden" value="">
                <input id="acao" name="acao" type="hidden" value="inserirDesconto">
                <table>
                    <thead>
                        <tr>
                            <th class="medio acao">
                                <input id="selecao_todos" type="checkbox" data-valor="0" checked="true" />
                            </th>
                        	<th class="menor centro">Cód. Produto</th>
                            <th class="maior centro">Produto</th>
                            <th class="menor centro">Qtd. Estoque</th>
                        	<th class="menor centro">Qtd. Inventariada</th>
                        	<th class="menor centro">Qtd. Divergente</th>
                        	<th class="menor centro">Valor Produto</th>
                        	<th class="menor centro">Valor a Cobrar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $totalResultados = 0;

                            foreach ($this->view->dados as $dados):

                            $totalResultados++;
                            $cor = ($cor=="par") ? "impar" : "par";
                        ?>
                            <tr class="<?php echo $cor; ?>">
                                <td class="centro">
                                    <?php if( $dados->possui_custo == 't' ): ?>
                                    <input id="<?php echo $dados->prdoid ?>" type="checkbox" checked="true" value="<?php echo $dados->prdoid ?>"
                                            name="opcao[]" data-valor="<?php echo $dados->valor_cobrar ?>" />
                                    <?php endIf; ?>
                                </td>
                                <td class="direita"><?php echo $dados->prdoid; ?></td>
                                <td class="esquerda"><?php echo $dados->produto; ?></td>
                                <td class="direita"><?php echo $dados->estoque; ?></td>
                                <td class="direita"><?php echo $dados->contagem; ?></td>
                                <td class="direita"><?php echo $dados->divergente; ?></td>
                                <td class="direita"><?php echo $dados->custo_medio; ?></td>
                                <td class="direita"><?php echo $dados->valor_cobrar; ?></td>
                            </tr>
                        <?php
                            endForeach;
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="esquerda" colspan="7">Total p/ cobrança</td>
                            <td id="total_cobranca" class="direita">
                                <?php echo $this->view->dados[0]->total_cobrar; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="esquerda" colspan="7">Total divergente</td>
                            <td class="direita">
                                <?php echo $this->view->dados[0]->total_cobrar; ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="8">
                                <?php
                                    if($totalResultados == 1){
                                        echo "1 registro encontrado.";
                                    } else {
                                        echo $totalResultados . " registros encontrados.";
                                    }
                                ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </form>
          </div>
    </div>

    <div class="bloco_acoes">
        <?php if($this->view->dados[0]->cobranca_divergente != 't'): ?>
            <button type="button" id="btn_confirmar_cobranca">Confirmar Cobrança</button>
        <?php endIf; ?>
        <button type="button" id="btn_voltar">Voltar</button>
    </div>
