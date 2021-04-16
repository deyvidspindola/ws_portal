jQuery(document).ready(function(){
    
    
    
    jQuery('.p_form').submit(function(){
        // Remove alertas de erro 
		removeAlerta();
        jQuery('#is_pdf').val('');		
		jQuery(".inputError").removeClass("inputError");
        
        var valid_period    = checkDate();
        var valid_hour_ini  = checkHourIni();
        var valid_hour_fim  = checkHourFim();
        
        if(!valid_period) {            
            return false;
        }
        
        if(!valid_hour_ini) {            
            return false;
        }
        
        if(!valid_hour_fim) {            
            return false;
        }
        
        return true;
        
    });

    // Zebrando a tabela
	jQuery('.result:odd').addClass('tde');
	jQuery('.result:even').addClass('tdc');
     
    jQuery('#tipo_relatorio').change(function(){
        
        if(jQuery(this).val() == 'analitico' ){
            jQuery('.analitico').fadeIn();
        }else{
            jQuery('.analitico').fadeOut();
        }
    });
    
    jQuery('#imprimir').click(function() {		        
        jQuery('.relatorio').printArea();
	});
    
    jQuery("#hora_ini, #hora_fim").mask("99:99");
    
    jQuery('#motivo_nivel_1').change(function(){
        
        var motivo_nivel_1 = jQuery(this).val();
        
        if(motivo_nivel_1 == "") {
            jQuery('#motivo_nivel_2').html('<option value="">Todos</option>');
            jQuery('#motivo_nivel_3').html('<option value="">Todos</option>');
        }else{
            jQuery.ajax({
            
                url: 'rel_atendimento_front_end.php',
                type: 'post',
                data: {acao: 'getComboMotivoNivel2', motivo_nivel_1: motivo_nivel_1},  
                beforeSend: function(){
                    jQuery('#motivo_nivel_2_loader').show();
                },
                success: function(data){
                    
                    var result = jQuery.parseJSON(data);
                    
                    jQuery.each(result, function(i, val){
                        
                        var option = '<option value="'+ val.atmoid +'">'+ val.atmdescricao +'</option>';
                        
                        jQuery('#motivo_nivel_2').append(option);
                        
                    });
                    
                    jQuery('#motivo_nivel_2_loader').hide();
                    
                }

            });
            
        }
    });
    
    jQuery('#motivo_nivel_2').change(function(){
        
        var motivo_nivel_2 = jQuery(this).val();
        
        if(motivo_nivel_2 == "") {
            jQuery('#motivo_nivel_3').html('<option value="">Todos</option>');
        }else{
            jQuery.ajax({
            
                url: 'rel_atendimento_front_end.php',
                type: 'post',
                data: {acao: 'getComboMotivoNivel3', motivo_nivel_2: motivo_nivel_2},  
                beforeSend: function(){
                    jQuery('#motivo_nivel_3_loader').show();
                },
                success: function(data){
                    
                    var result = jQuery.parseJSON(data);
                    
                    jQuery.each(result, function(i, val){
                        
                        var option = '<option value="'+ val.atmoid +'">'+ val.atmdescricao +'</option>';
                        
                        jQuery('#motivo_nivel_3').append(option);
                        
                    });
                    
                    jQuery('#motivo_nivel_3_loader').hide();
                    
                }

            });
            
        }
    });
	
	jQuery('#dt_ini').blur(function(){
		jQuery(this).val(jQuery(this).val().replace(/[^\d\/]/g, ''));
	})
	
	jQuery('#dt_fim').blur(function(){
		jQuery(this).val(jQuery(this).val().replace(/[^\d\/]/g, ''));
	})
    
});

jQuery.download = function(url, data, method){
	//url and data options required
	if( url && data ){ 
		//data can be string of parameters or array/object
		data = typeof data == 'string' ? data : jQuery.param(data);
		//split params into form inputs
		var inputs = '';
		jQuery.each(data.split('&'), function(){ 
			var pair = this.split('=');
			inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />'; 
		});
		//send request
		jQuery('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>')
		.appendTo('body').submit().remove();
	}
};

function gerarPdf() {
    
    jQuery('#acao').val('gerarPdf');    
    
    var post = jQuery('.p_form').serialize();
    
    jQuery.download('rel_atendimento_front_end.php?acao=gerarPdf', post);
    
    jQuery('#acao').val('pesquisar');
}

