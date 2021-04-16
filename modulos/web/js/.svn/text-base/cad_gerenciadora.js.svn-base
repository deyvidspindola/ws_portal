jQuery(document).ready(function(){

	var msg_campos_obrigatorios = 'Existem campos obrigatórios não preenchidos.';
	var msg_erro                = "Houve um erro no processamento dos dados.";
	var text_color_red_ini 	    = "<font color=\"red\">";
	var text_color_red_end 	    = "<\/font>";
	
	$('div_form_endpoint').ready( function() {	
			event.preventDefault();
			$("#bt_gravar").attr("disabled", true);
			jQuery("#endpoint_acao").val('index');
			var post_url = $('#endpoint_url_functions').val();
			var post_data = $('#endpoint_form').serialize();
			$.ajax({
				type: 'POST',
				url: post_url, 
				data: post_data,
				success: function(json_data){
									
					try{						
						var data_array = $.parseJSON(json_data);					
						
						if (data_array == null) {
							data_array = [];
						}
						
						jQuery("#mensagem_firewall").html(data_array['mensagemFirewall'] === undefined? '' : data_array['mensagemFirewall']);
						jQuery("#mensagem_aviso").html(data_array['mensagemAviso'] === undefined? '' : data_array['mensagemAviso']);
						jQuery("#mensagem_alerta").html(data_array['mensagemAlerta'] === undefined? '' : text_color_red_ini.concat(data_array['mensagemAlerta'],text_color_red_end));
						jQuery("#ger_endpoint_protocolo").val(data_array['endpointProtocolo'] === undefined? 'nenhum' : data_array['endpointProtocolo']);
						jQuery("#ger_endpoint_ip").val(data_array['endpointIp'] === undefined? '' : data_array['endpointIp']);
						jQuery("#ger_endpoint_porta").val(data_array['endpointPorta'] === undefined? '8080' : data_array['endpointPorta']);
						jQuery("#endpoint_acao").val(data_array['endpointAcao'] === undefined? 'index' : data_array['endpointAcao']);
						jQuery("#endpoint_id").val(data_array['endpointId'] === undefined? '' : data_array['endpointId']);
						
						if(jQuery("#ger_endpoint_protocolo").val() == 'nenhum') {
							jQuery("#tr_ger_endpoint_ip").hide();
							jQuery("#tr_ger_endpoint_porta").hide();
						} else {
							jQuery("#tr_ger_endpoint_ip").show();
							jQuery("#tr_ger_endpoint_porta").show();			
						}
						
						if(jQuery("#mensagem_firewall").html() != "" 
							&& (jQuery("#mensagem_aviso").html() != "" 
							|| jQuery("#mensagem_alerta").html() != text_color_red_ini.concat("",text_color_red_end) )) {
							jQuery("#tr_mensagem_firewall_espaco").show();
						} else {
							jQuery("#tr_mensagem_firewall_espaco").hide();
						}
					} catch (err) {
						jQuery("#mensagem_aviso").html(err.message);
						if(json_data.includes('Warning') || json_data.includes('Fatal error')) {
							jQuery("#mensagem_alerta").html(json_data);
						}
					}
					
					
				},
				error: function() {
					jQuery("#ger_endpoint_protocolo").val('nenhum');
					jQuery("#tr_ger_endpoint_ip").hide();
					jQuery("#tr_ger_endpoint_porta").hide();
					jQuery("#mensagem_alerta").html("Ocorreu um erro interno. Pressione F5 para tentar recarregar os dados ou consulte o administrador do sistema.");
					alert("Ocorreu um erro ao carregar os dados.");
				}
			});
			$("#bt_gravar").attr("disabled", false);
			
	});
	

	// Esconde campos se o protocolo for Nenhum
    jQuery("#ger_endpoint_protocolo").on("change", function() {	
		if(jQuery("#ger_endpoint_protocolo").val() == 'nenhum') {
			jQuery("#tr_ger_endpoint_ip").hide();
			jQuery("#tr_ger_endpoint_porta").hide();
		} else {
			jQuery("#tr_ger_endpoint_ip").show();
			jQuery("#tr_ger_endpoint_porta").show();			
		}
    });

	//Tratamento somente numeros inteiros para o campo porta
    jQuery('#ger_endpoint_porta').on('keyup blur', function() {
		var port = jQuery(this).val().replace(/[^0-9]/g, '');
		if(parseInt(port) > 65535){
			port = parseInt(port / 10);
		} 		
        jQuery(this).val(port);
    });
	
	//Tratamento formato IP
    jQuery('#ger_endpoint_ip').on('keyup blur', function() {
		var ip = jQuery(this).val().replace(/[^0-9.]/g, '');
		var ip_fields_array = ip.split(".");	
		var array_length = ip_fields_array.length;
		var ip_final = "";
		for (var i = 0; i < array_length && i<4; i++) {
			if(parseInt(ip_fields_array[i]) > 255) {
				ip_fields_array[i] = parseInt(ip_fields_array[i] / 10).toString()	;
			} 
			if(i>0 && i< array_length){
				ip_final = ip_final.concat("." ,ip_fields_array[i].substring(0,3));
			} else {
				ip_final = ip_final.concat(ip_fields_array[i]);
			}			
		}
			
        jQuery(this).val(ip_final);
    });

	//Botão confirmar
	jQuery("#bt_gravar").click(function(){ 
		$("#bt_gravar").attr("disabled", true);
	    isCamposValidos = jQuery.fn.validarCamposObrigatorios();
	    
	    if( isCamposValidos ) {
			event.preventDefault();
			var post_url = $('#endpoint_url_functions').val()
			var post_data = $('#endpoint_form').serialize();
			$.ajax({
				type: 'POST',
				url: post_url, 
				data: post_data,
				success: function(json_data){
					try {					
						var data_array = $.parseJSON(json_data);
						if (data_array == null) {
							data_array = [];
						}
						jQuery("#mensagem_firewall").html(data_array['mensagemFirewall'] === undefined? jQuery("#mensagem_firewall").html() : data_array['mensagemFirewall']);
						jQuery("#mensagem_aviso").html(data_array['mensagemAviso'] === undefined? jQuery("#mensagem_aviso").html() : data_array['mensagemAviso']);
						jQuery("#mensagem_alerta").html(data_array['mensagemAlerta'] === undefined? jQuery("#mensagem_alerta").html() : text_color_red_ini.concat(data_array['mensagemAlerta'],text_color_red_end));
						jQuery("#ger_endpoint_protocolo").val(data_array['endpointProtocolo'] === undefined? jQuery("#ger_endpoint_protocolo").val() : data_array['endpointProtocolo']);
						jQuery("#ger_endpoint_ip").val(data_array['endpointIp'] === undefined? jQuery("#ger_endpoint_ip").val() : data_array['endpointIp']);
						jQuery("#ger_endpoint_porta").val(data_array['endpointPorta'] === undefined? jQuery("#ger_endpoint_porta").val() : data_array['endpointPorta']);
						jQuery("#endpoint_acao").val(data_array['endpointAcao'] === undefined? jQuery("#endpoint_acao").val() : data_array['endpointAcao']);
						jQuery("#endpoint_id").val(data_array['endpointId'] === undefined? jQuery("#endpoint_id").val() : data_array['endpointId']);					
						
						if(jQuery("#mensagem_firewall").html() != "" 
							&& (jQuery("#mensagem_aviso").html() != "" 
							|| jQuery("#mensagem_alerta").html() != text_color_red_ini.concat("",text_color_red_end) )) {
							jQuery("#tr_mensagem_firewall_espaco").show();
						} else {
							jQuery("#tr_mensagem_firewall_espaco").hide();
						}
					} catch (err) {
						jQuery("#mensagem_aviso").html(err.message);
						if(json_data.includes('Warning') || json_data.includes('Fatal error')) {
							jQuery("#mensagem_alerta").html(json_data);
						}
					}
				},
				error: function() {
					jQuery("#mensagem_alerta").html("Ocorreu um erro interno. Pressione F5 para tentar recarregar os dados ou consulte o administrador do sistema.");
					alert("Ocorreu um erro ao enviar os dados.");
				}
			});
	    } else {
			jQuery("#mensagem_aviso").html('');
			jQuery("#mensagem_alerta").html(text_color_red_ini.concat(msg_campos_obrigatorios,text_color_red_end));	
		}
		
		$("#bt_gravar").attr("disabled", false);
	});
	
	//Oculta msgs
	jQuery.fn.limpaMensagens = function() {
		jQuery(".msgError").removeClass("msgError");
	};

	//Valida campos obrigatórios ao clicar no botão confirmar
	jQuery.fn.validarCamposObrigatorios = function() {

		jQuery.fn.limpaMensagens();
		
		var validacao = true;

		if (jQuery("#ger_endpoint_protocolo").val() != 'nenhum'){	
		
			if (jQuery("#ger_endpoint_ip").val() == ''){
				
				$('#lbl_ger_endpoint_ip').addClass('msgError');
				validacao = false;
			}

			if (jQuery("#ger_endpoint_porta").val() == ''){
				
				$('#lbl_ger_endpoint_porta').addClass('msgError');
				validacao = false;
			}
		}

		return validacao;
	}

});