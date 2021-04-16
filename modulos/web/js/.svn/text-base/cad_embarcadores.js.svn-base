jQuery('input[name="embcep"]').mask('99999-999');
jQuery('#not_id_embtelefone1,#not_id_embtelefone2,#not_id_embtelefone3').mask("(99) 9999-9999?9");	
jQuery('label[for="not_id_gerRiscoSel"],label[for="not_id_transpSel"]').parent().css('width', '160px');
jQuery("#id_embsegoid, #not_id_embemail, #not_id_embcidade, #not_id_embbairro, #not_id_embobservacao").css('width', '388px');
jQuery("#not_id_embcomplemento").css('width', '385px');
jQuery('#not_id_transpSel').hide();
jQuery('#not_id_gerRiscoSel').hide();

jQuery(document).ready(function(){
	
	jQuery(".botao").click(function () {
		var acao = jQuery(this).attr('name');

	 	if (jQuery(this).val() == 'Cancelar') {
	 		if (confirm('Deseja cancelar a operação?')) {
				jQuery('input[name="acao"]').val('cancelar');
			 	jQuery("#form").submit();
			} else {
				exibeAlertaMsg('Operação Cancelada');
			}
	 	} else if (jQuery(this).val() == 'Excluir') {
	 		if (confirm('Deseja excluir o registro?')) {
				jQuery('input[name="acao"]').val('excluir');
			 	jQuery("#form").submit();
			} else {
				exibeAlertaMsg('Operação Cancelada');
			}
	 	} else if (jQuery(this).val() == 'Salvar') {	 		
			jQuery('input[name="acao"]').val('salvar');			
	 	} else if (jQuery(this).val() == 'Atualizar') {	 		
			jQuery('input[name="acao"]').val('atualizar');			
	 	} else {
		 	jQuery('input[name="acao"]').val(acao);
		 	jQuery("#form").submit();
	 	}
	});

	jQuery(".editar").click(function () {
		 	jQuery("#form_acao").val('editar');
		 	jQuery('input[name="acao"]').val('editar');
		 	jQuery("#id_emboid").val(jQuery(this).attr('emboid'));
		 	jQuery("#form").submit();
	});

	// Valida campos de endereço quando rua está preenchida
	jQuery('input[name="embrua"]').bind('blur', function(){

		// seta valor default para não remover
		var remove = false;

		// se o campo rua for preenchido, altera variavel para remover o prefixo
		if(jQuery(this).val() != '')
			remove = true;

		// campos que devem ser alterados para 'required' se rua for preenchida
		var numero = jQuery('input[name="embnumero"]');
		var cidade = jQuery('input[name="embcidade"]');
		var cep    = jQuery('input[name="embcep"]');
		var estado = jQuery('select[name="embuf"]');	

		// array com os objetos
		var camposRequired = [numero, cidade, estado, cep];

		// chama função passando os parametros necessarios
		alteraObrigatorio(camposRequired, 'not_', remove);		
	});	

	// adiciona cabeçalho de 'ação' e 'nome'; adiciona botão de acão
	var cabecalhoListas = '';
	if("exibeCabecalhoListas" in window) {
		if(exibeCabecalhoListas == '1') {
			cabecalhoListas = '<tr class="headLista"><td nowrap class="tableRodapeModelo1" width="10%" align="center"><h3>A&ccedil;&atilde;o</h3></td><td nowrap class="tableRodapeModelo1"><h3>Nome</h3></td></tr>'
			exibeCabecalhoListas = '0';
		}
	}

	var combosAdd = jQuery('select[name="gerRiscoSel"],select[name="transpSel"]');
	combosAdd.each(function(){ 		
		var valuesAdd      = eval(jQuery(this).attr('name') + 'ArrAdd');
		var valuesAddLabel = eval(jQuery(this).attr('name') + 'ArrAddLabel');

		// popula tabelas com os registros ja selecionados
		var resultLista = '';		
		$.each(valuesAdd, function(index, value){
			var classeTr = (index % 2 == 0)?'tdc':'tde';
			var linkExcluir = '[<a href="javascript:void(0)" oid="'+value+'" class="excluirRegLista" onclick="excluirRegLista(this);">' +
								'<img src="images/icones/t1/x.png">' +
							'</a>]';

			resultLista += '<tr class="resultLista '+classeTr+'">' +
							'<td nowrap width="10%" align="center">'+linkExcluir+'</td>' + 
							'<td nowrap>'+valuesAddLabel[value]+'</td>' +
						'</tr>'
		});

		var montaTable = jQuery(this).closest('span').closest('table')
			.children('tbody')
				.append('<tr><td colspan="2" class="botaoAddContainer">'+
							'<input type="button" class="botaoAdd botao" value="Adicionar" />'+							
						'</td></tr>')
				.append(cabecalhoListas)
				.append(resultLista);
	});
		
	// validação campo numero
	jQuery('input[name="embnumero"]').bind("keyup", function() {
		var valor = jQuery(this).val();
		if(!isNumber(valor)) {
			jQuery(this).val('');
		}
	});
        	
	// botão add
	jQuery('.botaoAdd').bind('click', function(){
		var comboAdd   = jQuery(this).closest('table').children().find('select');	
		var tableLista = comboAdd.closest('span').closest('table');	
		var hiddenAdd  = tableLista.children().find('input[name="'+comboAdd.attr('name') + 'Add' + '"]');
		var hiddenRem  = tableLista.children().find('input[name="'+comboAdd.attr('name') + 'Rem' + '"]');
		var idSel      = comboAdd.val();
		var labelSel   = comboAdd.find('option:selected').text();
		var arrAdd 	   = eval(comboAdd.attr('name') + 'ArrAdd');
		var arrRem	   = eval(comboAdd.attr('name') + 'ArrRem');

		// verifica se foi selecionada uma opção
		if(idSel == '') {
			alert('Selecione uma opção'); 
			return;
		}

		// verifica se ja foi selecionada a opção
		var verificaLista = false;
		$.each(arrAdd, function(index, value){
			if(value == idSel) {					
					verificaLista = true;
			}
		});

		if(verificaLista == true) {
			alert('O registro já foi selecionado'); 
			return;
		}

		// adiciona cabeçalho de 'ação' e 'nome';
		if(tableLista.find('tr.headLista').length == 0) {
			cabecalhoListas = '<tr class="headLista"><td nowrap class="tableRodapeModelo1" width="10%" align="center"><h3>A&ccedil;&atilde;o</h3></td><td nowrap class="tableRodapeModelo1"><h3>Nome</h3></td></tr>'
			tableLista.children('tbody').append(cabecalhoListas);			
		}

		// adiciona id selecionado no campo hidden				
		arrAdd.push(idSel);
		hiddenAdd.val(arrAdd.join());	

		// remove da lista rem
		$.each(arrRem, function(key, value){		
			if(idSel == value) {					
				arrRem.splice(jQuery.inArray(value, arrRem), 1);
				hiddenRem.val(arrRem.join());
			}
		});	

		var linkExcluir = '[<a href="javascript:void(0)" oid="'+idSel+'" class="excluirRegLista" onclick="excluirRegLista(this);">' +
								'<img src="images/icones/t1/x.png">' +
							'</a>]';
							
		var classeTr = (tableLista.find('tr.resultLista').length % 2 == 0)?'tdc':'tde';
		tableLista
			.children('tbody')
				.append('<tr class="resultLista '+classeTr+'">' +
							'<td nowrap width="10%" align="center">'+linkExcluir+'</td>' + 
							'<td nowrap>'+labelSel+'</td>' +
						'</tr>');		

		// volta para a opção padrão		
		comboAdd.removeAttr('selected')
		    .find(':first')     
		        .attr('selected','selected');
	});
});

