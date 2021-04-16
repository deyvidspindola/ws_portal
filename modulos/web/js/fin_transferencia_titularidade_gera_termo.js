jQuery(document).ready(function(){
	
	
	
	jQuery("body").delegate('#prtcontratante','blur',function(){
		$('#prtcontratante').removeClass("erro");
	});

	jQuery("body").delegate('#prtrg','blur',function(){
		$('#prtrg').removeClass("erro");
	});
	
	jQuery("body").delegate('#prtrgorgaoemissor','blur',function(){
		$('#prtrgorgaoemissor').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpemi_dt','blur',function(){
		$('#prpemi_dt').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpnas_dt','blur',function(){
		$('#prpnas_dt').removeClass("erro");
	});
	
	jQuery("body").delegate('#prtfiliacaopai','blur',function(){
		$('#prtfiliacaopai').removeClass("erro");
	});
	
	jQuery("body").delegate('#prtfiliacaoMae','blur',function(){
		$('#prtfiliacaoMae').removeClass("erro");
	});
	
	jQuery("body").delegate('#prtsexo','blur',function(){
		$('#prtsexo').removeClass("erro");
	});
	
	jQuery("body").delegate('#prtestado_civil','blur',function(){
		$('#prtestado_civil').removeClass("erro");
	});
	
	jQuery("body").delegate('#prtoptante_simples','blur',function(){
		$('#prtoptante_simples').removeClass("erro");
	});
	
	jQuery("body").delegate('#prtcontratante','blur',function(){
		$('#prtcontratante').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpfund_dt','blur',function(){
		$('#prpfund_dt').removeClass("erro");
	});
	
	jQuery("body").delegate('#prtie_estado','blur',function(){
		$('#prtie_estado').removeClass("erro");
	});
	
	jQuery("body").delegate('#prtie_num','blur',function(){
		$('#prtie_num').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpend_cep','blur',function(){
		$('#prpend_cep').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpend_pais','blur',function(){
		$('#prpend_pais').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpend_est','blur',function(){
		$('#prpend_est').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpend_cid','blur',function(){
		$('#prpend_cid').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpend_cidade','blur',function(){
		$('#prpend_cidade').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpend_bairro','blur',function(){
		$('#prpend_bairro').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpend_combobairro','blur',function(){
		$('#prpend_combobairro').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpend_log','blur',function(){
		$('#prpend_log').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpend_num','blur',function(){
		$('#prpend_num').removeClass("erro");
	});
	
	jQuery("body").delegate('#prcfone_cont','blur',function(){
		$('#prcfone_cont').removeClass("erro");
	});
	
	jQuery("body").delegate('#prcfone_cont2','blur',function(){
		$('#prcfone_cont2').removeClass("erro");
	});
	
	jQuery("body").delegate('#prcfone_cont3','blur',function(){
		$('#prcfone_cont3').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpend_email','blur',function(){
		$('#prpend_email').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpend_emailnf','blur',function(){
		$('#prpend_emailnf').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpendcob_cep','blur',function(){
		$('#prpendcob_cep').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpendcob_pais','blur',function(){
		$('#prpendcob_pais').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpendcob_est','blur',function(){
		$('#prpendcob_est').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpendcob_cid','blur',function(){
		$('#prpendcob_cid').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpendCob_cidade','blur',function(){
		$('#prpendCob_cidade').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpend_combobairrocobr','blur',function(){
		$('#prpend_combobairrocobr').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpendcob_bairro','blur',function(){
		$('#prpendcob_bairro').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpendcob_log','blur',function(){
		$('#prpendcob_log').removeClass("erro");
	});
	
	jQuery("body").delegate('#prpendcob_num','blur',function(){
		$('#prpendcob_num').removeClass("erro");
	});
	
	jQuery("body").delegate('#tipoPagamento','blur',function(){
		$('#tipoPagamento').removeClass("erro");
	});
	
	jQuery("body").delegate('#tipo_pagamento','blur',function(){
		$('#tipo_pagamento').removeClass("erro");
	});
	
	jQuery("body").delegate('#nAgencia','blur',function(){
		$('#nAgencia').removeClass("erro");
	});
	
	jQuery("body").delegate('#nConta','blur',function(){
		$('#nConta').removeClass("erro");
	});
	
	jQuery("body").delegate('#nCartao','blur',function(){
		$('#nCartao').removeClass("erro");
	});
	
	jQuery("body").delegate('#dataCartao','blur',function(){
		$('#dataCartao').removeClass("erro");
	});
	
	jQuery("body").delegate('#data_vencimento','blur',function(){
		$('#data_vencimento').removeClass("erro");
	});
	
	jQuery('#cancelar_solicitacao').click(function(){
			jQuery.fn.cancelaSolicitacao();
		});
		
		
		jQuery('#gerar_termo').click(function(){
			
			
			if(!jQuery.fn.validateEmail(jQuery("#prpend_email").val())) {
				alert('E-mail invalido');
				$('#prpend_email').addClass('erro');
			}else if(!jQuery.fn.validateEmail(jQuery("#prpend_emailnf").val())) {
				$('#prpend_email').removeClass("erro");
				alert('E-mail invalido');
				$('#prpend_emailnf').addClass('erro');
			}else{
				$('#prpend_email').removeClass("erro");
				$('#prpend_emailnf').removeClass("erro");
				$('#salva_proposta_titularidade').attr('disabled', 'disabled');
				$('#gerar_termo').attr('disabled', 'disabled');
				$('#cancelar_solicitacao').attr('disabled', 'disabled');
				
				jQuery.fn.geraTermo();
			}

		});
	
	
});

