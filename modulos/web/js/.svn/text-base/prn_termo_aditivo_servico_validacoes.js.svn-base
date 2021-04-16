/**
 * Valida��es e Observadores do m�dulo Termo Aditivo de Servi�o.
 * @author Bruno Bonfim Affonso - <bruno.bonfim@sascar.com.br>
 * @package Principal
 * @version 1.0
 * @since 04/04/2013
 */

/*
 * Imagem carregando.
 */
var carregando = new Object();
    carregando.abrir  = function(){jQuery('#resultado_progress').attr('style','display: block;');}
    carregando.fechar = function(){jQuery('#resultado_progress').attr('style','display: none;');}

function limparMensagem(){
    jQuery('div.mensagem').attr("style","display: none;");
}

/**
 * Dispara o evento para aplicar a mascara de moeda.
 */
function formatarMoeda(){    
    jQuery('#valor_tabela').trigger('keyup');
    jQuery('#valor_negociado').trigger('keyup');
    jQuery('#desconto').trigger('keyup');
}

/**
 * Remove o formato da mascara de moeda.
 * @param String valor
 * @return float
 */
function removerMascaraMoeda(valor){
    valor = valor.replace(",",";");
    valor = valor.replace(".","");
    valor = valor.replace(";",".");
    
    return parseFloat(valor);
}

/**
 * Mensagens de feedback.
 * Tipo da mensagem: Sucesso (s), Alerta (w), Erro (e) e Informativo (i)
 * @param String tipo_mensagem
 * @param String mensagem
 */
function setMensagem(tipo_mensagem, mensagem){
    tipo_mensagem = jQuery.trim(tipo_mensagem);
    mensagem      = jQuery.trim(mensagem);
    
    if(tipo_mensagem == ""){
        setMensagem('e', 'Informe o tipo da mensagem!');
        return false;
    } else if(mensagem == ""){
        setMensagem('e', 'O par�metro da mensagem est� vazio!');
        return false;
    }
    
    switch(tipo_mensagem){
        case 's':
            jQuery('#msgsucesso').empty().html(mensagem);
            jQuery('#msgsucesso').attr("style","display: block;");
        break;
        case 'w':
            jQuery('#msgalerta').empty().html(mensagem);
            jQuery('#msgalerta').attr("style","display: block;");
        break;
        case 'e':
            jQuery('#msgerro').empty().html(mensagem);
            jQuery('#msgerro').attr("style","display: block;");
        break;
        case 'i':
            jQuery('#msginfo').empty().html(mensagem);
            jQuery('#msginfo').attr("style","display: block;");
        break;
    }
}
    
/**
 * Fun��o para validar os campos dos formul�rios, etc.
 * @param Object obj - Contem os dados 
 * @param String request - Nome da fun��o que chamou a valida��o dos dados.
 */
