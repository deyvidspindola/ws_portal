jQuery(document).ready(function(){

    //faz o tratamento para os campos de perídos
    jQuery("#dataInicial").periodo("#dataFinal");

    /*
     * Ações do botão Pesquisar
     */
   jQuery("#btn_pesquisar").click(function(){
       jQuery('#mensagem_erro').hide();
       jQuery('#mensagem_alerta').hide();
       jQuery('#mensagem_sucesso').hide();
       jQuery('#acao').val('pesquisar');
   });

   /*
    * Ações do botão Gerar Arquivo
    */
   jQuery("#btn_gerar_arquivo").click(function(){

        jQuery("#baixarXls").addClass('invisivel');
        jQuery("#loader_xls").removeClass('invisivel');

        jQuery.ajax({
            url: 'rel_equip_instalado_sem_subscription.php',
            type: 'POST',
            data: {
                acao: 'gerarArquivoCSV'
            },
            success: function(data) {

                if(data) {
                    nomeArquivo = String(jQuery.trim(data));
                    nomeArquivo = nomeArquivo.replace('/var/www/docs_temporario/', '');

                    jQuery("#loader_xls").addClass('invisivel');
                    jQuery("#baixarXls a").attr("href","download.php?arquivo=" + jQuery.trim(data));
                    jQuery("#baixarXls span").html(nomeArquivo);
                    jQuery("#baixarXls").removeClass('invisivel');
                }

            }
        });
   })
});