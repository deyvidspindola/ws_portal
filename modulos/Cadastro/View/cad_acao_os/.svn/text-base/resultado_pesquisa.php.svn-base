		

<div class="separador"></div>
<div class="resultado bloco_titulo">Ações Cadastradas</div>
<div class="resultado bloco_conteudo">
    <div class="listagem" id="bloco_itens">
        <table>
            <thead>
                <tr>
					<th>Descrição</th>
                    <th class="acao">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->acoes) > 0):
                    $classeLinha = "par";
                    ?>

                    <?php foreach ($this->view->acoes as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
							<tr class="<?php echo $classeLinha; ?>">
								<td class="esquerda"><?php echo wordwrap($resultado->mhcdescricao,100,"<br />", true); ?></td>
                                <td class="acao centro"><a href="#" class="excluir" data-mhcoid="<?php echo $resultado->mhcoid; ?>" title="Excluir"><img alt="Excluir" src="images/icon_error.png" class="icone"></a></td>
							</tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="centro" id="registros_encontrados">
                        <?php
                        $totalRegistros = count($this->view->acoes);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td> 
                </tr>
            </tfoot>
        </table>
    </div>
</div>