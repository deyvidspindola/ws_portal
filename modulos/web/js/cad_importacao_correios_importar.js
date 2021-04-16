jQuery(document).ready(function() {	
	
	jQuery("body").delegate('#importar', 'click', function(){
		jQuery.fn.importar();
	});	
    
});

jQuery.fn.limpaMensagens = function() {
	jQuery(".erro").removeClass("erro");
    jQuery('#msgalerta,#msgsucesso,#msgerro').hideMessage();
}

jQuery.fn.importar = function(strOid) {	
	
	jQuery.fn.limpaMensagens();
	
	if(jQuery("#arquivo_zip").val()==''){
		$("#arquivo_zip").addClass('erro').val('');
		jQuery('#msgalerta').html('Informe um arquivo para a importação.').showMessage();
		return;
	}

	jQuery("#loading").show();	
	jQuery("#content_log").hide();	
	jQuery("#importar").attr('disabled', 'disabled');
   	jQuery("#acao").val('importar');
	jQuery('#frm_importar').submit();
}