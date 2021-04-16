	

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
            
                <tr>
                    <th class="centro">N°</th>
                    <th class="menor centro">Data/ Hora</th>
                    <th class="medio centro">Cliente</th>
                    <th class="menor centro">Placa</th>
                    <th class="menor centro">Tipo Contrato</th>
                    <th class="medio centro">Motivo</th>
                    <th class="menor centro">Status</th>
                    <th class="centro">Tempo Concl.</th>
                    <th class="medio centro">Atendente</th>
                    <th class="centro">UF</th>
                    <th class="medio centro">Cidade</th>
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
							<tr id="<?php echo $resultado->bacoid ?>" class="<?php echo $classeLinha; ?>">

								<td class="direita"><?php echo $resultado->bacoid; ?></td>
								<td class="centro"><?php echo $resultado->bacdt_solicitacao; ?></td>								
								<td class="esquerda"><?php echo $resultado->clinome; ?></td>
								<td class="esquerda"><?php echo $resultado->bacplaca ?></td>
								<td class="esquerda"><?php echo $resultado->tpcdescricao; ?></td>
								<td class="esquerda"><?php echo $resultado->bmsdescricao; ?></td>
								<td class="esquerda"><?php echo $resultado->status; ?></td>
								<td class="direita"><?php echo $resultado->data; ?></td>
								<td class="esquerda"><?php echo $resultado->nm_usuario; ?></td>
								<td class="esquerda"><?php echo $resultado->clcuf_sg; ?></td>
								<td class="esquerda"><?php echo $resultado->clcnome; ?></td>
							    <td class="acao centro"><a href="javascript: void(0);" title="Editar"><img class="icone editar" src="images/edit.png" alt="Editar"></a></td>
                           </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="12" class="centro">
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