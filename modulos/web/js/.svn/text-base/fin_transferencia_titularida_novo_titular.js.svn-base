jQuery(document).ready(function(){
	   
	jQuery("#cartaoDebito").hide();
	jQuery("#cartaoCredito").hide();
	jQuery("#cad_manual").hide();
	jQuery("#cad_manual_end_cob").hide();
	jQuery("#bt_atualizar_cont_inst").hide();
	$('#bt_atualizar_cont_inst').attr('disabled', 'disabled');
	jQuery("#bt_cancelar_cont_inst").hide();
	$('#bt_cancelar_cont_inst').attr('disabled', 'disabled');
	//jQuery("#cnpjcpf").mask("999.999.999-99");
	$('#nCartao').mask("9999 9999 9999 9999");
	$("#dataCartao").mask("99/99");
	//jQuery("#prccpf_aut").mask("999.999.999-99");
    //$('#prtrg').mask('99.999.999-9');    // Máscara para RG<br/>
   // $('#nAgencia').mask('9999-9');    // Máscara para AGÊNCIA BANCÁRIA
    
	if(jQuery("#cnpjcpf").val().length <= 11) {
		jQuery("#cnpjcpf").mask("999.999.999-99");
	}else{
		jQuery("#cnpjcpf").mask("99.999.999/9999-99");	
	}
    $("#prtrg").bind("keyup blur focus", function(e) {
    	
    	            e.preventDefault();
    	
    	            var expre = /[^\d]/g;
    	
    	            $(this).val($(this).val().replace(expre,''));
    	
    	       });

    
    $("#prcrg_aut").bind("keyup blur focus", function(e) {
    	
        e.preventDefault();

        var expre = /[^\d]/g;

        $(this).val($(this).val().replace(expre,''));

   });

    $("#prccpf_aut").bind("keyup blur focus", function(e) {
    	
        e.preventDefault();

        var expre = /[^\d]/g;

        $(this).val($(this).val().replace(expre,''));

   });
    
 /*   $("#prccpf_aut").bind("focus", function(e) {
    	
    	jQuery("#prccpf_aut").mask("999.999.999-99");

   });*/
    
	if(jQuery('#tipo_pagamento').val() != '') {
		
		var tipoCobranca = jQuery("#tipoPagamento").val();

		if(tipoCobranca == 'Debito') {
			jQuery('#formasPagamento').html('');
			jQuery("#cartaoDebito").show();
			jQuery("#tipoPagamentoAtual").val('Debito');
		}else if(tipoCobranca == 'Credito'){
			jQuery('#formasPagamento').html('');
			jQuery('#cartaoCredito').show();
			jQuery("#tipoPagamentoAtual").val('Credito');
		}
	}

	
	jQuery("#bt_atualizar_pessoas_auto").hide();
	$('#bt_atualizar_pessoas_auto').attr('disabled', 'disabled');
	jQuery("#bt_cancelar_pessoas_auto").hide();
	$('#bt_cancelar_pessoas_auto').attr('disabled', 'disabled');

	$('input[name=end_cobranca]').change(function(){
	if($('input[name=end_cobranca]').is(':checked')){
		
		//prpend_cidade
		var cidade = '';
		var bairro = '';

	        var cep = jQuery("#prpend_cep").val();
	        var pais = jQuery("#prpend_pais").val();
	        var estado = jQuery("#prpend_est").val();
	        var endereco = jQuery("#prpend_log").val();
	        var numero = jQuery("#prpend_num").val();
	        var complemento = jQuery("#prpend_compl").val();
	        
	        if(jQuery("#prpend_cid").val() == '') {
	        	cidade = jQuery("#prpend_cidade").val()
	        }else{
	        	cidade = jQuery("#prpend_cid").val();
	        }
	        
	        if(jQuery("#prpend_bairro").val() == '') {
	        	bairro = jQuery("#prpend_combobairro").val();
	        }else{
	        	bairro = jQuery("#prpend_bairro").val();
	        }

	        jQuery("#prpendcob_cep").val(cep);
	        jQuery("#prpendcob_pais").val(pais);
	        jQuery("#prpendcob_est").val(estado);
	        jQuery("#prpendcob_cid").val(cidade);
	        jQuery("#prpendcob_bairro").val(bairro);
	        jQuery("#prpendcob_log").val(endereco);
	        jQuery("#prpendcob_num").val(numero);
	        jQuery("#prpendcob_compl").val(complemento);  
	    }else{
	        jQuery("#prpendcob_cep").val('');
	        jQuery("#prpendcob_pais").val('');
	        jQuery("#prpendcob_est").val('');
	        jQuery("#prpendcob_cid").val('');
	        jQuery("#prpendcob_bairro").val('');
	        jQuery("#prpendcob_log").val('');
	        jQuery("#prpendcob_num").val('');
	        jQuery("#prpendcob_compl").val(''); 
	    } 
	});

	jQuery("#bt_atualizar_cont_emerg").hide();
	$('#bt_atualizar_cont_emerg').attr('disabled', 'disabled');
	jQuery("#bt_cancelar_cont_emerg").hide();
	$('#bt_cancelar_cont_emerg').attr('disabled', 'disabled');
	
	
	jQuery("#prcfone_res_aut").mask("(99) 9999-9999");
	jQuery("#prcfone_cel_aut").mask("(99) 9999-9999");
	jQuery("#prcfone_com_aut").mask("(99) 9999-9999");
	jQuery("#prcfone_cont").mask("(99) 9999-9999");
	jQuery("#prcfone_cont2").mask("(99) 9999-9999");
	jQuery("#prcfone_cont3").mask("(99) 9999-9999");
	jQuery("#prpend_cep").mask("99999-999");
	jQuery("#prpendcob_cep").mask("99999-999");
	jQuery("#prcfone_res_cont_emerg").mask("(99) 9999-9999");
	jQuery("#prcfone_com_cont_emerg").mask("(99) 9999-9999");
	jQuery("#prcfone_cel_cont_emerg").mask("(99) 9999-9999");
	jQuery("#nCartao").mask("9999 9999 9999 9999");
	$("#dataCartao").mask("99/99");
	
	
	jQuery("#prcfone_res_cont_assist").mask("(99) 9999-9999");
	jQuery("#prcfone_com_cont_assist").mask("(99) 9999-9999");
	jQuery("#prcfone_cel_cont_assist").mask("(99) 9999-9999");
	
	
	$( "#tipo_pagamento" ).change(function() {
		   var optionSelected = $("option:selected", this);
		    var valueSelected = this.value;
		    if(valueSelected == '') {
		    	jQuery("#tipoPagamentoAtual").val('');
		    	jQuery('#formasPagamento').html('');
				jQuery("#cartaoCredito").hide();
				jQuery("#cartaoDebito").hide();
				jQuery("#idBanco").val('');
				jQuery("#nomeBanco").val('');
				jQuery("#cartaoCredito").val('');
				jQuery("#cartaoDebito").val('');
		    }else{
		    	jQuery("#tipoPagamentoAtual").val('');
		    	jQuery.fn.formaPagamentos(valueSelected);
		    }
		    
		    
	});

	

	
	jQuery("body").delegate('#bt_cancelar_cont_inst','click',function(){

		jQuery("#bt_add_cont_inst").show();
    	$('#bt_add_cont_inst').removeAttr('disabled');
    	jQuery("#bt_atualizar_cont_inst").hide();
    	jQuery("#bt_cancelar_cont_inst").hide();
    	$('#bt_atualizar_cont_inst').attr('disabled', 'disabled');
    	$('#bt_cancelar_cont_inst').attr('disabled', 'disabled');
    	jQuery('input[name=ptcioid_cont_assist]').val('');
    	jQuery('input[name=id_prop_InstalAssis]').val('');
    	jQuery('input[name=prcnome_cont_assist]').val('');
		jQuery('input[name=prcfone_res_cont_assist]').val('');
		jQuery('input[name=prcfone_com_cont_assist]').val('');
		jQuery('input[name=prcfone_cel_cont_assist]').val('');
		jQuery('input[name=prcid_nextel_cont_assist]').val('');

	});
	
	jQuery("body").delegate('#bt_cancelar_cont_emerg','click',function(){

		jQuery("#bt_add_cont_emerg").show();
    	$('#bt_add_cont_emerg').removeAttr('disabled');
    	jQuery("#bt_atualizar_cont_emerg").hide();
    	jQuery("#bt_cancelar_cont_emerg").hide();
    	$('#bt_atualizar_cont_emerg').attr('disabled', 'disabled');
    	$('#bt_cancelar_cont_emerg').attr('disabled', 'disabled');
    	jQuery('input[name=ptceoid_cont_emerg]').val('');
    	jQuery('input[name=id_prop_contEmerg]').val('');
    	jQuery('input[name=prcnome_cont_emerg]').val('');
		jQuery('input[name=prcfone_res_cont_emerg]').val('');
		jQuery('input[name=prcfone_com_cont_emerg]').val('');
		jQuery('input[name=prcfone_cel_cont_emerg]').val('');
		jQuery('input[name=prcid_nextel_cont_emerg]').val('');

	});

	jQuery("body").delegate('#bt_cancelar_pessoas_auto','click',function(){
		$('#prcnome_aut').removeClass("erro");
		$('#prccpf_aut').removeClass("erro");
		$('#prcrg_aut').removeClass("erro");
		$('#prtfone_res_aut').removeClass("erro");
		$('#prtfone_com_aut').removeClass("erro");
		$('#prtfone_cel_aut').removeClass("erro");
		$('#prtid_nextel_aut').removeClass("erro");
		
		$("#msgalertaPessoasAut").html("Favor preencher o campo RG.").hide();
		jQuery("#bt_add_pessoas_auto").show();
    	$('#bt_add_pessoas_auto').removeAttr('disabled');
    	jQuery("#bt_atualizar_pessoas_auto").hide();
    	jQuery("#bt_cancelar_pessoas_auto").hide();
    	$('#bt_atualizar_pessoas_auto').attr('disabled', 'disabled');
    	$('#bt_cancelar_pessoas_auto').attr('disabled', 'disabled');
    	jQuery('input[name=prtcnome_aut]').val('');
    	jQuery('input[name=prtcpf_aut]').val('');
    	jQuery('input[name=prtrg_aut]').val('');
    	jQuery('input[name=prtfone_res_aut]').val('');
    	jQuery('input[name=prtfone_com_aut]').val('');
		jQuery('input[name=prtfone_cel_aut]').val('');
		jQuery('input[name=prtid_nextel_aut]').val('');


	});

	
	jQuery("body").delegate('#salva_proposta_titularidade','click',function(){
		/*$('#prpend_email').removeClass("erro");
		$("#msgalertacliente").html("E-mail invalido").hide();
		if(!jQuery.fn.validateEmail(jQuery("#prpend_email").val())) {
				$('#prpend_email').removeClass("erro");
				$('#responsavel_titular').removeClass("erro");
				$("#msgalertacliente").html("E-mail invalido").hide();
				$("#msgalertacliente").html("E-mail invalido").showMessage();
				$('#prpend_email').addClass('erro');
				//$('#editarCliente').animate({scrollTop:0}, 'slow');
				$('html, body').animate({ scrollTop: $('#editarEndereco').offset().top }, 'slow');
				//$('html, body').animate({scrollTop:$('#msgalertacliente')}, 'slow');
	   }*/
		
		 /*  var dataForm = (document.forms[0]["dataDesativacao"].value).split("/");  
	        var hoje = new Date();  
	        var dataInformada = new Date(dataForm[2], dataForm[1]-1, dataForm[0]);  
	          
	        if ( hoje < dataInformada )  
	        {  
	            alert("Hoje [" + hoje + "] é ANTERIOR a data informada! [" + dataInformada + "]");  
	        } */
		
		var dataFund = jQuery("#prpfund_dt").val();
		var dataNasc = jQuery("#prpnas_dt").val();
		var dataEmissao = jQuery("#prpemi_dt").val();
		
		if(dataNasc == '' || dataNasc == null || dataNasc.length == 0  || typeof dataNasc === "undefined"){
			 if(dataFund == '' || dataFund == null || dataFund.length == 0  || typeof dataFund === "undefined"){
					 jQuery.fn.salvapropostatitularidade();
			 }else {
				
				 if(jQuery.fn.validaDataNasc(dataFund) == 0){
						alert("Data de fundação inválido.");
					}else{
				    	 jQuery.fn.salvapropostatitularidade();
				    }
			 }

			
		}else {
			
			if(jQuery.fn.validaDataNasc(dataNasc) == 0){
				alert("Data de nascimento inválida.");
			}else if(jQuery.fn.validaDataNasc(dataNasc) < 18){
				alert("O novo cliente deve ter mais de 18 anos.");
			}else{
				
				if(dataEmissao != '' || dataEmissao != null || dataEmissao.length != 0  || typeof dataEmissao != "undefined"){
					    var dataForm = (dataEmissao).split("/");  
				        var hoje = new Date();  
				        var dataInformada = new Date(dataForm[2], dataForm[1]-1, dataForm[0]);  
				          
				        if ( hoje < dataInformada )  
				        {  
				        	alert("Data de emissão é maior que data atual.");
				        }else{
				        	jQuery.fn.salvapropostatitularidade();
				        }
				}else{
					jQuery.fn.salvapropostatitularidade();
				}
      
		    	 
		    }
		}
		
		

	});

	


	

	
	jQuery("body").delegate('#nCartao','focus',function(){
		jQuery("#nCartao").mask("9999 9999 9999 9999");
	});
	
	jQuery("body").delegate("#dataCartao",'focus',function(){
		$("#dataCartao").mask("99/99");
	});
	
	jQuery("body").delegate('#prtno_documento','focus',function(){
		jQuery("#prtno_documento").mask("999.999.999-99");
	});
	
	jQuery("body").delegate('#prpend_cep','focus',function(){
		jQuery("#prpend_cep").mask("99999-999");
	});
	
	jQuery("body").delegate('#prpendcob_cep','focus',function(){
		jQuery('#prpendcob_cep').mask("99999-999");
	});
	
	jQuery("body").delegate('#btn_excluir_pessoas_aut','click',function(){
		jQuery.fn.excluircontatoPessoaAutorizada(jQuery(this).attr('rel'));
	});
	
	jQuery("body").delegate("#btn_excluir_contemerg",'click',function(){
		jQuery.fn.excluircontatoEmergencia(jQuery(this).attr('rel'));
	});
	
	jQuery("body").delegate('#btn_editar_contemerg','click',function(){
		jQuery.fn.editarEmergencia(jQuery(this).attr('rel'));
	});
	
	jQuery("body").delegate('#bt_atualizar_pessoas_auto','click',function(){
		if(jQuery("#prcnome_aut").val() == '' || jQuery("#prcnome_aut").val() == null || jQuery("#prcnome_aut").val().length == 0  || typeof jQuery("#prcnome_aut").val() === "undefined"){
			$("#msgalertaPessoasAut").html("Favor preencher o campo nome.").hide();
			$("#msgalertaPessoasAut").html("Favor preencher o campo nome.").showMessage();
			$('#prcnome_aut').addClass('erro');
		}else if(jQuery("#prccpf_aut").val() == '' || jQuery("#prccpf_aut").val() == null || jQuery("#prccpf_aut").val().length == 0  || typeof jQuery("#prccpf_aut").val() === "undefined"){
			$('#prcnome_aut').removeClass("erro");
			$("#msgalertaPessoasAut").html("Favor preencher o campo cpf.").hide();
			$("#msgalertaPessoasAut").html("Favor preencher o campo cpf.").showMessage();
			$('#prccpf_aut').addClass('erro');
		}
		else if(jQuery("#prcrg_aut").val() == '' || jQuery("#prcrg_aut").val() == null || jQuery("#prcrg_aut").val().length == 0  || typeof jQuery("#prcrg_aut").val() === "undefined"){
			$('#prcnome_aut').removeClass("erro");
			$('#prccpf_aut').removeClass("erro");
			$("#msgalertaPessoasAut").html("Favor preencher o campo RG.").hide();
			$("#msgalertaPessoasAut").html("Favor preencher o campo RG.").showMessage();
			$('#prcrg_aut').addClass('erro');
		}
		else if((jQuery("#prcfone_res_aut").val() == '' || jQuery("#prcfone_res_aut").val() == null || jQuery("#prcfone_res_aut").val().length == 0  || typeof jQuery("#prcfone_res_aut").val() === "undefined")
				&& (jQuery("#prcfone_com_aut").val() == '' || jQuery("#prcfone_com_aut").val() == null || jQuery("#prcfone_com_aut").val().length == 0  || typeof jQuery("#prcfone_com_aut").val() === "undefined")
				&& (jQuery("#prcfone_cel_aut").val() == '' || jQuery("#prcfone_cel_aut").val() == null || jQuery("#prcfone_cel_aut").val().length == 0  || typeof jQuery("#prcfone_cel_aut").val() === "undefined")){
			
			$('#prcnome_aut').removeClass("erro");
			$('#prccpf_aut').removeClass("erro");
			$('#prcrg_aut').removeClass("erro");
			$("#msgalertaPessoasAut").html("Favor preencher ao menos 1 telefone.").hide();
			$("#msgalertaPessoasAut").html("Favor preencher ao menos 1 telefone.").showMessage();

		}else{

			$('#prcnome_aut').removeClass("erro");
			$('#prccpf_aut').removeClass("erro");
			$('#prcrg_aut').removeClass("erro");
			$("#msgalertaPessoasAut").html("Favor preencher o campo RG.").hide();
			
			var s = (jQuery("#prccpf_aut").val()).replace(/\D/g,'');
			var tam =(s).length; // removendo os caracteres não numéricos
			
			// se for CPF
			if (tam==11 ){
				if(s == 00000000000 || s == 11111111111 || s == 22222222222 || s == 33333333333 || s == 44444444444 || 
						s == 55555555555 || s == 66666666666 || s == 77777777777 || s == 88888888888 || s == 99999999999){
					$("#msgalertaPessoasAut").html("Numero de CPF inválido!").hide();
					$("#msgalertaPessoasAut").html("Numero de CPF inválido!").showMessage();
					$('#prccpf_aut').addClass('erro');
				}
				else if (!jQuery.fn.validaCPF(s)){ // chama a função que valida o CPF
					$('#prccpf_aut').removeClass("erro");
					$("#msgalertaPessoasAut").html("Numero de CPF inválido!").hide();
					$("#msgalertaPessoasAut").html("Numero de CPF inválido!").hide();
					$("#msgalertaPessoasAut").html("Numero de CPF inválido!").showMessage();
					$('#prccpf_aut').addClass('erro');
				}else{
					$('#prccpf_aut').removeClass("erro");
					$("#msgalertaPessoasAut").html("Numero de CPF inválido!").hide();
					jQuery.fn.atualizarpessoasAutorizadas();
				}
			}
	
		}
		
		
	});
	
	jQuery("body").delegate('#bt_atualizar_cont_emerg','click',function(){
		if(jQuery("#prcnome_cont_emerg").val() == '' || jQuery("#prcnome_cont_emerg").val() == null || jQuery("#prcnome_cont_emerg").val().length == 0  || typeof jQuery("#prcnome_cont_emerg").val() === "undefined"){
			$("#msgalertaPessoaEmergencia").html("Favor preencher o campo nome.").hide();
			$("#msgalertaPessoaEmergencia").html("Favor preencher o campo nome.").showMessage();
			$('#prcnome_cont_emerg').addClass('erro');
		}else if((jQuery("#prcfone_res_cont_emerg").val() == '' || jQuery("#prcfone_res_cont_emerg").val() == null || jQuery("#prcfone_res_cont_emerg").val().length == 0  || typeof jQuery("#prcfone_res_cont_emerg").val() === "undefined")
				&& (jQuery("#prcfone_com_cont_emerg").val() == '' || jQuery("#prcfone_com_cont_emerg").val() == null || jQuery("#prcfone_com_cont_emerg").val().length == 0  || typeof jQuery("#prcfone_com_cont_emerg").val() === "undefined")
				&& (jQuery("#prcfone_cel_cont_emerg").val() == '' || jQuery("#prcfone_cel_cont_emerg").val() == null || jQuery("#prcfone_cel_cont_emerg").val().length == 0  || typeof jQuery("#prcfone_cel_cont_emerg").val() === "undefined")){
			
			$('#prcnome_cont_emerg').removeClass("erro");
			$("#msgalertaPessoaEmergencia").html("Favor preencher ao menos 1 telefone.").hide();
			$("#msgalertaPessoaEmergencia").html("Favor preencher ao menos 1 telefone.").showMessage();

		}else{
			$("#msgalertaPessoaEmergencia").html("Favor preencher o campo RG.").hide();
			jQuery.fn.atualizarcontatoEmergencia();
		}
		
	});
	
	jQuery("body").delegate('#bt_atualizar_cont_inst','click',function(){
		if(jQuery("#prcnome_cont_assist").val() == '' || jQuery("#prcnome_cont_assist").val() == null || jQuery("#prcnome_cont_assist").val().length == 0  || typeof jQuery("#prcnome_cont_assist").val() === "undefined"){
			$("#msgalertaInstalacaoAssistencia").html("Favor preencher o campo nome.").hide();
			$("#msgalertaInstalacaoAssistencia").html("Favor preencher o campo nome.").showMessage();
			$('#prcnome_cont_assist').addClass('erro');
		}else if((jQuery("#prcfone_res_cont_assist").val() == '' || jQuery("#prcfone_res_cont_assist").val() == null || jQuery("#prcfone_res_cont_assist").val().length == 0  || typeof jQuery("#prcfone_res_cont_assist").val() === "undefined")
				&& (jQuery("#prcfone_com_cont_assist").val() == '' || jQuery("#prcfone_com_cont_assist").val() == null || jQuery("#prcfone_com_cont_assist").val().length == 0  || typeof jQuery("#prcfone_com_cont_assist").val() === "undefined")
				&& (jQuery("#prcfone_cel_cont_assist").val() == '' || jQuery("#prcfone_cel_cont_assist").val() == null || jQuery("#prcfone_cel_cont_assist").val().length == 0  || typeof jQuery("#prcfone_cel_cont_assist").val() === "undefined")){
			
			$('#prcnome_cont_assist').removeClass("erro");

			$("#msgalertaInstalacaoAssistencia").html("Favor preencher ao menos 1 telefone.").hide();
			$("#msgalertaInstalacaoAssistencia").html("Favor preencher ao menos 1 telefone.").showMessage();

		}else{
			$("#msgalertaInstalacaoAssistencia").html("Favor preencher o campo RG.").hide();
			jQuery.fn.atualizarcontatoInstalacao();
		}
		
	});
	
	jQuery("body").delegate('#btn_excluir_carta','click',function(){
		jQuery.fn.excluirCarta(jQuery(this).attr('rel'));
	});
	
	jQuery("body").delegate('#btn_excluir_instAssistencia','focus',function(){
		jQuery.fn.excluircontatoInstalacao(jQuery(this).attr('rel'));
	});
	
	jQuery("body").delegate('#btn_excluir_arquivo','click',function(){
		jQuery.fn.excluirArquivo(jQuery(this).attr('rel'));
	});

	jQuery("body").delegate('#btn_editar_pessoas_aut','click',function(){
		jQuery.fn.editarPessoautorizada(jQuery(this).attr('rel'));
	});
	
	jQuery("body").delegate('#btn_editar_instAssistencia','click',function(){
		jQuery.fn.editarinstAssistencia(jQuery(this).attr('rel'));
	});
	
	jQuery("body").delegate('#bt_add_arquivos_carta','click',function(){
	
		if(jQuery("#arqAnexoReqCarta").val() == '' || jQuery("#arqAnexoReqCarta").val() == null || jQuery("#arqAnexoReqCarta").val().length == 0  || typeof jQuery("#arqAnexoReqCarta").val() === "undefined"){
			$("#msgalertaaddarquivocarta").html("Favor selecionar uma carta.").hide();
			$("#msgalertaaddarquivocarta").html("Favor selecionar uma carta.").showMessage();
		}else if(jQuery("#arqAnexoReqDescricaoCarta").val() == '' || jQuery("#arqAnexoReqDescricaoCarta").val() == null || jQuery("#arqAnexoReqDescricaoCarta").val().length == 0  || typeof jQuery("#arqAnexoReqDescricaoCarta").val() === "undefined"){
			$("#msgalertaaddarquivocarta").html("Favor preencher o campo Descrição.").hide();
			$("#msgalertaaddarquivocarta").html("Favor preencher o campo Descrição.").showMessage();
			$('#arqAnexoReqDescricaoCarta').addClass('erro');
		}else{
			$('#arqAnexoReqDescricaoCarta').removeClass("erro");
			$("#msgalertaaddarquivocarta").html("Favor preencher o campo Descrição.").hide();
			jQuery.fn.anexoCarta();
		}
		
	});
	
	jQuery("body").delegate('#aprova_credito','click',function(){
		jQuery.fn.aprovacaoAnaliseCredito();
	});
	
	jQuery("body").delegate('#aprova_divida','click',function(){
		jQuery.fn.aprovacaoTransferenciaTitularidade();
	});

	$("#reprova_credito").click(function() {
	$("#titularidadediv").css("display", "block");
	$("#tipoReprovacao").val('RAC');
	$('#aprova_credito').attr("disabled", true);
	$('#reprova_credito').attr("disabled", true);
	$('#aprova_divida').attr("disabled", true);
	$('#reprova_divida').attr("disabled", true);
	});
	
	$("#reprova_divida").click(function() {
		$("#titularidadediv").css("display", "block");
		$("#tipoReprovacao").val('RTT');
		$('#aprova_credito').attr("disabled", true);
		$('#reprova_credito').attr("disabled", true);
		$('#aprova_divida').attr("disabled", true);
		$('#reprova_divida').attr("disabled", true);
	});
	
	
	$("#reprovacao #cancelar").click(function() {
	$(this).parent().parent().hide();
	$('#aprova_credito').attr("disabled", false);
	$('#reprova_credito').attr("disabled", false);
	$('#aprova_divida').attr("disabled", false);
	$('#reprova_divida').attr("disabled", false);
	});
	// Contact form popup send-button click event.
	$("#salvar").click(function() {
	var descricao = $("#textareaReprova").val();

	if(descricao == ''){
		alert('Campo descrição obrigatório');
	}else{
		if($("#tipoReprovacao").val() == 'RAC') {
			jQuery.fn.reprovacaoAnaliseCredito();
		}else{
			jQuery.fn.reprovacaoTransferenciaTitularidade();
		}
	
	}
	
	});

	
	jQuery('#prcfone_res_aut')  
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
	
	
	jQuery('#prcfone_com_aut')  
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
	
	
	
	jQuery('#prcfone_cont')  
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
	
	jQuery('#prcfone_cont3')  
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
	
	
	jQuery('#prcfone_cont2')  
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
	
	jQuery('#prcfone_cel_aut')  
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
	
	jQuery('#prcfone_com_aut')  
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
	
	
	jQuery('#prcfone_cel_cont_assist')  
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
	
	jQuery('#prcfone_res_cont_assist')  
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
	
	jQuery('#prcfone_com_cont_assist')  
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

	
	jQuery('#prcfone_res_cont_emerg')  
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
	
	jQuery('#prcfone_com_cont_emerg')  
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
	
	jQuery('#prcfone_cel_cont_emerg')  
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
	

	
	jQuery("body").delegate('#bt_add_pessoas_auto','click',function(){

		if(jQuery("#prcnome_aut").val() == '' || jQuery("#prcnome_aut").val() == null || jQuery("#prcnome_aut").val().length == 0  || typeof jQuery("#prcnome_aut").val() === "undefined"){
			$("#msgalertaPessoasAut").html("Favor preencher o campo nome.").hide();
			$("#msgalertaPessoasAut").html("Favor preencher o campo nome.").showMessage();
			$('#prcnome_aut').addClass('erro');
		}else if(jQuery("#prccpf_aut").val() == '' || jQuery("#prccpf_aut").val() == null || jQuery("#prccpf_aut").val().length == 0  || typeof jQuery("#prccpf_aut").val() === "undefined"){
			$('#prcnome_aut').removeClass("erro");
			$("#msgalertaPessoasAut").html("Favor preencher o campo cpf.").hide();
			$("#msgalertaPessoasAut").html("Favor preencher o campo cpf.").showMessage();
			$('#prccpf_aut').addClass('erro');
		}
		else if(jQuery("#prcrg_aut").val() == '' || jQuery("#prcrg_aut").val() == null || jQuery("#prcrg_aut").val().length == 0  || typeof jQuery("#prcrg_aut").val() === "undefined"){
			$('#prcnome_aut').removeClass("erro");
			$('#prccpf_aut').removeClass("erro");
			$("#msgalertaPessoasAut").html("Favor preencher o campo RG.").hide();
			$("#msgalertaPessoasAut").html("Favor preencher o campo RG.").showMessage();
			$('#prcrg_aut').addClass('erro');
		}
		else if((jQuery("#prcfone_res_aut").val() == '' || jQuery("#prcfone_res_aut").val() == null || jQuery("#prcfone_res_aut").val().length == 0  || typeof jQuery("#prcfone_res_aut").val() === "undefined")
				&& (jQuery("#prcfone_com_aut").val() == '' || jQuery("#prcfone_com_aut").val() == null || jQuery("#prcfone_com_aut").val().length == 0  || typeof jQuery("#prcfone_com_aut").val() === "undefined")
				&& (jQuery("#prcfone_cel_aut").val() == '' || jQuery("#prcfone_cel_aut").val() == null || jQuery("#prcfone_cel_aut").val().length == 0  || typeof jQuery("#prcfone_cel_aut").val() === "undefined")){
			
			$('#prcnome_aut').removeClass("erro");
			$('#prccpf_aut').removeClass("erro");
			$('#prcrg_aut').removeClass("erro");
			$("#msgalertaPessoasAut").html("Favor preencher ao menos 1 telefone.").hide();
			$("#msgalertaPessoasAut").html("Favor preencher ao menos 1 telefone.").showMessage();

		}else{
			$('#prcnome_aut').removeClass("erro");
			$('#prccpf_aut').removeClass("erro");
			$('#prcrg_aut').removeClass("erro");
			$("#msgalertaPessoasAut").html("Favor preencher o campo RG.").hide();
			
			var s = (jQuery("#prccpf_aut").val()).replace(/\D/g,'');
			var tam =(s).length; // removendo os caracteres não numéricos
			
			// se for CPF
			if (tam==11 ){
				if(s == 00000000000 || s == 11111111111 || s == 22222222222 || s == 33333333333 || s == 44444444444 || 
						s == 55555555555 || s == 66666666666 || s == 77777777777 || s == 88888888888 || s == 99999999999){
					$("#msgalertaPessoasAut").html("Numero de CPF inválido!").hide();
					$("#msgalertaPessoasAut").html("Numero de CPF inválido!").showMessage();
					$('#prccpf_aut').addClass('erro');
				}
			else if (!jQuery.fn.validaCPF(s)){ // chama a função que valida o CPF
					$("#msgalertaPessoasAut").html("Numero de CPF inválido!").hide();
					$("#msgalertaPessoasAut").html("Numero de CPF inválido!").showMessage();
					$('#prccpf_aut').addClass('erro');
				}else{
					$('#prccpf_aut').removeClass("erro");
					$("#msgalertaPessoasAut").html("Numero de CPF inválido!").hide();
					jQuery.fn.cadPessoaAutorizada();
				}
			}

		}
		
	});
	
	
	jQuery("body").delegate('#bt_add_cont_emerg','click',function(){
	   
		if(jQuery("#prcnome_cont_emerg").val() == '' || jQuery("#prcnome_cont_emerg").val() == null || jQuery("#prcnome_cont_emerg").val().length == 0  || typeof jQuery("#prcnome_cont_emerg").val() === "undefined"){
			$("#msgalertaPessoaEmergencia").html("Favor preencher o campo nome.").hide();
			$("#msgalertaPessoaEmergencia").html("Favor preencher o campo nome.").showMessage();
			$('#prcnome_cont_emerg').addClass('erro');
		}else if((jQuery("#prcfone_res_cont_emerg").val() == '' || jQuery("#prcfone_res_cont_emerg").val() == null || jQuery("#prcfone_res_cont_emerg").val().length == 0  || typeof jQuery("#prcfone_res_cont_emerg").val() === "undefined")
				&& (jQuery("#prcfone_com_cont_emerg").val() == '' || jQuery("#prcfone_com_cont_emerg").val() == null || jQuery("#prcfone_com_cont_emerg").val().length == 0  || typeof jQuery("#prcfone_com_cont_emerg").val() === "undefined")
				&& (jQuery("#prcfone_cel_cont_emerg").val() == '' || jQuery("#prcfone_cel_cont_emerg").val() == null || jQuery("#prcfone_cel_cont_emerg").val().length == 0  || typeof jQuery("#prcfone_cel_cont_emerg").val() === "undefined")){
			
			$('#prcnome_cont_emerg').removeClass("erro");

			$("#msgalertaPessoaEmergencia").html("Favor preencher ao menos 1 telefone.").hide();
			$("#msgalertaPessoaEmergencia").html("Favor preencher ao menos 1 telefone.").showMessage();

		}else{
			$("#msgalertaPessoaEmergencia").html("Favor preencher o campo RG.").hide();
			jQuery.fn.cadContatoEmergencia();
		}
		
		
	});
	
	jQuery("body").delegate('#bt_add_cont_inst','click',function(){
		
		if(jQuery("#prcnome_cont_assist").val() == '' || jQuery("#prcnome_cont_assist").val() == null || jQuery("#prcnome_cont_assist").val().length == 0  || typeof jQuery("#prcnome_cont_assist").val() === "undefined"){
			$("#msgalertaInstalacaoAssistencia").html("Favor preencher o campo nome.").hide();
			$("#msgalertaInstalacaoAssistencia").html("Favor preencher o campo nome.").showMessage();
			$('#prcnome_cont_assist').addClass('erro');
		}else if((jQuery("#prcfone_res_cont_assist").val() == '' || jQuery("#prcfone_res_cont_assist").val() == null || jQuery("#prcfone_res_cont_assist").val().length == 0  || typeof jQuery("#prcfone_res_cont_assist").val() === "undefined")
				&& (jQuery("#prcfone_com_cont_assist").val() == '' || jQuery("#prcfone_com_cont_assist").val() == null || jQuery("#prcfone_com_cont_assist").val().length == 0  || typeof jQuery("#prcfone_com_cont_assist").val() === "undefined")
				&& (jQuery("#prcfone_cel_cont_assist").val() == '' || jQuery("#prcfone_cel_cont_assist").val() == null || jQuery("#prcfone_cel_cont_assist").val().length == 0  || typeof jQuery("#prcfone_cel_cont_assist").val() === "undefined")){
			
			$('#prcnome_cont_assist').removeClass("erro");

			$("#msgalertaInstalacaoAssistencia").html("Favor preencher ao menos 1 telefone.").hide();
			$("#msgalertaInstalacaoAssistencia").html("Favor preencher ao menos 1 telefone.").showMessage();

		}else{
			$("#msgalertaInstalacaoAssistencia").html("Favor preencher o campo RG.").hide();
			jQuery.fn.cadContatoInstalacaoAssistencia();
		}
		
	});
	
	jQuery("body").delegate('#bt_add_arquivos','click',function(){
		
		if(jQuery("#arqAnexoReqs").val() == '' || jQuery("#arqAnexoReqs").val() == null || jQuery("#arqAnexoReqs").val().length == 0  || typeof jQuery("#arqAnexoReqs").val() === "undefined"){
			$("#msgalertaaddarquivo").html("Favor selecionar um arquivo.").hide();
			$("#msgalertaaddarquivo").html("Favor selecionar um arquivo.").showMessage();
		}else if(jQuery("#arqAnexoReqDescricao").val() == '' || jQuery("#arqAnexoReqDescricao").val() == null || jQuery("#arqAnexoReqDescricao").val().length == 0  || typeof jQuery("#arqAnexoReqDescricao").val() === "undefined"){
			$("#msgalertaaddarquivo").html("Favor preencher o campo Descrição.").hide();
			$("#msgalertaaddarquivo").html("Favor preencher o campo Descrição.").showMessage();
			$('#arqAnexoReqDescricao').addClass('erro');
		}else{
			$('#arqAnexoReqDescricao').removeClass("erro");
			$("#msgalertaaddarquivo").html("Favor preencher o campo Descrição.").hide();
			jQuery.fn.anexarArquivos();
		}
		

	
		
	});
	

	$('#prpend_cep').blur(function(){
		jQuery('#imgCEP_endereco').html("");
		//var cep = jQuery("#prpend_cep").val();
		var cep = $("#prpend_cep").val().replace(/[^\d]+/g,'');
		if(cep != ''){
			jQuery.fn.buscaEnderecoCep(cep);
		}
       
	});
	
	$('#prpendcob_cep').blur(function(){
		jQuery('#imgCEP_enderecoCob').html("");
		//var cep = jQuery("#prpendcob_cep").val();
		var cep = $("#prpendcob_cep").val().replace(/[^\d]+/g,'');
		if(cep != ''){
			jQuery.fn.buscaEnderecoCepCobranca(cep);
		}
		
	});
	
	$('#prpend_est').change(function(){
		jQuery('#imgCEP_endereco').html("");
		var sigla = $("#prpend_est option:selected").text();
		jQuery.fn.buscaCidadesIdUF(sigla);
	});


	jQuery("body").delegate("#prpend_cidade",'change',function(){
		var sigla = $("#prpend_est option:selected").text();
		var cidade = jQuery("#prpend_cidade").val();
		jQuery.fn.buscaBairrosIdCidade(sigla,cidade);
	});
	
	jQuery("body").delegate("#cad_manual",'click',function(){
		        $("#bairrosSelect").html("");
    			$("#bairrosSelect").hide();
    			jQuery('#prpend_bairro').show();
    			jQuery("#cad_manual").hide();
	});
	
	$('#prpendcob_est').change(function(){
		 jQuery('#imgCEP_enderecoCob').html("");
		var sigla = $("#prpendcob_est option:selected").text();
		jQuery.fn.buscaCidadesIdUFEnderecoCobranca(sigla);
	});

	
	jQuery("body").delegate("#prpendCob_cidade",'change',function(){
		var sigla = $("#prpendcob_est option:selected").text();
		var cidade = jQuery("#prpendCob_cidade").val();
		jQuery.fn.buscaBairrosIdCidadeEndCobranca(sigla,cidade);
	});
	
	jQuery("body").delegate('#cad_manual_end_cob','click',function(){
	       $("#bairrosSelectCobr").html("");
			$("#bairrosSelectCobr").hide();
			jQuery('#prpendcob_bairro').show();
			jQuery("#cad_manual_end_cob").hide();
	});
});

