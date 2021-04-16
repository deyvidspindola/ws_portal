
function exibeAlertaMsg(msg) {
	criarDiv('mess', '<table height=\"32\" width=\"100%\" valign=\"middle\"><tr onclick=\"removeDiv(\'mess\');\"><td class="\msg\" width=\"96%\" heigth=\"100%\">&nbsp;<font color=\"#CD0000\">'+msg+'</font></td><td width=\"4%\"><img width=\"15\" height=\"15\" src=\"images/X.jpg\"></img></td></tr></table>', '100%', '34', '0', '0');
	alinhaDiv('mess');
	id_interval = setInterval("alinhaDiv('mess')",500);
	fade(0,'mess',80);
}


$(document).ready(function(){	
	

	 $(".botao").click(function () {
	 	var acao = $(this).attr('name');
	 	if ($(this).val() == 'Cancelar')
	 	{
	 		if (confirm('Deseja cancelar a operação?')) {
				window.location.href = acao;
			} 
	 	} else if ($(this).val() == 'Excluir')
	 	{
	 		if (confirm('Deseja excluir o registro?')) {
				$("#acao").val('excluir');
			 	$("#form").submit();
			} else {
				$("#div_msg").html("Operação Cancelada.");
			}
	 	} else {
		 	$("#acao").val(acao);
		 	$("#form").submit();
	 	}
	 });
	 $(".editar").click(function () {
		 	$("#acao").val('editar');
		 	$("#segoid").val($(this).attr('segoid'));
		 	$("#form").submit();
	 });
	 $(".excluir").click(function () {
	   if (confirm('Deseja excluir o registro?')) {
			$("#acao").val('excluir');
		 	$("#segoid").val($(this).attr('segoid'));
		 	$("#form").submit();
		} else {
			$("#div_msg").html("Operação Cancelada.");
		}
	 });

});