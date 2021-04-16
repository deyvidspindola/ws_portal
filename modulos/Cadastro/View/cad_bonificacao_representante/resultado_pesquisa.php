		

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
					<th class="menor">Competência</th>
                    <th class="maior">Representante</th>
                    <th class="menor">Categoria</th>
					<th class="menor">Custo de Eficiência Operacional</th>
					<th class="menor">Quantidade Mínima O.S.</th>
                    <th class="menor">Status</th>
					<th style="width: 7%;" class="menor">Ação</th>

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
								<td class="centro"><?php echo $resultado->bonredt_bonificacao; ?></td>
                                <td class="esquerda"><?php echo $resultado->repnome; ?></td>
                                <td class="esquerda"><?php echo wordwrap($resultado->bonrecatnome,100,"<br />", true); ?></td>
								<td class="direita"><?php echo number_format($resultado->bonrevalor_bonificacao, 2, ',', '.'); ?></td>
								<td class="direita"><?php echo $resultado->bonreqtd_min_os; ?></td>
                                <td class="esquerda"><?php echo $resultado->status_formatado; ?></td>
                                <td class="centro">
                                    <?php if ( $this->view->permissao ) : ?>
                                        <a href="cad_bonificacao_representante.php?acao=editar&bonreoid=<?php echo $resultado->bonreoid; ?>" title="Editar"><img alt="Editar" src="images/edit.png" class="icone" /></a>
                                        <?php if ($resultado->bonrestatus == 'A') : ?>
                                            <a href="#" title="Excluir" onclick="javascript: return excluir('<?php echo $resultado->bonreoid; ?>');"><img alt="Excluir" src="images/icon_error.png" class="icone" /></a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
							</tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" class="centro">
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