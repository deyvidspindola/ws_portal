jQuery(document).ready(	function() {

	jQuery('#bt_gerar_csv').click(function(){

		removeAlerta();

		var tipo_relatorio = jQuery('#tipo_relatorio').val();

		if(tipo_relatorio == 'online'){
			url_relatorio = 'rel_arquivo_venda_online.php';

		}else if(tipo_relatorio == 'd1'){
			url_relatorio = 'rel_arquivo_venda_d1.php';

		}else{
			criaAlerta('O tipo de relatorio deve ser definido.');
			return false;;
		}	

		jQuery.ajax({
			
			url: url_relatorio,
			type: 'post',
			data: jQuery('#frm').serialize()+ '&acao=getDadosRelatorio',
			
			beforeSend: function(){
				jQuery('#arquivo').css('display','none'); 
				jQuery('#loading').css('display','block'); 
			},			
			success: function(data){

				if(data != 0 && data != 'null'){

					var resultado = jQuery.parseJSON(data);

					jQuery('#loading').hide();

					jQuery('.msg_aquivo').html('Arquivo <font color="blue"><b>'+ resultado.nomeArquivo + '</b></font> gerado com sucesso.');

					jQuery('#nome_arquivo').val(resultado.nomeArquivo);

					jQuery('#path_arquivo').val(resultado.pathArquivo);

					jQuery('#arquivo_gerado').val('http://'+resultado.ulrArquivo);

					jQuery('#acao').val('getArquivo');
					
					jQuery('#frm').submit();

					$('#arquivo').fadeOut('fast', function() {
						$('#arquivo').fadeIn('slow');
					});

				}else{
					criaAlerta('Erro ao gerar arquivo, por favor tente novamente.');
					jQuery('#loading').hide();
					$('#arquivo').show();
					return false;
				}
			}
		});

	});

});