jQuery(function(){
	$('#limparCliente').hide();
	$('#cliente').keyup(function(){ 
	
		jQuery.ajax({
             url: 'fin_fat_manual.php',
             type: 'POST',
             data: {
                 acao: 'pesquisaClientes',
                 cliente: jQuery('input[name=cliente]').val()
             },
             success : function(data) {
            	 if ($('#cliente').val()!=''){
            		 
            		 var divResultAltura = jQuery('input[name="cliente"]').innerHeight();

						/* Mostra a lista de resultados
						*/
						jQuery('#cpx_div_result_cliente_nome_2').fadeIn('slow');

						/* Configura a altura da lista de resultados de acordo
						*  com a quantidade de linhas retornadas
						*/
						if(data.length < 15){
							jQuery('#cpx_div_result_cliente_nome_2').css({
								height: (data.length*(divResultAltura))+'px'
							});
						} else {
							jQuery('#cpx_div_result_cliente_nome_2').css({
								height: '200px'
							});
						}
						
     				$('#cpx_div_result_cliente_nome_2').show();
     				$('#cpx_div_result_cliente_nome_2').empty().html(data);
     				
     				jQuery(data).each(function(i,o){ 
     					
     					var idDiv = o.id;
     					var nome = o.title;

     					$("#"+idDiv).click(function(){
     						$("#cliente").attr("value", $(this).children().eq(0).html());
     						$("#id_cliente").attr("value",idDiv);
     						$('#cpx_div_result_cliente_nome_2').hide();
     						$('#limparCliente').show();
     						$('#cliente').attr("disabled", true);
     					});
     				});
     				
     			}
     			else{
     				$('#cpx_div_result_cliente_nome_2').empty();
     			}

             }
         });
		
		
		
		
		
     });

		$('#limparCliente').click(function(){
			$('#cliente').removeAttr('disabled');
			$("#cliente").attr("value",'');
				$("#id_cliente").attr("value",'');
			$('#limparCliente').hide();
		});
	
});