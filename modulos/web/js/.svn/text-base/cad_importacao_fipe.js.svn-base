
jQuery(function(){
	
  // $("#exibe_loanding").hide();
   $('#enviar_arquivo').attr('disabled', 'disabled'); 
   $('#fakeupload').val('');
   $('#fakeupload').attr('readonly','readonly');
   
   $("#enviar_arquivo").click(	function() {
	   
	  /* if($('#tipo_veiculo').val() == ''){
		   
		   criaAlerta('É necessário selecionar o tipo de veículo.', 'alerta');
		 
	   }else */if($('#arq_importacao').val() == ''){
		  
		   criaAlerta('É necessário informar o aquivo para importação.', 'alerta');
		
	   }else{
		   
		   $('#tipo').val('dadosFipe');
		   
		   //altera o mouse para modo de espera
		   $('body').mouseover(function(){
			   $(this).css({cursor: 'wait'});
		   });

		   //exibe a janela modal
		   $('#mod').click();
		   
		   $("#exibe_loanding").show();
		   
		   $("#mensagem ").hide();
		   $('#botao_file').attr('disabled', 'disabled'); 
		   //$('#tipo_veiculo').attr('readonly','readonly');
		   $("#btn_enviar").hide();
		   
		   $('#envia_arquivo').submit();

	   }
	   
   });
   
   
   
   $('#enviar_arquivo_tarifa').attr('disabled', 'disabled'); 
   $('#fakeupload_tarifa').val('');
   $('#fakeupload_tarifa').attr('readonly','readonly');
   
   $("#enviar_arquivo_tarifa").click(	function() {

	   $('#tipo').val('tarifa');
	   
	   //altera o mouse para modo de espera
	   $('body').mouseover(function(){
		   $(this).css({cursor: 'wait'});
	   });

	   //exibe a janela modal
	   $('#mod').click();

	   $("#exibe_loanding").show();

	   $("#mensagem ").hide();
	   $('#botao_file_tarifa').attr('disabled', 'disabled'); 
	  // $('#tipo_veiculo').attr('readonly','readonly');
	   $("#btn_enviar_tarifa").hide();

	   $('#envia_arquivo').submit();

   });
   
	
	//JANELA MODAL
	//seleciona os elementos a com atributo name="modal"
	$('a[name=modal]').click(function(e) {
		//cancela o comportamento padrão do link
		e.preventDefault();
		
		//armazena o atributo href do link
		var id = $(this).attr('href');
		
		//armazena a largura e a altura da tela
		var maskHeight = $(document).height();
		var maskWidth = $(window).width();
		
		//Define largura e altura do div#mask iguais ás dimensões da tela
		$('#mask').css({'width':maskWidth,'height':maskHeight});
		
		//efeito de transição
		$('#mask').fadeIn(500);
		$('#mask').fadeTo("fast",0.6);
		
		//armazena a largura e a altura da janela
		var winH = $(window).height();
		var winW = $(window).width();
		
		//centraliza na tela a janela popup
		$(id).css('top',  winH/2-$(id).height()/2);
		$(id).css('left', winW/2-$(id).width()/2);
		
		//centraliza a cor de fundo 
		//$('#mask').css('top',  winH/2-$(id).height()/2);
		//$('#mask').css('left', winW/2-$(id).width()/2);
		$('#mask').css('top', '0');
		$('#mask').css('left','0');
		
		//efeito de transição
		$(id).fadeIn(500);
	});
	//FIM JANELA MODAL
   
});


function exibeMsgOk(data)
{
    criaAlerta('Processo de importação iniciado ' + data + ', aguarde o recebimento do e-mail com a mensagem de finalização.', 'sucesso');
}

function exibeMsgErro(msg)
{
	if(msg != ''){
		 criaAlerta(msg,'erro');
	}else{
		 criaAlerta('Erro na importação do arquivo.', 'erro');
	}
   
}

function exibeAlerta(msg){
	if(msg != ''){
		criaAlerta(msg,'alerta');
	}
}

function exibeMsgArquivo()
{
    criaAlerta('O arquivo não possui formato válido. Favor enviar somente arquivos .CSV', 'alerta');
}

function habilitaEnvio() {
    if($("#arq_importacao").val() != "") {
        $('#enviar_arquivo').removeAttr('disabled');
        $('#enviar_arquivo_tarifa').attr('disabled', 'disabled');
    }
}


function habilitaEnvioTarifa() {
    if($("#arq_importacao_tarifa").val() != "") {
    	$('#enviar_arquivo').attr('disabled', 'disabled');
        $('#enviar_arquivo_tarifa').removeAttr('disabled');
    }
}

function criaAlerta(msg, status) {
    $("#mensagem ").text(msg).removeAttr('class').addClass('mensagem alerta').addClass(status).show();
}
