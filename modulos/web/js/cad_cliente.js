/**
 * @author	Keidi Nienkotter
 * @email 	knienkotter@brq.com
 * @since	29/07/2013
 */

jQuery(function() {
	
	/**
	 * CONTROLE DE ACESSO
	 */
	// formularios
	var array_cadastra_cliente = new Array( 
			'#cad_cliente_principal', 
			'#cad_endereco', 
			'#cad_cliente_particularidades', 
			'#cad_gerenciadora', 
			'#cad_cliente_contato', 
			'#cad_cliente_segmentacao',
			'#cad_cliente_operacoes',  
			'#forma_pagamento',  
			'#form_beneficios',  
			'.resultado_pesquisa' );
	
	var array_cliente_dados_cobranca = new Array(
			'#caracteristicaPagamento', 
			'#caracteristicaVencimentoCt', 
			'#fieldsetFaturamento' );
	
	var array_acess_quad_obr_financ_cliente = new Array(
			'#fieldset_obrigacao_financeira_cliente' );
	
	var array_cliente_dados_fiscais = new Array(
			'#fieldsetImpostoRetidoNaFonte' );
	
	// controle de acesso
	if(cadastra_cliente != 1){				
		for ( var i = 0; i < array_cadastra_cliente.length; i = i + 1 ) {			
			var item =  array_cadastra_cliente[ i ];
			desabilita_campos(item);
			desabilita_botoes(item);		 
		}			
	} 
	
	if(cliente_dados_cobranca != 1){		
		for ( var j = 0; j < array_cliente_dados_cobranca.length; j = j + 1 ) {			
			var item =  array_cliente_dados_cobranca[ j ];
			desabilita_campos(item);		 
		}	
	}
	
	if(acess_quad_obr_financ_cliente != 1){		
		for ( var j = 0; j < array_acess_quad_obr_financ_cliente.length; j = j + 1 ) {			
			var item =  array_acess_quad_obr_financ_cliente[ j ];
			desabilita_campos(item);		
			desabilita_botoes(item);	 
		}	
	}
	
	if(cliente_dados_fiscais != 1){		
		for ( var j = 0; j < array_cliente_dados_fiscais.length; j = j + 1 ) {			
			var item =  array_cliente_dados_fiscais[ j ];
			desabilita_campos(item);	 
		}	
	}
	
	function desabilita_campos (id){
		
		if(jQuery(id).length){
			jQuery(id).ready(function(){
				
				jQuery(id).find("input").attr("readonly", true).addClass('desabilitado').removeClass('obrigatorio').removeClass('obrigatorio_ou');
				jQuery(id).find("select").attr("disabled", true).attr("readonly", true).addClass('desabilitado').removeClass('obrigatorio').removeClass('obrigatorio_ou');
				jQuery(id).find("textarea").attr("readonly", true).addClass('desabilitado').removeClass('obrigatorio').removeClass('obrigatorio_ou');
				jQuery(id).find("input:checkbox").attr("disabled", true).attr("readonly", true).addClass('desabilitado').removeClass('obrigatorio').removeClass('obrigatorio_ou');
				jQuery(id).find("input:radio").attr("disabled", true).attr("readonly", true).addClass('desabilitado').removeClass('obrigatorio').removeClass('obrigatorio_ou');
				jQuery(id).find("input:file").attr("disabled", true).attr("readonly", true).addClass('desabilitado').removeClass('obrigatorio').removeClass('obrigatorio_ou');
				jQuery(id).find("button:submit").attr("disabled", true).attr("readonly", true).addClass('desabilitado').removeClass('obrigatorio').removeClass('obrigatorio_ou');
				jQuery(id).find("button[value=Inserir]").attr("disabled", true).attr("readonly", true).addClass('desabilitado').removeClass('obrigatorio').removeClass('obrigatorio_ou');
				jQuery(id).find("#buttonConfirmarCobranca").attr("disabled", true).attr("readonly", true).addClass('desabilitado').removeClass('obrigatorio').removeClass('obrigatorio_ou');
	
			});
		}
		
	}
	
	function desabilita_botoes (id){
		
		// remove acão de excluir
		jQuery(id).find(".td_acao_excluir").html('<img src="images/icon_error.png" class="grayscale desabilitado" />');
		
		// remove ancora para edição do registro
		jQuery(id).find(".td_acao_link a").each(function () {
			jQuery(this).parent().text(jQuery(this).text()); 
		});
		
	}
	/* FIM CONTROLE DE ACESSO */
	
	jQuery('.data input').datepicker('option', 'yearRange', 'c-100:c+100');
	
	jQuery(':disabled').addClass('desabilitado');	
	jQuery(':enabled').removeClass('desabilitado');	

    var clioid = jQuery("#clioid").val();
    jQuery(".anexo_comprovante_residencia").ajaxfileupload({
        params: {
                'acao' : 'setAnexoComprovanteEndereco',
                'clioid' : clioid
            },
        action: ACTION,
        valid_extensions : ['jpg','jpeg','bmp','png', 'pdf'],
        onComplete: function(response) {
                if (response == null) 
                {
                    var response = jQuery.parseJSON('{"status":"alerta", "mensagem":"Arquivo Inválido"}');
                } 
                var tipoResposta = response.status;
                var resposta = response.mensagem;
                if (!resposta)
                {
                    resposta = response.message;
                }
                var clioid = response.clioid;
                if (tipoResposta == 'sucesso') {
                    jQuery(".anexo_comprovante_residencia").closest('.conteudo').find(".arquivoresposta").html('<a href="javascript:return false;" class="excluirAnexo"  clioid="' + clioid +'" type="button"><img src="images/icon_error.png" /></a><a href="?acao=downloadAnexoComprovanteEndereco&clioid=' + clioid + '" target="_blank" >' + resposta + '</a>');   
                } else if (tipoResposta == 'alerta' || tipoResposta == false) {
                    jQuery(".anexo_comprovante_residencia").closest('.conteudo').find(".arquivoresposta").html('<p class="erroarquivo">' + resposta + '</p>');
                }
            jQuery(".anexo_comprovante_residencia").closest('.conteudo').find(".loadingtipo").hide();
        },
        onStart: function() {
            jQuery(".loadingtipo").show();
        },
        submit_button:  null
    });

    
    
    jQuery('body').delegate('.excluirAnexo', 'click', function(){
        var clioid = jQuery(this).attr('clioid');
        jQuery(".anexo_comprovante_residencia").closest('.conteudo').find(".loadingtipo").show();
        jQuery.post(ACTION, {
                        clioid: clioid,
                        acao : 'excluirAnexo'
                    }, function(data) {
                        jQuery(".anexo_comprovante_residencia").closest('.conteudo').find(".loadingtipo").hide();
                        var resposta = $.parseJSON(data);
                        if (resposta.status == 'sucesso') {
                            jQuery('.anexo_comprovante_residencia').val('');    
                            jQuery(".anexo_comprovante_residencia").closest('.conteudo').find(".arquivoresposta").html('<p class="sucessoarquivo">' + resposta.mensagem + '</p>');
                        } else {
                            jQuery(".anexo_comprovante_residencia").closest('.conteudo').find(".arquivoresposta").html('<p class="erroarquivo">' + resposta.mensagem + '</p>');
                        }
                    });
        

    });

    if (jQuery('#mensagem').text() == '')
    {
        jQuery('#mensagem').hide();
    }
    jQuery('#carregando').hide();
        
    jQuery.mask.definitions['9'] = '';
    jQuery.mask.definitions['#'] = '[0-9]';

	jQuery("#mes_ano").mask("##/##");

 /**
     * Controles de mascara para form pesquisa
     */
	// Set mascaras Documento Pessoas

	jQuery("#pesq_clitipo").ready(function(){
		if(jQuery("#pesq_clitipo").val() == 'J'){
			jQuery("#cpf_busca").mask("##.###.###/####-##");
		}else{
			jQuery("#cpf_busca").mask("###.###.###-##");
		}
	});

	//jQuery("#clirede_ip").mask("###.###.###.###");
    jQuery("#clino_cgc").mask("##.###.###/####-##");
	jQuery("#octcnpj").mask("##.###.###/####-##");
    // jQuery("#clino_cpf").mask("###.###.###-##");
	jQuery("#clino_cpf").mask("###.###.###-##");
	// jQuery("#cpf_busca").setMask("cpf").trigger('keyup').blur();
	
	// Altera tipo de pessoa (TELA PESQUISA)
    jQuery("#pesq_clitipo").change(function(){
    	
    	if(jQuery("#pesq_clitipo").val() == 'F'){
    		jQuery("#cpf_busca").mask("###.###.###-##");
    	}else {
    		jQuery("#cpf_busca").mask("##.###.###/####-##");
    	}
    });  

	jQuery("#clicnae").mask("####-#/##");
        
    /* reporte de erro telefone não vinha com mascara, ou vinha com mascara incompleta */	
    jQuery(".telefone").each(function () {
        var phone, element;  
        phone = $(this).val().replace(/\D/g, '');  
        element = $(this);  
        var dig1 = (phone.substr(0,1) != '') ? phone.substr(0,1) : '#';
        var dig2 = (phone.substr(1,1) != '') ? phone.substr(1,1) : '#';
        var dig3 = (phone.substr(2,1) != '') ? phone.substr(2,1) : '#';

        if ((dig1 == "1") && ((dig3 == "6") || (dig3 == "8") || (dig3 == "9"))) { //ddd para celulares em são paulo
            if(phone.length > 10){
                element.mask("(##) #####-####");
            }
        } else if((dig1 == "2") && (dig2 == "1" || dig2 == "2" || dig2 == "4" || dig2 == "7" || dig2 == "8") && ((dig3 == "6") || (dig3 == "8") || (dig3 == "9"))){
            if(phone.length > 10){
                element.mask("(##) #####-####");
            }
		} else {
            element.mask("(##) ####-####")
        }
    });

    /* telefone maskedinput v 2.3 */
    jQuery(".telefone").on('keyup', function (event) {
        var phone, element;  
        phone = $(this).val().replace(/\D/g, '');  
        element = $(this);  
        //pegando os 3 primeiros digitos
        var dig1 = (phone.substr(0,1) != '') ? phone.substr(0,1) : '#';
        var dig2 = (phone.substr(1,1) != '') ? phone.substr(1,1) : '#';
        var dig3 = (phone.substr(2,1) != '') ? phone.substr(2,1) : '#';
        //reporte de erro  ao preencher o ddd e não poder apagar

        if(phone.length > 10){
            element.mask("(##) #####-####");
        }
        if (phone.length == 3 && event.which == '8') {
            element.mask("("+dig1+""+dig2+") ####-####").focus();
        } else if (phone.length == 2 && event.which == '8') {
            element.mask("("+dig1+"#) ####-####").focus();
        } else if (phone.length == 1 && event.which == '8') {
            element.mask("(##) ####-####").focus();
        } else if (phone.length == 3 && (dig1 == "1") && ((dig3 == "6") || (dig3 == "8") || (dig3 == "9"))) { //ddd para celulares em são paulo
            element.mask("(1" + dig2 + ") " + dig3 + "####-####").focus();
        } else if (phone.length == 3 && (dig1 == "2") && (dig2 == "1" || dig2 == "2" || dig2 == "4" || dig2 == "7" || dig2 == "8") && ((dig3 == "6") || (dig3 == "8") || (dig3 == "9"))) { //ddd para celulares em são paulo
            element.mask("(2" + dig2 + ") " + dig3 + "####-####").focus();
        } else if (phone.length == 3) {
            element.mask("("+dig1+""+dig2+") "+dig3+"###-####").focus();
        } else if (phone.length == 1 && dig1 != "1"){
            element.mask("("+dig1+"#) ####-####").focus();
        }
    });
    /* Reporte de Erro no Teste, ao preencher incompleto o campo, clicar fora, e voltar no campo */
    jQuery(".telefone").blur(function () {
        var phone, element;  
        phone = $(this).val().replace(/\D/g, '');  
        element = $(this);  
        var dig1 = (phone.substr(0,1) != '') ? phone.substr(0,1) : '#';
        var dig2 = (phone.substr(1,1) != '') ? phone.substr(1,1) : '#';
        var dig3 = (phone.substr(2,1) != '') ? phone.substr(2,1) : '#';
        if ((dig1 == "1") && ((dig3 == "6") || (dig3 == "8") || (dig3 == "9"))) { //ele só vai colocar no formato de SP quando for ddd 11
            if(phone.length > 10){
                element.mask("(##) #####-####");
            }
        }else if ((dig1 == "2") && (dig2 == "1" || dig2 == "2" || dig2 == "4" || dig2 == "7" || dig2 == "8") && ((dig3 == "6") || (dig3 == "8") || (dig3 == "9"))) { //ele só vai colocar no formato de SP quando for ddd 11
            if(phone.length > 10){
                element.mask("(##) #####-####");
            }
        }else {
            element.mask("(##) ####-####")
        }
    });

	// Máscara Numérica
	$('.numerico').keyup( function(e){
		var campo = '#'+this.id;
		var valor = $(campo).val().replace(/[^0-9,]+/g,'');

		$(campo).val(valor);
	});
	
	// Máscara Numérica
	$('#clirede_ip').keyup( function(e){
		var campo = '#'+this.id;
		var valor = $(campo).val().replace(/[^0-9 .]+/g,'');

		$(campo).val(valor);
	});

	// Mascara Caracter
	jQuery('.alfanumCar').keyup( function(e){
		var campo = '#'+this.id;
		var valor = jQuery(campo).val().replace(/[^a-zA-Z0-9 @!&?áéíóúÁÉÍÓÚàèìòùÀÈÌÒÙãõÃÕ-]+/g,'');

		jQuery(campo).val(valor);
	});
	
	// Máscara Alfanumérica
	jQuery('.alfanum').keyup( function(e){
		var campo = '#'+this.id;
		var valor = jQuery(campo).val().replace(/[^a-zA-Z0-9 ]+/g,'');

		jQuery(campo).val(valor);
	});
	
	// Máscara Valor Moeda
	jQuery('.valor').maskMoney({thousands:'.', decimal:','});
	jQuery('.valorZerado').maskMoney({thousands:'.', decimal:',',allowZero: true});

	jQuery("#nome_busca").on("blur", function() {
		jQuery(this).val(jQuery.trim(jQuery(this).val()));
	});
	 
    /**
     * Ação de envio do formulário de pesquisa.
     */
    jQuery('body').delegate('#buttonPesquisar', 'click', function(){

    	// oculta mensagens anteriores
    	jQuery('#mensagem').hide();
        jQuery("#nome_busca").removeClass("alerta");
        jQuery("#cpf_busca").removeClass("alerta");
        jQuery("#pesq_clicloid").removeClass("alerta");
        jQuery("#pesq_clitipo").removeClass("alerta");
    	
    	// valida campos obrigatórios
        var erros = 0;   
        var erroTipoPessoa = '';

        if(jQuery("#nome_busca").val() == "") {
            jQuery("#nome_busca").addClass("alerta");
            erros++;
        }

        if(jQuery("#cpf_busca").val() == "") {
            jQuery("#cpf_busca").addClass("alerta");
            erros++;
        }

        if(jQuery("#pesq_clicloid").val() == "") {
            jQuery("#pesq_clicloid").addClass("alerta");
            erros++;
        }

        if(jQuery("#pesq_clitipo").val() == "") {
            jQuery("#pesq_clitipo").addClass("alerta");
            erroTipoPessoa = " E escolher o Tipo Pessoa.";
            erros = 3;
        }
        
        if(erros > 2) {
        	jQuery('#mensagem').html("É necessário preencher ao menos um dos campos chaves de pesquisa (Cliente, Classe, CPF ou CNPJ)."+erroTipoPessoa);
        	jQuery('#mensagem').addClass("alerta");
        	jQuery('#mensagem').show();
            return false;
        }
        
        jQuery("#nome_busca").removeClass("alerta");
        jQuery("#cpf_busca").removeClass("alerta");
        jQuery("#pesq_clicloid").removeClass("alerta");
        jQuery("#pesq_clitipo").removeClass("alerta");
            
        //desabilita botão pesquisar
        jQuery('#buttonPesquisar').attr('disabled', 'disabled');
        
        jQuery('#carregando').show();
        
        //esconde resultados anteriores
        jQuery('.resultado_pesquisa').hide();
        
        jQuery('#pesquisa_cliente').submit();/**/
        
    });
    
    /**
     * Ação para novo cadastro.
     */
    jQuery('body').delegate('#buttonNovo', 'click', function(){

        jQuery("#acao").val('principal');        
        jQuery("#pesquisa_cliente").submit();
        
    });
    
    /**
     * Ação seleciona tipo cliente cadastro principal.
     */
    // habilita por padrão o formulário pessoa Juridica (coso novo)
    if(!jQuery("#clitipo").val()){
		formTipoCliente('J');
		jQuery("#clitipo option[value='J']").attr('selected','selected');
    }
	
	// seleciona tipo cliente
    jQuery('body').delegate('#clitipo', 'change', function(){

    	formTipoCliente(jQuery("#clitipo").val());
        
    });
    
    // caso o tipo cliente venha selecionado (caso de edição)
    if(jQuery("#clitipo").val()){
    	
    	formTipoCliente(jQuery("#clitipo").val());
    	
    }
    
    jQuery("#forma_pagamento").ready(function(){

    	if(jQuery("#cliret_piscofins").is(":checked") == true){
			jQuery("#cliret_pis_perc").removeAttr('disabled').removeClass('desabilitado');
			jQuery("#cliret_cofins_perc").removeAttr('disabled').removeClass('desabilitado');
			jQuery("#cliret_csll_perc").removeAttr('disabled').removeClass('desabilitado');
			jQuery("#cliret_irf_perc").removeAttr('disabled').removeClass('desabilitado');
		}else{
			jQuery("#cliret_pis_perc").attr('disabled','disabled').addClass('desabilitado');
			jQuery("#cliret_cofins_perc").attr('disabled','disabled').addClass('desabilitado');
			jQuery("#cliret_csll_perc").attr('disabled','disabled').addClass('desabilitado');
			jQuery("#cliret_irf_perc").attr('disabled','disabled').addClass('desabilitado');
		}
    	
    	if(jQuery("#cliret_iss").is(":checked") == true){
			jQuery("#cliret_iss_perc").removeAttr('disabled').removeClass('desabilitado');
		}else{
			jQuery("#cliret_iss_perc").attr('disabled','disabled').addClass('desabilitado');
		}
    });
    
    
    jQuery("#buttonInserirFaturamento").click(function(){
    	
    	jQuery("#eqcoid_fat").addClass('obrigatorio');
    	jQuery("#valor_monitoramento").addClass('obrigatorio');
    	jQuery("#valor_renovacao").addClass('obrigatorio');
    	   	
    	jQuery("#forma_pagamento_forcoid").removeClass('obrigatorio');
    	jQuery("#numero_cartao").removeClass('obrigatorio');
    	jQuery("#mes_ano").removeClass('obrigatorio');
    	
    	if(!validacaoJquery(jQuery(this))){
	    	jQuery("#eqcoid_fat").removeClass('obrigatorio');
	    	jQuery("#valor_monitoramento").removeClass('obrigatorio');
	    	jQuery("#valor_renovacao").removeClass('obrigatorio');
	    	
	    	jQuery("#forma_pagamento_forcoid").addClass('obrigatorio');
	    	jQuery("#numero_cartao").addClass('obrigatorio');
	    	jQuery("#mes_ano").addClass('obrigatorio');
	    	
    		return false;
    	}
    	
    	if(jQuery("#buttonInserirFaturamento").html() == "Inserir"){
    		jQuery("#acao").val("setFaturamento");
    	}else{
    		var clifoid = jQuery(this).attr('clifoid');
            jQuery('#acao').val('excluirFaturamento');
    	}
    	
    	//jQuery('form').submit();
    });

    jQuery(".excluirObrigacaoCliente").click(function(){
    	jQuery("#motivoExclusao" + jQuery(this).attr('cliooid')).slideDown();
    	jQuery("#cliooid_deletar").val(jQuery(this).attr('cliooid'));
    });
    
    jQuery(".buttonExcluirObrigacao").click(function(){
    	
    	jQuery("#cliomotivo_exclusao" + jQuery(this).attr('cliooid')).addClass('obrigatorio');
    	
    	jQuery("#eqcoid_fat").removeClass('obrigatorio');
    	jQuery("#valor_monitoramento").removeClass('obrigatorio');
    	jQuery("#valor_renovacao").removeClass('obrigatorio');
    	
    	jQuery("#forma_pagamento_forcoid").removeClass('obrigatorio');
    	jQuery("#numero_cartao").removeClass('obrigatorio');
    	jQuery("#mes_ano").removeClass('obrigatorio');

    	if(!validacaoJquery(jQuery(this))){
    		jQuery("#eqcoid_fat").removeClass('obrigatorio');
    		jQuery("#valor_monitoramento").removeClass('obrigatorio');
    		jQuery("#valor_renovacao").removeClass('obrigatorio');
    		
    		jQuery("#forma_pagamento_forcoid").addClass('obrigatorio');
    		jQuery("#numero_cartao").addClass('obrigatorio');
    		jQuery("#mes_ano").addClass('obrigatorio');
    		
    		jQuery("#cliomotivo_exclusao" + jQuery(this).attr('cliooid')).removeClass('obrigatorio');
    		
    		return false;
    	}
    	
		jQuery("#acao").val("excluirObrigacao");
    });
    
    jQuery("#buttonInserirObrigacao").click(function(){
    	
    	jQuery("#cliono_periodo_mes").addClass('obrigatorio');
    	jQuery("#clioobroid").addClass('obrigatorio');
    	jQuery("#cliovl_obrigacao").addClass('obrigatorio');
    	jQuery("#cliodt_inicio").addClass('obrigatorio');
    	jQuery("#cliofaturamento").addClass('obrigatorio');
    	
    	if(jQuery("#clioobroid").val() == 50){
    		jQuery("#cliosoftware_principal").addClass('obrigatorio');
    		jQuery("#cliosoftware_secundario").addClass('obrigatorio');
    	}else{
    		jQuery("#cliosoftware_principal").removeClass('obrigatorio').removeClass('erro');
    		jQuery("#cliosoftware_secundario").removeClass('obrigatorio').removeClass('erro');
    	}
    	
    	if(jQuery("#cliofaturamento").val() == 'cortesia' || jQuery("#cliofaturamento").val() == 'demonstracao'){
    		jQuery("#cliodemonst_aprov").addClass('obrigatorio');
    		
    		if(jQuery("#cliofaturamento").val() == 'demonstracao'){
    			jQuery("#cliodemonst_validade").addClass('obrigatorio');
    		}else{
    			jQuery("#cliodemonst_validade").removeClass('obrigatorio').removeClass('erro');
    		}
    		
    	}else{
    		jQuery("#cliodemonst_aprov").removeClass('obrigatorio').removeClass('erro');
    		jQuery("#cliodemonst_validade").removeClass('obrigatorio').removeClass('erro');
    	}
    	
    	jQuery("#eqcoid_fat").removeClass('obrigatorio');
    	jQuery("#valor_monitoramento").removeClass('obrigatorio');
    	jQuery("#valor_renovacao").removeClass('obrigatorio');
    	
    	jQuery("#forma_pagamento_forcoid").removeClass('obrigatorio');
    	jQuery("#numero_cartao").removeClass('obrigatorio');
    	jQuery("#mes_ano").removeClass('obrigatorio');

    	if(!validacaoJquery(jQuery(this))){
    		jQuery("#eqcoid_fat").removeClass('obrigatorio');
    		jQuery("#valor_monitoramento").removeClass('obrigatorio');
    		jQuery("#valor_renovacao").removeClass('obrigatorio');
    		
    		jQuery("#forma_pagamento_forcoid").addClass('obrigatorio');
    		jQuery("#numero_cartao").addClass('obrigatorio');
    		jQuery("#mes_ano").addClass('obrigatorio');
    		
    		jQuery("#cliono_periodo_mes").removeClass('obrigatorio');
        	jQuery("#clioobroid").removeClass('obrigatorio');
        	jQuery("#cliovl_obrigacao").removeClass('obrigatorio');
        	jQuery("#cliodt_inicio").removeClass('obrigatorio');
        	jQuery("#cliofaturamento").removeClass('obrigatorio');
    		jQuery("#cliosoftware_principal").removeClass('obrigatorio');
    		jQuery("#cliosoftware_secundario").removeClass('obrigatorio');
    		jQuery("#cliodemonst_aprov").removeClass('obrigatorio');
    		jQuery("#cliodemonst_validade").removeClass('obrigatorio');
    		
    		return false;
    	}
    	
    	if(jQuery("#buttonInserirObrigacao").html() == "Inserir"){
    		jQuery("#acao").val("setObrigacao");
    	}
    });

    jQuery("#buttonConfirmarCobranca").click(function(){

    	if(!validacaoJquery(jQuery(this))){
    		return false;
    	}

    	if(cliente_dados_cobranca == 1){ 
    		confirma();
    	}else{
    		jQuery('form').submit();
    	}
    	    	
    });
    
    
    jQuery("#eqcoid_fat").change(function(){
    	var objId = "#"+jQuery("#eqcoid_fat").val();
    	if(jQuery(objId).length > 0){
    		var valores = jQuery(objId).attr('valores').trim();
    		valores = valores.split("|");
    		var clifvalor_monitoramento = valores[1]; //jQuery(objId).attr('clifvalor_monitoramento').trim();
    		var clifvalor_renovacao = valores[2]; //jQuery(objId).attr('clifvalor_renovacao').trim();
    		var clifoid = valores[0]; //jQuery(objId).attr('clifoid');
    		
    		jQuery("#valor_monitoramento").val(clifvalor_monitoramento);
    		jQuery("#valor_renovacao").val(clifvalor_renovacao);
    		jQuery("#clifoid").val(clifoid);
    		jQuery("#buttonInserirFaturamento").html("Excluir");
    	}else{
    		jQuery("#valor_monitoramento").val("");
    		jQuery("#valor_renovacao").val("");
    		jQuery("#buttonInserirFaturamento").html("Inserir")
    	}
    });

    
    jQuery("#cliret_piscofins").click(function() {
		
		if(jQuery("#cliret_piscofins").is(":checked") == true){
			jQuery("#cliret_pis_perc").removeAttr('disabled');
			jQuery("#cliret_cofins_perc").removeAttr('disabled');
			jQuery("#cliret_csll_perc").removeAttr('disabled');
			jQuery("#cliret_irf_perc").removeAttr('disabled');
            jQuery(':enabled').removeClass('desabilitado');

		}else{
			jQuery("#cliret_pis_perc").attr('disabled','disabled').val('');
			jQuery("#cliret_cofins_perc").attr('disabled','disabled').val('');
			jQuery("#cliret_csll_perc").attr('disabled','disabled').val('');
			jQuery("#cliret_irf_perc").attr('disabled','disabled').val('');
            jQuery(':disabled').addClass('desabilitado');
		}
		
	});

    
    /**
     * Ação de excluir de particularidades.
     */
    jQuery('body').delegate('.excluirParticularidade', 'click', function(){
        if (confirm('Deseja realmente excluir a particularidade para o cliente?')) {
            var clipfoid = jQuery(this).attr('clipfoid');
            jQuery('#clipfoid').val(clipfoid);
            jQuery('#excluirParticularidadePerfil').submit();
        }
    });
    
    /**
     * Ação de excluir de particularidades.
     */
    jQuery('body').delegate('.excluirContato', 'click', function(){
        if (confirm('Deseja realmente excluir o contato para o cliente?')) {
            var clicoid = jQuery(this).attr('clicoid');
            jQuery('#clicoid').val(clicoid);
            jQuery('#excluirClienteContato').submit();
        }
    });
    
    /**
     * Ação de excluir de particularidades.
     */
    jQuery('body').delegate('.excluirFaturamento', 'click', function(){
        if (confirm('Deseja realmente excluir o faturamento do cliente?')) {
            var clifoid = jQuery(this).attr('clifoid');
            jQuery('#acao').val('excluirFaturamento');
            jQuery('#clifoid').val(clifoid);
            jQuery('form').submit();
        }
    });
    
    /**
     * Ação de excluir de particularidades.
     */
    jQuery('body').delegate('.excluirEndereco', 'click', function(){
        if (confirm('Deseja realmente excluir o endereço de entrega?')) {
            var endoid = jQuery(this).attr('endoid');
            jQuery('#acao').val('excluirEnderecoEntrega');
            jQuery('#endoid').val(endoid);
            jQuery('form').submit();
        }
    });
    
    jQuery("#bancodigo").change(function(){
    	jQuery("#bannome").val(jQuery("#bancodigo").val());
    });

    jQuery("#bannome").change(function(){
    	jQuery("#bancodigo").val(jQuery("#bannome").val());
    });
    
    
    jQuery("#copiarDe").change(function(){
			var copiarDe = jQuery("#copiarDe").val();
	    	var copiarPara = "enderecoEntregaFavorito";
	    	if(copiarDe != '')
	        {
	    		CopiarDePara(copiarDe, copiarPara);
	        }else{
	        	resetaEndereco(jQuery(this));	
	        }		
	}); 
    
    jQuery("#copiarPrincipal").click(function() { 
		if(jQuery("#copiarPrincipal").is(":checked") == true){
			var copiarDe = "enderecoprincipal";
	    	var copiarPara = "enderecocobranca";
	    	
	    	CopiarDePara(copiarDe, copiarPara);
		} else {
            //reporte de erro, se for desmarcado o checkbox deve resetar o endereço
            resetaEndereco(jQuery(this));
        }
		
	}); 
    
    jQuery("#copiarDePara").click(function() { 
    	var copiarDe = jQuery("#copiarDe").val();
    	var copiarPara = jQuery("#copiarPara").val();
    	if(copiarDe != '')
        {
            CopiarDePara(copiarDe, copiarPara);    
        }
    	
		
	}); 
    
    jQuery('body').delegate('.excluirbeneficio', 'click', function(){
        if (confirm('Deseja realmente excluir o benefício para o cliente?')) {
            var clboid = jQuery(this).attr('clboid');
            jQuery('#clboid').val(clboid);
            jQuery('form').submit();
        }
    });



    jQuery("#cliret_iss").click(function() { 
		if(jQuery("#cliret_iss").is(":checked") == true){
			jQuery("#cliret_iss_perc").removeAttr('disabled').removeClass('desabilitado');
		}else{
			jQuery("#cliret_iss_perc").attr('disabled','disabled').removeClass('alerta').addClass('desabilitado').val('');
		}
		
	}); 
    
    jQuery("#clino_cgc").ready(function(){
    	
    	if(jQuery("#clitipo").val() == "J"){
	    	var cnpj = jQuery("#clino_cgc").val();
	    	
	    	// Replace - retira mascara
	    	cnpj = cnpj.replace(/\D/g, "");
	    	
			var CNPJValido = ValidaCNPJ(cnpj);
			
			if(cnpj != ''){
				if(CNPJValido == false){  resposta = 'incorreto'; }
				if(resposta == 'incorreto'){
		        	jQuery('#buttonConfirmarPrincipal').attr('disabled','disabled');
		    	}else{
		    		if(jQuery('#buttonConfirmarPrincipal:not([readonly="readonly"]')){
		    			jQuery('#buttonConfirmarPrincipal').removeAttr('disabled');
		    		}
		    	}
			}
    	}
    });
    
    jQuery('body').delegate('#clino_cgc', 'blur', function(){ 
    	
    	var cnpj = jQuery("#clino_cgc").val();
    	
    	// Replace - retira mascara
    	cnpj = cnpj.replace(/\D/g, "");
    	
		var CNPJValido = ValidaCNPJ(cnpj);
		
    	var clioid = jQuery("#clioid").val();
    	var post = {
    		cnpj: cnpj,
    		clioid: clioid
		};
    	
        jQuery.post(ACTION + '?acao=validaCNPJ', post,  function(data) {
        	
        	if(data.length){
        		
	        	var resposta = jQuery.parseJSON(data);
				
				if(CNPJValido == false){  resposta = 'incorreto'; }
	        	jQuery('#mensagem').removeClass('sucesso').removeClass('erro').removeClass("alerta");
	        	if(resposta > 0){
		            jQuery('#mensagem').html("Existe um cliente cadastrado com este CNPJ.");
		        	jQuery('#mensagem').addClass("alerta");
		        	jQuery('#mensagem').show();
		        	jQuery("#clino_cgc").addClass("alerta");
		        	jQuery('#buttonConfirmarPrincipal').attr('disabled','disabled');
	        	}else if(resposta == 'incorreto'){
	        		jQuery('#mensagem').html("CNPJ inválido");
		        	jQuery('#mensagem').addClass("alerta");
		        	jQuery('#mensagem').show();
		        	jQuery("#clino_cgc").addClass("alerta");
		        	jQuery('#buttonConfirmarPrincipal').attr('disabled','disabled');
	        	}else{
	        		jQuery('#mensagem').hide();
		        	jQuery("#clino_cgc").removeClass("alerta");
		    		if(jQuery('#buttonConfirmarPrincipal:not([readonly="readonly"]')){
		    			jQuery('#buttonConfirmarPrincipal').removeAttr('disabled');
		    		}
	        	}        
        	}
        });
    });    
        
    
    jQuery('body').delegate('#clino_cpf', 'blur', function(){  

	  	var cpf = jQuery("#clino_cpf").val();
	  	
	  	// Replace - retira mascara
	  	cpf = cpf.replace(/\D/g, "");
	  	
		var CPFValido = validaCPF(cpf);

    	var clioid = jQuery("#clioid").val();
    	var post = {
			cpf: cpf,
    		clioid: clioid
		};
    	
        jQuery.post(ACTION + '?acao=validaCPF', post,  function(data) {
        	
        	if(data.length){
        		
	        	var resposta = jQuery.parseJSON(data);    

				if(CPFValido == false){	resposta = 'incorreto'; }
				
	        	if(resposta > 0){
		            jQuery('#mensagem').html("Existe um cliente cadastrado com este CPF.");
		        	jQuery('#mensagem').addClass("alerta");
		        	jQuery('#mensagem').show();
		        	jQuery("#clino_cpf").addClass("alerta");
		        	jQuery('#buttonConfirmarPrincipal').attr('disabled','disabled');
	        	}else if(resposta == 'incorreto'){
	        		jQuery('#mensagem').html("CPF inválido");
		        	jQuery('#mensagem').addClass("alerta");
		        	jQuery('#mensagem').show();
		        	jQuery("#clino_cpf").addClass("alerta");
		        	jQuery('#buttonConfirmarPrincipal').attr('disabled','disabled');
		        	//jQuery("#clino_cpf").mask(maskCpf).val(inicpf);
	        	}else{
	        		jQuery('#mensagem').hide();
		        	jQuery("#clino_cpf").removeClass("alerta");
		        	jQuery('#buttonConfirmarPrincipal').removeAttr('disabled');
		        	//jQuery("#clino_cpf").setMask("cpf");
	        	}       
        	}
        });   

    });
    
    if (jQuery('#clitipo').length > 0 && jQuery('#clino_cpf').length > 0 && jQuery('#clino_cgc').length > 0) {
        var disparar_acao        = false;
        var mensagem_insercao    = 'Registro incluído com sucesso.';
        var mensagem_alteracao   = 'Registro alterado com sucesso.';
        var mensagem_atualizacao = 'Registro atualizado com sucesso.';
        var mensagem_exclusao    = 'Registro excluído com sucesso.';

        if (jQuery('#mensagem').hasClass('sucesso') == false) {
            disparar_acao = true;
        } else if (jQuery('#mensagem').html() != mensagem_insercao
            && jQuery('#mensagem').html() != mensagem_alteracao
            && jQuery('#mensagem').html() != mensagem_atualizacao
            && jQuery('#mensagem').html() != mensagem_exclusao) {
            disparar_acao = true;
        }

        if (disparar_acao) {
            if (jQuery('#clitipo').val() == 'F' && jQuery('#clino_cpf').val().trim() != '') {
                jQuery('#clino_cpf').trigger('blur');
            } else if (jQuery('#clitipo').val() == 'J' && jQuery('#clino_cgc').val().trim() != '') {
                jQuery('#clino_cgc').trigger('blur');
            }
        }
    }
    
    /** ABA OPERAÇÕES **/

	
	
    jQuery('#formEnderecoOperacao').hide();
    
    if( jQuery('#octoid').val() == '' ){
    	jQuery('#buttonSalvarOperacao').hide();
    	jQuery('#buttonExcluirOperacao').hide();
    }else{
    	jQuery('#buttonSalvarOperacao').show();
    	jQuery('#buttonExcluirOperacao').show();
    }
    
    jQuery("#octendoid").change(function(){
    	
    	if(jQuery("#octendoid").val() == 'N'){
    		jQuery('#formEnderecoOperacao').show();
    		
    		jQuery('.correios_cidade').html('<option value="">Selecione</option>');
    		jQuery('.correios_bairro').html('<option value="">Selecione</option>');
    		
    		jQuery('#formEnderecoOperacao input').val(''); // limpa campos
    		jQuery('#formEnderecoOperacao select').val(''); // limpa campos
    	}else{
    		jQuery('#formEnderecoOperacao').hide();
    	}
    	
    });
    
	jQuery('body').delegate('#octcnpj', 'blur', function(){ 
	    	
	    	var cnpj = jQuery("#octcnpj").val();
	    	
	    	// Replace - retira mascara
	    	cnpj = cnpj.replace(/\D/g, "");
	    	jQuery('#mensagem').removeClass('sucesso').removeClass('erro').removeClass("alerta");

			var CNPJValido = ValidaCNPJ(cnpj);
			if(CNPJValido == false) {  
				jQuery('#mensagem').html("CNPJ inválido");
	        	jQuery('#mensagem').addClass("alerta");
	        	jQuery('#mensagem').show();
	        	jQuery("#octcnpj").addClass("alerta");
	        	jQuery('#buttonIncluirOperacao').attr('disabled','disabled');
	        	jQuery('#buttonSalvarOperacao').attr('disabled','disabled');
			}else{
        		jQuery('#mensagem').hide();
	        	jQuery("#octcnpj").removeClass("alerta");
	        	jQuery('#buttonIncluirOperacao').removeAttr('disabled');
	        	jQuery('#buttonSalvarOperacao').removeAttr('disabled','disabled');

                // Verifica se existe alguma operação com aquele cnpj
                jQuery.post(ACTION, {
                    octcnpj: jQuery('#octcnpj').val(),
                    clioid: jQuery('#clioid').val(),
                    octoid: jQuery('#octoid').val(),
                    acao : 'validaCnpjOperacao'
                }, function(data) {
                    var resposta = $.parseJSON(data);
                    
                    if(resposta != null){
                        jQuery('#mensagem').html(resposta);
                        jQuery('#mensagem').addClass("erro");
                        jQuery('#mensagem').show();

                        jQuery('#octcnpj').focus();
                        jQuery('#buttonIncluirOperacao').attr("disabled", "disabled");
                    }else{
                        jQuery('#mensagem').hide();
                        jQuery('#buttonIncluirOperacao').removeAttr("disabled", "disabled");
                    }
                });
        	} 
			
	    });    	
    
    
    jQuery('body').delegate('#buttonExcluirOperacao', 'click', function(){
    	
    	// verifica se alguma operação foi selecionada
    	if( jQuery('#octoid').val() == '' ){
    		
    		jQuery('#mensagem').html("Selecione a operação a ser excluida.");
        	jQuery('#mensagem').addClass("alerta");
        	jQuery('#mensagem').show();
        	
    	}else{
    		
    		if (confirm('Deseja realmente excluir esta operação?')) {                

                jQuery('#acao').val('excluirClienteOperacao');
                jQuery('#cad_cliente_operacoes').submit();
                
            }
    		
    	}
    	
    });
       
    jQuery('body').delegate('#buttonSalvarOperacao', 'click', function(){
        
        // verifica se alguma operação foi selecionada
        if( jQuery('#octoid').val() == '' ){
            
            jQuery('#mensagem').html("Selecione a operação a ser editada.");
            jQuery('#mensagem').addClass("alerta");
            jQuery('#mensagem').show();
            
        }else if(jQuery('#enderecosSelecionados').val() === ""){
            jQuery('#mensagem').html("Selecione um endereço.");
            jQuery('#mensagem').addClass("alerta");
            jQuery('#mensagem').show();

            return false;
        }else{
            
            jQuery('#acao').val('editarClienteOperacao');
            
            if (!validacaoJquery(jQuery(this)))
                return false;
            // se passou nas validações executa o submit
            //jQuery(this).closest('form').submit();
            jQuery('#cad_cliente_operacoes').submit();
        }
        
    });   

    jQuery('body').delegate('#buttonIncluirOperacao', 'click', function(){
        jQuery('#mensagem').hide().removeClass('alerta').removeClass('sucesso').removeClass('erro');

        if(jQuery('#enderecosSelecionados').val() === ""){
            jQuery('#mensagem').html("Selecione um endereço.");
            jQuery('#mensagem').addClass("alerta");
            jQuery('#mensagem').show();

            return false;
        }else{
            if (!validacaoJquery(jQuery(this)))
    			return false;
    		// se passou nas validações executa o submit
    		//jQuery(this).closest('form').submit();
            jQuery('#cad_cliente_operacoes').submit();
        }
        
    	
    });
    
    jQuery('body').delegate('.idOperacao', 'click', function(){
    	
    	var clioid = jQuery('#clioid').val();
    	var octoid = $(this).attr('id');
    	
    	jQuery('#formEnderecoOperacao').hide();
    	jQuery('#buttonIncluirOperacao').hide();
    	jQuery('#buttonSalvarOperacao').show();
    	jQuery('#buttonExcluirOperacao').show();
    	
    	var post = {
    		octoid: octoid,
    		clioid: clioid
		};
        	
        jQuery.post(ACTION + '?acao=getClienteOperacoesById', post,  function(data) {
        	
        	if(data.length){
        		
	        	var resposta = jQuery.parseJSON(data);
                jQuery('#octoid').val(resposta[0].octoid);
                jQuery('#octoprid').val(resposta[0].octoprid);
                jQuery('#octresponsavel').val(resposta[0].octresponsavel);
                jQuery('#octnome').val(resposta[0].octnome);
                jQuery('#octtelefone').val(resposta[0].octtelefone);
                jQuery('#octcnpj').val(resposta[0].octcnpj);
                jQuery('#octinscr').val(resposta[0].octinscr);
	        	jQuery("#octcnpj").mask("##.###.###/####-##");


                cor = "";
                listaId = new Array();
                var cont = 0;
                
                jQuery('#enderecos').html('<thead><tr><th>Endereço</th><th style="width: 30px;">Excluir</th></tr></thead>');
                for(var endereco in resposta){
                    if(resposta[endereco].endlogradouro != ""){
                        cont++;
                        cor = (cont % 2 != 0) ? "" : "par";
                        id = resposta[endereco].oectendoid;
                        listaId.push(id);
                        logradouro = resposta[endereco].endlogradouro+", ";
                        logradouro += resposta[endereco].endno_numero+" - ";
                        logradouro += resposta[endereco].endbairro+" - ";
                        logradouro += resposta[endereco].endcidade+" / ";
                        logradouro += resposta[endereco].enduf;
                        jQuery('#enderecos').append('<tr class="'+cor+'"><td>'+logradouro+'</td><td class="centro"><img src="images/icon_error.png" oid="'+id+'" onclick="excluirEndereco(this)"></td></tr>');
                    }
                }
			
                jQuery("#enderecosSelecionados").val(listaId);
        	}
            jQuery('#enderecos').show();
        });
    	
    });

    // clone endereco
    var listaId  = new Array();
    jQuery("#buttonAdd").on('click',function() {

        id = jQuery("#octendoid").val();
        entrega_no_cep      = jQuery("#entrega_no_cep");
        entrega_uf          = jQuery("#entrega_uf");
        entrega_cidade      = jQuery("[name='entrega_cidade']");
        entrega_bairro      = jQuery("[name='entrega_bairro']");
        entrega_logradouro  = jQuery("[name='entrega_logradouro']");
        entrega_numero      = jQuery("#entrega_numero");

        entrega_no_cep.removeClass('erro');    
        entrega_uf.removeClass('erro');    
        entrega_cidade.removeClass('erro');    
        entrega_cidade.removeClass('erro');    
        entrega_bairro.removeClass('erro');    
        entrega_logradouro.removeClass('erro');    
        entrega_numero.removeClass('erro');    

        if(id == 'N'){

            var erros = 0;


            if(entrega_no_cep.val() === ''){
                entrega_no_cep.addClass('erro');
                erros++;
            }
            if(entrega_uf.val() === ''){
                entrega_uf.addClass('erro');
                erros++;
            }
            if(entrega_cidade.val() === ''){
                entrega_cidade.addClass('erro');
                erros++;
            }
            if(entrega_bairro.val() === ''){
                entrega_bairro.addClass('erro');
                erros++;
            }
            if(entrega_logradouro.val() === ''){
                entrega_logradouro.addClass('erro');
                erros++;
            }
            if(entrega_numero.val() === ''){
                entrega_numero.addClass('erro');
                erros++;
            }

            if(erros > 0){
                jQuery('#mensagem').html("Existem campos obrigatórios não preenchidos.");
                jQuery('#mensagem').addClass("alerta");
                jQuery('#mensagem').show();
                return false;
            }else{
                setEnderecoOperacao();
            }

        }else if(id != 'N' && id != ""){
           adicionaLinha();
        }else{
            jQuery('#mensagem').html("Selecione o endereço da operação");
            jQuery('#mensagem').addClass("alerta");
            jQuery('#mensagem').show();
        }
    }); 




    // FIM ABA OPERAÇÕES
    
    
    //Período de Emissão de Nota Fiscal   
    if(jQuery('#clic_periodo_emissao').is(":checked") == true && jQuery('#clic_periodo_emissao').is(":enabled")){
    	jQuery('#clicdt_inicial').removeAttr('disabled');
    	jQuery('#clicdt_inicial').removeClass('desabilitado');
    	jQuery('#clicdt_final').removeAttr('disabled');
    	jQuery('#clicdt_final').removeClass('desabilitado');
	}
    
    jQuery('body').delegate('#clic_periodo_emissao', 'click', function(){

    	if(jQuery('#clic_periodo_emissao').is(":checked") == true){
	    	jQuery('#clicdt_inicial').removeAttr('disabled');
	    	jQuery('#clicdt_inicial').removeClass('desabilitado');
	    	jQuery('#clicdt_final').removeAttr('disabled');
	    	jQuery('#clicdt_final').removeClass('desabilitado');
    	}else{
	    	jQuery('#clicdt_inicial').attr('disabled','disabled');
	    	jQuery('#clicdt_inicial').addClass('desabilitado');
	    	jQuery('#clicdt_final').attr('disabled','disabled');
	    	jQuery('#clicdt_final').addClass('desabilitado');
    	}
    	
    });
    
    /*
    * Acoes para a Aaba SIGGO
    * Somente liberar o botao [Confirmar] se ao menos um valor de combo for selecionado
    */
    jQuery('#cad_cliente_siggo').on('change','#clippessoa_politicamente_exposta1, #clippessoa_politicamente_exposta2, #clippspsoid, #cliptipo_segurado',function(event){

        jQuery('#btn_confirmar_siggo').removeProp('disabled');
        jQuery('#btn_confirmar_siggo').removeClass('desabilitado');

        var semValor = 0;

         jQuery('#cad_cliente_siggo select').each(function(id,value){
            valor = jQuery('#'+this.id + " option:selected").val();

             if(valor == '') {
                semValor++;
             }
});
        if(semValor === 4) {
            jQuery('#btn_confirmar_siggo').prop('disabled','true');
            jQuery('#btn_confirmar_siggo').addClass('desabilitado');
        }

    });

});

    jQuery('body').delegate('.link_box', 'click', function(){
        var alvo = jQuery(this).attr('href');        
        

        if (jQuery('.conteudo').find(alvo).is(':visible')) {

        } else {
            if (!validacaoJquery(jQuery(this))) {
                return false;        
            }
            jQuery('.box').fadeOut('fast', function() {
                jQuery('#endereco_load').fadeIn('fast', function() {
                    jQuery('#endereco_load').fadeOut('fast', function() {
                        jQuery(alvo).fadeIn();    
                    });
                    
                });
            });

        jQuery('.links .ativo').removeClass('ativo');
        jQuery(this).parent().addClass('ativo');
            
            
        }
    });

    jQuery('body').delegate('#octoprid', 'blur', function(){   

        jQuery.post(ACTION, {
            octoprid: jQuery('#octoprid').val(),
            octoid: jQuery('#octoid').val(),
            acao : 'validaIdOperacao'
        }, function(data) {
            var resposta = $.parseJSON(data);
            
            if(resposta != null){
                jQuery('#mensagem').html(resposta);
                jQuery('#mensagem').addClass("erro");
                jQuery('#mensagem').show();

                jQuery('#octoprid').focus();
                jQuery('#buttonIncluirOperacao').attr("disabled", "disabled");
            }else{
                jQuery('#mensagem').hide();
                jQuery('#buttonIncluirOperacao').removeAttr("disabled", "disabled");
            }
        });

    });

    function adicionaLinha() {
        jQuery('#enderecos').show();
        jQuery('#mensagem').hide().removeClass('sucesso').removeClass('alerta').removeClass('erro');
        cor = "";
        id = jQuery('#octendoid').val();
        text = jQuery('#octendoid option:selected').text();
        linhas = jQuery("#enderecos > tbody > tr").length;
        cor = (linhas % 2 != 0) ? "par" : "";

        
        if(linhas > 0){
            listaId = jQuery("#enderecosSelecionados").val().split(',');
        }else{

            listaId = new Array();
        }

        if(jQuery.inArray(id, listaId) != -1){
            jQuery('#mensagem').html("Endereço já cadastrado para esta operação.");
            jQuery('#mensagem').addClass("erro");
            jQuery('#mensagem').show();
            return false;
        }else{
            jQuery('#enderecos').append('<tr class="'+cor+'"><td>'+text+'</td><td class="centro"><img src="images/icon_error.png" oid="'+id+'" onclick="excluirEndereco(this)"></td></tr>');
            listaId.push(id);
            jQuery("#enderecosSelecionados").val(listaId);
            jQuery('#octendoid').val('');
        }
    }

    function setEnderecoOperacao(){
        listaId = jQuery("#enderecosSelecionados").val().split(',');

        var entrega_no_cep = jQuery('.correios_cep').val();
        var entrega_uf = jQuery('.correios_estado').val();
        var entrega_cidade = jQuery('.correios_cidade').val();
        var entrega_bairro = jQuery('.correios_bairro').val();
        var entrega_logradouro = jQuery('.correios_endereco').val();
        var entrega_numero = jQuery('.correios_numero').val();
        var entrega_complemento = jQuery('.correios_complemento').val();

        jQuery.post(ACTION, {
            clioid: jQuery('#clioid').val(),
            entrega_no_cep: entrega_no_cep,
            entrega_uf: entrega_uf,
            entrega_cidade: entrega_cidade,
            entrega_bairro: entrega_bairro,
            entrega_logradouro: entrega_logradouro,
            entrega_numero: entrega_numero,
            entrega_complemento: entrega_complemento,
            acao : 'setEnderecoEntrega'
        }, function(data) {
            var resposta = $.parseJSON(data);

            logradouro = entrega_logradouro+", ";
            logradouro += entrega_numero+" - ";
            logradouro += entrega_bairro+" - ";
            logradouro += entrega_cidade+" / ";
            logradouro += entrega_uf;
            optionEndereco = '<option value="'+resposta+'" selected>'+logradouro+'</option>';
            jQuery("#octendoid").append(optionEndereco);
            jQuery("#formEnderecoOperacao").hide('fast', function() {
                adicionaLinha();
            });
            // criaSelectClienteEndereco(resposta);

        });
    }

    function criaSelectClienteEndereco(endoid){

        jQuery.post(ACTION, {
            clioid: jQuery('#clioid').val(),
            acao : 'criaSelectClienteEndereco'
        }, function(data) {
            var resposta = $.parseJSON(data);     
            
            optionEndereco = '<option value="">Selecione</option>';
            optionEndereco += '<option value="N">[ NOVO ]</option>';

            for(var enderecoelemento in resposta){
                sel = "";
                if(resposta[enderecoelemento].endoid == endoid){
                    sel = "selected";
                }
                logradouro = resposta[enderecoelemento].endlogradouro+", ";
                logradouro += resposta[enderecoelemento].endno_numero+" - ";
                logradouro += resposta[enderecoelemento].endbairro+" - ";
                logradouro += resposta[enderecoelemento].endcidade+" / ";
                logradouro += resposta[enderecoelemento].enduf;

                optionEndereco += '<option value="'+resposta[enderecoelemento].endoid+'" '+sel+'>'+logradouro+'</option>';
            }
            jQuery(endereco).replaceWith('<select name="octendoid" id="octendoid">'+optionEndereco+'</select>');
                adicionaLinha();
            
        });

    }

    

    function excluirEndereco(obj){
        var newLIsta = new Array();
        id = jQuery(obj).attr('oid');
        listaId = jQuery("#enderecosSelecionados").val().split(',');
        for(var lista in listaId){
            if(id != listaId[lista]){
                newLIsta.push(listaId[lista]);
            }
        }

        jQuery(obj).closest('tr').remove();
        linhas = 1;
        jQuery("#enderecos > tbody > tr").each(function(){
            jQuery(this).removeClass("par");            
            cor = (linhas % 2 != 0) ? "" : "par";
            jQuery(this).addClass(cor);            

            linhas++;
        });

        jQuery("#enderecosSelecionados").val(newLIsta);
    }

    function isNumber (o) {
      return ! isNaN (o-0) && o !== null && o.replace(/^\s\s*/, '') !== "" && o !== false;
    }

    function unique(array){
        return $.grep(array,function(el,index){
            return index == $.inArray(el,array);
        });
    }

    //Mostra div com informações de duvidas
    jQuery('body').delegate('.duvidas_cep', 'click', function(){
        jQuery(this).closest('.chamada_correios').find(".descricao_duvidas").show();
    });
    //Fecha div de informações de duvidas
    jQuery('body').delegate('.descricao_fechar', 'click', function(){
        jQuery(this).closest('.chamada_correios').find(".descricao_duvidas").hide();
    });
    //Ao clicar em pesquisar por endereço, mostra o campo de pesquisa
    jQuery('body').delegate('.pesquisar_endereco', 'click', function(){
        
    	if(jQuery.browser.msie){
        	jQuery(this).closest('.chamada_correios').find('.semresultado').html('<input type="text" class="endereco_para_pesquisa_ie campo maior"><button class="realiza_pesquisa_endereco" onclick="javascript:return false;" type="button">Pesquisar</button><img src="images/progress4.gif" class="loading_endereco loader">');
    	}else{
    		jQuery(this).closest('.chamada_correios').find('.semresultado').html('<input type="text" class="endereco_para_pesquisa campo maior"><button class="realiza_pesquisa_endereco" onclick="javascript:return false;" type="button">Pesquisar</button><img src="images/progress4.gif" class="loading_endereco loader">');
    	}
    	
    });
    //Pesquisa por endereço
    jQuery('body').delegate('.realiza_pesquisa_endereco', 'click', function(){
        var endereco = jQuery(this).closest('.chamada_correios').find(".endereco_para_pesquisa").val();
        var dados = jQuery(this).closest('.chamada_correios');
        if (endereco != '')
        {
            $('.loading_endereco').show();
            jQuery.post(ACTION, {
                            endereco: endereco,
                            acao : 'criaSelectEndereco'
                        }, function(data) {
                            $('.loading_endereco').hide();
                            var resposta = $.parseJSON(data);
                            if (resposta.ocorrencia == 0) {
                                    $('.semresultado').html('Nenhum resultado, <a class="pesquisar_endereco" href="javascript:void(null)">[pesquisar novamente]</a><a class="inserir_manualmente" href="javascript:void(null)">[inserir manualmente]</a>');
                            } else {
                                var enderecoSelect = '<select class="altera_de_endereco"><option value="">Selecione</option>';
                                for(var enderecoElemento in resposta.enderecos){
                                   var end = resposta.enderecos[enderecoElemento];
                                    enderecoSelect += '<option value="'+end.descricao+'" uf="'+end.uf+'" endereco="'+end.endereco+'" cep="'+end.cep+'" cidade="'+end.cidade+'" bairro="'+end.bairro+'">'+end.descricao+'</option>';
                                }
                                enderecoSelect += '</select>';
                                opcoesPesquisar = '<br/><br/><a class="pesquisar_endereco" href="javascript:void(null)">[pesquisar novamente]</a><a class="inserir_manualmente" href="javascript:void(null)">[inserir manualmente]</a>';
                                jQuery(dados).find('.semresultado').html(enderecoSelect+opcoesPesquisar);
                                
                            }
                        });

        } else {
            alert('Endereço não pode ser nulo');
        }
    });

    //Inserir dados manualmente
    jQuery('body').delegate('.inserir_manualmente', 'click', function(){
        var dados =  jQuery(this).closest('.chamada_correios');
        var estado = jQuery(dados).find(".correios_estado");
        var cidade = jQuery(dados).find(".correios_cidade");
        var bairro = jQuery(dados).find(".correios_bairro");
        var endereco = jQuery(dados).find(".correios_endereco");
        var cep = jQuery(dados).find(".correios_cep");
            jQuery.post(ACTION, {
                            paisoid: 1,
                            acao : 'criaSelectEstado'
                        }, function(data) {
                            var resposta = $.parseJSON(data);            
                            optionEstado = '<option value="">Selecione</option>';
                            for(var estadoelemento in resposta.estado){
                               var est = resposta.estado[estadoelemento];
                               optionEstado += '<option value="'+est+'">'+est+'</option>';
                            }
                            jQuery(estado).replaceWith('<select name="'+jQuery(estado).attr('name')+'" class="'+jQuery(estado).attr('class')+'">'+optionEstado+'</select>');

                        });

        jQuery(cidade).replaceWith('<select name="'+jQuery(cidade).attr('name')+'" class="'+jQuery(cidade).attr('class')+'" ><option value="">Selecione o Estado</option></select>');
        jQuery(bairro).replaceWith('<select name="'+jQuery(bairro).attr('name')+'" class="'+jQuery(bairro).attr('class')+'" ><option value="">Selecione a Cidade</option></select>');
        jQuery(endereco).replaceWith('<input type="text" name="'+jQuery(endereco).attr('name')+'" class="'+jQuery(endereco).attr('class')+'" value=""/>');
        jQuery(cep).addClass('manual');
        jQuery(this).parent('.semresultado').html('');
    });
    //
    jQuery('body').delegate('.correios_estado', 'change', function(){
       
            var dados =  jQuery(this).closest('.chamada_correios');
            var estado = jQuery(dados).find(".correios_estado");
            var cidade = jQuery(dados).find(".correios_cidade");
            var bairro = jQuery(dados).find(".correios_bairro");
            var endereco = jQuery(dados).find(".correios_endereco");

            jQuery(".correios_cidade").removeClass('desabilitado');

            if (jQuery(this).val() != '') {
                jQuery.post(ACTION, {
                                estoid: jQuery(this).val(),
                                acao : 'criaSelectCidade'
                            }, function(data) {
                                var resposta = $.parseJSON(data);     
                                optionCidade = '<option value="">Selecione</option>';
                                for(var cidadeelemento in resposta.cidade){
                                   var cid = resposta.cidade[cidadeelemento];
                                   optionCidade += '<option value="'+cid+'" clcoid="'+cidadeelemento+'">'+cid+'</option>';
                                }
                                jQuery(cidade).replaceWith('<select name="'+jQuery(cidade).attr('name')+'" class="'+jQuery(cidade).attr('class')+'">'+optionCidade+'</select>');

                            });
            }
    });
    jQuery('body').delegate('.correios_cidade', 'change', function(){
        
            var dados =  jQuery(this).closest('.chamada_correios');
            var estado = jQuery(dados).find(".correios_estado");
            var cidade = jQuery(dados).find(".correios_cidade");
            var bairro = jQuery(dados).find(".correios_bairro");
            var endereco = jQuery(dados).find(".correios_endereco");
            valor = jQuery(':selected', jQuery(this)).attr('clcoid');

            jQuery(".correios_bairro").removeClass('desabilitado');
            
            if (valor != '') {
                jQuery.post(ACTION, {
                                clcoid: valor,
                                acao : 'criaSelectBairro'
                            }, function(data) {
                                var resposta = $.parseJSON(data);     
                                optionBairro = '<option value="">Selecione</option>';
                                optionBairro += '<option value="cadastromanual">-- Cadastrar Manual --</option>';
                                for(var bairroelemento in resposta.bairro){
                                   var bai = resposta.bairro[bairroelemento];
                                   optionBairro += '<option value="'+bai+'">'+bai+'</option>';
                                }
                                jQuery(bairro).replaceWith('<select name="'+jQuery(bairro).attr('name')+'" class="'+jQuery(bairro).attr('class')+'">'+optionBairro+'</select>');

                            });
            }
    });
    jQuery('body').delegate('.correios_bairro', 'change', function(){
        var valor = jQuery(this).val();
        var dados =  jQuery(this).closest('.chamada_correios');
        jQuery(dados).find(".correios_endereco").removeAttr("readonly");
        if (valor == 'cadastromanual') {
           
            var bairro = jQuery(dados).find(".correios_bairro");
            jQuery(bairro).replaceWith('<input type="text" name="'+jQuery(bairro).attr('name')+'" class="'+jQuery(bairro).attr('class')+'" value=""/>'); 
        }
    });
    jQuery('body').delegate('.altera_de_endereco', 'change', function(){
        if (jQuery(this).val() != '')
        {
            var dados =  jQuery(this).closest('.chamada_correios');
            var estado = jQuery(dados).find(".correios_estado");
            var cidade = jQuery(dados).find(".correios_cidade");
            var bairro = jQuery(dados).find(".correios_bairro");
            var endereco = jQuery(dados).find(".correios_endereco");
            var cep = jQuery(dados).find(".correios_cep");

            var respostaUf = jQuery(':selected', jQuery(this)).attr('uf');
            var respostaEndereco = jQuery(':selected', jQuery(this)).attr('endereco');
            var respostaCep = jQuery(':selected', jQuery(this)).attr('cep');
            var respostaCidade = jQuery(':selected', jQuery(this)).attr('cidade');
            var respostaBairro = jQuery(':selected', jQuery(this)).attr('bairro');
            jQuery(estado).val(respostaUf);
            jQuery(cidade).replaceWith('<input type="text" name="'+jQuery(cidade).attr('name')+'" class="'+jQuery(cidade).attr('class')+' desabilitado" readonly value="'+respostaCidade+'"/>');
            jQuery(bairro).replaceWith('<input type="text" name="'+jQuery(bairro).attr('name')+'" class="'+jQuery(bairro).attr('class')+'" value="'+respostaBairro+'"/>');
            jQuery(endereco).replaceWith('<input type="text" name="'+jQuery(endereco).attr('name')+'" class="'+jQuery(endereco).attr('class')+' desabilitado" readonly value="'+respostaEndereco+'"/>');
            jQuery(cep).val(respostaCep);
            jQuery(this).parent('.semresultado').html('');
        }
    });


    jQuery('body').delegate('.camponum', 'keyup', function(){    
        var valor = jQuery(this).val();
        if(!isNumber(valor))
        {
            jQuery(this).val(valor.replace(/[^\d]/g, ''));
        }
    });

    jQuery('body').delegate('.ip', 'keyup', function(){    
        var valor = jQuery(this).val();
            jQuery(this).val(valor.replace(/[^\d]/g, ''));
    });

    jQuery('body').delegate('.correios_cep', 'blur', function(){    
        /* reporte de erro, ao apagar todo o CEP e clicar fora deve limpar os campos */
        if (jQuery(this).val().length == 0)
        {
            resetaEndereco(jQuery(this));
        }
    });
    jQuery('body').delegate('.correios_cep', 'change', function(){    
        jQuery('.correios_cep').trigger('keyup');
    });
	$('body').ready(function() {
		jQuery('.correios_cep').trigger('keyup');
	});
    

    jQuery('body').delegate('.entrega_cep a', 'click', function(){
        jQuery("#endoid").val(jQuery(this).attr('endoid'));
        var copiarDe = ['.entrega_cep', '.entrega_estado', '.entrega_cidade', '.entrega_bairro', '.entrega_endereco', '.entrega_numero', '.entrega_complemento'];
        var copiarPara = ['.correios_cep', '.correios_estado', '.correios_cidade', '.correios_bairro', '.correios_endereco', '.correios_numero', '.correios_complemento'];
        for (var i = 0; i < copiarDe.length; i++) {
            jQuery(this).closest('.box').find(copiarPara[i]).val(jQuery(this).closest('tr').find(copiarDe[i]).text());
        }
        
    });

    jQuery('body').delegate('.correios_cep', 'keyup', function(){
        var valor = jQuery(this).val(); 
        var manual = jQuery(this).hasClass('manual');
        if(!isNumber(valor))
        {
            jQuery(this).val(valor.replace(/[^\d]/g, ''));
        } else if(valor.length == 8)  {
            var cep = jQuery(this).closest('.chamada_correios').find(".correios_cep").val();
            var loading_cep = jQuery(this).closest('.chamada_correios').find(".loading_cep");
            var dados =  jQuery(this).closest('.chamada_correios');
            $(loading_cep).show();


            jQuery.post(ACTION, {
                cep: cep,
                acao : 'criaSelectCep'
            }, function(data) {
                     
                var estado = jQuery(dados).find(".correios_estado");
                var cidade = jQuery(dados).find(".correios_cidade");
                var bairro = jQuery(dados).find(".correios_bairro");
                var endereco = jQuery(dados).find(".correios_endereco");
                
                //jQuery(".correios_bairro").removeClass('desabilitado');

                $(loading_cep).hide();
                var resposta = $.parseJSON(data);
                if (resposta.ocorrencia == 0) {
                	
                    jQuery(dados).find('.semresultado').html('<label style=\'width: 380px\'>Nenhum resultado encontrado para este CEP, deseja procurar por nome de rua? <a class="pesquisar_endereco" href="javascript:void(null)">[pesquisar]</a></label>');

                } else if (resposta.ocorrencia == 1) {
                    jQuery(estado).val(resposta.uf);
                    jQuery(cidade).replaceWith('<input type="text" name="'+jQuery(cidade).attr('name')+'" class="'+jQuery(cidade).attr('class')+' desabilitado" readonly value="'+resposta.cidade+'"/>');
                    jQuery(bairro).replaceWith('<input type="text" name="'+jQuery(bairro).attr('name')+'" class="'+jQuery(bairro).attr('class')+'" value="'+resposta.bairro+'"/>');
                    jQuery(endereco).replaceWith('<input type="text" name="'+jQuery(endereco).attr('name')+'" class="'+jQuery(endereco).attr('class')+' desabilitado" readonly value="'+resposta.endereco+'"/>');
                } else {
                    var cidadeArray = [];
                    var bairroArray = [];
                    var enderecoArray = [];
                    var i = 0;
                    for(var enderecoElemento in resposta.enderecos){
                       var end = resposta.enderecos[enderecoElemento];
                       jQuery(estado).val(end.uf);
                       bairroArray[i] = end.bairro;
                       cidadeArray[i] =  end.cidade;
                       enderecoArray[i] = end.tipo+' '+end.endereco;
                       i++;
                    }
                    bairroArray = unique(bairroArray);
                    enderecoArray = unique(enderecoArray);
                    cidadeArray = unique(cidadeArray);
                    
                    if (bairroArray.length == 1) {
                        jQuery(bairro).replaceWith('<input type="text" name="'+jQuery(bairro).attr('name')+'" class="'+jQuery(bairro).attr('class')+' desabilitado" readonly value="'+bairroArray[0]+'"/>');
                    } else if (bairroArray.length > 1) {
                        var bairrosSelect = '<select name="'+jQuery(bairro).attr('name')+'" class="'+jQuery(bairro).attr('class')+'">';
                        for (var i = 0; i < bairroArray.length; i++) {
                          bairrosSelect += '<option value="'+bairroArray[i]+'">'+bairroArray[i]+'</option>';
                        }
                        bairrosSelect += '</select>';
                        jQuery(bairro).replaceWith(bairrosSelect);
                    } else {
                        jQuery(bairro).replaceWith('<input type="text" name="'+jQuery(bairro).attr('name')+'" class="'+jQuery(bairro).attr('class')+'" value=""/>');
                    }
                    
                    if (cidadeArray.length == 1) {
                        jQuery(cidade).replaceWith('<input type="text" name="'+jQuery(cidade).attr('name')+'" class="'+jQuery(cidade).attr('class')+' desabilitado" readonly value="'+cidadeArray[0]+'"/>');
                    } else if (cidadeArray.length > 1) {
                        var cidadesSelect = '<select name="'+jQuery(cidade).attr('name')+'" class="'+jQuery(cidade).attr('class')+'">';
                        for (var i = 0; i < cidadeArray.length; i++) {
                          cidadesSelect += '<option value="'+cidadeArray[i]+'">'+cidadeArray[i]+'</option>';
                        }
                        cidadesSelect += '</select>';
                        jQuery(cidade).replaceWith(cidadesSelect);
                    } else {
                        jQuery(cidade).replaceWith('<input type="text" name="'+jQuery(cidade).attr('name')+'" class="'+jQuery(cidade).attr('class')+' desabilitado" readonly value=""/>');                        
                    }
                    
                    if (enderecoArray.length == 1) {
                        jQuery(endereco).replaceWith('<input type="text" name="'+jQuery(endereco).attr('name')+'" class="'+jQuery(endereco).attr('class')+' desabilitado" readonly value="'+enderecoArray[0]+'"/>');
                    } else if (enderecoArray.length > 1) {
                        var enderecosSelect = '<select name="'+jQuery(endereco).attr('name')+'" class="'+jQuery(endereco).attr('class')+'">';
                        for (var i = 0; i < enderecoArray.length; i++) {
                          enderecosSelect += '<option value="'+enderecoArray[i]+'">'+enderecoArray[i]+'</option>';
                        }
                        enderecosSelect += '</select>';
                        jQuery(endereco).replaceWith(enderecosSelect);
                    } else {
                        jQuery(endereco).replaceWith('<input type="text" name="'+jQuery(endereco).attr('name')+'" class="'+jQuery(endereco).attr('class')+'" value=""/>');
                    }

                }
                
            });
        }

    });
    

