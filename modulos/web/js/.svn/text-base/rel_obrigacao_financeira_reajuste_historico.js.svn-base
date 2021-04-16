jQuery(document).ready(function(){

	jQuery("#gerar_csv").click(function(){
		jQuery("#form").submit();
	});

	
	jQuery("#ofrhconnumero").keyup(function() {
			var valor =jQuery("#ofrhconnumero").val().replace(/[^0-9]+/g,'');
		 jQuery("#ofrhconnumero").val(valor);
	});
		   	 

   //AUTOCOMPLETE PARA TELA DE CADASTRO DE CREDITO FUTURO- NOME DE CLIENtE
   jQuery( "#clinome" ).autocomplete({
    source: "rel_obrigacao_financeira_reajuste_historico.php?acao=buscarClienteNome",
    minLength: 2,        
    response: function(event, ui ) {

        mudarTamanhoAutoComplete(ui.content.length);
        jQuery("#cliente_id").val('');
        escondeClienteNaoEncontrado();

        console.log();

        if(!ui.content.length && jQuery.trim(jQuery(this).val()) != "") {
            mostraClienteNaoEncontrado(jQuery(this).val() + " não consta no cadastro.");
        }

        if(jQuery.trim(jQuery(this).val()) == "") {
            jQuery(this).val('');
        }

        jQuery(this).autocomplete("option", {
            messages: {
                noResults: '',
                results: function() {}
            }
        });   

    },
    select: function( event, ui ) {            

        jQuery("#cliente_id").val(ui.item.id);
        jQuery('#clinome').val(ui.item.nome);
    }       
    
     
});
   
   
   jQuery("#clinome").blur(function() {
       
       if (jQuery.trim(jQuery("#cliente_id").val()) == '') {
           jQuery(this).val('');
       }
   });

   /*
* Limita tamanho do autocomplete
*/
function mudarTamanhoAutoComplete(qtdOpcoes) {

    if (qtdOpcoes > 0) {

        var tamanhoOpcao = 23;//height de cada opÃ§Ã£o
        var tamanhoListagem = qtdOpcoes * tamanhoOpcao;
        jQuery('ul.ui-autocomplete').height(tamanhoListagem);
    }else{
        jQuery('ul.ui-autocomplete').height(0);
    }

}
/*
 * Mostra Mensagem cliente nao encontrado
 */
 function mostraClienteNaoEncontrado (msg) {

    msg_cliente = typeof msg && jQuery.trim(msg) != '' ? msg : ' Cliente consta no cadastro.';
    msg_cliente = wordwrap(msg_cliente, 75, "\n", true);    

    jQuery("#mensagem_alerta").text(msg_cliente);
    jQuery("#mensagem_alerta").removeClass("invisivel");
    jQuery("#nome_cliente, #cpf, #cnpj, #contrato").val('');
}

/*
 * Mostra Esconde Mensagem cliente nao encontrado
 */
 function escondeClienteNaoEncontrado() {
    jQuery("#mensagem_alerta").text("");
    jQuery("#mensagem_alerta").addClass("invisivel");
}

		if(jQuery.trim(jQuery('#clinome').val()).length == 0){
		
			if(jQuery.trim(dt_ini).length == 0 || jQuery.trim(dt_fim).length == 0){
				criaAlerta("Campo Data Acionamento obrigatório."); 
				jQuery("#dt_ini").addClass("inputError");
				jQuery("#dt_fim").addClass("inputError");
				return false;
			}
		}

function wordwrap( str, width, brk, cut ) {
     brk = brk || '\n';
     width = width || 75;
     cut = cut || false;

     if (!str) { return str; }

     var regex = '.{1,' +width+ '}(\\s|$)' + (cut ? '|.{' +width+ '}|.+$' : '|\\S+?(\\s|$)');

     return str.match( RegExp(regex, 'g') ).join( brk );
}

});