function mudaAba(aba){

	var tokenVIVO = jQuery('#tokenVIVO').val();

	if (tokenVIVO) {
		location.href = 'prn_solicitacao_vivo.php?token='+tokenVIVO+'&acao='+aba;
	} else {
		location.href = 'prn_solicitacao_vivo.php?acao='+aba;
	}

}

    
$(document).ready(function() {
	
	/*
	 * customizar elementos do pesquisar cliente 
	 */
	$('[name="cpx_botao_pesquisa_cliente_nome"]').addClass('botao');
	$('[name="cpx_botao_pesquisa_cliente_nome"]').css('marginTop', '-4px');
	$('[name="cpx_pesquisa_cliente_nome"]').css('height', '20px');
	$('[name="cpx_pesquisa_cliente_nome"]').css('width', '370px');
	$('[name="cpx_pesquisa_cliente_nome"]').val($('#solicitacao_clinome').val());
	$('[name="cpx_valor_cliente_nome"]').val($('#solicitacao_clioid').val()).hide();
	$('[name="cpx_valor_cliente_nome"]').attr('obrigatorio', 'true');
	$('#cpx_div_clear_cliente_nome').remove();
	$('#limpar_cliente').css('marginTop', '-4px');
	$('#limpar_cliente').click(function(){
		$('[name="cpx_pesquisa_cliente_nome"]').val('');
		$('[name="cpx_valor_cliente_nome"]').val('');
	})
	
	/*
	 * validação do formulário ao salvar solicitação
	 */
	$('#btn_confirmar').click(function(){
		
		var validaCamposObrigatorios = true;
		
		$('[obrigatorio="true"]').each(function(i){
			$(this).removeClass('inputError');
			
			if ($.trim($(this).val()).length == 0) {
				$(this).addClass('inputError');
				validaCamposObrigatorios = false
			}
		});		
		
		if (validaCamposObrigatorios == false) {
			
			criaAlerta('Existem Campos Obrigatórios a serem preenchidos.');
			
			if ($('[name="cpx_valor_cliente_nome"]').val().length == 0) {
				$('[name="cpx_pesquisa_cliente_nome"]').addClass('inputError');	
			} else {
				$('[name="cpx_pesquisa_cliente_nome"]').removeClass('inputError');
			}
			
			return false;
		}
		
		if ($.trim($('#slpdescricao').val()).length < 5) {
			$('#slpdescricao').addClass('inputError');
			criaAlerta('O texto do campo detalhamento da solicitação é muito curto.');	
			return false;
		} 	
		
		$('#acao').val('salvarsolicitacao');
		$('#form').submit();
	})
	
	$('#incluir_tratativa').click(function(){
		
		if ($.trim($('#slpddescricao').val()).length == 0 ) {
			$('#slpddescricao').addClass('inputError');
			criaAlerta('O campo detalhamento da tratativa é obrigatório a ser preenchido.');	
			return false;
		} 
		
		$('#acao').val('salvartratativa');
		$('#form').submit();
		
	})
	
	$('#btn_salvar_motivo').click(function(){
		
		if ($.trim($('#slpmdescricao').val()).length == 0 ) {
			$('#slpmdescricao').addClass('inputError');
			criaAlerta('O campo descrição é obrigatório a ser preenchido.');	
			return false;
		}
		
		$('#acao').val('salvarmotivo');
		$('#form').submit();
	})
	
	$('#btn_excluir_motivo').click(function(){
		
		if($('#combobox_cadastro_motivo').val() == 0) {
			$('#combobox_cadastro_motivo').addClass('inputError');
			criaAlerta('Não foi selecionado registro para exclusão.');	
			return false;
		}
		
		if (confirm('Deseja excluir o motivo "' + $('#combobox_cadastro_motivo :selected').text() +'" ?')) {
			$('#acao').val('excluirmotivo');
			$('#form').submit();
		}
	})
	
	$('#combobox_cadastro_motivo')
	.change(function(){
		change_field_motivo_cadastro();
	})
	.ready(function(){
		change_field_motivo_cadastro();
	})
	
	function change_field_motivo_cadastro() {
		if ($('#combobox_cadastro_motivo').val() == 0) {
			$('#slpmdescricao').val('');
			$('#btn_excluir_motivo').hide();
		} else {
			$('#btn_excluir_motivo').show();
		}
	}
	
	
	$('#btn_pesquisar').click(function(){
		
		var valido = false;
		var data_ini = $('#data_inicial').val();
		var data_fim = $('#data_final').val();
		
		if(diferencaEntreDatas(data_fim, data_ini) > 150) {
	    	
	    	$("#data_inicial").addClass("inputError");
			$("#data_final").addClass("inputError");
	        criaAlerta('A data de vencimento inicial e final do período não pode ultrapassar o intervalo de 5 meses.');
	        return false;
	    }
	    
	    if(diferencaEntreDatas(data_fim, data_ini) < 0) {
	    	
	    	$("#data_inicial").addClass("inputError");
			$("#data_final").addClass("inputError");
	        criaAlerta('A data inicial deve ser menor que a data final do período.');
	        return false;
	    }
		
		if ($('#slpprotocolo_vivo').val().length > 0 || 
			$('#cpf_cnpj').val().length > 0 || 
			($('#ddd').val().length > 0 && $('#telefone').val().length > 0)) {
		
			valido = true;
		} 
				
		if (!valido) {
			if (data_ini.length == 0 || data_fim.length == 0) { 
			
				$("#data_inicial").addClass("inputError");
				$("#data_final").addClass("inputError");
				criaAlerta('É necessario informar o período para a pesquisa.');
	        	return false;
			}
		}
		
		$('#form').submit();
		
		return true;
		
	})
	
	$('#btn_retornar').click(function(){
		location.href='prn_solicitacao_vivo.php';
	})
	
	/*
	 * Ação botão
	 */
	$('#btn_novo').click(function() {
		$('#acao').val('cadastro');
		$('#form').submit();
	});		
	
});