jQuery(function(){

	//// Por padrão não mostrar os campos que são apenas visiveis na edição de um teste
	jQuery(".campos_editar").hide();

	/**
	 * Ao carregar a página popula a combo de grupos de teste ...
	 */
	jQuery(document).ready(function(){
		jQuery.ajax({
			url:  'cad_testes_ativacao.php',
			type: 'POST',
			data: 'acao=listarGruposTesteCadastrados',
			success: function(data){

				var grupos = jQuery.parseJSON(data);

				jQuery.each(grupos, function(key, object){

					var option = jQuery('<option>');

					option.attr('value', object.oid);
					option.html(object.desc);

					jQuery('#cb_grupo_testes').last().append(option);

				});

				//Esconde indicador de carregamento ...
				jQuery('#pesq_grupo_progress').hide();
			}
		});

		/**
		 * popula a combo [Número WS teste]
		 */
		jQuery.ajax({
			url:  'cad_testes_ativacao.php',
			type: 'POST',
			data: 'acao=listarWebServicesTeste',
			success: function(data){

				var dados = jQuery.parseJSON(data);

				jQuery.each(dados, function(key, object){

					option = jQuery('<option>');
					option.prop('value', object.ws);
					option.html(object.ws);
					jQuery('#cb_numero_ws_teste').last().append(option);

				});
			}
		});




		/**
		* Verificar se possui um código de teste, caso exista, a ação passa a ser 'editarTeste'
		* Chama a função para preenche com os dados do teste
		*/
		if (jQuery("#eptpoid").val() != ""){
			//// Remover mensagem dizendo que o campo ID é gerado automaticamente
        	jQuery("#msgCampoAutomatico").hide();
        	//// Habilitar botão Excluir
        	jQuery("#btn_excluir").show();

			jQuery("#acao").val("editarTeste");
			var eptpoid = jQuery("#eptpoid").val();
			carregaDadosTeste(eptpoid);
			jQuery(".campos_editar").show();
		} else {
			//// Ocultar os campos que são apenas para mostrar os dados no momento da edição
			jQuery(".campos_editar").hide();
			//// Desalibitar botão Excluir
        	jQuery("#btn_excluir").hide();
		}


	});

	/**
	 * Ao clicar no botao pesquisar chamar função de pesquisa
	 */
	jQuery('#btn_pesquisar').click(function(){
		/// Remover alerta caso tenha sido exibido anteriormente
		removeAlerta();
		pesquisar();
	});

});

/**
 *
 * Função para pesquisar os testes
 */
