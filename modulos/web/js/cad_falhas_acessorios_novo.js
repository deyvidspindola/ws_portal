jQuery(document).ready(function(){
     
	//aplica ao checkbox com id chk_all selecionar todos os inputs com nome chk_codigo[]
	jQuery("#chk_all").toggleChecked("input[name='chk_codigo[]']");
	
	jQuery("body").delegate( "#chk_all" , 'click', function(){		
		var isCheckedAll = jQuery(this).is(':checked');		
		if(isCheckedAll){
			jQuery('#btn_excluir').removeAttr('disabled');
		}else{
			jQuery('#btn_excluir').attr('disabled', 'disabled');
		}
		
	});

	jQuery("body").delegate("input[name='chk_codigo[]']", 'click', function(){
		var isCheckedOne = false;		
		jQuery("input[name='chk_codigo[]']").each(function(){			
			var isDisabled = jQuery(this).attr('disabled');
			if (isDisabled != 'disabled') {
				isChecked = jQuery(this).is(':checked');				
				if (isChecked) {
					isCheckedOne = true;
				}
			}
		});		
		if(isCheckedOne){
			jQuery('#btn_excluir').removeAttr('disabled');
		}else{
			jQuery('#btn_excluir').attr('disabled', 'disabled');
		}
	});	
	
    jQuery("#btn_pesquisar").click(function(){
    	jQuery.fn.limpaMensagens();
        var status = validarCampos();
        if(status) {
	        jQuery("#acao").val("pesquisar");
	        jQuery("#frm").submit();
        } else {
        	 if ( jQuery("#tipo").val() != "" && jQuery("#serial").val() == "" ){
            	 jQuery('#msg_alerta').html('Inserir o número de serial desejado.').showMessage();
            	 return;
             }
        	 jQuery('#msg_alerta').html('Existem campos obrigatórios não preenchidos.').showMessage();
        }
        return status;
    });
    
    jQuery("#btn_novo").click(function(){
    	jQuery.fn.limpaMensagens();
      	var status = validarCampos();
      	if (status) {
	        jQuery("#acao").val("salvar");
	        jQuery("#frm").submit();   		      		
      	} else {
	       	 if ( jQuery("#tipo").val() != "" && jQuery("#serial").val() == "" ){
	        	 jQuery('#msg_alerta').html('Inserir o número de serial desejado.').showMessage();
	        	 return;
	         }
      		jQuery('#msg_alerta').html('Existem campos obrigatórios não preenchidos.').showMessage();
      	}
      	return status;
    });
    
    jQuery("#btn_excluir").click(function() {
    	jQuery.fn.limpaMensagens();
    	
    	if(jQuery("input[name='chk_codigo[]']:checked").length == 0){
	    	jQuery('#msg_alerta2').html('Itens não selecionados.').showMessage();
            return false;
        }
    	
    	if (confirm('Deseja realmente excluir o item?')) {
            jQuery('#frm_action input[name="acao"]').val('excluir');
	        jQuery("#frm_action").submit();
    	} else {
    		return false;
    	}
    });
    
    jQuery(".linkEditar").click(function() {
    	jQuery.fn.limpaMensagens();
    	var valor = jQuery(this).attr('id');
    	jQuery("#hdn_cfaoid2").val(valor);
    	
    	jQuery('#frm_action input[name="acao"]').val('salvar');
        jQuery("#frm_action").submit();
    });
    
    function validarCampos() {
    	var status = true;
    	resetFormErros();

        if ( jQuery("#tipo").val() == "" ){
    		jQuery("#tipo").addClass('erro');
    		jQuery("#tipo").parent().children('label').addClass('erro');
    		status = false;
        }
        
        if ( jQuery("#serial").val() == "" ){
    		
    		jQuery("#serial").addClass('erro');
    		jQuery("#serial").parent().children('label').addClass('erro');
    		status = false;
        }
        
        return status;
    }
     
    jQuery("body").delegate('#btn_gravar', 'click', function(){
    	jQuery.fn.gravar();
	});
    
});

jQuery.fn.gravar = function(){
	jQuery.fn.limpaMensagens();
	
	if(jQuery.fn.validarGravar()) {
        jQuery('#frm_action input[name="acao"]').val('gravar');
		jQuery.ajax({
            url : 'cad_controle_falhas_acessorios_novo.php',
            type: 'POST',
            data: jQuery('#frm_action').serialize(),
            beforeSend: function(){
            	jQuery('#btn_gravar').attr('disabled', 'disabled').hide();
            	jQuery('#loading_gravar').show();
            	jQuery.fn.limpaMensagens();            
            },
            success: function(response){ 
                var data = jQuery.parseJSON(response);                
                if(data.status) {      
                	jQuery('#conteudo_listagem_insert').hide()
                    
                    var data = jQuery.parseJSON(response);
                        
                    var tr = ' <tr> ';                         
                    	tr +='     <td>'+data.dados.imobserial+'</td>';                         
                    	tr +='     <td>'+data.dados.prdproduto+'</td>';                         
                    	tr +='     <td>'+data.dados.cfadt_entrada+'</td>';                         
                    	tr +='	   <td>'+data.dados.ifddescricao+'</td>';                         
                    	tr +='     <td>'+data.dados.ifadescricao+'</td>';                         
                    	tr +='     <td>'+data.dados.ifcdescricao+'</td>';                         
                    	tr +=' </tr>';
                    
                    jQuery('#conteudo_listagem2').append(tr);    			
    				jQuery('#conteudo_listagem2 tr').removeClass('par');
                    jQuery('#conteudo_listagem2 tr:even').addClass('par');

                	jQuery('#loading_gravar').hide();
                    jQuery('#btn_gravar').removeAttr('disabled').show();
                    
                    if(jQuery('#hdn_cfaoid2').val()=="" && confirm('Deseja acrescentar mais algum registro?')) {
                    	jQuery('#conteudo_listagem_insert').show();
                    }
                    else {
                    	jQuery('#loading_gravar').show();
                    	jQuery('#btn_gravar').hide();
            	        jQuery("#frm #acao").val("pesquisar");
            	        jQuery("#frm").submit();
            	        return;
                    }
                    
                    jQuery("#defeito_lab").val("");
                    jQuery("#acao_lab").val("");
                    jQuery("#componente_lab").val("");
                	jQuery('#loading_gravar').hide();                    
                } else {
                    jQuery("#msg_"+data.tipoErro+"3").html(data.mensagem).showMessage();
                    jQuery('#loading_gravar').hide();
                }  
                
                jQuery('#btn_gravar').removeAttr('disabled').show();
            },
            complete: function() {}
        });			
		
	}
	return false;
}

//validar combos da gravação
jQuery.fn.validarGravar = function () {
	var status = true;	
	jQuery.fn.limpaMensagens();

    if ( jQuery("#defeito_lab").val() == "" ){
		jQuery("#defeito_lab").addClass('erro');
		status = false;
    }
    if ( jQuery("#acao_lab").val() == "" ){
		jQuery("#acao_lab").addClass('erro');
		status = false;
    }
    if ( jQuery("#componente_lab").val() == "" ){
		jQuery("#componente_lab").addClass('erro');
		status = false;
    }

	if(!status){
		jQuery('#msg_alerta3').html('Existem campos obrigatórios não preenchidos.').showMessage();
	}
	
    return status;
}

jQuery.fn.limpaMensagens = function() {
    jQuery('.mensagem').not('#msginfo').hideMessage();
	jQuery(".erro").not('.mensagem').removeClass("erro");
}
