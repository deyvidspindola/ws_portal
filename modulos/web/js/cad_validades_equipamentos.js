/**
 * @author	Diego Noguês
 * @email 	diegocn@brq.com
 * @since	06/08/2013
 */

jQuery(function() {    	

	if (jQuery('#mensagem').text() == '') {
        jQuery('#mensagem').hide();
    }
	
	// Ação de editar
	jQuery('body').delegate('.clickEditar',  'click', function(){
		jQuery('#veqpoid').val(jQuery(this).attr('id'));
		// Troca ação para o valor correspondente e dá submit no form
		jQuery('#acao').val('editar').closest('form').submit();
	});

	// Ações do form
	jQuery('body').delegate('#buttonNovo,#buttonCancelar,#buttonExcluir,#buttonPesquisar', 'click', function(){
		// Pega value do botão clicado
		var acaoValor = $(this).val();
		// Troca ação para o valor correspondente e dá submit no form
		jQuery('#acao').val(acaoValor).closest('form').submit();
	});
	
	jQuery("#veqpdescricao").on("blur", function() {
		jQuery(this).val(jQuery.trim(jQuery(this).val()));
	});

	jQuery('body').delegate('.camponum', 'keyup', function(){    
        var valor = jQuery(this).val();
        if(!$.isNumeric(valor))
        {
            jQuery(this).val(valor.replace(/[^\d]/g, ''));
        }
    });
});    