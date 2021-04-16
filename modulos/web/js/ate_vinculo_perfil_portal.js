jQuery(document).ready(function(){

    var msg_campos_obrigatorios = "Existem campos obrigatórios não preenchidos.";
    var msg_erro                = "Houve um erro no processamento dos dados.";
    var msg_sucesso             = "Registro incluí­do com sucesso.";
    var msg_perfil_ativo        = "Já existe um perfil ativo para este vínculo.";
    var msg_sucesso_excluir     = "Registro inativado com sucesso.";

    var telaAtual = jQuery('#tela').val();

    /*
    * Busca de representante por autocomplete
    */
    jQuery(".representante").autocomplete({
        source: 'ate_vinculo_perfil_portal.php?acao=recuperarRepresentante',
        minLength: 3,
        response: function(event, ui) {

            var conteudoInput = jQuery(this).val();

            jQuery('#msg_alerta_autocomplete').fadeOut(function() {
                if (!ui.content.length) {
                    jQuery('#msg_alerta_autocomplete').html('Nenhum resultado encontrado com: ' + conteudoInput).fadeIn();
                }
            });

            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {
                    }
                }
            });

        },
        select: function( event, ui ) {

            jQuery("#aprrepoid").val(ui.item.id);
            jQuery('#repnome').val(ui.item.value);

            if(telaAtual == 'vinculo') {

                 /*
                * popular combo Instalador - AJAX
                */
                jQuery.ajax({
                    url: 'ate_vinculo_perfil_portal.php',
                    type: 'POST',
                    data: {
                        acao: 'recuperarInstalador',
                        aprrepoid: ui.item.id
                    },
                    success: function(data) {

                        data = JSON.parse(data);

                        if(data) {

                            var combo = "<option value=''>Escolha</option>";

                            if (data.length > 0) {
                                jQuery.each(data, function(i, val) {
                                    combo += "<option value='" + val.id + "'>" + val.nome + " | "+val.login + "</option>";
                                });
                            }

                            jQuery("#aprusuoid_instalador").html(combo);
                        }

                    }
                });
            }

        }
    });

    /*
    * Acoes do botao  Pesquisar
    */
    jQuery('form').on('click','#btn_pesquisar', function(){

        esconderMensagens();
        if(validarCamposObrigatorios()){

            var dataInicial = jQuery("#data_inicial").val();
            var dataFinal = jQuery("#data_final").val();
            var data1 = new Date();
            var data2 = new Date();

            //Somente sera obrigatorio se uma das datas estiver preenchida
            if(dataInicial != '' || dataFinal != '') {

                if(dataInicial != '' && dataFinal == '') {
                    jQuery('#data_final').val(dataInicial);
                    dataFinal = dataInicial;

                } else if (dataInicial == '' && dataFinal != '') {
                    jQuery('#data_inicial').val(dataFinal);
                    dataInicial = dataFinal

                }

                /*
                * Aplicar formato americano nas datas
                */
                dataInicialArray = dataInicial.split('/');
                dataFinalArray = dataFinal.split('/');

                dataInicialAmericana = dataInicialArray[2] + '-' + dataInicialArray[1] + '-' + dataInicialArray[0];
                dataFinalAmericana =  dataFinalArray[2] +'-'+ dataFinalArray[1] + '-' + dataFinalArray[0];

                data1 = data1.setTime(Date.parse(dataInicialAmericana));
                data2 = data2.setTime(Date.parse(dataFinalAmericana));


                if (data1 <= data2) {
                    jQuery('form').submit();
                } else {
                    jQuery('#msg_alerta').text("Data inicial não pode ser maior que a data final.");
                    jQuery('#msg_alerta').show();
                }
            } else {
                jQuery('form').submit();
            }
        }

    });

    /*
    * Acoes do botao  Confirmar
    */
    jQuery('form').on('click','#btn_confirmar', function(){

        esconderMensagens();

        if(validarCamposObrigatorios()){

            /*
            * Inclusao de registro via AJAX
            */
            jQuery.ajax({
                url: 'ate_vinculo_perfil_portal.php',
                type: 'POST',
                data: jQuery("form").serialize()+'&acao=incluirPerfil',
                success: function(data) {

                   esconderMensagens();

                    if(data != 'ERRO' && data != 'EXISTE') {
                        jQuery('#msg_sucesso').html(msg_sucesso);
                        jQuery('#msg_sucesso').show();
                        jQuery('#bloco_itens').show();

                        hoje = new Date();
                        dia = hoje.getDate();
                        mes = hoje.getMonth() + 1;
                        ano = hoje.getFullYear();
                        hora = hoje.getHours();
                        minuto = hoje.getMinutes();

                        //Cria e insere a nova linha na lista
                        html =  '<tr>';
                        html +=  '<td>'+jQuery("#usuoid option:selected").text()+'</td>';
                        html +=  '<td>'+jQuery("#repnome").val()+'</td>';
                        html +=  '<td>'+jQuery("#aprusuoid_instalador option:selected").text()+'</td>';
                        html +=  '<td class="centro">'+dia+'/'+mes+'/'+ano+' '+hora + ':' + minuto+'</td>';
                        html +=  '<td>'+jQuery("#aprmotivo").val()+'</td>';
                        html +=  '<td class="centro">'
                        html +=  '<img id="excluir_'+data+'" data-aproid="'+data+'" class="icone hand excluir" src="images/icon_error.png" title="Excluir" />';
                        html +=  '</td>';
                        html +=  '</tr>';

                        jQuery('#bloco_itens table').append(html);

                        //Aplica a classe de corres nas linhas
                        aplicarCorLinha();

                    } else if (data == 'EXISTE') {
                        jQuery('#msg_alerta').html(msg_perfil_ativo);
                        jQuery('#msg_alerta').show();
                    } else {
                        jQuery('#msg_erro').html(msg_erro);
                        jQuery('#msg_erro').show();
                    }

                }
            });
        }
    });

    /*
    * Acoes do botao Vincular Perfil
    */
    jQuery('form').on('click','#btn_vincular', function(){

        jQuery('#acao').val('index');
        jQuery('#tela').val('vinculo');
        jQuery('form').submit();

    });

    /*
    * Acoes do botao Voltar
    */
    jQuery('form').on('click','#btn_voltar', function(){

        jQuery('#acao').val('index');
        jQuery('#tela').val('pesquisa');
        jQuery('form').submit();

    });

    /*
    * Acoes do icone excluir
    */
     jQuery("table").on('click','.excluir',function(event) {

        event.preventDefault();

        id_vinculo = jQuery(this).data('aproid');
        elemento = this;

        jQuery("#msg_excluir").dialog({
          title: "Confirmação de Exclusão",
          resizable: false,
          modal: true,
          buttons: {
            "Sim": function() {
              jQuery( this ).dialog( "close" );

                jQuery.ajax({
                    url: 'ate_vinculo_perfil_portal.php',
                    type: 'POST',
                    data: {
                        acao: 'inativarPerfil',
                        aproid: id_vinculo
                    },
                    success: function(data) {

                        if(data) {
                          esconderMensagens();

                            if(data == 'OK') {
                                jQuery('#msg_sucesso').html(msg_sucesso_excluir);
                                jQuery('#msg_sucesso').show();
                                jQuery(elemento).remove();

                            } else {
                                jQuery('#msg_erro').html(msg_erro);
                                jQuery('#msg_erro').show();
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

    /*
    * valida campos indicados como obrigatorios, nao preenchidos.
    */
    function validarCamposObrigatorios() {

        var erros = 0;

        jQuery('form .obrigatorio').each(function(id,valor){

         elemento = jQuery('#'+valor.id);

          if(jQuery.trim(elemento.val()) == '') {

                elemento.addClass('erro');
                erros++;
            }
        });

        if(erros > 0){
            jQuery('#msg_alerta').html(msg_campos_obrigatorios);
            jQuery('#msg_alerta').show();
            return false;
        } else {
            return true;
        }
    }


    /*
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
        jQuery('#bloco_itens table tr').removeClass('par');
        jQuery('#bloco_itens table tr').removeClass('impar');


        //aplica cores
        jQuery('#bloco_itens table tr').each(function(){
            cor = (cor == "par") ? "impar" : "par";
            jQuery(this).addClass(cor);
        });
    }

});