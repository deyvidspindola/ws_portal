jQuery(document).ready(function(){
	jQuery("#telefone1_titular").mask("(99) 9999-9999");
	jQuery("#telefone2_titular").mask("(99) 9999-9999");
	jQuery('#gera_csv').show();
	jQuery('#download_csv').hide();
        
	   jQuery("#gera_csv").click(function(){
		   jQuery.fn.geracsv();
	    });
           
	jQuery("#bt_limpar_pesquisa").click(function(){
		
		jQuery("#nomeCliente").val('');
		jQuery("#novoTitular").val('');
		jQuery("#dt_ini").val('');
		jQuery("#dt_fim").val('');
		jQuery("#statusSolicitacaoTransDivida").val('');
		jQuery("#statusSolicitacaoSerasa").val('');
		jQuery("#numeroContrato").val('');
		jQuery("#numeroSolicitacao").val('');
		jQuery("#dt_ini_conclusao").val('');
		jQuery("#dt_fim_conclusao").val('');
		jQuery("#usuarios_conclusao").val('');
	});
        
            
jQuery("body").delegate('input[type=radio][name=refidelizar]','change',function(){

    $("#prazoIsencao").hide();
    let value = $(this).val();
    console.log(value);
    if ( value == 'sim') {
            $("#prazoIsencao").show();
    }
});
		
    //botão novo
    jQuery("#bt_novo").click(function(){
        window.location.href = "fin_transferencia_titularidade.php?acao=cadastrar";
    });
	
    //botão voltar
    jQuery("#voltar").click(function(){
        window.location.href = "fin_transferencia_titularidade.php";
    });

    

	jQuery("body").delegate('#btvoltar','click',function(){
				 window.location.href = "fin_transferencia_titularidade.php";
				
			});
	$('#cnpjcpf').attr("disabled", true);

	$( "#cliente" ).keyup(function() {
		var min_length = 2; // min caracters to display the autocomplete
        
    	if(jQuery('input[name=cliente]').val().length < min_length){
    		$('#cliente_id').hide();
    	}
	});
	
	jQuery("body").delegate('#telefone1_titular','focus',function(){
		var telefone1_titular = $("#telefone1_titular").val().replace(/[^\d]+/g,'');
		var telefone2_titular = $("#telefone2_titular").val().replace(/[^\d]+/g,'');
		
		if(isNaN(parseInt(telefone1_titular))){
			jQuery("#telefone1_titular").val('');
		}
		
	});
	
	
   jQuery("body").delegate('#prptipo_pessoa_fis','click',function(){
	   $('#cnpjcpf').attr("disabled", false);
	   jQuery("#cnpjcpf").val('');
	});
   
   jQuery("body").delegate('#prptipo_pessoa_jur','click',function(){
	   $('#cnpjcpf').attr("disabled", false);
	   jQuery("#cnpjcpf").val('');
	});
	

	jQuery("#dt_ini").periodo("#dt_fim");


$( "#cliente" ).autocomplete({
            //minLength:2, 
            //delay: 50,  
	 		autoFocus: true,
            selectFirst: true,
            open: function () {
                $(this).data("autocomplete").menu.element.width(409);
            },
            source:function(request,response){
            	var min_length = 2; // min caracters to display the autocomplete
            
            	if(jQuery('input[name=cliente]').val().length >= min_length){
            		
            	  jQuery.ajax({
	      		        url: 'fin_transferencia_titularidade.php',
	      		        type: 'POST',
	      		        data: {
		                     acao: 'pesquisaClientes',
		                     cliente: jQuery('input[name=cliente]').val()
	      		        },
	      		        success : function(data) {
	      		        	if(jQuery('input[name=cliente]').val().length >= min_length && $('input[name=cliente]').is(':enabled') == true){	
	      		        		//jQuery('#limparCliente').show();
	      		        		$('#cliente_id').show();
	      		        		$('#cliente_id').html(data);
	      		        	}
	      					
	                 	}
      		    });
            	}else{
            		$('#cliente_id').hide();
            	}
           },
          select: function( event, ui ) {
            console.log('value:'+ ui.item.value + ',label:'+ui.item.label);
            $('#cliente_id').hide();
        }
    });
    
    
        $("body").delegate('#clientenovo', 'focus', function(e) {
	    $( this ).autocomplete({
	        //minLength:2,
	        //delay: 50,
	        autoFocus: true,
	        selectFirst: true,
	        open: function () {
	            $(this).data("autocomplete").menu.element.width(409);
	        },
	        source:function(request,response){
	            var min_length = 2; // min caracters to display the autocomplete
	
	            if(jQuery('input[name=clientenovo]').val().length >= min_length){
	
	                jQuery.ajax({
	                    url: 'fin_transferencia_titularidade.php',
	                    type: 'POST',
	                    data: {
	                        acao: 'pesquisaClientesNovo',
	                        cliente: jQuery('input[name=clientenovo]').val()
	                    },
	                    success : function(data) {
	                        if(jQuery('input[name=clientenovo]').val().length >= min_length && $('input[name=clientenovo]').is(':enabled') == true){
	                            //jQuery('#limparCliente').show();
	                            $('#clientenovo_id').show();
	                            $('#clientenovo_id').html(data);
	                        }
	
	                    }
	                });
	            }else{
	                $('#clientenovo_id').hide();
	            }
	        },
	        select: function( event, ui ) {
	            console.log('value:'+ ui.item.value + ',label:'+ui.item.label);
	            $('#clientenovo_id').hide();
	        }
	    });
    });

	jQuery("body").delegate('#confirmar_solicitacao','click',function(){
		
        	jQuery.fn.confirmarSolicitacao();			
	});

	jQuery("body").delegate('#bt_pesquisar','click',function(){
		
		jQuery('#msgalerta1').html('').hide();
		jQuery('#msgalerta2').html('').hide();
		
		$("#frm_pesquisar #cpfcnpj").attr('disabled', 'disabled');
		
		let cpfCnpj = $("#frm_pesquisar #cpfcnpj").val();
		
		if( cpfCnpj == ''){
			$("#frm_pesquisar #cliente").val('');
		}
		
		if(jQuery('#id_cliente').val() == '' && jQuery('#cpf_cnpj').val() == '' && jQuery('#contrato').val() == '' && jQuery('#placa').val() == '' ) {
			$("#msgalerta1").html("Existem campos obrigatórios não preenchidos.").showMessage();
		}
		
		jQuery.fn.pesquisa();
	});


	
	jQuery("body").delegate('#limparCliente','click',function(){
		
		jQuery('input').attr('disabled', 'disabled');

        window.location.href = "fin_transferencia_titularidade.php";
	});
	
	
    jQuery("body").delegate('#confirmar_transferencia','click',function(){
    	
        jQuery.fn.limpaMensagens();

        var status = $(this).children(".empresa").text();

        var items = [];
        let qtdStatusInvalido = 0;

        $("input[name='chk_oid[]']:checked").each(

            function(){
                let status = $(this).data("idstatus");

                if(status != '13') {
                    qtdStatusInvalido++;
                }

                items.push($(this).val());
            }
        );

        if(qtdStatusInvalido > 0) {
            jQuery('#msgalerta2').html('Apenas contratos com status Transferência de Titularidade podem ser transferido.').showMessage();
            return false;
        }

        jQuery.fn.confirmaTransferencia();
        
    });

    jQuery("body").delegate('#download_Planilha','click',function(){
        jQuery.fn.downloadPlanilha();
    });

	jQuery('#contato1')  
    .mask("(99) 9999-9999?9")  
    .live('keypress', function (event) {  
        var target, phone, element;  
        target = (event.currentTarget) ? event.currentTarget : event.srcElement;  
        phone = target.value.replace(/\D/g, '');  
        element = $(target);  
        element.unmask();  
        if(phone.length > 10) {  
            element.mask("(99) 99999-999?9");  
        } else {  
            element.mask("(99) 9999-9999?9");  
        }  
    });
	
	jQuery('#contato2')  
    .mask("(99) 9999-9999?9")  
    .live('keypress', function (event) {  
        var target, phone, element;  
        target = (event.currentTarget) ? event.currentTarget : event.srcElement;  
        phone = target.value.replace(/\D/g, '');  
        element = $(target);  
        element.unmask();  
        if(phone.length > 10) {  
            element.mask("(99) 99999-999?9");  
        } else {  
            element.mask("(99) 9999-9999?9");  
        }  
    }); 
	
	jQuery('#telefone1_titular')  
    .mask("(99) 9999-9999?9")  
    .live('keypress', function (event) {  
        var target, phone, element;  
        target = (event.currentTarget) ? event.currentTarget : event.srcElement;  
        phone = target.value.replace(/\D/g, '');  
        element = $(target);  
        element.unmask();  
        if(phone.length > 10) {  
            element.mask("(99) 99999-999?9");  
        } else {  
            element.mask("(99) 9999-9999?9");  
        }  
    });
	
	jQuery('#telefone2_titular')  
    .mask("(99) 9999-9999?9")  
    .live('keypress', function (event) {  
        var target, phone, element;  
        target = (event.currentTarget) ? event.currentTarget : event.srcElement;  
        phone = target.value.replace(/\D/g, '');  
        element = $(target);  
        element.unmask();  
        if(phone.length > 10) {  
            element.mask("(99) 99999-999?9");  
        } else {  
            element.mask("(99) 9999-9999?9");  
        }  
    });
	
	
	
	 $("#contato1").bind("keyup blur focus", function(e) {
		            e.preventDefault();
			            var expre = /[^\d]/g;
		            $(this).val($(this).val().replace(expre,''));
	});
	 
	 $("#contato2").bind("keyup blur focus", function(e) {
         e.preventDefault();
	            var expre = /[^\d]/g;
         $(this).val($(this).val().replace(expre,''));
	 });

	jQuery("body").delegate('#cnpjcpf','focus',function(){


		if(jQuery("input[name='prptipo_pessoa']:checked").val() == 'F') {
			jQuery("#cnpjcpf").mask("999.999.999-99");
		}else if(jQuery("input[name='prptipo_pessoa']:checked").val() == 'J') {
			jQuery("#cnpjcpf").mask("99.999.999/9999-99");	
		}else{
			$('#cnpjcpf').attr("disabled", true);
		}
	});
	
	jQuery("body").delegate('#cnpjcpf','focus',function(){
			jQuery("#prccpf_aut").mask("999.999.999-99");
	});

    jQuery("#chk_all_titulos_pendentes").toggleChecked("input[name='chk_contrato[]']");

	
	jQuery("#chk_all").toggleChecked("input[name='chk_oid[]']");
	    if(jQuery("input[name='chk_oid[]']:checked").length == 0){
	        return false;
	  }
	    




});

