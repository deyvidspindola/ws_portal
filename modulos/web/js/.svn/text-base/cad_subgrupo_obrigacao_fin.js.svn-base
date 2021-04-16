$(document).ready(function(){

	$('#lbl_paginacao_classificacao').parent().remove();
	
	$('.btn-link').click(function(){
		var href = $(this).data('href');
		window.location = href;
		return false;
	});

	$('.pesquisar').click(function(){
		var href = "cad_subgrupo_obrigacao_fin.php?acao=pesquisar&descricao="+$('.descricao-pesquisa').val();
		window.location = href;
		return false;
	});

	$.extend( $.fn.dataTable.defaults, {
        "searching": false,
        "ordering": false
	});
	
	$('#table-lista-subgrupo').dataTable({
		"pagingType": "simple_numbers",
		"bLengthChange": false,
		"dom": '<"top">rt<"bottom"flp><"clear">',
		"oLanguage": {
			"oPaginate": {
				"sFirst": "Primeira",
				"sLast" : "Última",
				"sPrevious" : "<",
				"sNext" : ">"
			},
			"sLengthMenu": "",
			"sZeroRecords": "",
			"sInfo": "",
			"sInfoEmpty": "",
			"sInfoFiltered": "",
		}
	});
	
	$('#tbl_historico').dataTable({
		"pageLength": 15,
		"pagingType": "simple_numbers",
		"bLengthChange": false,
		"dom": '<"top">rt<"bottom"flp><"clear">',
		"oLanguage": {
			"oPaginate": {
				"sFirst": "Primeira",
				"sLast" : "Última",
				"sPrevious" : "<",
				"sNext" : ">"
			},
			"sLengthMenu": "",
			"sZeroRecords": "",
			"sInfo": "",
			"sInfoEmpty": "",
			"sInfoFiltered": ""
		}
	});
});