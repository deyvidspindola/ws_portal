function pesquisarFornecedor(){
	
	var fornecedor = $("#getFornecedor").val();
	
	if(fornecedor.length < 3){
		$("#mensagem").attr("class","mensagem alerta");
		$("#mensagem").html("Digite pelo menos 3 caracteres para a busca de fornecedor.");
		$("#mensagem").css("display","block");
		return false;
	}
	
	$("#getFornecedor").val('');
	$("#foroid").html("<option value='0'>Aguarde Carregando Lista...</option>");
	
	// Monta lista de fornecedores
	$.post( ACTION + '?acao=getFornecedores',{
		fornecedor : fornecedor	
	}, function(data) {

		var resultado = $.parseJSON(data);
		$("#foroid").html('');
		
		if (resultado.length > 0) {
			
			var opt = "";
			
			$.each(resultado, function(index, value) {				
				opt = "<option value='"+value.foroid+"'>" +value.forfornecedor+ "</option>" ;
				$("#foroid").append(opt);
			}); 
			
			alteraBotaoPesquisar("Fornecedor","getFornecedor");
			
			$("#mensagem").css("display","none");
			$("#foroid").parent().css("display","block");
		} else {
			$("#foroid").val('');
			$("#foroid").parent().css("display","none");
			$("#mensagem").attr("class","mensagem alerta");
			$("#mensagem").html("Nenhum fornecedor encontrado com o termo pesquisado.");
			$("#mensagem").css("display","block");
		}

	});
	
	$('#listaFornecedor').show();
}

$(function() {

	$("#foroid").on('change', function(){
		$("#getFornecedor").val(jQuery(':selected', jQuery(this)).text());
		$('#listaFornecedor').hide();
	});

	$('#btGetFornecedor').click(pesquisarFornecedor);
	
	$('#listaFornecedor').hide();
	
	// Gif Loading e Mensagem de erro
	if ($('#mensagem').text() == ''){
		$('#mensagem').hide();
	}

	$('#carregando').hide();

});

/**
 * Aba principal
 */
