/*
 * Package: Relatorio
 * Author: Thomas de Lima
 */

//Variaveis
var msg_campos_obrigatorios           = "Existem campos obrigatórios não preenchidos.";
var msg_erro_processamento            = "Houve um erro no processamento dos dados.";
var msg_erro_arquivo                  = "Houve um erro no processamento do arquivo.";
var tipoPessoa                        = '';

jQuery(document).ready(function() {

    //========================================= Geral ===================================//

    /*
     * Tratamento somente numeros inteiros
     */
    jQuery('body').on('keyup blur', '.numerico', function() {
        jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));

    });

    /*
     * Mascara para moeda
     */
    jQuery('.moeda').maskMoney({
        thousands: '.',
        decimal: ','
    });
    jQuery('.placa').mask("aaa9999");

    /*
     * Função para inserir máscara e validar digitação para CNPJ, CPF
     * Obs: a função do [mascaras.js] é bugada!
     * formato mascara: exemplo: 000.000.000-00 (CPF)
     */
    function formatarCampo(valor, mascara) {

        valor = valor.replace(/[^0-9]/g, "");
        var tamanhoMaximo = mascara.length;
        var tamanhoMascara = valor.length;
        var boleanoMascara;
        var novoValorCampo = "";
        var posicaoCampo = 0;

        for (i = 0; i <= tamanhoMascara; i++) {

            boleanoMascara = ((mascara.charAt(i) == "-") || (mascara.charAt(i) == ".") || (mascara.charAt(i) == "/"));

            if (boleanoMascara) {
                novoValorCampo += mascara.charAt(i);
                tamanhoMascara++;
            } else {
                novoValorCampo += valor.charAt(posicaoCampo);
                posicaoCampo++;
            }
        }

        return novoValorCampo.slice(0, tamanhoMaximo);
    }


    //========================================= Tela de Pesquisa: INICIO ===================================//

    /*
     * botão pesquisar
     */
    jQuery("#btn_pesquisar").click(function() {

        jQuery.ajax({
            url: 'rel_linhas_reativacao.php',
            type: 'POST',
            data: {
                acao: 'destruirSessaoPaginacao'
            },
            success: function() {
                jQuery('#form_pesquisa').submit();
            }
        });
    });

    /*
     * Busca de clientes por autocomplete
     */
    jQuery("#clinome_pesq").autocomplete({

        source: 'rel_linhas_reativacao.php?acao=recuperarCliente',
        minLength: 3,
        response: function(event, ui) {

            jQuery('#msg_alerta_autocomplete').fadeOut(function() {
                if (!ui.content.length) {
                    jQuery('#msg_alerta_autocomplete').html("Cliente não encontrado em nossa base.").fadeIn();
                }
            });

            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {}
                }
            });

        },
        select: function(event, ui) {

            jQuery("#clioid_pesq").val(ui.item.id);
            jQuery('#clinome_pesq').val(ui.item.value);
        }
    });

    //Campo [Cliente]
    jQuery("#clinome_pesq").blur(function() {

        if (jQuery.trim(this.value) == '') {
            jQuery("#clioid_pesq").val('');
        }

    });

    //========================================= Tela de Pesquisa: FIM ===================================//

});


// ========================================== FUNCOES ============================================== //

/*
 * valida campos indicados como obrigatorios, nao preenchidos.
 */
function validarCamposObrigatorios() {

    var erros = 0;

    jQuery('form .obrigatorio').each(function(id, valor) {

        elemento = jQuery('#' + valor.id);

        if (jQuery.trim(elemento.val()) == '') {

            elemento.addClass('erro');
            erros++;
        }
    });

    if (erros > 0) {
        jQuery('#mensagem_alerta').html(msg_campos_obrigatorios);
        jQuery('#mensagem_alerta').show();
        return false;
    } else {
        return true;
    }
}

/*
 * Função para inserir máscara e validar digitação para CNPJ e CPF
 * Obs: a função do [mascaras.js] é bugada!
 * formato mascara: exemplo:
 */
function formatarCampo(idSeletor, mascara) {

    var valor = jQuery(idSeletor).val();
    valor = valor.replace(/[^0-9]/g, "");
    var tamanhoMaximo = mascara.length;
    var tamanhoMascara = valor.length;
    var boleanoMascara;
    var novoValorCampo = "";
    var posicaoCampo = 0;

    for (i = 0; i <= tamanhoMascara; i++) {
        boleanoMascara = ((mascara.charAt(i) == "-") || (mascara.charAt(i) == ".") || (mascara.charAt(i) == "/"));
        if (boleanoMascara) {
            novoValorCampo += mascara.charAt(i);
            tamanhoMascara++;
        } else {
            novoValorCampo += valor.charAt(posicaoCampo);
            posicaoCampo++;
        }
    }

    jQuery(idSeletor).val(novoValorCampo.slice(0, tamanhoMaximo));
}



