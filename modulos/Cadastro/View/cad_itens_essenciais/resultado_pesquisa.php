        

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div id="bloco_itens" class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="medio">Tipo de ordem de serviço</th>
                    <th class="menor">Motivo de ordem de serviço</th>
                    <th class="menor">Classe do equipamento</th>
                    <th class="menor">Equipamento</th>
                    <th class="menor">Versão</th>
                    <th class="menor">Modelo do veículo</th>
                    <th class="menor">Marca do veículo</th>
                    <th class="menor">Materiais/Acessórios</th>
                    <th class="acao">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($this->view->dados) > 0):?>
                    <?php foreach ($this->view->dados as $resultado) : ?>
                        <tr id="linha_<?php echo $resultado->iesoid; ?>" class="linha">
                            <td><?php echo $resultado->ostdescricao; ?></td>
                            <td><?php echo $resultado->otidescricao; ?></td>
                            <td><?php echo $resultado->eqcdescricao; ?></td>
                            <td><?php echo $resultado->eprnome;      ?></td>
                            <td><?php echo $resultado->eveversao;    ?></td>
                            <td><?php echo $resultado->mlomodelo;    ?></td>
                            <td><?php echo $resultado->mcamarca;     ?></td>
                            <td>
                                <?php if(is_array($resultado->iespiesoid)){ ?>
                                    <div item-id="<?php echo $resultado->iesoid; ?>" class="bt_detalhes">
                                        <b>[</b>
                                            <img class="mais-menos_<?php echo $resultado->iesoid; ?>" valign="absmoddle" src="images/icones/maisTransparente.gif">
                                            <img class="mais-menos_<?php echo $resultado->iesoid; ?>" style="display: none" valign="absmoddle" src="images/icones/menosTransparente.gif">
                                        <b>]</b>
                                    </div>
                                <?php }?>
                            </td>
                            <td class="acao centro">
                                <a title="Editar"  class="editar"  data-iesoid="<?php echo $resultado->iesoid; ?>" href="#"><img class="icone" src="images/edit.png"        alt="Editar"></a>
                                <a title="Excluir" class="excluir" data-iesoid="<?php echo $resultado->iesoid; ?>" href="#"><img class="icone" src="images/icon_error.png"  alt="Excluir"></a>
                            </td>
                        </tr>
                        <?php if(is_array($resultado->iespiesoid)){ ?>
                        <tr id="det_<?php echo $resultado->iesoid; ?>" class="detalhes">
                            <td colspan="9">
                                <div class="lista-itens">
                                    <table class="tabela-itens">
                                        <thead>
                                            <tr>
                                                <th class="menor">Quantidade</th>
                                                <th class="Maior">Produto</th>
                                                <th class="medio">Pode Instalar em Cliente Premium</th>
                                            </tr>
                                        </thead>
                                        <?php foreach ($resultado->iespiesoid as $produto) {?>
                                        <tr>
                                            <td class="quantidade-itens"><?php echo $produto->iespquantidade;?></td>
                                            <td ><?php echo $produto->prdproduto;?></td>
                                            <td class=""><?php echo $produto->premium;?></td>
                                        </tr>
                                        <?php }?>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <?php }?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="9  " id="registros_encontrados" class="centro">
                        <?php
                        echo ($this->view->totalResultados > 1) ? $this->view->totalResultados . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
        <?php echo ($this->view->totalResultados > 10) ? $this->view->paginacao : ''; ?>
    </div>
</div>