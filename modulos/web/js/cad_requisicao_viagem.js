jQuery(document).ready(function(){

    var isReembolso = jQuery('#reembolso').val();

    if (isReembolso) {
        jQuery('#mensagem_sucesso_prestacao').html('Prestação de contas incluída com sucesso.').show();
    }

    jQuery('#placaVeiculo').mask('aaa-9999');
    jQuery('#placaVeiculo').change(function() {
        jQuery(this).val(jQuery(this).val().toUpperCase());
    });

    jQuery('#dtPartida').periodo('#dtRetorno');

    jQuery('#numeroRequisicao, #distancia').keyup(function() {
        somenteNumeros(this);
    }).change(function() {
        somenteNumeros(this);
    });

    jQuery('#bt_limpar').click(function() {
        limparCampos();
    });

    jQuery('#bt_limparCadastro').click(function() {
        limparCampos();
        limparCampos('Adiantamento');
        limparCampos('Combustivel');
    });

    jQuery('#idaVolta').change(function() {
        // A = Somente Ida
        // I = Ida/Volta
        if(jQuery(this).val() == 'I') {
            jQuery('#divDataRetorno').fadeIn();
        } else {
            jQuery('#divDataRetorno').fadeOut();
            jQuery('#dtRetorno').val('');
        }
    });

    jQuery('#centroCusto').change(function() {

        comboCentroCusto = jQuery('#centroCusto');
        comboAprovadores = jQuery('#aprovador');

        jQuery.ajax({
            url: 'cad_requisicao_viagem.php',
            type: 'POST',
            dataType: 'json',
            data: {
                acao: 'ajaxBuscarAprovadoresCentroCusto',
                idCentroCusto: comboCentroCusto.val()
            },
            beforeSend: function() {
                jQuery('#bt_confirmar').attr('disabled','disabled');

                comboAprovadores.html('<option value="">- Selecione -</option>');
                comboAprovadores.attr('disabled', 'disabled');
            },
            success: function(data) {

                var opcoesAprovadoresCentroCusto = '<option value="">- Selecione -</option>';
                var selecionado = '';

                jQuery.each(data, function(key, value) {
                    // postAprovador é um input hidden que guarda a id
                    // do aprovador que está na variável do php $_POST['aprovador']
                    if(jQuery('#postAprovador').val() == value.cd_usuario) {
                        selecionado = 'selected="selected"';
                    } else {
                        selecionado = '';
                    }
                    opcoesAprovadoresCentroCusto += '<option value="' + value.cd_usuario + '"' + selecionado + '>' + value.nm_usuario + '</option>';
                });

                comboAprovadores.html(opcoesAprovadoresCentroCusto);

                if(comboCentroCusto.val() != '') {
                    comboAprovadores.removeAttr('disabled');
                }

                jQuery('#bt_confirmar').removeAttr('disabled');
            }
        });
    });

    jQuery('#tipoRequisicao').change(function() {

        jQuery('#blocoAdiantamento').hide();
        jQuery('#blocoCombustivel').hide();

        if(jQuery('#tipoRequisicao').val() === '' || jQuery('#tipoRequisicao').val() == 'L') {
            jQuery('#blocoTiposRequisicao').fadeOut();
            return false;
        } else {
            jQuery('#blocoTiposRequisicao').removeAttr('style');
        }

        if(jQuery('#tipoRequisicao').val() === 'A') {
            jQuery('#blocoCombustivel').fadeOut(function() {
                limparCampos('Combustivel');
                jQuery('#blocoAdiantamento').fadeIn();
            });
        } else if (jQuery('#tipoRequisicao').val() === 'C') {
            jQuery('#blocoAdiantamento').fadeOut(function() {
                limparCampos('Adiantamento');
                jQuery('#blocoCombustivel').fadeIn();
            });
        }

        return false;
    });


    jQuery('#dataCredito').change(function() {
        var dataCredito = jQuery('#dataCredito');
        var dataHoje = new Date();
        var dataParaCreditoFormatada = dataCredito.val().substr(3, 2) + "/" + dataCredito.val().substr(0, 2) + "/" + dataCredito.val().substr(6, 4);
        var dataParaCredito = new Date(dataParaCreditoFormatada);

        if(jQuery('#tipoRequisicao').val() == 'C' || jQuery(this).val() == '') {
            return false;
        }

        jQuery.ajax({
            url: 'cad_requisicao_viagem.php',
            type: 'POST',
            dataType: 'json',
            data: {
                acao: 'ajaxBuscarFeriados'
            },
            beforeSend: function() {
                jQuery('#bt_confirmar').attr('disabled','disabled');
            },
            success: function(data) {
                var feriados = data;

                dataParaCreditoFormatada = dataParaCredito.getDate() + "/"  + (dataParaCredito.getMonth()+1) + "/" + dataParaCredito.getFullYear()

                while((dataParaCredito.getTime() > dataHoje.getTime() && dataParaCredito.getDay() != 4) || jQuery.inArray(dataParaCreditoFormatada, feriados) > -1) {
                    dataParaCredito.setDate(dataParaCredito.getDate()-1);
                    dataParaCreditoFormatada = dataParaCredito.getDate() + "/"  + (dataParaCredito.getMonth()+1) + "/" + dataParaCredito.getFullYear();
                }

                if(dataParaCredito.getDay() == 4) {
                    var dataFormatada = dataParaCredito.getDate() + "/"  + (dataParaCredito.getMonth()+1) + "/" + dataParaCredito.getFullYear();
                    dataCredito.val(dataFormatada);
                    jQuery('#mensagem_alerta').fadeOut();
                } else if(dataCredito.val() != '') {
                    jQuery('#mensagem_alerta').html("Os pagamentos de adiantamentos são feitos nas quintas-feiras, " +
                        "favor informar uma data posterior à data " + dataCredito.val());
                    jQuery('#mensagem_alerta').fadeOut();
                    jQuery('#mensagem_alerta').fadeIn();
                    dataCredito.val('');
                }

                jQuery('#bt_confirmar').removeAttr('disabled');
            }
        });
    });


    jQuery('#empresa').change(function() {


        var idEmpresa = jQuery('#empresa').val();

        
        if (jQuery('#centroCusto').attr('type') == 'hidden') {
            return false;
        }

        jQuery('#centroCusto').html('<option value="">Escolha</option>');
        
        if(idEmpresa == '') {
            return false;
        }

        
        jQuery.ajax({
            url: 'cad_requisicao_viagem.php',
            type: 'POST',
            dataType: 'json',
            data: {
                acao: 'ajaxBuscarCentrosCusto',
                idEmpresa: idEmpresa
            },
            beforeSend: function() {
                jQuery('#bt_confirmar').attr('disabled','disabled');
            },
            success: function(data) {
                var opcoesComboCentroCusto = '';
                var selecionado = '';

                jQuery.each(data, function(key, value) {
                    // postCentroCusto é um input hidden que guarda o valor
                    // do centro de custo que está na variável do php $_POST['centroCusto']
                    if(jQuery('#postCentroCusto').val() == value.cntoid) {
                        selecionado = 'selected="selected"';
                    } else {
                        selecionado = '';
                    }
                    opcoesComboCentroCusto += '<option value="' + value.cntoid + '"' + selecionado + '>' + value.cntconta + '</option>';
                });

                jQuery('#centroCusto').append(opcoesComboCentroCusto);

                jQuery('#bt_confirmar').removeAttr('disabled');

                jQuery('#centroCusto').trigger('change');
            }
        });

        return true;
    });

    jQuery('#estadoOrigem, #estadoDestino').change(function() {
        var idEstado = jQuery(this).val();
        var comboCidades;
        var postCidade;

        if(jQuery('#tipoRequisicao').val() != 'C') {
            return false;
        }

        if(jQuery(this).attr('id') == 'estadoOrigem') {
            comboCidades = jQuery('#cidadeOrigem');
            postCidade = jQuery('#postCidadeOrigem');
        } else if(jQuery(this).attr('id') == 'estadoDestino') {
            comboCidades = jQuery('#cidadeDestino');
            postCidade = jQuery('#postCidadeDestino');
        } else {
            return false;
        }

        jQuery(comboCidades).html('<option value="">Escolha</option>');

        jQuery.ajax({
            url: 'cad_requisicao_viagem.php',
            type: 'POST',
            dataType: 'json',
            data: {
                acao: 'ajaxBuscarCidadesEstado',
                idEstado: idEstado
            },
            beforeSend: function() {
                jQuery('#bt_confirmar').attr('disabled','disabled');
            },
            success: function(data) {
                var opcoesComboCidades = '';
                var selecionado = '';

                jQuery.each(data, function(key, value) {
                    // postCidade é um input hidden que guarda o valor
                    // da cidade que está na variável do php $_POST['cidadeOrigem'] ou $_POST['cidadeDestino']
                    if(postCidade.val() == value.cidoid) {
                        selecionado = 'selected="selected"';
                    } else {
                        selecionado = '';
                    }
                    opcoesComboCidades += '<option value="' + value.cidoid + '"' + selecionado + '>' + value.ciddescricao + '</option>';
                });

                comboCidades.append(opcoesComboCidades);

                jQuery('#bt_confirmar').removeAttr('disabled');
            }
        });

        return true;
    });

    jQuery('#distancia').blur(function() {

        if(jQuery('#tipoRequisicao').val() != 'C') {
            return false;
        }

        jQuery.ajax({
            url: 'cad_requisicao_viagem.php',
            type: 'POST',
            dataType: 'json',
            data: {
                acao: 'ajaxBuscarDadosConsumo'
            },
            beforeSend: function() {
                jQuery('#bt_confirmar').attr('disabled','disabled');
                jQuery('#litrosDistancia').html('');
                jQuery('#creditoDistancia').html('');
            },
            success: function(data) {
                var distanciaPercorrida = jQuery('#distancia').val();
                var litrosConsumidos = parseFloat(distanciaPercorrida/data.acckmlitro);
                var creditoConsumido = parseFloat(litrosConsumidos*data.accvalorlitro);

                jQuery('#litrosDistancia').html(litrosConsumidos.toFixed(2).replace('.', ','));
                jQuery('#creditoDistancia').html(creditoConsumido.toFixed(2).replace('.', ','));

                jQuery('#bt_confirmar').removeAttr('disabled');
            }
        });

        return true;
    });


    //Trecho de código que corrige falhas do maskmoney no onpaste e keypress

    jQuery(".moeda").maskMoney({
        symbol:'',
        thousands:'.',
        decimal:',',
        symbolStay: false,
        showSymbol:false,
        precision:2,
        defaultZero: false,
        allowZero: false
    });

    jQuery(".moeda").on('paste',function(){
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

    jQuery(".moeda").on('keyup',function(){
        var id = jQuery(this).attr('id');
        jQuery("#"+id).val( maskValue(jQuery("#"+id).val()) );
    });

    settings = {};
    settings.allowNegative = false;
    settings.decimal = ',';
    settings.precision = 2;
    settings.thousands = '.';

    //botão novo
    jQuery("#bt_novo").click(function(){
        window.location.href = "cad_requisicao_viagem.php?acao=cadastrar";
    });

    //botão gravar
    jQuery("#bt_confirmar").click(function(){
        if(parseInt(jQuery('#idRequisicao').val()) > 0) {
            jQuery('#acao').val('editar');
        } else {
            jQuery('#acao').val('cadastrar');
        }
        jQuery('#form_cadastrar').submit();
    });

    jQuery("#bt_confirmarAprovacaoRequisicao").click(function(){
        jQuery('#acao').val('salvarAprovacaoRequisicao');
        jQuery('#form_cadastrar').submit();
    });

    jQuery("#bt_confirmarConferencia").click(function(){
        jQuery('#acao').val('salvarConferenciaPrestacaoContas');
        jQuery('#form_cadastrar').submit();
    });

    //botão voltar
    jQuery("#bt_voltar").click(function(){
        window.location.href = "cad_requisicao_viagem.php";
    });

    jQuery('#empresa, #tipoRequisicao, #estadoOrigem, #estadoDestino, #idaVolta').trigger('change');
    jQuery('#distancia').trigger('blur');

    //======================= Formulario Prestação de Contas: INICIO ====================

    jQuery("#adigvalor_unitario").maskMoney({
        symbol:'',
        thousands:'',
        decimal:',',
        symbolStay: false,
        precision:2,
        defaultZero: false
    });

     corrigeMaskMoney('adigvalor_unitario');

     jQuery("#adignota").mask("9?9999999" ,{placeholder:''});

    /**
     * Ações da combo Tipo de Despesa
     */
    jQuery("#adigtdpoid").change(function(){

            jQuery("#adigobs").removeClass('validar_obrigatorio');
            jQuery("#obs_despesa").text("Observações");

            jQuery("#adigtdpoid option").each(function(){

                if (jQuery(this).attr('selected') == 'selected') {
                    selecionado = jQuery(this).text();
                }

            });

           if(jQuery.trim(selecionado.toLowerCase()) == 'outras despesas') {
               jQuery("#adigobs").addClass('validar_obrigatorio');
               jQuery("#obs_despesa").text("Observações *");
           }

      });

    
    jQuery('.excluirItem').click(function(){

        var chave = jQuery(this).attr('id-item');
        var adioid = jQuery('#adioid').val();

        if (!confirm('Deseja excluir essa despesa?')) {
            return false;
        }

        jQuery.ajax({
            url: 'cad_requisicao_viagem.php',
            type: 'POST',
            data: {
                'acao': 'excluirItemSessao',
                'chave': chave,
                'adioid': adioid
            },
            success : function(data){
                 location.reload();
            }
        });

    })
    
    jQuery('.editarItem').click(function(){

        jQuery('#bt_adicionar').html('Salvar');

        var chave = jQuery(this).attr('id-item');

        var data_despesa = jQuery('#in_data_despesa_'+chave).val();
        var tipo_despesa = jQuery('#in_tipo_despesa_'+chave).val();
        var valor_despesa = jQuery('#in_valor_despesa_'+chave).val();
        var numero_nota = jQuery('#in_numero_nota_'+chave).val();
        var observacao_prestacao_contas = jQuery('#in_observacao_prestacao_contas_'+chave).val();

        var combo = "";
        jQuery.each(jQuery('#adigtdpoid option'), function(key, value){

            combo = jQuery(value).attr('value').split('|');

            if (combo[0] == tipo_despesa) {
                jQuery(value).attr('selected', 'selected');
                
                if (combo[1] == 'OUTRAS DESPESAS') {
                	jQuery("#obs_despesa").text("Observações *");
                }
                
                return false;
            }

        })

        jQuery('#chave').val(chave);
        jQuery('#adigdt_despesa').val(data_despesa);
        jQuery('#adigvalor_unitario').val(valor_despesa);
        jQuery('#adignota').val(numero_nota);
        jQuery('#adigobs').val(observacao_prestacao_contas);


    })

    jQuery('#bt_imprimir_prestacao').click(function(){

        var adioid = jQuery('input[name="adioid"]').val();

        jQuery.ajax({
            url: 'cad_requisicao_viagem.php',
            type: 'POST',
            data: {
                'acao': 'imprimirPrestacaoContas',
                'adioid': adioid
            },
            beforeSend: function() {
                jQuery('.carregando_item').show();
            },
            success : function(data){

                var relatorio_viagem = window.open('','','width=800,height=500,scrollbars=1');
                relatorio_viagem.document.write(data);
                relatorio_viagem.focus();

                jQuery('.carregando_item').hide();  

            }
        });

    })

    /**
     * Ações do botão Adicionar despesa
     */
     jQuery("#bt_adicionar").click(function(){

        //Remove a formatação de erros e esconde msgs.
        limparErrosTela();

        var erros = new Array();
        var acao = jQuery('#acao').val();

        erros = validarCamposObrigatorios();

        if (erros.length > 0) {

             jQuery("#mensagem_alerta_prestacao").text("Existem campos obrigatórios não preenchidos.");
             jQuery("#mensagem_alerta_prestacao").show();

             for(i=0;i<erros.length; i++) {
                  jQuery("#"+erros[i]).addClass('erro');
             }
        } else if (!validarDataPrestacao()) {

            jQuery("#mensagem_alerta_prestacao").text("Data da despesa não pode ser maior que a data atual.");
            jQuery("#mensagem_alerta_prestacao").show();
            jQuery("#adigdt_despesa").addClass('erro');

        } else {
        	
        	 var tipoDespesa = jQuery('#adigtdpoid').val();
             var arrTipoDespesa = tipoDespesa.split('|');
             var id_tipo_despesa = arrTipoDespesa[0];
             var tipo_despesa = arrTipoDespesa[1];
             var chave = jQuery('#chave').val();
             var data_despesa = jQuery('#adigdt_despesa').val();
             var valor_despesa = jQuery('#adigvalor_unitario').val();
             var numero_nota = jQuery('#adignota').val();
             var solicitar_reembolso_para = jQuery('#solicitar_reembolso_para').val();
             var observacao_prestacao_contas = jQuery('#adigobs').val();
             var adioid = jQuery('#adioid').val();
             var tipoRequisicao = jQuery('#tipoRequisicao').val();
             
             var total_adiantamento = jQuery('#total_adiantamento').val();
             var total_despesas = jQuery('#total_despesas').val();
             
             if(tipo_despesa == 'OUTRAS DESPESAS') {

                 if(jQuery("#adigobs").val().length < 10) {

                	 jQuery("#mensagem_alerta_prestacao").text("O campo Observações precisa ter mais de 10 caracteres.");
                	 jQuery("#adigobs").css({
                		 'background-color': '#FCF8E3',
                	     'border': '1px solid #A47E3C',
                	     'color': '#A47E3C'
                	 });
                     jQuery("#mensagem_alerta_prestacao").show();
                     return;
                 };
              }
        	
        	jQuery.ajax({
                url: 'cad_requisicao_viagem.php',
                type: 'POST',
                data: {
                    'acao': 'inserirItemSessao',
                    'chave': chave,
                    'adioid': adioid,
                    'data_despesa': data_despesa,
                    'tipo_despesa': tipo_despesa,
                    'id_tipo_despesa': id_tipo_despesa,
                    'valor_despesa': valor_despesa,
                    'numero_nota': numero_nota,
                    'solicitar_reembolso_para': solicitar_reembolso_para,
                    'observacao_prestacao_contas': observacao_prestacao_contas,
                    'tipoRequisicao' : tipoRequisicao
                },
                beforeSend: function() {
                    jQuery('.carregando_item').show();
                },
                success : function(data){

                    if (acao == 'editar') {
                        window.location = 'cad_requisicao_viagem.php?acao=editar&idRequisicao=' + adioid  + '&alterarItens=1';
                        //window.location = window.location + '&alterarItens=1';
                    } else if (acao == 'cadastrar') {
                        jQuery('#acao').val('cadastrar');
                        jQuery('#form_cadastrar').submit();
                    }
                }
            });
        	
            //TODO Ações de inclusão do item.
        }

     });
    
    jQuery('#bt_limpar_prestacao').click(function(){
        jQuery('#chave').val('');
        jQuery('#adigdt_despesa').val('');
        jQuery('#adigtdpoid').val('');
        jQuery('#adigvalor_unitario').val('');
        jQuery('#adignota').val('');
        jQuery('#obs_despesa').val('');
    })
    
    jQuery('#bt_confirmar_prestacao').click(function(){

        jQuery('#solicitar_reembolso_para').css('background', '#FFF');

        var adioid = jQuery('input[name="adioid"]').val();
        var flag_registro_bd = jQuery('#flag_registro_bd').val();
        var identificador_aprovador = "";
        var email_aprovador = "";

        var tipoRequisicao = jQuery('#tipoRequisicao').val();
        var idSolicitante = jQuery('#solicitante').val();
        var idEmpresa = jQuery('#empresa').val();
        var centroCusto = jQuery('#centroCusto').val();
        var justificativa = jQuery('#justificativa').val();

        if (jQuery('#solicitar_reembolso_para').length > 0) {

            identificador_aprovador = jQuery('#solicitar_reembolso_para').val();
            var arrIdentificador = identificador_aprovador.split('|');
            email_aprovador = arrIdentificador[1];

            if (jQuery.trim(identificador_aprovador).length == 0) {
            	jQuery("#mensagem_alerta_prestacao").text("O campo 'Solicitar Aprovação Para' é obrigatório.");
	           	 jQuery("#solicitar_reembolso_para").css({
	           		 'background-color': '#FCF8E3',
	           	     'border': '1px solid #A47E3C',
	           	     'color': '#A47E3C'
	           	 });
                jQuery("#mensagem_alerta_prestacao").show();
                return false;
            }
        }

        if (!confirm('Atenção, ao confirmar a prestação de contas, não será possível edita-la novamente, deseja mesmo confirmar?')) {
            return;
        }

        jQuery.ajax({
            url: 'cad_requisicao_viagem.php',
            type: 'POST',
            dataType: 'json',
            data: {
                'acao': 'salvarPrestacaoContas',
                'adioid' : adioid,
                'email_aprovador' : email_aprovador,
                'flag_registro_bd': flag_registro_bd,
                'tipoRequisicao': tipoRequisicao,
                'idSolicitante': idSolicitante,
                'idEmpresa': idEmpresa,
                'centroCusto': centroCusto,
                'justificativa': justificativa
            },
            beforeSend: function() {
                jQuery('.carregando_item').show();
            },
            success : function(data){

                if (data == '') {
                    location.reload();     
                } else {
                    window.location = data;
                }

            	
            }
        });

    })


    //======================= Formulario Prestação de Contas: FIM =======================

    //====================== APROVACAO REEMBOLSO ==========================
     
     jQuery('#bt_confirmarAprovacaoReembolso').click(function(){
    	 
    	 jQuery('#mensagemAprovacaoReembolso').removeClass('alerta').addClass('invisivel').html('');
    	 jQuery('#valorAprovacaoReembolso').removeClass('erro');
    	 jQuery('#observacoesAprovacaoReembolso').removeClass('erro');
    	 jQuery('#statusAprovacaoReembolso').removeClass('erro');
    	 
    	 if (jQuery('#valorAprovacaoReembolso').val() == '' ||
			 jQuery('#observacoesAprovacaoReembolso').val() == '' ||
    		 jQuery('#statusAprovacaoReembolso').val() == '') {
    		 
    		 if (jQuery('#valorAprovacaoReembolso').val() == '') {
    			 jQuery('#valorAprovacaoReembolso').addClass('erro');
    		 } else {
    			 jQuery('#valorAprovacaoReembolso').removeClass('erro');
    		 }
    		 
    		 if (jQuery('#observacoesAprovacaoReembolso').val() == '') {
    			 jQuery('#observacoesAprovacaoReembolso').addClass('erro');
    		 } else {
    			 jQuery('#observacoesAprovacaoReembolso').removeClass('erro');
    		 }
    		 
    		 if (jQuery('#statusAprovacaoReembolso').val() == '') {
    			 jQuery('#statusAprovacaoReembolso').addClass('erro');
    		 } else {
    			 jQuery('#statusAprovacaoReembolso').removeClass('erro');
    		 }
    		 
    		 jQuery('#mensagemAprovacaoReembolso').addClass('alerta').removeClass('invisivel').html('Existem campos obrigatórios não preenchidos.');
    		 
    		 return;
    	 }

    	 
    	 jQuery('#acao').val('salvarAprovacaoReembolso');
         jQuery('#form_cadastrar').submit();    	 
     })
});

function somenteNumeros(elemento) {
    var padrao = /[^0-9]/g;
    var novoValor = jQuery(elemento).val().replace(padrao, '');

    jQuery(elemento).val(novoValor);
}

function limparCampos(tipoRequisicao) {

    if(tipoRequisicao == undefined) {
        jQuery('#empresa').val('');
        jQuery('#empresa').trigger('change');
        jQuery('#statusSolicitacao').val('');
        jQuery('#tipoRequisicao').val('');
        jQuery('#tipoRequisicao').trigger('change');
        jQuery('#justificativa').val('');
        jQuery('#numeroRequisicao').val('');
        jQuery('#solicitante').val('');
    } else {

        if(tipoRequisicao == 'Adiantamento') {
            jQuery('#valorAdiantamento').val('');
            jQuery('#dataCredito').val('');
        } else if(tipoRequisicao == 'Combustivel') {
            jQuery('#projeto').val('');
            jQuery('#idaVolta').val('');
            jQuery('#dtPartida').val(jQuery('#dataAtual').val());
            jQuery('#dtRetorno').val(jQuery('#dataAtual').val());
            jQuery('#placaVeiculo').val('');
            jQuery('#estadoOrigem').val('');
            jQuery('#cidadeOrigem').val('');
            jQuery('#estadoDestino').val('');
            jQuery('#cidadeDestino').val('');
            jQuery('#distancia').val('');
        }

        jQuery('#aprovador').val('');
    }
}


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
}

/**
* Valida os campos obrigatórios do formulário de Prestação de Contas
* @return array
 */
function validarCamposObrigatorios() {

   var erros = new Array();
   var campos = new Array();
   var c = 0;

   campos[0] = 'adigdt_despesa';
   campos[1] = 'adigvalor_unitario';
   campos[2] = 'adigtdpoid';
   campos[3] = 'adignota';

    validarObs = jQuery("#adigobs.validar_obrigatorio").attr('class');

   if(validarObs == 'validar_obrigatorio'){
       campos[4] = 'adigobs';
   }

   for(i=0; i < campos.length; i++) {
       if (jQuery("#"+campos[i]).val() == '') {
           erros[c] = campos[i];
           c++;
       }
   }

   return erros;
}

/**
 * Limpa a formatação de Erros em tela
 */
function limparErrosTela() {

    jQuery("#mensagem_alerta_prestacao").hide();
    jQuery("#mensagem_sucesso_prestacao").hide();
    jQuery('.erro').removeClass('erro');

}

/**
 * Data informada da perstaçãod e contas informada não pode ser maior que a atual.
 */
function validarDataPrestacao() {

    var dataCompletaInformada = jQuery("#adigdt_despesa").val();
    var dataCompleta = dataCompletaInformada.split('/');
    var dataInformada = new Date();
    dataInformada.setDate(dataCompleta[0]);
    dataInformada.setMonth(dataCompleta[1]-1);
    dataInformada.setFullYear(dataCompleta[2]);

    var dataAtual = new Date();

    if (dataInformada > dataAtual) {
        return false;
    }
    return true;
}

