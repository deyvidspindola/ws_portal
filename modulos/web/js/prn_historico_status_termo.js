jQuery(function(){
       
    /*
     * Click do botao pesquisar
     */
    jQuery('#bt_pesquisar').click(function(){
        removeAlerta();

        //Validação do formulário
        var obrigatorio = true;
        if (jQuery('#contrato').val() == ""){
            criaAlerta('O campo "Contrato" deve ser preenchido.');
            jQuery('#contrato').addClass('inputError');
            obrigatorio = false;
        }
        if (!obrigatorio){
        	return false;
        } 

        jQuery("#acao").val('pesquisaStatusTermo');
        jQuery("#frm_pesquisa_status_termo").submit();
    })
    
    /*
     * Formatação para o campo "Contrato" aceitar apenas números.
     */ 
    jQuery("#contrato").keypress(function(){
    	formatar(this, '@');
    });
    jQuery("#contrato").blur(function(){
    	revalidar(this, '@', '');
    });
    
})
