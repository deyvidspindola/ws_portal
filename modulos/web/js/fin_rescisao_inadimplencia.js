jQuery(function() {	
	
	jQuery('.zebra:odd').addClass('par');
	
	jQuery('.loading').hide();
	
	jQuery('#confirmar').click(function(){
		
		jQuery.fn.cleanMessage();
		
		setTimeout(function(){
			
			jQuery('.resultado').hide();

			if(jQuery("#arquivo_csv").val()==''){
				$("#arquivo_csv").addClass('erro').val('');
				jQuery('#msgalerta').html('Nenhum arquivo informado.').showMessage();
				return;
			}
			
			/*
			 * Verifica a extensao do arquivo
			 */
			var extensaoArquivo = jQuery.fn.verifyExtension(jQuery("#arquivo_csv").val(), 'csv');
			
			if(!extensaoArquivo){
				$("#arquivo_csv").addClass('erro').val('');
				jQuery('#msgalerta').html('Formato do arquivo inválido.').showMessage();
				return;
			}
			
			jQuery('.loading').show();
			
			jQuery('#acao').val('importarArquivo');
			
			jQuery('#frm_importar').submit();
			
		}, 100);
		
	});	
    
});

/*
 * Funcao para validar a extensao do arquivo
 */
jQuery.fn.verifyExtension = function(arquivo, extensao){
		
	arquivo = arquivo.split('\\');
	
	nome_arquivo = arquivo[arquivo.length - 1];
	
	nome_arquivo = nome_arquivo.split('.');
	
	extensao_arquivo = nome_arquivo[nome_arquivo.length - 1];
	
	if(extensao_arquivo == extensao){
		return true;
	}
	
	return false;
}

/*
 * Funcao para limpar mensagens
 */
jQuery.fn.cleanMessage = function() {
    jQuery('#msgerro').hide();
    jQuery('#msgsucesso').hide();
    jQuery('#msgalerta').hide();
}