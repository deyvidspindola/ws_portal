/*
 * Package: Principal
 * Author: Andre L. Zilz
 */


//Variaveis
var msg_campos_obrigatorios           = "Existem campos obrigatórios não preenchidos.";
var msg_cpf_cnpj_obrigatorio          = "É Necessário informar o CPF/CNPJ do cliente,";
var msg_erro_processamento            = "Houve um erro no processamento dos dados.";
var msg_erro_arquivo                  = "Houve um erro no processamento do arquivo.";
var msg_sucesso_aprovacao             = "Solicitação de aprovação efetuada com sucesso.";
var msg_telefone_invalido             = "Número de telefone inválido";
var msg_telefone_obrigatorio          = "É necessário informar ao menos um telefone de contato.";
var msg_cliente_nao_informado         = "É necessário informar o CPF / CNPJ.";
var msg_sucesso_modificacao_cancelada = "Modificação cancelada com sucesso.";
var msg_alerta_taxa_zero              = "Para pagamento com cartão, a taxa não pode ser zero.";

var tipoPessoa                        = '';

const EFETIVACAO_DEMO                   = '8';
const MIGRACAO_INST_DERIVADA_RNR        = '9';
const UPGRADE_MOBILE_EQPTO_CONVENCIONAL = '10';
const MIGRACAO_EX_SEGURADO              = '14';
const MIGRACAO_REATIVACAO_EX_SEGURADO   = '16';
const DUPLICACAO_CONTRATO_PLACA2        = '21';
const GRUPO_TROCA_VEICULO               = '7';


