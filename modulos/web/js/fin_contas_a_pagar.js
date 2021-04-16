jQuery(document).ready(function(){


    //Ajusta abas
    var aba = $(location).attr('search');
    aba = aba.split('=');
    if(aba[0] == "?aba"){
        jQuery("#"+aba[1]).addClass('ativo');
    }else{
        jQuery("#gerar_arquivo").addClass('ativo');
    }
    
    /**
     * Acoes do icone excluir
     */
    jQuery("table").on('click','#enviar-remessa',function(event) {
        event.preventDefault();

        var linhasSelecionadas = new Array();
        var naoAutorizados = new Array();

        esconderMensagens();

        //busca registros selecionados
        jQuery(".selecao:checked").each(function(){

            //adiciona linha selecionada
            linhasSelecionadas.push($(this).data('linha'));

            if($(this).data('autorizado') == "f"){
                naoAutorizados.push($(this).data('linha'));
            }
        });

        if(linhasSelecionadas.length == 0){
            mostrarMensagemAlerta("Selecionar ao menos um título.");
            return;
        }

        if(naoAutorizados.length > 0){
            mostrarMensagemAlerta("Selecionar somente títulos autorizados.");
            return;
        }

        elemento = this;
        jQuery("#mensagem_alerta_arquivo").dialog({
            title: "Enviar remessa",
            resizable: false,
            modal: true,
            width: 500,
            buttons: {
                "Sim": function() {
                    jQuery('#acao').val('enviarRemessa');
                    jQuery('#frm_pesquisar').submit();
                },
                "Não": function() {
                    jQuery( this ).dialog( "close" );
                }
            }
        });
    });

    jQuery("table").on('click','#autorizar',function(event) {
        event.preventDefault();

        var linhasSelecionadas = new Array();
        var autorizados = new Array();

        esconderMensagens();

        //busca registros selecionados
        jQuery(".selecao:checked").each(function(){

            //adiciona linha selecionada
            linhasSelecionadas.push($(this).data('linha'));

            if($(this).data('autorizado') == "t"){
                autorizados.push($(this).data('linha'));
            }
        });

        if(linhasSelecionadas.length == 0){
            mostrarMensagemAlerta("Selecionar ao menos um título.");
            return;
        }

        if(autorizados.length > 0){
            mostrarMensagemAlerta("Selecionar somente títulos não autorizados.");
            return;
        }

        elemento = this;
        jQuery("#mensagem_alerta_autorizacao").dialog({
            title: "Autorizar títulos",
            resizable: false,
            modal: true,
            width: 500,
            buttons: {
                "Sim": function() {
                    jQuery('#acao').val('autorizarTitulos');
                    jQuery('#frm_pesquisar').submit();
                },
                "Não": function() {
                    jQuery( this ).dialog( "close" );
                }
            }
        });

    });
    
    jQuery("table").on('click','#imprimir',function(event) {
        event.preventDefault();

        var linhasSelecionadas = new Array();
        var arrayIds = new Array();
        var ids = "";

        esconderMensagens();

        //busca registros selecionados
        jQuery(".selecao:checked").each(function(){

            //adiciona linha selecionada
            linhasSelecionadas.push($(this).data('linha'));

            arrayIds.push($(this).val());
        });

        if(linhasSelecionadas.length == 0){
            mostrarMensagemAlerta("Selecionar ao menos um título.");
            return;
        }

        ids = arrayIds.join(",");

        window.open("imp_arq_apagar.php?ids="+ ids +"&consultar_busca="+ jQuery("#consultar_busca").val(),"_blank");

    });
    
    jQuery("table").on('click','#liberar_reenvio',function(event) {
        event.preventDefault();

        var linhasSelecionadas = new Array();
        var arrayIds = new Array();
        var ids = "";

        esconderMensagens();

        //busca registros selecionados
        jQuery(".selecao:checked").each(function(){

            //adiciona linha selecionada
            linhasSelecionadas.push($(this).data('linha'));

            arrayIds.push($(this).val());
        });

        if(linhasSelecionadas.length == 0){
            mostrarMensagemAlerta("Selecionar ao menos um título.");
            return;
        }

        elemento = this;
        jQuery("#mensagem_alerta_libera_reenvio").dialog({
            title: "Liberar títulos para reenvio",
            resizable: false,
            modal: true,
            width: 500,
            buttons: {
                "Sim": function() {
                    jQuery('#acao').val('liberarReenvio');
                    jQuery('#frm_pesquisar').submit();
                },
                "Não": function() {
                    jQuery( this ).dialog( "close" );
                }
            }
        });

    });

    jQuery("table").on('click','#atualizar_status',function(event) {
        jQuery('#acao').val('chamarCron');
        jQuery('#frm_pesquisar').submit();
    });

    //Gerar CSV
    jQuery('#gerar_csv').click(function() {
        jQuery('#acao').val('gerarCSV');
        jQuery('#frm_pesquisar').submit();
    });

    //Limpar campos pesquisa envio arquivos 
    jQuery('#bt_limpar_envio_arquivos').click(function() {
    	jQuery("#envio_arquivos").addClass('ativo');
        jQuery('#acao').val('');
        jQuery('#tecoid').val('');
        jQuery('#limpaTecoid').val('true');
        jQuery('#periodo_inicial_busca').val(dataFormatada());
        jQuery('#periodo_final_busca').val(dataFormatada());
        jQuery('#num_remessa').val('');
        jQuery('#banco').val('');
        jQuery('#status').val('');
        jQuery('#frm_pesquisar').submit();
    });
    
    //Limpar campos pesquisa titulos processados
    jQuery('#bt_limpar_titulos_processados').click(function() {
    	jQuery("#titulos_processados").addClass('ativo');
        jQuery('#acao').val('');
        jQuery('#tecoid').val('');
        jQuery('#limpaTecoid').val('true');
        jQuery('#periodo_inicial_busca').val(dataFormatada());
        jQuery('#periodo_final_busca').val(dataFormatada());
        jQuery('#consultar_busca').val('');
        jQuery('#banco').val('');
        jQuery('#status').val('');
        jQuery('#num_remessa').val('');
        jQuery('#cmp_fornecedor_autocomplete').val('');
        jQuery('#frm_pesquisar').submit();
    });


    //Autocomplete Fornecedor
    jQuery('#cmp_fornecedor_autocomplete').autocomplete({
        source: 'fin_arq_apagar.php?acao=buscarFornecedor',
        minLength: 3,
        response: function(event, ui) {
            if (!ui.content.length) {
                mostrarMensagemAlerta('Nenhum fornecedor encontrado com o termo: ' + $(this).val());
            } else {
                esconderMensagemAlerta();
            }
        },
        select: function(event, ui ) {
            $('#cmp_fornecedor').val(ui.item.id);
        }
    });

    /**
     * Tratamento somente numeros inteiros
     */
    jQuery('body').on('keyup blur', '#num_remessa', function() {
        jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
    });

    // Checkbox marcar todos
    jQuery('#selecao_todos').checarTodos('input.selecao'); 

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
     * Mensagens
     */
    function mostrarMensagemAlerta(mensagem) {
        $('#mensagem_alerta')
            .text(mensagem)
            .removeClass('invisivel').fadeIn();

        $('html, body').animate({ scrollTop: $('.modulo_titulo').offset().top }, 'fast');

    };
    function esconderMensagemAlerta() {
        $('#mensagem_alerta').fadeOut();
    };

    function mostrarMensagemSucesso(mensagem) {
        $('#mensagem_sucesso')
            .text(mensagem)
            .removeClass('invisivel').fadeIn();

        $('html, body').animate({ scrollTop: $('.modulo_titulo').offset().top }, 'fast');
    };
    function esconderMensagemSucesso() {
        $('#mensagem_sucesso').fadeOut();
    };

    function mostrarMensagemErro(mensagem) {
        $('#mensagem_erro')
            .text(mensagem)
            .removeClass('invisivel').fadeIn();

        $('html, body').animate({ scrollTop: $('.modulo_titulo').offset().top }, 'fast');
    };
    function esconderMensagemErro() {
        $('#mensagem_erro').fadeOut();
    }; 
    
    function dataFormatada() {
        var data = new Date();
        
        var dataFormatada = ("0" + data.getDate()).substr(-2) + "/" + ("0" + (data.getMonth() + 1)).substr(-2) + "/" + data.getFullYear();
        
        return dataFormatada;
    }
   
});