function envioForm() {
	jQuery.ajax({
        url: 'rel_insucesso_discador.php',
        type: 'post',
        data: jQuery('#form').serialize(),
        beforeSend: function(){

        	$('#div_msg').html("");
        	$('#loading').show();
            $('#loading').html('<img src="images/loading.gif" alt="" />');              
                
        },
        success: function(data){
            try{
            	//console.log(data);
                 var retorno = jQuery.parseJSON(data);                
    			
                if (retorno.msg != '' || retorno.erro == 1) {
                	jQuery('#div_msg').html('<b>'+retorno.msg+'</b>');
                	return;
                }

            }catch(e){	                
                jQuery('#div_msg').html(e.message);
            }
        },
        complete: function(){
        	jQuery('#loading').html('');	        	
        }
	});	
}

$(document).ready(function(){
	
	// Click no botão pesquisar 
	$("#bt_pesquisar").click(function(){
		
		removeAlerta();
		
		$('#div_msg').html("");
				
		if (!($("#ligddt_ligacao_ini").val()) || !($("#ligddt_ligacao_fin").val())){
			criaAlerta("Informe o período para realizar a pesquisa."); 
			$("#ligddt_ligacao_ini").addClass("inputError");
			$("#ligddt_ligacao_fin").addClass("inputError");
			return false;
		}
		
		if (diferencaEntreDatas($("#ligddt_ligacao_fin").val(), $("#ligddt_ligacao_ini").val()) < 0){
			criaAlerta("Período informado inválido."); 
			$("#ligddt_ligacao_ini").addClass("inputError");
			$("#ligddt_ligacao_fin").addClass("inputError");
			return false;
		}
		
		if ($("#tipo_proposta").val() == ''){
			criaAlerta("Informe o tipo de proposta para realizar a pesquisa."); 
			$("#tipo_proposta").addClass("inputError");
			$("#tipo_proposta").addClass("inputError");
			return false;
		}
				
		$('#loading').html('<img src="images/loading.gif" alt="" />');
		
		$("#acao").val("pesquisar");
		$("#form").submit();
		
	});

	
	// Click no botão enviar e-mail 
	jQuery('body').delegate('#bt_enviar_email', 'click', function(){
		
		removeAlerta();
		// Validação do formulário 		
		if ($("input[name='ligdoid[]']:checked").length==0){
			// Valida se possui algum checkbox marcado 
			criaAlerta("Por favor selecione um registro."); 
			return false;
		}
		$("#acao").val("enviar_email");
		
		envioForm();		
	
	
	});
	
	/* --------------------------------------------------------------------------------------------------- */	
	
	// Click no botão enviar sms 
	jQuery('body').delegate('#bt_enviar_sms', 'click', function(){
		
		removeAlerta();
		// Validação do formulário 		
		if ($("input[name='ligdoid[]']:checked").length==0){
			// Valida se possui algum checkbox marcado 
			criaAlerta("Por favor selecione um registro."); 
			return false;
		}		
		if($("#mensagem").val().length > 120){
			// Valida se o campo mensagem não possui mais do que 120 caracteres 
			criaAlerta("Mensagem a enviar excedeu 120 caracteres."); 
			$("#mensagem").addClass("inputError");
			$("#mensagem").focus();
			return false;
		}		
		$("#acao").val("enviar_sms");
		
		envioForm();
		
	});

	/* --------------------------------------------------------------------------------------------------- */	
	
	// Marca todos os checkbox
	jQuery('body').delegate('#marca_todos', 'click', function(){
		$("input[name='ligdoid[]']").each(function(){
			$(this).attr("checked", true);
		});
		return false;
	});

	/* --------------------------------------------------------------------------------------------------- */	
	
	// Desmarca todos os checkbox
	jQuery('body').delegate('#desmarca_todos', 'click', function(){
		$("input[name='ligdoid[]']").each(function(){
			$(this).attr("checked", false);
		});
		return false;
	});

	/* --------------------------------------------------------------------------------------------------- */	
	
	//jQuery('body').delegate("input[type='checkbox']", 'click', function(){
	//	alert($(this).val());
	//});
	
	jQuery('body').delegate("#mensagem", "keypress", function(){
		if($(this).val().length - 1 >= 999) {  
			$(this).val($(this).val().substring($(this).val().length - 1 - 999, $(this).val().length - 1));
		}
	});
	
	var idTipoProposta = jQuery('#tipo_proposta').val();
	
	if(idTipoProposta != ''){
		
		var ajax = true;
		
		if ($("#loading_tipo").length) {
			jQuery('#loading_tipo').show()
		} else {
			jQuery('#sub_tipo_proposta').after('<img src="images/progress4.gif" id="loading_tipo" />');
		}
		
		jQuery.ajax({
		
			url : 'rel_insucesso_discador.php',
			type : 'post',
			data : 'acao=getSubTipoProposta&idTipoProsposta=' + idTipoProposta + '&ajax=' + ajax,
			success : function(data) {
				
				var resultado = jQuery.parseJSON(data);
				
				if(resultado != ''){
	
					jQuery(".sub_tipo_pro").show();
					jQuery("#sub_tipo_proposta").html("");
					jQuery("#sub_tipo_proposta").append(jQuery('<option>Todos</option>').attr("value",''));
					
					jQuery.each(resultado,function(i, res) {
						jQuery("#sub_tipo_proposta").append(jQuery('<option></option>').attr("value", res.tppoid).text(res.tppdescricao));
					});
					
					jQuery("#sub_tipo_proposta").val(jQuery("#sub_tipo_pro_select").val()); 
	
				}else{
					jQuery("#sub_tipo_proposta").val("");
					jQuery("#sub_tipo_proposta").html('<option value="">Todos</option>');
				}

				jQuery('#loading_tipo').hide();
			}
		});
	}
	
	
	jQuery('#tipo_proposta').change( function(){
		
		jQuery(".sub_tipo_pro").hide();
		
		var idTipoProposta = jQuery('#tipo_proposta').val();
		
		if(idTipoProposta != ''){
		
			var ajax = true;
			
			if ($("#loading_tipo").length) {
				jQuery('#loading_tipo').show()
			} else {
				jQuery('#sub_tipo_proposta').after('<img src="images/progress4.gif" id="loading_tipo" />');
			}
			
			jQuery.ajax({
			
				url : 'rel_insucesso_discador.php',
				type : 'post',
				data : 'acao=getSubTipoProposta&idTipoProsposta=' + idTipoProposta + '&ajax=' + ajax,
				success : function(data) {
					
					var resultado = jQuery.parseJSON(data);
					
					if(resultado != ''){
	
						jQuery("#sub_tipo_proposta").html("");
						jQuery("#sub_tipo_proposta").append(jQuery('<option>Todos</option>').attr("value",''));
						
						jQuery(".sub_tipo_pro").show();
						
						jQuery.each(resultado,function(i, res) {
							jQuery("#sub_tipo_proposta").append(jQuery('<option></option>').attr("value", res.tppoid).text(res.tppdescricao));
						});
	
					}else{
						jQuery("#sub_tipo_proposta").val("");
						jQuery(".sub_tipo_pro").hide();
					}
	
					jQuery('#loading_tipo').hide();
				}
			});
		}
	});
	
});