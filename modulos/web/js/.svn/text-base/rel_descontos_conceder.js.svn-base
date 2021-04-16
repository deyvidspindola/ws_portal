jQuery(document).ready(function(){
   
   //botão novo
   jQuery("#bt_novo").click(function(){
       window.location.href = "rel_descontos_conceder.php?acao=cadastrar";
   });
   
   //botão voltar
   jQuery("#bt_voltar").click(function(){
       window.location.href = "rel_descontos_conceder.php";
   });

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

   //AUTOCOMPLETE 
    jQuery( "#nome_cliente" ).autocomplete({
        source: "fin_credito_futuro.php?acao=buscarClienteNome",
        minLength: 2,        
        response: function(event, ui ) {            
            
            mudarTamanhoAutoComplete(ui.content.length);
            jQuery("#cliente_id").val('');
            //habilitaDesabilitaAvancarStep1();
            escondeClienteNaoEncontrado();
                  
            if(!ui.content.length && jQuery.trim(jQuery(this).val()) != "") {
                mostraClienteNaoEncontrado();
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
            
            //no change
            jQuery('input[name="cadastro[tipo_pessoa]"], input[name="tipo_pessoa"]').each(function(){
                if( jQuery(this).val() ==  ui.item.tipo){
                    jQuery(this).attr('checked','checked');
                } 
            });
            
            jQuery("#cliente_id").val(ui.item.id);
            jQuery('#nome_cliente').val(ui.item.nome);
            //habilitaDesabilitaAvancarStep1();
            
            if (ui.item.tipo == 'J') {
                jQuery('#juridicoDoc').removeClass('invisivel');
                jQuery('#fiscaDoc').addClass('invisivel');
                jQuery('#juridicoNome input, #juridicoDoc input, #fiscaNome input, #fiscaDoc input').val('');               
                
                jQuery('#cnpj').val(ui.item.doc);
                
            } else {
                jQuery('#juridicoNome, #juridicoDoc').addClass('invisivel');
                jQuery('#fiscaNome, #fiscaDoc').removeClass('invisivel');
                jQuery('#juridicoNome input, #juridicoDoc input, #fiscaNome input, #fiscaDoc input').val('');               
                
                jQuery('#cpf').val(ui.item.doc);
            }
        }        
    });
    
    //Validações e mascaras     
    jQuery('#cpf').mask('999.999.999-99');
    jQuery('#cnpj').mask('99.999.999/9999-99');
    
    //AUTOCOMPLETE NOME PJ
    jQuery( "#razao_social" ).autocomplete({
        source: "fin_credito_futuro.php?acao=buscarClienteNome&filtro=J",
        minLength: 2,        
        response: function(event, ui ) {            
            
            mudarTamanhoAutoComplete(ui.content.length);
            jQuery("#cliente_id, #cfoclioid").val('');
            //habilitaDesabilitaAvancarStep1();
            escondeClienteNaoEncontrado();
                  
            if(!ui.content.length && jQuery.trim(jQuery(this).val()) != "") {
                mostraClienteNaoEncontrado();
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
            //no change
            jQuery("#cnpj").val(ui.item.doc);
            jQuery("#cliente_id, #cfoclioid").val(ui.item.id);
            //habilitaDesabilitaAvancarStep1();
        }        
    });
    
    //AUTOCOMPLETE NOME PF
    jQuery( "#nome" ).autocomplete({
        source: "fin_credito_futuro.php?acao=buscarClienteNome&filtro=F",
        minLength: 2,       
        response: function(event, ui ) {  
            
            mudarTamanhoAutoComplete(ui.content.length);
            jQuery("#cliente_id, #cfoclioid").val('');
            //habilitaDesabilitaAvancarStep1();
            escondeClienteNaoEncontrado();
            
            if(!ui.content.length && jQuery.trim(jQuery(this).val()) != "") {
                mostraClienteNaoEncontrado();
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
            
            jQuery("#cpf").val(ui.item.doc);
            jQuery("#cliente_id, #cfoclioid").val(ui.item.id);
            //habilitaDesabilitaAvancarStep1();
        }
    });
    
    //AUTOCOMPLETE DOCUMENTO CPF
    jQuery( "#cpf" ).autocomplete({
        source: "fin_credito_futuro.php?acao=buscarClienteDoc&filtro=F",
        minLength: 2,        
        response: function(event, ui ) {    
            
            mudarTamanhoAutoComplete(ui.content.length);
            
            jQuery("#cliente_id, #cfoclioid").val('');
            //habilitaDesabilitaAvancarStep1();
            
            escondeClienteNaoEncontrado();
            
            if(!ui.content.length && !jQuery.trim(jQuery(this).val().replace(/[^0-9]+/g, '')) == "") {
                mostraClienteNaoEncontrado();
            }
            
            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {}
                }
            });   
           
        },
        select: function( event, ui ) {
            //no change
            jQuery("#nome, #nome_cliente").val(ui.item.nome);
            jQuery("#cliente_id, #cfoclioid").val(ui.item.id);
            //habilitaDesabilitaAvancarStep1();
        }
    });    
    
    //AUTOCOMPLETE DOCUMENTO CNPJ
    jQuery( "#cnpj" ).autocomplete({
        source: "fin_credito_futuro.php?acao=buscarClienteDoc&filtro=J",
        minLength: 2,        
        response: function(event, ui ) {       
            
            mudarTamanhoAutoComplete(ui.content.length);
            
            jQuery("#cliente_id, #cfoclioid").val('');
            //habilitaDesabilitaAvancarStep1();
            
            escondeClienteNaoEncontrado();
            
            if(!ui.content.length && !jQuery.trim(jQuery(this).val().replace(/[^0-9]+/g, '')) == "") {
                mostraClienteNaoEncontrado();
            }
            
            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {}
                }
            });   
            
        },
        select: function( event, ui ) {
            jQuery("#razao_social, #nome_cliente").val(ui.item.nome);
            jQuery("#cliente_id, #cfoclioid").val(ui.item.id);
            //habilitaDesabilitaAvancarStep1();
        }
    });
    
    //Ao deletar caracter das busca, limpa os demais campos atrelados
    jQuery('#nome, #razao_social, #nome_cliente').keyup(function(e){
        if (e.which == 8 || e.which == 46) {
            jQuery('#cpf, #cnpj, #cliente_id, #contrato, #cfoclioid').val('');
            //habilitaDesabilitaAvancarStep1();
        }
    });
    
    //Ao deletar caracter das busca, limpa os demais campos atrelados
    jQuery('#cpf, #cnpj').keyup(function(e){
        if (e.which == 8 || e.which == 46) {
            jQuery('#nome, #razao_social, #cliente_id, #contrato, #nome_cliente, #cfoclioid').val('');
            //habilitaDesabilitaAvancarStep1();
        }
    });

    //Seleciona o tipo de pessoa (PJ||PF)
    jQuery('input[name="cadastro[tipo_pessoa]"], input[name="tipo_pessoa"]').change(function(){
        
        
        
        var tipo_pessoa = jQuery(this).val();
        
        jQuery('.ui-helper-hidden-accessible').html('');
        
        //Limpa o ID do cliente para a busca
        jQuery("#cliente_id, #cfoclioid").val('');
        jQuery("#contrato").val('');
        jQuery("#nome_cliente").val('');
        //habilitaDesabilitaAvancarStep1();
        
        if(tipo_pessoa == 'J') {
            jQuery('#juridicoNome, #juridicoDoc').removeClass('invisivel');
            jQuery('#fiscaNome, #fiscaDoc').addClass('invisivel');
            jQuery('#juridicoNome input, #juridicoDoc input, #fiscaNome input, #fiscaDoc input').val('');
        } else {
            jQuery('#juridicoNome, #juridicoDoc').addClass('invisivel');
            jQuery('#fiscaNome, #fiscaDoc').removeClass('invisivel');
            jQuery('#juridicoNome input, #juridicoDoc input, #fiscaNome input, #fiscaDoc input').val('');
        }
                        
    });

    //Ao selecionar os tipos de descontos, mudar os box de intervarlos de valor percetual ou valor
    jQuery(".tipo_desconto").change(function(){
        

        jQuery("#tipo_descont_1 .campo label, #tipo_descont_1 .campo input, #tipo_descont_2 .campo label, #tipo_descont_2 .campo input").removeClass('erro');

        if (jQuery(this).val() == '1') {
            jQuery("#tipo_descont_1").removeClass('invisivel');
            jQuery("#tipo_descont_2").addClass('invisivel');
            jQuery("#tipo_descont_2 input").val('');
        } else if (jQuery(this).val() == '2') {
            jQuery("#tipo_descont_1").addClass('invisivel');
            jQuery("#tipo_descont_2").removeClass('invisivel');
            jQuery("#tipo_descont_1 input").val('');
        } else {
            jQuery("#tipo_descont_1").addClass('invisivel');
            jQuery("#tipo_descont_2").addClass('invisivel');
            jQuery("#tipo_descont_1 input").val('');
            jQuery("#tipo_descont_2 input").val('');
        }
        
    });
    
    
    //Trecho de código que corrige falhas do maskmoney no onpaste e keypress

    jQuery(".moeda,.porcentagem").maskMoney({
        symbol:'', 
        thousands:'.', 
        decimal:',', 
        symbolStay: false, 
        showSymbol:false,
        precision:2, 
        defaultZero: false,
        allowZero: false
    });

    jQuery(".moeda,.porcentagem").on('paste',function(){
        var id = jQuery(this).attr('id');
        var maxlength = jQuery(this).attr('maxlength');
        
        setTimeout(function(){
            
            var v = jQuery("#"+id).val();
            var vMasc = maskValue(v);
            var nV = v;
            
            if (vMasc.length > maxlength) {
                nV = "";
                var maxChar = (maxlength - (vMasc.length - maxlength));
                var vArray = v.split("");
                var i = 0;
                for ( i ; i <= maxChar ; i++) {
                    nV += vArray[i];
                }   
            }
            
            jQuery("#"+id).val( maskValue(nV) );
            
        },10);
    });
    
    jQuery(".moeda,.porcentagem").on('keyup',function(){
        var id = jQuery(this).attr('id');
        jQuery("#"+id).val( maskValue(jQuery("#"+id).val()) );
    });

    jQuery('#bt_gerar_xls').click(function(){
        jQuery('#acao').val('gerar_xls');        
        jQuery('#form').submit();
    });
    
    settings = {};
    settings.allowNegative = false;
    settings.decimal = ',';
    settings.precision = 2;
    settings.thousands = '.';

    function maskValue(v) {
        
        var strCheck = '0123456789';
        var len = v.length;
        var a = '', t = '', neg='';

        if(len!=0 && v.charAt(0)=='-'){
            v = v.replace('-','');
            if(settings.allowNegative){
                neg = '-';
            }
        }

        for (var i = 0; i<len; i++) {
            if ((v.charAt(i)!='0') && (v.charAt(i)!=settings.decimal)) break;
        }

        for (; i<len; i++) {
            if (strCheck.indexOf(v.charAt(i))!=-1) a+= v.charAt(i);
        }

        var n = parseFloat(a);
        n = isNaN(n) ? 0 : n/Math.pow(10,settings.precision);
        t = n.toFixed(settings.precision);

        i = settings.precision == 0 ? 0 : 1;
        var p, d = (t=t.split('.'))[i].substr(0,settings.precision);
        for (p = (t=t[0]).length; (p-=3)>=1;) {
            t = t.substr(0,p)+settings.thousands+t.substr(p);
        }

        return (settings.precision>0)
        ? neg+t+settings.decimal+d+Array((settings.precision+1)-d.length).join(0)
        : neg+t;
    }

    jQuery('#bt_enviar_email').click(function(){
        jQuery("#dialog-motivos").dialog({
            autoOpen: false,
            width: 444,
            modal: true,
            create: function (event, ui) {                    
                
                if(navigator.appName.indexOf('Internet Explorer') != -1 && document.compatMode == 'BackCompat') {                            
                    jQuery("#dialog-motivos").prev().css('width','465px');
                    jQuery(".ui-dialog-titlebar-close .ui-button-text").remove();
                    jQuery(".ui-dialog-titlebar-close").css('width','20px');
                    jQuery(".ui-dialog-titlebar-close").css('height','20px');
                }

                var dt_ini = jQuery('#periodo_inclusao_ini').val();                
                var dt_fim = jQuery('#periodo_inclusao_fim').val();
                var nome_usuario = jQuery('#nm_usuario').val();

                dt_ini = jQuery.trim(dt_ini) != "" ? ' - ' + dt_ini : '';
                dt_fim = jQuery.trim(dt_fim) != "" ? ' a ' + dt_fim : '';

                jQuery('#assunto').val('Relatório Gerencial de Descontos a Conceder'+ dt_ini + dt_fim);

                jQuery('#corpo').val('Prezado(s),\n\nSegue, em anexo, o Relatório Gerencial de Descontos a Conceder referente o período de '+ dt_ini + dt_fim +'. \n\nAtt.\n'+ nome_usuario +'');

            },
            buttons: {
                "Enviar": function() {
                    jQuery('#acao').val('enviar_email');
                    //jQuery('#form').submit();

                    var serialize = jQuery('#form').serialize() + '&' + jQuery('#form_email').serialize();

                    jQuery.ajax({
                        url: 'rel_descontos_conceder.php',
                        type: 'post',
                        data: serialize,
                        beforeSend: function() {
                            jQuery('.ui-button').attr('disabled', 'disabled');
                            jQuery('body').css('cursor', 'wait');
                        },
                        success: function() {
                            //redireciona para usar o flash message
                            window.location.href = 'rel_descontos_conceder.php'
                        }
                    });

                    
                },
                "Cancelar": function() {                    
                    jQuery("#dialog-motivos").dialog('close');
                    jQuery("#dialog-motivos").dialog('destroy');
                }
            }            
        }).dialog('open');
    });
   
});

var htmlEscapes = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#x27;',
    '/': '&#x2F;'
};

// Regex containing the keys listed immediately above.
var htmlEscaper = /[&<>"'\/]/g;

// Escape a string for HTML interpolation.
_escape = function(string) {
    return ('' + string).replace(htmlEscaper, function(match) {
        return htmlEscapes[match];
    });
};

/*
* Limita tamanho do autocomplete
 */
function mudarTamanhoAutoComplete(qtdOpcoes) {
  
    if (qtdOpcoes > 0) {
        
        var tamanhoOpcao = 23;//height de cada opção
        var tamanhoListagem = qtdOpcoes * tamanhoOpcao;
        if (tamanhoListagem > 166) {
            jQuery('ul.ui-autocomplete').height(166);
        } else {
            jQuery('ul.ui-autocomplete').height(tamanhoListagem);
        }
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