/*
 * Reorganzia as cores das linhas na lista
 */
function aplicarCorLinha(idLista) {

    var cor = '';

    //remove cores
    jQuery('#' + idLista + ' table tbody tr').removeClass('par');
    jQuery('#' + idLista + ' table tbody tr').removeClass('impar');


    //aplica cores
    jQuery('#' + idLista + ' table tbody tr').each(function() {
        cor = (cor == "par") ? "impar" : "par";
        jQuery(this).addClass(cor);
    });
}

/**
 * Formatar moeda entre brasileiro / americano
 *
 * formato | [A] = americano para brasileiro, [B] = brasileiro para americano
 */
function tratarMoeda(valor, formato) {

    if (!valor) {
        valor = '0';
    }

    valor = valor.toString();

    if (formato == 'A') {
        valor = valor.replace(',', '');

        if (valor.indexOf('.') == -1) {
            valor = valor + ',' + '00';
        } else {
            casas = valor.split('.');
            valor = casas[0] + ',' + ((casas[1].length == 1) ? (casas[1] + '0') : casas[1]);
        }

    } else {
        valor = valor.replace(',', '.');
    }

    return valor;
}

/*
 * Aplica mascara de telefone
 * Adaptacao da funcao presente em 'lib/funcoes_masc.js'
 * acrescentando o ZERO no DDD
 */

function formatarTelefone(element) {

    var valor = element.value;
    var ddd = '';
    var prefixo = '';
    var is_cel_fone = '';
    var arrayDDD = new Array();
    var checkDDD = false;

    //DDD's que devem utilizar o nono digito

    // SP
    arrayDDD[0] = '011';
    arrayDDD[1] = '012';
    arrayDDD[2] = '013';
    arrayDDD[3] = '014';
    arrayDDD[4] = '015';
    arrayDDD[5] = '016';
    arrayDDD[6] = '017';
    arrayDDD[7] = '018';
    arrayDDD[8] = '019';

    // RJ
    arrayDDD[9] = '021';
    arrayDDD[10] = '022';
    arrayDDD[11] = '024';

    // ES
    arrayDDD[12] = '027';
    arrayDDD[13] = '028';

    //Remove tudo o que não é dígito
    valor = valor.replace(/[^0-9]/g, "");

    //Coloca parênteses em volta dos tres primeiros dígitos e espaço após)
    valor = valor.replace(/^(\d\d\d)(\d)/g, "($1) $2");

    //Resgatando o DDD considerando a máscara
    ddd = valor.charAt(1) + valor.charAt(2) + valor.charAt(3);

    if (ddd != '') {
        checkDDD = in_array(ddd, arrayDDD);
    }

    /*
     * Precisamos do prefixo para colocar 5 ou 4 dígitos antes do hífen,
     * considerando as regras abaixo:
     *
     * REGRA 1:
     *     Quando o DDD for de SP (11) e os prefixos forem 6, 8 ou 9 (Celular)
     *     colocaremos 5 dígitos antes do hífen.
     *
     * REGRA 2:
     *     Quando o DDD for de SP e o prefixo for 5 poderemos ter 4 ou 5 dígitos
     *  antes do hífen.
     *
     * Nota:
     *     Prefixo é o primeiro dígito do número do telefone após o DDD
     * */
    prefixo = valor.charAt(6);

    is_cel_fone = (prefixo == '6' || prefixo == '8' || prefixo == '9') ? true : false;

    if (checkDDD && (is_cel_fone || prefixo == '5')) {
        element.setAttribute('maxlength', 16);
    } else {
        element.setAttribute('maxlength', 15);
    }

    if ((checkDDD && is_cel_fone) || (checkDDD && prefixo == '5' && valor.length == 15)) {
        valor = valor.replace(/(\d{5})(\d{4})/, "$1-$2");
        valor = (valor.length > 16) ? valor.substring(0, 16) : valor;
    } else {
        valor = valor.replace(/(\d{4})(\d{4})/, "$1-$2");
        valor = (valor.length > 15) ? valor.substring(0, 15) : valor;
    }

    return valor;
}

/**
 * @param String ddd
 * @param Array arrayDDD
 * @returns Boolean
 */
function in_array(ddd, arrayDDD) {
    for (var i in arrayDDD) {
        if (arrayDDD[i] == ddd) {
            return true;
        }
    }
    return false;
}
