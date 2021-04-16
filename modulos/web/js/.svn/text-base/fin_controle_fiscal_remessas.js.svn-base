jQuery(document).ready(function(){ 

    jQuery("body").delegate('#pesquisar','click',function(){
        
        function daydiff(first, second) {
            return Math.round((second-first)/(1000*60*60*24));
        }
        // transformação de data para mm/dd/YYYY para comparações
        var dt_ini = jQuery("#dt_ini").val();
        var dtInicialArr = dt_ini.split("/");
        var dtInicial = new Date(dtInicialArr[2], dtInicialArr[1]-1, dtInicialArr[0]);
        
        var dt_fim = jQuery("#dt_fim").val();
        var dtFinalArr = dt_fim.split("/");
        var dtFinal = new Date(dtFinalArr[2], dtFinalArr[1]-1, dtFinalArr[0]);
        
        
        //se serie esta preenchida, remetente não, fornecedor não e destinatario não, então 35 dias de período máximo
        if(jQuery('#nSerie').val()!=='' && jQuery('#repreRespRem').val()==='' && jQuery('#repreFornDest').val()==='' && jQuery('#repreRespDest').val()===''){
            if(jQuery('#dt_ini').val()=== '' || jQuery('#dt_fim').val()=== ''){
                jQuery('#frame_load').html('');
                jQuery("#msgalerta").html("Existem campos obrigatórios (<font color='#FF0000'>*</font></style>) não preenchidos.").showMessage();
            }else if(daydiff(dtInicial,dtFinal)>35){
                jQuery('#frame_load').html('');
                jQuery("#msgalerta").html("Período máximo de pesquisa por série é de 35 dias. Selecione Remetente, Fornecedor ou Destinatário para não ter limited e período").showMessage();
            }else{
                jQuery.fn.pesquisa();
                jQuery('#frame_load').html('');
                jQuery('#msgalerta').html('').hide();
            }
        }else if(jQuery('#nSerie').val()!=='' && (jQuery('#repreRespRem').val()!=='' || jQuery('#repreFornDest').val()!=='' || jQuery('#repreRespDest').val()!=='')){
                jQuery.fn.pesquisa();
                jQuery('#frame_load').html('');
                jQuery('#msgalerta').html('').hide();
        }else if(jQuery('#dt_ini').val()=== '' || jQuery('#dt_fim').val()=== ''){
                jQuery('#frame_load').html('');
                jQuery("#msgalerta").html("Existem campos obrigatórios (<font color='#FF0000'>*</font></style>) não preenchidos.").showMessage();
        }else if(dtInicial>dtFinal){
                jQuery('#frame_load').html('');
                jQuery("#msgalerta").html("Data de início não pode ser maior que a data final.").showMessage();
        }else if(daydiff(dtInicial,dtFinal)>35){
                jQuery('#frame_load').html('');
                jQuery("#msgalerta").html("Período máximo de pesquisa é de 35 dias").showMessage();
        }else if(jQuery('#tipoMovimentacao').val()===''){
                jQuery('#frame_load').html('');
                jQuery("#msgalerta").html("Selecione o Tipo de Movimentação").showMessage();
        }else {

            jQuery.fn.pesquisa();
            jQuery('#frame_load').html('');
            jQuery('#msgalerta').html('').hide();
        }
    });
    
    jQuery("body").delegate('#gerarCsv','click',function(){
        
        function daydiff(first, second) {
            return Math.round((second-first)/(1000*60*60*24));
        }
        
        // transformação de data para mm/dd/YYYY para comparações
        var dt_ini = jQuery("#dt_ini").val();
        var dtInicialArr = dt_ini.split("/");
        var dtInicial = new Date(dtInicialArr[2], dtInicialArr[1]-1, dtInicialArr[0]);
        
        var dt_fim = jQuery("#dt_fim").val();
        var dtFinalArr = dt_fim.split("/");
        var dtFinal = new Date(dtFinalArr[2], dtFinalArr[1]-1, dtFinalArr[0]);
        
        
        //se serie esta preenchida, remetente não, fornecedor não e destinatario não, então 35 dias de período máximo
        if(jQuery('#nSerie').val()!=='' && jQuery('#repreRespRem').val()==='' && jQuery('#repreFornDest').val()==='' && jQuery('#repreRespDest').val()===''){
            if(jQuery('#dt_ini').val()=== '' || jQuery('#dt_fim').val()=== ''){
                jQuery('#frame_load').html('');
                jQuery("#msgalerta").html("Existem campos obrigatórios (<font color='#FF0000'>*</font></style>) não preenchidos.").showMessage();
            }else if(daydiff(dtInicial,dtFinal)>35){
                jQuery('#frame_load').html('');
                jQuery("#msgalerta").html("Período máximo de pesquisa por série é de 35 dias. Selecione Remetente, Fornecedor ou Destinatário para não ter limited e período").showMessage();
            }else{
                //jQuery.fn.pesquisa();
                //jQuery('#frame_load').html('');
                jQuery('#msgalerta').html('').hide();
                //modulos\Financas\View\fin_controle_fiscal_remessas
                jQuery('#frm_pesquisar').attr('action','modulos/Financas/View/fin_controle_fiscal_remessas/resultado_remessa_csv.php');
                jQuery('#frm_pesquisar').attr('target','_blank');
                jQuery('#frm_pesquisar').submit();
            }
        }else if(jQuery('#nSerie').val()!=='' && (jQuery('#repreRespRem').val()!=='' || jQuery('#repreFornDest').val()!=='' || jQuery('#repreRespDest').val()!=='')){
                //jQuery.fn.pesquisa();
                //jQuery('#frame_load').html('');
                jQuery('#msgalerta').html('').hide();
                //modulos\Financas\View\fin_controle_fiscal_remessas
                jQuery('#frm_pesquisar').attr('action','modulos/Financas/View/fin_controle_fiscal_remessas/resultado_remessa_csv.php');
                jQuery('#frm_pesquisar').attr('target','_blank');
                jQuery('#frm_pesquisar').submit();
        }else if(jQuery('#dt_ini').val()=== '' || jQuery('#dt_fim').val()=== ''){
                jQuery('#frame_load').html('');
                jQuery("#msgalerta").html("Existem campos obrigatórios (<font color='#FF0000'>*</font></style>) não preenchidos.").showMessage();
        }else if(dtInicial>dtFinal){
                jQuery('#frame_load').html('');
                jQuery("#msgalerta").html("Data de início não pode ser maior que a data final.").showMessage();
        }else if(daydiff(dtInicial,dtFinal)>35){
                jQuery('#frame_load').html('');
                jQuery("#msgalerta").html("Período máximo de pesquisa é de 35 dias").showMessage();
        }else if(jQuery('#tipoMovimentacao').val()===''){
                jQuery('#frame_load').html('');
                jQuery("#msgalerta").html("Selecione o Tipo de Movimentação").showMessage();
        }else {

            //jQuery.fn.pesquisa();
            //jQuery('#frame_load').html('');
            jQuery('#msgalerta').html('').hide();
            //modulos\Financas\View\fin_controle_fiscal_remessas
            jQuery('#frm_pesquisar').attr('action','modulos/Financas/View/fin_controle_fiscal_remessas/resultado_remessa_csv.php');
            jQuery('#frm_pesquisar').attr('target','_blank');
            jQuery('#frm_pesquisar').submit();
        }
    });
    

});

