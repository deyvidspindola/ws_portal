jQuery(document).ready(function() {
    //Botão novo
    jQuery("#bt_novo").click(function() {
        window.location = "cad_acao_motivo.php?acao=cadastrar";
    });

    //botao voltar
    jQuery("#bt_cancelar").click(function() {
        window.location = "cad_acao_motivo.php";
    });

    //botao editar
    jQuery("a.editar").click(function() {
        var id = jQuery(this).attr('rel');
        window.location = "cad_acao_motivo.php?acao=editar&aoamoid=" + id;
    });

    //botão excluir motivo
    jQuery(".deletarMotivo").click(function(event) {
        event.preventDefault();
        
        if (confirm('Deseja realmente excluir o Motivo?')){
            var motivoId = jQuery.trim(jQuery(this).attr('rel'));
            jQuery("#motivo_id").val(motivoId);
            jQuery("#acao").val("excluirMotivo");
            if (jQuery("#form_cadastrar").length > 0){
                jQuery("#form_cadastrar").submit();
            } else {
                jQuery("#form").submit();
            }
        }

    });
    //botão excluir ação
    jQuery(".deletar").click(function(event) {
        event.preventDefault();
        
        if (confirm('Deseja realmente excluir a Ação?')){
            var acao_id = jQuery.trim(jQuery(this).attr('rel'));
            jQuery("#acao_id").val(acao_id);
            jQuery("#acao").val("excluir");
            jQuery("#form").submit();
        }

    });


    //no change da ação, carrega os motivos
    jQuery("#aoamoid").change(function() {

        var aoampai = (jQuery.trim(jQuery(this).val()) != '') ? jQuery.trim(jQuery(this).val()) : '';

            jQuery("#aoamoid_motivo").html("<option value=''>Selecione</option>");

            jQuery.ajax({
                type: "POST",
                url: 'cad_acao_motivo.php',
                data: {
                    acao: 'carregarMotivos',
                    aoampai: aoampai
                },
                beforeSend: function() {
                    jQuery("#loading-motivos").css('display', '');
                },
                complete: function() {
                    jQuery("#loading-motivos").css('display', 'none');
                },
                success: function(data) {

                    if (typeof JSON != 'undefined') {
                        data = JSON.parse(data);
                    } else {
                        data = eval('(' + data + ')');
                    }

                    var retorno = "<option value=''>Selecione</option>";

                    if (data.length > 0) {
                        jQuery.each(data, function(i, val) {
                            retorno += "<option value='" + val.id + "'>" + val.label + "</option>";
                        });
                    }

                    jQuery("#aoamoid_motivo").html(retorno);
                }
            });
    });

});
