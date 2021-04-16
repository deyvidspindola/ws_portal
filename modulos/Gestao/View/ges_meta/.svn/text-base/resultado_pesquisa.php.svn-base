		

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th style="width: 90px">Nome da meta</th>
                    <th style="width: 50px">Ano</th>
                    <th style="width: 50px">Código</th>
                    <th style="width: 90px">Responsável</th>
                    <th style="width: 50px">Tipo</th>
                    <th style="width: 50px">Métrica</th>
                    <th style="width: 50px">Precisão</th>
                    <th style="width: 50px">Direção</th>
                    <th style="width: 50px">Limite Sup.</th>
                    <th style="width: 50px">Limite</th>
                    <th style="width: 50px">Limite Infer.</th>
                    <th style="width: 50px">Peso</th>
                    <th style="width: 100px">Ações</th>
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
                                <td class="esquerda"><?php echo wordwrap($resultado->meta, 10,"<br/>",true) ?></td>
                                <td class="centro"><?php echo $resultado->ano ?></td>
                                <td class="centro"><?php echo wordwrap($resultado->codigo, 13,"<br/>",true) ?></td>
                                <td class="esquerda"><?php echo wordwrap($resultado->responsavel, 15,"<br/>",true) ?></td>
                                <td class="esquerda"><?php echo $resultado->tipo ?></td>
                                <td class="centro"><?php echo $resultado->metrica ?></td>
                                <td class="direita"><?php echo $resultado->precisao ?></td>
                                <td class="esquerda"><?php echo $resultado->direcao ?></td>
                                <td class="direita"><?php echo wordwrap($resultado->limite_superior, 8,"<br/>",true) ?></td>
                                <td class="direita"><?php echo wordwrap($resultado->limite, 8,"<br/>",true) ?></td>
                                <td class="direita"><?php echo wordwrap($resultado->limite_inferior, 8,"<br/>",true) ?></td>
                                <td class="direita"><?php echo $resultado->peso ?></td>
                                <td class="centro">
                                    <a href="ges_meta.php?acao=editar&gmeoid=<?php echo $resultado->gmeoid ?>"><img title="Editar" class="icone" src="images/edit.png"></a>
                                    <a class="excluir-meta" data-meta="<?php echo $resultado->gmeoid ?>" href="ges_meta.php?acao=excluir&gmeoid=<?php echo $resultado->gmeoid ?>"><img title="Excluir" class="icone" src="images/icon_error.png"></a>
                                    <a class="copiar-meta" data-meta="<?php echo $resultado->gmeoid ?>" href="ges_meta.php?acao=copiar&gmeoid=<?php echo $resultado->gmeoid ?>"><img title="Copiar" class="icone" src="images/icone-copia.png"></a>
                                </td>
							</tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="13" class="centro">
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