/**
 * Preparando funcionalidades da tela.
 * 
 */
jQuery(document).ready(function() {
	
	$("#resultado").hide();
	
	jQuery("body").delegate('#bt_pesquisar','click',function(){
		
		   var dt_ini = jQuery("#rel_dt_ini").val();
		   var dt_fim = jQuery("#rel_dt_fim").val();

		   var data_ini = dt_ini.substring(6,10)+""+dt_ini.substring(3,5)+""+dt_ini.substring(0,2);
		    var data_fim  = dt_fim.substring(6,10) +""+dt_fim.substring(3,5) +""+dt_fim.substring(0,2);
			
		    var data = new Date();
		    var dia = (data.getDate() < 10 ? "0"+data.getDate() : data.getDate() );
		    var mes = (data.getMonth() < 9 ? "0"+(data.getMonth()+1) : data.getMonth()+1 );
		    var ano = data.getFullYear();
		    var dtatual = ano+""+mes+""+dia;
		    
		if(jQuery('#tipo_relatorio').val() == '' || jQuery('#rel_dt_ini').val() == '' || jQuery('#rel_dt_fim').val() == '') {
			$('#rel_dt_ini').removeClass("erro");
	    	 $('#rel_dt_fim').removeClass("erro");
	    	 $('#tipo_relatorio').removeClass("erro");
			if(jQuery('#tipo_relatorio').val() == '') {
		        $('#tipo_relatorio').addClass('erro');
			}
		    
			if(jQuery('#rel_dt_ini').val() == '') {
			        $('#rel_dt_ini').addClass('erro');
			}
			
			if(jQuery('#rel_dt_fim').val() == '') {
		        $('#rel_dt_fim').addClass('erro');
			}
			$("#msgalerta").html("Existem campos obrigatorios nao preenchidos..").showMessage();
	        return false;
		}else if ( (parseInt(data_ini, 10) > parseInt(data_fim, 10)) ) {
	        jQuery('#msgalerta').html('Data de inicio não pode ser maior que a data de fim.').showMessage();
	        $('#rel_dt_ini').addClass('erro');
	        $('#rel_dt_fim').addClass('erro');
	        return false;
	    }else {
	    	 $('#rel_dt_ini').removeClass("erro");
	    	 $('#rel_dt_fim').removeClass("erro");
	    	 $('#tipo_relatorio').removeClass("erro");
	    	$("#msgalerta").html("Existem campos obrigatorios nao preenchidos..").hide();
	    	jQuery.fn.rel();
	    }
		
	});
	
    jQuery("body").delegate('#bt_visualizar','click', function(){
    	jQuery.fn.showArquivos();
    });
    
    jQuery("body").delegate('#btn_excluir','click', function(){
    	jQuery.fn.excluir(jQuery(this).attr('rel'));
    });
});

jQuery.fn.rel = function() {
	


	$("#acao").val('verificaAcao');
	
	$("#frm").submit(function(){

		
	   jQuery.ajax({
	        url: 'rel_base_informacoes_faturamento.php',
	        type: 'POST',
	        data: jQuery('#frm').serialize()+'&ajax=true',
	        dataType: "json",
	        success: function (data) {
	        	if(data.tipo == "erro") {
        			jQuery('#msgerro').html(data.msg).showMessage();
	        		$('#rel_dt_ini').addClass('erro');
	     	        $('#rel_dt_fim').addClass('erro');
	        	}else {
	        		$("#div_msg").html(data.msg);
	        	 	$("input[type=submit]").attr("disabled", "disabled");
	       		 	$("#bt_visualizar").attr("disabled", "disabled");
	        	}
	        	
       	
	        },
	        error: function(){
	        	$("#div_msg").html("Problemas ao gerar relatório.");
	        }
	    });
	    
		return false;
	});
	
}

jQuery.fn.showArquivos = function(){
	$("#resultado").show();
}

jQuery.fn.excluir = function(key){
	
	 if (confirm("Tem certeza que deseja excluir o arquivo?"))
	  {
		    jQuery('#acao').val('excluir');
		    jQuery.ajax({
		        async: true,
		        url:   'rel_base_informacoes_faturamento.php',
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