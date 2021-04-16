<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
     <?php echo $this->view->ordenacao; ?>
    <div class="listagem">
        <table>
            <thead>
                <tr>
					<th class="maior">Descrição</th>
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
							<tr id="linha_<?php echo $resultado->mtpoid; ?>" class="<?php echo $classeLinha; ?>">
								<td class="esquerda"><?php echo $resultado->mtpdescricao; ?></td>
                                <td class="centro">
                                    <img class="icone editar hand" data-mtpoid="<?php echo $resultado->mtpoid; ?>" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/edit.png" title="Editar">
                                    <img class="icone excluir hand" data-mtpoid="<?php echo $resultado->mtpoid; ?>" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/icon_error.png" title="Excluir">
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
    <?php echo $this->view->paginacao; ?>
</div>