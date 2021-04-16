/**
 * Requisições AJAX do módulo Termo Aditivo de Serviços.
 * Validações e Observadores encontram-se em: prn_termo_aditivo_servico_validacoes.js
 * @author Bruno Bonfim Affonso - <bruno.bonfim@sascar.com.br>
 * @package Principal
 * @version 1.0
 * @since 03/04/2013
 */ 
jQuery(document).ready(function(){
    //Data atual
    jQuery('#dt_ini').datepicker("setDate", new Date());
    jQuery('#dt_fim').datepicker("setDate", new Date());
    //Observador
    onObserverPesquisa();    
});

function buscarServicos() {
    var obj = new Object();

    obj.tipo_serv = jQuery('#tipo_serv_pac option:selected').val();
    obj.pacote = jQuery.trim(jQuery('#pacote').val());

    jQuery.post("prn_termo_aditivo_servico.php", {
        acao: 'buscarServicos',
        dados: obj
    },
    function(data) {
        data = jQuery.parseJSON(data);

        jQuery("#servico").empty().html(data.option);

        jQuery("#contrato").val('');
        setValores();

        carregarSelectContrato();
    });
}

/**
 * Popula a combo referente a Contratos.
 */
function carregarSelectContrato(){
    var obj = new Object();

    var tipo = jQuery('#tipo_serv_pac option:selected').val();

    if(tipo == 'P') {
        obj.servico  = jQuery.trim(jQuery('#pacote').val());
    } else {
        obj.servico  = jQuery.trim(jQuery('#servico').val());
    }
    obj.cliente  = jQuery.trim(jQuery('#cliente').val());
    obj.cpf_cnpj = jQuery.trim(jQuery('#cpf_cnpj').val());

    if(!validarDados(obj, 'carregarSelectContrato')){
        return false;
    }
    
    jQuery.post("prn_termo_aditivo_servico.php",{
        acao  : 'carregar_contrato',
        dados : obj
    },
    function(data){
        data = jQuery.parseJSON(data);

        jQuery('#numerosContrato').val(data.option);
        
        //Obrigação Financeira é Tipo Cliente, desabilita a combo contrato.
        if(data.tipo_cliente){
            jQuery('#contrato').attr("disabled","disabled");                       
        } else{            
            jQuery('#contrato').removeAttr("disabled");
        }
        
        //Aplica as validações conforme a Situação e Serviço selecionado.
        setValores();
    });
}

/**
 * Retorna os registros da pesquisa.
 */
function pesquisarTermoAditivoServico(){
    var obj = new Object();
        obj.data_inicio    = jQuery.trim(jQuery('#dt_ini').val());
        obj.data_fim       = jQuery.trim(jQuery('#dt_fim').val());
        obj.cliente        = jQuery.trim(jQuery('#cliente').val());
        obj.placa          = jQuery.trim(jQuery('#placa').val());
        obj.numero_aditivo = jQuery.trim(jQuery('#nm_aditivo').val());
        obj.status         = jQuery.trim(jQuery('#status').val());
        obj.servico        = jQuery.trim(jQuery('#servico').val());
        
    if(!validarDados(obj, 'pesquisarTermoAditivoServico')){
        return false;
    }
    
    carregando.abrir();    
    jQuery.post("prn_termo_aditivo_servico.php",{
        acao  : 'pesquisar_termo',
        dados : obj
    },
    function(data){
        carregando.fechar();
        jQuery('#frame01').empty().html(data);
        onObserverPesquisa();
    });
}

/**
 * Tela para incluir/editar o termo aditivo de serviço.
 */
function carregarTelaTermoAditivoServico(termo_aditivo){
    carregando.abrir();
    jQuery.post("prn_termo_aditivo_servico.php",{
        acao          : 'carregar_tela_tas',
        termo_aditivo : jQuery.trim(termo_aditivo)
    },
    function(data){
        carregando.fechar();
        jQuery('#frame_content').empty().html(data);
        onObserverTermoAditivo();
        limparMensagem();
        
        if(jQuery.trim(jQuery('#id_termo').val()) != ""){
            //Se o STATUS não for PENDENTE
            if(parseInt(jQuery('#status').val()) != 1){
                jQuery('#adicionar_servico').hide();
                jQuery('#status').attr("disabled","disabled");
                jQuery('#confirmar').hide();
                jQuery('#excluir_termo').hide();
                jQuery('a[id*=lnk_remove_item_]').unbind();
            }
        }     
    });
}

/**
 * Incluí/Altera um Termo Aditivo
 */
function confirmarTermoAditivoServico(){
    var obj = new Object();
        obj.id_termo        = jQuery.trim(jQuery('#id_termo').val());
        obj.cliente         = jQuery.trim(jQuery('#cliente').val());
        obj.cpf_cnpj        = jQuery.trim(jQuery('#cpf_cnpj').val());
        obj.ta_servico      = jQuery.trim(jQuery('#ta_servico').val());
        obj.situacao        = jQuery.trim(jQuery('#situacao').val());
        obj.status          = jQuery.trim(jQuery('#status').val());
        obj.validade        = jQuery.trim(jQuery('#validade').val());
        
    if(!validarDados(obj, 'confirmarTermoAditivoServico')){
        return false;
    }
        
    carregando.abrir();    
    jQuery.post("prn_termo_aditivo_servico.php",{
        acao  : 'confirmar_termo',
        dados : obj
    },
    function(data){
        carregando.fechar();
        data = jQuery.parseJSON(data);     
        
        setMensagem(data.tipo_msg, data.msg);
        
        if(data.id_termo != "" && data.id_termo != undefined){        
            jQuery('#status').removeAttr("selected");
            setTimeout('carregarTelaTermoAditivoServico('+data.id_termo+')', 1500);
        }        
    });
}

