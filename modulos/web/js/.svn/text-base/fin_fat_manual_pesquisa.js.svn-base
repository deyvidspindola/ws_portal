jQuery(document).ready(function() {	

	jQuery("#chk_all").toggleChecked("input[name='chk_oid[]']");
	jQuery("#dt_ini").periodo("#dt_fim");
	
	jQuery("body").delegate('.componente_btn_pesquisar', 'click', function(){
		jQuery.fn.limpaMensagens2();
	});	  	
	
	jQuery("body").delegate('.componente_btn_limpar', 'click', function(){
		jQuery.fn.limpaMensagens2();
	});	
	
	jQuery("body").delegate('#confirmar_fatura', 'click', function(){
		jQuery.fn.limpaMensagens();
		
		if(jQuery("input[name='chk_oid[]']:checked").length == 0){
	    	jQuery('#msgalerta2').html('Ao menos uma nota fiscal deve ser marcada.').showMessage();
            return false;
        }
	   	jQuery("#acao").val('editar');
	   	jQuery('#frm_pesquisar').submit();	   	
	});	
	
    jQuery("#pesquisar").click(function(){
      	jQuery.fn.pesquisar();
    });
    
    jQuery("#cpx_pesquisa_cliente_nome_label").html( jQuery("#cpx_pesquisa_cliente_nome_label").html() + " *");
    
	jQuery('#nflno_numero').mask("9?99999999", {placeholder: ""});

});

jQuery.fn.limpaMensagens = function() {
	jQuery('.componente_nenhum_cliente').hideMessage();
	jQuery(".erro").removeClass("erro");
    jQuery('.mensagem').hideMessage();
}

jQuery.fn.limpaMensagens2 = function() {
	jQuery(".erro").removeClass("erro");
    jQuery('.mensagem').hide();
}

jQuery.fn.pesquisar = function() {

	jQuery.fn.limpaMensagens();
	
	var validacao = true;
	
	var vl1 = $('#dt_ini').val();
	var data_ini = vl1.substring(6,10)+""+vl1.substring(3,5)+""+vl1.substring(0,2);
	var vl2 = $('#dt_fim').val();
	var data_fim = vl2.substring(6,10)+""+vl2.substring(3,5)+""+vl2.substring(0,2);

	if ( (parseInt(data_fim,10) < parseInt(data_ini,10)) ) {
	jQuery('#msgalerta').html('A data inicial não pode ser maior que a data final.').showMessage();  		
	$('#dt_ini').addClass('erro');
		$('#dt_fim').addClass('erro');
		validacao = false;
	}
	if (jQuery("#nflno_numero").val() == '' && jQuery("#nflserie").val() == ''){
        if(jQuery("input[name='cpx_valor_cliente_nome']").val()==''){   		
            $("input[name='cpx_pesquisa_cliente_nome']").addClass('erro').val('');
            $("input[name='cpx_valor_cliente_cnpj']").addClass('erro').val('');
            $("input[name='cpx_valor_cliente_cpf']").addClass('erro').val('');
            validacao = false;
        }

        if ((vl1 == "" && vl2 == "") || vl1 == "" || vl2 == "") {	    		
            $('#dt_ini').addClass('erro');
            $('#dt_fim').addClass('erro');
            validacao = false;
        }

        if (vl1 == "" && vl2 != "") {	    		
            $('#dt_ini').val(vl2);
        }
        if (vl1 != "" && vl2 == "") {	    		
            $('#dt_fim').val(vl1);
        }
    } else {
       if (jQuery("#nflno_numero").val() != '' && jQuery("#nflserie").val() == '') {
           $("#nflserie").addClass('erro').focus();
           validacao = false;
       }
       if (jQuery("#nflno_numero").val() == '' && jQuery("#nflserie").val() != ''){
           $("#nflno_numero").addClass('erro').focus();
           validacao = false;
           
       }
    }
	
	if(!validacao){
		jQuery('#msgalerta').html('Existem campos obrigatórios não preenchidos.').showMessage();  
		return false;
	}

	jQuery("#acao").val('pesquisa');
	jQuery.ajax({
		url: 'fin_fat_manual.php',
		type: 'post',
		data: jQuery('#frm_pesquisar').serialize()+'&ajax=true',
	    contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
		beforeSend: function(){		
	    	jQuery.fn.limpaMensagens();
			jQuery('#frame01').html('<center><img src="images/loading.gif" alt="" /></center>');
			jQuery('#pesquisar').attr('disabled', 'disabled');
		},
		success: function(data){
			
			// Liberação do botão de pesquisa
			jQuery('#pesquisar').removeAttr('disabled');
			
			try{	
				// Transforma a string em objeto JSON
				var resultado = jQuery.parseJSON(data);
		    	jQuery('#msgerro').attr("class", "mensagem erro").html(resultado.message).showMessage();
				if(resultado.status=='errorlogin') window.location.href = resultado.redirect;
				jQuery('#frame01').html('');
				
			}catch(e){
				try{	
					// Transforma a string em objeto JSON
					jQuery('#frame01').html(data).hide().showMessage();						 				
				}catch(e){			
			    	jQuery('#msgerro').attr("class", "mensagem erro").html('Erro no processamento dos dados.').showMessage();
				}
			}
			
		}
	});	
}