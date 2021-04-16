jQuery(function(){
	
	/*
	 * Ao carregar a página popula as combos de teste e obrigações
	 * com valores cadastrados no banco ...
	 */
	jQuery(document).ready(function(){
		
		jQuery.ajax({
			url:  'cad_teste_obrigacao_fin.php',
			type: 'POST',
			data: 'acao=listarTestesCadastrados',
			success: function(data){

				var testes = jQuery.parseJSON(data);

				jQuery.each(testes, function(key, object){
					
					var option = jQuery('<option>');
					
					option.attr('value', object.oid);
					option.html(object.desc);
					
					jQuery('#cb_pesq_teste').last().append(option);

				});
				
				//Esconde indicador de carregamento ...
				jQuery('#pesq_teste_progress').hide();
			}
		})
		
		jQuery.ajax({
			url:  'cad_teste_obrigacao_fin.php',
			type: 'POST',
			data: 'acao=listarObrigacoesCadastradas',
			success: function(data){

				obrigacoes = jQuery.parseJSON(data);

				jQuery.each(obrigacoes, function(key, object){
					
					var option = jQuery('<option>');
					
					option.attr('value', object.oid);
					option.html(object.desc);
					
					jQuery('#cb_pesq_obrigacao').last().append(option);

				});
				
				//Esconde indicador de carregamento ...
				jQuery('#pesq_obrig_progress').hide();
				
			}
		})
	});	
	
	
	/*
	 * Adiciona evento no botão 'Pesquisar'
	 */
	jQuery('#btn_pesquisar').click(function(){
		
		jQuery.ajax({
			url:  'cad_teste_obrigacao_fin.php',
			type: 'POST',
			dataType: 'text',
			data: jQuery('#formPesqTesteObrig').serialize() + '&acao=pesquisar',
			beforeSend: function(){
				jQuery('.sem_resultados').remove();
				jQuery('#tbResultadoPesquisa').hide();
				jQuery('.processando').fadeIn();
			},
			success: function(data){
				try {
					
					var msg;
					
					var result = jQuery.parseJSON(data);
						
					jQuery('#tbResultadoPesquisa').fadeIn();
					
					//Limpa Resultados anteriores ...					
					jQuery('#tbResultadoPesquisa tr.linha_resultado').each(function(i, tr){
						jQuery(tr).remove();
					});
	
					//Adiciona novos resultados na tabela de resultados
					if(result){
						if(!result.error){
							if(result.length > 0){
								jQuery.each(result, function(i, registro){
									
									//Cria elementos ...
									var tr 			= jQuery('<tr>');
									var tdTeste 	= jQuery('<td>');
									var tdObrigacao = jQuery('<td>');
									var tdBtExcluir = jQuery('<td>');   
									
									//Adiciona propriedades e conteudo as celulas...	
									tr.addClass('linha_resultado');
		
									tdBtExcluir.attr('align', 'center');
									tdBtExcluir.css('width',  '5%');
		
									//Adiciona informações do teste e obrigação nas células
									tdTeste.html(registro.desc_teste);
									tdObrigacao.html(registro.desc_obrig);
		
									//Cria botão de exclusão da linha ...
									var btnExcluir = jQuery('<img>');
									
									btnExcluir.attr('src',   'images/icones/x1transparente.gif');
									btnExcluir.attr('alt',   'Excluir');
									btnExcluir.css('cursor', 'pointer');
									
									//Adiciona um evento onclick para chamar função de exclusão
									var dados = {id: registro.eptotoid};
									btnExcluir.bind('click', dados, excluirObrigacaoTeste);
									
									tdBtExcluir.append(btnExcluir);
									
									//Adiciona tds na tr ...
									tr.append(tdTeste);
									tr.append(tdObrigacao);
									tr.append(tdBtExcluir);
									
									//Adiciona a tr na tabela de resultados
									jQuery('#tbResultadoPesquisa').last().append(tr);
								});
						
							} else {
								
								//Ação para caso não haja resultado ...
								var tr = jQuery('<tr>');
								var td = jQuery('<td>');
								
								tr.addClass('sem_resultados');
								
								td.attr('align', 'center');
								td.attr('colspan', '3');
								td.html('Não foram encontrados resultados para pesquisa.');
								
								tr.append(td);
								
								jQuery('#tbResultadoPesquisa').last().append(tr);
							}
							
						} else {
							//Recupera a mensagem do erro vinda do PHP ...
							msg = result.message;
						}
					
					} else {
						msg = 'Não foi possível realizar a pesquisa';
					}
					
					//Zebra a tabela de resultados
					jQuery('#tbResultadoPesquisa .linha_resultado:even').addClass('tdc');
					jQuery('#tbResultadoPesquisa .linha_resultado:odd').addClass('tde');
		
				} catch(e) {
					msg = 'Erro ao realizar a pesquisa: ' + e.message;
				} 
					
				//Esconde indicador de load
				jQuery('.processando').hide();
				
				//Função de mensagem padrão do sistema ...
				if(msg){
					cria_msg_html('div_msg', msg);
				}
	
			}
		});
	});
	
	
	/*
	 * Adiciona evento no botão 'Novo'
	 */
	jQuery('#btn_novo').click(function(){
		
		//Limpa mensagens anteriores se houver
		cria_msg_html('div_msg', '');
		
		//Muda o subtitulo da rotina ...
		jQuery('#hSubTitulo').html('Novo Teste X Obrigação Financeira');
		
		//Esconde o resultado de pesquisas anteriores ...
		jQuery('#tbResultadoPesquisa').hide();
		
		//Reseta seleção do teste e obrigação ...
		jQuery('#cb_pesq_teste').val('');
		jQuery('#cb_pesq_obrigacao').val('');
		
		//Adiciona evento na combo de testes ...
		addEvtChangeComboTeste();
		
		//Esconde botões de pesquisa ...
		jQuery('#trBotoesPesquisa').hide();
		
		//Mostra botões de inclusão ...
		jQuery('#trBotoesInclusao').show();
	
	});
	
	
	/*
	 * Adiciona evento no botão 'salvar'
	 */
	jQuery('#btn_salvar').click(function(){
		
		//Armazena descrição da obrigação que está sendo gravada ...
		var descObrig = jQuery('#cb_pesq_obrigacao :selected').html(); 
		
		var idTeste = jQuery('#cb_pesq_teste').val();
		var idObrig = jQuery('#cb_pesq_obrigacao').val();

		//Validação dos campos teste e obrigação ...
		if(idTeste == '' || idObrig == ''){
			alert("Existem campos obrigatórios não preenchidos");
			return false;
		}
		
		jQuery.ajax({
			url:  'cad_teste_obrigacao_fin.php',
			type: 'POST',
			data: jQuery('#formPesqTesteObrig').serialize() + '&acao=salvar',
			beforeSend: function(){
				jQuery('.sem_resultados').remove();
				jQuery('.processando').fadeIn();
			},
			success: function(data){
				
				try {
					
					var msg;
					
					var result = jQuery.parseJSON(data);
					
					if(result){
						
						if(!result.error){
						
							//Cria elementos ...
							var tr 			= jQuery('<tr>');
							var tdObrigacao = jQuery('<td>');
							var tdBtExcluir = jQuery('<td>');
							
							//Adiciona propriedades e conteudo as celulas...	
							tr.addClass('linha_resultado');
	
							tdBtExcluir.attr('align', 'center');
							tdBtExcluir.css('width',  '5%');
	
							//Adiciona descrição da obrigação na célula ...
							tdObrigacao.html(descObrig);
							
							//Cria botão de exclusão da linha ...
							var btnExcluir = jQuery('<img>');
							
							btnExcluir.attr('src',   'images/icones/x1transparente.gif');
							btnExcluir.attr('alt',   'Excluir');
							btnExcluir.css('cursor', 'pointer');
							
							//Adiciona um evento onclick para chamar função de exclusão
							var dados = { id: result.eptotoid };
							btnExcluir.bind('click', dados, excluirObrigacaoTeste);
							
							tdBtExcluir.append(btnExcluir);
							
							//Adiciona tds na tr ...
							tr.append(tdObrigacao);
							tr.append(tdBtExcluir);
							
							//Adiciona a tr na tabela de resultados
							jQuery('#tbObrigacoesVinculadas').last().append(tr);
							
							//Limpa obrigação selecionada ...
							jQuery('#cb_pesq_obrigacao').val('');
							
							msg = 'Registro gravado com sucesso';
					
						} else {
							//Recupera a mensagem do erro vinda do PHP ...
							msg = result.message;
						}
						
					} else {
						msg = 'Não foi possível gravar o registro';
					}
					
					//Zebra a tabela de resultados - Reset
					jQuery('#tbObrigacoesVinculadas .linha_resultado').removeClass('tdc');
					jQuery('#tbObrigacoesVinculadas .linha_resultado').removeClass('tde');
					
					jQuery('#tbObrigacoesVinculadas .linha_resultado:even').addClass('tdc');
					jQuery('#tbObrigacoesVinculadas .linha_resultado:odd').addClass('tde');
				
				} catch (e) {
					msg = 'Erro ao gravar o registro: ' + e.message;
				}
			
				//Esconde indicador de load
				jQuery('.processando').hide();
				
				//Função de mensagem padrão do sistema ...
				if(msg){
					cria_msg_html('div_msg', msg);
				}
			}
		});
	})
	
	/*
	 * Adiciona evento no botão 'Voltar'
	 */
	jQuery('#btn_voltar').click(function(){
		
		//Limpa mensagens anteriores se houver
		cria_msg_html('div_msg', '');
						
		//Muda o subtitulo da rotina
		jQuery('#hSubTitulo').html('Dados para Pesquisa');
		
		//Esconde o resultado de pesquisas anteriores ...
		jQuery('#tbObrigacoesVinculadas').hide();
		
		//Reseta seleção do teste e obrigação ...
		jQuery('#cb_pesq_teste').val('');
		jQuery('#cb_pesq_obrigacao').val('');
		
		//Retira evento change da combo de testes ...
		jQuery('#cb_pesq_teste').unbind('change');
		
		//Esconde botões de inclusão ...
		jQuery('#trBotoesInclusao').hide();
		
		//Mostra botões de pesquisa ...
		jQuery('#trBotoesPesquisa').show();
		
	});

});


