jQuery(function() {    	

	//não deixa copiar e colar
	$(document).keydown(function(event) {
		if (event.ctrlKey==true && (event.which == '118' || event.which == '86')) {
			event.preventDefault();
		}
	});
	
	// Máscara Numérica
    jQuery('.numerico').keyup( function(e){
		var campo = '#'+this.id;
		var valor = jQuery(campo).val().replace(/[^0-9,.]+/g,'');

		jQuery(campo).val(valor);
	});
    
	
	if (jQuery('#mensagem').text() == '') {
        jQuery('#mensagem').hide();
    }

    // validação pesquisar
    jQuery('#buttonPesquisar').click(function(){
    	
    	
    	
    	valor1 = (jQuery('#alcovlr_inicio_pesq').val().length >= 7) ? jQuery('#alcovlr_inicio_pesq').val().replace('.','').replace(',','.') : jQuery('#alcovlr_inicio_pesq').val().replace(',','.');
		valor2 = (jQuery('#alcovlr_fim_pesq').val().length >= 7) ?  jQuery('#alcovlr_fim_pesq').val().replace('.','').replace(',','.') : jQuery('#alcovlr_fim_pesq').val().replace(',','.');

		if(parseFloat(valor1) > parseFloat(valor2)){
			jQuery('#mensagem').html('Valor Final menor que valor Inicial');
			jQuery('#alcovlr_inicio_pesq').addClass("erro");
			jQuery('#alcovlr_fim_pesq').addClass("erro");
	    	jQuery('#mensagem').addClass("alerta");
	    	jQuery('#mensagem').show();
	    	// jQuery('#buttonGravar').attr("disabled", true);
	        return false;
		
		}else{
			
			jQuery('#alcovlr_inicio_pesq').removeClass("erro");
			jQuery('#alcovlr_fim_pesq').removeClass("erro");
	    	jQuery('#mensagem').hide();
		}
    	
    	if(jQuery('#data_final').val() < jQuery('#data_inicial').val() && jQuery('#data_inicial').val() != ''){
    		
    		jQuery('#mensagem').html('Data final não pode ser menor que data inicial');
    		jQuery("#data_inicial").addClass("erro");
    		jQuery("#data_final").addClass("erro");
	    	jQuery('#mensagem').addClass("alerta");
	    	jQuery('#mensagem').show();
	        return false;
	        
    	}else if(calculaPeriodo($("#data_inicial").val(), $("#data_final").val()) > 365) {

    		jQuery("#data_inicial").addClass("erro");
    		jQuery("#data_final").addClass("erro");
    		jQuery('#mensagem').html('Favor selecionar um período com intervalo menor que um ano.');
    		jQuery('#mensagem').addClass("alerta");
    		jQuery('#mensagem').show();
    		
    		
    		return false;

    	}else{
    		
    		jQuery("#data_inicial").removeClass("erro");
    		jQuery("#data_final").removeClass("erro");
    		jQuery('#mensagem').hide();
    		
    		var acaoValor = jQuery(this).val();
			jQuery('#acao').val(acaoValor).closest('form').submit();
	    }
    });
    
    // submit form
	jQuery('body').delegate('#buttonCancelar,#buttonExcluir', 'click', function(){
		
		var acaoValor = jQuery(this).val();
		
		jQuery('#acao').val(acaoValor).closest('form').submit();
	});

	// abre formulario cadastro
	jQuery("#buttonNovo").click(function(){
		jQuery('#acao').val('cadastrar');
		var acaoValor = jQuery(this).val();
		
		jQuery('#acao').val(acaoValor).closest('form').submit();
	});

	// limpa formulario
	jQuery("#buttonLimpar").click(function(){
		
		jQuery('#alcovlr_inicio_pesq').val('');
		jQuery('#alcovlr_fim_pesq').val('');	
		
		//primeiro aprovador
		jQuery('#alcousuoid').val('');
		jQuery('#alcovlr_inicio').val('');
		jQuery('#alcovlr_fim').val('');
		jQuery('#alcodupla_check').removeAttr('checked');
		jQuery('#alcodt_exclusao').removeAttr('checked');
		jQuery('#data_inicial').val('');
		jQuery('#data_final').val('');	
		
		//segundo aprovador
		jQuery('#alcousuoid_dupla_check').val('');
		jQuery('#alcovlr_inicio_dupla_check').val('');
		jQuery('#alcovlr_fim_dupla_check').val('');
		
		//erros
		jQuery('#alcousuoid_dupla_check').removeClass("erro");
		jQuery('#alcousuoid').removeClass("erro");
		jQuery("#alcovlr_inicio_pesq").removeClass("erro");
		jQuery("#alcovlr_fim_pesq").removeClass("erro");
		jQuery("#data_inicial").removeClass("erro");
		jQuery("#data_final").removeClass("erro");
		
		jQuery('#alcovlr_inicio').removeClass("erro");
		jQuery('#alcovlr_fim').removeClass("erro");
		jQuery('#alcovlr_inicio_dupla_check').removeClass("erro");
		jQuery('#alcovlr_fim_dupla_check').removeClass("erro");
		
		jQuery('#mensagem').hide();
		
	});

	// validação de valores
	jQuery('#alcovlr_fim').blur( function(){
		valor1 = (jQuery('#alcovlr_inicio').val().length >= 7) ? jQuery('#alcovlr_inicio').val().replace('.','').replace(',','.') : jQuery('#alcovlr_inicio').val().replace(',','.');
		valor2 = (jQuery('#alcovlr_fim').val().length >= 7) ?  jQuery('#alcovlr_fim').val().replace('.','').replace(',','.') : jQuery('#alcovlr_fim').val().replace(',','.');

		if(parseFloat(valor1) > parseFloat(valor2)){
			jQuery('#mensagem').html('Valor Final menor que valor Inicial');
	    	jQuery('#mensagem').addClass("alerta");
	    	jQuery('#mensagem').show();
	    	// jQuery('#buttonGravar').attr("disabled", true);
	        return false;
		}else{
			//jQuery('#mensagem').hide();
			jQuery('#buttonGravar').removeAttr("disabled");
		}
	});

	// validação valor inicial segundo aprovador
	jQuery('#alcovlr_inicio_dupla_check').blur( function(){

		valor1 = (jQuery('#alcovlr_inicio_dupla_check').val().length >= 7) ? jQuery('#alcovlr_inicio_dupla_check').val().replace('.','').replace(',','.') : jQuery('#alcovlr_inicio_dupla_check').val().replace(',','.');
		valor2 = (jQuery('#alcovlr_fim').val().length >= 7) ?  jQuery('#alcovlr_fim').val().replace('.','').replace(',','.') : jQuery('#alcovlr_fim').val().replace(',','.');

		if(parseFloat(valor1) < parseFloat(valor2)){
			jQuery('#mensagem').html('Valor Inicial (Segundo Aprovador) tem que ser maior que o Valor Final do aprovador.');
	    	jQuery('#mensagem').addClass("alerta");
	    	jQuery('#mensagem').show();
	    	// jQuery('#buttonGravar').attr("disabled", true);
	        return false;
	    }else{
			//jQuery('#mensagem').hide();
			jQuery('#buttonGravar').removeAttr("disabled");
		}
	});

	// validação valor final segundo aprovador
	jQuery('#alcovlr_fim_dupla_check').blur( function(){
		
			alcovlr_fim = (jQuery('#alcovlr_fim').val().length >= 7) ?  jQuery('#alcovlr_fim').val().replace('.','').replace(',','.') : jQuery('#alcovlr_fim').val().replace(',','.');
			alcovlr_fim_dupla_check = (jQuery('#alcovlr_fim_dupla_check').val().length >= 7) ?  jQuery('#alcovlr_fim_dupla_check').val().replace('.','').replace('.','').replace(',','.') : jQuery('#alcovlr_fim_dupla_check').val().replace(',','.');

			if(parseFloat(alcovlr_fim) > parseFloat(alcovlr_fim_dupla_check)){
				
				jQuery('#mensagem').html('Valor Final (Segundo Aprovador) tem que ser maior que o Valor Final do aprovador.');
		    	jQuery('#mensagem').addClass("alerta");
		    	jQuery('#mensagem').show();
		    	// jQuery('#buttonGravar').attr("disabled", true);
		        return false;
	    }else{
			//jQuery('#mensagem').hide();
			jQuery('#buttonGravar').removeAttr("disabled");
		}
	});

	if(jQuery('#alcodupla_check').is(':checked')){
		jQuery('#alcousuoid_dupla_check').removeAttr('disabled');
		jQuery('#alcovlr_inicio_dupla_check').removeAttr('disabled');
		jQuery('#alcovlr_fim_dupla_check').removeAttr('disabled');
	}

	jQuery('#alcodupla_check').click(function(){
		if(jQuery('#alcodupla_check').is(':checked')){
			jQuery('#alcousuoid_dupla_check').removeAttr('disabled');
			jQuery('#alcovlr_inicio_dupla_check').removeAttr('disabled');
			jQuery('#alcovlr_fim_dupla_check').removeAttr('disabled');
		}else{
			jQuery('#alcousuoid_dupla_check').attr('disabled','true');
			jQuery('#alcovlr_inicio_dupla_check').attr('disabled','true');
			jQuery('#alcovlr_fim_dupla_check').attr('disabled','true');			
		}
	});

	// submit formulario para grava
	jQuery('body').delegate('#buttonGravar', 'click', function(){

		
	
		
		
		alcovlr_inicio = (jQuery('#alcovlr_inicio').val().length >= 7) ? jQuery('#alcovlr_inicio').val().replace('.','').replace(',','.') : jQuery('#alcovlr_inicio').val().replace(',','.');
		alcovlr_fim = (jQuery('#alcovlr_fim').val().length >= 7) ?  jQuery('#alcovlr_fim').val().replace('.','').replace(',','.') : jQuery('#alcovlr_fim').val().replace(',','.');
		
		
		
		
		if(parseFloat(alcovlr_inicio) > parseFloat(alcovlr_fim)){
			jQuery('#mensagem').html('Valor Final menor que valor Inicial');
	    	jQuery('#mensagem').addClass("alerta");
	    	jQuery('#mensagem').show();
	    	// jQuery('#buttonGravar').attr("disabled", true);
	        return false;
		}else{
			jQuery('#mensagem').hide();
			jQuery('#buttonGravar').removeAttr("disabled");
		}

		if(jQuery('#alcodupla_check').is(':checked') == true){
			alcovlr_inicio_dupla_check = (jQuery('#alcovlr_inicio_dupla_check').val().length >= 7) ? jQuery('#alcovlr_inicio_dupla_check').val().replace('.','').replace(',','.') : jQuery('#alcovlr_inicio_dupla_check').val().replace(',','.');
			alcovlr_fim = (jQuery('#alcovlr_fim').val().length >= 7) ?  jQuery('#alcovlr_fim').val().replace('.','').replace(',','.') : jQuery('#alcovlr_fim').val().replace(',','.');

			if(parseFloat(alcovlr_inicio_dupla_check) < parseFloat(alcovlr_fim)){
				jQuery('#mensagem').html('Valor Inicial (Segundo Aprovador) tem que ser maior que o Valor Final do aprovador.');
		    	jQuery('#mensagem').addClass("alerta");
		    	jQuery('#mensagem').show();
		    	// jQuery('#buttonGravar').attr("disabled", true);
		        return false;
		    }else{
				jQuery('#mensagem').hide();
				jQuery('#buttonGravar').removeAttr("disabled");
			}

			alcovlr_inicio_dupla_check = (jQuery('#alcovlr_inicio_dupla_check').val().length >= 7) ? jQuery('#alcovlr_inicio_dupla_check').val().replace('.','').replace(',','.') : jQuery('#alcovlr_inicio_dupla_check').val().replace(',','.');
			alcovlr_fim_dupla_check = (jQuery('#alcovlr_fim_dupla_check').val().length >= 7) ?  jQuery('#alcovlr_fim_dupla_check').val().replace('.','').replace('.','').replace(',','.') : jQuery('#alcovlr_fim_dupla_check').val().replace(',','.');

			if(parseFloat(alcovlr_fim) > parseFloat(alcovlr_fim_dupla_check)){
				
				jQuery('#mensagem').html('Valor Final (Segundo Aprovador) tem que ser maior que o Valor Final do aprovador.');
		    	jQuery('#mensagem').addClass("alerta");
		    	jQuery('#mensagem').show();
		    	// jQuery('#buttonGravar').attr("disabled", true);
		        return false;
		    }else{
				jQuery('#mensagem').hide();
				jQuery('#buttonGravar').removeAttr("disabled");
			}
		}

		
		if(jQuery('#alcousuoid').val() == jQuery('#alcousuoid_dupla_check').val() && jQuery('#alcodupla_check').is(':checked')){
			jQuery('#mensagem').html('O "usuário aprovador" e "segundo usuário aprovador" não podem ser iguais.');
	    	jQuery('#mensagem').addClass("alerta");
	    	jQuery('#mensagem').show();
	    	// jQuery('#buttonGravar').attr("disabled", true);
	        return false;
		}

		if(validaForm()){
			var acaoValor = jQuery(this).val();
			jQuery('#acao').val(acaoValor).closest('form').submit();
		}
		

	});	

	jQuery('.valor').maskMoney({thousands:'.', decimal:','});
	jQuery('.valorZerado').maskMoney({thousands:'.', decimal:',',allowZero: true});

	/**
     * Ação de excluir de particularidades.
     */
    jQuery('body').delegate('.excluirAlcada', 'click', function(){
        if (confirm('Deseja realmente excluir esta alçada de aprovação ?')) {
            
            var alcoid = jQuery(this).attr('alcoid');
            jQuery('#alcoid').val(alcoid);
            jQuery('#excluirAlcada').submit();
            
        }
    });

});    


