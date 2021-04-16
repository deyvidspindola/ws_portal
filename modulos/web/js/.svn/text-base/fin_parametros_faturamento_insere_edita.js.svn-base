/**
 * Mensagens de retorno
 */
var MSG_DATAINI_OBRIGATORIA 			= "Data inicial é obrigatória.";
var MSG_DATAINI_INVALIDA 				= "Data inicial informada não é valida.";
var MSG_DATAFIM_OBRIGATORIA 			= "Data final é obrigatória.";
var MSG_DATAFIM_INVALIDA 				= "Data final informada não é valida.";
var MSG_DATAFINAL_MENOR					= "Data final menor que a data inicial.";
var MSG_VALOR_DATA                      = "Preencher período inicial e final referente ao valor de cobrança.";

var MSG_QUANTIDADE_FINAL_MENOR			= "A quantidade de faturamento final deve ser maior do que a quantidade de faturamento inicial informada.";
var MSG_QUANTIDADE_MAIOR_ZERO			= "A quantidade informada deve ser maior que zero.";
var MSG_NAO_HA_CONTRATOS				= "Não há nenhum contrato para o cliente selecionado.";
var MSG_EXISTEM_CAMPOS_NAO_PREENCHIDOS 	= "Existem campos obrigatórios não preenchidos.";
var MSG_VALOR_IGUAL_ZERO				= "O valor informado deve ser maior que zero.";
var MSG_INFORMAR_PERIODO_DESCONTO		= "Preencher período inicial e final referente a porcentagem de desconto.";
var MSG_INFORMAR_PERIODO_ISENCAO		= "Preencher período inicial e final referente ao isento de cobrança.";
var MSG_ESCOLHER_UM_CONTRATO			= "É necessário escolher pelo menos um contrato.";
var MSG_SELECIONAR_OBRIGACAO_FINANCEIRA = "Pelo menos uma obrigação financeira deve ser selecionada.";
var MSG_INFORMA_OBSERVACAO			    = "Campo observação é obrigatório. Minimo 50 caracteres.";
var MSG_INFORMA_ARQUIVO                             = "Arquivo é obrigatório.";

/**
 * OnLoad
 */
