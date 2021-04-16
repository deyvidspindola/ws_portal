		

<div class="separador"></div>
<div class="resultado bloco_titulo">Relatório de Pré-faturamento</div>
<div class="resultado bloco_conteudo">

    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="menor">Nome do Cliente</th>
                    <th class="menor">Número do Contrato</th>
                    <th class="menor">Tipo do Contrato</th>
                    <th class="menor">Data de Início da Vigência</th>
                    <th class="menor">Ciclo</th>
                    <th class="menor">Situação do Contrato</th>
                    <th class="menor">Placa do Veículo</th>
                    <th class="menor">Equipamento</th>
                    <th class="menor">Subscription Id</th>
                    <th class="menor">Conta do Cliente</th>
                    <th class="menor">Obrigação Financeira</th>
                    <th class="menor">Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($this->view->dadosPreFaturamento) > 0) : ?>
                    <?php $classeLinha = "par"; ?>
                    <?php $totalGeral = 0; ?>

                    <?php foreach ($this->view->dadosPreFaturamento as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>

                        <tr class="<?php echo $classeLinha; ?>">
                            <td class="esquerda"><?php echo $resultado->nome_cliente ?></td>
                            <td class="direita"><?php echo $resultado->numero_contrato ?></td>
                            <td class="esquerda"><?php echo $resultado->tipo_contrato ?></td>
                            <td class="centro"><?php echo $resultado->data_inicio_vigencia ?></td>
                            <td class="centro"><?php echo $resultado->ciclo_faturamento ?></td>
                            <td class="esquerda"><?php echo $resultado->situacao_contrato ?></td>
                            <td class="direita"><?php echo $resultado->placa_veiculo ?></td>
                            <td class="esquerda"><?php echo $resultado->nome_equipamento ?></td>
                            <td class="esquerda"><?php echo $resultado->subscription_id ?></td>
                            <td class="esquerda"><?php echo $resultado->conta ?></td>
                            <td class="esquerda"><?php echo $resultado->nome_obrigacao ?></td>
                            <td class="direita"><?php echo number_format($resultado->valor_total, 2, ',', '.'); ?></td>
                            <?php $totalGeral += floatval($resultado->valor_total); ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="12" class="direita">
                        TOTAL GERAL: <?php echo number_format($totalGeral, 2, ',', '.'); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="12" class="centro">
                        <?php
                        $totalRegistros = count($this->view->dadosPreFaturamento);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>