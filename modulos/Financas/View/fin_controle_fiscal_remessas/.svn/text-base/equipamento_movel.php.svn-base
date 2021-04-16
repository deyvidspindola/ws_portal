<?php
require_once '_header.php';
$control = new FinControleFiscalRemessas();
$view = $control->setRetornaView();
?> 
<head>    
    <link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
    <script language="Javascript" type="text/javascript" src="modulos/web/js/fin_controle_fiscal_equipamento_movel.js"></script> 
    <script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js" charset="utf-8"></script> 
    <script language="javascript">
        function verificaNumero(e) {
            if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        }
        jQuery(document).ready(function () {
            jQuery("#pesquisa_nf_remessa").keypress(verificaNumero);
            jQuery("#pesquisa_n_serie").keypress(verificaNumero);
            jQuery("#pesquisa_contrato").keypress(verificaNumero);
            jQuery("#pesquisa_numero_pedido").keypress(verificaNumero);
        });

        function controleMovelAba(aba) {

            jQuery('#controle_movel_aba').val(aba);

            if (aba === 'envio') {

                jQuery('#aba_devolucoes').removeClass("ativo");
                jQuery('#aba_envio').addClass("ativo");

                jQuery('#envio_nf').html("NF Remessa");
                jQuery('#envio_possui_nf').html("Possuí NF de Remessa ?");

                jQuery('#resultado_pesquisa').html("");

            } else {
                jQuery('#aba_envio').removeClass("ativo");
                jQuery('#aba_devolucoes').addClass("ativo");

                jQuery('#envio_nf').html("NF Retorno");
                jQuery('#envio_possui_nf').html("Possuí NF de Retorno ?");

                jQuery('#resultado_pesquisa').html("");

            }

        }

        function pesquisarClientes() {

            var tamanho = jQuery("#pesquisa_nome_cliente").val().length;

            if (tamanho >= 3) {

                jQuery('#msgalerta').html('').hide();

                jQuery('#pesquisa_select_cliente_load').html('<center><img src="images/loading.gif" alt="" style="width: 50px;"/></center>');

                var nome = jQuery("#pesquisa_nome_cliente").val();

                jQuery.ajax({
                    url: 'fin_controle_fiscal_remessas.php',
                    type: 'post',
                    data: {
                        acao: 'pesquisarCliente',
                        nomeCliente: nome
                    },
                    dataType: 'json',
                    success: function (data) {

                        jQuery.each(data, function (index, el) {
                            jQuery('#pesquisa_select_cliente').append($('<option>', {
                                value: el.clioid,
                                text: el.clinome
                            }));
                        });

                        jQuery('#pesquisa_select_cliente_load').html('');

                        jQuery("#pesquisa_select_cliente").show();
                    }
                });
            } else {
                $("#msgalerta").html("Para pesquisar o cliente digite pelo menos 3 letras.").showMessage();
            }
        }

        function selecionaCliente() {

            var id = jQuery('select[name="pesquisa_select_cliente"] option:selected').val();

            var texto = jQuery('select[name="pesquisa_select_cliente"] option:selected').text();

            jQuery("#pesquisa_id_cliente").val(id);

            jQuery("#pesquisa_nome_cliente").val(texto);

            jQuery("#pesquisa_nome_cliente").css("background", "#F0F0F0");

            jQuery("#pesquisa_nome_cliente").prop("disabled", true);

            jQuery("#pesquisa_select_cliente").hide();

            jQuery("#pesquisa_cliente").hide();

            jQuery("#pesquisa_cliente_limpar").show();

        }

        function pesquisarClientesLimpar() {

            jQuery('#pesquisa_id_cliente').val('');

            jQuery('#pesquisa_nome_cliente').val('');

            jQuery("#pesquisa_select_cliente").html('<options></options>');

            jQuery("#pesquisa_cliente_limpar").hide();

            jQuery("#pesquisa_cliente").show();

            jQuery("#pesquisa_nome_cliente").prop("disabled", false);

            jQuery("#pesquisa_nome_cliente").css("background", "");

        }
    </script>