jQuery.fn.cadPessoaAutorizada = function(){
	jQuery("#autorizaPessoa").hide();
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data: {
			acao :'cadPessoaAutorizada',
			ptraoid : jQuery('input[name=ptraoid]').val(),
			prtnome_aut :jQuery('input[name=prtcnome_aut]').val(),
			prtcpf_aut : jQuery('input[name=prtcpf_aut]').val(),
			prtrg_aut : jQuery('input[name=prtrg_aut]').val(),
			prtfone_res_aut : jQuery('input[name=prtfone_res_aut]').val(),
			prtfone_com_aut : jQuery('input[name=prtfone_com_aut]').val(),
			prtfone_cel_aut : jQuery('input[name=prtfone_cel_aut]').val(),
			prtid_nextel_aut :jQuery('input[name=prtid_nextel_aut]').val(),

		},
		
		beforeSend: function(){	
			jQuery('#autorizaPessoa').html('<center><img src="images/loading.gif" alt="" /></center>');
		},
		success: function(data) {
			
			data = jQuery.parseJSON(data);
			  if(data.tipo_msg == 'i'){
			    	alert(data.msg);
			    }else if(data.tipo_msg == 'e'){
			        alert(data.msg);
			    }else{
			    	jQuery('input[name=ptpaoid_pessoa_aut]').val('');
			    	jQuery('input[name=id_prop_pessoaAut]').val('');
			    	jQuery('input[name=prtcnome_aut]').val(''),
			    	jQuery('input[name=prtcpf_aut]').val('');
			    	jQuery('input[name=prtrg_aut]').val('');
					jQuery('input[name=prtfone_res_aut]').val('');
					jQuery('input[name=prtfone_com_aut]').val('');
					jQuery('input[name=prtfone_cel_aut]').val('');
					jQuery('input[name=prtid_nextel_aut]').val('');
			    	jQuery('#autorizaPessoa').html('');
					jQuery('#adicionaAutorizaPessoa').html('');
					jQuery('#adicionaAutorizaPessoa').append(data.html);
			    }

			
		}
			
	});
	

}

