jQuery(function(){

	var comboTipo = jQuery('#tipo').val();

	if (comboTipo == 'C') {
		jQuery('.fato_causa').html('Fato / Causa *');
	}

	if (jQuery("#ano").val() == "") {
		jQuery("#ano").val(jQuery("#ano_referencia").val());
	}

	jQuery("#percentual").mask('9?99',{
        placeholder:""
    });

	jQuery('#tipo').change(function(){

		if (jQuery(this).val() == 'C') {
			jQuery('.fato_causa').html('Fato/Causa *');
		} else {
			jQuery('.fato_causa').html('Fato/Causa');
		}
	});

	var comboStatus = jQuery('#status').val();

	if (comboTipo == 'N') {
		jQuery('.motivo_cancelamento').html('Motivo Cancelamento *');
	}

	jQuery('#status').change(function(){

		if (jQuery(this).val() == 'N') {
			jQuery('.motivo_cancelamento').html('Motivo Cancelamento *');
		} else {
			jQuery('.motivo_cancelamento').html('Motivo Cancelamento');
		}
	});

	jQuery('#bt_confirmar').click(function(){

        jQuery('#alerta_acao_confirmar').hide();
        jQuery('#inicio_realizado').removeClass('erro');
        jQuery('#fim_realizado').removeClass('erro');

        var dataIni = jQuery('#inicio_realizado').val();
        var dataFim = jQuery('#fim_realizado').val();
        
        var dataIniPrevisto = jQuery('#inicio_previsto').val();
        var dataFimPrevisto = jQuery('#fim_previsto').val();

        if(validarIntervaloDatas(dataIni, dataFim)) {
            jQuery('#alerta_acao_confirmar').show();
			jQuery('#alerta_acao_confirmar').html('Data Fim Realizado deve ser maior ou igual a Data Inicio Realizado.');
			jQuery('#fim_realizado').addClass('erro');
        } else if (validarIntervaloDatas(dataIniPrevisto, dataFimPrevisto)){
            jQuery('#alerta_acao_confirmar').show();
			jQuery('#alerta_acao_confirmar').html('Data Fim Previsto deve ser maior ou igual a Data Inicio Realizado.');
			jQuery('#fim_previsto').addClass('erro');
        } else {
            jQuery('#acao').val('confirmar');
            jQuery('#form').submit();
        }
	});

	jQuery('#bt_adicionar').click(function(){

		jQuery('#descricao_item').removeClass('erro');
		jQuery('.alerta_item_acao').addClass('invisivel');

		if (jQuery.trim(jQuery('#descricao_item').val()).length == 0) {
			jQuery('.alerta_item_acao').removeClass('invisivel');
			jQuery('.alerta_item_acao').html('Existem campos obrigatórios não preenchidos.');
			jQuery('#descricao_item').addClass('erro');
			return false;
		}

		jQuery.ajax({
			url: 'ges_acoes.php',
			type: 'POST',
			dataType: 'json',
			data: {
				'acao': 'incluirItemAcao',
				'descricao': jQuery('#descricao_item').val(),
				'id_acao': jQuery('#id_acao').val()
			},
			beforeSend: function(){
				jQuery('.loading_itens').show();
			},
			success: function(response) {

				if (response.erro == '1') {
					jQuery('.erro_item_acao').html('Houve um erro no processamento.').show();
					jQuery('.loading_itens').hide();
					return false;
				}

				buscarItens();

			}
		});
	});

	buscarItens();

	jQuery('#bt_retornar').click(function(){
		document.location.href = 'ges_plano_acao.php?acao=visualizar&meta='+jQuery('#id_meta_selecionada').val()+'&plano='+jQuery('#id_plano_selecionado').val()+'&ano='+jQuery('#ano_referencia', window.parent.document).val();
	});

    jQuery("#plano").click(function(){

        jQuery.ajax({
            url: 'ges_acoes.php',
            type: 'POST',
            data: {
                'acao' : 'buscarResponsaveis',
                'plano': jQuery(this).val(),
                'ano': jQuery("#ano").val()
            },
            success: function (data) {
            	
                var dados = jQuery.parseJSON(data);

                //var combo = '<select id="responsavel" name="responsavel">';
                var combo = '';
                combo += '<option value="">Escolha</option>';

                    jQuery.each(dados, function(i, item){

                        combo += '<option value="' + item.funoid + '" ';
                        combo += (item.responsavel == item.funoid) ? 'selected="selected"' : '';
                        combo += '>';
                        combo += item.funnome + '</option>';

                    });

                //combo += '</select>';

                jQuery("#responsavel").html(combo);
            }
        });


    });

});

function buscarItens() {

	var id_acao = jQuery('#id_acao').val();

	jQuery.ajax({
		url: 'ges_acoes.php',
		type: 'POST',
		dataType: 'json',
		data: {
			'acao' : 'buscarItemAcao',
			'id_acao': id_acao
		},
		beforeSend: function () {
			jQuery('.resultados_tabela').fadeOut();
		},
		success: function (data) {

			if (data.erro == '1') {
				jQuery('.erro_item_acao').html('Houve um erro no processamento.').show();
				return false;
			}

			var tr = '';

			if (data.dados.length > 0) {

				jQuery.each(data.dados, function(i, item){

					var classeCSS = (i % 2 == 0) ? 'impar' : 'par';

					tr += '<tr class="'+classeCSS+'">';
				        tr += '<td class="centro">'+item.data+'</td>';
				        tr += '<td>'+item.descricao+'</td>';
				        tr += '<td>'+item.usuario+'</td>';
			        tr += '</tr>';

				});
			} else {
				tr = '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
			}

			jQuery('.resultado_item_acao').html(tr);

			var mensagem = (data.dados.length == 1) ? data.dados.length + ' registro encontrado.' : data.dados.length + ' registros encontrados';

			if (data.dados.length == 0) {
				mensagem = 'Nenhum registro encontrado.';
			}

			jQuery('.resultado_encontrado').html(mensagem);

			jQuery('.resultados_tabela').fadeIn();

			jQuery('.loading_itens').hide();

		}
	});
}

function validarIntervaloDatas(dataIni, dataFim) {

    if ((dataIni == '') && (dataFim == '')) {
        return false;
    }

    var data1 = new Date();
    var data2 = new Date();
    
    var dataIniPart = dataIni.split('/');
    var dataFimPart = dataFim.split('/');

    data1 = data1.setTime(Date.parse(dataIniPart[1] + '/' + dataIniPart[0] + '/' + dataIniPart[2]));
    data2 = data2.setTime(Date.parse(dataFimPart[1] + '/' + dataFimPart[0] + '/' + dataFimPart[2]));

    if (data1 > data2) {
        return true;
    } else {
        return false;
    }
}