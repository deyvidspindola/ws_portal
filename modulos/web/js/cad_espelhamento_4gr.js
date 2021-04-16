jQuery(document).ready(function(){

	jQuery('#clientes').hide();
	jQuery('#bt_add').hide();
	jQuery('#bt_cliente').hide();
	jQuery('#listagem').hide();
	jQuery('#cli_input').hide();
	jQuery('#cli_bt').hide();
	jQuery('#list_consulta').hide();
	jQuery('#bt_salvar').hide();
	jQuery('#bt_remover').hide();

	jQuery('#user_4gr').keyup(function(char){

		var rgx_pattern = new RegExp(/^[a-zA-Z0-9]$/);
		
		if ( rgx_pattern.test(char.key) ){
			var value = jQuery('#user_4gr').val();
			var value_up = value.toUpperCase();
			jQuery('#user_4gr').val(value_up);
		}
		else {
			var value = jQuery('#user_4gr').val();
			var resp = value.replace(char.key, '');
			jQuery('#user_4gr').val(resp);
		}
	});

	jQuery('#password_4gr').keyup(function(char){

		var rgx_pattern = new RegExp(/^[a-zA-Z0-9]$/);
		
		if ( rgx_pattern.test(char.key) ){
			var value = jQuery('#password_4gr').val();
			var value_up = value.toUpperCase();
			jQuery('#password_4gr').val(value_up);
		}
		else {
			var value = jQuery('#password_4gr').val();
			var resp = value.replace(char.key, '');
			jQuery('#password_4gr').val(resp);
		}
	});

	jQuery('#bt_limpar').click(function(){
		limpar(true);
	});

	jQuery('#gerenciadora').change(function(){
		//$("#name_4gr").val("4GR - ");
		$("#password_4gr").val("sascar");
	});

	jQuery('#tipo').change(function(){
		if ($("#tipo option:selected").val() != '4'){
			jQuery("#cliente").html('');
			jQuery('#clientes').hide();
			jQuery('#bt_add').hide();
			jQuery('#cli_input').hide();
			jQuery('#cli_bt').hide();
			jQuery('#search').val('');
			jQuery("#tb_cliente").html('');
			jQuery("#listagem").hide();
		}
		else {
			jQuery('#cli_input').show();
			jQuery('#cli_bt').show();
		}
	});

	jQuery('#bt_pesquisar').click(function(){
		
		$("#cliente").html('');
		var search = document.getElementsByName('search')[0].value;

		jQuery.ajax({
			 type	: 'post',
			 data	: {
				search : search
			 },
			 url	: 'cad_espelhamento_4gr.php?acao=listarClientes',
			 error	: function() {
				jQuery('#div_mensagem_geral')
				.removeClass('alerta sucesso invisivel')
				.addClass('erro')
				.html('Houve um erro na comunicação com o servidor.');
			},
			async : false,
			success : function(response){
				if (response.length != 0){
					jQuery('#clientes').show();
					jQuery('#bt_add').show();
					jQuery('#cliente').append('<option value="0">Selecione um cliente</option>');
					for(i=0;i<response.length;i++){
						jQuery('#cliente').append('<option value="' + response[i].clioid + '" data-doc="' + response[i].doc + '" data-tipo=' + response[i].tipo + '">' + response[i].nome + '</option>');
					}
				}
			}    
		});
	});

	jQuery('#bt_add').click(function(){
		
		jQuery('#listagem').show();
		var id = $("#cliente option:selected").val();
		var nome = $("#cliente option:selected").text();
		var doc = $("#cliente option:selected").attr("data-doc");
		var tipo = $("#cliente option:selected").attr("data-tipo");
		tipo = tipo.replace('"','');
		var add_id;

		$("#tb_cliente").children().each(function(index){
			var data_id = $( this ).attr("data-id");

			if (data_id == id)
				add_id = data_id;
		});

		if (add_id == id){
			alert("Cliente já adicionado!");
			return;
		}

		jQuery('#tb_cliente').append(
			'<tr class="par" data-id=' + id + '>' +
				'<th class="centro" style="width: 2%"></th>' +
				'<td class="esquerda" style="width: 80%">'+ nome +'</td>' +
				'<td class="direita">'+ tipo + '</td>' +
				'<td class="direita">'+ doc + '</td>' +
				'<td class="centro">' +
					'<button type="button" name="bt_remove" id="bt_remove" onclick="removeCliente(' + id + ')">X</button>' +
				'</td>' +
			'</tr>'
		);

		jQuery('#search').val('');
	});

	jQuery('#bt_cadastrar').click(function(){ 
		
		jQuery('#mensagem_erro').html('');
		jQuery('#mensagem_erro').hide();
		jQuery('#mensagem_sucesso').html('');
		jQuery('#mensagem_sucesso').hide();

		if ($("#gerenciadora option:selected").val() == "0"){
			alert('Favor selecionar a gerenciadora');
			return;
		}
		
		if ($("#user_4gr").val() == ""){
			alert('Favor inserir o usuário da 4°GR');
			return;
		}
		
		if ($("#password_4gr").val() == ""){
			alert('Favor inserir a senha da 4°GR');
			return;
		}
		
		if ($("#tipo option:selected").val() == "0"){
			alert('Favor selecionar o tipo');
			return;
		}
		
		if ($("#tb_cliente").html() == '' && $("#tipo option:selected").val() == '4' ){
			alert('Favor pesquisar e inserir um cliente');
			return;
		}

		var fields = {};

		fields.id_gr = $("#gerenciadora").val();
		fields.name_4gr = $("#name_4gr").val();
		fields.user_4gr = $("#user_4gr").val();
		fields.password_4gr = $("#password_4gr").val();
		fields.tipo = $("#tipo").val();
		fields.clientes = [];

		$("#tb_cliente").children().each(function(index){
			fields.clientes.push($( this ).attr("data-id"));
		});

		jQuery.ajax({
			 type	: 'post',
			 data	: fields,
			 url	: 'cad_espelhamento_4gr.php?acao=cadastrar4GR',
			 error	: function() {
				jQuery('#div_mensagem_geral')
				.removeClass('alerta sucesso invisivel')
				.addClass('erro')
				.html('Houve um erro na comunicação com o servidor.');
			},
			async : false,
			success : function(response){
				if (response.length != 0){
					if ( response.error){
						jQuery('#mensagem_erro').append(response.error);
						jQuery('#mensagem_erro').show();
					}
					else {
						jQuery('#mensagem_sucesso').append(response.success);
						jQuery('#mensagem_sucesso').show();

						jQuery('#name_4gr').prop("disabled", true);
						jQuery('#user_4gr').prop("disabled", true);
						jQuery("#tipo").prop("disabled", true);
						jQuery("#gerenciadora").prop("disabled", true);

						jQuery('#password_4gr').val('');
						jQuery('#bt_cadastrar').hide();
						jQuery('#bt_cliente').show();
						jQuery('#bt_consulta').hide();
						jQuery('#cli_input').hide();
						jQuery('#cli_bt').hide();
						jQuery('#clientes').hide();

						jQuery('#id_4gr').val(response.result.id_4gr);
						debugger;
					}
				}
			}    
		});
	});

	jQuery('#bt_consulta').click(function(){

		jQuery('#tb_consulta').html('');
		jQuery('#mensagem_erro').html('');
		jQuery('#mensagem_erro').hide();
		jQuery('#cli_input').hide();
		jQuery('#cli_bt').hide();
		jQuery('#bt_remover').hide();
		
		if ($("#user_4gr").val() == "" && $("#name_4gr").val() == ""){
			alert('Para fazer a consulta inserir o usuário ou o nome da 4°GR');
			return;
		}
		if ($("#tipo option:selected").val() == "0"){
			alert('Para fazer a consulta selecione o tipo da 4°GR');
			return;
		}

		var fields = {};

		fields.name_4gr = $("#name_4gr").val();
		fields.user_4gr = $("#user_4gr").val();
		fields.tipo = $("#tipo").val();

		jQuery.ajax({
			type	: 'post',
			data	: fields,
			url	: 'cad_espelhamento_4gr.php?acao=consultar4GR',
			error	: function() {
			   jQuery('#div_mensagem_geral')
			   .removeClass('alerta sucesso invisivel')
			   .addClass('erro')
			   .html('Houve um erro na comunicação com o servidor.');
		   },
		   async : false,
		   success : function(response){
			   if (response.length != 0){
				   if ( response.error){
					   jQuery('#mensagem_erro').append(response.error);
					   jQuery('#mensagem_erro').show();
				   }
				   else {
						jQuery('#list_consulta').show();
						jQuery('#clientes').hide();
						jQuery('#listagem').hide();
						jQuery('#tb_cliente').html('');

					   	for(i=0;i<response.result.length;i++){

							jQuery('#tb_consulta').append(
							'<tr class="par" data-id=' + response.result[i]['intid'] + '>' +
								'<th class="centro" style="width: 2%"></th>' +
								'<td class="esquerda" style="width: 80%"><a onClick="mostra4GR(' + response.result[i]['intid'] + ')">'+ response.result[i]['intnome']  +'</a></td>' +
								'<td class="direita">'+ response.result[i]['tipo'] + '</td>' +
								'<td class="esquerda">'+ response.result[i]['intlogin'] + '</td>' +
							'</tr>');
						}
				   	}
			   	}
		   	}    
	   	});

	   	jQuery('#name_4gr').prop("disabled", true);
		jQuery('#user_4gr').prop("disabled", true);
		jQuery("#tipo").prop("disabled", true);
		jQuery("#gerenciadora").prop("disabled", true);
	});

	jQuery('#bt_cliente').click(function(){
		jQuery('#bt_cliente').hide();
		jQuery('#cli_input').show();
		jQuery('#cli_bt').show();
		jQuery('#tb_consulta').html('');
		jQuery('#list_consulta').hide();
		//jQuery('#bt_salvar').show();
	});

	jQuery('#bt_salvar').click(function(){
		
		var fields = {};
		fields.id_4gr = jQuery('#id_4gr').val();
		fields.password_4gr = $("#password_4gr").val();
		fields.tipo = $("#tipo").val();
		fields.id_gr = $("#gerenciadora").val();
		fields.clientes = [];

		$("#tb_cliente").children().each(function(index){
			fields.clientes.push($( this ).attr("data-id"));
		});

		if (fields.clientes.length == 0 && fields.tipo == 4){
			alert('Favor pesquisar e inserir um cliente');
			return;
		}

		jQuery.ajax({
			 type	: 'post',
			 data	: fields,
			 url	: 'cad_espelhamento_4gr.php?acao=salvar4GR',
			 error	: function() {
				jQuery('#div_mensagem_geral')
				.removeClass('alerta sucesso invisivel')
				.addClass('erro')
				.html('Houve um erro na comunicação com o servidor.');
			},
			async : false,
			success : function(response){

				jQuery('#tb_cliente').html('');
				jQuery('#mensagem_erro').html('');
				jQuery('#mensagem_erro').hide();
				jQuery('#mensagem_sucesso').html('');
				jQuery('#mensagem_sucesso').hide();

				jQuery('#id_4gr').val(response.result.intid);
				jQuery('#name_4gr').val(response.result.intnome);
				jQuery('#name_4gr').prop("disabled", true);
				jQuery('#user_4gr').val(response.result.intlogin);
				jQuery('#user_4gr').prop("disabled", true);
				jQuery("#tipo").val(response.result.inttipo);
				jQuery("#tipo").prop("disabled", true);
				jQuery("#gerenciadora").val(response.result.gr_id);
				jQuery("#gerenciadora").prop("disabled", true);

				if (response.result.inttipo != 5 ){
					for(i=0;i<response.clientes.length;i++){
						jQuery('#tb_cliente').append(
							'<tr class="par" data-id=' + response.clientes[i].clioid + '>' +
								'<th class="centro" style="width: 2%"></th>' +
								'<td class="esquerda" style="width: 80%">'+ response.clientes[i].clinome +'</td>' +
								'<td class="direita">'+ response.clientes[i].clitipo + '</td>' +
								'<td class="direita">'+ response.clientes[i].doc + '</td>' +
								'<td class="centro">' +
									'<button type="button" name="bt_remove" id="bt_remove" onclick="removeCliente()">X</button>' +
								'</td>' +
							'</tr>'
						);
					}
				}

				jQuery('#listagem').show();
				jQuery('#bt_cadastrar').hide();
				jQuery('#bt_consulta').hide();
				//jQuery('#bt_salvar').hide();
				jQuery('#bt_cliente').show();
				jQuery('#cli_input').hide();
				jQuery('#cli_bt').hide();

				jQuery('#mensagem_sucesso').append(response.success);
				jQuery('#mensagem_sucesso').show();
			}    
		});
	});

	jQuery('#bt_remover').click(function(){ 
		
		var fields = {};
		fields.id_4gr = $("#id_4gr").val();

		jQuery.ajax({
			 type	: 'post',
			 data	: fields,
			 url	: 'cad_espelhamento_4gr.php?acao=remover4GR',
			 error	: function() {
				jQuery('#div_mensagem_geral')
				.removeClass('alerta sucesso invisivel')
				.addClass('erro')
				.html('Houve um erro na comunicação com o servidor.');
			},
			async : false,
			success : function(response){
				if (response.length != 0){
					if ( response.error){
						jQuery('#mensagem_erro').append(response.error);
						jQuery('#mensagem_erro').show();
					}
					else {
						jQuery('#mensagem_sucesso').html('');
						jQuery('#mensagem_sucesso').append(response.success);
						jQuery('#mensagem_sucesso').show();
						limpar(false);
					}
				}
			}    
		});
	});
});

