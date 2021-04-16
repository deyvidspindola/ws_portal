jQuery(document).ready(function(){
   
   //botão novo
   jQuery("#bt_novo").click(function(){
       window.location.href = "cad_versao_trava5_roda.php?acao=cadastrar";
   });
   
   //botão voltar
   jQuery("#bt_voltar").click(function(){
       window.location.href = "cad_versao_trava5_roda.php";
   });
   
    //botao editar
    jQuery("table").on('click','.editar',function(event) {

        event.preventDefault();

        id = jQuery(this).data('trvoid');

        window.location.href = "cad_versao_trava5_roda.php?acao=editar&trvoid="+id;
    });
    
    // botão excluir
    jQuery("table").on('click','.excluir',function(event) {

        event.preventDefault();

        id = jQuery(this).data('trvoid');

        jQuery("#mensagem_excluir").dialog({
            title: "Confirmação de Exclusão",
            resizable: false,
            modal: true,
            buttons: {
                "Sim": function() {
                    /*jQuery( this ).dialog( "close" );
                    jQuery.ajax({
                        url: 'cad_versao_trava5_roda.php',
                        type: 'POST',
                        data: {
                            acao: 'excluir',
                            trvoid: id
                        },
                        success: function(data) {

                            if(data) {
                                esconderMensagens();

                                if(data == 'OK') {
                                    jQuery('#mensagem_sucesso').html("Registro excluído com sucesso.");
                                    jQuery('#mensagem_sucesso').show();                                    
                                } else {
                                    jQuery('#mensagem_erro').html("Houve um erro no processamento dos dados.");
                                    jQuery('#mensagem_erro').show();
                                }
                            }

                        }
                    });*/
                    jQuery( "#acao" ).val("excluir");
                    jQuery( "#trvoid" ).val(id);
                    jQuery( "#form" ).submit();

                },
                "Não": function() {
                    jQuery( this ).dialog( "close" );
                }
            }
        });
    });
   
    // Esconde todas as mensagens e erros     
    function esconderMensagens() {
        jQuery('#msg_alerta').hide();
        jQuery('#msg_sucesso').hide();
        jQuery('#msg_erro').hide();
        jQuery('.obrigatorio').removeClass('erro');
    }
});