jQuery(function() {    	
	
	/*jQuery('body').delegate("#buttonVoltar","click",function(){
		history.go(-1);
	})*/
	
    jQuery('#data_inicial').periodo('#data_final');
	
	$('.numerico').keyup( function(e){
		var campo = '#'+this.id;
		var valor = $(campo).val().replace(/[^0-9]+/g,'');

		$(campo).val(valor);
	});

	// Ações do form
	jQuery('body').delegate('#buttonPesquisar', 'click', function(){
		
		// Pega value do botão clicado
		var acaoValor = jQuery(this).val();

		// Troca ação para o valor correspondente e dá submit no form
		jQuery('#acao').val(acaoValor).closest('form').submit();
	});
	
	// Radio box do serial
	jQuery("body").delegate("#sem_serial","click",function(){
		if($(this).is(":checked") == false){
			$("#numero_serie").removeAttr("readonly");
			$("#numero_serie").removeClass("desabilitado");
			$("#numero_serie").addClass("obrigatorio");
			$("#numero_serie").focus();
		}else{
			$("#numero_serie").attr("readonly",true);
			$("#numero_serie").addClass("desabilitado");
			$("#numero_serie").val("");
		}
	});
	
	/**
	 * Fornecedor
	 */
	// Pesquisar fornecedores
	jQuery('body').delegate("#buttonPesquisarFornecedor","click",function(){
		if(jQuery.trim(jQuery("#fornecedor_busca").val())!="" && jQuery("#fornecedor_busca").val().length >= 3){
			
			jQuery("#fornecedoresEncontrados").parent().css("display","none");
			jQuery("#carregando_fornecedor").show();
		
			jQuery.ajax({
				url: ACTION,
				type: 'post',
				data: {
					fornecedor_busca : jQuery("#fornecedor_busca").val(),
					acao : 'pesquisarFornecedor'
				},
				beforeSend: function(){
					jQuery("#fornecedoresEncontrados").html("");
				},
				success: function(data){
					
					// Transforma a string em objeto JSON
					var resultado = jQuery.parseJSON(data);
					
					jQuery("#fornecedoresEncontrados").html("<legend>Fornecedores Encontrados</legend>");
					
					if (resultado.length > 0) {
						var opt = "";
						jQuery.each(resultado, function(index, value) {
							
							opt = "<option value='"+value.foroid+"'>" +value.forfornecedor+ "</option>" ;
							jQuery("#fornecedoresEncontrados").append(opt);
						}); 
						
						jQuery("#mensagem").css("display","none");
						jQuery("#fornecedoresEncontrados").parent().css("display","block");
					} else {
						jQuery("#fornecedoresEncontrados").parent().css("display","none");
						jQuery("#mensagem").attr("class","mensagem alerta");
						jQuery("#mensagem").html("Nenhum fornecedor encontrado com o termo pesquisado.");
						jQuery("#mensagem").css("display","block");
					}
					$("#carregando_fornecedor").hide();
				}
			});
			
		}else{
			jQuery('#mensagem').html("Digite pelo três letras para fazer a busca");
        	jQuery('#mensagem').addClass("alerta");
        	jQuery('#mensagem').show();
		}
	})
	
	// Altera funcionalidade de botão Limpar Fornecedores para Pesquisar
	jQuery("body").delegate("#buttonLimparFornecedor", "click", function(){
		jQuery("#foroid").val("");
		jQuery("#fornecedor_busca").val("");

		jQuery("#buttonLimparFornecedor").text("Pesquisar");
		jQuery("#buttonLimparFornecedor").attr("value","pesquisarFornecedor");
		jQuery("#buttonLimparFornecedor").attr("name","buttonPesquisarFornecedor");
		jQuery("#buttonLimparFornecedor").attr("id","buttonPesquisarFornecedor");
		
		jQuery("#fornecedor_busca").removeAttr("readonly");
		
		jQuery("#fornecedor_busca").removeClass('desabilitado');
		jQuery("#fornecedor_busca").focus();
	});
	
	// Ação selecionar fornecedor
	jQuery("body").delegate("#fornecedoresEncontrados", "click", function(){
		var clioid = jQuery(this).val();
		var clinome = jQuery(this).find(":selected").text();
		
		// Caso tenha clicado em uma parte vazia do campo de opções
		if (clioid == "" || clinome == null) {
			return false;
		}
		
		// Preenche com o equipamento selecionado
		jQuery("#foroid").val(clioid);
		jQuery("#fornecedor_busca").val(clinome);

		alteraBotaoPesquisar("Fornecedor","fornecedor_busca");
		
		jQuery("#fornecedoresEncontrados").parent().css("display","none");
	});
	
	
	/**
	 * Produto
	 */
	// Pesquisar produto
	jQuery('body').delegate("#buttonPesquisarProduto","click",function(){
		if(jQuery.trim(jQuery("#produto_busca").val())!="" && jQuery("#produto_busca").val().length >= 3){
			
			jQuery("#produtoEncontrado").parent().css("display","none");
			jQuery("#carregando_produto").show();
			
			jQuery.ajax({
				url: ACTION,
				type: 'post',
				data: {
					produto_busca : jQuery("#produto_busca").val(),
					acao : 'pesquisarProduto'
				},
				beforeSend: function(){
					jQuery("#produtoEncontrado").html("");
				},
				success: function(data){
					
					// Transforma a string em objeto JSON
					var resultado = jQuery.parseJSON(data);
					
					jQuery("#produtoEncontrado").html("<legend>Produtos Encontrados</legend>");
					
					if (resultado.length > 0) {
						var opt = "";
						jQuery.each(resultado, function(index, value) {
							
							opt = "<option value='"+value.prdoid+"'>" +value.prdproduto+ "</option>" ;
							jQuery("#produtoEncontrado").append(opt);
						}); 
						
						jQuery("#mensagem").css("display","none");
						jQuery("#produtoEncontrado").parent().css("display","block");
					} else {
						jQuery("#produtoEncontrado").parent().css("display","none");
						jQuery("#mensagem").attr("class","mensagem alerta");
						jQuery("#mensagem").html("Nenhum produto encontrado com o termo pesquisado.");
						jQuery("#mensagem").css("display","block");
					}
					$("#carregando_produto").hide();
				}
			});
			
		}else{
			jQuery('#mensagem').html("Digite pelo três letras para fazer a busca");
        	jQuery('#mensagem').addClass("alerta");
        	jQuery('#mensagem').show();
		}
	})
	// Altera funcionalidade de botão Limpar produto para Pesquisar
	jQuery("body").delegate("#buttonLimparProduto", "click", function(){
		jQuery("#prdoid").val("");
		jQuery("#produto_busca").val("");
		
		jQuery("#buttonLimparProduto").text("Pesquisar");
		jQuery("#buttonLimparProduto").attr("value","pesquisarProduto");
		jQuery("#buttonLimparProduto").attr("name","buttonPesquisarProduto");
		jQuery("#buttonLimparProduto").attr("id","buttonPesquisarProduto");
		
		jQuery("#produto_busca").removeAttr("readonly");
		
		jQuery("#produto_busca").removeClass('desabilitado');
		jQuery("#produto_busca").focus();
	});
	
	// Ação selecionar produto
	jQuery("body").delegate("#produtoEncontrado", "click", function(){
		var clioid = jQuery(this).val();
		var clinome = jQuery(this).find(":selected").text();
		
		// Caso tenha clicado em uma parte vazia do campo de opções
		if (clioid == "" || clinome == null) {
			return false;
		}
		
		// Preenche com o produto selecionado
		jQuery("#prdoid").val(clioid);
		jQuery("#produto_busca").val(clinome);
		
		alteraBotaoPesquisar("Produto","produto_busca");
		
		jQuery("#produtoEncontrado").parent().css("display","none");
	});
	
	
	/**
	 * Cliente
	 */
	// Pesquisar cliente
	jQuery('body').delegate("#buttonPesquisarCliente","click",function(){
		if(jQuery.trim(jQuery("#cliente_busca").val())!="" && jQuery("#cliente_busca").val().length >= 3){
			
			jQuery("#clienteEncontrado").parent().css("display","none");
			jQuery("#carregando_cliente").show();
			
			jQuery.ajax({
				url: ACTION,
				type: 'post',
				data: {
					cliente_busca : jQuery("#cliente_busca").val(),
					acao : 'pesquisarCliente'
				},
				beforeSend: function(){
					jQuery("#clienteEncontrado").html("");
				},
				success: function(data){
					
					// Transforma a string em objeto JSON
					var resultado = jQuery.parseJSON(data);
					
					jQuery("#clienteEncontrado").html("<legend>Clientes Encontrados</legend>");
					
					if (resultado.length > 0) {
						var opt = "";
						jQuery.each(resultado, function(index, value) {
							
							opt = "<option value='"+value.clioid+"'>" +value.clinome+ "</option>" ;
							jQuery("#clienteEncontrado").append(opt);
						}); 
						
						jQuery("#mensagem").css("display","none");
						jQuery("#clienteEncontrado").parent().css("display","block");
					} else {
						jQuery("#clienteEncontrado").parent().css("display","none");
						jQuery("#mensagem").attr("class","mensagem alerta");
						jQuery("#mensagem").html("Nenhum cliente encontrado com o termo pesquisado.");
						jQuery("#mensagem").css("display","block");
					}
					$("#carregando_cliente").hide();
				}
			});
			
		}else{
			jQuery('#mensagem').html("Digite pelo três letras para fazer a busca");
        	jQuery('#mensagem').addClass("alerta");
        	jQuery('#mensagem').show();
		}
	})
	// Altera funcionalidade de botão Limpar cliente para Pesquisar
	jQuery("body").delegate("#buttonLimparCliente", "click", function(){
		jQuery("#clioid").val("");
		jQuery("#cliente_busca").val("");
		
		jQuery("#buttonLimparCliente").text("Pesquisar");
		jQuery("#buttonLimparCliente").attr("value","pesquisarCliente");
		jQuery("#buttonLimparCliente").attr("name","buttonPesquisarCliente");
		jQuery("#buttonLimparCliente").attr("id","buttonPesquisarCliente");
		
		jQuery("#cliente_busca").removeAttr("readonly");
		
		jQuery("#cliente_busca").removeClass('desabilitado');
		jQuery("#cliente_busca").focus();
	});
	
	// Ação selecionar cliente
	jQuery("body").delegate("#clienteEncontrado", "click", function(){
		var clioid = jQuery(this).val();
		var clinome = jQuery(this).find(":selected").text();
		
		// Caso tenha clicado em uma parte vazia do campo de opções
		if (clioid == "" || clinome == null) {
			return false;
		}
		
		//carregaOperacoes(clioid); // TODO: habilitar na entrega 8 CT
		
		// Preenche com o cliente selecionado
		jQuery("#clioid").val(clioid);
		jQuery("#cliente_busca").val(clinome);
		
		alteraBotaoPesquisar("Cliente","cliente_busca");
		
		jQuery("#clienteEncontrado").parent().css("display","none");
	});
	
	
	// Busca Representante estoque
	jQuery('body').delegate("#repoid","change",function(){
		jQuery("#mensagem").css("display","none");
	
		jQuery.ajax({
			url: ACTION,
			type: 'post',
			data: {
				repoid : jQuery(this).val(),
				acao : 'pesquisarRepresentanteEstoque'
			},
			beforeSend: function(){
				jQuery("#relroid").html("<option value=''>Selecione</option>");
			},
			success: function(data){
				
				// Transforma a string em objeto JSON
				var resultado = jQuery.parseJSON(data);
				
				if (resultado.length > 0) {
					var opt = "";
					jQuery.each(resultado, function(index, value) {
						opt = "<option value='"+value.relroid+"'>" +value.repnome+ "</option>" ;
						jQuery("#relroid").append(opt);
					}); 
					
				} else {
					jQuery("#mensagem").attr("class","mensagem alerta");
					jQuery("#mensagem").html("Nenhum Representante Estoque encontrado para este Representante.");
					jQuery("#mensagem").css("display","block");
				}
			}
		});
	})
	
	if (jQuery("#clioid").val() != "") {
		alteraBotaoPesquisar("Cliente","cliente_busca");
	}
	if (jQuery("#prdoid").val() != "") {
		alteraBotaoPesquisar("Produto","produto_busca");
	}
	if (jQuery("#foroid").val() != "") {
		alteraBotaoPesquisar("Fornecedor","fornecedor_busca");
	}
	
	setInterval("jQuery('.blinking').fadeOut().fadeIn();", 1200 );
	
}); 

