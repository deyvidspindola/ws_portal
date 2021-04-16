jQuery(function(){

	jQuery('#data_inicio').periodo('#data_fim');

	if (jQuery("#ano").val() == "") {
		jQuery("#ano").val(jQuery("#ano_referencia").val());
	}

	jQuery('#confirmar').click(function(){
		jQuery('#acao').val('confirmar');
		jQuery('#form').submit();
	});

	jQuery('#retornar').click(function(){
		document.location.href = 'ges_plano_acao.php?acao=visualizar&meta='+jQuery('#id_meta_selecionada').val()+'&plano='+jQuery('#id_plano_selecionado').val()+'&ano='+jQuery('#ano_referencia').val();
	});

	jQuery("#meta").change(function(){

        jQuery.ajax({
            url: 'ges_plano_acao.php',
            type: 'POST',
            data: {
                'acao' : 'buscarResponsaveis',
                'meta': jQuery(this).val(),
                'ano': jQuery("#ano").val()
            },
            success: function (data) {
            	
                var dados = jQuery.parseJSON(data);

                //var combo = '<select id="responsavel" name="responsavel">';
                var combo = '';
                combo += '<option value="">Escolha</option>';
                	if (dados == null) {
                		combo += "";
                	} else {
	                    jQuery.each(dados, function(i, item){

	                        combo += '<option value="' + item.funoid + '" ';
	                        combo += (item.responsavel == item.funoid) ? 'selected="selected"' : '';
	                        combo += '>';
	                        combo += item.funnome + '</option>';

	                    });
					}
                //combo += '</select>';

                jQuery("#responsavel").html(combo);
            }
        });


    });
	
})