jQuery(document).ready(function(){
    
    var height = 0;

    height = jQuery("#layout-principal-content").height();

    if (height < 500) {
        height = 500;
    }

    jQuery("#grupo-conteudo-arvore").height(height);
    jQuery("#layout-sidebar").height(height + 80);

    criarArvore();



    jQuery("#ano_referencia").change(function(e) {
        recarregaArvore();
    });
    


    jQuery("#bt_voltar_arvore").click(function(){

        jQuery("#arvore-conteudo").html('<div id="loader_1" class="carregando"></div>');

        var funcionarioId = jQuery(this).attr('data-funcionarioid');
        var ano = jQuery("#ano_referencia").val();
        var navegacao = jQuery(this).attr('data-count');

        //var multiplo = 0;
        //var acao     = 'voltarArvore';
        //if (jQuery(this).hasClass("voltar-multiplo")) {
            multiplo = 1;
            acao     = 'voltarArvoreMultiplo';
        //}


        jQuery.ajax({
            type: 'POST',
            async: false,
            url:  'ges_layout.php',
            data: {
                acao: acao,
                funcionario: funcionarioId,
                multiplo : multiplo,
                ano: ano,
                nivel_navegacao : navegacao
            },
            success: function(data) {

                if (typeof JSON != 'undefined') {
                    data = JSON.parse(data);
                } else {
                    data = eval('(' + data + ')');
                }

                jQuery(".arvore").remove();

                if (data.navegacao <= 0) {
                    data.navegacao = 0;
                }


                jQuery("#bt_voltar_arvore").attr('data-count',data.navegacao);

                data.html = data.html.replace(/\t/g, "");
                data.html = data.html.replace(/\r/g, "");
                data.html = data.html.replace(/\n/g, "");

                //if (multiplo == 1) {

                    jQuery("#arvore-conteudo").empty().html(data.html);
                    jQuery(".ui-fancytree").addClass('esconde-arvore');
               // }

                //if (multiplo != 1) {
                    //jQuery("#arvore-conteudo").empty().html(data.html);
                //}



                if (data.desabilita_voltar == '1') {
                    jQuery("#bt_voltar_arvore").css('display','none');
                }

                if (data.voltar_inicio != '1') {
                    jQuery("#bt_voltar_arvore").text('Retornar a árvore de ' + data.funcionario_retorno + '');
                    jQuery("#bt_voltar_arvore").attr('data-funcionarioid',data.funcionario_retorno_id);
                } else {
                    jQuery("#bt_voltar_arvore").text('Retornar ao início da árvore');
                    jQuery("#bt_voltar_arvore").attr('data-funcionarioid',data.funcionario_retorno_id);
                }


                criarArvore();

                height = jQuery("#layout-principal-content").height();

                if (height < 500) {
                    height = 500;
                }

                jQuery("#grupo-conteudo-arvore").height(height);
                jQuery("#layout-sidebar").height(height + 80);

                esconderArvore();

            }
        });


    });


    esconderArvore();


    jQuery( "body" ).delegate("#link_acao", "click", function() {
        var url;
        if (jQuery('#superUsuario').val() != 't') {
            if (parseInt(jQuery('#id_plano_selecionado').val()) > 0) {
                
                url = 'ges_acoes.php?meta='+jQuery('#id_meta_selecionada').val()+'&plano='+jQuery('#id_plano_selecionado').val()+'&ano='+jQuery('#ano_referencia').val();
                
                loaderIframe();
                
                jQuery("#ges_conteudo").attr("src", url);
                
                jQuery('#dialogo_acao').dialog("close");
                
            } else {

                var opcoes = '<br /><div>Nenhum plano de ação selecionado.</div>';

                jQuery('#dialogo_acao')
                .html(opcoes)
                .dialog({
                    draggable : false,
                    modal     : true,
                    resizable : false,
                    width     : 450,
                    title     : 'Alerta',
                    buttons   : {
                        Fechar : function() {
                            jQuery(this).dialog('close');
                            return false;
                        }
                    }
                });
                return false;
            }

        } else {
            
            url = 'ges_acoes.php?meta='+jQuery('#id_meta_selecionada').val()+'&plano='+jQuery('#id_plano_selecionado').val()+'&ano='+jQuery('#ano_referencia').val();
            
            loaderIframe();
            
            jQuery("#ges_conteudo").attr("src", url);
            
            jQuery('#dialogo_acao').dialog("close");
        }
			
    });

    jQuery( "body" ).delegate("#link_planoacao", "click", function() {
        var url;
        if (jQuery('#superUsuario').val() != 't') {
            if (parseInt(jQuery('#meta_selecionada').val()) > 0) {
                
                url = 'ges_plano_acao.php?meta='+jQuery('#meta_selecionada').val()+'&ano='+jQuery('#ano_referencia').val();
                
                loaderIframe();
                
                jQuery("#ges_conteudo").attr("src", url);
                
                jQuery('#dialogo_acao').dialog("close");
                
            } else {

                var opcoes = '<br /><div>Nenhuma meta selecionada.</div>';

                jQuery('#dialogo_acao')
                .html(opcoes)
                .dialog({
                    draggable : false,
                    modal     : true,
                    resizable : false,
                    width     : 450,
                    title     : 'Alerta',
                    buttons   : {
                        Fechar : function() {
                            jQuery(this).dialog('close');
                            return false;
                        }
                    }
                });
                return false;
            }

        } else {
            
            var id_meta = (parseInt(jQuery('#id_meta_selecionada').val()) > 0) ? jQuery('#id_meta_selecionada').val() : jQuery('#meta_selecionada').val(); 
            
            url = 'ges_plano_acao.php?meta='+id_meta+'&ano='+jQuery('#ano_referencia').val();
            
            loaderIframe();
            
            jQuery("#ges_conteudo").attr("src", url);
            
            jQuery('#dialogo_acao').dialog("close");
            
        }
			
    });

    jQuery( "body" ).delegate(".titulo_arvore, #titulo_arvore", "click", function() {
        //jQuery(".arvore-ativo").next().next().addClass('esconde-arvore');	
			

        if (jQuery(this).hasClass('arvore-ativo')) {
            jQuery(this).removeClass('arvore-ativo');
            jQuery(this).addClass('arvore-inativo');
            jQuery(this).next().next().addClass('esconde-arvore');
        } else {
            jQuery(".titulo_arvore, #titulo_arvore").addClass('arvore-inativo');
            jQuery(this).removeClass('arvore-inativo');
            jQuery(this).addClass('arvore-ativo');
            jQuery(this).next().next().removeClass('esconde-arvore');
        }


			
    });

    jQuery("body").delegate('#alterar_plano_acao', 'click', function(){
        var url;
        
        if (parseInt(jQuery('#id_plano_selecionado').val()) > 0) {
            url ='ges_plano_acao.php?acao=editar&plano='+jQuery('#id_plano_selecionado').val()+'&ano='+jQuery('#ano_referencia').val();
            
            loaderIframe();
            
            jQuery("#ges_conteudo").attr("src", url);
            
            jQuery('#dialogo_acao').dialog("close");
            
        } else {

            var opcoes = '<br /><div>Nenhum plano de ação selecionado.</div>';

            jQuery('#dialogo_acao')
            .html(opcoes)
            .dialog({
                draggable : false,
                modal     : true,
                resizable : false,
                width     : 450,
                title     : 'Alerta',
                buttons   : {
                    Fechar : function() {
                        jQuery(this).dialog('close');
                        return false;
                    }
                }
            });
            return false;
        }

			
    })




    jQuery( "body" ).delegate("#inserir_acao", "click", function() {

        var opcoes = '<div id="link_planoacao">Inserir PLANO DE AÇÃO</div>';
        opcoes += '<div id="link_acao">Inserir AÇÃO</div>';

        jQuery('#dialogo_acao')
        .html(opcoes)
        .dialog({
            draggable : false,
            modal     : true,
            resizable : false,
            width     : 450,
            title     : 'Selecione uma Opção:',
            buttons   : {
                Cancelar : function() {
                    jQuery(this).dialog('close');
                    return false;
                }
            //,
            //    Ok       : function() {
            //        jQuery('#prototipo_alterar_versao').val(jQuery('#prototipo_opcao_versao').val());
            //        jQuery('#prototipo_formulario_alterar_versao').submit();

            //       return true;
            //  }
            }
        });

    });

    
    jQuery('.link-menu').click(function(){
        loaderIframe();
    });
    
});



