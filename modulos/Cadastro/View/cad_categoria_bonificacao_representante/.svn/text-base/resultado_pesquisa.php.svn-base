		

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
					<th class="menor">Categoria</th>
					 <?php if ($_SESSION['funcao']['cadastro_categoria_bonificacao_rt'] == 1) {?>
					<th class="menor" style="width: 5%">Ação</th>
					<?php }?>

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
								<td class="esquerda"><?php echo wordwrap($resultado->bonrecatnome,30,"<br />", true); ?></td>
						 <?php if ($_SESSION['funcao']['cadastro_categoria_bonificacao_rt'] == 1) {?>
								<td>
    								<center>
                						<a style="text-decoration: none;" title=Editar  id="editar" href="cad_categoria_bonificacao_representante.php?acao=editar&id=<?php echo $resultado->bonrecatoid;?>">
                                            <IMG class=icone alt=Editar src="images/edit.png">
                                        </a>
                                        <?php if ($resultado->utilizado == 'f') : ?>
                                        <a title=Excluir rel="<?php echo $resultado->bonrecatoid;?>" id="btn_excluir" href="javascript:void(0);">
                                            <IMG class=icone alt=Excluir src="images/icon_error.png">
                                        </a>
                                        <?php endif; ?>
    		           			    </center>
								</td>
						<?php } ?>
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