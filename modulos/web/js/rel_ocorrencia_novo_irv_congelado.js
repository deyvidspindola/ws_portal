jQuery(document).ready(function(){

   //periodo
   jQuery("#ococdperiodo_inicial").periodo("#ococdperiodo_final");

   //botão novo
   jQuery("#bt_novo").click(function(){
       window.location.href = "rel_ocorrencia_novo_irv_congelado.php?acao=cadastrar";
   });

   //botão novo
   jQuery("#gerar_pdf").click(function(){
      jQuery("#acao").val("visualizacaoRelatorioCongelado");
      jQuery("#sub_acao").val("gerarPdf");
      jQuery("#form").submit();
   });

   //botão voltar
   jQuery("#bt_voltar").click(function(){
       window.location.href = "rel_ocorrencia_novo_irv_congelado.php";
   });


   jQuery("#filtrar_cpf_cnpj").mask('9?9999999999999',{placeholder:''});

    jQuery("#filtrar_valor_carga").maskMoney({
        symbol:'', 
        thousands:'.', 
        decimal:',', 
        symbolStay: false, 
        showSymbol:false,
        precision:2, 
        defaultZero: false,
        allowZero: false
    });

    jQuery("#filtrar_valor_carga").on('paste',function(){
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
    
    jQuery("#filtrar_valor_carga").on('keyup',function(){
        var id = jQuery(this).attr('id');
        jQuery("#"+id).val( maskValue(jQuery("#"+id).val()) );
    });




   jQuery(".excluir").click(function(event){
   		event.preventDefault();

   		var url  = jQuery(this).attr('href');

   		if (!confirm('Deseja realmente excluir o relatório?')) {
   			return false;
   		}

   		window.location.href = url;
   });


   jQuery(".reativar").click(function(event){
   		event.preventDefault();

   		var url  = jQuery(this).attr('href');

   		if (!confirm('Deseja realmente reativar o relatório?')) {
   			return false;
   		}

   		window.location.href = url;
   });



   montarFormulario();
   jQuery("#ococdtipo_relatorio").change(function(event) {
      if (jQuery(this).val() == 'M') {
          buscarMotivos(1);
      }else {
          buscarMotivos(0);
      }
      montarFormulario();
   });


   jQuery("#filtrar_marca").change(function(event) {
     /* Act on the event */
     buscarModelos(jQuery.trim(jQuery(this).val()));
   });

    jQuery("#filtrar_estado").change(function(event) {
     /* Act on the event */
     buscarCidades(jQuery.trim(jQuery(this).val()));
   });


   jQuery("#visualizar_relatorio").click(function(){
   		jQuery("#form_resultado").submit();
   });


   jQuery('#marcar_todos').change(function(){
        if (jQuery(this).is(':checked')) {
            jQuery('.selecionar_check').attr('checked','checked');
            jQuery("#visualizar_relatorio").removeAttr('disabled');
        } else {
            jQuery('.selecionar_check').removeAttr('checked');
            jQuery("#visualizar_relatorio").attr('disabled','disabled');
        }
    });

	if (jQuery(".selecionar_check:checked").length == jQuery(".selecionar_check").length) {
	    jQuery("#marcar_todos").attr('checked','checked');
	}

	if (jQuery(".selecionar_check:checked").length > 0) {
		jQuery("#visualizar_relatorio").removeAttr('disabled');
	}

    jQuery(".selecionar_check").change(function(){

        if (jQuery(".selecionar_check:checked").length == 0) {
            jQuery("#visualizar_relatorio").attr('disabled','disabled');
        }else {
            jQuery("#visualizar_relatorio").removeAttr('disabled');
        }

        if (jQuery(".selecionar_check:checked").length < jQuery(".selecionar_check").length) {
            jQuery('#marcar_todos').removeAttr('checked');
        }else{

            if (jQuery(".selecionar_check:checked").length == jQuery(".selecionar_check").length) {
                jQuery("#marcar_todos").attr('checked','checked');
            }
        }
    });




    jQuery("#btn_pesquisa_relatorio_congelado").click(function() {

    	jQuery("#acao").val("visualizacaoRelatorioCongelado");
    	jQuery("#sub_acao").val("pesquisarCongelados");
    	jQuery("#form").submit();
    });

    /*
    * Ações do botão Gerar Arquivo
    */
   jQuery("#btn_gerar_xls").click(function(){

        jQuery("#baixarXls").addClass('invisivel');
        jQuery("#loader_xls").removeClass('invisivel');

        jQuery("#acao").val("visualizacaoRelatorioCongelado");
        jQuery("#sub_acao").val("gerar_csv");
        jQuery("#form").submit();
   })

});

function buscarMotivos(motivosMacro) {

  jQuery.ajax({
    url: 'rel_ocorrencia_novo_irv_congelado.php',
    type: 'POST',
    data: {
      acao: 'listarMotivosAjax',
      motivos_macro: motivosMacro
    },
    success: function(data) {

        if (typeof JSON != 'undefined') {
          data = JSON.parse(data);
        } else {
          data = eval('(' + data + ')');
        }

        var option = '<option value="">Escolha</option>';
        jQuery(data).each(function(i,v){
          option += '<option value="' + v.id + '">' + v.label + '</option>';
        });
        jQuery("#filtrar_motivo").html(option);
    }
  });

}


function buscarModelos(marca){

  jQuery.ajax({
    url: 'rel_ocorrencia_novo_irv_congelado.php',
    type: 'POST',
    data: {
      acao: 'listarModelosAjax',
      marca_id: marca
    },
    success: function(data) {

        if (typeof JSON != 'undefined') {
          data = JSON.parse(data);
        } else {
          data = eval('(' + data + ')');
        }

        var option = '<option value="">Todas</option>';
        jQuery(data).each(function(i,v){
          option += '<option value="' + jQuery.trim(v.label) + '">' + v.label + '</option>';
        });
        jQuery("#filtrar_modelo").html(option);
    }
  });

}

function buscarCidades(uf) {

  jQuery.ajax({
    url: 'rel_ocorrencia_novo_irv_congelado.php',
    type: 'POST',
    data: {
      acao: 'listarCidadesAjax',
      uf: uf
    },
    success: function(data) {

        if (typeof JSON != 'undefined') {
          data = JSON.parse(data);
        } else {
          data = eval('(' + data + ')');
        }

        var option = '<option value="">Todas</option>';
        jQuery(data).each(function(i,v){
          option += '<option value="' + v.id + '">' + v.label + '</option>';
        });
        jQuery("#filtrar_cidade").html(option);
    }
  });
}


function montarFormulario(){

  jQuery("div.formularios").css("display","none");
  jQuery("div.formularios input, div.formularios select").attr('disabled','disabled');
   switch(jQuery.trim(jQuery("#ococdtipo_relatorio").val())) {
        case "A":
          jQuery(".formulario_analitico").css("display",'block');
          jQuery("div.formulario_analitico input, div.formulario_analitico select").removeAttr('disabled','disabled');
        break;

        case "P":
        case "D":
          jQuery(".formulario_apoio").css("display",'block');
          jQuery("div.formulario_apoio input, div.formulario_apoio select").removeAttr('disabled','disabled');
        break;

        case "M":
          jQuery(".formulario_macro").css("display",'block');
          jQuery("div.formulario_macro input, div.formulario_macro select").removeAttr('disabled','disabled');
        break;

        case "S":
          jQuery(".formulario_sintetico").css("display",'block');
          jQuery("div.formulario_sintetico input, div.formulario_sintetico select").removeAttr('disabled','disabled');
        break;

        case "R":
          jQuery(".formulario_sintetico_resumido").css("display",'block');
          jQuery("div.formulario_sintetico_resumido input, div.formulario_sintetico_resumido select").removeAttr('disabled','disabled');
        break;
      }

}

function abre_carta(id){
    col = '';
    if (confirm('Deseja imprimir os dados de contato?')){
        col = '&contato=true';
    }
    window.open('carta_ocorrencia.php?ocooid='+id+col);
}



settings = {};
    settings.allowNegative = false;
    settings.decimal = ',';
    settings.precision = 2;
    settings.thousands = '.';

    function maskValue(v) {
        
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