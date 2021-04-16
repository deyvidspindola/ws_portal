$(document).ready(	function() {
	
	// Máscara CPF/CNPJ
    jQuery('#documento').keyup( function(e){
		
    	var conteudo = jQuery('#documento').val();
		var valor = verifica_mascara(conteudo);
		
		jQuery('#documento').val(valor);
	});
	
	
	
	// Máscara Numérica
    jQuery('.numerico').keyup( function(e){
		var campo = '#'+this.id;
		var valor = jQuery(campo).val().replace(/[^0-9,]+/g,'');

		jQuery(campo).val(valor);
	});
    
 // Mascara Caracter
	jQuery('.alfanumCar').keyup( function(e){
		var campo = '#'+this.id;
		var valor = jQuery(campo).val().replace(/[^a-zA-Z0-9 @!&?áéíóúÁÉÍÓÚàèìòùÀÈÌÒÙãõÃÕ]+/g,'');

		jQuery(campo).val(valor);
	});
	
	// Máscara Alfanumérica
	jQuery('.alfanum').keyup( function(e){
		var campo = '#'+this.id;
		var valor = jQuery(campo).val().replace(/[^a-zA-Z0-9 ]+/g,'');

		jQuery(campo).val(valor);
	});
	
	
	 $('#pesquisar').click(function(){
	       
		 var verificarData = false;
		 
		 if($("#data_ini").val() === '' && $("#data_fim").val() ==='' && $("#status_apolice").val() !==''){
			 
			 verificarData = true;
		 
	     }else if($("#documento").val() == "" && $("#num_contrato").val() == "" && $("#placa").val() == "" && $("#status_apolice").val() == "" && $("#data_ini").val() === '' && $("#data_fim").val() ===''){
			 
	    	 verificarData = true;

		 }

		 if(verificarData){

			 $("#data_ini").css("background-color","#FFFFC0");
			 $("#data_fim").css("background-color","#FFFFC0");
			 criaAlerta("Existem campos obrigatórios não preenchidos.","alerta");
			 return false;
		 }


		 $("#data_ini").css("background-color",	"#FFFFFF");
		 $("#data_fim").css("background-color",	"#FFFFFF");

		 $("#mensagem ").hide();

		 if(dataValida($("#data_ini").val()) == false) {
			 $("#data_ini").css("background-color","#FFFFC0");
			 $("#data_ini").focus();
			 criaAlerta("Data Inicial é inválida.","alerta");
			 return false;
		 }

		 if(dataValida($("#data_fim").val()) == false) {
			 $("#data_fim").css("background-color","#FFFFC0");
			 $("#data_fim").focus();
			 criaAlerta("Data Final é inválida.","alerta");
			 return false;
		 }

		 if ($("#data_ini").val() == "" && $("#data_fim").val() !=""){
			 $("#data_ini").css("background-color","#FFFFC0");
			 $("#data_ini").focus();
			 criaAlerta("Infome a data Inicial.","alerta");
			 return false;

		 }else if($("#data_fim").val() == "" && $("#data_ini").val() !=""){
			 $("#data_fim").css("background-color","#FFFFC0");
			 $("#data_fim").focus();
			 criaAlerta("Infome a data Final.","alerta");
			 return false;

		 }else {

			 if (calculaPeriodo($("#data_ini").val(), $("#data_fim").val()) < 0){
				 $("#data_fim").css("background-color","#FFFFC0");
				 $("#data_fim").focus();
				 criaAlerta("Data Final não pode ser menor que Data Inicial.","alerta");
				 return false;
			 }
		 }

		 if(calculaPeriodo($("#data_ini").val(), $("#data_fim").val()) > 365) {
			 $("#data_ini").css("background-color","#FFFFC0");
			 $("#data_fim").css("background-color","#FFFFC0");
			 criaAlerta("Favor selecionar um período com intervalo menor que um ano.","alerta");
			 return false;
		 }
		
		 $('#btn_enviar').hide();
		 $('.resultado_pesquisa').html("");
		 $('.carregando').show();
		
		 $('#busca_dados').submit();

	 });


	 $('.detalhesForm').submit(function() {
		 $('#btn_enviar').hide();
		 $('.resultado_pesquisa').hide();
		 $('.carregando').show();
	 }); 

	 
	 $('#voltar').click(function(){
		 
		 $('.bloco_acoes').hide();
		 $('.resultado_pesquisa').hide();
		 $('.carregando').show();
	 });
	 
	 
	 $('#limpar').click(function(){
		 
		 $("#data_ini").val(""); 
         $("#data_fim").val("");
         $("#documento").val("");
         $("#num_contrato").val("");
         $("#placa").val("");
         $("#mensagem").hide();
         $("#data_ini").css("background-color",	"#FFFFFF");
     	 $("#data_fim").css("background-color",	"#FFFFFF");
         
         $("select#status_apolice").val('').attr('selected', true);
		 
	 });
	 
	 
	 $('body').delegate('.reenviarApolice','click', function() {

		 var contrato_cli = $(this).attr('contrato_cli');
	     var id_apolice   = $(this).attr('id_apolice');
		 
		 if(contrato_cli != ''){

			 $.ajax({
				 url : 'rel_produto_com_seguro.php',
				 type : 'post',
				 data : 'acao=ativarApolice&contrato_cli=' + contrato_cli +'&id_apolice=' + id_apolice,

				 beforeSend: function(){

					 $("#mensagem").hide();
					 $('.reenviarApolice').hide();
					 $('#linha_apo'+contrato_cli).hide();
					 $('#loading_linha_apo'+contrato_cli).show();
				 },
				 success: function(data) { 
					 
					 $('.reenviarApolice').show();
					 $('#linha_apo'+contrato_cli).show();
					 $('#loading_linha_apo'+contrato_cli).hide();

					 if(data == 1) {
						 
						 //criaAlerta('Apólice ativada com sucesso',"sucesso");
						 
						 alert('Apólice ativada com sucesso.');
						 $('#busca_dados').submit();
						 
					 }else{
						 
						 alert('Erro ao ativar apólice.\n'+data);
						 $('#busca_dados').submit();
						 
						 
						 //$("#mensagem ").html(data).removeAttr('class').addClass('mensagem alerta').addClass('alerta').show();
					 }

				 }
			 });
		 }

		 return false;
	 });
	 

	 $('body').delegate('.ativarApolice','click', function() {

		 var contrato_cli = $(this).attr('contrato_cli');
	     var id_apolice   = $(this).attr('id_apolice');
		 
		 if(contrato_cli != ''){

			 $.ajax({
				 url : 'rel_produto_com_seguro.php',
				 type : 'post',
				 data : 'acao=ativarApolice&contrato_cli=' + contrato_cli +'&id_apolice=' + id_apolice,

				 beforeSend: function(){

					 $("#mensagem").hide();
					 $('.ativarApolice').hide();
					 $('#linha_prop'+contrato_cli).hide();
					 $('#loading_linha_prop'+contrato_cli).show();
				 },
				 success: function(data) { 
					 
					 $('.ativarApolice').show();
					 $('#linha_prop'+contrato_cli).show();
					 $('#loading_linha_prop'+contrato_cli).hide();

					 if(data == 1) {
						 
						 //criaAlerta('Apólice ativada com sucesso',"sucesso");
						 
						 alert('Apólice ativada com sucesso.');
						 $('#busca_dados').submit();
						 
					 }else{
						 
						 alert('Erro ao ativar apólice.\n'+data);
						 $('#busca_dados').submit();
						 
						 
						 //$("#mensagem ").html(data).removeAttr('class').addClass('mensagem alerta').addClass('alerta').show();
					 }

				 }
			 });
		 }

		 return false;
	 });
	 
	 
	 $('body').delegate('.reenviarEmail','click', function() {

		 var contrato_cli = $(this).attr('contrato_cli');
		 var num_apolice  = $(this).attr('num_apolice');

		 if(contrato_cli != ''){

			 $.ajax({
				 url : 'rel_produto_com_seguro.php',
				 type : 'post',
				 data : 'acao=reenviarMail&contrato_cli=' + contrato_cli +'&num_apolice=' + num_apolice,

				 beforeSend: function(){

					 $('.reenviarEmail').hide();
					 $("#mensagem").hide();
					 $('#linha_'+contrato_cli).hide();
					 $('#loading_linha_'+contrato_cli).html('<img src="images/ajax-loader-circle.gif" />');
				 },
				 success: function(data) { 

					 $('.reenviarEmail').show();
					 $('#linha_'+contrato_cli).show();
					 $('#loading_linha_'+contrato_cli).html("");

					 var resultado = jQuery.parseJSON(data);

					 if(resultado == 1) {
						 
						 criaAlerta('E-mail enviado com sucesso',"sucesso");
					 }else{
						 
						 criaAlerta(resultado,"alerta");
					 }
				 }

			 });
		 }

		 return false;
	 });
			
});

