

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <?php echo $this->view->ordenacao; ?>
    <div class="listagem">
        <table>
            <thead>
                <tr>
					<th class="medio">Tipo</th>
					<th class="medio">Subtipo</th>
                    <th class="medio">Obrigações Financeiras (Acessórios)</th>
                    <th class="menor">Ação</th>

                </tr>
            </thead>
            <tbody>
                <?php
                if ($this->view->TotalRegistros > 0):
                    $classeLinha = "par";
                    ?>

                    <?php foreach ($this->view->dados as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
							<tr id="linha_<?php echo $resultado->vstovstoid; ?>" class="<?php echo $classeLinha; ?> bloco_itens">
								<td class="esquerda"><?php echo $resultado->tipvdescricao; ?></td>
								<td class="esquerda"><?php echo $resultado->vstdescricao; ?></td>
                                 <td>
                                    <?php if( count($resultado->lista_obrigacao) > 0 ){ ?>
                                        <div item-id="<?php echo $resultado->vstovstoid; ?>" class="bt_detalhes">
                                            <b>[</b>
                                                <img class="hand mais-menos_<?php echo $resultado->vstovstoid; ?>" valign="absmoddle" src="images/icones/maisTransparente.gif">
                                                <img class="hand mais-menos_<?php echo $resultado->vstovstoid; ?>" style="display: none" valign="absmoddle" src="images/icones/menosTransparente.gif">
                                            <b>]</b>
                                        </div>
                                    <?php }?>
                                </td>
                                 <td class="centro">
                                    <img class="icone editar hand" data-vstoid="<?php echo $resultado->vstovstoid; ?>" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/edit.png" title="Editar">
                                    <img class="icone excluir hand" data-vstoid="<?php echo $resultado->vstovstoid; ?>" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/icon_error.png" title="Excluir">
                                </td>
							</tr>
                            <?php if( count($resultado->lista_obrigacao) > 0 ){ ?>
                            <tr id="det_<?php echo $resultado->vstovstoid; ?>" class="detalhes lista-itens">
                                <td colspan="4">
                                    <table class="tabela-itens">
                                        <thead>
                                            <tr>
                                                <th class="menor esquerda">Lista</th>
                                            </tr>
                                        </thead>
                                            <?php foreach ($resultado->lista_obrigacao as $key => $value) {?>
                                            <?php $classeLinhaItem = ($classeLinha == "par") ? "" : "impar"; ?>
                                            <tr class="<?php echo $classeLinhaItem; ?>">
                                                <td><?php echo $value;?></td>
                                            </tr>
                                            <?php }?>
                                    </table>
                                </td>
                            </tr>
                            <?php }?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="centro">
                        <?php
                        echo ($this->view->TotalRegistros > 1) ? $this->view->TotalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php echo $this->view->paginacao; ?>
</div>