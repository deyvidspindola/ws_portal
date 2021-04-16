jQuery(function(){
			
	/**
	 * Popula a combo Tipo de operação
	 */
    /*jQuery('#tipo_operacao').html('');
    jQuery('#tipo_operacao').append('<option value="">Todos</option>');
    jQuery('#tipo_operacao').append('<option value="A">Alteração</option>');
    jQuery('#tipo_operacao').append('<option value="E">Exclusão</option>');
    jQuery('#tipo_operacao').append('<option value="I">Inclusão</option>');*/
	
	/**
	 * Popula a combo Canal de Entrada
	 */
    /*jQuery('#canal_entrada').html('');
    jQuery('#canal_entrada').append('<option value="">Todos</option>');
    jQuery('#canal_entrada').append('<option value="I">Intranet</option>');
    jQuery('#canal_entrada').append('<option value="P">Portal</option>');*/
	
	/**
	 * Popula a combo Motivo
	 */
    /*jQuery.ajax({
        url: 'rel_debito_automatico.php',
        type: 'POST',
        data: {
            acao: 'getMotivos'
        },
		success: function(data){
			
			var resultado = jQuery.parseJSON(data);
						
            jQuery('#motivo').html('');
            jQuery('#motivo').append('<option value="">Todos</option>');
            jQuery.each(resultado, function(i, motivo){
                jQuery('#motivo').append('<option value="'+motivo.id+'">'+motivo.descricao+'</option>');
            })
        }			
    });*/
	
	jQuery('#tipo_relatorio').change(function(){
		var res = jQuery('#tipo_relatorio').val();
		if (res == "analitico")
			jQuery('#trResultado').hide();
		else
			jQuery('#trResultado').show();
			
	});
	
	
    /**
    * Click do botao pesquisar
    */
	jQuery('#bt_pesquisar').click(function(){
		
        removeAlerta();
        
        /**
        * Limpa o campo CSV para garantir pois ja faz isso no submit do botao gerar_csv
        */
	if (diferencaEntreDatas($("#data_fim_pesquisa").val(), $("#data_inicio_pesquisa").val()) < 0){
            criaAlerta("Período inicial não pode ser maior que o período final."); 
            jQuery("#data_fim_pesquisa").addClass("inputError");
            return false;	
        }
		
		/**
		 * Limpa o campo CSV para garantir pois ja faz isso no submit do botao gerar_csv
		 */
		if (diferencaEntreDatas($("#data_fim_pesquisa").val(), $("#data_inicio_pesquisa").val()) < 0){
			criaAlerta("Data final menor que a data inicial."); 
			jQuery("#data_fim_pesquisa").addClass("inputError");
			return false;	
		}
		
		if(jQuery.trim(jQuery('#data_inicio_pesquisa').val()).length == 0 || jQuery.trim(jQuery('#data_fim_pesquisa').val()).length == 0){
			
            criaAlerta('Existem campos obrigatórios não preenchidos.');
						
			if(jQuery.trim(jQuery('#data_inicio_pesquisa').val()).length == 0){
				jQuery('#data_inicio_pesquisa').css('background', 'rgb(255, 255, 192)');		
			}else{
				jQuery('#data_inicio_pesquisa').css('background', '#FFF');
			}
			
			if(jQuery.trim(jQuery('#data_fim_pesquisa').val()).length == 0){
				jQuery('#data_fim_pesquisa').css('background', 'rgb(255, 255, 192)');		
			}else{
				jQuery('#data_fim_pesquisa').css('background', '#FFF');
			}	
			
		}else{
						
			removeAlerta();
			
			jQuery('#data_inicio_pesquisa').css('background', '#FFF'); 
			jQuery('#data_fim_pesquisa').css('background', '#FFF');
			
			if(jQuery('#gerar_csv').val()!='t'){
				jQuery('#div_pesquisar').show();
				jQuery('#bt_pesquisar').hide();
				
				
				jQuery('#bt_cancelar').fadeIn();
			}
			xhr = jQuery.ajax({
                async: false,
					url: 'rel_debito_automatico.php',
					type: 'POST',
					data: jQuery('#frm').serialize()+'&acao=pesquisar',
					beforeSend: function(){
						
                    jQuery('.grafico_pesquisa').hide();
						if(jQuery('#gerar_csv').val()!='t'){
							
							jQuery('.resultado_pesquisa').hide();
							jQuery('.processando').show();
							
						}else{
							jQuery('.processando_csv').show();
						}
						
					},
					success: function(data){
						try{
						// Transforma a string em objeto JSON
						var resultado = jQuery.parseJSON(data);

                        if (jQuery('#tipo_relatorio').val() == 'analitico'){
                            
                            relatorioAnalitico(resultado);
                        }
                        else if (jQuery('#tipo_relatorio').val() == 'sintetico'){
	                        relatorioSintetico(resultado, jQuery('input[name=resultado]:checked').val());
	                        jQuery('#grafico').html('');                            
	                        graficoSintetico(resultado); 
	                        jQuery('#grafico').html('');
	                        graficoSintetico(resultado);     
                            
                        }
					
                    }catch(e){
					
                        regexp = /{.*}/;
                        teste = regexp.exec(data);
                        codigo = teste[0].split('"');
                        jQuery("#div_msg_pesquisar").html('Erro de processamento.');
                        jQuery('#div_msg_pesquisar').show();
                        jQuery('#bt_cancelar').hide();
                        jQuery('#bt_pesquisar').fadeIn(1000);
                        jQuery('.processando').hide();						
                        jQuery('.grafico_pesquisa').hide();
                        
                    }		
                }
            });          
            
        }
        
    })
	
    
    function graficoSintetico(resultado){
        
        var ticks = [];
        var inclusao = [];
        var alteracao = [];
        var suspensao = [];
        var exclusao = [];
        var width = 0;
        var maximo = 0;
        
        if (resultado.pesquisa != null) {        
	        jQuery.each(resultado.pesquisa, function(i, historico){
	            ticks.push(i);
	            inclusao.push(historico['I']);
	            if (parseInt(historico['I']) > maximo){maximo = parseInt(historico['I']);}
	            
	            alteracao.push(historico['A']);
	            if (parseInt(historico['A']) > maximo){maximo = parseInt(historico['A']);}
	            
	            suspensao.push(historico['S']);
	            if (parseInt(historico['S']) > maximo){maximo = parseInt(historico['S']);}
	            
	            exclusao.push(historico['E']);
	            if (parseInt(historico['E']) > maximo){maximo = parseInt(historico['E']);}
	            
	            width += 150;
	        });        
	        
	        jQuery('#grafico').css('width', width.toString()+'px');
	        
	        var plot1 = $.jqplot('grafico', [inclusao, alteracao, suspensao, exclusao], {
	
	            grid: {
	                borderColor: '#FFFFFF',    
	                borderWidth: 0,          
	                shadow: false
	            },
	
	            seriesDefaults:{
	                renderer:$.jqplot.BarRenderer,
	                rendererOptions: {
	                    barPadding: 8,                                            
	                    barMargin: 20,     
	                    barDirection: 'vertical',
	                    barWidth: null
	                }
	            },
	
	            series:[                
	                {label:'Total Inclusão'},
	                {label:'Total Alteração'},
	                {label:'Total Suspensão'},
	                {label:'Total Exclusão'}
	            ],
	
	            seriesColors: [ 
	                "#00B200", 
	                "#1484CB", 
	                "#FF9326", 
	                "#FF2626"
	            ],
	
	            legend: {
	                show: true,
	                placement: 'outside'                
	            },
	            
	            
	            
	            axes: {
	                xaxis: {
	                    renderer: $.jqplot.CategoryAxisRenderer,
	                    ticks: ticks
	                },
	                yaxis:{
	                    min:0, 
	                    max:maximo
	                }
	            }
	        });
	        
	        jQuery('table [class=jqplot-table-legend]').css('width', '200px');
	        jQuery('.jqplot-table-legend-swatch-outline').css('border', '0');
	        
	        jQuery('.grafico_pesquisa').show();
        }
    }

    /**
    * Clique do botao cancelar pesquisa
    */
    jQuery('#bt_cancelar').click(function(){
        
        xhr.abort();
        jQuery('#bt_cancelar').hide();
        jQuery('.processando').hide();
        jQuery('#div_pesquisar').hide();
        jQuery('#bt_pesquisar').show();
		
    })
	
	
    /**
    * Clique do botão Gerar CSV
    * */
    jQuery('body').delegate('#bt_gerar_csv', 'click', function(){
		
        jQuery('#gerar_csv').val('t');
        jQuery('#bt_pesquisar').click();
		
    })

    
    function relatorioSintetico(resultado, tipo){
    	tr = '';
						tr += '<tr class="tableSubTitulo">';
        tr += '<td colspan="6"><h2>Resultado da Pesquisa</h2></td>';
        tr += '</tr>';
                
        tr += '<tr class="tableTituloColunas tab_registro">';
        tr += '<td width="25%" align="center"><h3>Data</h3></td>';
        tr += '<td width="10%" align="center"><h3>Total Inclusão</h3></td>';
        tr += '<td width="10%" align="center"><h3>Total Alteração</h3></td>';
        tr += '<td width="10%" align="center"><h3>Total Suspensão</h3></td>';
        tr += '<td width="10%" align="center"><h3>Total Exclusão</h3></td>';
        if (tipo == "D")
        	tr += '<td width="20%" align="center"><h3>Total do Dia</h3></td>';
        else
        	tr += '<td width="20%" align="center"><h3>Total do Mês</h3></td>';

        tr += '</tr>';
              
        if(resultado.pesquisa != null){
            var dia = 0;
            var total_i = 0;
            var total_a = 0;
            var total_s = 0;
            var total_e = 0;            
            var total_dia = 0;
            
            jQuery.each(resultado.pesquisa, function(i, historico){
                
                if (historico.A == undefined){historico.A = 0;} 
                if (historico.I == undefined){historico.I = 0;} 
                if (historico.S == undefined){historico.S = 0;}
                if (historico.E == undefined){historico.E = 0;}
                                
                total_i += parseInt(historico.I);
                total_a += parseInt(historico.A);
                total_s += parseInt(historico.S);                
                total_e += parseInt(historico.E);
                
                dia = parseInt(historico.I) + parseInt(historico.A) + parseInt(historico.S) + parseInt(historico.E);
                total_dia += dia;
                tr += '<tr class="tr_resultado_ajax">';
                tr += '<td align="center">'+i+'</td>';
                tr += '<td align="right">'+historico.I+'</td>';
                tr += '<td align="right">'+historico.A+'</td>';
                tr += '<td align="right">'+historico.S+'</td>';
                tr += '<td align="right">'+historico.E+'</td>';
                tr += '<td align="right">'+dia+'</td>';
                tr += '</tr>';
            });
            
            
            tr += '<tr class="tableRodapeModelo3">';
            tr += '<td align="left"><b>Total</b></td>';
            tr += '<td align="right"><b>'+total_i.toString()+'</b></td>';
            tr += '<td align="right"><b>'+total_a.toString()+'</b></td>';
            tr += '<td align="right"><b>'+total_s.toString()+'</b></td>';
            tr += '<td align="right"><b>'+total_e.toString()+'</b></td>';
            tr += '<td align="right"><b>'+total_dia.toString()+'</b></td>';
            tr += '</tr>';
            
            tr += '<tr class="tableRodapeModelo1" style="height:23px;">';
            tr += '<td align="center" colspan="16">';
            tr += '<input type="button" name="bt_gerar_csv" id="bt_gerar_csv" value="Exportar para CSV" class="botao" style="width:120px;">';
            tr += '</td>';
            tr += '</tr>';

            
            /**
            *  GERAR CSV
            */
            if(jQuery('#gerar_csv').val()=='t'){
                
                var total_i = 0;
                var total_a = 0;
                var total_s = 0;
                var total_e = 0;
                var total_dia = 0;
                var cliente = "";
                
                csv = '';
                
                csv += "Dados para Pesquisa;;;;;;\n";
                csv += "Tipo de Operação;Sintético;;;;;\n";
                
                var dataInicioPesq = jQuery("#data_inicio_pesquisa").val();
                var dataFinalPesq = jQuery("#data_fim_pesquisa").val();
                
               	csv += "Período;" + jQuery("#data_inicio_pesquisa").val() + ";" + jQuery("#data_fim_pesquisa").val() + ";;;;\n";
                
                csv += "Canal de Entrada;" + jQuery("#canal_entrada option:selected").text() + ";;;;;\n";
                
                cliente = (jQuery("#nome_cliente").val() == "")  ? 'Todos;'              : jQuery("#nome_cliente").val()+';';
                csv += "Cliente;" + cliente + ";;;;;\n";
 
                csv += "Motivo;" + jQuery("#motivo option:selected").text() + ";;;;;\n\n";
                
                csv += "Resultado da Pesquisa;;;;;;\n";
                if (tipo == "D")
                	csv += "Data; Total Inclusão; Total Alteração; Total Suspensão; Total Exclusão; Total do Dia\n";
                else
                	csv += "Data; Total Inclusão; Total Alteração; Total Suspensão; Total Exclusão; Total do Mês\n";
                
                jQuery.each(resultado.pesquisa, function(i, historico){

                    data        = (i)                   ? i+';'              : '0;';
                    inclusao    = (historico.I != "")   ? historico.I+';'    : '0;';
                    alteracao   = (historico.A != "")   ? historico.A+';'    : '0;';
                    suspensao   = (historico.S != "")   ? historico.S+';'    : '0;';
                    exclusao    = (historico.E != "")   ? historico.E+';'    : '0;';
                    dia         = parseInt(historico.I) + parseInt(historico.A) + parseInt(historico.S) + parseInt(historico.E) +  ';';
                    
                    total_i += parseInt(historico.I);
                    total_a += parseInt(historico.A);
                    total_s += parseInt(historico.S);
                    total_e += parseInt(historico.E);
                    
                    csv += data;
                    csv += inclusao;
                    csv += alteracao;
                    csv += suspensao;
                    csv += exclusao;
                    csv += dia;
                    csv += "\n";

                });
                
                total_dia += total_i + total_a + total_s + total_e;
                
                csv += 'Total;' + total_i + ';' + total_a + ';' + total_s + ';' + total_e + ';' + total_dia + ';\n;\n';
                
                var contador = 0;
                jQuery.each(resultado.debitos, function(i, debito){
                	contador = parseInt(debito) + parseInt(contador);
                });
                csv += "Total de clientes ativos com Débito Automático até hoje:;" + contador.toString() + ";\n";
                jQuery.each(resultado.debitos, function(i, debito){
                	csv += "Total de clientes ativos com " + i + ':; ' + debito + "\n";
                });
      
                jQuery('#exportdata').val(csv);
                jQuery('#acao').val('gerarCsv');
                $("#frm").submit();
                jQuery('#gerar_csv').val('');
                jQuery('.processando_csv').hide();
            }
        }
        else{
            tr += '<tr class="tableRodapeModelo3">';
            tr += '<td align="center" colspan="16" id="total_registros" class="total"><h3>Nenhum registro encontrado</h3></td>';
            tr += '</tr>';
        }
        
        jQuery('#div_msg_pesquisar').hide();
        jQuery('.processando').hide();
        jQuery('#bt_pesquisar').fadeIn(1000);
        jQuery('#div_pesquisar').hide();
        jQuery('#csv_hidden').html(tr);
        jQuery('#csv_hidden').show();
        jQuery('.resultado_pesquisa').html(tr);	
        jQuery('.resultado_pesquisa').fadeIn();
        jQuery('#bt_cancelar').hide();

        /*
        * Zebrando a tabela
        */
        jQuery('tr.tr_resultado_ajax:even').addClass('tdc');
        jQuery('tr.tr_resultado_ajax:odd').addClass('tde');

    }
    
    function relatorioAnalitico(resultado){
        
        tr='';
						
        tr += '<tr class="tableSubTitulo">';
			        	tr += '<td colspan="17"><h2>Resultado da Pesquisa</h2></td>';
			        	tr += '</tr>';
			        	tr += '<tr class="tableSubTitulo tableTituloColunas">';
        tr += '<td colspan="9"><h2>Dados do Histórico</h2></td>';
			        	tr += '<td colspan="4"><h2>Dados de Cobrança Anterior</h2></td>';
			        	tr += '<td colspan="4"><h2>Dados de Cobrança Posterior</h2></td>';
			        	tr += '</tr>';
			        	tr += '<tr class="tableTituloColunas tab_registro">';
			        	
			        	// Dados do Histórico
        tr += '<td width="1%" align="center"><h3>Cliente</h3></td>';
        tr += '<td width="1%" align="center"><h3>CPF/CNPJ</h3></td>';
        tr += '<td width="1%" align="center"><h3>Data / Hora</h3></td>';
        tr += '<td width="1%" align="center"><h3>Canal de Entrada</h3></td>';
        tr += '<td width="1%" align="center"><h3>Tipo Operação</h3></td>';
        tr += '<td width="1%" align="center"><h3>Motivo</h3></td>';
        tr += '<td width="1%" align="center"><h3>Protocolo</h3></td>';
        tr += '<td width="1%" align="center"><h3>Usuário</h3></td>';
        tr += '<td width="1%" align="center"><h3>Departamento</h3></td>';
			        	
			        	// Dados de Cobrança Anterior
        tr += '<td width="1%" align="center"><h3>Forma Cobrança</h3></td>';
        tr += '<td width="1%" align="center"><h3>Banco</h3></td>';
        tr += '<td width="1%" align="center"><h3>Agência</h3></td>';
        tr += '<td width="1%" align="center"><h3>Conta Corrente</h3></td>';
			        	
			        	// Dados de Cobrança Posterior
        tr += '<td width="1%" align="center"><h3>Forma Cobrança</h3></td>';
        tr += '<td width="1%" align="center"><h3>Banco</h3></td>';
        tr += '<td width="1%" align="center"><h3>Agência</h3></td>';
        tr += '<td width="1%" align="center"><h3>Conta Corrente</h3></td>';
			        	tr += '</tr>';
        
			        	if(resultado != null){
			        		
			        		inclusao = 0;
							alteracao = 0;
							exclusao = 0;
							
							jQuery.each(resultado.pesquisa, function(i, historico){
								
								/*
								 * Conta o numero de registros para tipo de operação I - Inclusao
								 */
								if(historico.tipo_operacao_contador == 'I'){
									inclusao++;
								}
								
								/*
								 * Conta o numero de registros para tipo de operação A - Alteração
								 */
								if(historico.tipo_operacao_contador == 'A'){
									alteracao++;
								}
								
								/*
								 * Conta o numero de registros para tipo de operação E - Exclusao
								 */
								if(historico.tipo_operacao_contador == 'E'){
									exclusao++;
								}
                
                if (historico.tipo_cliente == 'F'){                                
                    /*
                     *  Calcula a quantidade de caracteres faltantes
                     *  no CPF e completa com 0
                     */
                    var quantidade_faltante = 11 - historico.documento.length;
                    for (var cont=0; cont<quantidade_faltante; cont++){
                        historico.documento = '0' + historico.documento;
                    }

                    // Insere a mascara de CPF
                    historico.documento = 
                    historico.documento.substr(0, 3) + '.' +
                    historico.documento.substr(3, 3) + '.' +
                    historico.documento.substr(6, 3) + '-' +
                    historico.documento.substr(9, 2);

                }
                else if(historico.tipo_cliente == 'J'){

                    /*
                     *  Calcula a quantidade de caracteres faltantes
                     *  no CNPJ e completa com 0
                     */
                    var quantidade_faltante = 14 - historico.documento.length;
                    for (var cont=0; cont<quantidade_faltante; cont++){
                        historico.documento = '0' + historico.documento;
                    }

                    // Insere a mascara de CNPJ
                    historico.documento = 
                    historico.documento.substr(0, 2) + '.' +
                    historico.documento.substr(2, 3) + '.' +
                    historico.documento.substr(5, 3) + '/' +
                    historico.documento.substr(8, 4) + '-' +
                    historico.documento.substr(12, 2);
                }               
                
								tr += '<tr class="tr_resultado_ajax">';
								
								//Dados do Histórico
								tr += '<td align="left">'+historico.nome_cliente+'</td>';
                tr += '<td align="left">'+historico.documento+'</td>';                
								tr += '<td align="right">'+historico.data_cadastro+'</td>';
								tr += '<td align="left">'+historico.canal_entrada+'</td>';
								tr += '<td align="left">'+historico.tipo_operacao+'</td>';
								tr += '<td align="left">'+historico.motivo+'</td>';
								tr += '<td align="left">'+historico.protocolo+'</td>';
								tr += '<td align="left">'+historico.nome_usuario+'</td>';
								tr += '<td align="left">'+historico.departamento+'</td>';
					        	
					        	// Dados de Cobrança Anterior
								tr += '<td align="left">'+historico.forma_cobranca_anterior+'</td>';
								tr += '<td align="left">'+historico.banco_anterior+'</td>';
								tr += '<td align="right">'+historico.agencia_anterior+'</td>';
								tr += '<td align="right">'+historico.conta_corrente_anterior+'</td>';
					        	
					        	// Dados de Cobrança Posterior
								tr += '<td align="left">'+historico.forma_cobranca_posterior+'</td>';
								tr += '<td align="left">'+historico.banco_posterior+'</td>';
								tr += '<td align="right">'+historico.agencia_posterior+'</td>';
								tr += '<td align="right">'+historico.conta_corrente_posterior+'</td>';
					        	
					        	tr += '</tr>';
															
							})
							
							if(jQuery('#tipo_operacao').val()=='I' || jQuery('#tipo_operacao').val()==''){
								tr += '<tr class="tableRodapeModelo3">';
                tr += '<td align="right" colspan="16"><h3>Total de clientes que aderiram ao débito automático&nbsp</h3></td>';
								tr += '<td align="right"><h3>'+inclusao+'</h3></td>';
								tr += '</tr>';
			        		}
							
							if(jQuery('#tipo_operacao').val()=='A' || jQuery('#tipo_operacao').val()==''){
								tr += '<tr class="tableRodapeModelo3">';
                tr += '<td align="right" colspan="16"><h3>Total de clientes que alteraram o débito automático&nbsp</h3></td>';
								tr += '<td align="right"><h3>'+alteracao+'</h3></td>';
								tr += '</tr>';
							}
							
							if(jQuery('#tipo_operacao').val()=='E' || jQuery('#tipo_operacao').val()==''){
								tr += '<tr class="tableRodapeModelo3">';
                tr += '<td align="right" colspan="16"><h3>Total de clientes que excluíram o débito automático&nbsp</h3></td>';
								tr += '<td align="right"><h3>'+exclusao+'</h3></td>';
								tr += '</tr>';
							}
							
							mensagem_resgistro = (resultado.pesquisa.length > 1) ? 'A pesquisa retornou '+resultado.pesquisa.length+' resultados' : (resultado.pesquisa.length == 1) ? 'A pesquisa retornou 1 resultado' : 'Nenhum registro encontrado';
			        	}else{
			        		mensagem_resgistro = 'Nenhum registro encontrado';
			        	}
			        	
		        		tr += '<tr class="tableRodapeModelo3">';
        tr += '<td align="center" colspan="17" id="total_registros" class="total"><h3>'+mensagem_resgistro+'</h3></td>';
						tr += '</tr>';
						
						if(resultado != null){
							
							tr += '<tr class="tableRodapeModelo1" style="height:23px;">';
            tr += '<td align="center" colspan="17">';
					        tr += '<input type="button" name="bt_gerar_csv" id="bt_gerar_csv" value="Exportar para CSV" class="botao" style="width:120px;">';
					        tr += '</td>';
					        tr += '</tr>';
						}
						
						/**
						 *  GERAR CSV
						 */
						if(jQuery('#gerar_csv').val()=='t'){
							
							csv='';
							
							csv += "Resultado da Pesquisa;;;;;;;;;;;;;;;;;\n";
							csv += 'Dados do Histórico;;;;;;;;';
							csv += 'Dados de Cobrança Anterior;;;;';
							csv += "Dados de Cobrança Posterior;;;;\n";
							
				        	// Dados do Histórico
            csv += "Cliente; CPF/ CNPJ; Data / Hora; Canal de Entrada; Tipo Operação; Motivo; Protocolo; Usuário; Departamento;"; 
							csv += "Forma Cobrança; Banco; Agência; Conta Corrente;Forma Cobrança;Banco;Agência; Conta Corrente;\n";
							
							
							jQuery.each(resultado.pesquisa, function(i, historico){
								
                nome_cliente 		 = (historico.nome_cliente.length > 0)  	   ? historico.nome_cliente+';'             : ';';
                documento                = (historico.documento.length > 0)                ? historico.documento+';'                : ';';
                data_cadastro 		 = (historico.data_cadastro.length > 0) 	   ? historico.data_cadastro+';'            : ';';
                canal_entrada 		 = (historico.canal_entrada.length > 0) 	   ? historico.canal_entrada+';'            : ';';
                tipo_operacao 		 = (historico.tipo_operacao.length > 0) 	   ? historico.tipo_operacao+';'            : ';';
                motivo 			 = (historico.motivo.length > 0) 		   ? historico.motivo+';'                   : ';';
                protocolo 		 = (historico.protocolo.length > 0) 		   ? historico.protocolo+';'                : ';';
                nome_usuario 		 = (historico.nome_usuario.length > 0) 		   ? historico.nome_usuario+';'             : ';';
                departamento 		 = (historico.departamento.length > 0) 		   ? historico.departamento+';'             : ';';
                forma_cobranca_anterior  = (historico.forma_cobranca_anterior.length > 0)  ? historico.forma_cobranca_anterior+';'  : ';';
                banco_anterior		 = (historico.banco_anterior.length > 0) 	   ? historico.banco_anterior+';'           : ';';
                agencia_anterior 	 = (historico.agencia_anterior.length > 0) 	   ? historico.agencia_anterior+';'         : ';';
                conta_corrente_anterior  = (historico.conta_corrente_anterior.length > 0)  ? historico.conta_corrente_anterior+';'  : ';';
								forma_cobranca_posterior = (historico.forma_cobranca_posterior.length > 0) ? historico.forma_cobranca_posterior+';' : ';';
                banco_posterior 	 = (historico.banco_posterior.length > 0) 	   ? historico.banco_posterior+';'          : ';';
                agencia_posterior 	 = (historico.agencia_posterior.length > 0) 	   ? historico.agencia_posterior+';'        : ';';
								conta_corrente_posterior = (historico.conta_corrente_posterior.length > 0) ? historico.conta_corrente_posterior+';' : ';';
                                
								csv += nome_cliente;
                csv += documento;
								csv += data_cadastro;
								csv += canal_entrada;
								csv += tipo_operacao;
								csv += motivo;
								csv += protocolo;
								csv += nome_usuario; 
								csv += departamento;
								csv += forma_cobranca_anterior;
								csv += banco_anterior;
								csv += agencia_anterior;
								csv += conta_corrente_anterior;
								csv += forma_cobranca_posterior;
								csv += banco_posterior;
								csv += agencia_posterior;
								csv += conta_corrente_posterior;
								csv += "\n";
							
							})

							csv += 'Total de clientes que aderiram ao débito automático;';
							csv += inclusao+";";
							csv += "\n";
							
							csv += 'Total de clientes que alteraram o débito automático;';
							csv += alteracao+";";
							csv += "\n";
							
							csv += 'Total de clientes que excluíram o débito automático;';
							csv += exclusao+";";
							csv += "\n";
							
							
							jQuery('#exportdata').val(csv);
							
							jQuery('#acao').val('gerarCsv');
						    
							$("#frm").submit();
						    
							jQuery('#gerar_csv').val('');
							
							jQuery('.processando_csv').hide();
						    
						} // End GERAR CSV
						
						jQuery('#div_msg_pesquisar').hide();
						
						jQuery('.processando').hide();
						
						jQuery('#bt_pesquisar').fadeIn(1000);	
						
						jQuery('#div_pesquisar').hide();
						
						jQuery('#csv_hidden').html(tr);
						
						jQuery('#csv_hidden').show();
						
						jQuery('.resultado_pesquisa').html(tr);	
						
						jQuery('.resultado_pesquisa').fadeIn();
						
						jQuery('#bt_cancelar').hide();
						
						/*
						 * Zebrando a tabela
						 */
						jQuery('tr.tr_resultado_ajax:even').addClass('tdc');
						jQuery('tr.tr_resultado_ajax:odd').addClass('tde');
					
					}
					
})