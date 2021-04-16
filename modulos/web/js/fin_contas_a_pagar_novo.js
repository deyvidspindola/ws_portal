jQuery(document).ready(function(){
	

	jQuery('#download_csv').hide();
	
    

	
	jQuery("#todos").toggleChecked("input[name='ck[]']");

	
	$("body").delegate('#pesquisar','click',function(){
		
		if($("#tecoid").val() == ''){
			alert("O campo Empresa é obrigatório");
		}else{
			var consultar_busca = jQuery('#consultar_busca').val();
			$('#consultar_busca').val(consultar_busca);
			$('#acao').val('pesquisarGeraArquivo');
			jQuery.ajax({
				url: 'fin_arq_apagar.php',
				type: 'post',
				data: jQuery('#frm_pesquisar').serialize(),
				beforeSend: function(){		
					jQuery('#frame01').html('<center><img src="images/loading.gif" alt="" /></center>');
					
				},
				success:function(data) {
					jQuery('#frame01').val('');
					try{	
						// Transforma a string em objeto JSON
						var resultado = jQuery.parseJSON(data);
				    	jQuery('#msgerro').attr("class", "mensagem erro").html(resultado.message).showMessage();
						if(resultado.status=='errorlogin') window.location.href = resultado.redirect;
						jQuery('#frame01').html('');

					}catch(e){
						try{	
							// Transforma a string em objeto JSON
							jQuery('#frame01').html(data).hide().showMessage();						 				
						}catch(e){			
					    	jQuery('#msgerro').attr("class", "mensagem erro").html('Erro no processamento dos dados.').showMessage();
						}

				}
					 
				}
				
			});
		}

	});
	
	$("body").delegate('#imprimir','click',function(){
        $('#frm_pesquisar').attr('action','imp_arq_apagar.php?consultar_busca=apgdt_entrada');
       // $('a[href="imp_arq_apagar.php?consultar_busca=apgdt_entrada"]').attr('target', '_blank'); 
        $('#frm_pesquisar').submit();
        
	});
	
	$("body").delegate('#pesquisarEnvioArquivos', 'click',function(){
		$('#acao').val('pesquisaEnvioArquivos');
		jQuery.ajax({
			url:'fin_arq_apagar.php',
			type: 'post',
			data: jQuery('#frm_pesquisarArquivos').serialize(),
			beforeSend: function(){
				jQuery('#frame04').html('<center><img src="images/loading.gif" alt="" /></center>');
			},
			success:function(data){
				
			}
		});
	});
	
    /**
     * Acoes do icone excluir
     */
	$("body").delegate('#enviar-remessa','click',function(){
	       jQuery("#mensagem_alerta_arquivo").dialog({
	             title: "Enviar remessa",
	             resizable: false,
	             modal: true,
	             width: 500,
	             buttons: {
	                 "Sim": function() {
	                 jQuery( this ).dialog( "close" );
	                 var todos_autorizados = true;
	                 if(jQuery("input[name='ck[]']:checked").length == 0){
	                	alert('Ao menos um título precisa estar selecionado');
	                 }else{
	                	$("input[name='ck[]']:checked").each(function(){
	                		//items.push($(this).val());
	                		if($(this).attr('autorizado') == '0'){ 
	                			todos_autorizados = false; 
	                		}
	                		var check = $(this).is(":checked");
	                	});

	                	if(todos_autorizados){
	                	
                    		jQuery('#acao').val('gerarArquivoItau');
                    		jQuery.ajax({
                    			url: 'fin_arq_apagar.php',
                    			type: 'post',
                    			data: jQuery('#frm_pesquisar').serialize(),
                    		
                    		    contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
                    			beforeSend: function(){		
                    				jQuery('#frame04').html('<center><img src="images/loading.gif" alt="" /></center>');
                    				
                    			},
                    			success: function(data){
                    				jQuery('#frame04').val('');
                    				var resultado = jQuery.parseJSON(data);

                    				//jQuery('#arquivo_csv').html('');
                    			//	if(resultado.codigo == 0) {
                    					//jQuery('#download_csv').show();
                    					//jQuery('#gera_csv').hide();
                    					jQuery('#download_csv').show();
                    					jQuery('#download_csv').html(resultado.msg);
                    			//	}else{
                    					//jQuery('#gera_csv').hide();
                    					//jQuery('#arquivo_csv').attr("class", "mensagem erro").html(resultado.msg).showMessage();
                    			//	}
                    			}
                    			
                    		});
                    		
                    		
	                	}else{
	                		alert('Alguns títulos selecionados precisam ser autorizados')
	                	}
	               
	                 }


	                 },
	                 "Não": function() {
	                     jQuery( this ).dialog( "close" );
	                 }
	             }
	         });
    });
	

	
	$("body").delegate('#botaoautorizar','click',function(){
	    if(jQuery("input[name='ck[]']:checked").length == 0){
        	alert('Ao menos um título precisa estar selecionado');
         }else{
        		jQuery('#acao').val('AutorizarTitulos');
        		jQuery.ajax({
        			url: 'fin_arq_apagar.php',
        			type: 'post',
        			data: jQuery('#frm_pesquisar').serialize(),
        			contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
        			success: function(data){
        				var resultado = jQuery.parseJSON(data);

        				if(resultado.codigo == 0){
        					alert(resultado.msg);
        				}else{
        					alert(resultado.msg);
        				}
        		
        			}
        			
        		});
         }
	});
});


