jQuery(document).ready(function(){

   //botão voltar
   jQuery("#bt_voltar").click(function(){
       window.location.href = "rel_fotos_ordem_servico.php";
   });


    jQuery(".numeric").keypress(function (e) {
	    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
	        return false;
	    }
    });

    jQuery('.numeric').bind('paste', function(e) {
        e.preventDefault();
    });
   
});