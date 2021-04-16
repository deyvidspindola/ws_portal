jQuery(function(){
	
	jQuery('.numerico').keyup( function(e){
		var campo = '#'+this.id;
		var valor = jQuery(campo).val().replace(/[^0-9,.]+/g,'');

		jQuery(campo).val(valor);
	});
	
	//formata os campos de valores para moeda
	jQuery('.valor').maskMoney({thousands:'.', decimal:','});
	//jQuery('.valorZerado').maskMoney({thousands:'.', decimal:',',allowZero: true});
	
	//só exibe datas futuras na label Vencimento no cadastro de campanha
	$("#cad_vencimento").datepicker("option", "minDate", 0);
	
	 //exibe o combo dos estados brasileiros para escolha multipla
	  $(function() {
	        $('#uf_cliente').change(function() {
	            console.log($(this).val());
	        }).multipleSelect({
	            width: '100%'
	        });
	    });
	  
	  
	 // $("#uf_cliente option").prop("selected",true);
	  
	  
	//exibe o combo cód do cliente multipla
	  $(function() {
	        $('#cod_cliente').change(function() {
	            console.log($(this).val());
	        }).multipleSelect({
	            width: '100%'
	        });
	    });
	
	
     $('#limpar_pesquisa').click(function(){
		 
         $("#nome_campanha").val("");
         $("#data_ini").val(""); 
         $("#data_fim").val("");
         $("#data_vencimento").val("");
         $("#mensagem").hide();
         $("#data_ini").css("background-color",	"#FFFFFF");
     	 $("#data_fim").css("background-color",	"#FFFFFF");
     	 $("#data_vencimento").css("background-color",	"#FFFFFF");
     	 
     	 $(".resultado_pesquisa").html("");
		 
	 });
	
      
      $('#limpar_cadastro').click(function(){
 		 
          $("#cad_nome_campanha").val("");
          $("#aging_divida").val(""); 
          $("#cad_vencimento").val("");
          $("select#formato_envio").val('').attr('selected', true);
          $("#valor_divida_ini").val("");
          $("#valor_divida_fim").val("");
          $("select#tipo_pessoa").val('').attr('selected', true);
          $("select#tipo_cliente").val('').attr('selected', true);
          $("select#uf_cliente").val('').attr('selected', true);
          $("select#cod_cliente").val('').attr('selected', true);
          
          $("#cad_nome_campanha").css("background-color",	"#FFFFFF");
          $("#cad_vencimento").css("background-color",	"#FFFFFF");
          $("#aging_divida").css("background-color",	"#FFFFFF");
          $("#formato_envio").css("background-color",	"#FFFFFF");
          
          //limpa os chekboxes dos combos de multipla escolha
          $('input:checkbox').removeAttr('checked');
          
          $("#mensagem").hide();
      	 
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
    	  
    	  
		  //$('#buscar').attr('disabled', 'disabled'); 
    	  //$('#buscar').hide();
 		  $('.resultado_pesquisa').html("");
 		  $('.carregando').show();
 		
 		  $('#busca_dados').submit();
    	  
  	});	
      
      
      $('#novo').click(function(){
 		  $('#acao').val('novo');
    	  $('#busca_dados').submit();
 	 });  
      
      
     $('#voltar').click(function(){
 		  $('#acao').val('index');
    	  $('#frm_cadastro').submit();
 	 });  
      
     
     
     $("#gerar_campanha").click(function() {
    	 
    	 if($('#cad_nome_campanha').val() == ''){
    		 $("#cad_nome_campanha").css("background-color","#FFFFC0");
    		 $('#cad_nome_campanha').focus();
   		     criaAlerta('O Nome da Campanha deve ser informado.','alerta');
   		     return false;
    	 }
    	 $("#cad_nome_campanha").css("background-color",	"#FFFFFF");
    	 
    	 
    	 if($('#aging_divida').val() == ''){
    		 $("#aging_divida").css("background-color","#FFFFC0");
    		 $('#aging_divida').focus();
   		     criaAlerta('O Aging da Dívida deve ser informado.','alerta');
   		     return false;
    	 }
    	 $("#aging_divida").css("background-color",	"#FFFFFF");
    	 
    	 
    	 if($('#cad_vencimento').val() == ''){
    		 $("#cad_vencimento").css("background-color","#FFFFC0");
    		 $('#cad_vencimento').focus();
   		     criaAlerta('A data de Vencimento deve ser informada.','alerta');
   		     return false;
    	 }
    	 $("#cad_vencimento").css("background-color",	"#FFFFFF");
    	 
    	 
    	 if($('#formato_envio').val() == ''){
    		 $("#formato_envio").css("background-color","#FFFFC0");
    		 $('#formato_envio').focus();
   		     criaAlerta('O Formato de Envio deve ser informado.','alerta');
   		     return false;
    	 }
    	 $("#formato_envio").css("background-color",	"#FFFFFF");
    	 
    	 //verifica se o valor inicial é maior que o final
    	 var valor_ini = $("#valor_divida_ini").val().replace('.', '');
    	 valor_ini     = valor_ini.replace(',','');
    	 
    	 var valor_fim = $("#valor_divida_fim").val().replace('.', '');
         valor_fim     = valor_fim.replace(',','');
    	 
    	 if(parseInt(valor_ini)  > parseInt(valor_fim) ){
    		 
    		 $("#valor_divida_fim").css("background-color","#FFFFC0");
    		 $('#valor_divida_fim').focus();
   		     criaAlerta('O valor final não pode ser menor que o valor inicial.','alerta');
   		     return false;
    		 
    	 }
    	 $("#valor_divida_fim").css("background-color",	"#FFFFFF");
    	 
    	 
    	 
    	 $("#mensagem ").hide();
    	 
    	 
	    //altera o mouse para modo de espera
		$('body').mouseover(function(){
			$(this).css({cursor: 'wait'});
		});
		
	    //exibe a janela modal
		$('#mod').click();

		$('#acao').val('gerarCampanha') ;
		$('#frm_cadastro').submit();
    	 
     
 	});
     
     
     
	 $("body").delegate('#ftp','click', function(){
		 
		 
		 if(confirm('Deseja realmente enviar o arquivo para a GRÁFICA ?')){
		 
			var id_campanha  = jQuery(this).attr('rel')
					 
		  		jQuery(function() { 
	
	  			     jQuery.ajax({	
	  					 
	  					  url : 'fin_boletagem_massiva.php',
	  					 type : 'post',
	  					 data : {   
	  	  					        acao        : 'enviarArquivoFTP'
	  	  	  					  , id_campanha :  id_campanha 
	  	  	  					  
	  	  	  	  	  		     },
	  					
	  					 beforeSend: function(){
							
	  						jQuery(".ftp").hide();
	  						jQuery('.loading_ftp'+id_campanha).html('<img src="images/ajax-loader-circle.gif" />');
	  					 },
	  					 success: function(data) { 
	
	  						 alert(data);
	  						 
	  						$('#busca_dados').submit();
	  				 }
	  			});
  			     
  	      	});
			
		 }//fim confirm
		 
	});
	
     
     
    $("body").delegate('#envio_email','click', function(){
		 
		 if(confirm('Deseja realmente enviar os boletos por E-MAIL ?')){
		 
			var id_campanha  = jQuery(this).attr('rel');
			
			 //altera o mouse para modo de espera
			$('body').mouseover(function(){
				$(this).css({cursor: 'wait'});
			});
			
		    //exibe a janela modal
			$('#mod').click();
	
			$('#id_campanha').val(id_campanha) ;
			$('#frm_campanha').submit();
			
		 }//fim confirm
		 
	});
	 
	
	//JANELA MODAL
	//seleciona os elementos a com atributo name="modal"
	$('a[name=modal]').click(function(e) {
		//cancela o comportamento padrï¿½o do link
		e.preventDefault();
		
		//armazena o atributo href do link
		var id = $(this).attr('href');
		
		//armazena a largura e a altura da tela
		var maskHeight = $(document).height();
		var maskWidth = $(window).width();
		
		//Define largura e altura do div#mask iguais ï¿½s dimensï¿½es da tela
		$('#mask').css({'width':maskWidth,'height':maskHeight});
		
		//efeito de transiï¿½ï¿½o
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
		
		//efeito de transiï¿½ï¿½o
		$(id).fadeIn(500);
	});
	//FIM JANELA MODAL
   
});



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
