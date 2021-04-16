jQuery(document).ready(function() {
	jQuery('#inpvl_referencia').maskMoney({
		symbol        : '%',
		thousands     : '.',
		decimal       : ',',
		precision     : 1,
		defaultZero   : true,
        allowZero: true
	});
	
	if(jQuery('#inpdt_inicial').length > 0 && jQuery('#inpdt_final').length > 0) {
		if(jQuery('#inpdt_inicial').val() && jQuery('#inpdt_final').val() && session_busca) {
			pesquisar(true);
		}
	}
	
	if(jQuery('#inpdt_apresentacao').length > 0) {
		jQuery('#ui-datepicker-div').addClass('invisivel'); 
	}
	
	jQuery('#btn_novo').click(function() {
		location.href = 'cad_inpc.php?acao=cadastrar';
	});
	
	jQuery('#btn_retornar').click(function() {
		location.href = 'cad_inpc.php?sessao';
	});
	
	jQuery('#frm_formulario').submit(function() {
		jQuery('#acao').val('salvar');
		jQuery('#btn_retornar').attr('disabled', 'disabled');
		jQuery('#btn_salvar').attr('disabled', 'disabled');
		jQuery('#div_mensagem').hideMessage()
			.removeClass('alerta')
			.removeClass('erro')
			.removeClass('sucesso')
			.html('');
		
		resetFormErros();
		
		jQuery.ajax({
			data     : jQuery('#frm_formulario').serialize(),
			dataType : 'json',
			type     : 'post',
			url      : 'cad_inpc.php',
			complete : function() {
				jQuery('#btn_retornar').removeAttr('disabled');
				jQuery('#btn_salvar').removeAttr('disabled');
			},
			error    : function(data, status, error) {
				jQuery('#div_mensagem').showMessage()
					.addClass('erro')
					.html('Houve um erro na comunicação com o servidor.');
			},
			success  : function(response) {
				if(response.status == 'errorlogin' && response.redirect) {
					location.href = response.redirect;
				} else if(response.status) {
					if(response.mensagem.classe == 'sucesso') {
						jQuery('input[type="text"]').val('');
						
						if(jQuery('#tipo').val() == 'alterar') {
							jQuery('#inpdt_referencia').remove();
							jQuery('#inpdt_apresentacao')
								.attr('id', 'inpdt_referencia')
								.attr('name', 'inpdt_referencia')
								.removeAttr('disabled');
							jQuery('#tipo').val('cadastrar');
						}
					}
					
					jQuery('#div_mensagem').showMessage()
						.addClass(response.mensagem.classe)
						.html(response.mensagem.texto);
				} else {
					showFormErros(response.dados);
					
					jQuery('#div_mensagem').showMessage()
						.addClass(response.mensagem.classe)
						.html(response.mensagem.texto);
				}
			}
		});
		
		return false;
	});
	
	jQuery('#frm_listagem').submit(function() {
		pesquisar(false);
		
		return false;
	});
	
});

function excluir(data) {
	if(confirm('Tem certeza que deseja excluir o registro?') == true) {
		jQuery('#btn_pesquisar').attr('disabled', 'disabled');
		jQuery('#btn_novo').attr('disabled', 'disabled');
		
		jQuery.ajax({
			data     : { acao : 'excluir', data : data },
			dataType : 'json',
			type     : 'post',
			url      : 'cad_inpc.php',
			complete : function() {
				jQuery('#btn_pesquisar').removeAttr('disabled');
				jQuery('#btn_novo').removeAttr('disabled');
			},
			error    : function(data, status, error) {
				jQuery('#div_mensagem').showMessage()
					.addClass('erro')
					.html('Houve um erro na comunicação com o servidor.');
			},
			success  : function(response) {
				if(response.status == 'errorlogin' && response.redirect) {
					location.href = response.redirect;
				} else if(response.status) {
					location.href = 'cad_inpc.php?sessao&msg=excluir';
				} else {
					jQuery('#div_mensagem').showMessage()
						.addClass(response.mensagem.classe)
						.html(response.mensagem.texto);
				}
			}
		});
	}
	
	return false;
}

function pesquisar(auto) {
	jQuery('#acao').val('pesquisar');
	jQuery('#btn_pesquisar').attr('disabled', 'disabled');
	jQuery('#btn_novo').attr('disabled', 'disabled');
	jQuery('#div_mensagem').hideMessage()
		.removeClass('alerta')
		.removeClass('erro')
		.removeClass('sucesso')
		.html('');
	jQuery('#div_mensagem_listagem').hideMessage();
	jQuery('.resultado').hide();
	jQuery('.separador').show();
	jQuery('.carregando').show();
	
	if(!auto) {
		jQuery('#div_mensagem_sucesso').hideMessage();
	}
	
	resetFormErros();
	
	jQuery.ajax({
		data     : jQuery('#frm_listagem').serialize(),
		dataType : 'json',
		type     : 'post',
		url      : 'cad_inpc.php',
		complete : function() {
			jQuery('#btn_pesquisar').removeAttr('disabled');
			jQuery('#btn_novo').removeAttr('disabled');
			jQuery('.carregando').hide();
		},
		error    : function(data, status, error) {
			jQuery('#div_mensagem').showMessage()
				.addClass('erro')
				.html('Houve um erro na comunicação com o servidor.');
			jQuery('.separador').hide();
		},
		success  : function(response) {
			if(response.status == 'errorlogin' && response.redirect) {
				location.href = response.redirect;
			} else if(response.status) {
				if(response.dados.length > 0) {
					jQuery('.listagem table tbody').html(response.html);
					jQuery('.resultado').show();
					
					if(response.dados.length > 1) {
						jQuery('.bloco_mensagens p').html(response.dados.length + ' registros encontrados');
					} else {
						jQuery('.bloco_mensagens p').html('1 registro encontrado');
					}
				} else {
					jQuery('#div_mensagem_listagem').showMessage();
				}
			} else {
				showFormErros(response.dados);
				
				jQuery('#div_mensagem').showMessage()
					.addClass(response.mensagem.classe)
					.html(response.mensagem.texto);
				jQuery('.separador').hide();
			}
		}
	});
	
	return true;
}