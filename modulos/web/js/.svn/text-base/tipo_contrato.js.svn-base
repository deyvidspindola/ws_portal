function relPesquisa(modalidade) {
	/*
     *  Ajax para carregamento da listagem,
     *  chama o método pesquisar do Action
     */        
	var clinome = "";
	var clidoc = "";
	var tipo = "";
	
	if (modalidade=='M') {
		clinome = $('#cli_monit').val();
		clidoc = $('#cli_doc_monit').val();
		tipo = $("input[name='cli_tipo_monit']:checked").val();	
	} else {
		clinome = $('#cli_loc').val();
		clidoc = $('#cli_doc_loc').val();
		tipo = $("input[name='cli_tipo_loc']:checked").val();
	}
	
     jQuery.ajax({
         url: 'tipo_contrato.php',
         type: 'post',
         data: {'clinome':clinome,'clidoc':clidoc,'tipo':tipo,'acao':'pesquisarClientes'},
         beforeSend: function(){

             /*
             * Antes de enviar o ajax removemos a tabela
             * para que ela possa ser populada novamente sem
             * erros
             * */
             //jQuery('.resultado_pesquisa').hide();
             //jQuery('.tr_resultado_ajax').remove();
             jQuery('#pesquisa').html('<img src="images/loading.gif" alt="" /><br /><br />');
             /*
             * Bloqueio do botão de pesquisa para que no caso de o usuário
             * clicar várias vezes ele mande apenas uma requisição
             */
             //jQuery('#btn_gerar_resumo').attr('disabled', 'disabled');                    
                 
         },
         success: function(data){
         	try{
            	 //console.log(data);
            	 
                 // Transforma a string em objeto JSON
                 var resultado = jQuery.parseJSON(data);
                 
                 
            	 //console.log(resultado.clientes[0].id);
                 //return;
                 
                 var content = '<table width="100%" class="tableMoldura" align="center">'; 
                                  
                 /*if (resultado.clientes.length) {
                	 jQuery('#loading').html('Nenhum resultado encontrado');
                	 return;
                 }*/
                 
                 if (resultado.clientes.length>0) {
                     
                     // Título das colunas de listagem
                     content += '<tr class="tableSubTitulo">';
                     content += '<td colspan="7"><h2>Resultado da pesquisa</h2></td>';
                     content += '</tr>';
                     
                     content += '<tr class="tableTituloColunas tab_registro">';
                     content += '<td width="50%" align="center"><h3>Nome</h3></td>';
                     content += '<td width="50%" align="center"><h3>CPF/CNPJ</h3></td>';
                     content += '</tr>';
                     
                     // Monta a listagem de obrigacoes financeiras de acordo com o retorno da pesquisa
                     jQuery.each(resultado.clientes, function(i, cliente){
                    	 if (cliente.nome.length > 0 && cliente.nome != " ") {
                    		 var cd = cliente.doc;
                    		 if (cliente.tipo=='F') {
                    			 mascaraDoc = cd.substring(0,3)+"."+cd.substring(3,6)+"."+cd.substring(6,9)+"-"+cd.substring(9,11);
                    		 } else {
                    			 mascaraDoc = cd.substring(0,2)+"."+cd.substring(2,5)+"."+cd.substring(5,8)+"/"+cd.substring(8,12)+"-"+cd.substring(12,14);
                    		 }                    		 
                    		 content += '<tr class="tr_resultado_ajax">';
	                         content += '<td><a href="javascript: atualizaPagador('+cliente.id+',\''+cliente.tipo+'\',\''+modalidade+'\');">'+cliente.nome+'</a></td>';
	                         content += '<td>'+mascaraDoc+'</td>';
	                         content += '</tr>';
                    	 }
                     });                    
                     
                     // Rodapé da listagem
                     /*content += '<tr class="tableRodapeModelo1">';
                     content += '<td align="center" colspan="2">&nbsp;';
                     content += '</td>';
                     content += '</tr>';*/
                     content += '</table>';
                     
                     // Popula a tabela com os resultados
                     jQuery('#pesquisa').hide();
                     jQuery('#pesquisa').html(content);

                     // Zebra a tabela
                     jQuery('.tr_resultado_ajax:odd').addClass('tde');
                     jQuery('.tr_resultado_ajax:even').addClass('tdc');

                     // Mostra a tabela
                     jQuery('#pesquisa').fadeIn();
                     
                 }
                 else{
                     
                     /*
                    * Else do if(resultado.contratos.length) se caiu aqui
                    * quer dizer que a pesquisa não retornou nenhum item  
                    */                         
                     jQuery('#pesquisa').html('<b>Nenhum resultado encontrado.</b><br /><br />');
                     
                 }                    

             }catch(e){
                 
                 // Caso haja erros durante o processo, provavelmente na base de dados
                 jQuery('#pesquisa').html('<b>Erro no processamento dos dados.</b><br /><br />');
                 
             }
         }
     });
}

