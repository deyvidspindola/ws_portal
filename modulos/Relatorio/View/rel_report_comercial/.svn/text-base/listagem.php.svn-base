<?php $reportComercial = isset($this->view->dados->reportComercial) ? $this->view->dados->reportComercial : array(); ?>
<?php if (is_array($reportComercial) && count($reportComercial)) : ?>
    <div class="separador"></div>
    <form id="form_listagem" method="post">
        <input id="acao" type="hidden" name="acao" value="excluirReportComercial" />
        <div class="bloco_titulo">Relatórios Concluídos</div>
        <div class="bloco_conteudo">
            <div class="listagem">
                <table>
                    <thead>
                        <tr>
                            <th class="selecao">
                                <input id="marcar_todos_top" type="checkbox" title="Marcar todos" />
                            </th>
                            <th>Arquivo</th>
                            <th class="medio">Usuário Solicitante</th>
                            <th class="medio">Data de Solicitação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportComercial as $indice => $registro) : ?>
                            <tr class="<?php echo ($indice + 1) % 2 == 0 ? 'par' : 'impar'; ?>">
                                <td class="centro">
                                    <?php if ($registro->rpcprocessando == 'f') : ?>
                                        <input id="rpcoid_<?php echo $registro->rpcoid; ?>" type="checkbox" name="rpcoid[]" value="<?php echo $registro->rpcoid; ?>" class="rpcoid"/>
                                    <?php else : ?>
                                        <img src="modulos/web/images/ajax-loader-circle.gif" class="icone" />
                                    <?php endif; ?>
                                </td>
                                <td class="esquerda">
                                    <?php if(trim($registro->rpcarquivo) == '') : ?>
                                        <span style="color: #666666"><?php echo self::MENSAGEM_ARQUIVO_NA_FILA; ?></span>
                                    <?php elseif ($registro->rpcarquivo == self::MENSAGEM_ARQUIVO_SEM_REGISTRO) : ?>
                                        <span style="color: #a47e3c"><?php echo self::MENSAGEM_ARQUIVO_SEM_REGISTRO; ?></span>
                                    <?php elseif ($registro->rpcarquivo == self::MENSAGEM_ARQUIVO_COM_ERRO) : ?>
                                        <span style="color: #953b39"><?php echo self::MENSAGEM_ARQUIVO_COM_ERRO; ?></span>
                                    <?php else : ?>
                                        <a href="download.php?arquivo=<?php echo '/var/www/docs_temporario/'.$registro->rpcarquivo; ?>" target="_blank"><?php echo $registro->rpcarquivo; ?></a>
                                    <?php endif; ?>
                                </td>
                                <td class="esquerda"><?php echo $registro->nm_usuario; ?></td>
                                <td class="centro"><?php echo date('d/m/Y', strtotime($registro->rcpdt_solicitacao)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">
                                <?php if (count($reportComercial) == 1) : ?>
                                    1 registro encontrado.
                                <?php else : ?>
                                    <?php echo count($reportComercial); ?> registros encontrados.
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="bloco_acoes">
            <button id="bt_excluir" type="submit" disabled="disabled" >Excluir</button>
        </div>
    </form>
<?php endif; ?>