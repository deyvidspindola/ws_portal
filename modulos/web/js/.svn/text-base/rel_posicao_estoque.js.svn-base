var timerValidaDatas = null;

$(document).ready(function(){
	
	/**
	 * Mensagens de retorno
	 */
	var MSG_DATAPOSICAO_OBRIGATORIA 	= "Existem campos obrigat&oacute;rios n&atilde;o preenchidos.";

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
	 * Evento change da combo de cidades
	 * @tag select(name="uf")
	 * @Event OnChange
	 */
	$('select[name="uf"]').change(function() {
		
		var ufval	= $(this).val();
		var post				= {acao: 'buscarCidades', uf: ufval };
		 
		$('select[name="cidade"]').html('<option value="">Escolha UF</option>');
		
		if (ufval.length == 0) {
			return false;
		}
		
		$('#cid_progress').show();
		$.ajax({
		  url: 'rel_posicao_estoque.php',
		  dataType: 'json',
		  type: 'post',
		  data: post,
		  success: function(data) {
	 
			  var items = [];
			  if (data.retorno !== null && data.erro === false) {
				  
				  items.push('<option value="">Escolha UF</option>');

				  $.each(data.retorno, function(key, val) {
					  	
						if (val !== null && key !== null && val.label !== null && val.label !== null) {
							items.push('<option value="' + val.label + '">' + (unescape(val.label)) + '</option>');
						}
				  }); 
				  $('select[name="cidade"]').html(items.join(''));
			  }
			  
			  if (data.erro !== false) {
				  alert("erro no retorno de dados"); 
			  }

			  $('#cid_progress').hide();
		  }
		});
	});
	
	/**
	 * Evento change da combo de status de representantes
	 * @tag select(name="status_representante")
	 * @Event OnChange
	 */
	$('select[name="status_representante"]').change(function() {
		
		var repval	= $(this).val(); 
		var post	= {acao: 'buscarRepresentante', repstatus: repval };
		 
		$('select[name="representante"]').html('<option value="">Escolha</option>');
		
		if (repval.length == 0) {
			repval = $(this).val("");
		}
	 
		$('#rep_progress').show();
		$.ajax({
		  url: 'rel_posicao_estoque.php',
		  dataType: 'json',
		  type: 'post',
		  data: post,
		  success: function(data) { 
			  var items = [];
			  if (data.retorno !== null && data.erro === false) {
				  
				  items.push('<option value="">Escolha</option>');

				  $.each(data.retorno, function(key, val) {
					  	
						if (val !== null && key !== null && val.id !== null && val.nome !== null) {
							items.push('<option value="' + val.id + '">' +  val.nome  + '</option>');
						}
				  });

				  $('select[name="representante"]').html(items.join(''));
			  }
			  
			  if (data.erro !== false) {
				  alert("erro no retorno de dados"); 
			  }

			  $('#rep_progress').hide();
		  }
		});
	});
	
	/**
	 * Evento click do bot�o Pesquisar
	 * @tag input(type="button", name="pesquisar")
	 * @Event OnClick
	 */
	$('input[name="pesquisar"]').click(function(){ 

		pesquisar();
	
	});
	 
	$('input[name="gerar_csv"]').click(function(){
	 
		pesquisar(true); 		  
	    
	});
	$('#linkdownload').click(function(){
		$("#acao").val('gerarCsv'); 

		post = $('form[name="filtro"]').serialize();
		
		post += '&data_posicao='+jQuery('#data_posicao').val();
		post += '&cidade='+jQuery('#cidade').val();  
		dow = jQuery.download('rel_posicao_estoque.php', post); 
		 
	});
	/**
	 * Dispara a pesquisa do relatório
	 * @param boolean xls
	 */
	function pesquisar(csv) { 
	   var dataPosicao = $("#data_posicao").val();
 
       if (dataPosicao.length == 0) {
		    $("#mensagem_nenhum_registro").hide(100);
			$("#mensagem_alerta").show(100);
			jQuery("#data_posicao").addClass("erro"); 
			
			return false;
		} else{ 
			$("#mensagem_alerta").hide(100);
			$("#mensagem_nenhum_registro").hide(100);
			jQuery("#data_posicao").removeClass("erro");
		}
		if (csv == true) { 
			$('#resultado_relatorio').hide();
		 
			  var dataPosicao = $("#data_posicao").val();
			  dataPosicao.split("/");
			  dataPosicao = dataPosicao.replace(/-/g, "");
			  dataPosicao = dataPosicao.split("/").reverse().join("");
			  
			  $('#gerar_csv').prop('disabled', true);
			  $("#gerar_csv").prop('value', 'Aguarde..'); 
			  
			  $("#acao").val('pesquisar'); 

		      post = $('form[name="filtro"]').serialize();

              $.ajax({
				  url: 'rel_posicao_estoque.php',
				  dataType: 'json',
				  type: 'post',
				  data: post,
				  beforeSend: function() {
				  
					  $('#caixa').hide();  
					  $('#resultado_progress').show();
					  $('#resultado_relatorio').hide();
				  },
				  success: function(data) { 
				  if (data.erro == true) {
				      $("#mensagem_nenhum_registro").show(100);
				  }else{
				  	  $('#datainversa').html('RelatorioPosicaoEstoque_'+dataPosicao+".csv");
			          $('#caixa').show();  
				  }
				  },
				  complete: function() {
					  $('#resultado_progress').hide();
					  $('#resultado_relatorio').hide();
						
					  $("#gerar_csv").prop('value', 'Gerar CSV');
					  $('#gerar_csv').prop('disabled', false);
				  }
				}); 
		}
		else { 
			$("#acao").val('pesquisar');
			$('#pesquisar').prop('disabled', true);
			$("#pesquisar").prop('value', 'Aguarde..'); 
			
			post = $('form[name="filtro"]').serialize(); 
			$.ajax({
				  url: 'rel_posicao_estoque.php',
				  dataType: 'json',
				  type: 'post',
				  data: post,
				  beforeSend: function() {
					  $('#caixa').hide();  
					  $('#resultado_progress').show();
					  $('#resultado_relatorio').hide();
				  },
				  success: function(data) {
		 
					  if (data !== null  && data.erro === false) { 
						  $('#resultado_relatorio').html(data.retorno);
					  }else{
 						  $('#resultado_relatorio').html("");
					  }
					  
					  if (data.erro !== false) {
						  $("#mensagem_nenhum_registro").show(100);
				 
					  }
				  },
				  complete: function() {
					  $('#resultado_progress').hide();
					  $('#resultado_relatorio').show();
						
					  $("#pesquisar").prop('value', 'Pesquisar');
					  $('#pesquisar').prop('disabled', false);
				  }
				});
		}
	} 
	
});