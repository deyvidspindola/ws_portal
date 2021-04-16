<?php
include 'WS_Fox/includes/NotaFiscalManualView.php';

?>

<script>
    function gerarRelatorio() {

        document.form.target = "_blank";
        document.form.action = "modulos/Financas/View/fin_controle_fiscal_remessas/resultado_retiradas_csv.php";
        document.form.submit();
        document.form.target = "";
        document.form.action = "";
    }
</script>

<div class="bloco_titulo">Resultado Pesquisa</div>
<div class="bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>                    
                    <th class="centro">Data</th>                    
                    <th class="centro">OS</th>                    
                    <th class="centro">Contrato</th>
                    <th class="centro">Cliente</th>
                    <th class="centro">CNPJ</th>
                    <th class="centro">Representante</th>
                    <th class="centro">CNPJ</th>
                    <th class="centro">Tipo Pedido</th>
                    <th class="centro">N&ordm; Pedido</th>
                    <th class="centro">N&ordm; Nota</th>
                    <th class="centro">A&ccedil;&otilde;es</th>
                </tr>
            </thead>
            <tbody>
                <?php
              

                    while ($row = pg_fetch_object($this->view->retiradas)) {$x++;

                        $class = $class == '' ? 'par' : '';
                        ?>						
                        <tr class="<?= $class ?>">

                            <td><?=$row->dataos;?></td>
                            <td><?=$row->id_os; ?></td>
                            <td><?=$row->contrato; ?></td>
                            <td><?=utf8_encode($row->nome_cliente); ?></td>
                            <td><?=formata_cgc_cpf($row->cliente_cnpj); ?></td>
                            <td><?=utf8_encode($row->representante_nome); ?></td>
                            <td><?=formata_cgc_cpf($row->representante_cnpj); ?></td>
                            <td><?=$row->tipo_pedido; ?></td>
                            <td><?=$row->numero_pedido; ?></td>
                            <td><?=$row->pnfnf_numero; ?></td>
                            <td align="center">
                                <? if ($row->numero_pedido) { ?>
                                <a href="javascript:dialogo('<?= $row->numero_pedido; ?>', false)"><img width="20" height="20" src="images/edit.png" title="Alterar NF"></a>
                                <?}?>
                            </td>
                        </tr>			
                        <?}?>
            </tbody>
            <tfoot>
                <tr class="center">
                    <td align="center" colspan="14">
                        <?=($x > 0) ? $x . ' registros encontrados.' : '0 registro encontrado.';?>
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