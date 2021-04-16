jQuery(document).ready(function(){

    var msg_campos_obrigatorios = "Existem campos obrigatórios não preenchidos.";
    var msg_sucesso_excluir     = "Registro inativado com sucesso.";
    var msg_sucesso_editar      = "Registro alterado com sucesso.";
    var msg_erro                = "Houve um erro no processamento dos dados.";

   //botão novo
   jQuery("#bt_novo").click(function(){
       window.location.href = "marca.php?acao=cadastrar&retMarca=" + jQuery('#retMarca').val();
   });

   //botão voltar
   jQuery("#bt_voltar").click(function(){
       window.location.href = "marca.php?retMarca=" + jQuery('#retMarca').val();
   })

   jQuery('body').on('keyup blur', '#mcamarca', function() {

    valor = jQuery(this).val().replace(/[^a-z A-Z 0-9 \- .]/g, '');
    valor = valor.replace('  ', '');
    valor = valor.toUpperCase();

    jQuery(this).val( valor );

  });

   jQuery('body').on('click', '#bt_gravar', function() {

        elemento = jQuery('#mcamarca');
        elemento.removeClass('erro');

        jQuery('#mensagem_alerta').hide();

        if ( jQuery.trim(elemento.val()) == '' ) {

            elemento.addClass('erro');
            elemento.val('');

            jQuery('#mensagem_alerta').text(msg_campos_obrigatorios);
            jQuery('#mensagem_alerta').show();

        } else {
             jQuery('form').submit();
        }

   });

    jQuery('body').on('click', '#bt_retornar', function() {

        window.location.href = jQuery('#url_retorno').val();
   })


   /*
    * Acoes do icone excluir
    */
     jQuery("table").on('click','.excluir',function(event) {

        event.preventDefault();

        mcaoid = jQuery(this).data('mcaoid');

        jQuery("#msg_dialogo_exclusao").dialog({
          title: "Confirmação de Exclusão",
          resizable: false,
          modal: true,
          buttons: {
            "Sim": function() {

              jQuery( this ).dialog( "close" );

                jQuery.ajax({
                    url: 'marca.php',
                    type: 'POST',
                    data: {
                        acao: 'excluir',
                        mcaoid: mcaoid
                    },
                    success: function(data) {

                        if(data == 'OK') {
                            jQuery('#mensagem_sucesso').html(msg_sucesso_excluir);
                            jQuery('#mensagem_sucesso').show();
                            jQuery('#linha_' + mcaoid).remove();

                        } else {
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

        mcaoid = jQuery(this).data('mcaoid');

        window.location.href = "marca.php?acao=editar&mcaoid=" + mcaoid + "&retMarca=" + jQuery('#retMarca').val();;

    });



});