function validaCPF(cpf){

	var numeros, digitos, soma, i, resultado, digitos_iguais;
	digitos_iguais = 1;
    
	if (cpf.length < 11)
		return false;
      for (i = 0; i < cpf.length - 1; i++)
            if (cpf.charAt(i) != cpf.charAt(i + 1))
                  {
                  digitos_iguais = 0;
                  break;
                  }
			if (!digitos_iguais)
            {
            numeros = cpf.substring(0,9);
            digitos = cpf.substring(9);
            soma = 0;
            for (i = 10; i > 1; i--)
                  soma += numeros.charAt(10 - i) * i;
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(0))
                  return false;
            numeros = cpf.substring(0,10);
            soma = 0;
            for (i = 11; i > 1; i--)
                  soma += numeros.charAt(11 - i) * i;
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(1))
                  return false;
            return true;
            }
	else
		return false;
}
	
function ValidaCNPJ(cnpj) {

  var i = 0;
  var l = 0;
  var strNum = "";
  var strMul = "6543298765432";
  var character = "";
  var iValido = 1;
  var iSoma = 0;
  var strNum_base = "";
  var iLenNum_base = 0;
  var iLenMul = 0;
  var iSoma = 0;
  var strNum_base = 0;
  var iLenNum_base = 0;

  if (cnpj == "")
    return false;

  l = cnpj.length;
  for (i = 0; i < l; i++) {
    caracter = cnpj.substring(i,i+1)
    if ((caracter >= '0') && (caracter <= '9'))
       strNum = strNum + caracter;
  };

  if(strNum.length != 14)
    return false;

  strNum_base = strNum.substring(0,12);
  iLenNum_base = strNum_base.length - 1;
  iLenMul = strMul.length - 1;
  for(i = 0;i < 12; i++)
    iSoma = iSoma +
            parseInt(strNum_base.substring((iLenNum_base-i),(iLenNum_base-i)+1),10) *
            parseInt(strMul.substring((iLenMul-i),(iLenMul-i)+1),10);

  iSoma = 11 - (iSoma - Math.floor(iSoma/11) * 11);
  if(iSoma == 11 || iSoma == 10)
    iSoma = 0;

  strNum_base = strNum_base + iSoma;
  iSoma = 0;
  iLenNum_base = strNum_base.length - 1
  for(i = 0; i < 13; i++)
    iSoma = iSoma +
            parseInt(strNum_base.substring((iLenNum_base-i),(iLenNum_base-i)+1),10) *
            parseInt(strMul.substring((iLenMul-i),(iLenMul-i)+1),10)

  iSoma = 11 - (iSoma - Math.floor(iSoma/11) * 11);
  if(iSoma == 11 || iSoma == 10)
    iSoma = 0;
  strNum_base = strNum_base + iSoma;
  if(strNum != strNum_base) {
    return false
  }else{
    return true;
  }
}
	