jQuery(document).ready(function() {

    //========================================= Geral ===================================//

    /*
     * Popula as combos da tela de pesquisa ao ser carregada
     * deixando selecionado os valores submetidos
     */
    if (jQuery('#tela_ativa').text() == 'pesquisa') {
        popularCombos('cmgoid');
        popularCombos('depoid');
    } else if (jQuery('#tela_ativa').text() == 'cadastro') {

        tipo_pessoa = jQuery('#tipo_pessoa option:selected').val();
        cpf_cnpj = jQuery('#cpf_cnpj').val();
        id_tipo_modificacao = jQuery('#cmtoid option:selected').val();
        valorFormatado = '';

        if (tipo_pessoa == 'F') {
            valorFormatado = formatarCampo(cpf_cnpj, '000.000.000-00');
        } else if (tipo_pessoa == 'J') {
            valorFormatado = formatarCampo(cpf_cnpj, '00.000.000/0000-00');
        }

        jQuery('#cpf_cnpj').val(valorFormatado);

        //Recarregar combo [Banco]
        if (jQuery('#cmdfpforcoid').val() != '') {
            popularCombos('cmdfpforcoid');
        }

        if (id_tipo_modificacao == MIGRACAO_EX_SEGURADO) {
            popularCombos('migracao_ex');
        } else if (id_tipo_modificacao == MIGRACAO_REATIVACAO_EX_SEGURADO) {
            popularCombos('migracao_ex_reativacao');
        }
    }


    /*
     * botão voltar
     */
    jQuery("#btn_voltar").click(function() {

        if (jQuery('#tela_ativa').text() == 'cadastro') {

            jQuery("#msg_confirmar_voltar").dialog({
                title: "Confirmar voltar",
                resizable: false,
                modal: true,
                buttons: {
                    "Sim": function() {

                        jQuery(this).dialog("close");

                        //destruirSessao
                        jQuery.ajax({
                            url: 'prn_modificacao_contrato.php',
                            type: 'POST',
                            data: {
                                acao: 'destruirSessao'
                            },
                            success: function() {
                                window.location.href = "prn_modificacao_contrato.php";
                            }
                        });

                    },
                    "Não": function() {
                        jQuery(this).dialog("close");
                    }
                }
            });

        } else {

            //destruirSessao
            jQuery.ajax({
                url: 'prn_modificacao_contrato.php',
                type: 'POST',
                data: {
                    acao: 'destruirSessao'
                },
                success: function() {
                    window.location.href = "prn_modificacao_contrato.php";
                }
            });

        }



    });

    /*
     * Tratamento somente numeros inteiros
     */
    jQuery('body').on('keyup blur', '.numerico', function() {
        jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));

    });
    /*
     * Tratamento somente caracteres alfabeticos maiusculcos sem acento
     */
	jQuery('body').on('keyup blur', '.alpha', function() {
		jQuery(this).val(jQuery(this).val().replace(/[^A-Za-z \-]/g, '').toUpperCase());
	});

    /*
     * Mascara para moeda
     */
    jQuery('.moeda').maskMoney({
        thousands: '.',
        decimal: ','
    });

    /*
     * Função para inserir máscara e validar digitação para CNPJ, CPF
     * Obs: a função do [mascaras.js] é bugada!
     * formato mascara: exemplo: 000.000.000-00 (CPF)
     */
    function formatarCampo(valor, mascara) {

        valor = valor.replace(/[^0-9]/g, "");
        var tamanhoMaximo = mascara.length;
        var tamanhoMascara = valor.length;
        var boleanoMascara;
        var novoValorCampo = "";
        var posicaoCampo = 0;

        for (i = 0; i <= tamanhoMascara; i++) {

            boleanoMascara = ((mascara.charAt(i) == "-") || (mascara.charAt(i) == ".") || (mascara.charAt(i) == "/"));

            if (boleanoMascara) {
                novoValorCampo += mascara.charAt(i);
                tamanhoMascara++;
            } else {
                novoValorCampo += valor.charAt(posicaoCampo);
                posicaoCampo++;
            }
        }

        return novoValorCampo.slice(0, tamanhoMaximo);
    }


    //========================================= Tela de Pesquisa: INICIO ===================================//

    /*
     * botão pesquisar
     */
    jQuery("#btn_pesquisar").click(function() {

        jQuery.ajax({
            url: 'prn_modificacao_contrato.php',
            type: 'POST',
            data: {
                acao: 'destruirSessaoPaginacao'
            },
            success: function() {
                jQuery('#form_modificacao_pesquisa').submit();
            }
        });
    });

    /*
     * botão pesquisar (contratos a vencer)
     */
    jQuery("#btn_pesquisar_contratos_vencer").click(function() {

        jQuery.ajax({
            url: 'prn_modificacao_contrato.php',
            type: 'POST',
            data: {
                acao: 'destruirSessaoPaginacao'
            },
            success: function() {
                jQuery('#form_pesquisa_contratos_vencer').submit();
            }
        });

    });

    /*
     * botão novo
     */
    jQuery("#btn_novo").click(function() {
        window.location.href = "prn_modificacao_contrato.php?acao=cadastrar&sub_tela=aba_dados_principais";
    });

    /*
     * Popular combos [Usuario] e [Tipo Modificacao]
     */
    jQuery('#form_modificacao_pesquisa').on('change', '#depoid, #cmgoid', function() {
        popularCombos(this.id);
    });

    /*
     * Busca de motivo substituicao por autocomplete
     */
    jQuery("#msubdescricao").autocomplete({
        source: 'prn_modificacao_contrato.php?acao=recuperarMotivoSubstituicaoAjax&chamada=ajax',
        minLength: 3,
        response: function(event, ui) {

            var conteudoInput = jQuery(this).val();

            jQuery('#msg_alerta_autocomplete').fadeOut(function() {
                if (!ui.content.length) {
                    jQuery('#msg_alerta_autocomplete').html('Nenhum resultado encontrado com: ' + conteudoInput).fadeIn();
                }
            });

            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {}
                }
            });

        },
        select: function(event, ui) {

            jQuery("#mdfmsuboid").val(ui.item.id);
            jQuery('#msubdescricao').val(ui.item.value);
        }
    });

    /*
     * Busca de clientes por autocomplete
     */
    jQuery("#clinome_pesq").autocomplete({

        source: 'prn_modificacao_contrato.php?acao=recuperarCliente',
        minLength: 3,
        response: function(event, ui) {

            jQuery('#msg_alerta_autocomplete').fadeOut(function() {
                if (!ui.content.length) {
                    jQuery('#msg_alerta_autocomplete').html("Cliente não encontrado em nossa base.").fadeIn();
                }
            });

            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {}
                }
            });

        },
        select: function(event, ui) {

            jQuery("#clioid_pesq").val(ui.item.id);
            jQuery('#clinome_pesq').val(ui.item.value);
        }
    });

    //Campo [Cliente]
    jQuery("#clinome_pesq").blur(function() {

        if (jQuery.trim(this.value) == '') {
            jQuery("#clioid_pesq").val('');
        }

    });

    //Campo [Motivo Subistituicao]
    jQuery("#msubdescricao").blur(function() {

        if (jQuery.trim(this.value) == '') {
            jQuery("#msuboid").val('');
        }

    });

    //========================================= Tela de Pesquisa: FIM ===================================//


    //================================ Aba Cadastro Modificacao: INICIO ==================================//

    /*
     * Acoes da combo [Migrar Para]
     */
    jQuery('#migrar_para').click(function() {

        analise = jQuery('#migrar_para option:selected').data('analise');
        cliente_pagador = jQuery('#migrar_para option:selected').data('pagador');
        forma = cliente_pagador = jQuery('#migrar_para option:selected').data('forma');

        //Seleciona a forma de pgto do cliente
        jQuery('#cmdfpforcoid option').each(function(id, opcao) {
            if (opcao.value == forma) {
                jQuery(opcao).prop('selected', true);
                return false;
            }
        });

        jQuery('#cmdfpforcoid_aux').val(forma);
        jQuery('#cliente_pagador').val(cliente_pagador);

        if ((cliente_pagador != '0') && (analise == 't')) {
             jQuery('#analise_credito').val(analise);

        } else if ((cliente_pagador == '0') && (analise == 't')) {
            //fazer analise de credito manual
            jQuery('#analise_credito').val(analise);
            jQuery('#bloco_cpf_cnpj').show();
            jQuery('#esconder_cpf_cnpj').val('f');
        } else {
            jQuery('#bloco_cpf_cnpj').hide();
            jQuery('#esconder_cpf_cnpj').val('t');
        }

    });

    /*
     * Regras aplciadas para acao do campo [Nº Contrato]
     */
    jQuery('#cmfconnumero').blur(function() {

        jQuery('#img_cmfconnumero').show();

        var contrato        = jQuery.trim(this.value);
        var cliente         = jQuery('#cmfclioid_destino').val();
        var troca_cliente   = jQuery('#cmtoid option:selected').data('troca');
        var grupo           = jQuery('#cmtoid option:selected').data('grupo');
        var classe_contrato = jQuery('#msuboid option:selected').data('eqcoid');

        if (classe_contrato != '') {
            jQuery('#msubeqcoid').val(classe_contrato);
        } else {
            jQuery('#msubeqcoid').val('');
        }

        jQuery('input').removeClass('erro');
        jQuery('.mensagem').hide();

        if (contrato != '') {

            if (cliente == '') {

                jQuery('#cpf_cnpj').addClass('erro');
                jQuery('#mensagem_alerta').text(msg_cliente_nao_informado);
                jQuery('#mensagem_alerta').show();
                jQuery('#cmfconnumero').val('');

            } else {

                //Validar status da linha
                validarStatusLinha();

                //Validacoes para o contrato
                jQuery.ajax({
                    url: 'prn_modificacao_contrato.php',
                    type: 'POST',
                    async: false,
                    data: {
                        acao: 'validarContrato',
                        cmfconnumero: contrato,
                        cmfclioid_destino: cliente,
                        cmpttroca_cliente: troca_cliente,
                        cmtcmgoid: grupo
                    },
                    success: function(data) {

                        if (data.length > 0) {

                            data = JSON.parse(data);

                            if (Object.keys(data).length == 0 || (data.erro != 'erro_banco' && data.erro != '')) {

                                jQuery('#mensagem_alerta').text(data.erro);
                                jQuery('#mensagem_alerta').show();

                            } else if (data && data.erro == 'erro_banco') {
                                jQuery('#mensagem_erro').text(msg_erro_processamento);
                                jQuery('#mensagem_erro').show();
                                jQuery('#cmfconnumero').val('');
                                jQuery('#cmfconnumero').addClass('erro');

                            } else {
                                //Entrou porque contrato OK.

                                //Seleciona o tipo de contrato do contrato informado
                                jQuery('#cmftpcoid_destino option').each(function(id, valor) {
                                    if (valor.value == data.conno_tipo) {
                                        jQuery(this).prop('selected', true);
                                    }
                                });

                                if ((classe_contrato == '') || (classe_contrato === null)) {

                                    jQuery('#msubeqcoid').val(data.coneqcoid);
                                    classe_contrato = data.coneqcoid;

                                    //Seleciona na combo [Classe Contrato]
                                    jQuery('#cmfeqcoid_destino option').each(function() {
                                        if (this.value == classe_contrato) {
                                            jQuery(this).prop('selected', true);
                                            return false;
                                        }
                                    });

                                    //Atribui valor de monitoramento ao campo
                                    setValorMonitoramento();

                                }

                                //Selecionar Vigencia na combo
                                jQuery('#cmfcvgoid option').each(function(id, val) {

                                    if (jQuery.trim(val.text) == data.conprazo_contrato) {
                                        jQuery(val).prop('selected', true);
                                        return false;
                                    }
                                });

                                if (!jQuery('#acoes_lote').prop('checked')) {

                                    jQuery.ajax({
                                        url: 'prn_modificacao_contrato.php',
                                        type: 'POST',
                                        async: false,
                                        data: {
                                            acao: 'recuperarDadosVeiculo',
                                            cmfconnumero: contrato
                                        },
                                        success: function(data) {

                                            data = JSON.parse(data);

                                            if (data) {

                                                //Exibe o link com a placa do veiculo
                                                html = '<a href="veiculo.php?veioid=' + data.veioid + '" target="_blank">' + data.veiplaca + '</a>';
                                                jQuery('#link_placa').next().remove();
                                                jQuery('#link_placa').after(html);

                                                jQuery('#veiplaca').val(data.veiplaca);
                                                jQuery('#cmfveioid').val(data.veioid);


                                            } else {
                                                jQuery('#mensagem_erro').text(msg_erro_processamento);
                                                jQuery('#mensagem_erro').show();
                                            }

                                        }
                                    });
                                }


                                //Informacoes de monitoramento
                                jQuery.ajax({
                                    url: 'prn_modificacao_contrato.php',
                                    type: 'POST',
                                    async: false,
                                    data: {
                                        acao: 'recuperarMonitoramentoLocacaoContrato',
                                        cmfconnumero: contrato
                                    },
                                    success: function(data) {

                                        if (data.length > 0) {

                                            data = JSON.parse(data);

                                            if (Object.keys(data).length > 0) {

                                                valor_monitoramento = tratarMoeda(data.cpagmonitoramento, 'A');
                                                valor_locacao = tratarMoeda(data.cpagvl_servico, 'A');

                                                //Monitoramento
                                                jQuery('#cmdfpvlr_monitoramento_negociado').val(valor_monitoramento);
                                                jQuery('#cmdfpvlr_monitoramento_tabela').val(data.cpagmonitoramento);
                                                jQuery('#eqcvlr_minimo_mens').val(data.cpagmonitoramento);
                                                jQuery('#eqcvlr_maximo_mens').val(data.cpagmonitoramento);

                                                //Locacao
                                                jQuery('#cmdfpvlr_locacao_negociado').val(valor_locacao);
                                                jQuery('#cmdfpvlr_locacao_tabela').val(data.cpagvl_servico);
                                                jQuery('#tpivalor_minimo').val(data.cpagvl_servico);


                                                //Parcelamento
                                                jQuery('#cmdfpcpvoid option').each(function(id, opcao) {

                                                    if (opcao.value == data.cpagcpvoid) {
                                                        jQuery(opcao).prop('selected', true);
                                                        popularCombos('cpvoid');
                                                        return false;
                                                    }

                                                });

                                                jQuery('#cmdfpcpvoid_aux').val(data.cpagcpvoid);
                                            }

                                        } else {
                                            jQuery('#cmdfpvlr_monitoramento_negociado').val('0,00');
                                            jQuery('#cmdfpvlr_monitoramento_tabela').val('0.00');
                                            jQuery('#eqcvlr_minimo_mens').val('0.00');
                                            jQuery('#eqcvlr_maximo_mens').val('0.00');
                                            jQuery('#cmdfpcpvoid_aux').val('');
                                        }
                                    }
                                });

                            }
                            //FIM (data.length > 0)
                        }
                        //Fim sucess
                    }
                });
            }
        }

        jQuery('#img_cmfconnumero').hide();

    });

    /*
     * Acao da combo [Motivo substituicao]
     */
    jQuery('#form_cadastro_modificacao').on('change', '#msuboid', function() {

        troca_veiculo = jQuery('#msuboid option:selected').data('troca');
        classe_contrato = jQuery('#msuboid option:selected').data('eqcoid');

        jQuery('#troca_veiculo').val(troca_veiculo);

        //Seleciona na combo [Classe Contrato]
        jQuery('#cmfeqcoid_destino option').each(function() {
            if (this.value == classe_contrato) {

                jQuery(this).prop('selected', true);

                return false;
            }
        });

        setValorMonitoramento();

        if (troca_veiculo == 't') {
            jQuery('#bloco_novo_veiculo').show();
            jQuery('#exibir_novo_veiculo').val('t');
        } else {
            jQuery('#bloco_novo_veiculo').hide();
            jQuery('#exibir_novo_veiculo').val('f');
        }

    });

    /*
     * Acao da combo [Classe de Contrato]
     */
    jQuery('#form_cadastro_modificacao').on('change', '#cmfeqcoid_destino', function() {

        setValorMonitoramento();

    });

    /*
     * Acao da combo [Acessorios]
     */
    jQuery('#form_cadastro_modificacao').on('change', '#cmsobroid', function() {

        tipoNegociacao = jQuery('#cmssituacao').val();

        if (tipoNegociacao != '') {
            valor = jQuery('#cmsobroid option:selected').data('valor');
            jQuery('#cmsvalor_tabela').val(valor);

            valor = tratarMoeda(valor, 'A');
            jQuery('#cmsvalor_negociado').val(valor);
        }

    });

    /*
     * Acao da combo [Tipo de Neogiacao]
     */
    jQuery('#form_cadastro_modificacao').on('change', '#cmssituacao', function() {

        tipoNegociacao = jQuery('#cmssituacao').val();

        if (tipoNegociacao == 'L') {
            jQuery('#cmsvalor_negociado').removeClass('desabilitado').removeProp('disabled');

            valor = jQuery('#cmsobroid option:selected').data('valor');
            jQuery('#cmsvalor_tabela').val(valor);

            valor = tratarMoeda(valor, 'A');
            jQuery('#cmsvalor_negociado').val(valor);
        } else {
            jQuery('#cmsvalor_negociado').val('');
            jQuery('#cmsvalor_tabela').val('');
            jQuery('#cmsvalor_negociado').addClass('desabilitado').prop('disabled', true);
        }

    });


    /*
     * Acao do campo [Monitoramento]
     */
    jQuery('#form_cadastro_modificacao').on('blur', '#cmdfpvlr_monitoramento_negociado', function() {

        jQuery('.mensagem').hide();
        jQuery('.erro').removeClass('erro');

        valor = this.value.replace(',', '.');
        valor = parseFloat(valor);

        minimoReais = jQuery('#eqcvlr_minimo_mens').val();
        minimo = parseFloat(minimoReais.replace(',', '.'));

        maximoReais = jQuery('#eqcvlr_maximo_mens').val();
        maximo = parseFloat(maximoReais.replace(',', '.'));

        if ((valor < minimo) || (valor > maximo)) {
            jQuery('#msg_alerta_bloco_faturamento').show();
            jQuery('#msg_alerta_bloco_faturamento').html('Valor não permitido para a Classe, deve permanecer entre R$ ' + minimoReais + ' e R$ ' + maximoReais + '.');
            jQuery(this).addClass('erro');
        }
    });

    /*
     * Acao da combo [Parcelamento]
     */
    jQuery('#form_cadastro_modificacao').on('change', '#cmdfpcpvoid', function() {

        var contrato = jQuery.trim(jQuery('#cmfconnumero').val());
        var idParcela = jQuery('#cmdfpcpvoid').val();
        var classe_contrato = jQuery('#msuboid option:selected').data('eqcoid');


        if (jQuery('#cmdfpcpvoid option:selected').val() != '') {

            parcelas = jQuery('#cmdfpcpvoid option:selected').data('parcelas');
            jQuery('#cmdfpnum_parcela').val(parcelas);
            jQuery('#cmdfpcpvoid_aux').val(idParcela);


            //Informacoes de monitoramento
            jQuery.ajax({
                url: 'prn_modificacao_contrato.php',
                type: 'POST',
                data: {
                    acao: 'recuperarDadosLocacao',
                    cmfconnumero: contrato,
                    eqcoid: classe_contrato,
                    cmdfpcpvoid: idParcela
                },
                success: function(data) {

                    if (data.length > 0) {

                        data = JSON.parse(data);

                        if (Object.keys(data).length > 0) {

                            valor = tratarMoeda(data.tpivalor, 'A');

                            jQuery('#cmdfpvlr_locacao_negociado').val(valor);
                            jQuery('#cmdfpvlr_locacao_tabela').val(data.tpivalor);
                            jQuery('#tpivalor_minimo').val(data.tpivalor_minimo);
                        }

                    } else {
                        jQuery('#cmdfpvlr_locacao_negociado').val('0,00');
                        jQuery('#cmdfpvlr_locacao_tabela').val('0.00');
                        jQuery('#tpivalor_minimo').val('0.00');
                    }

                }
            });

            popularCombos('cpvoid');
            jQuery('#cmsqtde').val('1');

        } else {
            jQuery('#cmdfpvlr_locacao_negociado').val('');
            jQuery('#cmdfpvlr_locacao_tabela').val('');
            jQuery('#tpivalor_minimo').val('');
        }


    });

    /*
     * Acao do campo [Locacao]
     */
    jQuery('#form_cadastro_modificacao').on('blur', '#cmdfpvlr_locacao_negociado', function() {

        jQuery('.mensagem').hide();
        jQuery('.erro').removeClass('erro');

        valor = this.value.replace(',', '.');
        valor = parseFloat(valor);

        minimoReais = jQuery('#tpivalor_minimo').val();
        minimo = parseFloat(minimoReais.replace(',', '.'));


        if (valor < minimo) {
            jQuery('#msg_alerta_bloco_faturamento').show();
            jQuery('#msg_alerta_bloco_faturamento').html('Valor não permitido para a Classe, deve permanecer no mínimo no valor de R$ ' + minimoReais + '.');
            jQuery(this).addClass('erro');
        }
    });

    /*
     * Acao do checkbox [Isentar Valor de Locação]
     */
    jQuery('#form_cadastro_modificacao').on('click', '#cmdfpisencao_locacao', function() {

        if (jQuery('#cmdfpisencao_locacao').prop('checked')) {
            jQuery('#cmdfpisencao_locacao').val('t');
            jQuery('#cmdfpvlr_locacao_negociado').val('');
            jQuery('#cmdfpvlr_locacao_negociado').addClass('desabilitado').prop('disabled', true);
        } else {
            jQuery('#cmdfpisencao_locacao').val('f');
            jQuery('#cmdfpvlr_locacao_negociado').removeClass('desabilitado').removeProp('disabled');
            jQuery('#cmdfpvlr_locacao_negociado').val(jQuery('#cmdfpvlr_locacao_tabela').val());
        }

    });

    /*
     * Acao do checkbox [Isentar Valor de Taxa]
     */
    jQuery('#form_cadastro_modificacao').on('click', '#cmdfpisencao_taxa', function() {

        if (jQuery('#cmdfpisencao_taxa').prop('checked')) {
            jQuery('#cmdfpisencao_taxa').val('t');
            jQuery('#cmdfpvlr_taxa_negociado').val('');
            jQuery('#cmdfpvlr_taxa_negociado').addClass('desabilitado').prop('disabled', true);
            jQuery('#bloco_pgto_cartao').hide();
        } else {
            jQuery('#cmdfpisencao_taxa').val('f');
            jQuery('#cmdfpvlr_taxa_negociado').removeClass('desabilitado').removeProp('disabled');
            jQuery('#cmdfpvlr_taxa_negociado').val(jQuery('#cmdfpvlr_taxa_tabela').val());

            produto_siggo = jQuery('#cmtoid option:selected').data('siggo');

            forma = jQuery('#forma_pgto').val();

            if (produto_siggo == 't' && forma == 'credito') {
                jQuery('#bloco_pgto_cartao').show();
            }

        }

    });

    /*
     * Acao da combo [Taxa]
     */
    jQuery('#form_cadastro_modificacao').on('change', '#cmdfpobroid_taxa', function() {

        setarValorTaxa();

    });

    /*
     * Acao do botao [Adicionar] Acessorios
     */
    jQuery('#form_cadastro_modificacao').on('click', '#btn_adicionar_acessorio', function() {

        erros = 0;
        cpvoid = jQuery('#cmdfpcpvoid option:selected').val();
        dados = new Array();
        situacao = {
            L: 'Locação',
            C: 'Cliente',
            S: 'Cortesia',
            D: 'Demonstração',
            W: 'Virtual'
        };
        html = '';

        jQuery('.erro').removeClass('erro');
        jQuery('#msg_alerta_acessorio').hide();

        if (jQuery('#cmssituacao').val() != 'L') {
            jQuery('#cmsvalor_negociado').removeClass('obrigatorio_acessorio');
        } else {
            jQuery('#cmsvalor_negociado').addClass('obrigatorio_acessorio');
        }

        //Percorre elementos obrigatorios para identificar se algum nao foi informado
        jQuery('.obrigatorio_acessorio').each(function(id, valor) {

            elemento = jQuery('#' + valor.id);

            if (jQuery.trim(elemento.val()) == '') {
                elemento.addClass('erro');
                erros++;
            }
        });

        if (erros > 0) {
            jQuery('#msg_alerta_acessorio').html(msg_campos_obrigatorios);
            jQuery('#msg_alerta_acessorio').show();
            return false;
        }

        dados['obrobrigacao'] = jQuery('#cmsobroid option:selected').text();
        dados['cmsobroid'] = jQuery('#cmsobroid option:selected').val();
        dados['cmssituacao'] = jQuery('#cmssituacao option:selected').val();
        dados['cmsvalor_negociado'] = (jQuery('#cmsvalor_negociado').val() == '') ? '0,00' : jQuery('#cmsvalor_negociado').val();
        dados['cmsvalor_tabela'] = jQuery('#cmsobroid option:selected').data('valor');
        dados['cmsqtde'] = jQuery('#cmsqtde').val();


        html += '<tr class="">';
        html += '<td class="esquerda">';
        html += dados["obrobrigacao"];
        html += '<input type="hidden" id="acessorio_obroid" name="acessorio_obroid[]" value="' + dados["cmsobroid"] + '">';
        html += '<input type="hidden" id="acessorio_nome" name="acessorio_nome[]" value="' + dados["obrobrigacao"] + '">';
        html += '<input type="hidden" id="acessorio_cpvoid" name="acessorio_cpvoid[]" value="' + cpvoid + '">';
        html += '</td>';
        html += '<td class="esquerda">';
        html += situacao[dados["cmssituacao"]];
        html += '<input type="hidden" id="acessorio_situacao" name="acessorio_situacao[]" value="' + dados["cmssituacao"] + '">';
        html += '</td>';
        html += '<td class="direita">';
        html += 'R$ ' + dados["cmsvalor_negociado"];
        html += '<input type="hidden" id="acessorio_valor_negociado" name="acessorio_valor_negociado[]" value="' + dados["cmsvalor_negociado"] + '">';
        html += '<input type="hidden" id="acessorio_valor_tabela" name="acessorio_valor_tabela[]" value="' + dados["cmsvalor_tabela"] + '">';
        html += '</td>';
        html += '<td class="direita">';
        html += dados["cmsqtde"];
        html += '<input type="hidden" id="acessorio_qtde" name="acessorio_qtde[]" value="' + dados["cmsqtde"] + '">';
        html += '</td>';
        html += '<td class="centro">';
        html += '<img class="icone hand excluir" src="images/icon_error.png" title="Excluir" />';
        html += '</td>';
        html += '</tr>';

        //Exibir a lista com o contato inserido
        jQuery('#lista_acessorios').show();
        jQuery('#lista_acessorios tbody').append(html);

        //Reordenar as cores da slinhas
        aplicarCorLinha('lista_acessorios');

        //limpa campos do fornulario
        jQuery('#cmssituacao').val('');
        jQuery('#cmsvalor_negociado').val('');
        jQuery('#cmsobroid').val('');
        jQuery('#cmsqtde').val('1');

    });

    /*
     * Acoes do icone [excluir] do bloco de acessorios
     */
    jQuery("#lista_acessorios table").on('click', '.excluir', function() {

        elemento = this;
        linhasAtivas = 0;

        jQuery("#msg_confirmar_excluir_acessorio").dialog({
            title: "Confirmação de Exclusão",
            resizable: false,
            modal: true,
            buttons: {
                "Sim": function() {
                    jQuery(this).dialog("close");

                    jQuery(elemento).parent().parent().remove();
                    aplicarCorLinha('lista_acessorios');

                    //Verifica se foi o ultima linha removida. Se sim esconde o bloco de listagem
                    jQuery('#lista_acessorios table tbody tr').each(function() {
                        linhasAtivas++;
                    });

                    if (linhasAtivas == 0) {
                        jQuery('#lista_acessorios').hide();
                    }

                },
                "Não": function() {
                    jQuery(this).dialog("close");
                }
            }
        });
    });


    /*
     * Acao do botao [Adicionar] contatos
     */
    jQuery('#form_cadastro_modificacao').on('click', '#btn_adicionar_contato', function() {


        //------------ Validar Campos obrigatorios conforme regras da ES: inicio -------------//
        var temErros = true;
        var fonesObrigatorios = 0;
        var fonesInvalidos = 0;
        var erros = 0;

        jQuery('.erro').removeClass('erro');
        jQuery('#msg_alerta_contato').hide();

        //Telefones
        jQuery('.telefone').each(function(id, valor) {

            elemento = jQuery('#' + valor.id);
            digitos = elemento.val().replace(/[^0-9]/g, "").length;

            if (digitos == 0) {
                fonesObrigatorios++;
            } else if (digitos < 11) {
                fonesInvalidos++;
                elemento.addClass('erro');
            }

        });

        if (fonesInvalidos > 0) {
            jQuery('#msg_alerta_contato').html(msg_telefone_invalido);
            jQuery('#msg_alerta_contato').show();
        } else {

            if (fonesObrigatorios == 3) {
                jQuery('.telefone').addClass('obrigatorio');
            } else {
                jQuery('.telefone').removeClass('obrigatorio');
            }

            if (jQuery('#cmctautorizada').prop('checked')) {
                jQuery('#cmctcpf, #cmctrg').addClass('obrigatorio');
            } else {
                jQuery('#cmctcpf, #cmctrg').removeClass('obrigatorio');
            }

            if (jQuery('#cmctinstalacao').prop('checked')) {
                jQuery('#cmctobservacao').addClass('obrigatorio');
            } else {
                jQuery('#cmctobservacao').removeClass('obrigatorio');
            }

            //Percorre elementos obrigatorios para identificar se algum nao foi informado
            jQuery('.obrigatorio').each(function(id, valor) {

                elemento = jQuery('#' + valor.id);

                if (elemento.is("fieldset")) {
                    marcados = jQuery('#' + valor.id + ' input:checked').length;
                    if (marcados == 0) {
                        elemento.addClass('erro');
                        erros++;
                    }

                } else if (jQuery.trim(elemento.val()) == '') {
                    elemento.addClass('erro');
                    erros++;
                }
            });

            if (erros > 0) {
                jQuery('#msg_alerta_contato').html(msg_campos_obrigatorios);
                jQuery('#msg_alerta_contato').show();
            } else {
                temErros = false;
            }
        }

        // ------------ Validar Campos obrigatorios conforme regras da ES: fim -------------//

        //Se tudo OK incluir nova linha na lista de contatos
        if (!temErros) {
            status_modificacao = jQuery('#mdfstatus').val();
            campos = jQuery('#form_contatos :input').serializeArray();
            var dados = new Array();
            tipoContato = '';
            html = '';

            //Reorganiza os dados entre chave e valor
            jQuery.each(campos, function(id, valor) {
                dados[valor.name] = valor.value;
            });

            tipoContato += (typeof dados["cmctemergencia"] != 'undefined') ? 'Emergência, ' : '';
            tipoContato += (typeof dados["cmctinstalacao"] != 'undefined') ? 'Instalação, ' : '';
            tipoContato += (typeof dados["cmctautorizada"] != 'undefined') ? 'Autorizada, ' : '';

            cmctautorizada = (typeof dados["cmctautorizada"] == 'undefined') ? 'f' : 't';
            cmctemergencia = (typeof dados["cmctemergencia"] == 'undefined') ? 'f' : 't';
            cmctinstalacao = (typeof dados["cmctinstalacao"] == 'undefined') ? 'f' : 't';

            //Remover ultima virgula
            tipoContato = tipoContato.substring(0, tipoContato.lastIndexOf(','));

            html += '<tr class="">';
            html += '<td class="esquerda">';
            html += dados["cmctnome"];
            html += '<input type="hidden" id="contatos_nome" name="contatos_nome[]" value="' + dados["cmctnome"] + '">';
            html += '</td>';
            html += '<td class="direita">';
            html += dados["cmctcpf"];
            html += '<input type="hidden" id="contatos_cpf" name="contatos_cpf[]" value="' + dados["cmctcpf"] + '">';
            html += '</td>';
            html += '<td class="direita">';
            html += dados["cmctrg"];
            html += '<input type="hidden" id="contatos_rg" name="contatos_rg[]" value="' + dados["cmctrg"] + '">';
            html += '</td>';
            html += '<td class="direita">';
            html += dados["cmctfone_res"];
            html += '<input type="hidden" id="contatos_fone_res" name="contatos_fone_res[]" value="' + dados["cmctfone_res"] + '">';
            html += '</td>';
            html += '<td class="direita">';
            html += dados["cmctfone_com"];
            html += '<input type="hidden" id="contatos_fone_com" name="contatos_fone_com[]" value="' + dados["cmctfone_com"] + '">';
            html += '</td>';
            html += '<td class="direita">';
            html += dados["cmctfone_cel"];
            html += '<input type="hidden" id="contatos_fone_cel" name="contatos_fone_cel[]" value="' + dados["cmctfone_cel"] + '">';
            html += '</td>';
            html += '<td class="direita">';
            html += dados["cmctfone_nextel"];
            html += '<input type="hidden" id="contatos_nextel" name="contatos_nextel[]" value="' + dados["cmctfone_nextel"] + '">';
            html += '</td>';
            html += '<td class="esquerda">';
            html += tipoContato;
            html += '<input type="hidden" id="contatos_obs" name="contatos_obs[]" value="' + dados["cmctobservacao"] + '">';
            html += '<input type="hidden" id="contatos_autorizada" name="contatos_autorizada[]" value="' + cmctautorizada + '">';
            html += '<input type="hidden" id="contatos_emergencia" name="contatos_emergencia[]" value="' + cmctemergencia + '">';
            html += '<input type="hidden" id="contatos_instalacao" name="contatos_instalacao[]" value="' + cmctinstalacao + '">';
            html += '</td>';
            html += '<td class="centro">';

            if (status_modificacao != 'P') {
                html += '<img class="icone hand excluir" src="images/icon_error.png" title="Excluir" />';
            }

            html += '<img class="icone hand editar" src="images/edit.png" title="Editar" />';
            html += '</td>';
            html += '</tr>';

            //Exibir a lista com o contato inserido
            jQuery('#lista_contatos').show();
            jQuery('#lista_contatos tbody').append(html);

            //Reordenar as cores da slinhas
            aplicarCorLinha('lista_contatos');

            //limpa campos do fornulario
            jQuery('#form_contatos :input').each(function() {

                if (this.type == 'checkbox') {
                    jQuery(this).removeProp('checked');
                } else {
                    jQuery(this).val('');
                }
            });
        }

    });

    /*
     * Acoes do icone [excluir] do bloco de contatos
     */
    jQuery("#lista_contatos table").on('click', '.excluir', function(event) {

        event.preventDefault();
        elemento = this;
        var linhasAtivas = 0;
        jQuery('.obrigatorio').removeClass('erro');
        jQuery('.mensagem').hide();
        var modificacao = jQuery('#mdfoid').val();

        jQuery("#msg_confirmar_excluir").dialog({
            title: "Confirmação de Exclusão",
            resizable: false,
            modal: true,
            buttons: {
                "Sim": function() {
                    jQuery(this).dialog("close");

                    jQuery(elemento).parent().parent().remove();
                    aplicarCorLinha('lista_contatos');

                    //Verifica se foi o ultima linha removida. Se sim esconde o bloco de listagem
                    jQuery('#lista_contatos table tbody tr').each(function() {
                        linhasAtivas++;
                    });

                    if (linhasAtivas == 0) {
                        jQuery('#lista_contatos').hide();
                    }

                },
                "Não": function() {
                    jQuery(this).dialog("close");
                }
            }
        });
    });

    /*
     * Acoes do icone [editar]
     */
    jQuery("#lista_contatos table").on('click', '.editar', function(event) {

        campos = jQuery(this).parent().parent().find('td :input').serializeArray();
        var dados = new Array();

        jQuery('form .obrigatorio').removeClass('erro');
        jQuery('#msg_alerta_contato').hide();

        //Reorganiza os dados entre chave e valor
        jQuery.each(campos, function(id, valor) {
            chave = valor.name.replace('[]', '');
            dados[chave] = valor.value;
        });

        //Popular campos do formulario de contatos
        jQuery('#cmctnome').val(dados['contatos_nome']);
        jQuery('#cmctcpf').val(dados['contatos_cpf']);
        jQuery('#cmctrg').val(dados['contatos_rg']);
        jQuery('#cmctfone_res').val(dados['contatos_fone_res']);
        jQuery('#cmctfone_com').val(dados['contatos_fone_com']);
        jQuery('#cmctfone_cel').val(dados['contatos_fone_cel']);
        jQuery('#cmctfone_nextel').val(dados['contatos_nextel']);
        jQuery('#cmctobservacao').val(dados['contatos_obs']);

        if (dados['contatos_autorizada'] == 't') {
            jQuery('#cmctautorizada').prop('checked', true);
        }

        if (dados['contatos_emergencia'] == 't') {
            jQuery('#cmctemergencia').prop('checked', true);
        }

        if (dados['contatos_instalacao'] == 't') {
            jQuery('#cmctinstalacao').prop('checked', true);
        }

    });



    /*
     * atribuir cmfrczoid a input hidden
     */
    jQuery('#form_cadastro_modificacao').on('change', '#cmffunoid_executivo', function() {

        cmfrczoid = jQuery('#cmffunoid_executivo option:selected').data('dmv');

        jQuery('#cmfrczoid').val(cmfrczoid);

    });

    /*
     * Acoes da combo [Tipo Modificacao]
     */
    jQuery('#form_cadastro_modificacao').on('change', '#cmtoid', function() {

        jQuery('#img_cmtoid').show();

        id = jQuery('#cmtoid option:selected').val();

        //limpa o formulario
        jQuery('#form_cadastro_modificacao')[0].reset();
        jQuery(':input').val('');
        jQuery('#bloco_dados_adicionais').remove('a');
        jQuery('#lista_acessorios table tbody tr').each(function() {
            jQuery(this).remove();
        });
        jQuery('#lista_contatos table tbody tr').each(function() {
            jQuery(this).remove();
        });
        jQuery('#lista_acessorios').hide();
        jQuery('#lista_contatos').hide();

        jQuery.ajax({
            url: 'prn_modificacao_contrato.php',
            type: 'POST',
            async: false,
            data: {
                acao: 'destruirParametros'
            }
        });

        //Realimenta a selecao
        jQuery('#cmtoid').val(id);

        jQuery('#bloco_cadastro').show();
        jQuery('#exibir_cadastro').val('t');

        var analise             = jQuery('#cmtoid option:selected').data('analise');
        var apresentarLote      = jQuery('#cmtoid option:selected').data('lote');
        var produto_siggo       = jQuery('#cmtoid option:selected').data('siggo');
        var siggo_seguro        = jQuery('#cmtoid option:selected').data('seguro');
        var arquivo             = jQuery('#cmtoid option:selected').data('arquivo');
        var financeiro          = jQuery('#cmtoid option:selected').data('financeiro');
        var taxa_obrigatoria    = jQuery('#cmtoid option:selected').data('taxa');
        var obroid              = jQuery('#cmtoid option:selected').data('obroid');
        var grupo               = jQuery('#cmtoid option:selected').data('grupo');
        var id_tipo_modificacao = jQuery('#cmtoid option:selected').val();

        jQuery('#produto_siggo').val(produto_siggo);
        jQuery('#cmpgmodificacao_lote').val(apresentarLote);
        jQuery('#cmptleitura_arquivo').val(arquivo);
        jQuery('#cmptrecebe_dados_financeiro').val(financeiro);
        jQuery('#img_msuboid').removeClass('invisivel');
        jQuery('#cmpttaxa').val(taxa_obrigatoria);
        jQuery('#analise_credito').val(analise);
        jQuery('#cmtcmgoid').val(grupo);

        if (id_tipo_modificacao == EFETIVACAO_DEMO) {
            popularCombos('demonstracao');
        } else {
            popularCombos('nao_demonstracao');
        }


        if (id_tipo_modificacao == MIGRACAO_EX_SEGURADO || id_tipo_modificacao == MIGRACAO_REATIVACAO_EX_SEGURADO) {

            jQuery('#bloco_migrar_para').show();
            jQuery('#bloco_dados_contratuais').hide();
            jQuery('#bloco_dados_acessorios').hide();
            jQuery('#acoes_lote_migracao').val('t');
            jQuery('#bloco_cpf_cnpj').hide();
            jQuery('#esconder_cpf_cnpj').val('t');
            jQuery('#btn_prosseguir_lote').show();
            jQuery('#btn_confirmar_mofificacao').hide();
            jQuery('#separador').show();

            if (id_tipo_modificacao == MIGRACAO_EX_SEGURADO) {
                popularCombos('migracao_ex');
            } else {
                jQuery('#bloco_cadastro').hide();
                jQuery('#exibir_cadastro').val('f');
                popularCombos('migracao_ex_reativacao');
            }

            jQuery('#btn_prosseguir_anexos').hide();

        } else {

            jQuery('#btn_confirmar_mofificacao').show();
            jQuery('#bloco_migrar_para').hide();
            jQuery('#bloco_dados_contratuais').show();
            jQuery('#bloco_dados_acessorios').show();
            jQuery('#acoes_lote_migracao').val('f');
            jQuery('#bloco_cpf_cnpj').show();
            jQuery('#esconder_cpf_cnpj').val('f');
            jQuery('#btn_prosseguir_lote').hide();
        }

        //Define se deve apresentar o bloco de faturamento
        if (financeiro == 'f') {
            jQuery('#bloco_faturamento').hide();
        } else {
            jQuery('#bloco_faturamento').show();
        }

        //Se opcao diferente de (Escolha)
        if (id != '') {

            if(grupo == GRUPO_TROCA_VEICULO) {
                jQuery('#cmdfpcpvoid').addClass('desabilitado').prop('disabled', true);
            } else {
                jQuery('#cmdfpcpvoid').removeClass('desabilitado').removeProp('disabled');
            }

            jQuery('#tipo_pessoa').prop('disabled', false);

            if (financeiro == 't') {
                jQuery('#loco_faturamento').show();
            } else {
                jQuery('#loco_faturamento').hide();
            }

            if (produto_siggo == 't') {
                jQuery('#bloco_proposta_siggo').show();
            } else {
                jQuery('#bloco_proposta_siggo').hide();
            }

            if (apresentarLote == 'f') {
                jQuery('#acoes_lote').parent().hide();
                jQuery('#anexar_arquivo').parent().show();
            } else if ( (produto_siggo != 't') && (id_tipo_modificacao != UPGRADE_MOBILE_EQPTO_CONVENCIONAL) ) {
                jQuery('#acoes_lote').parent().show();
                jQuery('#anexar_arquivo').parent().hide();
            } else {
                jQuery('#acoes_lote').parent().hide();
                jQuery('#anexar_arquivo').parent().show();
            }


            if (siggo_seguro == 't') {

                jQuery('#cmfcvgoid').prop('disabled', true);

                cmfcvgoid = '';

                jQuery('#cmfcvgoid option').each(function(id, val) {

                    if (jQuery.trim(val.text) == '12') {
                        jQuery(val).prop('selected', true);
                        cmfcvgoid = val.value;
                        return false;
                    }
                });

                jQuery('#cmfcvgoid_aux').val(cmfcvgoid);

            } else {
                jQuery('#cmfcvgoid').prop('disabled', false);
                jQuery('#cmfcvgoid_aux').val('');
            }

            if (id == MIGRACAO_INST_DERIVADA_RNR) {
                jQuery('#bloco_novo_veiculo').show();
                jQuery('#exibir_novo_veiculo').val('t');
            } else {
                jQuery('#bloco_novo_veiculo').hide();
                jQuery('#exibir_novo_veiculo').val('f');
            }

            //Pupular combo [Motivo substituicao]
            jQuery.ajax({
                url: 'prn_modificacao_contrato.php',
                type: 'POST',
                async: false,
                data: {
                    acao: 'popularComboAjax',
                    oid: id,
                    combo: 'motivo_substituicao_classe'
                },
                success: function(data) {

                    data = JSON.parse(data);

                    if (data.length > 0) {

                        var combo = '<option data-troca="" data-eqcoid="" value="">Escolha</option>';

                        jQuery.each(data, function(i, val) {
                            combo += '<option data-troca="' + val.msubtrocaveiculo + '" data-eqcoid="' + val.msubeqcoid + '" value="' + val.msuboid + '">'
                            combo += val.msubdescricao + '</option>';
                        });

                        jQuery('#combo_msuboid').show();
                        jQuery('#msuboid').html(combo);
                        jQuery('#is_combo_motivo_visivel').val('S');

                    } else {
                        jQuery('#combo_msuboid').hide();
                        jQuery('#is_combo_motivo_visivel').val('N');
                    }

                }
            });

            //Setar valor default na combo [Taxa]
            jQuery('#cmdfpobroid_taxa option').each(function(id, objeto) {

                if (objeto.value == obroid) {
                    //seleciona o item da lista
                    jQuery(this).prop('selected', true);
                    setarValorTaxa();
                    return false;
                }
            });


        } else {
            jQuery('#combo_msuboid').addClass('invisivel');
            jQuery('#tipo_pessoa').prop('disabled', true);
            jQuery('#bloco_cadastro').hide();
            jQuery('#exibir_cadastro').val('f');
        }

        jQuery('.carregando').hide();
    });


    /*
     * Acao do checkbox [Acoes em Lote]
     */
    jQuery('#form_cadastro_modificacao').on('click', '#acoes_lote', function() {

        if (jQuery(this).prop('checked')) {
            jQuery('#cmfconnumero').val('');
            jQuery('#cmfconnumero').prop('disabled', true);
            jQuery('#cmfconnumero').addClass('desabilitado');
            jQuery('#bloco_veiculo').hide();
            jQuery('#btn_confirmar_mofificacao').hide();
            jQuery('#acoes_lote').val('t');
            jQuery('#btn_prosseguir_lote').show();
            jQuery('#btn_prosseguir_anexos').hide();

        } else {
            jQuery('#cmfconnumero').removeProp('disabled');
            jQuery('#cmfconnumero').removeClass('desabilitado');
            jQuery('#bloco_veiculo').show()
            jQuery('#btn_confirmar_mofificacao').show();
            jQuery('#acoes_lote').val('f');
            jQuery('#btn_prosseguir_lote').hide();
            jQuery('#btn_prosseguir_anexos').show();
        }
    });

    /*
     * Acao do checkbox [Anexar Arquivo(s)]
     */
    jQuery('#form_cadastro_modificacao').on('click', '#anexar_arquivo', function() {

        if (jQuery(this).prop('checked')) {
            jQuery('#btn_prosseguir_anexos').show();
            jQuery('#btn_confirmar_mofificacao').hide();
            jQuery('#anexar_arquivo').val('t');

        } else {
            jQuery('#btn_prosseguir_anexos').hide();
            jQuery('#btn_confirmar_mofificacao').show();
            jQuery('#anexar_arquivo').val('f');
        }
    });

    /*
     * Popular combo [sub-tipo]
     */
    jQuery('#form_cadastro_modificacao').on('change', '#cmftppoid', function() {
        popularCombos(this.id);
    });

    /*
     * Define o comportamento do campo CNPJ/ CPF
     */
    jQuery('#form_cadastro_modificacao').on('change', '#tipo_pessoa', function() {

        tipoPessoa = this.value;
        jQuery('#cpf_cnpj').val("");
        jQuery('#mensagem_alerta').hide();
        jQuery('#cpf_cnpj').removeClass('erro');

        if (this.value != '') {
            jQuery('#cpf_cnpj').removeProp('disabled');
            jQuery('#cpf_cnpj').removeClass('desabilitado');
        } else {
            jQuery('#cpf_cnpj').prop('disabled', true);
            jQuery('#cpf_cnpj').addClass('desabilitado');
        }

    });


    /*
     * Aplicar Mascar CPF
     */
    jQuery("#form_cadastro_modificacao").on('keyup blur', '"#cmctcpf', function() {

        valor = formatarCampo(this.value, '000.000.000-00');
        jQuery(this).val(valor);
    });

    /*
     * Aplicar Mascar Telefones
     */
    jQuery("#form_cadastro_modificacao").on('keyup', '.telefone', function() {

        //Funcao presente em lib/funcoes_masc.js
        valor = formatarTelefone(this);
        jQuery(this).val(valor);
    });

    /*
     * Busca de Placa por autocomplete
     */
    jQuery("#veiplaca_novo").autocomplete({
        source: 'prn_modificacao_contrato.php?acao=recuperarDadosVeiculoAjax',
        minLength: 2,
        response: function(event, ui) {

            var conteudoInput = jQuery(this).val();

            jQuery('#veiplaca_novo').removeClass('erro');

            jQuery('#msg_alerta_dados_contratuais').fadeOut(function() {
                if (!ui.content.length) {
                    jQuery('#msg_alerta_dados_contratuais').html('Nenhum resultado encontrado com: ' + conteudoInput).fadeIn();
                    jQuery('#veiplaca_novo').addClass('erro');
                }
            });

            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {}
                }
            });

        },
        select: function(event, ui) {

            jQuery("#cmfveioid_novo").val(ui.item.id);
            jQuery('#veiplaca_novo').val(ui.item.value);

        }
    });

    /*
     * Busca de clientes por autocomplete
     */
    jQuery("#form_cadastro_modificacao").on('keyup blur focus', '"#cpf_cnpj', function() {

        var tipo_pessoa = jQuery('#tipo_pessoa option:selected').val();
        var cpf_cnpj = jQuery(this).val();
        var produto_siggo = jQuery('#cmtoid option:selected').data('siggo');

        if (tipo_pessoa == 'F') {
            valorFormatado = formatarCampo(cpf_cnpj, '000.000.000-00');
        } else if (tipo_pessoa == 'J') {
            valorFormatado = formatarCampo(cpf_cnpj, '00.000.000/0000-00');
        }

        jQuery('#cpf_cnpj').val(valorFormatado);

        //Autocompletar
        jQuery("#cpf_cnpj").autocomplete({

            source: "prn_modificacao_contrato.php?acao=recuperarCliente&tipo_pessoa=" + tipo_pessoa,
            minLength: 3,
            response: function(event, ui) {

                var conteudoInput = jQuery(this).val();

                jQuery('#msg_alerta_autocomplete').fadeOut(function() {
                    if (!ui.content.length) {
                        jQuery('#msg_alerta_autocomplete').html('Nenhum resultado encontrado com: ' + conteudoInput).fadeIn();
                    }
                });

                jQuery(this).autocomplete("option", {
                    messages: {
                        noResults: '',
                        results: function() {}
                    }
                });

            },
            select: function(event, ui) {

                event.preventDefault();

                jQuery("#cmfclioid_destino").val(ui.item.id);
                id_tipo_modificacao = jQuery('#cmtoid option:selected').val();


                if (id_tipo_modificacao == MIGRACAO_EX_SEGURADO || id_tipo_modificacao == MIGRACAO_REATIVACAO_EX_SEGURADO) {
                    analise_pagador = jQuery('#migrar_para option:selected').data('analise');
                    cliente_pagador = jQuery('#migrar_para option:selected').data('pagador');
                } else {
                    analise_pagador = '';
                    cliente_pagador = '';
                }

                if (tipo_pessoa == 'F') {
                    valor = formatarCampo(ui.item.value, '000.000.000-00');
                } else if (tipo_pessoa == 'J') {
                    valor = formatarCampo(ui.item.value, '00.000.000/0000-00');
                }

                jQuery('#cpf_cnpj').val(valor);

                clinome = jQuery('#clinome').val();
                cmfclioid_destino = jQuery('#cmfclioid_destino').val();

                //Exibe o link com o nome do cliente
                html = '<a href="cad_cliente.php?acao=principal&clioid=' + ui.item.id + '" target="_blank">' + ui.item.nome + '</a>';
                jQuery('#link_nome_cliente').next().remove();
                jQuery('#link_nome_cliente').after(html);

                jQuery('#clinome').val(ui.item.nome);


                //Selecionar a forma de cobranca padrao do cliente
                jQuery('#cmdfpforcoid option').each(function(id, opcao) {
                    if (opcao.value == ui.item.forma) {
                        jQuery(opcao).prop('selected', true);
                        return false;
                    }
                });

                //Selecionar o dia de vcto padrao do cliente
                jQuery('#cmdfpvencimento_fatura option').each(function(id, opcao) {

                    dia = jQuery(this).text().trim();

                    if (dia == ui.item.dia_vcto) {
                        jQuery(opcao).prop('selected', true);
                        return false;
                    }
                });

                jQuery('#cmdfpforcoid_aux').val(ui.item.forma);
                jQuery('#cmdfpvencimento_fatura_aux').val(jQuery('#cmdfpvencimento_fatura option:selected').val());

                //popular combo [Forma de Pagamento]
                popularCombos('cmdfpforcoid');

                if (jQuery('#cmdfpforcoid option:selected').val() != '') {
                    forma = jQuery('#cmdfpforcoid option:selected').data('forma');

                    jQuery('#forma_pgto').val(forma);

                    if (forma == 'debito') {

                        jQuery('#bloco_debito').show();
                        jQuery('#bloco_credito').hide();
                        jQuery('#bloco_pgto_cartao').hide();

                        jQuery('#cmdfpdebito_banoid option').each(function(id, opcao) {
                            if (opcao.value == ui.item.banco) {
                                jQuery(opcao).prop('selected', true);
                                return false;
                            }
                        });

                        jQuery('#cmdfpdebito_agencia').val(ui.item.agencia);
                        jQuery('#cmdfpdebito_cc').val(ui.item.conta);

                    } else if (forma == 'credito') {

                        if (produto_siggo != 't') {
                            jQuery('#bloco_credito').show();
                        } else {
                            jQuery('#bloco_pgto_cartao').show();
                        }

                        jQuery('#bloco_debito').hide();
                        jQuery('#cmdfpcartao').val(ui.item.cartao);

                    } else {
                        jQuery('#bloco_debito, #bloco_credito').hide();
                        jQuery('#bloco_pgto_cartao').hide();
                    }
                } else {
                    jQuery('#forma_pgto').val('');
                    jQuery('#bloco_pgto_cartao').hide();
                    jQuery('#bloco_debito').hide();
                    jQuery('#bloco_credito').hide();
                }

            }
        });

    });

    /*
     * Acao do botao [Confirmar]
     */
    jQuery('body').on('click', '#btn_confirmar_mofificacao', function(event) {

        event.preventDefault();

        if (validarStatusLinha()){
            validarCAN();
        }
    }); 

    /*
     * Acao do botao [Prosseguir]
     */
    jQuery('body').on('click', '#btn_prosseguir_anexos', function(event) {

        event.preventDefault();

        jQuery('#acao').val('cadastrarAnexo');
        jQuery('#sub_tela').val('aba_anexos');

        jQuery('#form_cadastro_modificacao').submit();

    });

    /*
     * Acao do botao [Prosseguir]
     */
    jQuery('body').on('click', '#btn_prosseguir_lote', function(event) {

        event.preventDefault();

        jQuery('#falso_link').removeClass('falso-link').addClass('invisivel');
        jQuery('#real_link').show();
        jQuery('#aba_itens').removeClass('blocked');

        jQuery('#acao').val('cadastrarModificacaoLote');
        jQuery('#sub_tela').val('aba_itens');

        jQuery('#form_cadastro_modificacao').submit();

    });

    /*
     * Acao do botao [Importar]
     */
    jQuery('body').on('click', '#btn_importar', function() {

        jQuery('#acao').val('popularMigracaoLote');
        jQuery('#form_arquivo').submit();
    });


    /*
     * Acao para Marcar Todos
     */
    jQuery('body').on('click', '#selecao_todos_lote, #selecao_todos_lote_updown, #selecao_todos_analise, #selecao_todos_desfazer, #selecao_todos_autorizar', function() {

        var marcado = jQuery(this).prop('checked');
        var elemento = '';

        if (this.id == 'selecao_todos_lote') {
            elemento = '#form_migracao_lote';
        } else if (this.id == 'selecao_todos_lote_updown') {
            elemento = '#form_updown_lote';
        } else if (this.id == 'selecao_todos_desfazer') {
            elemento = '#form_desfazer';
        } else if (this.id == 'selecao_todos_autorizar') {
            elemento = '#form_acessorio';
        } else {
            elemento = '#form_analise_credito';
        }

        jQuery(elemento + ' table tbody tr input:checkbox').each(function() {

            if (marcado) {
                jQuery(this).prop('checked', true);
            } else {
                jQuery(this).prop('checked', false);
            }

        });
    });

    /*
     * Verificar se deve habilitar o botão de acao da lista
     */
    jQuery('body').on('click', 'table tr input:checkbox', function() {

        var bloco = jQuery(this).data('bloco');
        var elemento = '';
        var botao = '';
        var totalMarcados = 0;
        var status = '';

        switch (bloco) {
            case 'lote':
                elemento = '#form_migracao_lote';
                botao = '#btn_confirmar_lote';
                break;
            case 'updown':
                elemento = '#form_updown_lote';
                botao = '#btn_confirmar_lote_updown';
                break;
            case 'analise':
                elemento = '#form_analise_credito';
                botao = '#btn_confirmar_analise';
                break;
            case 'desfazer':
                elemento = '#form_desfazer';
                botao = '#btn_desfazer';
                status = jQuery('#mdfstatus').val();
                break;
            case 'autorizar':
                elemento = '#form_acessorio';
                botao = '#btn_excluir_acessorio';
                break;
        }

        jQuery(elemento + ' table tbody tr input:checkbox').each(function() {

            if (jQuery(this).prop('checked')) {
                totalMarcados++;
            }
        });

        if (totalMarcados > 0) {
            jQuery(botao).removeProp('disabled');
            jQuery(botao).removeClass('desabilitado');

        } else {
            jQuery(botao).prop('disabled', true);
            jQuery(botao).addClass('desabilitado');
        }

    });

    /*
     * Acao do botao [Desfazer]
     */
    jQuery('body').on('click', '#btn_desfazer', function(event) {

        event.preventDefault();

        elemento = jQuery('#observacao_desfazer');

        elemento.removeClass('erro');
        jQuery('#mensagem_alerta').hide();

        if (jQuery.trim(elemento.val()) == '') {
            elemento.addClass('erro');
            jQuery('#mensagem_alerta').text(msg_campos_obrigatorios);
            jQuery('#mensagem_alerta').show();

        } else {

            jQuery("#msg_confirmar_desfazer").dialog({
                title: "Confirmar exclusão",
                resizable: false,
                modal: true,
                buttons: {
                    "Sim": function() {

                        jQuery(this).dialog("close");
                        jQuery('#acao').val('desfazerModificacao');
                        jQuery('#form_desfazer').submit();

                    },
                    "Não": function() {
                        jQuery(this).dialog("close");
                    }
                }
            });

        }
    });

    /*
     * Acao do botao [Excluir] - aba Acessorios
     */
    jQuery('body').on('click', '#btn_excluir_acessorio', function(event) {

        event.preventDefault();

        jQuery("#msg_confirmar_excluir_acessorio").dialog({
            title: "Confirmar exclusão",
            resizable: false,
            modal: true,
            buttons: {
                "Sim": function() {

                    jQuery(this).dialog("close");
                    jQuery('#form_acessorio').submit();

                },
                "Não": function() {
                    jQuery(this).dialog("close");
                }
            }
        });

    });

    /*
     * Acao do botao [Confirmar] da aba Itens, bloco Migracao em lote
     */
    jQuery('body').on('click', '#btn_confirmar_lote', function() {

        jQuery('#acao_lote').val('cadastrar');

        jQuery('#form_migracao_lote').submit();

    });

    /*
     * Acao do botao [Confirmar] da aba Itens, bloco Upgrade / Downgrade em Lote
     */
    jQuery('body').on('click', '#btn_confirmar_lote_updown', function() {

        jQuery('#acao_updown').val('cadastrar');

        jQuery('#form_updown_lote').submit();

    });

    /** Opcoes do quadro [Cancelar] - aba dados principais */
    jQuery('body').on('click', '#cancelar_sim, #cancelar_nao', function(event) {

        cancelar = jQuery('#cancelar_sim').prop('checked');

        if (cancelar === true) {
            jQuery('#campo_observacao_cancelar').show();
            jQuery('#btn_confirmar').removeClass('desabilitado');
        } else {
            jQuery('#campo_observacao_cancelar').hide();
            jQuery('#btn_confirmar').addClass('desabilitado');
        }
    });

    /** Opcoes do quadro [Cancelar] - aba dados principais */
    jQuery('body').on('click', '#autorizar_sim, #autorizar_nao', function(event) {

        autorizar = jQuery('#autorizar_sim').prop('checked');

        if (autorizar === true) {
            jQuery('#btn_confirmar').removeClass('desabilitado');
        } else {
            jQuery('#btn_confirmar').addClass('desabilitado');
        }
    });

    /*
     * Acao do botao [Confirmar] da aba Dados Principais
     */
    jQuery('body').on('click', '#btn_confirmar', function(event) {

        event.preventDefault();

        cancelar = jQuery('#cancelar_sim').prop('checked');
        autorizar = jQuery('#autorizar_sim').prop('checked');

        if (cancelar === true) {

            elemento = jQuery('#observacao_cancelar');
            jQuery('.mensagem').hide();
            elemento.removeClass('erro');

            if (jQuery.trim(elemento.val()) == '') {
                elemento.addClass('erro');
                jQuery('#mensagem_alerta').text(msg_campos_obrigatorios);
                jQuery('#mensagem_alerta').show();

            } else {

                jQuery("#msg_confirmar_cancelar_modificacao").dialog({
                    title: "Confirmar",
                    resizable: false,
                    modal: true,
                    buttons: {
                        "Sim": function() {

                            jQuery(this).dialog("close");
                            jQuery('#acao').val('cancelarModificacao');
                            jQuery('#form_cancelar').submit();

                        },
                        "Não": function() {
                            jQuery(this).dialog("close");
                        }
                    }
                });
            }
        } else if (autorizar === true) {

            jQuery('#autorizar').val('t');
            jQuery('#form_autorizar').submit();

        } else if (autorizar === false) {

            jQuery("#msg_confirmar_nao_autorizar").dialog({
                title: "Confirmar",
                resizable: false,
                modal: true,
                buttons: {
                    "Sim": function() {

                        jQuery(this).dialog("close");
                        jQuery('#acao').val('cancelarModificacao');
                        jQuery('#form_cancelar').submit();

                    },
                    "Não": function() {
                        jQuery(this).dialog("close");
                    }
                }
            });

        }
    });


    //Acao do checkbox [Pagar com Cartao de Credito]
    jQuery('#cmdfppagar_cartao').click(function() {

        jQuery('#msg_alerta_bloco_faturamento').hide();
        jQuery('#cmdfpvlr_taxa_negociado').removeClass('erro');

        taxaZero = false;

        if (jQuery(this).prop('checked')) {

            taxa = jQuery('#cmdfpvlr_taxa_negociado').val();

            if (taxa == '') {
                taxaZero = true;
            } else {
                taxa = taxa.replace(',', '');
                taxa = taxa.replace('.', '');
                taxa = parseInt(taxa);
                if ((taxa == 0)) {
                    taxaZero = true;
                }
            }

            if (taxaZero) {
                jQuery('#msg_alerta_bloco_faturamento').show();
                jQuery('#msg_alerta_bloco_faturamento').html(msg_alerta_taxa_zero);
                jQuery('#cmdfpvlr_taxa_negociado').addClass('erro');

            } else {
                jQuery('#bloco_credito').show();
            }

            jQuery(this).val('t');

        } else {
            jQuery('#bloco_credito').hide();
            jQuery(this).val('f');
        }
    });

    /*
     * botão Anexar
     */
    jQuery("#btn_anexar").click(function(event) {

        jQuery('#bloco_anexo input').removeClass('erro');
        jQuery('#mensagem_alerta').hide();
        totalCamposNaoPreenchidos = 0;

        arquivo = jQuery('#arquivo_anexo').val();
        local = jQuery('#local_instalacao').val();

        if (arquivo == '') {
            jQuery('#arquivo_anexo').addClass('erro');
            totalCamposNaoPreenchidos++;
        }

        if (local == '') {
            jQuery('#local_instalacao').addClass('erro');
            totalCamposNaoPreenchidos++;
        }

        if (totalCamposNaoPreenchidos > 0) {
            jQuery('#mensagem_alerta').text(msg_campos_obrigatorios);
            jQuery('#mensagem_alerta').show();
        }

        if (totalCamposNaoPreenchidos == 0) {
            jQuery('#form_arquivo').submit();
        }

    });

    /*
     * Acoes do icone [excluir] do bloco de anexos
     */
    jQuery("#lista_anexos table").on('click', '.excluir', function() {

        elemento = this;
        linhasAtivas = 0;
        nomeArquivo = this.id;

        jQuery('#mensagem_erro').hide();

        jQuery("#msg_confirmar_excluir_anexo").dialog({
            title: "Confirmação de Exclusão",
            resizable: false,
            modal: true,
            buttons: {
                "Sim": function() {
                    jQuery(this).dialog("close");

                    jQuery.ajax({
                        url: 'prn_modificacao_contrato.php',
                        type: 'POST',
                        data: {
                            acao: 'removerArquivoTemporarioAjax',
                            arquivo: nomeArquivo
                        },
                        success: function(data) {

                            if (data == 'OK') {

                                jQuery(elemento).parent().parent().remove();
                                aplicarCorLinha('lista_anexos');

                                //Verifica se foi o ultima linha removida. Se sim esconde o bloco de listagem
                                jQuery('#lista_anexos table tbody tr').each(function() {
                                    linhasAtivas++;
                                });

                                if (linhasAtivas == 0) {
                                    jQuery('#lista_anexos').hide();
                                }
                            } else {
                                jQuery('#mensagem_erro').show();
                                jQuery('#mensagem_erro').text(msg_erro_arquivo);
                            }
                        }
                    });

                },
                "Não": function() {
                    jQuery(this).dialog("close");
                }
            }
        });
    });

    //================================ Aba Cadastro Modificacao: FIM ==================================//


    //========================== Aba Detalhes da Modificacao: INICIO ================================//

    /*
     * Acao do botao [Gerar Contrato(s)]
     */
    jQuery('#btn_gerar_contrato').click(function(event) {

        event.preventDefault();

        elemento = jQuery('#cartao_codigo');

        isPagarCartao = jQuery('#cmdfppagar_cartao').val();

        elemento.removeClass('erro');
        jQuery('#mensagem_alerta').hide();

        if ((elemento.val() == '') && (isPagarCartao == 't')) {
            elemento.addClass('erro');
            jQuery('#mensagem_alerta').text(msg_campos_obrigatorios);
            jQuery('#mensagem_alerta').show();
        } else {
            jQuery('#form_gerar_contratos').submit();
        }

    });

    /*
     * Acao do botao [Pesquisar] Aba Acessorios
     */
    jQuery('#btn_pesquisar_acessorios').click(function(event) {

        event.preventDefault();

        jQuery('#form_pesquisa_acessorio').submit();


    });


    //========================== Aba Detalhes da Modificacao: FIM ===================================//


    //=========================== Aba Analise Credito: INICIO =======================================//

    jQuery("#btn_confirmar_analise").click(function() {
        jQuery('#form_analise_credito').submit();
    });

    jQuery("#combo_status").change(function() {

        if (jQuery('#combo_status').val() == 'N') {

            jQuery("#campo_liberacao").prop('disabled', true);
            jQuery("#check_periodo").prop('disabled', true);

            jQuery("#campo_liberacao").val('');
            jQuery("#check_periodo").prop('checked', false);

        } else {
            jQuery("#campo_liberacao").prop('disabled', false);
            jQuery("#check_periodo").prop('disabled', false);
        }

    });

    jQuery("#check_periodo").click(function() {
        jQuery("#campo_liberacao").val('');
    });

    jQuery("#campo_liberacao").blur(function() {
        jQuery("#check_periodo").prop('checked', false);
    });


    //=========================== Aba Analise Credito: FIM ==========================================//
});