jQuery.fn.pesquisa = function() {

	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data: jQuery('#frm_pesquisar').serialize(),
	    contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
		beforeSend: function(){		
			jQuery('#frame01').html('<center><img src="images/loading.gif" alt="" /></center>');
			jQuery('#bt_pesquisar').attr('disabled', 'disabled');
			
		},
		success: function(data){
			//jQuery('#cliente').attr('disabled', 'disabled');
			// Liberação do botão de pesquisa
			jQuery('#bt_pesquisar').removeAttr('disabled');
			jQuery('#frame03').html('');
			jQuery('#frame03').hide();
			try{	
				// Transforma a string em objeto JSON
				var resultado = jQuery.parseJSON(data);
		    	jQuery('#msgerro').attr("class", "mensagem erro").html(resultado.message).showMessage();
				if(resultado.status=='errorlogin') window.location.href = resultado.redirect;
				jQuery('#frame01').html('');
			}catch(e){
				try{	
					// Transforma a string em objeto JSON
					jQuery('#frame01').html(data).hide().showMessage();						 				
				}catch(e){			
			    	jQuery('#msgerro').attr("class", "mensagem erro").html('Erro no processamento dos dados.').showMessage();
				}
		    }
		}
	});
}

jQuery.fn.confirmaTransferencia = function() {
	
		    
	 		data = {};
	        data.acao     = 'novo';
	        data.cliente  = jQuery('#frm_pesquisar #cliente').val();
	        data.id_cliente	= jQuery('#frm_pesquisar #id_cliente').val();
	        data.cpfcnpj	= jQuery('#frm_pesquisar #cpfcnpj').val();
	        data.contrato	= jQuery('#frm_pesquisar #contrato').val();	
	        data.placa	= jQuery('#frm_pesquisar #placa').val();	
	        data.classecontrato		= jQuery('#frm_pesquisar #classecontrato').val();
	        data.numeroresultados	= jQuery('#frm_pesquisar #numeroresultados').val();
	        data.ordenaresultados	= jQuery('#frm_pesquisar #ordenaresultados').val();
	        data.classificaresultados	= jQuery('#frm_pesquisar #classificaresultados').val();
	        data.statusId			 = jQuery('#frm_pesquisar #statusId').val();	       
	        data.telefone1_titular	 = jQuery('#frm_pesquisar #telefone1_titular').val();
	        data.telefone2_titular	 = jQuery('#frm_pesquisar #telefone2_titular').val();
	        data.email_titular		 = jQuery('#frm_pesquisar #email_titular').val();
	        data.responsavel_titular = jQuery('#frm_pesquisar #responsavel_titular').val();
	        
	        
/*		    if(data.cliente == ''){
		        $('#telefone1_titular').removeClass("erro");
		        $("#msgalerta2").hide();
		        let message = "Dados (nome) do Atual Titular não informado.";
		        $("#msgalerta2").html(message).showMessage();
		        $('#telefone1_titular').addClass('erro');
		        alert(message);
		        scroolToID('msgalerta2', 100);		      
		        return;
		    }
	        
		    if(data.cpfcnpj == ''){
		    	let message = "Dados do Atual (CPF/CNPJ) Titular não informado.";
		        $('#telefone1_titular').removeClass("erro");
		        $("#msgalerta2").hide();
		        $("#msgalerta2").html(message).showMessage();
		        $('#telefone1_titular').addClass('erro');
		        alert(message);
		        scroolToID('msgalerta2', 100);
		        
		        return;
		    }
		    
		    
		    if(isNaN(parseInt(data.telefone1_titular))){
		    	let message = "Cadastro do Atual titular incompleto (telefone comercial é obrigatório).";
		        $('#telefone1_titular').removeClass("erro");
		        $("#msgalerta2").hide();
		        $("#msgalerta2").html(message).showMessage();
		        $('#telefone1_titular').addClass('erro');
		        alert(message);
		        scroolToID('msgalerta2', 100);
		        return;
		    }	*/       
	        
	        let chk_oid = new Array();
	        $("input[name='chk_oid[]']:checked").each(function() {
	        	chk_oid.push($(this).val());
	        });
	        
	        data.chk_oid			 = chk_oid;
	        
    jQuery.ajax({
        url: 'fin_transferencia_titularidade.php',
        type: 'post',
        data: data,
        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
        beforeSend: function(){
            jQuery('#frame04').html('<center><img src="images/loading.gif" alt="" /></center>');

        },
        success: function(data){
            try{
                // Transforma a string em objeto JSON
                var resultado = jQuery.parseJSON(data);

                //document.write("<br>");
                //document.write(items);

                if(resultado.tipo_erro == 2){
                    jQuery('#msgalerta2').attr("class", "mensagem alerta").html(resultado.msg).showMessage();

                }else if(resultado.tipo_erro == 3){
                    jQuery('#msgalerta2').attr("class", "mensagem erro").html(resultado.msg).showMessage();

                }else{
                    jQuery('#msgerro').attr("class", "mensagem erro").html(resultado.message).showMessage();
                }

                if(resultado.status=='errorlogin') window.location.href = resultado.redirect;
                jQuery('#frame04').html('');
            }catch(e){
                try{
                    // Transforma a string em objeto JSON
                    jQuery('#frame04').html(data).hide().showMessage();
                }catch(e){
                    jQuery('#msgerro').attr("class", "mensagem erro").html('Erro no processamento dos dados.').showMessage();
                }
            }
        }
    });
}