jQuery.fn.cadContatoEmergencia =function(){
	jQuery("#contatoEmergencia").hide();

	//bt_add_cont_emerg
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data:{
			acao :'cadContatoEmergencia',
			ptraoid : jQuery('input[name=ptraoid]').val(),
			prcnome_cont_emerg :jQuery('input[name=prcnome_cont_emerg]').val(),
			prcfone_res_cont_emerg : jQuery('input[name=prcfone_res_cont_emerg]').val(),
			prcfone_com_cont_emerg : jQuery('input[name=prcfone_com_cont_emerg]').val(),
			prcfone_cel_cont_emerg : jQuery('input[name=prcfone_cel_cont_emerg]').val(),
			prcid_nextel_cont_emerg :jQuery('input[name=prcid_nextel_cont_emerg]').val(),
		},
		beforeSend:function(){
			jQuery('#contatoEmergencia').html('<center><img src="images/loading.gif" alt="" /></center>');
		},
		success:function(data) {
			data = jQuery.parseJSON(data);
			 if(data.tipo_msg == 'i'){
			    	alert(data.msg);
			    }else if(data.tipo_msg == 'e'){
			        alert(data.msg);
			    }else{
			    	
			    	jQuery('input[name=prcnome_cont_emerg]').val('');
					jQuery('input[name=prcfone_res_cont_emerg]').val('');
					jQuery('input[name=prcfone_com_cont_emerg]').val('');
					jQuery('input[name=prcfone_cel_cont_emerg]').val('');
					jQuery('input[name=prcid_nextel_cont_emerg]').val('');
					jQuery('#adicionaContatoEmergencia').html('');
					jQuery('#adicionaContatoEmergencia').append(data.html);
			    }

		}
		
	});
}

