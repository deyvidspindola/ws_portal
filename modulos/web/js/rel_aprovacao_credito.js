
jQuery(document).ready(function() {


    jQuery('#dt_ini').periodo('#dt_fim');


    jQuery('#pesquisar,#gerar_csv').click(function(){
        
        jQuery('.mensagem').html("");
        jQuery('.mensagem').addClass("invisivel");
        
        
        jQuery('.resultado').remove();
        
        if (jQuery('#dt_ini').val() == "" || jQuery('#dt_fim').val() == "" ) {
           
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
        if (jQuery(this).attr("id") == 'gerar_csv'){
            jQuery('#acao').val('gerar_csv');
        } else {
            jQuery('#acao').val('pesquisar');
        }
        jQuery('#form_rel_aprovacao_credito').submit();
        
    });
    
});