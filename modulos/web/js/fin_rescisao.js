jQuery(document).ready(function() {
    "use strict";
    

    /**
     * Inclui na classe Number (Int e Float) método para formatar moeda
     */
    Number.prototype.toMoney = function(c, d, t) {
        var n = this,
            c = isNaN(c = Math.abs(c)) ? 2 : c,
            d = d || ",",
            t = t || ".",
            s = n < 0 ? "-" : "",
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;

        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "jQuery1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    };

    /**
     * Cria alerta lindchu com a mensagem enviada
     */
    function alerta(msg) {
        removeAlerta();
        criaAlerta(msg);
    };

    /**
     * Destaca elemento com erro e exibe mensagem
     */
    function addError(elm, msg) {
        elm.addClass('highlight');
        alerta(msg);
    }

    /**
     * Retira destaque dos campos com erro
     */
    function clearErrors() {
        jQuery('.highlight').removeClass('highlight');
        removeAlerta();
    }

    /**
     * Formata dinheiro em float para ser usado nos cálculos do JS
     */
    function parseMoney(value) {
        if (typeof value == 'undefined') {
            return 0.00;
        }

        return parseFloat(
                    value.toString()
                         .replace(/[^\d\.\,]/, '')
                         .replace('.', '')
                         .replace(',', '.')
               );
    }

    /**
     * Periodos
     */


    /**
     * Formata elementos de layout
     */
    function layoutActions() {
        // Dá "zebra" nas tabelas
        jQuery('.bloco_conteudo tbody tr:odd').addClass('par');

        // Máscara de dinheiro
        jQuery('.mask-money').maskMoney({
            thousands:     '.'
          , decimal:       ','
          , defaultZero:   true
          , allowZero:     true
          , allowNegative: false
        });

        // Máscara de inteiros
        jQuery('.mask-numbers')
            .keydown(function() {
                maskNumbersOnly(jQuery(this));
            })
            .keypress(function() {
                maskNumbersOnly(jQuery(this));
            })
            .keyup(function() {
                maskNumbersOnly(jQuery(this));
            })
            .change(function() {
                maskNumbersOnly(jQuery(this));
            })
            .focus(function() {
                maskNumbersOnly(jQuery(this));
            });

        /**
         * Remove caracteres não numéricos de um campo
         */
        function maskNumbersOnly(elm) {
            if (/[^\d]/.test(elm.val())) {
                elm.val(elm.val().toString().replace(/[^\d]/g, ''));
            }
        }

        /**
         * Gambi para impedir 'ENTER' na busca de clientes
         */
        if (jQuery('.busca-cliente').length) {
            jQuery(document).keypress(function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                }
            });
        }

        /**
         * Setup datepicker
         */
        jQuery('input.data').datepicker({
            dateFormat    : 'dd/mm/yy'
          , dayNamesMin   : [ 'D', 'S', 'T', 'Q', 'Q', 'S', 'S' ]
          , monthNames    : [ 'Janeiro', 'Fevereiro', 'Março', 'Abril',
                'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro',
                'Novembro', 'Dezembro' ]
          , showOn      : 'both'
          , buttonImage     : 'images/calendar_cal.gif'
          , buttonImageOnly : true
        });

        // Datepicker data rescisão
        /* jQuery('#resmfax').datepicker({
            dateFormat    : 'dd/mm/yy'
          , dayNamesMin   : [ 'D', 'S', 'T', 'Q', 'Q', 'S', 'S' ]
          , monthNames    : [ 'Janeiro', 'Fevereiro', 'Março', 'Abril',
                'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro',
                'Novembro', 'Dezembro' ]
          , showOn      : 'both'
          , buttonImage     : 'images/calendar_cal.gif'
          , buttonImageOnly : true
          , maxDate         : 0
        }); */
    }
    
    layoutActions();

    /**
     * Exclui uma rescisão via AJAX e redireciona para a página inicial do módulo
     */
    jQuery('.rescisao-excluir').click(function() {
        var url = jQuery(this).data('url');

        var conf = confirm("Você tem certeza de que deseja excluir esta rescisão?");

        if (conf === true) {
            jQuery.post(url, function(r) {
                window.location = 'fin_rescisao.php';
            });
        }
    });

    /**
     * Busca o nome de um cliente via popup
     */
    jQuery('.busca-cliente').click(function(e) {
        // Gambi para não redirecionar a página
        e.preventDefault();

        // Abre popup padrão do sistema
        var searchWindow = window.open(
            'psq_cliente.php?campo_cod=clioid&campo_txt=cliente&nome_form=cliente_form'
          , 'ContrlWindow'
          , 'status,scrollbars=yes,menubar=no,toolbar=no,location=yes,width=480,height=220'
        );

        // buscaDataSolicitacao(searchWindow);
    });

    // Busca data de solicitação de rescisão ao digitar contrato
    /* jQuery('#connumero').change(function() {
        var connumero = jQuery(this).val()
          , url       = 'fin_rescisao.php?acao=buscaDataSolicitacaoContrato&connumero=' + connumero;

        // Apaga contratos carregados
        jQuery('.container-contratos').html('');

        jQuery.get(url, function(r) {
            if (r.length) {
                jQuery('#resmfax').val(r);
            }
        });
    }); */

    /**
     * Limpa a tela ao trocar a data da solicitação de rescisão
     */
    jQuery('#resmfax').change(function() {
        jQuery('.container-contratos').html('');
        jQuery('.container-multas').html('');
    });

    /**
     * Busca a data da última solicitação de rescisão (data de pré-rescisão)
     */
    function buscaDataSolicitacao(searchWindow) {
        // Se o popup for fechado, busca a data
        if (searchWindow.closed) {
            var clioid = jQuery('#clioid').val()
              , url    = 'fin_rescisao.php?acao=buscaDataSolicitacao&clioid=' + clioid;

            // Apaga contratos carregados
            jQuery('.container-contratos').html('');

            jQuery.get(url, function(r) {
                if (r.length) {
                    jQuery('#resmfax').val(r);
                }
            });
        } else {
            setTimeout(function() {
                buscaDataSolicitacao(searchWindow);
            }, 100);
        }
    };

    /**
     * Busca contratos do cliente
     */
    jQuery('#pesquisar-contratos').click(function() {
        var clioid    = jQuery('#clioid').val()
          , connumero = jQuery('#connumero').val()
          // , resmfax   = jQuery('#resmfax').val()
          , loader    = jQuery('.container-contratos-loader')
          , containerContratos = jQuery('.container-contratos')
          , containerMultas    = jQuery('.container-multas');

        var mensagem_alerta = jQuery("#mensagem_alerta");

        mensagem_alerta.hideMessage();

        // Valida preenchimento de nome de cliente ou número de contrato
        if (!clioid.length && !connumero.length) {
            mensagem_alerta.html('É necessário informar o nome do cliente e/ou número do contrato para realizar a pesquisa.').showMessage();
            return;
        }

        // Valida preenchimento da data de solicitação
        /* if (!resmfax.length) {
            mensagem_alerta.html('É necessário informar a data da solicitação.').showMessage();
            return;
        } */

        // Prepara parâmetros para usar na URL (GET)
        var params = jQuery.param({
            acao:       'buscaContratos'
          , clioid:     clioid
          , connumero:  connumero
          // , resmfax:    resmfax
        });

        // Remove conteúdos do container e exibe loader
        containerContratos.html('');
        containerMultas.html('');
        loader.show();

        jQuery.get('fin_rescisao.php?' + params, function(r) {
            containerContratos.html(r);
            loader.hide();
            layoutActions();

            jQuery('.data input').createDate();
        });
    });

    jQuery("#retornar-novo-contrato").click(function(){
        window.location.href ="fin_rescisao.php";
    });

    jQuery("#novo-contrato").click(function(){
        window.location.href ="fin_rescisao.php?acao=novo";
    });

    /**
     * Checkbox de seleção de todos os contratos
     */
    jQuery('.container-contratos').on('click', '.selecionar-todos-contratos', function() {
        jQuery('.contrato-cliente').attr('checked', !!jQuery(this).attr('checked'));
    });

    /**
     * Busca as multas de um contrato.
     * Delega evento para funcionar com carregamento via AJAX
     */
    jQuery('.container-contratos').on('click', '.pesquisar-multas', function() {
        var contratos   = jQuery('.contrato-cliente:checked')
          , idsContrato = contratos.map(function() {
                              return jQuery(this).val();
                          });
        var multas = jQuery('.contrato-multa-porcentagem');

        var multaValroes     = {};
        var solicitacaoDatas = {};

        var isentar_monitoramento = false;
        var isentar_locacao = false;

        idsContrato.each(function(index, valor){
          if (jQuery.trim(jQuery('#multa-' + valor).val())) {
            multaValroes[valor]     = jQuery('#multa-' + valor).val();
          }

          if(jQuery('#incluir_servicos_monitoramento_' + valor).is(':checked')) {
            isentar_monitoramento = true;
          }

          if(jQuery('#incluir_servicos_locacao_' + valor).is(':checked')) {
            isentar_locacao = true;
          }

          if (jQuery.trim(jQuery('#resmfax-' + valor).val())) {
            solicitacaoDatas[valor] = jQuery('#resmfax-' + valor).val();
          }
        });

        if (contratos.length === 0) {
            alerta('Você deve selecionar ao menos um contrato para efetuar a pesquisa.');
            return;
        }

        var params = jQuery.param({
                         acao                   :      'buscaMultas'
                       , connumero              : idsContrato.toArray()
                       , solicitacao            :   solicitacaoDatas
                       , multa                  : multaValroes
                       , clioid                 : jQuery('#clioid').val()
                       , isentar_monitoramento  : isentar_monitoramento
                       , isentar_locacao        : isentar_locacao
                     })
          , loader          = jQuery('.container-multas-loader')
          , containerMultas = jQuery('.container-multas');

        // Remove conteúdos do container e exibe loader
        containerMultas.html('');
        loader.show();

        jQuery.get('fin_rescisao.php?' + params, function(r) {
            containerMultas.html(r);
            loader.hide();
            layoutActions();

            // Calcula a taxa de retirada e a
            // rescisão ao terminar de carregar a página
            calculaTaxaRetirada();
            calculaTaxaMultaNaoDevolucao();
            calculaTotalRescisao();

        });
    });

    /**
     * Checkbox de seleção de todas as faturas
     */
    jQuery('.container-multas').on('click', '.selecionar-todos-multa-fatura', function() {
        jQuery('.multa-fatura').attr('checked', !!jQuery(this).attr('checked'));
        calculaTotalRescisao();
    });

    /**
     * Checkbox de seleção de todas as observações de faturas
     */
    jQuery('.container-multas').on('click', '.selecionar-todos-multa-faturas-observacao', function() {
        jQuery('.multa-faturas-observacao').attr('checked', !!jQuery(this).attr('checked'));
    });

    /**
     * Altera a observação das multas de faturas selecionadas
     */
    jQuery('.container-multas').on('change', '.change-multa-faturas-observacao', function() {
        var observacao = jQuery(this).val();

        // Preenche os campos em cada row
        jQuery('.multa-faturas-observacao:checked').map(function() {
            jQuery(this).closest('tr')
                   .find('.multa-faturas-locacao-observacao-text')
                   .val(observacao);
        });
    });

    /**
     * Recalcula total de faturas ao clicar num checkbox
     */
    jQuery('.container-multas').on('click', '.multa-fatura', function() {
        calculaTotalRescisao();
    });

    /**
     * Valida o desconto de uma multa
     */
    jQuery('.container-multas').on('blur change', '.multa-fatura-desconto', function() {

        var self = jQuery(this)
          , row  = self.closest('tr')
          , valorMulta    = parseMoney(row.find('.multa-fatura-valor').val())
          , valorDesconto = parseMoney(self.val());

          var somaMultas = 0;
          jQuery.each(jQuery('.multa-fatura-desconto'), function(i, value){

            somaMultas += parseMoney(jQuery(value).val());

          })
        
        jQuery('#valorPagoIndevidoMonitoramentoTotal').val((somaMultas).toMoney());
        
        if (parseMoney(jQuery('#valorPagoIndevidoMonitoramentoTotal').val()) > parseMoney(jQuery('#valorMultaMensalidade').val())) {
            jQuery('#valorPagoIndevidoMonitoramentoTotal').val((0.00).toMoney());
            alerta('O valor do desconto não pode ser maior que o valor da multa.');

            setTimeout(function() {
                removeAlerta();
            }, 3000);
        } else {
            jQuery('#valorMultaMensalidadeFaltante').val((parseMoney(jQuery('#valorMultaMensalidade').val()) - parseMoney(jQuery('#valorPagoIndevidoMonitoramentoTotal').val())).toMoney());
        }

        calculaTotalRescisao();
    });

    /**
     * Valida o desconto de uma multa
     */

    jQuery("body").delegate('#valorMultaMensalidade','blur',function(){
      
        var self = jQuery(this)
        , row  = self.closest('tr')
        , valorMulta    = parseMoney(row.find('.multa-fatura-valor').val())
        , valorDesconto = parseMoney(self.val());

        var somaMultas = 0;
        jQuery.each(jQuery('.multa-fatura-desconto'), function(i, value){

          somaMultas += parseMoney(jQuery(value).val());

        })
      
      jQuery('#valorPagoIndevidoMonitoramentoTotal').val((somaMultas).toMoney());
      
      if (parseMoney(jQuery('#valorPagoIndevidoMonitoramentoTotal').val()) > parseMoney(jQuery('#valorMultaMensalidade').val())) {
          jQuery('#valorPagoIndevidoMonitoramentoTotal').val((0.00).toMoney());
          alerta('O valor do desconto não pode ser maior que o valor da multa.');

          setTimeout(function() {
              removeAlerta();
          }, 3000);
      } else {
          jQuery('#valorMultaMensalidadeFaltante').val((parseMoney(jQuery('#valorMultaMensalidade').val()) - parseMoney(jQuery('#valorPagoIndevidoMonitoramentoTotal').val())).toMoney());
      }

      calculaTotalRescisao();
  });
    
    /**
     * Valida o desconto de uma multa
     */

    jQuery("body").delegate('#valorPagoIndevidoMonitoramentoTotal','blur',function(){
      
      var self = jQuery(this)
          , row  = self.closest('tr')
          , valorMulta    = parseMoney(row.find('.multa-fatura-valor').val())
          , valorDesconto = parseMoney(self.val());
      
       var somaMultas = 0;
         jQuery.each(jQuery('.multa-fatura-desconto'), function(i, value){

           somaMultas += parseMoney(jQuery(value).val());

         })
         
      //   jQuery('#valorPagoIndevidoMonitoramentoTotal').val((somaMultas).toMoney());

         if (parseMoney(jQuery('#valorPagoIndevidoMonitoramentoTotal').val()) > parseMoney(jQuery('#valorMultaMensalidade').val())) {
             jQuery('#valorPagoIndevidoMonitoramentoTotal').val((0.00).toMoney());
             alerta('O valor do desconto não pode ser maior que o valor da multa.');

             setTimeout(function() {
                 removeAlerta();
             }, 3000);
         } else {
             jQuery('#valorMultaMensalidadeFaltante').val((parseMoney(jQuery('#valorMultaMensalidade').val()) - parseMoney(jQuery('#valorPagoIndevidoMonitoramentoTotal').val())).toMoney());
         }

         calculaTotalRescisao();
  });
    
    
    jQuery("body").delegate('#totalMensalidadeEquipamento','blur',function(){
      
        var self = jQuery(this)
        , row  = self.closest('tr')
        , valorMulta    = parseMoney(row.find('.multa-fatura-valor').val())
        , valorDesconto = parseMoney(self.val());
    
     var somaMultas = 0;
       jQuery.each(jQuery('.multa-fatura-desconto'), function(i, value){

         somaMultas += parseMoney(jQuery(value).val());

       })

    //   jQuery('#valorPagoIndevidoMonitoramentoTotal').val((somaMultas).toMoney());
       
       
       if (parseMoney(jQuery('#totalMensalidadeIndevido').val()) > parseMoney(jQuery('#totalMensalidadeEquipamento').val())) {
           jQuery('#totalMensalidadeIndevido').val((0.00).toMoney());
           alerta('O valor do desconto não pode ser maior que o valor da multa.');

           setTimeout(function() {
               removeAlerta();
           }, 3000);
       } else {
           jQuery('#totalDiferencaIndevido').val((parseMoney(jQuery('#totalMensalidadeEquipamento').val()) - parseMoney(jQuery('#totalMensalidadeIndevido').val())).toMoney());
       }
  
      
      calculaTotalRescisao();
  });
    
    
    jQuery("body").delegate('#totalMensalidadeIndevido','blur',function(){
      
        var self = jQuery(this)
        , row  = self.closest('tr')
        , valorMulta    = parseMoney(row.find('.multa-fatura-valor').val())
        , valorDesconto = parseMoney(self.val());
    
     var somaMultas = 0;
       jQuery.each(jQuery('.multa-fatura-desconto'), function(i, value){

         somaMultas += parseMoney(jQuery(value).val());

       })

    //   jQuery('#valorPagoIndevidoMonitoramentoTotal').val((somaMultas).toMoney());
       
       
       if (parseMoney(jQuery('#totalMensalidadeIndevido').val()) > parseMoney(jQuery('#totalMensalidadeEquipamento').val())) {
           jQuery('#totalMensalidadeIndevido').val((0.00).toMoney());
           alerta('O valor do desconto não pode ser maior que o valor da multa.');

           setTimeout(function() {
               removeAlerta();
           }, 3000);
       } else {
           jQuery('#totalDiferencaIndevido').val((parseMoney(jQuery('#totalMensalidadeEquipamento').val()) - parseMoney(jQuery('#totalMensalidadeIndevido').val())).toMoney());
       }
  
      
      calculaTotalRescisao();
  });
    
    /**
     * Zera as multas de um contrato
     */
    jQuery('.container-contratos').on('click', '.zerar-multa-contrato', function() {
        var row = jQuery(this).closest('tr')
          , multaPorcentagem = row.find('.contrato-multa-porcentagem')
          , multaValor       = row.find('.contrato-multa-valor')
          , multaTotal       = row.find('.contrato-multa-total');

          multaPorcentagem.val(0);
          multaValor.val((0.00).toMoney());
          multaTotal.val((0.00).toMoney());

          calculaTotalRescisao();
    });

    /**
     * Recalcula a data de fim de vigência e meses faltantes
     */
    // jQuery('.container-contratos').on('keyup keydown keypress change', '.contrato-multa-meses', function() {
    //     var row         = jQuery(this).closest('tr')
    //       , meses       = parseInt(jQuery(this).val())
    //       , dataInicial = row.find('.contrato-multa-data-inicio').text()
    //       , dataFinal   = row.find('.contrato-multa-data-fim')
    //       , dataFim     = ''
    //       , contrato    = jQuery(this).parent('td').parent('tr').children('td:first-child').children('input').val();

    //     // Calcula a data final de vigência do contrato
    //     meses = meses || 0;
    //     jQuery(this).val(meses);

    //     var parts = dataInicial.split('/')
    //       , data = new Date(parts[1] + '/' + parts[0] + '/' + parts[2]);

    //     data.setMonth(data.getMonth() + meses);

    //     var day   = data.getDate().toString()
    //       , month = (data.getMonth() + 1).toString();

    //     // Gambi para formatar a data no formato dd/mm/yyyy
    //     if (day.length == 1) {
    //         dataFim += '0';
    //     }
    //     dataFim += day + '/';

    //     if (month.length == 1) {
    //         dataFim += '0';
    //     }
    //     dataFim += month + '/';
    //     dataFim += data.getFullYear();

    //     dataFinal.text(dataFim);

    //     // Calcula a quantidade de meses faltantes
    //     var dataAtual = (jQuery('#resmfax-' + contrato).val()).split('/');
    //     dataAtual = dataAtual[1] + '/' + dataAtual[0] + '/' + dataAtual[2];
    //     dataAtual = new Date(dataAtual);

    //     var mesesFaltantes = Math.ceil(
    //                             (data.getTime() - dataAtual.getTime())
    //                                 / 60 / 60 / 24 / 31 / 1000
    //                          );

    //     mesesFaltantes = (mesesFaltantes > 0) ? mesesFaltantes : 0;
    //     row.find('.contrato-multa-meses-faltantes').val(mesesFaltantes);
    // });

    jQuery('.container-contratos').on('change', '.contrato-data-recisao', function() {

        var dataInicial;
        var dataFinal;

        var row         = jQuery(this).closest('tr')
          , meses       = row.find('.contrato-multa-meses')
          , dataFim     = ''
          , contrato    = jQuery(this).parent('td').parent('tr').children('td:first-child').children('input').val();

          dataInicial   = jQuery('.contrato-multa-data-inicio').data('inicio');
          dataFinal     = jQuery('.contrato-multa-data-fim').data('fim');

        // Calcula a data final de vigência do contrato
        meses = parseInt(jQuery(meses).val()) || 0;

        var parts = dataInicial.split('/');
        var data = new Date(parts[1] + '/' + parts[0] + '/' + parts[2]);

        data.setMonth(data.getMonth() + meses);

        var day   = data.getDate().toString();
        var month = (data.getMonth() + 1).toString();

        // Formatar a data no formato dd/mm/yyyy
        if (day.length == 1) {
            dataFim += '0';
        }

        dataFim += day + '/';

        if (month.length == 1) {
            dataFim += '0';
        }

        dataFim += month + '/';
        dataFim += data.getFullYear();

        // Calcula a quantidade de meses faltantes
        var dataAtual = (jQuery('#resmfax-' + contrato).val()).split('/');
        
        if(dataAtual != '') {
          dataAtual = dataAtual[1] + '/' + dataAtual[0] + '/' + dataAtual[2];
          dataAtual = new Date(dataAtual);

          var mesesFaltantes = Math.ceil((data.getTime() - dataAtual.getTime()) / 60 / 60 / 24 / 31 / 1000);
          var mesesFaltantes2 = (data.getTime() - dataAtual.getTime()) / 60 / 60 / 24 / 31 / 1000;

          mesesFaltantes = (mesesFaltantes > 0) ? mesesFaltantes : 0;
          row.find('.contrato-multa-meses-faltantes').val(mesesFaltantes);
        }
    });

    /**
     * Recalcula a multa de um contrato ao modificar inputs
     */
    jQuery('.container-contratos').on('keyup keydown keypress change', '.contrato-multa-recalcula', function() {
        // Busca campos da coluna atual
        var row        = jQuery(this).closest('tr')
          , meses      = parseInt(row.find('.contrato-multa-meses-faltantes').val())
          , multaPorcentagem   = parseInt(row.find('.contrato-multa-porcentagem').val())
          , valorMonitoramento = parseMoney(row.find('.contrato-multa-obrigacao').val())
          , totalMultaMonitoramento = row.find('.contrato-multa-valor');

        // Calcula o total da multa de monitoramento
        var valorTotalMonitoramento = meses * valorMonitoramento;
        row.find('.contrato-multa-monitoramento').val(valorTotalMonitoramento.toMoney());

        // Calcula o total da multa de monitoramento
        var multaMonit = meses * valorMonitoramento * multaPorcentagem / 100;

        multaMonit = multaMonit || 0;
        totalMultaMonitoramento.val(multaMonit.toMoney());

        // Calcula o total da multa com desconto
        var total = multaMonit - parseMoney(jQuery('.contrato-multa-desconto').val());
        total = (total && total > 0) ? total : 0;
        row.find('.contrato-multa-total').val(total.toMoney());
    });

    /**
     * Zera multa e atualiza valores 
     */
    jQuery('.container-multas').on('click', '.zerar-multa-locacao', function() {
        
        // Capturando linha do botão clicado
        var row = jQuery(this).closest('tr');
        
        var multaPorcentagem = row.find('.multa-locacao-porcentagem');
        var multaPorcentagemPago = row.find('.multa-locacao-porcentagem-pago');
        var valorMulta = row.find('.multa-locacao-total');
        
        // Capturando Input Valor Total da Multa
        var inputID = jQuery(this).attr('id');
        var inputValorTotalMulta = '#valorTotalMulta_' + inputID.substring(11);

        var valorTotalMulta = jQuery(inputValorTotalMulta).val();
        var totalGeralMultas = jQuery('.multa-locacao-soma-geral').val();
        var totalDiferencaIndevido = jQuery('#totalDiferencaIndevido').val();
        var totalMensalidadeEquipamento = jQuery('#totalMensalidadeEquipamento').val();
        var totalRescisao = jQuery('#totalRescisao').val();

        // Diminuindo valor da multa de total geral
        totalGeralMultas = parseMoney(totalGeralMultas) - parseMoney(valorMulta.val());

        // Calculando Total diferença indevido
        totalDiferencaIndevido = parseMoney(totalDiferencaIndevido) - parseMoney(valorMulta.val());

        // Calculando Total mensalidade equipamento
        totalMensalidadeEquipamento = parseMoney(totalMensalidadeEquipamento) - parseMoney(valorMulta.val());

        // Calculando Total rescisão
        totalRescisao = parseMoney(totalRescisao) - parseMoney(valorMulta.val());
        
        // Diminuindo valor da multa de total da multa
        valorTotalMulta = parseMoney(valorTotalMulta) - parseMoney(valorMulta.val());
       
        // Atualizando total da multa
        jQuery(inputValorTotalMulta).val(valorTotalMulta.toMoney());

        // Atualizando Total Geral
        jQuery('.multa-locacao-soma-geral').val(totalGeralMultas.toMoney());
        

        // Atualizando Total Mensalidade Equipamento
        jQuery('#totalMensalidadeEquipamento').val(totalMensalidadeEquipamento.toMoney());
        
        // Atualizando Total Diferença Indevido
        jQuery('#totalDiferencaIndevido').val(totalDiferencaIndevido.toMoney());

        // Atualizando Total Rescisão
        jQuery('#totalRescisao').val(totalRescisao.toMoney());

        // totalDiferencaIndevido + valorMultaMensalidadeFaltante + taxaRetirada;

        // Zerando Multa
        valorMulta.val((0.00).toMoney());

        // Zerando Porcentagem Multa
        multaPorcentagem.val(0);
        multaPorcentagemPago.val(0);
    });

    /**
     * Recalcula a multa de locação ao modificar porcentagem
     */
    jQuery('.container-multas').on('keyup keydown keypress change', '.multa-locacao-porcentagem', function() {
        // Busca campos da coluna atual
        var row                =  jQuery(this).closest('tr')
          , multaPorcentagem   = parseInt(jQuery(this).val())
          , multaValorContrato = parseMoney(row.find('.mult  a-locacao-porcentagem-contrato').val())
          , totalMulta         = row.find('.multa-locacao-total');

        // Calculo da multa
        var multaMonit = multaPorcentagem * multaValorContrato / 100;

        // Exibe total da multa
        multaMonit = multaMonit || 0;
        totalMulta.val(multaMonit.toMoney());

        recalculaMultasLocacao();
    });

    /**
     * Altera a porcentagem de todas as multas de locação
     */
    jQuery('.container-multas').on('keyup keydown keypress change', '.multa-locacao-porcentagem-geral', function() {
        var valor = parseInt(jQuery(this).val()) || 0;

        jQuery('.multa-locacao-porcentagem').val(valor);
        jQuery(this).val(valor);

        recalculaMultasLocacao();
    });

    /**
     * Altera a porcentagem de todas as multas de locação DE UMA NOTA FISCAL
     */
    jQuery('.container-multas').on('keyup keydown keypress change', '.multa-locacao-porcentagem-pornota', function() {
        var valor = parseInt(jQuery(this).val()) || 0;

        jQuery(this).parents('tfoot').prev('tbody').find('.multa-locacao-porcentagem').val(valor);
        jQuery(this).val(valor);

        recalculaMultasLocacao();
    });

    /**
     * Recalcula o valor de cada multa de locação, individualmente
     */
    function recalculaMultasLocacao() {
        jQuery('.multa-locacao-porcentagem').map(function() {
            var row            = jQuery(this).closest('tr')
          , multaPorcentagem   = parseInt(jQuery(this).val())
          //, multaValorContrato = parseMoney(row.find('.multa-locacao-porcentagem-contrato').val())
          , multaValorContrato = parseMoney(row.find('.multa-locacao-valor').val())
          , totalMulta         = row.find('.multa-locacao-total');

            // Calculo da multa
            var multaMonit = multaPorcentagem * multaValorContrato / 100;

            // Exibe total da multa
            multaMonit = multaMonit || 0;
            totalMulta.val(multaMonit.toMoney());
        });

        // Recalcula o total da rescisão
        calculaTotalRescisao();
    }

    /**
     * Calcula o total das multas de locação
     */
    function calculaMultaLocacao() {
        // Soma o valor de todas as multas
        var antigo;
        var nota        = '';
        var somaGeral   = 0.00;
        var somaPorNota = 0.00;

        jQuery.each(jQuery('.multa-locacao-total'), function(index) {
            var codigo = jQuery.trim(jQuery(this).parents('tbody').children('tr:first-child').children('td:first-child').html());
            var valor  = parseFloat(parseMoney(jQuery(this).val()));

            if (codigo != nota) {
              nota = codigo;

              if (antigo) {
                antigo.parents('tbody').next('tfoot').find('.multa-locacao-soma-pornota').val(parseFloat(somaPorNota).toMoney());
              }

              somaPorNota = 0.00;
            }

            somaGeral   += valor;
            somaPorNota += valor;

            if (jQuery('.multa-locacao-total').length == index + 1) {
              jQuery(this).parents('tbody').next('tfoot').find('.multa-locacao-soma-pornota').val(parseFloat(somaPorNota).toMoney());
            }

            antigo = jQuery(this);
        });

        jQuery('.multa-locacao-soma-geral').val(parseFloat(somaGeral).toMoney());
    };

    /**
     * Checkbox de seleção de todas as observações de multas de locação
     */
    jQuery('.container-multas').on('click', '.multa-locacao-observacao-selecionar-geral', function() {
        jQuery('.multa-locacao-observacao-check').attr('checked', !!jQuery(this).attr('checked'));
    });

    /**
     * Checkbox de seleção de todas as observações de multas de locação DE UMA NOTA FISCAL
     */
    jQuery('.container-multas').on('click', '.multa-locacao-observacao-selecionar-pornota', function() {
        jQuery(this).parents('tfoot').prev('tbody').find('.multa-locacao-observacao-check').attr('checked', !!jQuery(this).attr('checked'));
    });

    /**
     * Altera a observação das multas de multas de locação selecionadas
     */
    jQuery('.container-multas').on('change', '.change-multa-locacao-observacao', function() {
        var observacao = jQuery(this).val();

        // Preenche os campos em cada row
        jQuery('.multa-locacao-observacao-check:checked').map(function() {
            jQuery(this).closest('tr')
                   .find('.multa-locacao-observacao')
                   .val(observacao);
        });
    });

    /**
     * Recalcula a taxa de retirada ao clicar num checkbox
     */
    jQuery('.container-multas').on('click', '.multa-retirada', function() {
        if(
            jQuery(this).parent().next().children('.multa-nao-retirada').attr('checked') == 'checked'
            && jQuery(this).attr('checked') == 'checked'
        ){
            jQuery(this).parent().next().children('.multa-nao-retirada').prop('checked', false);
        }
        calculaTaxaRetirada();
        calculaTaxaMultaNaoDevolucao();
        calculaTotalRescisao();
    });

    jQuery('.container-multas').on('click', '.multa-nao-retirada', function() {
        if(
            jQuery(this).parent().prev().children('.multa-retirada').attr('checked') == 'checked'
            && jQuery(this).attr('checked') == 'checked'
        ){
            jQuery(this).parent().prev().children('.multa-retirada').prop('checked', false);
        }
        calculaTaxaRetirada();
        calculaTaxaMultaNaoDevolucao();
        calculaTotalRescisao();
    });

    /**
     * Recalcula a taxa de retirada ao editar o valor da multa
     */
    jQuery('.container-multas').on('keyup keydown keypress', '.multa-retirada-valor', function() {
        calculaTaxaRetirada();
        calculaTotalRescisao();
    });

    jQuery('.container-multas').on('keyup keydown keypress', '.multa-nao-retirada-valor', function() {
        calculaTaxaMultaNaoDevolucao();
        calculaTotalRescisao();
    });

    /**
     * Recalcula o valor da taxa de retirada
     */
    function calculaTaxaRetirada() {
        var valorTotalTaxas = 0;

        jQuery('.multa-retirada:checked').map(function() {
            var row   = jQuery(this).closest('tr')
              , multa = parseMoney(row.find('.multa-retirada-valor').val());

            valorTotalTaxas += multa;
        });

        jQuery('.valor-total-taxas').val(valorTotalTaxas.toMoney());
    };

    function calculaTaxaMultaNaoDevolucao() {
        var valorTotalTaxas = 0;

        jQuery('.multa-nao-retirada:checked').map(function() {
            var row   = jQuery(this).closest('tr')
              , multa = parseMoney(row.find('.multa-nao-retirada-valor').val());

            valorTotalTaxas += multa;
        });

        jQuery('.valor-total-multa-nao-devolucao').val(valorTotalTaxas.toMoney());
    };

    /**
     * Recalula o valor das multas de serviços não faturados ao trocar valor
     */
    jQuery('.container-multas').on('keyup keydown keypress', '.multa-servico', function() {
        calculaTotalRescisao();
    });

    /**
     * Recalcula o valor das multas de serviços não faturados
     */
    function calculaMultaServicos() {
        var valorTotalMulta = 0;

        jQuery('.multa-servico').map(function() {
            valorTotalMulta += parseMoney(jQuery(this).val());
        });;

        jQuery('.multa-servico-total').val(valorTotalMulta.toMoney());
        jQuery('.valor-total-servicos').val(valorTotalMulta.toMoney());
    };

    /**
     * Recalcula o valor da rescisão ao alterar o valor da taxa de retirada
     */
    jQuery('.container-multas').on('keyup keydown keypress blur change focus', '.valor-total-taxas', function() {
        calculaTotalRescisao();
    });

    /**
     * Calcula o valor total da rescisão
     */
    function calculaTotalRescisao() {
        if (!jQuery('.valor-total-multa').val()
            || !jQuery('.valor-total-multa').val().length) {
            return;
        }

        // Recalcula a multa de locação
        calculaMultaLocacao();

        // Calcula o valor das multas de monitoramento e locação somadas
        var valorTotalMulta = 0;

        jQuery('.contrato-cliente:checked').map(function() {
            var row   = jQuery(this).closest('tr')
              , multa = parseMoney(row.find('.contrato-multa-total').val());

            valorTotalMulta += multa;
        });

        // Multas de locação
        // valorTotalMulta += parseMoney(jQuery('.multa-locacao-soma-geral').val());
        valorTotalMulta += parseMoney(jQuery('#totalMensalidadeEquipamento').val());

        // Seta o valor total da multa de monitoramente e de locação
        jQuery('.valor-total-multa').val(valorTotalMulta.toMoney());
        
        var valorFaltanteLocacao = valorTotalMulta - parseMoney(jQuery('#totalMensalidadeIndevido').val());
        jQuery('#totalDiferencaIndevido').val(valorFaltanteLocacao.toMoney());

        // Calcula o valor total das taxas de locação
        var valorTotalTaxas = parseMoney(jQuery('.valor-total-taxas').val());

        // Calcula o total das faturas
        var valorTotalFaturas = parseMoney(jQuery('.valor-total-faturas').val());


        /** Soma os valores pagos indevidos pelo cliente **/
        var valorIndevidoMonitoramento = parseMoney(jQuery('#valorPagoIndevidoMonitoramentoTotal').val());
        var valorIndevidoLocacao = parseMoney(jQuery('#totalMensalidadeIndevido').val());
        var somaIndevido = valorIndevidoMonitoramento + valorIndevidoLocacao

        //Calcula o valor a pagar do cliente
        var diferencaValorMonitoriamento = parseMoney(jQuery('#valorMultaMensalidade').val()) - valorIndevidoMonitoramento;
        if (diferencaValorMonitoriamento < 0){
            jQuery("#valorMultaMensalidadeDevolver").val(Math.abs(diferencaValorMonitoriamento).toMoney());
        } else{
            jQuery("#valorMultaMensalidadeFaltante").val(diferencaValorMonitoriamento.toMoney());
        }

        var valorNaoDevolucao = parseMoney(jQuery('.valor-total-multa-nao-devolucao').val());

        // Calcula o total da rescisão
        var totalRescisao = valorTotalMulta
                          + valorTotalTaxas
                          + valorTotalFaturas
                          + valorNaoDevolucao;
        totalRescisao = totalRescisao - somaIndevido;

        jQuery('.total-rescisao').val(totalRescisao.toMoney());
    };

    /**
     * Finaliza a rescisão e valida os campos do formulário
     */
    jQuery('body').on('click', '.rescisao-finalizar', function() {
        // Remove todos os alertas e elementos marcados
        clearErrors();
        var rescisaoMotivo     = jQuery('.rescisao-motivo')
          , rescisaoStatus     = jQuery('.rescisao-status')
          , rescisaoVencimento = jQuery('.rescisao-vencimento');

        if (rescisaoMotivo.val() == '0') {
            addError(rescisaoMotivo, 'É necessário informar o motivo.');
            return;
        }

        if (rescisaoStatus.val() == '0') {
            addError(rescisaoStatus, 'É necessário informar informar o status.');
            return;
        }

        if (rescisaoVencimento.val().length == 0) {
            addError(rescisaoVencimento, 'É necessário informar informar a data de vencimento.');
            return;
        }

        // Esconde o botão Finalizar
        jQuery('#btnFinalizarRescisao').hide();

        // Recalcula o total da rescisão
        //calculaTotalRescisao();

        // Busca todas as faturas
        var faturas = [];
        jQuery('.multa-fatura').map(function() {
            var self = jQuery(this)
              , row  = self.closest('tr');

            faturas.push({
                titoid:     self.val()
              , cobravel:   (self.is(':checked')) ? 't' : 'f'
              , observacao: row.find('.multa-faturas-locacao-observacao-text').val()
              , desconto:   parseMoney(row.find('.multa-fatura-desconto').val())
            });
        });

        // Baixa as faturas que não serão cobradas e cuja observação é "Nota baixada pela Sascar"
        var faturasBaixadas = [];
        jQuery('.multa-fatura:checked').map(function() {
            var self = jQuery(this);
            var docto  = self.closest('tr').find('.multaFaturaDocto').html();
            var vecto  = self.closest('tr').find('.multaFaturaVecto').html();
            var valor  = self.closest('tr').find('.multa-fatura-valor-total').val();

            faturasBaixadas.push({
              docto: docto,
              vecto: vecto,
              valor: valor
            });
            // if (obs.match(/Nota baixada pela Sascar/)) {
            //     faturasBaixadas.push(jQuery(this).val());
            // }
        });

        // Busca as faturas cujo valor do desconto foi alterado
        var faturasDescontos = [];
        jQuery('.multa-fatura-desconto').map(function() {
            var self   = jQuery(this)
              , row    = self.closest('tr')
              , titoid = row.find('.multa-fatura').val()
              , descontoPadrao = parseMoney(row.find('.multa-fatura-desconto-padrao').val())
              , descontoAtual  = parseMoney(self.val())
              , observacao     = row.find('.multa-faturas-locacao-observacao-text').val();

            // Se o desconto for diferente, adiciona ao array de modificados
            if (descontoAtual != descontoPadrao) {
                faturasDescontos.push({
                    titoid:     titoid
                  , desconto:   descontoAtual
                  , observacao: observacao
                });
            }
        });

        // Busca os contratos que serão rescindidos
        var multasContratos = [];
        jQuery('.contrato-cliente:checked').map(function() {
            
            var self = jQuery(this)
              , row  = self.closest('tr');
            
            var monitoramentoValor = 0;

            monitoramentoValor = parseMoney(jQuery('#contrato-multa-total-' + self.val()).val());

            multasContratos.push({
                connumero:   parseInt(self.val())
              , total:       monitoramentoValor
              , meses:       parseInt(row.find('.contrato-multa-meses-faltantes').val())
              , porcentagem: parseInt(row.find('.contrato-multa-porcentagem').val())
              , multa:       parseMoney(row.find('.contrato-multa-valor').val())
              , resmfax:     row.find('.contrato-data-recisao').val()
            });
        });
    
        // Busca as porcentagens das multas de locação
        var multasLocacao = {};
        jQuery('.contrato-multa-locacao').map(function() {
            var self   = jQuery(this);
            var row    = self.closest('tr');
            var total  = parseMoney(row.find('.multa-locacao-total').val());
            var porcentagem = parseInt(row.find('.multa-locacao-porcentagem').val(),10);
            var pago = row.find('.parcela-locacao-pago').val();

            if (pago == 'S'){
                porcentagem = parseInt(row.find('.multa-locacao-porcentagem-pago').val(),10);
            }

            // Soma os valores das multas de cada contrato
            if (!multasLocacao[self.val()]) {
                multasLocacao[self.val()] = {
                    porcentagem: porcentagem
                  , total: 0.00
                };
            }

            multasLocacao[self.val()]['total'] += total;
        });
        
        // Busca as multas de retirada
        var multasRetirada = [];
        jQuery('.multa-retirada:checked').map(function() {
            var self = jQuery(this);
            var row  = self.closest('tr');
            if(self.prop('checked')){
                multasRetirada.push({
                    contrato:         row.find('.multa-retirada-termo').val(),
                    item:             row.find('.multa-retirada-item').val(),
                    valorRetirada:    row.find('.multa-retirada-valorRetirada').val(),
                    obroidretirada:   row.find('.multa-retirada-obroidretirada').val(),
                    valor:            parseMoney(row.find('.multa-retirada-valor').val())
                });
            }
        });

        // Busca as multas de não retirada
        var multasNaoRetirada = [];
        jQuery('.multa-nao-retirada:checked').map(function() {
            var self = jQuery(this);
            var row  = self.closest('tr');
            if(self.prop('checked')){
                multasNaoRetirada.push({
                    contrato:         row.find('.multa-nao-retirada-termo').val(),
                    item:             row.find('.multa-nao-retirada-item').val(),
                    valorRetirada:    row.find('.multa-nao-retirada-valorRetirada').val(),
                    obroidretirada:   row.find('.multa-nao-retirada-obroidretirada').val(),
                    valor:            parseMoney(row.find('.multa-nao-retirada-valor').val())
                });
            }
        });
        
        var notas = [];
        jQuery('.notas-fiscais').each(function(i, val){
          notas.push(jQuery(this).val());
        });
        
        // Serializa valores do formulário
        var post = jQuery.param({
            clioid:            jQuery('#clioid').val()
          , faturas_baixadas:  faturasBaixadas
          , faturas_descontos: faturasDescontos
          , total_rescisao:    parseMoney(jQuery('.total-rescisao').val())
          , restaxa_remocao:   parseMoney(jQuery('.valor-total-taxas').val())
          , resmvl_nao_devolucao: parseMoney(jQuery('.valor-total-multa-nao-devolucao').val())
          , resmulta:          parseMoney(jQuery('.valor-total-multa').val())
          , resmmrescoid:      jQuery('.rescisao-motivo').val()
          , resmstatus:        jQuery('.rescisao-status').val()
          , contratos:         multasContratos
          , multas_locacao:    multasLocacao
          , faturas:           faturas
          , multas_retirada:   multasRetirada
          , multas_nao_retirada:   multasNaoRetirada
          , vencimento:        jQuery('.rescisao-vencimento').val()
          , observacao_carta:  jQuery('.observacao_carta:checked').val()
          , notas:  notas.join(',')
          , email:   jQuery('#email').val()
          , notasFiscaisBaixa: notasFiscaisBaixa
          , retiradaEquipamentos: multasRetirada
          , arrContratos: arrContratos
        });
        
        // Mostra loader
        jQuery('.container-finalizacao-loader').show();

        // jQuery.ajaxSetup({'beforeSend' : function(xhr) {xhr.overrideMimeType('charset=ISO-8859-1'); }});
        
        // Posta os dados via AJAX
        jQuery.ajax({
          type: 'POST',
          url: 'fin_rescisao.php?acao=finalizarRescisao',
          data: post,
          dataType: 'json',
          success: (function(r) {

            if(!r){
              alert("Ocorreu um erro ao gerar a rescis\u00e3o.");
              jQuery('.container-finalizacao-loader').hide();
              return false;
            }

            if(r.msgRetorno){
              alert(r.msgRetorno);
              jQuery('.container-finalizacao-loader').hide();
            } else if(r.resmoid) {
              
              var urlHref = 'fin_rescisao.php?acao=imprimir&resmoid=' + r.resmoid + '&titven=' + r.titven + '&email=' + r.email;

              if(r.idsBaixa != null){
                urlHref += '&idsbaixa=' + r.idsBaixa;
              }

              jQuery(location).attr('href', urlHref);
              
            } else {
              alert("Ocorreu um erro ao gerar a rescis\u00e3o.");
              jQuery('.container-finalizacao-loader').hide();
            }

          }),
          error : (function(r) {
            
            alert("Ocorreu um erro ao gerar a rescis\u00e3o.");
            jQuery('.container-finalizacao-loader').hide();
            
          })
        });
    });

    /**
     * Abre janela para impressão de segunda via de carta de rescisão
     */
    jQuery('.imprimir-segunda-via').click(function() {
        jQuery('.imprimir-segunda-via-check:checked').map(function() {
            var self    = jQuery(this)
              , clioid  = self.data('clioid')
              , resmoid = self.data('resmoid');

            window.open('fin_rescisao.php?acao=imprimir&segunda_via=true&resmoid=' + resmoid);
        });
    });


    jQuery("#data_inicial").periodo("#data_final");
    jQuery("#data_inicial").mask("99/99/9999");
    jQuery("#data_final").mask("99/99/9999");
});

