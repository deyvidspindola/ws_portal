<?php
@cabecalho();
@require_once ("lib/funcoes.js");
@require_once ("lib/funcoes.php");

?>
<head>            

    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css" />   	
    <link type="text/css" rel="stylesheet" href="includes/css/calendar.css">    
    
     <script src="lib/layout/1.1.0/jquery/jquery.maskedinput.min.js"></script>
     <script src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>     
     <script language="Javascript" type="text/javascript" src="includes/js/calendar.js"></script>
     <script src="lib/layout/1.1.0/bootstrap.js"></script>
    
<!--[if IE]>
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style-ie.css">
<![endif]-->    
 
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery("#pesquisar").on('click', function() {
			jQuery("#composicao").hide();
			
			jQuery('#dataInicial_v').css({color:"#000", backgroundColor: "#FFF", border: "1px solid #ccc" });
			jQuery('#dataFinal_v').css({color:"#000", backgroundColor: "#FFF", border: "1px solid #ccc" }); 
			
			jQuery('#dataInicial_p').css({color:"#000", backgroundColor: "#FFF", border: "1px solid #ccc" }); 
			jQuery('#dataFinal_p').css({color:"#000", backgroundColor: "#FFF", border: "1px solid #ccc" }); 
			
			jQuery('#dataInicial_c').css({color:"#000", backgroundColor: "#FFF", border: "1px solid #ccc" }); 
			jQuery('#dataFinal_c').css({color:"#000", backgroundColor: "#FFF", border: "1px solid #ccc" }); 

			jQuery('#dataInicial_ret').css({color:"#000", backgroundColor: "#FFF", border: "1px solid #ccc" }); 
			jQuery('#dataFinal_ret').css({color:"#000", backgroundColor: "#FFF", border: "1px solid #ccc" }); 
			
			
		});
	});
	
jQuery(function($){
   $("#dataInicial_v").mask("99/99/9999");
   $("#dataFinal_v").mask("99/99/9999");
   
   $("#dataInicial_p").mask("99/99/9999");
   $("#dataFinal_p").mask("99/99/9999");
   
   $("#dataInicial_c").mask("99/99/9999");
   $("#dataFinal_c").mask("99/99/9999");

   $("#dataInicial_ret").mask("99/99/9999");
   $("#dataFinal_ret").mask("99/99/9999");
   
   
});
</script>
</head>
<div class="modulo_titulo">Visualização de Transações de Débito Automático</div>
  <div class="modulo_conteudo">
  
	