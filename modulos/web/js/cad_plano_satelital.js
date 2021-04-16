/*
 * @author Jorge A. D. Kautzmann <jorge.kautzmann@sascar.com.br>
 * @description	Módulo para STI - JS
 * @version 28/03/2013 [1.0]
 * @package SASCAR Intranet
 * 
*/
/* Controle de planos */

/*
 * Função para manipular combo-box com fases
*/
function planoChange(){
	document.cadplanosat.acao.value='gerenciar-planos';
	document.cadplanosat.target='_top';
	document.cadplanosat.submit();
}
/*
 * Função validar inclusão de novo Fluxo
*/
function confirmarNovoPlano(){
	var teveErro = false;
	if(document.cadplanosat.asapoid.value != ''){
		alert('Selecione a opção Novo Plano!');
		return;
	}
	document.getElementById('asapoid').className	= 'inputNormal';
	document.getElementById('asapdescricao').className = 'inputNormal';
	if(document.cadplanosat.asapdescricao.value == ''){
		teveErro = true;
		document.getElementById('asapdescricao').className = 'inputError';
	}
	if(teveErro){
		alert('Existem campos de preenchimento obrigatório que não foram preenchidos!');
		return;
	}
	document.cadplanosat.acao.value='confirmar-novo-plano';
	document.cadplanosat.target='_top';
	document.cadplanosat.submit();
}

/*
 * Função para validar a exclusão de plano
*/
function excluirPlano(){
	if(document.cadplanosat.asapoid.value == ''){
		alert('Selecione um Plano para exclusão!');
		return;
	}
	if(confirm('Deseja realmente excluir este Plano?')){
		document.cadplanosat.acao.value='excluir-plano';
		document.cadplanosat.target='_top';
		document.cadplanosat.submit();
	}else{
		return;
	}
}
