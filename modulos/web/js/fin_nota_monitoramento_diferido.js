/**
 * STI 84974 - Cadastro de NOTAS DE SAÍDA do tipo: Monitoramento Diferido.
 * Item 116
 *
 * @author Bruno Bonfim Affonso - <bruno.bonfim@sascar.com.br>
 * @version 1.0
 * @since 16/12/2014
 */
var loading = new Object();
    loading.open  = function(){jQuery('#resultado_progress').show();};
    loading.close = function(){jQuery('#resultado_progress').hide();};
    
jQuery(document).ready(function(){       
    jQuery('#pesquisar').unbind().click(function(){
        pesquisar();
    });    
    jQuery('#novo').unbind().click(function(){
        cadastrar();
    });
});

/**
 * Realiza a pesquisa da nota do tipo Monitoramento Diferido.
 */
function pesquisar(){
    jQuery('.mensagem').empty().hide();
    jQuery('#frame01').empty();
    
    var dados = new Object();
        dados.periodo = jQuery.trim(jQuery('#dt_ini').val());
        dados.nota    = jQuery.trim(jQuery('#nota').val());
        dados.serie   = jQuery.trim(jQuery('#serie').val());
        
    if(dados.periodo == ''){
        jQuery('#msgalerta').empty().html('Informe o período.').show();
        return false;
    } else if(dados.nota != '' && dados.serie == ''){
        jQuery('#msgalerta').empty().html('Informe a série da nota.').show();
        return false;
    } else if(dados.serie != '' && dados.nota == ''){
        jQuery('#msgalerta').empty().html('Informe o número referência da nota.').show();
        return false;
    }
    
    loading.open();
    jQuery.post("fin_nota_monitoramento_diferido.php",{
        dados : dados,
        acao  : 'pesquisar'
    },
    function(data){
        loading.close();
        jQuery('#frame01').empty().html(data);
        jQuery('.bt_excluir').unbind().click(function(){
            excluir(jQuery(this).attr('data-nfid'));
        });
    });
}

/**
 * Renderiza a tela de cadastro.
 */
function cadastrar(){
    jQuery('.mensagem').empty().hide();
    jQuery('#frame01').empty();
    
    loading.open();
    jQuery.post("fin_nota_monitoramento_diferido.php",{
        acao  : 'cadastrar'
    },
    function(data){
        loading.close();
        jQuery('#frame_content').empty().html(data);
        jQuery('#confirmar').unbind().click(function(){confirmar();});    
        jQuery('#voltar').unbind().click(function(){voltar();});
    });
}

/**
 * Realiza o cadastro da nota do tipo Monitoramento Diferido.
 */
function confirmar(){
    jQuery('.mensagem').empty().hide();
    
    var dados = new Object();
        dados.nota  = jQuery.trim(jQuery('#nota').val());
        dados.serie = jQuery.trim(jQuery('#serie').val());
        
    if(dados.nota == ""){
        jQuery('#msgalerta').empty().html('Informe o número da nota.').show();
        return false;
    }
    
    if(dados.serie == ""){
        jQuery('#msgalerta').empty().html('Informe a série da nota.').show();
        return false;
    }
    
    loading.open();
    jQuery.post("fin_nota_monitoramento_diferido.php",{
        dados : dados,
        acao  : 'confirmar'
    },
    function(data){
        loading.close();
        data = jQuery.parseJSON(data);
        jQuery(data.tipo).empty().html(data.msg).show();
    });
}

/**
 * Refresh na página.
 */
function voltar(){
    location.reload();
}

/**
 * Remove o tipo da nota como Monitoramento Diferido.
 * @param string id
 */
function excluir(id){
    jQuery('.mensagem').empty().hide();
    id = jQuery.trim(id);
    
    if(id != ''){
        if(confirm("Você tem certeza que deseja excluir essa nota como Monitoramento Diferido?")){
            var dados = new Object();
                dados.id = id;
            
            loading.open();
            jQuery.post("fin_nota_monitoramento_diferido.php",{
                dados : dados,
                acao  : 'excluir'
            },
            function(data){
                loading.close();
                data = jQuery.parseJSON(data);
                jQuery(data.tipo).empty().html(data.msg).show();
                
                if(data.status){
                    jQuery("a[data-nfid*='"+dados.id+"']").parent().parent().children().eq(3).empty().html('Não');
                    jQuery("a[data-nfid*='"+dados.id+"']").parent().empty();
                }
            });
        } else{
            return false;
        }
    } else{
        return false;
    }
}