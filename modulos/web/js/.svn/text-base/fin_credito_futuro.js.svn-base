var timer = null;
var timer_verifica_textarea = null;
jQuery(document).ready(function(){

    timer_verifica_textarea = setInterval(function(){

        jQuery("textarea").each(function(i,o){
            var valor = jQuery(o).val();
            if (valor.length > 500) {
                jQuery(o).val(valor.substr(0,500));
            }
        });
    },100);

    jQuery("textarea").keypress(function(e){
        var lengthF = jQuery(this).val();

        if (lengthF.length > 499){
            e.preventDefault();
        }
    });

    jQuery("textarea").on('paste', function(e) {
        var lengthF = jQuery(this).val();

        if (lengthF.length > 499){
            e.preventDefault();
            jQuery(this).val(lengthF.substr(0,500));
        }
    });
    jQuery("textarea").on('change', function(e) { 
        var lengthF = jQuery(this).val();
        
        if (lengthF.length > 499){
            e.preventDefault();
            jQuery(this).val(lengthF.substr(0,500));
        }
    });


    jQuery("#tipo_desconto_valor label").addClass('naoValidar');
    
    //para corrigir modal no modo quirks
    if(navigator.appName.indexOf('Internet Explorer') != -1 && document.compatMode == 'BackCompat') {

        var windowHeight = jQuery(document).height();

        jQuery('body').after('<style>\n\
                                button.ui-dialog-titlebar-close{\n\
                                    height: 2px !important !important; \n\
                                    top: 13px !important; \n\
                                    right: 3px !important; \n\
                                    padding: 0px !important; \n\
                                } \n\
                                .ui-dialog-titlebar .ui-dialog-titlebar-close {\n\
                                    height: 1px !important;\n\
                                }\n\
                                .ui-button-icon-only .ui-button-text, .ui-button-icons-only .ui-button-text{\n\\n\\n\
                                    padding: 0px !important; \n\\n\
                                    height: 0px !important; \n\
                                }\n\
                                .ui-widget-overlay{\n\
                                    position: absolute !important; \n\
                                    height: ' + windowHeight + 'px !important\n\
                                }\n\</style>');
    }
    
    //edição    
    if (bloqueia_campos_edicao == true) {
        jQuery("#formulario_editar input, #formulario_editar radio, #formulario_editar textarea, #formulario_editar select").attr('disabled','disabled');
        jQuery("#bt_concluir").parent().remove();
    }
    
    //botão encerrar credito futuro    
    jQuery("#bt_encerrar").click(function(){
     
        
        jQuery.fn.limpaMensagens();
        jQuery("#justificativa_encerramento").val('');
       
        jQuery("#dialog-encerrar-credito-futuro").dialog({
            autoOpen: false,
            minHeight: 300 ,
            maxHeight: 700 ,
            width: 440,
            modal: true,
            create: function (event, ui) {
               
                if(navigator.appName.indexOf('Internet Explorer') != -1 && document.compatMode == 'BackCompat') {                            
                    jQuery("#dialog-encerrar-credito-futuro").prev().css('width','455px');
                    jQuery(".ui-dialog-titlebar-close .ui-button-text").remove();
                                jQuery(".ui-dialog-titlebar-close").css('width','20px');
                                jQuery(".ui-dialog-titlebar-close").css('height','20px');
                }
                            
            },
            buttons: {
                "Confirmar": function() {
                    
                    jQuery.fn.limpaMensagens();
                    
                    var creditoFuturoId = jQuery.trim(jQuery("#cfooid").val());
                    var justificativa = jQuery.trim(jQuery("#justificativa_encerramento").val());
                    
                    if (justificativa == '') {
                        jQuery("#encerrar_mensagem").html('<div class="mensagem alerta">A justificativa é uma informação obrigatória.</div>')
                        setErrors('justificativa_encerramento');
                        showFormErros(dados);
                        return false;
                    }
                    
                    jQuery("#form_encerrar").submit();
                    
                    
                },
                "Cancelar": function() {
                    jQuery("#dialog-encerrar-credito-futuro").dialog('close');
                }
            },
            "Cancelar": function() {
                jQuery("#dialog-encerrar-credito-futuro").dialog('close');
            }
        }).dialog('open');
        
    });
    
    //botão excluir credito futuro
    jQuery("#bt_excluir, .bt_excluir, #bt_excluir_massa").click(function(){
       
        
        if (jQuery(this).hasClass('bt_excluir') && jQuery(this).hasClass('excluir_listagem')) {
            jQuery("form#form_excluir input[name='cfooid']").val(jQuery(this).attr('data-cfooid'));
            
        }
       
        
        var excluirMassa = false;
       
        if (jQuery(this).attr('id') == 'bt_excluir_massa') {
            excluirMassa = true;
        }
       
       
        jQuery.fn.limpaMensagens();
        jQuery("#justificativa_exclusao").val('');
       
        jQuery("#dialog-excluir-credito-futuro").dialog({
            autoOpen: false,
            minHeight: 300 ,
            maxHeight: 700 ,
            width: 440,
            modal: true,
            create: function (event, ui) {
                if(navigator.appName.indexOf('Internet Explorer') != -1 && document.compatMode == 'BackCompat') {                            
                    jQuery("#dialog-excluir-credito-futuro").prev().css('width','455px');
                    jQuery(".ui-dialog-titlebar-close .ui-button-text").remove();
                                jQuery(".ui-dialog-titlebar-close").css('width','20px');
                                jQuery(".ui-dialog-titlebar-close").css('height','20px');
                }
                            
            },
            buttons: {
                "Confirmar": function() {
                    
                    jQuery.fn.limpaMensagens();
                    
                    var creditoFuturoId = jQuery.trim(jQuery("#cfooid").val());
                    var justificativa = jQuery.trim(jQuery("#justificativa_exclusao").val());
                    
                    if (justificativa == '') {
                        jQuery("#excluir_mensagem").html('<div class="mensagem alerta">A justificativa é uma informação obrigatória.</div>')
                        setErrors('justificativa_exclusao');
                        showFormErros(dados);
                        return false;
                    }
                    
                    if (!excluirMassa) {
                        jQuery("#form_excluir").submit();
                    } else {
                        jQuery("form#form_listagem_pesquisa").append('<input type="hidden" name="justificativa" value="' + jQuery("#justificativa_exclusao").val() + '" >')
                        jQuery("form#form_listagem_pesquisa input[name='acao']").val('excluirMassa');
                        jQuery("form#form_listagem_pesquisa").submit();
                    }
                    
                    
                },
                "Cancelar": function() {
                    jQuery("#dialog-excluir-credito-futuro").dialog('close');
                }
            },
            "Cancelar": function() {
                jQuery("#dialog-excluir-credito-futuro").dialog('close');
            }
        }).dialog('open');
       
    });
    
    jQuery(".detalhes_historico").click(function(){
        
        var itemHistoricoId = jQuery(this).attr('data-cfhoid');
        
        jQuery(".detalhe_historico_valor").html('');
        
        if (itemHistoricoId != '') {
        
            jQuery.ajax({
                url: 'fin_credito_futuro.php',
                type: 'POST',
                data: {
                    acao: 'buscarHistoricoPorId',
                    cfhoid: itemHistoricoId
                },
                success: function(data) {
                   
                    if (typeof JSON != 'undefined') {
                        data = JSON.parse(data);
                    } else {
                        data = eval('(' + data + ')');
                    }
                    
                    for(var i in data) {
                        jQuery(".historico_detalhe_" + i).html(data[i]);
                    }
                    
                    jQuery("#dialog-detalhes-historico-credito-futuro").dialog({
                        autoOpen: false,
                        width: 500,
                        modal: true,
                        create: function (event, ui) {
                            if(navigator.appName.indexOf('Internet Explorer') != -1 && document.compatMode == 'BackCompat') {                            
                                jQuery("#dialog-detalhes-historico-credito-futuro").prev().css('width','500px');
                                jQuery(".ui-dialog-titlebar-close .ui-button-text").remove();
                                jQuery(".ui-dialog-titlebar-close").css('width','20px');
                                jQuery(".ui-dialog-titlebar-close").css('height','20px');
                                
                            }
                            
                        },
                        buttons: {
                            "Fechar": function() {
                                jQuery("#dialog-detalhes-historico-credito-futuro").dialog('close');
                            }
                        },
                        "Fechar": function() {
                            jQuery("#dialog-detalhes-historico-credito-futuro").dialog('close');
                        }
                    }).dialog('open');
                   
                }
            });
        
        }
       
    });
    
    
    //cadastro - pesquisa
    habilitaDesabilitaAvancarStep1();
    if (step3 != 0) {      
        
        var campo = '';
        
        if (tipoMotivoCredito == 1) {
            validarCamposStep3Contestacao(campo,'0');
        } else if (tipoMotivoCredito == 2) {
            validarCamposStep3Indicacao(campo,'0');
        } else if (tipoMotivoCredito == 3) {
            validarCamposStep3Insencao(campo,'0');
        } else {
            validarCamposStep3Default(campo,'0');
        }
        
        if (post_realizado_step_3) {
            jQuery('#bt_avancar').removeAttr('disabled');
        } else {
            
        }
        
        jQuery("#form_cadastrar input, #form_cadastrar textarea, #form_cadastrar select, #form_cadastrar radio, #form_cadastrar checkbox").on('focusout',function(){
         
               

            var campo = jQuery(this);
         
            timer = setTimeout(function(){
                if (tipoMotivoCredito == 1) {
                    validarCamposStep3Contestacao(campo,'1');
                } else if (tipoMotivoCredito == 2) {
                    validarCamposStep3Indicacao(campo,'1');
                } else if (tipoMotivoCredito == 3) {
                    validarCamposStep3Insencao(campo,'1');
                } else {
                    validarCamposStep3Default(campo,'1');
                }
            },300);
         
        });
        
        jQuery("#form_cadastrar select, #form_cadastrar radio, #form_cadastrar checkbox").on('change',function(){
            
            var campo = jQuery(this);
            
            timer = setTimeout(function(){
                if (tipoMotivoCredito == 1) {
                    validarCamposStep3Contestacao(campo,'1');
                } else if (tipoMotivoCredito == 2) {
                    validarCamposStep3Indicacao(campo,'1');
                } else if (tipoMotivoCredito == 3) {
                    validarCamposStep3Insencao(campo,'1');
                } else {
                    validarCamposStep3Default(campo,'1');
                }
            },300)
         
        });
    }
    
    
    jQuery("#cfoancoid, #cfocfcpoid, #cfooid").mask('9?999999999',{
        placeholder:""
    });
    
    jQuery("#cfoconnum_indicado, #contrato").mask('9?999999999',{
        placeholder:""
    });
    
    jQuery('.campo_parcela').mask('9?99',{
        placeholder:""
    });
    
    //botões de navegação
    
    jQuery("#bt_concluir").click(function(){
        jQuery("#formulario_editar").submit();
    });
    
    jQuery("#bt_novo").click(function(){
        window.location.href = "fin_credito_futuro.php?acao=cadastrar";
    });
    
    //retornar
    jQuery('#bt_retornar').click(function(){
        window.location.href = "fin_credito_futuro.php?acao=pesquisar";
    });
    
    //avançar - submit
    jQuery("#bt_avancar").click(function(){
        
        if (jQuery(this).hasClass('salvar')) {
            
            if (confirm('Deseja incluir o registro?')) {
                jQuery('#form_cadastrar').submit();
            } else {
                return false;
            }
            
        } else {
            jQuery('#form_cadastrar').submit();
        }
        
        
    });
    
    //tratamento abas
    jQuery('ul.bloco_opcoes li').mousemove(function(){
        jQuery(this).removeAttr('style');
    })
    
    jQuery('ul.bloco_opcoes li').each(function(){
        if (!jQuery(this).hasClass('ativo')  && !jQuery(this).hasClass('voltar_aba')) {
            jQuery(this).addClass('inativo nohover');
        }
    });
    
    //voltar    
    jQuery('.voltar_aba').click(function(){
        var step = jQuery(this).attr('id');
         
        if (step == 'aba_1') {
            jQuery("#step").val('');
        }
         
        if (step == 'aba_2') {
            jQuery("#step").val('step_1');
        }
         
        jQuery("#voltar").val('1');
       
        jQuery('#form_cadastrar').submit();
         
    });
    
    jQuery("#bt_voltar").click(function(){
        
        
        
        if (jQuery(this).hasClass('voltar_ajax_step_2')) {
            
            jQuery.fn.limpaMensagens();
            jQuery('#bt_avancar').attr('disabled','disabled');
           
            jQuery.ajax({
                type: 'GET',
                url: 'fin_credito_futuro.php?acao=carregarhtml&conteudo=default_formulario_cadastro_step_2',
                success: function (data) {
                    jQuery('#conteudo_step_2').html(data);
                    jQuery('#motivo_credito_id').val(motivoCredito);
                    jQuery('#tipo_motivo').val(tipoMotivo);
                
                    jQuery('#protocolo, #contrato_indicado').mask('9?999999999',{
                        placeholder:""
                    });
                
                    jQuery("#bt_voltar").removeClass('voltar_ajax_step_2');
                }
            });
           
            return;
        } 
        
        
        var step = jQuery("#step").val();       
        if (step == 'step_2') {
            jQuery("#step").val('');
        }
       
        if (step == 'step_3') {
            jQuery("#step").val('step_1');
        }
       
        jQuery("#voltar").val('1');
       
        jQuery('#form_cadastrar').submit();
       
    });
    
    
    /**************************************************/
    
    jQuery('.cfoforma_aplicacao').change(function(){
        if (jQuery(this).val() == 2) {
            jQuery('.valor_parcela, .valor_parcela label, .valor_parcela input').removeClass('invisivel');
            jQuery('.valor_parcela input').val('1');
            jQuery('.espaco_falso').css('display','none');
        } else {
            jQuery('.valor_parcela, .valor_parcela label, .valor_parcela input').addClass('invisivel');
            jQuery('.valor_parcela input').val('1');
            jQuery('.espaco_falso').css('display','block');
        }
    });
    
    
    jQuery(".tipo_desconto_cadastro").change(function(){
       
        /**
        *Desconto (%) * / Valor (R$) *
        */
        clearTimeout(timer);

        jQuery("#bt_avancar").attr({
            disabled: 'disabled'
        });

        jQuery("div.alerta").remove();
        jQuery("#tipo_desconto_valor label, #tipo_desconto_valor input").removeClass('erro');
       
        //se tipo desconto for percentual
        if (jQuery(this).val() == '1') {           
            jQuery("#tipo_desconto_valor label").html('Desconto (%) *');
            jQuery("#tipo_desconto_valor input").attr('maxlength','6');
            jQuery("#tipo_desconto_valor input").addClass('isPercent');
            jQuery("#tipo_desconto_valor input").removeClass('isMoney');
           
        } else {
            //se tipo desconto for valor
            jQuery("#tipo_desconto_valor label").html('Valor Parcela (R$) *');
            jQuery("#tipo_desconto_valor input").attr('maxlength','12');
            jQuery("#tipo_desconto_valor input").addClass('isMoney');
            jQuery("#tipo_desconto_valor input").removeClass('isPercent');
        }
        
        jQuery("#tipo_desconto_valor input").val('');
       
    });
    
    /**************************************************/
    
    
    // step 2
    
    var motivoCredito = '';
    var tipoMotivo = '';
    var motivoDescricao = '';
    
    /**
     * Na escolha de motivos de créditos com tipos diferentes de "Contestação de crédito"
     * e "Indicação de amigo", executa o trecho abaixo
     */
    
    jQuery('body').delegate('.motivoSemParametrizacao','click',function(){
        
        jQuery.fn.limpaMensagens();
        jQuery('#bt_avancar').attr('disabled','disabled');
        
        motivoCredito = jQuery(this).attr('data-cfmcoid');
        tipoMotivo = jQuery(this).attr('data-tipo');
        motivoDescricao = jQuery(this).attr('data-descricao');
        
        jQuery('#motivo_credito_id').val(motivoCredito);
        jQuery('#tipo_motivo').val(tipoMotivo);
        jQuery('#motivo_descricao').val(motivoDescricao);
        
        jQuery("#form_cadastrar").submit();
    });
    
    
    /**
     * na escolha de motivos de créditos do tipo contestação de crédito ou indicação de amigo
     * executa o trecho abaixo
     */
    jQuery('body').delegate('.contestacao_contas, .indicacao_amigo','click',function(){
        
        
        jQuery.fn.limpaMensagens();
        jQuery('#bt_avancar').removeClass('invisivel');
        jQuery('#bt_avancar').attr('disabled','disabled');
        
        motivoCredito = jQuery(this).attr('data-cfmcoid');
        tipoMotivo = jQuery(this).attr('data-tipo');
        motivoDescricao = jQuery(this).attr('data-descricao');
        
        var conteudo = '';
        
        if (jQuery(this).hasClass('contestacao_contas')) {
            conteudo = 'contestacao_formulario_cadastro_step_2';
        }
        
        if (jQuery(this).hasClass('indicacao_amigo')) {
            conteudo = 'indicacao_formulario_cadastro_step_2';
        }
        
        if (conteudo == '') {
            return false;
        }
        
        jQuery.ajax({
            type: 'GET',
            url: 'fin_credito_futuro.php?acao=carregarhtml&conteudo=' + conteudo,
            success: function (data) {
                jQuery('#conteudo_step_2').html(data);
                jQuery('#motivo_credito_id').val(motivoCredito);
                jQuery('#tipo_motivo').val(tipoMotivo);
                jQuery('#motivo_descricao').val(motivoDescricao);
                
                jQuery('#protocolo, #contrato_indicado').mask('9?999999999',{
                    placeholder:""
                });
                
                jQuery("#bt_voltar").addClass('voltar_ajax_step_2');
            }
        });
    });    
    
    
    
    var timeOut;//usado na verificação de campos em tela de motivo de crédito do tipo contestação  e indicação deamigo
        
    /** 
     * o trecho abaixo é em relação ao motivo de credito do tipo contestação de crédito
     *   escolhido pelo usuario no step 2 do cadastro de crédito futuro
    */   
    jQuery('body').delegate('#protocolo', 'keyup', function(){
        clearTimeout(timeOut);
       
        var protocolo = jQuery(this).attr('value');
        
        jQuery("#bt_avancar").attr('disabled','disabled');
        jQuery("#valor_tipo_desconto").val('0,00');
                         
        if (jQuery.trim(protocolo) == '') {
            return false;
        }
        
        timeOut = setTimeout(function(){
      
            jQuery('.msg_contestacao').remove();
            
            jQuery.ajax({
                type:'POST',
                url: 'fin_credito_futuro.php',
                data: {
                    acao:'verificarProtocolo',
                    protocolo: protocolo
                },
                beforeSend: function () {
                    jQuery("#protocolo").mostrarCarregando();
                },
                success: function (data) {
                    
                    if (typeof JSON != 'undefined') {
                        data = JSON.parse(data);
                    } else {
                        data = eval('(' + data + ')');
                    }
                    
                    jQuery("#protocolo").esconderCarregando();
                    
                    if (data.status == true) {         

                        var valida = true;
                        
                        if (data.valor == 'NE') {
                            //nao encontrado
                            valida = false;
                            jQuery("#mensagem_info").after('<div class="msg_contestacao mensagem alerta">' + mensagens.protocolo_nao_encontrado + '</div>');
                        } else if (data.valor == 'NC') {
                            //nao concluido
                            valida = false;
                            jQuery("#mensagem_info").after('<div class="msg_contestacao mensagem alerta">' + mensagens.protocolo_nao_concluido + '</div>');
                        } else if (data.valor == 'JU') {
                            //ja ultilizado 
                            valida = false;
                            jQuery("#mensagem_info").after('<div class="msg_contestacao mensagem alerta">' + mensagens.protocolo_utilizado + '</div>');
                        } else if (data.valor == 'NP') {
                            //nao procede
                            valida = false;
                            jQuery("#mensagem_info").after('<div class="msg_contestacao mensagem alerta">' + mensagens.protocolo_nao_procede + '</div>');
                        } else if (data.valor == 'CD') {
                            //cliente diferente
                            valida = false;
                            jQuery("#mensagem_info").after('<div class="msg_contestacao mensagem alerta">' + mensagens.protocolo_difere_cliente + '</div>');
                        } else {

                            jQuery("#protocolo").prev('label').removeClass('erro');
                            jQuery("#protocolo").removeClass('erro');
                            //econtrado e válido
                            jQuery("#bt_avancar").removeAttr('disabled');
                            jQuery("#valor_tipo_desconto").val(data.valor);
                        }

                        if (!valida) {

                            jQuery("#protocolo").prev('label').addClass('erro');
                        jQuery("#protocolo").addClass('erro');
                        jQuery("#protocolo").val('');
                        jQuery("#protocolo").blur();
                        jQuery("#protocolo").focus();

                        }

                    } else {
                        //erro processamento
                        jQuery("#mensagem_info").after('<div class="msg_contestacao mensagem erro">' + mensagens.erro_processamento_dados + '</div>');
                    }
                    
                }
            });
      
        },1300);
    });    
    //fim do trecho para motivo de crédito do tipo contestação de crédito
    
    
    /** 
     * o trecho abaixo é em relação ao motivo de credito do indicação de amigo
     * escolhido pelo usuario no step 2 do cadastro de crédito futuro
    */   
    jQuery('body').delegate('#contrato_indicado', 'keyup', function(){
        clearTimeout(timeOut);
       
        var contrato = jQuery(this).attr('value');
        
        jQuery("#bt_avancar").attr('disabled','disabled');
        jQuery("#campo_contrato label,#campo_contrato input").removeClass('erro');
        jQuery(".msg_indicacao").remove();
                         
        if (jQuery.trim(contrato) == '') {
            return false;
        }
        
        timeOut = setTimeout(function(){
      
            jQuery('.msg_contestacao').remove();
            
            jQuery.ajax({
                type:'POST',
                url: 'fin_credito_futuro.php',
                data: {
                    acao:'verificarContrato',
                    contrato: contrato
                },
                beforeSend: function () {
                    jQuery("#loading-verifica-contrato").css('display','');
                    jQuery("#contrato_indicado").mostrarCarregando();
                },
                success: function (data) {
                    
                    if (typeof JSON != 'undefined') {
                        data = JSON.parse(data);
                    } else {
                        data = eval('(' + data + ')');
                    }
                    
                    jQuery("#contrato_indicado").esconderCarregando();
                    
                    if (data.status == true) {                        
                        
                        /* 
                        * JU -  Já Utilizado
                        * MC - Cliente do contrado indicado é o mesmo do cadastrado no crédito futuro
                        * SE - Contrato sem equipamento instalado
                        * SI - Sem data de inicio de vigencia
                        * NE - Não encontrado
                        */
                        
                        if (data.valor != 'OK') {
                            //return false;
                            jQuery("#campo_contrato label,#campo_contrato input").addClass('erro');
                        }
                        
                        if (data.valor == 'NE') {
                            //nao encontrado
                            jQuery("#mensagem_info").after('<div class="msg_indicacao mensagem alerta">' + mensagens.contrato_indicado_nao_encontrado + '</div>');
                        } else if (data.valor == 'MC') {
                            //mesmo cliente
                            jQuery("#mensagem_info").after('<div class="msg_indicacao mensagem alerta">' + mensagens.contrato_indicado_mesmo_cliente + '</div>');
                        } else if (data.valor == 'SE') {
                            //sem equipamento instalado 
                            jQuery("#mensagem_info").after('<div class="msg_indicacao mensagem alerta">' + mensagens.contrato_indicado_sem_equipamento + '</div>');
                        } else if (data.valor == 'SI') {
                            //sem data de inicio de vigencia
                            jQuery("#mensagem_info").after('<div class="msg_indicacao mensagem alerta">' + mensagens.contrato_indicado_nao_vigente + '</div>');
                        } else if (data.valor == 'JU') {
                            //ja utilizado
                            jQuery("#mensagem_info").after('<div class="msg_indicacao mensagem alerta">' + mensagens.contrato_indicado_utilizado + '</div>');
                        } else {
                            //econtrado e válido
                            jQuery("#bt_avancar").removeAttr('disabled');
                        }
                    } else {
                        //erro processamento
                        jQuery("#mensagem_info").after('<div class="msg_indicacao mensagem erro">' + mensagens.erro_processamento_dados + '</div>');
                    }
                    
                }
            });
      
        },1300);
    });    
    //fim do trecho para motivo de crédito do tipo indicaçaõ de amigo
    
    if (jQuery(".excluir_item").length == 0) {
        jQuery("#form_listagem_pesquisa .bloco_acoes").remove();
        jQuery(".td_excluir_todos, .td_excluit_item").remove();
        jQuery(".td_contador").attr('colspan','15');
    }
    
    jQuery('#marcar_todos').change(function(){
        if (jQuery(this).is(':checked')) {
            jQuery('.excluir_item').attr('checked','checked');
            jQuery("#bt_excluir_massa").removeAttr('disabled');
        } else {
            jQuery('.excluir_item').removeAttr('checked');
            jQuery("#bt_excluir_massa").attr('disabled','disabled');
        }
    });
    
    jQuery(".excluir_item").change(function(){

        if (jQuery(".excluir_item:checked").length == 0) {
            jQuery("#bt_excluir_massa").attr('disabled','disabled');
        }else {
            jQuery("#bt_excluir_massa").removeAttr('disabled');
        }

        if (jQuery(".excluir_item:checked").length < jQuery(".excluir_item").length) {
            jQuery('#marcar_todos').removeAttr('checked');
        }else{
            
            if (jQuery(".excluir_item:checked").length == jQuery(".excluir_item").length) {
                jQuery("#marcar_todos").attr('checked','checked');
            }
        } 
    });
    
    //faz o tratamento para os campos de perídos 
    jQuery("#periodo_inclusao_ini").periodo("#periodo_inclusao_fim");
    jQuery("#tipo_desconto_de").periodo("#tipo_desconto_ate");
   
   
    //Validações e mascaras     
    jQuery('#cpf').mask('999.999.999-99');
    jQuery('#cnpj').mask('99.999.999/9999-99');
        
    //Seleciona o tipo de pessoa (PJ||PF)
    jQuery('input[name="cadastro[tipo_pessoa]"], input[name="tipo_pessoa"]').change(function(){
        
        
        
        var tipo_pessoa = jQuery(this).val();
        
        jQuery('.ui-helper-hidden-accessible').html('');
        
        //Limpa o ID do cliente para a busca
        jQuery("#cliente_id, #cfoclioid").val('');
        jQuery("#contrato").val('');
        jQuery("#nome_cliente").val('');
        habilitaDesabilitaAvancarStep1();
        
        if(tipo_pessoa == 'J') {
            jQuery('#juridicoNome, #juridicoDoc').removeClass('invisivel');
            jQuery('#fiscaNome, #fiscaDoc').addClass('invisivel');
            jQuery('#juridicoNome input, #juridicoDoc input, #fiscaNome input, #fiscaDoc input').val('');
        } else {
            jQuery('#juridicoNome, #juridicoDoc').addClass('invisivel');
            jQuery('#fiscaNome, #fiscaDoc').removeClass('invisivel');
            jQuery('#juridicoNome input, #juridicoDoc input, #fiscaNome input, #fiscaDoc input').val('');
        }
                        
    });
    
    //AUTOCOMPLETE Contrato
    jQuery( "#contrato" ).autocomplete({
        source: "fin_credito_futuro.php?acao=buscarClienteContrato",
        minLength: 2,        
        response: function(event, ui ) {            
            
            mudarTamanhoAutoComplete(ui.content.length);
            jQuery("#cliente_id").val('');
            habilitaDesabilitaAvancarStep1();
            escondeClienteNaoEncontrado();
                  
            if(!ui.content.length && jQuery.trim(jQuery(this).val()) != "") {
                mostraClienteNaoEncontrado('Contrato não encontrado no banco de dados.');
                jQuery("#contrato").blur();
                jQuery("#contrato").focus();
            }
            
            if(jQuery.trim(jQuery(this).val()) == "") {
                jQuery(this).val('');
            }
            
            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {}
                }
            });   
            
        },
        select: function( event, ui ) {       
            
            /**validação contrato*/

            //no change
            jQuery('input[name="cadastro[tipo_pessoa]"], input[name="tipo_pessoa"]').each(function(){
                if( jQuery(this).val() ==  ui.item.tipo){
                    jQuery(this).attr('checked','checked');
                } 
            });
            
            jQuery("#cliente_id").val(ui.item.id);
            jQuery('#nome_cliente').val(ui.item.nome);
            habilitaDesabilitaAvancarStep1();
            
            if (ui.item.tipo == 'J') {
                jQuery('#juridicoDoc').removeClass('invisivel');
                jQuery('#fiscaDoc').addClass('invisivel');
                jQuery('#juridicoNome input, #juridicoDoc input, #fiscaNome input, #fiscaDoc input').val('');               
                
                jQuery('#cnpj').val(ui.item.doc);
                
            } else {
                jQuery('#juridicoNome, #juridicoDoc').addClass('invisivel');
                jQuery('#fiscaNome, #fiscaDoc').removeClass('invisivel');
                jQuery('#juridicoNome input, #juridicoDoc input, #fiscaNome input, #fiscaDoc input').val('');               
                
                jQuery('#cpf').val(ui.item.doc);
            }
        }        
    });
    
    
    //AUTOCOMPLETE PARA TELA DE CADASTRO DE CREDITO FUTURO- NOME DE CLIENtE
    jQuery( "#nome_cliente" ).autocomplete({
        source: "fin_credito_futuro.php?acao=buscarClienteNome",
        minLength: 2,        
        response: function(event, ui ) {            
            
            mudarTamanhoAutoComplete(ui.content.length);
            jQuery("#cliente_id").val('');
            habilitaDesabilitaAvancarStep1();
            escondeClienteNaoEncontrado();
                  
            if(!ui.content.length && jQuery.trim(jQuery(this).val()) != "") {
                mostraClienteNaoEncontrado();
            }
            
            if(jQuery.trim(jQuery(this).val()) == "") {
                jQuery(this).val('');
            }
            
            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {}
                }
            });   
            
        },
        select: function( event, ui ) {  
            
            //no change
            jQuery('input[name="cadastro[tipo_pessoa]"], input[name="tipo_pessoa"]').each(function(){
                if( jQuery(this).val() ==  ui.item.tipo){
                    jQuery(this).attr('checked','checked');
                } 
            });
            
            jQuery("#cliente_id").val(ui.item.id);
            jQuery('#nome_cliente').val(ui.item.nome);
            habilitaDesabilitaAvancarStep1();
            
            if (ui.item.tipo == 'J') {
                jQuery('#juridicoDoc').removeClass('invisivel');
                jQuery('#fiscaDoc').addClass('invisivel');
                jQuery('#juridicoNome input, #juridicoDoc input, #fiscaNome input, #fiscaDoc input').val('');               
                
                jQuery('#cnpj').val(ui.item.doc);
                
            } else {
                jQuery('#juridicoNome, #juridicoDoc').addClass('invisivel');
                jQuery('#fiscaNome, #fiscaDoc').removeClass('invisivel');
                jQuery('#juridicoNome input, #juridicoDoc input, #fiscaNome input, #fiscaDoc input').val('');               
                
                jQuery('#cpf').val(ui.item.doc);
            }
        }        
    });

    
    jQuery("#nome_cliente, #cpf, #cnpj").blur(function() {
                    
        if (jQuery.trim(jQuery("#cliente_id").val()) == '' && jQuery.trim(jQuery("#cfoclioid").val()) == '') {
            jQuery(this).val('');
        }
    });

    jQuery("#nome_cliente, #cpf, #cnpj, #nome, #razao_social, #contrato").bind('cut', function() {
        jQuery("#nome_cliente, #cpf, #cnpj, #nome, #cfoclioid, #razao_social, #cliente_id, #contrato").val('');
        habilitaDesabilitaAvancarStep1();
    });

    jQuery("#razao_social, #nome").blur(function() {
        /* Act on the event */
        if (jQuery.trim(jQuery("#cfoclioid").val()) == '') {
            jQuery(this).val('');
        }
    });
    
    
    
    //AUTOCOMPLETE NOME PJ
    jQuery( "#razao_social" ).autocomplete({
        source: "fin_credito_futuro.php?acao=buscarClienteNome&filtro=J",
        minLength: 2,        
        response: function(event, ui ) {            
            
            mudarTamanhoAutoComplete(ui.content.length);
            jQuery("#cliente_id, #cfoclioid").val('');
            habilitaDesabilitaAvancarStep1();
            escondeClienteNaoEncontrado();
                  
            if(!ui.content.length && jQuery.trim(jQuery(this).val()) != "") {
                mostraClienteNaoEncontrado();
            }
            
            if(jQuery.trim(jQuery(this).val()) == "") {
                jQuery(this).val('');
            }
            
            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {}
                }
            });   
            
        },
        select: function( event, ui ) {            
            //no change
            jQuery("#cnpj").val(ui.item.doc);
            jQuery("#cliente_id, #cfoclioid").val(ui.item.id);
            habilitaDesabilitaAvancarStep1();
        }        
    });

    jQuery('#nome').on('paste',function(){
        jQuery(this).keyup();
    });

    jQuery('#cnpj, #cpf').on('paste', function(){
        jQuery(this).autocomplete('search', jQuery(this).val());
    });
    
    //AUTOCOMPLETE NOME PF
    jQuery( "#nome" ).autocomplete({
        source: "fin_credito_futuro.php?acao=buscarClienteNome&filtro=F",
        minLength: 2,       
        response: function(event, ui ) {  
            
            mudarTamanhoAutoComplete(ui.content.length);
            jQuery("#cliente_id, #cfoclioid").val('');
            habilitaDesabilitaAvancarStep1();
            escondeClienteNaoEncontrado();
            
            if(!ui.content.length && jQuery.trim(jQuery(this).val()) != "") {
                mostraClienteNaoEncontrado();
            }
            
            if(jQuery.trim(jQuery(this).val()) == "") {
                jQuery(this).val('');
            }
            
            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {}
                }
            });   
            
        },
        select: function( event, ui ) {
            
            jQuery("#cpf").val(ui.item.doc);
            jQuery("#cliente_id, #cfoclioid").val(ui.item.id);
            habilitaDesabilitaAvancarStep1();
        }
    });
    
    //AUTOCOMPLETE DOCUMENTO CPF
    jQuery( "#cpf" ).autocomplete({
        source: "fin_credito_futuro.php?acao=buscarClienteDoc&filtro=F",
        minLength: 2,        
        response: function(event, ui ) {    
            
            mudarTamanhoAutoComplete(ui.content.length);
            
            jQuery("#cliente_id, #cfoclioid").val('');
            habilitaDesabilitaAvancarStep1();
            
            escondeClienteNaoEncontrado();
            
            if(!ui.content.length && !jQuery.trim(jQuery(this).val().replace(/[^0-9]+/g, '')) == "") {
                mostraClienteNaoEncontrado();
                jQuery("#cpf").blur();
                jQuery("#cpf").focus();
            }
            
            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {}
                }
            });   
           
        },
        select: function( event, ui ) {
            //no change
            jQuery("#nome, #nome_cliente").val(ui.item.nome);
            jQuery("#cliente_id, #cfoclioid").val(ui.item.id);
            habilitaDesabilitaAvancarStep1();
        }
    });    
    
    //AUTOCOMPLETE DOCUMENTO CNPJ
    jQuery( "#cnpj" ).autocomplete({
        source: "fin_credito_futuro.php?acao=buscarClienteDoc&filtro=J",
        minLength: 2,        
        response: function(event, ui ) {       
            
            mudarTamanhoAutoComplete(ui.content.length);
            
            jQuery("#cliente_id, #cfoclioid").val('');
            habilitaDesabilitaAvancarStep1();
            
            escondeClienteNaoEncontrado();
            
            if(!ui.content.length && !jQuery.trim(jQuery(this).val().replace(/[^0-9]+/g, '')) == "") {
                mostraClienteNaoEncontrado();
                jQuery("#cnpj").blur();
                jQuery("#cnpj").focus();
            }
            
            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {}
                }
            });   
            
        },
        select: function( event, ui ) {
            jQuery("#razao_social, #nome_cliente").val(ui.item.nome);
            jQuery("#cliente_id, #cfoclioid").val(ui.item.id);
            habilitaDesabilitaAvancarStep1();
        }
    });
    
    //Ao deletar caracter das busca, limpa os demais campos atrelados
    jQuery('#nome, #razao_social, #nome_cliente').keyup(function(e){
        if (e.which == 8 || e.which == 46) {
            jQuery('#cpf, #cnpj, #cliente_id, #contrato, #cfoclioid').val('');
            habilitaDesabilitaAvancarStep1();
        }
    });
    
    //Ao deletar caracter das busca, limpa os demais campos atrelados
    jQuery('#cpf, #cnpj').keyup(function(e){
        if (e.which == 8 || e.which == 46) {
            jQuery('#nome, #razao_social, #cliente_id, #contrato, #nome_cliente, #cfoclioid').val('');
            habilitaDesabilitaAvancarStep1();
        }
    });
    
    //Ao deletar caracter das busca, limpa os demais campos atrelados
    jQuery('#contrato').keyup(function(e){
        if (e.which == 8 || e.which == 46) {
            jQuery('#nome, #razao_social, #cpf, #cnpj, #cliente_id, #nome_cliente').val('');
            habilitaDesabilitaAvancarStep1();
        }
    });
    
    
    //Ao selecionar os tipos de descontos, mudar os box de intervarlos de valor percetual ou valor
    jQuery(".tipo_desconto").change(function(){
        

        jQuery("#tipo_descont_1 .campo label, #tipo_descont_1 .campo input, #tipo_descont_2 .campo label, #tipo_descont_2 .campo input").removeClass('erro');

        if (jQuery(this).val() == '1') {
            jQuery("#tipo_descont_1").removeClass('invisivel');
            jQuery("#tipo_descont_2").addClass('invisivel');
            jQuery("#tipo_descont_2 input").val('');
        } else if (jQuery(this).val() == '2') {
            jQuery("#tipo_descont_1").addClass('invisivel');
            jQuery("#tipo_descont_2").removeClass('invisivel');
            jQuery("#tipo_descont_1 input").val('');
        } else {
            jQuery("#tipo_descont_1").addClass('invisivel');
            jQuery("#tipo_descont_2").addClass('invisivel');
            jQuery("#tipo_descont_1 input").val('');
            jQuery("#tipo_descont_2 input").val('');
        }
        
    });
    
    
    //Trecho de código que corrige falhas do maskmoney no onpaste e keypress

    jQuery(".moeda,.porcentagem").maskMoney({
        symbol:'', 
        thousands:'.', 
        decimal:',', 
        symbolStay: false, 
        showSymbol:false,
        precision:2, 
        defaultZero: false,
        allowZero: false
    });

    jQuery(".moeda,.porcentagem").on('paste',function(){
        var id = jQuery(this).attr('id');
        var maxlength = jQuery(this).attr('maxlength');
        
        setTimeout(function(){
            
            var v = jQuery("#"+id).val();
            var vMasc = maskValue(v);
            var nV = v;
            
            if (vMasc.length > maxlength) {
                nV = "";
                var maxChar = (maxlength - (vMasc.length - maxlength));
                var vArray = v.split("");
                var i = 0;
                for ( i ; i <= maxChar ; i++) {
                    nV += vArray[i];
                }   
            }
            
            jQuery("#"+id).val( maskValue(nV) );
            
        },10);
    });
    
    jQuery(".moeda,.porcentagem").on('keyup',function(){
        var id = jQuery(this).attr('id');
        jQuery("#"+id).val( maskValue(jQuery("#"+id).val()) );
    });

    settings = {};
    settings.allowNegative = false;
    settings.decimal = ',';
    settings.precision = 2;
    settings.thousands = '.';

    function maskValue(v) {
        
        var strCheck = '0123456789';
        var len = v.length;
        var a = '', t = '', neg='';

        if(len!=0 && v.charAt(0)=='-'){
            v = v.replace('-','');
            if(settings.allowNegative){
                neg = '-';
            }
        }

        for (var i = 0; i<len; i++) {
            if ((v.charAt(i)!='0') && (v.charAt(i)!=settings.decimal)) break;
        }

        for (; i<len; i++) {
            if (strCheck.indexOf(v.charAt(i))!=-1) a+= v.charAt(i);
        }

        var n = parseFloat(a);
        n = isNaN(n) ? 0 : n/Math.pow(10,settings.precision);
        t = n.toFixed(settings.precision);

        i = settings.precision == 0 ? 0 : 1;
        var p, d = (t=t.split('.'))[i].substr(0,settings.precision);
        for (p = (t=t[0]).length; (p-=3)>=1;) {
            t = t.substr(0,p)+settings.thousands+t.substr(p);
        }

        return (settings.precision>0)
        ? neg+t+settings.decimal+d+Array((settings.precision+1)-d.length).join(0)
        : neg+t;
    }
    
    
    
    
});

