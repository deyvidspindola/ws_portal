<?php
require_once '_header.php';
$control = new FinControleFiscalRemessas();
$view = $control->setRetornaView();
?> 
<head>    
    <link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
    <script language="Javascript" type="text/javascript" src="modulos/web/js/fin_controle_fiscal_retiradas.js"></script> 
    <script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js" charset="utf-8"></script> 
    <script language="javascript">
        function verificaNumero(e) {
            if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        }
        jQuery(document).ready(function () {
            jQuery("#pesquisa_contrato").keypress(verificaNumero);
            jQuery("#pesquisa_nf_retorno_simbolico").keypress(verificaNumero);
            jQuery("#pesquisa_nf_remessa_simbolico").keypress(verificaNumero);

            carregarRepresentante();
        });

       function carregarRepresentante() {

            jQuery.ajax({
                url: 'fin_controle_fiscal_remessas.php',
                type: 'post',
                data: {
                    acao: 'carregaRepresentante'
                },
                dataType: 'json',
                success: function (data) {
                    
                    jQuery('#representante').append($('<option>', {
                            value: '',
                            text: 'SELECIONE'
                        }));
                    
                    jQuery.each(data, function (index, el) {
                        jQuery('#representante').append($('<option>', {
                            value: el.repoid,
                            text: el.repnome
                        }));
                    });

                }
            });
        }
        
        function pesquisarClientes() {

            var tamanho = jQuery("#pesquisa_nome_cliente").val().length;

            if (tamanho >= 3) {

                jQuery('#msgalerta').html('').hide();

                jQuery('#cliente_load').html('<center><img src="images/loading.gif" alt="" style="width: 50px;"/></center>');

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
                            jQuery('#cliente').append($('<option>', {
                                value: el.clioid,
                                text: el.clinome
                            }));
                        });

                        jQuery('#cliente_load').html('');

                        jQuery("#cliente").show();
                    }
                });
            } else {
                $("#msgalerta").html("Para pesquisar o cliente digite pelo menos 3 letras.").showMessage();
            }
        }

        function selecionaCliente() {

            var id = jQuery('select[name="cliente"] option:selected').val();

            var texto = jQuery('select[name="cliente"] option:selected').text();

            jQuery("#pesquisa_id_cliente").val(id);

            jQuery("#pesquisa_nome_cliente").val(texto);

            jQuery("#pesquisa_nome_cliente").css("background", "#F0F0F0");

            jQuery("#pesquisa_nome_cliente").prop("disabled", true);

            jQuery("#cliente").hide();

            jQuery("#pesquisa_cliente").hide();

            jQuery("#pesquisa_cliente_limpar").show();

        }

        function pesquisarClientesLimpar() {

            jQuery('#pesquisa_id_cliente').val('');

            jQuery('#pesquisa_nome_cliente').val('');

            jQuery("#cliente").html('<options></options>');

            jQuery("#pesquisa_cliente_limpar").hide();

            jQuery("#pesquisa_cliente").show();

            jQuery("#pesquisa_nome_cliente").prop("disabled", false);

            jQuery("#pesquisa_nome_cliente").css("background", "");

        }
    </script>
