<?php $totalRegistros = $this->view->quantidade; ?>
<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <?php echo $this->view->ordenacao; ?>
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="maior">Cliente</th>
                    <th class="menor">Placa</th>
                    <th class="menor">Projeto</th>
                    <th class="menor">Modalidade</th>
                    <th class="menor">Tipo</th>
                    <th class="menor">Defeito Alegado</th>
                    <th class="menor">O.S.</th>
                    <th class="menor">Status</th>
                    <th class="menor">Data Posição OS</th>
                    <th class="menor">Data Abertura OS</th>
                    <th class="menor">Data Posição Atual</th>
                    <th class="menor">Data Agendada</th>
                    <th class="menor">Ação</th>
                    <th class="menor">Motivo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->view->dados as $indice => $resultado) : ?>
                    <tr class="<?php echo ($indice + 1) % 2 == 0 ? 'par' : 'impar'; ?>">
                        <td class="esquerda"><?php echo $resultado->clinome; ?></td>
                        <td class="esquerda"><?php echo $resultado->veiplaca; ?></td>
                        <td class="esquerda"><?php echo $resultado->eprnome; ?></td>
                        <td class="esquerda"><?php echo $resultado->conmodalidade; ?></td>
                        <td class="esquerda"><?php echo $resultado->tpcdescricao; ?></td>
                        <td class="esquerda"><?php echo $resultado->osdfdescricao; ?></td>
                        <td class="direita"><a href="prn_ordem_servico.php?ESTADO=cadastrando&acao=editar&ordoid=<?php echo $resultado->ordoid ?>" target="_blank"><?php echo $resultado->ordoid; ?></a></td>
                        <td class="esquerda"><?php echo $resultado->ossdescricao; ?></td>
                        <td class="centro"><?php echo $resultado->veipdata_os; ?></td>
                        <td class="centro"><?php echo $resultado->orddt_ordem; ?></td>
                        <td class="centro"><?php echo $resultado->veipdata_atual; ?></td>
                        <td class="centro"><?php echo $resultado->osdata; ?></td>
                        <?php if (empty($resultado->aoamdescricao_acao) && empty($resultado->aoamdescricao_motivo)) : ?>
                            <td colspan="2" class="esquerda" id="coluna_acao_<?php echo $resultado->ordoid ?>">
                                <a href="#" class="acao_motivo" rel="<?php echo $resultado->ordoid ?>" projeto="<?php echo $resultado->eproid ?>" posicao="<?php echo $resultado->veipdata_atual ?>">Selecione Ação e Motivo</a>
                            </td>
                        <?php else : ?>
                            <td class="esquerda"><?php echo $resultado->aoamdescricao_acao; ?></td>
                            <td class="esquerda"><?php echo $resultado->aoamdescricao_motivo; ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="14" class="centro">
                        <?php echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.'; ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php echo $this->view->paginacao; ?>
</div>