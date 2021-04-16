           
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css">
    <!--[if IE]>
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style-ie.css">
    <![endif]-->    
    
    <link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css"> 
        
    <script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script> 
    <script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>  
    
    <script type="text/javascript" src="modulos/web/js/lib/validacao.js"></script>	
	<script type="text/javascript" src="modulos/web/js/cad_cicloequipamentoCT.js"></script>
    
	<script>
	$(document).ready(function(){
		$('#buttonNovo').click(novo);
		$('.editar').click(editar);
		
		$("#carregando").hide();
	});
	</script>


<div class="modulo_titulo">Cadastro de Ciclo de Equipamentos Cargo Tracck</div>
	<div class="modulo_conteudo">