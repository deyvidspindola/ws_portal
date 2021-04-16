jQuery(document).ready(function(){
    
    jQuery('#horario_inicial').mask('99:99', {
        placeholder: ''
    });   
    /* jQuery('#horario_final').mask('99:99', {
        placeholder: ''
    }); */
    jQuery('#hrpitempo').mask('9?99', {
        placeholder: ''
    }); 
   
    jQuery('#marcar_todos_top, #marcar_todos_bottom').checarTodos('.inibir');
   
    jQuery('#gtroid').change(function(){
        
        jQuery('#motaoid').html('<option value="">Escolha</option>');
        jQuery('#hrpiatendente').html('<option value="">Escolha</option>');
        jQuery('#hrpitempo').val('');
        jQuery('#tolerancia').val('');

        //Carregar combo "Tipo de Pausa"
        jQuery.ajax({
            url: 'cad_horario_pausa_item.php',
            type: 'post',
            dataType: 'json',
            data: {
                acao: 'carregarComboTipoPausa',
                gtroid: jQuery(this).val()
            },
            beforeSend: function() {
                jQuery('#motaoid').mostrarCarregando();
                jQuery('#mensagem_alerta_atendente').hide();
            },
            success: function(data) {
       
                jQuery.each(data, function(i, value){
                    jQuery('#motaoid').append('<option value="'+value.motaoid+'">'+value.motamotivo+'</option>');
                });

            }, 
            complete: function() {
                jQuery('#motaoid').esconderCarregando();
            },
            error: function() {
                jQuery('#motaoid').esconderCarregando();
            }
        });
       
        //Carregar combo "Atendente"
        jQuery.ajax({
            url: 'cad_horario_pausa_item.php',
            type: 'post',
            dataType: 'json',
            data: {
                acao: 'carregarComboAtendente',
                gtroid: jQuery(this).val()
            },
            beforeSend: function() {
                jQuery('#hrpiatendente').mostrarCarregando();
            },
            success: function(data) {
               
                jQuery.each(data, function(i, value){
                    jQuery('#hrpiatendente').append('<option value="'+value.usuoid+'">'+value.atendente+'</option>');
                });
   
            }, 
            complete: function() {
                jQuery('#hrpiatendente').esconderCarregando();
            },
            error: function() {
                jQuery('#hrpiatendente').esconderCarregando();
            }
        });
        
        //Caso usuário possua acesso, carregar a grid        
        jQuery(this).carregarGrid();
       
    });
   
    jQuery('#motaoid').change(function(){
        
        jQuery.ajax({
            url: 'cad_horario_pausa_item.php',
            type: 'post',
            dataType: 'json',
            data: {
                acao: 'buscarParametrosPausa',
                motaoid: jQuery('#motaoid').val(),
                gtroid: jQuery('#gtroid').val()
            },
            beforeSend: function() {
                jQuery('#hrpitempo').mostrarCarregando();
                jQuery('#tolerancia').mostrarCarregando();
                jQuery('#hrpitempo').val('');
                jQuery('#tolerancia').val('');
            },
            success: function(data) {
                
                if(data.status) {
                    jQuery('#hrpitempo').val(data.hrptempo);
                    jQuery('#tolerancia').val(data.hrptolerancia);
                } else {
                    
                    if(data.mensagemErro) {
                        jQuery('#mensagemErro').html(data.mensagemErro).fadeIn();
                    }
                    
                }
                
            },
            complete: function() {
                jQuery('#hrpitempo').esconderCarregando();
                jQuery('#tolerancia').esconderCarregando();
            },
            error: function() {
                jQuery('#hrpitempo').esconderCarregando();
                jQuery('#tolerancia').esconderCarregando();
            }
        });
        
    });
   
    jQuery('#motaoid, #hrpiatendente').change(function(){
        //Caso usuário possua acesso, carregar a grid        
        jQuery(this).carregarGrid();
    });
    
    jQuery('input[name="filtro_horario"]').change(function(){
        
        if(jQuery.trim(jQuery('#horario_inicial').val()) == "" /* || jQuery.trim(jQuery('#horario_final').val()) == "" */) {
           return false; 
        }
        
        //Caso usuário possua acesso, carregar a grid        
        jQuery(this).carregarGrid();
    });
    
    jQuery('#horario_inicial').blur(function(){
        
        /* if(jQuery.trim(jQuery('#horario_final').val()) == "") {
            return false;
        } */
        
        //Caso usuário possua acesso, carregar a grid        
        jQuery(this).carregarGrid();     
    });
    
    /* jQuery('#horario_final').blur(function(){
       
        if(jQuery.trim(jQuery('#horario_inicial').val()) == "") {
            return false;
        }
       
        //Caso usuário possua acesso, carregar a grid        
        jQuery(this).carregarGrid();     
    }); */
    
    jQuery('#hrpiatendente').change(function(){
        
        if(jQuery(this).val() == "") { 
            jQuery(this).carregarGrid(true);
            return false;
        }
        
        jQuery('#acao').val('validarPausaObrigatoria');
        
        jQuery.ajax({
            url: 'cad_horario_pausa_item.php',
            type: 'post',
            dataType: 'json',
            data: jQuery('#form').serialize(),                
            beforeSend: function() {
                jQuery('#hrpiatendente').mostrarCarregando();          
                jQuery('#mensagem_alerta_atendente').hide();
            },
            success: function(data) {
               
               if(data && data.mensagemAlerta) {
                   jQuery('#mensagem_alerta_atendente').html(data.mensagemAlerta).fadeIn();
               }
   
            }, 
            complete: function() {
                jQuery('#acao').val('cadastrar');
                jQuery('#hrpiatendente').esconderCarregando();
            },
            error: function() {
                jQuery('#acao').val('cadastrar');
                jQuery('#hrpiatendente').esconderCarregando();
            }
        });
        
    });
   
    jQuery('#bt_confirmar').click(function(){
        jQuery('#hrpimotivo_inibicao').val('');
    
        jQuery.ajax({
            url: 'cad_horario_pausa_item.php',
            type: 'post',     
            dataType: 'json',           
            data: jQuery('#form').not('.marcar_todos').serialize(),
            beforeSend: function() {               
                jQuery('#mensagem_alerta, #mensagem_erro, #mensagem_sucesso').hide();
                jQuery('#resultado_pesquisa').hide();
                jQuery('#carregando_grid').mostrarCarregando();
                resetFormErros();
                jQuery('#mensagem_alerta_atendente').hide();
                jQuery('#bt_confirmar').attr('disabled', 'disabled');
            },
            success: function(data) {
               
                if(data.status) {
                   
                    jQuery('#resultado_pesquisa').html(data.resultado);
                    jQuery('.inibir').trigger('change');
                    jQuery('#resultado_pesquisa').show();
                   
                    if(typeof jQuery('[title]').not('a').not('.data img').not('.mes_ano img').tooltip == 'function'){
                        jQuery('[title]').not('a').not('.data img').not('.mes_ano img').tooltip();
                    }
                   
                    //mostra mensagem de sucesso
                    jQuery('#mensagem_sucesso').html(data.mensagemSucesso).fadeIn();
                   
                } else {
                   
                    if(data.mensagemAlerta){
                        jQuery('#mensagem_alerta').html(data.mensagemAlerta).fadeIn();
                       
                        if(data.dados) {
                            showFormErros(data.dados);
                        }
                       
                    } else {
                        jQuery('#mensagem_erro').html(data.mensagemErro).fadeIn();
                    }                   
                }
               
            }, 
            complete: function() {               
                jQuery('#carregando_grid').esconderCarregando();
                jQuery('#bt_confirmar').removeAttr('disabled');
            },
            error: function() {
                jQuery('#carregando_grid').esconderCarregando();
                jQuery('#bt_confirmar').removeAttr('disabled');
            }
        });
   
    });
   
    jQuery('body').delegate('#bt_inibir', 'click', function(){
        
        //TODO colocar hidden para o motivo        
        var motivo = window.prompt('Digite o motivo da inibição', '');
        
        jQuery('#hrpimotivo_inibicao').val(motivo);
        
        if(motivo) {        
            jQuery('#acao').val('inibir');
            jQuery('#form').submit();
        }
    });

    jQuery('body').delegate('.editar', 'click', function() {
        var hrpioid = jQuery(this).attr('id');
        
        jQuery.ajax({
            url        : 'cad_horario_pausa_item.php',
            type       : 'post',
            data       : 'acao=carregarDadosPausa&hrpioid=' + hrpioid,
            dataType   : 'json',
            beforeSend : function() {
                jQuery('div[id*="mensagem_alerta"], div[id*="mensagem_erro"], div[id*="mensagem_sucesso"]').hide();
                jQuery('#bt_confirmar').attr('disabled', 'disabled');
            },
            complete   : function() {
                jQuery('#bt_confirmar').removeAttr('disabled');
            },
            success    : function(data) {
                if(data.status == 'errorlogin' && data.redirect) {
                    location.href = data.redirect;
                } else if(data.status) {
                    jQuery('#hrpioid').val(data.dados.hrpioid);
                    jQuery('#gtroid').val(data.dados.gtroid);
                    jQuery('#horario_inicial').val(data.dados.hrpihorario_ini);
                    jQuery('#hrpitempo').val(data.dados.hrpitempo);
                    
                    if(data.dados.hrpitolerancia.length == 1) {
                        data.dados.hrpitolerancia = '0' + data.dados.hrpitolerancia;
                    }
                    
                    jQuery('#tolerancia').val(data.dados.hrpitolerancia);
                    
                    carregarComboTipoPausa(data.dados.motaoid);
                    carregarComboAtendente(data.dados.cd_usuario);
                } else {
                    jQuery('#mensagem_' + data.mensagem.tipo).html(data.mensagem.texto).fadeIn();
                }
            },
            error      : function() {
                jQuery('#bt_confirmar').removeAttr('disabled');
            }
        });
        
        return true;
    });

    jQuery('body').delegate('.excluir', 'click', function(){
      
        if (!confirm('Deseja realmente excluir o registro?')){
            return false;
        }
        
        var hrpioid = jQuery(this).attr('id');
        
        jQuery('#acao').val('excluir');
        
        jQuery.ajax({
            url: 'cad_horario_pausa_item.php',
            type: 'post',
            dataType: 'json',            
            data: jQuery('#form').serialize() + '&hrpioid=' + hrpioid,
            beforeSend: function() {       
                jQuery('#mensagem_alerta, #mensagem_erro, #mensagem_sucesso').hide();                
                jQuery('#resultado_pesquisa').hide();
                jQuery('#carregando_grid').mostrarCarregando();
                jQuery('#mensagem_alerta_atendente').hide();
                jQuery('#bt_confirmar').attr('disabled', 'disabled');
            },
            success: function(data) {                
                if(data.status) {
                    
                    if(data.resultado) {
                        jQuery('#resultado_pesquisa').html(data.resultado);
                        jQuery('.inibir').trigger('change');
                        jQuery('#resultado_pesquisa').show();                        
                    } 
                    
                    if(typeof jQuery('[title]').not('a').not('.data img').not('.mes_ano img').tooltip == 'function'){
                        jQuery('[title]').not('a').not('.data img').not('.mes_ano img').tooltip();
                    }
                   
                    //mostra mensagem de sucesso
                    jQuery('#mensagem_sucesso').html(data.mensagemSucesso).fadeIn();
                    
                } else {
                   
                    if(data.mensagemAlerta){
                        //jQuery('#mensagem_alerta').html(data.mensagemAlerta).fadeIn();   
                        jQuery('#mensagem_sucesso').html('Registro excluído com sucesso.').show();             
                    } else {
                        jQuery('#mensagem_erro').html(data.mensagemErro).fadeIn();
                    }                   
                }
            },
            complete: function() {
                jQuery('#acao').val('cadastrar');
                jQuery('#carregando_grid').esconderCarregando();
                jQuery('#bt_confirmar').removeAttr('disabled');
            },
            error: function() {
                jQuery('#acao').val('cadastrar');
                jQuery('#carregando_grid').esconderCarregando();
                jQuery('#bt_confirmar').removeAttr('disabled');
            }
        });
      
    });
   
    //verifico se alguma opção foi selecionada
    jQuery("body").delegate('.inibir', 'change', function(){
        if (jQuery(".inibir:checked").length == 0) {
            jQuery("#bt_inibir").attr('disabled','disabled');
        }else{
            jQuery("#bt_inibir").removeAttr('disabled');
        }
    });
   
});

