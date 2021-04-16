jQuery(document).ready(function(){
    
    jQuery('.tpsprincipal').change(function() {        
        
        if(jQuery('#tpsprincipal_sim').is(':checked')) {
            jQuery('#combo_principal_novo').fadeOut();
            jQuery('#tpssegmentacao').val('');
        }
        
        if(jQuery('#tpsprincipal_nao').is(':checked')) {
            jQuery('#combo_principal_novo').fadeIn();
        }
                
    });
        
    jQuery('.tpsprincipal').trigger('change');
    
    jQuery('#btn_inserir, #btn_editar').click(function(){
        
        resetFormErros();
        
        var id_btn = jQuery(this).attr('id');
        var formSelector = id_btn == 'btn_inserir' ? '#form_inserir' : '#form_editar';
               
        jQuery.ajax({
            url: 'cad_tipo_segmentacao.php',
            type: 'post',
            data: jQuery(formSelector).serialize(),
            beforeSend: function() {
                jQuery('#msg_erro, #msg_sucesso, #msg_alerta').hideMessage();
                jQuery('.loading').fadeIn();
                jQuery('#'+id_btn).attr('disabled', 'disabled');
            },
            success: function(response) {
                
                try {
                    
                    var data = jQuery.parseJSON(response);
                    var tamanhoDivCombo = jQuery('#combo_principal_novo').width();
                    
                    if(data.status) {
                        resetFormErros();
                        
                        var tipo_principal = jQuery('#tpsprincipal_sim').is(':checked');
                        
                        if(formSelector == '#form_inserir') {
                            jQuery('#form_inserir')[0].reset();
                            jQuery('#combo_principal_novo').fadeOut();
                            jQuery("#msg_sucesso").html('Registro incluído com sucesso.').showMessage();
                        } else {
                            jQuery("#msg_sucesso").html('Registro alterado com sucesso.').showMessage();
                        }
                        
                        if(tipo_principal) {
                            //Recarrega combo
                            jQuery.ajax({
                                url: 'cad_tipo_segmentacao.php',
                                type: 'post',
                                data: {
                                    acao: 'recarregaComboSegmentacao'
                                },  
                                beforeSend: function() {
                                    jQuery('#combo_principal_novo').css('width', (tamanhoDivCombo+10)+'px');
                                    jQuery('.loaging-circle').show();
                                },
                                success: function(response) {
                                    var data = jQuery.parseJSON(response);

                                    jQuery('#tpssegmentacao option').remove();
                                    jQuery('#tpssegmentacao').append(jQuery('<option></option>').attr("value", '').text('Escolha'));

                                    jQuery.each(data, function(i, value){                                    
                                        jQuery('#tpssegmentacao').append(jQuery('<option></option>').attr("value", value.tpsoid).text(value.tpsdescricao));
                                    });

                                },
                                complete: function() {
                                    jQuery('#combo_principal_novo').css('width', tamanhoDivCombo + 'px');
                                    jQuery('.loaging-circle').hide();
                                }
                            });
                        }
                        
                    } else {
                        jQuery(".mensagem."+data.tipoErro).html(data.mensagem).showMessage();
                        showFormErros(data.dados);
                    }
                    
                } catch(e) {
                    jQuery('#msg_erro').html('Houve algum erro no processamento dos dados.').showMessage();
                }
            },
            complete: function() {
                jQuery('.loading').fadeOut();
                jQuery('#'+id_btn).removeAttr('disabled');
            }
        });
        
    });
 
    jQuery('#pesquisar').click(function(){
				
        jQuery.ajax({
            url : 'cad_tipo_segmentacao.php',
            type: 'POST',
            data: jQuery('#form').serialize()+'&acao=pesquisar',
            beforeSend: function(){
                jQuery('#resultado_pesquisa').hide();
                jQuery('#conteudo_listagem').html('');
                jQuery('.loading').fadeIn();
                jQuery('.mensagem').hideMessage();
                jQuery('#pesquisar').attr('disabled', 'disabled');
            },
            success: function(response){                
                
                var data = jQuery.parseJSON(response);
                
                jQuery.each(data.registros, function(i, registro) {
                    
                    var loading_exclusao = '<img src="modulos/web/images/ajax-loader-circle.gif" class="invisivel" id="loading_exclusao_'+registro.tpsoid+'"/>';
                    
                    var tr = '<tr>';
                    var tds = '<td class="tpsdescricao">' + registro.tpsdescricao + '</td>';
                    tds += '<td>' + registro.tipo_segmentacao_pai + '</td>';
                    tds += '<td class="centro"><a href="cad_tipo_segmentacao.php?acao=editar&id='+registro.tpsoid+'" target="_blank"><img width="18" class="icone" src="images/edit.png"></a></td>';
                    tds += '<td class="centro" id="td_exclusao_'+registro.tpsoid+'">'+loading_exclusao+'<a href="javascript:void(0);" class="excluir" id="'+registro.tpsoid+'"><img class="icone" src="images/icon_error.png"></a></td>';
                    tr += tds + '</tr>';
                    
                    jQuery('#conteudo_listagem').append(tr);                     
                    
                });
               
				
                jQuery('#conteudo_listagem tr:even').addClass('par');   
				
                var str_num_resultados = 'Pesquisa sem resultados.';
                numero_registros = data.numero_resultados;
                
                
                if(numero_registros == 1) {
                    str_num_resultados = '1 registro encontrado.';
                } else if(numero_registros > 1) {
                    str_num_resultados = numero_registros + ' registros encontrados.';
                }
                
                jQuery('#total_registros').html(str_num_resultados);
				
            },
            complete: function() {
                jQuery('.loading').hide();  
                jQuery('#resultado_pesquisa').show();
                jQuery('#pesquisar').removeAttr('disabled');
            }
        });
            
    })
		
    jQuery('body').delegate('.excluir', 'click', function() {
		
        var segmentacao = jQuery(this).parents('tr').find('.tpsdescricao').html();
        
        var confirma_exclusao = confirm('Deseja realmente excluir o tipo ' + segmentacao + '?');
        
        if(confirma_exclusao) {
            var id = jQuery(this).attr('id');
            
            var self = jQuery(this);
            var loading = jQuery('#loading_exclusao_' + id);            
            var td = jQuery('#td_exclusao_' + id);                        
            
            jQuery.ajax({
                url : 'cad_tipo_segmentacao.php',
                type: 'POST',
                data: {
                    acao: 'excluir', 
                    id: id, 
                    descricao: segmentacao
                },
                beforeSend: function(){
                    jQuery('.mensagem').hideMessage();
                    self.hide();
                    loading.fadeIn();                    
                },
                success: function(response){ 
                    var data = jQuery.parseJSON(response);
                    
                    if(data.status) {                        
                        jQuery("#msg_sucesso").html('Registro excluído.').showMessage();                        
                        
                        td.parents('tr').fadeOut(2000, function() {
                            td.parents('tr').remove();
                            jQuery('#conteudo_listagem tr').removeClass('par');
                            jQuery('#conteudo_listagem tr:even').addClass('par');
                            
                            numero_registros--;
                            
                            var str_num_resultados = 'Pesquisa sem resultados.';
                            
                            if(numero_registros == 1) {
                                str_num_resultados = '1 registro encontrado.';
                            } else if(numero_registros > 1) {
                                str_num_resultados = numero_registros + ' registros encontrados.';
                            }

                            jQuery('#total_registros').html(str_num_resultados);
                            
                        });
                        
                    } else {
                        jQuery(".mensagem."+data.tipoErro).html(data.mensagem).showMessage();
                    }
                    
                    
                },
                complete: function() {
                    loading.hide();    
                    self.show();
                }
            });
            
        }
        
    });
   
    jQuery('#btn_novo').click(function(){
        window.location.href = 'cad_tipo_segmentacao.php?acao=novo';
    });
    
    jQuery('#btn_voltar').click(function(){
        window.location.href = 'cad_tipo_segmentacao.php';
    });
    
});
 