/**
 * Adiciona um item aditivo. 
 */
function adicionarItemTermoAditivoServico(){    
    //Criando os atributos 
    var obj = new Object();
        obj.id_termo        = jQuery.trim(jQuery('#id_termo').val());
        obj.contrato        = jQuery.trim(jQuery('#contrato').val());
        obj.valor_tabela    = jQuery.trim(jQuery('#valor_tabela').val());
        obj.valor_negociado = jQuery.trim(jQuery('#valor_negociado').val());
        obj.desconto        = jQuery.trim(jQuery('#desconto').val());
        obj.tipo_reajuste   = jQuery.trim(jQuery('input[name="tipo_reajuste"]:checked').val());
        obj.valorMinimo     = jQuery.trim(jQuery('#vlrMinimo').val());
        obj.valorMaximo     = jQuery.trim(jQuery('#vlrMaximo').val());
        obj.tipo            = jQuery('#tipo_serv_pac option:selected').val();
        obj.numerosContrato = jQuery('#numerosContrato').val();
        obj.situacao        = jQuery.trim(jQuery('#situacao').val());
        
        if(obj.tipo == 'P') {
            obj.servico = jQuery.trim(jQuery('#pacote').val());
        } else {
            obj.servico = jQuery.trim(jQuery('#servico').val());
        }
        
    if(!validarDados(obj, 'adicionarItemTermoAditivoServico')){
        return false;
    }    
        
    carregando.abrir();
    jQuery.post("prn_termo_aditivo_servico.php",{
        acao  : 'adicionar_item',
        dados : obj
    },
    function(data){
        carregando.fechar();
        //Populando a tabela
        jQuery('#tbl_itens_aditivos').children().eq(1).append(data);
        
        //Remove e Adiciona novamente o CSS nas linhas da tabela e insere total de registros.
        setAtributosTabelaItens();
        onObserverTermoAditivo();       
    });    
}

/**
 * Remove o item aditivo
 * @param int id_item
 */
function removerItemAditivo(id_item){
    id_item = jQuery.trim(id_item);
    
    if(id_item != ""){
        jQuery.post("prn_termo_aditivo_servico.php",{
            acao    : 'remover_item',
            id_item : id_item
        },
        function(data){
            data = jQuery.parseJSON(data);
            
            if(data.status){
                jQuery('#lnk_remove_item_'+id_item).parent().parent().remove();
                setAtributosTabelaItens();
            } else{
                setMensagem(data.tipo_msg, data.msg);
            }            
        });
    }
}

/**
 * Exclui o Termo Aditivo
 */
function excluirTermoAditivoServico(){
    var id_termo = jQuery.trim(jQuery('#id_termo').val());
    
    if(id_termo == ""){
        return false;
    }
    
    carregando.abrir();
    jQuery.post("prn_termo_aditivo_servico.php",{
        acao     : 'excluir_termo',
        id_termo : id_termo
    },
    function(data){
        carregando.fechar();
        data = jQuery.parseJSON(data);
        
        if(data.status){
            setMensagem(data.tipo_msg, data.msg);
            setTimeout("jQuery('#retornar').trigger('click');", 1500);
        } else{
            setMensagem(data.tipo_msg, data.msg);
        }
    });
}

/**
 * Realiza a pesquisa pelo nome do cliente.
 */
function pesquisarCliente(){
    jQuery('#div_content_result_pesquisa').hide();
    
    var obj = new Object();
        obj.cliente = jQuery.trim(jQuery('#cliente').val());
    
    if(!validarDados(obj, 'pesquisarCliente')){
        return false;
    }
    
    //Corrigi a posicao do gif no IE e div que contem resultado da pesquisa de cliente
    if(jQuery.browser.msie){
        jQuery('#div_mini_loader').attr("style", "display: none; border: medium none; position: absolute; top: 247px; left: 426px; width: 17px;");
        jQuery('#div_content_result_pesquisa').attr("style", "max-height:300px; background-color: rgb(255, 255, 255); border-width: 1px; border-style: solid; border-color: rgb(255, 255, 255) rgb(204, 204, 204) rgb(204, 204, 204); -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; border-image: none; font-family: Arial; font-size: 12px; overflow: auto; position: absolute; top: 267px; left: 67px; width: 378px !important; box-shadow: 2px 2px 2px rgb(136, 136, 136); z-index: 999; display: none;");
    }
    
    jQuery('#div_mini_loader').show();    
    jQuery.post("prn_termo_aditivo_servico.php",{
        acao  : 'componente_pesquisar_cli',
        dados : obj
    },
    function(data){
        jQuery('#div_mini_loader').hide();
        data = jQuery.parseJSON(data);
        
        if(data.tipo_msg != undefined && data.msg != undefined){
            setMensagem(data.tipo_msg, data.msg);
        } else if(data.html != undefined){
            //Insere o HTML
            jQuery('#div_content_result_pesquisa').empty().html(data.html);
            //Mostra a caixa com os resultados
            jQuery('#div_content_result_pesquisa').show();
        }
        
        onObserverComponentePesquisa();
    });
}