jQuery.fn.cadContatoInstalacaoAssistencia = function(){
	
	jQuery("#contatoAssistencia").hide();

	//bt_add_cont_emerg
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data:{
			acao :'cadContatoInstalacao',
			ptraoid : jQuery('input[name=ptraoid]').val(),
			prcnome_cont_inst :jQuery('input[name=prcnome_cont_assist]').val(),
			prcfone_res_cont_inst : jQuery('input[name=prcfone_res_cont_assist]').val(),
			prcfone_com_cont_inst : jQuery('input[name=prcfone_com_cont_assist]').val(),
			prcfone_cel_cont_inst : jQuery('input[name=prcfone_cel_cont_assist]').val(),
			prcid_nextel_cont_inst :jQuery('input[name=prcid_nextel_cont_assist]').val(),
		},
		beforeSend:function(){
			jQuery('#contatoAssistencia').html('<center><img src="images/loading.gif" alt="" /></center>');
		},
		success:function(data) {
			data = jQuery.parseJSON(data);
			 if(data.tipo_msg == 'i'){
			    	alert(data.msg);
			    }else if(data.tipo_msg == 'e'){
			        alert(data.msg);
			    }else{
			    	jQuery('input[name=prcnome_cont_assist]').val(''),
					jQuery('input[name=prcfone_res_cont_assist]').val(''),
					jQuery('input[name=prcfone_com_cont_assist]').val(''),
					jQuery('input[name=prcfone_cel_cont_assist]').val(''),
					jQuery('input[name=prcid_nextel_cont_assist]').val(''),
			    	jQuery('#contatoAssistencia').html('');
					jQuery('#adicionaContatoAssistencia').html('');
					jQuery('#adicionaContatoAssistencia').append(data.html);
			    }
		}
		
	});
}

