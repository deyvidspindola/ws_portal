		

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div id="bloco_itens" class="listagem">
        <table>
            <thead>
                <tr>
					<th class="maior">Descrição</th>
                    <th class="medio">Marca</th>
                    <th class="medio">Chicote</th>
                    <th class="acao">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->dados) > 0):
                    $classeLinha = "par";
                    ?>

                    <?php foreach ($this->view->dados as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "impar") ? "par" : "impar"; ?>
							<tr class="<?php echo $classeLinha; ?>">
                                <td class="esquerda"><?php echo $resultado->modedescricao; ?></td>
                                <td class="esquerda"><?php echo $resultado->mmedescricao; ?></td>
                                <td class="esquerda"><?php echo $resultado->obrobrigacao; ?></td>
                                <td class="acao centro">
                                    <a title="Editar"  class="editar"  data-modeoid="<?php echo $resultado->modeoid; ?>" href="#"><img class="icone" src="images/edit.png"        alt="Editar"></a>
                                    <a title="Excluir" class="excluir" data-modeoid="<?php echo $resultado->modeoid; ?>" href="#"><img class="icone" src="images/icon_error.png"  alt="Excluir"></a>
                                </td>
							</tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" id="registros_encontrados" class="centro" data-quantidade="<?php echo $this->view->totalResultados?>">
                        <?php
                        echo ($this->view->totalResultados > 1) ? $this->view->totalResultados . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
        <?php echo ($this->view->totalResultados > 10) ? $this->view->paginacao : ''; ?>
    </div>
</div>