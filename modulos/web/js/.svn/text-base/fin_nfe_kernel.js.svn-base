$(document).ready(function () {

	$('#selecionarTodasNFs').change(function() {
		$('.checkbox_nf').prop('checked', $('#selecionarTodasNFs').prop('checked'));
	});

	$('#icone_data_emissao').click(function() {
		displayCalendar($('#nfldt_emissao')[0],'dd/mm/yyyy',this);
	});

	$('#gerar_rps').click(function(e){

		var btnGerar = $(this);

		btnGerar.hide();

		e.preventDefault();

		var formAction = $('#form_listagem').attr('action');
		var dataEmissao = $('input[name=nfldt_emissao]').val();
		var notasSelecionadas = $('.checkbox_nf:checked').map(function(){
			return $(this).val();
		}).get();

		$('#download_wrapper').show();

		$.ajax({
			type: 'POST',
			url: formAction,
			data: {
				'notas_fiscais[]' : notasSelecionadas,
				'nfldt_emissao': dataEmissao
			},
			success: function(data){
				
				$('#download_wrapper .carregando').hide();

				if('msgSucesso' in data){
					$('#download_wrapper .mensagem').show().addClass('sucesso').html(data.msgSucesso);
					$('#download_wrapper .download_arquivo').show();
					$('#download_wrapper .download_arquivo .nome_arquivo').text(data.urlArquivo);
					$('#download_wrapper .download_arquivo a').attr('href', 'download.php?arquivo=' + data.urlArquivo);
				}else if('msgErro' in data){
					btnGerar.show();
					$('#download_wrapper .mensagem').show().addClass('erro').html(data.msgErro);
				}

			},
			error: function(){
				btnGerar.show();
				$('#download_wrapper .carregando').hide();
				$('#download_wrapper .mensagem').show().addClass('erro').html('Falha de comunicação.');
			}
		});

		return false;

	});

	$('.checkbox_nf,#selecionarTodasNFs').change(function() {
		$('#liberar_rps').prop('disabled',($('.checkbox_nf:checked').length === 0));
	});

	$('#liberar_rps').click(function(){
		$('#form_acao').val('liberarRPS');
		$('#liberar_rps').prop('disabled',true);
		$('#form').submit();
	});
});