jQuery.fn.reprovacaoAnaliseCredito = function(){
	
	
	//bt_add_cont_emerg
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data:{
			acao :'reprovaSerasaManual',
			idProposta:$("#idproposta").val(),
			motivo : $("#textareaReprova").val(),
		},
		beforeSend:function(){
			jQuery('#contatoAssistencia').html('<center><img src="images/loading.gif" alt="" /></center>');
		},
		success:function(data) {
			$("#reprovacao").parent().hide();
			
			var resultado = jQuery.parseJSON(data);
			if(resultado.status == "msgsucesso") {
				if(resultado.statusanalise == 3){
					alert(resultado.message);
					window.location.href = "fin_transferencia_titularidade.php";
				}else {
					$('#aprova_divida').attr("disabled", true);
					$('#reprova_divida').attr("disabled", true);
				}
				

			}else {
				$('#confirmar_solicitacao').attr("disabled", false);
				jQuery('#msgerro').attr("class", "mensagem erro").html(resultado.message).showMessage();
				$('#aprova_credito').attr("disabled", false);
				$('#reprova_credito').attr("disabled", false);
				$('#aprova_divida').attr("disabled", false);
				$('#reprova_divida').attr("disabled", false);
			}
		}
		
	});
	//bt_add_cont_emerg

}

jQuery.fn.reprovacaoTransferenciaTitularidade = function(){
	
	
	//bt_add_cont_emerg
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data:{
			acao :'reprovaTransferenciaTitularidade',
			idProposta:$("#idproposta").val(),
			motivo : $("#textareaReprova").val(),
		},
		success:function(data) {
			$("#reprovacao").parent().hide();
			var resultado = jQuery.parseJSON(data);
				if(resultado.status == "msgsucesso") {
				if(resultado.statustitularidade == 3){
					alert(resultado.message);
					window.location.href = "fin_transferencia_titularidade.php";
				}else {
					$('#aprova_divida').attr("disabled", true);
					$('#reprova_divida').attr("disabled", true);
				}
				

			}else {
				$('#confirmar_solicitacao').attr("disabled", false);
				jQuery('#msgerro').attr("class", "mensagem erro").html(resultado.message).showMessage();
				$('#aprova_credito').attr("disabled", false);
				$('#reprova_credito').attr("disabled", false);
				$('#aprova_divida').attr("disabled", false);
				$('#reprova_divida').attr("disabled", false);
			}
		}
		
	});
	//bt_add_cont_emerg

}

jQuery.fn.aprovacaoTransferenciaTitularidade = function(){
	
	
	//bt_add_cont_emerg
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data:{
			acao :'aprovaTransferenciaTitularidade',
			idProposta:$("#idproposta").val(),
		},
		success:function(data) {
			$("#reprovacao").parent().hide();
			var resultado = jQuery.parseJSON(data);
			if(resultado.status == "msgsucesso") {
				if(resultado.statustitularidade == 2 && resultado.statusanalise == 2 ){
					alert(resultado.message);
					window.location.href = "fin_transferencia_titularidade.php";
				}else {
					alert(resultado.message);
					$('#aprova_divida').attr("disabled", true);
					$('#reprova_divida').attr("disabled", true);

				}
			}else {
				$('#confirmar_solicitacao').attr("disabled", false);
				alert(resultado.message);
				$('#aprova_credito').attr("disabled", false);
				$('#reprova_credito').attr("disabled", false);
				$('#aprova_divida').attr("disabled", false);
				$('#reprova_divida').attr("disabled", false);
			}
		}
		
	});
	//bt_add_cont_emerg

}

jQuery.fn.aprovacaoAnaliseCredito = function(){
	
	
	//bt_add_cont_emerg
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data:{
			acao :'aprovaSerasaManual',
			idProposta:$("#idproposta").val(),
		},
		success:function(data) {
			$("#reprovacao").parent().hide();
			var resultado = jQuery.parseJSON(data);
		if(resultado.status == "msgsucesso") {
			console.log(resultado.statusanalise);
			console.log(resultado.statustitularidade);
				if(resultado.statusanalise == 2 && resultado.statustitularidade == 2){
					alert(resultado.message);
					window.location.href = "fin_transferencia_titularidade.php";
				}else {
					alert(resultado.message);
					$('#aprova_credito').attr("disabled", true);
					$('#reprova_credito').attr("disabled", true);
				}
				

			}else {
				$('#confirmar_solicitacao').attr("disabled", false);
				jQuery('#msgerro').attr("class", "mensagem erro").html(resultado.message).showMessage();
				$('#aprova_credito').attr("disabled", false);
				$('#reprova_credito').attr("disabled", false);
				$('#aprova_divida').attr("disabled", false);
				$('#reprova_divida').attr("disabled", false);
			}
		}
		
	});
	//bt_add_cont_emerg

}

