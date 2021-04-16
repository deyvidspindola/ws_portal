jQuery(document).ready(function(){ 
    jQuery("body").delegate('#pesquisar','click',function(){
     
        
        function daydiff(first, second) {
            return Math.round((second-first)/(1000*60*60*24));
        }
        // transformação de data para mm/dd/YYYY para comparações
        var dt_ini = jQuery("#pesquisa_data_inicio").val();
        var dtInicialArr = dt_ini.split("/");
        var dtInicial = new Date(dtInicialArr[2], dtInicialArr[1]-1, dtInicialArr[0]);
        
        var dt_fim = jQuery("#pesquisa_data_fim").val();
        var dtFinalArr = dt_fim.split("/");
        var dtFinal = new Date(dtFinalArr[2], dtFinalArr[1]-1, dtFinalArr[0]);
        
     
        if(jQuery('#pesquisa_data_inicio').val()=== '' || jQuery('#pesquisa_data_fim').val()=== '') {
           jQuery('#frame_load').html('');
           jQuery("#msgalerta").html("Existem campos obrigatórios (<font color='#FF0000'>*</font></style>) não preenchidos.").showMessage();
        }else if(dtInicial>dtFinal){
            
            jQuery('#frame_load').html('');
            jQuery("#msgalerta").html("Data de início não pode ser maior que a data final.").showMessage();
            
        }else if(daydiff(dtInicial,dtFinal)>35){
                jQuery('#frame_load').html('');
                jQuery("#msgalerta").html("Período máximo de pesquisa é de 35 dias").showMessage();
        }else if(jQuery('#pesquisa_numero_pedido').val() && jQuery('#pesquisa_numero_pedido').val() < 1100000){
        
            jQuery('#frame_load').html('');
            jQuery("#msgalerta").html("O número do pedido deve ser acima de 11.000.00.").showMessage();
        }else {
            
            jQuery('#resultado_pesquisa').fadeOut();
            jQuery.fn.pesquisar_relatorio();
            //jQuery('#frame_load').html('');
            jQuery('#msgalerta').html('').hide();
	}
        
    });
    
});

jQuery.fn.pesquisar_relatorio = function() {
    
    jQuery.ajax({
        
        url: 'fin_controle_fiscal_remessas.php',
        type: 'post',
        data: {
            acao: 'equipamentoMovelPesquisar',
            controle_movel_aba          : jQuery('#controle_movel_aba').val(),
            pesquisa_data_inicio        : jQuery('#pesquisa_data_inicio').val(),
            pesquisa_data_fim           : jQuery('#pesquisa_data_fim').val(),
            pesquisa_nf_remessa         : jQuery('#pesquisa_nf_remessa').val(),
            pesquisa_id_cliente         : jQuery('#pesquisa_id_cliente').val(),
            pesquisa_tipo_relatorio     : jQuery('#pesquisa_tipo_relatorio').val(),
            pesquisa_contrato           : jQuery('#pesquisa_contrato').val(),
            pesquisa_possui_nf_remessa  : jQuery('#pesquisa_possui_nf_remessa').val(),
            pesquisa_n_serie            : jQuery('#pesquisa_n_serie').val(),
            pesquisa_numero_pedido      : jQuery('#pesquisa_numero_pedido').val()
	},
	contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
        beforeSend: function(){		
            jQuery('#frame_load').html('<center><img src="images/loading.gif" alt="" /></center>');
            jQuery('#bt_pesquisar').attr('disabled', 'disabled');
	},
        success: function(data){
            jQuery('#frame_load').html('');
            console.log(data);
            
            try{	
                var resultado = jQuery.parseJSON(data);
                
            }catch(e){
                
                try{	
                    jQuery('#resultado_pesquisa').html(data).hide().showMessage();						 				
                }catch(e){			
                    jQuery('#msgerro').attr("class", "mensagem erro").html('Erro no processamento dos dados.').showMessage();
                }
            }
            
	}
    });	
};