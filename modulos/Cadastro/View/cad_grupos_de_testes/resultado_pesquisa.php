		

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="medio centro">Equipamento</th>
                    <th class="medio centro">Classe</th>
                    <th class="medio centro">Versão</th>
					<th class="acao">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php $tabindex = 5; ?>
                <?php if (count($this->view->dados) > 0): ?>
                	<?php $classeLinha = "par"; ?>
                    <?php foreach ($this->view->dados as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                    	<?php $tabindex ++; ?>
							<tr class="<?php echo $classeLinha; ?>">
								<td><?php echo $resultado->eprnome; ?></td>
								<td><?php echo $resultado->eqcdescricao; ?></td>
								<td><?php echo $resultado->eveversao; ?></td>
								<td class="acao centro">
									<a href="<?php echo $_SERVER['PHP_SELF']."?acao=editar&epcvoid= ".$resultado->epcvoid; ?>" title="Editar">
										<img alt="Editar" src="images/edit.png" class="icone" tabindex="<?php echo $tabindex ?>">
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