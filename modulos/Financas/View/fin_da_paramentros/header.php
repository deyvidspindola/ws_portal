<?php
@cabecalho();
@require_once ("lib/funcoes.js");
@require_once ("lib/funcoes.php");

?>
<head>            

    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css" />
    <link type="text/css" rel="stylesheet" href="includes/css/base_form.css">  	
    <link type="text/css" rel="stylesheet" href="modulos/web/css/fin_da_parametros.css">  	
<!--[if IE]>
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style-ie.css">
<![endif]--> 
<script language="javascript" src="js/jquery.min.js"></script>   
<script>
$(document).ready(function(){
	
	$("#parametro_select_dataenvio").click(function(event){	
		event.preventDefault();	
			
		 if ($("#parametro_select_dataenvio").val() != "") {	
		// habilitar os select	Data do 1° envio seja selecionada 	
        		$("#parametro_select_datainicial").removeAttr("disabled");
        		$("#parametro_select_datafinal").removeAttr("disabled");
        		$("#parametro_select_mes").removeAttr("disabled"); 

		   } else if ($("#parametro_select_dataenvio").val() == "") {
		   	// limpa as variavel e desabilita o campo caso Data do 1° envio seja vazia
				$('#parametro_select_datainicial').val('');
	 			$("#parametro_select_datainicial").attr("disabled", "disabled");

	 			$('#parametro_select_datafinal').val('');
	 			$("#parametro_select_datafinal").attr("disabled", "disabled");

	 			$('#parametro_select_mes').val('');
	 			$("#parametro_select_mes").attr("disabled", "disabled");
		   }

	}),
	
	$("#parametro_select_dataenvio_1").click(function(event){	
		event.preventDefault();	
			
		 if ($("#parametro_select_dataenvio_1").val() != "") {
		 	// habilitar os select	Data do 2° envio seja selecionada
        		$("#parametro_select_diainicial_1").removeAttr("disabled");
        		$("#parametro_select_diaFinal_1").removeAttr("disabled");
        		$("#parametro_select_mes_1").removeAttr("disabled");

		   } else if ($("#parametro_select_dataenvio_1").val() == ""){
		   	// limpa as variavel e desabilita o campo caso Data do 2° envio seja vazia
			   	$('#parametro_select_diainicial_1').val('');
	 			$("#parametro_select_diainicial_1").attr("disabled", "disabled");

	 			$('#parametro_select_diaFinal_1').val('');
	 			$("#parametro_select_diaFinal_1").attr("disabled", "disabled");

	 			$('#parametro_select_mes_1').val('');
	 			$("#parametro_select_mes_1").attr("disabled", "disabled");
		   }
	});
});
</script>
<script language="Javascript">

  
function confirmacao(id) {
     var resposta = confirm("Tem certeza que deseja excluir este agendamento?");
 
     if (resposta == true) {
          window.location.href = "fin_da_parametros.php?excluir="+id;
     }
}
function valida() {
	if (jQuery("input[name='banco[]']:checked").length > 0) {
		//return true;
		if(form_parametros.email.value == ''){
			
	         var resposta = confirm("Você não selecionou nenhum EMAIL para enviar o aviso do arquivo de remessa, tem certeza que deseja continuar?"); 
		     if (resposta == true) 
		          window.location.href = "fin_da_parametros.php";	        
		     else
			  return false;
	     }
	} else {				
		 var resposta = confirm("Você não selecionou nenhum BANCO para gerar o arquivo de remessa, tem certeza que deseja continuar?"); 
	     if (resposta == true) 
	          window.location.href = "fin_da_parametros.php";	        
	     else
		  return false;
	}
	
	
}
</script>
<script type="text/javascript">
$(function(){
	$('#limpa1').click(function(){
	  $('#parametro_select_dataenvio').val('');
	  $('#parametro_select_datainicial').val('');
	   $("#parametro_select_datainicial").attr("disabled", "disabled");	  
	  $('#parametro_select_datafinal').val('');
	   $("#parametro_select_datafinal").attr("disabled", "disabled");	  
	  $('#parametro_select_mes').val('');
	   $("#parametro_select_mes").attr("disabled", "disabled");
	});
	
	$('#limpa2').click(function(){
	  $('#parametro_select_dataenvio_1').val('');
	   $("#parametro_select_diainicial_1").attr("disabled", "disabled");
	  $('#parametro_select_diainicial_1').val('');
	   $("#parametro_select_diaFinal_1").attr("disabled", "disabled");
	  $('#parametro_select_diaFinal_1').val('');
	  $("#parametro_select_mes_1").attr("disabled", "disabled");
	  $('#parametro_select_mes_1').val('');
	});
});
	
</script>

</head>
<div align="center"> 	
	<table width="98%" class="tableMoldura">		
		<tr class="tableTitulo">
			<td><h1>Débito Automático - Parâmetros</h1></td>
		</tr>
		
		<tr>
			<td>
				<?php if($retorno['tipo'] !=""){?>	
				 <div class="separador"></div>		
			     <div class="mensagem <?php print $retorno['tipo']; ?>" id="composicao"><?php print $retorno['msg']; ?></div>
		       <?php }?>	
		    </td>
		</tr>				
		<tr>
			<td align="center">	
		<div class="separador"></div>	
		