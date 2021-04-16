<?php
require_once '_header.php';
?>

<div class="modulo_titulo">Relatório Sinalização O.S. Teclado Apagado</div>

<div class="modulo_conteudo">

    <div class="bloco_titulo">Filtro do Relatório</div>
    <div class="bloco_conteudo">

        <div class="formulario">

            <form name="busca_dados" id="busca_dados" method="post" action="">
                <input type="hidden" name="acao" id="acao" value="pesquisar" />

                <div>
                    <label>Data de Abertura da O.S.</label>
                    <div class="campo data periodo">
                        <div class="inicial">
                            <label for="data_ini"></label>

                            <input type="text" id="data_ini" name="data_ini" class="campo" value="<?php echo $this->datainicial ?> " />

                        </div>
                        <div class="campo label-periodo" id="lableentre">à</div>
                        <div class="final">
                            <label for="data_fim"></label>

                            <input type="text" id="data_fim" name="data_fim" class="campo" value="<?php echo $this->datafinal ?> " />

                        </div>
                    </div>
                    <div class="clear"></div>

                    <div>
                        <label>Classe</label>
                        <select name="idclasse" id="idclasse" >

                            <?php
                                foreach ( $this->classesCliente  as $idclasse => $classe  )
                                {
                                    $selecionar = $classe['selecionado'] == "1"? "selected": "";
                                    ?>
                                        <option value="<?php echo $idclasse ?>" <?php echo $selecionar ?> ><?php echo $classe['descricao'] ?></option>
                                    <?php
                                }
                            ?>

                        </select>
                    </div>
                    <div class="clear"></div>
                    <div>
                        <label>Status da O.S.</label>
                        <select name="idsituacao" id="idsituacao">

                            <?php
                                foreach ( $this->situacaoOS  as $idsituacao => $situacao  )
                                {
                                    $selecionar = $situacao['selecionado'] == "1"? "selected": "";
                                    ?>
                                        <option value="<?php echo $idsituacao ?>" <?php echo $selecionar ?> ><?php echo $situacao['descricao'] ?></option>
                                    <?php
                                }
                            ?>

                        </select>
                    </div>
                    <div class="clear"></div>
                    <div>
                        <label>O.S. Cancelada Automaticamente</label>
                        <select name="idcancelado" id="idcancelado">

                            <?php
                                foreach ( $this->canceladoautomatico  as $idcancelado => $cancelado  )
                                {
                                    $selecionar = $cancelado['selecionado'] == "1"? "selected": "";
                                    ?>
                                    <option value="<?php echo $idcancelado ?>" <?php echo $selecionar ?> ><?php echo $cancelado['descricao'] ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                    </div>
                    <div class="clear"></div>

                    <div>
                        <label>Cliente</label>
                        <input name="cliente" id="cliente" type="text" value="<?php echo $this->cliente ?>" maxlength="100"/>
                    </div>
                    <div class="clear"></div>

                </div>
            </form>
        </div>
    </div>
    <div class="clear"></div>

    <div class="bloco_acoes">
        <button type="button" name="buscar" id="buscar">Buscar</button>
        <button type="button" name="voltar" id="voltar">Voltar</button>
    </div>
    <div class="clear"></div>
</div>

<div id="alerta" class="modulo_conteudo <?php echo $this->alerta == "" ? "alerta_escondido": "" ?>">

    <div id="texto_alerta" class="mensagem info"> <?php echo $this->alerta ?></div>

</div>

<?php
    if( count($this->relatorio ) > 0 )
    {

    ?>
        <div class="modulo_conteudo">

            <div class="bloco_titulo">Resultado da Pesquisa</div>
            <div class="bloco_conteudo">
                <div class="listagem">
                    <table>
                        <thead>
                        <tr>
                            <th>Data do último agendamento</th>
                            <th>Número da O.S.</th>
                            <th>Cliente</th>
                            <th>Classe do Contrato</th>
                            <th>Status da O.S.</th>
                            <th>Placa</th>
                            <th>Data da Última mensagem</th>
                        </tr>
                        </thead>
                        <tbody>

                            <?php
                                foreach ( $this->relatorio  as $idos => $os  )
                                {
                                    ?>
                                        <tr>
                                            <td><?php echo $os['data'] ?></td>
                                            <td>
                                                <a href="prn_ordem_servico.php?ESTADO=cadastrando&acao=editar&clioid=<?php echo $os['idcliente']?>&ordoid=<?php echo $idos ?>#div_1" target="_blank">
                                                    <?php echo $idos ?>
                                                </a>
                                            </td>
                                            <td><?php echo $os['cliente'] ?></td>
                                            <td><?php echo $os['classe'] ?></td>
                                            <td><?php echo $os['status'] ?></td>
                                            <td><?php echo $os['placa'] ?></td>
                                            <td><?php echo $os['datamensagem'] ?></td>
                                        </tr>
                                    <?php
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="clear"></div>

        </div>
    <?php
}
?>
