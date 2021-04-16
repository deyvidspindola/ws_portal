        

<div class="separador"></div>
<div class="resultado bloco_titulo">Marcas Cadastradas</div>
<div class="resultado bloco_conteudo">
    <div id="bloco_itens" class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="medio">Descrição</th>
                    <th class="acao">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->marcas) > 0):
                    $classeLinha = "par";
                    ?>

                    <?php foreach ($this->view->marcas as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "impar") ? "par" : "impar"; ?>
                            <tr class="<?php echo $classeLinha; ?>">
                                <td class="esquerda"><?php echo $resultado->mmedescricao; ?></td>
                                <td class="acao centro">
                                    <a title="Excluir" class="excluir_marca" data-mmeoid="<?php echo $resultado->mmeoid; ?>" href="#"><img class="icone" src="images/icon_error.png"  alt="Excluir"></a>
                                </td>
                            </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" id="registros_encontrados" class="centro" data-quantidade="<?php echo count($this->view->marcas);?>">
                        <?php
                        echo ( count($this->view->marcas) > 1) ? count($this->view->marcas) . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>