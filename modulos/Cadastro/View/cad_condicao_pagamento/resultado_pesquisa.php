<style>
    div.listagem table th {
        text-align: center!important;
    }
</style>

        <div class="separador"></div>
        <div class="resultado bloco_titulo">Resultado da Pesquisa</div>
		<div class="resultado bloco_conteudo">
			<div class="listagem">
				<table>
					<thead>
						<tr>
							<th id="th_descricao">Descrição</th>
							<th id="th_num_de_parcelas">Núm. De Parcelas</th>
							<th id="th_vencimentos">Vencimentos</th>
							<th id="th_acao">Ação</th>
						</tr>
					</thead>
					<tbody>
                        <?php if (count($this->view->dados) > 0):
                            $classeLinha = "par";
                        ?>

                        <?php foreach ($this->view->dados as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                        <tr class="<?php echo $classeLinha; ?>">
                            <td width="40%"><?php echo $resultado->cpgdescricao; ?></td>
                            <td width="20%" class="direita"><?php echo $resultado->cpgparcelas; ?></td>
                            <td width="30%" class="direita"><?php echo str_replace(';', '; ', $resultado->cpgvencimentos); ?></td>
                            <td width="10%" class="centro">
                             	<a href="cad_condicao_pagamento.php?acao=editar&cpgoid=<?php echo $resultado->cpgoid; ?>" rel="<?php echo $resultado->cpgoid; ?>"><img src="images/edit.png" title="Editar" class="icone" /></a>                               
                                <a class="deletar" href="cad_condicao_pagamento.php?acao=excluir&cpgoid=<?php echo $resultado->cpgoid; ?>" rel="<?php echo $resultado->cpgoid; ?>"><img src='images/icon_error.png' title='Excluir' class="icone" /></a>                              
                             </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>

                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="centro">
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