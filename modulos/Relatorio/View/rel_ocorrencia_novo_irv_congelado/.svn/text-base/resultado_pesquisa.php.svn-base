<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <?php if ($this->view->parametros->ococdtipo_relatorio != 'EX') : ?>
                        <th class="centro">
                                <input title="Marcar todos" id="marcar_todos" type="checkbox">
                        </th>
                    <?php endif; ?>

                    <th class="medio centro">Período</th>
                    <th class="menor centro">Tipo</th>
                    <th class="menor centro">Data/Hora Congelamento</th>
                    <th class="maior centro">Congelado por</th>
                    <th class="menor centro">Data/Hora Exclusão</th>
                    <th class="medio centro">Excluído por</th>
                    <th class="menor centro">Reativar</th>

                    <?php if ($this->view->parametros->ococdtipo_relatorio != 'EX') : ?>
                        <th class="acao">Ação</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->dados) > 0):
                    $classeLinha = "par";
                    ?>

                    <?php foreach ($this->view->dados as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
							<tr class="<?php echo $classeLinha; ?>">

                                <?php if ($this->view->parametros->ococdtipo_relatorio != 'EX') : ?>
                                    <td class="centro">
                                        <?php if (trim($resultado->ococddata_exclusao) == '') : ?>
                                            <?php $checked = isset($this->view->parametros->item[$resultado->ococdoid]) ? 'checked="checked"' : ''; ?>
                                            <input <?php echo $checked ?> type="checkbox" class="selecionar_check" name="item[<?php echo $resultado->ococdoid ?>]" value="<?php echo $resultado->tipo_sigla ?>">
                                        <?php endif; ?> 
                                    </td>
                                <?php endif; ?> 

								<td class="centro"><?php echo $resultado->ococdperiodo_inicial . ' a ' . $resultado->ococdperiodo_final; ?></td>
								<td class="esquerda"><?php echo $resultado->ococdtipo_relatorio; ?></td>
								<td class="centro"><?php echo $resultado->ococddata_congelamento; ?></td>
								<td class="esquerda"><?php echo $resultado->usuario_inclusao; ?></td>
								<td class="centro"><?php echo $resultado->ococddata_exclusao; ?></td>
								<td class="esquerda"><?php echo $resultado->usuario_exclusao; ?></td>

								<td class="acao centro">
                                    <?php if (trim($resultado->ococddata_exclusao) != '') : ?>
                                        <a class="reativar" href="rel_ocorrencia_novo_irv_congelado.php?acao=reativar&ococdoid=<?php echo $resultado->ococdoid ?>" title="Reativar">
                                            Reativar
                                        </a>
                                    <?php endif; ?>
                                </td>

                                <?php if ($this->view->parametros->ococdtipo_relatorio != 'EX') : ?>
                                    <td class="acao centro">
                                        <?php if (trim($resultado->ococddata_exclusao) == '') : ?>
                                            <a class="excluir" href="rel_ocorrencia_novo_irv_congelado.php?acao=excluir&ococdoid=<?php echo $resultado->ococdoid ?>" title="Excluir">
                                                <img alt="Excluir" src="images/icon_error.png" class="icone">
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>

							</tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <?php $colspan = $this->view->parametros->ococdtipo_relatorio != 'EX' ? 9 : 7 ?>
                    <td colspan="<?php echo  $colspan ?>" class="centro">
                        <?php
                        $totalRegistros = count($this->view->dados);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
                <tr>
                    <?php if ($this->view->parametros->ococdtipo_relatorio != 'EX') : ?>
                        <td colspan="9" class="centro">
                            <button disabled="disabled" id="visualizar_relatorio">Visualizar Relatório</button>
                        </td>
                    <?php endif; ?>
                </tr>
            </tfoot>
        </table>
    </div>
</div>