<div class="separador"></div>
<div class="bloco_titulo">Resultado da Pesquisa</div>
<div class="bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th nowrap="nowrap">N&ordm; O.S.</th>
                    <th nowrap="nowrap">Tipo O.S.</th>
                    <th class="menor" nowrap="nowrap">Data Agenda</th>
                    <th nowrap="nowrap">UF</th>
                    <th class="medio" nowrap="nowrap">Cidade</th>
                    <th class="maior" nowrap="nowrap">Representante</th>
                    <th class="medio" nowrap="nowrap">Instalador</th>
                    <th class="medio" nowrap="nowrap">Data/Hora da Reserva</th>
                    <th class="menor" nowrap="nowrap">Status</th>
                    <th class="medio" nowrap="nowrap">Produto</th>
                    <th class="menor" nowrap="nowrap">Qtde. Estoque</th>
                    <th class="medio" nowrap="nowrap">Classe do Contrato</th>
                    <th nowrap="nowrap">Em Trânsito</th>
                    <th nowrap="nowrap">N&ordm; Remessa</th>
                    <th class="menor" nowrap="nowrap">Data Remessa</th>
                    <th class="medio" nowrap="nowrap">Usuário Reserva</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->view->dados->pesquisa as $indice => $registro) : ?>
                    <tr class="<?php echo ($indice + 1) % 2 == 0 ? 'par' : 'impar'; ?>">
                        <td class="direita"><?php echo $registro->ordem_servico; ?></td>
                        <td><?php echo $registro->tipo_os; ?></td>
                        <td class="centro"><?php echo $registro->dt_agenda ? date('d/m/Y', strtotime($registro->dt_agenda)) : ''; ?></td>
                        <td class="centro"><?php echo $registro->uf; ?></td>
                        <td><?php echo $registro->cidade; ?></td>
                        <td><?php echo $registro->representante; ?></td>
                        <td><?php echo $registro->instalador; ?></td>
                        <td class="centro"><?php echo date('d/m/Y H:i:s', strtotime($registro->dt_reserva)); ?></td>
                        <td><?php echo $registro->status; ?></td>
                        <td><?php echo $registro->produto; ?></td>
                        <td><?php echo $registro->qtde_estoque; ?></td>
                        <td><?php echo $registro->classe; ?></td>
                        <td><?php echo $registro->transito; ?></td>
                        <td class="direita"><?php echo $registro->remessa; ?></td>
                        <td class="centro"><?php echo $registro->dt_remessa; ?></td>
                        <td><?php echo $registro->usuario; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="17">
                        <?php if (count($this->view->dados->pesquisa) == 1) : ?>
                            1 registro encontrado.
                        <?php else : ?>
                            <?php echo count($this->view->dados->pesquisa); ?> registros encontrados.
                        <?php endif; ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>