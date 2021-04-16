$(document).ready(function(){	
	
	/**
	 * Mensagens de retorno
	 */
	var MSG_DATAINI_OBRIGATORIA = "Data inicial é obrigatória.";
	var MSG_DATAINI_INVALIDA 	= "Data inicial informada não é valida.";
	var MSG_DATAFIM_OBRIGATORIA = "Data final é obrigatória.";
	var MSG_DATAFIM_INVALIDA 	= "Data final informada não é valida.";
	var MSG_DATAFINAL_MENOR		= "Data final menor que a data inicial.";
	
	$('input[name="pesquisar"]').click(function() {
		pesquisar();
	});
	
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
	
	
	/**
	 * @tag select(name="comboPropostas")
	 * @Event OnChange
	 */
	var vComboSubpropostas = '';

	$("#comboPropostas").change(function(){
		jQuery("#comboSubpropostas").html("");
		jQuery(".subtipo").hide();
		if ($(this).val() != '')
		{
			 $.ajax({
			            url: 'rel_envio_emails_automaticos.php',
			            type: 'post',
			            data: {
			            	tipoProposta: $(this).val(),
			                acao:'buscarSubProposta'
			            },
			            beforeSend: function(){
								  $('#loading_tipo').show();
			            },
			            success: function(data) {
			            	// $("#comboSubpropostas").html('<option value="">Selecione</option>');  
			            	var resultado = $.parseJSON(data);
			            	
			            	if(resultado != '') {
			            		// Monta combo de todos os comandos

			            	// 	$.each(resultado, function(i, item){
			            	// 		$("#comboSubpropostas").append('<option value="'+item.tppoid+'">'+item.tppdescricao+'</option>');
			            	// 	});
			            	// 	$("#comboSubpropostas option[value='"+vComboSubpropostas+"']").attr("selected","selected") ;
			            	// }
			            		jQuery("#comboSubpropostas").html("");
								jQuery("#comboSubpropostas").append(jQuery('<option>Todos</option>').attr("value",''));
								
								jQuery(".subtipo").show();
								
			            		$.each(resultado, function(i, item){
			            			$("#comboSubpropostas").append('<option value="'+item.tppoid+'">'+item.tppdescricao+'</option>');
			            		});
		
							}else{
								jQuery("#comboSubpropostas").val("");
								jQuery(".subtipo").hide();
							}
			            	jQuery('#loading_tipo').hide();
			            }
			        });
		}
		//  else {
		// 	// $("#comboSubpropostas").html('<option value="">Selecione</option>');  
		// 	jQuery("#comboSubpropostas").val("");
		// 	jQuery(".subtipo").hide();
		// }
	});

	function pesquisar() {
			
		var dataFim = $("#dt_fim").val();
		var dataIni = $("#dt_ini").val();
		var post = null;
		var result = null;
		
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
		
		$('input[name="acao"]').val('pesquisar');
		post = $('form[name="filtro"]').serialize();
		
		removeAlerta();
		if (!validaDatas()) {
			
		}
		
		$('#motivo_progress').show();

		$.ajax({
			  url: 'rel_envio_emails_automaticos.php',
			  dataType: 'json',
			  type: 'post',
			  data: post,
			  beforeSend: function() {

				  $('#resultado_relatorio').hide('fast', function() {
					  $('#resultado_progress').fadeIn('slow');
				  });
			  },
			  success: function(data) {
				  
				  if (data !== null  && data.erro === false) {
					  if (data.codigo == 0) {
						  $('#resultado_relatorio_container').html('<a href="'+data.retorno+'"><img src="images/icones/t3/caixa2.jpg" width="36px" alt="Baixar relatório" /><br />Relatório Envio de E-mails Automáticos</a>');
					  }
					  else {
						  $('#resultado_relatorio_container').html('<b>Nenhum resultado encontrado</b>');
					  }
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
	
	$('input[name="placa"]').keyup(function(){
		$(this).val(($(this).val()).toUpperCase());
	});
	
	$('input[name="numero_os"]')
		.keyup(function(){
			formatar(this, '@');
		})
		.blur(function() {
			revalidar(this,'@');
		});
	
	
	function validaDatas() {
		
		var dtIni = document.forms[0].dt_ini;
		var dtFim = document.forms[0].dt_fim;
		
		if (dtIni.value != '' && !revalidar(dtIni,'@@/@@/@@@@','data')) {
			$(dtIni).addClass("inputError");
			removeAlerta();
			criaAlerta(MSG_DATAINI_INVALIDA);
			return false;
		}
		else {
			$(dtIni).removeClass("inputError");
			return true;
		}
		
		if (dtFim.value != '' && !revalidar(dtFim,'@@/@@/@@@@','data')) {
			$(dtFim).addClass("inputError");
			
			removeAlerta();
			criaAlerta(MSG_DATAFIM_INVALIDA);
			return false;
		}
		else {
			$(dtFim).removeClass("inputError");
			return true;
		}
	}
	$('#dt_ini').focus();
});