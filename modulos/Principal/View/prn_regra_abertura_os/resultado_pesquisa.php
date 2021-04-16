<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div id="bloco_resultado" class="bloco_conteudo">
    <div class="listagem">
        <table class="resultado">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>O.S. Simultânea</th>
                    <th>Motivos</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
            <? foreach ($this->view->dados AS $resultado) {?>
                <tr>
                    <td><? echo $resultado->ostdescricao ?></td>
                    <td>
                    <? if(count($resultado->regraOrdemTipo) > 0) {?>
                        <table>
                            <thead>
                                <th>Tipo</th>
                                <th>Agendada</th>
                                <th>Situação</th>
                            </thead>
                            <tbody>
                            <? foreach ($resultado->regraOrdemTipo AS $regraOrdemTipo) { ?>
                                <tr>
                                    <td><? echo $regraOrdemTipo->ostdescricao ?></td>
                                    <td><? echo ($regraOrdemTipo->osrotagendada) == "t" ? "Sim" : "Não"; ?></td>
                                    <td><? echo ($regraOrdemTipo->osrotadzero) == "t" ? "Em D0" : "Antes D0"; ?></td>
                                </tr>
                            <? } ?>
                            </tbody>
                        </table>
                    <? } ?>
                    </td>
                    <td>
                    <? if(count($resultado->regraMotivo) > 0) { ?>
                        <table>
                            <thead>
                                <th>Tipo</th>
                                <th>Agendada</th>
                                <th>Situação</th>
                            </thead>
                            <tbody>
                            <? foreach ($resultado->regraMotivo AS $regraMotivo) { ?>
                                <tr>
                                    <td><? echo $regraMotivo->ostdescricao ?></td>
                                    <td><? echo ($regraMotivo->osrmagendada) == 't' ? "Sim" : "Não"; ?></td>
                                    <td><? echo ($regraMotivo->osrmadzero) == 't' ? "Em D0" : "Antes D0"; ?></td>
                                </tr>
                            <? } ?>
                            </tbody>
                        </table>
                    <? } ?>
                    </td>
                    <td class="acao centro">
                        <a class="editar"  data-ordraoid="<?php echo $resultado->ordraoid; ?>" href="#"><img class="icone" src="images/edit.png"        title="Editar"  alt="Editar"></a>
                        <a class="excluir" data-ordraoid="<?php echo $resultado->ordraoid; ?>" href="#"><img class="icone" src="images/icon_error.png"  title="Excluir" alt="Excluir"></a>
                    </td>
                </tr>
            <? } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" id="registros_encontrados" class="centro">
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