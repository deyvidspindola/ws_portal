/**
 * @author	Leandro Alves Ivanaga
 * @email 	leandroivanga@brq.com
 * @since	07/08/2013
 */
jQuery(function() {    	
	
	// controle de elementos de tela
	if (jQuery('#mensagem').text() == '') {
        jQuery('#mensagem').hide(); // oculta bloco caso não tenha mensagem
    }

	jQuery("#ceqpprdoid").attr('disabled',true); // produto
	jQuery("#ceqpveqpoid").attr('disabled',true); // validade
		
    jQuery('#bloco_clientes').hide(); // bloco de clientes deve iniciar oculto
    if(jQuery("#venda_restritaSim:checked").val() == 'S')
        jQuery('#bloco_clientes').show(); // caso a venda restrita estiver selecionada, mostra o bloco
	
    jQuery('#fieldsetConfigSoftware').attr('disabled',true); // bloqueia configuração de software

	jQuery("#select_clientes").hide();

	// Ações do form
	jQuery('body').delegate('#buttonNovo,#buttonCancelar,#buttonExcluir', 'click', function(){
		
		jQuery(this).closest('form').removeAttr('onsubmit'); // remove o bloqueio do submit (validações do salvar)
		
		// Pega value do botão clicado
		var acaoValor = jQuery(this).val();
		// Troca ação para o valor correspondente e dá submit no form
		jQuery('#acao').val(acaoValor).closest('form').submit();
	});


	// Ações do form
	jQuery('body').delegate('#buttonPesquisar', 'click', function(){

		var id = jQuery('#ceqpoid').val();
		var tipo = jQuery('input[name=tipo_equip]:checked').val();
		
		if (!id && !tipo) {     

			jQuery(".tipo_equip").addClass('erro');	
			jQuery(".tipo_equip").next('label').addClass('erro');	
			
        	jQuery("#mensagem").attr("class","mensagem alerta");
        	jQuery("#mensagem").html("Selecione o tipo de equipamento DCT ou RTN para continuar.");
        	jQuery("#mensagem").css("display","block");
            
        }else{
    		jQuery(this).closest('form').removeAttr('onsubmit'); // remove o bloqueio do submit (validações do salvar)
    		
    		// Pega value do botão clicado
    		var acaoValor = jQuery(this).val();
    		// Troca ação para o valor correspondente e dá submit no form
    		jQuery('#acao').val(acaoValor).closest('form').submit();
        }
		
	});

	// Ações do form de Pesquisar Equipamento
	jQuery('body').delegate('#ceqpprdoid', 'change', function(){

		if(jQuery("#acao").val() == "pesquisar"){
			buscaClientesPorTipoEquipamento();
		}
		
	});
	
	// Ações do form de Pesquisar Equipamento
	jQuery('body').delegate('.tipo_equip', 'click', function(){

		if($('#acao').val() == 'atualizar'){
			if(jQuery("input[name=tipo_equip]:checked").val() == 'RTN'){
				$('.tipo-imobilizado').show();
			}			
		}else{
			$('#codigo-material').attr('href', '#');
			$('#codigo-material').html('');
			$('#tipo-cadastro').html('');
			$('#tipo-produto').html('');
			$('#tipo-imobilizado').html('');
			$('.tipo-imobilizado').hide();
		}
		

		jQuery("#ceqpprdoid").removeClass("erro");
		jQuery("#ceqpveqpoid").removeClass("erro");		

		jQuery(".tipo_equip").removeClass('erro');	
		jQuery(".tipo_equip").next('label').removeClass('erro');	
		
		jQuery.ajax({
	        url: 'cad_configuracao_equipamento.php',
	        type: 'post',
	        
	        data: {
	        		tipo_equip : $(this).val(),
	        		acao : 'pesquisarEquipamento'
	        	},
	        beforeSend: function(){
	        	jQuery("#ceqpprdoid").html("");
	        		
	        },
	        success: function(data){
	        	// Transforma a string em objeto JSON
                var resultado = jQuery.parseJSON(data);
                var equipamento_selected = jQuery("#equipamento_selected").val();
 
                jQuery("#ceqpprdoid").html("<legend>Equipamentos Encontrados</legend>");
                                
                if (resultado.length > 0) {
                	
                	var opt = "<option value=''>Selecione o Equipamento</option>";
                	jQuery("#ceqpprdoid").append(opt);

                	jQuery.each(resultado, function(index, value) {

                		opt = "<option value='"+value.prdoid+"' data-tipo-produto='"+value.tipoproduto+"'>" +value.prdproduto+ "</option>" ;
                		jQuery("#ceqpprdoid").append(opt);
	                }); 
                	
                	jQuery('#oculto').show();
                	jQuery("#mensagem").css("display","none");
                	jQuery("#informativos").css("display","none");
                	jQuery("#ceqpprdoid").parent().css("display","block");
                	jQuery('#fieldsetConfigSoftware').attr('disabled',false); // libera configuração de software

    				if(jQuery("#acao").val() == "pesquisar"){
    					buscaClientesPorTipoEquipamento(); 
    				}
                	
                	if(equipamento_selected != ""){
                		jQuery("#ceqpprdoid").val(equipamento_selected);
                	}

    	        	if(jQuery("input[name=tipo_equip]:checked").val() == 'RTN')
    	        		jQuery("#ceqpveqpoid").attr('disabled',true);
    	        	if(jQuery("input[name=tipo_equip]:checked").val() == "DCT")
    	        		jQuery("#ceqpveqpoid").attr('disabled',false);

            		if(jQuery('#ceqpoid').val() != ''){
            			jQuery("#ceqpprdoid").attr('disabled',true); // produto		
            			jQuery(".tipo_equip").attr('disabled',true); // tipo produto		
            		}else{
                    	jQuery("#ceqpprdoid").attr('disabled',false);
            		}
                	
                } else {

    	        	if(jQuery("input[name=tipo_equip]:checked").val() == 'RTN')
    	        		jQuery("#ceqpveqpoid").attr('disabled',true);
    	        	if(jQuery("input[name=tipo_equip]:checked").val() == "DCT")
    	        		jQuery("#ceqpveqpoid").attr('disabled',false);
    	        	
                	jQuery("#ceqpprdoid").attr('disabled',true);
                	jQuery("#mensagem").attr("class","mensagem alerta");
                	jQuery("#mensagem").html("Nenhum equipamento encontrado com o termo pesquisado.");
                	jQuery("#mensagem").css("display","block");
                }
	        }
	    });
		
	});

	// Ação de editar
	jQuery('body').delegate('.clickEditar',  'click', function(){ 
		
		jQuery('#pesquisa_configuracao_equipamento').removeAttr('onsubmit'); // remove o bloqueio do submit (validações do salvar)
		
		jQuery('#ceqpoid').val(jQuery(this).attr('id'));
		// Troca ação para o valor correspondente e dá submit no form
		jQuery('#acao').val('editar').closest('form').submit();
		
		

	});

	// executa busca de equipamento conforme filtro selecionado DCT/RTN
	jQuery('body').ready(function(){
		jQuery(".tipo_equip:checked").each(function() {
			jQuery(this).click();
		});		
	});

	//pesquisar clientes
	jQuery("body").delegate("#bt_pesquisar_cliente", "click", function(){

		jQuery('#mensagem').hide();
		jQuery('#informativos').hide();
		jQuery("#select_clientes").hide();
		jQuery("#nomeCliente").removeClass('erro');
		jQuery("#clientes_resultado option").remove();

		var digitado = jQuery("#nomeCliente").val().length;
		var cliente = jQuery("#nomeCliente").val();
		var pessoa = jQuery("input[name=tipoPessoa]:radio:checked").val();


		if(digitado > 2) {

			jQuery("#nomeCliente").addClass('img_loader');

			jQuery.ajax({
				url: 'cad_configuracao_equipamento.php',
				type: 'post',
				data: {
					acao : 'buscarClienteNome',
					filtro : pessoa,
					nome: cliente
				},
				success: function(data){

					var resultado = jQuery.parseJSON(data);
					var opt = "";						

					if (resultado.length > 0) {

						jQuery("#select_clientes").show();	
						var listaIDs = '';
	                	
	                	jQuery.each(resultado, function(index, value) {

	                		opt = "<option value='"+value.clioid+"'>" +value.clinome+ ' | '+value.doc+ "</option>" ;
	                		jQuery("#clientes_resultado").append(opt);

		                }); 

		                jQuery("#nomeCliente").removeClass('img_loader');  

	                	
	                } else {
	                	jQuery("#nomeCliente").removeClass('img_loader');
	                	jQuery("#select_clientes").hide();
	                	jQuery("#mensagem").addClass("alerta");
	                	jQuery("#mensagem").html("Nenhum cliente encontrado com o termo pesquisado.");
	                	jQuery("#mensagem").show();
	                	jQuery(document).scrollTop(jQuery("#mensagem").offset().top );	
	                }
					
				
				}
			});
	
		} else {
			
			jQuery('#mensagem').html("Informe ao menos três caracteres para a pesquisa.");			
			jQuery('#mensagem').addClass("alerta");
			jQuery('#mensagem').show();
			jQuery("#nomeCliente").addClass('erro');
			jQuery(document).scrollTop(jQuery("#mensagem").offset().top );	
			return false;
		}
		
	});

	jQuery("body").delegate("#bt_adicionar_lista", "click", function(){
		
		jQuery('#mensagem').hide();
		jQuery('#informativos').hide();
		var resultados = 0;
		var listaIDs = '';

		jQuery("#clientes_resultado").find(":selected").each(function(id){
							
			var clioid = $(this).val();
			var dados = $(this).text().split('|');
			var clinome = jQuery.trim(dados[0]);
			var doc = jQuery.trim(dados[1]);		
			var linha = "";		
			var naLista = jQuery("#listaClientes").val().split(',');

			if(jQuery.inArray(clioid, naLista) !== -1){

				jQuery('#mensagem').html("Cliente já esta na lista");			
				jQuery('#mensagem').addClass("alerta");
				jQuery('#mensagem').show();	
				jQuery(document).scrollTop(jQuery("#mensagem").offset().top );		
				return false;

				
			} else {

				linha = '<tr class="" id="tr_'+clioid+'" dir="'+clioid+'" >';
				linha += '<td>'+clinome+'</td>';
				linha += '<td class="direita">'+doc+'</td>';
				linha += '<td class="centro">';
				linha += '<img  onclick="excluirCliente(this)" id="excluir_cliente"  title="Excluir" src="images/icon_error.png" class="icone" />';
				linha += '</td>';

				jQuery("#lista_clientes tbody").append(linha);
				resultados++;

			}

		});

		if(resultados > 0){
			jQuery("#bloco_lista_clientes").show();	

			var cor = "";		

			jQuery("#lista_clientes tbody").find("tr").each(function(id){	

				jQuery(this).removeClass("par");
				if(listaIDs == "")
					listaIDs = jQuery(this).attr('dir');
				else
					listaIDs += ',' + jQuery(this).attr('dir');

			});

			jQuery("#listaClientes").val(listaIDs);

			jQuery("#lista_clientes tbody").find("tr").each(function(id){						
							
				if(cor == "") {
					jQuery(this).addClass("par");
					cor = "par";	
				} else {
					cor = "";
				}
				
			});
		}	

	});
	
	// Ação escolha de venda restrita
	jQuery('body').delegate('input[name=venda_restrita]', 'click', function(){
		
		var restricao = jQuery("input[name=venda_restrita]:checked").val();

		if(restricao == 'S'){
			if(jQuery("#acao").val() == "pesquisar"){
				// tela de pesquisa
				buscaClientesPorTipoEquipamento();
			}else{
				// form cadastro/edição
				jQuery("#bloco_clientes").show();
			}
		}
		
		if(restricao == 'N'){
			jQuery("#bloco_clientes").hide();
		}

	});
	
	// controle do campo ID
    jQuery("#ceqpoid").on('keyup', function (event) {
    	
    	if(jQuery('#ceqpoid').val() == ""){
    		buscaPorEqu();
    	}else {
    		buscaPorId();
    	}        
    });
    // controle do campo ID - para o evento de limpar do IE (evento não identificado, por isso ajustado no onblur)
    jQuery("#ceqpoid").on('blur', function (event) { 
    	
    	if(jQuery('#ceqpoid').val() == ""){
    		buscaPorEqu();
    	}else {
    		buscaPorId();
    	}        
    });

    // Ação de salvar do formulario
	jQuery('body').delegate('.salvar', 'click', function(e) {
		
		jQuery('#mensagem').hide();
		jQuery('#informativos').hide();
		
		var venda_restrita = jQuery("input[name=venda_restrita]:checked").val();		
		var listaClientes = jQuery("#listaClientes").val();		

		var tipo = jQuery('input[name=tipo_equip]:checked').val();
		if (!tipo) {     

			jQuery(".tipo_equip").addClass('erro');	
			jQuery(".tipo_equip").next('label').addClass('erro');	
			
        	jQuery("#mensagem").attr("class","mensagem alerta");
        	jQuery("#mensagem").html("Selecione o tipo de equipamento DCT ou RTN para continuar.");
        	jQuery("#mensagem").css("display","block");
        	return false;  
        }
		
		if(venda_restrita == 'S' && listaClientes == ''){

			jQuery("fieldset#venda_restrita").attr("class","erro");
			
			jQuery("#mensagem").attr("class","mensagem alerta");
        	jQuery("#mensagem").html("Nenhum cliente selecionado para a restrição.");
        	jQuery("#mensagem").css("display","block");
        
		}else{
			
			jQuery("fieldset#venda_restrita").removeAttr("class","erro");
			
			if (!validacaoJquery(jQuery(this))) {
	            return false;        
	        }else{

	        	// salva taxa adesao
	        	var tipo_equip = jQuery("input[name=tipo_equip]:checked").val();
				if($('#acao').val() == 'salvar'){
					if(tipo_equip == 'RTN'){
						$('#ceqpobroid_taxa').val(339);
						$('#ceqpincidencia_taxa_con').attr('checked','checked');
						$('#buttonSalvarTaxa').click();
						$('.tipo-imobilizado').show();
						
						var objBtn = this;
						setTimeout(function(){
							jQuery(objBtn).closest('form').removeAttr('onsubmit'); // remove o bloqueio do submit (validações do salvar)
	        				jQuery(objBtn).closest('form').submit();	
						},1000);
						
					}					
				}

				jQuery(this).closest('form').removeAttr('onsubmit'); // remove o bloqueio do submit (validações do salvar)
			    jQuery(this).closest('form').submit();	
	    		
	        }
		}
        
    });	
	

    jQuery("body").delegate("#buttonSalvarTaxa", "click", function(){
		var ceqpincidencia_taxa = jQuery('input[name=ceqpincidencia_taxa]:radio:checked').val();
		var ceqpobroid_taxa 	= jQuery('#ceqpobroid_taxa').val();
		var ceqpoid				= jQuery('#ceqpoid').val();
		
		jQuery('#mensagem').hide();
		jQuery('#informativos').hide();
        jQuery("input[name=ceqpincidencia_taxa]:radio").removeClass("alerta");
        jQuery("#ceqpobroid_taxa").removeClass("alerta");
        var erros = 0;
        
        if(ceqpincidencia_taxa == "") {
            jQuery("input[name=ceqpincidencia_taxa]:radio").addClass("alerta");
            erros++;
        }
        
        if(ceqpobroid_taxa == "") {
        	jQuery("#ceqpobroid_taxa").addClass("alerta");
        	erros++;
        }
        
        if(erros > 0) {
        	jQuery('#mensagem').html("");
        	jQuery('#mensagem').addClass("alerta");
        	jQuery('#mensagem').show();
            jQuery(this).attr('disabled', 'disabled');
            jQuery(this).addClass('desabilitado');
        }else{
        	jQuery(this).removeAttr('disabled');
        	jQuery(this).removeClass('desabilitado');
        	
        	jQuery.ajax({
        		url: 'cad_configuracao_equipamento.php',
        		type: 'post',
        		data: {
        			acao 				: 'adicionarTaxa',
        			ceqpobroid_taxa		: ceqpobroid_taxa,
        			ceqpincidencia_taxa	: ceqpincidencia_taxa,
        			ceqpoid				: ceqpoid
        		},
        		success: function(data){
        			var resultado = jQuery.parseJSON(data);
        			if(resultado.status === 'sucesso'){
        				var incidencia = "";
        				var cor = "";
        				if(parseInt(resultado.total)%2 == 0){
        					cor = "par";
        				}
        				
        				if(ceqpincidencia_taxa == "E"){
        					incidencia = "Cobrar a Cada Equipamento";
        				}else if(ceqpincidencia_taxa == "C"){
        					incidencia = "Cobrar a Cada Contrato";
        				}
        				
        				obrigacao = jQuery('#ceqpobroid_taxa option:selected').text();
        				//

        				if($('#config_equip_ct_obrig_fin').val() == 1){
        					taxasCadastradas = "<tr class='" + cor + "' id='taxa_"+ceqpobroid_taxa+"'><td>"+obrigacao+"</td><td>"+incidencia+"</td><td class='centro'><a href='javascript:void(0);' class='removerSessionTaxa' dir='" + ceqpobroid_taxa + "'><img title='Excluir' src='images/icon_error.png' class='icone'></a></td></tr>";	
        				}else{
        					taxasCadastradas = "<tr class='" + cor + "' id='taxa_"+ceqpobroid_taxa+"'><td>"+obrigacao+"</td><td>"+incidencia+"</td><td class='centro'></td></tr>";	
        				}
        				
        				jQuery("#lista-taxas-cadastradas tbody").append(taxasCadastradas);
        				
        				
        				jQuery('#ceqpobroid_taxa').val("");
        				jQuery("#incidencia_taxa").show();
        				jQuery("#buttonSalvarTaxa").removeAttr('disabled');
        				jQuery("#buttonSalvarTaxa").removeClass('desabilitado');
        				
        				if(jQuery('#ceqpobroid_taxa').val() == ''){
        					jQuery("#buttonSalvarTaxa").attr('disabled',true);
        					jQuery("#buttonSalvarTaxa").addClass('desabilitado');
        					jQuery("#incidencia_taxa").hide();
        					jQuery("#incidencia_taxa input").attr('checked', false);
        				}else{
        					jQuery("#ceqpincidencia_taxa_equ").attr('checked', true);
        				}

        			}else{
        				jQuery('#mensagem').html("Obrigação Financeira Taxa já cadastrada para esta Configuração de Equipamento.");
        	        	jQuery('#mensagem').removeClass("sucesso");
        	        	jQuery('#mensagem').addClass("alerta");
        	        	jQuery('#mensagem').show();
        			}
        		}
        	});
        }        
        
	});
    
    jQuery("body").delegate("#ceqpobroid", "change", function(){
    	if($('#config_equip_ct_disp_comercial').val() == 1 && $('#ceqpobroid').val() != ""){
    		$('#fieldsetDisponibilidade').removeAttr('disabled');
    	}else{
			$('#fieldsetDisponibilidade').attr('disabled','disabled');
    	}
    });

    jQuery("body").delegate("#ceqpobroid_taxa", "change", function(){
		jQuery("#incidencia_taxa").show();	
		jQuery("#buttonSalvarTaxa").removeAttr('disabled');
		jQuery("#buttonSalvarTaxa").removeClass('desabilitado');	

		
		
		if(jQuery('#ceqpobroid_taxa').val() == ''){
			jQuery("#buttonSalvarTaxa").attr('disabled',true);
			jQuery("#buttonSalvarTaxa").addClass('desabilitado');
			jQuery("#incidencia_taxa").hide();
			jQuery("#incidencia_taxa input").attr('checked', false);
			
			jQuery("#table_taxa").hide();
			limpaTaxa();
			
		}else{
			jQuery("#ceqpincidencia_taxa_equ").attr('checked', true);
			jQuery("#table_taxa").show();
		}
	});
	

	jQuery("body").delegate(".removerSessionTaxa", "click", function(){
		
		if(confirm("Você deseja deletar esta taxa?")){
			var id = jQuery(this).attr('dir');
			jQuery.ajax({
				url: 'cad_configuracao_equipamento.php',
				type: 'post',
				data: {
					acao 				: 'removeSessionTaxa',
					ceqpobroid_taxa		: id
				},
				success: function(data){
					var resultado = jQuery.parseJSON(data);
					
					if(resultado.status === 'sucesso'){
						jQuery("#taxa_"+id+"").hide();
					}
				}
			});
		}
	});
	

	jQuery("body").delegate(".deletarTaxa", "click", function(){
		
		if(confirm("Você deseja deletar esta taxa?")){
			var id = jQuery(this).attr('dir');
			jQuery.ajax({
				url: 'cad_configuracao_equipamento.php',
				type: 'post',
				data: {
					acao 			: 'deletarTaxa',
					ceqptxoid		: id
				},
				success: function(data){
					var resultado = jQuery.parseJSON(data);
					
					if(resultado.status === 'sucesso'){
						jQuery('#mensagem').html("Registro excluído com sucesso.");
						jQuery('#mensagem').removeClass("alerta");
						jQuery('#mensagem').addClass("sucesso");
						jQuery('#mensagem').show();
						jQuery("#taxa_"+id+"").hide();
					}
				}
			});
		}
});

	// Ações do form de Pesquisar Equipamento
	jQuery('body').delegate('#ceqpprdoid', 'change', function(){
		
		jQuery.ajax({
	        url: 'cad_configuracao_equipamento.php',
	        type: 'post',
	        
	        data: {
	        		prdoid : $(this).val(),
	        		acao : 'pesquisarEquipamento'
	        },
	        success: function(data){
	        	// Transforma a string em objeto JSON
                var resultado = jQuery.parseJSON(data);

                tipo_cadastro = (resultado[0].prdtp_cadastro == 'P') ? 'Produto' : 'Serviço';
                $('#codigo-material').attr('href', 'cad_material_novo.php?acao=editar&prdoid='+resultado[0].prdoid);
                $('#codigo-material').html(resultado[0].prdoid);
                $('#tipo-cadastro').html(tipo_cadastro);
                $('#tipo-produto').html(resultado[0].ptidescricao);
                $('#tipo-imobilizado').html(resultado[0].imotdescricao);
                
                if($('#tipo_equipDCT').is(':checked') == true){
                	$('.tipo-imobilizado').hide();
                }else{
                	$('.tipo-imobilizado').show();
                }
                
                
	        }
	    });
		
	});

});

