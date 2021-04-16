jQuery(document).ready(function() {

     /*
     *Incrementa / Decrementa qtd disponivel em estoque
     */
    jQuery(".parametrizao_qtd").blur(function(){

        var idElemento = this.id;
        var idProduto = idElemento.substr(19);

        var qtdeOriginal = jQuery(".qtdeInicial_" + idProduto).val();
        var qtdeReservado = jQuery(".reservado_estoque_" + idProduto).val();

        var saldo =  (parseInt(qtdeOriginal) - parseInt(qtdeReservado));
        saldo = (saldo > 0) ? saldo : 0;

        jQuery('#qtde_'+idProduto).text(saldo.toString());

    });

    jQuery(".parametrizao_transito_qtd").blur(function(){

        var idElemento = this.id;
        var idProduto = idElemento.substr(17);
        
        var qtdeOriginal = jQuery(".qtdeInicial_transito_" + idProduto).val();
        var qtdeReservado = jQuery(".reservado_transito_" + idProduto).val();

        var saldo =  (parseInt(qtdeOriginal) - parseInt(qtdeReservado));
        saldo = (saldo > 0) ? saldo : 0;

        jQuery('#qtde_transito_'+idProduto).text(saldo.toString());

    });

    jQuery( "body" ).delegate( ".somenteNumero", "focus", function() {
        jQuery(this).mask('9?99',{
            placeholder:''
        });
    });

    jQuery('.campo.direita.mini').click(function(){
        jQuery(this).removeClass('erro');
    });

    if(navigator.appName.indexOf('Internet Explorer') != -1 && document.compatMode == 'BackCompat') {

        var windowHeight = jQuery(document).height();
        jQuery("#btn_solicitar").click(function(){
            setTimeout(function(){
                jQuery("button.ui-dialog-titlebar-close").addClass('important-style');
                jQuery("button.ui-dialog-titlebar-close .ui-button-text").remove();
            },1)
        });

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
                                    width: 350px !important;\n\
                                }\n\
                                #solicitar-observacao{ width: 276px !important;} </style>');


    }

    jQuery('.chb_disponivel_estoque_todos').checarTodos('.chb_disponivel_estoque');
    jQuery('.chb_disponivel_transito_todos').checarTodos('.chb_disponivel_transito');




    jQuery( "body" ).delegate( ".somenteNumero", "blur", function() {
        if (jQuery(this).attr('readonly') !== "readonly" && (parseInt(jQuery(this).attr('value')) === 0 || jQuery.trim(jQuery(this).attr('value')) === '')) {
            jQuery(this).attr('value','1');
        }

    });

    jQuery('.produtos').change(function() {

        atualizarStatusBotoes();

        var campo = jQuery(this).attr('id');

        if (jQuery(this).is(":checked")) {

            if (!solicita_unidades_estoque) {
                jQuery('.' + campo).attr('readonly', 'readonly');
                jQuery('.' + campo).addClass('desabilitado');
            } else {
                jQuery('.' + campo).removeAttr('readonly');
                jQuery('.' + campo).removeClass('desabilitado');
            }
            if(!(parseFloat(jQuery('.' + campo).val()) > 0)){
                jQuery('.' + campo).attr('value', '1');
            }
        } else {
            jQuery('.' + campo).attr('value', '0');
            jQuery('.' + campo).attr('readonly', 'readonly');
            jQuery('.' + campo).addClass('desabilitado');
        }
    });

    //ao selecionar todos
    jQuery('.chb_disponivel_estoque_todos, .chb_disponivel_transito_todos').change(function() {
        setTimeout(function(){
            atualizarStatusBotoes()
        }, 10);
    });



    if (!solicita_unidades_estoque) {
        jQuery('.chb_disponivel_estoque_todos, .chb_disponivel_transito_todos').change(function() {
            var checkTodos = jQuery(this);
            var tipo;

            if (checkTodos.hasClass('chb_disponivel_estoque_todos')) {
                tipo = 'chb_disponivel_estoque';
            } else {
                tipo = 'chb_disponivel_transito';
            }

            jQuery('.' + tipo).each(function() {

                var campo = jQuery(this).attr('id');

                if (checkTodos.is(':checked')) {
                    jQuery('.' + campo).attr('value', '1');
                } else {
                    jQuery('.' + campo).attr('value', '0');
                }
            });
        });
    }

    jQuery("#btn_solicitar").click(function() {

        var itens = new Array();

        var i = 0;
        jQuery("#tabela_solicitacao_produto").html('');
        jQuery(".produtos").each(function() {

            if (jQuery(this).is(':checked') && jQuery(this).hasClass('somente_disponivel')) {

                var produtoNome = jQuery(this).attr('data-nome');
                var produtoId = jQuery(this).attr('data-produtoid');

                produto = new Object();
                produto.nome = produtoNome;
                produto.id = produtoId;
                itens[i] = produto;

                if (!solicita_unidades_estoque) {
                    var attr = " readonly='readonly' class='campo somenteNumero direita mini desabilitado' value='1' ";
                } else {
                    var attr = " class='campo somenteNumero direita mini' value='1' ";
                }

                jQuery("#tabela_solicitacao_produto").append("<tr class='solicita_produto' data-produto-id='" + produto.id + "' data-id='solicita_produto_" + produto.id + "' ><td>" + produto.nome + "</td><td class='centro'><input id='solicita_produto_" + produto.id + "' " + attr + " type='text'></td></tr>");
                i++;
            }

        });

        //tabela_solicitacao_produto
        jQuery("#solicitar-produtos-form").dialog({
            autoOpen: false,
            minHeight: 300 ,
            maxHeight: 500 ,
            width: 350,
            modal: true,
            buttons: {
                "Solicitar": function() {

                    data = new Object();

                    if (jQuery.trim(jQuery("#solicitar-observacao").attr('value')) == '') {
                        jQuery("#lbl_observacao, #solicitar-observacao").addClass('erro');
                        return false;
                    }

                    data.acao = 'solicitarAgendamento';
                    data.repoid = jQuery("#repoid").attr('value');
                    data.sagordoid = jQuery("#solicitar_produtos_ordoid").attr('value');
                    data.sagobservacao = jQuery("#solicitar-observacao").attr('value');
                    data.itens = new Array();
                    //pego os campos da tabela e seus valores
                    var i = 0;
                    if ( jQuery("tr.solicita_produto").length > 0){
                        jQuery("tr.solicita_produto").each(function(){
                            data.itens[i] = new Array();
                            var inputQtdId = jQuery(this).attr('data-id');
                            data.itens[i][0] = jQuery(this).attr('data-produto-id');
                            data.itens[i][1] = jQuery('#'+inputQtdId).attr('value');
                            i++;
                        });
                    }else{
                        data = new Object();
                    }

                    if (data.itens.length > 0) {
                        data = jQuery.param(data);

                        jQuery.ajax({
                            type: "POST",
                            url: 'prn_cockpit_agendamento_os.php',
                            data: data,
                            success: function(data) {

                                if (data == '1') {
                                    //Solicitação de produtos realizada com sucesso
                                    jQuery("#area_mensagens_js").html("<div class=\"mensagem sucesso\">Solicitação de produtos realizada com sucesso.</div>");
                                    jQuery("#tabela_solicitacao_produto").html('');

                                } else {                                  
                                    jQuery("#area_mensagens_js").html("<div class=\"mensagem sucesso\">Houve um erro no processamento dos dados.</div>");
                                    jQuery("#tabela_solicitacao_produto").html('');
                                }

                                window.location.hash = "#area_mensagens_js";
                                jQuery("#btn_solicitar").attr('disabled','disabled');
                                jQuery("#lbl_observacao, #solicitar-observacao").removeClass('erro');
                                jQuery("#solicitar-observacao").attr('value','');
                                jQuery("input[type='checkbox']").attr('checked',false);
                                jQuery("#solicitar-produtos-form").dialog("close");
                            }
                        });

                    }
                },
                "Cancelar": function() {
                    jQuery("#lbl_observacao, #solicitar-observacao").removeClass('erro');
                    jQuery("#solicitar-observacao").attr('value','');
                    jQuery(this).dialog("close");
                }
            },
            "Cancelar": function() {
                allFields.val("").removeClass("ui-state-error");
            }
        }).dialog('open');

    });
    
    jQuery('#btn_cancelar_reservas').click(function(){

        jQuery("#motivo_cancelamento_form").dialog({
                autoOpen: false,
                minHeight: 300 ,
                width: 450,
                resizable: false,
                modal: true,
                buttons: {
                    "Efetivar Cancelamento": function() {

                        if (jQuery.trim(jQuery("#justificativa").attr('value')) == '') {
                            jQuery("#lbl_justificativa, #justificativa").addClass('erro');
                            return false;
                        } else {
                            jQuery("#cancelamentoJustificativa").val(jQuery.trim(jQuery("#justificativa").attr('value')));
                        }

                        /*
                         * Exclui item banco de dados e sessao
                         */
                        jQuery('#acao').val('excluirReserva');
                        //jQuery('#cancelamentoIdItem').val(parseFloat(jQuery(row).attr('database-id')));
                        //jQuery('#idProdutoExcluir').val(jQuery(row).attr('id'));
                        jQuery('#form_reservado').submit();
                    },
                    "Desistir Cancelamento": function() {
                        jQuery(this).dialog("close");
                    }
                },
                beforeClose: function(){
                    jQuery("#lbl_justificativa, #justificativa").removeClass('erro');
                    jQuery("#justificativa").attr('value','');
                }
            }).dialog('open');

    });

    jQuery('#btn_reservar, #btn_salvar_reservas').click(function(){

        jQuery("#area_mensagens_js").hide();
        jQuery("#area_mensagens_js").html("");

    	/*
    	 * Valida se possui quantidade disponivel para reservar tanto de estoque disponivel quanto de estoque em transito
    	 */
    	if (this.id == 'btn_reservar') {

    		var quantidadeIdisponivel = false;
	    	jQuery.each(jQuery('.produtos'), function(i, value){

	    		if (jQuery(value).is(':checked')) {

	    			 var idProduto = jQuery(value).parent().parent().attr('id');
                     var tipoSeletor = jQuery(value).data('tipo');
                     var maxProdutos = 0;

                     if (tipoSeletor == "estoque") {
                        var maxProdutos = parseFloat(jQuery('#max_estoque_'+idProduto).text());
                     } else {
                        var maxProdutos = parseFloat(jQuery('#max_estoque_transito_'+idProduto).text());
                     }
	    			 

	    			 if (maxProdutos == 0) {

		    			 var mensagemErro = MENSAGEM_PRODUTO_INDISPONIVEL;
		                 var regex = /NOME_PRODUTO/;
		                 mensagemErro = mensagemErro.replace(regex, jQuery(value).attr('data-nome'));
		                 gravarMensagem(mensagemErro, "alerta");
		                 quantidadeIdisponivel = true;
	    			 }
	    		}
	    	})
    	}

    	if (quantidadeIdisponivel) {
    		mostrarMensagens();
    		return false;
    	}

    	var enviarFormulario = false;
        var parametros = [];

        parametros['disponivelEstoque'] = "#max_estoque_";
        parametros['disponivelTransito'] = "#max_transito_";

        if(this.id == 'btn_reservar'){
            parametros['qtdDesejadaEstoque'] = ".disponivel_estoque_";
            parametros['qtdDesejadaTransito'] = ".disponivel_transito_";

            parametros['classeElementoTd'] = '.td_disponivel';
        } else {
            parametros['qtdDesejadaEstoque'] = ".reservado_estoque_";
            parametros['qtdDesejadaTransito'] = ".reservado_transito_";

            parametros['classeElementoTd'] = '.td_reservado';
        }

        enviarFormulario = validarReservaProdutos(parametros, this.id);

        if (enviarFormulario == true) {

        	if (this.id == 'btn_salvar_reservas') {

        		jQuery('#acao').val('salvarReservas');

        		jQuery.ajax({
            		url: 'prn_cockpit_agendamento_os.php',
            		type: 'POST',
            		data: jQuery('#form_reservado').serialize(),
            		success: function (data) {

            			if (data == "OK") {
            				window.close();
            			}
                        else {
                           //Caiu aqui pq encontrou saldo insuficiente

                           var dados = jQuery.parseJSON(data);

                           jQuery.each(dados, function(idx, valor){
                               //Adiciona classe de erro no campo e quantidade reservada
                                jQuery("#reserva_disponivel_" + idx).addClass('erro');

                                //Seta a nova quantidade disponivel
                                jQuery("#max_estoque_" + idx).html(valor.toString());
                            });

                           gravarMensagem("Quantidade de produtos disponíveis insuficiente!", "alerta");
                           mostrarMensagens();
                        }
            		}
            	})

        	} else {

        		jQuery('#acao').val('reservarProdutos');

        		jQuery('#form_disponivel').submit();
        	}
        } else {
            mostrarMensagens();
        }
    });

    jQuery(".btnExcluirItem").click(function() {

        var row = "#"+jQuery(this).parent().parent().attr('id');
        var produtoBanco = (parseFloat(jQuery(row).attr('database-id')) > 0) ? true : false;


        if(produtoBanco){
            jQuery('#cancelamento_nome_produto > p:first').html("Produto: <strong>"+jQuery.trim(jQuery(row+" > td:first").text())+"</strong>");

            //tabela_solicitacao_produto
            jQuery("#motivo_cancelamento_form").dialog({
                autoOpen: false,
                minHeight: 300 ,
                width: 450,
                resizable: false,
                modal: true,
                buttons: {
                    "Efetivar Cancelamento": function() {

                        if (jQuery.trim(jQuery("#justificativa").attr('value')) == '') {
                            jQuery("#lbl_justificativa, #justificativa").addClass('erro');
                            return false;
                        } else {
                            jQuery("#cancelamentoJustificativa").val(jQuery.trim(jQuery("#justificativa").attr('value')));
                        }

                        /*
                         * Exclui item banco de dados e sessao
                         */
                        jQuery('#acao').val('excluirItemReserva');
                        jQuery('#cancelamentoIdItem').val(parseFloat(jQuery(row).attr('database-id')));
                        jQuery('#idProdutoExcluir').val(jQuery(row).attr('id'));
                        jQuery('#form_reservado').submit();
                    },
                    "Desistir Cancelamento": function() {
                        jQuery(this).dialog("close");
                    }
                },
                beforeClose: function(){
                    jQuery("#lbl_justificativa, #justificativa").removeClass('erro');
                    jQuery("#justificativa").attr('value','');
                }
            }).dialog('open');
        } else {
        	/*
        	 * Exclui item sessao
        	 */
            jQuery('#acao').val('excluirItemReserva');
            jQuery('#idProdutoExcluir').val(jQuery(row).attr('id'));
            jQuery('#form_reservado').submit();
        }
    });


    jQuery('#btn_fechar_janela').click(function(){

    	jQuery.ajax({
    		url: 'prn_cockpit_agendamento_os.php',
    		type: 'POST',
    		data: {'acao': 'fecharJanela', 'ordoid': jQuery('#ordoid').val()}
    	})

    	window.close();

	});

    var mostrar_mensagem = false;
    var produtos_reservar = [];
    jQuery(".bt_reservar_produtos").click(function(){

        jQuery.each(jQuery('.produtos'), function(i, value){

            if (jQuery(value).is(':checked')) {

                 var idProduto = jQuery(value).parent().parent().attr('id');
                 var maxProdutos = 0;
                 var nomeProduto = jQuery(value).data('nome');

                 var produto = {
                    "id":           idProduto,
                    "nome":         nomeProduto,
                    "qtde_estoque": parseFloat(jQuery('#max_estoque_'+idProduto).text()),
                    "qtde_transito":parseFloat(jQuery('#max_estoque_transito_'+idProduto).text())
                 };

                 if (produto.qtde_estoque <= 0 && produto.qtde_transito <= 0) {
                    gravarMensagem("O produto "+nomeProduto+" não possui quantidade suficiente para ser reservado.", "alerta");
                    mostrar_mensagem = true;
                    jQuery(".mensagem").remove();
                 } else {

                    if ((jQuery('tr[id*="reservar_'+produto.id+'"]')).length == 0) {

                        var desabilitaEstoque = '';
                        var desabilitaTransito = '';
                        var qtdeEstoque = 0;
                        var qtdeTransito = 0;
                        var linha = jQuery('<tr id="reservar_'+produto.id+'">');
                    
                        jQuery(linha).append(jQuery('<td id="nome_produto_'+produto.id+'" class="esquerda">').html(nomeProduto));
                       
                        if (produto.qtde_estoque <= 0) {
                            desabilitaEstoque = 'disabled="true"';
                        } 

                        if (produto.qtde_transito <= 0) {
                            desabilitaTransito = 'disabled="true"';
                        } 

                        linha.append(jQuery('<td class="centro">').html('<input '+desabilitaEstoque+' class="campo  direita mini produto_reservar" name="produto_reservar_estoque['+idProduto+']" value="'+qtdeEstoque+'" data-produtoid="'+idProduto+'" data-tipo="estoque">'));
                        linha.append(jQuery('<td class="centro">').html('<input '+desabilitaTransito+' class="campo direita mini produto_reservar" name="produto_reservar_transito['+idProduto+']" value="'+qtdeTransito+'" data-produtoid="'+idProduto+'" data-tipo="transito">'));    

                        jQuery(linha).append(jQuery('<td class="centro">').html('<a href="#" class="btn_ExcluirItem"><img src="images/icon_error.png" title="Cancelar Reserva" class="icone" /></a>'));

                        jQuery("#lista_reservar tbody").append(linha);
                        habilitaSalvarReserva();
                    }
                 }
            }
        });

        if (mostrar_mensagem) {
            mostrarMensagens();
        }  

        jQuery('html, body').animate({ scrollTop: 0}, 500);
        habilitaSalvarReserva();
        zebrarListaReservar();
      
    });

    jQuery(".btn_ExcluirItem").live("click",function () {
        jQuery(this).parent().parent().remove();
        zebrarListaReservar();
        habilitaSalvarReserva();
    });

    jQuery(".produto_reservar").live("keyup", function() {
        habilitaSalvarReserva();

    });

    jQuery("#btn_salvar_reserva").click(function(){
        salvarReserva();
    });
});