/**
 * Aplica máscara para CPF ou CNPJ de acordo a quantidade de números digitados
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
    $("#mensagem ").text(msg).removeAttr('class').addClass('mensagem alerta').addClass(status).show();
}

/**
 * Calcula diferença de dias entre data final e data inicial
 * 
 * @param Date inicio
 * @param Date fim
 * 
 * @return Integer
 */
function calculaPeriodo(inicio, fim) {
    
    var qtdDias   = 0;
    var diferenca = 0;

    arrDataInicio = inicio.toString().split('/');
    arrDataFim    = fim.toString().split('/');

    dateInicio = new Date(arrDataInicio[1]+"/"+arrDataInicio[0]+"/"+arrDataInicio[2]);
    dateFim    = new Date(arrDataFim[1]+"/"+arrDataFim[0]+"/"+arrDataFim[2]);  

    diferenca = dateFim - dateInicio;
    qtdDias = Math.round(diferenca/(1000*60*60*24));

    return qtdDias;
}

function dataValida(data) {
	   
    arrData = data.split('/');
    
    dia = arrData[0];
    mes = arrData[1];
    ano = arrData[2];
    
    if ((mes < 1) || (mes > 12))
        return false;
    
    if ((mes == 1) || (mes == 3) || (mes == 5) || (mes == 7) || (mes == 8) || (mes == 10) || (mes == 12))
        if ((dia < 01) || (dia > 31))
            return false;
 
    if ((mes == 4) || (mes == 6) || (mes == 9) || (mes == 11)) 
        if ((dia < 01) || (dia > 30)) 
            return false;
    
    if (mes == 2) {
        if ((ano % 4) == 0) {
            if ((dia < 1) || (dia > 29)) {
                return false;
            }
        } else {
            if ((dia < 1) || (dia > 28)) {
                return false;
            }
        }
    }
    
    return true;
}