function atualizaPagador(clioid, clitipo, modalidade) {
	jQuery.ajax({
        url: 'tipo_contrato.php',
        type: 'post',
        data: {'clioid':clioid,'clitipo':clitipo,'modalidade':modalidade,'acao':'atributosCliente'},
        beforeSend: function(){
            jQuery('#pesquisa').html('<img src="images/loading.gif" alt="" /><br /><br />');  
        },
        success: function(data){
        	try{
        		
	           	//console.log(data);
	           	 
	            // Transforma a string em objeto JSON
	            var resultado = jQuery.parseJSON(data); 
	                           
	            //console.log(resultado.nome);
        		//console.log(resultado.id);
	            var campoNome = "";
	            var campoId = "";
	            var campoTipo = "";
	            var campoDoc = "";
	            var botao = "";
	            var radio = "";
	            if (modalidade=="M") {
	            	campoNome = $('#cli_monit');
	            	campoId = $('#cli_id_monit');
	            	campoDoc = $('#cli_doc_monit');
	            	botao = $("#bt_cli_monit");
	            	radio = $("input[name='cli_tipo_monit']");
	            } else {
	            	campoNome = $('#cli_loc');
	            	campoId = $('#cli_id_loc');
	            	campoDoc = $('#cli_doc_loc');  
	            	botao = $("#bt_cli_loc"); 
	            	radio = $("input[name='cli_tipo_loc']");
	            }
	            
	            var cd = resultado.doc;
	       		if (resultado.tipo=='F') {
	       			mascaraDoc = cd.substring(0,3)+"."+cd.substring(3,6)+"."+cd.substring(6,9)+"-"+cd.substring(9,11);	       			
	       		} else {
	       			mascaraDoc = cd.substring(0,2)+"."+cd.substring(2,5)+"."+cd.substring(5,8)+"/"+cd.substring(8,12)+"-"+cd.substring(12,14);
	       		} 
	            
	            campoNome.val(resultado.nome);
	            campoId.val(resultado.id);
	            campoDoc.val(mascaraDoc);
	            campoNome.attr('disabled','disabled');
	            campoDoc.attr('disabled','disabled');
	            radio.attr('disabled','disabled');
	            botao.val('Limpar');

	            jQuery('#pesquisa').html('');

            }catch(e){
                
                // Caso haja erros durante o processo, provavelmente na base de dados
                jQuery('#pesquisa').html('<b>Erro no processamento dos dados.</b>');
                
            }
        }
    });
}

$(document).ready(function(){
	
	
	if ($('#cli_monit').val().length>0) {
		$("#bt_cli_monit").val('Limpar');
		$("#cli_monit").attr('disabled','disabled');
		$("#cli_doc_monit").attr('disabled','disabled');
		$("input[name='cli_tipo_monit']").attr('disabled','disabled');
	}
	
	if ($('#cli_loc').val().length>0) {
		$("#bt_cli_loc").val('Limpar');
		$("#cli_loc").attr('disabled','disabled');
		$("#cli_doc_loc").attr('disabled','disabled');
		$("input[name='cli_tipo_loc']").attr('disabled','disabled');
	}
		
	//$('#cli_monit').setMask('@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@');
	//$('#cli_loc').setMask('/[0-9a-zA-Z ]/');
	
	//Evita que campops texto percam foco - crash do backspace no historico
	$(".cli_tipo_monit").focusin(function(){
		$("#cli_monit").focus();
	});	
	$(".cli_tipo_loc").focusin(function(){
		$("#cli_loc").focus();
	});	
	$("#bt_cli_monit").focusin(function(){
		$("#cli_doc_monit").focus();
	});	
	$("#bt_cli_loc").focusin(function(){
		$("#cli_doc_loc").focus();
	});
	
	
	if ($("input[name='cli_tipo_loc']:checked").val()=="F") {
		$("#cli_doc_loc").setMask('999.999.999-99');
	} else {
		$("#cli_doc_loc").setMask('99.999.999/9999-99');
	}
	$("#cli_doc_loc").keyup(function() {
		FormataCnpj($(this),event);
	});
	$("label[for='cli_doc_loc']").html('CNPJ:');
	
	
	
	$(".cli_tipo_loc").click(function(){
    	
    	$("#cli_doc_loc").val('');
    	
    	var frm_tipo1 = $(this).val();
    	
    	switch (frm_tipo1) {
			case 'F':
				$("#cli_doc_loc").setMask('999.999.999-99');
				$("label[for='cli_doc_loc']").html('CPF:');
			break;
			case 'J':
			default:
				$("#cli_doc_loc").setMask('999.999.999/9999-99');
				$("label[for='cli_doc_loc']").html('CNPJ:');
			break;
		}
    });
	
	if ($("input[name='cli_tipo_monit']:checked").val()=="F") {
		$("#cli_doc_monit").setMask('999.999.999-99');
	} else {
		$("#cli_doc_monit").setMask('99.999.999/9999-99');
	}
	$("label[for='cli_doc_monit']").html('CNPJ:');
	
	$(".cli_tipo_monit").click(function(){
    	
    	$("#cli_doc_monit").val('');
    	
    	var frm_tipo2 = $(this).val();
    	
    	switch (frm_tipo2) {
			case 'F':
				$("#cli_doc_monit").setMask('999.999.999-99');
				$("label[for='cli_doc_monit']").html('CPF:');
			break;
			case 'J':
			default:
				$("#cli_doc_monit").setMask('999.999.999/9999-99');
				$("label[for='cli_doc_monit']").html('CNPJ:');
			break;
		}
    });
	
	$("#bt_cli_monit").click(function(){
		if ($(this).val() == 'Pesquisar' ) {
			relPesquisa('M');
		} else {
			$("#cli_monit").val('');
			$("#cli_id_monit").val('');
			$("#cli_doc_monit").val('');
			$(this).val('Pesquisar');
			$("#cli_monit").removeAttr('disabled');
			$("#cli_doc_monit").removeAttr('disabled');
			$("input[name='cli_tipo_monit']").removeAttr('disabled');
		}
	});
	
	$("#bt_cli_loc").click(function(){
		if ($(this).val() == 'Pesquisar' ) {
			relPesquisa('L');
		} else {
			$("#cli_loc").val('');
			$("#cli_id_loc").val('');	
			$("#cli_doc_loc").val('');		
			$(this).val('Pesquisar');
			$("#cli_loc").removeAttr('disabled');
			$("#cli_doc_loc").removeAttr('disabled');
			$("input[name='cli_tipo_loc']").removeAttr('disabled');
		}
	});
});