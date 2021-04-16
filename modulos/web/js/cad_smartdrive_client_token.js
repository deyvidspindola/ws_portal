jQuery(document).ready(function(){

	var msg_campos_obrigatorios = "Existem campos obrigatórios não preenchidos.";
	var msg_sucesso_excluir     = "Registro excluído com sucesso.";
	var msg_sucesso_editar      = "Registro alterado com sucesso.";
	var msg_erro                = "Houve um erro no processamento dos dados.";
	var router                  = "cad_smartdrive_client_token.php";

	//Acrescenta * na label Nome do Cliente na tela de cadastro
	jQuery("div.cliente #cpx_pesquisa_cliente_nome_label").html( jQuery("#cpx_pesquisa_cliente_nome_label").html() + " *");

	//Tratamento somente numeros inteiros
    jQuery('body').on('keyup blur', '.numero', function() {
        jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
    });

	//Botão novo
	jQuery("#bt_novo").click(function(){
	   window.location.href = router + "?acao=cadastrar";
	});

	//Botão voltar
	jQuery("#bt_voltar").click(function(){
	   window.location.href = router;
	});

	//Botão confirmar
	jQuery("#bt_gravar").click(function(){        
	    isCamposValidos = jQuery.fn.validarCamposObrigatorios();
	    
	    if( isCamposValidos ) {
	        jQuery('form').submit();;
	    }
	});

	//Acoes do icone excluir
    jQuery("table").on('click','.excluir',function(event) {

        event.preventDefault();

        tokenid = jQuery(this).data('tokenid');

        jQuery("#msg_dialogo_exclusao").dialog({
          title: "Confirmação de Exclusão",
          resizable: false,
          modal: true,
          buttons: {
            "Sim": function() {

              jQuery( this ).dialog( "close" );

                jQuery.ajax({
                    url: 'cad_smartdrive_client_token.php',
                    type: 'POST',
                    data: {
                        acao: 'excluir',
                        tokenid: tokenid
                    },
                    success: function(data) {

                      try{

                        if(data == 'OK') {
                            jQuery('#linha_' + tokenid).remove();
                            jQuery('#mensagem_sucesso').html(msg_sucesso_excluir);
                            jQuery('#mensagem_sucesso').show();
                          	aplicarCorLinha();

                        } else { 
                            jQuery('#mensagem_erro').html(msg_erro);
                            jQuery('#mensagem_erro').show();
                        }
                     } catch(erro) { 
                        jQuery('#mensagem_erro').html(msg_erro);
                        jQuery('#mensagem_erro').show();
                     }

                    }
                });

            },
            "Não": function() {
              jQuery( this ).dialog( "close" );
            }
          }
        });
    });

	//Acoes do icone editar
    jQuery("table").on('click','.editar',function(event) {

        event.preventDefault();
        tokenid = jQuery(this).data('tokenid');
        window.location.href = router + "?acao=editar&tokenid=" + tokenid;
    });

	//Habilita o botão Confirmar ao selecionar cliente
	jQuery("div.cliente #cpx_div_result_cliente_nome").on("click", function() {
		
		if(jQuery('#cpx_valor_cliente_nome').val() == '') {
			jQuery('#bt_gravar').addClass('desabilitado');
            jQuery('#bt_gravar').prop('disabled',true);
		} else {
			jQuery('#bt_gravar').removeClass('desabilitado');
            jQuery('#bt_gravar').removeProp('disabled');
		}
	});

	//Desabilita o botão Confirmar ao acionar o botão Limpar do comp de pesq de clientes  
	jQuery("div.cliente #cpx_div_clear_cliente_nome").on("click", function() {
		
		jQuery('#bt_gravar').addClass('desabilitado');
        jQuery('#bt_gravar').prop('disabled',true);
	});

	//Corrige a cor da linha oa excluir registro da listagem
	function aplicarCorLinha(){
		var cor = '';

		//remove cores
		jQuery('.listagem table tr').removeClass('par');
		jQuery('.listagem table tr').removeClass('impar');

		//aplica cores
		jQuery('.listagem table tr').each(function(){
			cor = (cor == "par") ? "impar" : "par";
			jQuery(this).addClass(cor);
		});
	}

	//Oculta msgs
	jQuery.fn.limpaMensagens = function() {
		jQuery('.componente_nenhum_cliente').hideMessage();
		jQuery(".erro").removeClass("erro");
	    jQuery('#msgalerta,#msgsucesso,#msgerro').hideMessage();
	}

	//Valida campos obrigatórios ao clicar no botão confirmar
	jQuery.fn.validarCamposObrigatorios = function() {

		jQuery.fn.limpaMensagens();
		
		var validacao = true;

		if (jQuery("#token").val() == ''){
			
			$('#token').addClass('erro');
			validacao = false;
		}

		if (jQuery("#dt_expiracao").val() == ''){
			
			$('#dt_expiracao').addClass('erro');
		    validacao = false;
		}

		if (jQuery("#site_name").val() == ''){
			
			$('#site_name').addClass('erro');
		    validacao = false;
		}

		if(jQuery("input[name='cpx_valor_cliente_nome']").val() == ''){   		

			//cpx_pesquisa_cliente_nome_label
			$("input[name='cpx_pesquisa_cliente_nome']").addClass('erro').val('');
			$("input[name='cpx_valor_cliente_cnpj']").addClass('erro').val('');
			$("input[name='cpx_valor_cliente_cpf']").addClass('erro').val('');
			$("input[name='cpx_valor_tipo_pessoa']").addClass('erro').val('');
			validacao = false;
		}
		
		if(!validacao){
			jQuery('#mensagem_alerta').html('Existem campos obrigatórios não preenchidos.').showMessage();
			//return false;
		}

		return validacao;
	}

});