function formTipoCliente($tipo) {
	
	if($tipo == "F") {

    	// desabilita campos do formulário pessoa juridica
    	jQuery("#camposPessoaJuridica").hide();
    	jQuery("#camposPessoaJuridica input, #camposPessoaJuridica select").attr("disabled", "disabled");
    	
    	// habilita campos do formulário pessoa fisica
    	jQuery("#camposPessoaFisica").show();        	
    	jQuery("#camposPessoaFisica input, #camposPessoaFisica select").removeAttr("disabled", "disabled");
    	
    	
    } else if($tipo == "J") {

    	// desabilita campos do formulário pessoa fisica
    	jQuery("#camposPessoaFisica input, #camposPessoaFisica select").attr("disabled", "disabled");
    	jQuery("#camposPessoaFisica").hide();

    	// habilita campos do formulário pessoa fisica
    	jQuery("#camposPessoaJuridica input, #camposPessoaJuridica select").removeAttr("disabled", "disabled");
    	jQuery("#camposPessoaJuridica").show(); 	
    	
    }
}

function validaFormEndereco(){
	
	// return true; // REMOVER
	// oculta mensagens anteriores
	jQuery('#mensagem').hide();
	
	var camposObrigatorios = new Array
					(
							".correios_cep",".correios_pais",".correios_estado",".correios_cidade",".correios_bairro",
							".correios_endereco",".correios_numero","#clifone_res","#clifone_cel",
							"#clino_email",".correios_emailnfe",".correios_correspondencia",".correios_observacao"
					);
	
	for(i=0;i<camposObrigatorios.length;i++){
		jQuery(camposObrigatorios[i]).removeClass("alerta");
	}
	
	// valida campos obrigatórios
    var erros = 0;
    
	for(i=0;i<camposObrigatorios.length;i++){
        if(jQuery(camposObrigatorios[i]).val() == "") {
            jQuery(camposObrigatorios[i]).addClass("alerta");
            erros++;
        }
	}
	
    if(erros > 0) {

    	jQuery('#mensagem').html("Existem campos obrigatórios não preenchidos.");
    	jQuery('#mensagem').addClass("alerta");
    	jQuery('#mensagem').show();
        return false;
    }
    
    jQuery('#carregando').show();
    
    //jQuery('#cad_cliente_particularidades').submit();/**/
    jQuery('#carregando').hide();
    
}

