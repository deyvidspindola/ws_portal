jQuery(document).ready(function(){
	$("#resultado").hide();
	var objUtil = new Util();
	objUtil.toggleChecked($("#checked_all"), $(".toggle_checkbox"));
	
	var finGeraNfBoletoGrafica = new FinGeraNfBoletoGrafica();
	finGeraNfBoletoGrafica.mensagens();
	finGeraNfBoletoGrafica.mascaras();
	finGeraNfBoletoGrafica.pesquisar();
	finGeraNfBoletoGrafica.gerar();
	finGeraNfBoletoGrafica.voltar();
	//finGeraNfBoletoGrafica.excluir();
	
    jQuery("body").delegate('#btn_excluir','click', function(){
    	jQuery.fn.excluir(jQuery(this).attr('rel'));
    });
    
    
    jQuery("body").delegate('#ftp','click', function(){
    	jQuery.fn.ftp(jQuery(this).attr('rel'));
    });

    jQuery("body").delegate('#ftp','click', function(){
    	jQuery.fn.ftp(jQuery(this).attr('rel'));
    });
    
    jQuery("body").delegate('#btn_visualizar','click', function(){
    	jQuery.fn.showArquivos();
    });
	$("#frm_data").focus();
	
});

function FinGeraNfBoletoGrafica() {
	
	this.mensagens = function() {
		
		var msg = $("#div_msg_alidacao").html();
		
		if (msg != '') {
			alert(msg);
		}
	};
	
	this.mascaras = function() {
		
		$("#frm_data").setMask('99/99/9999');
		$("#frm_contrato").setMask('9999999999');
		$("#frm_placa").setMask('*******');
		
		var finGeraNfBoletoGrafica = new FinGeraNfBoletoGrafica();
		finGeraNfBoletoGrafica.alterarMascaraDoc();
		
	    $('.frm_tipo').click(function(){
	    	
	    	$("#frm_doc").val('');
	    	
			finGeraNfBoletoGrafica.alterarMascaraDoc();
	    	
	    	$("#frm_doc").focus();
	    });
	    
	    $('#frm_contrato').blur(function(){
			removeAlerta();
			
	    	var frm_contrato = parseInt($(this).val());
	    	
	    	if (frm_contrato > MAX_CONNUMERO) {
	    		
				$(this).css('background', '#FFFFC0');
				criaAlerta(MSG_VALIDATE_MAX_CONNUMERO + MAX_CONNUMERO);
				
	    		$(this).val('');
	    		$(this).focus();
	    	}
	    	else if (frm_contrato <= 0) {
	    		
				$(this).css('background', '#FFFFC0');
				criaAlerta(MSG_VALIDATE_MIN_CONNUMERO);
				
	    		$(this).val('');
	    		$(this).focus();
	    	}
	    	else {
				$(this).css('background', '#FFFFFF');
				removeAlerta();
	    	}
	    });
	};
	
	this.alterarMascaraDoc = function() {
		
    	var frm_tipo = $("input[name=frm_tipo]:checked").val();
    	
    	switch (frm_tipo) {
			case 'F':
				$("#frm_doc").setMask(MASK_CPF);
			break;
			case 'J':
			default:
				$("#frm_doc").setMask(MASK_CNPJ);
			break;
		}
	};
	

	
	this.gerar = function() {
		$("#btn_gerar").click(function(){
			
			/*if ($(".toggle_checkbox:checked").length == 0) {
				alert(MSG_NENHUMA_NF_SELECIONADA);
				
				return false;
			}*/
			
			$("#acao").val('prepararArquivo');

			return confirm(MSG_CONFIRMA_GERACAO);
		});
		
		$("#frm").submit(function(){
			
			removeAlerta();

			if ($("#frm_data").val() == '') {
				
				$("#frm_data").css('background', '#FFFFC0');
				
				criaAlerta(MSG_VALIDATE_REFERENCIA);
				
				return false;
			}

		    jQuery.ajax({
		        url: 'fin_gera_nf_boleto_grafica.php',
		        type: 'POST',
		        data: jQuery('#frm').serialize()+'&ajax=true',
		        success: function (data) {
		        		$("#div_msg").html(data);
		        		 $("input[type=submit]").attr("disabled", "disabled");
		        		 $("#btn_visualizar").attr("disabled", "disabled");
		        		 
		        		
		        }
		    });
			
			return false;
		});
	};
	
	this.pesquisar = function() {

		$("#btn_pesquisar").click(function(){
			$("#acao").val('prepararPrevia');
		});
		
		$("#frm").submit(function(){
			
			removeAlerta();

			if ($("#frm_data").val() == '') {
				
				$("#frm_data").css('background', '#FFFFC0');
				
				criaAlerta(MSG_VALIDATE_REFERENCIA);
				
				return false;
			}

		    jQuery.ajax({
		        url: 'fin_gera_nf_boleto_grafica.php',
		        type: 'POST',
		        data: jQuery('#frm').serialize()+'&ajax=true',
		        success: function (data) {
		        		$("#div_msg").html(data);
		        		 $("input[type=submit]").attr("disabled", "disabled");
		        		 $("#btn_visualizar").attr("disabled", "disabled");
		        }
		    });
			
			return false;
		});
	};
	
	
	this.voltar = function() {
		$("#btn_voltar").click(function(){
			document.location.href = 'principal.php';
		});
	};
	
	
	jQuery.fn.excluir = function(key){
		
		 if (confirm("Tem certeza que deseja excluir o arquivo?"))
		  {
			    jQuery('#acao').val('excluir');
			    jQuery.ajax({
			        async: true,
			        url:   'fin_gera_nf_boleto_grafica.php',
			        type:  'post',
			        data:  jQuery('#frm').serialize()+'&ajax=true&arquivo='+key,
			        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
			        success: function(data) {
			        	alert(data);
			        	location.reload();
			        }
			        
			    });
		  }
	}
	
	jQuery.fn.ftp = function(key){
	    jQuery('#acao').val('prepararArquivoFTP');
	    jQuery.ajax({
	        async: true,
	        url:   'fin_gera_nf_boleto_grafica.php',
	        type:  'post',
	        data:  jQuery('#frm').serialize()+'&ajax=true&arquivo='+key,
	        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
	        success: function(data) {
        		 location.reload();
	        }
	        
	    });
	    
	    return false;
	}
	
	jQuery.fn.showArquivos = function(){
		removeAlerta();
    	$("#resultado").show();
	}

}
