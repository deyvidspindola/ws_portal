var showMessage = 1;

function excluirMaterial(epmoid) {
	if (confirm("Deseja excluir esse material da instalação do equipamento?")) {
		jQuery.ajax({
			url: 'cad_equipamentos_materiais.php',
			type: 'post',
			data: {equipamento:$("#equipamento").val(),relacao:epmoid,acao:'excluir'},
			beforeSend: function(){
				jQuery('#relatorio').html('<div style="width: 100%; text-align:center;"><img src="images/loading.gif" alt="" /></div>');
			},
			success: function(data) {
				var resultado = jQuery.parseJSON(data);
				msg = resultado.retorno.msg;
				jQuery('#div_msg').html("<br />"+msg+"<br /><br />");

                                if (!resultado.retorno.error) {
                                    showMessage = 0;
                                    $("#equipamento").change();
                                } else {
                                    jQuery('#relatorio').html('');
                                }
			}
		});
	}
}

function adicionarMaterial(material) {
	if (material > 0) {
		jQuery.ajax({
			url: 'cad_equipamentos_materiais.php',
			type: 'post',
			data: {equipamento:$("#equipamento").val(),material:material,classe:$("#classe").val(),acao:'adicionar'},
			beforeSend: function(){
				jQuery('#relatorio').html('<div style="width: 100%; text-align:center;"><img src="images/loading.gif" alt="" /></div>');
			},
			success: function(data) {
				//console.log(data);
				var resultado = jQuery.parseJSON(data);
				msg = resultado.retorno.msg;
                jQuery('#div_msg').html("<br />"+msg+"<br /><br />");

                if (!resultado.retorno.error) {
            		showMessage = 0;
                	$("#equipamento").change();
                } else {
            		jQuery('#relatorio').html('');
                }
			}
		});
	}
}


function getRelatorio(data) {
	try {
		//console.log(data);
		var resultado = jQuery.parseJSON(data);
		msg = resultado.retorno.msg;
		var txt = "";
		txt += '<table class="espaco espaco2">';
		txt += '<tr class="tableSubTitulo">';
		txt += '<td class="celula_relatorio1"><b>Classe Contrato</b></td>';
		txt += '<td class="celula_relatorio2"><b>Material</b></td>';
		txt += '<td class="celula_botao"><b>Excluir</b></td>';
		txt += '</tr>';

		//console.log(resultado);
		//console.log(resultado.retorno.error);

		if (resultado.retorno.error == 1) {
			throw "erro1";
		} else {
			if (msg != null && msg != "" && showMessage) {
				jQuery('#div_msg').html("<br />"+msg+"<br /><br />");
			} else if (showMessage) {
				jQuery('#div_msg').html("");
			}
		}

		jQuery.each(resultado.relatorio, function() {
			txt += '<tr class="tr_resultado_ajax">';
			txt += '<td class="celula_relatorio1">'+this.eqcdescricao+'</td>';
			txt += '<td class="celula_relatorio2">'+this.prdproduto+'</td>';
			txt += '<td class="celula_botao"><a href="javascript: excluirMaterial('+this.eppoid+');">Excluir</a></td>';
			txt += '</tr>';
		});

		txt += '</table>';

		// Populando a tabela
		if (resultado.relatorio.length>0) {
			jQuery('#relatorio').html(""+txt);
		}
		else {
			jQuery('#relatorio').html("");
		}
		// Zebrando a tabela
		jQuery('.tr_resultado_ajax:odd').addClass('tde');
		jQuery('.tr_resultado_ajax:even').addClass('tdc');

		//mostrando os materiais
		if ($("#equipamento").val()>0 || $("#classe").val()>0) {
			$(".moldura2").fadeIn();
		} else {
			$(".moldura2").hide();
		}

	} catch(e) {
		$(".moldura2").show();
		jQuery('#div_msg').html("<br />Falha ao pesquisar materiais.<br /><br />");
		jQuery('#relatorio').html('');
	}

        showMessage = 1;
}


jQuery(document).ready(function(){
	$(".moldura2").hide();

	$("#equipamento").change(function(){
		jQuery.ajax({
			url: 'cad_equipamentos_materiais.php',
			type: 'post',
			data: {equipamento:$(this).val(),acao:'pesquisar', classe:$("#classe").val()},
			beforeSend: function(){
				jQuery('#relatorio').html('<div style="width: 100%; text-align:center;"><img src="images/loading.gif" alt="" /></div>');
			},
			success: function(data) {
                                getRelatorio(data);
			}
		});
	});

    $("#classe").change(function(){
        jQuery.ajax({
            url: 'cad_equipamentos_materiais.php',
            type: 'post',
            data: {equipamento:$("#equipamento").val(),acao:'pesquisar', classe:$(this).val()},
            beforeSend: function(){
                jQuery('#relatorio').html('<div style="width: 100%; text-align:center;"><img src="images/loading.gif" alt="" /></div>');
            },
            success: function(data) {
                getRelatorio(data);
            }
        });
    });


    $("#btn_buscar_material").click(function(){
        
    	if ($("#material_busca").val().length < 3) {
    		$("#div_msg").text("Mínimo de três letras para buscar os materiais.");
    		return;
    	}

        jQuery.ajax({
            url: 'cad_equipamentos_materiais.php',
            type: 'post',
            data: {material_busca:$("#material_busca").val(),acao:'pesquisarMateriais'},
            beforeSend: function(){
                jQuery('#lista_materiais').html('<div style="width: 100%; text-align:center;"><img src="images/loading.gif" alt="" /></div>');
            },
            success: function(data) {
            	var resultado = jQuery.parseJSON(data);
				// Populando a tabela
            	if (resultado != null && resultado.length > 0) {
	            	var txt = "";
					txt += '<table class="espaco espaco2">';
					txt += '<tr class="tableSubTitulo">';
					txt += '<td class="celula_relatorio1"><b>Material</b></td>';
					txt += '<td class="celula_botao"><b>Acionar</b></td>';
					txt += '</tr>';

					jQuery.each(resultado, function(i, value) {
						txt += '<tr class="tr_resultado_ajax">';
						txt += '<td class="celula_relatorio1">'+value.prdproduto+'</td>';
						txt += '<td class="celula_botao"><a href="javascript: adicionarMaterial('+value.prdoid+');">Adicionar</a></td>';
						txt += '</tr>';
					});
					txt += '</table>';
					jQuery('#lista_materiais').html(""+txt);
					// Zebrando a tabela
					jQuery('#lista_materiais .tr_resultado_ajax:odd').addClass('tde');
					jQuery('#lista_materiais .tr_resultado_ajax:even').addClass('tdc');

					$("#div_msg").text("");
				}
				else {
					jQuery('#lista_materiais').html("");
					$("#div_msg").text("Nenhum material encontrado.");
				}
            }
        });
    });

	jQuery("#btn_limpar_busca").click(function(){
        jQuery("#material_busca").val("");
        jQuery('#lista_materiais').html("");
    });

});