jQuery.fn.anexarArquivos = function(){
	jQuery("#anexosProposta").hide();
	jQuery('#acao').val('anexarArquivos');
	//bt_add_cont_emerg
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
	    cache: false,
	    contentType: false,
	    processData: false,
	    type: 'POST',
		data: function() {
            var data = new FormData();
            data.append("descricao", jQuery("#arqAnexoReqDescricao").val());
            data.append("acao", jQuery("#acao").val());
            data.append("arquivo", jQuery("#arqAnexoReqs").get(0).files[0]);
            data.append("ptraoid" , jQuery('input[name=ptraoid]').val());
            data.append("idpropAnexo" , jQuery('input[name=idpropAnexo]').val());
            
            return data;
            // Or simply return new FormData(jQuery("form")[0]);
        }(),
    	beforeSend:function(){
			jQuery('#anexosProposta').html('<center><img src="images/loading.gif" alt="" /></center>');
		},
		success:function(data) {
			
		    data = jQuery.parseJSON(data);
		    if(data.tipo_msg == 'i'){
		    	alert(data.msg);
		    	jQuery('#anexosProposta').html('');
		    	jQuery('#listaAnexos').html('');
		    }else{
		    	 jQuery('#arqAnexoReqs').val('');
			    	jQuery("#arqAnexoReqDescricao").val('');
			    	 jQuery('#anexosProposta').html('');
					 jQuery('#listaAnexos').html('');
					 jQuery('#listaAnexos').append(data.html);
	
		    }
		   
		}
		
	});

}

jQuery.fn.anexoCarta = function(){

	//jQuery("#anexosProposta").hide();
	jQuery('#acao').val('anexarCarta');
	//bt_add_cont_emerg
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
	    cache: false,
	    contentType: false,
	    processData: false,
	    type: 'POST',
		data: function() {
            var data = new FormData();
            data.append("descricao", jQuery("#arqAnexoReqDescricaoCarta").val());
            data.append("acao", jQuery("#acao").val());
            data.append("arquivo", jQuery("#arqAnexoReqCarta").get(0).files[0]);
            data.append("ptraoid" , jQuery('input[name=ptraoid]').val());
            
            return data;
            // Or simply return new FormData(jQuery("form")[0]);
        }(),
    	beforeSend:function(){
			jQuery('#anexosCartas').html('<center><img src="images/loading.gif" alt="" /></center>');
		},
		success:function(data) {
		    data = jQuery.parseJSON(data);
		    if(data.tipo_msg == 'i'){
		    	alert(data.msg);
		    	jQuery('#anexosCartas').html('');
		    	jQuery('#listaCartas').html(data.html);
		    }else{
		    	jQuery('#arqAnexoReqDescricaoCarta').val('');
		    	jQuery("#arqAnexoReqCarta").val('');
				 jQuery('#listaCartas').html('');
				 jQuery('#anexosCartas').html('');
				 jQuery('#listaCartas').append(data.html);
		    }

		}
		
	});
}

jQuery.fn.excluirCarta = function(key){

	 if (confirm("Tem certeza que deseja excluir a carta?"))
	  {
		   
		    jQuery.ajax({
		        async: true,
		        url:   'fin_transferencia_titularidade.php',
		        type:  'post',
		       // data:  jQuery('#frm').serialize()+'&ajax=true&arquivo='+key,
		        data:{
					acao :'excluirCarta',
					id: jQuery("#idCarta").val(),
					arquivo:key,
				},
		        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
		        success: function(data) {
		            data = jQuery.parseJSON(data);
				    if(data.tipo_msg == 'i'){
				    	alert(data.msg);
				    	jQuery('#anexosCartas').html('');
				    	jQuery('#listaCartas').html('');
				    }
		        }
		        
		    });
	  }
}

jQuery.fn.excluirArquivo = function(key){
	
	 if (confirm("Tem certeza que deseja excluir o anexo?"))
	  {
		   
		    jQuery.ajax({
		        async: true,
		        url:   'fin_transferencia_titularidade.php',
		        type:  'post',
		       // data:  jQuery('#frm').serialize()+'&ajax=true&arquivo='+key,
		        data:{
					acao :'excluirArquivo',
					id: jQuery("#idAnexo").val(),
					idpropAnexo: jQuery("#idpropAnexo").val(),
					arquivo:key,
				},
		        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
		        success: function(data) {
		        	jQuery('#anexosProposta').html('');
					 jQuery('#listaAnexos').html('');
		            data = jQuery.parseJSON(data);
				    if(data.tipo_msg == 'i'){
				    	alert(data.msg);
				    	jQuery('#anexosProposta').html('');
				    	jQuery('#listaAnexos').html('');
				    }else{
				    	 jQuery('#anexosProposta').html('');
						 jQuery('#listaAnexos').html('');
						 jQuery('#listaAnexos').append(data.html);
				    }
		        }
		        
		    });
	  }
}

jQuery.fn.excluircontatoInstalacao = function(key){
	 if (confirm("Tem certeza que deseja excluir assistencia?"))
	  {
		   
		    jQuery.ajax({
		        async: true,
		        url:   'fin_transferencia_titularidade.php',
		        type:  'post',
		       // data:  jQuery('#frm').serialize()+'&ajax=true&arquivo='+key,
		        data:{
					acao :'excluircontatoInstalacao',
					idInstalAssis: jQuery("#idInstalAssis").val(),
					id:key,
				},
		        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
		        success: function(data) {
			    	jQuery('#contatoAssistencia').html('');
					 jQuery('#adicionaContatoAssistencia').html('');
		            data = jQuery.parseJSON(data);
				    if(data.tipo_msg == 'i'){
				    	alert(data.msg);
				    	jQuery('#contatoAssistencia').html('');
						 jQuery('#adicionaContatoAssistencia').html('');
				    }else{
				    	jQuery('#contatoAssistencia').html('');
						 jQuery('#adicionaContatoAssistencia').html('');
						 jQuery('#adicionaContatoAssistencia').append(data.html);
				    }
		        }
		        
		    });
	  }
}

jQuery.fn.editarinstAssistencia = function(key) {
	jQuery("#bt_add_cont_inst").hide();
	$('#bt_add_cont_inst').attr('disabled', 'disabled');
	$('#bt_atualizar_cont_inst').removeAttr('disabled');
	jQuery("#bt_atualizar_cont_inst").show();
	jQuery("#bt_cancelar_cont_inst").show();
	$('#bt_cancelar_cont_inst').removeAttr('disabled');
	
	 jQuery.ajax({
	        async: true,
	        url:   'fin_transferencia_titularidade.php',
	        type:  'post',
	       // data:  jQuery('#frm').serialize()+'&ajax=true&arquivo='+key,
	        data:{
				acao :'EdicaoContatoInstalacao',
				id:key,
				
			},
	        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
	        success: function(data) {
	        	 data = jQuery.parseJSON(data);
	        	jQuery('input[name=ptcioid_cont_assist]').val(data.ptcioid);
	        	jQuery('input[name=id_prop_InstalAssis]').val(data.ptciptraoid);
	        	jQuery('input[name=prcnome_cont_assist]').val(data.nome);
				jQuery('input[name=prcfone_res_cont_assist]').val(data.foneresidencial);
				jQuery('input[name=prcfone_com_cont_assist]').val(data.fonecomercial);
				jQuery('input[name=prcfone_cel_cont_assist]').val(data.fonecelular);
				jQuery('input[name=prcid_nextel_cont_assist]').val(data.nextel);
				
	        }
	        
	    });
	 
}

jQuery.fn.atualizarcontatoInstalacao = function(){

	//bt_add_cont_emerg
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data:{
			acao :'atualizaContatoInstalacao',
			ptraoid : jQuery('input[name=ptcioid_cont_assist]').val(),
			id_prop_InstalAssis  :jQuery('input[name=id_prop_InstalAssis]').val(),
			prcnome_cont_inst :jQuery('input[name=prcnome_cont_assist]').val(),
			prcfone_res_cont_inst : jQuery('input[name=prcfone_res_cont_assist]').val(),
			prcfone_com_cont_inst : jQuery('input[name=prcfone_com_cont_assist]').val(),
			prcfone_cel_cont_inst : jQuery('input[name=prcfone_cel_cont_assist]').val(),
			prcid_nextel_cont_inst :jQuery('input[name=prcid_nextel_cont_assist]').val(),
		},
		beforeSend:function(){
			jQuery('#contatoAssistencia').html('<center><img src="images/loading.gif" alt="" /></center>');
		},
		success:function(data) {
			data = jQuery.parseJSON(data);
			  if(data.tipo_msg == 'i'){
			    	alert(data.msg);
			    }else{
			      	jQuery("#bt_add_cont_inst").show();
			    	$('#bt_add_cont_inst').removeAttr('disabled');
			    	jQuery("#bt_atualizar_cont_inst").hide();
			    	jQuery("#bt_cancelar_cont_inst").hide();
			    	$('#bt_atualizar_cont_inst').attr('disabled', 'disabled');
			    	$('#bt_cancelar_cont_inst').attr('disabled', 'disabled');
			    	jQuery('input[name=ptcioid_cont_assist]').val('');
			    	jQuery('input[name=id_prop_InstalAssis]').val('');
			    	jQuery('input[name=prcnome_cont_assist]').val(''),
					jQuery('input[name=prcfone_res_cont_assist]').val('');
					jQuery('input[name=prcfone_com_cont_assist]').val('');
					jQuery('input[name=prcfone_cel_cont_assist]').val('');
					jQuery('input[name=prcid_nextel_cont_assist]').val('');
			    	jQuery('#contatoAssistencia').html('');
					jQuery('#adicionaContatoAssistencia').html('');
					jQuery('#adicionaContatoAssistencia').append(data.html);
			    }
			 
		}
		
	});
}

