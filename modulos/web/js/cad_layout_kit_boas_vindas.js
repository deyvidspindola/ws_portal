jQuery(document).ready(function(){

	var vComboSubpropostas = '';
	
	jQuery('#comboPropostas').attr("disabled", "disabled");
	jQuery('#comboSubpropostas').attr("disabled", "disabled");
	jQuery('#comboContratos').attr("disabled", "disabled");
	jQuery('#comboLayout').attr("disabled", "disabled");
	jQuery('#comboClasse').attr("disabled", "disabled");
	jQuery('#comboServidor').attr("disabled", "disabled");
	jQuery('#inputAnexo').attr("disabled", "disabled");
	jQuery('#comboTipoLayout').attr("disabled", "disabled");
	
	jQuery("#comboConfiguracao").change(function(){
		
		var configEscolhida = jQuery("#comboConfiguracao").val();

		if(configEscolhida == '0'){
			// opção "novo" habilita combos 
			jQuery('#comboPropostas').removeAttr("disabled");
			jQuery('#comboContratos').removeAttr("disabled");
			jQuery('#comboLayout').removeAttr("disabled");	
			jQuery('#comboClasse').removeAttr("disabled");	
			jQuery('#comboServidor').removeAttr("disabled");	
			jQuery('#comboTipoLayout').removeAttr("disabled");
			
    		jQuery("#comboPropostas option[value='']").attr("selected","selected") ;
    		jQuery("#comboSubpropostas option[value='']").attr("selected","selected") ;
    		jQuery("#comboContratos option[value='']").attr("selected","selected") ;
    		jQuery("#comboLayout option[value='']").attr("selected","selected") ;
    		jQuery("#comboClasse option[value='']").attr("selected","selected") ;
    		jQuery("#comboServidor option[value='']").attr("selected","selected") ;
    		jQuery("#comboTipoLayout option[value='']").attr("selected","selected") ;
    		
    		jQuery("#inputAnexo").val("").hide();
    		jQuery("#selecionarAnexo").show().removeAttr("disabled");
    		jQuery("#listaAnexos").remove();
    		
    		jQuery("#bt_ver_anexo").hide();
        	jQuery('#htmlLayout').css("display", "none");
        	vComboSubpropostas = '';
			
		}else if(configEscolhida == ''){
			// opção "selecione" desabilita combos 
			jQuery('#comboPropostas').attr("disabled", "disabled");
			jQuery('#comboSubpropostas').attr("disabled", "disabled");
			jQuery('#comboContratos').attr("disabled", "disabled");
			jQuery('#comboLayout').attr("disabled", "disabled");			
			jQuery('#comboClasse').attr("disabled", "disabled");
			jQuery('#comboServidor').attr("disabled", "disabled");
			jQuery('#comboTipoLayout').attr("disabled", "disabled");
			
    		jQuery("#comboPropostas option[value='']").attr("selected","selected") ;
    		jQuery("#comboSubpropostas option[value='']").attr("selected","selected") ;
    		jQuery("#comboContratos option[value='']").attr("selected","selected") ;
    		jQuery("#comboClasse option[value='']").attr("selected","selected") ;
    		jQuery("#comboServidor option[value='']").attr("selected","selected") ;
    		jQuery("#comboLayout option[value='']").attr("selected","selected") ;
    		jQuery("#comboTipoLayout option[value='']").attr("selected","selected") ;
    		
    		jQuery("#inputAnexo").val("").hide();
    		jQuery("#selecionarAnexo").show().attr("disabled", "disabled");
    		jQuery("#listaAnexos").remove();
    		
    		jQuery("#bt_ver_anexo").hide();
        	jQuery('#htmlLayout').css("display", "none");
        	vComboSubpropostas = '';
    		
		}else if(configEscolhida > 0){

			// limpa seleção
    		jQuery("#comboPropostas option[value='']").attr("selected","selected") ;
    		jQuery("#comboSubpropostas option[value='']").attr("selected","selected") ;
    		jQuery("#comboContratos option[value='']").attr("selected","selected") ;
    		jQuery("#comboLayout option[value='']").attr("selected","selected") ;
    		jQuery("#comboClasse option[value='']").attr("selected","selected") ;
    		jQuery("#comboServidor option[value='']").attr("selected","selected") ;
    		jQuery("#comboTipoLayout option[value='']").attr("selected","selected") ;
    		jQuery("#inputAnexo").val("");
    		
			// desabilita combos 
			jQuery('#comboPropostas').attr("disabled", "disabled");
			jQuery('#comboSubpropostas').attr("disabled", "disabled");
			jQuery('#comboContratos').attr("disabled", "disabled");
			jQuery('#comboClasse').attr("disabled", "disabled");
			jQuery('#comboServidor').attr("disabled", "disabled");
			jQuery('#comboLayout').attr("disabled", "disabled");	
			jQuery('#inputAnexo').attr("disabled", "disabled");
			jQuery('#comboTipoLayout').attr("disabled", "disabled");
    		
			// busca valores a serem carregados nos combos !!!
			jQuery.ajax({
	            url: 'cad_layout_kit_boas_vindas.php',
	            type: 'post',
	            data: {
	            	config: $(this).val(),
	                acao:'carregaValoresConfig'
	            },
	            beforeSend: function(){
	                jQuery('#processando').html('<img src="images/loading.gif" alt="" />');
	            },
	            success: function(data) {
 
	            	var resultado = jQuery.parseJSON(data);
	            	
	            	if(resultado) {
	            		
	            		jQuery("#comboPropostas option[value='"+resultado.conftppoid+"']").attr("selected","selected") ;
	            		if(resultado.conftppoid > 0){
		            		jQuery("#comboPropostas").change(); // chama o carregamento do campo subtipo proposta
			        		vComboSubpropostas = resultado.conftppoid_sub; // guarda valor a selecionar após carregamento da comboSubProposta
	            		}
		        		jQuery("#comboContratos option[value='"+resultado.conftpcoid+"']").attr("selected","selected") ;
		        		jQuery("#comboClasse option[value='"+resultado.lconeqcoid+"']").attr("selected","selected") ;
		        		jQuery("#comboServidor option[value='"+resultado.lconsrvoid+"']").attr("selected","selected") ;
		        		jQuery("#comboLayout option[value='"+resultado.conflwkoid+"']").attr("selected","selected") ;
		        		jQuery("#comboTipoLayout option[value='"+resultado.lcontcloid+"']").attr("selected","selected") ;
		        		
		        		jQuery("#listaAnexos").remove();
		        		
		        		// Verificar se possui anexo
		        		if (resultado.lconanexo){
		        			jQuery("#selecionarAnexo").hide();
		        			
		        			jQuery("#inputAnexo").val(resultado.lconanexo).show().attr("disabled","disabled");
		        			jQuery("#inputAnexo").css("width","300px")
		        			
		        			jQuery("#bt_ver_anexo").attr("rel", resultado.lconanexo).show();
		        			
		        		}else{
		        			jQuery("#selecionarAnexo").hide();
		        			jQuery("#inputAnexo").val("Nenhum Anexo.").show().attr("disabled","disabled");
		        			jQuery("#bt_ver_anexo").hide();
		        		}
		        				        		
		        		jQuery("#comboLayout").change(); // chama o carregamento do html do layout
	            	}	            	
	            },
	            complete: function(){
	    			jQuery('#comboLayout').removeAttr("disabled");
	    			jQuery('#comboTipoLayout').removeAttr("disabled");
	    			jQuery('#comboServidor').removeAttr("disabled");
	                jQuery('#processando').html('');
	            }
	        });
						
		}
		
	});
		
	jQuery("#comboPropostas").change(function(){
        
		jQuery("#div_msg").hide();
		jQuery('#comboSubpropostas').attr("disabled", "disabled");
				        
        jQuery.ajax({
            url: 'cad_layout_kit_boas_vindas.php',
            type: 'post',
            data: {
            	tipoProposta: $(this).val(),
                acao:'buscarSubProposta'
            },
            beforeSend: function(){
                jQuery('#processando').html('<img src="images/loading.gif" alt="" />');
            },
            success: function(data) {
            	jQuery("#comboSubpropostas").html('<option value="">Selecione</option>');  
            	var resultado = jQuery.parseJSON(data);
            	
            	if(resultado) {
            		// Monta combo de todos os comandos
            		jQuery.each(resultado, function(i, item){
            			jQuery("#comboSubpropostas").append('<option value="'+item.tppoid+'">'+item.tppdescricao+'</option>');
            		});
            		jQuery("#comboSubpropostas option[value='"+vComboSubpropostas+"']").attr("selected","selected") ;
            	}
            	
            },
            complete: function(){
            	if(jQuery("#comboSubpropostas option").size() > 1 && jQuery('#comboConfiguracao').val() == 0){
            		jQuery('#comboSubpropostas').removeAttr("disabled");
            	}
                jQuery('#processando').html('');
            }
        });
    });
	
	jQuery("#comboLayout").change(function(){
        		
		var selecionado = $(this).val();
		if(selecionado > 0){
	        jQuery.ajax({
	            url: 'cad_layout_kit_boas_vindas.php',
	            type: 'post',
	            data: {
	            	idLayout: $(this).val(),
	                acao:'carregaHtmlLayout'
	            },
	            beforeSend: function(){
	                jQuery('#processando').html('<img src="images/loading.gif" alt="" />');
	            },
	            success: function(data) { 
	            	var resultado = jQuery.parseJSON(data);
	            	
	            	if(resultado) {
	            		jQuery("#htmlLayout").html(resultado);
	            	}
	            	
	            },
	            complete: function(){
	            	jQuery('#htmlLayout').css("display", "block");
	                jQuery('#processando').html('');
	            }
	        });
		}else{
        	jQuery('#htmlLayout').css("display", "none");
		}
    });
	
	jQuery("#bt_limpar").click(function(){
		
		jQuery("#comboConfiguracao option[value='']").attr("selected","selected") ;
		jQuery("#comboPropostas option[value='']").attr("selected","selected") ;
		jQuery("#comboSubpropostas option[value='']").attr("selected","selected") ;
		jQuery("#comboContratos option[value='']").attr("selected","selected") ;
		jQuery("#comboLayout option[value='']").attr("selected","selected") ;
		jQuery("#comboClasse option[value='']").attr("selected","selected") ;
		jQuery("#comboServidor option[value='']").attr("selected","selected") ;
		jQuery("#comboTipoLayout option[value='']").attr("selected","selected") ;
			
    	jQuery('#htmlLayout').css("display", "none");
    	
    	jQuery('#comboPropostas').attr("disabled", "disabled");
    	jQuery('#comboSubpropostas').attr("disabled", "disabled");
    	jQuery('#comboContratos').attr("disabled", "disabled");
    	jQuery('#comboLayout').attr("disabled", "disabled");
    	jQuery('#comboClasse').attr("disabled", "disabled");
    	jQuery('#comboServidor').attr("disabled", "disabled");
    	jQuery('#comboTipoLayout').attr("disabled", "disabled");
    	
    	jQuery("#inputAnexo").val("").hide();
		jQuery("#selecionarAnexo").show().attr("disabled", "disabled");
		jQuery("#listaAnexos").remove();
		
    	jQuery('#htmlLayout').css("display", "none");    	
	});
	

	jQuery("#bt_excluir").click(function(){
				
		if(jQuery("#comboConfiguracao").val() <= 0){
			jQuery("#comboConfiguracao").css('border-color','#643E41');
			jQuery('#div_msg_cadastro').html("Favor preencher campo configuração!");
			
		}else{
			var confirmacao = confirm('Confirma a exclusão?');
			if(confirmacao){
				jQuery.ajax({
		            url: 'cad_layout_kit_boas_vindas.php',
		            type: 'post',
		            data: {
		            	idConfig: jQuery("#comboConfiguracao").val(),
		                acao:'excluiConfiguracao'
		            },
		            beforeSend: function(){
		                jQuery('#processando').html('<img src="images/loading.gif" alt="" />');
		            },
		            success: function(data) { 
		            	
		            	var resultado = jQuery.parseJSON(data);
		    			jQuery('#div_msg_cadastro').html(resultado.retorno.msg);
		                if(resultado.retorno.error == 0){
		                	jQuery('<form action="cad_layout_kit_boas_vindas.php" method="post"><input type="hidden" name="acao" value="index" /></form>')
		            		.appendTo('body').submit().remove();
		                }
		            	
		            },
		            complete: function(){
		                jQuery('#processando').html('');
		            }
		        });
			}
		}
				
	});	
	
	jQuery("#selecionarAnexo").click(function(){
		
		jQuery.ajax({
            url: 'cad_layout_kit_boas_vindas.php',
            type: 'post',
            data: {
                acao:'listarArquivoDiretorio'
            },
            beforeSend: function(){
                jQuery('#processando').html('<img src="images/loading.gif" alt="" />');
            },
            success: function(data) { 
            	var resultado = jQuery.parseJSON(data);
            	          
            	if (resultado.error == 1){
            		jQuery("#inputAnexo").val(resultado.msg).show().css("width","300px");
            		jQuery("#selecionarAnexo").hide();
                	
            	}else if(jQuery(resultado).length == 0){
            		jQuery("#inputAnexo").val("Nenhum arquivo no diretório").show().css("width","300px");
            		jQuery("#selecionarAnexo").hide();
            	}else{
            		var arquivos = "<table>";            		
            		
            		var radio = "";
            		jQuery("#selecionarAnexo").hide().parent().append("<div name='listaAnexos' id='listaAnexos' />");
            		
            		jQuery("#listaAnexos").append("<table></table>");
            		
            		// Se a quantidade de arquivo maior que 5, quebra em colunas (máximo 3 colunas)
            		if (jQuery(resultado).length > 5){
            			var arqColum = 3; // num de colunas
            			var arqLinha = Math.ceil(jQuery(resultado).length / 3); 
            			
            			var p = 0;
            			var link = "";
            			var arq = "";
            			
            			for(var j = 0; j < 3; j++){
            				// Arquivos nas linhas
	            			for(var i = 0; i < arqLinha && p < jQuery(resultado).length; i++, p++){
	            				
	            				// Nome do arquivo
	            				arq = resultado[p];
	            				
	            				if (j > 0){
	            					var linha = jQuery('#listaAnexos table tr')[i];
	            					radio = "<input class='radio' type='radio' name='arqAnexo' id='arqAnexo' value='"+arq+"' >";
	            					link = "cad_layout_kit_boas_vindas.php?acao=visualizarAnexo&anexo=" + arq;
	            					radio += "<a href='"+link+"' style='margin-left:5px' title='Visualizar Arquivo'>" + arq + "</a>";	            					
	            					
	            					arquivos = "<td style='width:20%'>"+radio+"</td>";
	            					$('#listaAnexos table').find( linha ).append(arquivos);
	      
	            				}else{
	            					radio = "<input class='radio' type='radio' name='arqAnexo' id='arqAnexo' value='"+arq+"' >";
	            					link = "cad_layout_kit_boas_vindas.php?acao=visualizarAnexo&anexo=" + arq;
	            					radio += "<a href='"+link+"' style='margin-left:5px'>" + arq + "</a>";	 

	            					arquivos = "<tr><td style='width:20%' title='Visualizar Arquivo'>"+radio+"</td></tr>";
	            					
	            					jQuery("#listaAnexos").find("table").append(arquivos);
	            				}
	            			}
            			}
            		}else{
            			jQuery(resultado).each(function(index, value){
            				radio = "<input class='radio' type='radio' name='arqAnexo' id='arqAnexo' value='"+value+"' >";
        					link = "cad_layout_kit_boas_vindas.php?acao=visualizarAnexo&anexo=" + value;
        					radio += "<a href='"+link+"' style='margin-left:5px' title='Visualizar Arquivo'>" + value + "</a>";	 
            				
            				arquivos = "<tr><td style='width:20%'>"+radio+"</td></tr>";
            				jQuery("#listaAnexos").append(arquivos);
                		});
            		}
            	}
            },
            complete: function(){
                jQuery('#processando').html('');
            }
        });

	});
	
	jQuery("#bt_gravar").click(function(){
		
		jQuery("#comboConfiguracao").css('border-color','#C0C0C0');
		jQuery("#comboPropostas").css('border-color','#C0C0C0');		
		jQuery("#comboSubpropostas").css('border-color','#C0C0C0');
		jQuery("#comboContratos").css('border-color','#C0C0C0');
		jQuery("#comboClasse").css('border-color','#C0C0C0');
		jQuery("#comboServidor").css('border-color','#C0C0C0');
		jQuery("#comboLayout").css('border-color','#C0C0C0');
		jQuery("#comboTipoLayout").css('border-color','#C0C0C0');
		
		// validar campos
		var valida = true;
		var mensagem = '';
		if(jQuery("#comboConfiguracao").val() == ''){
			valida = false;
			mensagem += "Favor preencher campo configuração!<br>";
			jQuery("#comboConfiguracao").css('border-color','#643E41');
		}
		
		if(jQuery("#comboPropostas").val() == '' && jQuery("#comboContratos").val() == '' && jQuery("#comboClasse").val() == ''){
			valida = false;
			//mensagem += "Favor preencher ao menos um dos campos (tipo de proposta, subtipo de proposta, tipo de contrato)!<br>";
			mensagem += "Favor preencher ao menos um dos campos (tipo de proposta, subtipo de proposta, tipo de contrato, classe de contrato)!<br>"
			jQuery("#comboPropostas").css('border-color','#643E41');
			jQuery("#comboSubpropostas").css('border-color','#643E41');
			jQuery("#comboContratos").css('border-color','#643E41');
			jQuery("#comboClasse").css('border-color','#643E41');
		}

		if(jQuery("#comboServidor").val() == ''){
			valida = false;
			mensagem += "Favor preencher campo servidor!<br>";
			jQuery("#comboServidor").css('border-color','#643E41');
		}	
		if(jQuery("#comboLayout").val() == ''){
			valida = false;
			mensagem += "Favor preencher campo layout!<br>";
			jQuery("#comboLayout").css('border-color','#643E41');
		}
		if(jQuery("#comboTipoLayout").val() == ''){
			valida = false;
			mensagem += "Favor preencher campo tipo de layout!<br>";
			jQuery("#comboTipoLayout").css('border-color','#643E41');
		}
		
		if(valida == true){
			// Formulário preenchido corretamente
			jQuery("#form").prepend("<input name='acao' id='acao' value='incluiConfiguracao' type='hidden'/>");
			jQuery("#acao").hide();
			
			jQuery("#form").submit();			
			
		}else{
			jQuery('#div_msg_cadastro').html(mensagem);
		}
		
	});
	
	jQuery("#criarNovoLayout").click(function(){
		
		jQuery('<form action="cad_layout_kit_boas_vindas.php" method="post"><input type="hidden" name="acao" value="formCadLayout" /></form>')
		.appendTo('body').submit().remove();
		
	});
		
	jQuery("#bt_voltar").click(function(){
		
		jQuery('<form action="cad_layout_kit_boas_vindas.php" method="post"><input type="hidden" name="acao" value="index" /></form>')
		.appendTo('body').submit().remove();
		
	});

	jQuery("#div_lista_layout .editaLayout").click(function(){

		var idLayout = $(this).attr('id').split('_');
		
		jQuery.ajax({
            url: 'cad_layout_kit_boas_vindas.php',
            type: 'post',
            data: {
            	idLayout: idLayout[1],
                acao:'carregaDadosLayout'
            },
            beforeSend: function(){
                jQuery('#processando').html('<img src="images/loading.gif" alt="" />');
            },
            success: function(data) { 
            	var resultado = jQuery.parseJSON(data);

            	if(resultado) {
            		jQuery("#idLayout").val(resultado.lwkoid);
            		jQuery("#nomeLayout").val(resultado.lwkdescricao);
            		jQuery("#assuntoLayout").val(resultado.lwkassunto_email);

            		if(resultado.lwkpadrao == 't'){
            			jQuery("#padraoLayout").attr('checked','checked');
            		}else{
            			jQuery("#padraoLayout").removeAttr('checked');            			
            		}

            		jQuery("#htmlLayoutEdicao").val(resultado.lwklayout);
            	}
            	
            },
            complete: function(){
                jQuery('#processando').html('');
            }
        });
		
	});

	jQuery("#bt_ver_anexo").live("click",function(){
		var anexo = jQuery(this).attr("rel");
		
		jQuery.ajax({
            url: 'cad_layout_kit_boas_vindas.php',
            type: 'post',
            data: {
            	anexo: anexo,
                acao:'verAnexo'
            },
            beforeSend: function(){
                jQuery('#processando').html('<img src="images/loading.gif" alt="" />');
            },
            success: function(data) { 
            	var resultado = jQuery.parseJSON(data);
            	// Ocorreu algum erro na visualização do anexo
            	if (resultado.error == 1){
            		jQuery('#div_msg_cadastro').html(resultado.msg);
            	}else{
            		window.location.href = "cad_layout_kit_boas_vindas.php?acao=visualizarAnexo&anexo=" + anexo;
            	}
            },
            complete: function(){
                jQuery('#processando').html('');
            }
        });		
	});

	jQuery("#bt_limpar_layout").click(function(){

		jQuery("#idLayout").val("");
		jQuery("#nomeLayout").val("") ;
		jQuery("#padraoLayout").removeAttr('checked');
		jQuery("#htmlLayoutEdicao").val("");
		jQuery("#assuntoLayout").val("");
						
	});
	
	jQuery("#bt_excluir_layout").click(function(){

		if(jQuery("#idLayout").val() == ''){
			
			jQuery('#div_msg_layout').html("Selecione o layout a ser excluído!");
			
		}else{
			
			var confirmacao = confirm('Confirma a exclusão?');
			if(confirmacao){
				jQuery.ajax({
		            url: 'cad_layout_kit_boas_vindas.php',
		            type: 'post',
		            data: {
		            	idLayout: jQuery("#idLayout").val(),
		                acao:'excluiLayout'
		            },
		            beforeSend: function(){
		                jQuery('#processando').html('<img src="images/loading.gif" alt="" />');
		            },
		            success: function(data) { 
		            	
		            	var resultado = jQuery.parseJSON(data);
		    			jQuery('#div_msg_layout').html(resultado.retorno.msg);
		                if(resultado.retorno.error === 0){
		                	jQuery('<form action="cad_layout_kit_boas_vindas.php" method="post"><input type="hidden" name="acao" value="formCadLayout" /></form>')
		            		.appendTo('body').submit().remove();
		                }
		            	
		            },
		            complete: function(){
		                jQuery('#processando').html('');
		            }
		        });
			}
			
		}
				                                      
	});
	
	jQuery("#bt_gravar_layout").click(function(){

		// validar campos
		var valida = true;
		var mensagem = '';
		if(jQuery("#nomeLayout").val() == ''){
			valida = false;
			mensagem += "Favor preencher campo Nome!<br>";
			jQuery("#nomeLayout").css('border-color','#643E41');
		}
		
		if(jQuery("#assuntoLayout").val() == ''){
			valida = false;
			mensagem += "Favor preencher campo Assunto!<br>";
			jQuery("#assuntoLayout").css('border-color','#643E41');
		}
		
		if(jQuery("#htmlLayoutEdicao").val() == ''){
			valida = false;
			mensagem += "Favor preencher campo Layout!<br>";
			jQuery("#htmlLayoutEdicao").css('border-color','#643E41');
		}

		
		if(valida == true){
			
			// validar se existe outro padrão
					
			jQuery.ajax({
	            url: 'cad_layout_kit_boas_vindas.php',
	            type: 'post',
	            data: {
	            	idLayout: jQuery("#idLayout").val(),
	            	nomeLayout: jQuery("#nomeLayout").val(),
	            	assuntoLayout: jQuery("#assuntoLayout").val(),
	            	padraoLayout: jQuery("#padraoLayout:checkbox:checked").val(),
	            	htmlLayoutEdicao: jQuery("#htmlLayoutEdicao").val(),
	                acao:'validaLayout'
	            },
	            beforeSend: function(){
	                jQuery('#processando').html('<img src="images/loading.gif" alt="" />');
	            },
	            success: function(data) { 

    				var definePadrao = 'ignora';
	            	var idLayout = jQuery("#idLayout").val();
	            	var nomeLayout = jQuery("#nomeLayout").val();
	            	var assuntoLayout = jQuery("#assuntoLayout").val();
	            	var padraoLayout = jQuery("#padraoLayout:checkbox:checked").val();
	            	var htmlLayoutEdicao = jQuery("#htmlLayoutEdicao").val();
	            	var resultado = jQuery.parseJSON(data);
	            	
	            	if(resultado.retorno.error == 2){
	            		
	            		var confirmacao = confirm(resultado.retorno.msg);
        				 
	        			if(confirmacao == true){
	        				var definePadrao = 'sim';
	        			}else{
	        				var definePadrao = 'nao';	        				
	        			}
	            	}

	            	if(resultado.retorno.error != 1 ){
		            	jQuery.ajax({
	    		            url: 'cad_layout_kit_boas_vindas.php',
	    		            type: 'post',
	    		            data: {
	    		            	idLayout: idLayout,
	    		            	nomeLayout: nomeLayout,
	    		            	assuntoLayout: assuntoLayout,
	    		            	padraoLayout: padraoLayout,
	    		            	htmlLayoutEdicao: htmlLayoutEdicao,
	    		            	definePadrao: definePadrao,
	    		                acao:'incluiLayout'
	    		            },
	    		            beforeSend: function(){
	    		                jQuery('#processando').html('<img src="images/loading.gif" alt="" />');
	    		            },
	    		            success: function(data) { 
	    		            	
	    		            	var resultado = jQuery.parseJSON(data);
	    		    			jQuery('#div_msg_layout').html(resultado.retorno.msg);
	    		            	
	    		            },
	    		            complete: function(){
	    		                jQuery('#processando').html('');
	    		                if(resultado.retorno.error != 1){
	    		                	// recarrega conteudo lista de layouts
	    		                	jQuery('<form action="cad_layout_kit_boas_vindas.php" method="post"><input type="hidden" name="acao" value="formCadLayout" /></form>')
	    		            		.appendTo('body').submit().remove();
	    		                }
	    		            }
	    		        }); 
	            	}else{
	            		jQuery('#div_msg_layout').html(resultado.retorno.msg);
	            	}	            	
	            },
	            complete: function(){
	                jQuery('#processando').html('');	               
	            }
	        });
			
			
		}else{
			jQuery('#div_msg_layout').html(mensagem);
		}
				
	});
	
});