function carregarSubtipoProposta(elemento) {
	var elemento_alvo;
	
	var tipo_proposta     = elemento.val();
	var tipo_proposta_cod = 0;
	
	if(jQuery('#subtipoProposta').length > 0) {
		elemento_alvo = jQuery('#subtipoProposta');
	} else if(jQuery('#subtipo_proposta').length > 0) {
		elemento_alvo = jQuery('#subtipo_proposta');
	}
	
	tipo_proposta = tipo_proposta.split('-');
	
	tipo_proposta_cod = tipo_proposta[1];
	tipo_proposta     = tipo_proposta[0];
	
	if(tipo_proposta != '') {
		jQuery.ajax({
			data     : {
				acao         : 'carregarSubTipoProposta',
				tipoproposta : tipo_proposta,
				imprime      : 1
			},
			dataType : 'text',
			type     : 'post',
			url      : 'cad_parametrizacao_consulta_gestor_credito.php',
			complete : function() {
				
			},
			error    : function(data, status, error) {
				
			},
			success  : function(response) {
				elemento_alvo.html('');
				elemento_alvo.append('<option value="">Escolha</option>');
				
				// console.log(response);
				
				if(trim(response) != '') {
					elemento_alvo.append(response);
					
					elemento_alvo.parent('div').show('');
				} else {
					elemento_alvo.parent('div').hide('');
				}
				
				// console.log(elemento_alvo.parent('div').is(":visible"));
			}
		});
		
		if(jQuery('#tipo_proposta_texto').length > 0) {
			jQuery('#tipo_proposta_texto').val(tipo_proposta_cod);
		}
	} else {
		elemento_alvo.html('');
		elemento_alvo.append('<option value="">Escolha</option>');
		
		elemento_alvo.parent('div').hide('');
	}
	
	return false;
}


