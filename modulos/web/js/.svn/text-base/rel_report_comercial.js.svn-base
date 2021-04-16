jQuery(document).ready(function() {

    var script = 'rel_report_comercial.php';

    jQuery('#rpcdrczoid > option:first-child').click(function() {
        if (jQuery(this).html() == 'Marcar todos') {
            jQuery('#rpcdrczoid > option').attr('selected', 'selected');

            jQuery(this).html('Desmarcar todos');
        } else {
            jQuery('#rpcdrczoid > option').removeAttr('selected');

            jQuery(this).html('Marcar todos');
        }

        jQuery(this).removeAttr('selected');
        jQuery(this).focus();

        return true;
    });

    jQuery('#rpcclinome').autocomplete({
        source: script + '?acao=buscarCliente',
        minLength: 2,
        response: function(event, ui) {
            jQuery("#rpcclioid").val('');

            jQuery('#div_mensagem_geral')
                    .addClass('invisivel')
                    .removeClass('alerta erro info sucesso');

            if (ui.content.length) {
                var tamanho = ui.content.length * 23;

                if (tamanho > 166) {
                    jQuery('.ui-autocomplete').height(166);
                } else {
                    jQuery('.ui-autocomplete').height(tamanho);
                }
            } else {
                jQuery('#div_mensagem_geral')
                        .html('Cliente não consta no cadastro.')
                        .addClass('alerta')
                        .removeClass('invisivel');
            }
        },
        select: function(event, ui) {
            jQuery("#rpcclioid").val(ui.item.clioid);
        }
    });

    jQuery('.rpcoid').change(function() {
        if (jQuery('.rpcoid:checked').length == 0) {
            jQuery('#bt_excluir').attr('disabled', 'disabled');
        } else {
            jQuery('#bt_excluir').removeAttr('disabled');
        }
        return true;
    });

    jQuery('#marcar_todos_top').checarTodos('.rpcoid', function(){
        jQuery('.rpcoid').trigger('change');
    });

    jQuery('#form_listagem').submit(function() {
        mensagem = 'Deseja realmente excluir o arquivo?';

        if (jQuery('.rpcoid:checked').length > 1) {
            mensagem = 'Deseja realmente excluir os arquivos?';
        }

        if (confirm(mensagem)) {
            return true;
        }

        return false;
    });

});