jQuery(document).ready(function(){

    jQuery('#div_arquivo_massivo').hide();
    
    // esconder div importacao massivo caso seja edição
    if(jQuery('input[name="parfoid"]').val() != ''){
        jQuery('#div_importacao_massivo').hide()
    }
	// Ajuste automatico do tamanho da tela
	jQuery(window).resize(function() {
		var widthTabela = jQuery(window).width();
		
		if (widthTabela < 991) {
			
			jQuery('#tabela_container').removeAttr('width');
			jQuery('#tabela_container').width('990');
			jQuery('#tabela_container').css('display', 'block');
			
			jQuery('#tabela_container2').removeAttr('width');
			jQuery('#tabela_container2').width('990');
			jQuery('#tabela_container2').css('display', 'block');
			
			
		} else {
			
			jQuery('#tabela_container').width('98%');
			jQuery('#tabela_container').css('display', 'table');
			
			jQuery('#tabela_container2').width('98%');
			jQuery('#tabela_container2').css('display', 'table');
		}
	});
	
	
	//verifica??o para o IE n?o funciona se tiver desabilitado
	if(jQuery('input[name=parfoid]').val() == ''){
		// Da foco ao input inicial
		jQuery('input[name="nivel"]:checked').focus();
	}

//	jQuery(".trocas_isentas").mask('9?99',{placeholder:''});
//
//	jQuery(".trocas_valor").maskMoney({
//        decimal:",", 
//        thousands:"."
//    }).keydown(function(e){
//    	
//    	if (e.keyCode == 192) {
//    		return false;
//    	}
//    });

	/**
	 * Mascara de dinheiro
	 */
	jQuery("#perc_desconto").maskMoney({
        decimal:",",
        thousands:"."
    }).keydown(function(e){
    	
    	if (e.keyCode == 192) {
    		return false;
    	}
    });
	
	
	/**
	 * Mascara de dinheiro
	 */
	jQuery("#valor").maskMoney({
        decimal:",", 
        thousands:"."
    }).keydown(function(e){
    	
    	if (e.keyCode == 192) {
    		return false;
    	}
    });
	
	/**
	 * Mascaras de data
	 */
	
	jQuery('#isento_cobranca_dt_ini')
		.mask("99/99/9999")
		.blur(function(){
			if (!check_date(jQuery(this).val())) {
				removeAlerta();
				jQuery(this).val('');
				//jQuery(this).addClass('erro');
			} else {
				jQuery(this).removeClass('erro');
			}
		});
	
	jQuery('#isento_cobranca_dt_fim')
		.mask("99/99/9999")
		.blur(function(){
			var data = jQuery(this).val();
			if (data != "99/99/9999" && !check_date(data)) {
				removeAlerta();
				jQuery(this).val('');
				//jQuery(this).addClass('erro');
			} else {
				jQuery(this).removeClass('erro');
				validaPeriodoIsencao();
			}
		});
	
	jQuery('#perc_desconto_dt_ini')
		.mask("99/99/9999")
		.blur(function(){
			if (!check_date(jQuery(this).val())) {
				removeAlerta();
				jQuery(this).val('');
				//jQuery(this).addClass('erro');
			} else {
				jQuery(this).removeClass('erro');
			}
		});
	
	jQuery('input[name="perc_desconto_dt_ini"]').click(function(){
		jQuery(this).focus();
		jQuery(this).removeClass('erro');
	});
	
	jQuery('#perc_desconto_dt_fim')
		.mask("99/99/9999")
		.blur(function(){
			var data = jQuery(this).val();
			if (data != "99/99/9999" && !check_date(data)) {
				removeAlerta();
				jQuery(this).val('');
				//jQuery(this).addClass('erro');
			} else {
				jQuery(this).removeClass('erro');
				validaPeriodoPercDesconto();
			}
		});


	/**
	 * Mascaras do campo "Periodicidade Reajuste"
	 *
	 */
	jQuery('input[name="periodicidade_reajuste"]').mask("9?9");

	/**
	 * Mascaras do campo "Prazo de Vencimento (Dias)"
	 *
	 */
	//jQuery('input[name="prazo_vencimento"]').mask("9?9?9");

	/**
	 * @tag input(name=quantidade_faturamento_de)
	 * Mascaras do campo "Quantidade para faturamento"
	 * 
	 * @Event OnBlur
	 */
//	jQuery('input[name="quantidade_faturamento_de"]')
//		.mask("9?99")
//		.blur(function() {
//			validaCampoQuantidadeFaturamento();
//		});
//	
	/**
	 * @tag input(name=quantidade_faturamento_ate)
	 * Mascaras do campo "Quantidade para faturamento"
	 * 
	 * @Event OnBlur
	 */
//	jQuery('input[name="quantidade_faturamento_ate"]')
//		.mask("9?99")
//		.blur(function() {
//			validaCampoQuantidadeFaturamento();
//		});
//	
	
	// Desabilita os campos do componente da pesquisa do cliente
	jQuery('input[name="cpx_pesquisa_cliente_nome"]').attr('disabled', 'disabled').addClass('disabled');
	jQuery('input[name="cpx_valor_tipo_pessoa"]').attr('disabled', 'disabled').addClass('disabled');
	jQuery('input[name="cpx_valor_cliente_cnpj"]').attr('disabled', 'disabled').addClass('disabled');
	jQuery('input[name="cpx_valor_cliente_cpf"]').attr('disabled', 'disabled').addClass('disabled');
	jQuery('button[name="cpx_botao_pesquisa_cliente_nome"]').attr('disabled', 'disabled').addClass('disabled');
	

	/**
	 * Mascara campo contrato
	 */
	jQuery('input[name="contrato"]').mask("9?99999999999");
	

	jQuery('input[name="nivel"]').each(function(){

		if (jQuery(this).is(':checked') && (jQuery(this).val() == 1 || jQuery(this).val() == 2 || jQuery(this).val() == 3) ) {
			jQuery('.periodicidade_reajuste').fadeIn('fast');
			//jQuery('#trocas_taxas').fadeIn('fast');
			//jQuery('input[name="trocas_isentas"],input[name="trocas_taxa_unica"]').removeAttr('disabled');
			
		} 

	});

//
//	jQuery("#trocas_taxa_unica").change(function() {
//		if (jQuery(this).is(':checked')) {
//			jQuery(".trocas_valor").removeAttr('disabled').removeClass('disabled');
//		}else{
//			jQuery(".trocas_valor").val('').attr('disabled','disabled').addClass('disabled');
//		}
//	});

	
	/**
	 * @tag input
	 * @Event OnClick
	 */
	jQuery('input[name="nivel"]').click(function() {
		
		
		/**
		 * 1 - Contrato (um a um)
		 * 2 - Cliente
		 * 3 - Tipo Contrato
		 */
		var nivel = parseInt(jQuery(this).val());
		
				
		removeAlerta();
		
		// Limpa campos sinalizados
		jQuery('*.erro').removeClass('erro');
		
		jQuery('input[name="contrato"]').val('');
		jQuery('select[name="tipo_contrato"]').val('');
	
//		jQuery('#trocas_taxas input').val('').removeAttr('checked');
//		jQuery(".trocas_valor").val('').attr('disabled','disabled');

		
		switch (nivel) {
		
		/**
		 * "Contrato " os campos "Cliente",  "Tipo Pessoa", 
		 * "CNPJ" e "Tipo de Contrato" estarão desabilitados. O botão Pesquisar  também estará desabilitado.
		 */
		case 1:
			
				// Habilita
				jQuery('input[name="contrato"]').removeAttr('disabled').removeClass('disabled');
				jQuery('.dados_contrato').fadeIn('fast');
                                jQuery('#div_importacao_massivo').show();
				
				// Desabilita
				
				jQuery('.dados_tipo_contrato').hide();
				jQuery('.dados_cliente').hide();
				jQuery('input[name="cpx_valor_cliente_nome"]').val('');
				jQuery('input[name="cpx_pesquisa_cliente_nome"]').attr('disabled', 'disabled').addClass('disabled');
				jQuery('input[name="cpx_valor_tipo_pessoa"]').attr('disabled', 'disabled').addClass('disabled');
				jQuery('input[name="cpx_valor_cliente_cnpj"]').attr('disabled', 'disabled').addClass('disabled');
				jQuery('input[name="cpx_valor_cliente_cpf"]').attr('disabled', 'disabled').addClass('disabled');
				jQuery('button[name="cpx_botao_pesquisa_cliente_nome"]').attr('disabled', 'disabled').addClass('disabled');
				
				jQuery('select[name="tipo_contrato"]').attr('disabled', 'disabled').addClass('disabled');
				///jQuery('#trocas_taxas').fadeIn('fast');
				///jQuery('input[name="trocas_isentas"],input[name="trocas_taxa_unica"]').removeAttr('disabled').removeClass('disabled');
				
				// Exibe
				jQuery('.periodicidade_reajuste').fadeIn('fast');
				
			break;
			
		/**
		 *  "Cliente" os campos "Contrato" e "Tipo de Contrato" estarão desabilitados e importação massiva desabilitado
		 */
		case 2:
			
				// Habilita
			
			    jQuery('.dados_cliente').fadeIn('fast');
			
				jQuery('input[name="clioid"]').removeAttr('disabled').removeClass('disabled');
				
				jQuery('input[name="cpx_pesquisa_cliente_nome"]').removeAttr('disabled').removeClass('disabled');
				jQuery('input[name="cpx_valor_tipo_pessoa"]').removeAttr('disabled').removeClass('disabled');
				jQuery('input[name="cpx_valor_cliente_cnpj"]').removeAttr('disabled').removeClass('disabled');
				jQuery('input[name="cpx_valor_cliente_cpf"]').removeAttr('disabled').removeClass('disabled');
				jQuery('button[name="cpx_botao_pesquisa_cliente_nome"]').removeAttr('disabled').removeClass('disabled');
				
				// Desabilita
				
				jQuery('.dados_contrato').hide();
				jQuery('.dados_tipo_contrato').hide();
				jQuery('input[name="contrato"]').attr('disabled', 'disabled').addClass('disabled');
				jQuery('select[name="tipo_contrato"]').attr('disabled', 'disabled').addClass('disabled');
				jQuery('#div_importacao_massivo').hide();
                                
                        	//jQuery('#trocas_taxas').fadeIn('fast');
				//jQuery('input[name="trocas_isentas"],input[name="trocas_taxa_unica"]');

				// Exibe
				jQuery('.periodicidade_reajuste').fadeIn('fast');

			break;
		
		/**
		 * "Tipo de Contrato" os campos "Contrato", "Cliente" , "Tipo Pessoa" e "CNPJ" estarão desabilitados. 
		 * O botão Pesquisar também estará desabilitado.
		 */
		case 3:
			
				// Habilita
				jQuery('select[name="tipo_contrato"]').removeAttr('disabled').removeClass('disabled');
				jQuery('.dados_tipo_contrato').fadeIn('fast');
				
				// Desabilita
				jQuery('input[name="contrato"]').attr('disabled', 'disabled').addClass('disabled');
				jQuery('.dados_contrato').hide();
				jQuery('.dados_cliente').hide();
                                jQuery('#div_importacao_massivo').hide();
				
				jQuery('input[name="cpx_valor_cliente_nome"]').val('');
				jQuery('input[name="cpx_pesquisa_cliente_nome"]').attr('disabled', 'disabled').addClass('disabled');
				jQuery('input[name="cpx_valor_tipo_pessoa"]').attr('disabled', 'disabled').addClass('disabled');
				jQuery('input[name="cpx_valor_cliente_cnpj"]').attr('disabled', 'disabled').addClass('disabled');
				jQuery('input[name="cpx_valor_cliente_cpf"]').attr('disabled', 'disabled').addClass('disabled');
				jQuery('button[name="cpx_botao_pesquisa_cliente_nome"]').attr('disabled', 'disabled').addClass('disabled');
				
				//jQuery('#trocas_taxas').fadeIn('fast');
				//jQuery('input[name="trocas_isentas"],input[name="trocas_taxa_unica"]').removeAttr('disabled').removeClass('disabled');
				
				
				// Exibe
				jQuery('.periodicidade_reajuste').fadeIn('fast');
				
			break;
			
		default: 

				//jQuery('#trocas_taxas').fadeOut('fast');
				//jQuery('#trocas_taxas input').attr('disabled', 'disabled').removeClass('disabled');

				removeAlerta();
				criaAlerta("Nível não definido");
			break;
		}
	});
	
	
	/**
	 * @tag input(name=isento_cobranca)
	 * @Event OnClick
	 */
	jQuery('input[name="isento_cobranca"]').click(function(){
		
		var habilitado = jQuery(this).is(':checked');
		
		if (habilitado == true) {
			
			// Habilita
			jQuery('input[name="isento_cobranca_dt_ini"]').removeAttr('disabled').removeClass('disabled');
			jQuery('#img_isento_cobranca_dt_ini').removeAttr('disabled');
			
			jQuery('input[name="isento_cobranca_dt_fim"]').removeAttr('disabled').removeClass('disabled');
			jQuery('#img_isento_cobranca_dt_fim').removeAttr('disabled');

			jQuery('input[name="perc_desconto"]').val('');
			jQuery('input[name="perc_desconto_dt_ini"]').val('');
			jQuery('input[name="perc_desconto_dt_fim"]').val('');
			jQuery('input[name="perc_desconto_dt_ini"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');
			jQuery('input[name="perc_desconto_dt_fim"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');

			jQuery('input[name="valor"]').val('');
			jQuery('input[name="valor_dt_ini"]').val('');
			jQuery('input[name="valor_dt_fim"]').val('');
			jQuery('input[name="valor_dt_ini"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');
			jQuery('input[name="valor_dt_fim"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');

			jQuery('input[name="periodicidade_reajuste"]').val('');
			jQuery('input[name="prazo_vencimento"]').val('');

		} else {
			
			// Desabilita
			jQuery('input[name="isento_cobranca_dt_ini"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');
			jQuery('#img_isento_cobranca_dt_ini').attr('disabled','disabled');
			
			jQuery('input[name="isento_cobranca_dt_fim"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');
			jQuery('#img_isento_cobranca_dt_fim').attr('disabled','disabled');
		}
	});
	
	jQuery('input[name="perc_desconto"]').keydown(function(e) {

		if (e.keyCode == 9 && jQuery('input[name="perc_desconto_dt_ini"]').attr('disabled') == null) {
			var tab = window.setTimeout(function(){
				jQuery('input[name="perc_desconto_dt_ini"]').focus();
			}, 100);
		}
	});
	
	/**
	 * @tag input(type=text)
	 * @Event OnKeyup
	 */
	jQuery('input[name="perc_desconto"]').keyup(function() {
		
		var perc = jQuery(this).val();
		
		perc = perc.replace('.', '');
		perc = perc.replace(',', '.');
		
		
		if (perc > 0) {
			
			if (perc > 100) {
				jQuery(this).val('100,00');
			}
			
			// Habilita
			jQuery('input[name="perc_desconto_dt_ini"]').removeAttr('disabled').removeClass('disabled');
			jQuery('#img_perc_desconto_dt_ini').removeAttr('disabled');
			
			jQuery('input[name="perc_desconto_dt_fim"]').removeAttr('disabled').removeClass('disabled');
			jQuery('#img_perc_desconto_dt_fim').removeAttr('disabled');

			jQuery('input[name="isento_cobranca"]').prop('checked', false);
			jQuery('input[name="isento_cobranca_dt_ini"]').val('');
			jQuery('input[name="isento_cobranca_dt_fim"]').val('');
			jQuery('input[name="isento_cobranca_dt_ini"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');
			jQuery('input[name="isento_cobranca_dt_fim"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');

			jQuery('input[name="valor"]').val('');
			jQuery('input[name="valor_dt_ini"]').val('');
			jQuery('input[name="valor_dt_fim"]').val('');
			jQuery('input[name="valor_dt_ini"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');
			jQuery('input[name="valor_dt_fim"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');

			jQuery('input[name="periodicidade_reajuste"]').val('');
			jQuery('input[name="prazo_vencimento"]').val('');

		} else {
			
			// Desabilita
			jQuery('input[name="perc_desconto_dt_ini"]').attr('disabled','disabled').addClass('disabled').val('');
			jQuery('#img_perc_desconto_dt_ini').attr('disabled','disabled');
			
			jQuery('input[name="perc_desconto_dt_fim"]').attr('disabled','disabled').addClass('disabled').val('');
			jQuery('#img_perc_desconto_dt_fim').attr('disabled','disabled');
			
			jQuery('input[name="perc_desconto_dt_ini"]').removeClass('erro');
			jQuery('input[name="perc_desconto_dt_fim"]').removeClass('erro');
		}
	});
	
	
	/**
	 * @tag input(type=text)
	 * @Event OnKeyup
	 */
	jQuery('#valor').blur(function() {
		var valor = (jQuery(this).val()).replace('.','');
		valor = parseFloat(((jQuery(this).val()).replace(',','.')));
		//revalidarMoeda(this,2);
		if (valor > 0) {
			jQuery(this).removeClass('erro');
		}
	});

	jQuery('input[name="valor"]').keyup(function() {

		var val = jQuery(this).val();

		val = val.replace('.', '');
		val = val.replace(',', '.');


		if (val > 0) {
			// Habilita
			jQuery('input[name="valor_dt_ini"]').removeAttr('disabled').removeClass('disabled');
			//jQuery('#img_perc_desconto_dt_ini').removeAttr('disabled');

			jQuery('input[name="valor_dt_fim"]').removeAttr('disabled').removeClass('disabled');
			//jQuery('#img_perc_desconto_dt_fim').removeAttr('disabled');

			jQuery('input[name="perc_desconto"]').val('');
			jQuery('input[name="perc_desconto_dt_ini"]').val('');
			jQuery('input[name="perc_desconto_dt_fim"]').val('');
			jQuery('input[name="perc_desconto_dt_ini"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');
			jQuery('input[name="perc_desconto_dt_fim"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');

			jQuery('input[name="isento_cobranca"]').prop('checked', false);
			jQuery('input[name="isento_cobranca_dt_ini"]').val('');
			jQuery('input[name="isento_cobranca_dt_fim"]').val('');
			jQuery('input[name="isento_cobranca_dt_ini"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');
			jQuery('input[name="isento_cobranca_dt_fim"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');

			jQuery('input[name="periodicidade_reajuste"]').val('');
			jQuery('input[name="prazo_vencimento"]').val('');

		} else {

			// Desabilita
			jQuery('input[name="valor_dt_ini"]').attr('disabled','disabled').addClass('disabled').val('');
			//jQuery('#img_perc_desconto_dt_ini').attr('disabled','disabled');

			jQuery('input[name="valor_dt_fim"]').attr('disabled','disabled').addClass('disabled').val('');
			//jQuery('#img_perc_desconto_dt_fim').attr('disabled','disabled');

			jQuery('input[name="valor_dt_ini"]').removeClass('erro');
			jQuery('input[name="valor_dt_fim"]').removeClass('erro');
		}
	});


	/**
	 * @tag input(type=text)
	 * @Event OnKeyup
	 */
	jQuery('input[name="periodicidade_reajuste"]').blur(function() {

		var periodicidade = jQuery(this).val();

		periodicidade = periodicidade.replace('.', '');
		periodicidade = periodicidade.replace(',', '.');


		if (periodicidade > 0) {

			// Habilita
			jQuery('input[name="perc_desconto"]').val('');
			jQuery('input[name="perc_desconto_dt_ini"]').val('');
			jQuery('input[name="perc_desconto_dt_fim"]').val('');
			jQuery('input[name="perc_desconto_dt_ini"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');
			jQuery('input[name="perc_desconto_dt_fim"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');

			jQuery('input[name="isento_cobranca"]').prop('checked', false);
			jQuery('input[name="isento_cobranca_dt_ini"]').val('');
			jQuery('input[name="isento_cobranca_dt_fim"]').val('');
			jQuery('input[name="isento_cobranca_dt_ini"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');
			jQuery('input[name="isento_cobranca_dt_fim"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');

			jQuery('input[name="valor"]').val('');
			jQuery('input[name="valor_dt_ini"]').val('');
			jQuery('input[name="valor_dt_fim"]').val('');
			jQuery('input[name="valor_dt_ini"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');
			jQuery('input[name="valor_dt_fim"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');

			jQuery('input[name="prazo_vencimento"]').val('');
		}
	});


	/**
	 * @tag input(type=text)
	 * @Event OnKeyup
	 */
	jQuery('input[name="prazo_vencimento"]').keyup(function() {

		var prazo_vencimento = jQuery(this).val();

		prazo_vencimento = prazo_vencimento.replace('.', '');
		prazo_vencimento = prazo_vencimento.replace(',', '.');


		if (prazo_vencimento > 0) {

			/*if (prazo_vencimento > 120) {
				jQuery(this).val('120');
			}*/

			// Habilita
			jQuery('input[name="perc_desconto"]').val('');
			jQuery('input[name="perc_desconto_dt_ini"]').val('');
			jQuery('input[name="perc_desconto_dt_fim"]').val('');
			jQuery('input[name="perc_desconto_dt_ini"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');
			jQuery('input[name="perc_desconto_dt_fim"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');

			jQuery('input[name="isento_cobranca"]').prop('checked', false);
			jQuery('input[name="isento_cobranca_dt_ini"]').val('');
			jQuery('input[name="isento_cobranca_dt_fim"]').val('');
			jQuery('input[name="isento_cobranca_dt_ini"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');
			jQuery('input[name="isento_cobranca_dt_fim"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');

			jQuery('input[name="valor"]').val('');
			jQuery('input[name="valor_dt_ini"]').val('');
			jQuery('input[name="valor_dt_fim"]').val('');
			jQuery('input[name="valor_dt_ini"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');
			jQuery('input[name="valor_dt_fim"]').attr('disabled','disabled').addClass('disabled').val('').removeClass('erro');

			jQuery('input[name="periodicidade_reajuste"]').val('');

		}
	});


	/**
	 * @tag input(name=marcar_todos)
	 * @Event OnClick
	 */
	jQuery('body').delegate('input[name="marcar_todos"]', 'click', function(){
		if (jQuery(this).is(':checked')) {
			
			jQuery('input[name="marca_contrato[]"]').attr('checked', 'checked');
		} else {
			
			jQuery('input[name="marca_contrato[]"]').removeAttr('checked');
		}
	});
	
	/**
	 * @tag input(name=marca_contrato)
	 * @Event OnCick
	 */
//	jQuery('body').delegate('input[name="marca_contrato[]"]', 'click', function() {
//		
//		var marcados 	= jQuery('input[name="marca_contrato[]"]:checked').length;
//		var total	= jQuery('input[name="marca_contrato[]"]').length;
//		
//		if (marcados == total) {
//			
//			jQuery('input[name="marcar_todos"]').attr('checked', 'checked');
//		} else {
//			jQuery('input[name="marcar_todos"]').removeAttr('checked');
//		}
//	});
	
	
	/**
	 * @tag input(id=btn_retornar)
	 * @Event OnClick
	 */
	jQuery('#btn_retornar').click(function() {
		//window.location.href = window.location.href;
		jQuery('input[name="acao"]').val('retornar');
		jQuery('form[name="frm"]').submit();
	});
	
	
	/**
	 * @tag input(id=btn_confirmar)
	 * @Event OnClick
	 */	
	jQuery('#btn_confirmar').click(function() {
		
		// Limpa marcadores de campos
		jQuery('.erro').removeClass('erro');
		
		removeAlerta();
		/**
		 * Nivel
		 */
		var nivel = parseInt(jQuery('input[name="nivel"]:checked').val());
		
		/**
		 * Contrato
		 */
		var contrato  = jQuery('input[name="contrato"]').val();
		
		/**
		 *	Cliente (clioid)
		 */
		
		var nome_cliente = jQuery('input[name="cpx_pesquisa_cliente_nome"]').val(); 
		
		/**
		 * Tipo de contrato
		 */
		var tipoContrato = jQuery('[name="tipo_contrato"]').val();
		
		/**
		 * Obrigação financeira
		 */
		var obrigacaoFinanceira_checkbox = jQuery('.checkbox_obrigacao_financeira:checked');
		
		/**
		 * Valor
		 */
		var valor = jQuery('input[name="valor"]').val();
		valor = valor.replace('.', '');
		valor = valor.replace(',', '.');

        var valorDataIni = jQuery('input[name="valor_dt_ini"]').val();
        var valorDataFim = jQuery('input[name="valor_dt_fim"]').val();

		/**
		 * Isenção de cobrança
		 */
		var isento = jQuery('input[name="isento_cobranca"]').is(':checked');
		var isentoDataIni = jQuery('input[name="isento_cobranca_dt_ini"]').val();
		var isentoDataFim = jQuery('input[name="isento_cobranca_dt_fim"]').val();

		/**
		 * Perc. de deseconto
		 */
		var percDesconto = jQuery('input[name="perc_desconto"]').val();
		percDesconto = percDesconto.replace('.', '');
		percDesconto = percDesconto.replace(',', '.');
		
		var percDataIni  = jQuery('input[name="perc_desconto_dt_ini"]').val();
		var percDataFim  = jQuery('input[name="perc_desconto_dt_fim"]').val();
		
		//var peridiocidadeFaturamento = jQuery('select[name="periodicidade_faturamento"]').val();

		/**
		 * Perc. de deseconto
		 */
		var prazo_vencimento = jQuery('input[name="prazo_vencimento"]').val();

		/**
		 * Quantidade para faturamento
		 */
		//var quantidadeDe = jQuery('input[name="quantidade_faturamento_de"]').val();
		//var quantidadeAte = jQuery('input[name="quantidade_faturamento_ate"]').val();
		
                /**
		 * Importacao massiva de contratos
		 */
                var param_massivo = jQuery('input[name="param_massivo"]:checked').val();
                var arqcontratos = jQuery('input[name="arqcontratos"]').val();
                
		var validaObrigatorios        = true;
		var validaObrigacaoFinanceira = true;
		var validaDataIsento          = true;
		var validaDataDesconto        = true;
		var taxaValorObrigatorio      = true;
		var validaObservacao     	  = true;
		var validaPrazo_vencimento    = true;
		var validaValorData     	  = true;

		/**
		 * Valida obrigatoriedade
		 */
		validaPeriodoIsencao();


		/**
		 * Observacao
		 */
		var observacao = jQuery('[name="obs_param"]').val();

		switch (nivel) {
			
			case 1: 
				
				//  contrato
				if (contrato.length == 0 && param_massivo != 1) {
					validaObrigatorios = false;
					jQuery('input[name="contrato"]').addClass('erro');
				}

				//  observacao
				if (observacao.length == 0 || observacao.length < 50) {
					validaObservacao = false;
					jQuery('input[name="obs_param"]').addClass('erro');
				}
				
				break;
				
			case 2:
				
				//se o campo existir é um nome cadastro e valida se está preenchido
				if($('input[name="cpx_pesquisa_cliente_nome"]').length > 0){
					// Valida cliente	
					if (nome_cliente.length == 0) {
						
						validaObrigatorios = false;
						jQuery('input[name="cpx_pesquisa_cliente_nome"]').addClass('erro');
						jQuery('input[name="cpx_valor_cliente_cnpj"]').addClass('erro');
					}
				}

				//  observacao
				if (observacao.length == 0 || observacao.length < 50) {
					validaObservacao = false;
					jQuery('input[name="obs_param"]').addClass('erro');
				}
				
				break;
			case 3:
				
				// Valida tipo de contrato
				if (tipoContrato.length == 0) {
					validaObrigatorios = false;
					jQuery('select[name="tipo_contrato"]').addClass('erro');
				}

				//  observacao
				if (observacao.length == 0 || observacao.length < 50) {
					validaObservacao = false;
					jQuery('input[name="obs_param"]').addClass('erro');
				}
				
				break;
		}
		
		
		if(obrigacaoFinanceira_checkbox.length == 0) {
			jQuery('#obr_table').addClass('erro');
			validaObrigacaoFinanceira = false;
		}

                if (param_massivo == 1 & arqcontratos.length == 0) {
                    validaObrigatorios = false;
                    jQuery('input[name="arqcontratos"]').addClass('erro');
		}

//		if ((nivel == 1)||(nivel == 2)||(nivel == 3)) {
//
//			var taxaUnica      = jQuery("input[name='trocas_taxa_unica']").is(':checked') ? true : false;
//			var valorTaxaUnica = jQuery("input[name='trocas_valor']").val();
//
//
//			if (taxaUnica && jQuery.trim(valorTaxaUnica) == '') {
//				taxaValorObrigatorio = false;
//				jQuery("input[name='trocas_valor']").addClass('erro');
//			}
//
//		}

		
//		if (quantidadeDe == "" && quantidadeAte != "") {
//			validaObrigatorios = false;
//			jQuery('input[name="quantidade_faturamento_de"]').addClass('erro');
//		}
//		
//		if (quantidadeAte == "" && quantidadeDe != "") {
//			validaObrigatorios = false;
//			jQuery('input[name="quantidade_faturamento_ate"]').addClass('erro');
//		}

		
		/**
		 * Validação
		 * Valida se foi informado isenção o periodo deve ser informádo e validado
		 */
		if (isento == true) {
			
			if (isentoDataIni.length == 0 || isentoDataFim.length == 0) {
				
				if (isentoDataIni.length == 0 ) { // Valida data inicial
					jQuery('input[name="isento_cobranca_dt_ini"]').addClass('erro');
				}
				
				if (isentoDataFim.length == 0 ) { // Valida data final
					jQuery('input[name="isento_cobranca_dt_fim"]').addClass('erro');
				}
				
				validaDataIsento = false;
			}
			
			if (!validaPeriodoIsencao()) {
				return false;
			}
		}
		
		/**
		 * Validação
		 * Se for informado desconto, deve ser informado o período
		 */
		if (percDesconto > 0) {
			
			if (percDataIni.length == 0 || percDataFim.length == 0) {
				
				if (percDataIni.length == 0) {
					jQuery('input[name="perc_desconto_dt_ini"]').addClass('erro');
				}
				
				if (percDataFim.length == 0) {
					jQuery('input[name="perc_desconto_dt_fim"]').addClass('erro');
				} 
				
				validaDataDesconto = false;
			}
			
			if (!validaPeriodoPercDesconto()) {
				return false;
			}
		}


		/**
		 * Validação
		 * Se for informado desconto, deve ser informado o período
		 */
		if (valor > 0) {

			//  valor data
			if (valorDataIni.length == 0 || valorDataFim.length == 0) {

				if (valorDataIni.length == 0) {
					jQuery('input[name="valor_dt_ini"]').addClass('erro');
				}

				if (valorDataFim.length == 0) {
					jQuery('input[name="valor_dt_fim"]').addClass('erro');
				}

				validaValorData = false;
			}

			if (!validaPeriodoValor()) {
				return false;
			}
		}

		/**
		 * Valida??o
		 * Se for informado desconto, deve ser informado o per?odo
		 */
		if (prazo_vencimento != "") {
			if (prazo_vencimento <= 0 || prazo_vencimento > 120) {
				validaPrazo_vencimento = false;
				jQuery('input[name="prazo_vencimento"]').addClass('erro');
			}
		}
		
		
		// Se ainda exitir campos obrigatórios para preencher, mostra mensagem 
		if (!validaObrigatorios){
			criaAlerta(MSG_EXISTEM_CAMPOS_NAO_PREENCHIDOS);
			return false;
		}
		
		
		if(!validaObrigacaoFinanceira) {
			criaAlerta(MSG_SELECIONAR_OBRIGACAO_FINANCEIRA);
			return false;
		}
		
		if (!validaDataIsento) {
			criaAlerta(MSG_INFORMAR_PERIODO_ISENCAO);
			return false;
		}
		
		if (!validaDataDesconto) {
			criaAlerta(MSG_INFORMAR_PERIODO_DESCONTO);
			return false;
		} 

//		if (!taxaValorObrigatorio) {
//			criaAlerta('Valor da Taxa Única é obrigatório.');
//			return false;
//		}

		if (!validaObservacao) {
			criaAlerta(MSG_INFORMA_OBSERVACAO);
			return false;
		}

		if (!validaPrazo_vencimento) {
			criaAlerta(MSG_INFORMAR_PRAZO_VENCIMENTO);
			return false;
		}

		if (!validaValorData) {
			criaAlerta(MSG_VALOR_DATA);
			return false;
		}

		/**
		 * OUTRAS VALIDAÇÔES
		 */
		
		
		/**
		 * Validação
		 * Quantidade final não pode ser menor que quantidade inicial 
		 */
//		if (quantidadeAte != "" || quantidadeDe != "") {
//			
//			if (parseInt(quantidadeDe) == 0) {
//				jQuery('input[name="quantidade_faturamento_de"]').addClass('erro');
//				criaAlerta(MSG_QUANTIDADE_MAIOR_ZERO);
//				return false;
//			}
//			
//			if (parseInt(quantidadeAte) == 0) {
//				jQuery('input[name="quantidade_faturamento_ate"]').addClass('erro');
//				criaAlerta(MSG_QUANTIDADE_MAIOR_ZERO);
//				return false;
//			}
//			
//			if (parseInt(quantidadeAte) < parseInt(quantidadeDe)) {
//				criaAlerta(MSG_QUANTIDADE_FINAL_MENOR);
//				jQuery('input[name="quantidade_faturamento_ate"]').addClass('erro');
//				return false;
//			}
//		}

                        
		var parfoid = jQuery('input[name="parfoid"]').val();
		
		//se for vazio é inserção
		if(parfoid == ''){
                
                    jQuery('input[name="acao"]').val('salvar');
                    jQuery('form[name="frm"]').submit();
                 
                 // comentado devido a alteração de permitir cadastro de mais de um parâmetro
                //verifica se já possui parâmetros cadastrados
//			jQuery.ajax({
//				
//				url : 'fin_parametros_faturamento.php',
//				type : 'post',
//				data : 'acao=verificarCadastro&nivel=' + nivel + '&contrato=' + contrato +'&cod_cliente=' + jQuery('input[name="cpx_valor_cliente_nome"]').val() + '&tipo_contrato=' + tipoContrato,
//				success : function(data) {
//	
//					var result = jQuery.parseJSON(data);
//					 if(result == 1) {
//						jQuery('input[name="acao"]').val('salvar');
//                                                 jQuery('form[name="frm"]').submit();
//					 }else{
//						 //alert(result);
//						 return false;
//					 }
//				}
//			});
		
	    // é edição, então, salva as alterações
		}else{
                        jQuery('input[name="acao"]').val('salvar');
			jQuery('form[name="frm"]').submit();
		}

	});

    jQuery('#btn_salvar_massivo').click(function () {
        jQuery('input[name="acao"]').val('salvar');
        jQuery('form[name="frm-massivo"]').submit();
    });
    
	/**
	 * @tag select(name=tipo_contrato)
	 * @Event OnChange
	 */
	jQuery('select[name="tipo_contrato"]').change(function(){
		if (jQuery(this).val() != "") {
			jQuery(this).removeClass('erro');
		}
	});
	
	// Caso exista o botão excluir,
	//	Adicione o evento
	if (typeof jQuery('button[name="btn_excluir"]').get(0) != "undefined") {
		
		jQuery('button[name="btn_excluir"]').click(function(){
			
			if (confirm("Deseja excluir esse parâmetro do faturamento?")) {
				
				jQuery('input[name="acao"]').val('excluir');
				jQuery('form[name="frm"]').submit();
			}
		});
	}
	
	jQuery('#checkbox_obrigacao_financeira_todos').change(function() {
		if(jQuery(this).is(':checked')) {
			jQuery('.checkbox_obrigacao_financeira').attr('checked', true);
		} else {
			jQuery('.checkbox_obrigacao_financeira').attr('checked', false);
		}
	});
	
	jQuery('.checkbox_obrigacao_financeira').change(function() {
		if(jQuery('.checkbox_obrigacao_financeira:checked').length == jQuery('.checkbox_obrigacao_financeira').length) {
			jQuery('#checkbox_obrigacao_financeira_todos').attr('checked', true);
		} else {
			jQuery('#checkbox_obrigacao_financeira_todos').attr('checked', false);
		}
	});
    
    // habilitar ou desabilitar div de importação massiva
        jQuery('input[name="param_massivo"]').click(function () {

            var habilitado = jQuery(this).val();

            if (habilitado == 1) {
                jQuery('#div_arquivo_massivo').show();
                jQuery('input[name="contrato"]').val("");
            } else {
                jQuery('#div_arquivo_massivo').hide();
            }
    });

});

