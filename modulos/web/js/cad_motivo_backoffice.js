function pesquisar(id){
    
    jQuery('.resultado_pesquisa').hide();

        jQuery.ajax({
            url: 'cad_motivo_backoffice.php',
            type: 'POST',
            data: jQuery('#form').serialize()+'&acao=pesquisar',
            beforeSend: function(){
                jQuery('.processando_pesquisa').show();
                jQuery('.nenhum_registro_encontrado').hide();
                jQuery('.alerta, .sucesso, .erro').hide();
                jQuery('#div_msg_pesquisar').hide();                
            },
            success: function(data){


                try{

                    // Transforma a string em objeto JSON
                    var resultado = jQuery.parseJSON(data);

                    if(resultado.error){
                        throw resultado.message;
                    }else{

                        if(resultado.length > 0){
                        	
                        	var tr = '';
                            
                            tr += '<div class="bloco_titulo">Resultado da Pesquisa</div>';
                            	tr += '<div class="bloco_conteudo">';
                            		tr += '<div class="listagem">';
                            			tr += '<table>';
                        					tr += '<thead>';
                        						tr += '<tr>';
                        							tr += '<th class="maior">Motivo</th>';
                        							tr += '<th class="menor">Ação</th>';
                        						tr += '</tr>';
                        					tr += '</thead>';
                        				tr += '<tbody>';

                            jQuery.each(resultado, function(i, motivo){
                            	var zebra = 'par';
                            	var pastaIconeZebra = 'tf2';
                            	if (i%2==0) {
                            		zebra = 'impar';
                            		pastaIconeZebra = 't2';
                            	}
                                tr += '<tr class="tr_resultado_ajax '+zebra+'">';
	                                tr += '<td align="left">'+motivo.descricao+'</td>';
	                                tr += '<td class="centro">';
		                                tr += '<b></b><a style="border: none !important;" href="javascript: void(null);" id-descricao="'+motivo.id+'" descricao="'+motivo.descricao+'" class="acao_editar"><img src="images/edit.png" align="absmiddle" width="18" height="18" alt="Editar" title="Editar"></a>';
		                                tr += '<b></b><a style="border: none !important;" href="javascript: void(null);" id="'+motivo.id+'" class="acao_excluir"><img src="images/icones/'+pastaIconeZebra+'/error.jpg" align="absmiddle" width="18" height="18" alt="Excluir" title="Excluir"></a>';
	                                tr += '</td>';	
                                tr += '</tr>';
                            })
                            
                            var mensagem_resgistro = (resultado.length > 1) ? resultado.length+' registros encontrados.' : (resultado.length == 1) ? '1 registro encontrado.' : 'Nenhum Registro Encontrado.';

                            total_registros = resultado.length;
                            		tr += '</tbody>';
                            		tr += '<tfoot>';	
                            			tr += '<tr>';
                            				tr += '<td colspan="2" id="total_registros">'+mensagem_resgistro+'</td>';
                        				tr += '</tr>';
                    				tr += '</tfoot>';
                				tr += '</table>';
	                            tr += '</div>';
	                        tr += '</div>';
	
	                        // Populando a tabela com as TRs
	                        jQuery('.resultado_pesquisa').html(tr);
	
	
	                        jQuery('.resultado_pesquisa').fadeIn();
	                        
	                        if(id != null){
	                        	
		                        var tr_inserida = jQuery('a#'+id).parent().parent();
		                        
		                        jQuery(tr_inserida).effect('highlight', {}, 1000);
	                        }
	                        
	                        jQuery('tr.tr_resultado_ajax:even').addClass('tdc');
	                        jQuery('tr.tr_resultado_ajax:odd').addClass('tde');	

                        } else {
                        	jQuery('.resultado_pesquisa').hide();
                        	jQuery('.nenhum_registro_encontrado').show();
                        }

                        jQuery('.processando_pesquisa').hide();
                        
                    }


                }catch(e){
                	
                	jQuery('.msg_erro').html('Erro ao pesquisar registro.').show();
                    jQuery('.processando_pesquisa').hide();
                	
                }
                
            }

        });
    
}

