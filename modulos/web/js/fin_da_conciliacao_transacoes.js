$(function() {
        
	    var scntDiv = $('#p_scents');
        var i = $('#p_scents p').size() + 1;
        
        
        $('#addScnt').live('click', function() {
        	if(i != 6){
                $('<p><label for="autocomplete_1">Arquivo*</label><input style="border: 0px solid #C0C0C0;" class="upload" id="upload[]" type="file" name="upload_' + i +'" placeholder="Input Value" size="40" /></label> <a id="remScnt"><img class="icone" src="images/icon_error.png" title="Excluir"></a></p>').appendTo(scntDiv);
                i++;
                return false;
               }
        });
        
        $('#remScnt').live('click', function() { 
        	//alert('aqui');
                if( i > 1 ) {
                        $(this).parents('p').remove();
                        i--;
                }
                return false;
        });

        $('#enviar_arquivo').click(function() {
        	

    		 if($("#data_credito").val() === '' ){
    			 
    			 $("#data_credito").css("background-color","#FFFFC0");
    			 criaAlerta("Informe a data de crédito na C/C.","alerta");
    			 return false;
    		 
    	     }else if(dataValida($("#data_credito").val()) == false) {
    	    	 
    			 $("#data_credito").css("background-color","#FFFFC0");
    			 $("#data_credito").focus();
    			 criaAlerta("A data de crédito informada é inválida.","alerta");
    			 return false;
    		 
    	     }else{
    			
    	    	 if (calculaPeriodo($("#data_credito").val()) === 2){
    	    		 
    				 $("#data_credito").css("background-color","#FFFFC0");
    				 $("#data_credito").focus();
    				 criaAlerta("Data de crédito na C/C maior que a data atual. Favor redigitar.","alerta");
    				 return false;
    				 
    			 }else if(calculaPeriodo($("#data_credito").val()) === 3){
    				 
				    var d = new Date();
				    
                    var dateOffset = (1000*60*60*24) * 15;
                    var newDate = new Date(d.setTime(d.getTime() - dateOffset));

                    var month = newDate.getMonth() + 1;
                    var day   = newDate.getDate();
                    var year  = newDate.getFullYear();

                    var data_atrasada = (day < 10?'0':'')+day+"/"+(month < 10?'0':'')+month+"/"+year;
    				 
    				 $("#data_credito").css("background-color","#FFFFC0");
    				 $("#data_credito").focus();
    				 criaAlerta("Data de crédito na C/C menor que "+ data_atrasada + ". Favor redigitar.","alerta");
    				 return false;
    			 }
    	    	 
    		 }


    		 $("#data_credito").css("background-color",	"#FFFFFF");
        	
	         $("#composicao ").hide();   
	   		 $('.bloco_acoes').hide();
	   		 $('.carregando').show();
   	   }); 
        
});


function criaAlerta(msg, status) {
    $("#composicao ").text(msg).removeAttr('class').addClass('mensagem alerta').addClass(status).show();
}


/**
 * Calcula se a data informada é atual, se é maior e se é menor que 15 dias
 * 
 * @param Date data_credito
 * 
 * @return Bolean, Int
 */
function calculaPeriodo(data_credito) {
    
    var qtdDias   = 0;
    var diferenca = 0;

    var d = new Date();
    var month = d.getMonth() + 1;
    var day = d.getDate();
    var year = d.getFullYear();
    
    var data_atual = (day < 10 ?'0':'')+ day + '/' +( month < 10?'0':'')+ month + '/' + year;
        
    arrDataAtual   = data_atual.toString().split('/');
    arrDataCredito = data_credito.toString().split('/');

    dateAtual   = new Date(arrDataAtual[1]+"/"+arrDataAtual[0]+"/"+arrDataAtual[2]);
    dateCredito = new Date(arrDataCredito[1]+"/"+arrDataCredito[0]+"/"+arrDataCredito[2]);  

    diferenca =  dateAtual - dateCredito  ;
    qtdDias   = Math.round(diferenca/(1000*60*60*24));
    
    //data de crédito não pode se maior que a data atual
    if(qtdDias < 0){
    	 
    	 return 2;	
    
    // data de crédito deve ser menor que a data atual até 15 dias
    }else if(qtdDias > 15 && qtdDias != 0){
    	
    	return 3;
    }

    return true;

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