/*
 * Scripts - Manutenção de Itens Faturamento Unificado
 */
jQuery(document).ready(function(){

   /*
    * Acao do botao Incluir Itens
    */
    jQuery("#btn_incluir").click(function(){

        jQuery("#resultado_arquivo").hide();

       if(validarCampos()){
        jQuery("#acao").val("processarArquivo");
        jQuery("#modo").val("I");
        jQuery("#form").submit();
       }

    });

    /*
    * Acao do botao Remover Itens
    */
    jQuery("#btn_remover").click(function(){

       if(validarCampos()){
        jQuery("#acao").val("processarArquivo");
        jQuery("#modo").val("E");
        jQuery("#form").submit();
       }

    });

    /*
     * Validação de campos obrigatórios e data de vigência
     */
    function validarCampos(){

        jQuery('input.erro').removeClass('erro');
        jQuery('label.erro').removeClass('erro');
        jQuery('#mensagem_alerta').hide();
        jQuery('#mensagem_erro').hide();
        jQuery('#mensagem_sucesso').hide();

        var erros = false;
        var mesVigente;

        if (jQuery.trim(jQuery("#data_referencia").val()) == '') {
            jQuery("#data_referencia").addClass('erro');
            jQuery('label[for="data_referencia"]').addClass('erro');
            erros = true;
        }

        if (jQuery.trim(jQuery("#arquivo").val()) == '') {
            jQuery("#arquivo").addClass('erro');
            jQuery('label[for="arquivo"]').addClass('erro');
            erros = true;

        }

        if(erros) {
            jQuery('#mensagem_alerta').html('Existem campos obrigatórios não preenchidos.').show();
            return false;
        }
        else {

            mesVigente = validarMesVigente(jQuery('#data_referencia').val());

            if(!mesVigente) {
                jQuery("#data_referencia").addClass('erro');
                jQuery('label[for="data_referencia"]').addClass('erro');
                jQuery('#mensagem_alerta').html('Data de Referência inválida, informe uma data dentro do mês/ano vigentes.').show();
                return false;
            }
        }
        return true;
    }

    /*
     * Valida se o mes/ano de referência indicado é igual ao vigente.
     */
    function validarMesVigente(dataReferencia) {

        mesAnoReferencia = new String(dataReferencia);
        dataAtual = new Date();
        var mesAtual;
        var anoAtual;
        var mesReferencia;
        var anoReferencia;

        mesReferencia = mesAnoReferencia.substr(mesAnoReferencia.indexOf("/") + 1, 2);
        anoReferencia = mesAnoReferencia.substr(mesAnoReferencia.indexOf("/") + 4, 4);

        mesAtual = (dataAtual.getMonth() + 1);
        anoAtual = (dataAtual.getFullYear());

        if((mesReferencia != mesAtual) || (anoReferencia != anoAtual)) {
            return false;
        }
			return true;
    }

});
