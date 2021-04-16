
        
    jQuery('#btn_confirmar').click(function(){
        
        if (!jQuery('#importacao_status_contrato').val()){
            criaAlerta('Você não possui permissão de acesso à este recurso.');
            return false;
        }
        
        if (!jQuery('#arquivo').val()){
            criaAlerta('Informe o arquivo.');
            return false;
        }
        else if (confirm('Confirma a importação de arquivo?')){

            jQuery('#acao').val('importaCSV');
            jQuery('#importa_informacoes').attr('action', 'fin_importacao_status_contrato.php');
            jQuery('#importa_informacoes').submit();        
        }

    });

   
    