/**
 * Exibe a imagem de "carregando"
 * 
 * @see jQuery Selector válido http://api.jquery.com/category/selectors/
 * @example jQuery("selector").mostrarCarregando();
 * @return void
 */
jQuery.fn.mostrarCarregando = function() {
    
    jQuery(this).esconderCarregando();
    
    jQuery(this.selector).each(function(){
        
        var img = '';

        if(jQuery(this).is('input')) {

            img = '<img src="modulos/web/images/ajax-loader-circle.gif" class="carregando-input">';

            jQuery(this).parent().append(img);
        }

        if(jQuery(this).is('select')) {

            img = '<img src="modulos/web/images/ajax-loader-circle.gif" class="carregando">';

            jQuery(this).parent().append(img);
        }

        if(jQuery(this).is('div.carregando')) {        
            jQuery(this).slideDown();
        }    
    });
	
    return jQuery(this);
};

/**
 * Oculta a imagem de "carregando"
 * 
 * @see jQuery Selector válido http://api.jquery.com/category/selectors/
 * @example jQuery("selector").esconderCarregando();
 * @return void
 */
jQuery.fn.esconderCarregando = function() {
    jQuery(this.selector).each(function(){
        
        if(jQuery(this).is('input') || jQuery(this).is('select')) {

            jQuery(this ).parent().children('img.carregando, img.carregando-input').remove();

        }

        if(jQuery(this).is('div.carregando')) {  

            jQuery(this).slideUp();
        }   
    });
    
    return jQuery(this);
};


