function atender(repoid, sagoid, num_os, tipoOs, classe, flag_agendamento) {

    jQuery.ajax({
         type     : 'get',
         url      : 'rel_produtos_solicitados_distribuicao.php?acao=modalGerenciarSolicitacao&repoid='+repoid+'&sagoid='+sagoid+'&tipoOs='+tipoOs+'&classe='+classe+'&num_os='+num_os,
         error    : function() {

                  jQuery('#div_mensagem_geral')
                    .removeClass('alerta sucesso invisivel')
                    .addClass('erro')
                    .html('Houve um erro na comunicação com o servidor.');
        },
        success : function(response){
            jQuery("#solicitar-produtos-form").html(response);
            jQuery("#solicitar-produtos-form").dialog({
                autoOpen: false,
                minHeight: 300 ,
                maxHeight: 'auto' ,
                width: '80%',
                modal: true,
                open: function(event, ui) {
                    jQuery('input:checkbox').show();
                },
                buttons: [
                    {
                        id: "atender",
                        text: "Atender",
                        disabled: (flag_agendamento == 't' ? true : false),
                        click: function() {
                        itens = new Array();
                        jQuery("input[type=checkbox][name='produto[]']:checked").each(function(){
                            itens.push(jQuery(this).val());
                        });

                        if(itens.length == 0){
                            alert("Nenhum item selecionado.");
                        }else{
                            atenderItens(itens,sagoid);
                        }
                        }
                    },
                    {
                        id: "recusar",
                        text: "Recusar",
                        disabled: false,
                        click: function() {
                        itens = new Array();
                        jQuery("input[type=checkbox][name='produto[]']:checked").each(function(){
                            itens.push(jQuery(this).val());
                        });

                        if(itens.length == 0){
                            alert("Nenhum item selecionado.");
                        }else{
                            justificativa(itens,sagoid);
                        }
                        }
                    },
                    {
                        id: "cancelar",
                        text: "Cancelar",
                        disabled: false,
                        click: function() {
                        jQuery(this).dialog("close");
                    }
                }
                ],
            }).dialog('open');
        }
    });
}

function atenderCampinas(repoid, sagoid, num_os, tipoOs, classe) {

    jQuery.ajax({
         type     : 'get',
         url      : 'rel_produtos_solicitados_distribuicao.php?acao=modalGerenciarSolicitacao&repoid='+repoid+'&sagoid='+sagoid+'&tipoOs='+tipoOs+'&classe='+classe+'&num_os='+num_os,
         error    : function() {

                  jQuery('#div_mensagem_geral')
                    .removeClass('alerta sucesso invisivel')
                    .addClass('erro')
                    .html('Houve um erro na comunicação com o servidor.');
        },
        success : function(response){
            jQuery("#solicitar-produtos-form").html(response);
            jQuery("#solicitar-produtos-form").dialog({
                autoOpen: false,
                minHeight: 300 ,
                maxHeight: 'auto' ,
                width: '80%',
                modal: true,
                buttons: {
                    "Cancelar": function() {
                        jQuery(this).dialog("close");
                    }
                }
            }).dialog('open');
        }
    });
}

function justificativa(itens, sagoid){
    jQuery("#recusar-produtos-form").dialog({
        autoOpen: false,
        minHeight: 200 ,
        maxHeight: 'auto' ,
        width: '400',
        modal: true,
        buttons: {
             "Confirmar": function() {
                var justificativa = jQuery('#justificativa').val();

                if(jQuery.trim(justificativa) == ""){
                    alert("Existem campos obrigatórios não preenchidos.");
                }else{
                    recusarItens(itens, sagoid);
                    jQuery(this).dialog("close");
                }
            },
            "Cancelar": function() {
                jQuery(this).dialog("close");
            }
        }
    }).dialog('open');
}

function recusarItens(itens, sagoid){
   jQuery.ajax({
        type : 'post',
        data :  {
            'itens' : itens,
            'justificativa' : jQuery('#justificativa').val()
        },
        url  : 'rel_produtos_solicitados_distribuicao.php?acao=recusar&sagoid='+sagoid,
        dataType : 'json',
        error : function(){

            jQuery('#div_mensagem_geral')
                    .removeClass('alerta sucesso invisivel')
                    .addClass('erro')
                    .html('Houve um erro na comunicação com o servidor.');
        },
        success : function(response){
            if(response.status){

                jQuery.each( itens, function( key, item ) {

                    jQuery('#status_'+item)
                        .removeClass('verde laranja')
                        .addClass('vermelho')
                        .html('Recusado');
                    jQuery('#checkbox_'+item).html('');
                });

                jQuery('#justificativa').val('');

                var itensPendentes = parseInt(jQuery('#qtd_itens_pendentes').val());
                jQuery('#qtd_itens_pendentes').val(itensPendentes - itens.length);

                if(parseInt(jQuery('#qtd_itens_pendentes').val()) < 1){
                    jQuery('#thtodos').html('');
                }

                if(response.statusSolicitacao != ''){
                    if(response.statusSolicitacao == 'Recusado'){
                        jQuery('#thtodos').html('');
                    }
                     jQuery('#status_solicitacao_'+sagoid).html(response.statusSolicitacao);
                }
            }
        }
    })
}

