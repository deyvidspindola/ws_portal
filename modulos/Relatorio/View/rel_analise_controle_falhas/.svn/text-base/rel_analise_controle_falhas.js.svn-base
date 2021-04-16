jQuery(function(){
	
	jQuery('select').css('width', '350');
	
	/*
	 * bloqueia ação do backspace afim de evitar que volte a página anterior
	 * mas permite que seja utilizado o backspace em campos tipo texto
	 */
	jQuery(document).unbind('keydown').bind('keydown',function(e){
		if(e.keyCode == 8){
			if (e.target.nodeName != 'INPUT' && e.target.nodeName != 'TEXTAREA') {
				return false;	
			}
		}
	});
	
	function validaForm() {
		
		jQuery('#span_erro').empty();
		jQuery('#span_msg').empty();
		
		removeAlerta();
		jQuery('#data_entrada_lab_inicial').removeClass('inputError');
		jQuery('#data_entrada_lab_final').removeClass('inputError');
		
		if (
                    jQuery('#serial').val() == '' 
                    && jQuery('#nota_fiscal').val() == '' 
                    && jQuery('#placa').val() == '' 
                    && jQuery('#telefone').val() == '' 
                    && jQuery('#data_abertura_os_inicial').val() == '' 
                    && jQuery('#data_abertura_os_final').val() == ''                
                    && jQuery('#data_conclusao_os_inicial').val() == '' 
                    && jQuery('#data_conclusao_os_final').val() == ''                
                    && jQuery('#data_emissao_nf_inicial').val() == '' 
                    && jQuery('#data_emissao_nf_final').val() == ''
                )
		{
			if (jQuery('#data_entrada_lab_inicial').val() == '' || jQuery('#data_entrada_lab_final').val() == '') 
			{
				jQuery('#data_entrada_lab_inicial').addClass('inputError');
				jQuery('#data_entrada_lab_final').addClass('inputError');
				criaAlerta('Preencher o campo Data Entrada Lab.');
				
				return false;
			}			
		}
	
		return true;	
	}
	
	jQuery('#btn_pesquisar').click(function(){
		
		if(validaForm() == false) return false; 
		
		jQuery('#acao').val('pesquisar');
		jQuery('#form').submit();
	
	});
	
	jQuery('#btn_gerarCSV').click(function(){
		
		if(validaForm() == false) return false;
		
		jQuery.ajax({
			url : 'rel_analise_controle_falhas.php',
			type: 'POST',
			data: jQuery('#form').serialize() + '&acao=gerarCSV_ajax',
			beforeSend: function() 
			{
				jQuery('#btn_gerarCSV').attr('disabled', 'disabled');     
				jQuery('#lista_resultados').empty();
				jQuery('#loading').html('<img src="images/loading.gif" alt="" />');		
			},
			success: function(data) 
			{
                            try 
                            {								
                                    result = jQuery.parseJSON(data);	

                                    if(result.erro) 
                                    {
                                            jQuery('#span_erro').text(result.erro); 
                                            jQuery('#loading').empty();	
                                    }
                                    else 
                                    {
                                            jQuery('#span_msg').text(result.msg);
                                            jQuery('#loading').html("<br><center><a onclick='' href='download.php?arquivo="+result.file_name+"'><img src='images/icones/t3/caixa2.jpg'><br>Download do arquivo Relatório Análise e Controle de Falhas</a></center>");
                                    }
                            } 
                            catch (e) 
                            {
                                    jQuery('#span_erro').val('Falha ao gerar o arquivo');
                                    jQuery('#loading').empty();	
                            }
			}, 
			complete: function() 
			{
				jQuery('#btn_gerarCSV').removeAttr('disabled');
			}
			
		});

	});
	
	jQuery('#modelo_equipamento').change(function() {
		
		jQuery.ajax({
			url : 'rel_analise_controle_falhas.php',
			type: 'POST',
			data: 'modelo_equipamento='+jQuery(this).val()+'&acao=listarVersoesEquipamentos_ajax',
			beforeSend: function() {
				jQuery('#versao_equipamento').attr('disabled', 'disabled');
			},
			success: function(data) {
				
				try {
					
					result = jQuery.parseJSON(data);		
					
					str = '';
					jQuery.each(result, function(k, i) {
						str += '<option value="'+i.id+'">'+i.descricao+'</option>';	
					});
					
					jQuery('#versao_equipamento').html(str);
					
				} catch (e) {
					jQuery('#versao_equipamento').html('<option value="">- Falha na busca -</option>');
				}
				
			},
			complete: function() {
				jQuery('#versao_equipamento').removeAttr('disabled');
			}
		});
	});
	
});