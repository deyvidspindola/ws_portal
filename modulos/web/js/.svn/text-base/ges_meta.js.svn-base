jQuery(document).ready(function(){
   
   //botão novo
   jQuery("#bt_novo").click(function(){
       window.location.href = "ges_meta.php?acao=cadastrar";
   });
   
   //botão voltar
   jQuery("#bt_voltar").click(function(){
       window.location.href = "ges_meta.php?acao=pesquisar";
   })
   
    //botão exportar
    jQuery("#bt_exportar").click(function(){
        window.location.href = "ges_meta.php?acao=exportarPlanilha&anoReferencia=" + jQuery('#ano_referencia', window.parent.document).val();
    });
   
    //botão gravar
    jQuery("#bt_gravar").click(function(){
        var formula = jQuery('#campo_formula').val();

        if (formula != '') {
            jQuery('#mensagem_alerta').fadeOut();

            if ( formula.match(/\/ *0/g) ) {
                jQuery('#mensagem_alerta').fadeOut();
                jQuery('#campo_formula').addClass('erro');
                jQuery('#mensagem_alerta').html('A formula é matematicamente inválida.');
                jQuery('#mensagem_alerta').fadeIn();
                return false;
            } else {
                jQuery('#campo_formula').removeClass('erro');
                jQuery('#mensagem_alerta').fadeOut();
            }

            formula = formula.replace(/(\[.*?\])/g, 1);


            try {
                var resultado = eval(formula);
            } catch(E) {
                jQuery('#campo_formula').addClass('erro');
                jQuery('#mensagem_alerta').html('A formula é matematicamente inválida.');
                jQuery('#mensagem_alerta').fadeIn();
                return false;
            }
        }

        jQuery('#form_cadastrar').submit();
    });

   

   jQuery("#filtro_gmeano").change(function(event) {
   	
   		if (jQuery.trim(jQuery(this).val()) == '') {
   			jQuery("#filtro_gmeoid").html("<option value=''>-- Escolha --</option>");
   			popularComboFuncionario('','');
   			return;
   		}

   		jQuery("#filtro_gmeoid").next('.carregando').removeClass('invisivel');

   		jQuery.ajax({
   			type: 'POST',
   			async: false,
   			url : 'ges_meta.php',
   			data : {
   				acao: 'buscarNomeMetas',
   				ano:  jQuery(this).val()
   			},
   			success: function(data) {

   				jQuery("#filtro_gmeoid").next('.carregando').addClass('invisivel');

   				data = _parseJSON(data);

   				var options = "<option value=''>-- Escolha --</option>";
   				jQuery(data).each(function(i,v){
   					options += "<option value='" + v.id + "'>" + v.label + "</option>";
   				});

   				jQuery("#filtro_gmeoid").html(options);

   			}

   		});

   });


   //cadastro
   jQuery("#gmeano").change(function(event) {

   		var ano  = jQuery(this).val();
   		var campo = "gmefunoid_responsavel";

   		jQuery("#compartilhar_metas").html("<option value=''>-- Escolha --</option>");
   		popularComboFuncionarioCadastro(campo, '', '', '', ano);

   });


   jQuery("#gmefunoid_responsavel").change(function(event) {
   	/* Act on the event */

   		var ano  = jQuery("#gmeano").val();
   		var campo = "compartilhar_metas";
   		var funcionario = jQuery(this).val();

   		popularComboFuncionarioCadastro(campo, '', '', funcionario, ano);
   });


   jQuery("#filtro_gmeoid").change(function(event) {
   		var meta  = jQuery.trim(jQuery("#filtro_gmeoid").val());
   		var cargo = jQuery.trim(jQuery("#filtro_cargo").val());
   		var campo = "filtro_gmefunoid_responsavel";
   		var funcionario = "";
   		var ano = "";

   		popularComboFuncionarioCadastro(campo, meta,cargo, funcionario, ano);
   });

   jQuery("#filtro_cargo").change(function(event) {
   		var meta  = jQuery.trim(jQuery("#filtro_gmeoid").val());
   		var cargo = jQuery.trim(jQuery("#filtro_cargo").val());
   		var campo = "filtro_gmefunoid_responsavel";
   		var funcionario = "";
   		var ano = "";
   		popularComboFuncionarioCadastro(campo, meta,cargo, funcionario, ano);
   });



   if (funcionarios.length > 0) {

   		jQuery(funcionarios).each(function(i,v){

   			jQuery("#compartilhar_metas option[value='" + v.id + "']").attr('disabled','disabled');

   		});

   }

   //inicio adicionar funcionarios para compartilhamento
   
   jQuery("#adicionar_responsavel").click(function(event) {

   		event.preventDefault();   	

   		if (jQuery.trim(jQuery("#compartilhar_metas").val()) == '') {
   			return false;
   		}

   		var funcionario = new Object();
   		funcionario.id = jQuery.trim(jQuery("#compartilhar_metas").val());
   		funcionario.nome = jQuery("#compartilhar_metas option[value='" + funcionario.id + "']").text();

   		jQuery("#compartilhar_metas option[value='" + funcionario.id + "']").attr('disabled','disabled');

   		jQuery("#compartilhar_metas").val('');   		
   		funcionarios.push(funcionario);
   		carregaCompartilhamento(funcionarios);

   		

   });

   //excluir compartilhamento
   jQuery("body").delegate(".exclui_compartilhamento", "click", function(event) {

   		var funcionarioid = jQuery(this).attr('data-funcionarioid');
   		jQuery("#compartilhar_metas option[value='" + funcionarioid + "']").removeAttr('disabled');

   		var funcionarioTemp = new Array();

   		jQuery(funcionarios).each(function(i,v){
   			if (v.id != funcionarioid) {
   				funcionarioTemp.push(v);   				
   			}
   			return true;
   		});

   		funcionarios = funcionarioTemp;
   		carregaCompartilhamento(funcionarios);
   });


   function carregaCompartilhamento(funcionarios) {
   		var htmlTable = "";
   		var compartilhamento = "";

   		jQuery(funcionarios).each(function(i,v){
   			htmlTable += "<div data-funcionario='" + v.nome + "' >" + v.nome + " <span class='exclui_compartilhamento' data-funcionarioid='" + v.id + "'></span></div>"
   			compartilhamento += v.id + ",";
   		});

   		jQuery("#meta_compartilhamento").val(compartilhamento);
   		jQuery(".box-compartilhamento").html(htmlTable);
   }

   //fim adicionar funcionarios para compartilhamento


   //inicio adicionar indicadores na formula

   jQuery("#adicionar_indicador").click(function(event) {
   
   		event.preventDefault();

   		if (jQuery(this).hasClass('somenteLeitura')) {
   			jQuery("#mensagem_alerta").text('Modificações na fórmula não permitida. Essa meta possui indicadores com valores realizados já preenchidos.').removeClass('invisivel');
   			return false;
   		}	

   		if (jQuery.trim(jQuery("#combo_indicadores").val()) == '') {
   			return false;
   		}

   		var indicador = jQuery.trim(jQuery("#combo_indicadores").val());
   		var formula   = jQuery.trim(jQuery("#campo_formula").val());
   		var nova_formula = jQuery.trim(formula + ' ' + indicador);

   		jQuery("#campo_formula").val(nova_formula);

   });


   //limpa box de fórmula
   jQuery("#limpar_formula").click(function(event) {
   		event.preventDefault();

   		if (jQuery(this).hasClass('somenteLeitura')) {
   			jQuery("#mensagem_alerta").text('Modificações na fórmula não permitida. Essa meta possui indicadores com valores realizados já preenchidos.').removeClass('invisivel');
   			return false;
   		}

   		//TODO
   		//aplicar regra RN003 ao editar

   		jQuery("#campo_formula").val('');
   });

   //bloqueia campo
   jQuery("#campo_formula").on('paste',function(){
   	return false;
   })

   jQuery("#campo_formula").bind('keydown',soNums);

   //fim adicionar indicadores na formula



   //No change do tipo, carrega os indicadores conforme o tipo selecionado

   jQuery("#gmetipo").change(function(event) {
   		
   		if (jQuery.trim(jQuery(this).val()) == '') {
   			jQuery("#combo_indicadores").html("<option value=''>-- Escolha --</option>");
   			popularComboFuncionario('','');
   			return;
   		}

   		jQuery("#combo_indicadores").next('.carregando').removeClass('invisivel');

   		jQuery.ajax({
   			type: 'POST',
   			async: false,
   			url : 'ges_meta.php',
   			data : {
   				acao: 'buscarIndicadores',
   				tipo:  jQuery(this).val()
   			},
   			success: function(data) {

   				jQuery("#combo_indicadores").next('.carregando').addClass('invisivel');

   				data = _parseJSON(data);

   				var options = "<option value=''>-- Escolha --</option>";
   				jQuery(data).each(function(i,v){
   					options += "<option value='" + v.id + "'>" + v.label + "</option>";
   				});

   				jQuery("#combo_indicadores").html(options);

   			}

   		});


   });

   // fim  -- Escolha -- do tipo.


   //excluir meta
   jQuery(".excluir-meta").click(function(event){
   		event.preventDefault();

   		if (confirm('Deseja realmente excluir o item selecionado?')) {

   			var meta = jQuery(this).attr('data-meta');
   			jQuery("body").css('cursor','progress');

   			jQuery.ajax({
   				url: 'ges_meta.php',
   				type: 'POST',
   				data: {
   					acao: 'verificarMeta',
   					meta: meta
   				},
   				success: function(data){
   					data = _parseJSON(data);

   					if (data.exclusao_logica != 0) {
   						alert("Essa meta possui associações, será realizada a exclusão lógica.");
   					}

   					window.location.href = "ges_meta.php?acao=excluir&gmeoid=" + meta + "&logica=" + data.exclusao_logica;

   				

   				}
   			})

   		}

   });


   //copair meta
   jQuery(".copiar-meta").click(function(event) {
   		event.preventDefault();

   		if (confirm("Deseja realmente copiar essa meta?")) {
   			//console.log(jQuery(this).attr('href'));
   			window.location.href = jQuery(this).attr('href');

   		}

   });

   jQuery('#gmeprecisao').change(function(){
      settings.precision = parseInt(jQuery(this).val(), 10);
      if (jQuery('#gmelimite').val() != ''){
        jQuery('#gmelimite').val(maskValue(jQuery('#gmelimite').val()));  
      }
      if (jQuery('#gmelimite_superior').val() != ''){
        jQuery('#gmelimite_superior').val(maskValue(jQuery('#gmelimite_superior').val()));

      }

      if (jQuery('#gmelimite_inferior').val() != ''){
        jQuery('#gmelimite_inferior').val(maskValue(jQuery('#gmelimite_inferior').val()));

      }

   });


   //mascara de moeda

   jQuery(".moeda,.percentual").maskMoney({
        symbol:'', 
        thousands:'.', 
        decimal:',', 
        symbolStay: false, 
        showSymbol:false,
        precision:2, 
        defaultZero: false,
        allowZero: false
    });

    jQuery(".moeda,.percentual").on('paste',function(){
        var id = jQuery(this).attr('id');
        var maxlength = jQuery(this).attr('maxlength');
        
        setTimeout(function(){
            
            var v = jQuery("#"+id).val();
            var vMasc = maskValue(v);
            var nV = v;
            
            if (vMasc.length > maxlength) {
                nV = "";
                var maxChar = (maxlength - (vMasc.length - maxlength));
                var vArray = v.split("");
                var i = 0;
                for ( i ; i <= maxChar ; i++) {
                    nV += vArray[i];
                }   
            }
            
            jQuery("#"+id).val( maskValue(nV) );
            
        },10);
    });
    
    // jQuery(".moeda,.percentual_fix").on('keyup',function(){
    //     var id = jQuery(this).attr('id');
    //     jQuery("#"+id).val( maskValue(jQuery("#"+id).val()) );
    // });

    //fim mascara de moeda
   
});


	settings = {};
    settings.allowNegative = false;
    settings.decimal = ',';
    settings.precision = 0;
    settings.thousands = '.';

    function maskValue(v) {
      settings.precision = parseInt(jQuery('#gmeprecisao').val(), 10);
        
        var strCheck = '0123456789';
        var len = v.length;
        var a = '', t = '', neg='';

        if(len!=0 && v.charAt(0)=='-'){
            v = v.replace('-','');
            if(settings.allowNegative){
                neg = '-';
            }
        }

        for (var i = 0; i<len; i++) {
            if ((v.charAt(i)!='0') && (v.charAt(i)!=settings.decimal)) break;
        }

        for (; i<len; i++) {
            if (strCheck.indexOf(v.charAt(i))!=-1) a+= v.charAt(i);
        }

        var n = parseFloat(a);
        n = isNaN(n) ? 0 : n/Math.pow(10,settings.precision);
        t = n.toFixed(settings.precision);

        i = settings.precision == 0 ? 0 : 1;
        var p, d = (t=t.split('.'))[i].substr(0,settings.precision);
        for (p = (t=t[0]).length; (p-=3)>=1;) {
            t = t.substr(0,p)+settings.thousands+t.substr(p);
        }

        return (settings.precision>0)
        ? neg+t+settings.decimal+d+Array((settings.precision+1)-d.length).join(0)
        : neg+t;
    }


