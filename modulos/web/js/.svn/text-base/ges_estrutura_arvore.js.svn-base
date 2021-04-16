function excluir(gmaoid, gmanivel, gmasubnivel, gmaano){

	 jQuery.ajax({
        type       : 'post',
        url        : 'ges_estrutura_arvore.php?acao=validarExclusao',
        data       : {
        	'gmaoid'      : gmaoid,
            'gmanivel'    : gmanivel,
            'gmasubnivel' : gmasubnivel,
            'gmaano'      : gmaano
        },
        dataType   : 'json',
        error      : function() {
            jQuery('#div_mensagem_geral')
                .removeClass('alerta sucesso invisivel')
                .addClass('erro')
                .html('Houve um erro na comunicação com o servidor.');
        },
        beforeSend : function() {
               
            jQuery("#div_mensagem_geral").html('').esconderMensagem();
        },
        success    : function(response) {
            if (response.status) {               
            	if(response.confirmacao){
            		r = confirm('Deseja realmente excluir o item selecionado?');
            		if(r){
            			jQuery('#acao').val('excluir');
            			jQuery('#gmaoid').val(gmaoid);
            			jQuery("#form").submit();
            		}
            	}else{
                    jQuery('#mensagem_erro, #mensagem_alerta, #mensagem_sucesso')
                        .addClass('invisivel')
                        .html('');

            		jQuery('#div_mensagem_geral')
                        .removeClass('erro sucesso invisivel')
                        .addClass('alerta')
                        .html('Esse item não pode ser excluído! Existe item abaixo relacionado.')
                        .slideDown();
            	}
            }else{
                 jQuery('#div_mensagem_geral')
                    .removeClass('alerta erro sucesso invisivel')
                    .addClass(response.mensagem.tipo)
                    .html(response.mensagem.texto)
                    .slideDown();
            }
        }
    });
}

jQuery(document).ready(function(){
   
   //botão novo
   jQuery("#bt_novo").click(function(){
   	   window.location.href = "ges_estrutura_arvore.php?acao=cadastrar";
   });
   
   //botão volta
   jQuery("#bt_voltar").click(function(){
       window.location.href = "ges_estrutura_arvore.php?camposPreenchidos=true";
   })

   jQuery('#bt_gravar').click(function(){
   		jQuery("#form_cadastrar").submit();
   })


   var script = 'ges_estrutura_arvore.php';

 	jQuery('#gmadepoid').change(function() {
        
        jQuery('#div_mensagem_geral')
            .removeClass('alerta erro sucesso')
            .addClass('invisivel')
            .html(null);

        jQuery.ajax({
            type       : 'post',
            url        : script + '?acao=buscarCargos',
            data       : {
            	'gmadepoid' : jQuery('#gmadepoid').val()
            },
            dataType   : 'json',
            beforeSend : function() {
                jQuery('#gmaprhoid, #gmaprhoid_pesq').mostrarCarregando();
                jQuery('#gmafunoid').val("");
            },
            complete   : function() {
                jQuery('#gmaprhoid, #gmaprhoid_pesq').esconderCarregando();
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
                    jQuery('#gmaprhoid, #gmaprhoid_pesq').html(response.html);
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
   
   jQuery('#gmaprhoid').change(function() {
        
        jQuery('#div_mensagem_geral')
            .removeClass('alerta erro sucesso')
            .addClass('invisivel')
            .html(null);

        jQuery.ajax({
            type       : 'post',
            url        : script + '?acao=buscarFuncionarios',
            data       : {
            	'gmaprhoid' : jQuery('#gmaprhoid').val(),
            	'gmadepoid' : jQuery('#gmadepoid').val()
            },
            dataType   : 'json',
            beforeSend : function() {
                jQuery('#gmafunoid').mostrarCarregando();
            },
            complete   : function() {
                jQuery('#gmafunoid').esconderCarregando();
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
                    jQuery('#gmafunoid').html(response.html);
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
});