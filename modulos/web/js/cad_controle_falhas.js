jQuery(document).ready(function(){
     
     
    jQuery("#btn_pesquisar_historico_falhas").click(function(){
        
        removeAlerta();
        
        if ( jQuery("#equno_serie").val() == "" ){
            criaAlerta("Inserir número de serial desejado.");
            return false;
        }
        
        jQuery("#acao").val("pesquisarHistoricoFalhas");
        jQuery("#form").submit();        
    });
    
        
    jQuery("#btn_inserir_falhas").click(function(){
        
        if ( jQuery("#equno_serie").val() == "" ){
            criaAlerta("Inserir número de serial desejado.");
            return false;
        }
        
        jQuery("#acao").val("carregarInsercaoFalhas");
        jQuery("#form").submit();        
    });
        
    
    jQuery("#btn_gravar_falhas").click(function(){        
        
        var retorno = null;
        
        var controle_falhas_id              = jQuery("#ctfoid").val();
        var numero_serie                    = jQuery("#ctfno_serie").val();
        var modelo_equipamento_id           = jQuery("#ctfeproid").val();
        var modelo_equipamento_descricao    = jQuery("#eprnome").val();
        var data_entrada                    = jQuery("#ctfdt_entrada").val();         
        var defeito_constatado_id           = jQuery("#ctfifdoid option:selected").val();
        var defeito_constatado_descricao    = jQuery("#ctfifdoid option:selected").text();        
        var acao_laboratorio_id             = jQuery("#ctfifaoid option:selected").val();
        var acao_laboratorio_descricao      = jQuery("#ctfifaoid option:selected").text();        
        var componente_afetado_id           = jQuery("#ctfifcoid option:selected").val();
        var componente_afetado_descricao    = jQuery("#ctfifcoid option:selected").text();
        var numero_contrato                 = jQuery("#connumero").val();
        var item_ordem_servico                 = jQuery("#item_ordem_servico").val();
        
        removeAlerta();
        if (
            jQuery("#ctfifaoid").val() == "" 
            || jQuery("#ctfifcoid").val() == "" 
            || jQuery("#ctfifdoid").val() == "" 
        ){
        
            criaAlerta("Todos os filtros devem estar selecionados.");
            return false;
        }
        
        jQuery.ajax({
            type: "POST",
            url: "cad_controle_falhas.php",
            data:{
                acao: 'gravarFalhas',
                controle_falhas_id: controle_falhas_id,
                numero_serie: numero_serie,
                modelo_equipamento_id: modelo_equipamento_id,
                data_entrada: data_entrada,
                defeito_constatado_id: defeito_constatado_id,
                acao_laboratorio_id: acao_laboratorio_id,
                componente_afetado_id: componente_afetado_id,
                numero_contrato: numero_contrato,
                item_ordem_servico: item_ordem_servico
            },
            success: function(data){

                var resultado = jQuery.parseJSON(data);
                jQuery("#msg_retorno").val(resultado.message);

                if ( resultado.error == false ){
                    
                    if ( !controle_falhas_id ){

                        if (confirm('Deseja acrescentar mais algum registro?')){

                            var antepenultima_linha = jQuery("#itemControleFalha tr:last").prev().prev();       

                            nova_linha = antepenultima_linha.after("<tr class='tr_item_controle_falha'></tr>").next();

                            nova_linha.append("<td>"+numero_serie+"</td>");
                            nova_linha.append("<td>"+modelo_equipamento_descricao+"</td>");
                            nova_linha.append("<td>"+data_entrada+"</td>");
                            nova_linha.append("<td>"+defeito_constatado_descricao+"</td>");
                            nova_linha.append("<td>"+acao_laboratorio_descricao+"</td>");
                            nova_linha.append("<td>"+componente_afetado_descricao+"</td>");
                            zebraTabela(".tr_item_controle_falha");

                            jQuery("#ctfifaoid").val("");
                            jQuery("#ctfifcoid").val("");
                            jQuery("#ctfifdoid").val("");

                        }
                        else{
                            
                            if ( jQuery("#status_equipamento").val() != 24 ){
                                
                                if (confirm('Deseja alterar o status do equipamento para Em Recall - Disponível?')){
                                    jQuery.ajax({
                                        async: false,
                                        type: "POST",
                                        url: "cad_controle_falhas.php",
                                        data:{
                                            acao: 'alteraStatusEquipamento',
                                            numero_serie: numero_serie                                        
                                        }
                                    });
                                    jQuery("#acao").val("pesquisarHistoricoFalhas");
                                    jQuery("#form").submit();

                                }
                                else{

                                    jQuery("#acao").val("pesquisarHistoricoFalhas");
                                    jQuery("#form").submit();            
                                }
                            }
                            else{

                                jQuery("#acao").val("pesquisarHistoricoFalhas");
                                jQuery("#form").submit();            
                            }
                        }

                    }
                    else{

                        jQuery("#acao").val("pesquisarHistoricoFalhas");
                        jQuery("#form").submit();
                    }
                }
                else{

                    jQuery("#acao").val("pesquisarHistoricoFalhas");
                    jQuery("#form").submit();
                }
            }
        });
       
    });
    
    
    jQuery("#btn_excluir_historico_falhas").click(function(){
        
        if ( jQuery(".chk_controle_falhas:checked").length == 0){
            
            criaAlerta("Selecionar um item para exclusão.");
            return false;
        }
        
        if ( confirm('Deseja realmente excluir o(s) item(ns)?') ){
            
            jQuery("#acao").val("excluirHistoricoFalhas");
            jQuery("#form").submit();        
        }
    });
    
    
    jQuery("#equno_serie").keyup(function(){
        formatar(this, '@');
    });    
    
    jQuery("#equno_serie").blur(function(){
        revalidar(this, '@');
    });

        
});

function zebraTabela(seletor){

    jQuery( seletor ).removeClass('tde');
    jQuery( seletor ).removeClass('tdc');

    jQuery(seletor + ':odd').addClass('tde');
    jQuery(seletor + ':even').addClass('tdc');
}