function formataTelefone(obj){


    

	tamanho = obj.value.length;
	if (tamanho == 0){
		obj.value += "(";
	}
	if (tamanho == 3){
		obj.value += ")";
	}

	/*
	Testa para ver se o ddd começa com 11 e coloca maxlength para 14
	exemplo: (11)95345-1234 que antes era assim (11)5345-1234
	*/

	if(/(\(11\)9(5[0-9]|6[0-9]|7[01234569]|8[0-9]|9[0-9])).+/i.test(obj.value)){
    	$(obj).attr('maxlength','14');
    	if (tamanho == 9){
    		obj.value += "-";
    	}
	} else {
		$(obj).attr('maxlength','13');
		if (tamanho == 8){
			obj.value += "-";
		}
	}
}



function CopiarDePara(copiarDe, copiarPara ){
    // Copiar de 
    var correios_cep = jQuery("#"+copiarDe+" .correios_cep");
    var correios_pais_opt = jQuery(':selected', jQuery("#"+copiarDe +" .correios_pais")).text();
    // if (correios_pais == '')
    // {
        var correios_pais = jQuery("#"+copiarDe +" .correios_pais");
    // }
    var correios_estado = jQuery("#"+copiarDe +" .correios_estado");
    var correios_cidade = jQuery("#"+copiarDe +" .correios_cidade");
    var correios_bairro = jQuery("#"+copiarDe +" .correios_bairro");
    var correios_endereco = jQuery("#"+copiarDe +" .correios_endereco");
    var correios_numero = jQuery("#"+copiarDe +" .correios_numero");
    var correios_complemento = jQuery("#"+copiarDe +" .correios_complemento");
    
    // Copiar para
    var para_cep = jQuery("#"+copiarPara+" .correios_cep");
    var para_pais = jQuery("#"+copiarPara+" .correios_pais");
    var para_estado = jQuery("#"+copiarPara+" .correios_estado");
    var para_cidade = jQuery("#"+copiarPara+" .correios_cidade");
    var para_bairro = jQuery("#"+copiarPara+" .correios_bairro");
    var para_endereco = jQuery("#"+copiarPara+" .correios_endereco");
    var para_numero = jQuery("#"+copiarPara+" .correios_numero");
    var para_complemento = jQuery("#"+copiarPara+" .correios_complemento");

    jQuery(para_cep).replaceWith('<input type="text" name="'+jQuery(para_cep).attr('name')+'" id="'+jQuery("#"+copiarPara+" .correios_cep").attr('id')+'" class="'+jQuery("#"+copiarPara+" .correios_cep").attr('class')+'" value="'+jQuery(correios_cep).val()+'"/>');
    jQuery(para_pais).replaceWith('<select name="'+jQuery(para_pais).attr('name')+'" id="'+jQuery(correios_pais).attr('id')+'" class="'+jQuery(correios_pais).attr('class')+'"><option value="'+correios_pais.val()+'">'+correios_pais_opt+'</option></select>');
    jQuery(para_estado).val(correios_estado.val());
    jQuery(para_cidade).replaceWith('<input type="text" name="'+jQuery(para_cidade).attr('name')+'" id="'+jQuery(para_cidade).attr('id')+'" class="'+jQuery(para_cidade).attr('class')+' desabilitado" value="'+correios_cidade.val()+'" readonly/>');
    jQuery(para_bairro).replaceWith('<input type="text" name="'+jQuery(para_bairro).attr('name')+'" id="'+jQuery(para_bairro).attr('id')+'" class="'+jQuery(para_bairro).attr('class')+' desabilitado" value="'+correios_bairro.val()+'" readonly/>');
    jQuery(para_endereco).replaceWith('<input type="text" name="'+jQuery(para_endereco).attr('name')+'" id="'+jQuery(para_endereco).attr('id')+'" class="'+jQuery(para_endereco).attr('class')+' desabilitado" value="'+correios_endereco.val()+'" readonly/>');
    jQuery(para_numero).replaceWith('<input type="text" name="'+jQuery(para_numero).attr('name')+'" id="'+jQuery(para_numero).attr('id')+'" class="'+jQuery(para_numero).attr('class')+' desabilitado" maxlength="7" value="'+correios_numero.val()+'" readonly/>');
    jQuery(para_complemento).replaceWith('<input type="text" name="'+jQuery(para_complemento).attr('name')+'" id="'+jQuery(para_complemento).attr('id')+'" class="'+jQuery(para_complemento).attr('class')+'" value="'+correios_complemento.val()+'"/>');
    
}

