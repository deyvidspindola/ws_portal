		

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>				
					<th class="maior">Grupo</th>
                    <th style="width: 40px">Ação</th>
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
								<td class="esquerda"><?php echo wordwrap($resultado->itsedescricao,30,"<br />", true); ?></td>
                                <td class="centro">
                                    <a title="Alterar" href="cad_grupo_controle_documento.php?acao=editar&itseoid=<?php echo $resultado->itseoid; ?>">
                                        <img class="icone" src="images/edit.png" alt="Alterar">
                                    </a>
                                    <a class="bt_excluir excluir_listagem" title="Excluir" data-cfooid="178" href="cad_grupo_controle_documento.php?acao=excluir&itseoid=<?php echo $resultado->itseoid; ?>">
                                        <img class="icone" src="images/icon_error.png" alt="Excluir">
                                    </a>
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