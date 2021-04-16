function css_browser_selector(u){var ua=u.toLowerCase(),is=function(t){return ua.indexOf(t)>-1},g='gecko',w='webkit',s='safari',o='opera',m='mobile',h=document.documentElement,b=[(!(/opera|webtv/i.test(ua))&&/msie\s(\d)/.test(ua))?('ie ie'+RegExp.$1):is('firefox/2')?g+' ff2':is('firefox/3.5')?g+' ff3 ff3_5':is('firefox/3.6')?g+' ff3 ff3_6':is('firefox/3')?g+' ff3':is('gecko/')?g:is('opera')?o+(/version\/(\d+)/.test(ua)?' '+o+RegExp.$1:(/opera(\s|\/)(\d+)/.test(ua)?' '+o+RegExp.$2:'')):is('konqueror')?'konqueror':is('blackberry')?m+' blackberry':is('android')?m+' android':is('chrome')?w+' chrome':is('iron')?w+' iron':is('applewebkit/')?w+' '+s+(/version\/(\d+)/.test(ua)?' '+s+RegExp.$1:''):is('mozilla/')?g:'',is('j2me')?m+' j2me':is('iphone')?m+' iphone':is('ipod')?m+' ipod':is('ipad')?m+' ipad':is('mac')?'mac':is('darwin')?'mac':is('webtv')?'webtv':is('win')?'win'+(is('windows nt 6.0')?' vista':''):is('freebsd')?'freebsd':(is('x11')||is('linux'))?'linux':'','js']; c = b.join(' '); h.className += ' '+c; return c;}; css_browser_selector(navigator.userAgent);
    "use strict";
    var $ = jQuery;
 

