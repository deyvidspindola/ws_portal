/*
 * Package: Manutencao: Cobranca de Produtos Divergentes
 * Author: Andre L. Zilz
 */

var MSG_MARCAR_CHECKBOX = " Para que a cobrança possa ser efetuada, selecione os produtos que deseja cobrar.";
var MSG_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

jQuery(document).ready(function() {

	/*
     * botao [Confirmar Cobranca]
     */
      jQuery('body').on('click', '#btn_confirmar_cobranca', function() {

   		var marcados = 0;

        jQuery('#mensagem_alerta').hide();

    	jQuery('#bloco_produtos_divergentes table tbody tr input:checkbox').each(function() {

            if (jQuery(this).prop('checked') === true) {
            	marcados++;
            }

        });

        if(marcados == 0) {

        	jQuery('#mensagem_alerta').text(MSG_MARCAR_CHECKBOX);
            jQuery('#mensagem_alerta').show();

        } else {

            msg = "Será gerada uma cobrança para o representante " + jQuery('#repnome').text();
            msg += " no valor de " + jQuery('#total_cobranca').text();
            msg += ", referente aos produtos listados e faltantes em seu estoque. Deseja realmente efetuar a cobrança?";

            jQuery('#msg_confirmar_cobranca').text(msg);

        	jQuery("#msg_confirmar_cobranca").dialog({
	            title: "Confirmar Cobrança",
	            resizable: false,
	            modal: true,
	            buttons: {
	                "Sim": function() {

                        var produtos = '';

                        jQuery(this).dialog("close");

                        jQuery('#bloco_produtos_divergentes table tbody tr input:checkbox').each(function() {

                            if ( jQuery(this).prop('checked') === true ) {
                                produtos +=  this.id + ',';
                            }

                        });

                        produtos = produtos.substring(0, produtos.lastIndexOf(','));

                        jQuery('#produtos').val(produtos);

                        jQuery('form').submit();


	               },
	                "Não": function() {
	                    jQuery(this).dialog("close");
	                }
	            }
	        });

        }

    });

    /*
     * botão [Fechar]
     */
    jQuery("#btn_voltar").click(function() {

        window.location.href = 'man_acomp_inventario.php?invoid='+jQuery('#invoid').val()+'&frm_acao=detalhar';
    });


   	/*
     * Acao para Marcar Todos
     */
    jQuery('body').on('click', '#selecao_todos', function() {

        var marcado = jQuery(this).prop('checked');
        var elemento = '';

        jQuery('#bloco_produtos_divergentes table tbody tr input:checkbox').each(function() {

            if (marcado) {
                jQuery(this).prop('checked', true);
            } else {
                jQuery(this).prop('checked', false);
            }

        });

        total = calcularTotalCobranca();

        total = tratarMoeda(total, 'A2B');

        jQuery('#total_cobranca').text(total);

    });

	/*
     * Acao para os checkbox
     */
    jQuery('#bloco_produtos_divergentes').on('click', 'input:checkbox', function() {

    	total = calcularTotalCobranca();

        total = tratarMoeda(total, 'A2B');

        jQuery('#total_cobranca').text(total);

    });

});

function calcularTotalCobranca() {

	var total = 0.00;

	 jQuery('#bloco_produtos_divergentes table tbody tr input:checkbox').each(function() {

         if(jQuery(this).prop('checked') === true) {

            valor = jQuery(this).data('valor').replace('.', '');

         	total = parseFloat(valor.replace(',', '.')) + total;

         }
     });

     return total.toFixed(2);

}

/**
 * Formatar moeda entre brasileiro / americano
 *
 * formato | [A2B] = americano para brasileiro, [B2A] = brasileiro para americano
 */
function tratarMoeda(valor, formato) {

    valor = (!valor) ?  '000' : valor;

    valor = valor.toString();

    if (formato == 'A2B') {

        valor = valor.replace(',', '').replace('.', '');

        valor = valor.replace(/([0-9]{2})$/g, ",$1");

        if( valor.length > 6 ) {
                valor = valor.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
        }

    } else {
        valor = valor.replace(',', '.');
    }

    return valor;
}
