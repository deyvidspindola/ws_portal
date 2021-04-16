		

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
                <li><img src="images/apr_cinza_claro.gif" alt="Aprovado" /> Concluído </li>
            </ul>
        </fieldset>
    </div>
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="td_excluir_todos"><input type="checkbox" id="marcar_todos" name="marcar_todos" value=""/></th>
                    <th width="5%">Código</th>
                    <th>Dt. Inclusão</th>
                    <th class="maior">Cliente</th>
                    <th width="5%">CNPJ/CPF</th>
                    <th>Protocolo</th>
                    <th>Obrig. Fin. Desc.</th>
                    <th>Motivo do Crédito</th>
                    <!-- <th>Tipo do Desconto</th> -->
                    <th>% / Valor</th>
                    <th>Forma de Aplicação</th>
                    <th>Inclusão</th>
                    <th>Saldo</th>
                    <th>Status</th>
                    <th>Usuário</th>
                    <th style="width:100px">Ação</th>
                </tr>
            </thead>
            <tbody>	
                <?php foreach ($this->view->dados as $item) : ?>
                    <?php $class = $class == 'impar' ? 'par' : 'impar'; ?>
                    <tr class="<?php echo $class ?>">
                        <td class="td_excluit_item">
                            <?php if (!$item->bloqueiaExcluir) : ?>
                                <input type="checkbox" class="excluir_item" name="excluir_item[]" value="<?php echo $item->cfooid; ?>" />
                            <?php endif; ?>
                        </td>

                        <td class="direita"><?php echo $item->cfooid; ?></td>
                        <td class="centro"><?php echo $item->cfodt_inclusao; ?></td>
                        <td class="esquerda"><?php echo $item->clinome; ?></td>
                        <td class="centro"><?php echo $item->clicpfcnpj; ?></td>
                        <td class="direita"><?php echo $item->cfoancoid; ?></td>
                        <td><?php echo $item->obrobrigacao; ?></td>
                        <td><?php echo wordwrap(trim($item->cfmcdescricao),15,'<br/>',true) ?></td>
                        <!-- <td><?php echo $item->cfotipo_desconto; ?></td> -->
                        <td class="direita"><?php echo trim($item->valorDesconto); ?></td>
                        <td><?php echo $item->cfoforma_aplicacao; ?></td>
                        <td><?php echo $item->cfoforma_inclusao; ?></td>
                        <td class="direita">
                            <?php $parcelas_label = $item->parcelas_ativas > 1 ? ' parcelas' : ' parcela' ?>
                            <?php echo $item->cfotipo_desconto_id == '2' ? $item->cfosaldo : $item->parcelas_ativas . $parcelas_label; ?>
                        </td>
                        <td class="centro"><img src="images/<?php echo $status[$item->cfostatus]; ?>" /></td>
                        <td class="esquerda"><?php echo $item->nm_usuario; ?></td>
                        <td style="width:100px" class="centro">

                            <a href="fin_credito_futuro.php?acao=editar&id=<?php echo $item->cfooid ?>" title="Alterar">
                                <img class="icone" alt="Alterar" src="images/edit.png">
                            </a>

                            <?php if (!$item->bloqueiaExcluir) : ?>
                                <a class="bt_excluir excluir_listagem" href="javascript:void(0)" data-cfooid="<?php echo $item->cfooid; ?>" title="Excluir">
                                    <img class="icone" alt="Excluir" src="images/icon_error.png">
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

            <tfoot>
                <tr>
                    <td class="td_contador" colspan="15" class="centro">
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
    <button id="bt_excluir_massa" type="button" disabled="disabled">Excluir</button>
</div>