function gerarXls() {	
    jQuery('#acao').val('gerarXls');
        
    var post = jQuery('.p_form').serialize();
    
	jQuery.download('rel_atendimento_front_end.php?acao=gerarXls', post);
    
    jQuery('#acao').val('pesquisar');
}

function checkDate(){    

    // Valida se a data final é maior que a inicial 
    if (diferencaEntreDatas($("#dt_fim").val(), $("#dt_ini").val()) < 0){                
        criaAlerta('A data final não pode ser menor que a data inicial');
        jQuery("#dt_fim").addClass("inputError");
        return false;
    }
    
    if(jQuery.trim($("#dt_ini").val()) == "") {
        criaAlerta('O período é obrigatório');
        jQuery("#dt_ini").addClass("inputError");
        return false;
    }
	
	if(jQuery.trim($("#dt_ini").val()).length < 10) {
        criaAlerta('Formato de data inválido');
        jQuery("#dt_ini").addClass("inputError");
        return false;
    }
    
    if(jQuery.trim($("#dt_fim").val()) == "") {
        criaAlerta('O período é obrigatório');
        jQuery("#dt_fim").addClass("inputError");
        return false;
    }
	
	if(jQuery.trim($("#dt_fim").val()).length < 10) {
        criaAlerta('O período é obrigatório');
        jQuery("#dt_fim").addClass("inputError");
        return false;
    }

	var dtIni = jQuery.trim(jQuery("#dt_ini").val());
	var dtFim = jQuery.trim(jQuery("#dt_fim").val());
	var nomeCliente = jQuery.trim(jQuery("#nome_cliente").val());
	
	
	
	var maxDiasComCliente = (bissexto(dtIni) || bissexto(dtFim)) ? 366 : 365;
	var maxDiasSemCliente = getDaysInMonth(dtIni);
	
	if (nomeCliente == '') {
		if ((diferencaEntreDatas(dtFim, dtIni)) > maxDiasSemCliente){
			criaAlerta("O período de pesquisa não deve ultrapassar 1 mês.");
	        jQuery("#dt_ini").addClass("inputError");
	        jQuery("#dt_fim").addClass("inputError");
	        return false;
		}
	}
	else {
		if ((diferencaEntreDatas(dtFim, dtIni)) > maxDiasComCliente){
			criaAlerta("O período de pesquisa não deve ultrapassar 12 meses.");
	        jQuery("#dt_ini").addClass("inputError");
	        jQuery("#dt_fim").addClass("inputError");
	        return false;
		}
	}
    
    return true;
}

function getDaysInMonth(date) {
    var dt = date.replace( " ", "" );
    var year = parseInt(dt.substring(6, 10));
    var month = ((parseInt(dt.substring(3, 5))) - 1); // the month (from 0-11)
	
	var d = new Date(year, (month + 1), 0);
	
	return d.getDate(); // last day in month
}

function bissexto(data) {
    var dt = data.replace( " ", "" );
    var ano = dt.substring(6,10);
    ano = parseInt(ano, 10) + 0;
    
	if (((ano % 4) == 0 && (ano % 100)!=0) || (ano % 400)==0) {
		return true;
	}

	return false;
}

function checkHourIni(){    
         
    var input_hora_ini = jQuery("#hora_ini").val();    
    var aHoras;
    var hora_ini;
    var minuto_ini;
    
    if(jQuery.trim(input_hora_ini) != ""){
        aHoras = input_hora_ini.split(':');
        
        hora_ini    = ~~aHoras[0];
        minuto_ini  = ~~aHoras[1];
        
        if(hora_ini > 23 || minuto_ini > 59){
            criaAlerta( 'Por favor digite um horário válido');
            jQuery("#hora_ini").addClass("inputError");
            return false;
        }
        
    }
    
    return true;
} 

function checkHourFim(){    
    
    var input_hora_fim = jQuery("#hora_fim").val();
    var aHoras;
    var hora_fim;
    var minuto_fim;
    
    
    if(jQuery.trim(input_hora_fim) != ""){
        aHoras = input_hora_fim.split(':');
        
        hora_fim    = ~~aHoras[0];
        minuto_fim  = ~~aHoras[1];
        
        if(hora_fim > 23 || minuto_fim > 59){
            criaAlerta( 'Por favor digite um horário válido');
            jQuery("#hora_fim").addClass("inputError");
            return false;
        }
        
    }
    
    return true;
    
} 