function gerarResumo() {
	//Remove os alertas da tela, caso existam.
	jQuery(".input_error").removeClass(".input_error");
	removeAlerta();
	jQuery('#div_msg').html('');
	
	jQuery('#loading').show();
	
	if (jQuery('#frm_data').val()=="") {
		jQuery('#div_msg').html('Campos obrigatórios não preenchidos.');
		jQuery('#loading').html('');
		return;
	}
	
	jQuery("#obroid_aux").val('');
	jQuery("#acaoForm").val('prepararResumo');
	
	jQuery('#busca_obrigacoes').submit();
}

function faturar () {
	//Remove os alertas da tela, caso existam.
	jQuery(".input_error").removeClass(".input_error");
	removeAlerta();
	jQuery('#div_msg').html('');
	
	if (!jQuery('.chk_resumo_faturamento:checked').length) {
		alert('É obrigatório selecionar pelo menos uma obrigação financeira para faturar.');
	 	
		return;
	} else {
		jQuery('#div_msg').html('');
	}
	
	var lista_obroids = "";
	jQuery(".chk_resumo_faturamento:checked").each(function(){
		lista_obroids += jQuery(this).val() + ",";
	});
	lista_obroids = lista_obroids.substring(0,lista_obroids.length-1);
	
	if (confirm('Tem certeza que deseja faturar?')) {
		jQuery("#obroid_aux").val(lista_obroids);
		jQuery("#acaoForm").val('prepararFaturamento');
		jQuery('#busca_obrigacoes').submit();
	}
	
	return;
}

function alteraCampoCliente(nome) {
	jQuery("#lista_pesquisar_clientes").html('');
	jQuery("#lista_pesquisar_clientes").hide();
	jQuery("#frm_cliente").val(nome);
}

