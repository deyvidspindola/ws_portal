jQuery(document).ready(function(){

    // inicia com detalhes
    jQuery(".detalhes").hide()

    // inicia com loaders escondidos
    jQuery(".carregando").hide()

    //ajusta tabela de resultado de pesquisa
    corrigeTabela();
    aplicarCorLinha();
    
    itemOrdemListener();


    /**
     * Filtros
     */
    
    /**
     * Seleciona: Tipo ordem servico    [iesostoid]
     * Popula: Motivo de Ordem Serviço  [iesotioid]
     */
    jQuery("#iesostoid").on('change',function(event) {

        event.preventDefault();

        var iesostoid = jQuery('#iesostoid').val();
        var iesotitipo = jQuery('#iesotitipo').val();
        var iesoid = jQuery('#iesoid').val();

        //limpa combo para novo filtro
        limpaCombo("#iesotioid");

  
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            data: {
                acao: 'getMotivoOrdemServico',
                iesostoid: iesostoid,
                iesotitipo: iesotitipo,
                iesoid: iesoid
            },
            beforeSend: function(){
                jQuery("#form-iesotioid").children( ".carregando" ).show();
            },
            success: function(data) {
                jQuery.each(data, function(index, val) {
                    jQuery('#iesotioid').append(jQuery('<option>', { 
                        value: val.otioid,
                        text : val.otidescricao 
                    }));
                });
            },
            complete: function() {
                jQuery("#form-iesotioid").children( ".carregando" ).delay(800).hide();
            }
        });
    });

    /**
     * Seleciona: Tipo ordem servico    [iesostoid]
     * Popula: Motivo de Ordem Serviço  [iesotioid]
     */
    jQuery("#iesotitipo").on('change',function(event) {

        event.preventDefault();


        $('#iesotitipo').change(function(){
            $('#iesostoid').prop('selectedIndex',0);
        });

    //limpa combo para novo filtro
        limpaCombo("#iesotioid");

 
    });

    /**
     * Seleciona: Classe do Equipamento     [ieseqcoid]
     * Popula: Equipamento                  [ieseproid]
     */
    jQuery("#ieseqcoid").on('change',function(event) {

        event.preventDefault();

        //busca valor do Motivo Ordem Servico
        var ieseqcoid = jQuery(this).val();

        //limpa combos para novo filtro
        limpaCombo("#ieseproid");
        limpaCombo("#ieseveoid");

        jQuery.ajax({
            type: "POST",
            dataType: "json",
            data: {
                acao: 'getEquipamento',
                ieseqcoid: ieseqcoid
            },
            beforeSend: function(){
                jQuery("#form-ieseproid").children( ".carregando" ).show();
            },
            success: function(data) {
                jQuery.each(data, function(index, val) {
                    jQuery('#ieseproid').append(jQuery('<option>', { 
                        value: val.eproid,
                        text : val.eprnome 
                    }));
                });
            },
            complete: function() {
                jQuery("#form-ieseproid").children( ".carregando" ).delay(800).hide();
            }
        });
    });

    /**
     * Seleciona: Equipamento    [ieseproid]
     * Popula: Versão            [ieseveoid]
     */
    jQuery("#ieseproid").on('change',function(event) {

        event.preventDefault();

        //busca valor do Motivo Ordem Servico
        var ieseproid = jQuery(this).val();

        //limpa combo para novo filtro
        limpaCombo("#ieseveoid");

        jQuery.ajax({
            type: "POST",
            dataType: "json",
            data: {
                acao: 'getVersao',
                ieseproid: ieseproid
            },
            beforeSend: function(){
                jQuery("#form-ieseveoid").children( ".carregando" ).show();
            },
            success: function(data) {
                jQuery.each(data, function(index, val) {
                    jQuery('#ieseveoid').append(jQuery('<option>', { 
                        value: val.eveoid,
                        text : val.eveversao 
                    }));
                });
            },
            complete: function() {
                jQuery("#form-ieseveoid").children( ".carregando" ).delay(800).hide();
            }
        });
    });

    /**
     * Seleciona: Marca do Veículo    [iesmcaoid]
     * Popula: Modelo do Veículo      [iesmlooid]
     */
    jQuery("#iesmcaoid").on('change',function(event) {

        event.preventDefault();

        //busca valor do Motivo Ordem Servico
        var iesmcaoid = jQuery(this).val();

        //limpa combo para novo filtro
        limpaCombo("#iesmlooid");

        jQuery.ajax({
            type: "POST",
            dataType: "json",
            data: {
                acao: 'getModeloVeiculo',
                iesmcaoid: iesmcaoid
            },
            beforeSend: function(){
                jQuery("#form-iesmlooid").children( ".carregando" ).show();
            },
            success: function(data) {
                jQuery.each(data, function(index, val) {
                    jQuery('#iesmlooid').append(jQuery('<option>', { 
                        value: val.mlooid,
                        text : val.mlomodelo 
                    }));
                });
            },
            complete: function() {
                jQuery("#form-iesmlooid").children( ".carregando" ).delay(800).hide();
            }
        });
    });

    /**
     * FIM - Filtros
     */
    
    /**
     * Acoes do icone excluir
     */
     jQuery("table").on('click','.excluir',function(event) {

        event.preventDefault();

        id = jQuery(this).data('iesoid');

        jQuery("#mensagem_excluir").dialog({
            title: "Confirmação de Exclusão",
            resizable: false,
            modal: true,
            buttons: {
                "Sim": function() {
                jQuery( this ).dialog( "close" );

                    jQuery.ajax({
                        url: 'cad_itens_essenciais.php',
                        type: 'POST',
                        data: {
                            acao: 'excluir',
                            iesoid: id
                        },
                        success: function(data) {

                            if(data) {
                                esconderMensagens();

                                if(data == 'OK') {
                                    jQuery('#mensagem_sucesso').html("Registro excluído com sucesso.");
                                    jQuery('#mensagem_sucesso').show();
                                    jQuery("#linha_" + id).remove();
                                    jQuery("#det_" + id).remove();

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

   
    //botão pesquisar
    jQuery("#bt_pesquisar").click(function(){
        jQuery( "#acao" ).val("pesquisar");
        jQuery( "#form" ).submit();
    })

    /**
     * Acoes do icone editar
     */
    jQuery("table").on('click','.editar',function(event) {

        event.preventDefault();

        id = jQuery(this).data('iesoid');

        window.location.href = "cad_itens_essenciais.php?acao=editar&iesoid="+id;
    });

    //botão novo
    jQuery("#bt_novo").click(function(){
        window.location.href = "cad_itens_essenciais.php?acao=cadastrar";
    });


    //botão detalhes
    jQuery(".bt_detalhes").click(function(event){

        event.preventDefault();

        var id_item = jQuery(this).attr('item-id');

        jQuery("#det_" + id_item).toggle(0, function() {

            jQuery(".mais-menos_" + id_item).toggle();

        });
    });
   

    /**
     * Tela cadastro / edicao
     */
    
    //botão salvar
    jQuery("#bt_salvar").click(function(){
        jQuery( "#form_cadastrar" ).submit();
    });

    //botão importar
    jQuery("#bt_importar").click(function(){
        jQuery( "#acao" ).val("importar");
        jQuery( "#form_cadastrar" ).submit();
    });

    //botão voltar
    jQuery("#bt_voltar").click(function(){
        window.location.href = "cad_itens_essenciais.php";
    })

    //botão voltar
    jQuery("#bt_adicionar").click(function(){

        
        jQuery('.adicionado').show();

        //percorre itens selecionados
        jQuery('#iespprdoid option:selected').each(function(){ 

            jQuery('#iespprdoid option[value=' + jQuery(this).val() + ']').attr('disabled','disabled');

            jQuery('#itens_adicionados tbody').prepend('<tr id="id_' + jQuery(this).val() + '" class="linha"><td> ' + jQuery(this).text() + ' </td><td class="centro"><input id="item_' + jQuery(this).val() + '" name="item_' + jQuery(this).val() + '" type="text" value="' + jQuery('.quantidade').val() + '" maxlength="2" size="1" class="quantidade"></td><td class="acao centro"><a title="Excluir" class="excluir-item" data-iespprdoid="' + jQuery(this).val() + '" href="#"><img class="icone" src="images/icon_error.png"  alt="Excluir"></a></td></tr>');

            //limpa valor para não duplicar ao adicionar
            jQuery(this).removeAttr("selected");
        });

        corrigeTabela();
        aplicarCorLinha();
    })

    
    jQuery("table").on('click','.excluir-item',function(event) {

        event.preventDefault();

        id = jQuery(this).attr('data-iespprdoid');
        elemento = this;

        jQuery("#mensagem_excluir").dialog({
            title: "Confirmação de Exclusão",
            resizable: false,
            modal: true,
            buttons: {
                "Sim": function() {
                    jQuery( this ).dialog( "close" );

                    //remove item da tabela
                    jQuery("#id_"+id).remove();

                    //volta item para seleção
                    jQuery('#iespprdoid option[value=' + id + ']').removeAttr('disabled');

                    corrigeTabela();
                    aplicarCorLinha();
                },
                "Não": function() {
                    jQuery( this ).dialog( "close" );
                }
            }
        });
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


    /**
     * Reorganzia as cores das linhas na lista
     */
    function aplicarCorLinha(){

        var cor = '';

        //remove cores
        jQuery('#bloco_itens table tbody .linha').removeClass('par');
        jQuery('#bloco_itens table tbody .linha').removeClass('impar');

        //aplica cores
        jQuery('#bloco_itens table tbody .linha').each(function(){
            cor = (cor == "impar") ? "par" : "impar";
            jQuery(this).addClass(cor);
        });


        jQuery('.tabela-itens tr').removeClass('par');
        jQuery('.tabela-itens tr').removeClass('impar');

        jQuery('.tabela-itens tr').each(function(){
            cor = (cor == "impar") ? "par" : "impar";
            jQuery(this).addClass(cor);
        });
    }


    /**
     * Corrige informação da quantidade de registros encontrados.
     */
    function corrigeTabela(){

        var qtdLinhas = 0;

        //busca quantidade de linhas
        qtdLinhas = jQuery('#bloco_itens table tbody .linha').length;

        if(qtdLinhas == 0){
            jQuery('.resultado').hide();
            jQuery('.adicionado').hide();
        }
    }


    /**
     * Tratamento somente numeros inteiros, letras e underscore
     */
    jQuery('body').on('keyup blur', '.codigo', function() {
        jQuery(this).val(jQuery(this).val().replace(/[^A-Za-z0-9]/g, ''));
    });


    /**
     * Tratamento somente numeros inteiros
     */
    jQuery('body').on('keyup blur', '.quantidade', function() {
        jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));

        //atualiza quantidade de todos os itens
        jQuery('.quantidade').val(jQuery(this).val());

    });

    jQuery('body').on('blur', '.quantidade', function() {
        if (jQuery(this).val() <= 0) {
            jQuery(this).val(1);
        };

        //atualiza quantidade de todos os itens
        jQuery('.quantidade').val(jQuery(this).val());
    });


    /**
     * Limpa Combos
     */
    function limpaCombo(combo){

        jQuery(combo+" option").remove();

        jQuery(combo).append(jQuery('<option>', { 
            value: "",
            text : "Escolha"
        }));
    }
});


function itemOrdemListener() {
    jQuery("#iesotitipo").on('change',function(event) {
        if(jQuery("#iesotitipo").val().length == 0) {
            jQuery("#iesostoid").val('');
        }
    });
}