/**
 * Valida a data final com relação a data inicial do periodo de isenção
 * @returns {Boolean}
 */
function validaPeriodoIsencao() {
	
	var dtIni = jQuery('#isento_cobranca_dt_ini').val();
	var dtFim = jQuery('#isento_cobranca_dt_fim').val();
	
	if (diferencaEntreDatas(dtFim, dtIni) < 0){
		
		removeAlerta();
		criaAlerta(MSG_DATAFINAL_MENOR); 
		jQuery("#isento_cobranca_dt_fim").addClass("erro");
		return false;
	}
	return true;
}

/**
 * Valida a data final com relação a data inicial do periodo de porcentagem de desconto
 * @returns {Boolean}
 */
function validaPeriodoPercDesconto() {
	
	var dtIni = jQuery('#perc_desconto_dt_ini').val();
	var dtFim = jQuery('#perc_desconto_dt_fim').val();
	
	if (diferencaEntreDatas(dtFim, dtIni) < 0){
		
		removeAlerta();
		criaAlerta(MSG_DATAFINAL_MENOR); 
		jQuery("#perc_desconto_dt_fim").addClass("erro");
		return false;
	}
	return true;
}


/**
 * Valida a data final com relação a data inicial do periodo do valor
 * @returns {Boolean}
 */
