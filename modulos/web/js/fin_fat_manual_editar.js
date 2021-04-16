/**
 * Preparando funcionalidades da tela.
 * 
 */
jQuery(document).ready(function() {
    
	jQuery.fn.carregarItens(true);
    jQuery("#pesquisa_contrato").hide();
    jQuery("#dt_ref").datepicker("destroy");

    var count = 0;
    setInterval( function (){
        if(jQuery('input[name=cpx_valor_cliente_nome]').length) {
            if (jQuery('input[name=cpx_valor_cliente_nome]').val() != '') {
                jQuery('#incluir_item').removeAttr('disabled', 'disabled');
                jQuery('#incluir_item').removeClass('desabilitado');

                if (count == 1) {
                    jQuery.ajax({
                        url: 'fin_fat_manual.php',
                        type: 'POST',
                        data: {
                            acao: 'carregarCreditosAconceder',
                            clioid: jQuery('input[name=cpx_valor_cliente_nome]').val(),
                            ids_notas : 'null'
                        },
                        success : function(data) {
                            jQuery("#area_creditos_a_conceder").html(data);

                        }
                    });
                }
                count++;

            } else {
                jQuery('#incluir_item').attr('disabled', 'disabled');
                jQuery('#incluir_item').addClass('desabilitado');
                jQuery.fn.statusTela01();
                jQuery.fn.escondeTelaEdicaoItem();
            }
        }


        if (jQuery.trim(jQuery('input[name=cpx_valor_cliente_nome]').val()) == '') {
            count = 0;
        }
        
    }, 500);
	
    jQuery("body").delegate('.exclui_item', 'click', function(){
    	
        jQuery.fn.limpaMensagens();

        if(confirm('Tem certeza que deseja excluir o item da nota fiscal?')){
            jQuery.fn.excluirItem();
        }
        if (jQuery('#cpx_div_clear_cliente_nome').length) {
            setTimeout(function () {
                if (parseFloat(jQuery("#qt_itens").val()) == 0) {
                    jQuery('.componente_btn_limpar').removeAttr('readonly');
                    jQuery('.componente_btn_limpar').removeAttr('disabled');
                    jQuery('.componente_btn_limpar').removeClass('desabilitado');
                    jQuery('.componente_tipo_pessoa').removeAttr('readonly');
                    jQuery('.componente_tipo_pessoa').removeAttr('disabled');
                    jQuery('.componente_tipo_pessoa').removeClass('desabilitado');
                }
            }, 800);
        }
    });

    jQuery("body").delegate('#bt_pesquisar_obrfin'	,'click', function(){
        $( "#busca_obrigacao_financeira_campo_obroid" ).val('');
        $( "#busca_obrigacao_financeira_campo_obrobrigacao" ).val('');
        $( "#result_pesq_obrigacao_financeira" ).html('').hide();
        $( "#dialog-form" ).dialog( "open" );
    //jQuery.fn.openObrigacaoFinanceira();
    });
    
    $( "#dialog-form" ).dialog({
        autoOpen: false,
        title: 'Obrigação Financeira',
        height: 400,
        width: 700,
        modal: true,
        buttons: {
            "Pesquisar": function() {
                jQuery.fn.pesquisarObrigacoes();
            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        }
    //	    	,close: function() {
    //	    		allFields.val( "" ).removeClass( "ui-state-error" );
    //	    	}
    });

    jQuery("body").delegate('.edita_item','click', function(){
        jQuery.fn.statusTela01();
        //jQuery.fn.escondeTelaEdicaoItem();
        jQuery.fn.editarItem(jQuery(this).attr('rel'));
    });

    jQuery("body").delegate('#incluir_item', 'click', function(){
        jQuery.fn.limpaMensagens();
    	
        jQuery('#contrato_item').val('');
        jQuery('#contratos').val('');
        jQuery('#placa').val('');
        jQuery('#classe_equipamento').val('');
        jQuery('#equipamento').val('');
        jQuery('#tipo_contrato').val('');

        jQuery.fn.escondeTelaEdicaoItem();
    	
        jQuery('#frm_pesquisa_contato').show();
        jQuery('#pesquisa_contrato').show();
        jQuery('#frame02').html('');
        jQuery('#frame03').html('');
    });
	
    jQuery("body").delegate('#bt_retorna_contrato', 'click', function(){
        jQuery.fn.limpaMensagens();
        jQuery.fn.statusTela01();
    });

    jQuery("body").delegate('#btn_cancela_alteracao_item', 'click', function(){
        jQuery.fn.limpaMensagens();
        jQuery.fn.escondeTelaEdicaoItem();
    });
	
    jQuery("body").delegate('#bt_pesquisa_contrato', 'click', function(){
        jQuery.fn.pesquisarContratos(true);
    });
	
    jQuery("body").delegate('#bt_inclui_sem_contrato','click', function(){
        jQuery.fn.incluirItem(true, "SC");
    });

    jQuery("body").delegate('.conoid','click', function(){
        jQuery.fn.incluirItem(true, jQuery(this).find("a").text());
    });
	
    jQuery("body").delegate('#bt_inclui_item','click', function(){
        if (jQuery('#cpx_div_clear_cliente_nome').length > 0) {
            jQuery('.componente_btn_limpar').attr('readonly', 'readonly');
            jQuery('.componente_btn_limpar').attr('disabled', 'disabled');
            jQuery('.componente_btn_limpar').addClass('desabilitado');
            jQuery('.componente_tipo_pessoa').attr('readonly', 'readonly');
            jQuery('.componente_tipo_pessoa').attr('disabled', 'disabled');
            jQuery('.componente_tipo_pessoa').addClass('desabilitado');
        }
        jQuery.fn.salvarItem();
    });

    jQuery("body").delegate('#bt_altera_item','click', function(){
        jQuery.fn.atualizarItem(jQuery(this).attr('rel'));
    });
    
    jQuery("body").delegate('#outras_nf','click', function(){
        jQuery.fn.abaInfoNF();
    });
	
    jQuery("body").delegate('#itens_nf','click', function(){
        jQuery.fn.abaItens();
    });
	
    jQuery("body").delegate('#bt_gerar_nf', 'click', function(){
        //jQuery.fn.gerarNF();
        jQuery.fn.gerarPreviaNF(true);
    });

    jQuery("body").delegate('#bt_gerar_nf_nova', 'click', function(){
        //jQuery.fn.gerarNF();
        jQuery.fn.gerarPreviaNF(true);
    });


    jQuery("body").delegate('#bt_criar_parcelas', 'click', function(){
        jQuery.fn.criarParcelas();
    });

    jQuery("body").delegate('.valor_grid_field', 'keyup', function() {
        
        var totalParcelas = 0;

        var valores = new Object();

        var  i = 0;
        jQuery(".valor_grid_field").each(function(){

             valores['parcela_'+i] = jQuery(this).val();

             i++;
        });

       var parametros = jQuery.param(valores);
       parametros += '&acao=somaParcelas';

        jQuery.ajax({
            url: 'fin_fat_manual.php',
            type: 'POST',
            data: parametros,
            success : function (data) {
                totalParcelas = maskValue(data);
                jQuery("#total_parcelas_nota").html(totalParcelas);
            }
        });

        // console.log(totalParcelas);

        // //totalParcelas = Math.round(totalParcelas);

        // console.log(totalParcelas);

        // totalParcelas = totalParcelas.toString();
        // totalParcelas = totalParcelas.replace('.','');

        // totalParcelas = maskValue(totalParcelas);


        // jQuery("#total_parcelas_nota").html(totalParcelas);

        


    });

    jQuery("#chk_all").toggleChecked("input[name='chk_oid[]']");
    if(jQuery("input[name='chk_oid[]']:checked").length == 0){
        return false;
    }



    jQuery( "body" ).delegate( "#qtd_replicacoes", 'change', function() {
        if(parseFloat(jQuery(this).val()) <= 0 || jQuery(this).val() == ''){
           jQuery(this).val('1');
        } else if (parseFloat(jQuery(this).val()) > 5000) {
            jQuery(this).val('5000');
        }
        jQuery(this).mask('9?999',{
            placeholder:''
        });
    });
    
  
    
    // $( "#create-user" ).button()

    
    jQuery("body").delegate('#dt_emi', 'click', function(){
        jQuery.fn.atualizaRef();
    });
    
    jQuery("body").delegate('#dt_emi', 'keypress', function(){
        jQuery.fn.atualizaRef();
    });
    jQuery("body").delegate('#dt_emi', 'keyup', function(){
        jQuery.fn.atualizaRef();
    });

    jQuery("body").delegate('#dt_emi', 'change', function(){
        jQuery.fn.atualizaRef();
    });
});


jQuery.fn.atualizaCampo = function(){
	$('#item_total').trigger('focus');
}

jQuery.fn.openObrigacaoFinanceira = function(){

    jQuery.fn.closeObrigacaoFinanceira();
    
    jQuery('#div_busca_obrigacao_financeira_overlay').fadeIn();
    jQuery('#div_busca_obrigacao_financeira_background').fadeIn();
}

jQuery.fn.closeObrigacaoFinanceira = function(){
    jQuery('#div_busca_obrigacao_financeira_overlay').fadeOut();
    jQuery('#div_busca_obrigacao_financeira_background').fadeOut();
    jQuery('#result_pesq_obrigacao_financeira').html('');
}

jQuery.fn.atualizaRef = function(){
    var dt_emi = jQuery("#dt_emi").val();
    var data_emi  = dt_emi.substring(6,10) +""+dt_emi.substring(3,5) +""+dt_emi.substring(0,2);
	
    if(parseInt( data_emi, 10) < parseInt( '19800101', 10)){
        jQuery("#dt_ref").val("");
    }
    else {
        var ano = dt_emi.substring(6,10);
        var mes = dt_emi.substring(3,5);
        jQuery("#dt_ref").val( "01/"+mes+"/"+ano);
    }
}

/******************************************************************************************************
 * FUNCIONALIDADES DA ABA "Outras informações NF"
 *******************************************************************************************************/


jQuery.fn.criarParcelas = function() {
    jQuery.fn.limpaMensagens();
    
    var dt_emi = jQuery("#dt_emi").val();
    var dt_venc = jQuery("#dt_venc").val();
    var forcoid = jQuery("#forcoid").val();
    var parc = jQuery("#parc").val();
    var serie = jQuery('#nflserie').val();
    
    var data_venc = dt_venc.substring(6,10)+""+dt_venc.substring(3,5)+""+dt_venc.substring(0,2);
    var data_emi  = dt_emi.substring(6,10) +""+dt_emi.substring(3,5) +""+dt_emi.substring(0,2);

    var data_base_venc = dt_venc.substring(6,10)+"-"+dt_venc.substring(3,5)+"-"+dt_venc.substring(0,2);

    
    
    var data = new Date();
    var dia = (data.getDate() < 10 ? "0"+data.getDate() : data.getDate() );
    var mes = (data.getMonth() < 9 ? "0"+(data.getMonth()+1) : data.getMonth()+1 );
    var ano = data.getFullYear();
    var dtatual = ano+""+mes+""+dia;

    if ( (parseInt(data_venc, 10) < parseInt(data_emi, 10)) ) {
        jQuery('#msgalerta2').html('Data de vencimento não pode ser menor que a data de emissão.').showMessage();
        $('#dt_venc').addClass('erro');
        $('#dt_emi').addClass('erro');
        return;
    }


    if ( (parseInt(data_venc, 10) < parseInt(dtatual, 10)) ) {
        jQuery('#msgalerta2').html('Data de vencimento não pode ser menor que a data de hoje.').showMessage();
        $('#dt_venc').addClass('erro');
        return;
    }

    // Campos obrigatórios
    if(dt_emi=="" || dt_venc=="" || forcoid=="" || parc=="" || serie == ""){
        if (dt_emi=="") {
            jQuery("#dt_emi").addClass('erro');
        }
        if(dt_venc=="") {
            jQuery("#dt_venc").addClass('erro');
        }
        if(forcoid=="") {
            jQuery("#forcoid").addClass('erro');
        }
        if(parc=="") {
            jQuery("#parc").addClass('erro');
        }
            
        if (serie == "") {
            jQuery("#nflserie").addClass('erro');
        }
            
        jQuery('#msgalerta2').html('Existem campos obrigatórios não preenchidos.').showMessage();
        return;
    }
    
    if(parc==0 || parc=="") {
        jQuery("#parc").addClass('erro');           
        jQuery('#msgalerta2').html('Quantidade de parcelas deve ser maior que zero.').showMessage();
        return;
    }
    
    if(parseInt(jQuery("#qt_itens").val(),10)==0){
        jQuery('#msgalerta2').html('Não é possível gerar nota sem cadastar ítens.').showMessage();
        return;
    }
    
    if(parseInt(jQuery("#qt_itens").val(),10)==0){
        jQuery('#msgalerta2').html('Não é possível gerar nota sem cadastar ítens.').showMessage();
        return;
    }

    var ids_notas = jQuery.trim(jQuery("#ids_notas").val()) != '' ? jQuery.trim(jQuery("#ids_notas").val()) : 'null';

    var concederCreditos = false;
    if (jQuery("#credito_futuro_cliente .creditos_futuro").length > 0) {

        if(confirm('Deseja conceder os créditos pendentes?')){
            jQuery("#conceder_creditos").val('1');
            concederCreditos = true;                            
        } else {
            jQuery("#conceder_creditos").val('0');
            concederCreditos = false;
        }       
    }

    jQuery.ajax({
        url: 'fin_fat_manual.php',
        type: 'POST',
        data: {
            acao: 'calcularParcelas',
            qtd_parcela: parc,
            ajax: 'true', 
            ids_notas: ids_notas,
            data_base_vencimento : dt_venc,
            conceder_creditos: concederCreditos
        },
        success: function (data) {

            jQuery("#conteudo_parcelas").html('');
            jQuery("#conteudo_footer").html('');
            jQuery("#tabela_creditos_concedidos").css("display","none");

            if (typeof JSON != 'undefined') {
                        data = JSON.parse(data);
                    } else {
                        data = eval('(' + data + ')');
                    }

            if (data.desconto_aplicado != false) {    

                if (data.desconto_aplicado != false && parseFloat(data.total) !=  0.01 ) {
                    jQuery("#tabela_creditos_concedidos").css("display","block"); 
                }
                    

                if (parseFloat(data.total) ==  0.01) {
                    jQuery("#acao").val('validarNotaFiscal');
                    jQuery.ajax({
                        url: 'fin_fat_manual.php',
                        type: 'post',
                        data: jQuery('#frm_editar').serialize()+'&ajax=true',
                        success: function(response) {            
                            try {

                                var data = jQuery.parseJSON(response);                
                                if(data.status) {
                                    jQuery.fn.gerarPreviaNF(false);
                                    return false;

                                } else {
                                    jQuery("#msg"+data.tipoErro).html(data.mensagem).showMessage();
                                }

                            } catch(e) {
                                jQuery('#msg_erro').html('Houve algum erro no processamento dos dados.').showMessage();
                            }
                        }
                    });
            }


            jQuery("#conteudo_creditos_concedidos").html(data.desconto_aplicado_tabela);
            
            }

            if (parseFloat(data.total) !=  0.01) {
                
                
                jQuery("#conteudo_parcelas").html(data.tbody);
                jQuery("#conteudo_footer").html(data.tfoot);

                jQuery('.data_parcela input').createDate();

                



                jQuery(".valor_grid_field").maskMoney({
                    symbol:'', 
                    thousands:'.', 
                    decimal:',', 
                    symbolStay: false, 
                    showSymbol:false,
                    precision:2, 
                    defaultZero: false,
                    allowZero: false
                });

                jQuery('body').delegate('.valor_grid_field', 'paste', function() {
                    var id = jQuery(this).attr('id');
                    var maxlength = jQuery(this).attr('maxlength');

                    setTimeout(function(){

                        var v = jQuery("#"+id).val();
                        var vMasc = maskValue(v);
                        var nV = v;

                        if (vMasc.length > maxlength) {
                            nV = "";
                            var maxChar = (maxlength - (vMasc.length - maxlength));
                            var vArray = v.split("");
                            var i = 0;
                            for ( i ; i <= maxChar ; i++) {
                                nV += vArray[i];
                            }   
                        }

                        jQuery("#"+id).val( maskValue(nV) );

                    },10);
                });

jQuery('body').delegate('.valor_grid_field', 'keyup', function() {
    var id = jQuery(this).attr('id');
    jQuery("#"+id).val( maskValue(jQuery("#"+id).val()) );
});


jQuery("#parametrizacao_parcelas").css('display','block');
} else {
    jQuery.fn.gerarPreviaNF(false);
    return false;
}


            
        }
    });

   

    


}

/**
 * Vai para tela de confirmação da Nota Fiscal.
 */
jQuery.fn.gerarNF = function() {
    jQuery.fn.limpaMensagens();

    //criar metodo para valida��o de parcelas
    if (!validarParcelas()){
        return false;
    }
	
    var dt_emi = jQuery("#dt_emi").val();
    var dt_venc = jQuery("#dt_venc").val();
    var forcoid = jQuery("#forcoid").val();
    var parc = jQuery("#parc").val();
    var serie = jQuery('#nflserie').val();
	
    var data_venc = dt_venc.substring(6,10)+""+dt_venc.substring(3,5)+""+dt_venc.substring(0,2);
    var data_emi  = dt_emi.substring(6,10) +""+dt_emi.substring(3,5) +""+dt_emi.substring(0,2);
	
    var data = new Date();
    var dia = (data.getDate() < 10 ? "0"+data.getDate() : data.getDate() );
    var mes = (data.getMonth() < 9 ? "0"+(data.getMonth()+1) : data.getMonth()+1 );
    var ano = data.getFullYear();
    var dtatual = ano+""+mes+""+dia;
	
    if ( (parseInt(data_venc, 10) < parseInt(data_emi, 10)) ) {
        jQuery('#msgalerta2').html('Data de vencimento não pode ser menor que a data de emissão.').showMessage();
        $('#dt_venc').addClass('erro');
        $('#dt_emi').addClass('erro');
        return;
    }


    if ( (parseInt(data_venc, 10) < parseInt(dtatual, 10)) ) {
        jQuery('#msgalerta2').html('Data de vencimento não pode ser menor que a data de hoje.').showMessage();
        $('#dt_venc').addClass('erro');
        return;
    }

    // Campos obrigatórios
    if(dt_emi=="" || dt_venc=="" || forcoid=="" || parc=="" || serie == ""){
        if (dt_emi=="") {
            jQuery("#dt_emi").addClass('erro');
        }
        if(dt_venc=="") {
            jQuery("#dt_venc").addClass('erro');
        }
        if(forcoid=="") {
            jQuery("#forcoid").addClass('erro');
        }
        if(parc=="") {
            jQuery("#parc").addClass('erro');
        }
            
        if (serie == "") {
            jQuery("#nflserie").addClass('erro');
        }
			
        jQuery('#msgalerta2').html('Existem campos obrigatórios não preenchidos.').showMessage();
        return;
    }
	
    if(parc==0 || parc=="") {
        jQuery("#parc").addClass('erro');			
        jQuery('#msgalerta2').html('Quantidade de parcelas deve ser maior que zero.').showMessage();
        return;
    }
	
    if(parseInt(jQuery("#qt_itens").val(),10)==0){
        jQuery('#msgalerta2').html('Não é possível gerar nota sem cadastar ítens.').showMessage();
        return;
    }
	
    if(parseInt(jQuery("#qt_itens").val(),10)==0){
        jQuery('#msgalerta2').html('Não é possível gerar nota sem cadastar ítens.').showMessage();
        return;
    }
	
    jQuery("#acao").val('validarNotaFiscal');
    jQuery.ajax({
        url: 'fin_fat_manual.php',
        type: 'post',
        data: jQuery('#frm_editar').serialize()+'&ajax=true',
        success: function(response) {            
            try {
                
                var data = jQuery.parseJSON(response);                
                if(data.status) {
                    if(!confirm('Confirma a geração da nota fiscal?')) return;
                    jQuery("#acao").val('gerarNotaFiscal');
                    jQuery('#frm_editar').submit();
                
                } else {
                    jQuery("#msg"+data.tipoErro).html(data.mensagem).showMessage();
                }
                
            } catch(e) {
                jQuery('#msg_erro').html('Houve algum erro no processamento dos dados.').showMessage();
            }
        }
    });	
	
}



/******************************************************************************************************
 * FUNCIONALIDADES DA ABA "Itens da NF"
 *******************************************************************************************************/
jQuery.fn.limpaMensagens = function() {
    jQuery('.mensagem').hideMessage();
    jQuery(".erro").not('.mensagem').removeClass("erro");
}

/**
 * Carrega lista de ítens vinculados a nova nota.
 */
jQuery.fn.carregarItens = function(async) {
    jQuery("#acao").val('editarItens');
    jQuery.ajax({
        async: async,
        url: 'fin_fat_manual.php',
        type: 'post',
        data: jQuery('#frm_editar').serialize()+'&ajax=true',
        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
        beforeSend: function(){
            //jQuery.fn.limpaMensagens();
            jQuery('#frame01').html('<center><img src="images/loading.gif" alt="" /></center>').show();
            jQuery('#incluir_item').attr('disabled', 'disabled');
        },
        success: function(data){
            // Liberação do botão de pesquisa
            jQuery('#frame01').fadeOut().html('');
            jQuery('#incluir_item').removeAttr('disabled');
            try{
                //caso mensagem retorne diferente de html, faz o tratamento para json, no caso, sessão expirou
                var resultado = jQuery.parseJSON(data);
                jQuery('#msgerro').attr("class", "mensagem erro").html(resultado.message).showMessage();
                if(resultado.redirect != "") window.location.href = resultado.redirect;
            }catch(e){
                try{
                    // Transforma a string em objeto JSON
                    jQuery('#frame01').hide().html(data).showMessage();
                }catch(e){
                    jQuery('#msgerro').attr("class", "mensagem erro").html('Erro no processamento dos dados.').showMessage();
                }
            }
        }
    });
}

/**
 * Exclui um ítem da lista de ítens e recarrega a lista.
 */
jQuery.fn.excluirItem = function() {	
    jQuery("#acao").val('excluirItem');

    jQuery.ajax({
        async: false,
        url: 'fin_fat_manual.php',
        type: 'post',
        data: jQuery('#frm_editar').serialize()+'&ajax=true',
        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
        beforeSend: function(){
            jQuery.fn.limpaMensagens();
            //jQuery.fn.statusTela01();
            jQuery('#frame01').html('<center><img src="images/loading.gif" alt="" /></center>').show();
            jQuery('#incluir_item').attr('disabled', 'disabled');
        },
        success: function(data){
            // Liberação do botão de pesquisa
            jQuery('#incluir_item').removeAttr('disabled');
			
            try{
                //caso mensagem retorne diferente de html, faz o tratamento para json, no caso, sessão expirou
                var resultado = jQuery.parseJSON(data);
                jQuery('#msgerro2').attr("class", "mensagem erro").html(resultado.message).showMessage();
                if(resultado.redirect != "") window.location.href = resultado.redirect;
                jQuery('#frame01').fadeOut().html('');
            }catch(e){
                try{
                    // Transforma a string em objeto JSON
                    //jQuery.fn.statusTela01();
                    jQuery.fn.carregarItens(true);
                    jQuery('#msgsucesso2').html('Item excluído com sucesso.').showMessage();
                }catch(e){
                    jQuery('#msgerro2').attr("class", "mensagem erro").html('Erro no processamento dos dados.').showMessage();
                }
            }

            atualizarValorCreditoFuturoPercentual();
        }
    });
}

/**
 * Para incluir um ítem é preciso pesquisar o contrato, e seleciona-lo
 */
jQuery.fn.pesquisarContratos = function(async) {	
    jQuery("#acao").val('pesquisarContrato');
    if(jQuery("#contrato_item").val() == '' && jQuery("#placa").val() == '' && jQuery("#cliente").val() == '') {
		jQuery('#msgerro3').attr("class", "mensagem erro").html('Preencher um dos campos de pesquisa.').showMessage();
		
}else{
    jQuery.ajax({
        async: async,
        url: 'fin_fat_manual.php',
        type: 'post',
        data: jQuery('#frm_editar').serialize()+'&ajax=true',
        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
        beforeSend: function(){
            jQuery.fn.limpaMensagens();
            jQuery('#frame02').html('<center><img src="images/loading.gif" alt="" /></center>').show();
        },
        success: function(data){
            // Liberação do botão de pesquisa
            try{
                //caso mensagem retorne diferente de html, faz o tratamento para json, no caso, sessão expirou
                var resultado = jQuery.parseJSON(data);
                jQuery('#msgerro3').attr("class", "mensagem erro").html(resultado.message).showMessage();
                if(resultado.redirect != "") window.location.href = resultado.redirect;
                jQuery('#frame02').fadeOut().html('');
            }catch(e){
                try{
                    // Transforma a string em objeto JSON
                    jQuery('#frame02').hide().html(data).fadeIn();
                }catch(e){
                    jQuery('#msgerro3').attr("class", "mensagem erro").html('Erro no processamento dos dados.').showMessage();
                }
            }
        }
    });
}
}

/**
 * Seleciona obrigação financeira
 */
jQuery.fn.pesquisarObrigacoes = function() {	
    var ac='pesquisarObrigacoesFinancerias';
    var obroid = jQuery('#busca_obrigacao_financeira_campo_obroid').val();
    var obrobrigacao = jQuery('#busca_obrigacao_financeira_campo_obrobrigacao').val();

    jQuery.ajax({
        url: 'fin_fat_manual.php',
        type: 'post',
        data: 'obroid='+obroid+'&obrobrigacao='+obrobrigacao+'&acao='+ac+'&ajax=true',
        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
        beforeSend: function(){
            jQuery('#result_pesq_obrigacao_financeira').hide();
            jQuery('#div_img_pesquisa_obrigacao_financeira').show();
        },
        success: function(data){
            // Liberação do botão de pesquisa
            jQuery('#incluir_item').removeAttr('disabled');
			
            try{
                //caso mensagem retorne diferente de html, faz o tratamento para json, no caso, sessão expirou
                var resultado = jQuery.parseJSON(data);
                jQuery('#result_pesq_obrigacao_financeira').html(resultado.message).showMessage();
                if(resultado.redirect != "") window.location.href = resultado.redirect;
            }catch(e){
                try{
                    // Transforma a string em objeto JSON
                    jQuery('#result_pesq_obrigacao_financeira').html(data).showMessage();
                }catch(e){
                    jQuery('#msgerro2').attr("class", "mensagem erro").html('Erro no processamento dos dados.').showMessage();
                }
            }
            jQuery('#div_img_pesquisa_obrigacao_financeira').hide();
        }
    });
}

jQuery.fn.valorUnitarioObr = function(valor){
	jQuery('#item_nfivl_item').val(valor);
}

jQuery.fn.selecionaObr = function(obroid, obrobrigacao, tipo_item,valor) {
    jQuery('#item_nfiobroid').val(obroid);
    jQuery('#item_obrobrigacao').val(obroid+' - '+obrobrigacao);


    //Regra 74

    if (tipo_item != '0' && tipo_item == 'M') {
        jQuery("#opcao_2_1").attr('checked','checked');
    } else if (tipo_item != '0' && tipo_item == 'L') {
        jQuery("#opcao_1_1").attr('checked','checked');
    } else {
        jQuery("#opcao_1_1").removeAttr('checked');
    }

    jQuery('#dialog-form' ).dialog( "close" );
}

jQuery.fn.editarItem = function(key){
    jQuery('#acao').val('incluirItem');
    jQuery.ajax({
        async: true,
        url:   'fin_fat_manual.php',
        type:  'post',
        data:  jQuery('#frm_editar').serialize()+'&ajax=true&notaFiscalItem='+key,
        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
        beforeSend: function(){
            jQuery.fn.limpaMensagens();
            jQuery('#frame03').html('<center><img src="images/loading.gif" alt="" /></center>').show();
        },
        success: function(data) {
            try{
                jQuery('#frameEditarItem').html(data);
                jQuery('#edita_item_nf').fadeIn();

                jQuery('#item_nfivl_item').trigger('blur');
                jQuery('#item_nfidesconto').trigger('blur');

            }catch(e){
                jQuery('#msgerro4').attr("class", "mensagem erro").html('Erro no processamento dos dados.').showMessage();
            }

            atualizarValorCreditoFuturoPercentual();
        }
        
    });
}

/**
 * Carrega o formulário para preenchimento do cadastro de itens
 */
jQuery.fn.incluirItem = function(async, connumero) {
    jQuery("#acao").val('incluirItem');
    jQuery.ajax({
        async: async,
        url: 'fin_fat_manual.php',
        type: 'post',
        data: jQuery('#frm_editar').serialize()+'&ajax=true&connumero='+connumero,
        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
        beforeSend: function(){
            jQuery.fn.limpaMensagens();
            jQuery('#frame03').html('<center><img src="images/loading.gif" alt="" /></center>').show();
        },
        success: function(data){
            // Liberação do botão de pesquisa
            try{
                //caso mensagem retorne diferente de html, faz o tratamento para json, no caso, sessão expirou
                var resultado = jQuery.parseJSON(data);
                jQuery('#msgerro4').attr("class", "mensagem erro").html(resultado.message).showMessage();
                if(resultado.redirect != "") window.location.href = resultado.redirect;
                jQuery('#frame03').fadeOut().html('');
            }catch(e){
                try{
                    // Transforma a string em objeto JSON
                    //jQuery.fn.statusTela02();
                    jQuery('#frm_pesquisa_contato').fadeOut();
                    jQuery('#frame02').fadeOut().html('');
                    jQuery('#frame03').hide().html(data).fadeIn();
                }catch(e){
                    jQuery('#msgerro4').attr("class", "mensagem erro").html('Erro no processamento dos dados.').showMessage();
                }
            }

            //atualizarValorCreditoFuturoPercentual();
        }
    });
}

jQuery.fn.validarCamposItemNf = function (){
    var item_nfiobroid = jQuery('#item_nfiobroid').val();
    var item_nfivl_item = jQuery('#item_nfivl_item').val().replace('.','').replace(',','.');
    var item_nfidesconto = jQuery('#item_nfidesconto').val().replace('.','').replace(',','.');
    var item_total = jQuery('#item_total').val().replace('.','').replace(',','.');
    var item_tipo  = jQuery("input[name='item[nfitipo]']:checked").val();
    var qtd_replicacoes  = jQuery("#qtd_replicacoes").val();

    jQuery.fn.limpaMensagens();

    if(item_nfiobroid=="" || item_nfivl_item=="0.00" || (item_tipo!='M' && item_tipo!='L') || qtd_replicacoes ==""){
        if(item_nfiobroid=="")
            jQuery('#item_obrobrigacao').addClass('erro');

        if(item_nfivl_item=="0.00")
            jQuery('#item_nfivl_item').addClass('erro');

        if(item_tipo!='M' && item_tipo!='L')
            jQuery("#field_tipo").addClass('erro');

        if(qtd_replicacoes == "")
            jQuery("#qtd_replicacoes").addClass('erro');

        jQuery('#msgalerta3').html('Existem campos obrigatórios não preenchidos.').showMessage();
        return false;
    }

    if(parseFloat(item_nfivl_item) <= parseFloat(item_nfidesconto) && parseFloat(item_nfivl_item) > 0 ){
        jQuery('#item_nfivl_item').addClass('erro');
        jQuery('#item_nfidesconto').addClass('erro');
        jQuery('#msgalerta3').html('O valor do desconto deve ser menor que o valor unitario.').showMessage();
        return false;
    }

   /* if(parseFloat(item_nfivl_item)<=0 || parseFloat(item_nfidesconto)<0 || parseFloat(item_total)<=0 ){
        jQuery('#item_nfivl_item').addClass('erro');
        jQuery('#item_nfidesconto').addClass('erro');
        jQuery('#item_total').addClass('erro');
        jQuery('#msgalerta3').html('Valor inválido.').showMessage();
        return false;
    }*/
    
    if (parseFloat(qtd_replicacoes) <= 0 || parseFloat(qtd_replicacoes) > 5000 ) {
        jQuery('#qtd_replicacoes').addClass('erro');
        jQuery('#msgalerta3').html('O limite máximo é de 5000 itens.').showMessage();
        return false;

    }

    return true;
}

/**
 * Salva os dados iformados no formulario de cadastro de itens e recarrega lista de itens.
 */
jQuery.fn.salvarItem = function() {

    if(!jQuery.fn.validarCamposItemNf()){
        return false
    }
    
    setTimeout(function(){
		
        if(!confirm('Tem certeza que deseja incluir o item na nota fiscal?')){
            return;
        } else {
			
            jQuery("#acao").val('salvarItem');
            jQuery.ajax({
                url: 'fin_fat_manual.php',
                type: 'post',
                data: jQuery('#frm_editar').serialize()+'&ajax=true',
                contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
                beforeSend: function(){
                    jQuery.fn.limpaMensagens();
                    jQuery('#load_Save').html('<center><img src="images/loading.gif" alt="" /></center>').show();
                    jQuery('#incluir_item').attr('disabled', 'disabled');
                },
                success: function(data){
                    // Liberação do botão de pesquisa
                    jQuery('#incluir_item').removeAttr('disabled');

                    try{
                        //caso mensagem retorne diferente de html, faz o tratamento para json, no caso, sessão expirou
                        var resultado = jQuery.parseJSON(data);
                        jQuery('#msgalerta3').html(resultado.message).showMessage();
                        if(resultado.redirect != "") window.location.href = resultado.redirect;
                    }catch(e){
                        try{
                            jQuery.fn.statusTela01();
                            jQuery('#msgsucesso2').html('Item(ns) incluído(s) com sucesso.').showMessage();
                            jQuery.fn.carregarItens(true);
                        }catch(e){
                            jQuery('#msgerro3').attr("class", "mensagem erro").html('Erro no processamento dos dados.').showMessage();
                        }
                    }
                    atualizarValorCreditoFuturoPercentual();
                    jQuery('#load_Save').html('').hide();
                }
            });
        }
		
    }, 500);
	
   	
}

/**
 * Atualiza os dados informados no formulario de cadastro de itens e recarrega lista de itens.
 */
jQuery.fn.atualizarItem = function(chaveItem) {

    if(!jQuery.fn.validarCamposItemNf()){
        return false
    }

    setTimeout(function(){

        if(!confirm('Tem certeza que deseja alterar o item na nota fiscal?')){
            return;
        } else {

            jQuery("#acao").val('salvarItem');
            jQuery.ajax({
                url: 'fin_fat_manual.php',
                type: 'post',
                data: jQuery('#frm_editar').serialize()+'&ajax=true&chaveItem='+chaveItem,
                contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
                beforeSend: function(){
                    jQuery.fn.limpaMensagens();
                    jQuery('#load_Save').html('<center><img src="images/loading.gif" alt="" /></center>').show();
                    jQuery('#incluir_item').attr('disabled', 'disabled');
                },
                success: function(data){
                    jQuery('#incluir_item').removeAttr('disabled');

                    try{
                        jQuery.fn.escondeTelaEdicaoItem();
                        jQuery('#msgsucesso2').html('Item atualizado com sucesso.').showMessage();
                        jQuery.fn.carregarItens(true);
                    }catch(e){
                        jQuery('#msgerro3').attr("class", "mensagem erro").html('Erro no processamento dos dados.').showMessage();
                    }
                    atualizarValorCreditoFuturoPercentual();
                    jQuery('#load_Save').html('').hide();
                }
            });
        }

    }, 500);
}


function atualizarValorCreditoFuturoPercentual() {

    var creditosTipoPercentual = new Array();

    var i = 0;
    jQuery("#credito_futuro_cliente .credito_tipo_desconto_P").each(function(){
        //console.log(jQuery(this).children('.valor').text());

        var credito = new Object();

        credito.credito_id = jQuery(this).attr('data-creditoid');
        credito.percentual = jQuery(this).attr('data-porcentagem');
        credito.cfoaplicar_desconto = jQuery(this).attr('data-aplicarDescontoSobre');
        creditosTipoPercentual[i] = credito;

        i++;
    });

    if (creditosTipoPercentual.length > 0) {

        jQuery.ajax({
            url: 'fin_fat_manual.php',
            type: 'POST',
            data: {
                acao: 'atualizaValorCreditoPercentual',
                ids_notas: jQuery("#ids_notas").val(),
                creditos: creditosTipoPercentual
            },
            success: function(data) {

                if (typeof JSON != 'undefined') {
                        data = JSON.parse(data);
                    } else {
                        data = eval('(' + data + ')');
                    }

                //console.log(data);
                jQuery(data).each(function(i,v){
                    jQuery("#credito_futuro_" + v.credito_id + " .valor").text(v.valor_formatado);
                });
            }
        });

    }

}

/**
 * Retorna a tela ao status incial, mostrando apenas a lista de itens.
 */
jQuery.fn.statusTela01 = function (){
    jQuery('#frame02').fadeOut().html('');
    jQuery('#frame03').fadeOut().html('');
    jQuery('#pesquisa_contrato').fadeOut();
}

/**
 * Fecha todos os elementos na tela para criacao de novo formulario de cadastro de itens, 
 * configuracoes de nota ou demais formularios que nao nescessitem mostrar lista de itens.
 */
jQuery.fn.statusTela02 = function (){
    jQuery('#frame01').fadeOut();
    jQuery('#pesquisa_contrato').fadeOut();
    jQuery('#frame02').fadeOut();
}

/**
 * Esconde telas abertas na edi��o de um item
 */
jQuery.fn.escondeTelaEdicaoItem = function (){
    jQuery('#edita_item_nf').fadeOut();
    jQuery('#frameEditarItem').html('');
}

jQuery.fn.abaItens = function (){

    jQuery.fn.limpaMensagens();
    jQuery("#parametrizacao_parcelas").hide();
    jQuery(".ativo").removeClass("ativo");
    jQuery('#itens_nf').attr("class", "ativo")
    jQuery('#aba_itens_nf').show();
    jQuery('#aba_outras_inf_nf').hide();
}

jQuery.fn.abaInfoNF = function (){
	
    jQuery.fn.limpaMensagens();
	
    var qt_itens = parseInt(jQuery('#qt_itens').val(), 10);
    var qt_itens_sem_tipo = parseInt(jQuery('#qt_itens_sem_tipo').val(), 10);
    if(qt_itens < 1){
        return;
    }
    if(qt_itens_sem_tipo > 0){
        jQuery('#msgalerta2').html('Há Item(ns) que não possuem o tipo do item (Monitoramento ou Locação) preenchido. é necessário excluir o item e incluí-lo novamente.').showMessage();
        return;
    }
	
	
    jQuery(".ativo").removeClass("ativo");
    jQuery('#outras_nf').attr("class", "ativo")
    jQuery('#aba_itens_nf').hide();
    jQuery('#aba_outras_inf_nf').show();
}

jQuery.fn.formatarDinheiro = function(){
	 $('.campo_dinheiro').maskMoney({thousands:'', decimal:'.', allowZero:false, allowNegative:true, defaultZero:false});
}

jQuery.fn.gerarPreviaNF = function(verifica) {
    jQuery.fn.limpaMensagens();


    //criar metodo para validação de parcelas
    if (!validarParcelas()){
        return false;
    }
    
    var dt_emi = jQuery("#dt_emi").val();
    var dt_venc = jQuery("#dt_venc").val();
    var forcoid = jQuery("#forcoid").val();
    var parc = jQuery("#parc").val();
    var serie = jQuery('#nflserie').val();
    
    var data_venc = dt_venc.substring(6,10)+""+dt_venc.substring(3,5)+""+dt_venc.substring(0,2);
    var data_emi  = dt_emi.substring(6,10) +""+dt_emi.substring(3,5) +""+dt_emi.substring(0,2);
    
    var data = new Date();
    var dia = (data.getDate() < 10 ? "0"+data.getDate() : data.getDate() );
    var mes = (data.getMonth() < 9 ? "0"+(data.getMonth()+1) : data.getMonth()+1 );
    var ano = data.getFullYear();
    var dtatual = ano+""+mes+""+dia;
    
    if ( (parseInt(data_venc, 10) < parseInt(data_emi, 10)) ) {
        jQuery('#msgalerta2').html('Data de vencimento não pode ser menor que a data de emissão.').showMessage();
        $('#dt_venc').addClass('erro');
        $('#dt_emi').addClass('erro');
        return;
    }


    if ( (parseInt(data_venc, 10) < parseInt(dtatual, 10)) ) {
        jQuery('#msgalerta2').html('Data de vencimento não pode ser menor que a data de hoje.').showMessage();
        $('#dt_venc').addClass('erro');
        return;
    }

    // Campos obrigatórios
    if(dt_emi=="" || dt_venc=="" || forcoid=="" || parc=="" || serie == ""){
        if (dt_emi=="") {
            jQuery("#dt_emi").addClass('erro');
        }
        if(dt_venc=="") {
            jQuery("#dt_venc").addClass('erro');
        }
        if(forcoid=="") {
            jQuery("#forcoid").addClass('erro');
        }
        if(parc=="") {
            jQuery("#parc").addClass('erro');
        }
            
        if (serie == "") {
            jQuery("#nflserie").addClass('erro');
        }
            
        jQuery('#msgalerta2').html('Existem campos obrigatórios não preenchidos.').showMessage();
        return;
    }
    
    if(parc==0 || parc=="") {
        jQuery("#parc").addClass('erro');           
        jQuery('#msgalerta2').html('Quantidade de parcelas deve ser maior que zero.').showMessage();
        return;
    }
    
    if(parseInt(jQuery("#qt_itens").val(),10)==0){
        jQuery('#msgalerta2').html('Não é possível gerar nota sem cadastar ítens.').showMessage();
        return;
    }
    
    if(parseInt(jQuery("#qt_itens").val(),10)==0){
        jQuery('#msgalerta2').html('Não é possível gerar nota sem cadastar ítens.').showMessage();
        return;
    }
    
    jQuery("#acao").val('validarNotaFiscal');
    jQuery.ajax({
        url: 'fin_fat_manual.php',
        type: 'post',
        data: jQuery('#frm_editar').serialize()+'&ajax=true',
        success: function(response) {            
            try {
                
                var data = jQuery.parseJSON(response);                
                if(data.status) {
                    
                    if (verifica) {
                        if(confirm('Confirma a geração da nota fiscal?')){
                            jQuery("#acao").val('gerarPreviaNotaFiscal');
                            jQuery('#frm_editar').submit();
                        }
                    } else {
                        jQuery("#acao").val('gerarPreviaNotaFiscal');
                        jQuery('#frm_editar').submit();
                    }

                    
                    
                
                } else {
                    jQuery("#msg"+data.tipoErro).html(data.mensagem).showMessage();
                }
                
            } catch(e) {
                jQuery('#msg_erro').html('Houve algum erro no processamento dos dados.').showMessage();
            }
        }
    }); 
    
}


var somaItem = function (){
    var  total = 0;     
    var teste =   jQuery('#item_total').val('');

    var valor      = jQuery('#item_nfivl_item').val();
    var desconto   = jQuery('#item_nfidesconto').val();
    var valor2      = jQuery('#item_nfivl_item').val();
    valor          = valor.replace('.' , '')
    valor          = valor.replace(',' , '.')
    valor          = parseFloat(valor);
  //  valor          = valor.toFixed(2);
    
    desconto     = desconto.replace('.' , '')
    desconto     = desconto.replace(',' , '.')
    desconto     = parseFloat(desconto);
  //  desconto     = desconto.toFixed(2);

    if (valor >= desconto && valor > 0 ) {
        total = valor - desconto;
       // total = total.toFixed(2);
        
    }else if(valor < 0 ){
    	   total = valor2 ;
    } else {
        total = 0;
        total = total.toFixed(2);
    }
    
   jQuery('#item_total').val(total);
 
 
 
}


settings = {};
    settings.allowNegative = false;
    settings.decimal = ',';
    settings.precision = 2;
    settings.thousands = '.';

    function maskValue(v) {
        
        var strCheck = '0123456789';
        var len = v.length;
        var a = '', t = '', neg='';

        if(len!=0 && v.charAt(0)=='-'){
            v = v.replace('-','');
            if(settings.allowNegative){
                neg = '-';
            }
        }

        for (var i = 0; i<len; i++) {
            if ((v.charAt(i)!='0') && (v.charAt(i)!=settings.decimal)) break;
        }

        for (; i<len; i++) {
            if (strCheck.indexOf(v.charAt(i))!=-1) a+= v.charAt(i);
        }

        var n = parseFloat(a);
        n = isNaN(n) ? 0 : n/Math.pow(10,settings.precision);
        t = n.toFixed(settings.precision);

        i = settings.precision == 0 ? 0 : 1;
        var p, d = (t=t.split('.'))[i].substr(0,settings.precision);
        for (p = (t=t[0]).length; (p-=3)>=1;) {
            t = t.substr(0,p)+settings.thousands+t.substr(p);
        }

        return (settings.precision>0)
        ? neg+t+settings.decimal+d+Array((settings.precision+1)-d.length).join(0)
        : neg+t;
    }

function validarParcelas() {

    var parcelasOk = true;

    var valorOk = true;
    var valorPreenchido = true;

    var valorTotalParcelas = jQuery.trim(jQuery("span#total_parcelas_nota").text());
    var valorTotalNota = jQuery.trim(jQuery("span#total_nota").text());


    if (valorTotalNota !== valorTotalParcelas) {
        parcelasOk = false;
        valorOk    = false;        
    }

    jQuery(".valor_grid_field").each(function(){

        if (jQuery.trim(jQuery(this).val()) == '' ) {
            jQuery(this).addClass('erro');
             parcelasOk = false;
            valorPreenchido = false;
        }

    });

    ///////////Datas de vencimentos//////////////

    var qtdParcelas = jQuery(".data_grid_field").length;
    var dataOk = true;
    var dataPreenchida = true;


    var i  = 1;

    jQuery(".data_grid_field").each(function(){

        if (jQuery.trim(jQuery(this).val()) == '' ) {
            jQuery(this).addClass('erro');
             parcelasOk = false;
            dataPreenchida = false;
        }

    });

    for (i; i <= qtdParcelas; i++) {

        var prevField    = 'input[name="parcela[' + (i-1) + '][data]"]';
        var currentField = 'input[name="parcela[' + i + '][data]"]';
        var nextField    = 'input[name="parcela[' + (i+1) + '][data]"]';


        var currentFieldValue = jQuery(currentField).val();
        currentFieldValue = currentFieldValue.substring(6,10)+""+currentFieldValue.substring(3,5)+""+currentFieldValue.substring(0,2);
        currentFieldValue = parseInt(currentFieldValue);
        

        if (jQuery(prevField).is('input')) {
            var prevFieldValue = jQuery(prevField).val();
            prevFieldValue = prevFieldValue.substring(6,10)+""+prevFieldValue.substring(3,5)+""+prevFieldValue.substring(0,2);
            prevFieldValue = parseInt(prevFieldValue);

            if (currentFieldValue <= prevFieldValue) {
                parcelasOk = false;     
                dataOk = false;           
                jQuery(currentField).addClass('erro');
            }

        }

        if (jQuery(nextField).is('input')) {
            var nextFieldValue = jQuery(nextField).val();
            nextFieldValue = nextFieldValue.substring(6,10)+""+nextFieldValue.substring(3,5)+""+nextFieldValue.substring(0,2);
            nextFieldValue = parseInt(nextFieldValue);

            if (currentFieldValue >= nextFieldValue) {
                parcelasOk = false;
                dataOk = false;                
                jQuery(currentField).addClass('erro');
            }
           
        }

    }

    if (!dataPreenchida) {
        jQuery('#alertaParcelasData').html('A data de vencimento da parcela não pode ser vazio.').showMessage();
    }

    if (!dataOk && dataPreenchida) {
        jQuery('#alertaParcelasData').html('Há parcela(s) com data de vencimento inválida.').showMessage();
    }

    if (!valorPreenchido) {
        jQuery('#alertaParcelasValor').html('O valor da parcela não pode ser vazio.').showMessage();
    }

    if (!valorOk && valorPreenchido) {
        jQuery('#alertaParcelasValor').html('O valor total das parcelas é diferente do valor total da nota.').showMessage();
    }
    

    return parcelasOk;

}