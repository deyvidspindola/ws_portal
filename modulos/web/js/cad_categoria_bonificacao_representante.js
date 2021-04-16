jQuery(document).ready(function(){
   
   //bot�o novo
   jQuery("#bt_novo").click(function(){
	   
       window.location.href = "cad_categoria_bonificacao_representante.php?acao=cadastrar";
   });
   
   //bot�o voltar
   jQuery("#bt_voltar").click(function(){
       window.location.href = "cad_categoria_bonificacao_representante.php";
   })

   
   jQuery("body").delegate('#btn_excluir','click', function(){
	   if( confirm('Deseja realmente excluir esta categoria?') ){
		   var id = jQuery(this).attr('rel');
		   window.location.href ="cad_categoria_bonificacao_representante.php?acao=excluir&bonrecatoid="+id; 
	   } else {
	      e.preventDefault();
	   }
   });
   
   jQuery('#bt_gravar').click(function(){
	   
	   if(jQuery('#acao').val() == 'editar') {
		   if (confirm('Deseja realmente Alterar este registro?')) { 
				  jQuery('#form_cadastrar').submit();
				  }else{
					  return false;
				  }
	   }
	
   });
   
   
   
});