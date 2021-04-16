jQuery(document).ready(function(){
   
   
	
	jQuery('#btn_novo').click(function(){
        window.location.href = 'fin_credito_futuro_parametrizacao.php?acao=novo';
    });
    
   
 
 
     //Pesquisar os cadastros de motivos do credito
    jQuery("#pesquisarMotivoCredito").click(function(){
        jQuery("#acao").val("pesquisarMotivoCredito");
        jQuery("#form").submit();
    });
    
    
    //Pesquisar o tipo de campanha promocional
    jQuery("#pesquisarTipoCampanhaPromocional").click(function(){
        jQuery("#acao").val("pesquisarTipoCampanhaPromocional");
        jQuery("#form").submit();
    });
    
    
    //Retornar para Pesquisa de Motivo Credito
    jQuery("#retornarMotivoCredito").click(function(){
        jQuery("#acao").val("pesquisarMotivoCredito");
        jQuery("#form").submit();
    }); 
    
    
    jQuery('#btn_voltar').click(function(){
        window.location.href = 'fin_credito_futuro_parametrizacao.php';
    });
    
    //Retornar para Pesquisa do tipo de campanha promocional
    jQuery("#retornarTipoCampanhaPromocional").click(function(){
        jQuery("#acao").val("pesquisarTipoCampanhaPromocional");
        jQuery("#form").submit();
    });
    
    
    
    //Novo tipo de campanha promocional
    jQuery("#novoTipoCampanhaPromocional").click(function(){
        jQuery("#acao").val("cadastrarTipoCampanhaPromocional");
        jQuery("#form").submit();
    });
    
    
    //Novo motivo credito
    jQuery("#novoMotivoCredito").click(function(){
        jQuery("#acao").val("cadastrarMotivoCredito");
        jQuery("#form").submit();
    });
    
    
    
    
    
    //Gravar novo tipo de campanha promocional
    jQuery("#cadastrarTipoCampanhaPromocional").click(function(){
        
        jQuery('input.erro').removeClass('erro');
        jQuery('label.erro').removeClass('erro');
        jQuery('#mensagem_alerta').hideMessage();
        var descricao = jQuery("#descricao").val();
        
        if (jQuery.trim(descricao) == ''){
            jQuery('#mensagem_alerta').html('Existem campos obrigatórios não preenchidos.').showMessage();
            jQuery("#descricao").addClass('erro');
            jQuery('label[for="descricao"]').addClass('erro');
            return false;
        }
        
        jQuery("#acao").val("gravarTipoCampanhaPromocional");
        jQuery("#form").submit();
    });
    
    
    //Gravar novo Motivo Credito
    jQuery("#cadastrarMotivoCredito").click(function(){
        
        jQuery('input.erro').removeClass('erro');
        jQuery('label.erro').removeClass('erro');
        jQuery('#mensagem_alerta').hideMessage();
        var cfmcdescricao = jQuery("#cfmcdescricao").val();
        
        if (jQuery.trim(cfmcdescricao) == ''){
            jQuery('#mensagem_alerta').html('Existem campos obrigatórios não preenchidos.').showMessage();
            jQuery("#cfmcdescricao").addClass('erro');
            jQuery('label[for="cfmcdescricao"]').addClass('erro');
            return false;
        }
        
        jQuery("#acao").val("gravarMotivoCredito");
        jQuery("#form").submit();
    });
    
    
    
    
    
    
    
});


function excluirTipoCampanha(idTipoCampanha){
    if (confirm('Deseja realmente excluir o registro?')) {
        jQuery("#cftpoid").val(idTipoCampanha);
        jQuery("#acao").val("excluirTipoCampanhaPromocional");
        jQuery("#form").submit();
    }
}


function excluirMotivoCredito(idMotivoCredito){
    if (confirm('Deseja realmente excluir o registro?')) {
        jQuery("#cfmcoid").val(idMotivoCredito);
        jQuery("#acao").val("excluirMotivoCredito");
        jQuery("#form").submit();
    }
}




 