jQuery(document).ready(function() {
	
	jQuery('#botao_salvar_assistencia').click(function(){
		//alert('assistenciaSalvar');return;
        jQuery("#acao").val('assistenciaSalvar');
        jQuery("#assistencia_form").submit();
	});
	
	jQuery('#botao_salvar_predefinidos').click(function(){
		
	    if( !confirm( 'Deseja gravar os valores padrão?' ) )  return false; 
        jQuery("#acao").val('predefinidosSalvar');
        jQuery("#assistencia_form").submit();
        
	});
	
	/**
	 * Função para carregar os defeitos alegados na página de assistência.
	 
	function CarregarDefeitosAlegados() {
		var itensOs = jQuery("input[name='itens_os[]']:checked");
		var itens = new Array();
		itensOs.each(function(i, e) {
			itens.push(jQuery(e).val());
		});
		
		var tiposOs = jQuery("input[name='os_tipo_id[]']:checked");
		var tipos = new Array();
		tiposOs.each(function(i, e) {
			tipos.push(jQuery(e).val());
		});
		
		if ((tipos.length > 0) && (itens.length > 0)) {
			if (jQuery("#defeitos_carregados_assistencia").val())
				return;
			else
				jQuery("#defeitos_carregados_assistencia").val('true');
							
			jQuery.post('man_parametrizacao_ura.php?acao=assistenciaBuscarDefeitos', function(data) {			
				jQuery.each(data, function(key, defeito) { 
					var ar = jQuery('#defeitos_marcados').val().split(',');
					
					var ch = ''
					if(jQuery.inArray(''+defeito.id, ar)!=(-1)) ch='checked="checked"';
					
					var h = "<li><label for='defeito_id_"+defeito.id+"'>"+defeito.descricao+"</label><input type='checkbox' id='defeito_id_"+defeito.id+"' name='defeito_id[]' value='"+defeito.id+"' "+ch+" /></li>";
					jQuery('#ulDefeitos').append(h);
				});
			}, 'json');
		} else {
			jQuery('#ulDefeitos').empty();
			jQuery("#defeitos_carregados_assistencia").val('');
		}
	}
	
	jQuery('input[name="itens_os[]"]').click(function() {
		CarregarDefeitosAlegados();
	});
	
	jQuery('input[name="os_tipo_id[]"]').click(function() {
		CarregarDefeitosAlegados();
	});
	
	if(jQuery('input[name="itens_os[]"]:checked').length > 0
		&& jQuery('input[name="os_tipo_id[]"]:checked').length > 0) {
		CarregarDefeitosAlegados();
	}
	*/
});
