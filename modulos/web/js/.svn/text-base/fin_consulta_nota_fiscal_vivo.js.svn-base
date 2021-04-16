jQuery(document).ready(function(){
    
    //Cria o help inicial da tela
    adicionaHelp();

    //faz o tratamento para os campos de perídos 
    jQuery("#dt_evento_de").periodo("#dt_evento_ate");
	
    // permite somente números
    jQuery('#cpfcnpj').mask('9?9999999999999', { placeholder: '' });
    jQuery('#nota_fiscal').mask('9?99999999', { placeholder: '' });
    
    //exibe campo placa
    jQuery('input[name="selecao_por"]').change(function(){
        if (jQuery(this).val() == 'N') {
            jQuery('#div_placa').removeClass('visivel');
            jQuery('#div_placa').addClass('invisivel');
            jQuery('#status_sascar, #status_vivo, #cliente, #cpfcnpj, #nota_fiscal, #serie, #placa').val('');
        } else {
        	jQuery('#div_placa').removeClass('invisivel');
        	jQuery('#div_placa').addClass('visivel');
        	jQuery('#status_sascar, #status_vivo, #cliente, #cpfcnpj, #nota_fiscal, #serie, #placa').val('');
        }
    });
    
    function adicionaHelp(){

    	var title = "Período de Retorno Status VIVO";

        jQuery('#help-data-analise')
            .attr("title", title)
            .tooltip({
                position: {
                    my: 'left+5 center', 
                    at: 'right center'
                }
            });
    }
    
});