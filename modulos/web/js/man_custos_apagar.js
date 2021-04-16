

var msg_alerta = "";
var linhaDigitavel = "";
var stringCodigoBarras = "";
var botaoclick = 1;
var msg1 = "";

function mostraMensagem(msg1){
    jQuery('#mensagem_sucesso').hide();
    jQuery('#mensagem_alerta').html(msg1);
    jQuery('#mensagem_alerta').show();        
}

/**
 * Esconde todas as mensagens e Erros
 */
function esconderMensagens() {

    jQuery('#msg_alerta').hide();
    jQuery('#mensagem_alerta').hide();
    jQuery('#msg_sucesso').hide();
    jQuery('#msg_erro').hide();
    jQuery('.obrigatorio').removeClass('erro');
}

/***
 * FUNÇÃO PARA TRANSFORMAR UM VALOR FLOAT PARA O TIPO MOEDA
 * return 0.000,00
 */

function float2moeda(num) {

    //var num;
    var x = 0;        
    if(num < 0) {
        num = Math.abs(num);
        x = 1;
    }
    if(isNaN(num)) {
        num = "0";
    }
    cents   = Math.floor(( num * 100 + 0.5 ) % 100);
    num     = Math.floor(( num * 100 + 0.5 ) / 100).toString();

    if(cents < 10) {
        cents = "0" + cents;
    }
    for (var i = 0; i < Math.floor((num.length - ( 1 + i )) / 3); i++){            
         num = num.substring( 0, num.length - (4 * i + 3)) + '.' + num.substring(num.length - (4 * i + 3));
    }
    ret = num + ',' + cents;
    if (x == 1) {
        ret = ' - ' + ret;
    }
    
    return ret;
}

/**
 * FUNÇÃO PARA TRANSFORMAR UM VALOR MOEDA PARA O TIPO FLOAT
 * return 000.00
 */

function moeda2float(num1){
    try{
        //var num;
        var valorCampo = num1.replace(/[^a-zA-Z 0-9]+/g,'');
        var valornovo = parseFloat(valorCampo) / 100;

        if(isNaN(valornovo)){
            valornovo = 0
        }        
        return valornovo;

    }catch(e){
        //alert(e);
    }
    //return parseFloat(num.replace(/[^a-zA-Z 0-9]+/g,'')) / 100 ;
}    

/**
 * FUNÇÃO PARA VERIFICAR SE O CAMPO VALOR BRUTO É IGUAL AO VALOR PAGO
 * O VALOR PAGO É A SOMA DOS DESPESAS + RECEITAS + VALOR DO BOLETO.
 */ 
function calculaValor_liquido_total(){

    var valorTotal              = 0.00;
    var valorPago               = 0.00;
    var valorBruto              = 0.00;
    var valorMulta              = 0.00;
    var valorDescontoNF         = 0.00;
    var vl_desc_valor_total     = 0.00;
    var vl_desc_valor_titulo    = 0.00;
    var vl_juros_multa          = 0.00;
    var valorJuros              = 0.00;
    var valorTarifa_bancaria    = 0.00;
    var valorIr                 = 0.00;
    var valorPis                = 0.00;
    var valorConfins            = 0.00;
    var valorCsll               = 0.00;
    var valorInss               = 0.00;
    var valorIss                = 0.00;
    var valorCsrf               = 0.00;
    //var valor_entidades         = 0.00;
    
    var valorBruto              = moeda2float( $("#apgvl_apagar").val() );
    var valorMulta              = moeda2float( $("#apgvl_multa").val() );
    var valorDescontoNF         = moeda2float( $("#apgvl_desconto").val() );    
    var valorJuros              = moeda2float( $("#apgvl_juros").val() );
    var valorTarifa_bancaria    = moeda2float( $("#apgvl_tarifa_bancaria").val() );
    var valorIr                 = moeda2float( $("#apgvl_ir").val() );
    var valorPis                = moeda2float( $("#apgvl_pis").val() );
    var valorConfins            = moeda2float( $("#apgvl_cofins").val() );
    var valorCsll               = moeda2float( $("#apgvl_csll").val() );
    var valorInss               = moeda2float( $("#apgvl_inss").val() );
    var valorIss                = moeda2float( $("#apgvl_iss").val() );        
    var valorCsrf               = moeda2float( $("#apgcsrf").val() );
    //var valor_entidades         = moeda2float( $("#apgvalor_entidades").val() );
    
    if( $("#apgtipo_docto").val() == "05" && $("#apgforma_recebimento").val() == "31" ) { 

        // Se for Forma de Pagamento: Boleto e Tipo de Pagamento: Outros 
        vl_desc_valor_total     = valorDescontoNF;
        vl_desc_valor_titulo    = 0;
        vl_juros_multa_total    = valorJuros + valorMulta ;
        vl_juros_multa_titulo   = 0;        

        $("#apgvl_desconto").removeAttr('disabled');

    }else if( $("#apgtipo_docto").val() == "11" && $("#apgforma_recebimento").val() == 31){
        
        // Se for Forma de Pagamento: Boleto e Tipo de Pagamento: Concessionarias
        vl_desc_valor_total     = 0;
        vl_desc_valor_titulo    = valorDescontoNF;
        vl_juros_multa_total    = 0;
        vl_juros_multa_titulo   = valorJuros + valorMulta;
        valorTarifa_bancaria    = 0;

        $("#apgvl_desconto").removeAttr('disabled');

    }else if( ($("#apgtipo_docto").val() == "09" ) && $("#apgforma_recebimento").val() == 31){
        
        // Se for Forma de Pagamento: Boleto e Tipo de Pagamento: FGTS ou GNRE.
        vl_desc_valor_total     = valorDescontoNF;
        vl_desc_valor_titulo    = 0;
        vl_juros_multa_total    = valorJuros + valorMulta ;
        vl_juros_multa_titulo   = 0;
        valorTarifa_bancaria    = 0;

        //$("#apgvl_desconto").removeAttr('disabled');
        $("#apgvl_desconto").attr('disabled','disabled');

    }else if( ($("#apgtipo_docto").val() == "10" ) && $("#apgforma_recebimento").val() == 31){
        
        // Se for Forma de Pagamento: Boleto e Tipo de Pagamento: FGTS ou GNRE.
        vl_desc_valor_titulo    = valorDescontoNF;
        vl_desc_valor_total     = 0;        
        vl_juros_multa_total    = valorJuros + valorMulta ;
        vl_juros_multa_titulo   = 0;
        valorTarifa_bancaria    = 0;

        $("#apgvl_desconto").removeAttr('disabled');

    }else if( ($("#apgtipo_docto").val() == "05" ) && ( $("#apgforma_recebimento").val() == "0" || $("#apgforma_recebimento").val() == "4") ){
        
        vl_desc_valor_titulo    = valorDescontoNF;
        vl_desc_valor_total     = 0;        
        vl_juros_multa_total    = valorJuros + valorMulta ;
        vl_juros_multa_titulo   = 0;
        valorTarifa_bancaria    = 0;
        
        // se for forma de pagamento Cheque ou Dinheiro e tipo de Pagamento == outros        
        $("#apgvl_desconto").removeAttr('disabled');
    
    }else{ // demais casos 

        $("#apgvl_desconto").attr('disabled','disabled');
        
        vl_desc_valor_titulo    = 0;
        vl_desc_valor_total     = valorDescontoNF;        
        vl_juros_multa_titulo   = 0;
        vl_juros_multa_total    = valorJuros + valorMulta ;
        valorTarifa_bancaria    = 0;
    }

    valorPago   = valorBruto + valorTarifa_bancaria + vl_juros_multa_titulo - (vl_desc_valor_titulo + valorIr + valorPis + valorConfins + valorCsll + valorInss + valorIss + valorCsrf ) ;
    valorTotal  = valorPago + (vl_juros_multa_total - vl_desc_valor_total);
    
    valorPago   = (( valorPago  < 0) ? valorPago  : valorPago   );
    valorTotal  = (( valorTotal < 0) ? valorTotal : valorTotal  );
    
    $("#apgvl_pago").val( float2moeda(valorPago) );
    $("#apgvl_total").val( float2moeda(valorTotal) );

    if(valorPago < 0){
        return false;
    }else if(valorTotal < 0){
        return false;
    }else{
        return true;
    }

}