// ========================================== FUNCOES ============================================== //

/*
 * valida campos indicados como obrigatorios, nao preenchidos.
 */
function validarCamposObrigatorios() {

    var erros = 0;

    jQuery('form .obrigatorio').each(function(id, valor) {

        elemento = jQuery('#' + valor.id);

        if (jQuery.trim(elemento.val()) == '') {

            elemento.addClass('erro');
            erros++;
        }
    });

    if (erros > 0) {
        jQuery('#mensagem_alerta').html(msg_campos_obrigatorios);
        jQuery('#mensagem_alerta').show();
        return false;
    } else {
        return true;
    }
}

/*
 * Função para inserir máscara e validar digitação para CNPJ e CPF
 * Obs: a função do [mascaras.js] é bugada!
 * formato mascara: exemplo:
 */
function formatarCampo(idSeletor, mascara) {

    var valor = jQuery(idSeletor).val();
    valor = valor.replace(/[^0-9]/g, "");
    var tamanhoMaximo = mascara.length;
    var tamanhoMascara = valor.length;
    var boleanoMascara;
    var novoValorCampo = "";
    var posicaoCampo = 0;

    for (i = 0; i <= tamanhoMascara; i++) {
        boleanoMascara = ((mascara.charAt(i) == "-") || (mascara.charAt(i) == ".") || (mascara.charAt(i) == "/"));
        if (boleanoMascara) {
            novoValorCampo += mascara.charAt(i);
            tamanhoMascara++;
        } else {
            novoValorCampo += valor.charAt(posicaoCampo);
            posicaoCampo++;
        }
    }

    jQuery(idSeletor).val(novoValorCampo.slice(0, tamanhoMaximo));
}

