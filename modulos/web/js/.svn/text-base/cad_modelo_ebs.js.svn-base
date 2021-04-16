jQuery(document).ready(function(){

    /**
     * Acoes do icone excluir
     */
     jQuery("table").on('click','.excluir',function(event) {

        event.preventDefault();

        id = jQuery(this).data('modeoid');
        elemento = this;

        jQuery("#mensagem_excluir").dialog({
            title: "Confirmação de Exclusão",
            resizable: false,
            modal: true,
            buttons: {
                "Sim": function() {
                jQuery( this ).dialog( "close" );

                    jQuery.ajax({
                        url: 'cad_modelo_ebs.php',
                        type: 'POST',
                        data: {
                            acao: 'excluir',
                            modeoid: id
                        },
                        success: function(data) {

                            if(data) {
                                esconderMensagens();

                                if(data == 'OK') {
                                    jQuery('#mensagem_sucesso').html("Registro excluído com sucesso.");
                                    jQuery('#mensagem_sucesso').show();
                                    jQuery(elemento).parent().parent().remove();

                                    corrigeTabela();

                                    aplicarCorLinha();

                                } else {
                                    jQuery('#mensagem_erro').html("Houve um erro no processamento dos dados.");
                                    jQuery('#mensagem_erro').show();
                                }
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


    /**
     * Acoes do icone excluir marca
     */
     jQuery("table").on('click','.excluir_marca',function(event) {

        event.preventDefault();

        id = jQuery(this).data('mmeoid');
        elemento = this;

        jQuery("#mensagem_excluir").dialog({
            title: "Confirmação de Exclusão",
            resizable: false,
            modal: true,
            buttons: {
                "Sim": function() {
                jQuery( this ).dialog( "close" );

                    jQuery.ajax({
                        url: 'cad_modelo_ebs.php',
                        type: 'POST',
                        data: {
                            acao: 'excluirMarca',
                            mmeoid: id
                        },
                        success: function(data) {

                            if(data) {
                                esconderMensagens();

                                if(data == 'OK') {
                                    jQuery('#mensagem_sucesso').html("Registro excluído com sucesso.");
                                    jQuery('#mensagem_sucesso').show();
                                    jQuery(elemento).parent().parent().remove();
                                    jQuery('#registros_encontrados').data('quantidade', (jQuery('#registros_encontrados').data('quantidade') - 1) );

                                    corrigeTabela();

                                    aplicarCorLinha();
                                } else if(data == 'INVALIDO'){
                                    jQuery('#mensagem_alerta').html("Não foi possível excluir este registro, pois existem modelos relacionados a esta marca.");
                                    jQuery('#mensagem_alerta').show();
                                } else {
                                    jQuery('#mensagem_erro').html("Houve um erro no processamento dos dados.");
                                    jQuery('#mensagem_erro').show();
                                }
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

    /**
     * Autocomplete
     */
    jQuery("#modeobroid_autocomplete").autocomplete({
        source: 'cad_modelo_ebs.php?acao=buscarObrigacaoFinanceira',
        minLength: 3,
        response: function(event, ui) {
            jQuery('#modeobroid').val('');
            
            if (!ui.content.length) {
                jQuery('#msg_alerta')
                    .text('Nenhum registro encontrado com o termo: ' + $(this).val())
                    .removeClass('invisivel').fadeIn();
            } else {
                jQuery('#msg_alerta').fadeOut();
            }
        },
        select: function(event, ui ) {
            jQuery('#modeobroid').val(ui.item.id);
        }
    });


    /**
     * Acoes do icone editar
     */
    jQuery("table").on('click','.editar',function(event) {

        event.preventDefault();

        id = jQuery(this).data('modeoid');

        window.location.href = "cad_modelo_ebs.php?acao=editar&modeoid="+id;
    });
   
    //botão novo
    jQuery("#bt_novo").click(function(){
        window.location.href = "cad_modelo_ebs.php?acao=cadastrar";
    });

    //botão novo
    jQuery("#bt_novo_marca").click(function(){
        window.location.href = "cad_modelo_ebs.php?acao=cadastrarMarca";
    });
   
    //botão voltar
    jQuery("#bt_voltar").click(function(){
        window.location.href = "cad_modelo_ebs.php";
    })

    /**
     * Esconde todas as mensagens e Erros
     */
    function esconderMensagens() {

        jQuery('#msg_alerta').hide();
        jQuery('#msg_sucesso').hide();
        jQuery('#msg_erro').hide();

        jQuery('.obrigatorio').removeClass('erro');
    }

    /*
     * Reorganzia as cores das linhas na lista
     */
    function aplicarCorLinha(){

        var cor = '';

        //remove cores
        jQuery('#bloco_itens table tbody tr').removeClass('par');
        jQuery('#bloco_itens table tbody tr').removeClass('impar');

        //aplica cores
        jQuery('#bloco_itens table tbody tr').each(function(){
            cor = (cor == "impar") ? "par" : "impar";
            jQuery(this).addClass(cor);
        });
    }

    /*
     * Corrige informação da quantidade de registros encontrados.
     */
    function corrigeTabela(){

        var qtdLinhas = 0;

        //busca quantidade de linhas
        qtdLinhas = jQuery('#registros_encontrados').data('quantidade');
        jQuery("#registros_encontrados").html("");

        if(qtdLinhas == 0){
            jQuery('.resultado').hide();
        }else if(qtdLinhas == 1){
            jQuery("#registros_encontrados").html("1 registro encontrado.");
        }else{
            jQuery("#registros_encontrados").html(qtdLinhas + " registros encontrados.");
        }
    }

    /*
     * Tratamento somente numeros inteiros, letras e underscore
     */
    jQuery('body').on('keyup blur', '.codigo', function() {
        jQuery(this).val(jQuery(this).val().replace(/[^A-Za-z0-9]/g, ''));
    });
   
});