function removeCliente(id) {
	jQuery("tr[data-id='" + id + "']").remove();
}

function limpar(boo) {
	$("#name_4gr").val('');
	$("#user_4gr").val('');
	$("#password_4gr").val('');
	$("#gerenciadora").find("option[value=0]").attr("selected","true");
	$("#cliente").find("option[value=0]").attr("selected","true");
	$("#tipo").find("option[value=0]").attr("selected","true");
	$("#cliente option").each(function() {
		$(this).remove();
	});
	$("#clientes").hide();
	$("#bt_add").hide();
	$("#search").val('');

	$("#tb_cliente").html('');
	$("#listagem").hide();
	jQuery('#cli_input').hide();
	jQuery('#cli_bt').hide();

	jQuery('#id_4gr').val('');

	if (boo){
		jQuery('#mensagem_erro').html('');
		jQuery('#mensagem_erro').hide();
		jQuery('#mensagem_sucesso').html('');
		jQuery('#mensagem_sucesso').hide();
	}

	jQuery('#tb_consulta').html('');
	jQuery('#list_consulta').hide();

	jQuery('#bt_cliente').hide();
	jQuery('#bt_salvar').hide();

	jQuery('#name_4gr').prop("disabled", false);
	jQuery('#user_4gr').prop("disabled", false);
	jQuery("#tipo").prop("disabled", false);
	jQuery("#gerenciadora").prop("disabled", false);

	jQuery('#bt_cadastrar').show();
	jQuery('#bt_consulta').show();

	jQuery('#bt_remover').hide();
}

