
    <?php require_once _MODULEDIR_ . "SmartAgenda/View/sincronizacao_legado/cabecalho.php"; ?>

    <?php 
        //permissão para acessar a página
        if(!$_SESSION['funcao']['sincronizacao_legado_smart_agenda']){
            header("Location:"._PROTOCOLO_ . _SITEURL_ . "principal.php");
        }
    ?>

    <style type="text/css">

        #xml {
            border: 1px solid #999;
            font: normal;
            font-family: arial;
            font-size: 12px;
            width: 3500px;
            padding: 10px;
            background-color: #f9f9c9;
        }

    </style>

    <script type="text/javascript">
        jQuery(document).ready(function(){

            //carrega agendamentos
            <?php if(isset($this->view->objAgendamentos)): ?>
            var array_agendamentos = <?php echo json_encode($this->view->objAgendamentos).";";?>
            <?php endif;?>

            $("#bto-atividade").click(function(event) {

                var confirmar = confirm("Tem certeza que deseja realizar esta ação?");
                if(confirmar == false){
                    return false;
                }

                var arrSelec = [];
                var aux = 0;
                $(".ck_agendamento").each(function() {
                    if($(this).prop( "checked" )){
                        var os = $(this).val();
                        arrSelec.push({agendamento:array_agendamentos[os]});
                        aux++;
                    }
                });

                if(aux == 0){
                    alert("Selecione algum agendamento!");
                    return false;
                }

                jQuery.ajax({
                    type: "POST",
                    dataType: "json",
                    data: {
                        acao: 'criarAtividade',
                        agendamentos: arrSelec
                    },
                    success: function(data) {
                        $("#atividades_erro").html("Atividades ERRO: " + data);
                    }
                });
            });

            $("#ck_agendamento_todos").click(function(event) {

                if($(this).prop( "checked" )){
                    $(".ck_agendamento").each(function() {
                        $(this).prop('checked',true);
                    });
                }else{
                    $(".ck_agendamento").each(function() {
                        $(this).prop('checked',false);
                    });
                }

            });



        });

    </script>

     <!-- Mensagens-->
    <div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemErro; ?>
    </div>
    
    <div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemAlerta; ?>
    </div>
    
    <div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemSucesso; ?>
    </div>


    <form id="form-pesquisar" method="POST" enctype="multipart/form-data">
        <input type="hidden" id="acao" name="acao" value="pesquisar"/>
        <div class="bloco_titulo">Dados para Pesquisa</div>
        <div class="bloco_conteudo">
            <div class="formulario">

                <div class="campo data data-intervalo">
                    <label id="lbl_data_agendamento" for="cmp_data_inicio">Data de Agendamento</label>
                    <div class="calendario float-left">
                        <input id="cmp_data_inicio" class="campo validar" type="text" value="<?php echo $this->view->parametros->cmp_data_inicio; ?>" name="cmp_data_inicio">
                    </div>
                    <div class="ate float-left">
                        à
                    </div>
                    <div class="calendario float-left">
                        <input id="cmp_data_fim" class="campo validar" type="text" value="<?php echo $this->view->parametros->cmp_data_fim; ?>" name="cmp_data_fim">
                    </div>
                </div>

                <fieldset class="medio">
                    <input type="checkbox" name="radio_xml" id="radio_xml">
                    <label for='radio_xml'>Gerar XML (Selenium IDE)</label>
                </fieldset>

                <fieldset class="medio">
                    <input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
                    <input type="file" name="arquivo" id="arquivo" accept=".csv"> 
                </fieldset>

                <fieldset class="grande">
                    <input type="text" name="prestadores" id="prestadores" value="<?php echo $this->view->parametros->prestadores; ?>">
                    <label for='prestadores'><br>ID Separado por Virgula<br>(Hortosat(217) / Supersat(1714))</label>
                </fieldset>

                <div class="clear"></div>
            </div>
        </div>

        <div class="bloco_acoes">
            <button type="submit" id="bto-pesquisar">Pesquisar</button>
        </div>
    </form>

<div class="separador"></div>

