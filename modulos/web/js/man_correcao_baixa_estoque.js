jQuery(document).ready(function(){
	 
	
    // Faz o tratamento para os campos de período 
    jQuery("#dataInicial").periodo("#dataFinal");
	
	// Evento change da combo de instaladores
	jQuery('select[name="repoid_busca"]').change(function() {
		
		var repoid_buscaval	= jQuery(this).val();
		var post			= {acao: 'buscarInstaladores', repoid_busca: repoid_buscaval };
		 
		jQuery('select[name="itloid_busca"]').html('<option value="">Escolha um representante</option>');
		
		if (repoid_buscaval.length == 0) {
			return false;
		}
		
		jQuery.ajax({
			url: 'man_correcao_baixa_estoque.php',
			dataType: 'json',
			type: 'post',
			data: post,
			success: function(data) {
				var itens = [];
				if (data.retorno !== null && data.erro === false) {
				  
					itens.push('<option value="">Escolha um representante</option>');

					jQuery.each(data.retorno, function(key, val) {
						if (val !== null && key !== null && val.instalador !== null && val.instalador !== null) {
							itens.push('<option value="' + val.id + '">' + (unescape(val.instalador)) + '</option>');
						}
					}); 
					jQuery('select[name="itloid_busca"]').html(itens.join(''));
				}
			  
				if (data.erro !== false) {
					alert("Erro no retorno de dados"); 
				}
			}
		});
	});
	
    // Botão Diferença de Baixa
    jQuery("#bt_diferenca").click(function(){
        jQuery('#acao').val('pesquisarBaixaIncorreta');
        jQuery('#tipo_csv').val('D');
        jQuery('#form').submit();
    });
    
    // Checkbox marcar todos
    jQuery('#selecao_todos').checarTodos('input.selecao');
    
    // Botão Corrigir Baixas
	jQuery('#bt_corrigir').click(function(){

		arrayOrdem 	= new Array();
		
		jQuery("input[type=checkbox][name='check[]']:checked").each(function(){
			
			arrayOrdem.push(jQuery(this).val());
			
		});
       
		// Exibe alerta
		if(arrayOrdem.length == 0){
			jQuery('html, body').scrollTop(0);
			jQuery('#mensagem_alerta').show();
			jQuery('#mensagem_alerta').html('Nenhuma OS selecionada.');
		}
		else {
			jQuery('#acao').val('corrigirBaixaIncorreta');
	        jQuery('#arrayOS').val(arrayOrdem);
	        jQuery('#form').submit();
		}
    });

});