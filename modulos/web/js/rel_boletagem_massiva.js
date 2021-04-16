jQuery(function(){

     $('#limpar_pesquisa').click(function(){
		 
         $("#nome_campanha").val("");
         $("#data_ini").val(""); 
         $("#data_fim").val("");
         $("#data_vencimento").val("");
         $("#mensagem").hide();
         $("#data_ini").css("background-color",	"#FFFFFF");
     	 $("#data_fim").css("background-color",	"#FFFFFF");
     	 $("#data_vencimento").css("background-color",	"#FFFFFF");
     	 
     	 $('#apenas_pagos').removeAttr('checked');
     	 
     	 $(".resultado_pesquisa").html("");
		 
	 });
	
      
      $("#buscar").click(	function() {
  		
    	  if($('#data_ini').val() == '' && $('#data_fim').val() == ''){
    		  
    		  $("#data_ini").css("background-color","#FFFFC0");
    		  $("#data_fim").css("background-color","#FFFFC0");
    		  criaAlerta('Existem campos obrigatórios não preenchidos.','alerta');
    		  return false;
    		  
    	  }else if($('#data_ini').val() != '' && $('#data_fim').val() == ''){
    		 
    		  $("#data_fim").css("background-color","#FFFFC0");
    		  $("#data_ini").css("background-color","#FFFFFF");
    		  criaAlerta('A data fim do período deve ser informada.','alerta');
    		  return false;
    		  
    	  }else if($('#data_ini').val() == '' && $('#data_fim').val() != ''){
    		  
    		  $("#data_ini").css("background-color","#FFFFC0");
    		  $("#data_fim").css("background-color","#FFFFFF");
    		  criaAlerta('A data inicio do período deve ser informada.','alerta');
    		  return false;
    		  
    	  }else{

 			 if (calculaPeriodo($("#data_ini").val(), $("#data_fim").val()) < 0){
 				 $("#data_fim").css("background-color","#FFFFC0");
 				 criaAlerta("Data final não pode ser menor que data inicial do período.","alerta");
 				 return false;
 			 }
 			 
 			 if(calculaPeriodo($("#data_ini").val(), $("#data_fim").val()) > 182) {
 				 $("#data_ini").css("background-color","#FFFFC0");
 				 $("#data_fim").css("background-color","#FFFFC0");
 				 criaAlerta("Favor selecionar um período com intervalo menor ou igual a 6 meses.","alerta");
 				 return false;
 			 }
    		  
    		  $("#data_ini").css("background-color",	"#FFFFFF");
    		  $("#data_fim").css("background-color",	"#FFFFFF");
    		  $("#mensagem ").hide();
    	  }  
    	  
    	  
    	  $('.bloco_acoes').hide(); 
    	  
	
 		  $('.resultado_pesquisa').html("");
 		  $('.carregando').show();
 		
 		  $('#busca_dados').submit();
    	  
  	});	
      
  
});



/**
 * Calcula diferenï¿½a de dias entre data final e data inicial
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



function exibeMsgOk(data,nm_usuario)
{
    criaAlerta('Processo iniciado por: ' + nm_usuario + ' em: ' + data + '. ', 'sucesso');
    $('#iniciar_processamento').attr('disabled', 'disabled'); 
    
}


function exibeAlerta(msg){
	
	if(msg != ''){
		criaAlerta(msg,'alerta');
	}
}


function criaAlerta(msg, status) {
    $("#mensagem ").text(msg).removeAttr('class').addClass('mensagem alerta').addClass(status).show();
}
