<?php
/*
 * Cabeçalho e Estilos
 * */
cabecalho();
include("calendar/calendar.js");
require("lib/funcoes.js");
?>

<?php flush(); ?>

<head>            
    <link rel="stylesheet" href="calendar/calendar.css" type="text/css"  />    
    <link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
    <link type="text/css" rel="stylesheet" href="modulos/web/css/rel_atendimento_front_end.css">
    
    <script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>     
    <script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>
    <script type="text/javascript" src="modulos/web/js/lib/jquery.PrintArea.js"></script>
    <script type="text/javascript" src="includes/js/validacoes.js"></script> 
    <script language="Javascript" type="text/javascript" src="includes/js/auxiliares.js"></script> 
    <script type="text/javascript" src="modulos/web/js/rel_atendimento_front_end.js"></script>
    
</head>
<body>        
    <div align="center">
        <br />
        <form id="form" class="p_form" method="post" action="rel_atendimento_front_end.php">
            <input type="hidden" name="acao" id="acao" value="pesquisar" />
            <table width="100%" border="0" cellspacing="0" cellpadding="3" align="center" class="tableMoldura">
                <tr class="tableTitulo">
                    <td><h1>Relatório de Atendimentos</h1></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>                
                <tr>
                    <td align="center">
                        <table style="width: 1210px;" class="tableMoldura">
                            <tr class="tableSubTitulo">
                                <td colspan="8"><h2>Dados para pesquisa</h2></td>
                            </tr>
                            <tr>
                                <td colspan="8" align="center"><br></td>
                            </tr>  
                            <tr>                                
                                <td nowrap="nowrap" style="width:140px;"><label>Tipo:</label></td>
                                <td nowrap="nowrap" style="text-align:left;">
                                      <select class="u_select" name="tipo_relatorio" id="tipo_relatorio">

                                        <?php 
                                            $post_tipo_relatorio = isset($options['tipo_relatorio']) ? $options['tipo_relatorio'] : '';                                            
                                        ?>

                                        <option <?php echo $post_tipo_relatorio == "analitico" ? 'selected="selected"' : '' ;  ?> value="analitico">Analítico</option>
                                        <option <?php echo $post_tipo_relatorio == "sintetico" ? 'selected="selected"' : '' ;  ?> value="sintetico">Sintético</option>
                                        <option <?php echo $post_tipo_relatorio == "data_hora" ? 'selected="selected"' : '' ;  ?> value="data_hora">Data / Hora</option>
                                    </select>                                    
                                </td>
                            <tr>                                
                                <td nowrap="nowrap" style="width:140px;"><label>Período:</label></td>
                                <td nowrap="nowrap" style="text-align:left;">
                                    <input style="width:104px;" type="text" id="dt_ini" class="span2 float-left validate[required]" name="dt_ini" maxlength="10" onKeyUp="formata_dt(this);" value="<?php echo isset($options['dt_ini']) ? $options['dt_ini'] : date('d/m/Y'); ?>">
                                    <img src="images/calendar_cal.gif" class="float-left" align="absmiddle" border="0" alt="Calendário..."  style="padding-top: 3px" onclick="displayCalendar( document.getElementById('dt_ini'),'dd/mm/yyyy',this)">
                                    <span class="float-left" style="margin: 0 10px 0 5px; padding-top: 3px"> a </span>
                                    <input style="width:104px;" type="text" id="dt_fim" class="span2 float-left validate[required, funcCall[checkDate]]" name="dt_fim" maxlength="10" onKeyUp="formata_dt(this);" value="<?php echo isset($options['dt_fim']) ? $options['dt_fim'] : date('d/m/Y'); ?>">
                                    <img src="images/calendar_cal.gif" class="float-left" align="absmiddle" border="0" alt="Calendário..."  style="padding-top: 3px" onclick="displayCalendar( document.getElementById('dt_fim'),'dd/mm/yyyy',this)">                                
                                    <? echo desenhaHelpComment('Data Início do Atendimento');?>                                
                                </td>
                            </tr>   
                            <tr>                                
                                <td nowrap="nowrap" style="width:140px;"><label>Horário:</label></td>
                                <td nowrap="nowrap" style="text-align:left;">
                                    <input style="width:104px;" type="text" id="hora_ini" class="span2 float-left hours validate[funcCall[checkHourIni]]" name="hora_ini" value="<?php echo isset($options['hora_ini']) ? $options['hora_ini'] : '' ?>">
                                    <span class="float-left" style="margin: 0 17px; padding-top: 3px"> até </span>
                                    <input style="width:104px;" type="text" id="hora_fim" class="span2 float-left hours validate[funcCall[checkHourFim]]" name="hora_fim" value="<?php echo isset($options['hora_fim']) ? $options['hora_fim'] : ''; ?>">
                                </td>
                            </tr>
                            <tr>                                
                                <td nowrap="nowrap" style="width:140px;"><label>Nome do Cliente:</label></td>
                                <td style="width:300px;" nowrap="nowrap" style="text-align:left;">
                                    <input style="width:268px;" type="text" class="span11" id="nome_cliente" name="nome_cliente" value="<?php echo isset($options['nome_cliente']) ? $options['nome_cliente'] : ''; ?>">                            
                                </td>
                                <td class="analitico <?php echo in_array($post_tipo_relatorio, array("sintetico", "data_hora")) ? 'hide' : '' ;  ?>" nowrap="nowrap" style="width:60px;"><label>Placa:</label></td>
                                <td class="analitico <?php echo in_array($post_tipo_relatorio, array("sintetico", "data_hora")) ? 'hide' : '' ;  ?>" nowrap="nowrap" style="text-align:left;">
                                    <input style="width:104px;" type="text" class="span9 float-left" id="placa" name="placa" value="<?php echo isset($options['placa']) ? $options['placa'] : ''; ?>" />
                                </td>
                            </tr>
                            <tr class="analitico <?php echo in_array($post_tipo_relatorio, array("sintetico", "data_hora")) ? 'hide' : '' ;  ?>">                                
                                <td nowrap="nowrap" style="width:140px;"><label>Protocolo SASCAR:</label></td>
                                <td nowrap="nowrap" style="text-align:left;">
                                    <input style="width:268px;" type="text" class="span11" id="protocolo_sascar" name="protocolo_sascar" value="<?php echo isset($options['protocolo_sascar']) ? $options['protocolo_sascar'] : ''; ?>" />                                
                                </td>
                                <td nowrap="nowrap" style="width:140px;"><label>Protocolo Vivo:</label></td>
                                <td style="width:190px;" nowrap="nowrap" style="text-align:left;">
                                    <input type="text" class="span9" id="protocolo_vivo" name="protocolo_vivo" value="<?php echo isset($options['protocolo_vivo']) ? $options['protocolo_vivo'] : ''; ?>" />
                                </td>
                                <td nowrap="nowrap" style="width:140px;"><label>Tipo de ligação:</label></td>
                                <td nowrap="nowrap" style="text-align:left;">
                                    <select style="width:104px;" class="u_select" id="tipo_ligacao" name="tipo_ligacao">
                                        <?php 
                                            $tipo_ligacao = isset($options['tipo_ligacao']) ? $options['tipo_ligacao'] : '';
                                        ?>
                                        <option <?php echo trim($tipo_ligacao) == "" ? 'selected="selected"' :''; ?> value="">Todos</option>
                                        <option <?php echo trim($tipo_ligacao) == "0" ? 'selected="selected"' :''; ?> value="0">Sem ligação</option>
                                        <option <?php echo trim($tipo_ligacao) == "1" ? 'selected="selected"' :''; ?> value="1">Ligação Ativa</option>
                                        <option <?php echo trim($tipo_ligacao) == "2" ? 'selected="selected"' :''; ?> value="2">Ligação Receptiva</option>
                                        <option <?php echo trim($tipo_ligacao) == "3" ? 'selected="selected"' :''; ?> value="3">Retorno</option>
                                        <option <?php echo trim($tipo_ligacao) == "4" ? 'selected="selected"' :''; ?> value="4">Outros canais de comunicação (Nextel, Email)</option>

                                    </select>
                                </td>
                            </tr>
                            <tr>                                
                                <td nowrap="nowrap" style="width:140px;"><label>Pessoa Autorizada:</label></td>
                                <td nowrap="nowrap" style="text-align:left;">
                                    <input style="width:268px;" type="text" class="span11" id="pessoa_autorizada" name="pessoa_autorizada" value="<?php echo isset($options['pessoa_autorizada']) ? $options['pessoa_autorizada'] : ''; ?>" />                                                              
                                </td>
                                <td nowrap="nowrap" style="width:140px;"><label>Classe Cliente:</label></td>
                                <td nowrap="nowrap" style="text-align:left;">
                                    <select style="width:104px;" class="u_select" id="classe_cliente" name="classe_cliente">

                                        <?php 
                                            $post_cli_classe = isset($options['classe_cliente']) ? $options['classe_cliente'] : '';
                                        ?>

                                        <option value="">Todas</option>                                                                    
                                        <?php foreach($this->combo_cliente_classe as $classe): ?>
                                        <option <?php echo $post_cli_classe == $classe['clicloid'] ? 'selected="selected"' :''; ?> value="<?php echo $classe['clicloid'] ?>"><?php echo $classe['clicldescricao'] ?></option>
                                        <?php endforeach; ?>

                                    </select>
                                </td>
                            </tr>                            
                            <tr>                                
                                <td nowrap="nowrap" style="width:140px;"><label>Atendente:</label></td>
                                <td nowrap="nowrap" style="text-align:left;">
                                    <select style="width:268px;" class="u_select" id="atendente" name="atendente">
                                        <?php 
                                            $post_atend = isset($options['atendente']) ? $options['atendente'] : '';
                                        ?>

                                        <?php if($_SESSION['funcao']['permite_visualizacao_atendente'] == 1): ?>
                                        <option value="">Escolha...</option>
                                        <?php foreach($this->combo_atendentes as $atendente): ?>
                                        <option <?php echo trim($post_atend) == $atendente['cd_usuario'] ? 'selected="selected"' :''; ?> value="<?php echo $atendente['cd_usuario'] ?>"><?php echo $atendente['nm_usuario'] ?></option>
                                        <?php endforeach; ?>
                                        <?php else: ?>
                                        <option value="<?php echo $_SESSION['usuario']['oid'] ?>"><?php echo $_SESSION['usuario']['nome'] ?></option>
                                        <?php endif; ?>
                                    </select>                                
                                </td>
                            </tr>
                            <tr class="analitico <?php echo in_array($post_tipo_relatorio, array("sintetico", "data_hora")) ? 'hide' : '' ;  ?>">                                
                                <td nowrap="nowrap" style="width:140px;"><label>Motivo Nível 1:</label></td>
                                <td nowrap="nowrap" style="text-align:left;">
                                    <select style="width:104px;" class="u_select" id="motivo_nivel_1" name="motivo_nivel_1">

                                        <?php 
                                            $post_motivo_1 = isset($options['motivo_nivel_1']) ? $options['motivo_nivel_1'] : '';
                                        ?>

                                        <option value="">Todos</option>
                                        <?php foreach($this->combo_motivo_nivel1 as $motivo_nivel1): ?>
                                        <option <?php echo $post_motivo_1 == $motivo_nivel1['agroid'] ? 'selected="selected"' :''; ?> value="<?php echo $motivo_nivel1['agroid'] ?>"><?php echo $motivo_nivel1['agrdescricao'] ?></option>
                                        <?php endforeach; ?>

                                    </select>
                                </td>
                            </tr>
                            <tr class="analitico <?php echo in_array($post_tipo_relatorio, array("sintetico", "data_hora")) ? 'hide' : '' ;  ?>">                                
                                <td nowrap="nowrap" style="width:140px;"><label>Motivo Nível 2:</label></td>
                                <td nowrap="nowrap" style="text-align:left;">
                                    <select style="width:104px;" class="u_select" id="motivo_nivel_2" name="motivo_nivel_2">
                                        <?php 
                                            $post_motivo_2 = isset($options['motivo_nivel_2']) ? $options['motivo_nivel_2'] : '';
                                        ?>

                                        <option value="">Todos</option>
                                        <?php foreach($this->combo_motivo_nivel2 as $motivo_nivel2): ?>
                                        <option <?php echo $post_motivo_2 == $motivo_nivel2['atmoid'] ? 'selected="selected"' :''; ?> value="<?php echo $motivo_nivel2['atmoid'] ?>"><?php echo utf8_decode($motivo_nivel2['atmdescricao']) ?></option>
                                        <?php endforeach; ?>                                        
                                    </select>
                                    <img style="padding-top: 3px; display: none;" id="motivo_nivel_2_loader" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                </td>
                            </tr>
                            <tr class="analitico <?php echo in_array($post_tipo_relatorio, array("sintetico", "data_hora")) ? 'hide' : '' ;  ?>">                                
                                <td nowrap="nowrap" style="width:140px;"><label>Motivo Nível 3:</label></td>
                                <td nowrap="nowrap" style="text-align:left;">
                                    <select style="width:104px;" class="u_select" id="motivo_nivel_3" name="motivo_nivel_3">
                                        <?php 
                                            $post_motivo_3 = isset($options['motivo_nivel_3']) ? $options['motivo_nivel_3'] : '';
                                        ?>

                                        <option value="">Todos</option>
                                        <?php foreach($this->combo_motivo_nivel3 as $motivo_nivel3): ?>
                                        <option <?php echo $post_motivo_3 == $motivo_nivel3['atmoid'] ? 'selected="selected"' :''; ?> value="<?php echo $motivo_nivel3['atmoid'] ?>"><?php echo utf8_decode($motivo_nivel3['atmdescricao']) ?></option>
                                        <?php endforeach; ?>     
                                    </select>
                                    <img style="padding-top: 3px; display: none;" id="motivo_nivel_3_loader" alt="" src="modulos/web/images/ajax-loader-circle.gif">
                                </td>
                            </tr>
                            <tr class="analitico <?php echo in_array($post_tipo_relatorio, array("sintetico", "data_hora")) ? 'hide' : '' ;  ?>">                                
                                <td nowrap="nowrap" style="width:140px;"><label>Status Protocolo:</label></td>
                                <td nowrap="nowrap" style="text-align:left;">
                                    <select style="width:104px;" class="u_select" id="status_protocolo" name="status_protocolo">

                                        <?php 
                                            $post_status_protocolo = isset($options['status_protocolo']) ? $options['status_protocolo'] : '';
                                        ?>

                                        <option value="">Todos</option>
                                        <option <?php echo $post_status_protocolo == 'C' ? 'selected="selected"' :''; ?> value="C">Concluído</option>
                                        <option <?php echo $post_status_protocolo == 'P' ? 'selected="selected"' :''; ?> value="P">Pendente</option>
                                    </select>                                
                                </td>
                                <td nowrap="nowrap" style="width:140px;"><label>Status Aten/Mot:</label></td>
                                <td nowrap="nowrap" style="text-align:left;">
                                    <select style="width:104px;" class="u_select" id="status_aten_mot" name="status_aten_mot">

                                        <?php 
                                            $post_status_aten_mot = isset($options['status_aten_mot']) ? $options['status_aten_mot'] : '';
                                        ?>

                                        <option value="">Todos</option>
                                        <option <?php echo $post_status_aten_mot == 'C' ? 'selected="selected"' :''; ?> value="C">Concluído</option>
                                        <option <?php echo $post_status_aten_mot == 'P' ? 'selected="selected"' :''; ?> value="P">Pendente</option>
                                    </select>
                                </td>

                            </tr>
                            <tr>                                
                                <td nowrap="nowrap" style="width:140px;"><label>Tipo Contrato:</label></td>
                                <td nowrap="nowrap" style="text-align:left;">
                                    <select style="width:104px;" class="u_select" id="tipo_contrato" name="tipo_contrato">

                                        <?php 
                                            $post_tipo_contrato = isset($options['tipo_contrato']) ? $options['tipo_contrato'] : '';
                                        ?>
                                        <option value="">Todos</option>
                                        <option <?php echo $post_tipo_contrato == 'C' ? 'selected="selected"' :''; ?> value="C">Cliente (Todos)</option>
                                        <option <?php echo $post_tipo_contrato == 'S' ? 'selected="selected"' :''; ?> value="S">Seguradora (Todos)</option>
                                        <?php foreach($this->combo_tipo_contrato as $tipo_contrato): ?>
                                        <option <?php echo $post_tipo_contrato == $tipo_contrato['tpcoid'] ? 'selected="selected"' :''; ?> value="<?php echo $tipo_contrato['tpcoid'] ?>"><?php echo $tipo_contrato['tpcdescricao'] ?></option>
                                        <?php endforeach; ?>

                                    </select>
                                </td>
                                <td nowrap="nowrap" class="analitico <?php echo in_array($post_tipo_relatorio, array("sintetico", "data_hora")) ? 'hide' : '' ;  ?>" style="width:150px;"><label>Número de Resultados:</label></td>
                                <td nowrap="nowrap" class="analitico <?php echo in_array($post_tipo_relatorio, array("sintetico", "data_hora")) ? 'hide' : '' ;  ?>" style="text-align:left;">
                                    <input style="width:104px;" type="text" id="numero_resultados" name="numero_resultados" onkeypress="javascript:return numero(event,false,false);" value="<?php echo isset($options['numero_resultados']) ? $options['numero_resultados'] : ''; ?>" />                                   
                                </td>                                
                            </tr>
                            <!-- Botão para realizar a pesquisa -->
                            <tr class="tableRodapeModelo1">
                                <td align="center" colspan="8">
                                    <input type="submit" id="pesquisar" class="botao" value="Pesquisar" name="pesquisar" />
                                    <input type="button" onclick="gerarPdf(0);" class="botao" value="Gerar PDF" />
                                    <input type="button" onclick="gerarXls(0);" class="botao" value="Gerar XLS" />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <center>
                            <?php 
                                if(isset($dados_pesquisa)){

                                    if($post_tipo_relatorio == 'analitico') {
                                        include _MODULEDIR_ . 'Relatorio/View/rel_atendimento_front_end/analitico.php';
                                    }
                                    
                                    if($post_tipo_relatorio == 'sintetico' || $post_tipo_relatorio == 'data_hora') {                                        
                                        include _MODULEDIR_ . 'Relatorio/View/rel_atendimento_front_end/sintetico.php';
                                    }

                                    if($post_tipo_relatorio == 'data_hora') {                                        
                                       
                                    }
                                }
                            ?>					    
                        </center>
                    </td>
                </tr>
                <?php if(count($dados_pesquisa) > 0): ?>    
                <tr>
                    <td>
                        <center>
                            <div style="margin: 10px auto; width: 300px; text-align: center">                                
                                <input type="button" id="imprimir" class="botao" value="Imprimir" />
                            </div>                             
                        </center>                
                    </td>
                </tr>
                <?php endif; ?>   
            </table>
        </form>         
    </div>
    
</body>
<?php include "lib/rodape.php"; ?>