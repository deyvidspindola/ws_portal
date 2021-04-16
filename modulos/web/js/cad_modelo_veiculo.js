jQuery(document).ready(function(){

  var msg_campos_obrigatorios = "Existem campos obrigatórios não preenchidos.";
  var msg_sucesso_excluir     = "Registro excluído  com sucesso.";
  var msg_sucesso_editar      = "Registro alterado com sucesso.";
  var msg_erro                = "Houve um erro no processamento dos dados.";

  //botão novo
  jQuery("#bt_novo").click(function(){
    window.location.href = "modelo.php?acao=cadastrar&retModelo=" + jQuery('#retModelo').val();
  });

  //botão voltar
  jQuery("#bt_voltar").click(function(){
    window.location.href = "modelo.php?retModelo=" + jQuery('#retModelo').val();
  });

   //botão Gravar
  jQuery('body').on('click', '#bt_gravar', function() {

    isCamposValidos = validarCamposObrigatorios();

      if( ! isCamposValidos ) {
        jQuery('#mensagem_alerta').html(msg_campos_obrigatorios);
        jQuery('#mensagem_alerta').show();
      } else {

        isContinua = verificarDuplicidade();

        if(isContinua) {
          jQuery('form').submit();
        }

      }
  });

  //botão Gravar
  jQuery('body').on('click', '#bt_pesquisar', function() {

      isCamposValidos = validarCamposObrigatorios();

      if( ! isCamposValidos ) {
        jQuery('#mensagem_alerta').html(msg_campos_obrigatorios);
        jQuery('#mensagem_alerta').show();
      } else {
        jQuery('form').submit();
      }
  });

  //botão Retornar
  jQuery('body').on('click', '#bt_retornar', function() {
      window.location.href = jQuery('#url_retorno').val();
  });

  //botão "+"
  jQuery('body').on('click', '#bt_nova_marca', function() {

     modelo = jQuery('#mlooid option:selected').val();
     window.location.href = 'marca.php?mcaoid=&retMarca={modelo.php?acao=cadastrar&mlooid='+modelo+'}';
  });

  //Botao COPIAR
  jQuery('body').on('click', '#bt_copiar', function() {

     mlooid = jQuery('#copiar option:selected').val();

     jQuery.ajax({
          url: 'modelo.php',
          type: 'POST',
          data: {
              acao: 'recuperarAcessoriosAJAX',
              mlooid: mlooid
          },
          success: function(data) {

            try{

              data = JSON.parse(data);

              if(data) {

                  if (data.length > 0) {

                    var lista_acessorios = Array();

                    //Recupera os acessorios que ja estao na grid
                    jQuery('.listagem tbody tr').each(function(i, valor){
                         lista_acessorios[i] = jQuery(this).data('acessorio');
                    });

                    jQuery.each(data, function(i, val) {

                      check_cliente    = '';
                      check_seguradora = '';
                      valor_cliente    = 'f';
                      valor_seguradora = 'f';
                      acessorio_id     = val.acessorio_id;
                      campos           = Array();

                      //Se o acessorio ja esta na GRID entao vai para o proximo resultado do banco
                      if( jQuery.inArray( parseInt(acessorio_id), lista_acessorios ) >=  0 ){
                        return;
                      }

                      if( val.valor_cliente == 't' ) {
                        check_cliente = '<img class="icone" src="images/icones/t1/v.png" title="Sim">';
                        valor_cliente = 't';
                      }

                      if ( val.valor_seguradora ) {
                        check_seguradora = '<img class="icone" src="images/icones/t1/v.png" title="Sim">';
                        valor_seguradora = 't';
                      }

                      campos['acessorio_id']     = acessorio_id;
                      campos['acessorio']        = val.acessorio;
                      campos['ano_inicial']      = (val.ano_inicial = 'null') ? '' : val.ano_inicial;
                      campos['ano_final']        = (val.ano_final   = 'null') ? '' : val.ano_final;
                      campos['check_cliente']    = check_cliente;
                      campos['check_seguradora'] = check_seguradora;
                      campos['valor_cliente']    = valor_cliente;
                      campos['valor_seguradora'] = valor_seguradora;

                      montarHTML( campos );

                      jQuery('#bloco_itens table').append(html);

                      jQuery('#mlaiobroid option').each(function(id, opcao) {
                          if (opcao.value == acessorio_id) {
                              jQuery(opcao).prop('disabled', true);
                              return false;
                          }
                      });

                    });

                    aplicarCorLinha();
                    jQuery('#mlaiobroid').val('');

                  }

              }

            }catch(erro) {
              jQuery('#mensagem_erro').text(msg_erro);
              jQuery('#mensagem_erro').show();
            }

          }
        });
  });

  //Combo Marca / Modelo
  jQuery('#form-cadastro').on('change', '#copiar', function() {

    if( jQuery(this).val() == '' ) {
      jQuery('#bt_copiar').addClass('desabilitado');
    } else{
      jQuery('#bt_copiar').removeClass('desabilitado');
    }
  });

  //Combo MARCA
  jQuery('#form-modelo').on('change', '#mlomcaoid', function() {
        popularComboModelo();
  });

  //Check Marcas Inativas
  jQuery('#form-modelo').on('click', '#marca_inativa', function() {

      jQuery("#mlooid").html("<option value=''>Escolha</option>");

        if( !jQuery('#marca_inativa').prop('checked') ) {
          jQuery('#modelo_inativo').removeProp('checked');
          popularComboModelo();
        }

        popularComboMarca();
  });

  //Check Modelos Inativos
  jQuery('#form-modelo').on('click', '#modelo_inativo', function() {
        popularComboModelo();
  });

  //Combo Tipo
  jQuery('#form-cadastro').on('change', '#mlotipveioid', function() {

        mlotipveioid = jQuery('#mlotipveioid option:selected').val();

        jQuery.ajax({
          url: 'modelo.php',
          type: 'POST',
          data: {
              acao: 'recuperarSubTipoVeiculoAjax',
              mlotipveioid: mlotipveioid
          },
          success: function(data) {

            try{

              data = JSON.parse(data);

              if(data) {

                  var combo = "<option value=''>Escolha</option>";

                  if (data.length > 0) {
                      jQuery.each(data, function(i, val) {
                          combo += "<option value='" + val.chave + "'>" + val.valor + "</option>";
                      });
                  }

                  jQuery("#mlovstoid").html(combo);
              }

            }catch(erro) {
              jQuery('#mensagem_erro').text(msg_erro);
              jQuery('#mensagem_erro').show();
            }

          }
        });
  });

  //Combo Marca
  jQuery('#form-cadastro').on('change', '#mlomcaoid', function() {

        mlomcaoid = jQuery('#mlomcaoid option:selected').val();

        jQuery.ajax({
          url: 'modelo.php',
          type: 'POST',
          data: {
              acao: 'recuperarMarcaFamiliaAjax',
              mlomcaoid: mlomcaoid
          },
          success: function(data) {

            try{

              data = JSON.parse(data);

              if(data) {

                  var combo = "<option value=''>Escolha</option>";

                  if (data.length > 0) {
                      jQuery.each(data, function(i, val) {
                          combo += "<option value='" + val.chave + "'>" + val.valor + "</option>";
                      });
                  }

                  jQuery("#mlomcfoid").html(combo);
              }
          }catch(erro) {
              jQuery('#mensagem_erro').text(msg_erro);
              jQuery('#mensagem_erro').show();
            }

          }
        });
  });

  //Check valvula
  jQuery('#form-cadastro').on('click', '#mlovalvula', function() {

        if( jQuery('#mlovalvula').prop('checked') ) {
          jQuery('#valvula').removeClass('invisivel');
          jQuery('#mlovlmoid1').addClass('obrigatorio');

          jQuery('#mlovlmoid1').val('');
          jQuery('#mlovlmoid2').val('');
          jQuery('#mlovlmoid3').val('');

        } else {
          jQuery('#valvula').addClass('invisivel');
          jQuery('#mlovlmoid1').removeClass('obrigatorio');
        }
  });

  //Tratamento de campos numericos
  jQuery('body').on('keyup blur', '.numerico', function() {
      jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
  });


  //Tratamento de campos texto
  jQuery('body').on('keyup blur', '.texto', function() {

    valor = jQuery(this).val().replace(/[^a-z A-Z 0-9 \-\/() .]/g, '');
    valor = valor.replace('  ', '');
    jQuery(this).val( valor );
  });

  //Tratamento de campos texto (Caixa Alta)
  jQuery('body').on('keyup blur', '.upper', function() {

    valor = jQuery(this).val().toUpperCase();
    jQuery(this).val( valor );
  });

  //Botao ADICIONAR
  jQuery('body').on('click', '#bt_adicionar', function() {

        jQuery('#mensagem_alerta_item').hide();
        jQuery('#mlaiano_inicial').removeClass('erro');
        jQuery('#mlaiano_final').removeClass('erro');
        jQuery('#mlaiobroid').removeClass('erro');

        acessorio_id = jQuery('#mlaiobroid option:selected').val();


        if ( acessorio_id == '' ) {

            jQuery('#mlaiobroid').addClass('erro');
            jQuery('#mlaiobroid').val('');

            jQuery('#mensagem_alerta_item').text(msg_campos_obrigatorios);
            jQuery('#mensagem_alerta_item').show();

        } else {

            isAnoValido = validarIntervaloAno();

            if(! isAnoValido ) {
              jQuery('#mlaiano_inicial').addClass('erro');
              jQuery('#mlaiano_final').addClass('erro');
              jQuery('#mensagem_alerta_item').text("O ano inicial não pode ser maior que o ano final.");
              jQuery('#mensagem_alerta_item').show();

            } else {

              acessorio          = jQuery('#mlaiobroid option:selected').text();
              ano_inicial        = jQuery('#mlaiano_inicial option:selected').val();
              ano_final          = jQuery('#mlaiano_final option:selected').val();
              check_cliente      = '';
              check_seguradora   = '';
              valor_cliente      = 'f';
              valor_seguradora   = 'f';
              campos = Array();

              if( jQuery('#mlaiinstala_cliente').prop('checked') ) {
                check_cliente = '<img class="icone" src="images/icones/t1/v.png" title="Sim">';
                valor_cliente = 't';
              }

              if ( jQuery('#mlaiinstala_seguradora').prop('checked') ) {
                check_seguradora = '<img class="icone" src="images/icones/t1/v.png" title="Sim">';
                valor_seguradora = 't';
              }

              campos['acessorio_id'] = acessorio_id;
              campos['acessorio'] = acessorio;
              campos['ano_inicial'] = ano_inicial;
              campos['ano_final'] = ano_final;
              campos['check_cliente'] = check_cliente;
              campos['check_seguradora'] = check_seguradora;
              campos['valor_cliente'] = valor_cliente;
              campos['valor_seguradora'] = valor_seguradora;

              montarHTML( campos );

              jQuery('#bloco_itens table').append(html);

              //Aplica a classe de corres nas linhas
              aplicarCorLinha();

              jQuery('#mlaiobroid option:selected').prop('disabled', 'true');
              jQuery('#mlaiobroid').val('');

            }

        }
  });

  /*
  * Acoes do icone excluir acessorios
  */
  jQuery("table").on('click','.remover',function(event) {

        event.preventDefault();

        elemento = this;

        jQuery("#msg_dialogo_exclusao_item").dialog({
          title: "Confirmação",
          resizable: false,
          modal: true,
          buttons: {
            "Sim": function() {
              jQuery( this ).dialog( "close" );

              jQuery(elemento).parent().parent().remove();
              acessorio = jQuery(elemento).parent().parent().data('acessorio');
              aplicarCorLinha();

              jQuery('#mlaiobroid option').each(function(id, opcao) {
                  if (opcao.value == acessorio) {
                      jQuery(opcao).removeProp('disabled');
                      return false;
                  }
              });

            },
            "Não": function() {
              jQuery( this ).dialog( "close" );
            }
          }
        });
  });


  /*
  * Acoes do icone excluir registro
  */
  jQuery("table").on('click','.excluir',function(event) {

      event.preventDefault();

      mlooid = jQuery(this).data('mlooid');

      jQuery("#msg_dialogo_exclusao").dialog({
        title: "Confirmação de Exclusão",
        resizable: false,
        modal: true,
        buttons: {
          "Sim": function() {

            jQuery( this ).dialog( "close" );

              jQuery.ajax({
                  url: 'modelo.php',
                  type: 'POST',
                  data: {
                      acao: 'excluir',
                      mlooid: mlooid
                  },
                  success: function(data) {

                    try{

                      if(data == 'OK') {
                          jQuery('#mensagem_sucesso').html(msg_sucesso_excluir);
                          jQuery('#mensagem_sucesso').show();
                          jQuery('#linha_' + mlooid).remove();
                          aplicarCorLinha();

                      } else {
                          jQuery('#mensagem_erro').html(msg_erro);
                          jQuery('#mensagem_erro').show();
                      }

                   } catch(erro) {
                      jQuery('#mensagem_erro').html(msg_erro);
                      jQuery('#mensagem_erro').show();
                   }

                  }
              });

          },
          "Não": function() {
            jQuery( this ).dialog( "close" );
          }
        }
      });
  });

  /*
  * Acoes do icone editar
  */
  jQuery("table").on('click','.editar',function(event) {

      event.preventDefault();
      mlooid = jQuery(this).data('mlooid');
      window.location.href = "modelo.php?acao=editar&mlooid=" + mlooid + "&retModelo=" + jQuery('#retModelo').val();;
  });


  function popularComboModelo() {

      mlomcaoid = jQuery('#mlomcaoid option:selected').val();

      if(mlomcaoid == ''){
        jQuery("#mlooid").html("<option value=''>Escolha</option>");
         return true;
      }

      var status = jQuery('#modelo_inativo').prop('checked');
      status = status ? 'I' : 'A';

         jQuery.ajax({
          url: 'modelo.php',
          type: 'POST',
          data: {
              acao: 'recuperarModelosAjax',
              mlomcaoid: mlomcaoid,
              modelo_inativo: status
          },
          success: function(data) {

              data = JSON.parse(data);

              if(data) {

                  var combo = "<option value=''>Escolha</option>";

                  if (data.length > 0) {
                      jQuery.each(data, function(i, val) {
                          combo += "<option value='" + val.chave + "'>" + val.valor + "</option>";
                      });
                  }

                  jQuery("#mlooid").html(combo);
              }

          }
      });
  }

  function popularComboMarca() {

    var status = jQuery('#marca_inativa').prop('checked');
    status = (status === true) ? 'I' : 'A';

       jQuery.ajax({
        url: 'modelo.php',
        type: 'POST',
        data: {
            acao: 'recuperarMarcasAjax',
            marca_inativa: status
        },
        success: function(data) {

          try{

            data = JSON.parse(data);

            if(data) {

                var combo = "<option value=''>Escolha</option>";

                if (data.length > 0) {
                    jQuery.each(data, function(i, val) {
                        combo += "<option value='" + val.chave + "'>" + val.valor + "</option>";
                    });
                }

                jQuery("#mlomcaoid").html(combo);
            }

        } catch(erro) {
          jQuery('#mensagem_erro').text(msg_erro);
          jQuery('#mensagem_erro').show();
        }

        }
    });
  }

  function aplicarCorLinha(){

    var cor = '';

    //remove cores
    jQuery('.listagem table tr').removeClass('par');
    jQuery('.listagem table tr').removeClass('impar');


    //aplica cores
    jQuery('.listagem table tr').each(function(){
        cor = (cor == "par") ? "impar" : "par";
        jQuery(this).addClass(cor);
    });
  }

  function validarIntervaloAno() {

   anoFinal = jQuery('#mlaiano_final option:selected').val();
   anoInicial = jQuery('#mlaiano_inicial option:selected').val();

   if(anoInicial != '' && anoFinal != '') {

      if (anoInicial <= anoFinal) {
        return true;
      } else {
       return false;
      }
    } else {
      return true;
    }
  }

  function validarCamposObrigatorios() {

      var erros = 0;

      jQuery('.obrigatorio').each(function(id,valor){

       elemento = jQuery('#'+valor.id);
       elemento.removeClass('erro');

        if(jQuery.trim(elemento.val()) == '') {

              elemento.addClass('erro');
              erros++;
          }
      });

      if(erros > 0){
          return false;
      } else {
          return true;
      }
  }

  function verificarDuplicidade() {

     mlooid = jQuery('#mlooid').val();
     mlomodelo = jQuery('#mlomodelo').val();
     mlomcaoid = jQuery('#mlomcaoid option:selected').val();
     var retorno = false;

      jQuery.ajax({
        url: 'modelo.php',
        type: 'POST',
        async: false,
        data: {
            acao: 'verificarDuplicidadeAJAX',
            mlooid: mlooid,
            mlomodelo: mlomodelo,
            mlomcaoid: mlomcaoid
        },
        success: function(data) {

           try{

              if(data == 'S') {
                  jQuery('#mensagem_alerta').html('Já existe um modelo com essa descrição associado à marca selecionada.');
                  jQuery('#mensagem_alerta').show();
              } else if(data == 'N') {
                  retorno = true;
              } else {
                  jQuery('#mensagem_erro').html(msg_erro);
                  jQuery('#mensagem_erro').show();
              }

           } catch(erro) {
              jQuery('#mensagem_erro').html(msg_erro);
              jQuery('#mensagem_erro').show();
           }

        }
      });
      return retorno;
  }

  function montarHTML( campos ) {

    html = '<tr data-acessorio="'+campos['acessorio_id']+'" class="">';
      html += '<td class="esquerda">' + campos['acessorio'];
        html += '<input id="mlaioid" name="mlaioid[]" type="hidden" value="">';
        html += '<input id="mlaiobroid" name="mlaiobroid[]" type="hidden" value="'+campos['acessorio_id']+'">';
      html += '</td>';
      html += '<td class="direita">' +campos['ano_inicial'];
        html += '<input id="mlaiano_inicial" name="mlaiano_inicial[]" type="hidden" value="'+campos['ano_inicial']+'">';
      html += '</td>';
      html += '<td class="direita">' + campos['ano_final'];
        html += '<input id="mlaiano_final" name="mlaiano_final[]" type="hidden" value="'+campos['ano_final']+'">';
      html += '</td>';
        html += '<td class="centro">' + campos['check_cliente'];
        html += '<input id="mlaiinstala_cliente" name="mlaiinstala_cliente[]" type="hidden" value="'+ campos['valor_cliente']+'">';
      html += '</td>';
        html += '<td class="centro">' + campos['check_seguradora'];
        html += '<input id="mlaiinstala_seguradora" name="mlaiinstala_seguradora[]" type="hidden" value="'+campos['valor_seguradora']+'">';
      html += '</td>';
      html += '<td class="centro">';
        html += '<img class="icone remover hand" src="images/icon_error.png" title="Excluir">';
      html += '</td>';
    html += '</tr>';
  }

});