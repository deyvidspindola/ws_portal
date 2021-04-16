	
<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="maior centro" style="cursor: default;" id="th_cliente" >Cliente</th>
                    <th class="menor centro" style="cursor: default;" >Placa</th>
                    <th class="menor centro" style="cursor: default;" >Ação</th>
                    <th class="menor centro" style="cursor: default;" >Motivo</th>
                    <th class="menor centro" style="cursor: default;" >Atendente</th>
                    <th class="menor centro" style="cursor: default;" >Data Atendimento</th>
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
                            <td style="cursor: default; " class="esquerda"><?php echo $resultado->clinome; ?></td>
                            <td style="cursor: default; " class="esquerda"><?php echo $resultado->veiplaca; ?></td>
                            <td style="cursor: default; " class="esquerda"><?php echo $resultado->aoamdescricao_acao; ?></td>
                            <td style="cursor: default; " class="esquerda"><?php echo $resultado->aoamdescricao_motivo; ?></td>
                            <td style="cursor: default; " class="esquerda"><?php echo $resultado->ds_login; ?></td>
                            <td style="cursor: default; " class="centro"><?php echo $resultado->aotdt_cadastro; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="14" class="centro">
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

<div class="separador"></div>

<div class=" bloco_tabelas bloco_esquerdo">
    <div class="bloco_titulo" style="cursor: default; ">Quantidade de O.S.</div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="centro">Ação</th>
                        <th class="menor centro">Sub Total</th>
                    </tr>
                </thead>
                <?php if (count($this->view->dadosQuantidadeOS) > 0): ?>
                    <?php $classeLinha = "par"; ?>
                    <?php $totalDadosQuantidadeOS = 0; ?>
                    <?php foreach ($this->view->dadosQuantidadeOS as $dadosQuantidadeOS): ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                        <tbody>                            	
                            <tr class="<?php echo $classeLinha; ?>">
                                <td class="esquerda"><?php echo $dadosQuantidadeOS['descricao'] ?></td>
                                <td class="direita"><?php echo $dadosQuantidadeOS['total']; ?></td>
                            </tr>
                            <?php $totalDadosQuantidadeOS += $dadosQuantidadeOS['total']; ?>
                        <?php endforeach; ?>                                
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="esquerda">Total</td>
                            <td class="direita"><?php echo $totalDadosQuantidadeOS; ?></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<div class=" bloco_tabelas bloco_direito">
    <div class="bloco_titulo" style="cursor: default; ">Quantidade de Atendimentos</div>
    <div class="bloco_conteudo">
        <div class="listagem">                                
            <table>
                <thead>
                    <tr>
                        <th class="centro">Atendente</th>
                        <th class="menor centro">Sub Total</th></tr>
                </thead>
                <?php if (count($this->view->dadosQuantidadeAtendimentos) > 0): ?>
                    <?php $classeLinha = "par"; ?>
                    <?php $totalDadosQuantidadeAtendimento = 0; ?>
                    <?php foreach ($this->view->dadosQuantidadeAtendimentos as $dadosQuantidadeAtendimento): ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                        <tbody>                            	
                            <tr class="<?php echo $classeLinha; ?>">
                                <td class="esquerda"><?php echo $dadosQuantidadeAtendimento['atendente'] ?></td>
                                <td class="direita"><?php echo $dadosQuantidadeAtendimento['total']; ?></td>
                            </tr>
                            <?php $totalDadosQuantidadeAtendimento += $dadosQuantidadeAtendimento['total']; ?>
                        <?php endforeach; ?>                                
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="esquerda">Total</td>
                            <td class="direita"><?php echo $totalDadosQuantidadeAtendimento; ?></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>


<div style="clear:both"></div>

<div class="separador"></div>


