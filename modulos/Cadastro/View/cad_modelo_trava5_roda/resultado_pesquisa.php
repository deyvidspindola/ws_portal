		

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>					
                    <th class="menor esquerda">Modelo</th>
                    <th class="menor esquerda">Produto</th>
                    <th class="menor esquerda">Data Cadastro</th>
                    <th class="menor">Ação</th>
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
                                    <td class=""><?php echo wordwrap($resultado->tmodescricao,100,"<br />", true); ?></td>
                                    <td class=""><?php echo $resultado->prdproduto; ?></td>
                                    <td class=""><?php echo $resultado->tmodt_cadastro; ?></td>
                                    <td class="acao centro" nowrap>
                                        <a title="Editar"  class="editar"  data-tmooid="<?php echo $resultado->tmooid; ?>" href="#"><img class="icone" src="images/edit.png"        alt="Editar"></a>
                                        <a title="Excluir" class="excluir" data-tmooid="<?php echo $resultado->tmooid; ?>" href="#"><img class="icone" src="images/icon_error.png"  alt="Excluir"></a>
                                    </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="centro">
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