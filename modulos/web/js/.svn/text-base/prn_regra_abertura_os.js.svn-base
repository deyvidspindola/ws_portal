jQuery(document).ready(function(){

    var ordrapermite_ordens_simultaneas = jQuery('#ordrapermite_ordens_simultaneas').is(':checked');
    var ordrapermite_tipo_motivo_distinto = jQuery('#ordrapermite_tipo_motivo_distinto').is(':checked');
    var editar = jQuery('#editar').val();
    var ordraostoid = jQuery('#ordraostoid').val();

    if(editar == 'editar') {
        if(!ordrapermite_ordens_simultaneas) {
            jQuery("#os_simultanea").hide();        
        }
        
        callRecuperaParametrizacoesCadastradas();

        atualizaSelectMotivo(ordraostoid, ordrapermite_tipo_motivo_distinto);
    } else {
        jQuery("#os_simultanea").hide();
        jQuery("#motivos_distintos").hide();
        jQuery("#regras_cadastradas").hide();
    }

    //confirmar dados principais
    jQuery("#btn_confirmar").click(function(event){

        event.preventDefault();
        
        jQuery('#mensagem_alerta').hide();
        jQuery('#mensagem_sucesso').hide();
        jQuery('#mensagem_erro').hide();

        var ordrapermite_ordens_simultaneas = jQuery('#ordrapermite_ordens_simultaneas').is(':checked');
        var ordrapermite_tipo_motivo_distinto = jQuery('#ordrapermite_tipo_motivo_distinto').is(':checked');
        var editar = jQuery('#editar').val();
        var ordraostoid = jQuery('#ordraostoid').val();
        var ordraoid = jQuery('#ordraoid').val();

        atualizaSelectMotivo(ordraostoid, ordrapermite_tipo_motivo_distinto);
        
        if(editar != 'editar') {

            jQuery.ajax({
                url: 'prn_regra_abertura_os.php',
                type: 'POST',
                data: {
                    acao: 'cadastrarParametrizacao',
                    ordraostoid: ordraostoid,
                    ordrapermite_ordens_simultaneas: ordrapermite_ordens_simultaneas,
                    ordrapermite_tipo_motivo_distinto: ordrapermite_tipo_motivo_distinto
                },
                success: function(data) {

                    if(data) {

                        if(data === 'ERRO') {

                            //validar campos
                            if(jQuery('#ordraostoid').val() == "") {
                                jQuery('#mensagem_erro').html("Os campos marcados com * são obrigatórios.");
                                jQuery('#mensagem_erro').show();
                            }

                        } else {

                            jQuery("#ordraostoid").prop( "disabled", true );
                            jQuery("#ordrapermite_ordens_simultaneas").prop( "disabled", true );
                            jQuery("#ordrapermite_tipo_motivo_distinto").prop( "disabled", true );
                            jQuery("#btn_confirmar").prop( "disabled", true );

                            //insere id da parametrização inserida
                            jQuery("#ordraoid").val(data);

                            //verifica bloco de OS Simultanea
                            if(ordrapermite_ordens_simultaneas){
                                jQuery("#os_simultanea").show();
                            }

                            jQuery("#motivos_distintos").show();

                            jQuery('#mensagem_erro').hide();
                        }
                    }
                }
            });
        } else {
            jQuery.ajax({
                url: 'prn_regra_abertura_os.php',
                type: 'POST',
                data: {
                    acao: 'editarParametrizacao',
                    ordraostoid: ordraostoid,
                    ordraoid: ordraoid,
                    ordrapermite_ordens_simultaneas: ordrapermite_ordens_simultaneas,
                    ordrapermite_tipo_motivo_distinto: ordrapermite_tipo_motivo_distinto
                },
                success: function(data) {

                    if(data) {
                        if(data === 'ERRO') {

                            //validar campos
                            if(jQuery('#ordraostoid').val() == "") {
                                jQuery('#mensagem_erro').html("Os campos marcados com * são obrigatórios.");
                                jQuery('#mensagem_erro').show();
                            }

                        } else {

                            jQuery("#ordraostoid").prop( "disabled", true );

                            //verifica bloco de OS Simultanea
                            if(ordrapermite_ordens_simultaneas){
                                jQuery("#os_simultanea").show();
                            } else {
                                jQuery("#os_simultanea").hide();
                            }

                            callRecuperaParametrizacoesCadastradas();

                            jQuery('#mensagem_erro').hide();
                        }
                    }
                }
            });
        }
    });

    //Incluir regra de OS Simultanea
    jQuery("#simultanea_adicionar").click(function(event){

        event.preventDefault();
        
        jQuery('#mensagem_alerta').hide();
        jQuery('#mensagem_sucesso').hide();
        jQuery('#mensagem_erro').hide();

        var ordraoid = jQuery('#ordraoid').val();
        var simultanea_tipo_permitido = jQuery('#simultanea_tipo_permitido').val();
        var simultanea_agendada = jQuery('#simultanea_agendada').val();
        var simultanea_situacao = simultanea_agendada == 1 ? jQuery('#simultanea_situacao').val() : "0";

        if(simultanea_tipo_permitido == "" ||
        simultanea_situacao == "" ||
        simultanea_agendada == "") {
            esconderMensagens();
            jQuery('#mensagem_erro').html("Os campos marcados com * são obrigatórios.");
            jQuery('#mensagem_erro').show();
            return;
        }

        jQuery.ajax({
            url: 'prn_regra_abertura_os.php',
            type: 'POST',
            data: {
                acao: 'cadastrarRegraSimultanea',
                ordraoid: ordraoid,
                simultanea_tipo_permitido: simultanea_tipo_permitido,
                simultanea_situacao: simultanea_situacao,
                simultanea_agendada: simultanea_agendada
            },
            success: function(data) {

                if(data) {
                    if(data == 'OK') {

                        jQuery("#ordraostoid").prop( "disabled", true );

                        //verifica bloco de OS Simultanea
                        if(ordrapermite_ordens_simultaneas){
                            jQuery("#os_simultanea").show();
                        }

                        jQuery("#regras_cadastradas").show();

                        jQuery('#mensagem_sucesso').html("Registro cadastrado com sucesso.");
                        jQuery('#mensagem_sucesso').show();
                    } else if(data == 'ERRO') {
                        jQuery('#mensagem_erro').html("Houve um erro no processamento dos dados.");
                        jQuery('#mensagem_erro').show();
                    } else {
                        jQuery('#mensagem_alerta').html("Parametrização já cadastrada.");
                        jQuery('#mensagem_alerta').show();
                    }

                    callRecuperaParametrizacoesCadastradas();
                }
            }
        });
    });

    //Incluir regra de motivos
    jQuery("#motivos_adicionar").click(function(event){

        event.preventDefault();
        
        jQuery('#mensagem_alerta').hide();
        jQuery('#mensagem_sucesso').hide();
        jQuery('#mensagem_erro').hide();

        var ordraoid = jQuery('#ordraoid').val();
        var motivo_tipo_permitido = jQuery('#motivo_tipo_permitido').val();
        var motivo_agendada = jQuery('#motivo_agendada').val();
        var motivo_situacao = motivo_agendada == 1 ? jQuery('#motivo_situacao').val() : "0";

        if(motivo_tipo_permitido == "" ||
        motivo_situacao == "" ||
        motivo_agendada == "") {
            esconderMensagens();
            jQuery('#mensagem_erro').html("Os campos marcados com * são obrigatórios.");
            jQuery('#mensagem_erro').show();
            return;
        }

        jQuery.ajax({
            url: 'prn_regra_abertura_os.php',
            type: 'POST',
            data: {
                acao: 'cadastrarMotivo',
                ordraoid: ordraoid,
                motivo_tipo_permitido: motivo_tipo_permitido,
                motivo_situacao: motivo_situacao,
                motivo_agendada: motivo_agendada
            },
            success: function(data) {

                if(data) {
                    if(data == 'OK') {

                        jQuery("#ordraostoid").prop( "disabled", true );

                        jQuery("#regras_cadastradas").show();

                        jQuery('#mensagem_sucesso').html("Registro cadastrado com sucesso.");
                        jQuery('#mensagem_sucesso').show();
                    } else if(data == 'ERRO') {
                        jQuery('#mensagem_erro').html("Houve um erro no processamento dos dados.");
                        jQuery('#mensagem_erro').show();
                    } else {
                        jQuery('#mensagem_alerta').html("Parametrização já cadastrada.");
                        jQuery('#mensagem_alerta').show();
                    }

                    callRecuperaParametrizacoesCadastradas();
                }
            }
        });
    });

    function atualizaSelectMotivo(idPrincipal, permiteDistinto) {
        $('#motivo_tipo_permitido').find('option').each(function(index,element){
            if(element.value != idPrincipal && !permiteDistinto) {
                $("#motivo_tipo_permitido option[value='" + element.value + "']").hide();
            } else {
                $("#motivo_tipo_permitido option[value='" + element.value + "']").show();
            }
        });

        $('#motivo_tipo_permitido').val('');
    }

    function callRecuperaParametrizacoesCadastradas() {
        var ordrapermite_tipo_motivo_distinto = jQuery('#ordrapermite_tipo_motivo_distinto').is(':checked');
        var ordraoid = jQuery('#ordraoid').val();

        jQuery.ajax({
            url: 'prn_regra_abertura_os.php',
            type: 'POST',
            dataType: "json",
            data: {
                acao: 'recuperaParametrizacoesCadastradas',
                ordraoid: ordraoid
            },
            success: function(data) {
                $("#tabela_regras > tbody").html("");
                $("#footer_regras").html("");
                var linhas = "";
                var corLinha = 'par';
                var totalLinhas = 0;
                for(var key in data) {
                    linhas +=
                            '<tr class="' + corLinha + '"><td>'
                                + data[key].tipo + '</td><td>'
                                + data[key].descricao + '</td><td>'
                                + (data[key].zero == 'f' ? 'Antes D0' : 'Em D0') + '</td><td>'
                                + (data[key].agendada == 'f' ? 'Não' : 'Sim') + '</td><td class="acao centro">'
                                + '<a class="excluir" data-tipo="' + data[key].tipo + '" data-id_parametro="' + data[key].id_parametro + '" href="#"><img class="icone" src="images/icon_error.png"  title="Excluir" alt="Excluir"></a>'
                            + '</td></tr>';

                    corLinha = corLinha == 'par' ? 'impar' : 'par';
                    totalLinhas++;
                }

                if(totalLinhas > 0) {
                    $('#tabela_regras').append(linhas);     
                    $('#footer_regras').append(totalLinhas <= 1 ? totalLinhas + " registro encontrado."
                                                            : totalLinhas + " registros encontrados.");
                } else {
                    $('#regras_cadastradas').hide();
                }
            }
        });
    }

    jQuery("#simultanea_agendada").change(function(){
        var simultanea_agendada = jQuery('#simultanea_agendada').val();

        jQuery('#simultanea_situacao').val("");

        if(simultanea_agendada == 0) {
           jQuery('#simultanea_situacao').prop("disabled", true);
        } else {
           jQuery('#simultanea_situacao').prop("disabled", false);
        }
    });

    jQuery("#motivo_agendada").change(function(){
        var motivo_agendada = jQuery('#motivo_agendada').val();

        jQuery('#motivo_situacao').val("");

        if(motivo_agendada == 0) {
           jQuery('#motivo_situacao').prop("disabled", true); 
        } else {
           jQuery('#motivo_situacao').prop("disabled", false);
        }
    });
   
    //botão novo
    jQuery("#bt_novo").click(function(){
        window.location.href = "prn_regra_abertura_os.php?acao=cadastrar";
    });

    //botão voltar
    jQuery("#bt_voltar").click(function(){
        window.location.href = "prn_regra_abertura_os.php";
    });

    /**
     * Acoes do icone excluir
     */
     jQuery("table").on('click','.excluir',function(event) {

        event.preventDefault();

        id = jQuery(this).data('ordraoid');
        elemento = this;

        jQuery("#mensagem_excluir").dialog({
            title: "Confirmação de Exclusão",
            resizable: false,
            modal: true,
            buttons: {
                "Sim": function() {
                jQuery( this ).dialog( "close" );

                    jQuery.ajax({
                        url: 'prn_regra_abertura_os.php',
                        type: 'POST',
                        data: {
                            acao: 'excluir',
                            ordraoid: id
                        },
                        success: function(data) {

                            if(data) {
                                esconderMensagens();

                                if(data == 'OK') {
                                    jQuery('#mensagem_sucesso').html("Registro excluído com sucesso.");
                                    jQuery('#mensagem_sucesso').show();
                                    jQuery(elemento).parent().parent().remove();

                                    corrigeTabela();

                                    aplicarCorLinha();

                                } else {
                                    jQuery('#mensagem_erro').html("Houve um erro no processamento dos dados.");
                                    jQuery('#mensagem_erro').show();
                                }
                            }

                        }
                    });

                },
                "Não": function() {
                    jQuery( this ).dialog( "close" );
                }
            }
        });
    });

    $('#tabela_regras').on('click','.excluir',function(event) {

        event.preventDefault();

        jQuery('#mensagem_alerta').hide();
        jQuery('#mensagem_sucesso').hide();
        jQuery('#mensagem_erro').hide();

        id = jQuery(this).data('id_parametro');
        tipo = jQuery(this).data('tipo');
        elemento = this;

        jQuery("#mensagem_excluir").dialog({
            title: "Confirmação de Exclusão",
            resizable: false,
            modal: true,
            buttons: {
                "Sim": function() {
                jQuery( this ).dialog( "close" );

                    jQuery.ajax({
                        url: 'prn_regra_abertura_os.php',
                        type: 'POST',
                        data: {
                            acao: 'excluirRegra',
                            id: id,
                            tipo: tipo
                        },
                        success: function(data) {
                            if(data) {

                                if(data == 'OK') {
                                    jQuery('#mensagem_sucesso').html("Registro excluído com sucesso.");
                                    jQuery('#mensagem_sucesso').show();
                                } else {
                                    jQuery('#mensagem_erro').html("Houve um erro no processamento dos dados.");
                                    jQuery('#mensagem_erro').show();
                                }
                            }
                            callRecuperaParametrizacoesCadastradas();
                        }
                    });

                },
                "Não": function() {
                    jQuery( this ).dialog( "close" );
                }
            }
        });
    });


    /**
     * Acoes do icone editar
     */
    jQuery("table").on('click','.editar',function(event) {

        event.preventDefault();

        id = jQuery(this).data('ordraoid');

        window.location.href = "prn_regra_abertura_os.php?acao=editar&ordraoid="+id;


    });




    /**
     * Esconde todas as mensagens e Erros
     */
    function esconderMensagens() {

        jQuery('#msg_alerta').hide();
        jQuery('#msg_sucesso').hide();
        jQuery('#msg_erro').hide();

        jQuery('.obrigatorio').removeClass('erro');
    }

    /*
     * Reorganzia as cores das linhas na lista
     */
    function aplicarCorLinha(){

        var cor = '';

        //remove cores
        jQuery('#bloco_resultado table tbody tr').removeClass('par');
        jQuery('#bloco_resultado table tbody tr').removeClass('impar');

        //aplica cores
        jQuery('#bloco_resultado table tbody tr').each(function(){
            cor = (cor == "impar") ? "par" : "impar";
            jQuery(this).addClass(cor);
        });
    }

    /*
     * Corrige informação da quantidade de registros encontrados.
     */
    function corrigeTabela(){

        var qtdLinhas = 0;

        //busca quantidade de linhas
        qtdLinhas = jQuery('#bloco_resultado table tbody tr').length;
        jQuery("#registros_encontrados").html("");

        if(qtdLinhas == 0){
            jQuery('.resultado').hide();
        }else if(qtdLinhas == 1){
            jQuery("#registros_encontrados").html("1 registro encontrado.");
        }else{
        jQuery("#registros_encontrados").html(qtdLinhas + " registros encontrados.");
        }
    }
   
});