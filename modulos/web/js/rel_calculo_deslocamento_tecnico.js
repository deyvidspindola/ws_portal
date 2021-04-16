jQuery(document).ready(function() {

    //Evento Click do botão Pesquisar
    jQuery('#pesquisar').click(function(){

        //jQuery('.mensagem').html("");
        //jQuery('.mensagem').addClass("invisivel");
        
        //Validação das Datas
        /*if (jQuery('#dt_inicio').val() == "" || jQuery('#dt_fim').val() == "") {
            
            if (jQuery('#dt_inicio').val() == "") {
                jQuery('#dt_inicio').addClass("erro");
            }
            if (jQuery('#dt_fim').val() == "") {
                jQuery('#dt_fim').addClass("erro");
            }
            
            return false;
        }*/

        jQuery('#acao').val('pesquisar');

        jQuery('#form_rel_calculo_deslocamento_tecnico').submit();

    });

    //Evento Change da combo Representante para carregar a combo de Tecnicos
    jQuery('#repoid').change(function(event) {

        var rep_val = jQuery('#repoid').val();
        var post    = {acao: 'buscarTecnicos', repoid: rep_val };
         
        if (rep_val.length == 0) {
            jQuery('#itloid').html('<option value="">Escolha</option>');
            return false;
        }
        
        jQuery.ajax({
            url: 'rel_calculo_deslocamento_tecnico.php',
            dataType: 'json',
            type: 'post',
            data: post,
            success: function(data) { 

                var items = [];
                if (data.retorno !== null && data.erro === false) {
                      
                    items.push('<option value="">Escolha</option>');

                    jQuery.each(data.retorno, function(key, val) {
                            
                        if (val !== null && key !== null && val.itloid !== null && val.itlnome !== null) {
                            items.push('<option value="' + val.itloid + '">' +  val.itlnome  + '</option>');
                        }
                    });

                    jQuery('#itloid').html(items.join(''));
                }
                  
                if (data.erro !== false) {
                    alert("erro no retorno de dados"); 
                }
            }
        });
    });    
});

function esconderErros() {
    jQuery('label, input, select, textarea').removeClass('erro');
    jQuery('input, select, textarea').removeAttr('title');

    return true;
}

function mostrarErros(campos) {
    esconderErros();

    jQuery.each(campos, function(indice, dados) {
        jQuery('#' + dados.campo)
            .attr('title', dados.mensagem)
            .addClass('erro');
        jQuery('#' + dados.campo)
            .prev('label')
            .addClass('erro');

        return true;
    });

    return true;
}