function validaPeriodoValor() {

	var dtIni = jQuery('#valor_dt_ini').val();
	var dtFim = jQuery('#valor_dt_fim').val();

	if (diferencaEntreDatas(dtFim, dtIni) < 0){

		removeAlerta();
		criaAlerta(MSG_DATAFINAL_MENOR);
		jQuery("#valor_dt_fim").addClass("erro");
		return false;
	}
	return true;
}


/**
 * Validações do campo "Quantidade para faturamento"
 */
//function validaCampoQuantidadeFaturamento() {
//	
//	var quantidadeDe = parseInt(jQuery('input[name="quantidade_faturamento_de"]').val()); 
//	var quantidadeAte = parseInt(jQuery('input[name="quantidade_faturamento_ate"]').val());
//	
//	// Limpa o marcador de preenchimento
//	jQuery('input[name="quantidade_faturamento_de"]').removeClass('erro');
//	jQuery('input[name="quantidade_faturamento_ate"]').removeClass('erro');
//	
//	removeAlerta();
//	
//	if (isNaN(quantidadeDe) && isNaN(quantidadeAte)) {
//		return false;
//	}
//	
//	// Verifica se o campo foi preenchido
//	if (isNaN(quantidadeDe)) {
//		jQuery('input[name="quantidade_faturamento_de"]').addClass('erro');
//	}
//	
//	if (quantidadeDe == 0) {
//		jQuery('input[name="quantidade_faturamento_de"]').addClass('erro');
//		criaAlerta(MSG_QUANTIDADE_MAIOR_ZERO);
//	} 
//	
//	if (quantidadeAte == 0) {
//		jQuery('input[name="quantidade_faturamento_ate"]').addClass('erro');
//		criaAlerta(MSG_QUANTIDADE_MAIOR_ZERO);
//	} 
//	
//	// Verifica se o campo foi preenchido	
//	if (isNaN(quantidadeAte)) {
//		//jQuery('input[name="quantidade_faturamento_ate"]').addClass('erro');
//	}
//	
//	if (quantidadeAte < quantidadeDe) { 
//		jQuery('input[name="quantidade_faturamento_ate"]').addClass('erro');
//		criaAlerta(MSG_QUANTIDADE_FINAL_MENOR);
//	}
//}

