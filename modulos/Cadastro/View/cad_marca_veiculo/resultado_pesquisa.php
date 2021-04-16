<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
     <?php echo $this->view->ordenacao; ?>
    <div class="listagem">
        <table>
            <thead>
                <tr>
					<th class="maior">Marca</th>
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
							<tr id="linha_<?php echo $resultado->mcaoid; ?>" class="<?php echo $classeLinha; ?>">
								<td class="esquerda"><?php echo $resultado->mcamarca; ?></td>
                                <td class="centro">
                                    <img class="icone editar hand" data-mcaoid="<?php echo $resultado->mcaoid; ?>" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/edit.png" title="Editar">
                                    <?php if ($this->permissao_cadastro_marca) : ?>
                                        <img class="icone excluir hand" data-mcaoid="<?php echo $resultado->mcaoid; ?>" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/icon_error.png" title="Excluir">
                                    <?php endif; ?>
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