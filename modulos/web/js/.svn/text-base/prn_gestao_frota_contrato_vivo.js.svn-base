jQuery(document).ready(function(){

    if (jQuery('#tokenVIVO').val() != '') {

        var clioid = jQuery('#idCliente-vivo').val();
        dispararAjaxBuscaCliente(clioid, 'razao_social');
        
    }
    
	if (jQuery('#tokenVIVO').length > 0) {

		if (jQuery('#tokenVIVO').val() != '') {

		var clioid = jQuery('#idCliente-vivo').val();
		dispararAjaxBuscaCliente(clioid, 'razao_social');

		}
	}
    
    /**
     * Validações e mascaras
     **/
    jQuery('#cpf').mask('999.999.999-99');
    jQuery('#cnpj').mask('99.999.999/9999-99');
    
    jQuery('#numero_os').mask('9?99999999999', {placeholder: ''});
    
    jQuery('input[name="tipo_pessoa"]').change(function(){
      	
        //LIMPA TODOS OS CAMPOS
        jQuery('#razao_social, #cnpj, #nome, #cpf, #idvivo, #endereco').val('');
        jQuery('#pessoa_autorizada_nome, #pessoa_autorizada_telefone').val('');
        jQuery('#pessoa_emergencia_nome_1, #pessoa_emergencia_telefone_1, #pessoa_emergencia_nome_2, #pessoa_emergencia_telefone_2').val('');
    
        var tipo_pessoa = jQuery(this).val();
        
        jQuery('.ui-helper-hidden-accessible').html('');
        
        if(tipo_pessoa == 'J') {
            jQuery('.cpf, .nome').addClass('invisivel').not('label').val('');
            jQuery('.cnpj, .razao_social').removeClass('invisivel');
        } else {
            jQuery('.cnpj, .razao_social').addClass('invisivel').not('label').val('');
            jQuery('.cpf, .nome').removeClass('invisivel');
        }
                
        jQuery( "#idvivo" ).autocomplete("destroy");
        
        jQuery( "#idvivo" ).autocomplete({
            source: "prn_gestao_frota_contrato_vivo.php?acao=buscarDinamicamente&filtro=" + tipo_pessoa,
            minLength: 2,            
            response: function(event, ui ) {            
                
                jQuery('#msg_alerta').hide();
                
                if(!ui.content.length) {
                    jQuery('#msg_alerta').html(jQuery(this).val() + ' não consta no cadastro.').fadeIn();
                }

                jQuery(this).autocomplete("option", {
                    messages: {
                        noResults: '',
                        results: function() {}
                    }
                });   
                
            },
            select: function( event, ui ) {
                dispararAjaxBuscaCliente(ui.item.id, tipo_pessoa);
            }
        });
        
    });
    
    jQuery( "#razao_social" ).autocomplete({
        source: "prn_gestao_frota_contrato_vivo.php?acao=buscarDinamicamente&filtro=razao_social",
        minLength: 2,        
        response: function(event, ui ) {            
                  
            jQuery('#msg_alerta').hide();
                  
            if(!ui.content.length && jQuery.trim(jQuery(this).val()) != "") {
                jQuery('#msg_alerta').html(_escape(jQuery(this).val()) + ' não consta no cadastro.').fadeIn();
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
            dispararAjaxBuscaCliente(ui.item.id, 'razao_social');
        }        
    });
    
    jQuery( "#nome" ).autocomplete({
        source: "prn_gestao_frota_contrato_vivo.php?acao=buscarDinamicamente&filtro=nome",
        minLength: 2,       
        response: function(event, ui ) {  
            
            jQuery('#msg_alerta').hide();
            
            if(!ui.content.length && jQuery.trim(jQuery(this).val()) != "") {
                jQuery('#msg_alerta').html(_escape(jQuery(this).val()) + ' não consta no cadastro.').fadeIn();
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
            dispararAjaxBuscaCliente(ui.item.id, 'nome');
        }
    });
    
    jQuery( "#cpf" ).autocomplete({
        source: "prn_gestao_frota_contrato_vivo.php?acao=buscarDinamicamente&filtro=cpf",
        minLength: 2,        
        response: function(event, ui ) {    
            
            jQuery('#msg_alerta').hide();
            
            if(!ui.content.length && !jQuery.trim(jQuery(this).val().replace(/[^0-9]+/g, '')) == "") {
                jQuery('#msg_alerta').html(jQuery(this).val().replace(/[^0-9]+/g, '') + ' solicitado não consta no cadastro.').fadeIn();
            }
            
            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {}
                }
            });   
           
        },
        select: function( event, ui ) {
            dispararAjaxBuscaCliente(ui.item.id, 'cpf');
        }
    });    
    
    jQuery( "#cnpj" ).autocomplete({
        source: "prn_gestao_frota_contrato_vivo.php?acao=buscarDinamicamente&filtro=cnpj",
        minLength: 2,        
        response: function(event, ui ) {            
            
            jQuery('#msg_alerta').hide();
            
            if(!ui.content.length && !jQuery.trim(jQuery(this).val().replace(/[^0-9]+/g, '')) == "") {
                jQuery('#msg_alerta').html(jQuery(this).val().replace(/[^0-9]+/g, '') + ' solicitado não consta no cadastro.').fadeIn();
            }
            
            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {}
                }
            });   
            
        },
        select: function( event, ui ) {
            dispararAjaxBuscaCliente(ui.item.id, 'cnpj');
        }
    });
    
    jQuery( "#idvivo" ).autocomplete({
        source: "prn_gestao_frota_contrato_vivo.php?acao=buscarDinamicamente&filtro=" + jQuery('input[name="tipo_pessoa"]').val(),
        minLength: 2,        
        response: function(event, ui ) {            
           
           jQuery('#msg_alerta').hide();
           
           if(!ui.content.length) {
                jQuery('#msg_alerta').html(jQuery(this).val() + ' não consta no cadastro.').fadeIn();
            }
            
            jQuery(this).autocomplete("option", {
                messages: {
                    noResults: '',
                    results: function() {}
                }
            });   
           
        },
        select: function( event, ui ) {
            dispararAjaxBuscaCliente(ui.item.id, jQuery('input[name="tipo_pessoa"]').val());
        }
    });
    
    jQuery('#razao_social, #cnpj, #nome, #cpf, #idvivo').keyup(function(e){
        
        var tecla = e.which;
        var id = jQuery(this).attr('id');
                
        //BACKSPACE
        if(tecla == 8) {
            //LIMPA TODOS OS CAMPOS, MENOS O QUE ESTÁ SENDO APAGADO
            jQuery('#razao_social, #cnpj, #nome, #cpf, #idvivo, #endereco').not('#' +id).val('');
            jQuery('#pessoa_autorizada_nome, #pessoa_autorizada_telefone').val('');
            jQuery('#pessoa_emergencia_nome_1, #pessoa_emergencia_telefone_1, #pessoa_emergencia_nome_2, #pessoa_emergencia_telefone_2').val('');
            jQuery('#qtd_veiculos_ativos, #qtd_os').html('');
            jQuery('#clioid_hidden').val('');
            jQuery('#resultado_pesquisa_hidden').val('');
            jQuery('#resultado_pesquisa_os_hidden').val('');
        }
        
    });
    
    
    //--------------- TABELA DE VEÍCULOS
    
    jQuery('#exibir_veiculos').click(function(){
        
        if(jQuery('#clioid_hidden').val() == ""){
            jQuery('#tabela_veiculos_separador').show();
            jQuery('#alerta_veiculos').html('É necessário ter pesquisado um cliente previamente.').fadeIn();            
            return false;
        } 
        
        if(jQuery('#resultado_pesquisa_hidden').val() == '0'){
            jQuery('#tabela_veiculos_separador').show();
            jQuery('#alerta_veiculos').hide();
            jQuery('#alerta_veiculos').html('Nenhum registro encontrado.').fadeIn();            
            return false;
        }
        
    });
    
    jQuery('#btn_pesquisar_veiculos_nova_aba').click(function(){
        
        if(jQuery('#clioid_hidden').val() == ""){
            jQuery('#tabela_veiculos_separador').show();
            jQuery('#alerta_veiculos').html('É necessário ter pesquisado um cliente previamente.').fadeIn();            
            return false;
        } 
        
        if(jQuery('#resultado_pesquisa_hidden').val() == '0'){
            jQuery('#tabela_veiculos_separador').show();
            jQuery('#alerta_veiculos').hide();
            jQuery('#alerta_veiculos').html('Nenhum registro encontrado.').fadeIn();            
            return false;
        }
        
        window.open("prn_gestao_frota_contrato_vivo.php?acao=pesquisarVeiculos&clioid=" + jQuery('#clioid_hidden').val() + "&placa=" +jQuery('#placa').val(),"_blank");
        
        jQuery('#placa').val('');
        
    }); 
    
    jQuery('#btn_pesquisar_veiculos_idvivo_nova_aba').click(function(){
        
        if(jQuery('#clioid_hidden').val() == ""){
            jQuery('#tabela_veiculos_separador').show();
            jQuery('#alerta_veiculos').html('É necessário ter pesquisado um cliente previamente.').fadeIn();            
            return false;
        } 
        
        if(jQuery('#resultado_pesquisa_hidden').val() == '0'){
            jQuery('#tabela_veiculos_separador').show();
            jQuery('#alerta_veiculos').hide();
            jQuery('#alerta_veiculos').html('Nenhum registro encontrado.').fadeIn();            
            return false;
        }
        
        window.open("prn_gestao_frota_contrato_vivo.php?acao=pesquisarVeiculos&clioid=" + jQuery('#clioid_hidden').val() + "&idvivo_veiculo=" +jQuery('#idvivo_veiculo').val(),"_blank");
        
        jQuery('#idvivo_veiculo').val('');
    }); 
    
    jQuery('#btn_pesquisar_veiculos').click(function(){
        jQuery.ajax({
            url: 'prn_gestao_frota_contrato_vivo.php',
            type: 'post',
            dataType: 'json',
            data: {
                acao: 'pesquisaVeiculoPlaca',
                clioid: jQuery('#clioid_hidden').val(),
                placa: jQuery('#placa').val()
            },
            beforeSend: function() {
                jQuery('#alerta_veiculos').hide();          
                jQuery('#loader_veiculos').fadeIn();
                jQuery('#tabela_veiculos').hide();
                jQuery('#conteudo_veiculos').html('');  
                jQuery('#tabela_veiculos_separador').show();
            },
            success: function(data) {

                if(jQuery('#clioid_hidden').val() == ""){
                    jQuery('#alerta_veiculos').html('É necessário ter pesquisado um cliente previamente.').fadeIn();  
                    jQuery('#resultado_pesquisa_hidden').val('0');
                    return false;
                }

                if(data.error != undefined) {
                    jQuery('#alerta_veiculos').html(data.message).fadeIn();
                    jQuery('#resultado_pesquisa_hidden').val('0');
                    return false;
                }

                jQuery.each(data.resultados, function(i, veiculo){

                    var tds = '';

                    if((veiculo.idvivo == "")||(veiculo.idvivo == null)){
                    	tds += '<td></td>';
                    } else {
                    	tds += '<td>' + veiculo.idvivo + '</td>';	
                    }
                    tds += '<td class="direita"><a class="placa_veiculo" href="prn_gestao_frota_contrato_vivo.php?acao=carregarDadosHistoricoContatos&connumero=' + veiculo.connumero + '" target="_blank">' + veiculo.placa + '</a></td>';
                    tds += '<td>' + veiculo.status + '</td>';
                    tds += '<td>' + veiculo.descricao + '</td>';
                    tds += '<td class="direita">' + veiculo.valorServico + '</td>'; 
                    
                    if(veiculo.parcela == ""){
                    	 tds += '<td></td>';
                    }else if(veiculo.parcela.length == 1){
                    	
                    	if(veiculo.totalParcelas.length == 1){
                    		tds += '<td>0' + veiculo.parcela + '/0' + veiculo.totalParcelas +'</td>';
                    	}else{
                    		tds += '<td>0' + veiculo.parcela + '/' + veiculo.totalParcelas +'</td>';
                    	}
                    	
                    }else{
                    	tds += '<td>' + veiculo.parcela + '/' + veiculo.totalParcelas +'</td>';
                    }
                    
                    tds += '<td class="direita">' + veiculo.valorParcela + '</td>';
                    tds += '<td class="centro">' + veiculo.dataInicio + '</td>';
                    tds += '<td class="centro">' + veiculo.dataFim + '</td>';
                    tds += '<td>' + veiculo.tempoContrato + '</td>';

                    jQuery('#conteudo_veiculos').append('<tr>' + tds + '</tr>');

                });

                jQuery('#conteudo_veiculos tr:even').addClass('par');

                jQuery('#tabela_veiculos').fadeIn();

                jQuery('#resultado_pesquisa_hidden').val(data.resultados.length);

            },
            complete: function() {
                jQuery('#loader_veiculos').hide();
            }
     }); 
    });    
    
    jQuery('#btn_pesquisar_veiculos_idvivo').click(function(){
        jQuery.ajax({
            url: 'prn_gestao_frota_contrato_vivo.php',
            type: 'post',
            dataType: 'json',
            data: {
                acao: 'pesquisaVeiculoIdVivo',
                clioid: jQuery('#clioid_hidden').val(),
                idvivo_veiculo: jQuery('#idvivo_veiculo').val()
            },
            beforeSend: function() {
                jQuery('#alerta_veiculos').hide();          
                jQuery('#loader_veiculos').fadeIn();
                jQuery('#tabela_veiculos').hide();
                jQuery('#conteudo_veiculos').html('');  
                jQuery('#tabela_veiculos_separador').show();
            },
            success: function(data) {

                if(jQuery('#clioid_hidden').val() == ""){
                    jQuery('#alerta_veiculos').html('É necessário ter pesquisado um cliente previamente.').fadeIn();  
                    jQuery('#resultado_pesquisa_hidden').val('0');
                    return false;
                }

                if(data.error != undefined) {
                    jQuery('#alerta_veiculos').html(data.message).fadeIn();
                    jQuery('#resultado_pesquisa_hidden').val('0');
                    return false;
                }

                jQuery.each(data.resultados, function(i, veiculo){

                    var tds = '';
                    
                    if((veiculo.idvivo == "")||(veiculo.idvivo == null)){
                    	tds += '<td></td>';
                    } else {
                    	tds += '<td>' + veiculo.idvivo + '</td>';	
                    }
                    tds += '<td class="direita"><a class="placa_veiculo" href="prn_gestao_frota_contrato_vivo.php?acao=carregarDadosHistoricoContatos&connumero=' + veiculo.connumero + '" target="_blank">' + veiculo.placa + '</a></td>';
                    tds += '<td>' + veiculo.status + '</td>';
                    tds += '<td>' + veiculo.descricao + '</td>';
                    tds += '<td class="direita">' + veiculo.valorServico + '</td>'; 
                    
                    if(veiculo.parcela == ""){
                    	 tds += '<td></td>';
                    }else if(veiculo.parcela.length == 1){
                    	
                    	if(veiculo.totalParcelas.length == 1){
                    		tds += '<td>0' + veiculo.parcela + '/0' + veiculo.totalParcelas +'</td>';
                    	}else{
                    		tds += '<td>0' + veiculo.parcela + '/' + veiculo.totalParcelas +'</td>';
                    	}
                    	
                    }else{
                    	tds += '<td>' + veiculo.parcela + '/' + veiculo.totalParcelas +'</td>';
                    }
                    
                    tds += '<td class="direita">' + veiculo.valorParcela + '</td>';
                    tds += '<td class="centro">' + veiculo.dataInicio + '</td>';
                    tds += '<td class="centro">' + veiculo.dataFim + '</td>';
                    tds += '<td>' + veiculo.tempoContrato + '</td>';

                    jQuery('#conteudo_veiculos').append('<tr>' + tds + '</tr>');

                });

                jQuery('#conteudo_veiculos tr:even').addClass('par');

                jQuery('#tabela_veiculos').fadeIn();

                jQuery('#resultado_pesquisa_hidden').val(data.resultados.length);

            },
            complete: function() {
                jQuery('#loader_veiculos').hide();
            }
     }); 
    });    
    //--------------- TABELA DE OS
    
    jQuery('#exibir_grid_os').click(function(){
        
        if(jQuery('#clioid_hidden').val() == ""){
            jQuery('#tabela_os_separador').show();
            jQuery('#alerta_grid_os').html('É necessário ter pesquisado um cliente previamente.').fadeIn();            
            return false;
        } 
        
        if(jQuery('#resultado_pesquisa_os_hidden').val() == '0'){
            jQuery('#tabela_os_separador').show();
            jQuery('#alerta_grid_os').hide();
            jQuery('#alerta_grid_os').html('Nenhum registro encontrado.').fadeIn();            
            return false;
        }
        
    });
    
    jQuery('#btn_pesquisar_os_nova_aba').click(function(){
        
        if(jQuery('#clioid_hidden').val() == ""){
            jQuery('#tabela_os_separador').show();
            jQuery('#alerta_grid_os').html('É necessário ter pesquisado um cliente previamente.').fadeIn();            
            return false;
        } 
        
        if(jQuery('#resultado_pesquisa_os_hidden').val() == '0'){
            jQuery('#tabela_os_separador').show();
            jQuery('#alerta_grid_os').hide();
            jQuery('#alerta_grid_os').html('Nenhum registro encontrado.').fadeIn();            
            return false;
        }
        
        window.open("prn_gestao_frota_contrato_vivo.php?acao=pesquisarOrdemServico&clioid=" + jQuery('#clioid_hidden').val() + "&numero_os=" +jQuery('#numero_os').val(),"_blank");
        
        jQuery('#numero_os').val('');
        
    }); 
    
    jQuery('#btn_pesquisar_os_idvivo_nova_aba').click(function(){
        
        if(jQuery('#clioid_hidden').val() == ""){
            jQuery('#tabela_os_separador').show();
            jQuery('#alerta_grid_os').html('É necessário ter pesquisado um cliente previamente.').fadeIn();            
            return false;
        } 
        
        if(jQuery('#resultado_pesquisa_os_hidden').val() == '0'){
            jQuery('#tabela_os_separador').show();
            jQuery('#alerta_grid_os').hide();
            jQuery('#alerta_grid_os').html('Nenhum registro encontrado.').fadeIn();            
            return false;
        }
        
        window.open("prn_gestao_frota_contrato_vivo.php?acao=pesquisarOrdemServico&clioid=" + jQuery('#clioid_hidden').val() + "&idvivo_os=" +jQuery('#idvivo_os').val(),"_blank");
        
        jQuery('#idvivo_os').val('');
        
    }); 
    
    jQuery('#btn_pesquisar_os').click(function(){
        jQuery.ajax({
            url: 'prn_gestao_frota_contrato_vivo.php',
            type: 'post',
            dataType: 'json',
            data: {
                acao: 'carregarOrdemServico',
                clioid: jQuery('#clioid_hidden').val(),
                numero_os: jQuery('#numero_os').val()
            },
            beforeSend: function() {                                
                jQuery('#alerta_grid_os').hide();
                jQuery('#loader_grid_os').fadeIn();
                jQuery('#tabela_os').hide();
                jQuery('#conteudo_grid_os').html('');   
                jQuery('#tabela_os_separador').show();
            },
            success: function(data) {

                if(jQuery('#clioid_hidden').val() == ""){
                    jQuery('#alerta_grid_os').html('É necessário ter pesquisado um cliente previamente.').fadeIn();  
                    jQuery('#resultado_pesquisa_os_hidden').val('0');
                    return false;
                }

                if(data.error != undefined) {
                    jQuery('#alerta_grid_os').html(data.message).fadeIn();
                    jQuery('#resultado_pesquisa_os_hidden').val('0');
                    return false;
                }

                jQuery.each(data.resultados, function(i, os){

                    var tds = '';
                    if((os.idvivo == "")||(os.idvivo == null)){
                    	tds += '<td></td>';
                    } else {
                    	tds += '<td>' + os.idvivo + '</td>';	
                    }
                    tds += '<td class="direita"><a class="placa_veiculo" href="prn_gestao_frota_contrato_vivo.php?acao=carregarDadosHistoricoContatos&connumero=' + os.connumero + '" target="_blank">' + os.placa + '</a></td>';
                    tds += '<td class="direita">' + os.protocolo_vivo + '</td>'; 
                    tds += '<td class="direita">' + os.protocolo_sascar + '</td>'; 
                    tds += '<td class="direita"><a class="placa_veiculo" href="prn_gestao_frota_contrato_vivo.php?acao=carregarDadosHistoricoOrdemServico&ordoid=' + os.ordem_servico + '" target="_blank">' + os.ordem_servico + '</a></td>';
                    tds += '<td>' + os.motivo + '</td>'; 
                    tds += '<td>' + os.status + '</td>'; 
                    tds += '<td>' + os.ultima_acao + '</td>'; 
                    tds += '<td>' + os.defeito_alegado + '</td>'; 
                    tds += '<td class="centro">' + os.data_abertura + '</td>'; 
                    tds += '<td class="centro">' + os.data_encerramento + '</td>';
                    tds += '<td>' + os.tempo_conclusao + '</td>';
                    tds += '<td>' + os.atendente + '</td>';

                    jQuery('#conteudo_grid_os').append('<tr>' + tds + '</tr>');

                });

                jQuery('#conteudo_grid_os tr:even').addClass('par');

                jQuery('#tabela_os').fadeIn();

                jQuery('#resultado_pesquisa_os_hidden').val(data.resultados.length);

            },
            complete: function() {
                jQuery('#loader_grid_os').hide();
            }
     }); 
    });    
    
    jQuery('#btn_pesquisar_os_idvivo').click(function(){
        jQuery.ajax({
            url: 'prn_gestao_frota_contrato_vivo.php',
            type: 'post',
            dataType: 'json',
            data: {
                acao: 'carregarOrdemServicoIdVivo',
                clioid: jQuery('#clioid_hidden').val(),
                idvivo_os: jQuery('#idvivo_os').val()
            },
            beforeSend: function() {                                
                jQuery('#alerta_grid_os').hide();
                jQuery('#loader_grid_os').fadeIn();
                jQuery('#tabela_os').hide();
                jQuery('#conteudo_grid_os').html('');   
                jQuery('#tabela_os_separador').show();
            },
            success: function(data) {

                if(jQuery('#clioid_hidden').val() == ""){
                    jQuery('#alerta_grid_os').html('É necessário ter pesquisado um cliente previamente.').fadeIn();  
                    jQuery('#resultado_pesquisa_os_hidden').val('0');
                    return false;
                }

                if(data.error != undefined) {
                    jQuery('#alerta_grid_os').html(data.message).fadeIn();
                    jQuery('#resultado_pesquisa_os_hidden').val('0');
                    return false;
                }

                jQuery.each(data.resultados, function(i, os){

                    var tds = '';
                    if((os.idvivo == "")||(os.idvivo == null)){
                    	tds += '<td></td>';
                    } else {
                    	tds += '<td>' + os.idvivo + '</td>';	
                    }
                    tds += '<td class="direita"><a class="placa_veiculo" href="prn_gestao_frota_contrato_vivo.php?acao=carregarDadosHistoricoContatos&connumero=' + os.connumero + '" target="_blank">' + os.placa + '</a></td>';
                    tds += '<td class="direita">' + os.protocolo_vivo + '</td>'; 
                    tds += '<td class="direita">' + os.protocolo_sascar + '</td>'; 
                    tds += '<td class="direita"><a class="placa_veiculo" href="prn_gestao_frota_contrato_vivo.php?acao=carregarDadosHistoricoOrdemServico&ordoid=' + os.ordem_servico + '" target="_blank">' + os.ordem_servico + '</a></td>';
                    tds += '<td>' + os.motivo + '</td>'; 
                    tds += '<td>' + os.status + '</td>'; 
                    tds += '<td>' + os.ultima_acao + '</td>'; 
                    tds += '<td>' + os.defeito_alegado + '</td>'; 
                    tds += '<td class="centro">' + os.data_abertura + '</td>'; 
                    tds += '<td class="centro">' + os.data_encerramento + '</td>';
                    tds += '<td>' + os.tempo_conclusao + '</td>';
                    tds += '<td>' + os.atendente + '</td>';

                    jQuery('#conteudo_grid_os').append('<tr>' + tds + '</tr>');

                });

                jQuery('#conteudo_grid_os tr:even').addClass('par');

                jQuery('#tabela_os').fadeIn();

                jQuery('#resultado_pesquisa_os_hidden').val(data.resultados.length);

            },
            complete: function() {
                jQuery('#loader_grid_os').hide();
            }
     }); 
    });    
    
    
    jQuery('#conteudo_historico_pessoas_autorizadas tr:even').addClass('par');
    jQuery('#conteudo_historico_emergencia_avisar tr:even').addClass('par');
    jQuery('#conteudo_historico_contrato tr:even').addClass('par');
    jQuery('#conteudo_veiculos tr:even').addClass('par');
    jQuery('#conteudo_grid_os tr:even').addClass('par');

});

