jQuery(document).ready(function(){
    
    //faz o tratamento para os campos de perídos 
    jQuery("#cfodt_inclusao_de").periodo("#cfodt_inclusao_ate");
    jQuery("#cfodt_avaliacao_de").periodo("#cfodt_avaliacao_ate");

    jQuery("#contrato_pesquisa").mask('9?999999999',{placeholder:''});
    jQuery("#bt_retornar").attr('id','bt_retornar_pendente');

    jQuery("#bt_retornar_pendente").click(function(){
    	window.location.href="fin_credito_futuro_pendentes.php?acao=pesquisar";
    });

    jQuery("#bt_excluir, #bt_encerrar").remove();
    jQuery("#bt_concluir").parent().remove();

    if (pode_avaliar == '1'){
        jQuery("#bloco_acoes_visualizar #bt_retornar_pendente").before('<button class="bt_aprovar aprovar_interno" type="button">Aprovar</button><button class="bt_reprovar reprovar_interno" type="button">Reprovar</button>');
    }
    

    jQuery("form#formulario_editar input, form#formulario_editar select, form#formulario_editar textarea, form#formulario_editar radio, form#formulario_editar checkbox").attr('disabled','disabled');

    jQuery('#marcar_todos').change(function(){
        if (jQuery(this).is(':checked')) {
             jQuery("#bt_aprovar_massa, #bt_reprovar_massa").removeAttr('disabled');
        } else {
             jQuery("#bt_aprovar_massa, #bt_reprovar_massa").attr('disabled','disabled');
        }
    });
    
    jQuery(".excluir_item").change(function(){
        if (jQuery(".excluir_item:checked").length == 0 ){
            jQuery("#bt_aprovar_massa, #bt_reprovar_massa").attr('disabled','disabled');
        }else {
            jQuery("#bt_aprovar_massa, #bt_reprovar_massa").removeAttr('disabled');
           
        } 
    });


    jQuery(".bt_aprovar").click(function(event){
        event.preventDefault()
        if (confirm('Deseja aprovar o(s) item(ns) marcado(s)?')) {

            if (jQuery(this).hasClass('aprovar_interno')) {
                var credito_futuro_id = jQuery("form#formulario_editar #cfooid").val();
                window.location.href = 'fin_credito_futuro_pendentes.php?acao=aprovar&id='+ credito_futuro_id +'';
            } else {
                window.location.href = jQuery(this).attr('href');
            }
            
        }
    });


    jQuery('.bt_reprovar').click(function(){

        if (jQuery(this).hasClass('reprovar_interno')) {
            var cfooid = jQuery("form#formulario_editar #cfooid").val();
        } else {
            var cfooid = jQuery(this).attr('data-cfooid');
        }
        
        jQuery("#dialog-reprovar-credito-futuro").dialog({
            autoOpen: false,
            minHeight: 300 ,
            maxHeight: 700 ,
            width: 440,
            modal: true,
            create: function (event, ui) {

                jQuery("form#form_reprovar #cfooid").val(cfooid);
               
                if(navigator.appName.indexOf('Internet Explorer') != -1 && document.compatMode == 'BackCompat') {                            
                    jQuery("#dialog-reprovar-credito-futuro").prev().css('width','455px');
                    jQuery(".ui-dialog-titlebar-close .ui-button-text").remove();
                                jQuery(".ui-dialog-titlebar-close").css('width','20px');
                                jQuery(".ui-dialog-titlebar-close").css('height','20px');
                }
                            
            },
            buttons: {
                "Confirmar": function() {
                    
                    jQuery.fn.limpaMensagens();
                    
                    var creditoFuturoId = jQuery.trim(jQuery("#cfooid").val());
                    var justificativa = jQuery.trim(jQuery("#justificativa_reprova").val());
                    
                    if (justificativa == '') {
                        jQuery("#reprovar_mensagem").html('<div class="mensagem alerta">A justificativa é uma informação obrigatória.</div>')
                        setErrors('justificativa_reprova');
                        showFormErros(dados);
                        return false;
                    }
                
                    jQuery("#form_reprovar").submit();
                    
                },
                "Cancelar": function() {
                    jQuery("#dialog-reprovar-credito-futuro").dialog('close');
                    jQuery("textarea[name='justificativa']").val('');
                }
            },
            "Cancelar": function() {
                jQuery("textarea[name='justificativa']").val('');
                jQuery("#dialog-reprovar-credito-futuro").dialog('close');
            }
        }).dialog('open');
    });



jQuery("#bt_aprovar_massa").click(function(){

    if (confirm('Deseja aprovar o(s) item(ns) marcado(s)?')) {
        jQuery("#form_listagem_pesquisa input[name='acao']").val('aprovarMassa');
        jQuery("#form_listagem_pesquisa").submit();
    }

});

jQuery("#btn_gerar_xls").click(function(){
    jQuery("form#form #acao").val('gerarXls');
    jQuery("form#form").submit();
});

});