jQuery(document).ready(function(){
    
    if (jQuery(document).has("#persistirDados")) {
        if (jQuery("#persistirDados").val() != "") {
            var valores = jQuery("#persistirDados").val().split(",");
            jQuery("input[type=radio]").each(function() {
                if (jQuery(this).val() == valores[0]) {
                    jQuery(this).attr("checked", "checked");
                }
            });
            jQuery('#tipo_proposta option[value="'+valores[1]+'"]').attr("selected", "selected");
            jQuery('#tipo_contrato option[value="'+valores[2]+'"]').attr("selected", "selected");
        }
    }
	
	jQuery('#btn_confirmar_cadastro').click(function() {
		resetFormErros();
		jQuery('.mensagem .erro, .alerta, .sucesso').hide();
		if (validarCamposObrigatorios()) {
	        jQuery("#acao").val('salvar');
	        jQuery("#frm_cadastro_gestor_credito").submit();
		} else {
            jQuery("#msgalerta").html('Campos obrigatórios não preenchidos.');
			jQuery("#msgalerta").showMessage();
            
			return false;
		}
	});
	
	jQuery('#btn_retornar_cadastro').click(function() {
		document.location.href = "cad_parametrizacao_consulta_gestor_credito.php";
	});
	
	jQuery('#btn_novo').click(function() {
         window.location.href = 'cad_parametrizacao_consulta_gestor_credito.php?acao=cadastrar';
   });
	
	
	jQuery("#limite_contrato").mask('9?99999999', { placeholder: "" });
	
	function validarCamposObrigatorios() {
		var valido = true;
		var campos = [];
		if (jQuery("#tipo_pessoa input[type='radio']:checked").val() == null) {
			campos.push({campo: "tipo_pessoa"});
			valido = false;
		} if (jQuery("#tipo_proposta").val() == 0) {
			campos.push({campo: "tipo_proposta"});
			valido = false;
		} if ((jQuery("#subtipo_proposta").val() == 0) && (jQuery("#subtipo_proposta").parent('div').is(":visible"))) {
			campos.push({campo: "subtipo_proposta"});
			valido = false;
		} if (jQuery("#tipo_contrato").val() == "") {
			campos.push({campo: "tipo_contrato"});
			valido = false;
		} if (jQuery("#vaigestor input[type='radio']:checked").val() == null) {
			campos.push({campo: "vaigestor"});
			valido = false;
		} if ((jQuery("#limite_contrato").val() == 0) && (jQuery("#limite_contrato").parent('div').is(":visible"))) {
			campos.push({campo: "limite_contrato"});
			valido = false;
		}
		showFormErros(campos);
		return valido;
	}

	jQuery('#tipoProposta, #tipo_proposta').change(function() {
		carregarSubtipoProposta(jQuery(this));
	});

	jQuery("input[name='vaigestor']").change(function() {        
        if(jQuery('#vaigestor_sim').is(':checked')) {
            jQuery('#limite_contrato').parent('div').hide('');
        }
        
        if(jQuery('#vaigestor_nao').is(':checked')) {
            jQuery('#limite_contrato').parent('div').show('');
            jQuery('#limite_contrato').val('');
        }
                
    });

		jQuery('#pesquisar').click(function(){
            
			jQuery('.mensagem').hideMessage(); 
					
			jQuery.ajax({
				url : 'cad_parametrizacao_consulta_gestor_credito.php',
				type: 'POST',
				data: jQuery('#form').serialize()+'&acao=pesquisar',
				beforeSend: function(){
					jQuery('#resultado_pesquisa').hide();
					jQuery('#conteudo_listagem').html('');
					jQuery('.carregando').fadeIn();
					jQuery('.mensagem').hideMessage();
				},
				success: function(response){                
					
					var data = jQuery.parseJSON(response);
					numero_registros = data.numero_resultados;
                    
                    if (numero_registros > 0) {
					
                        jQuery.each(data.registros, function(i, registro) {

                            var loading_exclusao = '<img src="modulos/web/images/ajax-loader-circle.gif" class="invisivel" id="loading_exclusao_'+registro.gcpoid+'"/>';

                            var tr = '<tr>';
                            var tds = '';
                            tds += '<td>' + registro.vaigestor + '</td>';
                            tds += '<td>' + registro.tipopessoa + '</td>';
                            tds += '<td>';
                            tds += registro.tipoproposta;
                            
                            if(registro.limite == 0) {
                            	registro.limite = '';
                            }

                            if(registro.subtipoproposta) {
                            	tds += ' / ' + registro.subtipoproposta;
                            }
                            
                            tds += '</td>';
                            tds += '<td class="tipoContrato">' + registro.tipocontrato + '</td>';
                            tds += '<td class="direita">' + registro.limite + '</td>';
                            tds += '<td style="width: 100px" align="center"><span><a href="cad_parametrizacao_consulta_gestor_credito.php?acao=cadastrar&id='+registro.gcpoid+'"><img class="icone" width="18" src="images/edit.png"></a></span>';
                            tds += '<span  id="td_exclusao_'+registro.gcpoid+'">'+loading_exclusao+'<a href="javascript:void(0);" class="excluir" id="'+registro.gcpoid+'"><img class="icone" src="images/icon_error.png"></a></span></td>';
                            tr += tds + '</tr>';

                            jQuery('#conteudo_listagem').append(tr);

                        });

                        jQuery('#conteudo_listagem tr:even').addClass('par');   

                        var str_num_resultados = 'Nenhum registro encontrado.';


                        if(numero_registros == 1) {
                            str_num_resultados = '1 registro encontrado.';
                        } else if(numero_registros > 1) {
                            str_num_resultados = numero_registros + ' registros encontrados.';
                        }

                        jQuery('#total_registros').html(str_num_resultados);
					
                        jQuery('#resultado_pesquisa').show();
                    }
                    else {                        
                        jQuery('#msg_alerta').html('Nenhum registro encontrado.');
                        jQuery('#msg_alerta').showMessage();
                    }
				},
				complete: function() {
					jQuery('.carregando').hide();
				}
			});
				
		})        
        
        if (jQuery('#tipoPessoa').val() != '' || jQuery('#tipoProposta').val() != '' || jQuery('#tipoContrato').val() != '') {
            jQuery('#pesquisar').trigger('click');
        }
			
		jQuery('body').delegate('.excluir', 'click', function() {
            
			jQuery('.mensagem').hideMessage(); 
			
            var confirma_exclusao = confirm('Deseja realmente excluir o registro?');
			
			if(confirma_exclusao) {
                
                jQuery('.carregando').show();
                
				var id = jQuery(this).attr('id');
                
				var td = jQuery('#td_exclusao_' + id);
							
				
				jQuery.ajax({
					url : 'cad_parametrizacao_consulta_gestor_credito.php',
					type: 'POST',
					data: {
						acao: 'excluir', 
						id: id 
					},
					beforeSend: function(){
						jQuery('.mensagem').hideMessage();  
					},
					success: function(response){ 
						var data = jQuery.parseJSON(response);
						
						if(data.status) {                        
							jQuery("#msg_sucesso").html('Registro excluído com sucesso.').showMessage();
                            
							td.parents('tr').fadeOut(2000, function() {
								td.parents('tr').remove();
								jQuery('#conteudo_listagem tr').removeClass('par');
								jQuery('#conteudo_listagem tr:even').addClass('par');
								
								numero_registros--;
								
                                var str_num_resultados = 'Nenhum registro encontrado.';
                                
                                if (numero_registros > 0) {

                                    if(numero_registros == 1) {
                                        str_num_resultados = '1 registro encontrado.';
                                    } else if(numero_registros > 1) {
                                        str_num_resultados = numero_registros + ' registros encontrados.';
                                    }

                                    jQuery('#total_registros').html(str_num_resultados);
                                }
                                else {
                                    jQuery('#resultado_pesquisa').hide();
                                }
							});
							
						} else {
							jQuery("#msg_erro").html(data.mensagem).showMessage();
						}
					},
					complete: function() {
                        jQuery('.carregando').hide();
					}
				});				
			}			
		});
	    
	});