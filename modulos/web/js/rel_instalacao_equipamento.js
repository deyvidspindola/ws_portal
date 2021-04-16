jQuery(document).ready(function(){

    jQuery('#dataInicial').periodo('#dataFinal');

    jQuery('#dataInicial, #dataFinal').blur( function() {
        validarIntervaloDatas();
    });
    
    jQuery('#dataInicial, #dataFinal').change( function() {
        validarIntervaloDatas();
    });


    jQuery('#bt_gerar').click(function() {
        var erro = false;

        jQuery.each(jQuery('.campoObrigatorio'), function(key, value) {
            if(value.value === '') {
                jQuery(value).prev().addClass('erro');
                jQuery(value).addClass('erro');
                erro = true;
            } else {
                jQuery(value).prev().removeClass('erro');
                jQuery(value).removeClass('erro');
            }
        });

        if(erro === true) {
            jQuery('#mensagem_alerta').html("Existem campos obrigatórios não preenchidos.").fadeIn();
        } else {
            jQuery('#loader_1').show();
            jQuery('#form').submit();
        }
    });
   
});

jQuery(document).load(function(){
    jQuery('#loader_1').hide();
});

function converterParaObjetoDate(objeto) {
    var dia = parseInt(objeto.val().substring(0,2));
    var mes = parseInt(objeto.val().substring(3,5));
    var ano = parseInt(objeto.val().substring(6,10));

    return new Date(ano, mes-1, dia);
}

function validarIntervaloDatas() {
        var dataInicial = jQuery('#dataInicial');
        var dataFinal = jQuery('#dataFinal');

        if(dataInicial.val() === '' || dataFinal.val() === '') {
            return false;
        }

        var diferenca = converterParaObjetoDate(dataFinal) - converterParaObjetoDate(dataInicial);
        var diferencaDias = diferenca / 1000 / 60 / 60 / 24;

        if(diferencaDias > 90) {
            jQuery('#mensagem_alerta').html("Não é possível gerar relatório com intervalo maior que 90 dias.").fadeIn();
            dataInicial.val('');
            dataFinal.val('');
        }

        return false;
}