/**
 * Alterna marcando ou desmarcando todos os checkbox
 * 
 * @see jQuery Selector válido http://api.jquery.com/category/selectors/
 * @example jQuery("#checked_all").toggleChecked(".toggle_checkbox");
 * @var Selector selector Seletor dos checkbox que serão marcados ou desmarcados
 * @return void
 */
jQuery.fn.checarTodos = function(selector) {
    var principal = jQuery(this).selector;
    jQuery("body").delegate( jQuery(this).selector , 'click', function(){
		
        var isCheckedAll = jQuery(this).is(':checked');		
        jQuery(selector).each(function(){
			
            var isDisabled = jQuery(this).attr('disabled');
			
            if (isDisabled != 'disabled') {
                jQuery(this).attr('checked', isCheckedAll);
            }
        });
		
    });

    jQuery("body").delegate(selector, 'click', function(){
        var isCheckedAll = true;	
		
        jQuery(selector).not('.marcar_todos').each(function(){			
            var isDisabled = jQuery(this).attr('disabled');
            if (isDisabled != 'disabled') {
                isChecked = jQuery(this).is(':checked');
				
                if (!isChecked) {
                    isCheckedAll = false;
                }
            }
        });		
        jQuery(principal).attr('checked', isCheckedAll);
    });
};

