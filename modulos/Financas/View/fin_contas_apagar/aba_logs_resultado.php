        

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div id="bloco_itens" class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="menor">Data</th>
                    <th class="maior">Arquivos</th>
                </tr>
            </thead>
        <?php if (count($this->view->dados['logs']['comunicacao']) > 0): ?>
            <tbody>

                <?php $classeLinha = "par"; ?>
                    <?php foreach ($this->view->dados['logs']['comunicacao'] as $linha => $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "impar") ? "par" : "impar"; ?>
                            <tr class="<?php echo $classeLinha; ?>">
                                <td class="esquerda">
                                    <?php 
                                        $auxResultado = explode(".", $resultado);
                                        $auxResultado = substr($auxResultado[0], 5);
                                        echo substr($auxResultado, 6) . "/" . substr($auxResultado, 4, -2) . "/" . substr($auxResultado, 0, -4);
                                    ?>
                                </td>
                                <td class="esquerda">
                                    <a href="fin_arq_apagar.php?aba=logs&acao=baixarLogItau&arquivo=<?php echo substr($resultado, 5); ?>" class="arquivo" data-arquivo="<?php echo substr($resultado, 5); ?>"><?php echo substr($resultado, 5); ?>
                                </td>
                            </tr>
                    <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="18" id="registros_encontrados" class="centro">
                        <?php
                        $totalRegistros = count($this->view->dados['logs']['comunicacao']);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        <?php else : ?>
            <tbody>
                <tr>
                    <td colspan="18" class="centro">
                        Nenhum registro encontrado.
                    </td>
                </tr>
            </tbody>
        <?php endif; ?>
        </table>
    </div>
</div>

<div class="separador"></div>
<div class="resultado bloco_titulo">Logs de Arquivos de Retorno</div>
<div class="resultado bloco_conteudo">
    <div id="bloco_itens" class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="maior">Arquivos</th>
                </tr>
            </thead>
        <?php if (count($this->view->dados['logs']['remessas']) > 0): ?>
            <tbody>

                <?php
                    $classeLinha = "par";
                    ?>
                    <?php foreach ($this->view->dados['logs']['remessas'] as $linha => $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "impar") ? "par" : "impar"; ?>
                        <tr class="<?php echo $classeLinha; ?>">
                            <td class="esquerda">
                                <a href="download.php?arquivo=<?php echo $resultado;?>" class="arquivo" data-arquivo="<?php echo $resultado; ?>"><?php echo substr($resultado, 13); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="18" id="registros_encontrados" class="centro">
                        <?php
                        $totalRegistros = count($this->view->dados['logs']['remessas']);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        <?php else : ?>
            <tbody>
                <tr>
                    <td colspan="18" class="centro">
                        Nenhum registro encontrado.
                    </td>
                </tr>
            </tbody>
        <?php endif; ?>
        </table>
    </div>
</div>