function mostra4GR(id) {
	jQuery.ajax({
		type	: 'post',
		data	: {
		   intid : id
		},
		url	: 'cad_espelhamento_4gr.php?acao=pegar4GR',
		error	: function() {
		   jQuery('#div_mensagem_geral')
		   .removeClass('alerta sucesso invisivel')
		   .addClass('erro')
		   .html('Houve um erro na comunicação com o servidor.');
	   },
	   async : false,
	   success : function(response){
		   
			if ( response.error){
				jQuery('#mensagem_erro').append(response.error);
				jQuery('#mensagem_erro').show();
				jQuery('#list_consulta').hide();
				jQuery('#tb_consulta').html('');
			}
			else {
				jQuery('#tb_cliente').html('');
				jQuery('#mensagem_erro').html('');
				jQuery('#mensagem_erro').hide();
				jQuery('#mensagem_sucesso').html('');
				jQuery('#mensagem_sucesso').hide();

				jQuery('#id_4gr').val(response.result.intid);
				jQuery('#name_4gr').val(response.result.intnome);
				jQuery('#name_4gr').prop("disabled", true);
				jQuery('#user_4gr').val(response.result.intlogin);
				jQuery('#user_4gr').prop("disabled", true);
				jQuery("#tipo").val(response.result.inttipo);
				jQuery("#tipo").prop("disabled", true);
				jQuery("#gerenciadora").val(response.result.gr_id);
				jQuery("#gerenciadora").prop("disabled", true);

				if (response.result.inttipo == 4){
					for(i=0;i<response.clientes.length;i++){
						jQuery('#tb_cliente').append(
							'<tr class="par" data-id=' + response.clientes[i].clioid + '>' +
								'<th class="centro" style="width: 2%"></th>' +
								'<td class="esquerda" style="width: 80%">'+ response.clientes[i].clinome +'</td>' +
								'<td class="direita">'+ response.clientes[i].clitipo + '</td>' +
								'<td class="direita">'+ response.clientes[i].doc + '</td>' +
								'<td class="centro">' +
									'<button type="button" name="bt_remove" id="bt_remove" onclick="removeCliente(' + response.clientes[i].clioid + ')">X</button>' +
								'</td>' +
							'</tr>'
						);
					}
				}

				jQuery('#listagem').show();
				jQuery('#bt_cadastrar').hide();
				jQuery('#bt_consulta').hide();
				jQuery('#bt_salvar').show();

				if (jQuery("#tipo").val() == 4){
					jQuery('#bt_cliente').show();
			 	}

				jQuery('#list_consulta').hide();
				jQuery('#tb_consulta').html('');

				jQuery('#bt_remover').show();
			}
	   	}    
	});
} 