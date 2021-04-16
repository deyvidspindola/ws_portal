jQuery(document).ready(function(){

	 jQuery('#frm_importar').submit(function(){
		
		 $("#mensagem_erro").html("Existem campos obrigatórios não preenchidos.").hide();
		 $("#mensagem_sucesso").html("Arquivo importado com sucesso.").hide();

		 
		 $('#mes_ano').removeClass("erro");
		 $('#arquivo_csv').removeClass("erro");
		 $("#mensagem_alerta").html("Existem campos obrigatórios não preenchidos.").hide();
		 $("#mensagem_alerta").html("Existem campos obrigatórios não preenchidos.").hide();
		 
		 
		if(jQuery("#mes_ano").val() == '' && jQuery("#arquivo_csv").val() == '' ){
			$("#mensagem_alerta").html("Existem campos obrigatórios não preenchidos.").hide();
			$("#mensagem_alerta").html("Existem campos obrigatórios não preenchidos.").showMessage();
			$('#mes_ano').addClass('erro');
			$('#arquivo_csv').addClass('erro');
			return false;
		} 
		else if(jQuery("#mes_ano").val() == '' || jQuery("#mes_ano").val() == null || jQuery("#mes_ano").val().length == 0  || typeof jQuery("#mes_ano").val() === "undefined"){
				$("#mensagem_alerta").html("Existem campos obrigatórios não preenchidos.").hide();
				$("#mensagem_alerta").html("Existem campos obrigatórios não preenchidos.").showMessage();
				$('#mes_ano').addClass('erro');
				return false;
		}else if(jQuery("#arquivo_csv").val() == '' || jQuery("#arquivo_csv").val() == null || jQuery("#arquivo_csv").val().length == 0  || typeof jQuery("#arquivo_csv").val() === "undefined"){
				$("#mensagem_alerta").html("Existem campos obrigatórios não preenchidos.").hide();
				$("#mensagem_alerta").html("Existem campos obrigatórios não preenchidos.").showMessage();
				$('#arquivo_csv').addClass('erro');
				return false;
		}else{
			return true;
		}
                
	 });
         
        /*$('#excluir_registro').click(function(){

            var confirma = window.confirm("Deseja realmente excluir este arquivo?");

            if(confirma == true)
            {
                window.location = $(this).attr('href');
            }
        });*/
});