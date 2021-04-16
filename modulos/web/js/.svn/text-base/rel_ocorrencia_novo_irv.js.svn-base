
jQuery(document).ready(function() {

	jQuery("body").delegate('#ocorrencias_marcar_todos', 'click', function(){
		if (jQuery(this).attr("checked") == "checked"){
			jQuery(".chk_clioid").attr("checked", "checked");
		}
		else{
            jQuery(".chk_clioid").removeAttr("checked");
		}
	});

	jQuery("body").delegate('#enviar_email', 'click', function(){
	   if(jQuery(".chk_clioid:checked").length == 0){
		   alert('Selecione ao menos uma ocorrência.');
		   return;
       }

       document.frm.oco.value="";
       for(var i=0;i<document.frm.elements.length;i++){
           var e=document.frm.elements[i];
           if((e.name.substring(0,3)=="fc_") && (e.checked)){
               document.frm.oco.value+= ","+document.frm.elements[i].value;
           }
       }
       oco=document.frm.oco.value;

	   window.open('send_layout_emails.php?acao=emailOcorrencia&reset=t&origem=RO&oco='+oco  ,'','status,scrollbars=yes,menubar=no,toolbar=no,width=990,height=740,resizable=yes');
	});


	jQuery("body").delegate('#classificar, #ordenar', 'change', function(){
		jQuery('#frm').submit();
	});

	jQuery("body").delegate('.seleciona_filtro', 'change', function(){
		var coluna = '.column_'+jQuery(this).val();
		if(jQuery(this).is(':checked')) {
			jQuery(coluna).show();
		}
		else {
			jQuery(coluna).hide();
		}
	});

});