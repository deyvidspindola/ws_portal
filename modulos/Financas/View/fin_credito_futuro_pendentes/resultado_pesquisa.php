<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>


<?php
$status = array(
    '1' => 'apr_bom.gif',
    '3' => 'apr_neutro.gif',
    '4' => 'apr_ruim.gif',
    '2' => 'apr_cinza_claro.gif'
);
?>

<div class="bloco_conteudo">
    <div class="conteudo">
        <fieldset>
            <legend id="lgd_legenda_status">Status</legend>
            <ul>
                <li><img src="images/apr_bom.gif" alt="Concluído" /> Aprovado</li>
                <li><img src="images/apr_neutro.gif" alt="Pendente" /> Pendente</li>
                <li><img src="images/apr_ruim.gif" alt="Reprovado" /> Reprovado</li>
            </ul>
        </fieldset>
    </div>
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="td_excluir_todos"><input type="checkbox" id="marcar_todos" name="marcar_todos" value=""/></th>     
                    <th>Cód. Identif.</th>               
                    <th>Dt. Inclusão</th>
                    <th>Status</th>
                    <th>Cliente</th>
                    <th>CNPJ/CPF</th>
                    <th>Protocolo</th>
                    <th>Motivo do Crédito</th>
                    <th>% / Valor</th>
                    <th>Forma de Aplicação</th>
                    <th>Incluso por</th>
                    <th>Avaliado por</th>
                    <th style="width:100px">Ação</th>
                </tr>
            </thead>
            <tbody>	
                <?php foreach ($this->view->dados as $item) : ?>
                    <?php $class = $class == 'impar' ? 'par' : 'impar'; ?>
                    <tr class="<?php echo $class ?>">
                        <td class="td_excluit_item">
                            <?php if (!$item->ja_avaliado && $item->status_id == '3') : ?>
                                <input type="checkbox" class="excluir_item" name="analisar_item[]" value="<?php echo $item->cfooid; ?>" />
                            <?php endif; ?>
                        </td>
                        <td class="centro"><?php echo $item->cfooid; ?></td>
                        <td class="centro"><?php echo $item->dt_inclusao; ?></td>
                        <td class="centro"><img src="images/<?php echo $status[$item->status_id]; ?>" /></td>
                        <td><?php echo $item->cliente_nome; ?></td>
                        <td class="centro"><?php echo $item->doc; ?></td>
                        <td class="direita"><?php echo $item->protocolo; ?></td>
                        <td><?php echo $item->motivo_credito_descricao; ?></td>
                        <td class="direita"><?php echo $item->valor; ?></td>
                        <td><?php echo $item->forma_aplicacao_descricao; ?></td>
                        <td class="esquerda"><?php echo $item->usuario_inclusao_nome; ?></td>
                        <td class="esquerda"><?php echo $item->usuario_avaliador_nome; ?></td>

                        <td style="width:100px" class="centro">

                            <a href="fin_credito_futuro_pendentes.php?acao=visualizar&id=<?php echo $item->cfooid ?>" title="Visualizar">
                                <img class="icone" alt="Visualizar" src="images/detalhes.png">
                            </a>

                            <?php if (!$item->ja_avaliado  && $item->status_id == '3') : ?>
                                <a class="bt_aprovar" href="fin_credito_futuro_pendentes.php?acao=aprovar&id=<?php echo $item->cfooid ?>" title="Aprovar">
                                    <img class="icone" alt="Aprovar" src="images/aprovado.png">
                                </a>

                                <a class="bt_reprovar " href="javascript:void(0)" data-cfooid="<?php echo $item->cfooid; ?>" title="Reprovar">
                                    <img class="icone" alt="Reprovar" src="images/reprovado.png">
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

            <tfoot>
                <tr>
                    <td class="td_contador" colspan="16" class="centro">
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

<div class="bloco_acoes">
    <button id="bt_aprovar_massa" type="button" disabled="disabled">Aprovar</button>
</div>