// Helper de detecção de browser features
function css_browser_selector(u){var ua=u.toLowerCase(),is=function(t){return ua.indexOf(t)>-1},g='gecko',w='webkit',s='safari',o='opera',m='mobile',h=document.documentElement,b=[(!(/opera|webtv/i.test(ua))&&/msie\s(\d)/.test(ua))?('ie ie'+RegExp.jQuery1):is('firefox/2')?g+' ff2':is('firefox/3.5')?g+' ff3 ff3_5':is('firefox/3.6')?g+' ff3 ff3_6':is('firefox/3')?g+' ff3':is('gecko/')?g:is('opera')?o+(/version\/(\d+)/.test(ua)?' '+o+RegExp.jQuery1:(/opera(\s|\/)(\d+)/.test(ua)?' '+o+RegExp.jQuery2:'')):is('konqueror')?'konqueror':is('blackberry')?m+' blackberry':is('android')?m+' android':is('chrome')?w+' chrome':is('iron')?w+' iron':is('applewebkit/')?w+' '+s+(/version\/(\d+)/.test(ua)?' '+s+RegExp.jQuery1:''):is('mozilla/')?g:'',is('j2me')?m+' j2me':is('iphone')?m+' iphone':is('ipod')?m+' ipod':is('ipad')?m+' ipad':is('mac')?'mac':is('darwin')?'mac':is('webtv')?'webtv':is('win')?'win'+(is('windows nt 6.0')?' vista':''):is('freebsd')?'freebsd':(is('x11')||is('linux'))?'linux':'','js']; c = b.join(' '); h.className += ' '+c; return c;};
css_browser_selector(navigator.userAgent);

// Remove o evento do backspace
jQuery(document).unbind('keydown').bind('keydown', function (event) {
    var doPrevent = false;
    if (event.keyCode === 8) {
        var d = event.srcElement || event.target;
        if ((d.tagName.toUpperCase() === 'INPUT' && (d.type.toUpperCase() === 'TEXT' || d.type.toUpperCase() === 'PASSWORD'))
             || d.tagName.toUpperCase() === 'TEXTAREA') {
            doPrevent = d.readOnly || d.disabled;
        }
        else {
            doPrevent = true;
        }
    }

    if (doPrevent) {
        event.preventDefault();
    }
});

function forceCalc(){
    //Dispara funcao da linha 494 após mudar a data da solicitação
    jQuery('.contrato-multa-meses.contrato-multa-recalcula.mask-numbers').change();
}