jQuery.fn.geracsv = function(){
	
	jQuery('#acao').val('gerarCSV');
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data: jQuery('#frm_pesquisar').val(),
		
		beforeSend: function(){
			jQuery('#arquivo_csv').html('<center><img src="images/loading.gif" alt="" /></center>');	
		},
		   contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
		success: function(data){
			jQuery('#acao').val('gerarCSV');
			var resultado = jQuery.parseJSON(data);
			jQuery('#arquivo_csv').html('');
			if(resultado.codigo == 0) {
				jQuery('#download_csv').show();
				jQuery('#gera_csv').hide();
				$("#download_csv_link").attr("href", "download.php?arquivo="+resultado.arquivo);
			}else{
				jQuery('#gera_csv').hide();
				jQuery('#arquivo_csv').attr("class", "mensagem erro").html(resultado.msg).showMessage();
			}
		}
		
	});
}


function download(obj) {
    $.ajax({
        url: 'geraExcelRelatorioFinanceiro',
        type: 'POST',
        dataType: 'json',
        data: {
            values: JSON.stringify(obj)
        },
    })
        .success(function(data) {
            console.log(data)
            if(data.filename.length > 0) {
                window.location = 'downloadArquivo?filename=' + data.filename;
            } else {
                mostraAviso('Sem registros para gerar arquivo.', 'alerta')
            }
        })
        .done(function() {
            console.log("success");
        })
        .fail(function() {
            mostraAviso('Falha ao processar requisição.','erro');
            console.log("error");
        })
        .always(function() {
            console.log("complete");
            setTimeout(function() {
                $("#loading").fadeOut("fast");
            }, 1000);
        });
}
jQuery.fn.downloadPlanilha = function(){
    jQuery('#acao').val('gerarCSV');
    jQuery.ajax({
        url: 'fin_transferencia_titularidade.php',
        type: 'post',
        data: jQuery('#frm_pesquisar').serialize(),
   
        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
        beforeSend: function(){
            jQuery('#process').html('<center><img src="images/loading.gif" alt="" /></center>');

        },
        success: function(data){
     

            try{
           

                // Transforma a string em objeto JSON
                var resultado = jQuery.parseJSON(data);
                //jQuery('#msgerro').attr("class", "mensagem erro").html(resultado.message).showMessage();
                if(resultado.status=='msgsucesso'){
                    alert(resultado.message);
                    window.location.href = "fin_transferencia_titularidade.php";
                }else{
                    $('#download_Planilha').attr("disabled", false);
                    jQuery('#msgerro').attr("class", "mensagem erro").html(resultado.message).showMessage();
                }
                if(resultado.status=='errorlogin') window.location.href = resultado.redirect;
                jQuery('#process').html('');


            }catch(e){
                try{
                    // Transforma a string em objeto JSON
                    jQuery('#process').html(data).hide().showMessage();
                }catch(e){
                    $('#download_Planilha').attr("disabled", false);
                    jQuery('#msgerro').attr("class", "mensagem erro").html('Erro no processamento dos dados.').showMessage();
                }

            }
        }

    });
}