jQuery.fn.excluircontatoEmergencia = function(key){
	 if (confirm("Tem certeza que deseja excluir emergencia?"))
	  {
		   
		    jQuery.ajax({
		        async: true,
		        url:   'fin_transferencia_titularidade.php',
		        type:  'post',
		       // data:  jQuery('#frm').serialize()+'&ajax=true&arquivo='+key,
		        data:{
					acao :'excluircontatoEmergencia',
					idContEmerg : jQuery("#idContEmerg").val(),
					id:key,
				},
		        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
		        success: function(data) {
			    	jQuery('#contatoEmergencia').html('');
					 jQuery('#adicionaContatoEmergencia').html('');
		            data = jQuery.parseJSON(data);
				    if(data.tipo_msg == 'i'){
				    	alert(data.msg);
				    	jQuery('#contatoEmergencia').html('');
						 jQuery('#adicionaContatoEmergencia').html('');
				    }else{
				    	jQuery('#contatoEmergencia').html('');
						 jQuery('#adicionaContatoEmergencia').html('');
						 jQuery('#adicionaContatoEmergencia').append(data.html);
				    }
		        }
		        
		    });
	  }
}

jQuery.fn.excluircontatoPessoaAutorizada = function(key){
	 if (confirm("Tem certeza que deseja excluir pessoa autorizada?"))
	  {
		   
		    jQuery.ajax({
		        async: true,
		        url:   'fin_transferencia_titularidade.php',
		        type:  'post',
		       // data:  jQuery('#frm').serialize()+'&ajax=true&arquivo='+key,
		        data:{
					acao :'excluircontatoPessoaAutorizada',
					idContPessoaAut : jQuery("#idContPessoaAut").val(),
					id:key,
				},
		        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
		        success: function(data) {
			    	jQuery('#autorizaPessoa').html('');
					 jQuery('#adicionaAutorizaPessoa').html('');
		            data = jQuery.parseJSON(data);
				    if(data.tipo_msg == 'i'){
				    	alert(data.msg);
				      	jQuery('#autorizaPessoa').html('');
						 jQuery('#adicionaAutorizaPessoa').html('');
				    }else{
				      	jQuery('#autorizaPessoa').html('');
						 jQuery('#adicionaAutorizaPessoa').html('');
						 jQuery('#adicionaAutorizaPessoa').append(data.html);
				    }
		        }
		        
		    });
	  }
}


jQuery.fn.editarPessoautorizada = function(key) {

	jQuery("#bt_add_pessoas_auto").hide();
	$('#bt_add_pessoas_auto').attr('disabled', 'disabled');
	jQuery("#bt_atualizar_pessoas_auto").show();
	jQuery("#bt_cancelar_pessoas_auto").show();
	$('#bt_atualizar_pessoas_auto').removeAttr('disabled');
	$('#bt_cancelar_pessoas_auto').removeAttr('disabled');

	 jQuery.ajax({
	        async: true,
	        url:   'fin_transferencia_titularidade.php',
	        type:  'post',
	       // data:  jQuery('#frm').serialize()+'&ajax=true&arquivo='+key,
	        data:{
				acao :'EdicaoPessoaAutorizada',
				id:key,
			},
	        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
	        success: function(data) {
	            data = jQuery.parseJSON(data);
	        	jQuery('input[name=ptpaoid_pessoa_aut]').val(data.ptpaoid);
	        	jQuery('input[name=id_prop_pessoaAut]').val(data.ptpaptraoid);
	        	jQuery('input[name=prtcnome_aut]').val(data.nome);
	        	jQuery('input[name=prtcpf_aut]').val(data.cpf);
	        	jQuery('input[name=prtrg_aut]').val(data.rg);
				jQuery('input[name=prtfone_res_aut]').val(data.foneresidencial);
				jQuery('input[name=prtfone_com_aut]').val(data.fonecelular);
				jQuery('input[name=prtfone_cel_aut]').val(data.fonecomercial);
				jQuery('input[name=prtid_nextel_aut]').val(data.nextel);
			
				
	        }
	        
	    });
	 
}

jQuery.fn.atualizarpessoasAutorizadas = function(){
	//bt_add_cont_emerg
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data:{
			acao :'atualizaPessoaAutorizada',
			ptpaoid : jQuery('input[name=ptpaoid_pessoa_aut]').val(),
			ptpaptraoid  :jQuery('input[name=id_prop_pessoaAut]').val(),
			ptpanome : jQuery('input[name=prtcnome_aut]').val(),
			ptpacpf : jQuery('input[name=prtcpf_aut]').val(),
			ptparg : jQuery('input[name=prtrg_aut]').val(),
			ptpafone_res_pessoa_auto : jQuery('input[name=prtfone_res_aut]').val(),
			ptpafone_com_pessoa_auto : jQuery('input[name=prtfone_com_aut]').val(),
			ptpafone_cel_pessoa_auto : jQuery('input[name=prtfone_cel_aut]').val(),
			ptpaid_nextel_pessoa_auto :jQuery('input[name=prtid_nextel_aut]').val(),
		},
		beforeSend:function(){
			jQuery('#autorizaPessoa').html('<center><img src="images/loading.gif" alt="" /></center>');
		},
		success:function(data) {
			data = jQuery.parseJSON(data);

			  if(data.tipo_msg == 'i'){
			    	alert(data.msg);
			    }else{
			      	jQuery("#bt_add_pessoas_auto").show();
			    	$('#bt_add_pessoas_auto').removeAttr('disabled');
			    	jQuery("#bt_atualizar_pessoas_auto").hide();
			    	jQuery("#bt_cancelar_pessoas_auto").hide();
			    	$('#bt_atualizar_pessoas_auto').attr('disabled', 'disabled');
			    	$('#bt_cancelar_pessoas_auto').attr('disabled', 'disabled');
			    	jQuery('input[name=ptpaoid_pessoa_aut]').val('');
			    	jQuery('input[name=id_prop_pessoaAut]').val('');
			    	jQuery('input[name=prtcnome_aut]').val(''),
			    	jQuery('input[name=prtcpf_aut]').val('');
			    	jQuery('input[name=prtrg_aut]').val('');
					jQuery('input[name=prtfone_res_aut]').val('');
					jQuery('input[name=prtfone_com_aut]').val('');
					jQuery('input[name=prtfone_cel_aut]').val('');
					jQuery('input[name=prtid_nextel_aut]').val('');
			    	jQuery('#autorizaPessoa').html('');
					jQuery('#adicionaAutorizaPessoa').html('');
					jQuery('#adicionaAutorizaPessoa').append(data.html);
		
			    }
			 
		}
		
	});
}

jQuery.fn.editarEmergencia = function(key) {

	jQuery("#bt_add_cont_emerg").hide();
	$('#bt_add_cont_emerg').attr('disabled', 'disabled');
	jQuery("#bt_atualizar_cont_emerg").show();
	jQuery("#bt_cancelar_cont_emerg").show();
	$('#bt_atualizar_cont_emerg').removeAttr('disabled');
	$('#bt_cancelar_cont_emerg').removeAttr('disabled');

	 jQuery.ajax({
	        async: true,
	        url:   'fin_transferencia_titularidade.php',
	        type:  'post',
	       // data:  jQuery('#frm').serialize()+'&ajax=true&arquivo='+key,
	        data:{
				acao :'EdicaoEmergencia',
				id:key,
			},
	        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
	        success: function(data) {
	        	 data = jQuery.parseJSON(data);
	        	jQuery('input[name=ptceoid_cont_emerg]').val(data.ptceoid);
	        	jQuery('input[name=id_prop_contEmerg]').val(data.ptceptraoid);
	        	jQuery('input[name=prcnome_cont_emerg]').val(data.nome);
				jQuery('input[name=prcfone_res_cont_emerg]').val(data.foneresidencial);
				jQuery('input[name=prcfone_com_cont_emerg]').val(data.fonecelular);
				jQuery('input[name=prcfone_cel_cont_emerg]').val(data.fonecomercial);
				jQuery('input[name=prcid_nextel_cont_emerg]').val(data.nextel);
			
				
	        }
	        
	    });
	 
}



jQuery.fn.formaPagamentos = function(key){

	//bt_add_cont_emerg
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data:{
			acao :'formaPagamentoID',
			id : key,
		},
		beforeSend:function(){
			jQuery('#formasPagamento').html('<center><img src="images/progress4.gif" alt="" /></center>');
		},
		success:function(data) {
			data = jQuery.parseJSON(data);

			if(data.codigo == 1 && data.msg == 'Debito') {
				jQuery('#tipoPagamentoAtual').val('Debito');
				jQuery('#formasPagamento').html('');
				jQuery("#cartaoDebito").show();
				jQuery("#dataCartao").val('');
				jQuery("#nCartao").val('');
				jQuery("#idBanco").val(data.codigobanco);
				jQuery("#nomeBanco").val(data.nomebanco);
				jQuery("#nAgencia").val('');
				jQuery("#nConta").val('');
				jQuery("#cartaoCredito").hide();
			}else if(data.codigo == 2 && data.msg == 'Credito'){
				jQuery('#tipoPagamentoAtual').val('Credito');
				jQuery('#formasPagamento').html('');
				jQuery("#cartaoDebito").hide();
				jQuery("#idBanco").val('');
				jQuery("#nomeBanco").val('');
				jQuery("#cartaoCredito").show();
				jQuery("#dataCartao").val('');
				jQuery("#nCartao").val('');
				jQuery("#nAgencia").val('');
				jQuery("#nConta").val('');
				
				
			}else{
				jQuery('#tipoPagamentoAtual').val('');
				jQuery('#formasPagamento').html('');
				jQuery("#cartaoCredito").hide();
				jQuery("#cartaoDebito").hide();
				jQuery("#idBanco").val('');
				jQuery("#nomeBanco").val('');
				jQuery("#dataCartao").val('');
				jQuery("#nCartao").val('');
				jQuery("#nAgencia").val('');
				jQuery("#nConta").val('');
			}
			
			 
		}
		
	});
}