function validarDados(obj, request){
    limparMensagem();

    switch(request){
        case 'pesquisarTermoAditivoServico':
            if(obj.cliente == "" && obj.placa == "" && obj.numero_aditivo == "" && obj.status == "" && obj.servico == ""){
                if(obj.data_inicio == ""){
                    setMensagem('w', 'Informe o Per&iacute;odo!');
                    return false;
                } else if(obj.data_fim == ""){
                    setMensagem('w', 'Informe o Per&iacute;odo!');
                    return false;
                } else{
                    return true;
                }
            } else{
                return true;
            }
        break;
        case 'carregarSelectContrato':
            if(obj.servico == ""){
                return false;
            } else if(obj.cliente == ""){
                setMensagem('w', 'Informe o Cliente!');
                return false;
            } else if(obj.cpf_cnpj == ""){
                setMensagem('w', 'Informe o CPF/CNPJ do Cliente!');
                return false;
            } else{
                return true;
            }
        break;
        case 'setValores':
            if(obj.situacao != "" && obj.servico == ""){
                return false;                
            } else if(obj.servico != "" && obj.situacao == ""){
                setMensagem('w', 'Informe o tipo da Situa��o!');
                return false;
            } else{
                return true;
            }
        break;
        case 'confirmarTermoAditivoServico':
            if(obj.cliente == ""){
                setMensagem('w', 'Informe o Cliente!');
                return false;
            } else if(obj.cpf_cnpj == ""){
                setMensagem('w', 'Informe o CPF/CNPJ do Cliente!');
                return false;
            } else if(obj.situacao == ""){
                setMensagem('w', 'Informe o tipo da Situa��o!');
                return false;
            } else if((obj.validade != undefined && obj.validade != "")
                        && (obj.situacao != undefined && obj.situacao == "D")) {

                var dataValidade = new Date(obj.validade.substring(6, 10),
                                            obj.validade.substring(3, 5) - 1,
                                            obj.validade.substring(0, 2));

                if(dataValidade <= new Date()) {
                    setMensagem('w', 'A data de validade deve ser posterior � data de hoje!');
                    return false;
                } else {
                    return true;
                }
            } else if(obj.pacote == "") {
                setMensagem('w', 'Informe o Pacote!');
                return false;
            } else {
                return true;
            }
        break;
        case 'pesquisarCliente':
            if(obj.cliente == ""){
                setMensagem('w', 'Informe o Cliente!');
                return false;
            } else if((obj.cliente).length < 3){
                setMensagem('w', 'O nome do Cliente deve possuir no m�nimo 3 caracteres!');
                return false;
            } else{
                return true;
            }
        break;
        case 'adicionarItemTermoAditivoServico':
            if(obj.id_termo == ""){
                setMensagem('w', 'N� do Termo Aditivo est� vazio!');
                return false;
            } else if(obj.servico == "" && obj.tipo != 'P'){
                setMensagem('w', 'Informe o tipo do Servi�o!');
                return false;
            } else if(obj.pacote == "" && obj.tipo == 'P') {
                setMensagem('w', 'Informe o tipo do Pacote!')
            } else if(obj.valor_tabela == ""){
                setMensagem('w', 'Informe o Valor de Tabela!');
                return false;
            } else if(obj.valor_negociado == ""){
                setMensagem('w', 'Informe o Valor Negociado!');
                return false;
            } else if((obj.servico != 1574 && obj.servico != 1575) && obj.contrato == "") {
                setMensagem('w', 'Informe o Contrato!');
                return false;
            } else if((obj.servico != 1574 && obj.servico != 1575) && obj.numerosContrato.indexOf("," + obj.contrato + ",") == -1) {
                setMensagem('w', 'O item n�o pode ser vinculado � este contrato!');
                return false;
            }else if(obj.desconto == ""){
                setMensagem('w', 'Informe o Desconto!');
                return false;
            } else if(obj.valor_negociado != undefined && obj.valorMinimo != undefined && obj.valorMaximo != undefined
                && obj.valorMinimo != '' && obj.valorMaximo != '' && obj.valorMinimo != ' ' && obj.valorMaximo != ' '){

                var valor_negociado = removerMascaraMoeda(obj.valor_negociado);
                var valorMinimo    = obj.valorMinimo;
                var valorMaximo    = obj.valorMaximo;
                if((valor_negociado < valorMinimo ||
                    valor_negociado > valorMaximo)){
                    if(obj.situacao != "D") {
                        setMensagem("w", "O valor Negociado deve estar entre R$" + valorMinimo.replace('.', ',') + " e R$" + valorMaximo.replace('.', ',') + ".");
                        return false;
                    } else {
                        return true;
                    }
                } else{
                    return true;
                }
            } else{
                return true;
            }
        break;
    }
}

/**
 * Eventos da tela de Pesquisa
 */
function onObserverPesquisa(){ 
    //Botao pesquisar
    jQuery('#pesquisar').unbind().click(function(){
        pesquisarTermoAditivoServico();
    });
    //Botao Novo - Termo Aditivo
    jQuery('#novo').unbind().live("click",function(){
        jQuery('#frame01').empty();
        limparMensagem();
        carregarTelaTermoAditivoServico();
    });
    //Link - Carrega dados para edi��o
    jQuery('a[id*=num_termo_]').unbind().click(function(){
        var id_termo = jQuery(this).attr("id").split("_")[2];
        jQuery('#frame01').empty();
        limparMensagem();
        carregarTelaTermoAditivoServico(id_termo);
    });
}

