jQuery(function(){
	
	/**
	 * Validação para o campo contrato
	 * Deve aceitar apenas caracteres numéricos
	 */
	jQuery('#contrato').keypress(function(){
		jQuery(this).val(jQuery(this).val().replace(/[^\d]/g, ''));
	}).keyup(function(){
		jQuery(this).trigger('keypress');
	}).blur(function(){
		jQuery(this).trigger('keypress');
	})
	
	
	// M�scara CPF/CNPJ
    jQuery('#documento').keyup( function(e){
		
    	var conteudo = jQuery('#documento').val();
		var valor = verifica_mascara(conteudo);
		
		jQuery('#documento').val(valor);
	});
           
	
	jQuery('.field_cpf').hide();
	
    
	/**
	 * Click do botão Pesquisar
	 */
	jQuery('#btn_pesquisar').click(function(){
		
		removeAlerta();
		
		var ckeckbox_null = true;
	
		jQuery('#msg').html('');
		jQuery('.resultado_pesquisa').hide();
		jQuery('.carregando').show();
		jQuery('#btn_enviar').hide();
		
		//verifica se alguma ckeckbox foi marcado
		 $('.checkbox').each(
			         function(){
			          
			        	 if($(this).is(':checked')){
			        		 ckeckbox_null = false;
			        	 }
			         }
			      );
		
		if($('#contrato').val() == '' && ckeckbox_null == true && $('#cliente').val() == '' && $('#documento').val() == ''
			&& $("#tipo_contrato").val() == "" && $("#obrigacao_financeira").val() == '' /*&& $("#periodicidade_faturamento").val() == ''*/ ){
			
			criaAlerta('Informe algum dado para efetuar a pesquisa.');
			
			jQuery('.carregando').hide();
			jQuery('#btn_enviar').show();
			
			return false;
		}
		
		 $('#filtro').submit();
		
	});
	
	
	
	jQuery('input[name="todos"]').click(function() {
		
		 if($(this).is(':checked')){
			 jQuery('input[name="nivel[]"]').attr('checked', 'checked');
    	 }else{
    		 jQuery('input[name="nivel[]"]').attr('checked', false);
    	 }

	});
	
	
    jQuery('#limpar').click(function(){
		
    	removeAlerta();
    	//limpa os inputs
		jQuery('#contrato').val('');
		jQuery('#cliente').val('');
		jQuery('#documento').val('');
	
		//desmarca todos os checkboxs
		 $('.checkbox').each(
			         function(){
			            $(this).attr("checked", false);
			         }
			      );
		
		//limpar os combos
		$("select#tipo_contrato").val('').attr('selected', true);
		$("select#obrigacao_financeira").val('').attr('selected', true);
		//$("select#periodicidade_faturamento").val('').attr('selected', true);
    		
	});


	/**
	 * Click do botão Gerar CSV
	 */
	jQuery('#gerar_csv').click(function(){

		removeAlerta();

		var ckeckbox_null = true;

		//jQuery('#msg').html('');
		//jQuery('.resultado_pesquisa').hide();
		//jQuery('.carregando').show();
		//jQuery('#btn_enviar').hide();

		//verifica se alguma ckeckbox foi marcado
		$('.checkbox').each(
			function(){

				if($(this).is(':checked')){
					ckeckbox_null = false;
				}
			}
		);

		if($('#contrato').val() == '' && ckeckbox_null == true && $('#cliente').val() == '' && $('#documento').val() == ''
			&& $("#tipo_contrato").val() == "" && $("#obrigacao_financeira").val() == '' /*&& $("#periodicidade_faturamento").val() == ''*/ ){

			criaAlerta('Informe algum dado para efetuar a pesquisa.');

			jQuery('.carregando').hide();
			jQuery('#btn_enviar').show();

			return false;
		}
		jQuery('input[name="acao"]').val('gerar_csv');
		jQuery('form[name="filtro"]').submit();
		//$('#filtro').submit();

	});



	// Inclusão e Edição 
	
	/**
	 * @tag input(type=button)
	 * @Event OnClick
	 */
	jQuery('#btn_novo').click(function() {
		jQuery('input[name="acao"]').val('novo');
		jQuery('form[name="filtro"]').submit();
	});
	
        // ORGMKTOTVS-3517 - [CRIS]
        jQuery('#btn_excluir_massivo').click(function() {
		jQuery('input[name="acao"]').val('excluir_massivo');
		jQuery('form[name="filtro"]').submit();
	});
        // FIM ORGMKTOTVS-3517 - [CRIS]
        
	jQuery('body').delegate('a.link_editar', 'click', function(){
		
		var id = jQuery(this).data('id');
		
		/**
		 * STI 84969
		 * Antes de abrir tela para edição é verificado o tipo de parâmetro
		 */
		jQuery.ajax({
			
			url : 'fin_parametros_faturamento.php',
			type : 'post',
			data : 'acao=verificarTipoParametro&id_parametro='+id ,
			success : function(data) {
		
				if(data == 1) {

					jQuery('input[name="acao"]').val('editar');
					jQuery('input[name="parfoid"]').val(id);
					jQuery('form[name="filtro"]').submit();

				}else{

					var dados = jQuery.parseJSON(data)
					
					if(dados.tipo_erro == 2){
						criaAlerta(dados.msg,"alerta");
						return false;
					}
					
					if(dados.tipo_erro == 3){
						criaAlerta(dados.msg,"erro");
						return false;
					}
					
				}
			}
		});
		
	});
	
});