jQuery(document).ready(function() {
	
	// Zebra a tabela
    jQuery('.tr_resultado_submit:odd').addClass('tde');
    jQuery('.tr_resultado_submit:even').addClass('tdc');
	
	// Seta máscara de datas
	jQuery("#frm_data").setMask('99/99/9999');
	
	// Seta máscara para campo de CPF/CNPJ
	if (jQuery("#frm_tipo_f").attr('checked')=='checked') {
		jQuery("#frm_doc").setMask('999.999.999-99');
	} else {
		jQuery("#frm_doc").setMask('99.999.999/9999-99');
	}
	jQuery("#frm_contrato").setMask('9999999999');
	jQuery("#frm_placa").setMask('********');
	
	// Seta eventos dos objetos
    jQuery('.frm_tipo').click(function(){
    	
    	jQuery("#frm_doc").val('');
    	
    	var frm_tipo = jQuery(this).val();
    	
    	switch (frm_tipo) {
			case 'F':
				jQuery("#frm_doc").setMask('999.999.999-99');
			break;
			case 'J':
			default:
				jQuery("#frm_doc").setMask('999.999.999/9999-99');
			break;
		}
    });
	

	jQuery('#btn_limpar_resumo').click(function(){
    	
    	//Remove os alertas da tela, caso existam.
        jQuery(".input_error").removeClass(".input_error");
        removeAlerta();
        jQuery('#div_msg').html('');
    	jQuery('#div_msg2').html('');
        
		if (!confirm('Deseja realmente limpar o resumo?')) {
			jQuery('#loading').html('');
			return;
		}	
		jQuery.ajax({
			async: false,
			url: 'fin_faturamento_unificado.php',
			type: 'post',
			data: jQuery('#busca_obrigacoes').serialize()+'&acao=limparResumo',
			beforeSend: function(){
				//jQuery('#loading').html('<img src="images/loading.gif" alt="" />'); 
				jQuery('input').attr('disabled', 'disabled');
				       	
			},
			success: function(data){
				jQuery('input').attr('disabled', false); 
				jQuery('input').removeAttr('disabled');
				try {					
					var resultado = jQuery.parseJSON(data);
					//console.log(resultado.msg);
    				jQuery('#div_msg').html(resultado.msg);	
    				jQuery('#result_resumo_faturamento').html("");	
    				
					
				} catch (e) {
					alert(e.message);
				}
			}
		});
    });
    
    jQuery('body').delegate('#marcar_todos', 'click', function(){
    	
    	if (jQuery(this).attr("checked")) {
    		jQuery(".chk_resumo_faturamento").each(function(){
        		jQuery(this).attr('checked', 'checked');
        	});
    	} else {
    		jQuery(".chk_resumo_faturamento").each(function(){
        		jQuery(this).removeAttr('checked');
        	});
    	}
    	
    });

    
    jQuery('#btn_gerar_resumo').click(function(){
		//Remove os alertas da tela, caso existam.
		jQuery(".input_error").removeClass(".input_error");
		removeAlerta();
		jQuery('#div_msg').html('');
		jQuery('#div_msg2').html('');
		
		if (jQuery('#frm_data').val()=="") {
			jQuery('#div_msg').html('Campos obrigatórios não preenchidos');
			jQuery('#loading').html('');
			return;
		}
		
		var gerar = true;
		jQuery.ajax({
			async: false,
			url: 'fin_faturamento_unificado.php',
			type: 'post',
			data: jQuery('#busca_obrigacoes').serialize()+'&acao=getIGPM',
			beforeSend: function(){
				//jQuery('#loading').html('<img src="images/loading.gif" alt="" />');        	
			},
			success: function(data){
				try {					
					
					var resultado = jQuery.parseJSON(data);
	
					if (resultado.code == 1) {
						if (confirm(resultado.msg)) {
							gerar = true;   	 		
						} else {
							gerar = false;
							return;
						}
					} else {
						gerar = true;
						//gerarResumo();
					}
					
				} catch (e) {
					gerar = false;
					alert(e.message);
				}
			}
		});
		
        if(gerar){
	        jQuery.ajax({
				async: false,
	            url: 'fin_faturamento_unificado.php',
	            type: 'post',
	            data: jQuery('#busca_obrigacoes').serialize()+'&acao=getINPC',
	            beforeSend: function(){
	                //jQuery('#loading').html('<img src="images/loading.gif" alt="" />');               
	            },
	            success: function(data){
	                try {
	
	                    var resultado = jQuery.parseJSON(data);
	
	                    if (resultado.code == 1) {
	                        if (confirm(resultado.msg)) {
	        					gerar = true;
	                            //gerarResumo();                                             
	                        } else {
	        					gerar = false;
	                            return;
	                        }
	                    } else {
	    					gerar = true;
	                        //gerarResumo();
	                    }
	
	                } catch (e) {
						gerar = false;
	                    alert(e.message);
	                }
	            }
	        });
        }
        
        if(gerar){
        	gerarResumo();
        }
    	
    });
	
	jQuery('#btn_consultar_resumo').click(function(){
		jQuery('#arquivo').hide();
    	jQuery('.resumo_faturamento').hide();
    	
    	//Remove os alertas da tela, caso existam.
        jQuery(".input_error").removeClass(".input_error");
        removeAlerta();
        jQuery('#div_msg').html('');
    	jQuery('#div_msg2').html('');
        
        if (jQuery('#frm_data').val()=="") {
        	jQuery('#div_msg').html('Campos obrigatórios não preenchidos');
        	jQuery('#loading').html('');
        	return;
        }
		
		jQuery("#obroid_aux").val('');
		jQuery("#acaoForm").val('consultarResumo');
		jQuery('#busca_obrigacoes').submit();
    });
    
    jQuery('body').delegate('#marcar_todos', 'click', function(){
    	
    	if (jQuery(this).attr("checked")) {
    		jQuery(".chk_resumo_faturamento").each(function(){
        		jQuery(this).attr('checked', 'checked');
        	});
    	} else {
    		jQuery(".chk_resumo_faturamento").each(function(){
        		jQuery(this).removeAttr('checked');
        	});
    	}
    	
    });
    
    jQuery('#btn_pesquisar_cliente').click(function(){
    	
    	jQuery.ajax({
            url: 'fin_faturamento_unificado.php',
            type: 'post',
            data: jQuery('#busca_obrigacoes').serialize()+'&acao=pesquisarCliente',
            beforeSend: function(){
            	jQuery('#loading_pesquisar_clientes').html('<img src="images/progress.gif" alt="" />'); 
            	jQuery('#div_msg').html('');
            	jQuery("#lista_pesquisar_clientes").show();
            },
            success: function(data){
            	try {
    	        	//console.log(data);   
            		var content = '<select size="3" style="width:500px" onchange="alteraCampoCliente(this.value)">';            		
    	        	var resultado = jQuery.parseJSON(data);  
    	        	 
    	        	if (resultado.erro == 0) {
    	        		
    	        		if (resultado.retorno.length) {
	    	        		jQuery(resultado.retorno).each(function(i, cliente){
	    	        			var _descricao = (cliente.descricao == null || cliente.descricao == undefined) ? "" : cliente.descricao;
	    	        			if (_descricao != "") {
	    	        				content += '<option value="'+_descricao+'">'+_descricao+'</option>';
	    	        			}
	    	        		});
	    	        		
	    	        		content += '</select>';
	    	        		
	    	        		jQuery("#lista_pesquisar_clientes").html(content);
    	        		} else {
    	        			//jQuery('#div_msg').html('Nenhum cliente encontrado.');
    	        		}
    	        		 
    	        	} else {
    	        		 jQuery('#div_msg').html(resultado.msg);
    	        	}   	            
    	            
            	} catch (e) {
            		jQuery('#div_msg').html(e.message);
            	}
            },
            complete: function() {
            	jQuery('#loading_pesquisar_clientes').html('');
            }
        });	
    	
    	
    });
	
    jQuery('body').delegate('#btn_gerar_faturamento', 'click', function(){
        faturar();
    }); 
    
    jQuery('body').delegate('#btn_relatorio_prefaturamento_planilha', 'click', function(){
    	var selecionados = jQuery(".chk_resumo_faturamento:checked").length * 1;
		
		if (selecionados == 0) {
			alert('É obrigatório selecionar pelo menos uma obrigação financeira.');
			jQuery('#btn_relatorio_prefaturamento').removeAttr('disabled');
			jQuery('#loading').html('');
			return;
		} else {
			jQuery('#div_msg').html('');
			jQuery(".input_error").removeClass(".input_error");
			removeAlerta();
			
			var total_contratos = 0;
			jQuery(".chk_resumo_faturamento:checked").each(function(){
				total_contratos += (jQuery("#qtd_con_"+jQuery(this).val()).val() * 1);
			});
			
			var lista_obroids = "";
			jQuery(".chk_resumo_faturamento:checked").each(function(){
				lista_obroids += jQuery(this).val() + ",";
			});
			lista_obroids = lista_obroids.substring(0,lista_obroids.length-1);
			jQuery("#obroid_aux").val(lista_obroids);
			jQuery("#acaoForm").val('gerarRelatorioPreFaturamentoCSV');
			jQuery('#busca_obrigacoes').submit();
		}
    });    
    
    jQuery('body').delegate('#btn_relatorio_prefaturamento', 'click', function(){
		var selecionados = jQuery(".chk_resumo_faturamento:checked").length * 1;
		
		if (selecionados == 0) {
			alert('É obrigatório selecionar pelo menos uma obrigação financeira.');
			jQuery('#loading').html('');
			return;
		} else {
			jQuery('#div_msg').html('');
			jQuery(".input_error").removeClass(".input_error");
			removeAlerta();
			
			var total_contratos = 0;
			jQuery(".chk_resumo_faturamento:checked").each(function(){
				total_contratos += (jQuery("#qtd_con_"+jQuery(this).val()).val() * 1);
			});
			
			if (total_contratos > 2000 && tipo_listagem=='html') {
				alert('O limite máximo de linhas para apresentar em tela são de 2.000. Utilize o relatório em CSV.');
				jQuery('#loading').html('');
				return;
			} else{
				var lista_obroids = "";
				jQuery(".chk_resumo_faturamento:checked").each(function(){
					lista_obroids += jQuery(this).val() + ",";
				});
				lista_obroids = lista_obroids.substring(0,lista_obroids.length-1);
				jQuery("#obroid_aux").val(lista_obroids);
				jQuery("#acaoForm").val('gerarRelatorioPreFaturamento');
				jQuery('#busca_obrigacoes').submit();
			}
		}
    }); 
    
    jQuery('body').delegate('#btn_gerar_xls', 'click', function(){
		jQuery("#acaoForm").val('gerarRelatorioPreFaturamentoCSV2');
		jQuery('#busca_obrigacoes').submit();
    });
    
    jQuery('#btn_gerar_relatorio_pendencias_CSV').click(function(){
    	jQuery("#acaoForm").val('gerarRelatorioPendenciasCSV');
		jQuery('#busca_obrigacoes').submit();
    });
    
	jQuery('body').delegate('#btn_voltar', 'click', function(){
		jQuery("#obroid_aux").val('');
        jQuery("#acaoForm").val('verificarPendencias');
		jQuery('#busca_obrigacoes').submit();
    });
    
    jQuery('#btn_pendencias_faturamento').click(function(){
        jQuery("#obroid_aux").val('');
        
        if (jQuery('#frm_data').val()=="") {
        	jQuery('#div_msg').html('Campos obrigatórios não preenchidos');
        	return;
        }
            
    });

    /*
     * Botao Parar Resumo
     */
     jQuery('#btn_parar_resumo').click(function(){

     	jQuery('#loading').html('<img src="images/loading.gif" alt="" />').hide();
    
     	if (!confirm('Deseja realmente parar a geração do resumo?')) {
     		return;
     	}

     	jQuery('#loading').show();

     	jQuery('#acaoForm').val('pararResumo');
     	jQuery('#busca_obrigacoes').submit();

     })
	
});