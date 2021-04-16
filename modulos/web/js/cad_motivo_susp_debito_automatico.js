function pesquisar(){
    
    jQuery('.resultado_pesquisa').hide();

        removeAlerta();

        jQuery.ajax({
            url: 'cad_motivo_susp_debito_automatico.php',
            type: 'POST',
            data: jQuery('#form').serialize()+'&acao=pesquisar',
            beforeSend: function(){
                jQuery('.processando').show();
                jQuery('#div_msg_pesquisar').hide();
            },
            success: function(data){


                try{

                    // Transforma a string em objeto JSON
                    var resultado = jQuery.parseJSON(data);

                    var tr = '';

                    tr += '<tr class="tableSubTitulo">';
                    tr += '<td colspan="2"><h2>Resultado da Pesquisa</h2></td>';
                    tr += '</tr>';
                    tr += '<tr class="tableTituloColunas tab_registro">';
                    tr += '<td align="center" width="5%"><h3>Excluir<h3></td>';
                    tr += '<td align="left"><h3>Descrição</h3></td>';
                    tr += '</tr>';

                    if(resultado.error){
                        throw resultado.message;
                    }else{

                        if(resultado.length > 0){

                            jQuery.each(resultado, function(i, motivo){

                                tr += '<tr class="tr_resultado_ajax">';
                                tr += '<td align="center"><b></b><a href="javascript: void(null);" id="'+motivo.id+'" class="acao_excluir"><b>[</b><img src="images/del.gif" align="absmiddle" width="13" height="12" alt="Remover" title="Excluir"></a><b>]</b></td>';
                                tr += '<td align="left">'+motivo.descricao+'</td>';						
                                tr += '</tr>';
                            })

                        }

                        var mensagem_resgistro = (resultado.length > 1) ? 'A pesquisa retornou '+resultado.length+' registros' : (resultado.length == 1) ? 'A pesquisa retornou 1 registro' : 'Nenhum registro encontrado';

                        total_registros = resultado.length;

                        tr += '<tr class="tableRodapeModelo3">';
                        tr += '<td align="center" colspan="2" id="total_registros" class="total"><h3>'+mensagem_resgistro+'</h3></td>';
                        tr += '</tr>';

                        jQuery('.processando').hide();

                        // Populando a tabela com as TRs
                        jQuery('.resultado_pesquisa').html(tr);


                        jQuery('.resultado_pesquisa').fadeIn();

                        jQuery('tr.tr_resultado_ajax:even').addClass('tdc');
                        jQuery('tr.tr_resultado_ajax:odd').addClass('tde');	
                    }


                }catch(e){


                    regexp = /{.*}/;

                    teste = regexp.exec(data);

                    codigo = teste[0].split('"');

                    jQuery("#div_msg_pesquisar").html('Erro de processamento.');

                    jQuery('#div_msg_pesquisar').show();
                    jQuery('.processando').hide();
                }


            }

        });
    
}