/**
 * Aplica máscar para CPF ou CNPJ de acordo a quantidade de números digitados
 * 
 * @param conteudo
 * @returns
 */
function verifica_mascara(conteudo) {
	
	//se quantidade de dígitos for menor ou igual a 14, aplica máscara para CPF
	if (conteudo.length <= 14){

		conteudo = conteudo.replace( /\D/g , ""); //Remove tudo o que não é dígito
		conteudo = conteudo.replace( /(\d{3})(\d)/ , "$1.$2"); //Coloca um ponto entre o terceiro e o quarto dígitos
		conteudo = conteudo.replace( /(\d{3})(\d)/ , "$1.$2"); //Coloca um ponto entre o terceiro e o quarto dígitos
		//de novo (para o segundo bloco de números)
		conteudo = conteudo.replace( /(\d{3})(\d{1,2})$/ , "$1-$2"); //Coloca um hífen entre o terceiro e o quarto dígitos
	
	//aplica máscara para CNPJ
	}else{

		conteudo = conteudo.replace( /\D/g , ""); //Remove tudo o que não é dígito
		conteudo = conteudo.replace( /^(\d{2})(\d)/ , "$1.$2"); //Coloca ponto entre o segundo e o terceiro dígitos
		conteudo = conteudo.replace( /^(\d{2})\.(\d{3})(\d)/ , "$1.$2.$3"); //Coloca ponto entre o quinto e o sexto dígitos
		conteudo = conteudo.replace( /\.(\d{3})(\d)/ , ".$1/$2"); //Coloca uma barra entre o oitavo e o nono dígitos
		conteudo = conteudo.replace( /(\d{4})(\d)/ , "$1-$2"); //Coloca um hífen depois do bloco de quatro dígitos
	}
   
   return conteudo ;
}


function criaAlerta(msg, status) { 
	$("#mensagem").show();
    $("#mensagem").text(msg).removeAttr('class').addClass('mensagem alerta').addClass(status).show();
}


function removeAlerta() {
    $("#mensagem").hide();
}



function ver_dados(valor, tipo){
	
	var w= 700;//largura
	var h= 300;//altura

	LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
	TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
	
	jQuery.ajax({
		
		url : 'modulos/Financas/View/fin_parametros_faturamento/popup_parametros_faturamento.php',
		type : 'post',
		data : 'acao=insere_sessao&dados=' + valor +'&tipo=' + tipo,
		success : function(data) {
			
			if(data == 1) {

				window.open('modulos/Financas/View/fin_parametros_faturamento/popup_parametros_faturamento.php','ContrlWindow','status=no,location=no,scrollbars=yes,menubar=no,toolbar=no,top='+TopPosition+',left='+LeftPosition+',width=700,height=300');

			}else{

				alert('Falha ao exibir obrigação financeira');
				return false;
			}
		}
	});
	

}