// Altera funcionalidade de botão Pesquisar para Limpar
function alteraBotaoPesquisar(id,id_busca) {
	jQuery("#buttonPesquisar" + id).text("Limpar");
	jQuery("#buttonPesquisar" + id).attr("value","limpar");
	jQuery("#buttonPesquisar" + id).attr("name","buttonLimpar" + id);
	jQuery("#buttonPesquisar" + id).attr("id","buttonLimpar" + id);
	
	jQuery("#" + id_busca).attr("readonly","readonly");
	
	jQuery("#" + id_busca).addClass('desabilitado');
}

function carregando(){
	jQuery('#carregando_importacao').show();
	jQuery('#btImportar').hide();
}

function carregaOperacoes(clioid){
		
	jQuery.ajax({
		url: ACTION,
		type: 'post',
		data: {
			clioid : clioid,
			acao : 'pesquisarOperacoes'
		},
		beforeSend: function(){
			jQuery("#operacao").html("");
		},
		success: function(data){
			
			// Transforma a string em objeto JSON
			var resultado = jQuery.parseJSON(data);
			
			if (resultado.length > 0) {
				var opt = "<option value=''>Selecione</option>";
				jQuery("#operacao").append(opt);
				jQuery.each(resultado, function(index, value) {
					
					opt = "<option value='"+value.octoid+"'>[" +value.octoprid+"] "+value.octnome+ "</option>" ;
					jQuery("#operacao").append(opt);
				}); 
				
				jQuery("#mensagem").css("display","none");
				jQuery("#operacao").parent().css("display","block");
			}
		}
	});
	
}