/**
 * Calcula diferença de dias entre data final e data inicial
 * 
 * @param Date inicio
 * @param Date fim
 * 
 * @return Integer
 */
function calculaPeriodo(inicio, fim) {
    
    var qtdDias   = 0;
    var diferenca = 0;

    arrDataInicio = inicio.toString().split('/');
    arrDataFim    = fim.toString().split('/');

    dateInicio = new Date(arrDataInicio[1]+"/"+arrDataInicio[0]+"/"+arrDataInicio[2]);
    dateFim    = new Date(arrDataFim[1]+"/"+arrDataFim[0]+"/"+arrDataFim[2]);  

    diferenca = dateFim - dateInicio;
    qtdDias = Math.round(diferenca/(1000*60*60*24));

    return qtdDias;
}

// validação campos obrigatorio formulario
function validaForm(){
	
	jQuery('#mensagem').hide();
	
	var camposObrigatorios = new Array
					(
						".alcousuoid", ".alcovlr_inicio", ".alcovlr_fim", ".alcodupla_check"
					);

	for(i=0;i<camposObrigatorios.length;i++){
		jQuery(camposObrigatorios[i]).removeClass("erro");
	}
	
	// valida campos obrigatÃ³rios
    var erros = 0;
    
	for(i=0;i<camposObrigatorios.length;i++){
        if(jQuery(camposObrigatorios[i]).val() == "") {
            jQuery(camposObrigatorios[i]).addClass("erro");
            erros++;
        }
	}
	
	if(jQuery('#alcodupla_check').is(':checked')){
    	return validaFormSegundo();
    }else{
    	var camposObrigatorios = new Array(".alcousuoid_dupla_check", ".alcovlr_inicio_dupla_check", ".alcovlr_fim_dupla_check");

    	for(i=0;i<camposObrigatorios.length;i++){
			jQuery(camposObrigatorios[i]).removeClass("erro");
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

    

    return true;
}

// validação campos obrigatorio se alcodupla_check = true
function validaFormSegundo(){
	
	// return true; // REMOVER
	// oculta mensagens anteriores
	jQuery('#mensagem').hide();
	
	var camposObrigatorios = new Array
					(
							".alcovlr_fim", ".alcousuoid_dupla_check", ".alcovlr_inicio_dupla_check", ".alcovlr_fim_dupla_check"
					);

	for(i=0;i<camposObrigatorios.length;i++){
		jQuery(camposObrigatorios[i]).removeClass("erro");
	}
	
	// valida campos obrigatÃ³rios
    var erros = 0;
    
	for(i=0;i<camposObrigatorios.length;i++){
        if(jQuery(camposObrigatorios[i]).val() == "") {
            jQuery(camposObrigatorios[i]).addClass("erro");
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

    return true;
}