/*
 * Funcao generica para popular combos
 */
function popularCombos(elemento) {

    //ID do item selecionado
    var id = '';
    //String que define qual metodo chamar no PHP
    var comboPopular = '';
    //ID do elemento referente a combo que sera populada
    var idComboPopular = '';
    //ID do elemento que sera selecionado no reload pós pesquisa / inclusao / edicao
    var idRequest = '';

    //Verifica qual combo foi acionada e parametriza as variaveis
    switch (elemento) {
        case 'depoid':
            id = jQuery('#depoid option:selected').val();
            comboPopular = 'usuario';
            idComboPopular = '#mdfusuoid_cadastro';
            idRequest = jQuery('#usuoid_recarga_tela').val();
            jQuery('#img_usuario').removeClass('invisivel');
            break;
        case 'cmgoid':
            id = jQuery('#cmgoid option:selected').val();
            comboPopular = 'tipo_modificacao';
            idComboPopular = '#cmtoid';
            idRequest = jQuery('#cmtoid_recarga_tela').val();
            jQuery('#img_cmtoid').removeClass('invisivel');
            break;
        case 'cmdfpforcoid':
            id = jQuery('#cmdfpforcoid option:selected').val();
            comboPopular = 'banco';
            idComboPopular = '#cmdfpdebito_banoid';
            idRequest = jQuery('#cmdfpdebito_banoid_recarga_tela').val();
            jQuery('#img_cmdfpdebito_banoid').removeClass('invisivel');
            break;
        case 'cmftppoid':
            id = jQuery('#cmftppoid option:selected').val();
            comboPopular = 'sub_tipo_proposta';
            idComboPopular = '#cmftppoid_subtitpo';
            idRequest = jQuery('#cmftppoid_subtitpo_recarga_tela').val();
            jQuery('#img_cmftppoid_subtitpo').removeClass('invisivel');
            break;
        case 'migracao_ex':
            id = '0';
            comboPopular = elemento;
            idComboPopular = '#migrar_para';
            idRequest = jQuery('#migrar_para_recarga_tela').val();
            jQuery('#img_migrar_para').removeClass('invisivel');
            break;
        case 'migracao_ex_reativacao':
            id = '0';
            comboPopular = elemento;
            idComboPopular = '#migrar_para';
            idRequest = jQuery('#migrar_para_recarga_tela').val();
            jQuery('#img_migrar_para').removeClass('invisivel');
            break;
        case 'cpvoid':
            id = jQuery('#cmdfpcpvoid option:selected').val();
            comboPopular = 'acessorios';
            idComboPopular = '#cmsobroid';
            idRequest = jQuery('#cmsobroid_recarga_tela').val();
            jQuery('#img_cmsobroid').removeClass('invisivel');
            break;
        case 'demonstracao':
            id = '0';
            comboPopular = 'demonstracao';
            idComboPopular = '#cmftpcoid_destino';
            idRequest = '';
            break;
        case 'nao_demonstracao':
            id = '0';
            comboPopular = 'nao_demonstracao';
            idComboPopular = '#cmftpcoid_destino';
            idRequest = '';
            break;
    }



    if (id != '') {

        jQuery.ajax({
            url: 'prn_modificacao_contrato.php',
            type: 'POST',
            data: {
                acao: 'popularComboAjax',
                oid: id,
                combo: comboPopular
            },
            async: false,
            success: function(data) {

                var combo = '';

                if ((elemento == 'migracao_ex') || (elemento == 'migracao_ex_reativacao')) {
                    combo += "<option value='' data-pagador='' data-analise='' data-forma=''>Escolha</option>";
                } else {
                    combo += "<option value=''>Escolha</option>";
                }

                if (data.length > 0) {

                    data = JSON.parse(data);

                    if (Object.keys(data).length > 0) {

                        jQuery.each(data, function(i, val) {

                            selecionado = (idRequest == val.id) ? ' selected="true"' : '';

                            if ((elemento == 'migracao_ex') || (elemento == 'migracao_ex_reativacao')) {
                                combo += '<option value="' + val.id + '"' + selecionado + ' data-pagador="' + val.cliente_pagador + '"';
                                combo += ' data-analise="' + val.analise_credito + '" data-forma="' + val.forma_pgto + '">';
                                combo += val.descricao + '</option>';
                            } else if (elemento == 'cpvoid') {
                                combo += '<option value="' + val.id + '"' + selecionado;
                                combo += ' data-valor="' + val.valor + '">';
                                combo += val.descricao + '</option>';
                            } else {
                                combo += '<option value="' + val.id + '"' + selecionado + '>' + val.descricao + '</option>';
                            }
                        });
                    }

                }

                jQuery(idComboPopular).html(combo);
            }
        });

    } else {

        combo = "<option value=''>Escolha</option>";
        jQuery(idComboPopular).html(combo);

    }

    jQuery('.carregando').addClass('invisivel');
}