function habilitaSalvarReserva() {

    var qtdeTotal = 0;

    jQuery('input[name*="produto_reservar"]').each(function(o,i){
        var valor = jQuery(i).val();
        if (!isNaN(valor) && valor != "") {
            qtdeTotal += parseInt(valor);    
        }
    });
    
    if (qtdeTotal == 0) {
        jQuery("#btn_salvar_reserva").attr('disabled','disabled');
        jQuery("#btn_salvar_reserva").addClass("desabilitado");
    } else {
        jQuery("#btn_salvar_reserva").removeAttr('disabled');
        jQuery("#btn_salvar_reserva").removeClass("desabilitado");
    }
}

function salvarReserva() {

    var mostrar_mensagem = false;
    jQuery(".produto_reservar").each(function(o,i){

        var idProduto = jQuery(i).data('produtoid');
        var tipo = jQuery(i).data('tipo');
        var quantidade = 0;
        var quantidadeReserva = parseInt(jQuery(i).val());
        var nomeProduto = jQuery('#nome_produto_'+idProduto).text();

        if (tipo == "estoque") {
            quantidade = parseInt(jQuery("#qtde_"+idProduto).text());
        } else {
            quantidade = parseInt(jQuery("#qtde_transito_"+idProduto).text());
        }

        if (quantidadeReserva == 0) { 
            return;
        }

        if ( quantidadeReserva > quantidade) {
            gravarMensagem("O produto "+nomeProduto+" não possui quantidade suficiente para ser reservado.", "alerta");
            mostrar_mensagem = true;
        } 
     });

    if (mostrar_mensagem) {
        jQuery(".mensagem").remove();
        mostrarMensagens();
        jQuery('html, body').animate({ scrollTop: 0}, 500);
    } else {
       jQuery("#form_disponivel").find("#acao").val('efetuarReserva');
        jQuery('#form_disponivel').submit();
    }
}