/**
 * Eventos da tela Inclusao/Edicao de Termo Aditivo
 */
function onObserverTermoAditivo(){
    //Corrigi bot�es
    if(jQuery.browser.msie){
        jQuery('#pesquisar_cliente').removeAttr().attr('style', 'margin: 19px 0 0 0;');
        jQuery('#adicionar_servico').removeAttr().attr('style', 'margin: 23px 0 0 0;'); 
    } else if(jQuery.browser.safari){
        jQuery('#pesquisar_cliente').removeAttr().attr('style', 'margin: 21px 0 0 0;');
        jQuery('#adicionar_servico').removeAttr().attr('style', 'margin: 21px 0 0 0;');            
    } else{
        jQuery('#pesquisar_cliente').removeAttr().attr('style', 'margin: 19px 0 0 0;');
        jQuery('#adicionar_servico').removeAttr().attr('style', 'margin: 19px 0 0 0;');
    }    
    //Ocultando campos - inclusao
    if(jQuery.trim(jQuery('#id_termo').val()) == ""){
        jQuery('#ta_servico').parent().hide();
        jQuery('#td_fieldset').children().hide();
        jQuery('#status').parent().hide();        
    } else{
        jQuery('#ta_servico').parent().show();
        jQuery('#td_fieldset').children().show();
        jQuery('#status').parent().show();        
    }    
    //Pesquisar Cliente
    jQuery('#pesquisar_cliente').unbind().click(function(){
        pesquisarCliente();
    });    
    //Tipo da Situa��o
    jQuery('#situacao').unbind().change(function(){
        setValores();
    });    
    //Ao mudar o tipo do Servi�o, chama a fun��o para carregar o select do Contrato.
    jQuery('#servico').unbind().change(function(){
        carregarSelectContrato();
    });    
    //Mascara moeda
    formatarMoeda();
    
    //Item Aditivo Servi�o
    jQuery('#adicionar_servico').unbind().click(function(){
        adicionarItemTermoAditivoServico();
    });    
    //Confirmar inclusao/alteracao Termo Aditivo
    jQuery('#confirmar').unbind().click(function(){
        confirmarTermoAditivoServico();
    });
    //Link Remover Item Aditivo
    jQuery('a[id*=lnk_remove_item_]').unbind().click(function(){
        var id_item = jQuery(this).attr("id").split("_")[3];
        removerItemAditivo(id_item);
    });
    //Excluir TA
    jQuery('#excluir_termo').unbind().click(function(){
        excluirTermoAditivoServico();
    });

    jQuery('#pacote').unbind().change(function(){
        buscarServicos();
    });

    jQuery('#tipo_serv_pac').change(function(){
        buscarServicos();
    });

    //Valor Negociado
    /*jQuery('#valor_negociado').unbind().change(function(){
        setValores();
    });*/
}

/**
 * Eventos do componente de pesquisa cliente
 */
function onObserverComponentePesquisa(){
    //Ao selecionar o cliente, fecha a combo e preenche campos.
    jQuery('.div_link_result').unbind().click(function(){
        var cliente  = jQuery.trim(jQuery(this).children()[0].value);
        var cpf_cnpj = jQuery.trim(jQuery(this).children()[1].value);
        
        //Limpa os campos
        jQuery('#cliente').val('');
        jQuery('#cpf_cnpj').val('');        
        //Insere os valores
        jQuery('#cliente').val(cliente);
        jQuery('#cpf_cnpj').val(cpf_cnpj);        
        //Fecha combo
        jQuery('#div_content_result_pesquisa').empty().hide();
    });
}

/**
 * Aplica os valores conforme
 * o tipo da Situa��o e Servi�o.
 */
