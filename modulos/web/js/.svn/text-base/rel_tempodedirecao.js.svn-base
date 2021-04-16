jQuery(function(){
	/*
 	** Busca de cliente por autocomplete
 	**/
     jQuery("#nome_cliente").autocomplete({
        //source: 'rel_tempodedirecao.php?acao=recuperarCliente&nome_cliente=sascar' + jQuery(this).val(),
        source: function(request,response){
		jQuery.ajax({
		  type: "POST",
		  url:"rel_tempodedirecao.php",
		  data: jQuery('#frm').serialize()+'&acao=recuperarCliente',
		  success: response,
		  dataType: 'json'
		})
	},
	minLength: 3,
        response: function(event, ui) {

            var conteudoInput = jQuery(this).val();

            jQuery('#msg_alerta_autocomplete').fadeOut(function() {
                if (!ui.content.length) {
                    jQuery('#msg_alerta_autocomplete').html('Nenhum resultado encontrado com: ' + conteudoInput).fadeIn();
                }
            });

            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {
                    }
                }
            });

        },
        select: function( event, ui ) {

            jQuery("#clioid").val(ui.item.id);
            jQuery('#nome_cliente').val(ui.item.value);
        }
    });

    /**
    * Click do botao pesquisar
    */
	jQuery('#bt_pesquisar').click(function(){
		
        removeAlerta();
        
        /**
        * Limpa o campo CSV para garantir pois ja faz isso no submit do botao gerar_csv
        */
	if (diferencaEntreDatas($("#data_fim_pesquisa").val(), $("#data_inicio_pesquisa").val()) < 0){
            criaAlerta("Per�odo inicial n�o pode ser maior que o per�odo final."); 
            jQuery("#data_fim_pesquisa").addClass("inputError");
            return false;	
        }
		
		/**
		 * Limpa o campo CSV para garantir pois ja faz isso no submit do botao gerar_csv
		 */
		if (diferencaEntreDatas($("#data_fim_pesquisa").val(), $("#data_inicio_pesquisa").val()) < 0){
			criaAlerta("Data final menor que a data inicial."); 
			jQuery("#data_fim_pesquisa").addClass("inputError");
			return false;	
		}
		
		if(jQuery.trim(jQuery('#data_inicio_pesquisa').val()).length == 0 || jQuery.trim(jQuery('#data_fim_pesquisa').val()).length == 0){
			
            criaAlerta('Existem campos obrigat�rios n�o preenchidos.');
						
			if(jQuery.trim(jQuery('#data_inicio_pesquisa').val()).length == 0){
				jQuery('#data_inicio_pesquisa').css('background', 'rgb(255, 255, 192)');		
			}else{
				jQuery('#data_inicio_pesquisa').css('background', '#FFF');
			}
			
			if(jQuery.trim(jQuery('#data_fim_pesquisa').val()).length == 0){
				jQuery('#data_fim_pesquisa').css('background', 'rgb(255, 255, 192)');		
			}else{
				jQuery('#data_fim_pesquisa').css('background', '#FFF');
			}	
			
		}else{
						
			removeAlerta();
			
			jQuery('#data_inicio_pesquisa').css('background', '#FFF'); 
			jQuery('#data_fim_pesquisa').css('background', '#FFF');
			
			if(jQuery('#gerar_csv').val()!='t'){
				jQuery('#div_pesquisar').show();
				jQuery('#bt_pesquisar').hide();
				
				
				jQuery('#bt_cancelar').fadeIn();
			}
			xhr = jQuery.ajax({
			                async: true,
					url: 'rel_tempodedirecao.php',
					type: 'POST',
					data: jQuery('#frm').serialize()+'&acao=pesquisar',
					beforeSend: function(){
						if(jQuery('#gerar_csv').val()!='t'){	
							jQuery('.resultado_pesquisa').hide();
            						jQuery('#resultado_cabecalho_fixo').hide();
							jQuery('#resultado_bloco_mensagens').hide();
							jQuery('.processando').show();	
						}else{
							jQuery('.processando_csv').show();
						}
						
					},
					success: function(data){
						try{
						// Transforma a string em objeto JSON
						var resultado = jQuery.parseJSON(data);

                        relatorio(resultado);
                        
					
                    }catch(e){
					
                        regexp = /{.*}/;
                        teste = regexp.exec(data);
                        codigo = teste[0].split('"');
                        jQuery("#div_msg_pesquisar").html('Erro: ' + resultado.message);
                        jQuery('#div_msg_pesquisar').show();
                        jQuery('#bt_cancelar').hide();
                        jQuery('#bt_pesquisar').fadeIn(1000);
                        jQuery('.processando').hide();						
                        
                    }		
                }
            });          
            
        }
        
    })
	
    

    /**
    * Clique do botao cancelar pesquisa
    */
    jQuery('#bt_cancelar').click(function(){
        
        xhr.abort();
        jQuery('#bt_cancelar').hide();
        jQuery('.processando').hide();
        jQuery('#div_pesquisar').hide();
        jQuery('#bt_pesquisar').show();
		
    })
	
	
    /**
    * Clique do bot�o Gerar CSV
    * */
    jQuery('body').delegate('#bt_gerar_csv', 'click', function(){
		
        jQuery('#gerar_csv').val('t');
        jQuery('#bt_pesquisar').click();
		
    })

    
    function relatorio(resultado){

        tr='';
						
        tr += '<thead><tr class="tableSubTitulo">';
			        	tr += '<th colspan="10"><center><h2>Resultado da Pesquisa</h2></center></th>';
                        tr += '</tr>';
                        
		tr += '<tr class="tableTituloColunas tab_registro">';
			    
			        	// Dados do Hist�rico
        tr += '<th width="10%" align="center"><h3>Data de Envio</h3></th>';
        tr += '<th width="10%" align="center"><h3>Data de Chegada</h3></th>';
        tr += '<th width="10%" align="center"><h3>Placa</h3></th>';
        tr += '<th width="10%" align="center"><h3>Macro</h3></th>';
        tr += '<th width="10%" align="center"><h3>Parametriza&ccedil;&atilde;o</h3></th>';
        tr += '<th width="10%" align="center"><h3>Tipo</h3></th>';
        tr += '<th width="10%" align="center"><h3>Motorista</h3></th>';
        tr += '<th width="10%" align="center"><h3>Login</h3></th>';
        tr += '<th width="10%" align="center"><h3>Cliente</h3></th>';
        tr += '<th width="10%" align="center"><h3>Detalhes da Macro</h3></th>';
			        	tr += '</tr></thead><tbody>';
        
        if(resultado != null){
                        
            jQuery.each(resultado.pesquisa, function(i, historico){          

		        data_envio = (!Object.is(historico.data_envio,undefined) && !Object.is(historico.data_envio,null)) ? historico.data_envio : '';
                data_chegada = (!Object.is(historico.data_chegada,undefined) && !Object.is(historico.data_chegada,null))       ? historico.data_chegada : '';
                veiplaca = (!Object.is(historico.veiplaca,undefined) && !Object.is(historico.veiplaca,null))           ? historico.veiplaca : '';
                mttdnome = (!Object.is(historico.mttdnome,undefined) && !Object.is(historico.mttdnome,null))           ? historico.mttdnome : '';
                tmttdescricao = (!Object.is(historico.tmttdescricao,undefined) && !Object.is(historico.tmttdescricao,null)) ? historico.tmttdescricao : '';
                tipo = (!Object.is(historico.tipo,undefined) && !Object.is(historico.tipo,null))       ? historico.tipo : '';
                motonome = (!Object.is(historico.motonome,undefined) && !Object.is(historico.motonome,null))           ? historico.motonome : '';
                mentmotologin = (!Object.is(historico.mentmotologin,undefined) && !Object.is(historico.mentmotologin,null))            ? historico.mentmotologin : '';
                clinome = (!Object.is(historico.clinome,undefined) && !Object.is(historico.clinome,null))      ? historico.clinome : '';
                mentmensagem = (!Object.is(historico.mentmensagem,undefined) && !Object.is(historico.mentmensagem,null))       ? historico.mentmensagem : '';
 

                tr += '<tr class="tr_resultado_ajax">';

                tr += '<td align="left">'+data_envio+'</td>';
                tr += '<td align="left">'+data_chegada+'</td>';                
                tr += '<td align="left">'+veiplaca+'</td>';
                tr += '<td align="left">'+mttdnome+'</td>';
                tr += '<td align="left">'+tmttdescricao+'</td>';
                tr += '<td align="left">'+tipo+'</td>';
                tr += '<td align="left">'+motonome+'</td>';
                tr += '<td align="left">'+mentmotologin+'</td>';
                tr += '<td align="left">'+clinome+'</td>';
                tr += '<td align="left">'+mentmensagem+'</td>';

                tr += '</tr>';
															
			})
            
            mensagem_limite_resultados = (resultado.pesquisa.length == jQuery('#limite_resultados').val()) ? ' *Limite de '+jQuery('#limite_resultados').val() +' resultados por pesquisa atingido' : '';
                
                
            mensagem_resgistro = (resultado.pesquisa.length > 1) ? 'A pesquisa retornou '+resultado.pesquisa.length+' resultados. ' + mensagem_limite_resultados : (resultado.pesquisa.length == 1) ? 'A pesquisa retornou 1 resultado. ' + mensagem_limite_resultados : 'Nenhum registro encontrado';
            }else{
                mensagem_resgistro = 'Nenhum registro encontrado';
            }
			        	
	
	        mensagem_resgistro_div = '<table class="tableMoldura resultado_pesquisa"><tr class="tableRodapeModelo3"><td align="center" id="total_registros" class="total"> <h3>'+mensagem_resgistro+' </h3></td></tr>';
					
            if(resultado != null){
                
                mensagem_resgistro_div += '<tr class="tableRodapeModelo3" style="height:23px;">';
		        mensagem_resgistro_div += '<td align="center">';
                mensagem_resgistro_div += '<input type="button" name="bt_gerar_csv" id="bt_gerar_csv" value="Exportar para CSV" class="botao" style="width:120px;">';
                mensagem_resgistro_div += '</td>';
                mensagem_resgistro_div += '</tr>';
            }
						
            /**
             *  GERAR CSV
             */
            if(jQuery('#gerar_csv').val()=='t'){
                
                csv='';                
                // Cabecalho
                csv += "Data de Envio; Data de Chegada; Placa; Macro; Parametrizacao; Tipo; Motorista; Login; Cliente; Detalhes da Macro\n";
                
				jQuery.each(resultado.pesquisa, function(i, historico){                    
                    data_envio = (!Object.is(historico.data_envio,undefined) && !Object.is(historico.data_envio,null))  	   ? historico.data_envio+';'             : ';';
                    data_chegada = (!Object.is(historico.data_chegada,undefined) && !Object.is(historico.data_chegada,null))  	   ? historico.data_chegada+';'             : ';';
                    veiplaca = (!Object.is(historico.veiplaca,undefined) && !Object.is(historico.veiplaca,null))  	   ? historico.veiplaca+';'             : ';';
                    mttdnome = (!Object.is(historico.mttdnome,undefined) && !Object.is(historico.mttdnome,null))  	   ? historico.mttdnome+';'             : ';';
                    tmttdescricao = (!Object.is(historico.tmttdescricao,undefined) && !Object.is(historico.tmttdescricao,null))  	   ? historico.tmttdescricao+';'             : ';';
                    tipo = (!Object.is(historico.tipo,undefined) && !Object.is(historico.tipo,null))  	   ? historico.tipo+';'             : ';';
                    motonome = (!Object.is(historico.motonome,undefined) && !Object.is(historico.motonome,null))  	   ? historico.motonome+';'             : ';';
                    mentmotologin = (!Object.is(historico.mentmotologin,undefined) && !Object.is(historico.mentmotologin,null))  	   ? historico.mentmotologin+';'             : ';';
                    clinome = (!Object.is(historico.clinome,undefined) && !Object.is(historico.clinome,null))  	   ? historico.clinome+';'             : ';';
                    mentmensagem = (!Object.is(historico.mentmensagem,undefined) && !Object.is(historico.mentmensagem,null))  	   ? historico.mentmensagem+';'             : ';';

                    csv += data_envio;
                    csv += data_chegada;
                    csv += veiplaca;
                    csv += mttdnome;
                    csv += tmttdescricao;
                    csv += tipo;
                    csv += motonome;
                    csv += mentmotologin;
                    csv += clinome; 
                    csv += mentmensagem;
                    csv += "\n";
                
                })							
                
                jQuery('#exportdata').val(csv);
                
                jQuery('#acao').val('gerarCsv');
                
                $("#frm").submit();
                
                jQuery('#gerar_csv').val('');
                
                jQuery('.processando_csv').hide();
                
            } // End GERAR CSV
						
            jQuery('#div_msg_pesquisar').hide();
            
            jQuery('.processando').hide();
            
            jQuery('#bt_pesquisar').fadeIn(1000);	
            
            jQuery('#div_pesquisar').hide();
            
            jQuery('#csv_hidden').html(tr);
            
            jQuery('#csv_hidden').show();
            
            jQuery('.resultado_pesquisa').html(tr+'</tbody>');	
            
            jQuery('.resultado_pesquisa').fadeIn();
            
	    jQuery('#resultado_bloco_mensagens').html(mensagem_resgistro_div);

            jQuery('#resultado_bloco_mensagens').fadeIn();

	    jQuery('#resultado_cabecalho_fixo').fadeIn();                                                        

            jQuery('#bt_cancelar').hide();
            
            /*
                * Zebrando a tabela
                */
            jQuery('tr.tr_resultado_ajax:even').addClass('tdc');
            jQuery('tr.tr_resultado_ajax:odd').addClass('tde');
        
        }
					
})
