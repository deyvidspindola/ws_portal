<script type="text/javascript">
jQuery(document).ready(function() {

	jQuery("#bt_cenvio").click(function(){
		removeAlerta();
// 		criaAlerta("Existem campos obrigatórios não preenchidos.");
		// Ajax responsável por realizar a pesquisa
		jQuery.ajax({
			url: 'send_layout_emails.php',
			type: 'get',
			data: jQuery('#editar_layout_emails').serialize()+'&acao=enviaEmailOcorrencia&ajax=true',
	        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
			beforeSend: function(){
				jQuery('#div_msg').html('<img src="images/loading.gif" alt="" />');
				jQuery('#bt_cenvio').attr('disabled', 'disabled');
				removeAlerta();
			},
			success: function(data){
				
				// Liberação do botão de pesquisa
				jQuery('#btn_pesquisar_inibicao').removeAttr('disabled');
				
				try{	
					// Transforma a string em objeto JSON
				    //console.info(data);
					var resultado = jQuery.parseJSON(data);
					criaAlerta(resultado.message);					 				
				}catch(e){
				   // Caso haja erros durante o processo, provavelmente na base de dados
					criaAlerta("Ocorreu um erro ao enviar e-mail.");
				}

				jQuery('#div_msg').html('');
 				jQuery('#bt_cenvio').removeAttr('disabled');
			}
		});

    });    

});
</script>
<div style="margin: 10px;">		
<form method="post" id="editar_layout_emails" enctype="multipart/form-data" >	            		
	<input type="hidden" name="seeoid" id="seeoid" value="<?=$this->_getIdLayout()?>" />		                
	
	<div style="text-decoration: underline;font-size: 12px;">
		Confira se o layout desejado está correto e clique em "Confirmar Envio", caso contrário clique em "Cancelar Envio" para voltar para tela de edição.
	</div>
	<br />
		<div style="clear:both;"></div>
		<iframe style="overflow:auto;border-style:solid;border-width:1px;height:650px;width:100%"  src="send_layout_emails.php?acao=emailOcorrenciaHTML&seeoid=<?=$this->_getIdLayout()?>&parse=true" ></iframe>
    <center>
    	<span id="div_msg" class="msg"></span>
    </center>
	<center>
    	<input type="button"  class="botao"  name="bt_cenvio"   id="bt_cenvio"    value="Confirmar Envio" style="width: 120px;" />
    	<input type="button"  class="botao"  name="bt_cancelar" id="bt_cancelar"  value="Cancelar Envio"  style="width: 120px;" onclick="javascript:window.location.href='send_layout_emails.php?acao=emailOcorrencia&reset=f'" />
    </center>
</form>

</div>