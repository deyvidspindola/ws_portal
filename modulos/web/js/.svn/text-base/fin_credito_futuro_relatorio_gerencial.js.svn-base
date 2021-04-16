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
   
   //botão novo
   jQuery("#bt_novo").click(function(){
       window.location.href = "fin_credito_futuro_relatorio_gerencial.php?acao=cadastrar";
   });
   
   //botão voltar
   jQuery("#bt_voltar").click(function(){
       window.location.href = "fin_credito_futuro_relatorio_gerencial.php";
   });

   jQuery("#btn_retornar").click(function(){
        window.location.href = "principal.php?menu=Financas";
   });

   jQuery('#cliente_doc_F').mask('999.999.999-99');
   jQuery('#cliente_doc_J').mask('99.999.999/9999-99');
   jQuery('#numero_nf').mask('9?999999999',{placeholder:''});

   jQuery("#periodo_inclusao_ini").periodo("#periodo_inclusao_fim");

   jQuery("#btn_pesquisar").click(function() {
       jQuery("#sub_acao").val(jQuery(this).attr('data-pesquisa'));
       jQuery("#form").submit();
   });


   jQuery("#btn_gerarXls").click(function(){

        var tipoRelatorio = jQuery(this).attr("data-tipo");
        var tipoResultado = jQuery(this).attr("data-resultado");

        if (tipoRelatorio == 'A') {
            var action = "gerarXlsAnalitico";
        } else {
            if (tipoResultado == 'd') {
                var action = "gerarXlsSinteticoDiario";
            } else {
                var action = "gerarXlsSinteticoMensal";
            }
        }

        jQuery("#baixarXls").addClass('invisivel');
        jQuery("#loader_xls").removeClass('invisivel');

        jQuery.ajax({
            url: 'fin_credito_futuro_relatorio_gerencial.php',
            type: 'POST',
            data: {
                acao: action,
                tipoResultado: tipoResultado
            },
            success: function(data) {
                jQuery("#loader_xls").addClass('invisivel');
                jQuery("#baixarXls a").attr("href","download.php?arquivo=" + jQuery.trim(data));
                jQuery("#baixarXls").removeClass('invisivel');
            }
        });

   });



   /**
    * @button(id=btn-analitico-enviar-email)
    * @event OnClick
    */
    jQuery("#btn_enviarEmail").click(function(){

        if (jQuery(this).attr('data-tipo') == 'A') {
            var modal = '#dialog-email-analitico';
            var tipo_pesquisa = 'A';
        } else {
            var modal = '#dialog-email-sintetico';
            var tipo_pesquisa = 'S';
        }

        jQuery.ajax({
            url : 'fin_credito_futuro_relatorio_gerencial.php',
            type: 'POST',
            async: false,
            data: {
                acao: 'resetFormularioEnviarEmail',
                tipo_pesquisa: tipo_pesquisa
            },
            success: function(data) {

                if (typeof JSON != 'undefined') {
                    data = JSON.parse(data);
                } else {
                    data = eval('(' + data + ')');
                }

                jQuery("input[name='email_para']").val(data.email);
                jQuery("textarea[name='email_corpo']").val(data.conteudo);

            }
        });

        jQuery("input[name='email_para'], #lbl_email_para, textarea[name='email_corpo'], #lbl_email_corpo").removeClass('erro');
        jQuery("#email_mensagem_alerta").addClass('invisivel');

        jQuery("#loader_email").addClass('invisivel');
        jQuery("#form_envio_email, div.ui-dialog-buttonpane").removeClass('invisivel');

        jQuery(modal).dialog({
            autoOpen: false,
            minHeight: 300 ,
            maxHeight: 700 ,
            width: 550,
            modal: true,
            create: function (event, ui) {

                if(navigator.appName.indexOf('Internet Explorer') != -1 && document.compatMode == 'BackCompat') {                            
                    jQuery(modal).prev().css('width','585px');
                    jQuery(".ui-dialog-titlebar-close .ui-button-text").remove();
                    jQuery(".ui-dialog-titlebar-close").css('width','20px');
                    jQuery(".ui-dialog-titlebar-close").css('height','20px');
                }

            },
            buttons: {
                "Enviar": function() {

                    var obrigatorios = true;

                    jQuery("input[name='email_para'], #lbl_email_para, textarea[name='email_corpo'], #lbl_email_corpo").removeClass('erro');
                    jQuery("#email_mensagem_alerta").addClass('invisivel');

                    if (jQuery.trim(jQuery("input[name='email_para']").val()) == '') {
                        jQuery("input[name='email_para'], #lbl_email_para").addClass('erro');                        
                        obrigatorios = false;
                    }

                    if (jQuery.trim(jQuery("textarea[name='email_corpo']").val()) == '') {
                        jQuery("textarea[name='email_corpo'], #lbl_email_corpo").addClass('erro');                        
                        obrigatorios = false;
                    }

                    if (!obrigatorios) {
                        jQuery("#email_mensagem_alerta").removeClass('invisivel');
                        return false;
                    }

                    jQuery("#loader_email").removeClass('invisivel');
                    jQuery("#form_envio_email, div.ui-dialog-buttonpane").addClass('invisivel');


                    var parametros = jQuery("#form_envio_email").serialize();

                    jQuery.ajax({
                        url: 'fin_credito_futuro_relatorio_gerencial.php',
                        type: 'POST',
                        data: parametros,
                        success: function(data) {
                            
                            jQuery.fn.limpaMensagens();
                            
                            if (data == '1') {                                
                                jQuery("#mensagem_sucesso").html("E-mail enviado com sucesso.").removeClass('invisivel');
                                jQuery(modal).dialog('close');
                            } else {
                                jQuery("#mensagem_erro").html("Houve um erro no processamento dos dados.").removeClass('invisivel');
                                jQuery(modal).dialog('close');
                            }
                        }
                    });
                },
                "Cancelar": function() {
                 jQuery(modal).dialog('close');
             }
         }
     }).dialog('open');
    });

   /**
	* @input(type=select, id=tipo_relatorio)
	* @event OnChange
	*/
	jQuery("select[name='tipo_relatorio']").change(function(e) {

		var tipo = jQuery(this).val();
        jQuery.fn.limpaMensagens();

        jQuery("#resultado_pesquisa").fadeOut().html('');
        

        jQuery("input[name='tipo_resultado']").each(function(){
            if (jQuery(this).val() == 'd') {
                jQuery(this).attr('checked','checked');
            }
        });  

		if (tipo === "A") {

			jQuery(".sintetico").fadeOut('fast', function() {
				jQuery(".analitico").fadeIn('slow');	
			});
		} else {

			jQuery(".analitico").fadeOut('fast', function() {
				jQuery(".sintetico").fadeIn('slow');	
			});
		}
	});

	/**
	 * Seleciona o tipo de pessao (CNPJ/CPF)
	 */
	jQuery("input[name='tipo_pessoa']").change(function(){
		var tipo_pessoa = jQuery(this).val();

		jQuery("input[name='nome_cliente'], input[name='cliente_doc_F'], input[name='cliente_doc_J'], input[name='cliente_indicaor_id']").val('');
		jQuery("#doc_J, #doc_F").css('display','none');

		jQuery("#doc_" + tipo_pessoa).css('display','block');

	});


	/**
	 *	Autocomplete por Nome do cliente indicador
	 */
	 jQuery("input[name='nome_cliente']").autocomplete({
        source: "fin_credito_futuro.php?acao=buscarClienteNome",
        minLength: 2,        
        response: function(event, ui ) {            
            
            mudarTamanhoAutoComplete(ui.content.length);
            jQuery("#cliente_id").val('');
            escondeClienteNaoEncontrado();
                              
            if(!ui.content.length && jQuery.trim(jQuery(this).val()) != "") {
                mostraClienteNaoEncontrado();
                fixAutocomplete('input[name="nome_cliente"]');
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

            //no change
            jQuery('input[name="tipo_pessoa"]').each(function(){
                if( jQuery(this).val() ==  ui.item.tipo){
                    jQuery(this).attr('checked','checked');
                } 
            });

            jQuery("#doc_J, #doc_F").css('display','none');
            jQuery("#doc_" + ui.item.tipo).css('display','block');
            
            if (ui.item.tipo == 'J') {                
                jQuery('input[name="cliente_doc_J"]').val(ui.item.doc);                
            } else {
                jQuery('input[name="cliente_doc_F"]').val(ui.item.doc);
            }
        }        
    });


	//AUTOCOMPLETE DOCUMENTO CPF
    jQuery('input[name="cliente_doc_F"]').autocomplete({
        source: "fin_credito_futuro.php?acao=buscarClienteDoc&filtro=F",
        minLength: 2,        
        response: function(event, ui ) {    
            
            mudarTamanhoAutoComplete(ui.content.length);
            
            jQuery("#cliente_id").val('');
            escondeClienteNaoEncontrado();

            if(!ui.content.length && !jQuery.trim(jQuery(this).val().replace(/[^0-9]+/g, '')) == "") {
                mostraClienteNaoEncontrado();
                fixAutocomplete('input[name="cliente_doc_F"]');
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
            jQuery("input[name='nome_cliente']").val(ui.item.nome);
            jQuery("#cliente_id").val(ui.item.id);
           
        }
    });    
    
    //AUTOCOMPLETE DOCUMENTO CNPJ
    jQuery('input[name="cliente_doc_J"]').autocomplete({
        source: "fin_credito_futuro.php?acao=buscarClienteDoc&filtro=J",
        minLength: 2,        
        response: function(event, ui ) {       
            
            mudarTamanhoAutoComplete(ui.content.length);
            
            jQuery("#cliente_id").val('');
            escondeClienteNaoEncontrado();
            
            if(!ui.content.length && !jQuery.trim(jQuery(this).val().replace(/[^0-9]+/g, '')) == "") {
                mostraClienteNaoEncontrado();
                fixAutocomplete('input[name="cliente_doc_J"]');
            }
            
            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {}
                }
            });   
            
        },
        select: function( event, ui ) {
            jQuery("input[name='nome_cliente']").val(ui.item.nome);
            jQuery("#cliente_id").val(ui.item.id);
            
        }
    });

    jQuery("#nome_cliente, #cliente_doc_F, #cliente_doc_J").blur(function() {                                        
        if (jQuery.trim(jQuery("#cliente_id").val()) == '') {
            jQuery(this).val('');
        }
    });

    //Ao deletar caracter das busca, limpa os demais campos atrelados
    jQuery('#nome_cliente').keyup(function(e){
        if (e.which == 8 || e.which == 46) {
            jQuery('#cliente_doc_F, #cliente_doc_J, #cliente_id').val('');
        }
    });
    
    //Ao deletar caracter das busca, limpa os demais campos atrelados
    jQuery('#cliente_doc_F, #cliente_doc_J').keyup(function(e){
        if (e.which == 8 || e.which == 46) {
            jQuery('#nome_cliente, #cliente_id').val('');
        }
    });

    jQuery.fn.limpaMensagens = function() {
        jQuery('.mensagem').fadeOut();
        jQuery(".erro").not('.mensagem').removeClass("erro");
    }

   
});

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

function mostraClienteNaoEncontrado (msg) {

    msg_cliente = typeof msg && jQuery.trim(msg) != '' ? msg : 'Cliente não consta no cadastro.';

    jQuery("#mensagem_alerta").text(msg_cliente);
    jQuery("#mensagem_alerta").removeClass("invisivel");
    jQuery("#nome_cliente, #cliente_doc_J, #cliente_doc_F, #cliente_indicaor_id").val('');
}

/*
 * Mostra Esconde Mensagem cliente nao encontrado
 */
function escondeClienteNaoEncontrado() {
    jQuery("#mensagem_alerta").text("");
    jQuery("#mensagem_alerta").addClass("invisivel");
}


function fixAutocomplete(inputId) {

    jQuery(inputId).blur();
    jQuery(inputId).focus();

}