/*
 * Reorganzia as cores das linhas na lista
 */
function aplicarCorLinha(idLista) {

    var cor = '';

    //remove cores
    jQuery('#' + idLista + ' table tbody tr').removeClass('par');
    jQuery('#' + idLista + ' table tbody tr').removeClass('impar');


    //aplica cores
    jQuery('#' + idLista + ' table tbody tr').each(function() {
        cor = (cor == "par") ? "impar" : "par";
        jQuery(this).addClass(cor);
    });
}

/**
 * Formatar moeda entre brasileiro / americano
 *
 * formato | [A] = americano para brasileiro, [B] = brasileiro para americano
 */
function tratarMoeda(valor, formato) {

    if (!valor) {
        valor = '0';
    }

    valor = valor.toString();

    if (formato == 'A') {
        valor = valor.replace(',', '');

        if (valor.indexOf('.') == -1) {
            valor = valor + ',' + '00';
        } else {
            casas = valor.split('.');
            valor = casas[0] + ',' + ((casas[1].length == 1) ? (casas[1] + '0') : casas[1]);
        }

    } else {
        valor = valor.replace(',', '.');
    }

    return valor;
}

/*
 * Seta o valor default no campo [valor taxa]
 */
function setarValorTaxa() {

    valor = jQuery('#cmdfpobroid_taxa option:selected').data('valor');

    valor = tratarMoeda(valor, 'A');

    if (!jQuery('#cmdfpisencao_taxa').prop('checked')) {
        jQuery('#cmdfpvlr_taxa_negociado').val(valor);
    }

    jQuery('#cmdfpvlr_taxa_tabela').val(valor);
}