/*
 * Adiciona evento onchange na combo de testes ...
 */
function addEvtChangeComboTeste(){
	jQuery('#cb_pesq_teste').change(function(){
		
		var idTeste = jQuery('#cb_pesq_teste').val();
		
		if(idTeste != '') {
			jQuery.ajax({
				url:  'cad_teste_obrigacao_fin.php',
				type: 'POST',
				data: 'acao=pesquisar&cb_pesq_teste=' + jQuery('#cb_pesq_teste').val(),
				beforeSend: function(){
					jQuery('.sem_resultados').remove();
					jQuery('#tbObrigacoesVinculadas').hide();
					jQuery('.processando').fadeIn();
				},
				success: function(data){
					try {
						
						var msg;
						
						var result = jQuery.parseJSON(data);

						if(result){
							
							//Exibe
							jQuery('#tbObrigacoesVinculadas').fadeIn();
							
							//Limpa Resultados anteriores ...					
							jQuery('#tbObrigacoesVinculadas tr.linha_resultado').each(function(i, tr){
								jQuery(tr).remove();
							});
							
							if(!result.error){
								if(result.length > 0){
									//Adiciona novos resultados na tabela de resultados
									jQuery.each(result, function(i, registro){
										
										//Cria elementos ...
										var tr 			= jQuery('<tr>');
										var tdObrigacao = jQuery('<td>');
										var tdBtExcluir = jQuery('<td>');   
										
										//Adiciona propriedades e conteudo as celulas...	
										tr.addClass('linha_resultado');
		
										tdBtExcluir.attr('align', 'center');
										tdBtExcluir.css('width',  '5%');
		
										//Adiciona informações do teste e obrigação nas células
										tdObrigacao.html( registro.desc_obrig );
		
										//Cria botão de exclusão da linha ...
										var btnExcluir = jQuery('<img>');
										
										btnExcluir.attr('src',   'images/icones/x1transparente.gif');
										btnExcluir.attr('alt',   'Excluir');
										btnExcluir.css('cursor', 'pointer');
										
										//Adiciona um evento onclick para chamar função de exclusão
										var dados = {id: registro.eptotoid};
										btnExcluir.bind('click', dados, excluirObrigacaoTeste);
										
										tdBtExcluir.append(btnExcluir);
										
										//Adiciona tds na tr ...
										tr.append(tdObrigacao);
										tr.append(tdBtExcluir);
										
										//Adiciona a tr na tabela de resultados
										jQuery('#tbObrigacoesVinculadas').last().append(tr);
									});
										
								} else {
									
									//Ação para caso não haja resultado ...
									var tr = jQuery('<tr>');
									var td = jQuery('<td>');
									
									tr.addClass('sem_resultados');
									
									td.attr('align', 'center');
									td.attr('colspan', '2');
									td.html('Não foram encontradas obrigações vinculadas ao teste');
									
									tr.append(td);
									
									jQuery('#tbObrigacoesVinculadas').last().append(tr);
									
								}
								
								//Zebra a tabela de resultados
								jQuery('#tbObrigacoesVinculadas tr.linha_resultado:even').addClass('tdc');
								jQuery('#tbObrigacoesVinculadas tr.linha_resultado:odd').addClass('tde');
							
							} else {
								//Recupera a mensagem do erro vinda do PHP ...
								msg = result.message;
							}
							
						} else {
							msg = 'Não foi possível buscar as obrigações vinculadas ao teste';
						}
					
					} catch (e) {
						msg = 'Erro ao buscar obrigações vinculadas ao teste: ' + e.message;
					} 
					
					//Esconde indicador de load
					jQuery('.processando').hide();
					
					//Função de mensagem padrão do sistema ...
					if(msg){
						cria_msg_html('div_msg', msg);
					}
				}
			})
		}
	})
}

	
/*
 * Inativa o vinculo do teste com a obrigação 
 * (Tabela 'equipamento_projeto_tipo_teste_planejado')
 */