jQuery.fn.validateEmail = function (email)
{
	var atpos=email.indexOf("@");
	var dotpos=email.lastIndexOf(".");
	if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length)
	{
	return false;
	}
	return true;
};


jQuery.fn.cancelaSolicitacao = function(){
	
	 if (confirm("Tem certeza que deseja cancelar a proposta de transferência? "))
	  {
		var ptraoid = jQuery('input[name=ptraoid]').val();
	
		
		jQuery.ajax({
			url: 'fin_transferencia_titularidade.php',
			type: 'post',
			data:{
				acao : 'cancelarSolicitacao',
				id: ptraoid,
			},
			success:function(data) {
			  data = jQuery.parseJSON(data);
			  if(data.status=='msgsucesso'){

		    		alert(data.message);
		    		window.location.href = "fin_transferencia_titularidade.php";
		    	}else{
		    		alert(data.message);
		    	}
				if(resultado.status=='errorlogin') window.location.href = resultado.redirect;
				jQuery('#process').html('');
			}
			
		});
	  }
}

jQuery.fn.geraTermo = function(){
	
	jQuery('#acao').val('gerarTermo');


	//jQuery('#voltar').attr('disabled', 'disabled');

	$('#prtie_estado').removeClass('erro');
	$('#prtie_num').removeClass('erro');
	jQuery.ajax({
		url:'fin_transferencia_titularidade.php',
			type: 'post',
			data: jQuery('#frm_novo_titular').serialize(),
			beforeSend:function(){
			
				jQuery('#carregandoAguardeInicio').html('<center><img src="images/loading.gif" alt="" /></center>');
				jQuery('#carregandoAguardeMeio').html('<center><img src="images/loading.gif" alt="" /></center>');
				jQuery('#carregandoAguardeFim').html('<center><img src="images/loading.gif" alt="" /></center>');
				jQuery('#carregandoAguardeEndereco').html('<center><img src="images/loading.gif" alt="" /></center>');
				jQuery('#carregandoAguardeAutorizada').html('<center><img src="images/loading.gif" alt="" /></center>');
				
				
			},
			success:function(data){
				data = jQuery.parseJSON(data);
				jQuery('#carregandoAguardeInicio').html('');
				jQuery('#carregandoAguardeMeio').html('');
				jQuery('#carregandoAguardeFim').html('');
				jQuery('#carregandoAguardeEndereco').html('');
				jQuery('#carregandoAguardeAutorizada').html('');
				
				//jQuery('#salva_proposta_titularidade').removeAttr('disabled');
			//	jQuery('#gerar_termo').removeAttr('disabled');
			//	jQuery('#cancelar_solicitacao').removeAttr('disabled');
			//	jQuery('#voltar').removeAttr('disabled');
				
				if(data.status == 'error'){
					alert('Existem campos obrigatórios sem preenchimento');
					$.map(data, function(val, key) {
						$('#'+val+'').addClass('erro');
					});
					    jQuery('#salva_proposta_titularidade').removeAttr('disabled');
						jQuery('#gerar_termo').removeAttr('disabled');
						jQuery('#cancelar_solicitacao').removeAttr('disabled');
					
				}else if(data.status == 'erroie'){
					alert(data.message);
					$('#prtie_estado').addClass('erro');
					$('#prtie_num').addClass('erro');
				    $('#salva_proposta_titularidade').removeAttr('disabled');
					$('#gerar_termo').removeAttr('disabled');
					$('#cancelar_solicitacao').removeAttr('disabled');
				}else if(data.status == 'errorCombos'){
					alert(data.message);
				    $('#salva_proposta_titularidade').removeAttr('disabled');
					$('#gerar_termo').removeAttr('disabled');
					$('#cancelar_solicitacao').removeAttr('disabled');
				}else if(data.status == 'errorGeracao'){
					alert(data.message);
				    $('#salva_proposta_titularidade').removeAttr('disabled');
					$('#gerar_termo').removeAttr('disabled');
					$('#cancelar_solicitacao').removeAttr('disabled');
				}else if(data.status == 'errorContrato'){
					alert(data.message);
				    $('#salva_proposta_titularidade').removeAttr('disabled');
					$('#gerar_termo').removeAttr('disabled');
					$('#cancelar_solicitacao').removeAttr('disabled');
				}else{
					alert(data.message);
					forma_cobranca(data.cliente);
				}
			}
	});

	
}