function dispararAjaxBuscaCliente(clioid, filtro) {
    
    jQuery('#clioid_hidden').val(clioid);
    jQuery('#exibir_veiculos').attr('href', 'prn_gestao_frota_contrato_vivo.php?acao=pesquisarVeiculos&clioid='+clioid);
    jQuery('#exibir_grid_os').attr('href', 'prn_gestao_frota_contrato_vivo.php?acao=pesquisarOrdemServico&clioid='+clioid);
    
    jQuery.ajax({
        url: 'prn_gestao_frota_contrato_vivo.php',
        type: 'post',
        dataType: 'json',
        data: {
            acao: 'pesquisarInformacoesCliente',
            clioid: clioid
        },
        beforeSend: function() {
            //jQuery('#conteudo_veiculos').html('');
            //jQuery('#conteudo_grid_os').html('');
            jQuery('#alerta_veiculos').hide();
            jQuery('#alerta_grid_os').hide();
            jQuery('#msg_alerta').hideMessage();
            //jQuery('#tabela_veiculos').hide();
            //jQuery('#tabela_os').hide();
            jQuery('.limpar_campos').val('');
            //jQuery('.ui-helper-hidden-accessible').html('');
        },
        success: function(data) {            
            switch(filtro) {
                case 'cpf':
                case 'nome':
                case 'F':
                    
                    jQuery('#cpf').val(data.cliente.cpf_cnpj);
                    jQuery('#nome').val(data.cliente.nome);
                    
                    break;
                    
                default:
                    jQuery('#cnpj').val(data.cliente.cpf_cnpj);
                    jQuery('#razao_social').val(data.cliente.nome);
            }
            
            jQuery('#idvivo').val(data.cliente.idvivo);
            jQuery('#endereco').val(data.cliente.endereco);      
            
            if(data.veiculos.total_ativos != undefined) {
                jQuery('#qtd_veiculos_ativos').html(data.veiculos.total_ativos);
            } else {
                jQuery('#qtd_veiculos_ativos').html(0);
            }
            
            if(data.ordensServico.total_registros != undefined) {
                jQuery('#qtd_os').html(data.ordensServico.total_registros);
            } else {
                jQuery('#qtd_os').html(0);
            }
            
            jQuery.each(data.contatos, function(i, contato){
                
                if(contato.tipo_contato == 'A') {
                    jQuery('#pessoa_autorizada_nome').val(contato.nome);
                    jQuery('#pessoa_autorizada_telefone').val(contato.telefone);
                } else {                    
                    jQuery('#pessoa_emergencia_nome_' + i).val(contato.nome);
                    jQuery('#pessoa_emergencia_telefone_' + i).val(contato.telefone);
                }
                
            });      
            
            jQuery('#resultado_pesquisa_hidden').val(data.veiculos.resultados.length);
            jQuery('#resultado_pesquisa_os_hidden').val(data.ordensServico.resultados.length);
            
        },
        complete: function() {
            
        },
        error: function() {
            
        }
    });
    
}

// List of HTML entities for escaping.
var htmlEscapes = {
  '&': '&amp;',
  '<': '&lt;',
  '>': '&gt;',
  '"': '&quot;',
  "'": '&#x27;',
  '/': '&#x2F;'
};

// Regex containing the keys listed immediately above.
var htmlEscaper = /[&<>"'\/]/g;

// Escape a string for HTML interpolation.
_escape = function(string) {
  return ('' + string).replace(htmlEscaper, function(match) {
    return htmlEscapes[match];
  });
};