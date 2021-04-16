jQuery(document).ready(function(){

    var msg_campos_obrigatorios = "Existem campos obrigatÛrios n„o preenchidos.";
    var msg_sucesso_excluir     = "Registro inativado com sucesso.";
    var msg_sucesso_editar      = "Registro alterado com sucesso.";
    var msg_erro                = "Houve um erro no processamento dos dados.";

   //bot„o novo
   jQuery("#bt_novo").click(function(){
       window.location.href = "cad_motivo_teste_parcial.php?acao=cadastrar";
   });

   //bot„o voltar
   jQuery("#bt_voltar").click(function(){
       window.location.href = "cad_motivo_teste_parcial.php";
   })

   jQuery('body').on('keyup blur', '#mtpdescricao', function() {

    valor = jQuery(this).val().replace(/[^a-z A-Z 0-9 ·‡‚„ÈËÍÌÔÛÙıˆ˙ÁÒ¡¿¬√…»Õœ”‘’÷⁄« \- .]/g, '');
    valor = valor.replace('  ', '');

    jQuery(this).val( valor );

  });

   jQuery('body').on('click', '#bt_gravar', function() {

        elemento = jQuery('#mtpdescricao');
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

   /*
    * Acoes do icone excluir
    */
     jQuery("table").on('click','.excluir',function(event) {

        event.preventDefault();

        mtpoid = jQuery(this).data('mtpoid');

        jQuery("#msg_dialogo_exclusao").dialog({
          title: "ConfirmaÁ„o de Exclus„o",
          resizable: false,
          modal: true,
          buttons: {
            "Sim": function() {

              jQuery( this ).dialog( "close" );

                jQuery.ajax({
                    url: 'cad_motivo_teste_parcial.php',
                    type: 'POST',
                    data: {
                        acao: 'excluir',
                        mtpoid: mtpoid
                    },
                    success: function(data) {

                        if(data == 'OK') {
                            jQuery('#mensagem_sucesso').html(msg_sucesso_excluir);
                            jQuery('#mensagem_sucesso').show();
                            jQuery('#linha_' + mtpoid).remove();

                        } else {
                            alert( data );
                            jQuery('#mensagem_erro').html(msg_erro);
                            jQuery('#mensagem_erro').show();
                        }

                    }
                });

            },
            "N„o": function() {
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

        mtpoid = jQuery(this).data('mtpoid');

        window.location.href = "cad_motivo_teste_parcial.php?acao=editar&mtpoid=" + mtpoid;

    });



});