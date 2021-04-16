		

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">

 <?php echo $this->view->ordenacao; ?>
    <div id="bloco_itens" class="listagem">
        <table>
            <thead>
                <tr>
					<th class="menor">UF</th>
					<th class="menor">Cidade</th>
					<th class="menor">Bairro</th>
					<th class="menor">A&ccedil;&atilde;o</th> 

                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->dados) > 0):
                    $classeLinha = "par";
                    ?>

                    <?php foreach ($this->view->dados as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
							<tr class="<?php echo $classeLinha; ?>" id='tr<?php  echo $resultado->cmboid; ?>'>
								<td class="centro"><?php echo $resultado->cmbestoid; ?></td>
								<td class="centro"><?php echo $resultado->cmbclcoid; ?></td>
								<td class="centro"><?php echo $resultado->cmbcbaoid; ?></td>
								<td class="centro">
								    <a title="Editar"  class="editar"  data-cmboid="<?php echo $resultado->cmboid; ?>" href="#"><img class="icone" src="images/edit.png"        alt="Editar"></a>
                                    <a title="Excluir" class="excluir" data-cmboid="<?php echo $resultado->cmboid; ?>" href="#"><img class="icone" src="images/icon_error.png"  alt="Excluir"></a>
								</td>
							</tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" id="registros_encontrados" class="centro">
                        <?php
                        $totalRegistros = count($this->view->dados);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php echo ($this->view->totalResultados > 10) ? $this->view->paginacao : ''; ?>
</div>