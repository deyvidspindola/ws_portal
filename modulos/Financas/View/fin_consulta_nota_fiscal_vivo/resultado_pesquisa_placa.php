<div class="separador"></div>
<div class="bloco_titulo">Resultado </div>
<div class="bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="centro">NF/ Série</th>
                    <th class="menor centro">Cod. Cliente</th>
                    <th class="maior centro">Cliente</th>
                    <th class="menor centro">Vencimento</th>
                    <th class="menor centro">Placa</th>
                    <th class="menor centro">Contrato</th>
                    <th class="menor centro">Mês / Ano Referência</th>
                    <th class="menor centro">Ciclo</th>
                    <th class="menor centro">Retorno VIVO</th>
                    <th class="menor centro">Valor Item NF</th>
                    <th class="menor centro">Valor VIVO</th>
                    <th class="medio centro">Status SASCAR</th>
                    <th class="medio centro">Status VIVO</th>
                    <th class="medio centro">Obrigação Financeira</th>
                </tr>
            </thead>
            <tbody>
                <?php
$somaValorItemNf = 0;
$somaValorVivo = 0;

foreach ($this->view->dados as $item) {
    $class = $class == 'impar' ? 'par' : 'impar';
                ?>
                    <tr class="<?php echo $class ?>">
                        <td nowrap><?php echo $item->nf_serie; ?></td>
                        <td class="direita"><?php echo $item->codigo_cliente; ?></td>
                        <td><?php echo $item->cliente; ?></td>
                        <td class="centro"><?php echo $item->vencimento; ?></td>
                        <td><?php echo $item->placa; ?></td>
                        <td class="direita"><?php echo $item->contrato; ?></td>
                        <td class="centro"><?php echo $item->data_referencia; ?></td>
                        <td class="direita"><?php echo $item->ciclo; ?></td>
                        <td class="centro"><?php echo $item->retorno_vivo; ?></td>
                        <td class="direita"><?php echo number_format($item->valor_item_nf,2,",","."); ?></td>
                        <td class="direita"><?php echo number_format($item->valor_vivo,2,",","."); ?></td>
                        <td><?php echo $item->status_sascar; ?></td>
                        <td><?php echo $item->status_vivo; ?></td>
                        <td><?php echo $item->obrigacao_financeira; ?></td>
                    </tr>
                <?php
    $somaValorItemNf += $item->valor_item_nf;
    $somaValorVivo += $item->valor_vivo;
}
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="9" class="direita">Total</td>
                    <td class="direita"><?php echo number_format($somaValorItemNf,2,",","."); ?></td>
                    <td class="direita"><?php echo number_format($somaValorVivo,2,",","."); ?></td>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="14" class="centro">
                        <?php
$totalRegistros = count($this->view->dados);
echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="separador"></div>

<?php if ($this->view->csv !== false) { ?>
    <div class="bloco_titulo">Download</div>
    <div class="bloco_conteudo">
        <div class="conteudo centro">
            <a target="_blank" href="download.php?arquivo=<?php echo $this->view->csv ?>">
                <img src="images/icones/t3/caixa2.jpg">
                <br>
                Consulta Nota Fiscal - VIVO
                <?php //echo basename($this->view->csv) ?>
            </a>
        </div>
    </div>

    <?php
}
?>