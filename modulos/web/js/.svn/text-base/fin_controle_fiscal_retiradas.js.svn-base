jQuery(document).ready(function(){ 
    jQuery("body").delegate('#pesquisar','click',function(){
     
        jQuery('#frame_load').html('<center><img src="images/loading.gif" alt="" /></center>');
     
        if(jQuery('#pesquisa_data_inicio').val()    === '' || 
           jQuery('#pesquisa_data_fim').val()       === '' ||
           jQuery('#pesquisa_tipo_relatorio').val() === '' ) {
       
           jQuery('#frame_load').html('');
		
           jQuery("#msgalerta").html("Existem campos obrigatórios (<font color='#FF0000'>*</font>) não preenchidos.").showMessage();
        }else if(jQuery('#pesquisa_data_inicio').val() > jQuery('#pesquisa_data_fim').val()){
            
            jQuery('#frame_load').html('');
            
            jQuery("#msgalerta").html("Data de início não pode ser maior que a data final.").showMessage();
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
            acao: 'retiradasPesquisar',
            controle_movel_aba                      : jQuery('#controle_movel_aba').val(),            
            pesquisa_data_inicio                    : jQuery('#pesquisa_data_inicio').val(),
            pesquisa_data_fim                       : jQuery('#pesquisa_data_fim').val(),            
            pesquisa_nf_remessa_simbolico           : jQuery('#pesquisa_nf_remessa_simbolico').val(),
            pesquisa_nf_retorno_simbolico           : jQuery('#pesquisa_nf_retorno_simbolico').val(),
            pesquisa_id_cliente                     : jQuery('#pesquisa_id_cliente').val(),
            pesquisa_tipo_relatorio                 : jQuery('#pesquisa_tipo_relatorio').val(),
            pesquisa_contrato                       : jQuery('#pesquisa_contrato').val(),
            pesquisa_possui_nf_retorno_simbolico    : jQuery('#pesquisa_possui_nf_retorno_simbolico').val(),
            pesquisa_possui_nf_remessa_simbolico    : jQuery('#pesquisa_possui_nf_remessa_simbolico').val(),
            pesquisa_n_serie                        : jQuery('#pesquisa_n_serie').val(),
            representante                           : jQuery('#representante').val(),
            cliente                                 : jQuery('#pesquisa_id_cliente').val()
	},
	contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
        //beforeSend: function(){		
        //    jQuery('#frame_load').html('<center><img src="images/loading.gif" alt="" /></center>');
        //    jQuery('#bt_pesquisar').attr('disabled', 'disabled');
	//},
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