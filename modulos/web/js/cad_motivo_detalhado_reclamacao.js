jQuery(function(){


	if(jQuery('#motivo_geral').val() != ''){

		jQuery('#motivo_geral option').each(function(i, value){

			if (jQuery(value).attr('selected')) {
				jQuery('.filtroPesquisado').html('Motivo Geral: '+(jQuery.trim(jQuery(value).text())));
				jQuery('#id_motivo_geral').val(jQuery(value).attr('value'));
			}
		})
	}

	if(jQuery('#detalhamento_motivo').val() != ''){

		jQuery('#detalhamento_motivo option').each(function(i, value){

			if (jQuery(value).attr('selected')) {
				jQuery('.filtroPesquisado').html('Motivo Detalhado: '+(jQuery.trim(jQuery(value).text())));
				jQuery('#id_detalhamento_motivo').val(jQuery(value).attr('value'));
			}
		})
	}

	jQuery('#motivo_geral , #detalhamento_motivo').change(function(){

		if (this.id == 'motivo_geral') {
			jQuery('#detalhamento_motivo').val('');
		} else {
			jQuery('#motivo_geral').val('');
		}
	})

	jQuery('#bt_atualizar').click(function(){
		jQuery('#acao').val('atualizar');
		jQuery('#form').submit();
	})

	jQuery("#bt_novo").click(function(){
		jQuery('#acao').val('novo');
		jQuery('#form').submit();
	})

	jQuery("#bt_confirmar").click(function(){
		jQuery('#acao').val('cadastrar');
		jQuery('#form_cadastrar').submit();
	})

	jQuery('.excluir').click(function(){

		if (!confirm('Deseja realmente excluir esse registro?')) {
			return false;
		}

		var idExcluir = jQuery(this).attr('id-motivo');
		jQuery('#exclusao').val(idExcluir);
		jQuery('#acao').val('excluir');
		jQuery('#form_cadastrar').submit();

	})

	jQuery('#bt_voltar').click(function(){
		location.href = "cad_motivo_detalhado_reclamacao_cliente.php";
	})

	jQuery('.checkResultado').click(function(){

        var checkboxResultado = jQuery(this);
        var motivoDetalhadoCheck = checkboxResultado.attr('id');
        var marcados = jQuery('#marcados').val();
        var desmarcados = jQuery('#desmarcados').val();

        if (checkboxResultado.is(':checked')) {

            if (marcados == "") {
                jQuery('#marcados').val(motivoDetalhadoCheck);
            } else {
                jQuery('#marcados').val(marcados +','+ motivoDetalhadoCheck);
            }

           	if (desmarcados != "") {
           		
                var arrValores = desmarcados.split(',');

                var newArrChecks = jQuery.grep( arrValores, function( valoresArray, i ) {
                  return valoresArray != motivoDetalhadoCheck;
                });

                jQuery('#desmarcados').val(newArrChecks);
            }

        } else {

            if (marcados != "") {
                var arrValores = marcados.split(',');

                var newArrChecks = jQuery.grep( arrValores, function( valoresArray, i ) {
                  return valoresArray != motivoDetalhadoCheck;
                });

                jQuery('#marcados').val(newArrChecks);
            }

            if (desmarcados == "") {
                jQuery('#desmarcados').val(motivoDetalhadoCheck);
            } else {
                jQuery('#desmarcados').val(desmarcados +','+ motivoDetalhadoCheck);
            }
        }

    })
	
})