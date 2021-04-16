jQuery(document).ready(function() {
	jQuery('#btn_acao').click(function(){
		jQuery('#acao').val('importarAcao');
		jQuery('#form').submit();
	});

	jQuery('#btn_ind_prev').click(function(){
		jQuery('#acao').val('importarIndicadoresPrevistos');
		jQuery('#form').submit();
	});


	jQuery('#btn_ind_real').click(function(){
		jQuery('#acao').val('importarIndicadoresRealizados');
		jQuery('#form').submit();
	});
});