function resetaEndereco (elemento) {
    var dados =  jQuery(elemento).closest('.chamada_correios');
    var estado = jQuery(dados).find(".correios_estado");
    var cidade = jQuery(dados).find(".correios_cidade");
    var bairro = jQuery(dados).find(".correios_bairro");
    var endereco = jQuery(dados).find(".correios_endereco");
    var numero = jQuery(dados).find(".correios_numero");
    var complemento = jQuery(dados).find(".correios_complemento");
    var cep = jQuery(dados).find(".correios_cep");
    jQuery(estado).val('');
    jQuery(cidade).val('');
    jQuery(bairro).val('');
    jQuery(endereco).val('');
    jQuery(numero).val('').removeClass('desabilitado').removeAttr('readonly');
    jQuery(complemento).val('');
    jQuery(cep).val('');
}

function verificaObrigacao(valor){
	if(valor != 50){
		jQuery("#cliosoftware_principal").val("").attr('disabled',true).addClass('desabilitado');
		jQuery("#cliosoftware_secundario").val("").attr('disabled',true).addClass('desabilitado');
	}else{
		jQuery("#cliosoftware_principal").val("").attr('disabled',false).removeClass('desabilitado');
		jQuery("#cliosoftware_secundario").val("").attr('disabled',false).removeClass('desabilitado');
	}
}

function verificaFaturamento(valor){
	if(valor == 'cortesia' || valor == 'demonstracao'){
		jQuery("#cliodemonst_aprov").val("").attr('disabled',false).removeClass('desabilitado');
		
		if(valor == 'demonstracao'){
			jQuery("#cliodemonst_validade").val("").attr('disabled',false).removeClass('desabilitado');
		}else{
			jQuery("#cliodemonst_validade").val("").attr('disabled',true).addClass('desabilitado');
		}
	}else{
		jQuery("#cliodemonst_aprov").val("").attr('disabled',true).addClass('desabilitado');
		jQuery("#cliodemonst_validade").val("").attr('disabled',true).addClass('desabilitado');
	}
}

// fim arquivo