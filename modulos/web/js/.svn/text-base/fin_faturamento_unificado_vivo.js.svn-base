jQuery(document).ready(function(){

    /**
     * No modo quirks a <th> não estava pegando a classe
     * Removendo e adicionando funciona!
     */
    jQuery('#quirksHack').removeClass('selecao');
    jQuery('#quirksHack').addClass('selecao');
    

    jQuery('#placa').mask('aaa-9999');

    jQuery('#placa').on('blur', function() {
        jQuery('#placa').val(jQuery('#placa').val().toUpperCase());
    });
   
    //botão limpar resumo
    jQuery("#bt_limparResumo").click(function(){
        jQuery('#acao').val('limparResumo');
        if(confirm("Deseja realmente limpar o resumo?")) {
            jQuery('#form').submit();
            return true;
        } else {
            return false;
        }
    });

    //botão parar resumo
    jQuery("#bt_pararResumo").click(function(){
        jQuery('#acao').val('pararResumo');
        if(confirm("Deseja realmente parar a geração do resumo?")) {
            jQuery('#form').submit();
            return true;
        } else {
            return false;
        }
    });

    //botão gerar resumo
    jQuery("#bt_gerarResumo").click(function(){
        jQuery('#acao').val('prepararResumo');
        jQuery('#form').submit();
    });

    //botão consultar resumo
    jQuery("#bt_consultarResumo").click(function(){
        jQuery('#acao').val('consultarResumo');
        jQuery('#form').submit();
    });
    
    //botão gerar faturamento
    jQuery("#bt_gerarFaturamento").click(function(){
        jQuery('#acao').val('prepararFaturamento');
        jQuery('#form').submit();
    });

    //botão gerar planilha csv
    jQuery("#bt_gerarPlanilha").click(function(){
        jQuery('#acao').val('gerarPlanilha');
        jQuery('#form').submit();
    });

    //botão gerar relatório pré-faturamento
    jQuery("#bt_gerarRelatorio").click(function(){
        jQuery('#acao').val('gerarRelatorio');
        jQuery('#form').submit();
    });

    //Validações e mascaras     
    jQuery('#cpf').mask('999.999.999-99');
    jQuery('#cnpj').mask('99.999.999/9999-99');
        
    //Seleciona o tipo de pessoa (PJ||PF)
    jQuery('input[name="tipoPessoa"]').change(function(){
        var tipoPessoa = jQuery(this).val();
        
        jQuery('.ui-helper-hidden-accessible').html('');
        
        //Limpa o ID do cliente para a busca
        jQuery("#nomeCliente").val('');
        
        if(tipoPessoa == 'J') {
            jQuery('#juridicaNome, #juridicaDoc').removeClass('invisivel');
            jQuery('#fisicaNome, #fisicaDoc').addClass('invisivel');
            jQuery('#juridicaNome input, #juridicaDoc input, #fisicaNome input, #fiscaDoc input').val('');
        } else {
            jQuery('#juridicaNome, #juridicaDoc').addClass('invisivel');
            jQuery('#fisicaNome, #fisicaDoc').removeClass('invisivel');
            jQuery('#juridicaNome input, #juridicaDoc input, #fisicaNome input, #fisicaDoc input').val('');
        }
                        
    });

    //AUTOCOMPLETE NOME PJ
    jQuery( "#razaoSocial" ).autocomplete({
        source: "fin_faturamento_unificado_vivo.php?acao=buscarClienteNome&filtro=J",
        minLength: 2,
        response: function(event, ui ) {

            mudarTamanhoAutoComplete(ui.content.length);
            escondeClienteNaoEncontrado();

            if(!ui.content.length && jQuery.trim(jQuery(this).val()) != "") {
                mostraClienteNaoEncontrado();
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
            //no change
            jQuery("#cnpj").val(ui.item.doc);
        }
    });

    jQuery('#nomeCliente').on('paste',function(){
        jQuery(this).keyup();
    });

    jQuery('#cnpj, #cpf').on('paste', function(){
        jQuery(this).autocomplete('search', jQuery(this).val());
    });

    //AUTOCOMPLETE NOME PF
    jQuery( "#nomeCliente" ).autocomplete({
        source: "fin_faturamento_unificado_vivo.php?acao=buscarClienteNome&filtro=F",
        minLength: 2,
        response: function(event, ui ) {

            mudarTamanhoAutoComplete(ui.content.length);
            escondeClienteNaoEncontrado();

            if(!ui.content.length && jQuery.trim(jQuery(this).val()) != "") {
                mostraClienteNaoEncontrado();
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
            jQuery("#cpf").val(ui.item.doc);
        }
    });

    jQuery('#selecao_todos').checarTodos('input.selecao');
   
});

function mudarTamanhoAutoComplete(qtdOpcoes) {

    if (qtdOpcoes > 0) {

        var tamanhoOpcao = 23;//height de cada opção
        var tamanhoListagem = qtdOpcoes * tamanhoOpcao;
        if (tamanhoListagem > 166) {
            jQuery('ul.ui-autocomplete').height(166);
        } else {
            jQuery('ul.ui-autocomplete').height(tamanhoListagem);
        }
    }else{
        jQuery('ul.ui-autocomplete').height(0);
    }

}

/*
 * Mostra Esconde Mensagem cliente nao encontrado
 */
function escondeClienteNaoEncontrado() {
    jQuery("#mensagem_alerta").text("");
    jQuery("#mensagem_alerta").addClass("invisivel");
}

/*
 * Mostra Mensagem cliente nao encontrado
 */
function mostraClienteNaoEncontrado (msg) {

    msg_cliente = typeof msg && jQuery.trim(msg) != '' ? msg : 'Cliente não consta no cadastro.';

    jQuery("#mensagem_alerta").text(msg_cliente);
    jQuery("#mensagem_alerta").removeClass("invisivel");
    jQuery("#cpf, #cnpj").val('');
}