jQuery.fn.confirmarSolicitacao = function(){
     
        contratosArray = {};
        
       $("#montalog").hide();
         
        $('#confirmar_solicitacao').attr("disabled",true);
         
        $("#dadosTransferencia  tbody tr").each( function(key){

           contratoHandle = {};
           contratoHandle.contrato           = $(this).find("input[name='contrato']").val();
           contratoHandle.valorAcessorios    = $(this).find("input[name='valorAcessorios']").val(); 
           contratoHandle.valorMonitoramento = $(this).find("input[name='valorMonitoramento']").val();  
           contratoHandle.valorLocacao		 = $(this).find("input[name='valorLocacao']").val();  
           contratoHandle.dataInicioVigencia = $(this).find("input[name='dataInicioVigencia']").val();
	   
           contratosArray[key] = contratoHandle ;
        }); 
               
	jQuery('#acao').val('transferirEmMassaJson');
	jQuery.ajax({
                url: 'fin_transferencia_titularidade.php/?acao=transferirEmMassaJson',
                dataType: 'json',
                type: 'post',
                contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
                data: JSON.stringify( 
                       {  
                          atualTitular:{nome:$("#atual_titular").val() , cpfCnpj:$("#numero_cpf_cnpj").val()}, 
                          novoTitular:{id: $('#novocpfcnpj').data('clienteID') , nome:$("#clientenovo").val() , cpfCnpj:$("#novocpfcnpj").val(), contratos:$("#tipo_contrato").val(),classeContrat:$("#classe_contrato").val(), refidelizar:$('input[name="refidelizar"]:checked').val(),prazoIsencao:$("#prazoIsencao").val()},
                          contratos:contratosArray
                        } 
                ),
                beforeSend: function(){	
			jQuery('#process').html('<center><img src="images/loading.gif" alt="" /></center>');
			
		},
		success: function(resultado){
               
                     
			
			try{	
                                if(resultado.status=='errorlogin') window.location.href = resultado.redirect;
                                
                                let messageView = resultado['message_view'] + resultado['contratos_errors_view']  + resultado['contratos_transferidos_view'] ;
		
	
                                
                                 $("#montalog").html(messageView);
                                 $("#montalog").show();
                                 
                             
				
                                alert(resultado.message);
                                
                                if(resultado.success === 0){
                                	$('#confirmar_solicitacao').removeAttr("disabled");
                                }
				
				jQuery('#process').html('');
		    	
				
			}catch(e){
				try{	
					// Transforma a string em objeto JSON
					jQuery('#process').html(data).hide().showMessage();						 				
				}catch(e){	
					$('#confirmar_solicitacao').attr("disabled", false);
			    	jQuery('#msgerro').attr("class", "mensagem erro").html('Erro no processamento dos dados.').showMessage();
				}

		}
		}
		
	});
}