function popularComboFuncionario(meta, cargo) {

	jQuery("#filtro_gmefunoid_responsavel").next('.carregando').removeClass('invisivel');

	jQuery.ajax({
   			type: 'POST',
   			async: false,
   			url : 'ges_meta.php',
   			data : {
   				acao: 'buscarFuncionarios',
   				meta: meta,
   				cargo: cargo
   			},
   			success: function(data) {

   				jQuery("#filtro_gmefunoid_responsavel").next('.carregando').addClass('invisivel');

   				data = _parseJSON(data);

   				var options = "<option value=''>-- Escolha --</option>";
   				jQuery(data).each(function(i,v){
   					options += "<option value='" + v.id + "'>" + v.label + "</option>";
   				});

   				jQuery("#filtro_gmefunoid_responsavel").html(options);

   			}

   		});


}


function popularComboFuncionarioCadastro(campo, meta, cargo, funcionario, ano) {

	jQuery("#"+campo).next('.carregando').removeClass('invisivel');

	jQuery.ajax({
   			type: 'POST',
   			async: false,
   			url : 'ges_meta.php',
   			data : {
   				acao: 'buscarFuncionarios',
   				meta: meta,
   				cargo: cargo,
   				funcionario: funcionario,
   				ano: ano
   			},
   			success: function(data) {

   				jQuery("#"+campo).next('.carregando').addClass('invisivel');

   				data = _parseJSON(data);

   				var options = "<option value=''>-- Escolha --</option>";
   				jQuery(data).each(function(i,v){
   					options += "<option value='" + v.id + "'>" + v.label + "</option>";
   				});

   				jQuery("#"+campo).html(options);

   			}

   		});


}

function _parseJSON(data) {

	if (typeof JSON != 'undefined') {
	    data = JSON.parse(data);
	} else {
	    data = eval('(' + data + ')');
	}

	return data;

}


function soNums(e){

    //teclas adicionais permitidas (tab,delete,backspace,setas direita e esquerda)
    keyCodesPermitidos = new Array(32,39,111,106,109,107,110);
     
    //numeros e 0 a 9 do teclado alfanumerico
    for(x=48;x<=57;x++){
        keyCodesPermitidos.push(x);
    }
     
    //numeros e 0 a 9 do teclado numerico
    for(x=96;x<=105;x++){
        keyCodesPermitidos.push(x);
    }
     
    //Pega a tecla digitada
    keyCode = ( window.event ) ? e.keyCode : e.which;

    //Verifica se a tecla digitada é permitida
    if ($.inArray(keyCode,keyCodesPermitidos) != -1){
        return true;
    }   
    return false;
}
