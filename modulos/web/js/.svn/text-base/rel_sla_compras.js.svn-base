jQuery(document).ready(function() {
	
	jQuery("#cotacao").mask('9?9999999', { placeholder: "" });
	jQuery("#rms").mask('9?9999999', { placeholder: "" });
	
	jQuery('#pesquisar').click(function() {
		var validation = {"status":true, "dados": []};
        resetFormErros();
		jQuery('#msg_alerta, #msg_alerta1').hideMessage();
		jQuery('.alerta,.erro,.sucesso').hideMessage();
		
		if ((jQuery('#cotacao').val() != "") && (jQuery('#rms').val() != "")) {
			jQuery('#msg_alerta').text('Preencha apenas uma opção, Cotação ou RMS.');
			jQuery('#msg_alerta').showMessage();
			
			validation.status = false;
			validation.dados.push({"campo":"cotacao","mensagem":""});
			validation.dados.push({"campo":"rms","mensagem":""});
		} else if (jQuery.trim(jQuery('#data_inicio').val()) == "" ||jQuery.trim(jQuery('#data_fim').val()) == "") {
			jQuery('#msg_alerta').text('Existem campos obrigatórios não preenchidos.');
			
			validation.status = false;
			validation.dados.push({"campo":"data_inicio","mensagem":"Campo obrigat\u00f3rio."});
			validation.dados.push({"campo":"data_fim","mensagem":"Campo obrigat\u00f3rio."});
			
                jQuery('#data_inicio').addClass("erro");
                jQuery('#data_fim').addClass("erro");            
                jQuery('#msg_alerta').showMessage();
              
		} 
		
    	if(validation.status!=true) 
    	{
    		showFormErros(validation.dados);
    		return false;
    	}		
		
		jQuery('#msg_alerta').hideMessage();		
		jQuery('#acao').val('pesquisar');

		return true;
    	
	});	
	
});