jQuery(function(){
	
    removeAlerta();
    
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
		
        jQuery('#form input[type="text"]').val('');
		
        jQuery('#descricao_cadastro').css('background', '#FFF');
		
        jQuery('.resultado_pesquisa').hide();
		
        jQuery('#div_msg_cadastro').hide();
		
        jQuery('.div_acao_cadastrar').show();
		
        jQuery('.div_acao_pesquisar').hide();
		
    })
	
    /**
	 * Click do botão Cadastrar
	 */
    jQuery('#bt_cadastrar').click(function(){
		
        removeAlerta();
		
        jQuery('#div_msg_cadastro').hide();
		
        jQuery('#descricao_cadastro').css('background', '#FFF');
		
        if(jQuery.trim(jQuery('#descricao_cadastro').val()).length == 0){
            criaAlerta('O campo Descrição é obrigatório.');
            jQuery('#descricao_cadastro').css('background', 'rgb(255, 255, 192)');	
            return false;
        }
		
        jQuery.ajax({
            url: 'cad_motivo_susp_debito_automatico.php',
            type: 'POST',
            data: {
                descricao: jQuery('#descricao_cadastro').val(), 
                acao: 'cadastrar'
            },
            beforeSend: function(){
                jQuery('.processando').show();
            },
            success: function(data){
				
				
                try{
                    // Transforma a string em objeto JSON
                    var resultado = jQuery.parseJSON(data);	
				
                    if(resultado.error){
					
                        throw resultado.message;
					
                    }else{
                        jQuery('.processando').hide();
                        jQuery('.div_acao_pesquisar').show();
                        jQuery('.div_acao_cadastrar').hide();
                        jQuery('.resultado_pesquisa').show();
					
                        jQuery('#form input[type="text"]').val('');
			
                        // Recarrega a pesquisa
                        pesquisar();
                        
                        jQuery('#div_msg_pesquisar').html('Registro cadastrado com sucesso.');
                        jQuery('#div_msg_pesquisar').show();
					
                    }
				
				
                }catch(e){
					
					
                    regexp = /{.*}/;
		    		
                    teste = regexp.exec(data);
		    		            			
                    codigo = teste[0].split('"');
		     	
                    switch(codigo[5]){
                        case '002' :
                            jQuery("#div_msg_cadastro").html('Já existe um motivo cadastrado com essa descrição.');
                            break;
                        default:
                            jQuery("#div_msg_cadastro").html('Erro de processamento.');
                            break;
                    }
		    		
                    jQuery('#div_msg_cadastro').show();
                    jQuery('.processando').hide();
					
                }
            }
        })
		
    })
	
    /**
	 * Click do botão Voltar
	 */
    jQuery('#bt_voltar').click(function(){
		
        removeAlerta();
		
        jQuery('#form input[type="text"]').val('');
	
        pesquisar();
        
        jQuery('.div_acao_cadastrar').hide();
        jQuery('.resultado_pesquisa').hide();
        jQuery('#div_msg_cadastro').show();
        jQuery('#div_msg_pesquisar').hide();
        jQuery('.div_acao_pesquisar').show();
		
    })
	
	
    /**
	 *  Clique do botão (link) excluir
	 */
    jQuery('body').delegate('.acao_excluir', 'click', function(){
		
        jQuery('#div_msg_pesquisar').hide();
		
        var link = jQuery(this);
		
        if(confirm('Deseja realmente excluir o registro?')){
		
            jQuery.ajax({
                url: 'cad_motivo_susp_debito_automatico.php',
                type: 'POST',
                data: {
                    id: link.attr('id'), 
                    acao: 'excluir'
                },
                beforeSend: function(){
                    jQuery('.processando_excluir').show();
                },
                success: function(data){
					
					
                    try{
                        // Transforma a string em objeto JSON
                        var resultado = jQuery.parseJSON(data);	
					
                        if(resultado.error){
						
                            throw resultado.message;
						
                        }else{
						
                            jQuery('#div_msg_pesquisar').html('Registro excluído com sucesso.');
                            jQuery('#div_msg_pesquisar').show();
                            
                            jQuery('#descricao_pesquisa').val('');
                            pesquisar();
                            
                            link.parent().parent().remove();
                            total_registros--;
                            var mensagem_resgistro = (total_registros > 1) ? 'A pesquisa retornou '+total_registros+' registros' : (total_registros == 1) ? 'A pesquisa retornou 1 registro' : 'Nenhum registro encontrado';
						
                            jQuery('#total_registros').html('<h3>'+mensagem_resgistro+'</h3>');
						
                            jQuery('.processando_excluir').hide();
						
                            jQuery('tr.tr_resultado_ajax').removeClass('tdc');
                            jQuery('tr.tr_resultado_ajax').removeClass('tde');	
						
                            jQuery('tr.tr_resultado_ajax:even').addClass('tdc');
                            jQuery('tr.tr_resultado_ajax:odd').addClass('tde');	
						
                        }
					
					
                    }catch(e){
					
                        regexp = /{.*}/;
		    		
                        teste = regexp.exec(data);
		    		            			
                        codigo = teste[0].split('"');
		     	
                        jQuery("#div_msg_pesquisar").html('Erro de processamento.');
		    		
                        jQuery('#div_msg_pesquisar').show();
                        jQuery('.processando').hide();
					
                    }
                }
            });			
			
        }
		
    })    
    
    // Carrega a pesquisa ao carregar a tela
    pesquisar();
    
})