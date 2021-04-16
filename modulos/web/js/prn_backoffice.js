jQuery(document).ready(function(){
    var placas = false;
    
    //faz o tratamento para os campos de perídos 
    jQuery("#dt_evento_de").periodo("#dt_evento_ate");

   //jQuery(function(){
     // jQuery("#clifone").mask("(99) 9999-9999");
     //  jQuery.mask.addPlaceholder("~","[+-]");
         
 
   // });

    jQuery("#bt_gerarArquivo").click(function() {
        jQuery("#acao").val('gerar_csv');
        jQuery("#form").submit();
    });
   
   jQuery("#bt_novo").click(function(){
       window.location.href = "prn_backoffice.php?acao=cadastrar";
   });
   
    jQuery('body').delegate('.editar', 'click', function() {
        var id = jQuery(this).parent().parent().parent().attr('id');
        window.location.href = "prn_backoffice.php?acao=editar&bacoid=" + id;
    });

   
    jQuery("#bt_voltar").click(function() {
        window.location.href = "prn_backoffice.php";
    });
       
    jQuery("#bt_incluir_historico").click(function(){
        jQuery("#acao").val('incluir_historico');
        jQuery("#form_cadastrar").submit();
    });
   
    jQuery( "#clinome" ).autocomplete({
        source: "prn_backoffice.php?acao=buscarDinamicamente&filtro=nome",
        minLength: 3,       
        response: function(event, ui ) {  
            
            jQuery("#clioid").val('');

            /*jQuery('#msg_alerta').hide();
            
            if(!ui.content.length && jQuery.trim(jQuery(this).val()) != "") {
                jQuery('#msg_alerta').html(_escape(jQuery(this).val()) + ' não consta no cadastro.').fadeIn();
            }*/
            
            if(jQuery.trim(jQuery(this).val()) == "") {
                jQuery(this).val('');
            }
            
            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {}
                }
            });   
            
        },
        select: function( event, ui ) {
            jQuery("#clioid").val(ui.item.id);
            jQuery("#clifone").val(ui.item.telefone);
            jQuery("#cpf_cgc").val(ui.item.cpf_cgc);
            
            placas = false;
            $("#idplaca option").remove();
            var html = "<option value=''>Escolha</option>";
            jQuery("#idplaca").append(html);
        }
    });
    
    jQuery("#nomePlacaInput").autocomplete({
	        source: "prn_backoffice.php?acao=buscarPorPlaca",
	        minLength: 5,
	        response: function(event, ui ) { 
	            
	            jQuery("#idPlacaInput").val('');
	            
	            if(jQuery.trim(jQuery(this).val()) == "") {
	                jQuery(this).val('');
	            }
	            
	            jQuery(this).autocomplete("option", {
	                messages: {
	                    noResults: '',
	                    results: function() {}
	                }
	            });   
	            
	        },	
	        select: function( event, ui ) {
	        	
	            jQuery("#idPlacaInput").val(ui.item.veioid);
	            jQuery("#clioid").val(ui.item.clioid);
	            
	            jQuery("#clinome").val(ui.item.clinome);
	            jQuery("#clifone").val(ui.item.telefone);
	            jQuery("#cpf_cgc").val(ui.item.cpf_cgc);
	            jQuery("#clifone").val(ui.item.telefone);
	            jQuery("#tpcdescricao").val(ui.item.tpcdescricao);
	            jQuery("#tpcoid").val(ui.item.tpcoid);
	            
	            autocompletePlacaDois(ui.item.clioid);
	            
	        }
    	
    });
    
    function autocompletePlacaDois(clioid){
        $("#idplaca option").remove();

        var html = "<option value=''>Escolha</option>";
        jQuery("#idplaca").append(html);

        if(!jQuery.trim(clioid) == "") {
            jQuery.ajax({
                url: 'prn_backoffice.php?acao=buscarDinamicamente&filtro=placa&clioid='+clioid,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    html = "<option value=''>Aguarde</option>";
                    jQuery("#idplaca").html(html).attr('disabled','disabled');
                    jQuery('label[for="idplaca"]').addClass('ui-autocomplete-loading');
                },
                success: function(resultado) {
                    html = "<option value=''>Escolha</option>";
                    if(resultado != null){
                        jQuery.each(resultado,function(i, result){
                        	if(result.veiplaca == $('#nomePlacaInput').val()){                        		
                        		html += "<option selected tipoContratoId='"+ result.tpcoid +"' tipoContratoDesc='"+ result.tpcdescricao +"' value='"+ result.veiplaca +"'>"+result.veiplaca+"</option>";
                        	}else{
                        		html += "<option tipoContratoId='"+ result.tpcoid +"' tipoContratoDesc='"+ result.tpcdescricao +"' value='"+ result.veiplaca +"'>"+result.veiplaca+"</option>";
                        	}
                        });
                        placas = true;
                    } else {
                        html = "<option value=''>Sem Resultados</option>";
                    }
                },
                complete: function(){
                    jQuery('label[for="idplaca"]').removeClass('ui-autocomplete-loading');
                    jQuery("#idplaca").html(html).removeAttr('disabled');
                },
                error: function(xhr, status, error){
                    alert(status + ' - ' + error);
                }
            });   
        }
    }
    
    function autocompletePlaca(clioid, tpcoid){
        $("#idplaca option").remove();

        var html = "<option value=''>Escolha</option>";
        jQuery("#idplaca").append(html);

        if(!jQuery.trim(clioid) == "") {
            jQuery.ajax({
                url: 'prn_backoffice.php?acao=buscarDinamicamente&filtro=placa&clioid='+clioid,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    html = "<option value=''>Aguarde</option>";
                    jQuery("#idplaca").html(html).attr('disabled','disabled');
                    jQuery('label[for="idplaca"]').addClass('ui-autocomplete-loading');
                },
                success: function(resultado) {
                    html = "<option value=''>Escolha</option>";
                    if(resultado != null){
                        jQuery.each(resultado,function(i, result){
                        	if(result.tpcoid==tpcoid){                        		
                        		html += "<option selected tipoContratoId='"+ result.tpcoid +"' tipoContratoDesc='"+ result.tpcdescricao +"' value='"+ result.veiplaca +"'>"+result.veiplaca+"</option>";
                        	}else{
                        		html += "<option tipoContratoId='"+ result.tpcoid +"' tipoContratoDesc='"+ result.tpcdescricao +"' value='"+ result.veiplaca +"'>"+result.veiplaca+"</option>";
                        	}
                        });
                        placas = true;
                    } else {
                        html = "<option value=''>Sem Resultados</option>";
                    }
                },
                complete: function(){
                    jQuery('label[for="idplaca"]').removeClass('ui-autocomplete-loading');
                    jQuery("#idplaca").html(html).removeAttr('disabled');
                },
                error: function(xhr, status, error){
                    alert(status + ' - ' + error);
                }
            });   
        }
    }
    
    jQuery('#idplaca').change(function(){
        tipoContratoDesc = jQuery('#idplaca option:selected').attr('tipoContratoDesc')
        tipoContratoId = jQuery('#idplaca option:selected').attr('tipoContratoId')
        
        jQuery('#tpcdescricao').val(tipoContratoDesc);
        jQuery('#tpcoid').val(tipoContratoId);
        jQuery('#nomePlacaInput').val(jQuery('#idplaca option:selected').val());
    });

    jQuery("#clinome").blur(function(){
        if(jQuery.trim(jQuery(this).val()) == "") {
            jQuery("#clioid").val('');
            autocompletePlaca('');
        }
    });
    
    jQuery("#nomePlacaInput").blur(function(){
        if(jQuery.trim(jQuery(this).val()) == "") {
            jQuery("#idPlacaInput").val('');
        }
    });
    
    jQuery("select#idplaca").mouseenter(function(e){
        if(jQuery(this).val() == "" && e.target.nodeName.toLowerCase() == "select" && placas == false)  {
            autocompletePlaca(jQuery("#clioid").val());   
        }
    });
       
});

// List of HTML entities for escaping.
var htmlEscapes = {
  '&': '&amp;',
  '<': '&lt;',
  '>': '&gt;',
  '"': '&quot;',
  "'": '&#x27;',
  '/': '&#x2F;'
};

// Regex containing the keys listed immediately above.
var htmlEscaper = /[&<>"'\/]/g;

// Escape a string for HTML interpolation.
_escape = function(string) {
  return ('' + string).replace(htmlEscaper, function(match) {
    return htmlEscapes[match];
  });
};