function atenderItens(itens, sagoid){

    jQuery.ajax({
        type : 'post',
        data :  {
            'itens' : itens
        },
        url  : 'rel_produtos_solicitados_distribuicao.php?acao=atender&sagoid='+sagoid,
        dataType : 'json',
        error : function(){

            jQuery('#div_mensagem_geral')
                    .removeClass('alerta sucesso invisivel')
                    .addClass('erro')
                    .html('Houve um erro na comunicação com o servidor.');
        },
        success : function(response){
            if(response.status){

                jQuery.each( itens, function( key, item ) {

                    jQuery('#status_'+item)
                        .removeClass('vermelho laranja')
                        .addClass('verde')
                        .html('Atendido');
                    jQuery('#checkbox_'+item).html('');
                });

                var itensPendentes = parseInt(jQuery('#qtd_itens_pendentes').val());
                    jQuery('#qtd_itens_pendentes').val(itensPendentes - itens.length);

                if(response.statusSolicitacao == 'Atendido'){
                      jQuery('#thtodos').html('');
                }else{

                    if(parseInt(jQuery('#qtd_itens_pendentes').val()) < 1){
                        jQuery('#thtodos').html('');
                    }
                }
                jQuery('#status_solicitacao_'+sagoid).html(response.statusSolicitacao);
            }
        }
    })
}

function marcardesmarcar(){
   if (jQuery("#todos").attr("checked")){
      jQuery('.checkbox').each(
         function(){
            jQuery(this).attr("checked", true);
         }
      );
   }else{
      jQuery('.checkbox').each(
         function(){
            jQuery(this).attr("checked", false);
         }
      );
   }
}

jQuery(document).ready(function() {

    jQuery('#sagdt_cadastro_inicial').periodo('#sagdt_cadastro_final');

    var script = 'rel_produtos_solicitados_distribuicao.php';

    jQuery('#ufuf').change(function() {

        jQuery('#acao').val('buscarCidade');

        jQuery('#div_mensagem_geral')
            .removeClass('alerta erro sucesso')
            .addClass('invisivel')
            .html(null);
        jQuery.ajax({
            type       : 'post',
            url        : script,
            data       : jQuery('#form_pesquisa').serialize(),
            dataType   : 'json',
            beforeSend : function() {
                jQuery('#ciddescricao').mostrarCarregando();
            },
            complete   : function() {
                jQuery('#ciddescricao').esconderCarregando();
            },
            error      : function() {
                jQuery('#div_mensagem_geral')
                    .removeClass('alerta sucesso invisivel')
                    .addClass('erro')
                    .html('Houve um erro na comunicação com o servidor.');
            },
            success    : function(response) {
                if (response.status === 'errorlogin' && response.redirect) {
                    location.href = response.redirect;
                } else if (response.status && response.html) {
                    jQuery('#ciddescricao').html(response.html);
                } else {
                    jQuery('#div_mensagem_geral')
                        .removeClass('alerta erro sucesso invisivel')
                        .addClass(response.mensagem.tipo)
                        .html(response.mensagem.texto);
                }
            }
        });
        return true;
    });

    jQuery('#bt_exportar, #bt_pesquisar').click(function() {
        var elemento = jQuery(this);

        jQuery('#acao').val('validarPesquisa');

        jQuery.ajax({
            type       : 'post',
            url        : script,
            data       : jQuery('#form_pesquisa').serialize(),
            dataType   : 'json',
            beforeSend : function() {
                esconderErros();

                jQuery('#div_mensagem_geral')
                    .removeClass('alerta erro sucesso invisivel')
                    .addClass('invisivel')
                    .html(null);

                jQuery('.alerta').hide();

                return true;
            },
            complete   : function() {
                return true;
            },
            error      : function() {
                jQuery('#div_mensagem_geral')
                    .removeClass('alerta erro sucesso invisivel')
                    .addClass('erro')
                    .html('Houve um erro na comunicação com o servidor.');

                return true;
            },
            success    : function(response) {
                if (response.status === 'errorlogin' && response.redirect) {
                    location.href = response.redirect;
                } else if (response.status) {
                    jQuery('#acao').val('pesquisar');

                    if (elemento.attr('id') === 'bt_exportar') {
                        jQuery('#acao').val('exportar');
                    }

                    jQuery('#form_pesquisa').submit();
                } else {
                    mostrarErros(response.campos);

                    jQuery('#div_mensagem_geral')
                        .removeClass('alerta erro sucesso invisivel')
                        .addClass(response.mensagem.tipo)
                        .html(response.mensagem.texto);
                }

                return true;
            }
        });

        return true;
    });

    jQuery('#tprel').change(function() {
        if (jQuery(this).val() == 'A') {
            jQuery('#bt_exportar').show();
        } else {
            jQuery('#bt_exportar').hide();
        }

        return true;
    });

});

function esconderErros() {
    jQuery('label, input, select, textarea').removeClass('erro');
    jQuery('input, select, textarea').removeAttr('title');

    return true;
}

function mostrarErros(campos) {
    esconderErros();

    jQuery.each(campos, function(indice, dados) {
        jQuery('#' + dados.campo)
            .attr('title', dados.mensagem)
            .addClass('erro');
        jQuery('#' + dados.campo)
            .prev('label')
            .addClass('erro');

        return true;
    });

    return true;
}