function alteraObrigatorio(campos, prefixo, remove) {
	$.each(campos, function(){
		// pega id atual 
		var idAtual = jQuery(this).attr('id');

		// remove prefixo se for true; volta o prefixo se for false
		if(remove)
			var idNew      = idAtual.replace(prefixo, '');
		else
			var idNew      = prefixo + idAtual.replace(prefixo, '');

		// seta novo id
		jQuery(this).attr('id', idNew);
	});
}

function excluirRegLista(obj) {
	
	// adiciona o valor a ser removido no campo hidden
	var comboAdd  = jQuery(obj).closest('table').children().find('select');
	var hiddenRem = comboAdd.closest('span').closest('table').children().find('input[name="'+comboAdd.attr('name') + 'Rem' + '"]');
	var hiddenAdd = comboAdd.closest('span').closest('table').children().find('input[name="'+comboAdd.attr('name') + 'Add' + '"]');
	var arrRem 	  = eval(comboAdd.attr('name') + 'ArrRem');
	var arrAdd 	  = eval(comboAdd.attr('name') + 'ArrAdd');

	// adiciona id selecionado no campo hidden				
	arrRem.push(jQuery(obj).attr('oid'));
	hiddenRem.val(arrRem.join());

	// remove da lista add
	$.each(arrAdd, function(key, value){	
		if(jQuery(obj).attr('oid') == value) {			
			arrAdd.splice(jQuery.inArray(value, arrAdd), 1);			
			hiddenAdd.val(arrAdd.join());
		}
	});
	
	jQuery(obj).closest('tr').remove();
	
	return false;
}