function zebrarListaReservar() {
    jQuery('#lista_reservar tbody tr:odd').addClass('par');
    jQuery('#lista_reservar tbody tr:even').addClass('impar');
}


function validarReservaProdutos(parametros, botaoId){
    var enviarFormulario = true;

    jQuery(parametros['classeElementoTd']).parent().each(function(i, value){


        var disponivelEstoque   = parseFloat(jQuery('.qtdeInicial_'+jQuery(value).attr('id')).attr('value'));
        var disponivelTransito  = parseFloat(jQuery(parametros['disponivelTransito']+jQuery(value).attr('id')).text());

        var qtdDesejadaEstoque  = parseFloat(jQuery(parametros['qtdDesejadaEstoque']+jQuery(value).attr('id')).val());
        var qtdDesejadaTransito = parseFloat(jQuery(parametros['qtdDesejadaTransito']+jQuery(value).attr('id')).val());

        var checkBoxEstoqueMarcado, checkBoxTransitoMarcado;
        var reservado;

        if(botaoId == 'btn_reservar'){
            checkBoxEstoqueMarcado  = jQuery(document.getElementsByName('checkbox['+jQuery(value).attr('id')+'][checkedDisponivel]')).is(':checked');
            checkBoxTransitoMarcado = jQuery(document.getElementsByName('checkbox['+jQuery(value).attr('id')+'][checkedTransito]')).is(':checked');
            reservado = false;
        } else {
            checkBoxEstoqueMarcado  = true;
            checkBoxTransitoMarcado = true;
            reservado = true;
        }

        // Verifica se algum checkbox da linha esta selecionado
        if(checkBoxEstoqueMarcado || checkBoxTransitoMarcado){
            if (checkBoxEstoqueMarcado && qtdDesejadaEstoque === 0 && !reservado){
                jQuery(parametros['qtdDesejadaEstoque']+jQuery(value).attr('id')).addClass('erro');
                enviarFormulario = false;
                gravarMensagem(MENSAGEM_INPUT_ZERADO, "alerta");
            } else if(checkBoxEstoqueMarcado && (qtdDesejadaEstoque > disponivelEstoque+1)) {       
                jQuery(parametros['qtdDesejadaEstoque']+jQuery(value).attr('id')).addClass('erro');
                enviarFormulario = false;
                gravarMensagem(MENSAGEM_PRODUTOS_INSUFICIENTES, "alerta");
            }

            if (checkBoxTransitoMarcado && qtdDesejadaTransito == 0 && !reservado) {
                jQuery(parametros['qtdDesejadaTransito']+jQuery(value).attr('id')).addClass('erro');
                enviarFormulario = false;
                gravarMensagem(MENSAGEM_INPUT_ZERADO, "alerta");
            }else if(checkBoxTransitoMarcado && (qtdDesejadaTransito > disponivelTransito)) {
                jQuery(parametros['qtdDesejadaTransito']+jQuery(value).attr('id')).addClass('erro');
                enviarFormulario = false;
                gravarMensagem(MENSAGEM_PRODUTOS_INSUFICIENTES, "alerta");
            }
        }
    });

    return enviarFormulario;
};

