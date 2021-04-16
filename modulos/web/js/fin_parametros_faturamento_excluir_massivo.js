/**
 * Mensagens de retorno
 */
var MSG_SELECIONAR_PARAMETRO = "Pelo menos um parâmetro deve ser selecionado.";
var MSG_SELECIONAR_NIVEL = "Nível deve ser selecionado.";
var MSG_INFORMA_ARQUIVO = "Arquivo é obrigatório.";

/**
 * OnLoad
 */
jQuery(document).ready(function () {

    /**
     * @tag input(id=btn_retornar)
     * @Event OnClick
     */
    jQuery('#btn_retornar_massivo').click(function () {
        //window.location.href = window.location.href;
        jQuery('input[name="acao"]').val('retornar');
        jQuery('form[name="frm-excluir-massivo"]').submit();
    });

    /**
     * @tag input(id=btn_confirmar_massivo)
     * @Event OnClick
     */

    jQuery('#btn_confirmar_massivo').click(function () {

        // Limpa marcadores de campos
        jQuery('.erro').removeClass('erro');

        removeAlerta();
        /**
         * Nivel
         */
        var nivel = jQuery('input[name="nivel_excluir"]:checked').is(':checked');

        /**
         * isencao
         */
        var isento = jQuery('input[name="isento_excluir"]').is(':checked');

        /**
         * valor
         */

        var valor = jQuery('input[name="valor_excluir"]:checked').is(':checked');

        /**
         * desconto
         */
        var desconto = jQuery('input[name="desconto_excluir"]').is(':checked');

        /**
         * Exclusão massiva de contratos
         */
        var arqcontratos = jQuery('input[name="arqcontratos_excluir"]').val();

        if (!nivel) {
            criaAlerta(MSG_SELECIONAR_NIVEL);
            return false;
        }

        if (!isento && !valor && !desconto) {
            criaAlerta(MSG_SELECIONAR_PARAMETRO);
            return false;
        }

        if (arqcontratos.length == 0) {
            criaAlerta(MSG_INFORMA_ARQUIVO);
            return false;
        }

        jQuery('input[name="acao"]').val('excluir_massivo');
        jQuery('form[name="frm-excluir-massivo"]').submit();
        
        // limpar 
        jQuery('input[name="isento_excluir"]').prop("checked", false)
        jQuery('input[name="desconto_excluir"]').prop("checked", false)
        jQuery('input[name="valor_excluir"]').prop("checked", false)
        jQuery('input[name="arqcontratos_excluir"]').val("");
    });

});


function criaAlerta(msg, status) {
    $("#mensagem").show();
    $("#mensagem").text(msg).removeAttr('class').addClass('mensagem alerta').addClass(status).show();
}


function removeAlerta() {
    $("#mensagem").hide();
}