jQuery(document).ready(function() {
	
	jQuery("body").delegate('#botao_salvar_assistencia','click', function(){
        jQuery("#acao").val('assistenciaSalvar');
        jQuery("#assistencia_form").submit();
	});
	
	jQuery("body").delegate('#botao_salvar_estatistica','click', function(){
        jQuery("#acao").val('estatisticaSalvar');
        jQuery("#estatistica_form").submit();
	});
	
	jQuery("body").delegate('#botao_salvar_preestatistica','click', function(){
		if(!confirm( 'Deseja gravar os valores padrão?' )) return false;
        jQuery("#acao").val('estatisticaDefault');
        jQuery("#estatistica_form").submit();
	});
	
	jQuery("body").delegate('#botao_salvar_cron','click', function(){
        jQuery("#acao").val('cronSalvar');        
        jQuery("#cron_form").submit();
	});
	
	/**
	 * Função para carregar os defeitos alegados na página de assistência.
	 */
	function CarregarDefeitosAlegados() {
		var itensOs = jQuery("input[name='itens_os[]']:checked");
		var itens = new Array();
		itensOs.each(function(i, e) {
			itens.push(jQuery(e).val());
		});
		
		var tiposOs = jQuery("input[name='os_tipo_id[]']:checked");
		var tipos = new Array();
		tiposOs.each(function(i, e) {
			tipos.push(jQuery(e).val());
		});
		
		if ((tipos.length > 0) && (itens.length > 0)) {
			if (jQuery("#defeitos_carregados_assistencia").val())
				return;
			else
				jQuery("#defeitos_carregados_assistencia").val('true');
							
			jQuery.post('man_parametrizacao_ura.php?acao=assistenciaBuscarDefeitos', function(data) {			
				jQuery.each(data, function(key, defeito) { 
					var h = "<li><label for='defeito_id_"+defeito.id+"'>"+defeito.descricao+"</label><input type='checkbox' id='defeito_id_"+defeito.id+"' name='defeito_id[]' value='"+defeito.id+"' /></li>";
					jQuery('#ulDefeitos').append(h);
				});
			}, 'json');
		} else {
			jQuery('#ulDefeitos').empty();
			jQuery("#defeitos_carregados_assistencia").val('');
		}
	}
	
	jQuery('input[name="itens_os[]"]').click(function() {
		CarregarDefeitosAlegados();
	});
	
	jQuery('input[name="os_tipo_id[]"]').click(function() {
		CarregarDefeitosAlegados();
	});
	
    //Adcionar Clientes 

	
	
	jQuery("body").delegate("button[name='cpx_botao_pesquisa_cliente_nome']", 'click', function(){
		

		 jQuery('#msg_inclusao_manual').hide();

	});


    jQuery("body").delegate('#btn_adicionar', 'click', function(e){
    	
    	
    	jQuery(".erro").removeClass("erro");
        jQuery('#msg_inclusao_manual').hide();
        
        var validacao = true;
        
        
       removeAlerta();
        
        if($("input[name='cpx_valor_cliente_nome']").val()==''){
			//jQuery("#msg_inclusao_manual").attr("class", "mensagem alerta");			
     		//jQuery('#msg_inclusao_manual').html('Selecione um Cliente.').fadeIn();  
        	criaAlerta('Selecione um Cliente.');
		       		
		   		validacao = false;            
		}
    	jQuery("input[name='puecliente_frota[]']").each( function(key, valor) { 
    		
    		var v1 = $("input[name='cpx_valor_cliente_nome']").val();
    		var v2 = valor.value;
    		//console.info(v1);
    		
    		
    		if(v1==v2){
    			//jQuery("#msg_inclusao_manual").attr("class", "mensagem alerta");
         		//jQuery('#msg_inclusao_manual').html('Este Cliente já foi adicionado.').fadeIn();  	
    			criaAlerta('Este Cliente já foi adicionado.');   		
    		   		validacao = false;            
    		}
    		
    	});
    	
    
    	
    		
    	
	       
       if (!validacao)
           return false;
       
    	
    	var texto = jQuery('#puecliente_frota').html();    
    	$('#puecliente_frota').html(texto+'<div class="listagem"><table style="width:100%;margin:0px;" id ="puecliente_frotaX"><tr><td>'+ $("input[name='cpx_pesquisa_cliente_nome']").val()+
    			'<input type="hidden"  name = "puecliente_frota[]"  id = "puecliente_frota_'+$("input[name='cpx_valor_cliente_nome']").val()+'"  value="'+$("input[name='cpx_valor_cliente_nome']").val()+'" /></td> '+
    			'<td style="width:18px;"><button id="clear_cliente_nome" class="componente_btn_limpar" name="clear_cliente_nome"  type="button"> X </button></td></tr></table></div>'); 
    	 
    	
    }); 
    
    jQuery("input[name='cpx_valor_cliente_nome']").hide()
    
    jQuery('body').delegate('#clear_client', 'click', function(){
    	jQuery('#puecliente_frota').html('');
    	
    	

    	});
    
    
    jQuery('body').delegate(".componente_btn_limpar", 'click', function(){
    		$(this).closest('table').remove();
   });

    
  // Formatação para o campo "Número da Prioridade" aceitar apenas números.
    jQuery("#pueperiodo_atualizacao").attr("maxlength", 3);
    jQuery("#pueperiodo_atualizacao").keypress(function(){
        formatar(this, '@');
    });
    jQuery("#pueperiodo_atualizacao").blur(function(){
        revalidar(this, '@', '');
    });
	
    
    jQuery("#puependencia_financeira").attr("maxlength", 3);
    jQuery("#puependencia_financeira").keypress(function(){
        formatar(this, '@');
    });
    jQuery("#puependencia_financeira").blur(function(){
        revalidar(this, '@', '');
    });
	
    
    jQuery("#pupacionamento").attr("maxlength", 3);
    jQuery("#pupacionamento").keypress(function(){
        formatar(this, '@');
    });
    jQuery("#pupacionamento").blur(function(){
        revalidar(this, '@', '');
    });
    
    
    
    jQuery("#puppendencia_financeira").attr("maxlength", 3);
    jQuery("#puppendencia_financeira").keypress(function(){
        formatar(this, '@');
    });
    jQuery("#puppendencia_financeira").blur(function(){
        revalidar(this, '@', '');
    });
    
    
	
});
