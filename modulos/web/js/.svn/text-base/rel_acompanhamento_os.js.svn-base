var timerValidaDatas = null;

$(document).ready(function(){
	
	/**
	 * Mensagens de retorno
	 */
	var MSG_DATAINI_OBRIGATORIA 	= "Data inicial é obrigatória.";
	var MSG_DATAINI_INVALIDA 		= "Data inicial informada não é valida.";
	var MSG_DATAFIM_OBRIGATORIA 	= "Data final é obrigatória.";
	var MSG_DATAFIM_INVALIDA 		= "Data final informada não é valida.";
	var MSG_DATAFINAL_MENOR			= "Data final menor que a data inicial.";

	$('#img_dt_ini').click(function() {
		displayCalendar(document.forms[0].dt_ini,'dd/mm/yyyy',this);
	});
	
	$('#img_dt_fim').click(function() {
		displayCalendar(document.forms[0].dt_fim,'dd/mm/yyyy',this);
	});
	
	/**
	 * @tag input(type="text", name="dt_ini")
	 * @Event OnBlur
	 */
	$('#dt_ini').blur(function(){
		
		if ($(this).val() != '' && revalidar(this,'@@/@@/@@@@','data')) {
			$(this).removeClass("inputError");
		}
	});
	
	/**
	 * @tag input(type="text", name="dt_ini")
	 * @Event OnKeypress
	 */
	$('#dt_ini').keypress(function(){
		formatar(this, '@@/@@/@@@@');
	});
	
	/**
	 * @tag input(type="text", name="dt_fim")
	 * @Event OnKeypress
	 */
	$('#dt_fim').keypress(function(){
		formatar(this, '@@/@@/@@@@');
	});
	
	/**
	 * @tag input(type="text", name="dt_fim")
	 * @Event OnFocus
	 */
	$('#dt_ini').focus(function() {
		$('#dt_fim').blur();
	});
	
	/**
	 * @tag input(type="text", name="dt_ini")
	 * @Event OnChange
	 */
	$('#dt_ini').change($('#dt_ini').blur());
	
	
	/**
	 * @tag input(type="text", name="dt_ini")
	 * @Event OnBlur
	 */
	$('#dt_fim').blur(function(){
		
		if ($(this).val() != '' && revalidar(this,'@@/@@/@@@@','data')) {
			$(this).removeClass("inputError");
		}
	});
	
	/**
	 * @tag input(type="text", name="dt_fim")
	 * @Event OnFocus
	 */
	$('#dt_fim').focus(function() {
		$('#dt_ini').blur()
	});
	
	/**
	 * @tag input(type="text", name="dt_ini")
	 * @Event OnChange
	 */
	$('#dt_fim').change($('#dt_fim').blur());
	

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
	 * Evento click do botão Pesquisar
	 * @tag input(type="button", name="pesquisar")
	 * @Event OnClick
	 */
	$('input[name="pesquisar"]').click(function(){
		
		pesquisar();
	});
	
	/**
	 * Evento click do botão Gerar XLS
	 * @tag input(type="button", name="gerar_xls")
	 * @Event OnClick
	 */
	$('input[name="gerar_xls"]').click(function(){
		
		pesquisar(true);
	});

	/**
	 * Evento change da combo de modelos de equipamento
	 * @tag select(name="modelo_equipamento")
	 * @Event OnChange
	 */
	$('select[name="modelo_equipamento"]').change(function() {
		
		var modeloEquipamento	= $(this).val();
		var dados				= {acao: 'buscaVersoesModelo', modelo: modeloEquipamento };
		
		$('select[name="versao_equipamento"]').html('<option value="">Todos</option>');
		
		if (modeloEquipamento.length == 0) {
			return false;
		}
		
		$('#versao_equipamento_progress').show();
		$.ajax({
		  url: 'rel_acompanhamento_os.php',
		  dataType: 'json',
		  type: 'post',
		  data: dados,
		  success: function(data) {
			  
			  var items = [];
			  if (data.retorno !== null && data.erro === false) {
				  
				  items.push('<option value="">Todos</option>');

				  $.each(data.retorno, function(key, val) {
					  	
						if (val !== null && key !== null && val.id !== null && val.descricao !== null) {
							items.push('<option value="' + val.id + '">' + val.descricao + '</option>');
						}
				  });

				  $('select[name="versao_equipamento"]').html(items.join(''));
			  }
			  
			  if (data.erro !== false) {
				  removeAlerta();
				  criaAlerta("Erro ao buscar versões.");
			  }

			  $('#versao_equipamento_progress').hide();
		  }
		});
	});
	
	
	
	/**
	 * Evento change da combo item
	 * @tag select(name="item")
	 * @Event OnChange
	 */
	$('select[name="item"]').change(function(){
		buscaMotivo();
	});
	
	$('select[name="tipo"]').change(function(){
		buscaMotivo();
	});
	
	/**
	 * Dispara a pesquisa do relatório
	 * @param boolean xls
	 */
	function pesquisar(xls) {
		
		var dataFim = $("#dt_fim").val();
		var dataIni = $("#dt_ini").val();
		var post = null;
		
		
		formata_dt(document.getElementById('dt_ini'));
		formata_dt(document.getElementById('dt_fim'));
		
		/**
		 * Valida período
		 */
		if (dataIni.length == 0 && dataFim.length == 0) {
			
			removeAlerta();
			criaAlerta("Período é obrigatório."); 
			
			jQuery("#dt_ini").addClass("inputError");
			jQuery("#dt_fim").addClass("inputError");
			return false;
		} 
		else if (dataIni.length == 0) {
			
			removeAlerta();
			criaAlerta(MSG_DATAINI_OBRIGATORIA); 
			jQuery("#dt_ini").addClass("inputError");
			return false;
		} 
		else if (dataFim.length == 0) {
			
			removeAlerta();
			criaAlerta(MSG_DATAFIM_OBRIGATORIA); 
			jQuery("#dt_fim").addClass("inputError");
			return false;
		} 
		
		if (diferencaEntreDatas($("#dt_fim").val(), $("#dt_ini").val()) < 0){
			
			removeAlerta();
			criaAlerta(MSG_DATAFINAL_MENOR); 
			jQuery("#dt_fim").addClass("inputError");
			return false;	
		}
		
		removeAlerta();
		$('#dt_ini').removeClass("inputError");
		$('#dt_fim').removeClass("inputError");
		
		if (xls == true) {
			
			$("#acao").val('gerarXls');
						
			post 	= $('form[name="filtro"]').serialize();
			
			post += '&tipo_contrato='+jQuery('#tipo_contrato').val();
			
			jQuery.download('rel_acompanhamento_os.php', post);
		}
		else {
			
			$("#acao").val('pesquisar');
			post 	= $('form[name="filtro"]').serialize();
			$.ajax({
				  url: 'rel_acompanhamento_os.php',
				  dataType: 'json',
				  type: 'post',
				  data: post,
				  beforeSend: function() {
					  
					  $('#resultado_relatorio').html('');
					  $('#resultado_relatorio').fadeOut('fast', function() {
						  $('#resultado_progress').fadeIn('slow');
					  });
				  },
				  success: function(data) {
					  
					  if (data !== null  && data.erro === false) {
						  
						  $('#resultado_relatorio').html(data.retorno);
					  }
					  
					  if (data.erro !== false) {
						  
						  removeAlerta();
						  criaAlerta("Erro ao pesquisar.");
					  }
				  },
				  complete: function() {
					  $('#resultado_progress').fadeOut('fast', function() {
						  $('#resultado_relatorio').fadeIn('slow');
					  });
				  }
				});
		}
		removeAlerta();
		$('#dt_ini').removeClass("inputError");
		$('#dt_fim').removeClass("inputError");
	} 
	
	function validaDatas() {
		
		var dtIni = document.forms[0].dt_ini;
		var dtFim = document.forms[0].dt_fim;
		
		if (dtIni.value != '' && !revalidar(dtIni,'@@/@@/@@@@','data')) {
			$(dtIni).addClass("inputError");
		}
		else {
			$(dtIni).removeClass("inputError");
		}
		
		if (dtFim.value != '' && !revalidar(dtFim,'@@/@@/@@@@','data')) {
			$(dtFim).addClass("inputError");
		}
		else {
			$(dtFim).removeClass("inputError");
		}
	}
	
	function buscaMotivo() {
		
		var itemSelecionado 	= $('select[name="item"]').val();
		var tipoOS 				= $('select[name="tipo"]').val();
		var dados 				= {acao: 'buscaMotivos', item: itemSelecionado, tipo: tipoOS};
		
		$('select[name="motivo"]').html('<option value="">Todos</option>');
		
		if (tipoOS.length == 0) {
			return false;
		}
		
		if (itemSelecionado.length == 0) {
			return false;
		}
		
		$('#motivo_progress').show();
		
		$.ajax({
		  url: 'rel_acompanhamento_os.php',
		  dataType: 'json',
		  type: 'post',
		  data: dados,
		  success: function(data) {
			  
			  var items = [];
			  
			  if (data !== null  && data.erro === false) {
				  
				  items.push('<option value="">Todos</option>');
				  
				  $.each(data.retorno, function(key, val) {
					
					  if (val !== null && key !== null && val.id !== null && val.descricao !== null) {
							items.push('<option value="' + val.id + '">' + val.descricao + '</option>');
						}
				  });

				  $('select[name="motivo"]').html(items.join(''));
			  }
			  
			  if (data.erro !== false) {
				  removeAlerta();
				  criaAlerta("Erro ao buscar motivos.");
			  }
			  
			  $('#motivo_progress').hide();
		  }
		});
	}
	
	$('input[name="placa"]').keyup(function(){
		$(this).val(($(this).val()).toUpperCase());
	});
	
	$('#dt_ini').focus();
});