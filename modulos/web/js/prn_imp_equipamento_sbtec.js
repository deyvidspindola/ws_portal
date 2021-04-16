/**
 * STI 83172 - Requisições
 * 
 * @author Bruno Bonfim Affonso [bruno.bonfim@sascar.com.br]
 * @package Principal
 * @version 1.0
 * @since 22/11/2013
 */ 
jQuery(document).ready(function(){
	jQuery('#processar').unbind().click(function(){
		processar();
	});
});

/* Loading */
var carregando = new Object();
carregando.abrir  = function(){jQuery('#resultado_progress').attr('style','display: block;');}
carregando.fechar = function(){jQuery('#resultado_progress').attr('style','display: none;');}

function processar(){
	jQuery('#acao').val('processar');
	jQuery('#frm_importacao').attr('action', 'imp_equipamento_sbtec.php');
	jQuery('#frm_importacao').submit();
}

function importarEquipamento(){
	//Atributos
	var id = '';
	var separador = '';	
	//Limpando mensagens
	jQuery('.mensagem').empty().attr('style','display:none;');	
	//Recuperando os ID's
	jQuery('input[id*=equ_]').each(function(i, v){
	    if(jQuery(this).is(":checked") == true){
	        id += separador+jQuery(this).val();
	        separador = ',';
	    }
	});	
	//Verificando se nao esta vazio.
	id = jQuery.trim(id);
	
	if(id != ''){
		carregando.abrir();
		jQuery.post('imp_equipamento_sbtec.php',{
			acao : 'importar',
			dados : id
		},
		function(data){
			carregando.fechar();
			data = jQuery.parseJSON(data);
            
            //Removendo as linhas que forão importadas com sucesso.
            if(data.arraySucesso.length > 0){
                var v = '';
                for(i = 0; i < data.arraySucesso.length; i++){
                    v = data.arraySucesso[i];
                    jQuery('#equ_'+v).parent().parent().remove();
                }
                //Aplica variação de cor nas linhas
                aplicarZebra();
            }
            
			jQuery(data.classe).html(data.msg).attr('style','display:block;');
			jQuery('body,html').animate({scrollTop:0},1000); //Retorna ao topo da pagina            
		});
	} else{
		jQuery('#msgalerta').html('Selecione o(s) equipamento(s) para importar.').attr('style','display:block;');
		jQuery('body,html').animate({scrollTop:0},1000); //Retorna ao topo da pagina
		return false;
	}
}

function aplicarZebra(){
    var classe = '';
    var length = 0;    
        length = jQuery('.listagem').find('table').children().eq(1).children().length;
    
    if(length > 0){
        for(i = 0; i < length; i++){
            classe = !(i % 2) ? 'par' : '';
            jQuery('.listagem').find('table').children().eq(1).children().eq(i).removeClass().addClass(classe);
        }
    }
}