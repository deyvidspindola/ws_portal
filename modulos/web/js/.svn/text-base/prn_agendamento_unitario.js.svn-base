/**
 * Sascar - Tecnologia e Seguranca Automotiva S/A
 *
 * O conteúdo deste arquivo não pode ser utilizado por nenhum
 * propósito sem a permissão prévia da Sascar.
 *
 * A classe AgendamentoUnitario é responsável pela operação na tela de busca e
 * gerenciamento de agendamento incluindo todas as iterações da tela.
 *
 * @package  SmartAgenda
 * @author   Adenilson Santos <adenilson.santos.ext@sascar.com.br>
 * @link     http://www.sascar.com.br
 */
jQuery(document).ready(function(){

    jQuery('body').on('keyup blur', '#cmp_placa', function() {
        jQuery(this).val(jQuery(this).val().replace(/[^A-Za-z0-9\-]/g, '').toUpperCase());

    });

   var agendamentoUnitario = new AgendamentoUnitario();
    agendamentoUnitario.init();
});
(function(pkg, $){

    if(!$){
        throw new Error('A classe AgendamentoUnitario requer jQuery 1.7.1');
        return;
    }
    pkg = pkg || {};

    pkg.AgendamentoUnitario = function() {
        this.boxCancelarAgendamento;
        this.boxCancelarAgendamentoConfirma;
        this.btoCancelarAgendamento;
        this.btoNovaOS;
        this.btoLimpar;
        this.formCancelarAgendamento;
        this.formPesquisa;
        this.formNovaOS;
        this.cmpCliente;
        this.cmpClienteAutoComplete;
        this.cmpAgendamentoAberto;
        this.cmpCEP;
        this.cmpEstado;
        this.cmpCidade;
        this.cmpModalMotivo;
        this.cmpModalObservacao;
        this.modalCancelarAgendamento;
        this.modalCancelarAgendamentoConfirma;
        this.mensagemSucesso;
        this.mensagemAlerta;
        this.mensagemErro;
        this.alertaModal;
        this.carregando
        this.carregarCidade;
        this.msgRetorno;
        this.info;
        this.paramCancela;

        this.init = function() {
            this.boxCancelarAgendamento = $("#cancelar-agendamento");
            this.boxCancelarAgendamentoConfirma = $("#cancelar-agendamento-confirma");
            this.btoCancelarAgendamento = $(".cancelar");
            this.btoNovaOS = $("#bto-nova-os");
            this.btoLimpar = $("#bto-limpar-pesquisa");
            this.formPesquisa = $("#form-pesquisar");
            this.formNovaOS = $("#form-nova-os");
            this.formCancelarAgendamento = this.boxCancelarAgendamento.find('form');
            this.cmpCliente = $('#cmp_cliente');
            this.cmpClienteAutoComplete = $('#cmp_cliente_autocomplete');
            this.cmpAgendamentoAberto = $('#cmp_agendamento_aberto');
            this.cmpPlaca = $('#cmp_placa');
            this.cmpCEP = $('#cpm_cep');
            this.cmpEstado = $('#cmp_uf');
            this.cmpCidade = $('#cmp_cidade');
            this.cmpModalMotivo = $('#cmp_motivo');
            this.cmpModalObservacao = $('#cmp_observacao');
            this.mensagemSucesso = $('#mensagem_sucesso');
            this.mensagemAlerta = $('#mensagem_alerta');
            this.mensagemErro = $('#mensagem_erro');
            this.alertaModal = $('#alerta-modal');
            this.carregando = $('#loading');
            this.carregarCidade = $('#carregar-cidade');
            this.msgRetorno = $('#msgRetorno');
            this.info = $('#info');
            this.paramCancela = false;

            this.configurar();
            this.configurarModal();
            this.configurarModalConfirma();
            this.configurarAutoCompletarCliente();

            this.carregarCidades(this.cmpEstado);
            this.verificarCampos(this.cmpAgendamentoAberto);
        };

        this.configurar = function() {
            var _this = this;

            // Faz o bind para o evento submit quando pressionado o enter
            this.formCancelarAgendamento.on('submit', function(event) {
                event.preventDefault();
                _this.verificarCancelarAgendamento();
            });

            this.formPesquisa.on('submit', function(event) {
                event.preventDefault();
                _this.validarFormularioPesquisa();
            });

            // Faz o bind para o modal de cancelamento de agendamento
            this.btoCancelarAgendamento.click(function() {
                // Pega as informações do elemento atual
                var data = eval("(" + $.trim($(this).attr('data')) + ")");

                //define a variável do código do agendamento da OS
                $(document).data('ordoid', data.ordoid);
                $(document).data('osaoid', data.osaoid);
                _this.msgRetorno.hide();
                _this.modalCancelarAgendamento.dialog("open");
            });

            // Definindo mascaras e definições de campo
            $('.numerico').numeric({negative: false, decimal: false});
            this.cmpCEP.mask("99999-999");

            // Verifica se a opinião de agendamento aberto foi selecionada
            this.cmpAgendamentoAberto.on('change', function() {
                _this.verificarCampos(this);
            });

            this.cmpEstado.on('change', function() {
                _this.carregarCidades(this);
            });

            this.btoNovaOS.click(function() {
                try {
                    window.open('about:blank', 'agendamentoUnitarioNovaOS');
                    _this.formNovaOS.attr('target', 'agendamentoUnitarioNovaOS').submit();
                 } catch(e) {}
            });

            this.btoLimpar.click(function() {
                var acao = $('#acao').val();
                _this.formPesquisa.find('input').val('');
                _this.formPesquisa.find('select').val(0);
                _this.formPesquisa.find('input[type=checkbox]').prop('checked', false);
                _this.verificarCampos(_this.cmpAgendamentoAberto);
                $('#acao').val(acao);
            });
        };

        this.configurarAutoCompletarCliente = function() {
            var _this = this;
            this.cmpClienteAutoComplete.autocomplete({
                source: 'prn_agendamento_unitario.php?acao=buscarClientes',
                minLength: 3,
                response: function(event, ui) {
                    if (!ui.content.length) {
                        _this.mostrarMensagemAlerta('Nenhum cliente encontrado com o termo: ' + $(this).val());
                    } else {
                        _this.esconderMensagemAlerta();
                    }
                },
                select: function(event, ui ) {
                    _this.cmpCliente.val(ui.item.id);
                }
            });
        };

        this.validarFormularioPesquisa = function() {
            var valido = false;
            var camposDestaque = new Array();
            this.esconderMensagemAlerta();

            //valida campo cliente
            if(!$(this.cmpClienteAutoComplete).val().trim()){
                $(this.cmpCliente).val('');
            }

            this.formPesquisa.find('.validar').each(function() {

                var valor = $(this).val().trim();

                if ((valor == 0) && $(this).prop('nodeName') == 'SELECT') {
                    valor = null;
                }

                if($(this).attr('name') == 'cmp_agendamento_aberto') {

                    if(!$(this).is(':checked') ) {
                        valor = null;
                    }
                }

                if (valor) {
                    valido = true;
                    return false;
                }else{
                    //adiciona campo para destaque
                    camposDestaque.push({campo:$(this).attr("id")});
                }
            });

            if (valido) {
                this.formPesquisa.get(0).submit();
            } else {
                //destaca campos
                showFormErros(camposDestaque);

                this.mostrarMensagemAlerta('Existem campos obrigatórios não preenchidos.');
            }
        };


        this.validarFormularioCancelar = function() {

            var _this = this;

            var resultado = true;
            var camposDestaque = new Array();

            if (this.cmpModalMotivo.val() <= 0) {
                resultado = false;
                //adiciona campo para destaque
                camposDestaque.push({campo:$(this.cmpModalMotivo).attr("id")});
            }

            if (!this.cmpModalObservacao.val().trim().length) {
                resultado = false;
                //adiciona campo para destaque
                camposDestaque.push({campo:$(this.cmpModalObservacao).attr("id")});
            }

            if (!resultado) {

                //destaca campos
                showFormErros(camposDestaque);

                //mostra mensagem
                _this.alertaModal.removeClass('invisivel');

                return false;
            }else{

                //esconde mensagem
                _this.alertaModal.addClass('invisivel');

                return true;

            }

        };

        this.verificarCancelarAgendamento = function() {

            var _this = this;
            var avanca = false;

            if (this.paramCancela) {
                avanca = true;
            }else{
                avanca = _this.validarFormularioCancelar();
            }

            if(avanca){

                $.ajax({
                    type: "POST",
                    url: 'prn_agendamento_unitario.php?acao=cancelarAgendamentoAjax',
                    data: {
                        ordoid : $(document).data('ordoid'),
                        osaoid : $(document).data('osaoid'),
                        motivo : this.cmpModalMotivo.val(),
                        obs    : this.cmpModalObservacao.val(),
                        param  : this.paramCancela
                        },
                    dataType: 'JSON',
                    beforeSend: function(){
                        $(".ui-dialog-buttonset").hide();
                        _this.carregando.show();
                    },
                    success: function(json) {

                        if(json.codigo == 1){
                            _this.modalCancelarAgendamento.dialog( "close" );
                            $("#result_" + $(document).data('ordoid')).remove();
                            _this.aplicarCorLinha();
                            _this.mostrarMensagemSucesso(json.msg);
                            location.href = 'prn_agendamento_unitario.php';

                        }else if(json.codigo == 3){

                            _this.carregando.hide();
                            $(".ui-dialog-buttonset").show();

                            $('#num-os-modal').text(json.os_relacionada);
                            jQuery('#tipo-os-1').text(json.tipo_os_1);
                            jQuery('#tipo-os-2').text(json.tipo_os_2);

                            _this.modalCancelarAgendamentoConfirma.dialog( "open" );
                        }else{
                            _this.modalCancelarAgendamento.dialog( "close" );
                            _this.mostrarMensagemErro("Houve um erro no processamento dos dados.");
                        }
                    },
                    error: function() {
                        _this.modalCancelarAgendamento.dialog( "close" );
                        _this.mostrarMensagemErro("Houve um erro no processamento dos dados.");
                    }
                });
            }


        };

        this.configurarModal = function() {
            var _this = this;

            this.modalCancelarAgendamento = this.boxCancelarAgendamento.dialog({
                autoOpen: false,
                closeOnEscape: false,
                maxHeight: 380,
                width: 420,
                resizable: false,
                modal: true,
                buttons: {
                    "Cancelar Agendamento": function() {
                        _this.verificarCancelarAgendamento();
                    },
                    "Voltar": function() {
                        _this.modalCancelarAgendamento.dialog("close");
                    }
                },
                close: function() {
                    _this.redefinirFormCancelarAgendamento();
                }
            });
        };


        this.configurarModalConfirma = function() {

        	var _this = this;

            this.modalCancelarAgendamentoConfirma = this.boxCancelarAgendamentoConfirma.dialog({
                autoOpen: false,
                closeText: "hide",
                closeOnEscape: false,
                width: 420,
                resizable: false,
                modal: true,
                buttons: {
                    "Sim": function() {
                    	_this.paramCancela = true;
                    	_this.verificarCancelarAgendamento();
                        $( this ).dialog( "close" );
                    },
                    "Não": function() {
                        $( this ).dialog( "close" );
                    }
                },
                close: function() {
                    _this.redefinirFormCancelarAgendamento();
                }
            });
        };


        /**
         * Mensagens
         */

        this.mostrarMensagemAlerta = function(mensagem) {
            this.mensagemAlerta
                .text(mensagem)
                .removeClass('invisivel').fadeIn();
        };
        this.esconderMensagemAlerta = function() {
            this.mensagemAlerta.fadeOut();
        };

        this.mostrarMensagemSucesso = function(mensagem) {
            this.mensagemSucesso
                .text(mensagem)
                .removeClass('invisivel').fadeIn();
        };
        this.esconderMensagemSucesso = function() {
            this.mensagemSucesso.fadeOut();
        };

        this.mostrarMensagemErro = function(mensagem) {
            this.mensagemErro
                .text(mensagem)
                .removeClass('invisivel').fadeIn();
        };
        this.esconderMensagemErro = function() {
            this.mensagemErro.fadeOut();
        };



        this.redefinirFormCancelarAgendamento = function() {
            $('#alerta-modal').addClass('invisivel');
            resetFormErros();
            this.formCancelarAgendamento.get(0).reset();
        };

        this.verificarCampos = function(elemento) {
            var disable = $(elemento).is(':checked');

            this.cmpCEP.prop('disabled', disable);
            this.cmpEstado.prop('disabled', disable);
            this.cmpCidade.prop('disabled', disable);
        };

        this.carregarCidades = function(elemento) {
            var _this = this;
            if ($(elemento).val() > 0) {
                this.carregarCidade.show();
                $.ajax({
                    type: "POST",
                    url: 'prn_agendamento_unitario.php?acao=buscarCidades',
                    data: {idEstado: $(elemento).val()},
                    dataType: 'JSON',
                    success: function(json) {
                        if (Object.keys(json).length > 0) {
                            _this.cmpCidade.find('option').remove();

                            var option = $('<option>').val(0).text('Escolha');
                            _this.cmpCidade.append(option);

                            for (idCidade in json) {
                                option = $('<option>').val(idCidade)
                                                      .text(json[idCidade]);
                                _this.cmpCidade.append(option);
                            }
                            _this.carregarCidade.hide();
                        }
                    }
                });
            }
        };

        /**
         * Cores das linhas na tabela de resultados
         */
        this.aplicarCorLinha = function() {
            var _this = this;
            var cor = '';

            _this.corrigeTabela();

            //remove cores
            jQuery('#bloco_itens table tbody tr').removeClass('par');
            jQuery('#bloco_itens table tbody tr').removeClass('impar');

            //aplica cores
            jQuery('#bloco_itens table tbody tr').each(function(){
                cor = (cor == "par") ? "impar" : "par";
                jQuery(this).addClass(cor);
            });
        };

        /*
         * Corrige informação da quantidade de registros encontrados.
         */
         this.corrigeTabela = function(){

            var qtdLinhas = 0;

            //busca quantidade de linhas
            qtdLinhas = jQuery('#bloco_itens table tbody tr').length;
            jQuery("#registros_encontrados").html("");

            if(qtdLinhas == 0){
                jQuery('.resultado').hide();
                jQuery('#bloco_itens').hide();

            }else if(qtdLinhas == 1){
                jQuery("#registros_encontrados").html("1 registro encontrado.");
            }else{
                jQuery("#registros_encontrados").html(qtdLinhas + " registros encontrados.");
            }
        };
    };
})(window, jQuery);