jQuery(document).ready(function(){

    var msg_campos_obrigatorios = "Existem campos obrigatórios não preenchidos.";
    var msg_sucesso_excluir     = "Registro inativado com sucesso.";
    var msg_sucesso_editar      = "Registro alterado com sucesso.";
    var msg_erro                = "Houve um erro no processamento dos dados.";

   //botão novo
   jQuery("#bt_gravar").click(function(){

        isCamposValidos = validarCamposObrigatorios();

        if( isCamposValidos ) {
            jQuery('form').submit();;
        }
   });

   /*
    * Tratamento somente numeros inteiros
   */
    jQuery('body').on('keyup blur', '.numero', function() {
        jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));

    });


    function validarCamposObrigatorios() {

         var erros = 0;

        jQuery('.obrigatorio').each(function(id,valor){

         elemento = jQuery('#'+valor.id);
         elemento.removeClass('erro');

          if(jQuery.trim(elemento.val()) == '') {

                elemento.addClass('erro');
                erros++;
            }
        });

        if(erros > 0){
            jQuery('#mensagem_alerta').html(msg_campos_obrigatorios);
            jQuery('#mensagem_alerta').show();
            return false;
        } else {
            return true;
        }


    }

});