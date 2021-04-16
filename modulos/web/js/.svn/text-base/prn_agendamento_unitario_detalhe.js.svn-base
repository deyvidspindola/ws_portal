/**
 * Sascar - Tecnologia e Seguranca Automotiva S/A
 *
 * O conteúdo deste arquivo não pode ser utilizado por nenhum
 * propósito sem a permissão prévia da Sascar.
 *
 * A classe AgendamentoUnitarioDetalhe é responsável pela operação na tela de
 * de detalhe de agendamento incluindo as operações de busca de agenda e
 * conclusão de agendamento e reagendamento.
 *
 * @package  SmartAgenda
 * @author   Adenilson Santos <adenilson.santos.ext@sascar.com.br>
 * @link     http://www.sascar.com.br
 */
jQuery(document).ready(function(){
   var agendamentoUnitarioDetalhe = new AgendamentoUnitarioDetalhe();
    agendamentoUnitarioDetalhe.init();
});
(function(pkg, $){

    if(!$){
        throw new Error('A classe AgendamentoUnitarioDetalhe requer jQuery 1.7.1');
        return;
    }
    pkg = pkg || {};

    pkg.AgendamentoUnitarioDetalhe = function() {
        this.btoExcluirRedirecionamento;
        this.cmpIdOsPrincipal;
        this.cmpResponsavel;
        this.cmpResponsavelCelular;
        this.cmpContato;
        this.cmpContatoCelular;
        this.cmpContatoEmail;
        this.boxConfirmacao;
        this.msgAgenda;
        this.msgErroAgenda;
        this.mensagemAlerta;
        this.mensagemAlertaAgenda;
        this.mensagemAlertaContato;
        this.blocoAlertaContato;
        this.btoAgendaDisponivel;
        this.btoLimpar;
        this.bt_limpar_endereco
        this.frmAgendar;
        this.formEndereco;
        this.listagemSlots;
        this.icoSelecionado;
        this.icoDisponivel;
        this.icoMelhorDia;
        this.txtResumoData;
        this.txtResumoHora;
        this.txtResumoTipo;
        this.txtResumoLocal;
        this.txtResumoEndereco;
        this.bt_agendar;

        //formulario contato
        this.txtRespCelular;
        this.txtContatoCelular;
        this.txtContatoEmail;

        this.init = function() {
            this.btoExcluirRedirecionamento = $(".excluir-redirecionamento");
            this.cmpIdOsPrincipal = $("#id_os_principal");
            this.boxConfirmacao = $("#box-confirmacao");
            this.btoAgendaDisponivel = $(".agenda-disponivel");
            this.btoLimpar = $("#bt_limpar");
            this.bt_limpar_endereco = $("#bt_limpar_endereco");
            this.frmAgendar = $("#formSalvarAgendamento");
            this.formEndereco = $('#formEndereco');
            this.listagemSlots = $("#listagem-slots");
            this.listagemDatas = $("#listagem-datas");
            this.icoSelecionado = $("#icone-selecionado");
            this.icoDisponivel = $("#icone-disponivel");
            this.icoMelhorDia = $("#icone-melhor-dia");
            this.txtResumoData = $('#resumo-data');
            this.txtResumoHora = $('#resumo-hora');
            this.txtResumoTipo = $('#resumo-tipo');
            this.txtResumoLocal = $('#resumo-local');
            this.txtResumoEndereco = $('#resumo-endereco');
            this.cmpResponsavel = $('#cmp_responsavel');
            this.cmpResponsavelCelular = $('#cmp_responsavel_celular');
            this.cmpContato = $('#cmp_contato');
            this.cmpContatoCelular = $('#cmp_contato_celular');
            this.cmpContatoEmail = $('#cmp_contato_email');
            this.cmpRetiradaReinstalacao = $('#retirada_reinstalacao');
            this.boxOsAdicional = $('#os-adicional');
            this.msgAgenda = $('.msg-agenda');
            this.msgErroAgenda = $('#mensagem_erro_agenda');
            this.mensagemAlerta = $('#mensagem_alerta');
            this.mensagemAlertaAgenda = $('#mensagem_alerta_agenda');
            this.mensagemAlertaContato = $('#mensagem_alerta_contato');
            this.blocoAlertaContato = $('#bloco_alerta_contato');
            this.txtRespCelular = $('#cmp_responsavel_celular');
            this.txtContatoCelular = $('#cmp_contato_celular');
            this.txtContatoEmail = $('#cmp_contato_email');
            this.bt_agendar = $("#bt_agendar");

            this.configurar();
            this.exibirMelhorDia();
            this.verificarExibicaoOAS();
        };

        this.configurar = function() {
            var _this = this;

            this.btoExcluirRedirecionamento.click(function() {
                _this.cancelarRedirecionamento($(this));
            });

            this.btoAgendaDisponivel.click(function() {
                _this.exibirSlots($(this));
            });

            this.listagemSlots.find('input[name=cmp_time_slot]').change(function() {
                _this.atualizarResumo(this);
            });

            this.formEndereco.on('submit', function(event) {
                event.preventDefault();

                //adiciona campos obrigatorios
                $( "#comp_end_estado" ).addClass( "validar" );
                $( "#comp_end_cidade" ).addClass( "validar" );
                $( "#comp_end_cidade_input" ).addClass( "validar" );
                $( "#comp_end_bairro" ).addClass( "validar" );
                $( "#comp_end_bairro_input" ).addClass( "validar" );
                $( "#comp_end_logradouro" ).addClass( "validar" );
                $( "#comp_end_logradouro_input" ).addClass( "validar" );
                $( "#comp_end_numero" ).addClass( "validar" );

                _this.validarFormularioEndereco();
            });

            this.frmAgendar.submit(function(e) {
                e.preventDefault();
                _this.salvar(this);
            });

            this.btoLimpar.click(function() {
                _this.limparFormularios($(this));
            });
            this.bt_limpar_endereco.click(function() {
                _this.limparFormularioDetalhe($(this));
            });
            this.cmpContatoCelular.on('keyup', function() {
                this.value = telefone(this);
            });

            this.cmpResponsavelCelular.on('keyup', function() {
                this.value = telefone(this);
            });

            this.cmpRetiradaReinstalacao.change(function () {
                _this.verificarExibicaoOAS();
            });

            //valida campo Email Contato
            this.txtContatoEmail.change(function () {
                var camposDestaque = new Array();

                if (this.value.length) {
                    if(_this.validacaoEmail(this.value)){
                        _this.esconderMensagemAlertaContato('alerta_contato_email');
                        $("#lbl_contato_email").removeClass('erro');
                        $(this).removeClass('erro');
                    }else{
                        _this.mostrarMensagemAlertaContato('alerta_contato_email');
                        camposDestaque.push({campo:$(this).attr("id")});
                        _this.destacarCampos(camposDestaque);
                    }
                }else{
                    _this.esconderMensagemAlertaContato('alerta_contato_email');
                    $("#lbl_contato_email").removeClass('erro');
                    $(this).removeClass('erro');
                }
            });

            //valida campo Celular Contato
            this.txtContatoCelular.change(function () {
                var camposDestaque = new Array();

                if (this.value.length) {
                    if(_this.validacaoCelular(this.value)){
                        _this.esconderMensagemAlertaContato('alerta_contato_telefone');
                        $("#lbl_contato_celular").removeClass('erro');
                        $(this).removeClass('erro');
                    }else{
                        _this.mostrarMensagemAlertaContato('alerta_contato_telefone');
                        camposDestaque.push({campo:$(this).attr("id")});
                        _this.destacarCampos(camposDestaque);
                    }
                }else{
                    _this.esconderMensagemAlertaContato('alerta_contato_telefone');
                    $("#lbl_contato_celular").removeClass('erro');
                    $(this).removeClass('erro');
                }
            });

            //valida campo Celular Responsável
            this.txtRespCelular.change(function () {
                var camposDestaque = new Array();

                if (this.value.length) {
                    if(_this.validacaoCelular(this.value)){
                        _this.esconderMensagemAlertaContato('alerta_contato_telefone');
                        $("#lbl_responsavel_celular").removeClass('erro');
                        $(this).removeClass('erro');
                    }else{
                        _this.mostrarMensagemAlertaContato('alerta_contato_telefone');
                        camposDestaque.push({campo:$(this).attr("id")});
                        _this.destacarCampos(camposDestaque);
                    }
                }else{
                    _this.esconderMensagemAlertaContato('alerta_contato_telefone');
                    $("#lbl_responsavel_celular").removeClass('erro');
                    $(this).removeClass('erro');
                }

            });
        };

        this.verificarExibicaoOAS = function () {

            if (this.cmpRetiradaReinstalacao.is(':checked')) {
                this.boxOsAdicional.show();
            } else {
                this.boxOsAdicional.hide();
            }
        };

        this.cancelarRedirecionamento = function(elemento) {
            var _this = this;

            this.boxConfirmacao.dialog({
                closeOnEscape: false,
                resizable: false,
                width: 300,
                modal: true,
                buttons: {
                    Sim: function() {
                        $.ajax({
                            type: "POST",
                            url: 'prn_agendamento_unitario.php?acao=cancelarRedirecionamento',
                            data: {idOrdemServico: _this.cmpIdOsPrincipal.val()},
                            dataType: 'JSON',
                            success: function(json) {
                                if (json.resultado) {
                                    $(elemento).parents('.direcionamento')
                                               .remove()
                                }
                                //limpa formulario de endereco
                                jQuery('#formEndereco')[0].reset();
                                jQuery('#formEndereco #comp_end_referencia').val('');
                                reiniciarComponente();

                                _this.boxConfirmacao.dialog("close");
                            }
                        });
                    },
                    "Não": function() {
                        $(this).dialog("close");
                    }
                }
            });
        };

        this.ocultarListagemSlots = function() {
            var diaSelecionado = this.listagemDatas.find('img.selecionado');

            this.listagemSlots.hide();
            this.listagemSlots.find('tbody').hide();

            diaSelecionado.removeClass('selecionado');
            if (diaSelecionado.hasClass('melhor-dia')) {
                diaSelecionado.attr('src', this.icoMelhorDia.attr('src'));
            } else {
                diaSelecionado.attr('src', this.icoDisponivel.attr('src'));
            }
        };

        this.exibirMelhorDia = function() {
            var pagina = this.getParam('pagina');

            if ((typeof(pagina) !== undefined) && (pagina > 1)) {
                return;
            }

            var melhorData = this.btoAgendaDisponivel.find('img.melhor-dia');
            var dataDisponivel = this.btoAgendaDisponivel;

            if (melhorData.length) {
                this.exibirSlots($(melhorData.get(0)).parent('a'));
            }
            else if (dataDisponivel.length) {
                this.exibirSlots($(dataDisponivel.get(0)));
            }
        };

        this.getParam = function(sParam) {
            var sPageURL = window.location.search.substring(1);
            var sURLVariables = sPageURL.split('&');
            for (var i = 0; i < sURLVariables.length; i++)
            {
                var sParameterName = sURLVariables[i].split('=');
                if (sParameterName[0] == sParam)
                {
                    return sParameterName[1];
                }
            }
        };

        this.exibirSlots = function(elemento) {

            var icone = elemento.find('img').first();
            var data = elemento.parent('div').next().text();
            var dia = elemento.closest('table').find('th')
                              .eq(elemento.closest('td').index()).text();

            var diasSemana = {
                'Segunda' : 'Segunda-Feira',
                'Terça' : 'Terça-Feira',
                'Quarta' : 'Quarta-Feira',
                'Quinta' : 'Quinta-Feira',
                'Sexta' : 'Sexta-Feira',
                'Sábado' : 'Sábado',
                'Domingo' : 'Domingo'
            };

            $('#alerta_time_slot').hide();

            this.ocultarListagemSlots();
            this.limparResumo();

            this.txtResumoData.text(data + ' - ' + diasSemana[dia]);
            icone.attr('src', this.icoSelecionado.attr('src'))
                 .addClass('selecionado');
            $('#slots-' + data).show();
            $('#slots-' + data).find('input:first').attr('checked', true);
            this.atualizarResumo($('#slots-' + data).find('input:first'));
            this.listagemSlots.show();
        };

        this.limparResumo = function() {
            this.txtResumoHora.empty();
            this.txtResumoTipo.empty();
            this.txtResumoLocal.empty();
            this.txtResumoEndereco.empty();
        };

        this.atualizarResumo = function(elemento) {
            var tds = $(elemento.closest('tr')).find('td');
            var tipo = tds.eq(1).text();
            var horario = tds.eq(2).text();
            var local = tds.eq(3).find('.nome').text();
            var endereco = tds.eq(3).find('.endereco');
            var complemento = tds.eq(3).find('.complemento');

            if (endereco.length) {
                endereco = endereco.html().split('<br>').join(' - ');
            }
            if (complemento.length && complemento.text().length) {
                endereco = endereco + ' (' + complemento.text() + ')';
            }
            this.txtResumoHora.text(horario);
            this.txtResumoTipo.text(tipo);
            if (tipo == 'FIXO') {
                this.txtResumoLocal.text(local);
                this.txtResumoEndereco.text(endereco);
            } else {
                this.txtResumoLocal.empty();
                this.txtResumoEndereco.empty();
            }
        };

        this.mostrarMensagemAlerta = function(mensagem) {
            this.mensagemAlerta
                .text(mensagem)
                .removeClass('invisivel').fadeIn();
        };

        this.esconderMensagemAlerta = function() {
            this.mensagemAlerta.fadeOut();
        };

        this.mostrarMensagemAlertaAgenda = function(mensagem) {
            this.mensagemAlertaAgenda
                .text(mensagem)
                .removeClass('invisivel').fadeIn();
        };

        this.esconderMensagemAlerta = function() {
            this.mensagemAlerta.fadeOut();
        };

        this.mostrarMensagemAlertaContato = function(id) {
            $("#"+id).show();
        };

        this.esconderMensagemAlertaContato = function(id) {
            var esconder = true;
            var responsavelCelular = $.trim(this.txtRespCelular.val());
            var contatoCelular = $.trim(this.txtContatoCelular.val());

            if(id == "alerta_contato_telefone"){
                if(responsavelCelular.length){
                    if(!this.validacaoCelular(this.txtRespCelular.val())){
                        esconder = false;
                    }
                }

                if(contatoCelular.length){
                    if(!this.validacaoCelular(this.txtContatoCelular.val())){
                        esconder = false;
                    }
                }
                if( esconder ){
                    $("#"+id).hide();
                }

            }else{
                $("#"+id).hide();
            }
        };

        this.validarFormularioEndereco = function() {
            var valido = true;
            var camposDestaque = new Array();
            this.esconderMensagemAlerta();

            this.formEndereco.find('.validar').each(function() {
                var valor = $(this).val().trim();
                if ((valor == 0) && $(this).prop('nodeName') == 'SELECT') {
                    valor = null;
                }
                if (!valor) {
                    if($(this).is(':visible')){
                        valido = false;
                        //adiciona campo para destaque
                        camposDestaque.push({campo:$(this).attr("id")});
                    }
                }
            });

            if (valido) {
                this.formEndereco.get(0).submit();
            } else {
                //destaca campos
                showFormErros(camposDestaque);

                this.mostrarMensagemAlerta('Existem campos obrigatórios não preenchidos.');
            }
        };

        this.salvar = function(formulario) {
            this.esconderMensagemAlertaContato();

            var slot = this.listagemSlots.find(
                'input[name=cmp_time_slot]:checked'
            );

            var errosGenerico = 0;
            var erroTimeSlot = false;
            var errosInvalidoEmail = 0;
            var errosInvalidoCelular = 0;
            var campos = new Array();

            if (slot.length) {
                var contato = $.trim(this.cmpContato.val());
                var contatoCelular = $.trim(this.cmpContatoCelular.val());
                var contatoEmail = $.trim(this.cmpContatoEmail.val());
                var responsavel = $.trim(this.cmpResponsavel.val());
                var responsavelCelular = $.trim(this.cmpResponsavelCelular.val());

                if (!contato) {
                    campos.push({campo:this.cmpContato.attr("id")});
                    errosGenerico++;
                }
                if (contatoCelular.length) {
                    if ( !this.validacaoCelular(contatoCelular) ) {
                        campos.push({campo:this.cmpContatoCelular.attr("id")});
                        errosInvalidoCelular++;
                    }
                } else {
                    campos.push({campo:this.cmpContatoCelular.attr("id")});
                    errosGenerico++;
                }
                if (contatoEmail.length) {
                    if ( !this.validacaoEmail(contatoEmail) ) {
                        campos.push(this.cmpContatoEmail);
                        campos.push({campo:this.cmpContatoEmail.attr("id")});
                        errosInvalidoEmail++;
                    }
                } else {
                    campos.push({campo:this.cmpContatoEmail.attr("id")});
                    errosGenerico++;
                }
                if (responsavelCelular.length) {
                    if ( !this.validacaoCelular(responsavelCelular) ) {
                        campos.push({campo:this.cmpResponsavelCelular.attr("id")});
                        errosInvalidoCelular++;
                    }
                }
                if (this.txtResumoTipo.text() == 'MOVEL') {
                    if (!responsavel) {
                        campos.push({campo:this.cmpResponsavel.attr("id")});
                        errosGenerico++;
                    }
                    if (responsavelCelular.length) {
                        if ( !this.validacaoCelular(responsavelCelular) ) {
                            campos.push({campo:this.cmpResponsavelCelular.attr("id")});
                            errosInvalidoCelular++;
                        }
                    } else {
                        campos.push({campo:this.cmpResponsavelCelular.attr("id")});
                        errosGenerico++;
                    }
                }
            } else {
                erroTimeSlot = true;
            }
            if (errosGenerico || errosInvalidoEmail || errosInvalidoCelular || erroTimeSlot) {
                if (erroTimeSlot){
                    this.mostrarMensagemAlertaContato('alerta_time_slot');
                } else if (errosInvalidoEmail) {
                    this.mostrarMensagemAlertaContato('alerta_contato_email');
                } else if (errosInvalidoCelular) {
                    this.mostrarMensagemAlertaContato('alerta_contato_telefone');
                } else {
                    this.mostrarMensagemAlertaContato('alerta_contato');
                }
                if (campos.length) {
                    showFormErros(campos);
                }
            } else {
                this.bt_agendar.prop('disabled',true);
                formulario.submit();
            }
        };

        this.mensagemAgenda = function(mensagem) {
            this.msgErroAgenda.text(mensagem).show();
            location.href = "#visualizar";
        };

        this.limparFormularios = function(elemento) {
            var formulario = elemento.closest('form');

            formulario.find('input, textarea').val('');
            formulario.find('select').val(0);
            formulario.find('input[type=checkbox]').prop('checked', false);
        };

         this.limparFormularioDetalhe = function(elemento) {
            var formulario = elemento.closest('form');

            jQuery('#comp_end_referencia').val('');
            jQuery('#atendimento_emergencial').prop('checked', false);

            //funcao no JS do componente de endereco
            reiniciarComponente();

        };

        this.validacaoEmail =  function(elemento) {

            var patternEmail = /^[\w-]+(\.[\w-]+)*@(([A-Za-z\d][A-Za-z\d-]{0,61}[A-Za-z\d]\.)+[A-Za-z]{2,6}|\[\d{1,3}(\.\d{1,3}){3}\])$/;
            return patternEmail.test(elemento);
        };

        this.validacaoCelular = function(elemento) {

            var reg = new RegExp(/^[(]\d{2}[)] \d{4,5}-\d{4}$/);
            var valor = elemento;
            var prefixo = '';

            if ( reg.test(valor) ){

                //Remove tudo o que não é dígito
                valor = valor.replace(/\D/g,"");
                prefixo = valor.charAt(2);

                return (prefixo == '6' || prefixo == '8' || prefixo == '9') ? true : false;

            }else{
                return false;
            }
        }

        this.destacarCampos = function(campos) {

            jQuery.each(campos, function() {
                if(jQuery('#' + this.campo).length > 0) {
                    var element = jQuery('#' + this.campo).parent();

                    element.children().addClass('erro');
                    element.append($('<span>').text(this.mensagem));
                } else {
                    var element = jQuery('input[name=' + this.campo + ']').parent();

                    element.addClass('erro');
                    element.append($('<span>').text(this.mensagem));
                }
            });
        }

    };
})(window, jQuery);