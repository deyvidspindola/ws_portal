<?php include 'header_view.php';?>
<link type="text/css" rel="stylesheet" href="lib/css/datatables/bootstrap.min.css"/>
<link type="text/css" rel="stylesheet" href="lib/css/datatables/dataTables.bootstrap.css"/>
<script type="text/javascript" src="lib/js/datatables/jquery.dataTables.js"></script>
<script type="text/javascript" src="lib/js/datatables/dataTables.bootstrap.js"></script>

<div class="modulo_titulo">Parâmetro de Desconto Dívida Ativa</div>		
<div class="modulo_conteudo">

<!-- 	<div class="mensagem alerta" id="msgalerta" style="display:none;"></div>
	<div class="mensagem sucesso" id="msgsucesso" style="display:none;"></div>
	<div class="mensagem erro" id="msgerro" style="display:none;"></div>
    <div class="mensagem info" id="msginfo" style="display:none;"></div> -->

    <?php 
        // Mensagens de alerta
        if(!empty($response)) {
            echo '<div class="' . $response['class'] . '">'; 
            echo $response['message'];
            echo "</div>";
        }
    ?>
	       
    <div class="bloco_titulo">Políticas de Desconto</div>
    
    <div class="bloco_conteudo">
        
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th style="text-align:center" class="maior">Atraso</th>
                        <th style="text-align:center" class="menor">Desconto</th>
                        <th style="text-align:center" class="medio">Aplicação</th>
                        <th style="text-align:center" class="menor">Ação</th>
                    </tr>
                </thead>
                <tbody>
                <?php $i = 0;?>
                <?php foreach($politicasDesconto as $politicaDesconto) : ?>
                    <?php $class  = $i++ % 2 == 0 ? "par" : ""; ?>
                    <tr class="<?=$class?>">
                        
                        <!-- Descrição Atraso -->
                        <td><?=$politicaDesconto['poddescricao_atraso'];?></td>
                        
                        <!-- Valor Desconto -->
                        <td><?=$politicaDesconto['podvlr_desconto'];?>%</td>

                        <!-- Aplicação -->
                        <td><?=$politicaDesconto['podaplicacao'];?></td>
                        
                        <!-- Ação -->
                        <td class="centro">
                            <span>
                                <a href="man_politica_desconto.php?action=edit&podoid=<?=$politicaDesconto['podoid']?>">
                                    <img title="Editar" src="images/edit.png" class="icone">
                                </a>
                            </span>
                        </td>
                    </tr>
                <?php endforeach;?>             
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" align="center"><?=count($politicasDesconto)?> registro(s) encontrado(s)</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="separador"></div>

    <?php if(!empty($historicoPoliticasDesconto)) : ?>

        <div class="bloco_titulo">Histórico de Alterações</div>
        
        <div class="bloco_conteudo">
            
            <div class="listagem" style="margin-top:-5;">
                <table id="tbl_historico" class="table" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th style="text-align:center" class="medio">Atraso</th>
                            <th style="text-align:center" class="maior">Alteração</th>
                            <th style="text-align:center" class="medio">Usuário</th>
                            <th style="text-align:center" class="menor">Data</th>
                                                    
                        </tr>
                    </thead>
                    <tbody>
                    <?php $i = 0;?>
                    <?php foreach($historicoPoliticasDesconto as $historico) : ?>
                        <?php $class  = $i++ % 2 == 0 ? "par" : ""; ?>
                        <tr class="<?=$class?>"> 
                            
                            <!-- Descrição Atraso -->
                            <td><?=$historico['poddescricao_atraso'];?></td>
                            
                            <!-- Alteração -->
                            <td><?=$historico['hipdalteracao']?></td>                        
                            
                            <!-- Usuário -->
                            <td><?=$historico['nm_usuario']?></td>

                            <!-- Data -->
                            <td><?=$historico['hipddt_alteracao']?></td>

                        </tr>
                    <?php endforeach;?>             
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" align="center"><?=count($historicoPoliticasDesconto)?> registro(s) encontrado(s)</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>	

<div class="separador"></div>
<script>
    $.extend( $.fn.dataTable.defaults, {
        "searching": false,
        "ordering": false
    });

    $(function() {
        $('#tbl_historico').dataTable({
            "pagingType": "simple_numbers",
            "bLengthChange": false,
            "dom": '<"top">rt<"bottom"flp><"clear">',
            "oLanguage": {
                "oPaginate": {
                    "sFirst": "Primeira",
                    "sLast" : "Última",
                    "sPrevious" : "<",
                    "sNext" : ">"
                },
                "sLengthMenu": "",
                "sZeroRecords": "",
                "sInfo": "",
                "sInfoEmpty": "",
                "sInfoFiltered": ""
            }
        });
    });
</script>