/**
 * Carrega Grid de Pesquisa
 * 
 * @see jQuery Selector válido http://api.jquery.com/category/selectors/
 * @example jQuery("selector").carregarGrid();
 * @return void
 */
jQuery.fn.carregarGrid = function(forcarEnvio) {
    
    if(jQuery.trim(jQuery(this).val()) == "" && !forcarEnvio) {
        return false;
    }

    //Caso usuário possua acesso, carregar a grid       
    jQuery('#acao').val('pesquisar');

    jQuery.ajax({
        url: 'cad_horario_pausa_item.php',
        type: 'post',     
        dataType: 'json',           
        data: jQuery('#form').serialize(),
        beforeSend: function() {                              
            jQuery('#resultado_pesquisa').hide();
            jQuery('#carregando_grid').mostrarCarregando();
            jQuery('#mensagem_alerta_atendente, #mensagem_sucesso, #mensagem_erro').hide();
            jQuery('#bt_confirmar').attr('disabled', 'disabled');
            resetFormErros();
        },
        success: function(data) {

            if(data.status) {

                jQuery('#mensagem_alerta, #mensagem_erro').hide();

                jQuery('#resultado_pesquisa').html(data.resultado);
                jQuery('#resultado_pesquisa').show();

                if(typeof jQuery('[title]').not('a').not('.data img').not('.mes_ano img').tooltip == 'function'){
                    jQuery('[title]').not('a').not('.data img').not('.mes_ano img').tooltip();
                }
                
                jQuery('.inibir').trigger('change');

            } else {

                if(data.mensagemAlerta){

                    jQuery('#mensagem_erro, #mensagem_sucesso').hide();

                    jQuery('#mensagem_alerta').html(data.mensagemAlerta).fadeIn();
                    
                    if(data.dados) {                    
                        showFormErros(data.dados);
                    }      

                } else {

                    jQuery('#mensagem_alerta, #mensagem_sucesso').hide();

                    jQuery('#mensagem_erro').html(data.mensagemErro).fadeIn();
                }                   
            }

        }, 
        complete: function() {               
            jQuery('#acao').val('cadastrar');
            jQuery('#carregando_grid').esconderCarregando();
            jQuery('#bt_confirmar').removeAttr('disabled');
        },
        error: function() {                           
            jQuery('#acao').val('cadastrar');
            jQuery('#carregando_grid').esconderCarregando();
            jQuery('#bt_confirmar').removeAttr('disabled');
        }
    });
};

