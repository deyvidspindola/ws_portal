/*
* @author	Willian Ouchi
* @email 	willian.ouchi@meta.com.br
* @since	07/11/2012
* */

jQuery(document).ready(function(){
        
    // Formatação para o campo "Dias em Atraso" aceitar apenas números.
    jQuery("#diasatraso").keypress(function(){
        formatar(this, '@');
    });
    jQuery("#diasatraso").blur(function(){
        revalidar(this, '@', '');
    });
    
    // Formatação para o campo "Contrato" aceitar apenas números.
    jQuery("#connumero").keypress(function(){
        formatar(this, '@');
    });    
    jQuery("#connumero").blur(function(){
        revalidar(this, '@', '');
    });
    
    
    /*
    *   Botão Pesquisar da tela inicial
    *   Carrega a listagem de contratos
    */
    jQuery.ajax({
        async: false,
        url: 'fin_rel_contratos_revenda_atraso.php',
        type: 'post',
        data: 'acao=carregarInformacoes',
        beforeSend: function(){
            
            jQuery('#btn_pesquisar').attr('disabled', 'disabled');
            jQuery('#loading').html('<img src="images/loading.gif" alt="" />');
            
        },
        success: function(data){
            try{                    
                // Transforma a string em objeto JSON
                var resultado = jQuery.parseJSON(data);
                
                jQuery("#conno_tipo").html('');
                jQuery("#conno_tipo").append(jQuery('<option value="">Todos</option>'));
                jQuery.each(resultado.tiposContrato, function(i, tipoContrato){
                    jQuery("#conno_tipo").append(jQuery('<option></option>').attr("value", tipoContrato.tpcoid).text(tipoContrato.tpcdescricao));
                });
                
            }
            catch(e){

                // Caso haja erros durante o processo, provavelmente na base de dados
                jQuery('#loading').html('<b>Erro no carregamento da tela.</b>');                    
            }
        },
        complete: function(){
            
            // Liberação do botão de pesquisa
            jQuery('#btn_pesquisar').removeAttr('disabled');
            jQuery('#loading').html('');  
        }
    });
    
    
    /*
    *   Botão Pesquisar da tela inicial
    *   Carrega a listagem de contratos
    */    
    jQuery('#btn_pesquisar').click(function(){
        
        //Remove os alertas da tela, caso existam.
        jQuery('input').css('background-color', '#FFFFFF');
        jQuery('select').css('background-color', '#FFFFFF');
        removeAlerta();
        
         /*
         * Valida se pelo menos um dos campos do filtro está preenchido.
         */ 
    if (
            jQuery('#clinome').val() == "" && 
            jQuery('#connumero').val() == "" &&
            jQuery('#diasatraso').val() == "" &&
            jQuery('#conno_tipo').val() == "" &&
            (jQuery('#nfldt_emissao_ini').val() == "" || jQuery('#nfldt_emissao_ini').val() == "") &&
            jQuery('#rczcd_zona').val() == ""        
        ){
                        
            criaAlerta('Ao menos um filtro de pesquisa deve ser informado.');
            return false;            
        }
        
         /*
         * Valida se o campo "Dias em Atraso" está preenchido e o valor não é zero.
         * Este campo deve aceitar apenas números inteiros maior que zero.
         */        
        if (jQuery('#diasatraso').val() != "" && jQuery('#diasatraso').val() == 0){

            jQuery('#diasatraso').css("background-color", "#FFFFC0");
            criaAlerta('O valor do campo "Dias em Atraso" deve ser maior que zero.');
            return false;            
        }
        
         /*
         * Valida se a data final não é maior que a data inicial.
         */
        if (jQuery('#nfldt_emissao_ini').val() != '' && jQuery('#nfldt_emissao_fin').val() != '') {
            
            if(diferencaEntreDatas(jQuery('#nfldt_emissao_fin').val(), jQuery('#nfldt_emissao_ini').val()) < 0){
                
                jQuery('#nfldt_emissao_ini').css("background-color", "#FFFFC0");
                jQuery('#nfldt_emissao_fin').css("background-color", "#FFFFC0");
                criaAlerta('A "Data final" deve ser maior que a "Data inicial".');
                return false;
            }            
        }       
         
         
        /*
        *  Ajax para carregamento da listagem,
        *  chama o método pesquisar do Action
        */        
        jQuery.ajax({
            url: 'fin_rel_contratos_revenda_atraso.php',
            type: 'post',
            data: jQuery('#busca_contratos').serialize()+'&acao=pesquisar_contratos',
            beforeSend: function(){
               
                /*
                * Antes de enviar o ajax removemos a tabela
                * para que ela possa ser populada novamente sem
                * erros
                * */
                jQuery('#msg').html('');
                jQuery('.resultado_pesquisa').hide();
                jQuery('.tr_resultado_ajax').remove();
                jQuery('#loading').html('<img src="images/loading.gif" alt="" />');

                /*
                * Bloqueio do botão de pesquisa para que no caso de o usuário
                * clicar várias vezes ele mande apenas uma requisição
                */
                jQuery('#bt_pesquisr').attr('disabled', 'disabled'); 
                
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
                    content += '<td align="center"><h3>Contrato</h3></td>';
                    content += '<td align="center"><h3>Dt. Cadastro</h3></td>';
                    content += '<td align="center"><h3>Dt. Ini. Vigência</h3></td>';
                    content += '<td align="center"><h3>Tp. Contrato</h3></td>';
                    content += '<td align="center"><h3>DMV</h3></td>';
                    content += '<td align="center"><h3>Cliente</h3></td>';                        
                    content += '<td align="center"><h3>Telefone</h3></td>';
                    content += '<td align="center"><h3>E-mail</h3></td>';
                    content += '<td align="center"><h3>Nota Fiscal</h3></td>';
                    content += '<td align="center"><h3>Dt. Emissão</h3></td>';
                    content += '<td align="center"><h3>Valor Nota</h3></td>';
                    content += '<td align="center"><h3>Dt. Vencimento</h3></td>';
                    content += '<td align="center"><h3>Valor Título</h3></td>';
                    content += '</tr>';
                   
                    if(resultado.contratos.length){
                        
                        // Monta a listagem de contratos de acordo com o retorno da pesquisa
                        jQuery.each(resultado.contratos, function(i, contrato){
                            var nota_fiscal = "";
                            if (contrato.nflserie){
                                var nota_fiscal = contrato.nflno_numero+'/'+contrato.nflserie;
                            }
                            else{
                                var nota_fiscal = contrato.nflno_numero;
                            }
                            content += '<tr class="tr_resultado_ajax">';
                            
                            content += '<td><a href="contrato_servicos.php?connumero='+contrato.connumero+'&acao=consultar" target="_blank">'+contrato.connumero+'</a></td>';
                            content += '<td>'+contrato.condt_cadastro+'</td>';
                            content += '<td>'+contrato.condt_ini_vigencia+'</td>';
                            content += '<td>'+contrato.tpcdescricao+'</td>';
                            content += '<td>'+contrato.dmv+'</td>';                            
                            content += '<td>'+contrato.clinome+'</td>';
                            content += '<td>'+contrato.clifone+'</td>';
                            content += '<td>'+contrato.cliemail+'</td>';
                            content += '<td><a href="rel_notafiscal.php?acao=NF&nfloid='+contrato.nfloid+'&nflserie='+contrato.nflserie+'&nflno_numero='+contrato.nflno_numero+'" target="_blank">'+nota_fiscal+'</a></td>';
                            content += '<td>'+contrato.nfldt_emissao+'</td>';
                            content += '<td>'+contrato.nflvl_total+'</td>';
                            content += '<td>'+contrato.titdt_vencimento+'</td>';
                            content += '<td>'+contrato.titvl_titulo+'</td>';
                            
                            content += '</tr>';
                        });
                        
                        // Total de registros
                        content += '<tr class="tableRodapeModelo3">';
                        content += '<td align="center" colspan="13" id="total_registros">A pesquisa retornou <b>'+resultado.total_registros+'</b> registro(s).</td>';
                        content += '</tr>';  
                        
                        // Rodapé da listagem
                        content += '<tr class="tableRodapeModelo3">';
                        content += '<td align="center" colspan="13">'; 
                        content += '<input type="button" name="btn_imprimir" id="btn_imprimir" value="Imprimir" class="botao">&nbsp;'; 
                        content += '<input type="button" name="btn_gerar_xls" id="btn_gerar_xls" value="Exportar para Excel" class="botao">';
                        content += '</td>';
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
                    
                    jQuery('#loading').html('');

                    // Mostra a tabela
                    jQuery('.resultado_pesquisa').fadeIn();
                    
                }catch(e){
                    
                    // Caso haja erros durante o processo, provavelmente na base de dados
                    jQuery('#loading').html('<b>Erro no processamento dos dados.</b>');
                    
                }
                
            },
            complete: function(){
                
                // Liberação do botão de pesquisa
                jQuery('#btn_pesquisar').removeAttr('disabled');
                jQuery('#loading').html('');                
            }
            
        });
        
    });
    
        
    jQuery('body').delegate('#btn_gerar_xls', 'click', function(){
        
        var tabela = jQuery('.resultado_pesquisa').clone();
        
        jQuery(tabela).find('.tableSubTitulo').remove();
        jQuery(tabela).find('.tableRodapeModelo3').remove();
        
        jQuery(tabela).find('a').each(function(){
            var text = jQuery(this).text();
            jQuery(this).parent().text(text);
        });
        
        jQuery.ajax({
            url: 'fin_rel_contratos_revenda_atraso.php',
            type: 'post',
            data: 'tabela='+jQuery(tabela).html()+'&acao=gerarXLS',
            success: function(data){               
                var resultado = jQuery.parseJSON(data);
                window.open("download.php?arquivo="+resultado.file_path+resultado.file_name, "relatorio_xls");
               
            }
        
        });
        
    });       
    
    
    jQuery('body').delegate('#btn_imprimir', 'click', function(){
        
        var tabela = jQuery('.resultado_imprimir').clone();
        
        jQuery(tabela).find('.tableSubTitulo').remove();
        jQuery(tabela).find('.tableRodapeModelo3').remove();
               
        jQuery(tabela).printArea();
       
    });
    
});

