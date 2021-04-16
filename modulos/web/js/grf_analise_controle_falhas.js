/**
 * document.onLoad
 */
jQuery(document).ready(function() {
	
	jQuery('#data_inicio').periodo('#data_fim');
	
	/**
	 * form[id=frm].onSubmit
	 */
	jQuery('#frm').submit(function() {
		var formulario = jQuery(this);
		
		// LIMPA CAMPOS E MENSAGENS
		limpaCampos();
		
		jQuery('#btn_pesquisar').attr('disabled', 'disabled');
		jQuery('.separador').hide('fast');
		jQuery('.resultado').hide('fast')
		jQuery('.grafico').html('');
		jQuery('.listagem').html('');
		jQuery('.carregando').showMessage();
		
		/*
		 * AJAX
		 */
		jQuery.ajax({
			data     : formulario.serialize(),
			dataType : 'json',
			type     : 'post',
			url      : formulario.attr('action'),
			complete : function() {
				jQuery('#btn_pesquisar').removeAttr('disabled');
				jQuery('.carregando').hideMessage();
			},
			error    : function(data, status, error) {
				jQuery('.mensagem')
					.addClass('erro')
					.html('Erro ao gerar gráfico. (motivo: ' + status + ')');
				jQuery('.mensagem').showMessage();
				
			},
			success  : function(response) {
				// ERRO DE SESSÃO
				if(response.status == 'errorlogin' && response.redirect) {
					location.href = response.redirect;
				// PROCESSA O RESULTADO
				} else if(response.status) {
					// APRESENTA O GRÁFICO
					if(response.dados) {
						jQuery('.separador').show('fast');
						jQuery('.resultado').show('fast');
						
						jQuery('.grafico').append(
							jQuery('<img>')
								.attr(
									'src',
									'images/grafico/_AnaliseControleFalhas-'
										+ jQuery('#tipo_grafico').val()
										+ '-'
										+ response.imagem
										+ '.jpg?'
										+ Math.random()
								)
						);
						
						switch(jQuery('#tipo_grafico').val()) {
							case "DefeitoLabRvs" :
								gerarLegendaRvs(response.dados);
								break;
							case "MtbfLocacao" :
							case "MtbfVenda" :
								break;
                            case "Aging":
                                gerarLegendaAging(response.dados);
                                break;
							default :
								gerarLegenda(response.dados);
						}
					// APRESENTA A MENSAGEM: "Nenhum resultado encontrado."
					} else {
						jQuery('.mensagem')
							.addClass('alerta')
							.html(response.mensagem);
						jQuery('.mensagem').showMessage();
					}
				// ERRO DE PROCESSAMENTO
				} else {
					destacaCamposIncorretos(response.dados);
					
					if(response.mensagem) {
						jQuery('.mensagem').html(response.mensagem);
					} else {
						jQuery('.mensagem').html('Erro ao gerar gráfico.');
					}
				}
			}
		});
		
		return false;
	});
	
});

/**
 * Gera a legenda do gráfico
 * 
 * @param object datas
 * @returns {Boolean}
 */
