jQuery(document).ready(function() {

    jQuery('#modtpadrao').click(function(){

        var repoid = jQuery('#repoid_busca').val();
        var modtoid = jQuery('#modtoid').val();

        if(repoid == ''){
            jQuery('#modtpadrao').prop('checked', false);
        }

        if (jQuery('#modtpadrao').prop('checked')) {

            //Verificar se ja existe um modal padrao cadastrado para o RT
            jQuery.ajax({
                url: 'man_cad_modal_transporte.php',
                type: 'POST',
                data: {
                    acao: 'verificarRecorrenciaModalPadrao',
                    repoid_busca: repoid,
                    modtoid: modtoid,
                    acao_novo: jQuery('#acao_novo').val()
                },
                success: function(data) {

                    data = JSON.parse(data);

                    if (data) {

                        jQuery("#msg_confirmar_atualizar").html("A modal padrão para esse representante é "+data+". Deseja alterar?");

                        //Tela de dialogo
                        jQuery("#msg_confirmar_atualizar").dialog({
                            title: "Confirmar Alteração",
                            resizable: false,
                            modal: true,
                            buttons: {
                                "Sim": function() {

                                    jQuery(this).dialog("close");
                                    jQuery('#alterar_modal_padrao').val('S');

                                },
                                "Não": function() {
                                    jQuery(this).dialog("close");
                                    jQuery('#modtpadrao').prop('checked', false);
                                }
                            }
                        });

                    }

                }
            });
        }
    });

});