jQuery(document).ready(function(){

    jQuery("#tipvdescricao").live('change keypress blur keyup',function() {
        jQuery("#tipvdescricao").val(jQuery("#tipvdescricao").val().replace(/[^a-zA-Z0-9 ]/gi, '').replace(/[_]/g, '-'))
    });

    //Autocomplete Fornecedor
    jQuery('#cmp_fornecedor_autocomplete').autocomplete({    
        source: 'man_custos_apagar.php?acao=buscarFornecedor',
        minLength: 3,
        response: function(event, ui) {
            
            $("#cmp_fornecedor").val(""); // Limpa o campo para evitar a gravação do fornecedor errado.           
            
            if (!ui.content.length) {
                mostraMensagem('Nenhum fornecedor encontrado com o termo: ' + $(this).val());
                $("#fordocto").val("");                
                $("#cmp_fornecedor").val("");
            } else {
                esconderMensagens();
            }
        },
        select: function(event, ui ) {            
            $("#cmp_fornecedor").val(ui.item.id);
            $("#fordocto").val(ui.item.fordocto);
            $("#cmp_fornecedor_autocomplete").val(ui.item.label);
        }
    });
    
    /**
     * Acoes do icone excluir
     */
     jQuery("form").on('click','.excluir',function(event) {

        event.preventDefault();

        id = jQuery(this).data('apgoid');        

        jQuery("#mensagem_excluir").dialog({
            title: "Confirmação de Exclusão",
            resizable: false,
            width: 500,
            modal: true,
            buttons: {
                "Sim": function() {
                jQuery( this ).dialog( "close" );

                    jQuery.ajax({
                        url: 'man_custos_apagar.php',
                        type: 'POST',
                        data: {
                            acao: 'excluir',
                            apgoid: id
                        },
                        success: function(data) {

                            if(data) {
                                esconderMensagens();

                                if(data == 'OK') {
                                    alert("Registro apagado com sucesso!");
                                    window.location.href = 'custos_apagar.php';
                                    return false;
                                } else {
                                    jQuery('#mensagem_erro').html("Houve um erro no processamento dos dados.");
                                    jQuery('#mensagem_erro').show();
                                }
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

    /**
     * CHAMADA PARA A MESMA FUNÇÃO PELO BOTÃO GERAR CODIGO DE BARRAS.
     */ 
    jQuery(".botao_gerar").click(function() {
        
        botaoclick = 2;
        var camposDestaque = new Array();
        msg_alerta = "";

        $("#apgcodigo_barras").val("");
        
        if($("#apgforma_recebimento").val() == 31) {            
            
            if( $("#apgtipo_docto").val() == 05) {

                tipoOper = 1;

                if($("#apglinha_digitavel1").val() == "" ){         camposDestaque.push({campo:$("#apglinha_digitavel1").attr("id")}); }
                if($("#apglinha_digitavel1").val().length != 5) {   camposDestaque.push({campo:$("#apglinha_digitavel1").attr("id")}); }
                if($("#apglinha_digitavel2").val() == "" ){         camposDestaque.push({campo:$("#apglinha_digitavel2").attr("id")}); }
                if($("#apglinha_digitavel2").val().length != 5 ){   camposDestaque.push({campo:$("#apglinha_digitavel2").attr("id")}); }
                if($("#apglinha_digitavel3").val() == "" ){         camposDestaque.push({campo:$("#apglinha_digitavel3").attr("id")}); }
                if($("#apglinha_digitavel3").val().length != 5 ){   camposDestaque.push({campo:$("#apglinha_digitavel3").attr("id")}); }
                if($("#apglinha_digitavel4").val() == "" ){         camposDestaque.push({campo:$("#apglinha_digitavel4").attr("id")}); }
                if($("#apglinha_digitavel4").val().length != 6 ){   camposDestaque.push({campo:$("#apglinha_digitavel4").attr("id")}); }
                if($("#apglinha_digitavel5").val() == "" ){         camposDestaque.push({campo:$("#apglinha_digitavel5").attr("id")}); }
                if($("#apglinha_digitavel5").val().length != 5 ){   camposDestaque.push({campo:$("#apglinha_digitavel5").attr("id")}); }
                if($("#apglinha_digitavel6").val() == "" ){         camposDestaque.push({campo:$("#apglinha_digitavel6").attr("id")}); }
                if($("#apglinha_digitavel6").val().length != 6 ){   camposDestaque.push({campo:$("#apglinha_digitavel6").attr("id")}); }
                if($("#apglinha_digitavel7").val() == "" ){         camposDestaque.push({campo:$("#apglinha_digitavel7").attr("id")}); }
                if($("#apglinha_digitavel7").val().length != 1 ){   camposDestaque.push({campo:$("#apglinha_digitavel7").attr("id")}); }
                if($("#apglinha_digitavel8").val() == "" ){         camposDestaque.push({campo:$("#apglinha_digitavel8").attr("id")}); }
                if($("#apglinha_digitavel8").val().length != 14 ){  camposDestaque.push({campo:$("#apglinha_digitavel8").attr("id")}); }                

                if( $("#apglinha_digitavel1").val() == "" && $("#apglinha_digitavel2").val() == "" && $("#apglinha_digitavel3").val() == "" && $("#apglinha_digitavel4").val() == "" && 
                    $("#apglinha_digitavel5").val() == "" && $("#apglinha_digitavel6").val() == "" && $("#apglinha_digitavel7").val() == "" && $("#apglinha_digitavel8").val() == "" ){

                    msg_alerta = "O campo 'Linha Digitável' deve ser informado para gerar o código de barras.";

                }else if($("#apglinha_digitavel1").val().length != 5 || $("#apglinha_digitavel2").val().length != 5 || $("#apglinha_digitavel3").val().length != 5 || $("#apglinha_digitavel4").val().length != 6 || 
                         $("#apglinha_digitavel5").val().length != 5 || $("#apglinha_digitavel6").val().length != 6 || $("#apglinha_digitavel7").val().length != 1 || $("#apglinha_digitavel8").val().length != 14 ){
                    
                    msg_alerta = "O campo 'Linha Digitável' é inválido, informe todos os números da linha digitável";
                
                }

            }else if($("#apgtipo_docto").val() == 09 || $("#apgtipo_docto").val() ==  10 || $("#apgtipo_docto").val() ==  11) {

                tipoOper = 2;

                if($("#apglinha_digitavel_conc1").val() == ""){         camposDestaque.push({campo:$("#apglinha_digitavel_conc1").attr("id")}); }
                if($("#apglinha_digitavel_conc1").val().length != 12 ){ camposDestaque.push({campo:$("#apglinha_digitavel_conc1").attr("id")}); }
                if($("#apglinha_digitavel_conc2").val() == ""){         camposDestaque.push({campo:$("#apglinha_digitavel_conc2").attr("id")}); }
                if($("#apglinha_digitavel_conc2").val().length != 12 ){ camposDestaque.push({campo:$("#apglinha_digitavel_conc2").attr("id")}); }
                if($("#apglinha_digitavel_conc3").val() == ""){         camposDestaque.push({campo:$("#apglinha_digitavel_conc3").attr("id")}); }
                if($("#apglinha_digitavel_conc3").val().length != 12 ){ camposDestaque.push({campo:$("#apglinha_digitavel_conc3").attr("id")}); }
                if($("#apglinha_digitavel_conc4").val() == ""){         camposDestaque.push({campo:$("#apglinha_digitavel_conc4").attr("id")}); }
                if($("#apglinha_digitavel_conc4").val().length != 12){  camposDestaque.push({campo:$("#apglinha_digitavel_conc4").attr("id")}); }

                if( $("#apglinha_digitavel_conc1").val() == "" && $("#apglinha_digitavel_conc2").val() == "" && $("#apglinha_digitavel_conc3").val() == "" && $("#apglinha_digitavel_conc4").val() == ""  ){
                    msg_alerta = "O campo 'Linha Digitável' deve ser informado para gerar o código de barras.";

                }else if($("#apglinha_digitavel_conc1").val().length != 12 || $("#apglinha_digitavel_conc2").val().length != 12 || $("#apglinha_digitavel_conc3").val().length != 12 || $("#apglinha_digitavel_conc4").val().length != 12  ){                
                    msg_alerta = "O campo 'Linha Digitável' é inválido, informe todos os números da linha digitável";                
                }
            }
            
            if(msg_alerta != ""){ 
                jQuery('#mensagem_sucesso').hide();               
                jQuery('#mensagem_alerta').html(msg_alerta);
                jQuery('#mensagem_alerta').show();
                showFormErros(camposDestaque);
            }else{       
                $("#apgcodigo_barras").val();         
                enviar();                
            }
        }
    });


    function enviar(){        
         
        apgoid = jQuery("#apgoid").val();
        elemento = this;
        var camposDestaque = new Array();
        var msg_alerta = "";
        var tipoOper = 0;        
        var retornoDigito = false;
        
        resetFormErros();

        if($("#apgforma_recebimento").val() == 31){            
            
            if( $("#apgtipo_docto").val() ==  05) {

                tipoOper = 1;
                linhaDigitavel =    $("#apglinha_digitavel1").val() + 
                                    $("#apglinha_digitavel2").val() +
                                    $("#apglinha_digitavel3").val() +
                                    $("#apglinha_digitavel4").val() +
                                    $("#apglinha_digitavel5").val() +
                                    $("#apglinha_digitavel6").val() +
                                    $("#apglinha_digitavel7").val() +
                                    $("#apglinha_digitavel8").val() ;

            }else if($("#apgtipo_docto").val() == 09 || $("#apgtipo_docto").val() ==  10 || $("#apgtipo_docto").val() ==  11) {

                tipoOper = 2;
                linhaDigitavel =    $("#apglinha_digitavel_conc1").val() + 
                                    $("#apglinha_digitavel_conc2").val() +
                                    $("#apglinha_digitavel_conc3").val() +
                                    $("#apglinha_digitavel_conc4").val();                
            }                
        }        
        
        if(msg_alerta == ""){

            resetFormErros();                
            jQuery('#mensagem_alerta').hide();

            if(tipoOper == 1){
                jQuery.ajax({
                    url: 'man_custos_apagar.php',
                    type: 'POST',                     
                    data: {
                        acao: 'gerarCodigoDeBarras',
                        tipoOperacao:               tipoOper,     // 1 - GNRE, 2 - Concessionarias
                        apgoid:                     apgoid,
                        apglinha_digitavel1:        $("#apglinha_digitavel1").val(),
                        apglinha_digitavel2:        $("#apglinha_digitavel2").val(),
                        apglinha_digitavel3:        $("#apglinha_digitavel3").val(),
                        apglinha_digitavel4:        $("#apglinha_digitavel4").val(),
                        apglinha_digitavel5:        $("#apglinha_digitavel5").val(),
                        apglinha_digitavel6:        $("#apglinha_digitavel6").val(),
                        apglinha_digitavel7:        $("#apglinha_digitavel7").val(),
                        apglinha_digitavel8:        $("#apglinha_digitavel8").val(),                        
                        apgdt_vencimento:           $("#apgdt_vencimento").val(),
                        apgvl_apagar:               $("#apgvl_apagar").val(),
                        apgvl_pago:                 $("#apgvl_pago").val(),
                    },
                    success: function(data) {
                        var camposDestaque = new Array();
                        var msg_alerta = "";
                        var obj = jQuery.parseJSON( data );

                        // apos inserir a nova string no campo codigo de barras faz a verificação de data de vencimento e valor do titulo                        
                        verificaDataVencimentoValor( linhaDigitavel, obj.stringCodigoBarras, tipoOper);
                        
                    }            
                });
            }else if(tipoOper == 2){
                jQuery.ajax({
                    url: 'man_custos_apagar.php',
                    type: 'POST',                     
                    data: {
                        acao: 'gerarCodigoDeBarras',
                        tipoOperacao:               tipoOper, // 1 - GNRE, 2 - Concessionarias
                        apgoid:                     apgoid,                        
                        apglinha_digitavel_conc1:   $("#apglinha_digitavel_conc1").val(),
                        apglinha_digitavel_conc2:   $("#apglinha_digitavel_conc2").val(),
                        apglinha_digitavel_conc3:   $("#apglinha_digitavel_conc3").val(),
                        apglinha_digitavel_conc4:   $("#apglinha_digitavel_conc4").val(),                        
                        apgdt_vencimento:           $("#apgdt_vencimento").val(),
                        apgvl_apagar:               $("#apgvl_apagar").val(),
                        apgvl_pago:                 $("#apgvl_pago").val(),
                    },
                    success: function(data) {
                        var camposDestaque = new Array();
                        var msg_alerta = "";
                        var obj = jQuery.parseJSON( data );

                        // apos inserir a nova string no campo codigo de barras faz a verificação de data de vencimento e valor do titulo
                        verificaDataVencimentoValor( linhaDigitavel, obj.stringCodigoBarras, tipoOper );
                    }            
                });
            }
        }
        return false;       
    }    

    /**
     * FUNÇÃO PARA VERIFICAR SE A DATA DE VENCIMENTO BATE COM O VALOR DO CODIGO DE BARRAS E
     * SE O VALOR R$ DO TITULO BATE COM O VALOR DO CODIGO DE BARRAS.
     */

    function verificaDataVencimentoValor( linhaDigitavel, stringCodigoBarras, tipoOper ){

        apgoid = jQuery("#apgoid").val();
        elemento = this;        

        jQuery.ajax({
            url: 'man_custos_apagar.php',
            type: 'POST',            
            data: {
                acao:               'verificaCodBarrasToValorDataVencimento',
                apgoid:             apgoid,
                apgcodigo_barras:   $("#apgcodigo_barras").val(),
                apgdt_vencimento:   $("#apgdt_vencimento").val(),
                apgvl_apagar:       $("#apgvl_apagar").val(),
                apgvl_pago:         $("#apgvl_pago").val(),
                linhaDigitavel:     linhaDigitavel,
                tipoOper:           tipoOper,
            },
            success: function(data) {
                                                
                var camposDestaque = new Array();
                var msg_alerta = "";                

                resetFormErros();                
                jQuery('#mensagem_alerta').hide();
                
                var obj = jQuery.parseJSON( data );                
                
                $("#apgcodigo_barras").val(obj.novocodigobarras);

                if(obj.sucesso == "OK") {                    
                    if(botaoclick == 1){
                        $("#form_cadastrar").submit();
                    }                    
                }else if(obj.sucesso == "codigoBarrasVazio"){
                    msg_alerta = "O código de barras não pode ser vazio, insira o codigo de barras ou digite os valores na linha digitável";
                    camposDestaque.push({campo:$("#apgcodigo_barras").attr("id")});

                }else if(obj.sucesso == "digitosVerificadoresErrados"){
                    msg_alerta = "A linha digitável informada é inválida para gerar o código de barras, tente novamente.";
                    if(tipoOper == 1){
                        camposDestaque.push({campo:$("#apglinha_digitavel1").attr("id")});
                        camposDestaque.push({campo:$("#apglinha_digitavel2").attr("id")});
                        camposDestaque.push({campo:$("#apglinha_digitavel3").attr("id")});
                        camposDestaque.push({campo:$("#apglinha_digitavel4").attr("id")});
                        camposDestaque.push({campo:$("#apglinha_digitavel5").attr("id")});
                        camposDestaque.push({campo:$("#apglinha_digitavel6").attr("id")});
                        camposDestaque.push({campo:$("#apglinha_digitavel7").attr("id")});
                        camposDestaque.push({campo:$("#apglinha_digitavel8").attr("id")});
                    }else if(tipoOper == 2 ){
                        camposDestaque.push({campo:$("#apglinha_digitavel_conc1").attr("id")});
                        camposDestaque.push({campo:$("#apglinha_digitavel_conc2").attr("id")});
                        camposDestaque.push({campo:$("#apglinha_digitavel_conc3").attr("id")});
                        camposDestaque.push({campo:$("#apglinha_digitavel_conc4").attr("id")});
                    }

                } else if(obj.sucesso == "valores diferentes") {
                    
                    msg_alerta = "O valor do título <strong>"+ obj.valorCodBarras + "</strong> informado no código de barras está diferente do valor <strong>"+ obj.valor+"</strong> do campo 'Valor Pago / Título'";                  
                    camposDestaque.push({campo:$("#apgvl_pago").attr("id")});                    

                }else if(obj.sucesso == "datas diferentes"){
                    msg_alerta = "A data de vencimento <strong>"+ obj.data_vencimento +"</strong> informada no código de barras está diferente da data <strong>"+ obj.apgdt_vencimento +"</strong> do campo 'Vencimento'.";

                    camposDestaque.push({campo:$("#apgdt_vencimento").attr("id")});                    

                }else if(obj.sucesso == 'nOK'){                    
                    msg_alerta  = "A data de vencimento <strong>"+ obj.data_vencimento +"</strong> informada no código de barras está diferente da data <strong>"+ obj.apgdt_vencimento +"</strong> do campo 'Vencimento'.";
                    msg_alerta += "<BR>O valor do título <strong>"+ obj.valorCodBarras + "</strong> informado no código de barras está diferente do valor <strong>"+ obj.valor+"</strong> do campo 'Valor Pago / Título'";
                    
                    camposDestaque.push({campo:$("#apgdt_vencimento").attr("id")});
                    camposDestaque.push({campo:$("#apgvl_pago").attr("id")});
                }

                if(msg_alerta != ""){
                    jQuery('#mensagem_sucesso').hide();                                    
                    jQuery('#mensagem_alerta').html(msg_alerta);
                    jQuery('#mensagem_alerta').show();
                }
                showFormErros(camposDestaque);
            }            
        });

        return false;  
    }

   
    jQuery("#bt_gravar").click(function() {

        botaoclick = 1;
        msg_alerta = "";

        var camposDestaque = new Array();

        if($('#cmp_fornecedor').val() == ""){
            msg_alerta = "Informe um fornecedor válido";
            camposDestaque.push({campo:$("#cmp_fornecedor_autocomplete").attr("id")}); 
            camposDestaque.push({campo:$("#fordocto").attr("id")});
            mostraMensagem(msg_alerta);
            showFormErros(camposDestaque);
            return false;
        }

        if( $("#apgdt_pagamento").val() == ""  ) {
            msg_alerta = "O campo Data de pagamento deve ser preenchido corretamente!";
            camposDestaque.push({campo:$("#apgdt_pagamento").attr("id")});
            mostraMensagem(msg_alerta);
            showFormErros(camposDestaque);
            return false;
        }

        if(calculaValor_liquido_total() == false){ // realiza o calculo dos valores Pago / Titulo e valor total do título.            
            msg_alerta = "Existem valores negativos no cálculo do campo 'Valor Pago / Titulo' ou no campo 'Valor Total' ";
            camposDestaque.push({campo:$("#apgvl_pago").attr("id")});
            camposDestaque.push({campo:$("#apgvl_total").attr("id")});
            mostraMensagem(msg_alerta);
            showFormErros(camposDestaque);
            return false;                    
        }

        /**
         * TESTE PARA VER SE O CAMPO DESCONTO ESTA BLOQUEADO E SE O VALOR É MAIOR QUE ZERO, SE SIM, NÃO DEIXA ENVIAR O FORMULARIO
         */ 
        if(document.getElementById('apgvl_desconto').disabled == true){
            if(moeda2float(document.getElementById('apgvl_desconto').value) > 0){
                //msg_alerta = "Títulos com as formas de pagamento Darf Normal, Darf Simples,  GARE/SP - ICMS, GPS, Guia Recolhimento, Crédito C/C, Crédito em Conta Salário, Cheque, Dinheiro não podem ter o campo Desconto preenchido.<BR>";
                msg_alerta = "Títulos com as formas de pagamento Darf Normal, Darf Simples, GARE–SP ICMS, GPS, FGTS, Guia Recolhimento, Crédito C/C, Crédito em Conta Salário, Cheque e Dinheiro não podem ter o campo Desconto preenchido.<BR>";
                camposDestaque.push({campo:$("#apgvl_desconto").attr("id")});
                mostraMensagem(msg_alerta);
                showFormErros(camposDestaque);
                return false;                    
            }           
        } 

        if($("#apgforma_recebimento").val() == 31 ){

            /*if($("#apgtipo_docto").val() == ""){
                msg_alerta = "Selecione uma opção no campo Tipo de Pagamento<BR>";
                camposDestaque.push({campo:$("#apgtipo_docto").attr("id")});            
            }*/ 
            
            if($("#apgcodigo_barras").val() == "" && ( $("#apgtipo_docto").val() == "09" || $("#apgtipo_docto").val() == "10" || $("#apgtipo_docto").val() == "11" ) ){

                msg_alerta += "O valor do campo Código de Barras não pode ser vazio<BR>"; 
                if(msg_alerta != ""){                
                    camposDestaque.push({campo:$("#apgcodigo_barras").attr("id")});
                }
            }else if($("#apgcodigo_barras").val().length < 44 && ( $("#apgtipo_docto").val() == "09" || $("#apgtipo_docto").val() == "10" || $("#apgtipo_docto").val() == "11" ) ){
                msg_alerta = "O valor do campo Código de Barras está incompleto"; 
                if(msg_alerta != ""){                
                    camposDestaque.push({campo:$("#apgcodigo_barras").attr("id")});
                }

            }else if($("#apgtipo_docto").val() == "05" && $("#apgforma_recebimento").val() == "31" ){

                if($("#apgcodigo_barras").val() == ""){
                    msg_alerta += "O valor do campo Código de Barras não pode ser vazio"; 
                    if(msg_alerta != ""){                
                        camposDestaque.push({campo:$("#apgcodigo_barras").attr("id")});
                    } 
                }else if($("#apgcodigo_barras").val().length < 44){
                    msg_alerta = "O valor do campo Código de Barras está incompleto"; 
                    if(msg_alerta != ""){                
                        camposDestaque.push({campo:$("#apgcodigo_barras").attr("id")});
                    }
                }
                if(msg_alerta == ""){
                    enviar();
                }

            }else if($("#apgtipo_docto").val() == "06" || $("#apgtipo_docto").val() == "07" || $("#apgtipo_docto").val() == "08" || $("#apgtipo_docto").val() == "12" || $("#apgtipo_docto").val() == "13" ) { 

                if( ($("#apgidentificador_gps").val() == "" && $("#apgtipo_docto").val() == "07") || ($("#apgidentificador_gps_nome").val() == "" && $("#apgtipo_docto").val() == "07") ) {
                    msg_alerta = "O campo Identificador deve ser preenchido corretamente!";
                    camposDestaque.push({campo:$("#apgidentificador_gps").attr("id")});
                    camposDestaque.push({campo:$("#apgidentificador_gps_nome").attr("id")});

                    mostraMensagem(msg_alerta);
                    showFormErros(camposDestaque);
                    return false;

                }else if( $("#apginscricao_estadual").val() == "" && $("#apgtipo_docto").val() == "13") {
                    msg_alerta = "O campo Inscrição Estadual deve ser preenchido corretamente!";
                    camposDestaque.push({campo:$("#apginscricao_estadual").attr("id")});

                    mostraMensagem(msg_alerta);
                    showFormErros(camposDestaque);

                    return false;

                }else if( $("#apgcnpj_contribuinte").val() == "" && $("#apgtipo_docto").val() == "13") {
                    msg_alerta = "O campo CNPJ do Contribuinte deve ser preenchido corretamente!";
                    camposDestaque.push({campo:$("#apgcnpj_contribuinte").attr("id")});
                    
                    mostraMensagem(msg_alerta);
                    showFormErros(camposDestaque);
                    return false;

                }else{
                    $("#form_cadastrar").submit();
                }

            }else if($("#apgtipo_docto").val() == "09" || $("#apgtipo_docto").val() == "10" || $("#apgtipo_docto").val() == "11") {
                enviar();

            }else if($("#apgtipo_docto").val() == "05" && $("#apgforma_recebimento").val() != "31" ) {
                // outros geral sem ser boleto
                $("#form_cadastrar").submit();

            }
        }else if($("#apgforma_recebimento").val() == 0 ){
            
            if($("#apgno_cheque").val() == "" && $("#apgforma_recebimento").val() != ""){
                msg_alerta = "Preenha o campo No. Cheque<BR>";
                camposDestaque.push({campo:$("#apgno_cheque").attr("id")});
            
            }
            /*if($("#apgtipo_docto").val() == ""){
                msg_alerta = "Selecione uma opção no campo Tipo de Pagamento<BR>";
                camposDestaque.push({campo:$("#apgtipo_docto").attr("id")});
            
            }*/
            if(msg_alerta == ""){
                $("#form_cadastrar").submit();            
            }

        }else if($("#apgforma_recebimento").val() == "" ){    
            msg_alerta += "Selecione uma opção no campo Forma de Pagamento<BR>"; 
            if(msg_alerta != ""){                
                camposDestaque.push({campo:$("#apgforma_recebimento").attr("id")});
            }             

        }else if($("#apgforma_recebimento").val() == "1" || $("#apgforma_recebimento").val() == "2" || $("#apgforma_recebimento").val() == "4"){
            if($("#apgtipo_docto").val() == ""){
                msg_alerta = "Selecione uma opção no campo Tipo de Pagamento";
                camposDestaque.push({campo:$("#apgtipo_docto").attr("id")});
            
            }else{
                $("#form_cadastrar").submit();
            }
        }
        
        if(msg_alerta != ""){
            jQuery('#mensagem_sucesso').hide();
            jQuery('#mensagem_alerta').html(msg_alerta);
            jQuery('#mensagem_alerta').show();        
        }
        showFormErros(camposDestaque);
    });

    //botão voltar
    jQuery("#bt_voltar").click(function(){        
        window.location.href = "custos_apagar.php";
    })

    /*
     * Tratamento somente numeros inteiros, letras e underscore
     */
    jQuery('body').on('keyup blur', '.codigo', function() {
        jQuery(this).val(jQuery(this).val().replace(/[^A-Za-z0-9]/g, ''));
    });
  
});

$(document).ready(function(){
  $(".somenteNumero").bind("keyup blur focus", function(e) {
        e.preventDefault();
        var expre = /[^\d]/g;
        $(this).val($(this).val().replace(expre,''));
   });
});


$(document).ready(function(){

    /*allowZero: true, allowNegative: true, allowEmpty: true, defaultZero: false, affixesStay: false, precision: 2, thousands: '', decimal: '.'*/

    $("#apgvl_pago").maskMoney({defaultZero: false, allowEmpty: true, allowZero: true, allowNegative: false, showSymbol:false, symbol:"R$ ", decimal:",", thousands:"."});
    $("#apgvl_apagar").maskMoney({defaultZero: false, allowEmpty: true, allowZero: true, allowNegative: false, showSymbol:false, symbol:"R$ ", decimal:",", thousands:"."});
    $("#apgvl_desconto").maskMoney({defaultZero: false, allowEmpty: true, allowZero: true, allowNegative: false, showSymbol:false, symbol:"R$ ", decimal:",", thousands:"."});    
    $("#apgvl_juros").maskMoney({defaultZero: false, allowEmpty: true, allowZero: true, showSymbol:false, symbol:"R$ ", decimal:",", thousands:"."});
    $("#apgvl_multa").maskMoney({defaultZero: false, allowEmpty: true, allowZero: true, showSymbol:false, symbol:"R$ ", decimal:",", thousands:"."});
    $("#apgvl_tarifa_bancaria").maskMoney({defaultZero: false, allowEmpty: true, allowZero: true, showSymbol:false, symbol:"R$ ", decimal:",", thousands:"."});
    $("#apgvl_ir").maskMoney({defaultZero: false, allowEmpty: true, allowZero: true, showSymbol:false, symbol:"R$ ", decimal:",", thousands:"."});
    //$("#apgcod_ir").maskMoney({showSymbol:false, symbol:"R$ ", allowEmpty: true, allowZero: true});
    $("#apgvl_pis").maskMoney({defaultZero: false, allowEmpty: true, allowZero: true, showSymbol:false, symbol:"R$ ", decimal:",", thousands:"."});
    $("#apgvl_cofins").maskMoney({defaultZero: false, allowEmpty: true, allowZero: true, showSymbol:false, symbol:"R$ ", decimal:",", thousands:"."});
    $("#apgvl_csll").maskMoney({defaultZero: false, allowEmpty: true, allowZero: true, showSymbol:false, symbol:"R$ ", decimal:",", thousands:"."});
    $("#apgvl_inss").maskMoney({defaultZero: false, allowEmpty: true, allowZero: true, showSymbol:false, symbol:"R$ ", decimal:",", thousands:"."});
    $("#apgvl_iss").maskMoney({defaultZero: false, allowEmpty: true, allowZero: true, showSymbol:false, symbol:"R$ ", decimal:",", thousands:"."});
    $("#apgcsrf").maskMoney({defaultZero: false, allowEmpty: true, allowZero: true, showSymbol:false, symbol:"R$ ", decimal:",", thousands:"."});

    $("#apgvalor_receita_bruta").maskMoney({defaultZero: false, allowEmpty: true, allowZero: true, showSymbol:false, symbol:"R$ ", decimal:",", thousands:"."});
    $("#apgpercentual_receita_bruta").maskMoney({defaultZero: false, allowEmpty: true, allowZero: true, showSymbol:false, symbol:"R$ ", decimal:",", thousands:"."});
    $("#apgvalor_entidades").maskMoney({defaultZero: false, allowEmpty: true, allowZero: true, showSymbol:false, symbol:"R$ ", decimal:",", thousands:"."});

    /** Carrega form sem campos */
    $(".campoCheque").hide();
    $(".campoboleto").hide();
    $(".campocodigodebarras").hide();
    $(".campoboletoconcessionaria").hide();
    
    removeCamposTipo();
    
    /**
     * Logica para ocultar e exibir campos conforme a combo de Forma de Pagamento.
     */
    $("#apgforma_recebimento").on('change', function(){        

        $(".campoboleto").hide();
        $(".campocodigodebarras").hide();
        
        ocultaTodosCamposPorTipo();
        
        //$("#apgno_cheque").val("");
        $(".campoCheque").hide();

        removeCamposTipo();        

        // remove o valor selecionado na combo abaixo
        $('#apgtipo_docto').children('option').removeAttr("selected");    
        
        // retorna o estado inicial
        if(this.value == ""){            
            $(".campoCheque").hide();
            $(".campoboleto").hide();
            $(".campocodigodebarras").hide();   
            limpaCamposConcessionaria();
            limpaCamposBoleto();
            $("#apgno_cheque").val("");                     
        }        

        if(this.value == "31") { // se for igual a boleto
            // tipo de Docto altera os valores da combo; exibe:
            /* - DARF - GPS - Guia Recolhimento - FGTS - GNRE - Concessionária - Outros */
            $("#apgtipo_docto").append( "<option value='11'>Concessionária</option>" );
            $("#apgtipo_docto").append( "<option value='06'>DARF Normal</option>" );
            $("#apgtipo_docto").append( "<option value='12'>DARF Simples</option>" );            
            $("#apgtipo_docto").append( "<option value='09'>FGTS</option>" );
            $("#apgtipo_docto").append( "<option value='13'>GARE – SP ICMS</option>" );            
            $("#apgtipo_docto").append( "<option value='10'>GNRE</option>" );            
            $("#apgtipo_docto").append( "<option value='07'>GPS</option>" );
            $("#apgtipo_docto").append( "<option value='08'>Guia Recolhimento</option>" );
            $("#apgtipo_docto").append( "<option value='05'>Outros</option>" );
            
            $("#apgtipo_docto").val($("#apgtipo_docto1_hidden").val()).change();
            $("#apgno_cheque").val("");

        }else if(this.value == "1" || this.value == "2" || (this.value == "0" && this.value != "") || this.value == "4" ){            

            if(this.value == "0" && this.value != ""){
                $(".campoCheque").show();                
            }else{
                $("#apgno_cheque").val("");
            }
            $("#apgtipo_docto").append( "<option value='04'>Duplicata</option>" );
            $("#apgtipo_docto").append( "<option value='02'>Fatura</option>" );
            $("#apgtipo_docto").append( "<option value='03'>Nota Fiscal</option>" );
            $("#apgtipo_docto").append( "<option value='01'>Nota Fiscal/Fatura</option>" );
            $("#apgtipo_docto").append( "<option value='05'>Outros</option>" );

            $("#apgtipo_docto").val($("#apgtipo_docto1_hidden").val()).change();
        }else{
            
            removeCamposTipo();

            $("#apgno_cheque").val("");
            $("campoCheque").hide();
            $("#apgtipo_docto").val("");            
        }
        
        $("#apgtipo_docto").on('change', function(){            

            ocultaTodosCamposPorTipo();
            calculaValor_liquido_total();

            if( (this.value == 05 && $("#apgforma_recebimento").val() == 31) ){
                $(".campocodigodebarras").show();
                $(".campoboleto").show();
                $(".campoboletoconcessionaria").hide();

                //limpaCamposBoleto();
                limpaCamposConcessionaria();                
                limpaCamposValoresOcultosTipoPagamento();

            }else if(this.value == 09 || this.value == 11 || this.value == 10 ){
                $(".campocodigodebarras").show();
                $(".campoboleto").hide();
                $(".campoboletoconcessionaria").show();
                
                limpaCamposBoleto();                

                if(this.value == 09){
                    $(".oculta_apgcodigo_receita").show();
                    $(".oculta_apgidentificador_fgts").show();                    
                }                
                $("#apgperiodo_referencia1").val("");
                $("#apgperiodo_referencia2").val("");
                $("#apgnumero_referencia").val("");
                $("#apgvalor_receita_bruta").val("");
                $("#apgpercentual_receita_bruta").val("");                
                $("#apgdivida_ativa").val("");
                $("#apgnum_parcela").val("");
                $("#apgvalor_entidades").val("");
                $("#apgno_cheque").val("");
                $("#apgidentificador_gps").val("");
                $("#apgcnpj_contribuinte").val("");
                $("#apginscricao_estadual").val("");

            }else if(this.value == 12 || this.value == 13 || this.value == 06 || this.value == 07){
                
                $(".campocodigodebarras").hide();
                $(".campoboleto").hide();
                $(".campoboletoconcessionaria").hide();

                limpaCamposConcessionaria();
                limpaCamposBoleto();
                limpaCampoCodigoBarras();
                
                if(this.value == 06) { // DARF Normal
                    $(".oculta_apgcodigo_receita").show();
                    $(".oculta_apgperiodo_referencia2").show();
                    $(".oculta_apgperiodo_referencia1").hide();
                    $(".oculta_apgnumero_referencia").show();
                    $(".oculta_apgidentificador_gps").hide();
                    $(".oculta_apgcnpj_contribuinte").hide();
                    $(".oculta_apginscricao_estadual").hide();

                    // limpa valores dos campos ocultos                    
                    $("#apgvalor_receita_bruta").val("");
                    $("#apgperiodo_referencia1").val("");
                    $("#apgpercentual_receita_bruta").val("");
                    $("#apgidentificador_fgts").val("");
                    $("#apgdivida_ativa").val("");
                    $("#apgnum_parcela").val("");
                    $("#apgvalor_entidades").val("");
                    $("#apgno_cheque").val("");
                    $("#apgidentificador_gps").val("");
                    $("#apgcnpj_contribuinte").val("");
                    $("#apginscricao_estadual").val("");

                }else if(this.value == 12){ // DARF Simples
                    $(".oculta_apgcodigo_receita").show();
                    $(".oculta_apgperiodo_referencia2").show();
                    $(".oculta_apgperiodo_referencia1").hide();
                    $(".oculta_apgvalor_receita_bruta").show();
                    $(".oculta_apgpercentual_receita_bruta").show();
                    $(".oculta_apgidentificador_gps").hide();
                    $(".oculta_apgcnpj_contribuinte").hide();
                    $(".oculta_apginscricao_estadual").hide();

                    // limpa valores dos campos ocultos
                    $("#apgperiodo_referencia1").val("");                    
                    $("#apgnumero_referencia").val("");                    
                    $("#apgidentificador_fgts").val("");
                    $("#apgdivida_ativa").val("");
                    $("#apgnum_parcela").val("");
                    $("#apgvalor_entidades").val("");
                    $("#apgno_cheque").val("");
                    $("#apgidentificador_gps").val("");
                    $("#apgcnpj_contribuinte").val("");
                    $("#apginscricao_estadual").val("");

                }else if(this.value == 13){ // GARE
                    $(".oculta_apgcodigo_receita").show();                    
                    $(".oculta_apgperiodo_referencia2").hide();                    
                    $(".oculta_apgperiodo_referencia1").show();
                    $(".oculta_apgdivida_ativa").show();
                    $(".oculta_apgnum_parcela").show();
                    $(".oculta_apgidentificador_gps").hide();
                    $(".oculta_apgcnpj_contribuinte").show();
                    $(".oculta_apginscricao_estadual").show();
                    
                    // limpa valores dos campos ocultos
                    $("#apgperiodo_referencia2").val("");
                    $("#apgnumero_referencia").val("");
                    $("#apgvalor_receita_bruta").val("");
                    $("#apgpercentual_receita_bruta").val("");
                    $("#apgidentificador_fgts").val("");                    
                    $("#apgvalor_entidades").val("");
                    $("#apgno_cheque").val("");
                    $("#apgidentificador_gps").val("");

                }else if(this.value == 07){ // GPS
                    $(".oculta_apgcodigo_receita").show();                    
                    $(".oculta_apgperiodo_referencia2").hide();
                    $(".oculta_apgperiodo_referencia1").show();
                    $(".oculta_apgvalor_entidades").show();
                    $(".oculta_apgidentificador_gps").show();
                    $(".oculta_apgcnpj_contribuinte").hide();
                    $(".oculta_apginscricao_estadual").hide();
                    
                    // limpa valores dos campos ocultos
                    $("#apgperiodo_referencia2").val("");
                    $("#apgnumero_referencia").val("");
                    $("#apgvalor_receita_bruta").val("");
                    $("#apgpercentual_receita_bruta").val("");
                    $("#apgidentificador_fgts").val("");
                    $("#apgdivida_ativa").val("");
                    $("#apgnum_parcela").val("");
                    $("#apgno_cheque").val("");
                    $("#apgcnpj_contribuinte").val("");
                    $("#apginscricao_estadual").val("");
                }               

            }else{
                $(".campoboletoconcessionaria").hide();
                $(".campocodigodebarras").hide();
                $(".campoboleto").hide();                

                limpaCamposConcessionaria();
                limpaCamposBoleto();
                limpaCamposValoresOcultosTipoPagamento();
                limpaCampoCodigoBarras();                

            }
        });

    });

    /**
     * carrega combo quando entrar na pagina
     */ 
    $(function () {

        //alert($("#apgforma_recebimento").val());

        if($("#apgforma_recebimento").val() == 0 && $("#apgforma_recebimento").val() != ""){
            $(".campoCheque").show();                
        }
        
        $("#apgforma_recebimento").change();
        $("#apgtipo_docto").change();

        if($("#apgtipo_docto1_hidden").val() == "05" && $("#apgforma_recebimento").val() == "31" ){
            $(".campocodigodebarras").show();
            $(".campoboleto").show();
            $(".campoboletoconcessionaria").hide();
        
        }else if($("#apgtipo_docto1_hidden").val() == "09" || $("#apgtipo_docto1_hidden").val() == "11" || $("#apgtipo_docto1_hidden").val() == "10" ){
            $(".campocodigodebarras").show();
            $(".campoboleto").hide();
            $(".campoboletoconcessionaria").show();

            if($("#apgtipo_docto1_hidden").val() == "09"){
                $(".oculta_apgcodigo_receita").show();
                $(".oculta_apgidentificador_fgts").show();                    
            }     

        }else if($("#apgtipo_docto1_hidden").val() == "12" || $("#apgtipo_docto1_hidden").val() == "13" || $("#apgtipo_docto1_hidden").val() == "06" || $("#apgtipo_docto1_hidden").val() == "07"){                

            $(".campocodigodebarras").hide();
            $(".campoboleto").hide();
            $(".campoboletoconcessionaria").hide();
               
            if($("#apgtipo_docto1_hidden").val() == "06") { // DARF Normal
                $(".oculta_apgcodigo_receita").show();                
                $(".oculta_apgperiodo_referencia2").show();
                $(".oculta_apgperiodo_referencia1").hide();
                $(".oculta_apgnumero_referencia").show();
                $(".oculta_apgidentificador_gps").hide();
                $(".oculta_apgcnpj_contribuinte").hide();
                $(".oculta_apginscricao_estadual").hide();
                
                $("#apgperiodo_referencia1").val("");
                $("#apgidentificador_gps").val("");

            }else if($("#apgtipo_docto1_hidden").val() == "12"){ // DARF Simples
                $(".oculta_apgcodigo_receita").show();
                $(".oculta_apgperiodo_referencia2").show();
                $(".oculta_apgperiodo_referencia1").hide();
                $(".oculta_apgvalor_receita_bruta").show();
                $(".oculta_apgpercentual_receita_bruta").show();
                $(".oculta_apgidentificador_gps").hide();
                $(".oculta_apgcnpj_contribuinte").hide();
                $(".oculta_apginscricao_estadual").hide();

                $("#apgperiodo_referencia1").val("");
                $("#apgidentificador_gps").val("");

            }else if($("#apgtipo_docto1_hidden").val() == "13"){ // GARE
                $(".oculta_apgcodigo_receita").show();
                $(".oculta_apgperiodo_referencia2").hide();
                $(".oculta_apgperiodo_referencia1").show();
                $(".oculta_apgdivida_ativa").show();
                $(".oculta_apgnum_parcela").show();
                $(".oculta_apgidentificador_gps").hide();
                $(".oculta_apgcnpj_contribuinte").show();
                $(".oculta_apginscricao_estadual").show();

                $("#apgperiodo_referencia2").val("");
                $("#apgidentificador_gps").val("");
                
            }else if($("#apgtipo_docto1_hidden").val() == "07"){ // GPS                
                $(".oculta_apgcodigo_receita").show();
                $(".oculta_apgperiodo_referencia2").hide();
                $(".oculta_apgperiodo_referencia1").show();
                $(".oculta_apgvalor_entidades").show();
                $(".oculta_apgidentificador_gps").show();
                $(".oculta_apgcnpj_contribuinte").hide();
                $(".oculta_apginscricao_estadual").hide();

                $("#apgperiodo_referencia2").val("");
            }

        }else{
            $(".campoboletoconcessionaria").hide();
            $(".campocodigodebarras").hide();
            $(".campoboleto").hide();
        }


    });

    function limpaCamposConcessionaria(){
        $("#apglinha_digitavel_conc1").val("");
        $("#apglinha_digitavel_conc2").val("");
        $("#apglinha_digitavel_conc3").val("");
        $("#apglinha_digitavel_conc4").val("");
        //$("#apgcodigo_barras").val("");
    }

    function limpaCamposBoleto(){
        $("#apglinha_digitavel1").val("");
        $("#apglinha_digitavel2").val("");
        $("#apglinha_digitavel3").val("");
        $("#apglinha_digitavel4").val("");
        $("#apglinha_digitavel5").val("");
        $("#apglinha_digitavel6").val("");
        $("#apglinha_digitavel7").val("");
        $("#apglinha_digitavel8").val("");
        //$("#apgcodigo_barras").val("");        
    }

    function limpaCamposValoresOcultosTipoPagamento(){
        // limpa valores dos campos ocultos
        $("#apgcodigo_receita").val("");
        $("#apgperiodo_referencia1").val("");
        $("#apgperiodo_referencia2").val("");        
        $("#apgnumero_referencia").val("");
        $("#apgvalor_receita_bruta").val("");
        $("#apgpercentual_receita_bruta").val("");
        $("#apgidentificador_fgts").val("");
        $("#apgdivida_ativa").val("");
        $("#apgnum_parcela").val("");
        $("#apgvalor_entidades").val("");
        $("#apgidentificador_gps").val("");
        $("#apgcnpj_contribuinte").val("");
        $("#apginscricao_estadual").val("");
    }

    function ocultaTodosCamposPorTipo(){
        /* oculta todos os campo novos */
        $(".oculta_apgcodigo_receita").hide();        
        $(".oculta_apgperiodo_referencia2").hide();
        $(".oculta_apgperiodo_referencia1").hide();
        $(".oculta_apgnumero_referencia").hide();        
        $(".oculta_apgvalor_receita_bruta").hide();
        $(".oculta_apgpercentual_receita_bruta").hide();
        $(".oculta_apgidentificador_fgts").hide();
        $(".oculta_apgdivida_ativa").hide();
        $(".oculta_apgnum_parcela").hide();
        $(".oculta_apgvalor_entidades").hide();
        $(".oculta_apgidentificador_gps").hide();
        $(".oculta_apgcnpj_contribuinte").hide();
        $(".oculta_apginscricao_estadual").hide();        
        /* fim campos novos */
    }

    function removeCamposTipo(){
        $("#apgtipo_docto option[value='01']").remove();
        $("#apgtipo_docto option[value='02']").remove();
        $("#apgtipo_docto option[value='03']").remove();
        $("#apgtipo_docto option[value='04']").remove();
        $("#apgtipo_docto option[value='05']").remove();
        $("#apgtipo_docto option[value='06']").remove();
        $("#apgtipo_docto option[value='07']").remove();
        $("#apgtipo_docto option[value='08']").remove();
        $("#apgtipo_docto option[value='09']").remove();
        $("#apgtipo_docto option[value='10']").remove();
        $("#apgtipo_docto option[value='11']").remove();
        $("#apgtipo_docto option[value='12']").remove();
        $("#apgtipo_docto option[value='13']").remove();
    }

    function limpaCampoCodigoBarras(){
        $("#apgcodigo_barras").val("");
    }    

});

/**
 * RETORNA FORNECEDOR DO BANCO DE DADOS CONFORME NUMERO CNPJ/CPF INSERIDO RETORNA PARA O CAMPO apgidentificador_gps_nome
 * //01.395.176/0001-52
 */
$(document).ready(function () {    
   
    $('input[name=apgidentificador_gps]').live('blur', function (e) {     
        jQuery.ajax({
            url: 'man_custos_apagar.php',
            type: 'POST',                     
            data: {
                acao:               'buscaFornecedorGPS',                                
                identificadorGPS:   $("#apgidentificador_gps").val().replace(/[^a-zA-Z 0-9]+/g,'') // envia já sem ponto e barras (somente numeros)
            },
            success: function(data) {                
                var obj = jQuery.parseJSON(data);                
                $("#apgidentificador_gps_nome").val(obj.label);
                $("#cmp_fornecedor").val(obj.id);
                $("#fordocto").val(obj.fordocto);
            }
        });      

    });

    $('input[name=fordocto]').live('blur', function (e) {        
        jQuery.ajax({
            url: 'man_custos_apagar.php',
            type: 'POST',                     
            data: {
                acao:               'buscaFornecedorGPS',                                
                identificadorGPS:   $("#fordocto").val().replace(/[^a-zA-Z 0-9]+/g,'') // envia já sem ponto e barras (somente numeros)
            },
            //01.395.176/0001-52
            success: function(data) {
                var obj = jQuery.parseJSON(data);                
                $("#cmp_fornecedor_autocomplete").val(obj.label);
                $("#cmp_fornecedor").val(obj.id);
                $("#fordocto").val(obj.fordocto);
            }
        });      

    });
});
