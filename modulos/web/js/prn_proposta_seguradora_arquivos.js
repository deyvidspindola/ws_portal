jQuery(window).load(function(){   
    jQuery('#bt_pesquisar').unbind().click(function(){
        pesquisarArquivo();
    });
});

function abre_abas(link){
    location = link;
}

function pesquisarArquivo(){
    //Validando a data
    if(!ValidaDataPC(prpsdt_ultima_acao_inicio_busca)){
        return false;
    } else if(!ValidaDataPC(prpsdt_ultima_acao_final_busca)){
        return false;
    }
    
    var obj = new Object();
        obj.data_inicial  = jQuery.trim(jQuery('#prpsdt_ultima_acao_inicio_busca').val());
        obj.data_final    = jQuery.trim(jQuery('#prpsdt_ultima_acao_final_busca').val());
        obj.tipo_arquivo  = jQuery.trim(jQuery('#slc_tipo_arquivo').val());
        obj.tipo_contrato = jQuery.trim(jQuery('#slc_tipo_contrato').val());
        obj.status        = jQuery.trim(jQuery('#slc_status').val());
    
    if(obj.data_inicial == ""){
        alert("Informe o Período!");
        return false;
    } else if(obj.data_final == ""){
        alert("Informe o Período!");
        return false;
    } else if(obj.tipo_contrato == ""){
        alert("Informe o Tipo do Contrato!");
        return false;
    }
    
    jQuery.post("prn_proposta_seguradora_arquivos.php",{
        acao  : 'resultado_pesquisar_arquivo',
        dados : obj
    },
    function(data){
        jQuery('#resultadoConteudo').empty().html(data);
    });
}

function downloadFile(file){
    file = jQuery.trim(file);
    
    if(file == ""){
        return false;
    }
    
    jQuery.post("prn_proposta_seguradora_arquivos.php",{
        acao  : 'validar_arquivo',
        dados : file
    }, function(data){
        data = jQuery.parseJSON(data);
        
        if(data.status){
            //Informando os parâmetros
            jQuery('#form').attr('action', 'prn_proposta_seguradora_arquivos.php');
            jQuery('#caminho_arquivo').val(file);
            jQuery('#acao').val('baixar_arquivo');
            
            //Submit
            jQuery('#form').submit();
            
            limparParametrosForm();            
        } else{
            limparParametrosForm();
            alert("Arquivo inexistente!");  
        }
    }); 
}

function limparParametrosForm(){
    //Removendo os parâmetros do form
    jQuery('#form').attr('action', '#');
    jQuery('#caminho_arquivo').val('');
    jQuery('#acao').val('');
}