jQuery.fn.pesquisa = function() {

	jQuery.ajax({
		url: 'fin_controle_fiscal_remessas.php',
		type: 'post',
		  data: {
              acao: 'pesquisar',
			  dt_ini:jQuery('input[name=dt_ini]').val(),
			  dt_fim:jQuery('input[name=dt_fim]').val(),
			  nRemessa:jQuery('input[name=nRemessa]').val(),
			  nfRemessa:jQuery('input[name=nfRemessa]').val(),
			  tipoRelatorio:jQuery('#tipoRelatorio').val(),
			  tipoMovimentacao:jQuery('#tipoMovimentacao').val(),
			  statusRemessa:jQuery('#statusRemessa').val(),
			  nSerie:jQuery('#nSerie').val(),
			  repreRespRem:jQuery('#repreRespRem').val(),
                          repreFornDest:jQuery('#repreFornDest').val(),
			  repreRespDest:jQuery('#repreRespDest').val(),
                          numero_pedido:jQuery('#numero_pedido').val()
	        },
	   
		beforeSend: function(){		
			jQuery('#frame01').html('<center><img src="images/loading.gif" alt="" /></center>');
			jQuery('#bt_pesquisar').attr('disabled', 'disabled');
			
		},
		success: function(data){
			console.log(data);
			
			try{	
				
                            var resultado = jQuery.parseJSON(data);
				
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

	
};