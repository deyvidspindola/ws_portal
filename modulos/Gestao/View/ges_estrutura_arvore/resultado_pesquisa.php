		

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="maior">Funcionário</th>
                    <th class="maior">Departamento</th>
                    <th class="maior">Cargo</th>
                    <th class="menor">Nível</th>
                    <th class="menor">Subnível</th>
                    <th class="maior">Superior imediato</th>

					<th class="menor">Nome</th>
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
								<td class="esquerda"><?php echo $resultado->funcionario; ?></td>
								<td class="esquerda"><?php echo $resultado->departamento; ?></td>
								<td class="esquerda"><?php echo $resultado->cargo; ?></td>
								<td class="centro"><?php echo $resultado->gmanivel; ?></td>
								<td class="centro"><?php echo $resultado->gmasubnivel; ?></td>
								<td class="esquerda"><?php echo $resultado->superior_imediato; ?></td>
								<td class="esquerda"><?php echo $resultado->gmanome; ?></td>
							    <td class="centro"> 
                                    <a href="ges_estrutura_arvore.php?acao=editar&gmaoid=<?php echo $resultado->gmaoid ?>">
                                        <img title="Editar" class="icone" src="images/edit.png">
                                    </a>
                                    <a onclick="excluir(<?php echo $resultado->gmaoid; ?>, <?php echo $resultado->gmanivel; ?>, <?php echo $resultado->gmasubnivel; ?>, <?php echo $resultado->gmaano; ?>);" href="javascript:void(0);">
                                        <img title="Excluir" src="<?php echo _PROTOCOLO_ . _SITEURL_ ?>images/icon_error.png" class="icone">
                                    </a>
                                </td>
                            </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10" class="centro">
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