function excluirCliente(elemento) {

	var linhas = 0;
	var cor = "";
	var listaIDs = '';

	jQuery(elemento).closest('tr').remove();

	jQuery("#lista_clientes tbody").find("tr").each(function(id){
		jQuery(this).removeClass("par");		

		listaIDs += ',' + jQuery(this).attr('dir');
	});

	jQuery("#listaClientes").val(listaIDs);

	 jQuery("#lista_clientes tbody").find("tr").each(function(){

		if(cor == "") {
			jQuery(this).addClass("par");
			cor = "par";	
		} else {
			cor = "";
		}

		linhas++;
		
	});

	 if(linhas == 0){
	 	jQuery("#bloco_lista_clientes").hide();	
	 }
}

function buscaClientesPorTipoEquipamento(){

	jQuery.ajax({
        url: 'cad_configuracao_equipamento.php',
        type: 'post',
        
        data: {
        		equipamento_selected : jQuery("#equipamento_selected").val(),
        		ceqpprdoid : jQuery("#ceqpprdoid").val(),
        		tipo_equip : jQuery("input[name=tipo_equip]:checked").val(),
        		acao : 'buscarClienteTipoEquipamento'
        	},
        beforeSend: function(){
        	jQuery("#cliente").children().remove();
        },
        success: function(data){

        	var resultado = jQuery.parseJSON(data);

			if (resultado.length > 0) {

				if(jQuery("input[name=venda_restrita]:checked").val() == 'S'){
					jQuery("#bloco_clientes").show();
				}
				
				jQuery("input[name=venda_restrita]").attr('disabled',false);
				
            	jQuery.each(resultado, function(index, value) {

            		opt = "<option value='"+value.clioid+"'>" +value.clinome+ ' | '+value.doc+ "</option>" ;
            		jQuery("#cliente").append(opt);

                }); 

                jQuery("#cliente").removeClass('img_loader');  

                var clientes_selected = jQuery("#clientes_selected").val();
            	if(clientes_selected != ""){
            		jQuery("#cliente").val(clientes_selected.split(','));
            	}
            	
            } else {
            	
            	jQuery("#cliente").removeClass('img_loader');
            	jQuery("#bloco_clientes").hide();
            	jQuery("#venda_restritaNao").attr('checked', true);
				jQuery("input[name=venda_restrita]").attr('disabled',true);

            }
			
        }
    });
	
	
}

function buscaPorId(){

    jQuery('.tipo_equip').attr('disabled',true); // bloqueia equipamento
    jQuery('#ceqpprdoid').attr('disabled',true); // bloqueia equipamento    
    
    jQuery('#fieldsetConfigSoftware').attr('disabled',true); // bloqueia configuração de software
    
}
function buscaPorEqu(){

	jQuery('.tipo_equip').attr('disabled',false); // desbloqueia equipamento
	jQuery('#ceqpprdoid').attr('disabled',false); // desbloqueia equipamento
    
    var tipoEqu = jQuery("input[name=tipo_equip]:checked").val();
    if(tipoEqu)
        jQuery('#fieldsetConfigSoftware').attr('disabled',false); // desbloqueia configuração de software
    else
        jQuery('#fieldsetConfigSoftware').attr('disabled',true); // bloqueia configuração de software
	
}
