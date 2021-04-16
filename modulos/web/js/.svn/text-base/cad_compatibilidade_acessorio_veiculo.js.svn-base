jQuery(document).ready(function(){
   
	// permite somente números ano BUSCA
	jQuery('#cavano_busca').mask('9?999', { placeholder: '' });

	// permite somente números ano EDIÇÂO
	jQuery('#cavano').mask('9?999', { placeholder: '' });
	
	// Evento change da combo de marcas de veículos BUSCA
	jQuery('select[name="cavmcaoid_busca"]').change(function() {

		var mcaoid_buscaval	= jQuery(this).val();
		var post			= {acao: 'buscarModelos', mcaoid: mcaoid_buscaval };
 
		jQuery('select[name="cavmlooid_busca"]').html('<option value="">Escolha uma marca</option>');

		if (mcaoid_buscaval.length == 0) {
			return false;
		}

		jQuery.ajax({
			url: 'cad_compatibilidade_acessorio_veiculo.php',
			dataType: 'json',
			type: 'post',
			data: post,
			success: function(data) {
				var itens = [];
				if (data.retorno !== null && data.erro === false) {
	  
					itens.push('<option value="">Escolha</option>');

					jQuery.each(data.retorno, function(key, val) {
						if (val !== null && key !== null && val.mlooid !== null && val.mlomodelo !== null) {
							itens.push('<option value="' + val.mlooid + '">' + (unescape(val.mlomodelo)) + '</option>');
						}
					}); 
					jQuery('select[name="cavmlooid_busca"]').html(itens.join(''));
				}
  
				if (data.erro !== false) {
					alert("Erro no retorno de dados"); 
				}
			}
		});
	});
   
	// Evento change da combo de marcas de veículos EDIÇÂO
	jQuery('select[name="cavmcaoid"]').change(function() {

		var mcaoid_editval	= jQuery(this).val();
		var post			= {acao: 'buscarModelos', mcaoid: mcaoid_editval };
 
		jQuery('select[name="cavmlooid"]').html('<option value="">Escolha uma marca</option>');

		if (mcaoid_editval.length == 0) {
			return false;
		}

		jQuery.ajax({
			url: 'cad_compatibilidade_acessorio_veiculo.php',
			dataType: 'json',
			type: 'post',
			data: post,
			success: function(data) {
				var itens = [];
				if (data.retorno !== null && data.erro === false) {
	  
					itens.push('<option value="">Escolha</option>');

					jQuery.each(data.retorno, function(key, val) {
						if (val !== null && key !== null && val.mlooid !== null && val.mlomodelo !== null) {
							itens.push('<option value="' + val.mlooid + '">' + (unescape(val.mlomodelo)) + '</option>');
						}
					}); 
					jQuery('select[name="cavmlooid"]').html(itens.join(''));
				}
  
				if (data.erro !== false) {
					alert("Erro no retorno de dados"); 
				}
			}
		});
	});
   
	// Botão novo
	jQuery("#bt_novo").click(function(){
		window.location.href = "cad_compatibilidade_acessorio_veiculo.php?acao=cadastrar";
	});
   
	// Botão voltar
	jQuery("#bt_voltar").click(function(){
		window.location.href = "cad_compatibilidade_acessorio_veiculo.php";
	});
	
  	// Editar
  	jQuery('body').delegate('.editar', 'click', function(){
  		var cavoid = jQuery(this).attr('cavoid');
  		jQuery('#cavoid').val(cavoid);
  		jQuery('#acao').val('editar');
  		jQuery('#form').submit();
  	});

   	// Excluir
   	jQuery('body').delegate('.excluir', 'click', function(){
   		if (confirm('Confirma a exclusão do registro?')) {
   			var cavoid = jQuery(this).attr('cavoid');
   			jQuery('#cavoid').val(cavoid);
   			jQuery('#acao').val('excluir');
   			jQuery('#form').submit();
   		}
   	});
   
});