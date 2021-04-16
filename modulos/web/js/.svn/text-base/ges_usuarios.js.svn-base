jQuery(document).ready(function() {
	var script = 'ges_usuarios.php';

 	jQuery('#depoid').change(function() {
        
        jQuery('#acao').val('buscarCargos');

        jQuery('#div_mensagem_geral')
            .removeClass('alerta erro sucesso')
            .addClass('invisivel')
            .html(null);

        jQuery.ajax({
            type       : 'post',
            url        : script,
            data       : jQuery('#form_pesquisa').serialize(),
            dataType   : 'json',
            beforeSend : function() {
                jQuery('#prhoid').mostrarCarregando();
            },
            complete   : function() {
                jQuery('#prhoid').esconderCarregando();
            },
            error      : function() {
                jQuery('#div_mensagem_geral')
                    .removeClass('alerta sucesso invisivel')
                    .addClass('erro')
                    .html('Houve um erro na comunicação com o servidor.');
            },
            success    : function(response) {               
                if (response.status === 'errorlogin' && response.redirect) {
                    location.href = response.redirect;
                } else if (response.status && response.html) {
                    jQuery('#prhoid').html(response.html);
                } else {
                    jQuery('#div_mensagem_geral')
                        .removeClass('alerta erro sucesso invisivel')
                        .addClass(response.mensagem.tipo)
                        .html(response.mensagem.texto);
                }
            }
        });
        return true;
    });

 	jQuery('#bt_pesquisar').click(function() {
        
        jQuery('#acao').val('pesquisar');

        jQuery('#form_pesquisa').submit();

        return true;
    });

 	jQuery(".checkbox").click(function(){
 		if(jQuery(this).val() == 1){
 			jQuery(this).val(0);
 		}else{
 			jQuery(this).val(1);
 		}
 	});

 	jQuery("#bt_atualizar").click(function(){
 		
        var ids = jQuery("#ids").val();
        funcionarios = ids.split(",");
 		
        var atualizacao = new Object();
 		
        jQuery.each( funcionarios, function( key, id ) {
         	atualizacao[id] = new Object();
         	atualizacao[id]['importacao'] = jQuery("#importacao_"+id).val();
         	atualizacao[id]['criar_pa'] = jQuery("#criar_pa_"+id).val();
         	atualizacao[id]['super_usuario'] = jQuery("#super_usuario_"+id).val();
         	atualizacao[id]['criar_acao'] = jQuery("#criar_acao_"+id).val();
        });

        jQuery.ajax({
	        type : 'post',
	        data :  {
	            'atualizacao' : atualizacao
	        },
	        url  : 'ges_usuarios.php?acao=atualizar',
	        dataType : 'json',
            beforeSend : function() {
               
                jQuery("#div_mensagem_geral").html('').esconderMensagem();
            },
			success: function (response){
                jQuery('#div_mensagem_geral')
                    .removeClass('alerta erro sucesso invisivel')
                    .addClass(response.mensagem.tipo)
                    .html(response.mensagem.texto)
                    .slideDown();
			},
			error : function(){
            	jQuery('#div_mensagem_geral')
                    .removeClass('alerta sucesso invisivel')
                    .addClass('erro')
                    .html('Houve um erro na comunicação com o servidor.');
        	}
		});
 	})
});