function gerarLegenda(datas) {
	/*
	 * VARIÁVEIS
	 */
	var counter  = 1;
	var months   = [
		"Janeiro",
		"Fevereiro",
		"Março",
		"Abril",
		"Maio",
		"Junho",
		"Julho",
		"Agosto",
		"Setembro",
		"Outubro",
		"Novembro",
		"Dezembro"
	];
	var percent = "";
	
	switch(jQuery('#tipo_grafico').val()) {
		case "ComDefeitoLabBaseInstalada" :
		case "ComDefeitoLabRetiradaBaseInstalada" :
			percent = "%";
			break;
	}
	
	// CRIA A TABELA
	jQuery('.listagem')
		.append(
			jQuery('<table>')
				.append(
					jQuery('<thead>').append(
						jQuery('<tr>')
							.append(
								jQuery('<th>').html('&nbsp;')
							)
							.append(
								jQuery('<th>').html('&nbsp;')
							)
					)
				).append(
					jQuery('<tbody>')
				)
		);
	
	var thead = jQuery('.listagem > table > thead > tr');
	var tbody = jQuery('.listagem > table > tbody');
	
	// POPULA A TABELA
	jQuery.each(datas, function(date, dt_details) {
		tbody.append('<tr>');
		
		var ticket = "";
		var tr     = tbody.children('tr:last-child');
		
		// DEFINE A CLASSE DA LINHA ATUAL
		if(counter % 2 == 0) {
			tr.addClass('par');
		}
		
		// DEFINE O "TÍTULO" DA LINHA ATUAL
		if(date == 'media') {
			ticket = "Média"
		} else {
			date = date.split('-');
			
			ticket = months[date[1] - 1] + ' ' + date[0];
		}
		
		// ADICIONA AS COLUNAS PRINCIPAIS (imagem e "título")
		tr
			.append(
				jQuery('<td>')
					.addClass('centro')
					.append(
						jQuery('<img>')
							.attr('src', 'images/grafico/ic_' + dt_details.cor + '.png')
					)
			)
			.append(
				jQuery('<td>')
					.attr('nowrap', 'nowrap')
					.addClass('negrito')
					.html(ticket)
			);
		
		// ADICIONA AS COLUNAS DE VALORES
		jQuery.each(dt_details.dados, function(project, amount) {
			// ADICIONA OS CABEÇALHOS DAS COLUNAS
			if(counter == 1) {
				thead
					.append(
						jQuery('<th>')
							.attr('nowrap', 'nowrap')
							.addClass('centro')
							.html(project)
					);
			}
			
			tr.append(
				jQuery('<td>')
					.addClass('direita')
					.html(amount + percent)
			);
		});
		
		counter++;
	});
	
	return true;
}


function gerarLegendaAging(dados){
	
	// CRIA A TABELA
	jQuery('.listagem')
		.append(
			jQuery('<table>')
				.append(
					jQuery('<thead>').append(jQuery('<tr>'))
				).append(jQuery('<tbody>'))
		);
	
	var thead = jQuery('.listagem > table > thead > tr');
	var tbody = jQuery('.listagem > table > tbody');
    
    //Contador das linhas e cabeçalhos
    var counter = 0;
    
    //Armazena os equipamentos já adicionados
    var equipamentos = [];
    
    
    //Adiciona os cabecalhos
    jQuery.each(dados, function(periodo, periodo_detalhes) {
        counter++;
        
        if (counter == 1){
            thead.append(
                jQuery('<th>')
                    .html("&nbsp;")
            );
        } 
        periodo = (periodo.indexOf('+1') < 0) ? periodo + ' dias' : periodo;
        thead.append(
            jQuery('<th>')
                .attr("colspan", 2)
                .attr('width', '100')
                .addClass('centro')
                .html(periodo)
        );
                
        //Popula a tabela
        jQuery.each(periodo_detalhes.dados, function(equipamento, equipamento_detalhes) {
            var tr;
            
            if (!in_array(equipamento, equipamentos)){
                
                tr = jQuery('<tr>')
                        .attr('id', 'equipamento_' + equipamento_detalhes.cor);
                tr.append(
                    jQuery('<td>')
                        .addClass('negrito')                        
                        .append(
                            jQuery('<img>')
                                .attr('src', 'images/grafico/ic_' + equipamento_detalhes.cor + '.png')
                        )
                        .attr('nowrap', 'nowrap')
                        .append(document.createTextNode(' % Falhas ' + equipamento))
                );
                equipamentos.push(equipamento);
                
                // DEFINE A CLASSE DA LINHA ATUAL
                if(counter % 2 == 0) {
                    tr.addClass('par');
                }
                counter++;
                tbody.append(tr);
            }
            
            tr = jQuery('#equipamento_'+equipamento_detalhes.cor);
            tr.append(
                jQuery('<td>')
                    .attr('width', '45')
                    .addClass('centro')
                    .html(equipamento_detalhes.percentual+'%')
            );
            tr.append(
                jQuery('<td>')
                    .attr('width', '45')
                    .addClass('centro')
                    .html(equipamento_detalhes.qtd_equipamentos)
            );

        });
        
    });
    
    return true;
	
	// POPULA A TABELA
	jQuery.each(datas, function(bug, bug_details) {
		tbody.append('<tr>');
		
		var tr = tbody.children('tr:last-child');
		
		// DEFINE A CLASSE DA LINHA ATUAL
		if(counter % 2 == 0) {
			tr.addClass('par');
		}
		
		// ADICIONA AS COLUNAS PRINCIPAIS (imagem e "título")
		tr
			.append(
				jQuery('<td>')
					.addClass('centro')
					.append(
						jQuery('<img>')
							.attr('src', 'images/grafico/ic_' + bug_details.cor + '.png')
					)
			)
			.append(
				jQuery('<td>')
					.attr('nowrap', 'nowrap')
					.addClass('negrito')
					.html(bug)
			);
		
		// ADICIONA AS COLUNAS DE VALORES
		jQuery.each(bug_details.dados, function(date, date_details) {
			// ADICIONA OS CABEÇALHOS DAS COLUNAS
			if(counter == 1) {
				date = date.split('-');
				
				thead
					.append(
						jQuery('<th>')
							.attr('nowrap', 'nowrap')
							.addClass('centro')
							.html(months[date[1] - 1] + ' ' + date[0])
					);
			}
			
			tr.append(
				jQuery('<td>')
					.addClass('direita')
					.html(date_details.porcentagem + '%')
			);
		});
		
		counter++;
	});
	
	return true;
}


