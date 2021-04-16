var showMessage = 1;

function excluirMaterial(matoid) {
    if (confirm("Deseja excluir esse material do motivo de instalação ?")) {	
        jQuery.ajax({
            url: 'cad_motivos_materiais.php',
            type: 'post',
            data: {
                motivo:jQuery("#motivo").val(),
                produto:jQuery("#produto").val(),
                relacao:matoid,
                acao:'excluir'
            },
            beforeSend: function(){
                jQuery('#relatorio').html('<div style="width: 100%; text-align:center;"><img src="images/loading.gif" alt="" /></div>');
            },
            success: function(data) {
                var resultado = jQuery.parseJSON(data);
                msg = resultado.retorno.msg;
                jQuery('#div_msg').html("<br />"+msg+"<br /><br />");
                jQuery('#div_msg').show();
		
                if (!resultado.retorno.error) {
                    showMessage = 0;
                    
                    // Após exlusão realiza a busca novamente
                    buscarRelatorio();
                    //jQuery("#motivo").change();
                } else {
                    jQuery('#relatorio').html('');
                }
            }
        });
    }
}


function getRelatorio(data) {
    try {	    	
        var resultado = jQuery.parseJSON(data);
        var msg = "";			
        var txt = "";
        txt += '<table class="espaco espaco2">';
        txt += '<tr class="tableSubTitulo">';
        txt += '<td class="celula_relatorio"><b>Material</b></td>';		            	
        txt += '<td class="celula_relatorio"><b>Essencial</b></td>';                     
        txt += '<td class="celula_botao"><b>Excluir</b></td>';
        txt += '</tr>';
		
		
        if (resultado.retorno.error == 1) {
            throw "erro1";
        } else {
            if (msg != null && msg != "" && showMessage) {
                jQuery('#div_msg').html("<br />"+msg+"<br /><br />");
                jQuery('#div_msg').show();
                
            } else if (showMessage) {
                jQuery('#div_msg').html("");
            }
        }
		
        jQuery.each(resultado.relatorio, function() {
            txt += '<tr class="tr_resultado_ajax">';
            txt += '<td class="celula_relatorio">'+this.prdproduto+'</td>';
            txt += '<td class="celula_relatorio">'+this.essencial+'</td>';
            txt += '<td class="celula_botao"><a href="javascript: excluirMaterial('+this.mproid+');">Excluir</a></td>';
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
        if (jQuery("#motivo").val()>0) {
        	jQuery(".moldura2 .celula_label, .moldura2 .celula_input").show();
            jQuery(".moldura2").fadeIn();
        } else {
            jQuery(".moldura2").hide();	
        }
    } catch(e) {
        jQuery('#div_msg').html("<br />Falha ao pesquisar materiais.<br /><br />");
        jQuery('#div_msg').show();
        
        jQuery('#relatorio').html(''); 
    }
        
    showMessage = 1;
}

jQuery(document).ready(function(){
    
    jQuery(".moldura2").hide();
	
    jQuery(document).on("click", ".adicionar", function(){
        var elem = jQuery(this).parents('tr');
        var material = jQuery(elem).data('value');

        if (material > 0) {
            var essencial = 0;
            if (jQuery(elem).find('input[type="checkbox"][name="essencial"]').attr('checked')) {
                essencial = 1;
            }

            jQuery.ajax({
                url: 'cad_motivos_materiais.php',
                type: 'post',
                data: {
                    motivo: document.getElementById('motivo').value,
                    produto: document.getElementById('produto').value,
                    material: material,
                    essencial:essencial,
                    acao:'adicionar'
                },
                beforeSend: function(){
                    jQuery('#relatorio').html('<div style="width: 100%; text-align:center;"><img src="images/loading.gif" alt="" /></div>');
                },
                success: function(data) {
                    var resultado = jQuery.parseJSON(data);
                    msg = resultado.retorno.msg;
                    jQuery('#div_msg').html("<br />"+msg+"<br /><br />");
                    jQuery('#div_msg').show();
                    
                    if (!resultado.retorno.error) {
                        showMessage = 0;
                        
                        // Após inserção realiza a busca novamente
                        buscarRelatorio();

                        if (essencial) {
                            jQuery(elem).find('input[type="checkbox"][name="essencial"]').removeAttr('checked');
                        }
                        //jQuery("#motivo").change();
                    } else {
                        jQuery('#relatorio').html('');
                    }
                }
            });
        }
    });
    
    jQuery(document).on("change", "#tipo", function(){
    	jQuery("#motivo").attr("disabled","disabled");
    	
        jQuery(".moldura2").hide();
        jQuery("#div_msg").hide();
        
        // Se escolheu algum tipo
        if (document.getElementById('tipo').value != ""){
        	jQuery.ajax({
                url: 'cad_motivos_materiais.php',
                type: 'post',
                data: {
                    tipo: document.getElementById('tipo').value,
                    acao:'buscarMotivos'
                },
                beforeSend: function(){
                    jQuery('#relatorio').html('<div style="width: 100%; text-align:center;"><img src="images/loading.gif" alt="" /></div>');
                },
                success: function(data) {
                    jQuery("#motivo").html('<option value="">--Escolha--</option>' + data);
                    jQuery("#motivo").removeAttr("disabled");
                    jQuery("#produto").html('<option value="">--Escolha o Motivo--</option>');
                }
            });
        }else{
        	// Não escolheu nenhum tipo
        	jQuery("#motivo").removeAttr("disabled");
        	jQuery("#motivo").html('<option value="">--Escolha o Tipo--</option>');
            jQuery("#produto").html('<option value="">--Escolha o Motivo--</option>');
        }    
    });
    
    jQuery(document).on("change", "#motivo", function(){
    	// Chama a função para carregar a combo produto
        if (document.getElementById('motivo').value != ""){
    		getProduto();
    	}else{
    		// Não escolheu nenhum motivo
    		jQuery("#produto").html('<option value="">--Escolha o Motivo--</option>');
    	}
    	// Chama a função de buscar o relatorio de materiais.
    	buscarRelatorio();
    });
    
    jQuery(document).on("change", "#produto", function(){
    	// Chama a função de buscar o relatorio de materiais.
    	buscarRelatorio();
    });

    $("#btn_buscar_material").click(function(){
        
        if ($("#material_busca").val().length < 3) {
            $("#div_msg").text("Mínimo de três letras para buscar os materiais.").show();
            return;
        }

        jQuery.ajax({
            url: 'cad_motivos_materiais.php',
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
                    txt += '<td class="celula_relatorio2"><b>Essencial</b></td>';
                    txt += '<td class="celula_botao"><b>Acionar</b></td>';
                    txt += '</tr>';

                    jQuery.each(resultado, function(i, value) {
                        txt += '<tr class="tr_resultado_ajax" data-value="'+value.prdoid+'">';
                        txt += '<td class="celula_relatorio1">'+value.prdproduto+'</td>';
                        txt += '<td class="celula_relatorio2"><input type="checkbox" name="essencial" value="1" /></td>';
                        txt += '<td class="celula_botao"><a  class="adicionar">Adicionar</a></td>';
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
                    $("#div_msg").text("Nenhum material encontrado.").show();
                }
            }
        });
    });

    jQuery("#btn_limpar_busca").click(function(){
        jQuery("#material_busca").val("");
        jQuery('#lista_materiais').html("");
    });
	
});

/**
 * Função: Buscar a lista de produtos, para preenchimento na combo 
*/
function getProduto(){
	jQuery("#produto").html('<option value="">--Escolha--</option>').attr("disabled","disabled");
	
	jQuery.ajax({
        url: 'cad_motivos_materiais.php',
        type: 'post',
        data: {
            acao:'getProdutos'
        },
        success: function(data) {
        	var resultado = jQuery.parseJSON(data);
        	        	
        	if (resultado.error > 0){
        		jQuery("#produto").html('<option value="">--Escolha o Motivo--</option>');
        		jQuery('#div_msg_produto').html("<br />"+resultado.msg+"<br /><br />");
        		jQuery('#div_msg_produto').show();
        	}else{
        		jQuery('#div_msg_produto').hide();

        		jQuery("#produto").html('<option value="">--Escolha--</option>');
        		
            	jQuery.each(resultado, function(i, value){
            		jQuery("#produto").append('<option value="'+value.prdoid+'">'+value.prdproduto+'</option>')
            	});
            	jQuery("#produto").removeAttr("disabled");
        	}
        }
    });
}

/**
 * Função: Buscar o relatorio de Materiais, referentes ao Motivo/Produto
 */
function buscarRelatorio(){
	jQuery.ajax({
        url: 'cad_motivos_materiais.php',
        type: 'post',
        data: {
            motivo: document.getElementById('motivo').value,
            produto: document.getElementById('produto').value,
            acao: 'pesquisar'
        },
        beforeSend: function(){
            jQuery('#relatorio').html('<div style="width: 100%; text-align:center;"><img src="images/loading.gif" alt="" /></div>');
            jQuery(".moldura2 .celula_label, .moldura2 .celula_input").hide();
            jQuery(".moldura2").show();
        },
        success: function(data) {
        	getRelatorio(data);                
        }
    });
	
}