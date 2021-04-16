<?php
require './WS_Fox/includes/NotaFiscalManualView.php';
require './WS_Fox/Action/PedidoNotaFiscal.class.php';
require './WS_Fox/dao/PedidoNotaFiscalDAO.class.php';

require './WS_Fox/dao/ParametrosDAO.class.php';
require './WS_Fox/Action/Parametros.class.php';

?>

<script>
    function gerarRelatorio() {

        document.form.target = "_blank";
        document.form.action = "modulos/Financas/View/fin_controle_fiscal_remessas/resultado_equipamento_movel_csv.php";
        document.form.submit();
        document.form.target = "";
        document.form.action = "";
    }
</script>

<?php $result = $this->view->equipamentoMovel; ?>

<div class="bloco_titulo">Resultado Pesquisa</div>
<div class="bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <?php if ($result[2]['aba'] === 'envio') { ?>
                        <th class="centro">Enviado</th>
                    <?php } else { ?>
                        <th class="centro">Retorno</th>
                    <?php } ?>

                    <?php if ($result[2]['tipo_relatorio'] === 'serial') { ?>

                        <th class="centro">Contrato</th>
                        <th class="centro">N&ordm; S&eacute;rie</th>

                    <?php } ?>

                    <th class="centro">C&oacute;digo/Produto</th>
                    <th class="centro">NCM</th>
                    <th class="centro">Qtde</th>
                    <th class="centro">Valor</th>
                    <th class="centro">Cliente</th>
                    <th class="centro">CPF/CNPJ</th>
                    <th class="centro">UF</th>
                    <th class="centro">NF Remessa</th>
                    <th class="centro">A&ccedil;&otilde;es</th>
                    <th class="centro">N&ordm; Pedido</th>
                </tr>
            </thead>
            <tbody>
                <?php
               

                    while ($row = pg_fetch_object($result[0])) {$x++;

                        $class = $class == '' ? 'par' : '';
                        ?>						
                        <tr class="<?= $class ?>">
                            <td><?= $row->data_envio; ?></td>

                            <?php if ($result[2]['tipo_relatorio'] === 'serial') { ?>

                                <td><?= $row->contrato; ?></td>
                                <td><?= $row->serie; ?></td>

                            <?php } ?>

                                <td><?= $row->codigo_produto . " - " . utf8_encode($row->descricao_produto); ?></td>
                            <td><?= $row->codigo_ncm; ?></td>
                            <td><?= $row->quantidade; ?></td>
                            <td align="right">

                                <?php
                                $precoUnitario = '';

                                if ($row->preco_unitario_1) {
                                    $precoUnitario = $row->preco_unitario_1;
                                } else if ($row->preco_unitario_2) {
                                    $precoUnitario = $row->preco_unitario_2;
                                } else {
                                    $precoUnitario = $row->preco_unitario_3;
                                }
                                ?>

                                <?= 'R$' . number_format($precoUnitario, 2, ',', '.'); ?>

                            </td>
                            <td><?= utf8_encode($row->nome_cliente); ?></td>
                            <td><?= formata_cgc_cpf($row->cliente_cnpj)?></td>
                            <td><?= $row->cliente_uf; ?></td>
                            <td align="center"><?= $row->nf_remessa; ?></td>
                            <td align="center" nowrap>

                                <?php
                               
                                    if($row->numero_pedido>0){
                                        $pedido=new PedidoNotaFiscal();
                                        $urlpdf=$pedido->getPdfNotaURL($row->numero_pedido);
                                        if($urlpdf!==false){?>
                                            <a href="<?=$urlpdf;?>" target="_blank"><img src="images/impr2.gif" title="Imprimir NF"></a>
                                         
                                <?}}?>

                                <?php if ($row->numero_pedido) { ?>
                                    <a href="javascript:dialogo('<?= $row->numero_pedido; ?>', false)"><img width="20" height="20" src="images/edit.png" title="Alterar NF"></a>
                                <?php } ?>

                            </td>
                            <td><?= $row->numero_pedido; ?></td>
                        </tr>			
                        <?php
                    }?>
               
            </tbody>
            <tfoot>
                <tr class="center">
                    <td align="center" colspan="14">
                        <?php
                        echo ($x > 0) ? $x. ' registros encontrados.' : '0 registro encontrado.';
                        ?>
                    </td>
                </tr>
                <tr class="center">
                    <td align="center" colspan="14">
                        <button type="button" id="gerarCsv" onclick="gerarRelatorio();">Gerar CSV</button>
                    </td>
                </tr>         
            </tfoot>
        </table>
    </div>
</div>