jQuery.fn.atualizarcontatoEmergencia = function(){

	//bt_add_cont_emerg
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data:{
			acao :'atualizaContatoEmergencia',
			ptceoid : jQuery('input[name=ptceoid_cont_emerg]').val(),
			id_prop_contEmerg  :jQuery('input[name=id_prop_contEmerg]').val(),
			prcnome_cont_emerg :jQuery('input[name=prcnome_cont_emerg]').val(),
			prcfone_res_cont_emerg : jQuery('input[name=prcfone_res_cont_emerg]').val(),
			prcfone_com_cont_emerg : jQuery('input[name=prcfone_com_cont_emerg]').val(),
			prcfone_cel_cont_emerg : jQuery('input[name=prcfone_cel_cont_emerg]').val(),
			prcid_nextel_cont_emerg :jQuery('input[name=prcid_nextel_cont_emerg]').val(),
		},
		beforeSend:function(){
			jQuery('#contatoEmergencia').html('<center><img src="images/loading.gif" alt="" /></center>');
		},
		success:function(data) {
			data = jQuery.parseJSON(data);

			  if(data.tipo_msg == 'i'){
			    	alert(data.msg);
			    }else{
			      	jQuery("#bt_add_cont_emerg").show();
			    	$('#bt_add_cont_emerg').removeAttr('disabled');
			    	jQuery("#bt_atualizar_cont_emerg").hide();
			    	jQuery("#bt_cancelar_cont_emerg").hide();
			    	$('#bt_atualizar_cont_emerg').attr('disabled', 'disabled');
			    	$('#bt_cancelar_cont_emerg').attr('disabled', 'disabled');
			    	jQuery('input[name=ptceoid_cont_emerg]').val('');
			    	jQuery('input[name=id_prop_contEmerg]').val('');
			    	jQuery('input[name=prcnome_cont_emerg]').val(''),
					jQuery('input[name=prcfone_res_cont_emerg]').val('');
					jQuery('input[name=prcfone_com_cont_emerg]').val('');
					jQuery('input[name=prcfone_cel_cont_emerg]').val('');
					jQuery('input[name=prcid_nextel_cont_emerg]').val('');
			    	jQuery('#contatoEmergencia').html('');
					jQuery('#adicionaContatoEmergencia').html('');
					jQuery('#adicionaContatoEmergencia').append(data.html);
		
			    }
			 
		}
		
	});
}

jQuery.fn.salvapropostatitularidade = function(){
	jQuery('#acao').val('salvaPropostaTransferencia');

	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data: jQuery('#frm_novo_titular').serialize(),
		success:function(data) {
			data = jQuery.parseJSON(data);
			  if(data.tipo_msg == 'i'){
			    	alert(data.msg);
			    }else if(data.tipo_msg == 'e'){
			        alert(data.msg);
			    }
			 
		}
		
	});
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


jQuery.fn.buscaEnderecoCep = function(cep)
{
	
	$("#bairrosSelect").html("");
   	$("#bairrosSelect").hide();
	jQuery('#prpend_bairro').show();
	$("#cidadesSelect").html("");
	$("#cidadesSelect").hide();
	jQuery('#cidadesInput').show();
	jQuery("#cad_manual").hide();
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data:{
			acao :'retornaEnderecosCEP',
			cep : cep,
		},
		beforeSend:function(){
			jQuery('#imgCEP_endereco').html('<img src="images/progress4.gif" alt="" />');
		},
		success:function(data) {
			data = jQuery.parseJSON(data);
			jQuery('#imgCEP_endereco').html('');
			 if(data.sucesso == "i") {
			      $('#prpend_log').val(data.Logradouro);
                  $('#prpend_bairro').val(data.bairro);
                  $('#prpend_cid').val(data.cidade);
                  $('#prpend_est > option').each(function(){
                	  if($(this).text()==data.estado) $(this).parent('select').val($(this).val())
                	  })
                  $('#prpend_num').focus();
			 }else{
				 jQuery('#imgCEP_endereco').html('Nenhum resultado encontrado para este CEP');
			 }
		}
		
	});

};

jQuery.fn.buscaEnderecoCepCobranca = function(cep)
{
	$("#bairrosSelectCobr").html("");
   	$("#bairrosSelectCobr").hide();
	jQuery('#prpendcob_bairro').show();
	$("#cidadesCobrSelect").html("");
	$("#cidadesCobrSelect").hide();
	jQuery('#cidadesCobrInput').show();
	jQuery("#cad_manual_end_cob").hide();
	
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data:{
			acao :'retornaEnderecosCEP',
			cep : cep,
		},
		success:function(data) {
			data = jQuery.parseJSON(data);
			 if(data.sucesso == "i") {
			      $('#prpendcob_log').val(data.Logradouro);
                  $('#prpendcob_bairro').val(data.bairro);
                  $('#prpendcob_cid').val(data.cidade);
                  $('#prpendcob_est > option').each(function(){
                	  if($(this).text()==data.estado) $(this).parent('select').val($(this).val())
                	  })
                  $('#prpendcob_num').focus();
			 }else{
				 jQuery('#imgCEP_enderecoCob').html('Nenhum resultado encontrado para este CEP');
			 }
		}
		
	});

};

jQuery.fn.buscaCidadesIdUF = function(estado)
{
	jQuery('#cidadesInput').hide();
	jQuery('#prpend_cid').val('');
	jQuery('#prpend_bairro').val('');

	
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data:{
			acao :'listCidadesSiglaEstadoEndereco',
			sigla : estado,
		},
		success:function(data) {
		  data = jQuery.parseJSON(data);
		
		  if(data.tipo_msg == 'i'){
			  jQuery('#cidadesInput').show();
			  $("#cidadesSelect").html("");
			  $("#cidadesSelect").hide();
			  alert(data.msg);
		    }else if(data.tipo_msg == 'e'){
		    	 jQuery('#cidadesInput').show();
				  $("#cidadesSelect").html("");
				  $("#cidadesSelect").hide();
				  alert(data.msg);
		    }else{
		    	$("#cidadesSelect").show();
		    	 $("#cidadesSelect").html("");
		         $("#cidadesSelect").html(data.html);
		    }
		}
		
	});


};

jQuery.fn.buscaBairrosIdCidade = function(sigla,cidade){
	jQuery('#prpend_bairro').hide();
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data:{
			acao :'listaBairros',
			siglaEstado : sigla,
			cidade : cidade,
		},
		success:function(data) {
		  data = jQuery.parseJSON(data);

		  if(data.tipo_msg == 'i'){

			  jQuery('#prpend_bairro').show();
			  $("#bairrosSelect").html("");
			  $("#bairrosSelect").hide();
			  alert(data.msg);
		    }else if(data.tipo_msg == 'e'){
		    	jQuery('#prpend_bairro').show();
				  $("#bairrosSelect").html("");
				  $("#bairrosSelect").hide();
				  alert(data.msg);
		    }else{
		    	$("#bairrosSelect").show();
		    	$("#bairrosSelect").html("");
		        $("#bairrosSelect").html(data.html);
		        jQuery("#cad_manual").show();
		    }
		}
		
	});

};



jQuery.fn.buscaCidadesIdUFEnderecoCobranca = function(estado)
{
	jQuery('#cidadesCobrInput').hide();
	jQuery('#prpendcob_cid').val('');
	jQuery('#prpendcob_bairro').val('');

	
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data:{
			acao :'listCidadesSiglaEstadoEnderecoCobranca',
			sigla : estado,
		},
		success:function(data) {
		  data = jQuery.parseJSON(data);
		
		  if(data.tipo_msg == 'i'){
			  jQuery('#cidadesCobrInput').show();
			  $("#cidadesCobrSelect").html("");
			  $("#cidadesCobrSelect").hide();
			  alert(data.msg);
		    }else if(data.tipo_msg == 'e'){
		    	 jQuery('#cidadesCobrInput').show();
				  $("#cidadesCobrSelect").html("");
				  $("#cidadesCobrSelect").hide();
				  alert(data.msg);
		    }else{
		    	$("#cidadesCobrSelect").show();
		    	 $("#cidadesCobrSelect").html("");
		         $("#cidadesCobrSelect").html(data.html);
		    }
		}
		
	});


};


jQuery.fn.buscaBairrosIdCidadeEndCobranca = function(sigla,cidade){
	jQuery('#prpendcob_bairro').hide();
	jQuery.ajax({
		url: 'fin_transferencia_titularidade.php',
		type: 'post',
		data:{
			acao :'listaBairrosEnderecoCobranca',
			siglaEstado : sigla,
			cidade : cidade,
		},
		success:function(data) {
		  data = jQuery.parseJSON(data);

		  if(data.tipo_msg == 'i'){

			  jQuery('#prpendcob_bairro').show();
			  $("#bairrosSelectCobr").html("");
			  $("#bairrosSelectCobr").hide();
			  alert(data.msg);
		    }else if(data.tipo_msg == 'e'){
		    	jQuery('#prpendcob_bairro').show();
				  $("#bairrosSelectCobr").html("");
				  $("#bairrosSelectCobr").hide();
				  alert(data.msg);
		    }else{
		    	$("#bairrosSelectCobr").show();
		    	$("#bairrosSelectCobr").html("");
		        $("#bairrosSelectCobr").html(data.html);
		        jQuery("#cad_manual_end_cob").show();
		    }
		}
		
	});

};

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

jQuery.fn.validaDataNasc = function(nasc){
	
	var array_data = nasc.split("/");
	var anoNasc = parseInt(array_data[2]); 
	var data = new Date();
  

	  var nasc_dia = parseInt(array_data[0]); 
	  var nasc_mes = parseInt(array_data[1]); 
	  var nasc_ano = parseInt(array_data[2]); 
	  
	 
	  var dia = data.getDate();
	  var mes = data.getMonth()+1;
	  var  ano = data.getFullYear();

	  var idade = ano - nasc_ano;

	  if(ano <= nasc_ano) {
		  return 0;
	  }
	  
	  if(dia >= nasc_dia && mes >= nasc_mes){
	    return idade;
	  }else{
		  idade = idade - 1;
	     return idade;
	   }
}