<?php if($this->view->gerarXml == true && count($this->view->gerarXml) > 0){ ?>

    <div id="xml">
        <?php $id = 1; ?>
        &lt?xml version="1.0" encoding="UTF-8" standalone="yes"?&gt<br>
        &lttestdata&gt<br>
        <?php foreach ($this->view->dadosXml as $chave => $dados): ?>
            &ltvars id="<?php echo $id; ?>" ordem="<?php echo $dados['ordem']?>" uf="<?php echo $dados['uf']?>" cidade="<?php echo $dados['cidade']?>" bairro="<?php echo $dados['bairro']?>" logradouro="<?php echo $dados['logradouro']?>" num="<?php echo $dados['num']?>" complemento="<?php echo $dados['complemento']?>" referencia="<?php echo $dados['referencia']?>" responsavel="<?php echo $dados['responsavel']?>" celularresp="<?php echo $dados['celularresp']?>" contato="<?php echo $dados['contato']?>" celularcont="<?php echo $dados['celularcont']?>" observacoes="<?php echo $dados['observacao']?>" data="<?php echo $dados['data']?>" timeslot="<?php echo $dados['timeslot']?>"/&gt<br>
        <?php $id++; ?>
        <?php endforeach; ?>
        &lt/testdata&gt<br>
    </div>
<?php }else{?>

    
    <div class="resultado bloco_titulo">Resultado da Pesquisa - <?php echo count($this->view->agendamentos)?> Registros Encontrados</div>
    <div id="bloco_itens" class="bloco_conteudo">
        <div class="listagem">
            <form id="form-agendamentos" method="POST" enctype="multipart/form-data">
            <input type="hidden" id="acao" name="acao" value="criarAtividade"/>
            <table id="resultados">
                <thead>
                    <tr>
                        <th class="medio"><input type="checkbox" id="ck_agendamento_todos" name="ck_agendamento_todos"></th>
                        <th class="medio">ID Agenda</th>
                        <th class="medio">OS</th>
                        <th class="medio">Data Agen.</th>
                        <th class="medio">Hora Agen.</th>
                        <th class="medio">Time Slot</th>
                        <th class="medio">ID Técnico</th>
                        <th class="medio">ID Represent.</th>
                        <th class="medio">CEP</th>
                        <th class="medio">Estado</th>
                        <th class="medio">Cidade</th>
                        <th class="medio">Bairro</th>
                        <th class="medio">Endereco</th>
                        <th class="medio">Numero</th>
                        <th class="medio">Complemento</th>
                        <th class="medio">Ponto Ref.</th>
                        <th class="medio">Responsavel</th>
                        <th class="medio">Cel. Resp.</th>
                        <th class="medio">Contato</th>
                        <th class="medio">Cel. Cont.</th>
                        <th class="medio">Observacoes</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(count($this->view->agendamentos)): ?>
                    <?php foreach ($this->view->agendamentos as $chave => $dados): ?>
                    <tr id="result_<?php echo $dados['ordem']; ?>" class="<?php if ($chave % 2): ?>par<?php else: ?>impar<?php endif; ?> linhas">
                        <td class="centro">
                        <?php if( strtotime($dados['data']) >= strtotime(date('Y-m-d')) ) : ?>
                            <input type="checkbox" class="ck_agendamento" name="check_agendamento" VALUE="<?php echo $dados['ordem']; ?>">
                        <?php endif; ?>
                        </td>
                        <td class="centro"><?php echo $dados['idAgenda']; ?></td>
                        <td class="centro"><?php echo $dados['ordem']; ?></td>
                        <td class="centro"><?php echo $dados['data']; ?></td>
                        <td class="centro"><?php echo $dados['hora']; ?></td>
                        <td class="centro"><?php echo $dados['timeslot']; ?></td>
                        <td class="centro"><?php echo $dados['tecnico']; ?></td>
                        <td class="centro"><?php echo $dados['representante']; ?></td>
                        <td class="centro"><?php echo $dados['cep']; ?></td>
                        <td class="centro"><?php echo $dados['uf']; ?></td>
                        <td class="centro"><?php echo $dados['cidade']; ?></td>
                        <td class="centro"><?php echo $dados['bairro']; ?></td>
                        <td class="centro"><?php echo $dados['logradouro']; ?></td>
                        <td class="centro"><?php echo $dados['num']; ?></td>
                        <td class="centro"><?php echo $dados['complemento']; ?></td>
                        <td class="centro"><?php echo $dados['referencia']; ?></td>
                        <td class="centro"><?php echo $dados['responsavel']; ?></td>
                        <td class="centro"><?php echo $dados['celularresp']; ?></td>
                        <td class="centro"><?php echo $dados['contato']; ?></td>
                        <td class="centro"><?php echo $dados['celularcont']; ?></td>
                        <td class="centro"><?php echo $dados['observacao']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6"><button type="button" id="bto-atividade" name="bto-atividade">Criar Atividade no OFSC</button></td>
                        <td id="registros_encontrados" colspan="15"><?php echo count($this->view->agendamentos)?> Registros Encontrados</td>
                    </tr>
                </tfoot>
            </table>
            </form>
        </div>
    </div>

    <div class="separador"></div>
    <div class="separador"></div>
    <div class="separador"></div>

    <div class="resultado bloco_titulo"><?php echo count($this->view->erros); ?> Agendamentos com erros</div>
    <div id="bloco_itens" class="bloco_conteudo">
        <div class="listagem">
            <table>
                <?php if(count($this->view->erros)) :?>
                <?php $arrayColunas = array_keys(get_object_vars($this->view->erros[0]));?>
                <thead>
                    <tr>
                    <?php foreach ($arrayColunas as $errosChave => $errosValor) :?>
                        <th><?php echo $errosValor;?></th>
                    <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->view->erros as $errosChave => $errosValor) :?>
                    <tr class="<?php if ($errosChave % 2): ?>par<?php else: ?>impar<?php endif; ?>">
                        <?php foreach ($arrayColunas as $colChave => $colValor) :?>
                        <td class="centro"><?php echo $errosValor->$colValor; ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <?php endif; ?>
            </table>
        </div>
    </div>


    <div id="atividades_erro"></div>

<?php } ?>
