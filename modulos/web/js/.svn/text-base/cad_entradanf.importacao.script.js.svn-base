jQuery( document ).ready(function() {

  jQuery("#btnProcessaCVS").click(function (e) {
    // SERANDO CAMPOS QUE SERA IJETADO INFORMACOES
    var progresso = jQuery("#progresso");
    var tempo = jQuery("#tempo");
    var fase = jQuery("#fase");
    var tempoPercentual = jQuery("#tempoPercentual");
    var btnProcessaCVS = jQuery("#btnProcessaCVS");
    var progressoProcessamentoCsv = jQuery(".progressoProcessamentoCsv");
    var resumoImportacao = jQuery("#resumoImportacao");
    var progressoData = "";
    var jsonProgresso = "";




    var dataForm = new FormData();
    dataForm.append('obrtipo_doc', jQuery("#obrgrupo_doc option:selected").val());
    dataForm.append('obrempresa', jQuery("#obrempresa option:selected").val());
    dataForm.append('arquivo', jQuery("#arquivoCSV").prop("files")[0]);
    dataForm.append('entiitmoid', jQuery("#entiitmoid").val());

    dataForm.append('estabelecimento', jQuery("#obrestabelecimento").val());
    dataForm.append('fornecedoroid', jQuery("#fornecedoroid").val());

    dataForm.append('acao', 'importar');


    // TIRANDO O EVENTO DO SUBMIT
    e.preventDefault();

    resumoImportacao.hide();

    // CHAMANDO VIA AJAX POST IMPORTACAO CSV
    jQuery.ajax({
      type: 'POST',
      url: 'cad_entradanf.php',
      data: dataForm,
      processData: false,
      contentType: false,
      xhrFields: {
        onprogress: function (e){
         var data = e.currentTarget.response;



         // ATUALIZA BARRA DE PROGRESSO
         if ( data.lastIndexOf('|') >= 0 )
         {
           // DESATIVA BOTAO DE ENVIO
           btnProcessaCVS.prop('disabled',true);

           // EXIBE PROGRESSBAR
           progressoProcessamentoCsv.fadeIn('slow');

           // CONVERTE RETORNO PARA JSON
           progressoData = data.slice((data.lastIndexOf('|') + 1));
           jsonProgresso = jQuery.parseJSON(progressoData);

           // INJETA DADOS NO HTML
           if (jsonProgresso.progresso)
           {
             fase.html(jsonProgresso.fase);
             tempoPercentual.html( jsonProgresso.progresso + '%');
             progresso.css('width', jsonProgresso.progresso + '%');
             tempo.html(jsonProgresso.tempo);
           }
          }
        }
      },
      success: function (data) {

        // CASO SEJA MUITO RAPIDO COLOCA A BARRA DE PROGRESSO EM 100%
        tempoPercentual.html( '100%');
        progresso.css('width', '100%');

        // EXIBE PROGRESSBAR
        progressoProcessamentoCsv.fadeOut('slow', function(){
          // DESATIVA BOTAO DE ENVIO
          btnProcessaCVS.prop('disabled',false);

          // CONVERTE RETORNO PARA JSON
          var jsonText = data.slice((data.lastIndexOf('|') + 1));
          var json = jQuery.parseJSON(jsonText);

          //console.log(json);
          if (json.arquivo_retorno)
          {
            if (json.total_importados > 0)
              var msg = 'Importação falhou porque existem erros em <B>'+json.total_erros+'</b> iten(s) do arquivo. <a href="download.php?arquivo='+json.arquivo_retorno+'">Clique aqui</a>  para baixar o arquivo de resalvas.';
            else
              var msg = 'Importação falhou porque existem erros em todos os itens do arquivo. <a href="download.php?arquivo='+json.arquivo_retorno+'">Clique aqui</a>  para baixar o arquivo de resalvas.';
          }
          else
          {
              var msg = 'Importação finalizada com sucesso sem erros, total de <B>'+json.total_importados+'</b> itens importados</b>';
              //jQuery("#obrtotal").val(json.total_valor);
          }

          resumoImportacao.html(msg);
          resumoImportacao.fadeIn('slow');

          if (json.total_importados > 0)
          {
            // atualizando listagem de itens
            xajax_monta_quadro_produtos();

            // exibe listagem de itens
            jQuery('.fix_importacaoCSVFile').fadeIn('slow');
            jQuery('.fix_imoprtacaoCSVImputs').fadeIn('slow');
          }

        });


      },
      error: function (xhr, ajaxOptions, thrownError) {
        // TRATAR ERROS
        //alert(thrownError);
        alert(xhr.getResponseHeader("statusText"));
      },

   });
  });

  //Checkbox de importação de documento
  jQuery('#checkImportacaoCSV').click(function(){
    if (jQuery('#checkImportacaoCSV').is(':checked')){
      //oculta campos de input de itens do documento e exibe form de importação
      jQuery('.fix_imoprtacaoCSVImputs').hide();
      jQuery('.fix_importacaoCSVFile').show();
      jQuery("#moldura_suspensa").css('display','none');

    }else{
      //oculta formulario de importação e exibe campos  de input de itens do documento
      jQuery('.fix_importacaoCSVFile').hide();
      jQuery('.fix_imoprtacaoCSVImputs').show();
      jQuery("#moldura_suspensa").css('display','block');
    }
  })

});
function excluirItensDocumento()
{
    jQuery.ajax({
      type: 'POST',
      url: 'cad_entradanf.php',
      data: 'acao=remolver_todos_itens_importados',
      success: function (data) {
            // atualizando listagem de itens
            xajax_monta_quadro_produtos();
            removeDiv('mess');
            criaAlerta('Todos os Itens/produtos excluído com sucesso.');
      }
    });
}
function habilitaImportacaoArquivo(id_grupo_documento)
{
    if (id_grupo_documento == 1 || id_grupo_documento == 2 || id_grupo_documento == 3)
    {
        jQuery('.fix_checkImportacaoCSVLocal').show();
        if (jQuery('#checkImportacaoCSV').is(':checked')){
            jQuery('.fix_imoprtacaoCSVImputs').hide();
            jQuery('.fix_importacaoCSVFile').show();
            jQuery("#moldura_suspensa").css('display','none');
        }


    }else{
        jQuery('.fix_checkImportacaoCSVLocal').hide();
        jQuery('.fix_importacaoCSVFile').hide();
        jQuery('.fix_imoprtacaoCSVImputs').show();
        jQuery("#moldura_suspensa").css('display','block');
    }


}