function exibeAlertaMsg(msg) {
	criarDiv('mess', '<table height=\"32\" width=\"100%\" valign=\"middle\"><tr onclick=\"removeDiv(\'mess\');\"><td class="\msg\" width=\"96%\" heigth=\"100%\">&nbsp;<font color=\"#CD0000\">'+msg+'</font></td><td width=\"4%\"><img width=\"15\" height=\"15\" src=\"images/X.jpg\"></img></td></tr></table>', '100%', '34', '0', '0');
	alinhaDiv('mess');
	id_interval = setInterval("alinhaDiv('mess')",500);
	fade(0,'mess',80);
}

function isNumber (o) {
	return ! isNaN (o-0) && o !== null && o.replace(/^\s\s*/, '') !== "" && o !== false;
}

// Função buscar transportadoras
function getTranspCli(){
	
	jQuery('#not_id_transpSel').show();
	var transp = jQuery("#not_id_transpCli").val();

	jQuery.ajax({
		dataType: "json",
		url: 'cad_embarcadores.php',
		type: 'post',
		data: {
			transpCli : transp,
			acao : 'transpCliente'
		},
		beforeSend: function(){
			jQuery("#not_id_transpSel").html("<option value=''>Carregando..</option>");
		},
		success: function(data){

			if (data == 0) {
				alert('Nenhum resultado encontrado');
				jQuery("#not_id_transpSel").hide();
			
			}else{
				var opt = "";
				jQuery("#not_id_transpSel").attr('size',5);
				jQuery("#not_id_transpSel").html("");
				
				jQuery.each(data, function(index, value) {
					opt = "<option value='"+index+"'>" +value+ "</option>" ;
					jQuery("#not_id_transpSel").append(opt);
				});
			}
			
		}
	});

}


// Função buscar gerenciadoras de risco
function getGerencRisco(){

	jQuery('#not_id_gerRiscoSel').show();
	
	var gerenc = jQuery("#not_id_gerencGet").val();
	
	jQuery.ajax({
		dataType: "json",
		url: 'cad_embarcadores.php',
		type: 'post',
		data: {
			gerencSel : gerenc,
			acao : 'gerencRisco'
		},
		beforeSend: function(){
			jQuery("#not_id_gerRiscoSel").html("<option value=''>Carregando..</option>");
		},
		success: function(data){

			if (data == 0) {
				alert('Nenhum resultado encontrado');
				jQuery("#not_id_gerRiscoSel").hide();
			
			}else{
				var opt = "";
				jQuery("#not_id_gerRiscoSel").attr('size',5);
				jQuery("#not_id_gerRiscoSel").html("");
				
				jQuery.each(data, function(index, value) {
					opt = "<option value='"+index+"'>" +value+ "</option>" ;
					jQuery("#not_id_gerRiscoSel").append(opt);
				});
			}
			
		}
	});

}