/**
 * Atualizar combo
 */

function atualizarComboMotivo() {
	
	jQuery.ajax({
		url: 'cad_motivo_backoffice.php',
		type: 'POST',
		data: {
			'acao': 'atualizarCombo'
		},
		beforeSend: function(){
			jQuery('#carregar_combo').css({'display': 'inline'});
		},
		success: function(data){
			
			var response = jQuery.parseJSON(data);
			
			var combo = '<option value="">Escolha</option>';
			
			if (response.length > 0) {
				
				jQuery.each(response, function(i, motivo){
                	
					combo += '<option value="'+motivo.id+'">'+motivo.descricao+'</option>';
					
                })
			}
			
			jQuery('.comboMotivo').html(combo);
		},
		complete: function(){
			jQuery('#carregar_combo').hide();
		}
	})
}

jQuery(function(){
	
    removeAlerta();
    
    jQuery('.div_acao_cadastrar').hide();
	
    jQuery('.div_acao_pesquisar').show();
    
    /**
    * Click do botão Pesquisar
    */
    jQuery('#bt_pesquisar').click(function(){
	
        pesquisar();
        
    })
	
    /**
	 * Click do botão Novo
	 */
    jQuery('#bt_novo').click(function(){
		
        removeAlerta();
		
        jQuery('#motivo_cadastro').removeClass('erro');
        
        jQuery('#motivo_cadastro').val('');
        jQuery('#id_motivo').val('');
		
        jQuery('.resultado_pesquisa').hide();
		
        jQuery('.alerta, .sucesso, .erro').hide();
		
        jQuery('.div_acao_cadastrar').show();
		
        jQuery('.div_acao_pesquisar').hide();
		
    })
    
    /**
	 * Click do botão Novo
	 */
    jQuery('body').delegate('.acao_editar', 'click', function(){
		
        jQuery('#motivo_cadastro').removeClass('erro');
        
        jQuery('#motivo_cadastro').val(jQuery(this).attr('descricao'));
        jQuery('#id_motivo').val(jQuery(this).attr('id-descricao'));
		
        jQuery('.resultado_pesquisa').hide();
		
        jQuery('.alerta, .sucesso, .erro').hide();
		
        jQuery('.div_acao_cadastrar').show();
		
        jQuery('.div_acao_pesquisar').hide();
		
    })
	
    /**
	 * Click do botão Cadastrar
	 */
    jQuery('#bt_cadastrar').click(function(){
		
    	jQuery('.campos_obrigatorios').hide();
		
        jQuery('#div_msg_cadastro').hide();
		
        jQuery('#motivo_cadastro').removeClass('erro'); 
		
        if(jQuery.trim(jQuery('#motivo_cadastro').val()).length == 0){
        	jQuery('.campos_obrigatorios').show();
        	jQuery('#motivo_cadastro').addClass('erro');        	
            return false;
        }
		
        jQuery.ajax({
            url: 'cad_motivo_backoffice.php',
            type: 'POST',
            data: {
                descricao: jQuery('#motivo_cadastro').val(),
                id: jQuery('#id_motivo').val(),
                acao: 'cadastrar'
            },
            beforeSend: function(){
                jQuery('.processando_cadastro').show();
                jQuery('.alerta, .sucesso, .erro').hide();
            },
            success: function(data){
				
                try{
                    // Transforma a string em objeto JSON
                    var resultado = jQuery.parseJSON(data);	
				
                    if(resultado.error){
					
                        throw resultado.message;
					
                    }else{
                        
                        if (jQuery('#id_motivo').val() > 0) {
                        	jQuery('.registro_alterado').show();
                        } else {
                        	jQuery('#id_motivo').val(resultado.id);
                        	jQuery('.registro_incluido').show();
                        }                           
                    }
                    
                    jQuery('.processando_cadastro').hide();
				
                }catch(e){
					
                    regexp = /{.*}/;
		    		
                    teste = regexp.exec(data);
                    
                    
                    
                    if(teste != null){
		    		            			
	                    codigo = teste[0].split('"');
	                    
	                    var mensagem = "";
	                    
	                    switch(codigo[5]){
	                    	case '001': 
		                    	mensagem = "Erro ao inserir registro!";
		                    	classe = '.msg_erro';
		                    	break;
	                    
	                        case '002' :
	                        	
	                        	mensagem = 'O registro já existe no banco de dados!';
	                        	classe = '.msg_alerta';
	                            break;
	                            
	                        default:
	                        	mensagem = 'Erro de processamento.';
	                        	classe = '.msg_erro';
	                            break;
	                    }
	                    
                    } else {
                		mensagem = 'Erro de processamento.';
                		classe = '.msg_erro';
                	}
                    
                    jQuery(classe).html(mensagem);
                    jQuery(classe).show();
		    		
                    jQuery('.processando_cadastro').hide();
					
                }
            }
        })
		
    })
	
    /**
	 * Click do botão Voltar
	 */
    jQuery('#bt_voltar').click(function(){
    	
    	atualizarComboMotivo();
    	
		jQuery('#motivo_cadastro').removeClass('erro');
		
		jQuery('.resultado_pesquisa').hide();
		
		jQuery('.alerta, .sucesso, .erro').hide();
		
		jQuery('.div_acao_cadastrar').hide();
		
		jQuery('.div_acao_pesquisar').show();
    	
    	
    
    })
    
	
    /**
	 *  Clique do botão (link) excluir
	 */
    jQuery('body').delegate('.acao_excluir', 'click', function(){
		
        jQuery('#div_msg_pesquisar').hide();
		
        var link = jQuery(this);
        
        var tr = link.parent().parent();
		
        if(confirm('Deseja realmente excluir o Motivo?')){
		
            jQuery.ajax({
                url: 'cad_motivo_backoffice.php',
                type: 'POST',
                data: {
                    id: link.attr('id'), 
                    acao: 'excluir'
                },
                beforeSend: function(){
                    jQuery('.processando_excluir').show();
                    jQuery('.alerta, .sucesso, .erro').hide();
                },
                success: function(data){
					
					
                    try{
                        // Transforma a string em objeto JSON
                        var resultado = jQuery.parseJSON(data);	
					
                        if(resultado.error){
						
                            throw resultado.message;
						
                        }else{
						
                            jQuery('.registro_excluido').show();
                            
                            jQuery(tr).effect('highlight', {}, 1000, function() {
                            	
                            	tr.remove();
                            	
                            	jQuery('.impar').removeClass('impar');
                                jQuery('.par').removeClass('par');	
    						
                                jQuery('.tr_resultado_ajax:even').addClass('impar');
                                jQuery('.tr_resultado_ajax:odd').addClass('par');
                            });
                            
                            total_registros--;
                            var mensagem_resgistro = (total_registros > 1) ? total_registros+' registros encontrados.' : (total_registros == 1) ? '1 registro encontrado.' : 'Nenhum Registro Encontrado.';
						
                            if (total_registros > 0) {
                            	jQuery('#total_registros').html(mensagem_resgistro);
                            } else {
                            	jQuery('.resultado_pesquisa').hide();
                            }
                        
                            atualizarComboMotivo();
                            
                            jQuery('.processando_excluir').hide();
                        }
					
					
                    }catch(e){
					
                        regexp = /{.*}/;
		    		
                        teste = regexp.exec(data);
		    		            
                        if(teste != null){
                        	
	                        codigo = teste[0].split('"');
			     	
	                        var mensagem = "";
	                        
	                        switch(codigo[5]){
	                        	case '001': 
	                        		mensagem = "Erro ao excluir registro.";
	                        		classe = '.msg_erro';
	    	                    	break;
	                                
	                            default:
	                            	mensagem = 'Erro de processamento.';
	                            classe = '.msg_erro';
	                                break;
	                        }
	                        
                        } else {
                    		mensagem = 'Erro de processamento.';
                    		classe = '.msg_erro';
                    	}
                        
                        jQuery(classe).html(mensagem);
                        jQuery(classe).show();
	                    
                        jQuery('.processando_excluir').hide();
					
                    }
                }
            });			
			
        }
		
    })    
    
    
})