/**
 * Fun��o para alterar a forma de pagamento
 */
function forma_cobranca(key){

		var origem = "CF";
        var entrada = "I";
        
        
        var dadosCobranca =  "&clioid=" + key;
            dadosCobranca += "&forcoid=" + jQuery("#tipo_pagamento").val();
            dadosCobranca += "&forma_pagamento_clidia_vcto=" + jQuery("#data_vencimento").val();
            dadosCobranca += "&numero_cartao=" + jQuery("#nCartao").val();
            dadosCobranca += "&mes_ano=" + jQuery("#dataCartao").val();
            dadosCobranca += "&motivo_alterar_debito=" + '';
            dadosCobranca += "&debito_banco=" + jQuery("#idBanco").val();
            dadosCobranca += "&debito_agencia=" + jQuery("#nAgencia").val();
            dadosCobranca += "&debito_conta=" + jQuery("#nConta").val();
            dadosCobranca += "&origem_chamada=" + origem;
            dadosCobranca += "&entrada=" + entrada;
    
        jQuery.ajax({
            url: 'prn_manutencao_forma_cobranca_cliente.php',
            type: 'post',
            data: 'acao=confirmarFormaPagamento'+dadosCobranca,
    		beforeSend:function(){
    			
				jQuery('#carregandoAguardeInicio').html('<center><img src="images/loading.gif" alt="" /></center>');
				jQuery('#carregandoAguardeMeio').html('<center><img src="images/loading.gif" alt="" /></center>');
				jQuery('#carregandoAguardeFim').html('<center><img src="images/loading.gif" alt="" /></center>');
				jQuery('#carregandoAguardeEndereco').html('<center><img src="images/loading.gif" alt="" /></center>');
				jQuery('#carregandoAguardeAutorizada').html('<center><img src="images/loading.gif" alt="" /></center>');
				
				
			},
            success: function(ret){
                var resultado = ret;//jQuery.parseJSON(data);
                
                jQuery('#loading').fadeOut();
           
                //Mensagens de Retorno Informando situa��o ao operador
                if(resultado == '"OK"') {
                	jQuery('#carregandoAguardeInicio').html('');
    				jQuery('#carregandoAguardeMeio').html('');
    				jQuery('#carregandoAguardeFim').html('');
    				jQuery('#carregandoAguardeEndereco').html('');
    				jQuery('#carregandoAguardeAutorizada').html('');
    				
                    criaAlerta("Forma de Pagamento atualizada com sucesso!");
                    jQuery("#numero_cartao").val('');
                    //jQuery("#codigo_seguranca").val('');
                    jQuery("#mes_ano").val('');
                    jQuery("#forma_pagamento_clidia_vcto").val('');
                    jQuery('#bt_confirmar_forma').show();
                    jQuery('#acao').val("Consultar");
                    jQuery('#frm_contrato').submit();
                    
                       
                } else {
                	jQuery('#carregandoAguardeInicio').html('');
    				jQuery('#carregandoAguardeMeio').html('');
    				jQuery('#carregandoAguardeFim').html('');
    				jQuery('#carregandoAguardeEndereco').html('');
    				jQuery('#carregandoAguardeAutorizada').html('');
                    //criaAlerta("N�o foi poss�vel realizar a altera��o dos dados!");
                	jQuery("#pcliccartaocredito").val("");
                	jQuery("#pcliccartaovenc").val("");
                    criaAlerta("N�o foi poss�vel realizar a altera��o dos dados !  <br/><br/>" +  resultado );
                    jQuery('#bt_confirmar_forma').show();
                }
            }
        });
        

}


