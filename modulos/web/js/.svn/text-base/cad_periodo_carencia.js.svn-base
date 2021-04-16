jQuery(document).ready(function() {

	jQuery('#periodo_vigencia').mask("9?99");

	jQuery('#fim_carencia_email').mask("9?99");

	jQuery('#bt_limpar_reativacao').click(function() {
		jQuery('#periodo_vigencia').val("");
		jQuery('#data_vigencia').val("");
	});
	
	jQuery('#data_vigencia').blur(function() {
		if (jQuery(this).val() != "") {
			jQuery(this).removeClass('inputError');
		} else {
			jQuery(this).addClass('inputError');
		}
	});
	
	jQuery('#bt_confirmar_modelo').click(function(){
		var valido = true;
		var carencia = jQuery('#fim_carencia_email').val();
		var assunto = jQuery('#assunto_email').val();
		var modelo = jQuery('#modelo_email').val();
		jQuery('.inputError').removeClass('inputError');
		removeAlerta();
		if (carencia == "") {
			jQuery('#fim_carencia_email').addClass('inputError');
			valido = false;
		} if (assunto == "") {
			jQuery('#assunto_email').addClass('inputError');
			valido = false;
		} if (modelo == "") {
			jQuery('#modelo_email').addClass('inputError');
			valido = false;
		} if (valido) {
	        jQuery("#acao").val('manutencaoModelosEmailSalvar');
	        jQuery("#frm_manutencao_modelos_email").submit();
		} else {
			criaAlerta("Há campos obrigatórios não preenchidos.");
			return false;
		}
	});

	jQuery("#bt_confirmar_reativacao").click(function(){
		removeAlerta();
		var res = validarFormReativacao();
    	if (res) {
	        jQuery("#acao").val('salvarParametrosReativacao');
	        jQuery("#frm_reativacao_cobranca").submit();
    	} else {
    		criaAlerta('Há campos obrigatórios não preenchidos.');
    		return false;
    	}
    });

    function validarFormReativacao() {
    	jQuery('.inputError').removeClass('inputError');
		removeAlerta();
    	var retorno = true;
    	if (jQuery("#data_vigencia").val() == "") {
    		jQuery("#data_vigencia").addClass("inputError");
    		retorno = false;
    	} if (jQuery("#periodo_vigencia").val() == "") {
    		jQuery("#periodo_vigencia").addClass("inputError");
    		retorno = false;
    	} if (retorno) {
    		var params = { periodo : jQuery("#periodo_vigencia").val() };
    		
    		jQuery.ajax({ 
    				type : "POST", 
    				url: 'cad_periodo_carencia.php?acao=verificarEmailsPeriodo', 
    				data : params,
    				async: false
    				}).success(function(data) {
    					if (data == "1") {
    	    				if (confirm("Há modelos de email com períodos superiores ao período informado, se você confirmar esse período, os modelos serão excluídos. Confirma?")) {
    	    					retorno = true;
    	    				} else {
    	    					retorno = false;
    	    				}
    	    			} else {
    	    				retorno = true;
    	    			}
    		});
    	}
    	return retorno;
    }    

    function carregarEmailPorCarencia(carenciaV) {
    	jQuery('.msg').text("");
    	removeAlerta();
    	var params = { carencia: carenciaV };
    	jQuery.post('cad_periodo_carencia.php?acao=manutencaoModelosBuscarCarencia', params, function(data) {
    		if (!(parseInt(carenciaV) > parseInt(data.maximo))) {
				if (data.erro == "0") {
					jQuery('#idModelo').val(data.id);
					jQuery('#fim_carencia_email').val(carenciaV);
					jQuery('#assunto_email').val(data.assunto);
					jQuery('#modelo_email').val(data.mensagem);
				} else {
					jQuery('#idModelo').val(0);
				}
			} else {
				removeAlerta();
				criaAlerta("A quantidade de dias para fim da carência deve ser menor ou igual a "+data.maximo+".");
				jQuery('#fim_carencia_email').focus();
			}
		}, 'json');
    }

    jQuery('#bt_limpar_modelo').click(function(){
    	jQuery('.msg').text("");
    	removeAlerta();
    	jQuery('#idModelo').val('');
    	jQuery('#fim_carencia_email').val('');
    	jQuery('#fim_carencia_email').removeClass('inputError');
    	jQuery('#assunto_email').val('');
    	jQuery('#assunto_email').removeClass('inputError');
    	jQuery('#modelo_email').val('');
    	jQuery('#modelo_email').removeClass('inputError');
    });
    
    jQuery('#fim_carencia_email').blur(function(){
    	carregarEmailPorCarencia(jQuery(this).val());
	});

    jQuery(".link_editar_email").click(function(){
    	carregarEmailPorCarencia(jQuery(this).attr("href"));
    	return false;
    });

    jQuery(".link_excluir_email").click(function(){
    	if(confirm('Tem certeza que deseja excluir esse modelo de e-mail?')) {
	    	var email = { id: jQuery(this).attr("href") };
	    	jQuery.post('cad_periodo_carencia.php?acao=manutencaoModelosEmailExcluir', email, function(data) {
	    		if (data == "1") {
	    			document.location.href='cad_periodo_carencia.php?acao=manutencaoModelosEmail';
	    			jQuery('#excluirEmail').val('true');
    			}
	    	}, 'json');
    	}
    	return false;
    });
});