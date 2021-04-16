jQuery(document).ready(function() {
	"use strict";
	
	$ = jQuery;
	
	// Cria alerta e limpa alerta anterior
	function alerta(msg) {
		removeAlerta();
		criaAlerta(msg);
	}
	
	// Cria tabela zebrada AUTOMAGICAMENTE, SHOOP DA WHOOP!
	$('#tabela_resultados tr.item:even').addClass('tdc');
    $('#tabela_resultados tr.item:odd').addClass('tde');
	
    /**
	 * Evento de seleção de todos os checkboxes
	 */
	$('#check_all').click(function() {
		var self = $(this);
		
		if (self.is(':checked')) {
			$('.item_id_del').attr('checked', true);			
		} else {
			$('.item_id_del').attr('checked', false);
		}
	});
	
	/**
	 * Valida os comboboxes de busca/adição de registro
	 */
	function validaCombos() {
		var item_produto_id = $('#item_produto_id'),
			item_falha_id = $('#item_falha_id'),
			isValid = true;
		
		item_produto_id.removeClass('highlight');
		item_falha_id.removeClass('highlight');
		
		if (parseInt(item_produto_id.val()) === 0) {
			alerta('Preencher os campos obrigatórios.');
			item_produto_id.addClass('highlight');
			isValid = false;
		}
		
		if (parseInt(item_falha_id.val()) === 0) {
			alerta('Preencher os campos obrigatórios.');
			item_falha_id.addClass('highlight');
			isValid = false;
		}
		
		return isValid;
	}
	
	/**
	 * Evento de exclusão de item
	 */
	$('#bt_excluir').click(function(e) {
		var id_items = $('.item_id_del:checked'),
			action = $(this).data('action'),
			item_falha_id = $(this).data('item-falha-id'); // ID do tipo de falha (1, 2 ou 3)
		
		// Checa se há algum checkbox selecionado
		if (id_items.length === 0) {
			alerta('Selecionar um registro para exclusão.');
			e.preventDefault();
			return;
		}
		
		// Exibe diálogo de confirmação
		var confirmacao = confirm('Deseja realmente excluir o item?');
		
		if (confirmacao === false) {
			e.preventDefault();
			return;
		}
		
		// Parâmetros enviados via POST, gambi!!!!!!
		var post = id_items.serialize() + '&item_falha_id=' + item_falha_id;
		
		$.post(action + '?acao=excluir', post, function() {
			window.location.reload();
		});
	});
	
	/**
	 * Evento de cadastro de item
	 */
	$('#bt_novo').click(function(e) {
		var item_descricao = $('#item_descricao');
		
		// Não envia formulário descrição for vazia
		if (item_descricao.val().length === 0) {
			alerta('Inserir no campo correspondente o texto a ser gravado.');
			item_descricao.addClass('highlight');
			e.preventDefault();
		}
		
		// Verifica se os combos estão selecionados
		if (!validaCombos()) {
			e.preventDefault();
		}
		
		// Configura o target e o método da requisição
		var form = $('#pesquisa_controle_falhas'),
			action = form.data('action');
		
		form.attr('action', action + '?acao=novo')
			.attr('method', 'POST');
	});
	
	/**
	 * Evento de pesquisa de itens
	 */
	$('#bt_pesquisar').click(function(e) {		
		// Configura o target e o método da requisição
		var form = $('#pesquisa_controle_falhas'),
			action = form.data('action');
		
		// Verifica se os combos estão selecionados
		if (!validaCombos()) {
			e.preventDefault();
		}
		
		form.attr('action', action)
			.attr('method', 'GET');
	});
});