function setValores(){
    var tipo = jQuery('#tipo_serv_pac option:selected').val();

    var obj = new Object();
    obj.situacao = jQuery.trim(jQuery('#situacao').val());
    if(tipo == 'P') {
        obj.servico  = jQuery.trim(jQuery('#pacote').val());
    } else {
        obj.servico  = jQuery.trim(jQuery('#servico').val());
    }
        
    if(!validarDados(obj, 'setValores')){
        return false;
    }

    jQuery.ajaxSetup({async: false});

    jQuery.post("prn_termo_aditivo_servico.php",{
        acao  : 'get_valor_min_obrigacao',
        dados : obj
    },
    function(data) {
        jQuery('#vlrMinimo').val(data);
    });

    jQuery.post("prn_termo_aditivo_servico.php",{
        acao  : 'get_valor_max_obrigacao',
        dados : obj
    },
    function(data) {
        jQuery('#vlrMaximo').val(data);
    });
    
    jQuery.post("prn_termo_aditivo_servico.php",{
        acao  : 'get_valor_obrigacao',
        dados : obj
    },
    function(data){
        jQuery('#valor_tabela').removeAttr("readonly");
        jQuery('#valor_negociado').removeAttr("readonly");
        jQuery('#desconto').removeAttr("readonly");

        var vlrMinimo = jQuery('#vlrMinimo').val();
        var vlrMaximo = jQuery('#vlrMaximo').val();

        if(vlrMinimo == '0.00') {
            vlrMinimo = '';
        }
        if(vlrMaximo == '0.00') {
            vlrMaximo = '';
        }
        
        if(obj.situacao == 'C' || obj.situacao == 'D'){
            jQuery('#valor_tabela').val(data);
            jQuery('#valor_tabela').attr("readonly", "readonly");
            
            if((vlrMinimo == '' && vlrMaximo == '') || obj.situacao == 'D') {
                jQuery('#valor_negociado').val(parseFloat(0));
                jQuery('#valor_negociado').attr("readonly", "readonly");   
            } else {
                jQuery("#valor_negociado").removeAttr("readonly");
            }         

            jQuery('#desconto').val(data);
            jQuery('#desconto').attr("readonly", "readonly");
            
            formatarMoeda();
            
        } else if(obj.situacao == 'M'){
            jQuery('#valor_tabela').val(data);
            if(vlrMinimo != '' && vlrMaximo != '') {
                jQuery('#valor_tabela').attr("readonly", "readonly");            
            } else {
                jQuery("#valor_tabela").removeAttr("readonly");
            }

            formatarMoeda();
            
            var valor_tabela    = removerMascaraMoeda(jQuery('#valor_tabela').val());
            var valor_negociado = removerMascaraMoeda(jQuery('#valor_negociado').val());
            
            //Adicionando atributos ao objeto
            obj.valor_negociado =  valor_negociado;
            obj.valor_tabela    =  valor_tabela;
            
            if(!validarDados(obj, 'setValores')){
                jQuery('#desconto').val(parseFloat(0));
                formatarMoeda();
                return false;
            }
            
            //Desconto
            var desconto = valor_tabela - valor_negociado;
            jQuery('#desconto').val(desconto.toFixed(2));
            formatarMoeda();
        }       
    });
}

/**
 * Reorganiza a classe css
 * nas linhas da tabela e atualiza
 * o total de registros.
 */
function setAtributosTabelaItens(){
    //Atributos
    var classe = "";
    var total_itens = 0;
    
    //Remove class
    jQuery('#tbl_itens_aditivos').children().eq(1).children().removeClass('par');
    
    //Percorrendo as linhas e adiciona a classe
    jQuery('#tbl_itens_aditivos').children().eq(1).children().each(function(i,v){
        classe = (!(i % 2)) ? "par" : "";
        jQuery(this).addClass(classe);
    });
    
    //Total de registros
    total_itens = jQuery('#tbl_itens_aditivos').children().eq(1).children().length;
    
    if(total_itens == 1){
        total_itens = total_itens + ' registro.';
        jQuery('#tbl_itens_aditivos').children().eq(2).children().children().empty().html(total_itens);
    } else if(total_itens > 1){
        total_itens = total_itens + ' registros.';
        jQuery('#tbl_itens_aditivos').children().eq(2).children().children().empty().html(total_itens);
    } else{
        jQuery('#tbl_itens_aditivos').children().eq(2).children().children().empty();
    }
}