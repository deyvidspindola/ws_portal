jQuery(document).ready(function() {

    var script = 'rel_produtos_reservados.php';

    popularComboInstalador();

    jQuery('#ufuf').change(function() {
        jQuery('#acao').val('buscarCidade');

        jQuery('#div_mensagem_geral')
            .removeClass('alerta erro sucesso')
            .addClass('invisivel')
            .html(null);

        jQuery.ajax({
            type       : 'post',
            url        : script,
            data       : jQuery('#form_pesquisa').serialize(),
            dataType   : 'json',
            beforeSend : function() {
                jQuery('#ciddescricao').mostrarCarregando();
            },
            complete   : function() {
                jQuery('#ciddescricao').esconderCarregando();
            },
            error      : function() {
                jQuery('#div_mensagem_geral')
                    .removeClass('alerta sucesso invisivel')
                    .addClass('erro')
                    .html('Houve um erro na comunicação com o servidor.');
            },
            success    : function(response) {
                if (response.status === 'errorlogin' && response.redirect) {
                    location.href = response.redirect;
                } else if (response.status && response.html) {
                    jQuery('#ciddescricao').html(response.html);
                } else {
                    jQuery('#div_mensagem_geral')
                        .removeClass('alerta erro sucesso invisivel')
                        .addClass(response.mensagem.tipo)
                        .html(response.mensagem.texto);
                }
            }
        });

        return true;
    });

    jQuery('#bt_exportar, #bt_pesquisar').click(function() {
        jQuery('#acao').val('pesquisar');

        if (jQuery(this).attr('id') === 'bt_exportar') {
            jQuery('#acao').val('exportar');
        }

        jQuery('#form_pesquisa').submit();

        return true;
    });

    /*
     * Tratamento somente numeros inteiros
     */
    jQuery('body').on('keyup blur', '.numerico', function() {
        jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));

    });

    /*
    * Acao da combo <representante>
     */
    jQuery('#form_pesquisa').on('change', '#repoid', function() {
       popularComboInstalador();
    });

    /*
    * Recuperar os dados dos instaladores e popular combo <instalador>
     */
    function popularComboInstalador(){

    var prestador = jQuery('#repoid option:selected').val();
    var itloid = jQuery('#itloid_select').val();

    jQuery.ajax({
            url: script,
            type: 'POST',
            data: {
                acao: 'popularComboInstaladorAjax',
                repoid: prestador
            },
            async: false,
            success: function(data) {

                var combo = '<option value="">Escolha</option>';

                try{

                    if (data.length > 0) {

                        data = JSON.parse(data);

                        if (Object.keys(data).length > 0) {

                            jQuery.each(data, function(i, val) {

                                selecionado = (itloid == val.id) ? ' selected="true"' : '';

                                combo += '<option value="' + val.id + '"' + selecionado + '>' + val.descricao + '</option>';

                            });
                        }

                    }

                } catch(err) {
                    jQuery('#itloid').html(combo);
                }

                jQuery('#itloid').html(combo);

            }
        });
}

});

