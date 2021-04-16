

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Faixa Inicial Parque Médio</th>
                    <th>Faixa Final Parque Médio</th>
                    <th>Revenue Share VIVO</th>
                    <th>Revenue Share SASCAR</th>
                    <th>Preço Mínimo</th>
                    <th>Incremento Valor</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->dados) > 0):
                    $classeLinha = "par";
                    ?>

                    <?php foreach ($this->view->dados as $i => $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
							<tr class="<?php echo $classeLinha; ?>">
                                <td class="direita"><?php echo $resultado->prscroid ?></td>
                                <td class="direita"><?php echo number_format($resultado->prscrfaixa_inicial, 0, '', '.') ?></td>
                                <td class="direita"><?php echo number_format($resultado->prscrfaixa_final, 0, '', '.') ?></td>
                                <td class="direita"><?php echo number_format($resultado->prscrrevenue_share_vivo, 2, ',', '.') ?> %</td>
                                <td class="direita"><?php echo number_format($resultado->prscrrevenue_share_sascar, 2, ',', '.') ?> %</td>
                                <td class="direita">R$ <?php echo number_format($resultado->prscrpreco_minimo, 2, ',', '.')?></td>
                                <td class="direita">R$ <?php echo number_format($resultado->prscrincremento_valor, 2, ',', '.')?></td>
                                <td class="acao">
                                    <span>
                                        <a class="editar" id="" href="cad_parametrizacao_rs_calculo_repasse.php?acao=editar&prscroid=<?php echo $resultado->prscroid ?>"><img class="icone" src="<?php echo _PROTOCOLO_ . _SITEURL_ ?>images/edit.png" title="Editar"></a>
                                        <?php if((count($this->view->dados) - 1) == $i): ?>
                                        <a class="excluir" id="<?php echo $resultado->prscroid ?>" href="javascript:void(0);"><img class="icone" src="<?php echo _PROTOCOLO_ . _SITEURL_ ?>images/icon_error.png" title="Excluir"></a>
                                    <?php endif; ?>
                                    </span>
                                </td>
							</tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8 class="centro">
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