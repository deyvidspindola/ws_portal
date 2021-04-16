jQuery(document).ready(function(){

    /**
     * No modo quirks a <th> n√£o estava pegando a classe
     * Removendo e adicionando funciona!
     */
    jQuery('#quirksHack').removeClass('selecao');
    jQuery('#quirksHack').addClass('selecao');
    
    //bot„o gerar previs„o
    jQuery("#bt_gerarPrevisao").click(function(){
        jQuery('#acao').val('prepararPrevisao');
        jQuery('#form').submit();
    });
    
    //bot„o consultar
    jQuery("#bt_consultar").click(function(){
        jQuery('#acao').val('consultarPrevisao');
        jQuery('#form').submit();
    });
    
    //bot„o processar
    jQuery("#bt_processar").click(function(){
        jQuery('#acao').val('processarPrevisao');
        jQuery('#form').submit();
    });
    
    //bot„o excluir    
    jQuery("#bt_excluir").click(function(){

        var confirm = window.confirm('Deseja realmente excluir as previsıes n„o processadas, conforme os filtros informados?');

        if(confirm) {
            jQuery('#acao').val('excluirPrevisao');
            jQuery('#form').submit();
        }

    });
   
});