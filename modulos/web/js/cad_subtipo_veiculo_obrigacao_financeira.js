jQuery(document).ready(function(){

    var msg_campos_obrigatorios = "Existem campos obrigatórios não preenchidos.";
    var msg_sucesso_excluir     = "Registro inativado com sucesso.";
    var msg_sucesso_editar      = "Registro alterado com sucesso.";
    var mensagem_erro           = "Houve um erro no processamento dos dados.";
    var router                  = 'cad_subtipo_veiculo_obrigacao_financeira.php';

    jQuery(".detalhes").hide()

   //botão novo
   jQuery("#bt_novo").click(function(){
       window.location.href = router + "?acao=cadastrar";
   });

   //botão voltar
   jQuery("#bt_voltar").click(function(){
       window.location.href = router;
   });

   //botão voltar
   jQuery("#bt_gravar").click(function(){

        marcarTodosSubTipos();

        isCamposValidos = validarCamposObrigatorios();

        if(isCamposValidos) {
            jQuery( "#form-cadastro" ).submit();
        }

   });

   /*
    * Acoes do icone editar
    */
     jQuery("table").on('click','.editar',function(event) {

        event.preventDefault();

        vstoid = jQuery(this).data('vstoid');

        window.location.href = router + "?acao=editar&vstoid=" + vstoid;

    });

    /*
    * Acoes do icone excluir
    */
     jQuery("table").on('click','.excluir',function(event) {

        event.preventDefault();

        vstoid = jQuery(this).data('vstoid');

        jQuery("#msg_dialogo_exclusao").dialog({
          title: "Confirmação de Exclusão",
          resizable: false,
          modal: true,
          buttons: {
            "Sim": function() {

              jQuery( this ).dialog( "close" );

                jQuery.ajax({
                    url: router,
                    type: 'POST',
                    data: {
                        acao: 'excluirRegistroAjax',
                        vstoid: vstoid
                    },
                    success: function(data) {

                        try{
                            if(data == 'OK') {
                                jQuery('#mensagem_sucesso').html(msg_sucesso_excluir);
                                jQuery('#mensagem_sucesso').show();
                                jQuery('#linha_' + vstoid).remove();
                                jQuery('#det_' + vstoid).remove();

                                aplicarCorLinha();

                            } else {
                                jQuery('#mensagem_erro').html(mensagem_erro);
                                jQuery('#mensagem_erro').show();
                            }
                        } catch(erro){
                            jQuery('#mensagem_erro').html(mensagem_erro);
                            jQuery('#mensagem_erro').show();
                        }

                    }
                });

            },
            "Não": function() {
              jQuery( this ).dialog( "close" );
            }
          }
        });
    });

   jQuery('#form-pesquisa').on('change', '#tipvoid', function() {

        tipvoid = jQuery('#tipvoid option:selected').val();

        if(tipvoid == ''){
          jQuery("#vstoid").html("<option value=''>Escolha</option>");
           return true;
        }

           jQuery.ajax({
            url: router,
            type: 'POST',
            data: {
                acao: 'recuperarListaSubTipoAjax',
                tipvoid: tipvoid,
                cadastro: 'N'
            },
            success: function(data) {

                try{

                    data = JSON.parse(data);

                    if(data) {

                        var combo = "<option value=''>Escolha</option>";

                        if (data.length > 0) {
                            jQuery.each(data, function(i, val) {
                                combo += "<option value='" + val.chave + "'>" + val.valor + "</option>";
                            });
                        }

                        jQuery("#vstoid").html(combo);
                    }

                } catch(erro){
                    jQuery('#mensagem_erro').html(mensagem_erro);
                    jQuery('#mensagem_erro').show();
                }

            }
        });
    });

  jQuery('#form-cadastro').on('change', '#tipvoid', function() {

        tipvoid = jQuery('#tipvoid option:selected').val();

        if(tipvoid == ''){
           jQuery("#vstoid").html("<option value=''></option>");
           return true;
        }

           jQuery.ajax({
            url: router,
            type: 'POST',
            data: {
                acao: 'recuperarListaSubTipoAjax',
                tipvoid: tipvoid,
                cadastro: 'S'
            },
            success: function(data) {

                try{

                    data = JSON.parse(data);

                    if(data) {

                        var lista = '';

                        if (data.length > 0) {
                            jQuery.each(data, function(i, val) {
                                lista += "<option value='" + val.chave + "'>" + val.valor + "</option>";
                            });
                        }

                        jQuery("#vstoid").html(lista);
                    }

                } catch(erro){
                    jQuery('#mensagem_erro').html(mensagem_erro);
                    jQuery('#mensagem_erro').show();
                }

            }
        });
    });

    //icone mais-menos
    jQuery(".bt_detalhes").click(function(event){

        event.preventDefault();

        var id_item = jQuery(this).attr('item-id');

        jQuery("#det_" + id_item).toggle(0, function() {

            jQuery(".mais-menos_" + id_item).toggle();

        });

    });

    function validarCamposObrigatorios() {

        var erros = 0;

        jQuery('form .obrigatorio').each(function(id,valor){

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

    /*
    * Reorganzia as cores das linhas na lista
    */
    function aplicarCorLinha(){

        var cor = '';

        //remove cores
        jQuery('tbody .bloco_itens').removeClass('par');
        jQuery('tbody .bloco_itens').removeClass('impar');


        //aplica cores
        jQuery('tbody .bloco_itens').each(function(){
            cor = (cor == "par") ? "impar" : "par";
            jQuery(this).addClass(cor);
        });
    }

    function marcarTodosSubTipos() {

        var elementosSelecionados = 0;

        elementosSelecionados = jQuery('#form-cad #vstoid').val();

        if( jQuery.isEmptyObject(elementosSelecionados) ){

            jQuery('#form-cad #vstoid option').each(function(){
                jQuery(this).prop('selected', true);
            });
        }
    }

});