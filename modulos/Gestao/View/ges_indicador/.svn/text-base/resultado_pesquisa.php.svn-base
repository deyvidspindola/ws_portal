<div id="resultado_pesquisa">
  <div class="bloco_titulo">Resultado da Pesquisa</div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="maior centro">Nome do Indicador</th>
	                    <th class="medio centro">Código</th>
	                    <th class="menor centro">Tipo</th>
	                    <th class="medio centro">Tipo Indicador</th>
	                    <th class="medio centro">Métrica</th>
	                    <th class="medio centro">Precisão</th>
                        <th class="medio centro">Ação</th>
                	</tr>
                </thead>
                <tbody>
                    <?php foreach($this->view->dados->resultado as $linha): ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                        <tr class="<?php echo $classeLinha; ?>">
                            <td class="maior esquerda">
                                <a href="ges_indicador.php?acao=editar&gmioid=<?php echo $linha->gmioid; ?>"><?php echo $linha->gminome; ?></a>
                            </td>
                            <td class="medio esquerda"><?php echo $linha->gmicodigo; ?></td>
                            <td class="medio esquerda"><?php echo $this->view->dados->tipos[$linha->gmitipo]; ?></td>
                            <td class="medio esquerda"><?php echo $this->view->dados->tipos_indicador[$linha->gmitipo_indicador]; ?></td>
                            <td class="medio esquerda"><?php echo $this->view->dados->metricas[$linha->gmimetrica]; ?></td>
                            <td class="medio direita"><?php echo $linha->gmiprecisao; ?></td>
                            <td class="medio centro">
                                <a href="javascript:void(0);" rel="<?php echo $linha->gmioid; ?>" class="copiar">
                                    <img class="icone" src="images/copy.png" title="Copiar">
                                </a>
                                <a href="javascript:void(0);" rel="<?php echo $linha->gmioid; ?>" class="excluir">
                                    <img class="icone" src="images/icon_error.png" title="Excluir">
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7">
                        	<?php
                                $totalRegistros = count($this->view->dados->resultado);
                                echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                            ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>