</head>
<form name="form" id="form" method="POST" action="">
    <input type="hidden" name="acao" id="acao" value="equipamentoMovelPesquisar" />
    <input type="hidden" id="controle_movel_aba" name="controle_movel_aba" value="envio"/>

    <?php require_once '_msgPrincipal.php'; ?>
    <ul class="bloco_opcoes">
        <li><a href="fin_controle_fiscal_remessas.php?acao=pesquisa">Remessas</a></li>
        
        <li class="ativo"><a href="fin_controle_fiscal_remessas.php?acao=equipamentoMovel">Equipamento Móvel</a></li>
    </ul>
    <div class="bloco_titulo">Controle Móvel</div>
    <div class="bloco_conteudo">
        <div class="formulario">

            <ul class="bloco_opcoes">
                <li id="aba_envio" name="aba_envio" onclick="controleMovelAba('envio')" class="ativo"><a href="#">Envios</a></li>
                <li id="aba_devolucoes" name="aba_devolucoes" onclick="controleMovelAba('devolucoes')"><a href="#">Devoluções</a></li>
            </ul>

            <div class="bloco_titulo">Dados para Pesquisa</div>
            <div class="bloco_conteudo">
                <div class="formulario">
                    <table border="0" cellspacing="2" cellpadding="0" bgcolor="#FFFFFF" align="center" width="100%">        
                        <tr>
                            <td>
                                <table >
                                    <tr>
                                        <td width="250px">   
                                            
                                            
                                            <div class="campo data">
                                                Período(<font color="#FF0000">*</font>)
                                                <input class="campo"  type="text" name="pesquisa_data_inicio" id="pesquisa_data_inicio" maxlength="10" value="<?php echo $dt_ini; ?>" />
                                            </div>
                                            <div style="margin-top: 23px !important;" class="campo label-periodo">à</div>
                                            <div class="campo data">
                                                <label>&nbsp;</label>
                                                <input  class="campo"  type="text" name="pesquisa_data_fim" id="pesquisa_data_fim" maxlength="10" value= "<?php echo $dt_fim; ?>" />
                                            </div>      
                                        </td>
                                        <td width="110px">
                                            <div id="envio_nf" name="envio_nf">NF Remessa</div>
                                            <input type="text" id="pesquisa_nf_remessa" name="pesquisa_nf_remessa" size='10'>
                                        </td>
                                        <td width="110px">
                                            <div>
                                            N&ordm; S&eacute;rie
                                            </div>
                                            <input type="text" id="pesquisa_n_serie" name="pesquisa_n_serie" size='10'>
                                        </td>
                                        <td width="110px">
                                            <div>
                                            Contrato</div>
                                            <input type="text" id="pesquisa_contrato" name="pesquisa_contrato" size='10'>
                                        </td>
                                        <td width="110px">
                                            <div>
                                            N&ordm; Pedido</div>
                                            <input type="text" id='pesquisa_numero_pedido' name='pesquisa_numero_pedido' size='10'>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%">
                                            <table>
                                                <tr>
                                                    <td><div>
                                                        Cliente</div>
                                                        <input type="hidden" id="pesquisa_id_cliente" name="pesquisa_id_cliente" style="width: 240px">
                                                        <input type="text" id="pesquisa_nome_cliente" name="pesquisa_nome_cliente" style="width: 240px">&nbsp;&nbsp;
                                                    </td>
                                                    <td valign="bottom">
                                                        <div>
                                                        <input type="button" id="pesquisa_cliente" name="pesquisa_cliente" value="Pesquisar" onclick="javascript:pesquisarClientes()">
                                                        </div>
                                                    </td>
                                                    <td valign="bottom">
                                                        <div>
                                                            <input type="button" id="pesquisa_cliente_limpar" name="pesquisa_cliente_limpar" value="Limpar" onclick="javascript:pesquisarClientesLimpar()" style="display: none;">
                                                            </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <select name="pesquisa_select_cliente" id="pesquisa_select_cliente" multiple="multiple" onclick="selecionaCliente()" style="width: 240px; height: 200px; display: none;"></select>
                                                    </td>
                                                    <td>
                                                        <div id="pesquisa_select_cliente_load"></div>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td>
                                            Tipo Relat&oacute;rio (<font color="#FF0000">*</font>)<br />
                                            <select name="pesquisa_tipo_relatorio" id="pesquisa_tipo_relatorio">
                                                <option value='serial'>Serial</option>
                                                <option value='produto'>Produto</option>
                                            </select>
                                        </td>
                                        <td colspan="3">
                                            <div id="envio_possui_nf" name="envio_possui_nf">Possu&iacute; NF de Remessa?</div>
                                            <select id="pesquisa_possui_nf_remessa" name="pesquisa_possui_nf_remessa">
                                                <option value='todos'>Todos</option>
                                                <option value='sim'>Sim</option>
                                                <option value='nao'>Não</option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="bloco_acoes">
                <button type="button" id="pesquisar">Pesquisar</button>
            </div>
        </div>
    </div>
    <div class="separador"></div>
    <div id="frame_load"></div>
    <div id="resultado_pesquisa"></div>
</form>