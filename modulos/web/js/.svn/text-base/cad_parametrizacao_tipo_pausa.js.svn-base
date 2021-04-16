jQuery(document).ready(function(){
   jQuery('#hrptempo').mask('9?99', {placeholder: ''});   
   
   jQuery('body').delegate('.excluir', 'click', function(){
       
       var confirm = window.confirm('Deseja realmente excluir o registro?');
       
       if(confirm) {
            jQuery('#hrpoid').val(jQuery(this).attr('id'));
            jQuery('#acao').val('excluir');

            jQuery('#form').submit();
       }
       
   });
   
});