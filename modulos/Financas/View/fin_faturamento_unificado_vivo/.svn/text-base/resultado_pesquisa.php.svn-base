		

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <input type="hidden" value="<?php echo (isset($this->view->parametros->parametrosFormatado) && !empty($this->view->parametros->parametrosFormatado) ? $this->view->parametros->parametrosFormatado : '') ?>" name="parametrosConsulta" />
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="selecao" id="quirksHack">
                        <input id="selecao_todos" type="checkbox" />
                    </th>
                    <th class="maior">Obrigação Financeira</th>
                    <th class="medio">Quantidade de Contratos</th>
                    <th class="menor">Valor Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($this->view->dados) > 0) : ?>
                    <?php $classeLinha = "par"; ?>
                    <?php $totalGeral = 0; ?>

                    <?php foreach ($this->view->dados as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                        <tr class="<?php echo $classeLinha; ?>">
                            <td class="centro">
                                <input class="selecao" type="checkbox" value="<?php echo $resultado->id_obrigacao; ?>" name="obrigacoesFaturar[]" />
                            </td>
                            <td class="esquerda"><?php echo $resultado->nome_obrigacao; ?></td>
                            <td class="direita"><?php echo $resultado->quantidade_contratos; ?></td>
                            <td class="direita">
                                <?php echo number_format($resultado->valor_faturamento, 2, ',', '.'); ?>
                                <?php $totalGeral += floatval($resultado->valor_faturamento); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="direita">
                        TOTAL GERAL: <?php echo number_format($totalGeral, 2, ',', '.'); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="centro">
                        <?php
                        $totalRegistros = count($this->view->dados);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="centro">
                        <button type="button" id="bt_gerarPlanilha">Gerar Planilha CSV</button>
                        <button type="button" id="bt_gerarRelatorio">Relatório Pré-Faturamento</button>
                        <button type="button" id="bt_gerarFaturamento">Gerar Faturamento</button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>