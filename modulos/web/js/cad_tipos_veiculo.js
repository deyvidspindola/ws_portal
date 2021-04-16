jQuery(document).ready(function(){

    jQuery("#tipvdescricao").live('change keypress blur keyup',function() {
        jQuery("#tipvdescricao").val(jQuery("#tipvdescricao").val().replace(/[^a-zA-Z0-9 ]/gi, '').replace(/[_]/g, '-'))
    });

    /**
     * Acoes do icone excluir
     */
     jQuery("table").on('click','.excluir',function(event) {

        event.preventDefault();

        id = jQuery(this).data('tipvoid');
        elemento = this;

        jQuery("#mensagem_excluir").dialog({
            title: "Confirmação de Exclusão",
            resizable: false,
            modal: true,
            buttons: {
                "Sim": function() {
                jQuery( this ).dialog( "close" );

                    jQuery.ajax({
                        url: 'cad_tipos_veiculo.php',
                        type: 'POST',
                        data: {
                            acao: 'excluir',
                            tipvoid: id
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
     * Acoes do icone editar
     */
    jQuery("table").on('click','.editar',function(event) {

        event.preventDefault();

        id = jQuery(this).data('tipvoid');

        window.location.href = "cad_tipos_veiculo.php?acao=editar&tipvoid="+id;
    });
   
    //botão novo
    jQuery("#bt_novo").click(function(){
        window.location.href = "cad_tipos_veiculo.php?acao=cadastrar";
    });
   
    //botão voltar
    jQuery("#bt_voltar").click(function(){
        window.location.href = "cad_tipos_veiculo.php";
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
        qtdLinhas = jQuery('#bloco_itens table tbody tr').length;
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