</head>
<form name="form" id="form" method="POST" action="">
    <input type="hidden" name="acao" id="acao" value="equipamentoMovelPesquisar" />

    <?php require_once '_msgPrincipal.php'; ?>
    <ul class="bloco_opcoes">
        <li><a href="fin_controle_fiscal_remessas.php?acao=pesquisa">Remessas</a></li>
        <li><a href="fin_controle_fiscal_remessas.php?acao=instalacoes">Instalações</a></li>
        <li class="ativo"><a href="fin_controle_fiscal_remessas.php?acao=retiradas">Retiradas</a></li>
        <li><a href="fin_controle_fiscal_remessas.php?acao=equipamentoMovel">Equipamento Móvel</a></li>
    </ul>
    <div class="bloco_titulo">Ordem de Serviço Retiradas</div>
    <div class="bloco_conteudo">
        <div class="formulario">

           

            <div class="bloco_titulo">Dados para Pesquisa</div>
            <div class="bloco_conteudo">
                <div class="formulario">
                    <table border="0" cellspacing="2" cellpadding="0" bgcolor="#FFFFFF" align="center" width="100%">        
                        <tr>
                            <td>
                                <table width="100%">
                                    <tr>

                                        <td>   
                                            <label>Per&iacute;odo (<font color="#FF0000">*</font>)</label>
                                            <div class="campo data">
                                                <input class="campo"  type="text" name="pesquisa_data_inicio" id="pesquisa_data_inicio" maxlength="10" value="<?php echo $dt_ini; ?>" />
                                            </div>
                                            <div style="margin-top: 5px !important;" class="campo label-periodo">à</div>
                                            <div class="campo data">
                                                <input  class="campo"  type="text" name="pesquisa_data_fim" id="pesquisa_data_fim" maxlength="10" value= "<?php echo $dt_fim; ?>" />
                                            </div>      
                                        </td>

                                        <td>
                                            <label for="pesquisa_contrato">Contrato</label>
                                            <input type="text" id="pesquisa_contrato" name="pesquisa_contrato" size='10'>
                                        </td>

                                        <td>
                                            <label for="pesquisa_nf_retorno_simbolico">NF Retorno Simbólico</label>
                                            <input type="text" id="pesquisa_nf_retorno_simbolico" name="pesquisa_retorno_simbolico" size='10'>
                                        </td>
                                        <td>
                                            <label for="pesquisa_possui_nf_retorno_simbolico">Possu&iacute; NF de Retorno Simbólico ?</label>
                                            <select id="pesquisa_possui_nf_retorno_simbolico" name="pesquisa_possui_nf_retorno_simbolico">
                                                <option value="">Todos</option>
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </td>
                                        

                                    </tr>

                                    <tr>

                                        <td>
                                            <label for="pesquisa_select_representante">Representante</label>
                                            <select name="representante" id="representante" style="width: 240px;"></select>
                                        </td>

                                        <td>
                                            <label for="pesquisa_n_serie">N&ordm; S&eacute;rie</label>
                                            <input type="text" id="pesquisa_n_serie" name="pesquisa_n_serie" size='10'>
                                        </td>

                                        <td>
                                            <label for="pesquisa_nf_remessa_simbolico">NF Remessa Simbólico</label>
                                            <input type="text" id="pesquisa_nf_remessa_simbolico" name="pesquisa_remessa_simbolico" size='10'>
                                        </td>

                                        <td>
                                            <label for="pesquisa_possui_nf_remessa_simbolico">Possu&iacute; NF de Remessa Simbólico ?</label>
                                            <select id="pesquisa_possui_nf_remessa_simbolico" name="pesquisa_possui_nf_remessa_simbolico">
                                                <option value="">Todos</option>
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </td>

                                    </tr>

                                    <tr>

                                        <td width="40%">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <label>Cliente</label>
                                                        <input type="hidden" id="pesquisa_id_cliente" name="pesquisa_id_cliente" style="width: 240px">
                                                        <input type="text" id="pesquisa_nome_cliente" name="pesquisa_nome_cliente" style="width: 240px">&nbsp;&nbsp;
                                                    </td>
                                                    <td valign="bottom">
                                                        <input type="button" id="pesquisa_cliente" name="pesquisa_cliente" value="Pesquisar" onclick="javascript:pesquisarClientes()">
                                                    </td>
                                                    <td valign="bottom">
                                                        <input type="button" id="pesquisa_cliente_limpar" name="pesquisa_cliente_limpar" value="Limpar" onclick="javascript:pesquisarClientesLimpar()" style="display: none;">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <select name="cliente" id="cliente" multiple="multiple" onclick="selecionaCliente()" style="width: 240px; height: 200px; display: none;"></select>
                                                    </td>
                                                    <td colspan="2">
                                                        <div id="cliente_load"></div>
                                                    </td>
                                                </tr>
                                            </table>
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