jQuery(document).ready(function() {

    jQuery("#bt_pesquisar").click(function(){

       jQuery("acao").val("pesquisar");
       jQuery("#loader").removeClass("invisivel");
       jQuery("#resultado_pesquisa").addClass("invisivel");
       jQuery(".mensagem").addClass("invisivel");
       jQuery("#form_pesquisa").submit();

    });

    jQuery('#bt_novo').click(function(){
    	jQuery('#acao').val('novo');
    	jQuery('#form_pesquisa').submit();
    });

    jQuery('#bt_confirmar').click(function(){
    	jQuery('#acao').val('cadastrar');
    	jQuery('#form_cadastrar').submit();
    });

    jQuery('#bt_voltar').click(function(){
        window.location = 'ges_indicador.php';
    })

    jQuery('#gmiprecisao').blur(function() {
        validarPrecisao(this);
    }).keyup(function() {
        validarPrecisao(this);
    });

    jQuery('a.excluir').click(function(){

        var gmioid = jQuery(this).attr('rel');

        if (confirm("Tem certeza que deseja excluir este indicador?")){
            jQuery("#gmioid").val(gmioid);
            jQuery('#acao').val('excluir');
            jQuery('#form_pesquisa').submit();
        }

    });

    jQuery('a.copiar').click(function(){

        var gmioid = jQuery(this).attr('rel');

        if (confirm("Tem certeza que deseja copiar este indicador?")){
            jQuery("#gmioid").val(gmioid);
            jQuery('#acao').val('copiar');
            jQuery('#form_pesquisa').submit();
        }

    });

});

function validarPrecisao(campo){

    if( jQuery.isNumeric( jQuery(campo).val() ) ) {
        var valoresValidos = [0, 1, 2, 3, 4];
        var precisao = parseInt(jQuery(campo).val());

        if ( jQuery.inArray( precisao, valoresValidos ) == -1 ) {
            jQuery('#gmiprecisao').val('');
        }
    } else {
        jQuery(campo).val('');
    }
}