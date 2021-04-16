jQuery(document).ready(function() {


    //Cria o help inicial da tela
    adicionaHelp();

    //Carrega o periodo
    jQuery("#data_inicial").periodo('#data_final');


    //Carrega os motivos conforme a ação    
    jQuery("#aoamoid_acao").change(function() {

        var aoampai = (jQuery.trim(jQuery(this).val()) != '') ? jQuery.trim(jQuery(this).val()) : '';

        jQuery("#aoamoid_motivo").html("<option value=''>Escolha</option>");

        jQuery.ajax({
            type: "POST",
            url: 'rel_analise_tratamento_os.php',
            data: {
                acao: 'carregarMotivos',
                aoampai: aoampai
            },
            beforeSend: function() {
                jQuery(".carregando").css('display', '');
            },
            complete: function() {
                jQuery(".carregando").css('display', 'none');
            },
            success: function(data) {

                if (typeof JSON != 'undefined') {
                    data = JSON.parse(data);
                } else {
                    data = eval('(' + data + ')');
                }

                var retorno = "<option value=''>Escolha</option>";

                if (data.length > 0) {
                    jQuery.each(data, function(i, val) {
                        retorno += "<option value='" + val.id + "'>" + val.label + "</option>";
                    });
                }

                jQuery("#aoamoid_motivo").html(retorno);
            }
        });
    });

    //Botão Pesquisar
    jQuery('#bt_pesquisar').click(function() {
        jQuery('#acao').val('pesquisar');
        jQuery('#form').submit();
    });

    //Botão Gerar CSV
    jQuery('#bt_gerar_csv').click(function() {
        jQuery('#acao').val('gerarCSV');
        jQuery('#form').submit();
    });

    //Link de ação e motivo
    jQuery('a.acao_motivo').click(function() {
        jQuery('#mensagem_sucesso').esconderMensagem();
        var ordoid = jQuery(this).attr('rel');
        var projeto = jQuery(this).attr('projeto');
        var posicao = jQuery(this).attr('posicao');

        window.open('./rel_analise_tratamento_os.php?acao=cadastrar&ordoid=' + ordoid + '&eproid=' + projeto + '&veipdata=' + posicao, '', 'width=600,height=350');
    });

    //Link de ação e motivo - cadastrar
    jQuery('#bt_incluir').click(function() {

        var aotordoid = jQuery('#aotordoid').val();

        jQuery('#mensagem_alerta').esconderMensagem();
        if (jQuery("#aoamoid_motivo").val() == "" || jQuery("#aoamoid_acao").val() == "") {
            jQuery('#mensagem_alerta').html('Existem campos obrigatórios não preenchidos.').mostrarMensagem();
        } else {


            jQuery.ajax({
                url: 'rel_analise_tratamento_os.php',
                type: 'post',
                data: jQuery('#form_cadastrar').serialize(),
                beforeSend: function() {
                    jQuery('#bt_incluir').attr('disabled', true);
                },
                success: function(data) {
                    var resultado = jQuery.parseJSON(data);

                    if (resultado.status) {
                        //Atualiza a ação e motivo na janela pai e exibe a mensagem               	
                        jQuery('#coluna_acao_' + aotordoid, window.opener.document).atualizarAcaoMotivo(aotordoid, self);

                    }

                }
            });

        }
    });

    //Link de ação e motivo - cancelar
    jQuery('#bt_cancelar').click(function() {
        self.close();
    });

    //Exibir Combos pesquisa Filtro Analitico
    jQuery('#tipo').change(function() {

        var valor_combo = jQuery(this).val();

        if (valor_combo === '2') {

            jQuery('.analitico').show();
        } else {

            jQuery('.analitico').hide();

        }
        adicionaHelp();
    });

    jQuery('#tipo').change();


});


jQuery.fn.atualizarAcaoMotivo = function(ordoid, janela) {
    var colunaAtual = jQuery(this);
    jQuery.ajax({
        url: 'rel_analise_tratamento_os.php',
        type: 'post',
        data: {
            acao: 'buscarAcaoMotivo',
            ordoid: ordoid
        },
        beforeSend: function() {

            colunaAtual.html('<img class="centro" src="modulos/web/images/ajax-loader-circle.gif" />');
        },
        success: function(data) {

            var resultado = jQuery.parseJSON(data);

            //Remove Colspan
            colunaAtual.attr('colspan', '1');

            //Pega a tr (linha)
            var linha = colunaAtual.parents('tr');

            //Adiciona o conteudo a coluna atual
            colunaAtual.html(resultado.acaoDescricao);

            //Adiciona uma nova coluna a linha
            linha.append('<td>' + resultado.motivoDescricao + '</td>');

            //Exibe a mensagem
            jQuery('#mensagem_sucesso', window.opener.document).html('Registro incluído com sucesso.').slideUp().slideToggle(300, function() {
                janela.close();
            });

        }

    });
}


function adicionaHelp(){
    var valor_combo = jQuery("#tipo").val();

    var title = "";

    if (valor_combo === '2') {
        title = "Data Atendimento";
    } else {
        title = "Data Posição Atual";
    }   

    jQuery('#help-data-analise')
        .attr("title", title)
        .tooltip({
            position: {
                my: 'left+5 center', 
                at: 'right center'
            }
        });
}