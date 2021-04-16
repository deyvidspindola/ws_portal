/**
 * Verifica se foi executada a importação.
 * Caso sim,
 * 		Chama o método de impressão do relatório
 * Se não,
 * 		Habilita o upload novamente para uma nova importação.
 */
function confirmarImportacao() {
	
	var registros 	= $('#registros_arquivo_importado').val();
	var importado   = $('#arquivo_importado').val();
	var listagem    = $('#listagem').val();
	var post		= null;
	var confirma	= null;
	
	if (registros != '' && registros > 0 && listagem.length == 0) {
		
		confirma  = confirm("Total de registros no arquivo: " + registros + "\nConfirma Importação?");
		if (confirma == true) {
			
			 $('#acao').val('imprimirRelatorio');
			 $('#listagem').val('1');
			 $('form[name="filtro"]').removeAttr('target');
			 
			 post = $('form[name="filtro"]').serialize();
			 
			 $.ajax({
				  url: 'rel_linhas.php',
				  dataType: 'json',
				  type: 'post',
				  data: post,
				  beforeSend: function() {

					  $('#resultado_relatorio').fadeOut('fast', function() {
						  $('#resultado_progress').fadeIn('slow');
					  });
				  },
				  success: function(data) {
					  
					  if (data !== null  && data.erro === 0) {
							var browser = $.browser;
							
							// if ( browser.msie && browser.version.slice(0,3) == "9.0" ) {
							if ( browser.msie) {
								var expr = new RegExp('>[ \t\r\n\v\f]*<', 'g');
								var replace = data.retorno.replace(expr, '><');

								$('#resultado_relatorio').html(replace);
							}else{
								$('#resultado_relatorio').html(data.retorno);	
							}

						  	
					  }
					  else {
						  removeAlerta();
						  criaAlerta(data.retorno);
					  }
					  
					  $('#arquivo').removeAttr('readonly');
						
					  $('#arquivo').removeAttr('disabled');
					  
					  $('#arquivo').val('');

				  },
				  complete: function() {
					  $('#resultado_progress').fadeOut('fast', function() {
						  $('#resultado_relatorio').fadeIn('slow');
					  });
				  }
			 });
			 
			
			
			 //$('form[name="filtro"]').submit();
			return true;
		}
		else {
			$('#arquivo').removeAttr('readonly');
			
			$('#arquivo').removeAttr('disabled');
			
			$('#arquivo_importado').val('');
			
			return false;
		}
	}
}

/**
 * Inicio do DOM
 * @tag document.body
 * @Event OnLoad
 */
$(document).ready(function() {
	
	setInterval("jQuery('.blinking').fadeOut().fadeIn();", 1600 );

	// Downloader
	jQuery.download = function(url, data, method){
		
		//url and data options required
		if( url && data ){ 
			//data can be string of parameters or array/object
			data = typeof data == 'string' ? data : jQuery.param(data);
			//split params into form inputs
			var inputs = '';
			jQuery.each(data.split('&'), function(){ 
				var pair = this.split('=');
				inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />'; 
			});
			//send request
			jQuery('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>')
			.appendTo('body').submit().remove();
		};
	};
	
	/**
	 * @tag iframe
	 * @Event OnLoad
	 */
	$("#postiframe").load(function () {
    	confirmarImportacao();
    });
	
	/**
	 * @tag input(name="bt_exportar")
	 * @Event OnClick
	 */
	$('body').delegate('input[name="bt_exportar"]', 'click', function(){
		$("#acao").val('exportar');
		post 	= $('form[name="filtro"]').serialize();
		jQuery.download('rel_linhas.php', post);
		
	});
	
	$('body').delegate('a.link_download_relatorio', 'click', function(){
		$("#acao").val('exportar');
		post 	= $('form[name="filtro"]').serialize();
		jQuery.download('rel_linhas.php', post);
	});
	
	/**
	 * Botão gerar relatório, efetua o submit que importa o arquivo para gerar o relatório
	 * @tag input(type="button")
	 * @event OnClick
	 */
	$('input[name="gerar_relatorio"]').click(function() {
		
		var arquivo 	= $('#arquivo').val();
		var form		= null;
		var iframe		= null;
		
		$('#registros_arquivo_importado').val('');
		
		if (arquivo.length == 0) {
			
			removeAlerta();
			criaAlerta("Informe o arquivo.");
			return false;
		}
		
		removeAlerta();
		$('#arquivo_importado').val('');
		
        form 	= $('form[name="filtro"]');
        
        $('#acao').val('importar');
        
        form.attr('target', 'postiframe');
        form.attr("action", "rel_linhas.php");
        form.submit();
		
		$('#arquivo').attr('readonly', 'readonly');
		$('#arquivo').attr('disabled','disabled');

        return false;
	});
});