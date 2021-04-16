<div id="resultado_pesquisa">
    <div class="separador"></div>
    <div class="resultado bloco_titulo">Resultado da Pesquisa</div>
    <div class="resultado bloco_conteudo">
        <?php echo $this->view->ordenacao; ?>
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="menor">Data Ajuste</th>
                        <th class="menor">Inventário</th>
                        <th class="menor">Representante</th>
                        <th class="menor">Código Item</th>
                        <th class="menor">Produto</th>
                        <th class="menor">Modelo</th>
    					<th class="menor">Serial</th>
    					<th class="menor">Estoque Atual</th>
    					<th class="menor">Valor Unitário</th>
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
                                    <td class="centro"><?=date("d/m/Y",strtotime($resultado->invdt_ajuste)); ?></td>
                                    <td class="direita"><?=$resultado->invoid;?></td>
                                    <td class="esquerda"><?=$resultado->repnome;?></td>
                                    <td class="direita"><?=$resultado->prdoid;?></td>
                                    <td class="esuerda"><?=$resultado->prdproduto;?></td>
                                    <td class="esquerda"><?=$this->retornaModeloImobilizado($resultado);?></td>
    								<td class="esquerda"><?=$resultado->imobserial;?></td>
                                    <td class="esquerda"><?=$this->retornaRepresentanteEquipamento($resultado);?></td>
                                    <td class="direita">
                                    <?php if(!is_null($resultado->pcmcusto_medio)): ?>
                                        <?=number_format($resultado->pcmcusto_medio, 2, ',', '.');?>
                                    <?php endif; ?> 
                                    </td>
    							</tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="9" class="centro">
                            <?php
                            //$totalRegistros = count($this->view->dados);
                            $totalRegistros = $this->view->totalResultados;
                            echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="9" class="centro">
                           <button type="submit" name="bt-csv" id="bt-csv">Exportar CSV</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <?php echo ($this->view->totalResultados > 10) ? $this->view->paginacao : ''; ?>
        </div>
    </div>
</div>