function esconderArvore() {
    jQuery(".titulo_arvore, #titulo_arvore").each(function(){
        jQuery(this).next().next().addClass('esconde-arvore');
    });
}


function carregaArvore(funcionarioId, superior) {

    jQuery(".arvore").html('<div id="loader_1" class="carregando"></div>');
    var ano = jQuery("#ano_referencia").val();
    var navegacao = jQuery("#bt_voltar_arvore").attr('data-count');

    jQuery.ajax({
        type: 'POST',
        async: false,
        url:  'ges_layout.php',
        data: {
            acao: 'carregarArvore',
            funcionario: funcionarioId,
            superior: superior,
            nivel_navegacao: navegacao,
            ano: ano
        },
        success: function(data) {

            if (typeof JSON != 'undefined') {
                data = JSON.parse(data);
            } else {
                data = eval('(' + data + ')');
            }

            jQuery(".arvore").remove();

            data.html = data.html.replace(/\t/g, "");
            data.html = data.html.replace(/\r/g, "");
            data.html = data.html.replace(/\n/g, "");

            jQuery("#arvore-conteudo").empty().html(data.html);

            if (data.voltar_inicio != '1') {
                jQuery("#bt_voltar_arvore").text('Retornar a árvore de ' + data.funcionario_retorno + '');
                jQuery("#bt_voltar_arvore").attr('data-funcionarioid',data.funcionario_retorno_id);
            } else {
                jQuery("#bt_voltar_arvore").text('Retornar ao início da árvore');
                jQuery("#bt_voltar_arvore").attr('data-funcionarioid',data.funcionario_retorno_id);
            }

            jQuery("#bt_voltar_arvore").css('display','block');


            criarArvore();

            height = jQuery("#layout-principal-content").height();

            if (height < 500) {
                height = 500;
            }

            jQuery("#grupo-conteudo-arvore").height(height);
            jQuery("#layout-sidebar").height(height + 80);

            esconderArvore();
        }
    });

}