function set_itemnovo(label, id, cpfcnpj) {
    // change input value
    $('#clientenovo').val(label);
    $('#id_clientenovo').val(id);
    $('#novocpfcnpj').val(cpfcnpj);
    $('#novocpfcnpj').data('clienteID', id);
    // hide proposition list
    $('#clientenovo_id').hide();
    $('#clientenovo').attr("disabled", true);
   
    $('#novocpfcnpj').attr("disabled",true);
    
}

jQuery.fn.validateEmail = function (email)
{
	var atpos=email.indexOf("@");
	var dotpos=email.lastIndexOf(".");
	if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length)
	{
	return false;
	}
	return true;
};

function set_item(label, id, cpfcnpj) {
    // change input value
    $('#cliente').val(label);
    $("#cpfcnpj").val(cpfcnpj);
    $('#id_cliente').val(id);
    // hide proposition list
    $('#cliente_id').hide();
    $('#cliente').attr("disabled", true);
    $("#cpfcnpj").attr("disabled", true);
}

jQuery.fn.limpaMensagens = function() {
	// Liberação do botão de pesquisa
	jQuery('#cliente').removeAttr('disabled');
	jQuery('.componente_nenhum_cliente').hideMessage();
	jQuery(".erro").removeClass("erro");
    jQuery('.mensagem').hideMessage();
}


