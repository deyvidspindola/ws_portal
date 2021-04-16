jQuery(document).ready(function(){

    /**
     * Acoes do icone excluir
     */
     jQuery("table").on('click','.excluir',function(event) {

        event.preventDefault();

        id = jQuery(this).data('mhcoid');
        elemento = this;

        jQuery("#mensagem_excluir").dialog({
            title: "Confirmação de Exclusão",
            resizable: false,
            modal: true,
            buttons: {
                "Sim": function() {
                jQuery( this ).dialog( "close" );

                    jQuery.ajax({
                        url: 'cad_acao_os.php',
                        type: 'POST',
                        data: {
                            acao: 'excluir',
                            mhcoid: id
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
     * Esconde todas as mensagens e Erros
     */
    function esconderMensagens() {

        jQuery('#mensagem_alerta').hide();
        jQuery('#mensagem_sucesso').hide();
        jQuery('#mensagem_erro').hide();

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
            jQuery('#mensagem_alerta').html('Nenhum registro encontrado.').show();
        }else if(qtdLinhas == 1){
            jQuery("#registros_encontrados").html("1 registro encontrado.");
        }else{
        	jQuery("#registros_encontrados").html(qtdLinhas + " registros encontrados.");
        }
    }

    /**
     * Ações dos botões de vinculo de Ação vs Departamento
     */
    jQuery('#btnadd').click(function() {
        jQuery('#acoes_nao_vinc option:selected').remove().prependTo('#acoes_vinc');
        jQuery('#acoes_vinc option').prop("selected", false);
        return false;
    });
    jQuery('#btnremove').click(function() {
        jQuery('#acoes_vinc option:selected').remove().prependTo('#acoes_nao_vinc');
        jQuery('#acoes_nao_vinc option').prop("selected", false);
        return false;
    });
    jQuery(document).on('click mouseup', '#acoes_nao_vinc', function () {
        jQuery('#acoes_vinc option').prop("selected", false);
    });
    jQuery(document).on('click mouseup', '#acoes_vinc', function () {
        jQuery('#acoes_nao_vinc option').prop("selected", false);
    });
    jQuery(document).on('click mouseup', 'div.mcombos', function () {
        jQuery('#btnadd').prop( "disabled", (jQuery('#acoes_nao_vinc option:selected').length <= 0) );
        jQuery('#btnremove').prop( "disabled", (jQuery('#acoes_vinc option:selected').length <= 0) );
        return false;
    });

    /**
     * Ações do combo de Departamento
     */
    jQuery('#depoid').change(function() {
        esconderMensagens();
        populaVinculos(this.value);
    });

    // Ao carregar a página carrega os vinculos
    populaVinculos(jQuery('#depoid').val());

    function populaVinculos(depoid) {
        jQuery.getJSON("cad_acao_os.php?acao=pesquisarVinculos&id="+depoid, function(json) {
            // Reseta Vínculos
            jQuery('#btnadd').prop( "disabled", true);
            jQuery('#btnremove').prop( "disabled", true);
            jQuery("#acoes_vinc option, #acoes_nao_vinc option").remove();
            if (jQuery('#depoid').val() == '0') {
                jQuery('#acoes_nao_vinc').prop( "disabled", true);
            } else {
                jQuery('#acoes_nao_vinc').prop( "disabled", false);
            }
            // Popula Combos
            jQuery.each(json.acoes_nao_vinc, function(i,data){   
                $('#acoes_nao_vinc').append($('<option>').text(data.mhcdescricao).attr('value', data.mhcoid));
            });
            jQuery.each(json.acoes_vinc, function(i,data){   
                $('#acoes_vinc').append($('<option>').text(data.mhcdescricao).attr('value', data.mhcoid));
            });
        });
    }

    /**
     * Ação do form ao enviar
     */
    jQuery('#form_cadastrar').submit(function() {
        jQuery('#acoes_nao_vinc option, #acoes_vinc option').prop("selected", true);
    });

    /*
     * Tratamento somente numeros inteiros, letras e underscore
     */
    jQuery('body').on('keyup blur', '.acaoinput', function() {
        jQuery(this).val(jQuery(this).val().replace(/[~'\\%<>]/g, ''));
    });
});