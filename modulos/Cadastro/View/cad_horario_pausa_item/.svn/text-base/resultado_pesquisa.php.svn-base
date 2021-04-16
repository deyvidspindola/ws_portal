<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="centro">
                        <input <?php echo (!$this->view->acesso) ? 'disabled="disabled"' : '' ;?> title="marcar todos" type="checkbox" id="marcar_todos_top" class="inibir marcar_todos" name="marcar_todos_top" value="" />
                    </th>
                    <th id="th_grupo_de_trabalho" class="centro">Grupo de Trabalho</th>
                    <th id="th_tipo_de_pausa" class="centro">Tipo de Pausa</th>
                    <th id="th_atendente" class="centro">Atendente</th>
                    <th id="th_tempo" class="centro">Tempo</th>
                    <th id="th_horario" class="centro">Horário</th>
                    <th id="th_tolerancia" class="centro">Tolerância</th>
                    <th id="th_acao" class="acao">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($this->view->dados) > 0):
                    $classeLinha = "par";
                ?>

                    <?php foreach ($this->view->dados as $resultado) : ?>
                    <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                <tr class="<?php echo $classeLinha; ?>">
                    <td class="centro">
                        <input <?php echo (!$this->view->acesso) ? 'disabled="disabled"' : '' ;?> type="checkbox" class="inibir" name="inibir[]" value="<?php echo $resultado->hrpioid ?>" />
                    </td>
                    <td><?php echo $resultado->grupo_trabalho ?></td>
                    <td><?php echo $resultado->tipo_pausa ?></td>
                    <td><?php echo $resultado->atendente ?></td>
                    <td class="direita"><?php echo $resultado->tempo ?> minutos</td>
                    <td class="centro"><?php echo $resultado->horario ?></td>
                    <td class="direita"><?php echo $resultado->tolerancia ?> minutos</td>
                    <td class="centro">
                    	<?php if ($this->view->acesso): ?>
                        <span>
                            <a class="editar" id="<?php echo $resultado->hrpioid ?>" href="javascript:void(0);"><img class="icone" src="<?php echo _PROTOCOLO_ . _SITEURL_ ?>images/edit.png" title="Editar"></a>
                            <a class="excluir" id="<?php echo $resultado->hrpioid ?>" href="javascript:void(0);"><img class="icone" src="<?php echo _PROTOCOLO_ . _SITEURL_ ?>images/icon_error.png" title="Excluir"></a>
                        </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="centro">
                        <input <?php echo (!$this->view->acesso) ? 'disabled="disabled"' : '' ;?> title="marcar todos" type="checkbox" id="marcar_todos_bottom" class="inibir marcar_todos" name="marcar_todos_bottom" value="" />
                    </td>
                    <td colspan="7" class="centro">
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
    <?php if ($this->view->acesso): ?>
    <button name="bt_inibir" id="bt_inibir" type="button">Inibir Marcados</button>
    <?php endif; ?>
</div>