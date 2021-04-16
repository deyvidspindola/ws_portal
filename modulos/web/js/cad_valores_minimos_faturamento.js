 
jQuery(document).ready(function(){
	
	function popularCampos(retorno) {
		jQuery('#vmfoid').val(retorno.vmfoid);
		jQuery('#vmfvl_acionamento').val(retorno.vmfvl_acionamento);
		moeda(document.getElementById('vmfvl_acionamento'),2);
		jQuery('#vmfqtd_min_acionamento').val(retorno.vmfqtd_min_acionamento);
		jQuery('#vmfqtd_max_acionamento').val(retorno.vmfqtd_max_acionamento);
		jQuery('#vmfvl_localizacao_web').val(retorno.vmfvl_localizacao_web);
		moeda(document.getElementById('vmfvl_localizacao_web'),2);
		jQuery('#vmfvl_localizacao_solicitada').val(retorno.vmfvl_localizacao_solicitada);
		moeda(document.getElementById('vmfvl_localizacao_solicitada'),2);
		jQuery('#vmfvl_bloqueio_solicitado').val(retorno.vmfvl_bloqueio_solicitado);
		moeda(document.getElementById('vmfvl_bloqueio_solicitado'),2);
		jQuery('#vmfvl_faturamento_minimo').val(retorno.vmfvl_faturamento_minimo);
		moeda(document.getElementById('vmfvl_faturamento_minimo'),2);
	}
	
	jQuery.ajax({
		url: 'cad_valores_minimos_faturamento.php',
		type: 'post',
		data:"acao=recuperar",
		beforeSend: function(){
			jQuery('#feedback').html('<div style="width: 100%; text-align:center;"><img src="images/loading.gif" alt="" /></div>');
		},
		error: function() {
                        jQuery('#div_msg').html('Falha ao pesquisar valores');
			jQuery('#feedback').html('');
		},
		success: function(data) {				
			resultado = jQuery.parseJSON(data);		
			jQuery('#div_msg').html(resultado.msg);
			jQuery('#feedback').html('');
			
			if (!resultado.error) {
				popularCampos(resultado.retorno);
			}
		}
	});
	
	
	$("#salvar").click(function(){
		removeAlerta();
		jQuery(".inputError").removeClass("inputError");
		
		
		if(jQuery.trim(jQuery('#vmfvl_acionamento').val()).length == 0 ||
                   jQuery.trim(jQuery('#vmfqtd_min_acionamento').val()).length == 0 ||
                   jQuery.trim(jQuery('#vmfqtd_max_acionamento').val()).length == 0 ||
                   jQuery.trim(jQuery('#vmfvl_localizacao_web').val()).length == 0 ||
                   jQuery.trim(jQuery('#vmfvl_localizacao_solicitada').val()).length == 0 || 
                   jQuery.trim(jQuery('#vmfvl_bloqueio_solicitado').val()).length == 0 || 
                   jQuery.trim(jQuery('#vmfvl_faturamento_minimo').val()).length == 0) {
			criaAlerta('Os campos assinalados com * são de preenchimento obrigatório!');
			return false;
		}
	
		if(jQuery('#vmfvl_acionamento').val() == "0,00" || 
                   jQuery('#vmfqtd_min_acionamento').val() == 0 ||
                   jQuery('#vmfqtd_max_acionamento').val() == 0 ||
                   jQuery('#vmfvl_localizacao_web').val() == "0,00" ||
                   jQuery('#vmfvl_localizacao_solicitada').val() == "0,00" || 
                   jQuery('#vmfvl_bloqueio_solicitado').val() == "0,00" || 
                   jQuery('#vmfvl_faturamento_minimo').val() == "0,00") {
			criaAlerta('A informação deve ser maior que zero!');
			return false;
		}
		
		if (parseInt(jQuery('#vmfqtd_max_acionamento').val()) < parseInt(jQuery('#vmfqtd_min_acionamento').val())) {
			criaAlerta("A quantidade mínima para faturamento não pode ser maior que a quantidade máxima para faturamento!"); 
			jQuery("#vmfqtd_max_acionamento").addClass("inputError");
			return false;	
		}
		
		
		
		
		jQuery.ajax({
			url: 'cad_valores_minimos_faturamento.php',
			type: 'post',
			data:$("#frm").serialize()+"&acao=salvar",
			beforeSend: function(){
				jQuery('#feedback').html('<div style="width: 100%; text-align:center;"><img src="images/loading.gif" alt="" /></div>');
			},
			error: function() {
                                jQuery('#div_msg').html('Falha ao salvar valores');
                                jQuery('#feedback').html('');
			},
			success: function(data) {
				resultado = jQuery.parseJSON(data);
				jQuery('#div_msg').html(resultado.msg); 
				jQuery('#feedback').html('');
				
				if (!resultado.error) {
					popularCampos(resultado.retorno);

				}
			}
		});
	});	
	
});
