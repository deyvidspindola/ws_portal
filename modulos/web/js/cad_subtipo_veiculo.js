jQuery(document).ready(function(){

    var msg_campos_obrigatorios = "Existem campos obrigatÛrios n„o preenchidos.";
    var msg_sucesso_excluir     = "Registro inativado com sucesso.";
    var msg_sucesso_editar      = "Registro alterado com sucesso.";
    var msg_erro                = "Houve um erro no processamento dos dados.";

    //bot„o novo
    jQuery("#bt_novo").click(function(){
        window.location.href = "cad_subtipo_veiculo.php?acao=cadastrar";
    });

    //bot„o voltar
    jQuery("#bt_voltar").click(function(){
        window.location.href = "cad_subtipo_veiculo.php";
    })

    jQuery('body').on('keyup blur', '#vstdescricao', function() {

        valor = jQuery(this).val().replace(/[^a-z A-Z 0-9 ·‡‚„ÈËÍÌÔÛÙıˆ˙ÁÒ¡¿¬√…»Õœ”‘’÷⁄« \- .]/g, '');
        valor = valor.replace('  ', '');
        valor = valor.toUpperCase();

        jQuery(this).val( valor );

    });

    jQuery('body').on('click', '#bt_gravar', function() {
       jQuery('form').submit();
    });

    /*
     * Acoes do icone excluir
     */
    jQuery("table").on('click','.excluir',function(event) {

        event.preventDefault();

        vstoid = jQuery(this).data('vstoid');

        jQuery("#msg_dialogo_exclusao").dialog({
            title: "ConfirmaÁ„o de Exclus„o",
            resizable: false,
            modal: true,
            buttons: {
                "Sim": function() {

                    jQuery( this ).dialog( "close" );

                    jQuery.ajax({
                        url: 'cad_subtipo_veiculo.php',
                        type: 'POST',
                        data: {
                            acao: 'excluir',
                            vstoid: vstoid
                        },
                        success: function(data) {

                            if(data == 'OK') {
                                jQuery('#mensagem_sucesso').html(msg_sucesso_excluir);
                                jQuery('#mensagem_sucesso').show();
                                jQuery('#linha_' + vstoid).remove();
                                aplicarCorLinha();

                            } else {
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

        vstoid = jQuery(this).data('vstoid');

        window.location.href = "cad_subtipo_veiculo.php?acao=editar&vstoid=" + vstoid;

    });

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