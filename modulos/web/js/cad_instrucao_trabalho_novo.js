jQuery(document).ready(function() {

    jQuery('#btn_aprovar').click(function() {

        // Remove alertas de erro 
        removeAlerta();

        jQuery.ajax({
            url: 'cad_instrucao_trabalho_novo.php',
            type: 'post',
            data: jQuery('#frm').serialize() + '&acao=aprovar',
            beforeSend: function() {
                jQuery('#loading').show();
            },
            success: function(data) {
                jQuery('#loading').hide();

                var resultado = jQuery.parseJSON(data);

                if (resultado.error) {
                    criaAlerta(resultado.message);
                } else {

                    var ul_error = '<ul style="margin: 0 0 5px 20px; padding: 0;">';
                    var has_error = false;
                    var elaborador_anterior = '';

                    jQuery.each(resultado, function(i, documento) {

                        if (documento.error) {
                            has_error = true;
                            ul_error += '<li>' + documento.message + '</li>';
                        } else {


                            var tr_id = documento.doc_id;
                            var img_ativo = '<img src="images/indicadores/quadrados/ap/ap15.jpg" />';

                            jQuery('#' + tr_id).effect('highlight', {}, 1000, function() {
                                setTimeout(function() {
                                    jQuery('#' + tr_id).removeAttr("style").hide().fadeIn();

                                    jQuery('#' + tr_id + ' .aprovador').html(documento.aprovador);
                                    jQuery('#' + tr_id + ' .data').html(documento.date);
                                    jQuery('#' + tr_id + ' .status').html(img_ativo);
                                    jQuery('#' + tr_id + ' .aprovacao').html('');

                                }, 200);
                            });

                            if (!documento.email_sended && elaborador_anterior != documento.elaborador) {
                                has_error = true;

                                var elaborador = documento.elaborador == null ? '' : documento.elaborador;

                                elaborador_anterior = elaborador;

                                ul_error += '<li style="font-weight: normal;">O email não pode ser enviado, favor verificar o cadastro do remetente <b>' + elaborador + '</b></li>';
                            }

                        }

                    });

                    ul_error += '</ul>';

                    if (has_error) {
                        criaAlerta(ul_error);
                    }

                }

            }

        });

    });

    jQuery('#btn_excluir').click(function() {

        // Remove alertas de erro 
        removeAlerta();

        jQuery.ajax({
            url: 'cad_instrucao_trabalho_novo.php',
            type: 'post',
            data: jQuery('#frm').serialize() + '&acao=excluir',
            beforeSend: function() {
                jQuery('#loading').show();
            },
            success: function(data) {
                jQuery('#loading').hide();

                var resultado = jQuery.parseJSON(data);

                if (resultado.error) {
                    criaAlerta(resultado.message);
                } else {

                    jQuery.each(resultado, function(i, documento) {

                        if (documento.error) {
                            criaAlerta(documento.message);
                        } else {


                            var tr_id = documento.doc_id;
                            var img_excluido = '<img src="images/indicadores/quadrados/ap/ap04.jpg" />';

                            jQuery('#' + tr_id).effect('highlight', {}, 1000, function() {
                                setTimeout(function() {
                                    jQuery('#' + tr_id).removeAttr("style").hide().fadeIn();

                                    jQuery('#' + tr_id + ' .data').html(documento.date);
                                    jQuery('#' + tr_id + ' .status').html(img_excluido);


                                }, 200);
                            });

                        }

                    });

                }

            }

        });

    });


    jQuery('#check_all').click(function() {

        if (jQuery(this).is(':checked')) {

            jQuery('.checkbox_select').each(function() {
                jQuery(this).attr('checked', 'checked');
            });

        } else {

            jQuery('.checkbox_select').each(function() {
                jQuery(this).removeAttr('checked');
            });

        }
    });
    jQuery('#itdescricao').ready(function() {

        // Remove alertas de erro 
        removeAlerta();
    });
});