jQuery(document).ready(function(){
   
	// Evento change da combo de projetos de equipamento
	jQuery('select[name="eproid_busca"]').change(function() {
		
		var eproid_buscaval	= jQuery(this).val();
		var post			= {acao: 'buscarVersoes', eproid_busca: eproid_buscaval };
		 
		jQuery('select[name="eveoid_busca"]').html('<option value="">Escolha um projeto</option>');
		
		if (eproid_buscaval.length == 0) {
			return false;
		}
		
		jQuery.ajax({
			url: 'cad_grupos_de_testes.php',
			dataType: 'json',
			type: 'post',
			data: post,
			success: function(data) {
				var itens = [];
				if (data.retorno !== null && data.erro === false) {
				  
					itens.push('<option value="">Escolha</option>');

					jQuery.each(data.retorno, function(key, val) {
						if (val !== null && key !== null && val.eveoid !== null && val.eveversao !== null) {
							itens.push('<option value="' + val.eveoid + '">' + (unescape(val.eveversao)) + '</option>');
						}
					}); 
					jQuery('select[name="eveoid_busca"]').html(itens.join(''));
				}
			  
				if (data.erro !== false) {
					alert("Erro no retorno de dados"); 
				}
			}
		});
	});
	   
	//botão pesquisar
	jQuery("#bt_pesquisar").click(function(){
        jQuery('#acao').val('pesquisar');
        jQuery('#form').submit();
	})
   
	//botão voltar
	jQuery("#bt_voltar").click(function(){
		window.location.href = "principal.php?menu=Cadastro";
	})
    
    // Botão salvar grupo
	jQuery('#bt_salvar').click(function(){

		arrayEptpoid = new Array();
		
		jQuery("input[type=checkbox][name='check[]']:checked").each(function(){	
			arrayEptpoid.push(jQuery(this).val()); 
		});
       
		// Exibe alertas
		if(arrayEptpoid.length == 0 && jQuery('#egtnome').val() == '' ){
			jQuery('html, body').scrollTop(0);
			jQuery('#mensagem_alerta').show();
			jQuery('#mensagem_alerta').html('Existem campos obrigatórios não preenchidos e nenhum Teste/ Comando foi selecionado.');
			jQuery('#egtnome').addClass('erro');
			jQuery('label[for="egtnome"]').addClass('erro');
		}
		else if(arrayEptpoid.length == 0){
			jQuery('html, body').scrollTop(0);
			jQuery('#mensagem_alerta').show();
			jQuery('#mensagem_alerta').html('Nenhum Teste/ Comando selecionado.');
		}
		else if(jQuery('#egtnome').val() == ''){
			jQuery('html, body').scrollTop(0);
			jQuery('#mensagem_alerta').show();
			jQuery('#mensagem_alerta').html('Existem campos obrigatórios não preenchidos.');
			jQuery('#egtnome').addClass('erro');
			jQuery('label[for="egtnome"]').addClass('erro');
		}
		else {
	        jQuery('#arrayEptpoid').val(arrayEptpoid);
			jQuery('#acao').val('salvar');
	        jQuery('#form').submit();
		}
    });
	
	// Editar Grupo
    jQuery('body').delegate('.editarGrupo', 'click', function(){
            var egtoid = jQuery(this).attr('egtoid');
            jQuery('#egtoid').val(egtoid);
			jQuery('#acao').val('editarGrupo');
	        jQuery('#form').submit();
    });
	
	// Excluir Grupo
    jQuery('body').delegate('.excluirGrupo', 'click', function(){
        if (confirm('Confirma a exclusão do grupo?')) {
            var egtoid = jQuery(this).attr('egtoid');
            jQuery('#egtoid').val(egtoid);
			jQuery('#acao').val('excluirGrupo');
	        jQuery('#form').submit();
        }
    });
   
});