		

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>					
					<th class="menor esquerda">Versão</th>
					<th class="menor esquerda">Data Cadastro</th>		
                                        <th class="acao">Ação</th>		
                                        

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
								<td width="80%" class=""><?php echo wordwrap($resultado->trvdescricao,100,"<br />", true); ?></td>
                                                                <td class="" nowrap><?php echo $resultado->trvcadastro; ?></td>								
                                                                <td class="acao centro" nowrap>
                                                                    <a title="Editar"  class="editar"  data-trvoid="<?php echo $resultado->trvoid; ?>" href="#"><img class="icone" src="images/edit.png"        alt="Editar"></a>
                                                                    <a title="Excluir" class="excluir" data-trvoid="<?php echo $resultado->trvoid; ?>" href="#"><img class="icone" src="images/icon_error.png"  alt="Excluir"></a>
                                                                </td>
							</tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="centro">
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