jQuery(function(){

      $("#buscar").click(	function() {
  		
    	  if($('#data_ini').val() == '' && $('#data_fim').val() == ''){
    		  
    		  $("#data_ini").css("background-color","#FFFFC0");
    		  $("#data_fim").css("background-color","#FFFFC0");
    		  criaAlerta('Existem campos obrigatórios não preenchidos.');
    		  return false;
    		  
    	  }else if($('#data_ini').val() != '' && $('#data_fim').val() == ''){
    		 
    		  $("#data_fim").css("background-color","#FFFFC0");
    		  $("#data_ini").css("background-color","#FFFFFF");
    		  criaAlerta('A data fim do período deve ser informada.');
    		  return false;
    		  
    	  }else if($('#data_ini').val() == '' && $('#data_fim').val() != ''){
    		  
    		  $("#data_ini").css("background-color","#FFFFC0");
    		  $("#data_fim").css("background-color","#FFFFFF");
    		  criaAlerta('A data inicio do período deve ser informada.');
    		  return false;
    		  
    	  }else{

 			 if (calculaPeriodo($("#data_ini").val(), $("#data_fim").val()) < 0){
 				 $("#data_fim").css("background-color","#FFFFC0");
 				 criaAlerta("Data final não pode ser menor que data inicial do período.");
 				 return false;
 			 }
 			 
 			 if(calculaPeriodo($("#data_ini").val(), $("#data_fim").val()) > 90) {
 				 $("#data_ini").css("background-color","#FFFFC0");
 				 $("#data_fim").css("background-color","#FFFFC0");
 				 criaAlerta("Favor selecionar um período com intervalo menor ou igual a 90 dias.");
 				 return false;
 			 }
    		  
    		  $("#data_ini").css("background-color",	"#FFFFFF");
    		  $("#data_fim").css("background-color",	"#FFFFFF");
    		  $("#mensagem ").hide();
    	  }  
    	  
    	  
    	  $('.bloco_acoes').hide();
 		
 		  $('#busca_dados').submit();
    	  
  	});

	$("#voltar").click(	function() {
		window.location.assign("principal.php?menu=Relatorios");
	});
      
  
});

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


function criaAlerta(msg) {
    $("#alerta").removeClass('alerta_escondido');
	$("#texto_alerta").html(msg  + "<br>");
}
