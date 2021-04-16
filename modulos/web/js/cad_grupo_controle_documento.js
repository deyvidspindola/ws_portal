jQuery(document).ready(function(){
   
   //botão novo
   jQuery("#bt_novo").click(function(){
       window.location.href = "cad_grupo_controle_documento.php?acao=cadastrar";
   });
   
   //botão voltar
   jQuery("#bt_voltar").click(function(){
       window.location.href = "cad_grupo_controle_documento.php?acao=pesquisar";
   });

   jQuery(".bt_excluir").click(function(event){
   	event.preventDefault();

   	if (confirm("Deseja realmente excluir o registro?")) {
   		window.location.href = jQuery(this).attr('href');
   	}

   });
   
});