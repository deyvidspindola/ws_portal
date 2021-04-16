jQuery(document).ready(function(){
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
        jQuery('#bloco_itens table tbody tr').removeClass('par');
        jQuery('#bloco_itens table tbody tr').removeClass('impar');

        //aplica cores
        jQuery('#bloco_itens table tbody tr').each(function(){
            cor = (cor == "impar") ? "par" : "impar";
            jQuery(this).addClass(cor);
        });
    }


    jQuery('#form').on('submit', function() {
        if(jQuery('#form #placa').val().length > 0 && jQuery('#form #placa').val().length < 4) {
            jQuery('#mensagem_alerta').text(jQuery('#form #placa').data('msg-error')).removeClass('invisivel');
            return false;
        }
    });

    /*
     * Tratamento somente numeros inteiros
     */
    jQuery('body').on('keyup blur', '#mes', function() {
        jQuery(this).val(jQuery(this).val().replace(/[^z0-9]/g, ''));
    });

    /*
     * Tratamento somente numeros inteiros
     */
    jQuery('body').on('keyup blur', '#ano', function() {
        jQuery(this).val(jQuery(this).val().replace(/[^z0-9]/g, ''));
    });

    /*
     * Tratamento somente numeros inteiros
     */
    jQuery('body').on('keyup blur', '#contrato', function() {
        jQuery(this).val(jQuery(this).val().replace(/[^z0-9]/g, ''));
    });


});