/**
 * Gera a legenda do gráfico
 * 
 * @param object datas
 * @returns {Boolean}
 */
function gerarLegendaRvs(datas) {
	/*
	 * VARIÁVEIS
	 */
	var counter  = 1;
	var months   = [
		"Janeiro",
		"Fevereiro",
		"Março",
		"Abril",
		"Maio",
		"Junho",
		"Julho",
		"Agosto",
		"Setembro",
		"Outubro",
		"Novembro",
		"Dezembro"
	];
	
	// CRIA A TABELA
	jQuery('.listagem')
		.append(
			jQuery('<table>')
				.append(
					jQuery('<thead>').append(
						jQuery('<tr>')
							.append(
								jQuery('<th>').html('&nbsp;')
							)
							.append(
								jQuery('<th>').html('&nbsp;')
							)
					)
				).append(
					jQuery('<tbody>')
				)
		);
	
	var thead = jQuery('.listagem > table > thead > tr');
	var tbody = jQuery('.listagem > table > tbody');
	
	// POPULA A TABELA
	jQuery.each(datas, function(bug, bug_details) {
		tbody.append('<tr>');
		
		var tr = tbody.children('tr:last-child');
		
		// DEFINE A CLASSE DA LINHA ATUAL
		if(counter % 2 == 0) {
			tr.addClass('par');
		}
		
		// ADICIONA AS COLUNAS PRINCIPAIS (imagem e "título")
		tr
			.append(
				jQuery('<td>')
					.addClass('centro')
					.append(
						jQuery('<img>')
							.attr('src', 'images/grafico/ic_' + bug_details.cor + '.png')
					)
			)
			.append(
				jQuery('<td>')
					.attr('nowrap', 'nowrap')
					.addClass('negrito')
					.html(bug)
			);
		
		// ADICIONA AS COLUNAS DE VALORES
		jQuery.each(bug_details.dados, function(date, date_details) {
			// ADICIONA OS CABEÇALHOS DAS COLUNAS
			if(counter == 1) {
				date = date.split('-');
				
				thead
					.append(
						jQuery('<th>')
							.attr('nowrap', 'nowrap')
							.addClass('centro')
							.html(months[date[1] - 1] + ' ' + date[0])
					);
			}
			
			tr.append(
				jQuery('<td>')
					.addClass('direita')
					.html(date_details.porcentagem + '%')
			);
		});
		
		counter++;
	});
	
	return true;
}

/**
 * Verifica se "needle" existe em "haystack"
 * Se existir retorna a posição dele no array
 * 
 * @param string needle
 * @param array  haystack
 * @returns {Mixed}
 */
function array_search(needle, haystack) {
	for(i = 0; i < haystack.length; i++) {
		if(haystack[i] === needle) {
			return i;
		}
	}
	
	return false;
}

/**
 * Verifica se "needle" existe em "haystack"
 * 
 * @param string needle
 * @param array  haystack
 * @returns {Boolean}
 */
function in_array(needle, haystack) {
	for(i = 0; i < haystack.length; i++) {
		if(haystack[i] === needle) {
			return true;
		}
	}
	
	return false;
}