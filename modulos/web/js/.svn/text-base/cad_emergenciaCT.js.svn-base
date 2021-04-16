
function voltar(){
	window.location = 'cad_emergenciaCT.php';
}

function novo() {
	$('#acao').val('novo');
	$('#form').submit();
}

function editar(ID){
	$('#emeeqpoid').val(ID);
	$('#acao').val('editar');
	$('#form').submit();
}

function salvar(){
	$('#emeeqpoid').val(ID);
	$('#acao').val('salvar');
}

function cadastrar(){
	$('#emeeqpoid').val(ID);
	$('#acao').val('cadastrar');
}

function excluir(){
	$('#acao').val('excluir');
	$('#form').submit();
}

jQuery(function() {
	
	if (jQuery('#mensagem').text() == '') {
        jQuery('#mensagem').hide();
    }
	
	//Ações do form
	jQuery('body').delegate('#buttonCancelar', 'click', function(){
		// Pega value do botão clicado
		var acaoValor = $(this).val();

		// Troca ação para o valor correspondente e dá submit no form
		jQuery('#acao').val(acaoValor).closest('form').submit();
	});

});  