function pesquisar(event) {
	try {
		var msg;

		jQuery.ajax({
			url: 'cad_testes_ativacao.php',
			type: 'POST',
			data: jQuery('#formPesqTesteAtivacao').serialize() + '&acao=pesquisar',
			beforeSend: function(){
				jQuery('.sem_resultados').remove();
				jQuery('#tbResultadoPesquisa').hide();
				jQuery('.processando').fadeIn();

				jQuery('#loading').html('<img src="images/loading.gif" alt="" />');
	            jQuery('#loading').fadeIn();

			},
			success: function(data){
				jQuery('#loading').fadeOut();

				var response = jQuery.parseJSON(data);

				jQuery('#tbResultadoPesquisa').fadeIn();

				//Limpa Resultados anteriores ...
				jQuery('#tbResultadoPesquisa tr.linha_resultado').each(function(i, tr){
					jQuery(tr).remove();
				});

				if(response){
					if(!response.error){
						if(response.length > 0){
							jQuery.each(response, function(i, registro){

								//Cria linha de resultado ...
								var tr = jQuery('<tr>');

								tr.addClass('linha_resultado');

								//Cria células ...
								var tdOid 		 = jQuery('<td>');
								var tdGrupo 	 = jQuery('<td>');
								var tdDescricao  = jQuery('<td>');
								var tdSigla		 = jQuery('<td>');
								var tdAcao		 = jQuery('<td>');
								var tdVerPorta	 = jQuery('<td>');
								var tdEnvConfig	 = jQuery('<td>');
								var tdTelemetria = jQuery('<td>');
								var tdSatelital  = jQuery('<td>');
								var tdValPosicao = jQuery('<td>');
								var tdBtEditar   = jQuery('<td>');
								var tdBtExcluir  = jQuery('<td>');

								//Adiciona propriedades e conteúdo as celulas ...
								tdOid.attr('nowarap', 'nowrap');
								tdOid.html(registro.oid);

								tdGrupo.attr('nowarap', 'nowrap');
								tdGrupo.html(registro.grupo);

								tdDescricao.attr('nowarap', 'nowrap');
								tdDescricao.html(registro.desc);

								tdSigla.attr('align', 'center');
								tdSigla.html(registro.sigla);

								tdAcao.html(registro.acao);

								tdVerPorta.attr('align', 'center');
								tdVerPorta.html(registro.enviacfg);

								tdEnvConfig.attr('align', 'center');
								tdEnvConfig.html(registro.enviacfg);

								tdTelemetria.attr('align', 'center');
								tdTelemetria.html(registro.telemetria);

								tdSatelital.attr('align', 'center');
								tdSatelital.html(registro.satelital);

								tdValPosicao.attr('align', 'center');
								tdValPosicao.html(registro.valposicao);

								//Cria Botão de Edição e suas propriedades ...
								tdBtEditar.attr('align', 'center');

								var btnEditar = jQuery('<img>');

								btnEditar.attr('src',   'images/icones/file.gif');
								btnEditar.attr('alt',   'Editar');
								btnEditar.css('cursor', 'pointer');

								var dados = {id: registro.oid};
								btnEditar.bind('click', dados, editarTesteAtivacao);

								tdBtEditar.append(btnEditar);

								//Cria Botão de Exclusão e suas propriedades ...
								tdBtExcluir.attr('align', 'center');

								var btnExcluir = jQuery('<img>');

								btnExcluir.attr('src',   'images/icones/x1transparente.gif');
								btnExcluir.attr('alt',   'Excluir');
								btnExcluir.css('cursor', 'pointer');

								var dados = {id: registro.oid, epttoid:registro.epttoid};
								btnExcluir.bind('click', dados, excluirTesteAtivacao);

								tdBtExcluir.append(btnExcluir);

								//Adiciona elementos em seus respectivos containeres ...
								tr.append(tdOid);
								tr.append(tdGrupo);
								tr.append(tdDescricao);
								tr.append(tdSigla);
								tr.append(tdAcao);
								tr.append(tdVerPorta);
								tr.append(tdEnvConfig);
								tr.append(tdTelemetria);
								tr.append(tdSatelital);
								tr.append(tdValPosicao);
								tr.append(tdBtEditar);
								tr.append(tdBtExcluir);

								jQuery('#tbResultadoPesquisa').last().append(tr);

								//Zebra a tabela de resultados ...
								jQuery('#tbResultadoPesquisa tr.linha_resultado:odd').addClass('tde');
								jQuery('#tbResultadoPesquisa tr.linha_resultado:even').addClass('tdc');

							});

						} else {

							//Ação para caso não haja resultado ...
							var tr = jQuery('<tr>');
							var td = jQuery('<td>');

							tr.addClass('sem_resultados');

							td.attr('align', 'center');
							td.attr('colspan', '12');
							td.html('Não foram encontrados resultados para pesquisa.');

							tr.append(td);

							jQuery('#tbResultadoPesquisa').last().append(tr);
						}

					} else {
						//Recupera a mensagem do erro vinda do PHP ...
						msg = resultado.message;
					}

				} else {
					msg = 'Não foi possívei realizar a pesquisa';
				}
			}
		});

	} catch(e) {
		msg = 'Erro ao realizar pesquisa: ' + e.message;
	}

	jQuery('.processando').hide();

	if(msg){
		cria_msg_html('div_msg', msg);
	}
}
/**
 * Função para salvar caso todos os campos necessários forem prenchidos
 */
