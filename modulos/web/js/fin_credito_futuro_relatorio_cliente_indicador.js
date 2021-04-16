jQuery(document).ready(function(){
    
    //faz o tratamento para os campos de perídos 
    jQuery("#cfcidt_inclusao_de").periodo("#cfcidt_inclusao_ate");
    jQuery("#cfcidt_avaliacao_de").periodo("#cfcidt_avaliacao_ate");

    jQuery("#cfcitermo_pesquisa").mask('9?999999999',{placeholder:''});
    
    //Validações e mascaras     
    jQuery('#cpf').mask('999.999.999-99');
    jQuery('#cnpj').mask('99.999.999/9999-99');
        
	/**
	 * Combo cliente indicador
	 * 
	 */
    //Seleciona o tipo de pessoa (PJ||PF)
    jQuery('input[name="cadastro[tipo_pessoa]"], input[name="tipo_pessoa"]').change(function(){
        
        var tipo_pessoa = jQuery(this).val();
        
        jQuery('.ui-helper-hidden-accessible').html('');
        
        //Limpa o ID do cliente para a busca
        jQuery("#cliente_id, #cfciclioid").val('');
        
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

    //AUTOCOMPLETE NOME PJ
    jQuery( "#razao_social" ).autocomplete({
        source: "fin_credito_futuro_relatorio_cliente_indicador.php?acao=buscarClienteNome&filtro=J",
        minLength: 2,        
        response: function(event, ui ) {            
            
            mudarTamanhoAutoComplete(ui.content.length);
            
            jQuery("#cliente_id, #cfciclioid").val('');
            
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
            jQuery("#cliente_id, #cfciclioid").val(ui.item.id);
            
        }        
    });
    
    //AUTOCOMPLETE NOME PF
    jQuery( "#nome" ).autocomplete({
        source: "fin_credito_futuro_relatorio_cliente_indicador.php?acao=buscarClienteNome&filtro=F",
        minLength: 2,       
        response: function(event, ui ) {  
            
            mudarTamanhoAutoComplete(ui.content.length);
            jQuery("#cliente_id, #cfciclioid").val('');
            
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
            jQuery("#cliente_id, #cfciclioid").val(ui.item.id);
            
        }
    });
    
    //AUTOCOMPLETE DOCUMENTO CPF
    jQuery( "#cpf" ).autocomplete({
        source: "fin_credito_futuro_relatorio_cliente_indicador.php?acao=buscarClienteDoc&filtro=F",
        minLength: 2,        
        response: function(event, ui ) {    
            
            mudarTamanhoAutoComplete(ui.content.length);
            
            jQuery("#cliente_id, #cfciclioid").val('');
            
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
            jQuery("#nome").val(ui.item.nome);
            jQuery("#cliente_id, #cfciclioid").val(ui.item.id);
            
        }
    });    
    
    //AUTOCOMPLETE DOCUMENTO CNPJ
    jQuery( "#cnpj" ).autocomplete({
        source: "fin_credito_futuro_relatorio_cliente_indicador.php?acao=buscarClienteDoc&filtro=J",
        minLength: 2,        
        response: function(event, ui ) {       
            
            mudarTamanhoAutoComplete(ui.content.length);
            
            jQuery("#cliente_id, #cfciclioid").val('');
            
            
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
            jQuery("#razao_social").val(ui.item.nome);
            jQuery("#cliente_id, #cfciclioid").val(ui.item.id);
            
        }
    });

	/**
	 * Combo cliente indicado
	 * 
	 */
    //Seleciona o tipo de pessoa (PJ||PF)
    jQuery('input[name="cadastro[tipo_pessoa_indicado]"], input[name="tipo_pessoa_indicado"]').change(function(){
        
        var tipo_pessoa = jQuery(this).val();
        
        jQuery('.ui-helper-hidden-accessible').html('');
        
        //Limpa o ID do cliente para a busca
        jQuery("#cliente_id_indicado, #conclioid").val('');
        
        if(tipo_pessoa == 'J') {
            jQuery('#juridicoNomeIndicado, #juridicoDocIndicado').removeClass('invisivel');
            jQuery('#fiscaNomeIndicado, #fiscaDocIndicado').addClass('invisivel');
            jQuery('#juridicoNomeIndicado input, #juridicoDocIndicado input, #fiscaNomeIndicado input, #fiscaDocIndicado input').val('');
        } else {
            jQuery('#juridicoNomeIndicado, #juridicoDocIndicado').addClass('invisivel');
            jQuery('#fiscaNomeIndicado, #fiscaDocIndicado').removeClass('invisivel');
            jQuery('#juridicoNomeIndicado input, #juridicoDocIndicado input, #fiscaNomeIndicado input, #fiscaDocIndicado input').val('');
        }
                        
    });

    //AUTOCOMPLETE NOME PJ
    jQuery( "#razao_social_indicado" ).autocomplete({
        source: "fin_credito_futuro_relatorio_cliente_indicador.php?acao=buscarClienteNome&filtro=J",
        minLength: 2,        
        response: function(event, ui ) {            
            
            mudarTamanhoAutoComplete(ui.content.length);
            
            jQuery("#cliente_id_indicado, #conclioid").val('');
            
            escondeClienteNaoEncontrado();
                  
            if(!ui.content.length && jQuery.trim(jQuery(this).val()) != "") {
                mostraClienteNaoEncontradoIndicado();
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
            jQuery("#cnpj_indicado").val(ui.item.doc);
            jQuery("#cliente_id_indicado").val(ui.item.id);
            
        }        
    });
    
    //AUTOCOMPLETE NOME PF
    jQuery( "#nome_indicado" ).autocomplete({
        source: "fin_credito_futuro_relatorio_cliente_indicador.php?acao=buscarClienteNome&filtro=F",
        minLength: 2,       
        response: function(event, ui ) {  
            
            mudarTamanhoAutoComplete(ui.content.length);
            
            jQuery("#cliente_id_indicado, #conclioid").val('');
            
            escondeClienteNaoEncontrado();
            
            if(!ui.content.length && jQuery.trim(jQuery(this).val()) != "") {
            	mostraClienteNaoEncontradoIndicado();
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
            
            jQuery("#cpf_indicado").val(ui.item.doc);
            jQuery("#cliente_id_indicado").val(ui.item.id);
            
        }
    });
    
    //AUTOCOMPLETE DOCUMENTO CPF
    jQuery( "#cpf_indicado" ).autocomplete({
        source: "fin_credito_futuro_relatorio_cliente_indicador.php?acao=buscarClienteDoc&filtro=F",
        minLength: 2,        
        response: function(event, ui ) {    
            
            mudarTamanhoAutoComplete(ui.content.length);
            
            jQuery("#cliente_id_indicado, #conclioid").val('');
            
            escondeClienteNaoEncontrado();
            
            if(!ui.content.length && !jQuery.trim(jQuery(this).val().replace(/[^0-9]+/g, '')) == "") {
            	mostraClienteNaoEncontradoIndicado();
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
            jQuery("#nome_indicado").val(ui.item.nome);
            jQuery("#cliente_id_indicado").val(ui.item.id);
            
        }
    });    
    
    //AUTOCOMPLETE DOCUMENTO CNPJ
    jQuery( "#cnpj_indicado" ).autocomplete({
        source: "fin_credito_futuro_relatorio_cliente_indicador.php?acao=buscarClienteDoc&filtro=J",
        minLength: 2,        
        response: function(event, ui ) {       
            
            mudarTamanhoAutoComplete(ui.content.length);
            
            jQuery("#cliente_id_indicado, #conclioid").val('');
            
            
            escondeClienteNaoEncontrado();
            
            if(!ui.content.length && !jQuery.trim(jQuery(this).val().replace(/[^0-9]+/g, '')) == "") {
            	mostraClienteNaoEncontradoIndicado();
            }
            
            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {}
                }
            });   
            
        },
        select: function( event, ui ) {
            jQuery("#razao_social_indicado").val(ui.item.nome);
            jQuery("#cliente_id_indicado").val(ui.item.id);
            
        }
    });
    
    // auxiliares

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
        jQuery("#razao_social, #nome, #cpf, #cnpj, #contrato").val('');
        jQuery("#razao_social_indicado, #nome_indicado, #cpf_indicado, #cnpj_indicado, #cfcitermo_pesquisa").val('');
    }

    /*
     * Mostra Mensagem cliente nao encontrado - CLIENTE INDICADO
     */
    function mostraClienteNaoEncontradoIndicado (msg) {

        msg_cliente = typeof msg && jQuery.trim(msg) != '' ? msg : 'Cliente não consta no cadastro.';

        jQuery("#mensagem_alerta").text(msg_cliente);
        jQuery("#mensagem_alerta").removeClass("invisivel");
        jQuery("#contrato").val('');
        jQuery("#razao_social_indicado, #nome_indicado, #cpf_indicado, #cnpj_indicado, #cfcitermo_pesquisa").val('');
    }

    /*
     * Mostra Esconde Mensagem cliente nao encontrado
     */
    function escondeClienteNaoEncontrado() {
        jQuery("#mensagem_alerta").text("");
        jQuery("#mensagem_alerta").addClass("invisivel");
    }

    jQuery("#btn_gerar_xls").click(function(){
	    jQuery("form#form #acao").val('gerarXls');
	    jQuery("form#form").submit();
	});

});