function excluirObrigacaoTeste(event){
		
		//Recupera a linha que está sendo excluída para remover da tabela
		var linha = this.parentNode.parentNode;
		
		var eptotoid = event.data.id;
	
		if(eptotoid != ''){
			if(confirm('Deseja realmente excluir a obrigação financeira do Teste?')){				
				jQuery.ajax({
					url:  'cad_teste_obrigacao_fin.php',
					type: 'POST',
					data: 'acao=excluirObrigacaoTeste&eptotoid=' + eptotoid,
					beforeSend: function(){
						jQuery('.processando').fadeIn();
					},
					success: function(data){
					
						try {
						
						var msg;
						
						var result = jQuery.parseJSON(data);
						
						if(result){
							if(!result.error){
								if(result.status == 1){
									
									//Remove a linha da tabela ...
									jQuery(linha).remove();
									
									//Verifica se o ultimo registro foi excluído
									if(jQuery('#tbObrigacoesVinculadas tr.linha_resultado').length == 0){
										
										//Ação para caso não haja mais linhas no resultado ...
										var tr = jQuery('<tr>');
										var td = jQuery('<td>');
										
										tr.addClass('sem_resultados');
										
										td.attr('align', 'center');
										td.attr('colspan', '2');
										td.html('Não foram encontradas obrigações vinculadas ao teste');
										
										tr.append(td);
										
										jQuery('#tbObrigacoesVinculadas').last().append(tr);
									}
									
									//Zebra a tabela de resultados - Reset
									jQuery('.linha_resultado').removeClass('tdc');
									jQuery('.linha_resultado').removeClass('tde');
									
									jQuery('.linha_resultado:even').addClass('tdc');
									jQuery('.linha_resultado:odd').addClass('tde');
																
									msg = 'Registro excluído com sucesso';
									
								} else {
									msg = 'Não foi possível excluir o registro';
								}
								
							} else {
								//Recupera a mensagem do erro vinda do PHP ...
								msg = result.message;
							}
							
						} else {
							msg = 'Não foi possível excluir vinculo da obrigação com o teste';
						}

					} catch(e) {
						msg = 'Erro ao excluir vinculo da obrigação com o teste' + e.message;
					}
					
					//Esconde indicador de load
					jQuery('.processando').hide();
					
					//Função de mensagem padrão do sistema ...
					if(msg){
						cria_msg_html('div_msg', msg);
					}
					
					//Posiciona a página no topo para visualização da msg ...
					window.scrollTo(0, 0);
				}
			});
		}
	}
}