var htmlEscapes = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#x27;',
    '/': '&#x2F;'
};

// Regex containing the keys listed immediately above.
var htmlEscaper = /[&<>"'\/]/g;

// Escape a string for HTML interpolation.
_escape = function(string) {
    return ('' + string).replace(htmlEscaper, function(match) {
        return htmlEscapes[match];
    });
};

/*
* Limita tamanho do autocomplete
 */
function mudarTamanhoAutoComplete(qtdOpcoes) {
  
    if (qtdOpcoes > 0) {
        
        var tamanhoOpcao = 23;//height de cada opção
        var tamanhoListagem = qtdOpcoes * tamanhoOpcao;
        if (tamanhoListagem > 166) {
            jQuery('ul.ui-autocomplete').height(166);
        } else {
            jQuery('ul.ui-autocomplete').height(tamanhoListagem);
        }
    }else{
        jQuery('ul.ui-autocomplete').height(0);
    }
  
}

/*
 * Mostra Mensagem cliente nao encontrado
 */
function mostraClienteNaoEncontrado (msg) {

    msg_cliente = typeof msg && jQuery.trim(msg) != '' ? msg : 'Cliente não consta no cadastro.';

    jQuery("#mensagem_alerta").text(msg_cliente);
    jQuery("#mensagem_alerta").removeClass("invisivel");
    jQuery("#nome_cliente, #cpf, #cnpj, #contrato").val('');
}

/*
 * Mostra Esconde Mensagem cliente nao encontrado
 */
function escondeClienteNaoEncontrado() {
    jQuery("#mensagem_alerta").text("");
    jQuery("#mensagem_alerta").addClass("invisivel");
}

//Cadastro
/*
 * Habilita ou desabilita o botão do primeiro passo da tela de cadastro
 * 'aba 1 cliente'
 */
function habilitaDesabilitaAvancarStep1 () {
    if ( jQuery.trim(jQuery("#cliente_id").val()) != '') {
        jQuery("#bt_avancar").removeAttr('disabled');
    } else{
        jQuery("#bt_avancar").attr('disabled','disabled');
    }
}



function marcarCampo(campo) {    
    campo.prev().addClass('erro');
    campo.addClass('erro');
}

function desmarcarCampo(campo) {
    campo.prev().removeClass('erro');
    campo.removeClass('erro');
}

function validarCamposStep3Contestacao(campo, mostrarErros) {
    
    if (typeof campo == 'object' && campo.hasClass('naoValidar')) {
        return false;
    }
    
    jQuery.fn.limpaMensagens();
    mensagem = new Array();
    dados = new Array();
    var obrigatorio = true;
    
    //campo valor
    if (jQuery.trim(jQuery("#valor_tipo_desconto").val()) == '') {        
        setErrors('valor_tipo_desconto');
        obrigatorio = false;
    }
    
    //campo parcelas    
    if (jQuery.trim(jQuery("#cfoqtde_parcelas").val()) == '') {        
        setErrors('cfoqtde_parcelas');
        obrigatorio = false;
    }
    
    if (jQuery.trim(jQuery("#cfoqtde_parcelas").val()) == '0') {        
        setErrors('cfoqtde_parcelas');
        setMsg(mensagens.step_3_campo_parcela_igual_zero);
    }
    
    //valor do desconto
    
    valor = jQuery("#valor_tipo_desconto").val().replace('.', '');
    valor = valor.replace(',','.');

    if (jQuery.trim(valor) == '') {        
        setErrors('valor_tipo_desconto');
        obrigatorio = false;
        //setMsg('O valor do desconto não pode ser vazio.');
    }

    if (jQuery.trim(parseFloat(valor)) == 0) {        
        setErrors('valor_tipo_desconto');
        setMsg(mensagens.step_3_campo_valor_igual_zero);
    }
    
    
    //campo parcelas    
    if (jQuery.trim(jQuery("#cfoobroid_desconto").val()) == '') {        
        setErrors('cfoobroid_desconto');
        obrigatorio = false;
    }
    
    if (!obrigatorio) {
        setMsg(mensagens.step_3_campo_obrigatorio);
    }
    
    if (mensagem.length > 0) {
        
        if (mostrarErros == '1') {
            printarMsg(mensagem);
            showFormErros(dados);
        }
        
        jQuery('#bt_avancar').attr('disabled','disabled');
    } else {
        jQuery('#bt_avancar').removeAttr('disabled');
    }
    
}


function validarCamposStep3Indicacao(campo, mostrarErros) {    
    
    if (typeof campo == 'object' && campo.hasClass('naoValidar')) {
        return false;
    }
    
    jQuery.fn.limpaMensagens();
    mensagem = new Array();
    dados = new Array();
    var obrigatorio = true;
    
    
    //campo parcelas    
    if (jQuery.trim(jQuery("#cfoqtde_parcelas").val()) == '') {        
        setErrors('cfoqtde_parcelas');
        obrigatorio = false;
    }
    
    if (jQuery.trim(jQuery("#cfoqtde_parcelas").val()) == '0') {        
        setErrors('cfoqtde_parcelas');
        setMsg(mensagens.step_3_campo_parcela_igual_zero);
    }
    
    //valor do desconto    
    
    
    valor = jQuery("#valor_tipo_desconto").val().replace('.', '');
    valor = valor.replace(',','.');
    
    if (jQuery("#valor_tipo_desconto").hasClass('isMoney')) {
        

        if (jQuery.trim(valor) == '') {        
            setErrors('valor_tipo_desconto');
            obrigatorio = false;
            //setMsg('O valor do desconto não pode ser vazio.');
        }

        if (jQuery.trim(parseFloat(valor)) == 0) {        
            setErrors('valor_tipo_desconto');
            setMsg(mensagens.step_3_campo_valor_igual_zero);
        }
        
    } else {

        if (jQuery.trim(valor) == '') {        
            setErrors('valor_tipo_desconto');
            obrigatorio = false;
            //setMsg('O percentual do desconto não pode ser vazio.');
        }        
        
        if (jQuery.trim(parseFloat(valor)) == 0) {        
            setErrors('valor_tipo_desconto');
            setMsg(mensagens.step_3_campo_porcento_igual_zero);
        }
        
        if (jQuery.trim(parseFloat(valor)) > 100) {        
            setErrors('valor_tipo_desconto');
            setMsg(mensagens.step_3_campo_maior_igual_cem);
        }
        
    }
    
    //campo parcelas    
    if (jQuery.trim(jQuery("#cfoobroid_desconto").val()) == '') {        
        setErrors('cfoobroid_desconto');
        obrigatorio = false;
    }
    
    if (!obrigatorio) {
        setMsg(mensagens.step_3_campo_obrigatorio);
    }
    
    if (mensagem.length > 0) {
        if (mostrarErros == '1') {
            printarMsg(mensagem);
            showFormErros(dados);
        }
        jQuery('#bt_avancar').attr('disabled','disabled');
    } else {
        jQuery('#bt_avancar').removeAttr('disabled');
    }
    
}

function validarCamposStep3Insencao(campo, mostrarErros) {
    
    
    if (typeof campo == 'object' && campo.hasClass('naoValidar')) {
        return false;
    }
    
    jQuery.fn.limpaMensagens();
    mensagem = new Array();
    dados = new Array();
    var obrigatorio = true;
    
    
    //campo parcelas    
    if (jQuery.trim(jQuery("#cfoqtde_parcelas").val()) == '') {        
        setErrors('cfoqtde_parcelas');
        obrigatorio = false;
    }


    
    if (jQuery.trim(jQuery("#cfoqtde_parcelas").val()) == '0') {        
        setErrors('cfoqtde_parcelas');
        setMsg(mensagens.step_3_campo_parcela_igual_zero);
    }
    
    if (jQuery.trim(jQuery("#cfoobroid_desconto").val()) == '') {        
        setErrors('cfoobroid_desconto');
        obrigatorio = false;
    }
    
    
    if (!obrigatorio) {
        setMsg(mensagens.step_3_campo_obrigatorio);
    }
    
    if (mensagem.length > 0) {
        if (mostrarErros == '1') {
            printarMsg(mensagem);
            showFormErros(dados);
        }
        jQuery('#bt_avancar').attr('disabled','disabled');
    } else {
        jQuery('#bt_avancar').removeAttr('disabled');
    }
}

function validarCamposStep3Default(campo,mostrarErros) {

    if (typeof campo == 'object' && campo.hasClass('naoValidar')) {
        return false;
    }
    
    jQuery.fn.limpaMensagens();
    mensagem = new Array();
    dados = new Array();
    var obrigatorio = true;
    
    
    //campo parcelas    
    if (jQuery.trim(jQuery("#cfoqtde_parcelas").val()) == '') {        
        setErrors('cfoqtde_parcelas');
        obrigatorio = false;
    }
    
    if (jQuery.trim(jQuery("#cfoqtde_parcelas").val()) == '0') {        
        setErrors('cfoqtde_parcelas');
        setMsg(mensagens.step_3_campo_parcela_igual_zero);
    }
    
    //valor do desconto    
    
    
    valor = jQuery("#valor_tipo_desconto").val().replace('.', '');
    valor = valor.replace(',','.');

    if (jQuery("#valor_tipo_desconto").hasClass('isMoney')) {
        

        if (jQuery.trim(valor) == '') {        
            setErrors('valor_tipo_desconto');
            //setMsg('O valor do desconto não pode ser vazio.');
            obrigatorio = false;
        }

        if (jQuery.trim(parseFloat(valor)) == 0) {        
            setErrors('valor_tipo_desconto');
            setMsg(mensagens.step_3_campo_valor_igual_zero);
        }
        
    } else {   


        if (jQuery.trim(valor) == '') {        
            setErrors('valor_tipo_desconto');
            //setMsg('O percentual do desconto não pode ser vazio.');
            obrigatorio = false;
        }     
        
        if (jQuery.trim(parseFloat(valor)) == 0) {        
            setErrors('valor_tipo_desconto');
            setMsg(mensagens.step_3_campo_porcento_igual_zero);
        }
        
        if (jQuery.trim(parseFloat(valor)) > 100) {        
            setErrors('valor_tipo_desconto');
            setMsg(mensagens.step_3_campo_maior_igual_cem);
        }
        
    }
    
    //campo parcelas    
    if (jQuery.trim(jQuery("#cfoobroid_desconto").val()) == '') {        
        setErrors('cfoobroid_desconto');
        obrigatorio = false;
    }
    
    if (!obrigatorio) {
        setMsg(mensagens.step_3_campo_obrigatorio);
    }
    
    if (mensagem.length > 0) {
        if (mostrarErros == '1') {
            printarMsg(mensagem);
            showFormErros(dados);
        }
        jQuery('#bt_avancar').attr('disabled','disabled');
    } else {
        jQuery('#bt_avancar').removeAttr('disabled');
    }
}



var mensagens = {
    protocolo_utilizado: 'Esse protocolo não é válido, pois já foi utilizado.',
    protocolo_nao_encontrado: 'Esse protocolo não foi encontrado.',
    protocolo_nao_concluido: 'Esse protocolo não esta concluído.',
    protocolo_nao_procede: 'Protocolo não procedente.',
    protocolo_difere_cliente: 'Protocolo não pertence ao cliente beneficiado pelo crédito.',
    contrato_indicado_nao_encontrado: 'Contrato não encontrado no banco de dados.',
    contrato_indicado_mesmo_cliente: 'Contrato indicado não pode ser do mesmo cliente beneficiado pelo crédito.',
    contrato_indicado_utilizado: 'Contrato indicado já informado em outro registro de crédito futuro.',
    contrato_indicado_sem_equipamento: 'Contrato indicado não possui equipamento instalado.',
    contrato_indicado_nao_vigente: 'Contrato ainda não vigente.',
    erro_processamento_dados: 'Houve um erro no processamento de dados.',
        
    step_3_campo_valor_igual_zero: 'O valor do desconto não pode ser igual a 0.',
    step_3_campo_obrigatorio: 'Informações obrigatórias não preenchidas.',
    step_3_campo_parcela_igual_zero : 'A quantidade de parcelas não pode ser igual a 0.',
    step_3_campo_porcento_igual_zero : 'O percentual do desconto não pode ser igual a 0%.',
    step_3_campo_maior_igual_cem: 'O percentual do desconto não pode ser maior que 100%.',
    credito_excluido: 'Crédito futuro excluído com sucesso.'
    
    
}

var mensagem = new Array();
var dados = new Array();

function setErrors (id,mensagem) {
    dados.push({
        campo: id,
        mensagem: mensagem	
    });
}
        
function setMsg (string_mensagem) {
    mensagem.push({
        mensagem: string_mensagem
    });
}

function printarMsg(mensagem) {
    if (mensagem.length > 1) {
        var htmlMsg = "<div class=\"mensagem alerta\" > <ul style=\"margin: 13px; margin-top:0px; padding: 0;\">";
                
        jQuery(mensagem).each(function(i,v){
            htmlMsg += "<li>" + v.mensagem + "</li>";
        });
            
        htmlMsg += "</ul></div>";
            
        jQuery("#mensagem_info").after(htmlMsg);
            
    } else {
        jQuery(mensagem).each(function(i,v){
            jQuery("#mensagem_info").after('<div class=\"mensagem alerta\" >'+ v.mensagem +'</div>');
        });
    }
}

jQuery.fn.limpaMensagens = function() {
    jQuery('.mensagem').not('.info').remove();
    jQuery('.mensagem').not('.info').remove();
    jQuery(".erro").not('.mensagem').removeClass("erro");
    resetFormErros();
}