function gravarMensagem(mensagem, tipoMensagem){
    var gravar = false;

    if(jQuery.inArray(mensagem, mensagens['texto']) == -1){
        gravar = true;
    }

    if(gravar){
        mensagens['texto'].push(mensagem);
        mensagens['tipo'].push(tipoMensagem);
    }
}

function mostrarMensagens(){

	jQuery("#area_mensagens_js").hide();

    jQuery("#area_mensagens_js").html("");

    if(mensagens['texto'].length > 0){
        while(mensagens['texto'].length){
            jQuery("#area_mensagens_js").append("<div class=\"mensagem "+mensagens['tipo'].pop()+"\">"+mensagens['texto'].pop()+"</div>");
        }
    }

    window.location.href = "#";
    mensagens['texto'].slice(mensagens['texto'].length);
    mensagens['tipo'].slice(mensagens['tipo'].length);

    jQuery("#area_mensagens_js").fadeIn();
}

var MENSAGEM_PRODUTO_INDISPONIVEL = "\
O produto 'NOME_PRODUTO' não está disponível em estoque. Por \
gentileza, verifique se o mesmo está disponível em remessas \
ou solicite ao departamento de Distribuição SASCAR.";

var MENSAGEM_PRODUTOS_INSUFICIENTES = "Não há produtos suficientes para essa requisição.";

var MENSAGEM_INPUT_ZERADO = "Você deve reservar ao menos um produto quando selecionar a checkbox.";

var mensagens = [];
mensagens['texto'] = [];
mensagens['tipo'] = [];

function toObject(arr) {
    var rv = {};
    for (var i = 0; i < arr.length; ++i)
        rv[i] = arr[i];
    return rv;
}

function atualizarStatusBotoes(){
    var botoesId = [];
    botoesId.push("#btn_solicitar");
    botoesId.push("#btn_reservar");

    if(algumaCheckBoxSelecionada()){
        habilitarBotoes(botoesId);
    } else {
        desabilitarBotoes(botoesId);
    }
}

function algumaCheckBoxSelecionada(){
    if(jQuery(".produtos:checked").length == 0){
        return false;
    }
    return true;
}

function habilitarBotoes(botoesId) {
    for(var i=0; i < botoesId.length; i++){
        jQuery(botoesId[i]).removeAttr('disabled');
    }
}

function desabilitarBotoes(botoesId){
    for(var i=0; i < botoesId.length; i++){
        jQuery(botoesId[i]).attr('disabled','disabled');
    }
}