/**
 * @author	Paulo Henrique
 * @email 	paulohenrique@brq.com
 * @since	06/08/2013
 */



	function validacaoJquery (elemento) {	
		
		var obrigatorio_todos = false;
		var thisFieldset = false;
		jQuery.each(jQuery('.conteudo:visible'), function() {
			obrigatorio_todos = false;
			jQuery.each(jQuery(this).find('.obrigatorio_todos:enabled:visible'), function() {
				var thisFieldset = jQuery(this).closest('.conteudo');
				if (jQuery.trim(jQuery(this).val()) != '')
				{
					jQuery(thisFieldset).find('.obrigatorio_todos').addClass('obrigatorio');
					obrigatorio_todos = true;
				} else {
					if (obrigatorio_todos == false)
					jQuery(thisFieldset).find('.obrigatorio_todos').removeClass('obrigatorio');
				}
			});
		});
		
		var obrigatorio_ou = false;
		var thisFieldset = false;
		var itensVazios = 0;
		var itensOu = 0;
		jQuery.each(jQuery('.camposObrigatoriosOU'), function() {
					
			itensOu = jQuery(this).find('.obrigatorio_ou:enabled:visible').length;
			
			jQuery.each(jQuery(this).find('.obrigatorio_ou:enabled:visible'), function() {
				
				if (jQuery.trim(jQuery(this).val()) == ''){
					itensVazios += 1;
				} 

			});
		});

		if(itensVazios == itensOu){
			jQuery('.camposObrigatoriosOU').find('.obrigatorio_ou').addClass('obrigatorio');
			obrigatorio_ou = true;
		}else{
			jQuery('.camposObrigatoriosOU').find('.obrigatorio_ou').removeClass('obrigatorio');
			jQuery('.camposObrigatoriosOU').find('.obrigatorio_ou').removeClass('erro');
			obrigatorio_ou = false;	
		}
		

		if (jQuery(elemento).hasClass('valida_invisivel') == true)
		{
			camposObrigatorios = jQuery('.obrigatorio:enabled');
		} else {
			camposObrigatorios = jQuery('.obrigatorio:enabled:visible');
		}


		var erro = false;

		boxPrimeiroErro = '';


		jQuery.each(camposObrigatorios, function() {
			if (jQuery.trim(jQuery(this).val()) == '')
			{
				if (boxPrimeiroErro == '') {
					boxPrimeiroErro = jQuery(this).closest('.conteudo_validacao');
				}
				// console.log(jQuery(this).attr('id'));
				jQuery(this).addClass('erro');
				erro = true;
			} else {
				jQuery(this).removeClass('erro');
			}

		});


		var filtro = /^\d{1,3}[.]\d{1,3}[.]\d{1,3}[.]\d{1,3}$/;
		jQuery.each(jQuery('.valida_ip'), function() {
			if(jQuery.trim(jQuery(this).val()) != '')
			{
                if(!filtro.test(jQuery(this).val()))
                {
                	if (boxPrimeiroErro == '') {
						boxPrimeiroErro = jQuery(this).closest('.conteudo_validacao');
					}
					jQuery(this).addClass('erro');
					erro = true;
                } else {
					jQuery(this).removeClass('erro');
                }
            }  else if (obrigatorio_todos == false) {
				jQuery(this).removeClass('erro');
            }
		});

		var filtro = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
		jQuery.each(jQuery('.valida_email'), function() {
			if(jQuery.trim(jQuery(this).val()) != '')
			{
                if(!filtro.test(jQuery(this).val()))
                {
                	if (boxPrimeiroErro == '') {
						boxPrimeiroErro = jQuery(this).closest('.conteudo_validacao');
					}
					jQuery(this).addClass('erro');
					erro = true;
                } else {
					jQuery(this).removeClass('erro');
                }
            }  else if (jQuery(this).hasClass('obrigatorio') == false){
				jQuery(this).removeClass('erro');
            }
		});

		var filtro = /^[0-9]{8}$/;
		jQuery.each(jQuery('.valida_cep'), function() {
			if(jQuery.trim(jQuery(this).val()) != '')
			{
                if(!filtro.test(jQuery(this).val()))
                {
                	if (boxPrimeiroErro == '') {
						boxPrimeiroErro = jQuery(this).closest('.conteudo_validacao');
					}
					jQuery(this).addClass('erro');
					erro = true;
                } else {
					jQuery(this).removeClass('erro');
                }
            }  else if (jQuery(this).hasClass('obrigatorio') == false){
				jQuery(this).removeClass('erro');
            }
		});


		if (erro == true) {
	        
			//Somente os inputs com a classe erro, visiveis e habilitados
			
			
			var erroOcorrencia = jQuery('.erro:visible:enabled');

			if (jQuery(boxPrimeiroErro).is(':visible') == false && jQuery(elemento).hasClass('valida_invisivel') == true)
			{
				// jQuery('.conteudo_validacao').fadeOut('fast', function () {
				// 	jQuery(boxPrimeiroErro).fadeIn('fast');
				// })
	    		var id = jQuery(boxPrimeiroErro).attr('id');
	    		jQuery('.link_box[href="#'+id+'"]').trigger('click');
	    		//Se é para validar os invisiveis, então pegamos todos os inputs erros habilitados
	    		var erroOcorrencia = jQuery('#'+id+' .erro:enabled');
			}



			var labelErros = '';
			var origemErro = '';
			var labelErrosOU = '';
			jQuery.each(erroOcorrencia, function() {

				var nameCampo = jQuery(this).attr('name');
				
				if (nameCampo != '')
				{
					thisLabel = jQuery(this).parent().children('label').text().replace('*:', '');
					// alert(thisLabel);
					if (thisLabel != '') {

						if (jQuery(this).hasClass('valida_email') == true || jQuery(this).hasClass('valida_cep') == true || jQuery(this).hasClass('valida_ip') == true)
						{
							labelErros+= 'Campo '+thisLabel+' inválido ou está sem preencher.<br>';	
							// labelErros+= origemErro+'Campo '+thisLabel+' inválido.<br>';								
						} else {
							labelErros+= 'Campo '+thisLabel+' é obrigatório<br>';		
							// labelErros+= origemErro+'Campo '+thisLabel+' é obrigatório<br>';		
						}
							
					}else{
						labelErrosOU += jQuery('label[for="'+nameCampo+'"]').text().replace(':', ', ');
					}
				}
			});
			
			if (labelErrosOU != ''){
				labelErros += 'É necessário preencher ao menos um dos campos: '+labelErrosOU.slice(0, -3)+'.<br>';
			}
			
//			if (labelErros == '')
//			{
				//jQuery('#mensagem').html("Existem campos obrigatórios não preenchidos.");
//			} else {
				jQuery('#mensagem').html(labelErros);
//			}
			
//			jQuery('#mensagem').addClass("alerta");
//			jQuery('#mensagem').html("Existem campos obrigatórios não preenchidos.");
//	    	jQuery('#mensagem').show();
//	        return false;
			jQuery('#mensagem').removeClass("sucesso").addClass("alerta").show();
			
			jQuery(document).scrollTop(jQuery("#mensagem").offset().top );
			
			// se deu erro nao executa o submit
			return false;
		}	
		return true;
	}

jQuery(function() {
    jQuery('body').delegate('.validacao', 'click', function(){
		jQuery('#mensagem').hide();

	    jQuery.each(jQuery('input[type=text]'), function(){
		    // var value = jQuery(this).val();
		    // jQuery(this).val(jQuery.trim(value));
		    jQuery(this).val(jQuery.trim(jQuery(this).val()));
	  	});


		if (!validacaoJquery(jQuery(this)))
			return false;
		// se passou nas validações executa o submit
		jQuery(this).closest('form').submit();
        
    });



});