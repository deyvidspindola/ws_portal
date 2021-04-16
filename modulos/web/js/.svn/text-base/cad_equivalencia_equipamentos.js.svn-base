jQuery(document).ready(function(){
    
    jQuery("#marcar_todos").toggleChecked(".excluir_produto");    
    
    if(typeof jQuery('[title]').tooltip == 'function'){
        jQuery('[title]').not('a').tooltip('destroy');    
    }
    
    if(typeof jQuery('[title]').not('a').not('.data img').not('.mes_ano img').tooltip == 'function'){
       jQuery('[title]').not('a').not('.data img').not('.mes_ano img').tooltip({position: {my: 'left+5 center', at: 'right center'}});
    }
    
    //verifico se alguma opção foi selecionada
    jQuery("body").delegate('.excluir_produto', 'change', function(){
        if (jQuery(".excluir_produto:checked").length == 0) {
            jQuery("#bt_excluir").attr('disabled','disabled');
        }else{
            jQuery("#bt_excluir").removeAttr('disabled');
        }
    });
    
    jQuery("body").delegate('#marcar_todos', 'change', function(){
        if (jQuery("#marcar_todos:checked").length == 0) {
            jQuery("#bt_excluir").attr('disabled','disabled');
        }else{
            jQuery("#bt_excluir").removeAttr('disabled');
        }
    });
    
    //pesquisar
    jQuery("#bt_pesquisar").click(function(){
        jQuery("#acao").attr('value','pesquisar');
        jQuery("#form").submit();
    });
    
    //novo
    jQuery("#bt_novo").click(function(){
        jQuery('#acao').val('cadastrar');
        jQuery('#form').submit();
    });
    
    //gerar xls
    jQuery("#bt_gerar_xls").click(function(){
    	
    	jQuery.fn.limpaMensagens();
        jQuery("#acao").attr('value','exportarXLS');
        jQuery.ajax({
    		url: 'cad_equivalencia_equipamentos.php',
            type: 'post',
    		data: jQuery('#form').serialize()+'&ajax=true',
    		beforeSend: function(){		
    	    	//jQuery.fn.limpaMensagens();
    			jQuery('#resultado_pesquisa').html('<center><img src="images/loading.gif" alt="" /></center>').show();
    		},
            success: function(response) {            
                try {                    
                	var data = jQuery.parseJSON(response);     
                    
                	jQuery('#resultado_pesquisa').html('');     
                    var html='<div class="bloco_titulo">Download</div>';
                    	html+='<div class="bloco_conteudo"><div class="formulario">';
                    		html+='<label><a href="'+data.file+'" ><center><img width="36px" alt="Baixar relatório" src="images/icones/t3/caixa2.jpg">';
                    		html+='<br>Relatório de Equivalências de Classes</center></a></label>';
                    	html+='</div></div>';
                    
                	if(data.tipo=='E') {
                        jQuery('#mensagens_carregamento_combos').html(data.mensagem).showMessage();
                    }
                	else if(data.tipo=='A') {
                        jQuery('#mensagens_alerta').html(data.mensagem).showMessage();
                    }
                    else {
                    	//jQuery('#mensagens_alerta').html('processado').showMessage();
                    	jQuery('#resultado_pesquisa').html(html).show();
                    }
                } catch(e) {
                    jQuery('#resultado_pesquisa').html('');
                    jQuery('#mensagens_alerta').html('Houve algum erro no processamento dos dados.').showMessage();
                }    			
            }
        });	
        
    });
    
    //cadastrar equipamento item
    jQuery("#bt_confirmar").click(function(){
        jQuery("#formCadastro").submit();
    });
    
    //voltar
    jQuery("#bt_voltar").click(function(){
        window.location.href = "cad_equivalencia_equipamentos.php?acao=pesquisar";
    });
    
    
    
    //excluir produtos
    jQuery("body").delegate('#bt_excluir', 'click', function(){
        
        if (!confirm('Deseja realmente excluir este(s) produto(s) das equivalências desta classe de contrato?')){
            return false;
        }
        
        var dados = jQuery("#formExcluirProduto").serialize();
        
        jQuery.ajax({
            url: 'cad_equivalencia_equipamentos.php',
            type: 'post',
            dataType: 'json',
            async: false,
            data: dados,
            beforeSend: function() {       
                jQuery('#load-listagem').show();
                jQuery('#resultado_pesquisa').hide();                
                jQuery.fn.limpaMensagens();
                jQuery('.alerta, .erro, .sucesso').remove();
            },
            success: function(data) {                
                jQuery('.info').after('<div class="mensagem '+data.tipoMensagem+'">' + data.mensagem + '</div>');
                carregarListagemProdutos(data.equivalenciaId);
            },
            complete: function() {
            },
            error: function() {
                //jQuery('#mensagens_carregamento_combos').html('Houve falha na comunicação com o servidor.').fadeIn();
            }
        });
    });
    
    //cadastrar produto filho
    jQuery("#bt_incluir_produto").click(function(){
        jQuery.fn.limpaMensagens();
        jQuery('.alerta, .erro, .sucesso').remove();
        jQuery("#bt_incluir_produto").attr('disabled','disabled');
                
        var dados = jQuery("#formCadastroProdutos").serialize();
        var equivalenciaId = jQuery('#eeieeqoid').val();
        
        jQuery.ajax({
            url: 'cad_equivalencia_equipamentos.php',
            type: 'post',
            dataType: 'json',
            async: false,
            data: dados,
            beforeSend: function() {       
                jQuery('#load-listagem').show();
                jQuery('#resultado_pesquisa').hide();
            },
            success: function(data) {
                //console.log(data);
                jQuery("#bt_incluir_produto").removeAttr('disabled');
                if (data['status'] == '0') {
                    
                    jQuery('#load-listagem').hide();                    
                    jQuery('#resultado_pesquisa').show();
                    
                    if (data['mensagem'] != '') {
                        if (data['tipo'] == 'A') {
                            jQuery('.info').after('<div class="mensagem alerta">' + data['mensagem'] + '</div>');
                        }
                        if (data['tipo'] == 'E') {
                            jQuery('.info').after('<div class="mensagem erro">' + data['mensagem'] + '</div>');
                        }
                    }
                    
                    if (data['erro'] != '') {
                        var formErro = jQuery.parseJSON(data['erro']);
                        showFormErros(formErro);
                    }
                    
                } else if (data['status'] == '1') {
                    
                    if (data['mensagem'] != '') {
                        if (data['tipo'] == 'S') {
                            jQuery('.info').after('<div class="mensagem sucesso">' + data['mensagem'] + '</div>');
                        }
                    }
                    jQuery("#eeitipo, #eeiprdoid, #eeiversao").val('');
                    jQuery("#eeiprdoid, #eeiversao").html('<option value="">Selecione</option>');
                    jQuery('#listagem_equivalencia_equipamentos').html('');
                    jQuery('#bt_copiar').attr('disabled', 'disabled');
                    
                    if(jQuery('.bloco_copia').css('display') != 'none') {
                        jQuery('.bloco_copia').fadeOut();
                    } 
                    
                    carregarListagemProdutos(equivalenciaId);
                                                            
                }
                
            },
            complete: function() {                      
            },
            error: function() {
                //jQuery('#mensagens_carregamento_combos').html('Houve falha na comunicação com o servidor.').fadeIn();
            }
        });
    })
    
    
    function carregarListagemProdutos(equivalenciaId){
        
        jQuery.ajax({
            url: 'cad_equivalencia_equipamentos.php',
            type: 'post',
            dataType: 'html',
            data: {
                acao: 'carregarListagemEquipamento',
                eeqoid: equivalenciaId,
                isAjax: 1
            },
            beforeSend: function() {  
            },
            success: function(data) {
                jQuery('#load-listagem').hide();
                if (data == '') {
                    jQuery("#bt_copiar").removeAttr('disabled');
                }
                jQuery('#listagem_equivalencia_equipamentos').html(data);
            },
            complete: function() { 
                jQuery('#load-listagem').hide();                
            }
        });
    }
    
    //Ao selecionar "Classes sem cadastro" os combos são desabilitados    
    
    jQuery('.combo_pesquisa').change(function(){
        if(jQuery('.combo_pesquisa .opcao:selected').length > 0){
            jQuery('#classes_sem_cadastro').attr('disabled','disabled');
        }else{
            jQuery('#classes_sem_cadastro').removeAttr('disabled');
        }
    });
    
    
    jQuery('#classes_sem_cadastro').change(function(){
        if (jQuery(this).is(':checked')) {
            jQuery('#form SELECT').val('');
            jQuery('#form SELECT').attr('disabled','disabled');
        }else{
            jQuery('#form SELECT').removeAttr('disabled');
        }
    });
    
    if (jQuery('#classes_sem_cadastro').is(':checked')) {
            jQuery('#form SELECT').attr('disabled','disabled');
    }else{
            jQuery('#form SELECT').removeAttr('disabled');
    }
    ////////////////////////////////////////////////////
    
    
    
    
    jQuery('#eqqmodalidade').change(function(){
                
        /**
         * Se não selecionar a modalidade limpa os campos:
         * Combo: Classe do Contrato
         * Combo: Tipo do Contrato
         **/        
        if(jQuery(this).val() == "") {
            jQuery('#classes_sem_cadastro').removeAttr('disabled');
            jQuery('#mensagens_carregamento_combos').fadeOut();
            jQuery('#eeqeqcoid, #eeqtpcoid').html("<option value=''>Selecione</option>");
            jQuery('#eeqeqcoid').val('');
            jQuery('#eeqtpcoid').val('');            
            return false;            
        }
            

        var modalidade = jQuery(this).val();

        jQuery.ajax({
            url: 'cad_equivalencia_equipamentos.php',
            type: 'post',
            dataType: 'json',
            data: {
                acao: 'carregarClassesContratos',
                eqqmodalidade: modalidade
            },
            beforeSend: function() {                    
                jQuery('#loading-classe-contrato').mostrarCarregando();
                jQuery('#mensagens_carregamento_combos').fadeOut();
                jQuery('#eeqeqcoid, #eeqtpcoid').html("<option value=''>Selecione</option>");
            },
            success: function(data) {
                var options = "<option value=''>Selecione</option>";
                jQuery.each(data,function(i,val){
                    options += "<option class='opcao' value='"+ val.id +"'>"+ val.label +"</option>";
                });
                jQuery('#eeqeqcoid').html(options);
            },
            complete: function() {                    
                jQuery('#loading-classe-contrato').esconderCarregando();
            },
            error: function() {
                //jQuery('#mensagens_carregamento_combos').html('Houve falha na comunicação com o servidor.').fadeIn();
            }
        });        
        
    });
    
    
    jQuery('#eeqeqcoid').change(function(){
                
        /**
         * Se não selecionar a classe do contrato limpa os campos:
         * Combo: Tipo do Contrato
         **/
        if(jQuery(this).val() == "") {     
            
            
            jQuery('#mensagens_carregamento_combos').fadeOut();
            jQuery('#eeqtpcoid').html("<option value=''>Selecione</option>");
            jQuery('#eeqtpcoid').val('');
            return false;            
        }       

        var modalidade = jQuery('#eqqmodalidade').val();
        var classeContrato = jQuery("#eeqeqcoid").val();

        jQuery.ajax({
            url: 'cad_equivalencia_equipamentos.php',
            type: 'post',
            dataType: 'json',
            data: {
                acao: 'carregarTiposContratos',
                eqqmodalidade: modalidade,
                eeqeqcoid: classeContrato
            },
            beforeSend: function() {                    
                jQuery('#loading-tipo-contrato').mostrarCarregando();
                jQuery('#mensagens_carregamento_combos').fadeOut();
                jQuery('#eeqtpcoid').html("<option value=''>Selecione</option>");
            },
            success: function(data) {
                var options = "<option value=''>Selecione</option>";
                jQuery.each(data,function(i,val){
                    options += "<option class='opcao' value='"+ val.id +"'>"+ val.label +"</option>";
                });
                jQuery('#eeqtpcoid').html(options);
            },
            complete: function() {                    
                jQuery('#loading-tipo-contrato').esconderCarregando();
            },
            error: function() {
                //jQuery('#mensagens_carregamento_combos').html('Houve falha na comunicação com o servidor.').fadeIn();
            }
        });        
        
    });
    
    //busca de produtos ao selecionar o tipo de produto
    jQuery('#eeitipo').change(function(){
                
        /**
         * Se não selecionar a classe do contrato limpa os campos:
         * Combo: Tipo do Contrato
         **/
        jQuery('#eeiprdoid, #eeiversao').html("<option value=''>Selecione</option>");
        jQuery('#eeiprdoid, #eeiversao').val('');
            
        if(jQuery(this).val() == "") {
            jQuery('#mensagens_carregamento_combos').fadeOut();
            return false;            
        }      

        var tipoProduto = jQuery(this).val();

        jQuery.ajax({
            url: 'cad_equivalencia_equipamentos.php',
            type: 'post',
            dataType: 'json',
            data: {
                acao: 'carregarProdutos',
                eeitipo: tipoProduto
            },
            beforeSend: function() {                    
                jQuery('#loading-cadastro-produtos').mostrarCarregando();
                jQuery('#mensagens_carregamento_combos').fadeOut();
            },
            success: function(data) {
                var options = "<option value=''>Selecione</option>";
                jQuery.each(data,function(i,val){
                    options += "<option class='opcao' value='"+ val.id +"'>"+ val.label +"</option>";
                });
                jQuery('#eeiprdoid').html(options);
            },
            complete: function() {                    
                jQuery('#loading-cadastro-produtos').esconderCarregando();
            },
            error: function() {
                //jQuery('#mensagens_carregamento_combos').html('Houve falha na comunicação com o servidor.').fadeIn();
            }
        });        
        
    });
    
    
    //busca de versões ao selecionar o produto
    jQuery('#eeiprdoid').change(function(){
                
        /**
         * Se não selecionar a classe do contrato limpa os campos:
         * Combo: Tipo do Contrato
         **/
        jQuery('#eeiversao').html("<option value=''>Selecione</option>");
        jQuery('#eeiversao').val('');
            
        if(jQuery(this).val() == "") {
            jQuery('#mensagens_carregamento_combos').fadeOut();
            return false;            
        }      

        var tipoProduto = jQuery('#eeitipo').val();
        var produto = jQuery('#eeiprdoid').val();

        jQuery.ajax({
            url: 'cad_equivalencia_equipamentos.php',
            type: 'post',
            dataType: 'json',
            data: {
                acao: 'carregarVersoes',
                tipo: tipoProduto,
                prdoid: produto
            },
            beforeSend: function() {                    
                jQuery('#loading-cadastro-versoes').mostrarCarregando();
                jQuery('#mensagens_carregamento_combos').fadeOut();
            },
            success: function(data) {
                var options = "<option value=''>Selecione</option>";
                jQuery.each(data,function(i,val){
                    options += "<option class='opcao' value='"+ val.label +"'>"+ val.label +"</option>";
                });
                jQuery('#eeiversao').html(options);
            },
            complete: function() {                    
                jQuery('#loading-cadastro-versoes').esconderCarregando();
            },
            error: function() {
                //jQuery('#mensagens_carregamento_combos').html('Houve falha na comunicação com o servidor.').fadeIn();
            }
        });        
        
    });
    
    /**
     * Eventos para cópia da Classe
     **/
    jQuery('#bt_copiar').click(function(){
        
        jQuery('#mensagens_carregamento_combos').fadeOut();
        jQuery('.mensagem').not('.info').fadeOut();
        resetFormErros();
        jQuery('#eeqeqcoid_copia, #eeqtpcoid_copia').html("<option value=''>Selecione</option>");
        jQuery('#eqqmodalidade_copia').val('');
        jQuery('#eeqeqcoid_copia').val('');
        jQuery('#eeqtpcoid_copia').val('');
        
        if(jQuery('.bloco_copia').css('display') == 'none') {
            jQuery('.bloco_copia').fadeIn();
        } else {
            jQuery('.bloco_copia').fadeOut();
        }
        
    });
    
    jQuery('#bt_cancelar_copia').click(function(){
        
        jQuery('#mensagens_carregamento_combos').fadeOut();
        jQuery('.mensagem').not('.info').fadeOut();
        resetFormErros();
        jQuery('#eeqeqcoid_copia, #eeqtpcoid_copia').html("<option value=''>Selecione</option>");
        jQuery('#eqqmodalidade_copia').val('');
        jQuery('#eeqeqcoid_copia').val('');
        jQuery('#eeqtpcoid_copia').val('');
        
        jQuery('.bloco_copia').fadeOut();
        
    });
    
    jQuery('#eqqmodalidade_copia').change(function(e,params){
                
       
        var classeSelecionada = params != undefined && params.id != '' ? params.id : ''; 
        
        /**
         * Se não selecionar a modalidade limpa os campos:
         * Combo: Classe do Contrato
         * Combo: Tipo do Contrato
         **/
        if(jQuery(this).val() == "") {            
            jQuery('#mensagens_carregamento_combos').fadeOut();
            jQuery('#eeqeqcoid_copia, #eeqtpcoid_copia').html("<option value=''>Selecione</option>");
            jQuery('#eeqeqcoid_copia').val('');
            jQuery('#eeqtpcoid_copia').val('');
            
            return false;            
        }        

        var modalidade = jQuery(this).val();

        jQuery.ajax({
            url: 'cad_equivalencia_equipamentos.php',
            type: 'post',
            dataType: 'json',
            data: {
                acao: 'carregarClassesContratos',
                eqqmodalidade: modalidade,
                copia: true
            },
            beforeSend: function() {                    
                jQuery('#loading-classe-contrato-copia').mostrarCarregando();
                jQuery('#mensagens_carregamento_combos').fadeOut();
                jQuery('#eeqeqcoid_copia, #eeqtpcoid_copia').html("<option value=''>Selecione</option>");
            },
            success: function(data) {
                var options = "<option value=''>Selecione</option>";
                jQuery.each(data,function(i,val){
                    if (classeSelecionada != '' && classeSelecionada == val.id) {
                        options += "<option selected='selected' value='"+ val.id +"'>"+ val.label +"</option>";
                    } else {
                        options += "<option value='"+ val.id +"'>"+ val.label +"</option>";
                    }
                    
                });
                jQuery('#eeqeqcoid_copia').html(options);
            },
            complete: function() {                    
                jQuery('#loading-classe-contrato-copia').esconderCarregando();
            },
            error: function() {
                //jQuery('#mensagens_carregamento_combos').html('Houve falha na comunicação com o servidor.').fadeIn();
            }
        });        
        
    });
    
    
    jQuery('#eeqeqcoid_copia').change(function(e,params){
        
        var tipoContratoSelecionado = params != undefined && params.id != '' ? params.id : ''; 
        var classeContratoSelecionado = params != undefined && params.idClasseContrato != '' ? params.idClasseContrato : '';

        /**
         * Se não selecionar a classe do contrato limpa os campos:
         * Combo: Tipo do Contrato
         **/
        if(jQuery(this).val() == "" && classeContratoSelecionado == "") {            
            jQuery('#mensagens_carregamento_combos').fadeOut();
            jQuery('#eeqtpcoid_copia').html("<option value=''>Selecione</option>");
            jQuery('#eeqtpcoid_copia').val('');
            return false;            
        }        

        var modalidade = jQuery('#eqqmodalidade_copia').val();
        
        var classeContrato = "";
        if (jQuery("#eeqeqcoid_copia").val() != '') {
            classeContrato = jQuery("#eeqeqcoid_copia").val();
        } else if (classeContratoSelecionado != '') {
            classeContrato = classeContratoSelecionado;
        }
            
        jQuery.ajax({
            url: 'cad_equivalencia_equipamentos.php',
            type: 'post',
            dataType: 'json',
            data: {
                acao: 'carregarTiposContratos',
                eqqmodalidade: modalidade,
                eeqeqcoid: classeContrato,
                copia: true
            },
            beforeSend: function() {                    
                jQuery('#loading-tipo-contrato-copia').mostrarCarregando();
                jQuery('#mensagens_carregamento_combos').fadeOut();
                jQuery('#eeqtpcoid_copia').html("<option value=''>Selecione</option>");
            },
            success: function(data) {
                var options = "<option value=''>Selecione</option>";
                jQuery.each(data,function(i,val){
                    if (tipoContratoSelecionado != '' && tipoContratoSelecionado === val.id ) {
                        options += "<option selected='selected' value='"+ val.id +"'>"+ val.label +"</option>";
                    }else{
                        options += "<option value='"+ val.id +"'>"+ val.label +"</option>";
                    }
                    
                });
                jQuery('#eeqtpcoid_copia').html(options);
            },
            complete: function() {                    
                jQuery('#loading-tipo-contrato-copia').esconderCarregando();
            },
            error: function() {
                //jQuery('#mensagens_carregamento_combos').html('Houve falha na comunicação com o servidor.').fadeIn();
            }
        });        
        
    });

    //parametros da pesquisa, para popular o form dinamicamente

    if (typeof(parametroModalidadeContrato) != "undefined" && parametroModalidadeContrato !== null) {
        parametroModalidadeContrato = jQuery.trim(parametroModalidadeContrato);
    }else{
        parametroModalidadeContrato = '';
    }

    if (typeof(parametroPesquisaClasseContrato) != "undefined" && parametroPesquisaClasseContrato !== null) {
        parametroPesquisaClasseContrato = jQuery.trim(parametroPesquisaClasseContrato);
    }else{
        parametroPesquisaClasseContrato = '';
    }

    if (typeof(parametroPesquisaTipoContrato) != "undefined" && parametroPesquisaTipoContrato !== null) {
        parametroPesquisaTipoContrato = jQuery.trim(parametroPesquisaTipoContrato);
    }else{
        parametroPesquisaTipoContrato = '';
    }
    

    

    if ( jQuery.trim(parametroModalidadeContrato) != '' ) {
        
        //se selecionar opção  desabilita o checkbox de  "Classes sem cadastro"
        jQuery('#classes_sem_cadastro').attr('disabled','disabled');
            
        var modalidade = parametroModalidadeContrato;

            jQuery.ajax({
                url: 'cad_equivalencia_equipamentos.php',
                type: 'post',
                dataType: 'json',
                data: {
                    acao: 'carregarClassesContratos',
                    eqqmodalidade: modalidade
                },
                beforeSend: function() {                    
                    jQuery('#loading-classe-contrato').mostrarCarregando();
                    jQuery('#mensagens_carregamento_combos').fadeOut();

                },
                success: function(data) {
                    var options = "<option value=''>Selecione</option>";
                    jQuery.each(data,function(i,val){
                        if (parametroPesquisaClasseContrato != '' && parametroPesquisaClasseContrato === val.id) {
                            options += "<option class='opcao' selected='selected' value='"+ val.id +"'>"+ val.label +"</option>";
                        }else{
                            options += "<option class='opcao' value='"+ val.id +"'>"+ val.label +"</option>";
                        }

                    });
                    jQuery('#eeqeqcoid').html(options);
                },
                complete: function() {                    
                    jQuery('#loading-classe-contrato').esconderCarregando();
                },
                error: function() {
                    //jQuery('#mensagens_carregamento_combos').html('Houve falha na comunicação com o servidor.').fadeIn();
                }
            });

    }

    if ( jQuery.trim(parametroPesquisaClasseContrato) != '' ) {

            var modalidade2 = parametroModalidadeContrato;
            var classeContrato = parametroPesquisaClasseContrato;

            jQuery.ajax({
                url: 'cad_equivalencia_equipamentos.php',
                type: 'post',
                dataType: 'json',
                data: {
                    acao: 'carregarTiposContratos',
                    eqqmodalidade: modalidade2,
                    eeqeqcoid: classeContrato
                },
                beforeSend: function() {                    
                    jQuery('#loading-tipo-contrato').mostrarCarregando();
                    jQuery('#mensagens_carregamento_combos').fadeOut();
                },
                success: function(data) {
                    var options = "<option value=''>Selecione</option>";
                    jQuery.each(data,function(i,val){
                        if (parametroPesquisaTipoContrato != '' && parametroPesquisaTipoContrato === val.id) {
                            options += "<option class='opcao' selected='selected' value='"+ val.id +"'>"+ val.label +"</option>";
                        } else {
                            options += "<option class='opcao' value='"+ val.id +"'>"+ val.label +"</option>";
                        }

                    });
                    jQuery('#eeqtpcoid').html(options);
                },
                complete: function() {                    
                    jQuery('#loading-tipo-contrato').esconderCarregando();
                },
                error: function() {
                    //jQuery('#mensagens_carregamento_combos').html('Houve falha na comunicação com o servidor.').fadeIn();
                }
            });
    }
    
    // variaveis do cadastro do produto
    if (typeof(parametroCadastroTipoProduto) != "undefined" && parametroCadastroTipoProduto !== null) {
        parametroCadastroTipoProduto = jQuery.trim(parametroCadastroTipoProduto);
    }else{
        parametroCadastroTipoProduto = '';
    }
    
    if (typeof(parametroCadastroProduto) != "undefined" && parametroCadastroProduto !== null) {
        parametroCadastroProduto = jQuery.trim(parametroCadastroProduto);
    }else{
        parametroCadastroProduto = '';
    }
    
    if (typeof(parametroCadastroVersoes) != "undefined" && parametroCadastroVersoes !== null) {
        parametroCadastroVersoes = jQuery.trim(parametroCadastroVersoes);
    }else{
        parametroCadastroVersoes = '';
    }
    
    
    if ( jQuery.trim(parametroCadastroTipoProduto) != '' ) {

        var tipoProduto = parametroCadastroTipoProduto;

        jQuery.ajax({
            url: 'cad_equivalencia_equipamentos.php',
            type: 'post',
            dataType: 'json',
            data: {
                acao: 'carregarProdutos',
                eeitipo: tipoProduto
            },
            beforeSend: function() {                    
                jQuery('#loading-cadastro-produtos').mostrarCarregando();
                jQuery('#mensagens_carregamento_combos').fadeOut();
            },
            success: function(data) {
                var options = "<option value=''>Selecione</option>";
                jQuery.each(data,function(i,val){
                    if (parametroCadastroProduto != '' && parametroCadastroProduto == val.id) {
                        options += "<option selected = 'selected' value='"+ val.id +"'>"+ val.label +"</option>";
                    }else{
                        options += "<option value='"+ val.id +"'>"+ val.label +"</option>";
                    }
                    
                });
                jQuery('#eeiprdoid').html(options);
            },
            complete: function() {                    
                jQuery('#loading-cadastro-produtos').esconderCarregando();
            },
            error: function() {
                //jQuery('#mensagens_carregamento_combos').html('Houve falha na comunicação com o servidor.').fadeIn();
            }
        });
    }
    
    
    if (parametroCadastroProduto != '') {
        var tipoProduto2 = parametroCadastroTipoProduto;
        var produto = parametroCadastroProduto;

        jQuery.ajax({
            url: 'cad_equivalencia_equipamentos.php',
            type: 'post',
            dataType: 'json',
            data: {
                acao: 'carregarVersoes',
                tipo: tipoProduto,
                prdoid: produto
            },
            beforeSend: function() {                    
                jQuery('#loading-cadastro-versoes').mostrarCarregando();
                jQuery('#mensagens_carregamento_combos').fadeOut();
            },
            success: function(data) {
                var options = "<option value=''>Selecione</option>";
                jQuery.each(data,function(i,val){
                    if (parametroCadastroVersoes != '' && parametroCadastroVersoes == val.id) {
                        options += "<option selected='selected' value='"+ val.id +"'>"+ val.label +"</option>";
                    } else {
                        options += "<option value='"+ val.label +"'>"+ val.label +"</option>";
                    }
                    
                });
                jQuery('#eeiversao').html(options);
            },
            complete: function() {                    
                jQuery('#loading-cadastro-versoes').esconderCarregando();
            },
            error: function() {
                //jQuery('#mensagens_carregamento_combos').html('Houve falha na comunicação com o servidor.').fadeIn();
            }
        }); 
    }
    
    
    
    
    //para trazer a cópia de classe já selecionada
    //parametros da pesquisa, para popular o form dinamicamente

    if (typeof(parametroModalidadeContrato_copia) != "undefined" && parametroModalidadeContrato_copia !== null) {
        parametroModalidadeContrato_copia = jQuery.trim(parametroModalidadeContrato_copia);
    }else{
        parametroModalidadeContrato_copia = '';
    }

    if (typeof(parametroPesquisaClasseContrato_copia) != "undefined" && parametroPesquisaClasseContrato_copia !== null) {
        parametroPesquisaClasseContrato_copia = jQuery.trim(parametroPesquisaClasseContrato_copia);
    }else{
        parametroPesquisaClasseContrato_copia = '';
    }

    if (typeof(parametroPesquisaTipoContrato_copia) != "undefined" && parametroPesquisaTipoContrato_copia !== null) {
        parametroPesquisaTipoContrato_copia = jQuery.trim(parametroPesquisaTipoContrato_copia);
    }else{
        parametroPesquisaTipoContrato_copia = '';
    }
    
    
    if (parametroModalidadeContrato_copia != '') {
        jQuery('#eqqmodalidade_copia').trigger('change',{id:parametroPesquisaClasseContrato_copia});
    }
    
    
    if (parametroPesquisaClasseContrato_copia != '') {
        jQuery('#eeqeqcoid_copia').trigger('change',{id:parametroPesquisaTipoContrato_copia,idClasseContrato:parametroPesquisaClasseContrato_copia});
    }
    
    


    if (typeof(formErros) != "undefined" && formErros !== null) {
        formErros = jQuery.trim(formErros);
    }else{
        formErros = '';
    }

    //faz o erros dos formularios
    if (jQuery.trim(formErros) != '') {
        formErros = jQuery.parseJSON(formErros);
        showFormErros(formErros);
    }

});


jQuery.fn.limpaMensagens = function() {
    jQuery('.mensagem').not('.info').hideMessage();
    //jQuery('.mensagem').not('.info').remove();
    jQuery(".erro").not('.mensagem').removeClass("erro");
    resetFormErros();
}