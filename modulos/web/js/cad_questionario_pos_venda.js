function isNumber (o) {
  return ! isNaN (o-0) && o !== null && o.replace(/^\s\s*/, '') !== "" && o !== false;
}

$(document).ready(function(){	
    //verificação de peso minimo 0, máximo 100
    jQuery(".peso").live("keyup", function() {
        var valor = jQuery(this).val();
        if(!isNumber(valor))
        {
            jQuery(this).val('');
        } else if (valor > 100)
        {
            jQuery(this).val('100');
            jQuery(this).parent().children('.pesoerro').html('Peso máximo é 100').show().delay(1000).queue(function(n) {
                $(this).hide(); n();
            });

        } else if (valor < 0) {
            $(this).val('0');
            jQuery(this).parent().children('.pesoerro').html('Peso mínimo é 0');
        }
        
    }); 
    //verificação ordem deve ser numerico
    jQuery(".pqiitem_ordem").live("keyup", function() {
        var valor = jQuery(this).val();
        if(!isNumber(valor))
        {
            jQuery(this).val('');
        }
        
    });	
    //clique para aparecer o formulario de questões
    jQuery(".mostra_questao").live("click", function() {
        var questionario = 'cad_questionario_pos_venda.php';
        var posicionar = jQuery(this).parent();
        var questaoid = jQuery(this).attr('questaoid');
        var questionarioid = jQuery(this).attr('questionarioid');
        jQuery.post(questionario, { questionario: "padrao", questaoid: questaoid, questionarioid: questionarioid}, function(data) {
            jQuery(posicionar).append(data);
            
        });
        jQuery(this).hide();
    });

    //clique para aparecer o formulario de edição de questão
    jQuery(".mostra_altera_questao").live("click", function() {
        var questionario = 'cad_questionario_pos_venda.php';
        var posicionar = jQuery("#"+jQuery(this).attr('bloco'));
        var questionarioid = jQuery(this).attr('questionarioid');
        var pqioid = jQuery(this).attr('pqioid');
        var tipo = jQuery(this).attr('tipo');
        var questaoid = jQuery(this).attr('questaoid');
        var tabela = jQuery(this).closest('table');
        jQuery.post(questionario, { questionario: tipo, pqioid: pqioid, questionarioid: questionarioid, questaoid: questaoid}, function(data) {
            jQuery(posicionar).append(data);
            
        });
        jQuery(tabela).detach();
    });

    //cancelar entrada de nova questão
    jQuery(".cancelar_questao").live("click", function() {

        jQuery(this).closest('.adicionar_questao').children('.mostra_questao').show();
        jQuery(this).closest('.padrao').detach();

    });

    //cancelar alteração de questão
    jQuery(".cancelar_altera").live("click", function() {
        var questionario = 'cad_questionario_pos_venda.php';
        var questaoid = jQuery(this).closest('.padrao').find(".questaoid").val();
        jQuery.post(questionario, {
                        geraComposicaoAssincrona : 'sim',
                        questaoid: questaoid
                    }, function(data) {
                        jQuery('#composicao').html(data);
                    });

    });


    //trigger para desabilitar o Serviço Básico dependendo do Tipo da Pesquisa
    jQuery(".psvtipo").live("change", function() {
        var vinculoservico = jQuery(':selected', jQuery(this)).attr("vinculoservico");
        
        if (vinculoservico == 'f') {
            jQuery(".psveqcoid").val('').attr('disabled', 'disabled'); 
        } else {
            jQuery(".psveqcoid").removeAttr('disabled'); 
        }
        
    });

    //verifica o tipo da pesquisa que vem do $_POST
    if (jQuery(':selected', jQuery(jQuery(".psvtipo"))).attr("vinculoservico") == 'f') {
        jQuery(".psveqcoid").val('').attr('disabled', 'disabled'); 
    }

    //trigger para exibir informações conforme o tipo item
    jQuery(".pqitipo_item").live("change", function() {
        var elemento = jQuery(this).closest('.padrao');
        var todosTipos = ["padrao", "radio", "checkbox", "select"];
        var tipo = jQuery(':selected', jQuery(this)).attr("tipo");
        var peso = jQuery(':selected', jQuery(this)).attr("peso");
        
        jQuery(todosTipos).each(function() {
            if (this == tipo)
            {
               jQuery(elemento).find('.questelemento-' + this).show(); 

            } else {
                jQuery(elemento).find('.questelemento-' + this).hide(); 
            }
        });

        if (tipo != 'padrao') {
            jQuery(elemento).find('.addcampoelement').show(); 
        } else {
            jQuery(elemento).find('.addcampoelement').hide(); 
        }
        if (peso == 'sempeso') {
            jQuery(elemento).find('.pqiavalia_representante').attr('disabled', 'disabled').removeAttr('checked');
            jQuery(elemento).find('.peso').attr('disabled', 'disabled').val('0'); 
        } else {
            jQuery(elemento).find('.pqiavalia_representante').removeAttr('disabled'); 
            jQuery(elemento).find('.peso').removeAttr('disabled').val(''); 
        }
        
    });
    //adiciona inputs do tipo radio até no máximo 10
    jQuery(".addradio").live("click", function() {
        var novoVal = parseInt(jQuery(this).closest('.questelemento-radio').children('.numradio').val());
        if (novoVal < 10)
        {
            novoVal++;
            jQuery(this).closest('.questelemento-radio').children('.numradio').val(novoVal);
            jQuery(this).closest('.questelemento-radio').find('.lista_radios').append('<li>'+novoVal+'<br/><input type="radio" name="mostraradio"></li>');
        }
    });
    //adiciona inputs do tipo check
    jQuery(".addcheck").live("click", function() {
        var novoTexto = jQuery.trim(jQuery(this).closest('.questelemento-checkbox').children('.textocheck').val());
            if(novoTexto != '') {               
                // textosAtuais = jQuery(this).closest('.questelemento-checkbox').find('.numcheck').serializeArray();
                var ocorrenciaTexto = jQuery(this).closest('.questelemento-checkbox').find('.numcheck[value="'+novoTexto+'"]').length;
                if (ocorrenciaTexto == 0) {
                    jQuery(this).closest('.questelemento-checkbox').append('<input type="hidden" class="numcheck" name="numcheck[]" value="'+novoTexto+'">');
                    jQuery(this).closest('.questelemento-checkbox').find('.lista_checks').append('<li><input type="checkbox" name="mostracheck">'+novoTexto+'</li>');    
                    jQuery(this).closest('.questelemento-checkbox').children('.textocheck').val('');
                } else {
                    alert('Essa opção já existe');
                }
            } else {
                alert('Texto não pode ser nulo');
            }
            
        });
    //adiciona inputs do tipo select
    jQuery(".addselect").live("click", function() {
        var novoTexto = jQuery.trim(jQuery(this).closest('.questelemento-select').children('.textoselect').val());
            if(novoTexto != '') {               
                var ocorrenciaTexto = jQuery(this).closest('.questelemento-select').find('.numselect[value="'+novoTexto+'"]').length;
                if (ocorrenciaTexto == 0) {
                    jQuery(this).closest('.questelemento-select').append('<input type="hidden" class="numselect" name="numselect[]" value="'+novoTexto+'">');
                    jQuery(this).closest('.questelemento-select').find('.lista_selects').append('<option value="">'+novoTexto+'</option>');  
                    var ocorrencias = jQuery(this).closest('.questelemento-select').find(".numselect").length;
                    jQuery(this).closest('.questelemento-select').find('.lista_selects').attr('size', ocorrencias);
                    jQuery(this).closest('.questelemento-select').children('.textoselect').val('');
                } else {
                    alert('Essa opção já existe');
                }
            } else {
                alert('Texto não pode ser nulo');
            }
            
        });
    //Salvar questão!
    jQuery(".salvar_questao").live("click", function() {

        //pega o tipo do item selecionado
        var tipo = jQuery(':selected', jQuery(jQuery(this).closest('.padrao').find(".pqitipo_item"))).attr("tipo");
        var tipoItem = jQuery(this).closest('.padrao').find(".pqitipo_item").val();
        var tipoOcorrencia = jQuery(this).closest('.padrao').find(".pqirhtoid").val();
        var descricao = jQuery(this).closest('.padrao').find(".pqiitem_topico").val();
        var peso = jQuery(this).closest('.padrao').find(".peso").val();
        var questaoid = jQuery(this).closest('.padrao').find(".questaoid").val();
        var questionarioid = jQuery(this).closest('.padrao').find(".questionarioid").val();
        var pqiitem_ordem = jQuery(this).closest('.padrao').find(".pqiitem_ordem").val();
        var alertas = jQuery(this).closest('.padrao').find(".alertas");
        var questionario = 'cad_questionario_pos_venda.php';
        // Seta variáveis diretamente dependentes do tipo como false, para não passar seu valor por post
        var arquivo = '';
        var descricaoOcorrencia = '';
        var avaliarRepresentante = 'FALSE';
        var numcampos = '';
        var pqioid = '';

        pqioid = jQuery(this).closest('.padrao').find(".pqioid").val();
        
        if (tipo == 'padrao') {
            arquivo = jQuery(this).closest('.padrao').find(".arquivo").val();
            descricaoOcorrencia = jQuery(this).closest('.padrao').find(".pqidescricao_ocorrencia").val();
            avaliarRepresentante = jQuery(this).closest('.padrao').find(".pqiavalia_representante:checked").length;
            if (avaliarRepresentante > 0)
            {
                avaliarRepresentante = 'TRUE';
            } else {
                avaliarRepresentante = 'FALSE';
            }
        } else if (tipo == 'radio') {
            numcampos = jQuery(this).closest('.padrao').find(".numradio").val();
        } else if (tipo == 'checkbox') {
            numcampos = jQuery(this).closest('.padrao').find(".numcheck").map(function() {
                return $(this).val();
            }).get();
        } else if (tipo == 'select') {
            numcampos = jQuery(this).closest('.padrao').find(".numselect").map(function() {
                return $(this).val();
            }).get();
        }
            jQuery.post(questionario, {
                salvaQuestionario: tipo,
                pqitipo_item: tipoItem,
                pqirhtoid: tipoOcorrencia,
                pqiitem_topico: descricao,
                pqipeso: peso,
                pqidescricao_ocorrencia: descricaoOcorrencia,
                pqiavalia_representante: avaliarRepresentante,
                questaoid: questaoid,
                questionarioid: questionarioid,
                pqiitem_ordem: pqiitem_ordem,
                arquivo : arquivo,
                numcampos : numcampos,
                pqioid : pqioid
            }, function(data) {
                var resposta = $.parseJSON(data);
                if (resposta.mensagem != '' && resposta.status != 'SUCESSO')
                {
                    alertas.fadeOut('fast', function () {
                        jQuery(this).text(resposta.mensagem).fadeIn('fast');
                    })
                } else {
                    alertas.text('');
                }
                
                if (resposta.status == 'SUCESSO'){
                    jQuery('#psvstatus').html(resposta.comboStatus);
                    jQuery.post(questionario, {
                        geraComposicaoAssincrona : 'sim',
                        questaoid: questaoid,
                        questionarioid: questionarioid,
                        mensagem : resposta.mensagem
                    }, function(data) {
                       jQuery('#composicao').html(data);
                       jQuery('.mensagem').delay(5000).queue(function(n) {
                            $(this).hide(); n();
                       });
                    });
                }
            });
    });
});