/*
 * Recupera o valor de monitoramento conforme a classe de contrato
 *
 */
function setValorMonitoramento() {

    contrato = jQuery('#cmfconnumero').val();
    classe_contrato = jQuery('#cmfeqcoid_destino option:selected').val();

    //Informacoes de monitoramento
    jQuery.ajax({
        url: 'prn_modificacao_contrato.php',
        type: 'POST',
        data: {
            acao: 'recuperarDadosMonitoramento',
            cmfconnumero: contrato,
            eqcoid: classe_contrato
        },
        success: function(data) {

            if (data.length > 0) {

                data = JSON.parse(data);

                if (Object.keys(data).length > 0) {

                    valor = tratarMoeda(data.eqcvlr_mens, 'A');

                    jQuery('#cmdfpvlr_monitoramento_negociado').val(valor);
                    jQuery('#cmdfpvlr_monitoramento_tabela').val(data.eqcvlr_mens);
                    jQuery('#eqcvlr_minimo_mens').val(data.eqcvlr_minimo_mens);
                    jQuery('#eqcvlr_maximo_mens').val(data.eqcvlr_maximo_mens);

                }
            } else {
                jQuery('#cmdfpvlr_monitoramento_negociado').val('0,00');
                jQuery('#cmdfpvlr_monitoramento_tabela').val('0.00');
                jQuery('#eqcvlr_minimo_mens').val('0.00');
                jQuery('#eqcvlr_maximo_mens').val('0.00');
            }

        }
    });

}

