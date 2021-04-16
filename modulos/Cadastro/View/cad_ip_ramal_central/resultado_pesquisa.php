		

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div id="bloco_itens" class="listagem">
        <table>
            <thead>
                <tr>
					<th class="menor">Ramal</th>
					<th class="medio">IP</th>
                    <th class="medio">Descrição</th>
                    <th class="medio">DT Cadastro</th>
                    <th class="medio">Roteamento</th>
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
								<td class="esquerda"><?php echo $resultado->ripramal; ?></td>
								<td class="esquerda"><?php echo $resultado->ripip; ?></td>
                                <td class="esquerda"><?php echo $resultado->ripdescricao; ?></td>
                                <td class="esquerda"><?php echo $resultado->ripdt_cadastro; ?></td>
                                <td class="esquerda"><?php echo $resultado->ripponto_roteamento; ?></td>
                                <td class="acao centro">
                                    <a title="Editar"  class="editar"  data-oid="<?php echo $resultado->oid; ?>" href="#"><img class="icone" src="images/edit.png"        alt="Editar"></a>
                                    <a title="Excluir" class="excluir" data-oid="<?php echo $resultado->oid; ?>" href="#"><img class="icone" src="images/icon_error.png"  alt="Excluir"></a>
                                </td>
							</tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" id="registros_encontrados" class="centro">
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