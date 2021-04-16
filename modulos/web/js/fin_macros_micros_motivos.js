jQuery(function(){

	/**
	 * Click do botão Pesquisar
	 */
	jQuery('#btn_pesquisar').click(function(){
		
		removeAlerta();
		
		var ckeckbox_null = true;
	
		jQuery('#msg').html('');
		jQuery('.resultado_pesquisa').hide();
		jQuery('.carregando').show();
		jQuery('#btn_enviar').hide();
		
		//verifica se alguma ckeckbox foi marcado
		 $('.checkbox').each(
			         function(){
			          
			        	 if($(this).is(':checked')){
			        		 ckeckbox_null = false;
			        	 }
			         }
			      );
		
		if($('#descricao').val() == '' && ckeckbox_null == true){
			
			criaAlerta('Informe algum dado para efetuar a pesquisa.');
			
			jQuery('.carregando').hide();
			jQuery('#btn_enviar').show();
			
			return false;
		}
		
		 $('#filtro').submit();
		
	});
	
	
	
	jQuery('input[name="todos"]').click(function() {
		
		 if($(this).is(':checked')){
			 jQuery('input[name="nivel[]"]').attr('checked', 'checked');
    	 }else{
    		 jQuery('input[name="nivel[]"]').attr('checked', false);
    	 }

	});
	
	
    jQuery('#limpar').click(function(){
		
    	removeAlerta();
    	//limpa os inputs
		jQuery('#contrato').val('');
		jQuery('#cliente').val('');
		jQuery('#documento').val('');
	
		//desmarca todos os checkboxs
		 $('.checkbox').each(
			         function(){
			            $(this).attr("checked", false);
			         }
			      );
		
		//limpar os combos
		$("select#tipo_contrato").val('').attr('selected', true);
		$("select#obrigacao_financeira").val('').attr('selected', true);
		//$("select#periodicidade_faturamento").val('').attr('selected', true);
    		
	});


	// Inclusão e Edição 
	
	/**
	 * @tag input(type=button)
	 * @Event OnClick
	 */
	jQuery('#btn_novo').click(function() {
		jQuery('input[name="acao"]').val('novo');
		jQuery('form[name="filtro"]').submit();
	});

	jQuery('body').delegate('a.link_editar', 'click', function(){

		var id = jQuery(this).data('id');
		/**
		 * STI 84969
		 * Antes de abrir tela para edição é verificado o tipo de parâmetro
		 */
		jQuery.ajax({

			url : 'fin_macros_micros_motivos.php',
			type : 'post',
			success : function(data) {
				jQuery('input[name="acao"]').val('editar');
				jQuery('input[name="pfmoid"]').val(id);
				jQuery('form[name="filtro"]').submit();

			}
		});
		
	});
	
});

function criaAlerta(msg, status) { 
	$("#mensagem").show();
    $("#mensagem").text(msg).removeAttr('class').addClass('mensagem alerta').addClass(status).show();
}


function removeAlerta() {
    $("#mensagem").hide();
}