/*
 * Aplica mascara de telefone
 * Adaptacao da funcao presente em 'lib/funcoes_masc.js'
 * acrescentando o ZERO no DDD
 */

function formatarTelefone(element) {

    var valor = element.value;
    var ddd = '';
    var prefixo = '';
    var is_cel_fone = '';
    var arrayDDD = new Array();
    var checkDDD = false;

    /*
    ***************************************************************************************************
    ANATEL - Nono dígito passou a ser utilizado em CELULARES de TODO país a partir de DEZEMBRO DE 2016.
    ***************************************************************************************************
    //DDD's que devem utilizar o nono digito

    // SP
    arrayDDD[0] = '011';
    arrayDDD[1] = '012';
    arrayDDD[2] = '013';
    arrayDDD[3] = '014';
    arrayDDD[4] = '015';
    arrayDDD[5] = '016';
    arrayDDD[6] = '017';
    arrayDDD[7] = '018';
    arrayDDD[8] = '019';

    // RJ
    arrayDDD[9] = '021';
    arrayDDD[10] = '022';
    arrayDDD[11] = '024';

    // ES
    arrayDDD[12] = '027';
    arrayDDD[13] = '028';
    */

    //Remove tudo o que não é dígito
    valor = valor.replace(/[^0-9]/g, "");

    //Coloca parênteses em volta dos tres primeiros dígitos e espaço após)
    valor = valor.replace(/^(\d\d\d)(\d)/g, "($1) $2");

    //Resgatando o DDD considerando a máscara
    ddd = valor.charAt(1) + valor.charAt(2) + valor.charAt(3);

    /*
    ***************************************************************************************************
    ANATEL - Nono dígito passou a ser utilizado em CELULARES de TODO país a partir de DEZEMBRO DE 2016.
    ***************************************************************************************************
    if (ddd != '') {
        checkDDD = in_array(ddd, arrayDDD);
    }
    */
    checkDDD = true;

    /*
     * Precisamos do prefixo para colocar 5 ou 4 dígitos antes do hífen,
     * considerando as regras abaixo:
     *
     * REGRA 1:
     *     Quando o DDD for de SP (11) e os prefixos forem 6, 8 ou 9 (Celular)
     *     colocaremos 5 dígitos antes do hífen.
     *
     * REGRA 2:
     *     Quando o DDD for de SP e o prefixo for 5 poderemos ter 4 ou 5 dígitos
     *  antes do hífen.
     *
     * Nota:
     *     Prefixo é o primeiro dígito do número do telefone após o DDD
     * */
    prefixo = valor.charAt(6);

    is_cel_fone = (prefixo == '6' || prefixo == '8' || prefixo == '9') ? true : false;

    if (checkDDD && (is_cel_fone || prefixo == '5')) {
        element.setAttribute('maxlength', 16);
    } else {
        element.setAttribute('maxlength', 15);
    }

    if ((checkDDD && is_cel_fone) || (checkDDD && prefixo == '5' && valor.length == 15)) {
        valor = valor.replace(/(\d{5})(\d{4})/, "$1-$2");
        valor = (valor.length > 16) ? valor.substring(0, 16) : valor;
    } else {
        valor = valor.replace(/(\d{4})(\d{4})/, "$1-$2");
        valor = (valor.length > 15) ? valor.substring(0, 15) : valor;
    }

    return valor;
}

