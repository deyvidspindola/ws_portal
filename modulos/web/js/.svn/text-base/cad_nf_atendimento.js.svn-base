/*
* @author	Willian Ouchi
* @email 	willian.ouchi@meta.com.br
* @since	07/11/2012
* */

jQuery(document).ready(function(){
    
    jQuery("#nfavalor_fixo").maskMoney({
        decimal:",", 
        thousands:"."
    });
    jQuery("#nfavalor_fixo").blur(function(){
        revalidarMoeda(this,2)
    });
    jQuery("#nfavalor_unidade_recuperada").maskMoney({
        decimal:",", 
        thousands:"."
    });
    jQuery("#nfavalor_unidade_recuperada").blur(function(){
        revalidarMoeda(this,2)
    });
    jQuery("#nfatotal_recuperado").maskMoney({
        decimal:",", 
        thousands:"."
    });
    jQuery("#nfatotal_recuperado").blur(function(){
        revalidarMoeda(this,2)
    });
    jQuery("#nfavalor_unidade_nao_recuperado").maskMoney({
        decimal:",", 
        thousands:"."
    });
    jQuery("#nfavalor_unidade_nao_recuperado").blur(function(){
        revalidarMoeda(this,2)
    });
    jQuery("#nfatotal_nao_recuperado").maskMoney({
        decimal:",", 
        thousands:"."
    });
    jQuery("#nfatotal_nao_recuperado").blur(function(){
        revalidarMoeda(this,2)
    });
    jQuery("#nfavalor_variavel").maskMoney({
        decimal:",", 
        thousands:"."
    });
    jQuery("#nfavalor_variavel").blur(function(){
        revalidarMoeda(this,2)
    });
    jQuery("#nfavalor_unidade_excedente").maskMoney({
        decimal:",", 
        thousands:"."
    });
    jQuery("#nfavalor_unidade_excedente").blur(function(){
        revalidarMoeda(this,2)
    });
    jQuery("#nfavalor_total").maskMoney({
        decimal:",", 
        thousands:"."
    });
    jQuery("#nfavalor_total").blur(function(){
        revalidarMoeda(this,2)
    });
    
    jQuery("#nfaqtde_recuperada").mask("?999999", {placeholder:''});    
    jQuery("#nfaqtde_recuperada").focus(function() {
        jQuery("#nfaqtde_recuperada").select();
    });
    
    jQuery("#nfaqtde_nao_recuperada").mask("?999999", {placeholder:''});
    jQuery("#nfaqtde_nao_recuperada").focus(function() {
        jQuery("#nfaqtde_nao_recuperada").select();
    });

    jQuery("#nfaqtde_acionamento_excedente").mask("?999999", {placeholder:''});
    jQuery("#nfaqtde_acionamento_excedente").focus(function() {
        jQuery("#nfaqtde_acionamento_excedente").select();
    });

    /*
     *  Carrega as informações iniciais da tela na primeira 
     *  vez que é chamada.
     */
    jQuery.ajax({
        async: false,
        url: 'cad_nf_atendimento.php',
        type: 'post',
        data: 'acao=carregarInformacoes',
        beforeSend: function(){
            
            jQuery('#btn_pesquisar').attr('disabled', 'disabled');
            jQuery('#btn_novo').hide();
            jQuery('#loading').html('<img src="images/loading.gif" alt="" />');            
            jQuery('#loading').show();
        },
        success: function(data){
            try{                    
                
                // Transforma a string em objeto JSON
                var resultado = jQuery.parseJSON(data);

                jQuery("#permissao_total_ocorrencia").val(resultado.permissao_total_ocorrencia);                
                //jQuery("#permissao_total_ocorrencia").val(0);               
                jQuery("#tetoid").val(resultado.tetoid);
                
                if (!resultado.permissao_total_ocorrencia){
                    jQuery('#btn_novo').show();
                }
                
                /*
                 *  Carrega o combo de equipes se possuir apenas 1 resultado
                 *  significa que o usuário é de uma equipe e aparecerá apenas 
                 *  a equipe dele no filtro, senão ele poderá escolher Todos ou 
                 *  definir uma equipe
                 */
                jQuery("#par_tetoid").html('');
                
                jQuery.each(resultado.equipes, function(i, equipe){
                    jQuery("#par_tetoid").append(jQuery('<option></option>').attr("value", equipe.tetoid).text(equipe.tetdescricao));
                });
                
                jQuery('#loading').hide();
                
                jQuery("#par_nfadt_nota_ini").focus();
                
            }
            catch(e){

                // Caso haja erros durante o processo, provavelmente na base de dados
                jQuery('#loading').html('<b>Erro no carregamento da tela.</b>');                    
            }
        },
        complete: function(){

            jQuery('#btn_pesquisar').removeAttr('disabled');
            jQuery('#btn_novo').removeAttr('disabled');

        }
    });    
        

/*FILTRO DA PESQUISA */
    
    
function carregaItensNF(resultado){

  
        if (jQuery("#permissao_total_ocorrencia").val() == 1){
            jQuery("#tr_insere_acionamento").hide();
        }
        else{             
            if(resultado.acionamentos != undefined){

                jQuery("#tr_insere_acionamento").html('<td colspan="2"><label for="nfacoid">Acionamentos:</label>&nbsp;&nbsp;<select id="preroid" name="preroid"></select><button id="insere_acionamento">+</button></td>');
                jQuery("#preroid").html('');
                jQuery.each(resultado.acionamentos, function(i, acionamento){
                    jQuery("#preroid").append(jQuery('<option></option>').attr("value", acionamento.preroid).text(acionamento.prerdt_atendimento+' - '+acionamento.prerplaca_veiculo));
                });
            }
            else{
               jQuery("#tr_insere_acionamento").html('<td colspan="2"><label>Não há acionamentos cadastrados.</label></td>');
            }
            
            jQuery("#tr_insere_acionamento").show();                    
        }

        /*
         * Remove o conteúdo da tabela de Acionamentos
         * e inclui novamente de acordo com a NF de atendimento.
         */
        jQuery('.itens_nota').hide();
        jQuery('.tr_acionamentos_ajax').remove();               

        var content = '';
        content += '<tr class="tableTituloColunas">';                            
        content += '<td><h2>Data Acionamento</h2></td>';
        content += '<td><h2>Veículo</h2></td>';
        content += '<td><h2>Excluir</h2></td>';
        content += '<td><h2>Aprovar</h2></td>';
        content += '</tr>';

    
    if(resultado.itens_nota != undefined ){
        jQuery.each(resultado.itens_nota, function(i, item_nota){

            content += '<tr class="tr_acionamentos_ajax">';                            
            content += '<td>'+item_nota.prerdt_atendimento+'</td>';
            content += '<td>'+item_nota.prerplaca_veiculo+'</td>';
            content += '<td>'+item_nota.excluir+'</td>';
            content += '<td>'+item_nota.aprovado+'</td>';
            content += '</tr>';
        });
    }
    if (jQuery("#permissao_total_ocorrencia").val() == 1){
        content += '<tr class="tableRodapeModelo1">';                            
        content += '<td colspan="4" align="center"><input id="gerar_pdf" class="botao" type="button" value="Gerar PDF" name="gerar_pdf">&nbsp;<input id="gerar_xls" class="botao" type="button" value="Gerar XLS" name="gerar_xls">';
        content += '</tr>';
    }
    //Popula a tabela com os resultados
    jQuery('.itens_nota').html(content);
    jQuery('.tr_acionamentos_ajax:odd').addClass('tde');
    jQuery('.tr_acionamentos_ajax:even').addClass('tdc');   

    //Zebra a tabela
    jQuery('.itens_nota').show();
    jQuery(".acionamentos").fadeIn();
    
    // Se a NF está aprovada apresenta o box de Anexos
    if (resultado.nf_aprovada == true){
        jQuery(".anexados").fadeIn();
        if (jQuery("#permissao_total_ocorrencia").val() == 1){
            jQuery("#campo_previsao_pagamento").show();
        }
    }
    
}
    
    jQuery('#btn_pesquisar').click(function(){

        //Remove os alertas da tela, caso existam.
        jQuery('input').css('background-color', '#FFFFFF');
        jQuery('select').css('background-color', '#FFFFFF');
        removeAlerta();
                
		jQuery("#par_nfadt_nota_ini").removeClass("inputError");
		jQuery("#par_nfadt_nota_fin").removeClass("inputError");
	
        var dt_ini = jQuery('#par_nfadt_nota_ini').val();
        var dt_fim = jQuery('#par_nfadt_nota_fin').val();
      
        if (diferencaEntreDatas(dt_fim, dt_ini) < 0){
			criaAlerta("Data final menor que a data inicial."); 
			jQuery("#par_nfadt_nota_fin").addClass("inputError");
			return false;	
		}        
        
        /*
        *  Ajax para carregamento da listagem,
        *  chama o método pesquisar do Action
        */        
        jQuery.ajax({
            url: 'cad_nf_atendimento.php',
            type: 'post',
            data: jQuery('#busca_nf').serialize()+'&acao=pesquisar',
            beforeSend: function(){
               
                /*
                * Antes de enviar o ajax removemos a tabela
                * para que ela possa ser populada novamente sem
                * erros
                * */
                jQuery('#msg').html('');
                jQuery('.resultado_pesquisa').hide();
                jQuery('.tr_resultado_ajax').remove();
                jQuery('.cadastro_nf').hide();                
                jQuery('#loading').html('<img src="images/loading.gif" alt="" />');
                jQuery('#loading').show();

                /*
                * Bloqueio do botão de pesquisa para que no caso de o usuário
                * clicar várias vezes ele mande apenas uma requisição
                */
                jQuery('#btn_pesquisr').attr('disabled', 'disabled'); 
                
            },
            success: function(data){
                
                try{
                    
                    // Transforma a string em objeto JSON
                    var resultado = jQuery.parseJSON(data);
                   
                    var content = '';                    
                   
                    // Cabeçalho e Título das colunas de listagem
                    content += '<tr class="tableSubTitulo">';
                    content += '<td colspan="13"><h2>Resultado da pesquisa</h2></td>';
                    content += '</tr>';

                    content += '<tr class="tableTituloColunas">';
                    content += '<td align="center"><h3>Equipe</h3></td>';
                    content += '<td align="center"><h3>Data</h3></td>';
                    content += '<td align="center"><h3>Valor Fixo</h3></td>';
                    content += '<td align="center"><h3>Valor Unidade Recuperado</h3></td>';
                    content += '<td align="center"><h3>Quantidade Recuperada</h3></td>';
                    content += '<td align="center"><h3>Total Recuperado</h3></td>';                        
                    content += '<td align="center"><h3>Valor Unid. Não Recuperado</h3></td>';
                    content += '<td align="center"><h3>Quantidade Não Recuperada</h3></td>';
                    content += '<td align="center"><h3>Total Não Recuperado</h3></td>';
                    content += '<td align="center"><h3>Quantidade Acionamento Excedente</h3></td>';
                    content += '<td align="center"><h3>Valor Unid. Excedente</h3></td>';
                    content += '<td align="center"><h3>Total</h3></td>';
                    content += '<td align="center"><h3>Aprovado</h3></td>';
                    content += '</tr>';
                                       
                    if(resultado.total_registros > 0){
                        
                        // Monta a listagem de contratos de acordo com o retorno da pesquisa
                        jQuery.each(resultado.nfs, function(i, nf){

                            content += '<tr class="tr_resultado_ajax">';
                            
                            content += '<td nowrap>'+nf.tetdescricao+'</td>';
                            content += '<td nowrap><a href="" nfaoid="'+nf.nfaoid+'" class="lnk_nfadt_nota">'+nf.nfadt_nota_periodo+'</a></td>';
                            content += '<td align="right">'+nf.nfavalor_fixo+'</td>';
                            content += '<td align="right">'+nf.nfavalor_unidade_recuperada+'</td>';
                            content += '<td align="center">'+nf.nfaqtde_recuperada+'</td>';                            
                            content += '<td align="right">'+nf.nfatotal_recuperado+'</td>';
                            content += '<td align="right">'+nf.nfavalor_unidade_nao_recuperado+'</td>';
                            content += '<td align="center">'+nf.nfaqtde_nao_recuperada+'</td>';
                            content += '<td align="right">'+nf.nfatotal_nao_recuperado+'</td>';
                            content += '<td align="center">'+nf.nfaqtde_acionamento_excedente+'</td>';
                            content += '<td align="right">'+nf.nfavalor_unidade_excedente+'</td>';
                            content += '<td align="right">'+nf.nfavalor_total+'</td>';
                            content += '<td align="center">'+nf.aprovado+'</td>';
                            content += '</tr>';
                        });
                        
                        // Total de registros
                        content += '<tr class="tableRodapeModelo3">';
                        content += '<td align="center" colspan="13" id="total_registros">A pesquisa retornou <b>'+resultado.total_registros+'</b> registro(s).</td>';
                        content += '</tr>';                          
                       
                    }
                    else{
                        
                        /*
                        * Else do if(resultado.contratos.length) se caiu aqui
                        * quer dizer que a pesquisa não retornou nenhum item  
                        */ 
                        content += '<tr class="tableRodapeModelo3">';
                        content += '<td align="center" colspan="13" id="total_registros">Sem Resultados.</td>';
                        content += '</tr>';
                    }      
                        
                    
                    // Popula a tabela com os resultados
                    jQuery('.resultado_pesquisa').html(content);

                    // Zebra a tabela
                    jQuery('.tr_resultado_ajax:odd').addClass('tde');
                    jQuery('.tr_resultado_ajax:even').addClass('tdc');
                    
                    // Mostra a tabela
                    jQuery('.resultado_pesquisa').fadeIn();
                    jQuery('#loading').html(''); 
                    
                }catch(e){
                    
                    // Caso haja erros durante o processo, provavelmente na base de dados
                    jQuery('#loading').html('<b>Ocorreu um erro na pesquisa, tente novamente.</b>');                    
                }
                
            },
            complete: function(){
                
                // Liberação do botão de pesquisa
                jQuery('#btn_pesquisar').removeAttr('disabled');
            }
            
        });
        
    });
       
       
    jQuery('#btn_novo').click(function(){
        
        jQuery('.dados_pesquisa').hide();
        jQuery('.resultado_pesquisa').hide();
        
        jQuery('#msg').html('');
        jQuery('#loading').html('');
        
        jQuery('input').css('background-color', '#FFFFFF');
        jQuery('.inputError').removeClass('inputError');
        jQuery('#btn_salvar, .exclui_item_nf, .aprova_item_nf, .reprova_item_nf').removeAttr('disabled');
        
        jQuery("#nfaoid").val('');
        jQuery("#nfadt_nota_inicial").val('');
        jQuery("#nfadt_nota_final").val('');
        jQuery("#nfavalor_fixo").val('');
        jQuery("#nfavalor_unidade_recuperada").val('');
        jQuery("#nfaqtde_recuperada").val('');
        jQuery("#nfatotal_recuperado").val('');
        jQuery("#nfavalor_unidade_nao_recuperado").val('');
        jQuery("#nfaqtde_nao_recuperada").val('');
        jQuery("#nfatotal_nao_recuperado").val('');
        jQuery("#nfavalor_variavel").val('');
        jQuery("#nfaqtde_acionamento_excedente").val('');
        jQuery("#nfavalor_unidade_excedente").val('');
        jQuery("#nfadt_previsao_pgto").val('');
        jQuery("#nfavalor_total").val('');        
        
        jQuery("#campo_previsao_pagamento").hide();
        
        jQuery('.cadastro_nf').fadeIn();  
        jQuery('#nfadt_nota_inicial').focus();
        
    });
    
    
    /*
     *  Botão salvar do formulário de NF
     */
    jQuery('#btn_salvar').click(function(){        
        
        var acao = "";
        if ( !jQuery('#nfaoid').val() ){            
            acao = 'inserir';
        }
        else{            
            acao = 'editar';
        }
        
        jQuery('#msg').html('');
        
        var dt_ini = jQuery('#nfadt_nota_inicial').val();
        var dt_fim = jQuery('#nfadt_nota_final').val();
      
        if (diferencaEntreDatas(dt_fim, dt_ini) < 0){
            
            criaAlerta("Data final menor que a data inicial."); 
            jQuery("#nfadt_nota_final").addClass("inputError");
            return false;	            
        }     
        
        //Remove os alertas da tela, caso existam.
        jQuery("input").css('background-color', '#FFFFFF');
        jQuery(".inputError").removeClass("inputError");
        removeAlerta();
        
        //Validação do formulário
        var obrigatorio = true;
        if (!jQuery('#nfadt_nota_inicial').val()){
            criaAlerta('Existem campos obrigatórios não preenchidos');
            jQuery('#nfadt_nota_inicial').addClass('inputError');
            obrigatorio = false;
        }
        if (!jQuery('#nfadt_nota_final').val()){            
            criaAlerta('Existem campos obrigatórios não preenchidos');
            jQuery('#nfadt_nota_final').addClass('inputError');
            obrigatorio = false;
        }
        if (!jQuery('#nfavalor_total').val()){            
            criaAlerta('Existem campos obrigatórios não preenchidos');
            jQuery('#nfavalor_total').addClass('inputError');
            obrigatorio = false;
        }        
        if (!obrigatorio){            
            return false;
        } 
        
        var monetario = true;
        jQuery('.monetario').each(function(){
            
            var campo = jQuery(this).clone();
            jQuery(campo).val(jQuery(campo).val().replace(".", ""));
            jQuery(campo).val(jQuery(campo).val().replace(",", "."));            
            if (jQuery(campo).val() > 100000){
                jQuery(this).addClass("inputError");
                criaAlerta('Campos com valores maiores que o limite permitido');
                monetario = false;
                return false;                
            }
        });        
        if (!monetario){            
            return false;
        }        
        
        
        var inteiro = true;
        jQuery('.inteiro').each(function(){
            
            var campo = jQuery(this).clone();
            jQuery(campo).val(jQuery(campo).val().replace(".", ""));
            if (jQuery(campo).val() > 100000){
                jQuery(this).addClass("inputError");
                criaAlerta('Campos com valores maiores que o limite permitido');
                inteiro = false;
                return false;                
            }
        });        
        if (!inteiro){            
            return false;
        } 
        
        
        jQuery.ajax({
            url: 'cad_nf_atendimento.php',
            type: 'post',
            data: jQuery('#salva_nf').serialize()+'&tetoid='+jQuery('#tetoid').val()+'&acao='+acao,
            beforeSend: function(){
                
                jQuery('#msg').html('');
                jQuery('#loading').html('<img src="images/loading.gif" alt="" />');
                jQuery('#loading').show();
            },
            success: function(data){
                
                var resultado = jQuery.parseJSON(data);
                
                /*
                 * Se retornou o ID (nfaoid) significa que foi inserido
                 * corretamente. Armazena ID no campo nfaoid para identificar
                 * a nota durante a inserção dos itens.
                 */
                if(acao == "inserir"){ 
                    if (resultado.nfaoid > 0){

                        jQuery("#nfaoid").val(resultado.nfaoid);
                        jQuery('#msg').html('NF inserida com sucesso.');
                    }else{

                        jQuery('#msg').html('Houve um erro durante a inserção.');
                    }
                }
                else if(acao == "editar"){ 
                    if (resultado.nfaoid > 0){

                        jQuery("#nfaoid").val(resultado.nfaoid);
                        jQuery('#msg').html('NF editada com sucesso.');
                    }else{

                        jQuery('#msg').html('Houve um erro durante a edição.');
                    }                    
                }
                jQuery('#msg').show();
                carregaItensNF(resultado);
                
            },
            complete: function(){
                jQuery('#loading').html('');
            }   
        });
    });
    
    
    
    jQuery('#btn_cancelar').click(function(){
        
        removeAlerta();
        jQuery('#msg').html('');
        jQuery('#loading').html('');
        
        jQuery('#btn_pesquisar').click();
        
        jQuery('.dados_pesquisa').fadeIn();
        jQuery('.resultado_pesquisa').fadeIn();        
        jQuery('.cadastro_nf').hide();        
        jQuery('.acionamentos').hide(); 
        jQuery('.anexados').hide();
        upload.disable();
    });
    
    
    /*
     *  Link para edição a partir da listagem de resultados.
     */
    jQuery("body").delegate(".lnk_nfadt_nota", "click", function(){
        
        jQuery("#campo_previsao_pagamento").hide();
        
        jQuery('#msg').hide();
        jQuery('input').css('background-color', '#FFFFFF');
        jQuery('.inputError').removeClass('inputError');
        
        jQuery.ajax({
            async: false,
            url: 'cad_nf_atendimento.php',
            type: 'post',
            data: 'acao=buscarNF&nfaoid='+jQuery(this).attr('nfaoid')+'&tetoid='+jQuery("#tetoid").val(),
            beforeSend: function(){
                
                jQuery('#msg').html('');
                
                jQuery("#nfaoid").val('');
                jQuery("#nfadt_nota_inicial").val('');
                jQuery("#nfadt_nota_final").val('');
                jQuery("#nfavalor_fixo").val('');
                jQuery("#nfavalor_unidade_recuperada").val('');
                jQuery("#nfaqtde_recuperada").val('');
                jQuery("#nfatotal_recuperado").val('');
                jQuery("#nfavalor_unidade_nao_recuperado").val('');
                jQuery("#nfaqtde_nao_recuperada").val('');
                jQuery("#nfatotal_nao_recuperado").val('');
                jQuery("#nfavalor_variavel").val('');
                jQuery("#nfaqtde_acionamento_excedente").val('');
                jQuery("#nfavalor_unidade_excedente").val('');
                jQuery("#nfadt_previsao_pgto").val('');
                jQuery("#nfavalor_total").val('');                
                
                jQuery('#loading').html('<img src="images/loading.gif" alt="" />');
                jQuery('#loading').show();
            },
            success: function(data){
    
                var resultado = jQuery.parseJSON(data);
                
                //jQuery("#tetoid").val(resultado.tetoid);
                jQuery("#nfaoid").val(resultado.nf.nfaoid);
                jQuery("#nfadt_nota_inicial").val(resultado.nf.nfadt_nota_inicial);
                jQuery("#nfadt_nota_final").val(resultado.nf.nfadt_nota_final);
                jQuery("#nfavalor_fixo").val(resultado.nf.nfavalor_fixo);
                jQuery("#nfavalor_unidade_recuperada").val(resultado.nf.nfavalor_unidade_recuperada);
                jQuery("#nfaqtde_recuperada").val(resultado.nf.nfaqtde_recuperada);
                jQuery("#nfatotal_recuperado").val(resultado.nf.nfatotal_recuperado);
                jQuery("#nfavalor_unidade_nao_recuperado").val(resultado.nf.nfavalor_unidade_nao_recuperado);
                jQuery("#nfaqtde_nao_recuperada").val(resultado.nf.nfaqtde_nao_recuperada);
                jQuery("#nfatotal_nao_recuperado").val(resultado.nf.nfatotal_nao_recuperado);
                jQuery("#nfavalor_variavel").val(resultado.nf.nfavalor_variavel);
                jQuery("#nfaqtde_acionamento_excedente").val(resultado.nf.nfaqtde_acionamento_excedente);
                jQuery("#nfavalor_unidade_excedente").val(resultado.nf.nfavalor_unidade_excedente);
                jQuery("#nfadt_previsao_pgto").val(resultado.nf.nfadt_previsao_pgto);
                jQuery("#nfavalor_total").val(resultado.nf.nfavalor_total);
                jQuery("#nfacoid").html('');
                
                jQuery('.result').remove();
                
                jQuery.each(resultado.anexos, function(i, anexo){                    
                    var content = '';
                    content += '<tr class="result">';
                    content += '<td>'+anexo.arquivo+'</td>';
                    content += '<td><a href="download.php?arquivo=/var/www/anexos_nf_atendimento/'+jQuery("#nfaoid").val()+'/'+anexo.arquivo+'"><img id="'+anexo.id_anexo+'" rel="'+anexo.arquivo+'" class="preview_anexo" align="absmiddle" height="12" width="13" title="Preview" alt="Preview" src="images/icones/file.gif"></a></td>';
                    content += '<td>'+anexo.data+'</td>';
                    content += '<td>'+anexo.usuario+'</td>';
                    
                    if(resultado.nf.nfadt_previsao_pgto == "") {
                        content += '<td class="center"><b>[</b><img id="'+anexo.id_anexo+'" rel="'+anexo.arquivo+'" class="remover_anexo" align="absmiddle" height="12" width="13" title="Remover" alt="Remover" src="images/del.gif"><b>]</b></td>';
                    }else {
                        content += '<td>&nbsp;</td>';
                    }
                    
                    content += '</tr>';

                    jQuery('.anexados').append(content);

                    // Zebrando a tabela
                    jQuery('.anexados tr.result:odd').addClass('tde');
                    jQuery('.anexados tr.result:even').addClass('tdc');
                    
                });
                
                carregaItensNF(resultado);
                
                if(resultado.nf.nfadt_previsao_pgto != "") {
                    jQuery('#btn_salvar, .exclui_item_nf, .aprova_item_nf, .reprova_item_nf').attr('disabled', 'disabled');                    
                    jQuery('.anexados tr').eq(1).hide();
                    jQuery('.acionamentos tr').eq(1).hide();
                }else{
                    jQuery('#btn_salvar, .exclui_item_nf, .aprova_item_nf, .reprova_item_nf').removeAttr('disabled');
                    jQuery('.anexados tr').eq(1).show();
                    if(!jQuery('#permissao_total_ocorrencia').val()) {
                        jQuery('.acionamentos tr').eq(1).show();
                    }
                }
                                                
                if(resultado.nf_aprovada == true && resultado.nf.nfadt_previsao_pgto == "") {
                    upload.enable(jQuery('#nfaoid').val());
                }else{
                    upload.disable();
                }
                
            },
            complete: function(){

                jQuery('#loading').html('');
                jQuery('#loading').hide();
                
                jQuery('#nfadt_nota_inicial').focus();
            }   
        });        
        
        jQuery('.dados_pesquisa').hide();
        jQuery('.resultado_pesquisa').hide();
        jQuery('.cadastro_nf').fadeIn();
                
        return false;
    });

    
    
    //Anexo
    jQuery('body').delegate('.remover_anexo', 'click', function(){
        
        var confirm = window.confirm('Tem certeza que deseja remover este anexo?');
        
        if(confirm) {
            
            var id_anexo = jQuery(this).attr('id');  
            var nome_arquivo = jQuery(this).attr('rel');
            var tr = jQuery(this).parent().parent();
            
            jQuery.ajax({
                url : 'cad_nf_atendimento.php',
                type: 'POST',
                data: {
                    acao: 'excluirAnexo',
                    id_anexo: id_anexo,
                    id_nf: jQuery('#nfaoid').val(),
                    nome_arquivo: nome_arquivo
                },
                beforeSend: function(){
                    
                    jQuery('#loading').html('<img src="images/loading.gif" alt="" />');
                    jQuery('#loading').show();
                },
                success: function(data){
                    
                    var result = jQuery.parseJSON(data);
                    
                    if(!result.error) {
                        
                        tr.effect('highlight', {}, 1000);
                        
                        tr.fadeOut(500, function(){
                            
                            tr.remove();
                            
                            
                            jQuery('.anexados tr.result').removeClass('tde');
                            jQuery('.anexados tr.result').removeClass('tdc');
                            
                            // Zebrando a tabela
                            jQuery('.anexados tr.result:odd').addClass('tde');
                            jQuery('.anexados tr.result:even').addClass('tdc');
                        });
                    }                    
                    
                }, 
                complete: function() {
                    jQuery('#loading').hide();
                    jQuery('#div_msg').show();
                }
            });
        
        }
        
    });
	    
    // Zebrando a tabela
    jQuery('#anexados tr.result:odd').addClass('tde');
    jQuery('#anexados tr.result:even').addClass('tdc');
    
    
    

/* ACIONAMENTOS */


/*
*  Insere um acionamento no item da NF de atendimento, ao inserir 
*  remove o acionamento da combobox.
*/
jQuery('body').delegate('#insere_acionamento', 'click', function(){

    var botao = jQuery(this);

    jQuery.ajax({
        async: false,
        url: 'cad_nf_atendimento.php',
        type: 'post',
        data: 'acao=insereAcionamento&nfaoid='+jQuery("#nfaoid").val()+'&preroid='+jQuery("#preroid").val(),
        beforeSend: function(){

            jQuery('#loading').html('<img src="images/loading.gif" alt="" />');
            jQuery('#loading').show();

            botao.attr('disabled', 'disabled');

        },
        success: function(data){

            var resultado = jQuery.parseJSON(data);
            
            //jQuery('#preroid option').remove();
            //jQuery('#preroid').html('');
                        
            jQuery("#tr_insere_acionamento").html('<td colspan="2"><label for="nfacoid">Acionamentos:</label>&nbsp;&nbsp;<select id="preroid" name="preroid"></select><button id="insere_acionamento">+</button></td>');
            jQuery("#preroid").html('');
            
            if(resultado.acionamentos != undefined){
                jQuery.each(resultado.acionamentos, function(i, acionamento){
                   jQuery("#preroid").append(jQuery('<option></option>').attr("value", acionamento.preroid).text(acionamento.prerdt_atendimento+' - '+acionamento.prerplaca_veiculo)); 
                });            
            }
            if(jQuery('#preroid option').length == 0) {
                jQuery("#tr_insere_acionamento").html('<td colspan="2"><label>Não há acionamentos cadastrados.</label></td>');
            }

            /*
             * Remove o conteúdo da tabela de Acionamentos
             * e inclui novamente de acordo com a NF de atendimento.
             */
            jQuery('.itens_nota').hide();
            jQuery('.tr_acionamentos_ajax').remove();               
            var content = '';
            if(resultado.itens_nota != undefined){

                content += '<tr class="tableTituloColunas">';                            
                content += '<td><h2>Data Acionamento</h2></td>';
                content += '<td><h2>Veículo</h2></td>';
                content += '<td><h2>Excluir</h2></td>';
                content += '<td><h2>Aprovar</h2></td>';
                content += '</tr>';
                
                jQuery.each(resultado.itens_nota, function(i, item_nota){

                    content += '<tr class="tr_acionamentos_ajax">';                            
                    content += '<td>'+item_nota.prerdt_atendimento+'</td>';
                    content += '<td>'+item_nota.prerplaca_veiculo+'</td>';
                    content += '<td>'+item_nota.excluir+'</td>';
                    content += '<td>'+item_nota.aprovado+'</td>';
                    content += '</tr>';
                });
                
                jQuery('.anexados').hide();
                upload.disable();
                                
            }
            // Popula a tabela com os resultados
            jQuery('.itens_nota').html(content);
            jQuery('.tr_acionamentos_ajax:odd').addClass('tde');
            jQuery('.tr_acionamentos_ajax:even').addClass('tdc');
            
            // Zebra a tabela
            jQuery('.itens_nota').fadeIn();

        }, 
        complete: function() {

            jQuery('#loading').html('');
            jQuery('#loading').hide();

            botao.removeAttr('disabled');

        }
    });

    return false;
});


/*
*  Botão Aprovar itens da NF
*/
jQuery('body').delegate('.aprova_item_nf', 'click', function(){

    var tr = jQuery(this).parent().parent();

    jQuery.ajax({
        async: false,
        url: 'cad_nf_atendimento.php',
        type: 'post',
        data: 'acao=aprovaItemNF&nfacoid='+jQuery(this).attr("nfacoid")+'&nfaoid='+jQuery("#nfaoid").val(),
        beforeSend: function(){

            jQuery('#loading').html('<img src="images/loading.gif" alt="" />');
            jQuery('#loading').show();
        },
        success: function(data){

            var resultado = jQuery.parseJSON(data);

            //if (resultado.error == false){

                if(resultado.itens_nota != undefined){
                    content = '';
                    content += '<td>'+resultado.itens_nota[0].prerdt_atendimento+'</td>';
                    content += '<td>'+resultado.itens_nota[0].prerplaca_veiculo+'</td>';
                    content += '<td>'+resultado.itens_nota[0].excluir+'</td>';
                    content += '<td>'+resultado.itens_nota[0].aprovado+'</td>';
                    tr.html(content);
                }
                
                // Se a NF está aprovada apresenta o box de Anexos
                if (resultado.nf_aprovada == true){
                    jQuery(".anexados").fadeIn();
                    upload.enable(jQuery('#nfaoid').val());
                }
                                
                jQuery('#loading').html('');
                
           // }  
           // else{

               // jQuery('#loading').html('Erro ao aprovar o item da nota.');
           // }

        }
    });

    return false;
});



/*
*   Botão Reprovar itens da NF
*/
jQuery('body').delegate('.reprova_item_nf', 'click', function(){

   var tr = jQuery(this).parent().parent();
   
   jQuery.ajax({
        async: false,
        url: 'cad_nf_atendimento.php',
        type: 'post',
        data: 'acao=reprovaItemNF&nfacoid='+jQuery(this).attr("nfacoid")+'&nfaoid='+jQuery("#nfaoid").val(),
        beforeSend: function(){

            jQuery('#loading').html('<img src="images/loading.gif" alt="" />');
            jQuery('#loading').show();
        },
        success: function(data){

            var resultado = jQuery.parseJSON(data);

            //if (resultado.error == false){

                if(resultado.itens_nota != undefined){
                    content = '';
                    content += '<td>'+resultado.itens_nota[0].prerdt_atendimento+'</td>';
                    content += '<td>'+resultado.itens_nota[0].prerplaca_veiculo+'</td>';
                    content += '<td>'+resultado.itens_nota[0].excluir+'</td>';
                    content += '<td>'+resultado.itens_nota[0].aprovado+'</td>';
                    tr.html(content);
                }

                jQuery('#loading').html('');
           // }  
           // else{

             //   jQuery('#loading').html('Erro ao aprovar o item da nota.');
           // }
           
           if(resultado.nf_aprovada == false) {
               jQuery('.anexados').hide();
               upload.disable();
           }
           
        }
   });

   return false;
}); 



/*
*  Exclui um acionamento no item da NF de atendimento, ao excluir 
*  adiciona o acionamento da combobox.
*/
jQuery('body').delegate('.exclui_item_nf', 'click', function(){

    var botao = jQuery(this);        
    var tr = botao.parent().parent();

    jQuery.ajax({
        async: false,
        url: 'cad_nf_atendimento.php',
        type: 'post',
        data: 'acao=excluiItemNF&nfacoid='+jQuery(this).attr("nfacoid")+'&tetoid='+jQuery("#tetoid").val()+'&nfaoid='+jQuery("#nfaoid").val(),
        beforeSend: function(){

            botao.attr('disabled', 'disabled');

            jQuery('#loading').html('<img src="images/loading.gif" alt="" />');
            jQuery('#loading').show();
        },
        success: function(data){

            var resultado = jQuery.parseJSON(data);
            // Popula a tabela com os resultados
            jQuery("#tr_insere_acionamento").html('<td colspan="2"><label for="nfacoid">Acionamentos:</label>&nbsp;&nbsp;<select id="preroid" name="preroid"></select><button id="insere_acionamento">+</button></td>');
            jQuery("#preroid").html('');
            jQuery.each(resultado.acionamentos, function(i, acionamento){
                jQuery("#preroid").append(jQuery('<option></option>').attr("value", acionamento.preroid).text(acionamento.prerdt_atendimento+' - '+acionamento.prerplaca_veiculo));
            });                
            
            tr.remove();
            
            jQuery('.tr_acionamentos_ajax').removeClass('tde');
            jQuery('.tr_acionamentos_ajax').removeClass('tdc');

            jQuery('.tr_acionamentos_ajax:odd').addClass('tde');
            jQuery('.tr_acionamentos_ajax:even').addClass('tdc');                
            
            // Se a NF está aprovada apresenta o box de Anexos
            if (resultado.nf_aprovada == true){
                jQuery(".anexados").fadeIn();
                upload.enable(jQuery('#nfaoid').val());
            }

        }, 
        complete: function() {

            jQuery('#loading').html('');
            jQuery('#loading').hide();

            botao.removeAttr('disabled');

        }
    });

    return false;
});


});