function criaAlerta(msg, status) { 
	$("#mensagem").show();
    $("#mensagem").text(msg).removeAttr('class').addClass('mensagem alerta').addClass(status).show();
}


function removeAlerta() {
    $("#mensagem").hide();
}



/**
* Valida uma data passada
* @param string dia
* @param string mes
* @param string ano
* @return Bool TRUE em caso de data válida, do contrário FALSE
*/
function check_date(data){
	
		var dataTmp = data.split('/');
		var dia = dataTmp[0];
		var mes = dataTmp[1];
		var ano = dataTmp[2];
     	var dateRegExp =/^(19|20)\d\d-(0?[1-9]|1[012])-(0?[1-9]|[12][0-9]|3[01])$/;
        if (!dateRegExp.test(ano+"-"+mes+"-"+dia)) return false; // formato inválido
    	dia = parseInt(dia);
		mes = parseInt(mes);
		ano = parseInt(ano);
        if (dia == 31 && ( /^0?[469]$/.test(mes) || mes == 11) ) {
            return false; // dia 31 de um mes de 30 dias
        }else if (dia >= 30 && mes == 2) {
            return false; // mais de 29 dias em fevereiro
        }else if (mes == 2 && dia == 29 && !(ano % 4 == 0 && (ano % 100 != 0 || ano % 400 == 0))) {
            return false; // dia 29 de fevereiro de um ano não bissexto
        }else {
            return true; // Data válida
        }
}


function pad(number, length) {
	   
    var str = '' + number;
    
    while (str.length < length) {
        str = '0' + str;
    }
    return str;
};