/**
 * @param String ddd
 * @param Array arrayDDD
 * @returns Boolean
 */
function in_array(ddd, arrayDDD) {
    for (var i in arrayDDD) {
        if (arrayDDD[i] == ddd) {
            return true;
        }
    }
    return false;
}

/*
 * Funcao p/ validar compatibilidade entre as classes de 
 * equipamento (Telemetria CAN) e o Modelo/ Ano do veículo
 */
function validarCAN() {
    contrato 		= jQuery('#cmfconnumero').val();
    classe_contrato = jQuery('#cmfeqcoid_destino option:selected').val();
    placa_veinovo 	= jQuery('#veiplaca_novo').val();
    executivo       = jQuery('#cmffunoid_executivo').val();
	
	if (
	(contrato === undefined || contrato == null || contrato.length <= 0) || 
	(classe_contrato === undefined || classe_contrato == null || classe_contrato.length <= 0) || 
	(placa_veinovo === undefined || placa_veinovo == null || placa_veinovo.length <= 0) 
	) {
		jQuery('#acao').val('cadastrar');
		jQuery('form').submit();
	}
	else {
    
		jQuery.ajax({
			url: 'prn_modificacao_contrato.php',
			type: 'POST',
			async: false,
			data: {
				acao: 'validaCompatibilidadeCAN',
				cmfconnumero: contrato,
				eqcoid: classe_contrato,
				veiplaca_novo: placa_veinovo,
                executivo: executivo
			},
			success: function(data) {
				// val === undefined || val == null || val.length <= 0
				if (data == 'null' || data === undefined || data == null || data.length <= 0) {
					jQuery('#acao').val('cadastrar');
					jQuery('form').submit();
				}
				else {
					data = JSON.parse(data);
					
					if (data && data.erro != undefined) {
						
						jQuery('#mensagem_alerta').text(data.erro);
						jQuery('#mensagem_alerta').show();
						jQuery('html, body').animate({
							scrollTop: $(".modulo_titulo").offset().top
						}, 0);
						
					}
					else {

						for (var i=0; i<Object.keys(data).length; i++) {
							
							if (Object.keys(data[i]).length > 0) {
			
								var modelo 	= data[i].mlomodelo;
								var ano 	= data[i].cavano;
								var classe 	= data[i].eqcdescricao;
								var msg_compatibilidade_CAN = null;
								
								if (data[i].cavstatus == 't'){
									
									jQuery('#acao').val('cadastrar');
									jQuery('form').submit();
									msg_compatibilidade_CAN = null;
									break;
									
								}
								else if (data[i].cavstatus == 'f'){
									
									var str1 = "O veículo, modelo ";
									var str2 = ", ano ";
									var str3 = ", não é compatível com a classe ";
									var str4 = ".";
									// O veículo, modelo data.mlomodelo, ano data.cavano, não é compatível com a classe data.eqcdescricao.
									msg_compatibilidade_CAN = str1.concat(modelo,str2,ano,str3,classe,str4);
									
								}
								else if (data[i].cavstatus == null){
									
									var str1 = "A compatibilidade entre o veículo, modelo ";
									var str2 = ", ano ";
									var str3 = " e a classe ";
									var str4 = " ainda não foi homologada.";
									// A compatibilidade entre o veículo, modelo data.mlomodelo, ano data.cavano e a classe data.eqcdescricao ainda não foi homologada.
									msg_compatibilidade_CAN = str1.concat(modelo,str2,ano,str3,classe,str4);
									
								}
			
							}
							
						}
						if (msg_compatibilidade_CAN != null) {

							jQuery('#mensagem_alerta').text(msg_compatibilidade_CAN);
							jQuery('#mensagem_alerta').show();
							jQuery('html, body').animate({
								scrollTop: $(".modulo_titulo").offset().top
							}, 0);
							
						}                	
						
					} 
					
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){									
				jQuery('#acao').val('cadastrar');
				jQuery('form').submit();
			}
		});
	}
}


/**
 * Função para validar o status da linha
 */
function validarStatusLinha()
{
    contrato    = jQuery('#cmfconnumero').val();
    cmtoid      = jQuery('#cmtoid').val();
    retorno     = "";
    
    jQuery.ajax({
        url: 'prn_modificacao_contrato.php',
        type: 'POST',
        async: false,
        data: {
            acao: 'validaStatusLinha',
            connumero: contrato,
            cmtoid: cmtoid,
            ajax: true
        },
        success: function(data) {
            
            if (data.length > 0) {
                
                data = JSON.parse(data);

                if (data && data.erro != undefined) {
                    
                    jQuery('#mensagem_alerta').html(data.erro);
                    jQuery('#mensagem_alerta').show();
                    jQuery('html, body').animate({
                        scrollTop: $(".modulo_titulo").offset().top
                    }, 0);

                    retorno = false;
                }
                else {
                    retorno = true;
                }

            }else{
                retorno = true;
            }
        }
    });

    return retorno;

}