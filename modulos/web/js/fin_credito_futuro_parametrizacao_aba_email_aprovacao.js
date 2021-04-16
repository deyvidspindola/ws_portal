jQuery(document).ready(function(){
    
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
                                }\n\
                                .ui-widget-header{\n\
                                    width: 100% !important;\n\
                                }\n\
                                #solicitar-observacao{ width: 276px !important;} </style>');


    }
    
    jQuery( "#nome" ).autocomplete({
        source: "fin_credito_futuro_parametrizacao.php?acao=buscarResponsavel",
        minLength: 2,
        response: function(event, ui ) { 
                        
            if(!ui.content.length && jQuery.trim(jQuery(this).val()) != "") {
            //TODO
            //jQuery('#msg_alerta').html(_escape(jQuery(this).val()) + ' não consta no cadastro.').showMessage();
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
            jQuery.fn.limpaMensagens();            
            
            modalSelecionarTipoMotivo(ui.item);
            
        }
    });
    
    
    function fecharLimparModal() {
        //escondo a modal e limpo os campos
        jQuery("#selecionar-tipo-motivos").dialog('close');
        jQuery("#selecionar-tipo-motivos .info-usuario #nome span").html('');
        jQuery("#selecionar-tipo-motivos .info-usuario #email span").html('');
        jQuery(".parametrizar_motivo_credito_item, #selecionar_todos").removeAttr('checked');
    }
    
    function enviarTiposMotivoUsuario(dados) {
        
        jQuery.ajax({
            url: 'fin_credito_futuro_parametrizacao.php',
            type: 'post',
            data: dados,
            dataType: 'json',
            beforeSend: function() {
                resetFormErros();
                jQuery('#loader_responsavel').fadeIn();
                jQuery('#separador_loader_responsavel').show();
                jQuery('#msg_responsavel').removeClass('sucesso').removeClass('erro').removeClass('alerta').html('').hideMessage();                    
            },
            success: function(data) {

                if(data.status) {

                    fecharLimparModal();
                    

                    jQuery('#msg_responsavel').addClass('sucesso').html('Registro incluído com sucesso.').showMessage();
                    jQuery('#nome').val('');
                    
                    jQuery.ajax({
                        type: 'POST',
                        url: 'fin_credito_futuro_parametrizacao.php',
                        data: {
                            acao: 'listarResponsaveis'
                        },
                        success: function(data){
                            jQuery("#listagem-responsaveis").html(data);
                        }
                    });

                } else {
                    
                    fecharLimparModal();
                    jQuery('#msg_responsavel').addClass(data.tipoErro).html(data.mensagem).showMessage();

                    showFormErros(data.dados);
                }

            },            
            complete: function() {                
                jQuery('#loader_responsavel').fadeOut();
                jQuery('#separador_loader_responsavel').hide();                    
            },
            error: function() {
                jQuery('#msg_responsavel').addClass('erro').html('Houve um erro no processamento dos dados.').showMessage();
            }
        });
    }
    
    
    function modalSelecionarTipoMotivo(usuario) {
        
        //        setTimeout(function(){
        //                jQuery("button.ui-dialog-titlebar-close").addClass('important-style');
        //                jQuery("button.ui-dialog-titlebar-close .ui-button-text").remove();
        //            },1)
        
        jQuery("#selecionar-tipo-motivos .info-usuario #nome span").html(usuario.label);
        jQuery("#selecionar-tipo-motivos .info-usuario #email span").html(usuario.email);
        
        jQuery('#selecionar-tipo-motivos .alertaCampos, #selecionar-tipo-motivos .alertaSeparador').css('display','none');
        jQuery('#bloco_titulo_motivo, #bloco_conteudo_motivo').removeClass('erroField');
        jQuery('.alertaSeparador').css('display','block');
        
        //tabela_solicitacao_produto
        jQuery("#selecionar-tipo-motivos").dialog({
            autoOpen: false,
            minHeight: 300 ,
            maxHeight: 700 ,
            width: 400,
            modal: true,
            create: function( event, ui ) {               
                
                
                setTimeout(function(){
                    jQuery("button.ui-dialog-titlebar-close").addClass('important-style');
                    jQuery("button.ui-dialog-titlebar-close .ui-button-text").remove();
                },1)
                
                jQuery('#selecionar_todos').change(function(){
                    if (jQuery(this).is(":checked")) {
                        jQuery(".parametrizar_motivo_credito_item").attr('checked',true);
                    } else{
                        jQuery(".parametrizar_motivo_credito_item").attr('checked',false);
                    } 
                });
                
                jQuery(".parametrizar_motivo_credito_item").change(function(){
                    if (jQuery(".parametrizar_motivo_credito_item:checked").length == jQuery(".parametrizar_motivo_credito_item").length) {
                        jQuery('#selecionar_todos').attr('checked',true);
                    } else {
                        jQuery('#selecionar_todos').removeAttr('checked');
                    }
                });
                
            },
            buttons: {
                "Salvar": function() {
                    
                    jQuery('#selecionar-tipo-motivos .alertaCampos, #selecionar-tipo-motivos .alertaSeparador').css('display','none');
                    jQuery('#bloco_titulo_motivo, #bloco_conteudo_motivo').removeClass('erroField');
                    jQuery('.alertaSeparador').css('display','block');
                    
                    if (jQuery(".parametrizar_motivo_credito_item:checked").length == 0) {
                        jQuery('#selecionar-tipo-motivos .alertaCampos, #selecionar-tipo-motivos .alertaSeparador').css('display','block');
                        jQuery('.alertaSeparador').css('display','none');
                        jQuery('#bloco_titulo_motivo, #bloco_conteudo_motivo').addClass('erroField');
                        return false;
                    }
                    
                    var parametros = new Object();
                    
                    parametros.acao = "adicionarResponsavel";
                    parametros.id_usuario = usuario.id;
                    
                    parametros.motivos = new Object();
                    //resgato os valores marcados e nao marcado e quardo em um objeto
                    var i = 0;
                    
                    jQuery(".parametrizar_motivo_credito_item").each(function(){                        
                        if (jQuery(this).is(':checked')) {
                            parametros.motivos[i] = jQuery(this).attr('value');
                        }                        
                        i++;
                    });
                    
                    var data = jQuery.param(parametros);
                    
                    if (jQuery.trim(data) != '') {
                        enviarTiposMotivoUsuario(data);
                    }
                    
                },
                "Cancelar": function() {
                    fecharLimparModal();
                }
            },
            "Cancelar": function() {
                fecharLimparModal();
            }
        }).dialog('open');
    }
    
    
    jQuery('body').delegate('.excluir_reponsavel', 'click', function(){
        jQuery.fn.limpaMensagens();
        var confirm = window.confirm('Deseja realmente excluir este usuário?');
        
        if(!confirm) {
            return false;
        }
        
        var strId = jQuery(this).attr('id');
        var arrayId = strId.split('_');
        var id = arrayId[2];
        var self = jQuery(this);
        var usuarioId = jQuery(this).attr('data-usuario');
        
        jQuery.ajax({
            url: 'fin_credito_futuro_parametrizacao.php',
            type: 'post',
            data: {
                acao: 'excluirEmailResponsavel',
                cferoid: id,
                usuarioid: usuarioId
            },
            dataType: 'json',
            beforeSend: function() {
                resetFormErros();
                jQuery('#loader_responsavel').fadeIn();
                jQuery('#separador_loader_responsavel').show();
                jQuery('#msg_responsavel').removeClass('sucesso').removeClass('erro').removeClass('alerta').html('').hideMessage();
            },
            success: function(data) {
                
                if(data.status) {
                    
                    jQuery('#msg_responsavel').addClass('sucesso').html('Registro excluído com sucesso.').showMessage();
                                        
                    jQuery.ajax({
                        type: 'POST',
                        url: 'fin_credito_futuro_parametrizacao.php',
                        data: {
                            acao: 'listarResponsaveis'
                        },
                        success: function(data){
                            jQuery("#listagem-responsaveis").html(data);
                        }
                    });
                    
                } else {
                    jQuery('#msg_responsavel').addClass(data.tipoErro).html(data.mensagem).showMessage();
                }
                
                
                
            },            
            complete: function() {                
                jQuery('#loader_responsavel').fadeOut();
                jQuery('#separador_loader_responsavel').hide();
            },
            error: function() {
                jQuery('#msg_responsavel').addClass('erro').html('Houve um erro no processamento dos dados.').showMessage();
            }
        });
        
    });
    
    //jQuery('#conteudo_responsaveis tr:even').addClass('par');
    
    jQuery("#cfeavalor_credito_futuro").maskMoney({
        symbol:'', 
        thousands:'.', 
        decimal:',', 
        symbolStay: false,
        defaultZero: false
    });
    //jQuery("#cfeavalor_percentual_desconto").maskMoney({symbol:'', thousands:'.', decimal:'.', symbolStay: false, precision:2, defaultZero: false});
    jQuery("#cfeavalor_percentual_desconto").maskMoney({
        symbol:'', 
        thousands:',', 
        decimal:',', 
        symbolStay: false, 
        precision:2, 
        defaultZero: false
    });
    jQuery("#cfeaparcelas").mask("9?9",{
        placeholder:''
    });
	
    corrigeMaskMoney('cfeavalor_credito_futuro');
    corrigeMaskMoney('cfeavalor_percentual_desconto');
	
    /**
     * Corrige problema com o plugin maskMoney
     * @param id
     **/
    function corrigeMaskMoney(id){
        jQuery("#"+id).keyup(function() {
            var str= jQuery("#"+id).val();
            var n=str.replace(/\'/g,'');
            n = n.replace(/\"/g,'');
            n = n.replace(/\%/g,'');
            n = n.replace(/[a-zA-Z]/g,'');
            n = n.replace(/\(/g,'');
            n = n.replace(/\)/g,'');
            n = n.replace(/\]/g,'');
            n = n.replace(/\[/g,'');
            n = n.replace(/\}/g,'');
            n = n.replace(/\{/g,'');
            n = n.replace(/\=/g,'');
            n = n.replace(/\-/g,'');
            jQuery("#"+id).attr('value',n);
        });
        
        jQuery("#"+id).on('paste',function() { 
            setTimeout(function(){
                var str= jQuery.trim(jQuery("#"+id).val());
                var n = str;
            
                if (id == "cfeavalor_credito_futuro"){
                    // var objER  = new RegExp("[0-9]{2}.[0-9]{3},[0-9]{2}");
                    var verifica = str.replace(".","");
                    verifica = str.replace(",","");
                
                    if (IsNumeric(verifica) && verifica.length >= 7){
                        verifica2 = verifica.split("");
                        str = verifica2[0] + verifica2[1] + '.' + verifica2[2] + verifica2[3] + verifica2[4] + ',' + verifica2[5] + verifica2[6];
                    
                    }else if (IsNumeric(verifica) && verifica.length == 6) {
                        verifica2 = verifica.split("");
                        str = verifica2[0] + '.'+ verifica2[1]  + verifica2[2] + verifica2[3] + ',' + verifica2[4] + verifica2[5];
                    }else if (IsNumeric(verifica) && verifica.length == 5) {
                        verifica2 = verifica.split("");
                        str = verifica2[0] + verifica2[1]  + verifica2[2] + ',' + verifica2[3]  + verifica2[4];
                    }else if (IsNumeric(verifica) && verifica.length == 4) {
                        verifica2 = verifica.split("");
                        str = verifica2[0] + verifica2[1] + ',' + verifica2[2]  + verifica2[3];
                    }else if (IsNumeric(verifica) && verifica.length == 3) {
                        verifica2 = verifica.split("");
                        str = verifica2[0] + ',' + verifica2[1]  + verifica2[2];
                    }else if (IsNumeric(verifica) && verifica.length == 2) {
                        verifica2 = verifica.split("");
                        str = '0' + ',' + verifica2[0]  + verifica2[1];
                    }else if (IsNumeric(verifica) && verifica.length == 1) {
                        verifica2 = verifica.split("");
                        str = '00,' + '0' + verifica2[0];
                    }
                
                    if (!IsNumeric(verifica)) {
                        n = '';
                    }else{
                        n = str;
                    }
                
                } else if (id == "cfeavalor_percentual_desconto") {
                    //var objER1  = new RegExp("[0-9]{2}.[0-9]{1}");
                
                    if (str.length > 1) {
                        var stringReserva = parseFloat(str);
                    }else{
                        var stringReserva = parseFloat(str);
                    }
                    stringReserva = String(stringReserva);
                    //console.log(stringReserva);
                
                    var verifica = stringReserva.replace(",","");
                
                    if (IsNumeric(verifica) && verifica.length >= 5){
                        verifica2 = verifica.split("");
                        str = verifica2[0] + verifica2[1] + verifica2[2] + ',' + verifica2[3] + verifica2[4];
                    }else if (IsNumeric(verifica) && verifica.length == 4) {
                        str = verifica2[0] + verifica2[1] + ',' + verifica2[2] + verifica2[3];
                    }else if (IsNumeric(verifica) && verifica.length == 3){
                        verifica2 = verifica.split("");
                        str = verifica2[0] + verifica2[1] + ',' + verifica2[2];
                    }else if (IsNumeric(verifica) && verifica.length== 2) {
                        verifica2 = verifica.split("");
                        str = verifica2[0] + ',' + verifica2[1];
                    }else if (IsNumeric(verifica) && verifica.length== 1) {
                        verifica2 = verifica.split("");
                        str = verifica2[0] + ',0';
                    }
                    var countString = str.split('.');
                
                    if (!IsNumeric(verifica) && (countString.length == 1 || !IsNumeric(countString[0]) || !IsNumeric(countString[1]) )) {
                        n = '';
                    }else{
                        n = str;
                    }
                }
            
                jQuery("#"+id).attr('value',n);
            },50);

        });
    }
    
    function IsNumeric(input){
        var RE = /^-{0,1}\d*\.{0,1}\d+$/;
        return (RE.test(input));
    }
    
    
    jQuery('#confirmar').click(function(event){
        jQuery.fn.limpaMensagens();
		 
        event.preventDefault();
		 
        jQuery('.alerta').hideMessage();
        jQuery('.sucesso').hideMessage();
		 
        var mensagem = [];
        var dados = [];
        var validaObrigatorio = true; 
        var verificaUsuariosCadastrado = true;

        //verifico se há responsaveis cadastrado
        if (jQuery('#conteudo_responsaveis tr').size() < 1) {
            verificaUsuariosCadastrado = false;
			
            mensagem.push({
                mensagem: "Pelo menos um responsável deve ser informado."
            });
        }

        //valida se todos os campos obrigatorio estão preenchidos
        jQuery('.requerido').each(function(){
            if(jQuery.trim(jQuery(this).val()) == "") {
                dados.push({
                    campo: jQuery(this).attr('id'),
                    mensagem: "Campo obrigatório."	
                });	
                validaObrigatorio = false;											
            }
        });

        //verifico se o campo valor de credito fututo é maior que zero
        if (jQuery.trim(jQuery('#cfeavalor_credito_futuro').val()) != "" && jQuery.trim(jQuery('#cfeavalor_credito_futuro').val()) == 0) {
            dados.push({
                campo: 'cfeavalor_credito_futuro',
                mensagem: "Valor inválido."	
            });	
			
            mensagem.push({
                mensagem: "O valor do crédito futuro deve ser maior que 0."
            });
        }
		
        //O percentual de desconto do crédito futuro for maior que:
        if(jQuery.trim(jQuery('#cfeavalor_percentual_desconto').val()) != "") {

            if (jQuery.trim(parseFloat(jQuery('#cfeavalor_percentual_desconto').val())) >= 100) {
				
                dados.push({
                    campo: 'cfeavalor_percentual_desconto',
                    mensagem: "Valor inválido."	
                });	

                mensagem.push({
                    mensagem: "Percentual de desconto do crédito futuro deve ser menor que 100%."
                });
				
            } else if (jQuery.trim(parseFloat(jQuery('#cfeavalor_percentual_desconto').val())) == 0) {

                dados.push({
                    campo: 'cfeavalor_percentual_desconto',
                    mensagem: "Valor inválido."	
                });	
				
                mensagem.push({
                    mensagem: "O percentual de desconto do crédito futuro deve ser maior que 0."
                });
				
            }
			
        } 

        //A quantidade de parcelas do crédito futuro for maior que:
        if(jQuery.trim(jQuery('#cfeaparcelas').val()) != "") {

            if (jQuery.trim(jQuery('#cfeaparcelas').val()) == 1)  {
                dados.push({
                    campo: 'cfeaparcelas',
                    mensagem: "Valor inválido."	
                });	

                mensagem.push({
                    mensagem: "Quantidade de parcelas do crédito futuro deve ser maior que 1."
                });

            } else if (jQuery.trim(jQuery('#cfeaparcelas').val()) == 0) {
                dados.push({
                    campo: 'cfeaparcelas',
                    mensagem: "Valor inválido."	
                });	

                mensagem.push({
                    mensagem: "A quantidade de parcelas do crédito futuro deve ser maior que 0."
                });
            }
			
        }


        //inicio da validação do corpo de e-mail
        var str = jQuery('#cfeacorpo').val();
        var percentual = str.match(/\[PERCENTUAL\]/g) ? true : false;
        var valor = str.match(/\[VALOR\]/g)  ? true : false;
        var qtdparcelas = str.match(/\[QTD.PARCELAS\]/g) ? true : false;					
		
        if (!percentual || !valor || !qtdparcelas) {
            //mensagem O Corpo do E-mail deve conter as expressões [VALOR], [PERCENTUAL] e [QTD.PARCELAS]”
			
            dados.push({
                campo: 'cfeacorpo',
                mensagem: "Valor inválido."	
            });	
			
            mensagem.push({
                mensagem: "O Corpo do E-mail deve conter as expressões [VALOR], [PERCENTUAL] e [QTD.PARCELAS]."
            });
        }

        //fim da validação do corpo de e-mail

        //validação de combobox
        //Contestação de valor
		
        var comboTeste = true;
		
        var contestacaoValor = jQuery('#cfeaobroid_contestacao').val();

        if (jQuery.trim(jQuery('#cfeaobroid_contestacao').val()) == "") {
            dados.push({
                campo: 'cfeaobroid_contestacao',
                mensagem: "Campo obrigatório."	
            });	
            validaObrigatorio = false;
        } else if (comparaSelect(jQuery('#cfeaobroid_contestacao').attr('id') , contestacaoValor)) {
            dados.push({
                campo: 'cfeaobroid_contestacao'
            //mensagem: ""	
            });	

            comboTeste = false;
        //exibe mensagem  As obrigações financeiras devem ser diferentes uma das outras.
        }


        //validação de combobox
        //Contas a receber
		
        var contasAreceber = jQuery('#cfeaobroid_contas').val();

        if (jQuery.trim(jQuery('#cfeaobroid_contas').val()) == "") {
            dados.push({
                campo: 'cfeaobroid_contas',
                mensagem: "Campo obrigatório."	
            });	
            validaObrigatorio = false;
        } else if (comparaSelect(jQuery('#cfeaobroid_contas').attr('id') , contasAreceber)) {
            dados.push({
                campo: 'cfeaobroid_contas'
            //mensagem: ""	
            });	

            comboTeste = false;
        //exibe mensagem  As obrigações financeiras devem ser diferentes uma das outras.
        }



        //validação de combobox
        //campanha promocional
		
        var campanhaPromocional = jQuery('#cfeaobroid_campanha').val();

        if (jQuery.trim(jQuery('#cfeaobroid_campanha').val()) == "") {
            dados.push({
                campo: 'cfeaobroid_campanha',
                mensagem: "Campo obrigatório."	
            });	
            validaObrigatorio = false;
        } else if (comparaSelect(jQuery('#cfeaobroid_campanha').attr('id') , campanhaPromocional)) {
            dados.push({
                campo: 'cfeaobroid_campanha'
            //mensagem: ""	
            });	

            comboTeste = false;
        //exibe mensagem  As obrigações financeiras devem ser diferentes uma das outras.
        }

        //verifica se há campos obrigatorios não preenchidos e exibe a mensagem
        if (!validaObrigatorio) {
            mensagem.push({
                mensagem: "Existem campos obrigatórios não preenchidos."
            });
        }

        //verifica se os combos estão com valores iguais para mostrar a mensagem
        if (!comboTeste){
            mensagem.push({
                mensagem: "As obrigações financeiras devem ser diferentes uma das outras."
            });
        }
		
        //funcção que compara valor das combobox, se esta repetindo ou não
        function comparaSelect(idSelect,valorSelecionado){
            var invalido = false;
            jQuery('select').each(function(){
                if (jQuery(this).val() == valorSelecionado && jQuery(this).attr('id') != idSelect) {
                    dados.push({
                        campo: jQuery(this).attr('id')
                    //mensagem: ""	
                    });
                    invalido = true;
                }								
            });

            return invalido;
        }

        //verrifico se existe erros
        if (dados.length > 0 || !verificaUsuariosCadastrado) {
            showFormErros(dados);
            if (mensagem.length > 0) {
                //verrifico se existe mensagem e mostro
                jQuery.each(mensagem, function(index, value) {
                    jQuery('#infoParametrizacao').after('<div class="mensagem alerta">' + value.mensagem + '</div>');
                });
            }
				
        } else {
            //envia formularios
            jQuery('#acao').attr('value','emailAprovacao');
            jQuery('#form').submit();
        }
		
		
    });
    
    
});

jQuery.fn.limpaMensagens = function() {
    jQuery('.mensagem').not('.info').hideMessage();
    jQuery(".erro").not('.mensagem').removeClass("erro");
    resetFormErros();
}

// List of HTML entities for escaping.
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