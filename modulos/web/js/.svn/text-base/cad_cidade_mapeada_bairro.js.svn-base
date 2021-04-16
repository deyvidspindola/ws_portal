jQuery(document).ready(function(){
   
   //botão novo
   jQuery("#bt_novo").click(function(){
       window.location.href = "cad_cidade_mapeada_bairro.php?acao=cadastrar";
   });
   
   //botão voltar
   jQuery("#bt_voltar").click(function(){
       window.location.href = "cad_cidade_mapeada_bairro.php";
   })
   
   
     jQuery('#bt_gravar').click(function(){
    	 
    	 
    	 if(jQuery('#cmbestoid').val() == '' && jQuery('#cmbclcoid').val() == ''  && jQuery('#cmbcbaoid').val() == ''){
    		 criaAlerta('Existem campos obrigatórios não preenchidos.','alerta');
	   		 return false;
    	 }
    	 
//    	 if(jQuery('#cmbestoid').val() == '' ){
//    		 jQuery("#cmbestoid").css("background-color","#FFFFC0");
//	   		 criaAlerta('A UF deve ser informada.','alerta');
//	   		 return false;
//    	 }
    	 
    	 if(jQuery('#cmbclcoid').val() == '' || jQuery('#cmbclcoid').val() == 'Escolha' ){
    		 jQuery("#cmbclcoid").css("background-color","#FFFFC0");
	   		 criaAlerta('A Cidade deve ser informada.','alerta');
	   		 return false;	
    	 }

    	 if(jQuery('#cmbcbaoid').val() == '' || jQuery('#cmbcbaoid').val() == 'Escolha'){
    		 jQuery("#cmbcbaoid").css("background-color","#FFFFC0");
	   		 criaAlerta('O Bairro deve ser informado.','alerta');
	   		 return false;	
    	 }
    	 
    	 
    	 jQuery('#form_cadastrar').submit();
    	 
	 });
   
   
   //busca as cidades ao escolher o estado
	jQuery('#cmbestoid').change( function(){
				
		 var idEstado = '';
		 
		 $("#cmbestoid").css("background-color","#FFFFFF");
		 $("#mensagem_alerta").hide();
		
		 try { 
				 
			    idEstado  = jQuery('#cmbestoid').val();
			    
			    jQuery('#cmbcbaoid').html('<option>Escolha</option>');
			    
			    if(jQuery("#acao").val() == 'pesquisar'){
			    	 jQuery("#cmbcbaoid").append(jQuery('<option></option>').attr("value", 't').text('Todos'));
			    }
			    
			    jQuery('#cmbclcoid').html('<option>Escolha</option>');
			    jQuery("select#cmbclcoid").val('').attr('selected', true);
			    
			    
		  		jQuery(function() { 
	
	  			     jQuery.ajax({	
	  					 
	  					  url : 'cad_cidade_mapeada_bairro.php',
	  					 type : 'post',
	  					 data : {   
	  	  					        acao        : 'getCidades'
	  	  	  					  , idEstado    :  idEstado 
	  	  	  					  
	  	  	  	  	  		     },
	  	  	  	  	  		     
	  	  	  	  	  	 beforeSend: function(){
	  	  	  	  	            jQuery("#cmbclcoid").hide();
		  						jQuery('.carregando_cidades').html('<img src="images/ajax-loader-circle.gif" />');
		  				 },
	  					 success: function(data) { 
	
	  						var resultado = jQuery.parseJSON(data);
	  						
	  						 jQuery("#cmbclcoid").show();
	  						
	  						 if(idEstado != 't' && idEstado != ''){
	  					    	
	  							if(jQuery("#acao").val() == 'pesquisar'){
	  							   jQuery("#cmbclcoid").append(jQuery('<option></option>').attr("value", 't').text('Todas'));
	  							}
	  							
	  					    	jQuery.each(resultado, function(i, dados){
		  							jQuery("#cmbclcoid").append(jQuery('<option></option>').attr("value", dados.clcoid).text(dados.clcnome));
		  						});
	  					    	
						    }else{
						    	
						    	if(jQuery("#acao").val() == 'pesquisar'){
						    		 jQuery("#cmbclcoid").append(jQuery('<option></option>').attr("value", 't').text('Todas'));
						    	 }
						    }
	  						 
	  						jQuery('.carregando_cidades').html('');
	  						 
	  				     }
	  			     });
	      	     });
				 
			 
	    }catch(err) {
		      alert(err);
	    }
	});
   
   
    //busca os bairros ao escolher a cidade
	jQuery('#cmbclcoid').change( function(){
				
		 var idCidade = '';
		 
		 $("#cmbclcoid").css("background-color","#FFFFFF");
		 $("#mensagem_alerta").hide();
		 
		
		 try { 
				 
			    idCidade  = jQuery('#cmbclcoid').val();
			    
			    jQuery('#cmbcbaoid').html('<option>Escolha</option>');
			    jQuery("select#cmbcbaoid").val('').attr('selected', true);
			    
		  		jQuery(function() { 
	
	  			     jQuery.ajax({	
	  					 
	  					  url : 'cad_cidade_mapeada_bairro.php',
	  					 type : 'post',
	  					 data : {   
	  	  					        acao        : 'getBairros'
	  	  	  					  , idCidade    :  idCidade 
	  	  	  					  
	  	  	  	  	  		     },
	  	  	  	  	  		     
	  	  	  	  	  	 beforeSend: function(){
	  	  	  	  	            jQuery("#cmbcbaoid").hide();
		  						jQuery('.carregando_bairros').html('<img src="images/ajax-loader-circle.gif" />');
		  				 },
	  					 success: function(data) { 
	  						 
	  						 if(data == 'null' && idCidade != 't' && idCidade != 'Escolha' ){
	  							 criaAlerta('A cidade selecionada não possui bairro cadastrado.');
	  							 $("#cmbcbaoid").css("background-color","#FFFFC0");
	  						 }else{
	  							 $("#cmbcbaoid").css("background-color","#FFFFFF");
	  						 }
	  						 
	  						 var resultado = jQuery.parseJSON(data);
	  						
	  						 jQuery("#cmbcbaoid").show();
	  						
	  						 if(idCidade != 't' && idCidade != ''){
	  					    	
	  							if(jQuery("#acao").val() == 'pesquisar'){
	  								jQuery("#cmbcbaoid").append(jQuery('<option></option>').attr("value", 't').text('Todos'));
	  							}
	  							
	  							if(resultado != null){
	  								jQuery.each(resultado, function(i, dados){
			  							jQuery("#cmbcbaoid").append(jQuery('<option></option>').attr("value", dados.cbaoid).text(dados.cbanome));
			  						});
	  							}
	  							
						    }else{
						    	if(jQuery("#acao").val() == 'pesquisar'){
	  								jQuery("#cmbcbaoid").append(jQuery('<option></option>').attr("value", 't').text('Todos'));
	  							}
						    }
	  						 
	  						jQuery('.carregando_bairros').html('');
	  						 
	  				     }
	  			     });
	      	     });
				 
			 
	    }catch(err) {
		      alert(err);
	    }
	});
   
	
	jQuery("table").on('click','.excluir',function(event) {

	    event.preventDefault();
	        
	    var cmboid  = jQuery(this).attr('data-cmboid');
	        
        jQuery("#mensagem_excluir").dialog({
        title: "Confirmação de Exclusão",
        resizable: false,
        modal: true,
        buttons: {
            "Sim": function() {
             jQuery( this ).dialog( "close" );
             
	  		    jQuery(function() { 
	
				     jQuery.ajax({	
						 
						  url : 'cad_cidade_mapeada_bairro.php',
						 type : 'post',
						 data : {   
		  					        acao  : 'excluir'
		  	  					  , cmboid    :  cmboid 
		  	  					  
		  	  	  	  		     },
						
						 beforeSend: function(){
						
							jQuery(".excluir"+cmboid).hide();
							jQuery('.loading_excluir'+cmboid).html('<img src="images/ajax-loader-circle.gif" />');
						 },
						 success: function(data) { 
							 
							 jQuery('#mensagem_sucesso').html("Registro excluído com sucesso.");
	                         jQuery('#mensagem_sucesso').show();
							 
							jQuery("#tr"+cmboid).remove();
							
							corrigeTabela();
	
	                        aplicarCorLinha();
							
						 }
					});
				     
		    	});

             },
             "Não": function() {
                 jQuery( this ).dialog( "close" );
             }
           }
        });
	});
	
	
	 jQuery("table").on('click','.editar',function(event) {

	        event.preventDefault();

	        id = jQuery(this).data('cmboid');

	        window.location.href = "cad_cidade_mapeada_bairro.php?acao=editar&cmboid="+id;
	    });
	
	
	
	 //tira marcação de alerta ao escolher o bairro
	jQuery('#cmbcbaoid').change( function(){
				
		 $("#cmbcbaoid").css("background-color","#FFFFFF");
		 $("#mensagem_alerta").hide();
	
	});
   
	
	/*
     * Reorganzia as cores das linhas na lista
     */
    function aplicarCorLinha(){

        var cor = '';

        //remove cores
        jQuery('#bloco_itens table tbody tr').removeClass('par');
        jQuery('#bloco_itens table tbody tr').removeClass('impar');

        //aplica cores
        jQuery('#bloco_itens table tbody tr').each(function(){
            cor = (cor == "impar") ? "par" : "impar";
            jQuery(this).addClass(cor);
        });
    }

    /*
     * Corrige informação da quantidade de registros encontrados.
     */
    function corrigeTabela(){

        var qtdLinhas = 0;

        //busca quantidade de linhas
        qtdLinhas = jQuery('#bloco_itens table tbody tr').length;
        jQuery("#registros_encontrados").html("");

        if(qtdLinhas == 0){
            jQuery('.resultado').hide();
        }else if(qtdLinhas == 1){
            jQuery("#registros_encontrados").html("1 registro encontrado.");
        }else{
        jQuery("#registros_encontrados").html(qtdLinhas + " registros encontrados.");
        }
    }
	
	
	function criaAlerta(msg, status) {
	    $("#mensagem_alerta ").text(msg).removeAttr('class').addClass('mensagem alerta').addClass(status).show();
	}

   
   
});