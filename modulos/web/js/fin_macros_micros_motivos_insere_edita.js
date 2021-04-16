/**
 * Mensagens de retorno
 */
var MSG_EXISTEM_CAMPOS_NAO_PREENCHIDOS 	= "Existem campos obrigatórios não preenchidos.";

/**
 * OnLoad
 */
jQuery(document).ready(function(){

	// Ajuste automatico do tamanho da tela
	jQuery(window).resize(function() {
		var widthTabela = jQuery(window).width();
		
		if (widthTabela < 991) {
			
			jQuery('#tabela_container').removeAttr('width');
			jQuery('#tabela_container').width('990');
			jQuery('#tabela_container').css('display', 'block');
			
			jQuery('#tabela_container2').removeAttr('width');
			jQuery('#tabela_container2').width('990');
			jQuery('#tabela_container2').css('display', 'block');
			
			
		} else {
			
			jQuery('#tabela_container').width('98%');
			jQuery('#tabela_container').css('display', 'table');
			
			jQuery('#tabela_container2').width('98%');
			jQuery('#tabela_container2').css('display', 'table');
		}
	});
	
	
	//verifica��o para o IE n�o funciona se tiver desabilitado
	if(jQuery('input[name=parfoid]').val() == ''){
		// Da foco ao input inicial
		jQuery('input[name="nivel"]:checked').focus();
	}

	jQuery('input[name="nivel"]').each(function(){

		if (jQuery(this).is(':checked') && (jQuery(this).val() == 1 || jQuery(this).val() == 2 ) ) {

			
		} 

	});

//
//	jQuery("#trocas_taxa_unica").change(function() {
//		if (jQuery(this).is(':checked')) {
//			jQuery(".trocas_valor").removeAttr('disabled').removeClass('disabled');
//		}else{
//			jQuery(".trocas_valor").val('').attr('disabled','disabled').addClass('disabled');
//		}
//	});


	
	/**
	 * @tag input(id=btn_retornar)
	 * @Event OnClick
	 */
	jQuery('#btn_retornar').click(function() {
		//window.location.href = window.location.href;
		jQuery('input[name="acao"]').val('retornar');
		jQuery('form[name="frm"]').submit();
	});
	
	
	/**
	 * @tag input(id=btn_confirmar)
	 * @Event OnClick
	 */	
	jQuery('#btn_confirmar').click(function() {

		// Limpa marcadores de campos
		jQuery('.erro').removeClass('erro');
		
		removeAlerta();
		/**
		 * Nivel
		 */
		var nivel = parseInt(jQuery('input[name="nivel"]:checked').val());
		
		/**
		 * Contrato
		 */
		var descricao  = jQuery('input[name="descricao"]').val();
                
		var validaObrigatorios        = true;
		var validaPrazo_vencimento    = true;
		var validaValorData     	  = true;

		/**
		 * Observacao
		 */
		var observacao = jQuery('[name="obs_param"]').val();

		switch (nivel) {
			
			case 1: 
				
				//  contrato
				if (descricao.length == 0) {
					validaObrigatorios = false;
					jQuery('input[name="descricao"]').addClass('erro');
				}
				break;
				
			case 2:

				//  contrato
				if (descricao.length == 0) {
					validaObrigatorios = false;
					jQuery('input[name="descricao"]').addClass('erro');
				}

				break;
		}

		
		// Se ainda exitir campos obrigatórios para preencher, mostra mensagem 
		if (!validaObrigatorios){
			criaAlerta(MSG_EXISTEM_CAMPOS_NAO_PREENCHIDOS);
			return false;
		}

		/**
		 * OUTRAS VALIDAÇÔES
		 */
		

                        
		var parfoid = jQuery('input[name="parfoid"]').val();
		
		//se for vazio é inserção
		if(parfoid == ''){
                
                    jQuery('input[name="acao"]').val('salvar');
                    jQuery('form[name="frm"]').submit();
                 
                 // comentado devido a alteração de permitir cadastro de mais de um parâmetro
                //verifica se já possui parâmetros cadastrados
//			jQuery.ajax({
//				
//				url : 'fin_parametros_faturamento.php',
//				type : 'post',
//				data : 'acao=verificarCadastro&nivel=' + nivel + '&contrato=' + contrato +'&cod_cliente=' + jQuery('input[name="cpx_valor_cliente_nome"]').val() + '&tipo_contrato=' + tipoContrato,
//				success : function(data) {
//	
//					var result = jQuery.parseJSON(data);
//					 if(result == 1) {
//						jQuery('input[name="acao"]').val('salvar');
//                                                 jQuery('form[name="frm"]').submit();
//					 }else{
//						 //alert(result);
//						 return false;
//					 }
//				}
//			});
		
	    // é edição, então, salva as alterações
		}else{
                        jQuery('input[name="acao"]').val('salvar');
			jQuery('form[name="frm"]').submit();
		}

	});

	// Caso exista o botão excluir,
	//	Adicione o evento
	if (typeof jQuery('button[name="btn_excluir"]').get(0) != "undefined") {
		
		jQuery('button[name="btn_excluir"]').click(function(){
			
			if (confirm("Deseja excluir esse motivo?")) {
				
				jQuery('input[name="acao"]').val('excluir');
				jQuery('form[name="frm"]').submit();
			}
		});
	}

});




function criaAlerta(msg, status) { 
	$("#mensagem").show();
    $("#mensagem").text(msg).removeAttr('class').addClass('mensagem alerta').addClass(status).show();
}


function removeAlerta() {
    $("#mensagem").hide();
}



function pad(number, length) {
	   
    var str = '' + number;
    
    while (str.length < length) {
        str = '0' + str;
    }
    return str;
};

