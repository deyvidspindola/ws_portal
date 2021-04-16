jQuery(document).ready(function() {	
	
	jQuery("body").delegate('#importar', 'click', function(){
		jQuery.fn.importar();
	});	
    
});

jQuery.fn.limpaMensagens = function() {
	jQuery(".erro").removeClass("erro");
    jQuery('.mensagem').hideMessage();
}

jQuery.fn.importar = function(strOid) {	
	
	jQuery.fn.limpaMensagens();
	
	if(jQuery("#arquivo_csv").val()==''){
		$("#arquivo_csv").addClass('erro').val('');
		jQuery('#msgalerta').html('Informe o arquivo csv a ser importado.').showMessage();
		return;
	}
	
	if(!confirm('Confirma a importação do arquivo ?')) return;
   	jQuery("#acao").val('telaImportacao');
	jQuery('#frm_importar').submit();
}