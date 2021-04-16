	<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="conteudo">
        <div style="width: 570px; float: left;">
            <fieldset>
                <legend>Legenda</legend>
                <ul>
                    <div style="float: left;">
                        <li>
                            <img src="./images/apr_neutro.gif">
                            Pendente de aprovação
                        </li><br>
                        <li>
                            <img src="./images/apr_ruim.gif">
                            Requisição reprovada
                        </li><br>
                        <li>
                            <img src="./images/apr_excluido.gif">
                            Finalizada
                        </li><br>
                        <li>
                            <img src="./images/apr_bom.gif">
                            Pendente de prestação de contas
                        </li>
                    </div>
                    <div style="float: left;">
                        <li>
                            <img src="./images/apr_ex.gif">
                            Pendente conferência de prestação de contas
                        </li><br>
                        <li>
                            <img src="./images/apr_azul.gif">
                            Pendente aprovação de reembolso
                        </li><br>
                        <li>
                            <img src="./images/apr_roxo.gif">
                            Aguardando devolução
                        </li>
                    </div>
                </ul>
            </fieldset>
        </div>
    <?php echo $this->view->ordenacao; ?>
    </div>
    <div class="clear"></div>
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="mini">Número da Requisição</th>
                    <th class="medio">Data</th>
                    <th class="menor">Valor Solicitado</th>
                    <th class="medio">Tipo de Solicitação</th>
                    <th>Solicitante</th>
                    <th class="mini">Status</th>
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
                            <?php 
                                if ($_SESSION['funcao']['visualizar_requisicao_viagem'] == 1) { ?>
                                    <td class="direita"><a href="cad_requisicao_viagem.php?acao=editar&idRequisicao=<?php echo $resultado->adioid; ?>"><?php echo $resultado->adioid; ?></a></td>
                               <? } else { ?>
                               <? if($resultado->permissaoEdicao === false) : ?>
                                <td class="direita"><?php echo $resultado->adioid; ?></td>
                            <?php else : ?>
                                <td class="direita"><a href="cad_requisicao_viagem.php?acao=editar&idRequisicao=<?php echo $resultado->adioid; ?>"><?php echo $resultado->adioid; ?></a></td>
                            <?php endif;

                            } ?>
                            <td class="centro"><?php echo $resultado->data; ?></td>
                            <td class="direita"><?php echo number_format($resultado->adivalor, 2, ',', ''); ?></td>
                            <td class="esquerda"><?php echo $resultado->adittipo_solicitacao; ?></td>
                            <td class="esquerda"><?php echo $resultado->forfornecedor; ?></td>
                            <?php
                            switch ($resultado->status) :
                                case "A":
                                    echo '<td class="centro"><img src="./images/apr_excluido.gif" title="Finalizada"></td>';
                                    break;
                                case "C":
                                    echo '<td class="centro"><img src="./images/apr_ruim.gif" title="Requisição reprovada"></td>';
                                    break;
                                case "P":
                                    echo '<td class="centro"><img src="./images/apr_neutro.gif" title="Pendente de aprovação"></td>';
                                    break;
                                case "S":
                                    echo '<td class="centro"><img src="./images/apr_bom.gif" title="Pendente de prestação de contas"></td>';
                                    break;
                                case "R":
                                    echo '<td class="centro"><img src="./images/apr_azul.gif" title="Pendente aprovação de reembolso"></td>';
                                    break;
                                case "F":
                                    echo '<td class="centro"><img src="./images/apr_ex.gif" title="Pendente conferência de prestação de contas"></td>';
                                    break;
                                case "D":
                                    echo '<td class="centro"><img src="./images/apr_roxo.gif" title="Aguardando devolução"></td>';
                                    break;
                            endswitch;
                            ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="centro">
                        <?php
                        $totalRegistros = count($this->view->dados);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php echo $this->view->paginacao; ?>
</div>