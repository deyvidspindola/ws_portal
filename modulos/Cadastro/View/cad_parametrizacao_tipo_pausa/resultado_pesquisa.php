<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="centro">Grupo de Trabalho</th>
                    <th class="centro">Tipo Pausa</th>
                    <th class="centro">Exibe Alerta</th>
                    <th class="centro">Cadastro Obrigatório</th>
                    <th class="centro">Tempo</th>
                    <th class="centro">Tolerância</th>
                    <th class="centro">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($this->view->dados) > 0): 
                    $classeLinha = "par";
                ?>

                <?php foreach ($this->view->dados as $resultado) : ?>
                <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                <tr class="<?php echo $classeLinha; ?>">
                    <td><?php echo $resultado->gtrnome ?></td>
                    <td><?php echo $resultado->motamotivo ?></td>
                    <td><?php echo $resultado->exibe_alerta ?></td>
                    <td><?php echo $resultado->cadastro_obrigatorio ?></td>
                    <td class="direita"><?php echo str_pad($resultado->hrptempo, 2, '0', STR_PAD_LEFT) ?> minutos</td>
                    <td class="direita"><?php echo str_pad($resultado->hrptolerancia, 2, '0', STR_PAD_LEFT) ?> minutos</td>                    
                    <td class="centro">                        
                        <span>
                            <a href="javascript:void(0);">
                                <img style="height: 16px;" class="icone excluir" id="<?php echo $resultado->hrpoid ?>" src="<?php echo _PROTOCOLO_ . _SITEURL_?>/images/icon_error.png" title="Excluir">
                            </a>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>

            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" class="centro">
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