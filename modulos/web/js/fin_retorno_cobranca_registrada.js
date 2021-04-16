jQuery(function(){
	
	
	$("#parar_processamento").click(	function() {
		
		
		if(confirm('Deseja realmente parar o processamento em andamento ?')){

			$("#exibe_loanding").show();
	
			$('#parar_processamento').attr('disabled', 'disabled'); 
	
			//altera o mouse para modo de espera
			$('body').mouseover(function(){
				$(this).css({cursor: 'wait'});
			});
	
			//exibe a janela modal
			$('#mod').click();
	
			$('#tipo').val('pararImportacao') ;
			$('#frm').submit();
			
		}
		
		return false;

	});	


	$("#iniciar_processamento").click(	function() {
	   
	  if($('#obr_busca_forma_cobranca').val() == '' && document.getElementById('arquivo_importar').value == ''){
		  
		  $("#obr_busca_forma_cobranca").css("background-color","#FFFFC0");
		  $("#arquivo_importar").css("background-color","#FFFFC0");
		 
		  criaAlerta('Existem campos obrigatórios não preenchidos.');
		  
		  return false;
		  
	  }else{
		  
		  $("#obr_busca_forma_cobranca").css("background-color",	"#FFFFFF");
		  $("#arquivo_importar").css("background-color",	"#FFFFFF");
		  $("#mensagem ").hide();
	  }
	   
	   
	  if($('#obr_busca_forma_cobranca').val() == ''){
		  
		  $("#obr_busca_forma_cobranca").css("background-color","#FFFFC0");
		  criaAlerta('Informe a forma de cobrança.');
		  
		  return false;
	  
	  }else{
		  $("#obr_busca_forma_cobranca").css("background-color",	"#FFFFFF");
		  $("#mensagem ").hide();
	  }
	  
	  
	  if(document.getElementById('arquivo_importar').value == ''){
	  
		  criaAlerta('Informe o arquivo para leitura.');
		  $("#arquivo_importar").css("background-color","#FFFFC0");
		  
		  return false;
		  
	  }else{
	  
		  if(confirm('Deseja realmente processar arquivo de retorno?')){

			  
			   $('#tipo').val('dadosRetornoCobranca');
			   
			   //altera o mouse para modo de espera
			   $('body').mouseover(function(){
				   $(this).css({cursor: 'wait'});
			   });

			   //exibe a janela modal
		       $('#mod').click();
		   
		   	   $("#arquivo_importar").css("background-color",	"#FFFFFF");			   
		   	   $("#mensagem ").hide();
			   
			   
			   $("#exibe_loanding").show();
			  			   
			   $('#iniciar_processamento').attr('disabled', 'disabled'); 
			  
		       $('#frm_acao').val('upload') ;
		       $('#frm').submit();
			  
		  }
		  
		  return false;

	  }
	   
	   return false;
	   
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


function exibeMsgOk(data,nm_usuario)
{
    criaAlerta('Processo iniciado por: ' + nm_usuario + ' em: ' + data + '. ', 'sucesso');
    $('#iniciar_processamento').attr('disabled', 'disabled'); 
    
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
    criaAlerta('O arquivo não possui formato válido. Favor enviar somente arquivos .ret', 'alerta');
}

function exibeMsProcessamento(){
	criaAlerta('Processamento concluído, verifique seu e-mail com o relatório do processo.', 'alerta');
}

//function habilitaEnvio() {
//    if($("#arq_importacao").val() != "") {
//        $('#enviar_arquivo').removeAttr('disabled');
//        $('#enviar_arquivo_tarifa').attr('disabled', 'disabled');
//    }
//}

//
//function habilitaEnvioTarifa() {
//    if($("#arq_importacao_tarifa").val() != "") {
//    	$('#enviar_arquivo').attr('disabled', 'disabled');
//        $('#enviar_arquivo_tarifa').removeAttr('disabled');
//    }
//}

function criaAlerta(msg, status) {
    $("#mensagem ").text(msg).removeAttr('class').addClass('mensagem alerta').addClass(status).show();
}
