jQuery(document).ready(function() {


    jQuery('#dt_ini').periodo('#dt_fim');


    jQuery('#pesquisar').click(function(){
        
        jQuery('.mensagem').html("");
        jQuery('.mensagem').addClass("invisivel");
        
        
        jQuery('.resultado').remove();
        
        if (jQuery('#dt_ini').val() == "" || jQuery('#dt_fim').val() == ""){
           
            jQuery('.alerta').html("Existem campos obrigatórios não preenchidos.");
            
            if (jQuery('#dt_ini').val() == ""){
                jQuery('#dt_ini').addClass("erro");
            }
            
            if (jQuery('#dt_fim').val() == ""){
                jQuery('#dt_fim').addClass("erro");
            }
            
            jQuery('.alerta').removeClass("invisivel");
            return false;
            
        }
        
        jQuery('#acao').val('pesquisar');
        jQuery('#form').submit();
        
    });
    
    
    jQuery('#gerar_csv').click(function(){
        
        jQuery('.mensagem').html("");
        jQuery('.mensagem').addClass("invisivel");
        
        
        jQuery('.resultado').remove();
        
        if (jQuery('#dt_ini').val() == "" || jQuery('#dt_fim').val() == ""){
           
            jQuery('.mensagem').html("Existem campos obrigatórios não preenchidos.");
            
            if (jQuery('#dt_ini').val() == ""){
                jQuery('#dt_ini').addClass("erro");
            }
            
            if (jQuery('#dt_fim').val() == ""){
                jQuery('#dt_fim').addClass("erro");
            }
            
            jQuery('.alerta').removeClass("invisivel");
            return false;
            
        }
        
        jQuery('#acao').val('gerar_csv');
        jQuery('#form').submit();
    });
    
    
});