/**
 * Carrega a combo: Tipo de Pausa.
 * 
 * @param int motaoid Código do Tipo de Pausa
 * @return void
 */
function carregarComboTipoPausa(motaoid) {
    jQuery.ajax({
        url        : 'cad_horario_pausa_item.php',
        type       : 'post',
        data       : {
            acao   : 'carregarComboTipoPausa',
            gtroid : jQuery('#gtroid').val()
        },
        dataType   : 'json',
        beforeSend : function() {
            jQuery('#motaoid').html('<option value="">Escolha</option>');
            jQuery('#motaoid').mostrarCarregando();
        },
        complete   : function() {
            jQuery('#motaoid').esconderCarregando();
        },
        success    : function(data) {
            jQuery.each(data, function(i, value) {
                if(parseInt(value.motaoid) == parseInt(motaoid)) {
                    jQuery('#motaoid').append('<option value="' + value.motaoid + '" selected="selected">' + value.motamotivo + '</option>');
                } else {
                    jQuery('#motaoid').append('<option value="' + value.motaoid + '">' + value.motamotivo + '</option>');
                }
            });
        },
        error      : function() {
            jQuery('#motaoid').esconderCarregando();
        }
    });
}

/**
 * Carrega a combo: Atendente.
 * 
 * @param int cd_usuario Código do Atendente
 * @return void
 */
function carregarComboAtendente(cd_usuario) {
    jQuery.ajax({
        url        : 'cad_horario_pausa_item.php',
        type       : 'post',
        data       : {
            acao   : 'carregarComboAtendente',
            gtroid : jQuery('#gtroid').val()
        },
        dataType   : 'json',
        beforeSend : function() {
            jQuery('#hrpiatendente').html('<option value="">Escolha</option>');
            jQuery('#hrpiatendente').mostrarCarregando();
        },
        complete   : function() {
            jQuery('#hrpiatendente').esconderCarregando();
        },
        success    : function(data) {
            jQuery.each(data, function(i, value) {
                if(parseInt(value.usuoid) == parseInt(cd_usuario)) {
                    jQuery('#hrpiatendente').append('<option value="' + value.usuoid + '" selected="selected">' + value.atendente + '</option>');
                } else {
                    jQuery('#hrpiatendente').append('<option value="' + value.usuoid + '">' + value.atendente + '</option>');
                }
            });
        },
        error      : function() {
            jQuery('#hrpiatendente').esconderCarregando();
        }
    });
}