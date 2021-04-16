jQuery(document).ready(function(){
	
	var cadOperadoraGerencial = new CadOperadoraGerencial();
	
	cadOperadoraGerencial.atualizarBotoes();
	
	$("#selecionar_um").click(function(){
		cadOperadoraGerencial.selecionarUm();
	});
	$("#selecionar_todos").click(function(){
		cadOperadoraGerencial.selecionarTodos();
	});
	$("#retirar_um").click(function(){
		cadOperadoraGerencial.retirarUm();
	});
	$("#retirar_todos").click(function(){
		cadOperadoraGerencial.retirarTodos();
	});
	$("#bans_operadora").change(function(){
		cadOperadoraGerencial.atualizarQtdBans();
	});
	
	$("#nome").focus();
});

function CadOperadoraGerencial() {
	var self = this;
	
	this.selecionarUm = function() {
		self.transferirOption("#bans_disponiveis option:selected", "#bans_operadora");
	};
	
	this.selecionarTodos = function() {
		self.transferirOption("#bans_disponiveis option", "#bans_operadora");
	};
	
	this.retirarUm = function() {
		self.transferirOption('#bans_operadora option:selected:not([class$="readonly"])', "#bans_disponiveis");
	};
	
	this.retirarTodos = function() {
		self.transferirOption('#bans_operadora option:not([class$="readonly"])', "#bans_disponiveis");
	};
	
	this.transferirOption = function(selectorDe, selectorPara) {
		var hasReadonly = false;
		
		$(selectorDe).each(function () {
			var readonly = $(this).hasClass('readonly');
			
			if (readonly) {
				hasReadonly = true;
			}
			else {
				self.adicionarOption(selectorPara, $(this).val(), $(this).text());
				
				self.removerOption(selectorDe);
			}
		});
		
		self.atualizarQtdBans();
		self.atualizarBotoes();
		
		if (hasReadonly) {
			alert(MSG_BAN_GASTOS);
		}
	};
	
	this.adicionarOption = function(selector, value, text) {
		$(selector).append($("<option></option>").attr("value", value).text(text));
		
		self.ordenarOptions(selector);
	};
	
	this.ordenarOptions = function(selector) {
		$(selector).each(function() {
		    var selectedValue = $(this).val();
		    
		    $(this).html($("option", $(this)).sort(function(a, b) { 
		        return a.text == b.text ? 0 : a.text < b.text ? -1 : 1;
		    }));
		    
		    $(this).val(selectedValue);
		});
	};
	
	this.removerOption = function(selector) {
		$(selector).remove();
	};
	
	this.atualizarQtdBans = function() {
		var qtdOperadora = $("#bans_operadora option").length;
		
		var textQtd = qtdOperadora + ((qtdOperadora > 1) ? ' BANS' : ' BAN');
		
		$("#qtd_bans_operadora").text(textQtd);
	};
	
	this.atualizarBotoes = function() {
		var qtdDisposniveis = $("#bans_disponiveis option").length;
		var qtdOperadora = $("#bans_operadora option").length;
		
		if (qtdDisposniveis > 0) {
			$("#selecionar_um").show();
			$("#selecionar_todos").show();
		}
		else {
			$("#selecionar_um").hide();
			$("#selecionar_todos").hide();
		}
		
		if (qtdOperadora > 0) {
			$("#retirar_um").show();
			$("#retirar_todos").show();
		}
		else {
			$("#retirar_um").hide();
			$("#retirar_todos").hide();
		}
	};
	
	this.validarMinimoBans = function() {
		var qtdOperadora = $("#bans_operadora option").length;
		
		if (qtdOperadora < MIN_BAN_OPERADORA) {
			alert(MSG_MIN_BAN_OPERADORA);
			
			return false;
		}
		
		return true;
	};
	
	this.selecionarTodosBansOperadora = function() {
		$("#bans_operadora option").each(function () {
			$(this).attr('selected', 'selected');
		});
	};
}