function carregaArvoreMultiplo(funcionarioId, superior) {

    jQuery("#arvore-conteudo").html('<div id="loader_1" class="carregando"></div>');

    jQuery(".arvore").remove();

    var ano = jQuery("#ano_referencia").val();
    var navegacao = jQuery("#bt_voltar_arvore").attr('data-count');

    jQuery.ajax({
        type: 'POST',
        async: false,
        url:  'ges_layout.php',
        data: {
            acao: 'carregarArvoreMultiplo',
            funcionario: funcionarioId,
            superior: superior,
            nivel_navegacao: navegacao,
            ano: ano
        },
        success: function(data) {

            if (typeof JSON != 'undefined') {
                data = JSON.parse(data);
            } else {
                data = eval('(' + data + ')');
            }

            if (data.navegacao <= 0) {
                data.navegacao = 0;
            }

            data.html = data.html.replace(/\t/g, "");
            data.html = data.html.replace(/\r/g, "");
            data.html = data.html.replace(/\n/g, "");

            jQuery("#arvore-conteudo").empty().html(data.html);

            jQuery("#bt_voltar_arvore").attr('data-count',data.navegacao);


            if (data.voltar_inicio != '1') {
                jQuery("#bt_voltar_arvore").text('Retornar a árvore de ' + data.funcionario_retorno + '');
                jQuery("#bt_voltar_arvore").attr('data-funcionarioid',data.funcionario_retorno_id);
            } else {
                jQuery("#bt_voltar_arvore").text('Retornar ao início da árvore');
                jQuery("#bt_voltar_arvore").attr('data-funcionarioid',data.funcionario_retorno_id);
            }

            jQuery("#bt_voltar_arvore").css('display','block');


            criarArvore();

            height = jQuery("#layout-principal-content").height();

            if (height < 500) {
                height = 500;
            }

            jQuery("#grupo-conteudo-arvore").height(height);
            jQuery("#layout-sidebar").height(height + 80);

            esconderArvore();
        }
    });

}



