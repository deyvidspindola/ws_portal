jQuery(document).ready(function(){

  //para corrigir modal no modo quirks
    if(navigator.appName.indexOf('Internet Explorer') != -1 && document.compatMode == 'BackCompat') {

        var windowHeight = jQuery(document).height();

        jQuery('body').after('<style>\n\
                                button.ui-dialog-titlebar-close{\n\
                                    height: 2px !important !important; \n\
                                    top: 13px !important; \n\
                                    right: 3px !important; \n\
                                    padding: 0px !important; \n\
                                } \n\
                                .ui-dialog-titlebar .ui-dialog-titlebar-close {\n\
                                    height: 1px !important;\n\
                                }\n\
                                .ui-button-icon-only .ui-button-text, .ui-button-icons-only .ui-button-text{\n\\n\\n\
                                    padding: 0px !important; \n\\n\
                                    height: 0px !important; \n\
                                }\n\
                                .ui-widget-overlay{\n\
                                    position: absolute !important; \n\
                                    height: ' + windowHeight + 'px !important\n\
                                }\n\</style>');
    }  

	jQuery("#data_de").periodo("#data_ate");

   //botão novo
   jQuery("#bt_novo").click(function(){
     window.location.href = "rel_indicador_cancelamento.php?acao=cadastrar";
 });
   
   //botão voltar
   jQuery("#bt_voltar").click(function(){
     window.location.href = "rel_indicador_cancelamento.php";
 });


   //AUTOCOMPLETE PARA TELA DE CADASTRO DE CREDITO FUTURO- NOME DE CLIENtE
   jQuery( "#nome_cliente" ).autocomplete({
    source: "rel_indicador_cancelamento.php?acao=buscarClienteNome",
    minLength: 2,        
    response: function(event, ui ) {            

        mudarTamanhoAutoComplete(ui.content.length);
        jQuery("#cliente_id").val('');
        escondeClienteNaoEncontrado();

        if(!ui.content.length && jQuery.trim(jQuery(this).val()) != "") {
            mostraClienteNaoEncontrado(jQuery(this).val() + " não consta no cadastro.");
        }

        if(jQuery.trim(jQuery(this).val()) == "") {
            jQuery(this).val('');
        }

        jQuery(this).autocomplete("option", {
            messages: {
                noResults: '',
                results: function() {}
            }
        });   

    },
    select: function( event, ui ) {            

        jQuery("#cliente_id").val(ui.item.id);
        jQuery('#nome_cliente').val(ui.item.nome);
    }        
});


jQuery("#nome_cliente").blur(function() {
    /* Act on the event */
    if (jQuery.trim(jQuery("#cliente_id").val()) == '' ) {
        jQuery("#nome_cliente").val('');
    }

});



   jQuery(".listar_motivos").click(function() {

    var tipo_id  = jQuery(this).attr('data-tipo');
    var status   = jQuery(this).attr('data-status');
    var cliente  = jQuery("#cliente_id").val();
    var data_de  = jQuery("#data_de").val();
    var data_ate = jQuery("#data_ate").val();


    jQuery.ajax({
        async : 'false',
        url: 'rel_indicador_cancelamento.php',
        type: 'POST',
        data: {
            acao      : 'buscarMotivos',
            cliente_id: cliente,
            data_de   : data_de,
            data_ate  : data_ate,
            tipo      : tipo_id,
            status    : status
        },
        success: function(data) {

            if (typeof JSON != 'undefined') {
                data = JSON.parse(data);
            } else {
                data = eval('(' + data + ')');
            }

            var conteudo = "";

            jQuery(data).each(function(i,v){
                conteudo += "<tr>";
                conteudo += "<td>" + v.mtrdescricao + "</td>";
                conteudo += "<td class='centro'>" + v.qtd + "</td>";
                conteudo += "</tr>";
            });


            jQuery("#motivo_conteudo").html(conteudo);


            jQuery("#dialog-motivos").dialog({
                autoOpen: false,
                width: 350,
                modal: true,
                create: function (event, ui) {                    
                    
                    if(navigator.appName.indexOf('Internet Explorer') != -1 && document.compatMode == 'BackCompat') {                            
                        jQuery("#dialog-motivos").prev().css('width','350px');
                        jQuery(".ui-dialog-titlebar-close .ui-button-text").remove();
                        jQuery(".ui-dialog-titlebar-close").css('width','20px');
                        jQuery(".ui-dialog-titlebar-close").css('height','20px');
                    }

                },
                buttons: {
                    "Fechar": function() {
                        jQuery("#motivo_conteudo").html('');
                        jQuery("#dialog-motivos").dialog('close');
                    }
                },
                "Fechar": function() {
                    jQuery("#motivo_conteudo").html('');
                    jQuery("#dialog-motivos").dialog('close');
                }
            }).dialog('open');

}

});


});



});


/*
* Limita tamanho do autocomplete
*/
function mudarTamanhoAutoComplete(qtdOpcoes) {

    if (qtdOpcoes > 0) {

        var tamanhoOpcao = 23;//height de cada opção
        var tamanhoListagem = qtdOpcoes * tamanhoOpcao;
        jQuery('ul.ui-autocomplete').height(tamanhoListagem);
    }else{
        jQuery('ul.ui-autocomplete').height(0);
    }

}

/*
 * Mostra Mensagem cliente nao encontrado
 */
 function mostraClienteNaoEncontrado (msg) {

    msg_cliente = typeof msg && jQuery.trim(msg) != '' ? msg : 'Cliente não consta no cadastro.';

    jQuery("#mensagem_alerta").text(msg_cliente);
    jQuery("#mensagem_alerta").removeClass("invisivel");
    jQuery("#nome_cliente, #cpf, #cnpj, #contrato").val('');
}

/*
 * Mostra Esconde Mensagem cliente nao encontrado
 */
 function escondeClienteNaoEncontrado() {
    jQuery("#mensagem_alerta").text("");
    jQuery("#mensagem_alerta").addClass("invisivel");
}