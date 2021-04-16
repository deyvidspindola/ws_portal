$(document).ready(function(){	

	$(".botao").click(function () {
		var acao = $(this).attr('name');

	 	if ($(this).attr('id') == 'exportar') {
	 		//var htmlResult = $('#tableResultReport').parent().html();
			//$.post(urlPost, { 'acao': 'exportarXls', 'html': htmlResult });			
			//$.download(urlPost,'acao=exportarXls&html='+ htmlResult );
			$('input[name="acao"]').val(acao);
		 	$("#form").submit();
	 	} else {
		 	$('input[name="acao"]').val(acao);
		 	$("#form").submit();
	 	}
	});	

    jQuery('select').each(function() {
        if (jQuery(this).find('option:selected').length == 0)
        {
        	jQuery(this).val('');
        }
    });

});

// Função buscar transportadoras
function getTranspCli(){

	var transp = jQuery("#not_id_transpCli").val();
	var not_id_clioid = '#not_id_clioid';
	
	if(transp.length > 2){

		jQuery.ajax({
			dataType: "json",
			url: 'cad_embarcadores.php',
			type: 'post',
			data: {
				transpCli : transp,
				acao : 'transpCliente'
			},
			beforeSend: function(){
				jQuery(not_id_clioid).html("<option value=''>Carregando..</option>");
			},
			success: function(data){

				if (data == 0) {
					alert('Nenhum resultado encontrado');
					jQuery(not_id_clioid).html("<option value=''>TODOS</option>");
				
				}else{
					var opt = "";
					jQuery(not_id_clioid).html("");
					
					jQuery.each(data, function(index, value) {
						opt = "<option value='"+index+"'>" +value+ "</option>" ;
						jQuery(not_id_clioid).append(opt);
					});
				}
				
			}
		});

	}else{
		alert('A pesquisa deve conter ao menos 3 dígitos');
	}
}


// Função buscar gerenciadores de risco
function getGerenc(){

	var gerenc = jQuery("#not_id_gerencGet").val();
	var not_id_gerenc = '#not_id_gerenc';
	
	if(gerenc.length > 2){

		jQuery.ajax({
			dataType: "json",
			url: 'cad_embarcadores.php',
			type: 'post',
			data: {
				gerencSel : gerenc,
				acao : 'gerencRisco'
			},
			beforeSend: function(){
				jQuery(not_id_gerenc).html("<option value=''>Carregando..</option>");
			},
			success: function(data){

				if (data == 0) {
					alert('Nenhum resultado encontrado');
					jQuery(not_id_gerenc).html("<option value=''>TODOS</option>");
				
				}else{
					var opt = "";
					jQuery(not_id_gerenc).html("");
					
					jQuery.each(data, function(index, value) {
						opt = "<option value='"+index+"'>" +value+ "</option>" ;
						jQuery(not_id_gerenc).append(opt);
					});
				}
				
			}
		});

	}else{
		alert('A pesquisa deve conter ao menos 3 dígitos');
	}
}


function carregaTransportadores(clientes){

	var selected = '';
	var transp	 = jQuery("#not_id_transpCli").val();
	
	if(transp.length > 2)
	{
		jQuery.ajax({
			dataType: "json",
			url: 'cad_embarcadores.php',
			type: 'post',
			data: {
				transpCli : transp,
				acao : 'transpCliente'
			},
			beforeSend: function(){
				jQuery('#not_id_clioid').html("<option value=''>Carregando..</option>");
			},
			success: function(data){

				if (data == 0){
					jQuery('#not_id_clioid').html("<option value=''>TODOS</option>");
				}else{
					var opt = "";
					jQuery('#not_id_clioid').html("");
					
					jQuery.each(data, function(index, value) {

						if(clientes.indexOf(index) >= 0){
							selected = "selected='selected'";
						}else{
							selected = '';
						}
						
						opt = "<option value='"+index+"' "+selected+">" +value+ "</option>" ;
						jQuery('#not_id_clioid').append(opt);
					});
				}
				
			}
		});
	}
}

function carregaGerenciadoras(gerenc){

	var selected  = '';
	var gerencGet = jQuery("#not_id_gerencGet").val();
	
	if(gerencGet.length > 2)
	{
		jQuery.ajax({
			dataType: "json",
			url: 'cad_embarcadores.php',
			type: 'post',
			data: {
				gerencSel : gerencGet,
				acao : 'gerencRisco'
			},
			beforeSend: function(){
				jQuery('#not_id_gerenc').html("<option value=''>Carregando..</option>");
			},
			success: function(data){

				if (data == 0){
					jQuery('#not_id_gerenc').html("<option value=''>TODOS</option>");
				}else{
					var opt = "";
					jQuery('#not_id_gerenc').html("");
					
					jQuery.each(data, function(index, value) {

						if(gerenc.indexOf(index) >= 0){
							selected = "selected='selected'";
						}else{
							selected = '';
						}
						
						opt = "<option value='"+index+"' "+selected+">" +value+ "</option>" ;
						jQuery('#not_id_gerenc').append(opt);
					});
				}
				
			}
		});
	}
}