<div class=" bloco_tabelas bloco_esquerdo">
    <div class="bloco_titulo" style="cursor: default; ">Atendimentos por Ação</div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <?php if (count($this->view->dadosAtendimentosPorAcao) > 0): ?>
                    <?php $totalDadosAtendimentoAcaoGeral = 0; ?>
                    <?php foreach ($this->view->dadosAtendimentosPorAcao as $dadosAtendimentoAcao): ?>
                    <?php $classeLinha = "par"; ?>
                    <?php $totalDadosAtendimentoAcao = 0; ?>
						<thead>
                            <tr>
                                <th class="centro"><?php echo $dadosAtendimentoAcao['acao'] ?></th>
                                <th class="menor centro">Sub Total</th>
                            </tr>
                        </thead>
                        <tbody>  
                        <?php foreach ($dadosAtendimentoAcao['motivos'] as $dadosAtendimentoAcaoMotivos): ?>
                            <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                                <tr class="<?php echo $classeLinha; ?>">
                                    <td class="esquerda"><?php echo $dadosAtendimentoAcaoMotivos['descricao'] ?></td>
                                    <td class="direita"><?php echo $dadosAtendimentoAcaoMotivos['total']; ?></td>
                                </tr>
                            <?php $totalDadosAtendimentoAcao += $dadosAtendimentoAcaoMotivos['total']; ?>
                        <?php endforeach; ?>
                            <tr>
                                <td style="background-color:#BAD0E5; font-weight: bold;" class="esquerda">Total</td>
                                <td style="background-color:#BAD0E5; font-weight: bold;" class="direita"><?php echo $totalDadosAtendimentoAcao; ?></td>
                            </tr>
                        <?php $totalDadosAtendimentoAcaoGeral += $totalDadosAtendimentoAcao; ?>
                    <?php endforeach; ?>
                    	</tbody>
                        <tfoot>
                            <tr>
                                <td class="esquerda">Total Geral</td>
                                <td class="direita"><?php echo $totalDadosAtendimentoAcaoGeral; ?></td>
                            </tr>
                        </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>


<div class=" bloco_tabelas bloco_direito">
    <div class="bloco_titulo" style="cursor: default; ">Quantidade por Projeto</div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <?php if (count($this->view->dadosQuantidadesProjetos['dados']) > 0): ?>
                    <?php $classeLinha = "par"; ?>
                    <?php $totalQtdSubtotal = 0; ?>
                    <?php $totalPorAcao = array(); ?>
                        <thead>
                            <tr>
                                <th class="menor centro">Projeto</th>
                                <?php foreach ($this->view->dadosQuantidadesProjetos['acoes'] as $acao): ?>
                                <th class="menor centro"><?php echo $acao; ?></th>
                                <?php endforeach; ?>
                                <th class="menor centro">Sub Total</th></tr>
                        </thead> 
                        <tbody>
                             <?php foreach ($this->view->dadosQuantidadesProjetos['projetos'] as $projeto): ?>
                            <?php $classeLinha = ($classeLinha == "par") ? "" : "par"; ?>
                            <tr class="<?php echo $classeLinha; ?>">
                                <td class="esquerda"><?php echo $projeto; ?></td>
                                <?php foreach ($this->view->dadosQuantidadesProjetos['acoes'] as $acao): ?>
                                <td class="direita">
                                    <?php echo $this->view->dadosQuantidadesProjetos['dados'][$projeto][$acao]; ?>
                                </td>
                                <?php 
                                if (!isset($totalPorAcao[$acao])){
                                    $totalPorAcao[$acao] = 0;
                                }
                                $totalPorAcao[$acao] += $this->view->dadosQuantidadesProjetos['dados'][$projeto][$acao];
                                ?>
                                <?php endforeach; ?>
                                <td class="direita"><?php echo $this->view->dadosQuantidadesProjetos['dados'][$projeto]['subtotal']; ?></td>
                            </tr>
                            <?php $totalQtdSubtotal += $this->view->dadosQuantidadesProjetos['dados'][$projeto]['subtotal']; ?>
            
                            <?php endforeach; ?>
                        </tbody>
                        
                        <tfoot>
                            <tr>
                                <td class = "esquerda">Total</td>
                            <?php foreach ($this->view->dadosQuantidadesProjetos['acoes'] as $acao): ?>
                                <td class="direita"><?php echo $totalPorAcao[$acao]; ?></td>
                            <?php endforeach; ?>
                                <td class="direita">
                                    <?php echo $totalQtdSubtotal; ?>
                                </td>
                            </tr>
                        </tfoot>
                <?php endif; ?>

            </table>
        </div>
    </div>
</div>

<div style = "clear:both"></div>
</div>
<div class = "separador"></div>