function salvarTesteAtivao(event) {

	var acao = jQuery("#acao").val();

	//// Se ação for editarTeste, pede confirmação do usuário, caso o usuário cancele o sistema não realiza nenhuma ação
	if (acao == "editarTeste"){
		if(!confirm('ATENÇÃO: Deseja realmente alterar os dados do Teste?')) {
			return false;
		}
	}

	//// Passar pela função de validação
	if (validaTesteAtivacao()){

        jQuery('#tipo_os').children('input').removeProp('disabled');

		jQuery.ajax({
            url: 'cad_testes_ativacao.php',
            type: 'post',
            data: $("#formCadTesteAtivacao").serialize()+'&acao='+acao ,

            beforeSend: function(){
                //exibe loading de processamento
                jQuery('#loading').html('<img src="images/loading.gif" alt="" />');
                jQuery('#loading').show();

                //remove botao salvar
                jQuery('#bt_salvar').hide();
            },
            success: function(data){
                try{
                	if (data == "ok")
                	{
                		alert("Teste salvo com sucesso!");
                		//// Se ação for editarTeste recarrega com os novos valores, se for cadastro novo retorna para a tela de pesquisa
                		if (acao == "editarTeste"){
                			var eptpoid = jQuery("#eptpoid").val();
                			carregaDadosTeste(eptpoid);
                		}
                		else
                			window.location.href = "cad_testes_ativacao.php" ;
                	}
                	else
                	{
                		criaAlerta('Erro ao salvar o teste.');
                	}
                }catch(e){

                	criaAlerta('Erro ao salvar o teste.');

                }

                jQuery('#tipo_os').children('input').prop('disabled', true);
                jQuery('#loading').html('');
            }
        });
	}
}

/**
 *
 * Função para validar se os campos necessários foram preenchidos
 */
function validaTesteAtivacao(event){
	/// Remover alerta caso tenha sido exibido anteriormente
	removeAlerta();

	/// Se entrar em algum if os campos obrigatórios não foram preenchidos, informando ao usuário
	if (jQuery("#cb_grupo_testes").val() == ""){

		criaAlerta("Campo Grupo Teste deve ser preenchido.");
		return false;
	}
	if (jQuery("#cmp_descricao_teste").val() == ""){
		criaAlerta("Campo Descrição do Teste deve ser preenchido.");
		return false;
	}
	if (jQuery("#cmp_sigla_teste").val() == ""){
		criaAlerta("Campo Sigla Teste deve ser preenchido.");
		return false;
	}
	if (jQuery("#cb_acao_teste").val() == ""){
		criaAlerta("Campo Ação do Teste deve ser preenchido.");
		return false;
	}
	if (jQuery("#cb_indica_telemetria").val() == ""){
		criaAlerta("Campo Indica Telemetria deve ser preenchido.");
		return false;
	}
	if (jQuery("#cb_teste_satelital").val() == ""){
		criaAlerta("Campo Teste Satelital deve ser preenchido.");
		return false;
	}
	if (jQuery("#cb_valida_posicao").val() == ""){
		criaAlerta("Campo Valida Posicao deve ser preenchido.");
		return false;
	}
	if (jQuery("#cb_envia_configuracao").val() == ""){
		criaAlerta("Campo Envia Configuração deve ser preenchido.");
		return false;
	}
	if (jQuery("#cb_verifica_porta").val() == ""){
		criaAlerta("Campo Verifica Porta deve ser preenchido.");
		return false;
	}
	if (jQuery("#cb_numero_ws_teste").val() == ""){
		criaAlerta("Campo Número WS teste deve ser preenchido.");
		return false;
	}

	if (jQuery("#cmp_instrucao_teste").val() == ""){
		criaAlerta("Campo Instrução do Teste deve ser preenchido.");
		return false;
	}
	if (jQuery("#cmp_teste_sucesso").val() == ""){
		criaAlerta("Campo Mensagem Teste Sucesso deve ser preenchido.");
		return false;
	}
	if (jQuery("#cmp_teste_insucesso").val() == ""){
		criaAlerta("Campo Mensagem Teste Insucesso deve ser preenchido.");
		return false;
	}
	if (jQuery("#cb_teste_obrigatorio").val() == ""){
		criaAlerta("Campo Teste Obrigatório deve ser preenchido.");
		return false;
	}

	return true;
}

/**
 * Abrir tela de edição
 */
