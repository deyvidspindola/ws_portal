jQuery(document).ready(function(){

	tabListener();

	formSubmitListener();

	contratoListener();

	numericListener();

	tipoPessoaListener();

	formataCpfCnpj();

	$("#cep").mask("99999-999",{placeholder:""});

});

function formataCpfCnpj() {
	var tipoPessoa = jQuery("#tipo_pessoa_cotacao").val();

	if(tipoPessoa.length == 0) {
		jQuery("#cpf_cnpj").val('');
		jQuery("#cpf_cnpj").attr('disabled' , 'disabled');
		jQuery("#cpf_cnpj").attr('placeholder' ,'Selecione o tipo de pessoa.');
	} else if(tipoPessoa.length > 0) {

		jQuery("#cpf_cnpj").removeAttr('disabled');
		jQuery("#cpf_cnpj").attr('placeholder' ,'');

		if(tipoPessoa == '1') {
			$("#cpf_cnpj").mask("999.999.999-99",{placeholder:""});
		} else if(tipoPessoa == '2') {
			$("#cpf_cnpj").mask("99.999.999/9999-99",{placeholder:""});
		}

	}
}

function tipoPessoaListener(){

	jQuery("#tipo_pessoa_cotacao").on('change', function(e) {
		formataCpfCnpj();
	});
	
}

function numericListener() {
	// Campos numericos
	jQuery('.numeric').on('change',function(e) {
		var campo = jQuery(this).attr('id');
		var input = document.getElementById(campo);

		if (input.value != input.value.replace(/[^0-9]/g, '')) {
			jQuery("#" + campo).val('')
			e.preventDefault();
		}

	});
}

function limpaDadosContratoProposta() {

	var campos = [
					'nome_cliente',
					'sexo',
					'estado_civil',
					'profissa',
					'dt_nasc',
					'pep1',
					'pep2',
					'ddd_res',
					'fone_res',
					'ddd_cel',
					'num_cel',
					'email',
					'endereco',
					'endereco_num',
					'complemento',
					'cidade',
					'uf',
					'placa',
					'chassi',
					'uti_vei',
					'tipo_seguro',
					'forma_pag',
					'classe_produto_prop',
					'id_corretor_intranet'
				]; 

	jQuery.each(campos, function(key,value){
		jQuery("#" + value).val("");
	});
}

function popularCamposProposta(result) {
	jQuery.each(result, function(i,item){

		for (var key in item) {
			jQuery("#" + key).val(item[key]);
		}

  	});
}

function contratoListener() {

	jQuery("#num_contrato").bind('input propertychange', function() {

    	var numContrato = jQuery("#num_contrato").val();

    	jQuery.ajax({
	    	data: {
    			'acao':'dados_cli',
    			'connumero': numContrato
    		},
    		dataType: "JSON",
	    	type: "POST",
	    	success: function(result){

	    		if(result) {
		    		if(result.length == 0){
		    			limpaDadosContratoProposta();
		    		} else {
		    			popularCamposProposta(result);
	        		}
        		}
	    	}
		});

    });
}

function tabListener() {

	jQuery("#aba_cotacao").click(function() {

		jQuery("#proposta,#apolice").hide();

		jQuery("#cotacao").show();

		// abas
		jQuery("#aba_proposta,#aba_apolice").removeClass('ativo');

		jQuery("#aba_apolice,#aba_proposta").css({ background: "url(images/fundo.gif)" 	});

		jQuery("#aba_cotacao").addClass('ativo');

	});

	jQuery("#aba_proposta").click(function() {

		jQuery("#cotacao,#apolice").hide();

		jQuery("#proposta").show();

		// abas
		jQuery("#aba_apolice,#aba_cotacao").removeClass('ativo');

		jQuery("#aba_apolice,#aba_cotacao").css({ background: "url(images/fundo.gif)" });

		jQuery("#aba_proposta").addClass('ativo');
		
	});

	jQuery("#aba_apolice").click(function() {

		jQuery("#cotacao,#proposta").hide();

		jQuery("#apolice").show();

		// abas
		jQuery("#aba_proposta,#aba_cotacao").removeClass('ativo');

		jQuery("#aba_cotacao,#aba_proposta").css({ background: "url(images/fundo.gif)" });

		jQuery("#aba_apolice").addClass('ativo');
		
	});
}

function formSubmitListener() {

	jQuery("#enviar_cotacao").click(function(){
		jQuery("#form_cotacao").submit();
	});
	
	jQuery("#enviar_proposta").click(function() {
		jQuery("#form_proposta").submit();
	});

	jQuery("#enviar_apolice").click(function() {
		jQuery("#form_apolice").submit();
	});

}