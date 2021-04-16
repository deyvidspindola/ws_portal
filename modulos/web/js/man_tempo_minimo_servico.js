jQuery(document).ready(function(){

    var msg_campos_obrigatorios = "Existem campos obrigatórios não preenchidos.";
    var msg_sucesso_excluir     = "Registro inativado com sucesso.";
    var msg_sucesso_editar      = "Registro alterado com sucesso.";
    var msg_erro                = "Houve um erro no processamento dos dados.";

    jQuery('.detalhes').hide();

   //botão novo
   jQuery("#bt_novo").click(function(){
        window.location.href = "man_tempo_minimo_servico.php?acao=cadastrar";
   });

   //botão voltar
   jQuery("#bt_voltar").click(function(){
        window.location.href = "man_tempo_minimo_servico.php";
   })

   jQuery('body').on('click', '#bt_inserir', function() {

        isValidado = validarCamposObrigatorios();

        if( isValidado ) {
            jQuery('form').submit();
        }

   });

   jQuery('body').on('click', '#bt_pesquisar', function() {

        elemento_stmchave = jQuery('#stmchave');
        elemento_stmrepoid = jQuery('#stmrepoid');

        elemento_stmchave.removeClass('erro');
        elemento_stmrepoid.removeClass('erro');

        jQuery('#mensagem_alerta').hide();

        if ( jQuery.trim(elemento_stmchave.val()) == '' && jQuery.trim(elemento_stmrepoid.val()) == '') {

            elemento_stmchave.addClass('erro');
            elemento_stmrepoid.addClass('erro');

            jQuery('#mensagem_alerta').text(msg_campos_obrigatorios);
            jQuery('#mensagem_alerta').show();

        } else {
             jQuery('form').submit();
        }

   })


  jQuery('body').on('keyup blur', '.numero', function() {
    jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
  });

  jQuery('body').on('keyup blur', '.alfanumerico', function() {

    valor = jQuery(this).val().replace(/[^a-zA-Z0-9]/g, '');
    valor = valor.toUpperCase();

    jQuery(this).val( valor );

  });



  jQuery('body').on('change', '.radio', function() {

    if (jQuery(this).val() == 'A') {
        jQuery('#ordem_servico').val('');
        jQuery('#stmchave').val('');
        jQuery('#ordem_servico').addClass('desabilitado');
        jQuery('#ordem_servico').prop('disabled', true);

    } else {
        jQuery('#ordem_servico').removeClass('desabilitado');
        jQuery('#ordem_servico').removeProp('disabled');

        if(jQuery('#ordem_servico').val() != '') {

            gerarChave( jQuery('#ordem_servico').val() , jQuery('input[name=stmponto]:checked').val());
        }
    }

  });

  jQuery('body').on('change keyup blur', '.chave', function() {

    local = jQuery('input[name=stmponto]:checked').val();
    tipo = jQuery('#ostgrupo').val();
    grupo = jQuery('#agccodigo').val();
    peso = jQuery('#peso').val();

    jQuery('#stmchave').val( tipo + local + grupo + peso );

  });

  jQuery('#ordem_servico').on('blur',function(event) {

        event.preventDefault();

        var ordem_servico = jQuery(this).val();
        var stmponto = jQuery('input[name=stmponto]:checked').val();

        jQuery('#mensagem_alerta').hide();

        gerarChave(ordem_servico, stmponto);

    });


   /*
    * Acoes do icone excluir
    */
     jQuery("table").on('click','.excluir',function(event) {

        event.preventDefault();

        stmoid = jQuery(this).data('stmoid');

        jQuery("#msg_dialogo_exclusao").dialog({
          title: "Confirmação de Exclusão",
          resizable: false,
          modal: true,
          buttons: {
            "Sim": function() {

              jQuery( this ).dialog( "close" );

                jQuery.ajax({
                    url: 'man_tempo_minimo_servico.php',
                    type: 'POST',
                    data: {
                        acao: 'inativarRegistro',
                        stmoid: stmoid
                    },
                    success: function(data) {

                        if(data == 'OK') {
                            jQuery('#mensagem_sucesso').html(msg_sucesso_excluir);
                            jQuery('#mensagem_sucesso').show();
                            jQuery('#linha_' + stmoid).remove();

                        } else {
                            alert( data );
                            jQuery('#mensagem_erro').html(msg_erro);
                            jQuery('#mensagem_erro').show();
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
    * Acoes do icone editar
    */
     jQuery("table").on('click','.editar',function(event) {

        event.preventDefault();

        stmoid = jQuery(this).data('stmoid');

        window.location.href = "man_tempo_minimo_servico.php?acao=editarRegistro&stmoid=" + stmoid;

    });


    jQuery('body').on('click', '#mais, #menos', function(event) {

        event.preventDefault();

        id = jQuery(this).parent().attr('item-id');
        icone = jQuery(this).attr('id');

        if( icone == 'mais' ) {
            jQuery(this).hide();
            jQuery('.menos_'+id).show();
            jQuery('#det_'+id).show();
        } else {
            jQuery(this).hide();
            jQuery('.mais_'+id).show();
            jQuery('#det_'+id).hide();
        }


    });

    jQuery('body').on('click', '#bt_pesquisar', function() {

        elemento_stmchave = jQuery('#stmchave');
        elemento_stmrepoid = jQuery('#stmrepoid');

        elemento_stmchave.removeClass('erro');
        elemento_stmrepoid.removeClass('erro');

        jQuery('#mensagem_alerta').hide();

        if ( jQuery.trim(elemento_stmchave.val()) == '' && jQuery.trim(elemento_stmrepoid.val()) == '') {

            elemento_stmchave.addClass('erro');
            elemento_stmrepoid.addClass('erro');

            jQuery('#mensagem_alerta').text(msg_campos_obrigatorios);
            jQuery('#mensagem_alerta').show();

        } else {
             jQuery('form').submit();
        }

   })


  function gerarChave(ordem_servico, stmponto){
     jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                acao: 'montarChaveTempo',
                ordem_servico: ordem_servico,
                stmponto: stmponto
            },
            success: function(data) {

                if(data != '') {
                  jQuery('#stmchave').val(data);

                  isChaveExiste(data);

                } else {
                    jQuery('#mensagem_alerta').text("Ordem de Serviço inválida. Não foi possível determinar a chave.");
                    jQuery('#mensagem_alerta').show()
                };
            }
        });
  }

  function isChaveExiste(chave){

     jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                acao: 'pesquisarChaveEspecifica',
                stmchave: chave
            },
            success: function(data) {
                if(data == 0) {
                  dialogo(chave);
                }
            }
        });
  }

  function dialogo(chave) {

        jQuery("#msg_dialogo").dialog({
          title: "Chave sem cadastro",
          resizable: false,
          modal: true,
          buttons: {
            "Sim": function() {
              jQuery( this ).dialog( "close" );
              window.location.href = "man_tempo_minimo_servico.php?acao=cadastrar&stmchave=" + chave;
            },
            "Não": function() {
              jQuery( this ).dialog( "close" );
            }
          }
        });
  }

    /*
    * valida campos indicados como obrigatorios, nao preenchidos.
    */
    function validarCamposObrigatorios() {


        var erros = 0;

        jQuery('form .obrigatorio').each(function(id,valor){

         elemento = jQuery('#'+valor.id);
         elemento.removeClass('erro');

          if(jQuery.trim(elemento.val()) == '') {

                elemento.addClass('erro');
                erros++;
            }
        });

        if(erros > 0){
            jQuery('#mensagem_alerta').html(msg_campos_obrigatorios);
            jQuery('#mensagem_alerta').show();
            return false;
        } else {
            return true;
        }
    }

});