function editarTesteAtivacao(event){
	// ID Teste
	var eptpoid = event.data.id;
	jQuery("#eptpoid").val(eptpoid);
	exibeCadastro();
}

/**
 *
 * Exclusão de um teste
 */
function excluirTesteAtivacao(event){

	//// Verificar de qual págida está sendo chamada a função de excluir um teste
	//// para desta forma pegar o id do teste a ser excluído
	var acao = jQuery("#acao").val();

	if (acao == "pesquisar")
	{
		var eptpoid = event.data.id;
		var epttoid = event.data.epttoid;
	}
	else
	{
		var eptpoid = jQuery("#eptpoid").val();
		var epttoid = jQuery("#epttoid").val();
	}

	if(confirm('ATENÇÃO: Deseja realmente excluir o Teste do sistema?')) {
		jQuery.ajax({
            url: 'cad_testes_ativacao.php',
            type: 'post',
            data: 'eptpoid='+eptpoid+'&epttoid='+epttoid+'&acao=excluiTeste',
            beforeSend: function(){
                //exibe loading de processamento
                jQuery('#loading').show();
            },
            success: function(data){
                try{
                	if (data != "erro")
                	{
                		if (acao == "pesquisar")
                		{
		            		// Em caso de sucesso para excluir o teste do sistema, exibe alerta de exclusão e realiza nova pesquisa
		            		criaAlerta('Teste excluído com sucesso.');
		            		pesquisar();
                		}
                		else
            			{
                			alert('Teste excluído com sucesso.');
                			window.location.href = "cad_testes_ativacao.php" ;
            			}
                	}
                	else
                		criaAlerta('Não foi possível exluir o Teste.');
                } catch(e){
                //exibe mensagem de erro
                criaAlerta('Erro ao excluir o Teste.');
            }

            //remove loading de processamento
            jQuery('#loading').hide();
            }
		});
	}
}

/**
 * Função que preenche a tela de cadastro com os dados do teste
 */
function carregaDadosTeste(eptpoid) {

	jQuery.ajax({
        url: 'cad_testes_ativacao.php',
        type: 'post',
        data: 'eptpoid='+eptpoid+'&acao=carregaDados',
        beforeSend: function(){
            //exibe loading de processamento
        	jQuery('#loading').html('<img src="images/loading.gif" alt="" />');
            jQuery('#loading').fadeIn(5000);
        },
        success: function(data){
        	//// Colocar o campo Sigla como apenas leitura, pois este não pode ser alterado
        	jQuery("#cmp_sigla_teste").attr("readonly",true).css("background-color","#D3D3D3");
        	jQuery(".campos_editar input").attr("readonly",true).css("background-color","#D3D3D3");

        	//// Carregar dinamicamente os dados do Teste, adicionado a função setTimeout
            ///quando for o campo grupo teste, para evitar o carregamento antes mesmo de
            ///ter preenchido o select com os grupos de teste
        	var response = jQuery.parseJSON(data);

        	jQuery.each( response, function( index, value ) {
        		if (index == "cb_grupo_testes") {
        			setTimeout(function(){
        				jQuery("#"+index).val(value)
        			}, 1000);
        		} else if (index == "cb_numero_ws_teste") {
        			setTimeout(function(){
        				jQuery("#"+index).val(value)
        			}, 1000);
        		} else if( index == 'eptpostoid_teste_obrigatorio' ) {

                    setTimeout(function(){

                        jQuery('#tipo_os').children('input').each(function( ) {

                            var elemento = this;
                            jQuery(elemento).removeProp('checked');

                            jQuery.each( value, function( chave, valor ) {
                                if(elemento.value == valor) {
                                    jQuery(elemento).prop('checked', true);
                                }
                            });
                        });
                    }, 50);

                } else {
        			jQuery("#"+index).val(value);
    			}


        	});

        	jQuery('#loading').fadeOut();
        }
	});
}

/**
 * Função que alterna entre formulário de pesquisa e cadastro
*/

function exibeCadastro(event) {
	jQuery("#acao").val('cadastro');
	jQuery("#formPesqTesteAtivacao").submit();
}
