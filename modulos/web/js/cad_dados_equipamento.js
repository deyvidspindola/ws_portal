jQuery(document).ready(function(){

    if(localStorage.getItem('tabela-lmu-column') != null) {
        var colunas = localStorage.getItem('tabela-lmu-column').split(',')
        jQuery('#tabela-lmu .coluna-tabela').hide();
        jQuery("input:checkbox[name='tabela-lmu-column']").removeAttr('checked');
        colunas.forEach(function(keyColumn, i){
            jQuery('#tabela-lmu .' + keyColumn).show();
        });
    }

    if(localStorage.getItem('tabela-mtc-column') != null) {
        var colunas = localStorage.getItem('tabela-mtc-column').split(',')
        jQuery('#tabela-mtc .coluna-tabela').hide();
        jQuery("input:checkbox[name='tabela-mtc-column']").removeAttr('checked');
        colunas.forEach(function(keyColumn, i){
            jQuery('#tabela-mtc .' + keyColumn).show();
        });
    }

    var equipamentos = {};

    var totalEquipamentos = jQuery('.tabela-equipamentos table tbody tr').length;
    var carregado = 0;

    if (totalEquipamentos == 0) {
        jQuery('#bt_pesquisar').removeClass('desabilitado');
        jQuery('#bt_pesquisar').removeAttr('disabled');
        jQuery('#bt_pesquisar').removeAttr('title');
    }

    var loadDadosEquipamento = function() {
        jQuery('.tabela-equipamentos table tbody tr').each(function(i, v) {

            jQuery.ajax({
                // url: 'cad_tipos_veiculo.php',
                type: 'GET',
                dataType: 'json',
                // dataType: 'xml',
                data: {
                    acao: 'getDadosEquipamento',
                    esn: jQuery(this).data('equesn'),
                    projeto: jQuery(this).find('.eprnome').text()
                },
                success: function(data) {
                    if (data.sucesso == true) {
                        if (data.equipamentoConfig == 'COMMAND_MTC') {
                            
                            jQuery(v).find('.versao_firmware').text(data['cabecalho']['versao_firmware']['@attributes']['val']);
                            jQuery(v).find('.firmware_date').text(data['cabecalho']['firmware_date']['@attributes']['val']);
                            jQuery(v).find('.lua_versao_script').text(data['cabecalho']['lua_versao_script']['@attributes']['val']);
                            jQuery(v).find('.data_script_lua').text(data['cabecalho']['data_script_lua']['@attributes']['val']);
                            jQuery(v).find('.data_chegada').text(data['cabecalho']['data_chegada']['@attributes']['val']);

                            equipamentos[jQuery(v).data('equesn')] = {};
                            equipamentos[jQuery(v).data('equesn')].versao_firmware = data['cabecalho']['versao_firmware']['@attributes']['val'];
                            equipamentos[jQuery(v).data('equesn')].firmware_date = data['cabecalho']['firmware_date']['@attributes']['val'];
                            equipamentos[jQuery(v).data('equesn')].lua_versao_script = data['cabecalho']['lua_versao_script']['@attributes']['val'];
                            equipamentos[jQuery(v).data('equesn')].data_script_lua = data['cabecalho']['data_script_lua']['@attributes']['val'];
                            equipamentos[jQuery(v).data('equesn')].data_chegada = data['cabecalho']['data_chegada']['@attributes']['val'];

                        } else if (data.equipamentoConfig == 'COMMAND_LMU') {
                            jQuery(v).find('.firmware').text(data['cabecalho']['sw_version']['@attributes']['val']);
                            jQuery(v).find('.versao_perfil').text(data['cabecalho']['versao_perfil']['@attributes']['val']);
                            jQuery(v).find('.versao_teclado').text(data['cabecalho']['versao_teclado']['@attributes']['val']);

                            jQuery(v).find('.hosted_app').text(data['advanced_setup']['others']['hosted_app']['@attributes']['val']);
                            // jQuery(v).find('.telemetria_segundo_a_segundo').text(data['telemetria_cabecalho']['telemetria_segundo_a_segundo']['@attributes']['val']);
                            jQuery(v).find('.peg_enables').text(data['advanced_setup']['others']['peg_enables']['@attributes']['val']);
                            jQuery(v).find('.logoff_30seg').text(data['peg_enables_normalizado']['logoff_30seg']);
                            jQuery(v).find('.app_tempo_direcao').text(data['peg_enables_normalizado']['app_tempo_direcao']);
                            jQuery(v).find('.bloq_auto_ignicao').text(data['peg_enables_normalizado']['bloq_auto_ignicao']);

                            jQuery(v).find('.inbound_url00').text(data['advanced_setup']['server_settings']['inbound_url00']['@attributes']['val']);
                            jQuery(v).find('.inbound_port00').text(data['advanced_setup']['server_settings']['inbound_port00']['@attributes']['val']);
                            jQuery(v).find('.inbound_url01').text(data['advanced_setup']['server_settings']['inbound_url01']['@attributes']['val']);
                            jQuery(v).find('.inbound_port01').text(data['advanced_setup']['server_settings']['inbound_port01']['@attributes']['val']);

                            jQuery(v).find('.data_chegada').text(data['cabecalho']['data_chegada']['@attributes']['val']);
                            jQuery(v).find('.modelo_isv').text(data['advanced_setup']['ad_telemetria']['isv_fw_model']['@attributes']['val']);

                            jQuery(v).find('.ini_speed_deaccel').text(data['telemetria_cabecalho']['ini_speed_deaccel']['@attributes']['val']);
                            jQuery(v).find('.breack_deaccel').text(data['telemetria_cabecalho']['breack_deaccel']['@attributes']['val']);

                            equipamentos[jQuery(v).data('equesn')] = {};
                            equipamentos[jQuery(v).data('equesn')].firmware = data['cabecalho']['sw_version']['@attributes']['val'];
                            equipamentos[jQuery(v).data('equesn')].versao_perfil = data['cabecalho']['versao_perfil']['@attributes']['val'];
                            equipamentos[jQuery(v).data('equesn')].versao_teclado = data['cabecalho']['versao_teclado']['@attributes']['val'];
                            equipamentos[jQuery(v).data('equesn')].hosted_app = data['advanced_setup']['others']['hosted_app']['@attributes']['val'];
                            // equipamentos[jQuery(v).data('equesn')].telemetria_segundo_a_segundo = data['telemetria_cabecalho']['telemetria_segundo_a_segundo']['@attributes']['val'];
                            equipamentos[jQuery(v).data('equesn')].peg_enables = data['advanced_setup']['others']['peg_enables']['@attributes']['val'];
                            equipamentos[jQuery(v).data('equesn')].logoff_30seg = data['peg_enables_normalizado']['logoff_30seg'];
                            equipamentos[jQuery(v).data('equesn')].app_tempo_direcao = data['peg_enables_normalizado']['app_tempo_direcao'];
                            equipamentos[jQuery(v).data('equesn')].bloq_auto_ignicao = data['peg_enables_normalizado']['bloq_auto_ignicao'];

                            equipamentos[jQuery(v).data('equesn')].inbound_url00 = data['advanced_setup']['server_settings']['inbound_url00']['@attributes']['val'];
                            equipamentos[jQuery(v).data('equesn')].inbound_port00 = data['advanced_setup']['server_settings']['inbound_port00']['@attributes']['val'];
                            equipamentos[jQuery(v).data('equesn')].inbound_url01 = data['advanced_setup']['server_settings']['inbound_url01']['@attributes']['val'];
                            equipamentos[jQuery(v).data('equesn')].inbound_port01 = data['advanced_setup']['server_settings']['inbound_port01']['@attributes']['val'];
                            equipamentos[jQuery(v).data('equesn')].data_chegada = data['cabecalho']['data_chegada']['@attributes']['val'];
                            equipamentos[jQuery(v).data('equesn')].modelo_isv = data['advanced_setup']['ad_telemetria']['isv_fw_model']['@attributes']['val'];

                            equipamentos[jQuery(v).data('equesn')].ini_speed_deaccel = data['telemetria_cabecalho']['ini_speed_deaccel']['@attributes']['val'];
                            equipamentos[jQuery(v).data('equesn')].breack_deaccel = data['telemetria_cabecalho']['breack_deaccel']['@attributes']['val'];
                        }
                        jQuery(v).find('.erro-consulta').hide();
                        jQuery(v).find('.download').show();
                        jQuery(v).find('.detalhes').show();
                    } else {
                        jQuery(v).find('.dados_equipamento').text('FALHA');
                        if (typeof data.valor != 'undefined') {
                            jQuery(v).find('.erro-consulta').attr('title', data.valor);
                            jQuery(v).find('.erro-consulta').show();
                            jQuery(v).find('.download').hide();
                            jQuery(v).find('.detalhes').hide();
                        }
                    }

                    carregado++;

                    if (carregado == totalEquipamentos) {
                        jQuery('.desabilitado').removeClass('desabilitado').removeAttr('disabled').removeAttr('title');
                        jQuery('#bt_exportar').show();
                    }
                }, 
                error: function() {
                    carregado++;
                    if (carregado == totalEquipamentos) {
                        jQuery('.desabilitado').removeClass('desabilitado').removeAttr('disabled').removeAttr('title');
                        jQuery('#bt_exportar').show();
                    }

                    // ERRO NO RETORNO DOS DADOS
                    jQuery(v).find('.dados_equipamento').text('FALHA');
                    jQuery(v).find('.erro-consulta').attr('title', 'Problema ao retornar dados do equipamento.');
                    jQuery(v).find('.erro-consulta').show();
                    jQuery(v).find('.download').hide();
                    jQuery(v).find('.detalhes').hide();
                }
            });
        });
    }

    if (totalEquipamentos > 50) {
        if (confirm('Muitos equipamentos para realizar buscar de detalhes.\n' + 
            'Essa consulta irá demorar!')) {
            loadDadosEquipamento();
        } else {
            jQuery('#bt_pesquisar').removeClass('desabilitado');
            jQuery('#bt_pesquisar').removeAttr('disabled');
            jQuery('#bt_pesquisar').removeAttr('title');
        }
    } else {
        loadDadosEquipamento();
    }


    jQuery('#tabela-lmu-column-button').on('click', function() {
        
        if(localStorage.getItem('tabela-lmu-column') != null) {
            var colunas = localStorage.getItem('tabela-lmu-column').split(',')
            jQuery("input:checkbox[name='tabela-lmu-column']").removeAttr('checked');
            colunas.forEach(function(keyColumn, i){
                jQuery("input:checkbox[name='tabela-lmu-column'][value='"+keyColumn+"']").attr('checked', 'checked');
            });
        }

        jQuery("#tabela-lmu-dialog").dialog({
            autoOpen: false,
            minHeight: 150,
            width: 370,
            modal: false,
            buttons: {
                "Aplicar": function() {
                    var selected = new Array();
                    jQuery('#tabela-lmu .coluna-tabela').hide();
                    jQuery("input:checkbox[name='tabela-lmu-column']:checked").each(function() {
                        var keyColumn = jQuery(this).val();
                        jQuery('#tabela-lmu .' + keyColumn).show();
                        selected.push(keyColumn);
                    });

                    localStorage.setItem('tabela-lmu-column', selected);
                    jQuery(this).dialog("close");
                },
                "Cancelar": function() {
                    jQuery(this).dialog("close");
                }
            },
        }).dialog('open');
    });

    jQuery('#tabela-mtc-column-button').on('click', function() {
        
        if(localStorage.getItem('tabela-mtc-column') != null) {
            var colunas = localStorage.getItem('tabela-mtc-column').split(',')
            jQuery("input:checkbox[name='tabela-mtc-column']").removeAttr('checked');
            colunas.forEach(function(keyColumn, i){
                jQuery("input:checkbox[name='tabela-mtc-column'][value='"+keyColumn+"']").attr('checked', 'checked');
            });
        }

        jQuery("#tabela-mtc-dialog").dialog({
            autoOpen: false,
            minHeight: 150,
            width: 370,
            modal: false,
            buttons: {
                "Aplicar": function() {
                    var selected = new Array();
                    jQuery('#tabela-mtc .coluna-tabela').hide();
                    jQuery("input:checkbox[name='tabela-mtc-column']:checked").each(function() {
                        var keyColumn = jQuery(this).val();
                        jQuery('#tabela-mtc .' + keyColumn).show();
                        selected.push(keyColumn);
                    });

                    localStorage.setItem('tabela-mtc-column', selected);
                    jQuery(this).dialog("close");
                },
                "Cancelar": function() {
                    jQuery(this).dialog("close");
                }
            },
        }).dialog('open');
    });

    // Exibir/ocultar no click
    // jQuery("#tabela-lmu input:checkbox[name='tabela-lmu-column']").on('change', function() {
    //     var keyColumn = jQuery(this).val();
    //     jQuery('#tabela-lmu .' + keyColumn).toggle();
    // });

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

    jQuery('#form #bt_exportar').on('click', function() {
        jQuery('#form #equipamentos').val(JSON.stringify(equipamentos));
    });

    jQuery('#form').on('submit', function() {
        if(jQuery('#form #placa').val().length > 0 && jQuery('#form #placa').val().length < 4) {
            jQuery('#mensagem_alerta').text(jQuery('#form #placa').data('msg-error')).removeClass('invisivel');
            return false;
        }
    });

    /*
     * Tratamento somente numeros inteiros, letras e underscore
     */
    jQuery('body').on('keyup blur', '.codigo', function() {
        jQuery(this).val(jQuery(this).val().replace(/[^A-Za-z0-9]/g, ''));
    });

    // Resetar o cliente ao digitar outro nome
    jQuery('#form #cliente').on('keyup keydown', function() {
        jQuery('#form #clioid').val('');
    });

    // Seleção de cliente específico na lista consolidada
    jQuery('.selecionar-cliente').on('click', function() {
        jQuery('#form #cliente').val(jQuery(this).parents('tr').find('.nome').text());
        jQuery('#form #clioid').val(jQuery(this).data('clioid'));
        jQuery('#form').submit();
    });


});