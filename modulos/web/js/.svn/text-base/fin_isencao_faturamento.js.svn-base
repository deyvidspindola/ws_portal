jQuery(document).ready(function(){

	// permite somente números
	jQuery('#conoid_busca').mask('9?99999999999', { placeholder: '' });
	   
	// função marcar/ desmarcar tds, em lib/layout/1.1.0/bootstrap.js
	jQuery("#marcar_todos").checarTodos(".contrato");
	   
	// Atribui o mês atual (obs. getMonth() retorna de 0 a 11)
	var dt_atu = new Date();
	var mes = ((dt_atu.getMonth()) + 1);
	
	//botâo novo
	jQuery("#bt_novo").click(function(){
		window.location.href = "fin_isencao_faturamento.php?acao=cadastrar";
	});
   
	//botâo limpar pesquisa
	jQuery("#bt_limpar").click(function(){
       window.location.href = "fin_isencao_faturamento.php";
	});

	//botâo atualizar
	jQuery('#bt_atualizar').click(function(){

		arrayContratos 	= new Array();
		arrayParfoid	= new Array();
	   
		jQuery("input[type=checkbox][name='contrato[]']:checked").each(function(){
			var result=(jQuery(this).val()).split('#');
			if (result[0]){
				arrayContratos.push(result[0]);
			}
			if (result[1]){
				arrayParfoid.push(result[1]);
			}
		});
       
		// Exibe alerta
		if(arrayContratos.length == 0){
			jQuery('html, body').scrollTop(0);
			jQuery('#mensagem_alerta').show();
			jQuery('#mensagem_alerta').html('Nenhum registro selecionado.');
		}
		// Abre o modal para cadastro de dados da paralisação
		else {
			jQuery('#mensagem_alerta').hide();
			jQuery('#mensagem_erro').hide();
			jQuery('#mensagem_sucesso').hide();
		   
			jQuery.ajax({
		         type	: 'get',
		         data	: {
		         	arrayContratos	: arrayContratos,
		         	arrayParfoid	: arrayParfoid
		         },
		         url	: 'fin_isencao_faturamento.php?acao=editar',
		         error	: function() {
		            jQuery('#div_mensagem_geral')
		            .removeClass('alerta sucesso invisivel')
		            .addClass('erro')
		            .html('Houve um erro na comunicação com o servidor.');
		        },
				async : false,
		        success : function(response){ 
		            jQuery("#solicitar-paralisacao-form").html(response);
		            jQuery("#solicitar-paralisacao-form").dialog({
		                autoOpen: false,
		                minHeight: 300 ,
		                maxHeight: 'auto' ,
		                width: '60%',
		                modal: true,
		                buttons: {
		                    "Confirmar": function() {

		                    	var periodo_isencao 	= jQuery('#periodo_isencao').val();
		                        var parfemail_contato 	= jQuery('#parfemail_contato').val();
	                        	
		                        // Confirma
	                        	confirmar(arrayContratos, periodo_isencao, parfemail_contato, arrayParfoid, this);
		                        
		                    },
		                    "Cancelar": function() {
		                        jQuery(this).dialog("close");
		                    }
		                }
		            }).dialog('open');
		        }    
		    });
		   
		}
	   
	});
   
	//botão excluir
	jQuery("#bt_excluir").click(function(){
	   arrayContratos 	= new Array();
	   arrayParfoid		= new Array();
	   var placas 		= '';
	   var separa 		= ': ';
	   
		jQuery("input[type=checkbox][name='contrato[]']:checked").each(function(){
		   var result=(jQuery(this).val()).split('#');
		   if (result[0]){
			   arrayContratos.push(result[0]);
		   }
		   if (result[1]){
			   arrayParfoid.push(result[1]);
		   }
		   if (result[2]){				   
			   var chr = result[2].substring(0,3).toLocaleUpperCase();
			   var num = result[2].substring(3);
			   var plc = chr.concat('-',num);
			   
			   placas = placas.concat(separa,plc);
			   separa = ', ';
		   }
		   
		});

		// Exibe alerta
		if(arrayContratos.length == 0){
		   	jQuery('#mensagem_alerta').hide();
			jQuery('html, body').scrollTop(0);
			jQuery('#mensagem_alerta').show();
			jQuery('#mensagem_alerta').html('Nenhum registro selecionado.');
			jQuery('#acao').val('');
		}
		else if(arrayContratos.length > 0 && arrayParfoid.length == 0){
		   	jQuery('#mensagem_alerta').hide();
			jQuery('html, body').scrollTop(0);
			jQuery('#mensagem_alerta').show();
			jQuery('#mensagem_alerta').html('A exclusão não pode ser concluída, pois o(s) registro(s), não possui(em) paralisação cadastrada.');
			jQuery('#acao').val('');	   
		}
		else{			
		   //Confirma exclusão dos veículos placa(s) xxx-9999, xxx-9999
			if (confirm('Confirma a exclusão, da isenção de faturamento, do(s) veículo(s)'.concat(placas,'?'))){
		        jQuery('#arrayParfoid').val(arrayParfoid);
		        jQuery('#acao').val('excluir');
		        jQuery('#form').submit();
		   }
		}
	});

	//link visualizar no contrato
	jQuery('.link').click(function(){

		arrayContratos 	= new Array();
		arrayParfoid		= new Array();
		var parfoid 		= jQuery(this).attr('id');
		var status 		= jQuery(this).attr('status');
		arrayParfoid.push(parfoid);
		
		// Remove mensagens
		jQuery('#mensagem_alerta').hide();
		jQuery('#mensagem_erro').hide();
		jQuery('#mensagem_sucesso').hide();
	   
		// Abre o modal para visualização de dados da paralisação
		jQuery.ajax({
	        type	: 'get',
	        data	: {
				parfoid	: parfoid
	        },
	        url		: 'fin_isencao_faturamento.php?acao=visualizar',
	        async 	: false,
	        error	: function() {
	            jQuery('#div_mensagem_geral')
	            .removeClass('alerta sucesso invisivel')
	            .addClass('erro')
	            .html('Houve um erro na comunicação com o servidor.');
	        },
	        success : function(response){ 
	            jQuery("#solicitar-paralisacao-form").html(response);
	            jQuery("#solicitar-paralisacao-form").dialog({
	                autoOpen: false,
	                minHeight: 300 ,
	                maxHeight: 'auto' ,
	                width: '60%',
	                modal: true,
	                buttons: { 
                   		"Confirmar": function() {
							var periodo_isencao 	= jQuery('#periodo_isencao').val();
                   			var parfemail_contato 	= jQuery('#parfemail_contato').val();
                   			// Confirma
                   			confirmar(arrayContratos, periodo_isencao, parfemail_contato, arrayParfoid, this);
                   		},
	                    "Cancelar": function() {
	                        jQuery(this).dialog("close");
	                    }
	                }
	            }).dialog('open');
	            	            
           		// Se diferente de 'Em Isenção' não permite confirmar
           		if (status != 'ap03') {
           			// Exibe apenas o botão cancelar
           			jQuery( "#solicitar-paralisacao-form" ).dialog( "option", "buttons", [{
           			      text: "Cancelar",
           			      click: function() {
           			    	 jQuery(this).dialog("close");
           			      }
           			    }]
           			);
    	            
    	            // Remove mensagem 
    	     	   	jQuery('#mensagem_info').hide();
           		}

				// Desabilita os campos
				jQuery("#periodo_isencao").attr('disabled','disabled');
				jQuery("#parfemail_contato").attr('disabled','disabled');
	        }    
	    });
	});

	//botão limpar resultado pesquisa
	jQuery("#bt_limpar2").click(function(){
		window.location.href = "fin_isencao_faturamento.php";
	});
   
	// Habilita/ desabilita checkboxes de meses ao clicar no 4° Check
	jQuery("body").delegate('#cancelar_4', 'click', function(){
		var $this 	= jQuery(this);
   
		if ($this.is(':checked')) {
			// the checkbox was checked
			// atribui novo valor à combo Periodo Isenção
			jQuery("#periodo_isencao option[value='120']").attr("selected","selected");
		} else {
			// the checkbox was unchecked
			jQuery("#cancelar_4").attr('disabled','disabled');
			jQuery("#cancelar_3").removeAttr('disabled');
			jQuery("#cancelar_2").attr('disabled','disabled');
			// atribui novo valor à combo Periodo Isenção
			jQuery("#periodo_isencao option[value='90']").attr("selected","selected");
		}
		
		// Os checks referentes aos meses menores ou iguais ao mês atual devem estar desabilitados e checados
		if ($this.val() <= mes) {
			jQuery("#cancelar_2").attr('disabled','disabled');
			jQuery("#cancelar_2").attr('checked','checked');
			jQuery("#cancelar_3").attr('disabled','disabled');
			jQuery("#cancelar_3").attr('checked','checked');
			jQuery("#cancelar_4").attr('disabled','disabled');
			jQuery("#cancelar_4").attr('checked','checked');
		} 
		
		// O check 1 deve estar sempre desabilitado e checado
		jQuery("#cancelar_1").attr('disabled','disabled');
		jQuery("#cancelar_1").attr('checked','checked');
	}); 
   
	// Habilita/ desabilita checkboxes de meses ao clicar no 3° Check
	jQuery("body").delegate('#cancelar_3', 'click', function(){
		var $this = jQuery(this); 

		if ($this.is(':checked')) {
			// the checkbox was checked
			// Se existir um 4° Check
			if ( jQuery("#cancelar_4").length ) {
				jQuery("#cancelar_4").removeAttr('disabled');
				jQuery("#cancelar_3").attr('disabled','disabled');
				jQuery("#cancelar_2").attr('disabled','disabled');				
			}
			else {
				jQuery("#cancelar_3").removeAttr('disabled');
				jQuery("#cancelar_2").attr('disabled','disabled');
			}
			// atribui novo valor à combo Periodo Isenção
			jQuery("#periodo_isencao option[value='90']").attr("selected","selected");
		} else {
			// the checkbox was unchecked
			jQuery("#cancelar_4").attr('disabled','disabled');
			jQuery("#cancelar_3").attr('disabled','disabled');
			jQuery("#cancelar_2").removeAttr('disabled');
			// atribui novo valor à combo Periodo Isenção
			jQuery("#periodo_isencao option[value='60']").attr("selected","selected");
		}
	   
		// Os checks referentes aos meses menores ou iguais ao mês atual devem estar desabilitados e checados
		if ($this.val() <= mes) {
			jQuery("#cancelar_2").attr('disabled','disabled');
			jQuery("#cancelar_2").attr('checked','checked');
			jQuery("#cancelar_3").attr('disabled','disabled');
			jQuery("#cancelar_3").attr('checked','checked');
		}
   
		// O check 1 deve estar sempre desabilitado e checado
		jQuery("#cancelar_1").attr('disabled','disabled');
		jQuery("#cancelar_1").attr('checked','checked');
	});
   
	// Habilita/ desabilita checkboxes de meses ao clicar no 2° Check
	jQuery("body").delegate('#cancelar_2', 'click', function(){
		var $this = jQuery(this);
	   
		if ($this.is(':checked')) {
			// the checkbox was checked
			// Se existir um 3° Check
			if ( jQuery("#cancelar_3").length ) {
				jQuery("#cancelar_4").attr('disabled','disabled');
				jQuery("#cancelar_3").removeAttr('disabled');
				jQuery("#cancelar_2").attr('disabled','disabled');
			}
			else {
				jQuery("#cancelar_2").removeAttr('disabled');
			}
			// atribui novo valor à combo Periodo Isenção
			jQuery("#periodo_isencao option[value='60']").attr("selected","selected");
		} else {
			// the checkbox was unchecked
			jQuery("#cancelar_2").removeAttr('disabled');
			// atribui novo valor à combo Periodo Isenção
			jQuery("#periodo_isencao option[value='30']").attr("selected","selected");
		}

		// Os checks referentes aos meses menores ou iguais ao mês atual devem estar desabilitados e checados
		if ($this.val() <= mes) {
			jQuery("#cancelar_2").attr('disabled','disabled');
			jQuery("#cancelar_2").attr('checked','checked');
			jQuery("#periodo_isencao option[value='60']").attr("selected","selected");
			// Se existir um 3° Check
			if ( jQuery("#cancelar_3").length ) {
				jQuery("#cancelar_3").removeAttr('disabled');
			}
		}
		
		// O check 1 deve estar sempre desabilitado e checado
		jQuery("#cancelar_1").attr('disabled','disabled');
		jQuery("#cancelar_1").attr('checked','checked');		
		
	});
   
});