function criarArvore() {
    jQuery(".arvore").fancytree({

        click: function(e,data){

            var url;

            var node = data.node;
            var classes = data.node.extraClasses;

            //Ação da Meta
            if (classes.indexOf("alvo") != -1) {

                var metaid = node.data.metaid;
                var ano = jQuery("#ano_referencia").val();

                requererGrafico(metaid, ano);
                jQuery('#id_meta_selecionada').val(metaid);   
                jQuery('#meta_selecionada').val(metaid);
                jQuery('#id_plano_selecionado').val('');  
            }

            if (classes.indexOf("plano") != -1) {

                var planoid = node.data.planoid;

                jQuery('#id_plano_selecionado').val(planoid);   

                url = 'ges_plano_acao.php?acao=visualizar&meta='+jQuery('#meta_selecionada').val()+'&plano='+planoid+'&ano='+jQuery('#ano_referencia').val();

                loaderIframe();

                jQuery("#ges_conteudo").attr("src", url);


            //Ação

            }

            if (classes.indexOf("navegacao") != -1 && classes.indexOf("multiplo") == -1) {

                jQuery("#bt_voltar_arvore").attr('data-count', parseInt(jQuery("#bt_voltar_arvore").attr('data-count')) + 1 );
                var funcionario_id = node.data.funcionarioid;
                var superior       = node.data.superior;
                carregaArvore(funcionario_id, superior);
                jQuery("#bt_voltar_arvore").css('display','block');


            } else if (classes.indexOf("multiplo") != -1) {

                jQuery("#bt_voltar_arvore").attr('data-count', parseInt(jQuery("#bt_voltar_arvore").attr('data-count')) + 1 );
                var funcionario_id = node.data.funcionarioid;
                var superior       = node.data.superior;
                carregaArvoreMultiplo(funcionario_id, superior);
                jQuery("#bt_voltar_arvore").css('display','block');

            }
        }

    });
}

/**
 * Faz a solicitação por AJAX para a Action geradora do gráfico
 **/
function requererGrafico (metaid, ano) {

    var urlGrafico;

    urlGrafico = 'ges_grafico_meta.php?acao=iniciarProcesso&metaid='+metaid+'&ano='+ano;

    loaderIframe();

    jQuery("#ges_conteudo").attr("src", urlGrafico);
}

function loaderIframe(){
    jQuery("#conteudo-inicial").hide();
    jQuery("#loader_iframe").removeClass('invisivel');
    jQuery("#ges_conteudo").hide();
}

function resizeIframe(obj) {    
    
    jQuery("#loader_iframe").addClass('invisivel');
    
    jQuery(obj).show();
    
    var altura = obj.contentWindow.document.body.scrollHeight;

    if (altura > 800){
        var div = altura + 90;
        jQuery("#layout-sidebar").height(div + 'px');
    } else {
        jQuery("#layout-sidebar").height('890px');
    }
    
    obj.style.height = altura + 'px';
    
}


function recarregaArvore(){

    jQuery("#arvore-conteudo").html('<div id="loader_1" class="carregando"></div>');

    var ano  = jQuery("#ano_referencia").val();
    var acao = "carregarArvore";

    if (jQuery("#ano_referencia").hasClass('superusuario')) {
        acao = "buscarArvoreMultiplaAno";
    }

    jQuery.ajax({
        type: 'POST',
        async: false,
        url:  'ges_layout.php',
        data: {
            acao: acao,
            ano : ano
        },
        success: function(data) {

            if (typeof JSON != 'undefined') {
                data = JSON.parse(data);
            } else {
                data = eval('(' + data + ')');
            }

            jQuery(".arvore").remove();

            data.html = data.html.replace(/\t/g, "");
            data.html = data.html.replace(/\r/g, "");
            data.html = data.html.replace(/\n/g, "");

            jQuery("#arvore-conteudo").empty().html(data.html);

            criarArvore();

            height = jQuery("#layout-principal-content").height();

            if (height < 500) {
                height = 500;
            }

            jQuery("#grupo-conteudo-arvore").height(height);
            jQuery("#layout-sidebar").height(height + 80);

            esconderArvore();
        }
    });

}