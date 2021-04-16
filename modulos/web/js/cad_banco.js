/**
 * @author	Emanuel Pires Ferreira
 * @email 	epferreira@brq.com
 * @since	13/03/2013
 * @STI     80195
 */

jQuery(function() {

    jQuery('.tr_resultado_ajax:odd').addClass('tde');
    jQuery('.tr_resultado_ajax:even').addClass('tdc');

    /**
     * Ação de envio do formulário de pesquisa.
     */
    jQuery('body').delegate('#bt_pesquisar', 'click', function(){
        
        //desabilita botão pesquisar
        jQuery('#bt_pesquisar').attr('disabled', 'disabled');

        //esconde resultados anteriores
        jQuery('.resultado_pesquisa').hide();
        jQuery('.total_registros').hide();

        //Loading da montagem da tela
        jQuery('#loading').fadeIn();
        
        jQuery('#busca_bancos').submit();
        
    });
    
    /**
     * Ações de envio do formulário de pesquisa.
     * Esconde erros, valida campos, calcula datas e exibe loading 
     */            
    jQuery('#novoBanco').submit(function() {
        
        var erros = 0;

        jQuery(".input_error").removeClass("input_error");
        removeAlerta();

        if(jQuery("#cfbbanco").val() == "") {
            jQuery("#cfbbanco").addClass("input_error");
            erros++;
        }
        
        jQuery.post('cad_banco.php',{cfbbanco: jQuery("#cfbbanco").val(), acao: 'verificaIntegridade'},function(data){
            if(data > 0) {
                criaAlerta("Campo Código já cadastrado anteriormente.");
                return false;
            }
        });
        
        if(jQuery("#cfbtecoid").val() == 0) {
            jQuery("#cfbtecoid").addClass("input_error");
            erros++;
        }
        
        if(jQuery.trim(jQuery("#cfbnome").val()).length == 0) {
            jQuery("#cfbnome").addClass("input_error");
            erros++;
        }
        
        if(jQuery("#cfbplcoid").val() == "") {
            jQuery("#cfbplcoid").addClass("input_error");
            erros++;
        }
        
        if(jQuery("#cfbagencia").val() == "") {
            jQuery("#cfbagencia").addClass("input_error");
            erros++;
        }
        
        if(jQuery("#cfbconta_corrente").val() == "") {
            jQuery("#cfbconta_corrente").addClass("input_error");
            erros++;
        }
        
        if(jQuery("#cfbtipo").val() == "") {
            jQuery("#cfbtipo").addClass("input_error");
            erros++;
        }
        
        if(erros > 0) {
            criaAlerta("Existem campos obrigatórios não preenchidos.");
            return false;
        }
        
                
        jQuery.ajax({
            url: 'cad_banco.php',
            type: 'post',
            data: jQuery("#novoBanco").serialize()+'&acao=salvar',
            beforeSend: function(){
                //exibe loading de processamento
                jQuery('#loading').html('<img src="images/loading.gif" alt="" />');
                jQuery('#loading').fadeIn(5000);

                //remove botao salvar
                jQuery('#bt_salvar').hide();
            },
            success: function(data){
                
                if(data != "erro") {
                    
                    criaAlerta("Banco cadastrado com sucesso!");
                    
                } else {
                    criaAlerta("Erro ao cadastrar o Banco");
                }
                
                jQuery('#loading').html('');
                jQuery('#loading').fadeOut(5000);
            }
        });
    
        
        return false;
    });
    
    /**
     * Ações de envio do formulário de pesquisa.
     * Esconde erros, valida campos, calcula datas e exibe loading 
     */            
    jQuery('#editarBanco').submit(function() {
        
        var erros = 0;

        jQuery(".input_error").removeClass(".input_error");
        removeAlerta();

        if(jQuery.trim(jQuery("#cfbnome").val()).length == 0) {
            jQuery("#cfbnome").addClass("input_error");
            erros++;
        }
        
        if(jQuery("#cfbplcoid").val() == "") {
            jQuery("#cfbplcoid").addClass("input_error");
            erros++;
        }
        
        if(jQuery("#cfbagencia").val() == "") {
            jQuery("#cfbagencia").addClass("input_error");
            erros++;
        }
        
        if(jQuery("#cfbconta_corrente").val() == "") {
            jQuery("#cfbconta_corrente").addClass("input_error");
            erros++;
        }
        
        if(jQuery("#cfbtipo").val() == "") {
            jQuery("#cfbtipo").addClass("input_error");
            erros++;
        }
        
        if(erros > 0) {
            criaAlerta("Existem campos obrigatórios não preenchidos.");
            return false;
        }
                
        jQuery.ajax({
            url: 'cad_banco.php',
            type: 'post',
            data: jQuery("#editarBanco").serialize()+'&acao=salvar',
            beforeSend: function(){
                //exibe loading de processamento
                jQuery('#loading').html('<img src="images/loading.gif" alt="" />');
                jQuery('#loading').fadeIn(5000);

                //remove botao salvar
                jQuery('#bt_salvar').hide();
            },
            success: function(data){
                
                if(data != "erro") {
                    
                    criaAlerta("Banco editado com sucesso!");
                    
                } else {
                    criaAlerta("Erro ao editado o Banco");
                }
                
                jQuery('#loading').html('');
                jQuery('#loading').fadeOut(5000);
            }
        });
    
        
        return false;
    });
});


/**
 * Função que alterna entre formulário de pesquisa e cadastro
 * 
 * @return null
 */
function exibeCadastro() {
    jQuery("#acao").val('novo');
    
    jQuery("#busca_bancos").submit();
}

/**
 * Função responsável por atualizar combo Conta Contábil conforme 
 * preenchimento do campo Empresa
 *  
 * @param Integer tecoid - código da empresa
 * 
 * @return null
 */
function pesquisaContaContabil(tecoid) {

    jQuery("#cfbplcoid").prop('disabled', 'disabled');
    
    jQuery('#loadConta').css('display',function() {
        jQuery('#loadConta').fadeIn(350,function(){
            'block';
        });
    });
    
    jQuery("#cfbplcoid").html('<option value="">Selecione</option>');

    jQuery.post('cad_banco.php', {
        acao: 'buscaContaContabil', 
        tecoid: tecoid}, 
        function (data) {

            if(data !== false) {

                // Transforma a string em objeto JSON
                var resultado = jQuery.parseJSON(data);                                       

                if(resultado) { 
                    if(resultado.planos.length){

                        // Monta a listagem de clientes de acordo com o retorno da pesquisa
                        jQuery.each(resultado.planos, function(i, plano){
                            jQuery("#cfbplcoid").append('<option value="'+plano.plcoid+'">'+plano.plcdescricao+'</option>');
                        });
                    } 
                }
            }
        }
    );

    jQuery('#loadConta').css('display',function() {
        jQuery('#loadConta').fadeOut(350,function(){
            'none';
        });
    });

    $('#cfbplcoid').prop('disabled', false);
}