// Aplica a máscara no campo CPF/ CNPJ no evento input para formatação dinâmica
function aplica_mascara_cpfcnpj(campo,tammax,teclapres) {
	var tecla = teclapres.keyCode;

	if ((tecla < 48 || tecla > 57) && (tecla < 96 || tecla > 105) && tecla != 46 && tecla != 8) {
		return false;
	}

	var vr = campo.value;
	vr = vr.replace( /\//g, "" );
	vr = vr.replace( /-/g, "" );
	vr = vr.replace( /\./g, "" );
	var tam = vr.length;

	if ( tam <= 2 ) {
		campo.value = vr;
	}
	if ( (tam > 2) && (tam <= 5) ) {
		campo.value = vr.substr( 0, tam - 2 ) + '-' + vr.substr( tam - 2, tam );
	}
	if ( (tam >= 6) && (tam <= 8) ) {
		campo.value = vr.substr( 0, tam - 5 ) + '.' + vr.substr( tam - 5, 3 ) + '-' + vr.substr( tam - 2, tam );
	}
	if ( (tam >= 9) && (tam <= 11) ) {
		campo.value = vr.substr( 0, tam - 8 ) + '.' + vr.substr( tam - 8, 3 ) + '.' + vr.substr( tam - 5, 3 ) + '-' + vr.substr( tam - 2, tam );
	}
	if ( (tam == 12) ) {
		campo.value = vr.substr( tam - 12, 3 ) + '.' + vr.substr( tam - 9, 3 ) + '/' + vr.substr( tam - 6, 4 ) + '-' + vr.substr( tam - 2, tam );
	}
	if ( (tam > 12) && (tam <= 14) ) {
		campo.value = vr.substr( 0, tam - 12 ) + '.' + vr.substr( tam - 12, 3 ) + '.' + vr.substr( tam - 9, 3 ) + '/' + vr.substr( tam - 6, 4 ) + '-' + vr.substr( tam - 2, tam );
	}
}

// Ações ao confirmar alteração/ edição
function confirmar(arrayContratos, periodo_isencao, parfemail_contato, arrayParfoid, dialog){
	
    jQuery.ajax({
        type 		: 'post',
        data 		: {
        	arrayContratos		: arrayContratos,
        	periodo_isencao		: periodo_isencao,
        	parfemail_contato	: parfemail_contato,
        	arrayParfoid		: arrayParfoid
        },
        url  		: 'fin_isencao_faturamento.php?acao=confirmar',
        dataType 	: 'json',
		async 		: false,
        beforeSend : function (){
        	 jQuery('.mensagem').not('.info').remove();
        },
        error 		: function(){
            jQuery('#div_mensagem_geral')
            .removeClass('alerta sucesso invisivel')
            .addClass('erro')
            .html('Houve um erro na comunicação com o servidor.');
        },
        success 	: function(response){
        	
        	if (response.status) {
                jQuery(dialog).dialog("close");
                jQuery('#form').before('<div class="mensagem sucesso">' + response.mensagem.texto + '</div>');
                jQuery('#resultado_pesquisa').remove();
        	}
        	else {
        		
            	if (response.mensagem.tipo == 'alerta') {
                        jQuery('.info').after('<div class="mensagem alerta">' + response.mensagem.texto + '</div>');
                }
                if (response.mensagem.tipo == 'erro') {
                        jQuery('.info').after('<div class="mensagem erro">' + response.mensagem.texto + '</div>');
                }
               
                showFormErros(response.camposdestaque);
        	}
        }
    })
}