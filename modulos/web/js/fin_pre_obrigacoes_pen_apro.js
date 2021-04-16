jQuery(document).ready(function(){

    /**
     * No modo quirks a <th> não estava pegando a classe
     * Removendo e adicionando funciona!
     */
    jQuery('#quirksHack').removeClass('selecao');
    jQuery('#quirksHack').addClass('selecao');
    
    //bot�o gerar previs�o
    jQuery("#bt_gerarPrevisao").click(function(){
        jQuery('#acao').val('prepararPrevisao');
        jQuery('#form').submit();
    });
    
    jQuery("#bt_gerarRevisao").click(function(){
        jQuery('#acao').val('prepararPrevisao');
        jQuery('#form').submit();
    });
    
    //bot�o consultar
    jQuery("#bt_consultar").click(function(){
        jQuery('#acao').val('consultarPrevisao');
        jQuery('#form').submit();
    });
    
    //bot�o processar
    jQuery("#bt_processar").click(function(){
        jQuery('#acao').val('processarPrevisao');
        jQuery('#form').submit();
    });
    
    jQuery("#alterarStatus").click(function(){
        jQuery('#form').submit();
    });

    $('#voltar').click(function() {
        //history.back()
    	window.location.href = "fin_pre_obrigacoes_pen_apro.php";
    });
    
   $("#obrigacaoFinPenApro").click(function() {
	   if ($(this).is(':checked')) {               
           $('.desabilitavel').attr("disabled", true);  
           $('#tipo').attr("disabled", true);
           $('#status option[value=2]').attr('selected','selected');
         ///  $('select:option[value="2"]').prop('selected', true);
           
        }else if ($(this).not(':checked')) {   	                 
        	 $('.desabilitavel').attr("disabled", false);  
             $('#tipo').attr("disabled", false);                
             $('#status option[value=]').attr('selected','selected');
        } 
	   
	});
    
 $(document).ready(function(){
        $("#valor").maskMoney({showSymbol:false, symbol:"", precision: 2, defaultZero:false, allowZero:true, decimal:",", thousands:"."});
  });
    
    //bot�o excluir    
    jQuery("#bt_excluir").click(function(){

        var confirm = window.confirm('Deseja realmente excluir as previs�es n�o processadas, conforme os filtros informados?');

        if(confirm) {
            jQuery('#acao').val('excluirPrevisao');
            jQuery('#form').submit();
        }

    });
   
});