jQuery.download = function(url, data, method){
	//url and data options required
	if( url && data ){ 
		//data can be string of parameters or array/object
		data = typeof data == 'string' ? data : jQuery.param(data);		 
		//split params into form inputs
		var inputs = '';
		jQuery.each(data.split('&'), function(){ 
			var pair = this.split('=');
			inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />'; 
		});
		//send request
		jQuery('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>')
		.appendTo('body').submit().remove();
	};
};

jQuery('body').delegate('#gerar_pdf', 'click', function(){

	var nfaoid = jQuery('#nfaoid').val();

	jQuery.download('cad_nf_atendimento.php', 'acao=gerarPdf&id='+nfaoid);
	
})

jQuery('body').delegate('#gerar_xls', 'click', function(){
	
	var nfaoid = jQuery('#nfaoid').val();
	
    jQuery.download('cad_nf_atendimento.php', 'acao=gerarXls&id='+nfaoid);
        
	
})

 var upload = {
    
    self_: null,
    
    enable: function() {
        
        this.self_ = new AjaxUpload('arquivo', {
            action: 'cad_nf_atendimento.php',
            type: 'post',
            data: {
                acao: 'uploadAnexo',
                id_nf: jQuery('#nfaoid').val()
            },
            name: 'arquivo',
            onSubmit: function(file, ext){
                jQuery('#msg').html('');
                jQuery('#loading').html('<img src="images/loading.gif" alt="" />');
                jQuery('#loading').show();
                
            },
            onComplete: function(file, response){                                    
                var resultado = jQuery.parseJSON(response);
                
                jQuery('#msg').html(resultado.message);
                jQuery('#msg').show();            

                if(!resultado.error) {
                    
                    var content = '';
                    content += '<tr class="result">';
                    content += '<td>'+resultado.nome_arquivo+'</td>';
                    content += '<td><a href="download.php?arquivo=/var/www/anexos_nf_atendimento/'+jQuery("#nfaoid").val()+'/'+resultado.nome_arquivo+'"><img id="'+resultado.id_arquivo+'" rel="'+resultado.nome_arquivo+'" class="preview_anexo" align="absmiddle" height="12" width="13" title="Preview" alt="Preview" src="images/icones/file.gif"></a></td>';
                    content += '<td>'+resultado.data_inclusao+'</td>';
                    content += '<td>'+resultado.usuario+'</td>';
                    content += '<td class="center"><b>[</b><img id="'+resultado.id_arquivo+'" rel="'+resultado.nome_arquivo+'" class="remover_anexo" align="absmiddle" height="12" width="13" title="Remover" alt="Remover" src="images/del.gif"><b>]</b></td>';
                    content += '</tr>';

                    jQuery('.anexados').append(content);

                    // Zebrando a tabela
                    jQuery('.anexados tr.result:odd').addClass('tde');
                    jQuery('.anexados tr.result:even').addClass('tdc');

                }

                jQuery('#loading').html('');
                jQuery('#loading').hide();
            }
        });
    },
    
    disable: function() {
        
        if(this.self_ != null) {
            this.self_.destroy();
        }
        
    }
    
}