jQuery.fn.validaCPF =  function(s) {
	var c = s.substr(0,9);
	var dv = s.substr(9,2);
	var d1 = 0;
	for (var i=0; i<9; i++) {
		d1 += c.charAt(i)*(10-i);
 	}
	if (d1 == 0) return false;
	d1 = 11 - (d1 % 11);
	if (d1 > 9) d1 = 0;
	if (dv.charAt(0) != d1){
		return false;
	}
	d1 *= 2;
	for (var i = 0; i < 9; i++)	{
 		d1 += c.charAt(i)*(11-i);
	}
	d1 = 11 - (d1 % 11);
	if (d1 > 9) d1 = 0;
	if (dv.charAt(1) != d1){
		return false;
	}
    return true;
}

jQuery.fn.isNumeric = function(value) {

    return /^\d+(?:\.\d+)?$/.test(value);

}

// Função que valida CNPJ
//O algorítimo de validação de CNPJ é baseado em cálculos
//para o dígito verificador (os dois últimos)
//Não entrarei em detalhes de como funciona
jQuery.fn.validaCNPJ = function(CNPJ) {
	var a = new Array();
	var b = new Number;
	var c = [6,5,4,3,2,9,8,7,6,5,4,3,2];
	for (i=0; i<12; i++){
		a[i] = CNPJ.charAt(i);
		b += a[i] * c[i+1];
	}
	if ((x = b % 11) < 2) { a[12] = 0 } else { a[12] = 11-x }
	b = 0;
	for (y=0; y<13; y++) {
		b += (a[y] * c[y]);
	}
	if ((x = b % 11) < 2) { a[13] = 0; } else { a[13] = 11-x; }
	if ((CNPJ.charAt(12) != a[12]) || (CNPJ.charAt(13) != a[13])){
		return false;
	}
	return true;
}

function exibe_aba(aba_cao){
    let historico = (aba_cao == 'historico')? ' - Histórico' : '' ;

    $('#contrato').val('');
    $('#cpfcnpj').val('');
    $('#placa').val('');
    $('#frame01').html('').hide();
    $('#frame02').html('').hide();
    $('#frame04').html('').hide();
    $('#bt_pesquisar').removeAttr('disabled');
    $("#cliente").removeAttr('disabled');
    $("#cliente").val('');
    $('#id_cliente').val('');
    $('#acao').val('');

    $('#frm_pesquisar .modulo_conteudo').find('.bloco_titulo').text('Transferência de Titularidade' + historico);
    $('#acao').val(aba_cao);
}


function scroolToID(objID, speed){
    var obj = $('#'+objID);
    
    $('html, body').animate({
        scrollTop: $(obj).offset().top
    }, speed);
}

