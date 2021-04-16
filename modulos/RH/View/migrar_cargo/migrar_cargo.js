/**
 * @author Dyorg Almeida <dyorg.almeida@meta.com.br>
 * @since 08/01/2013 
 */
jQuery(document).ready(function(){

	/**
	 * carregar lista de departamentos por empresa 
	 * @param seletor_combobox_departamento - seletor do combobox departamento a ser populado 
	 * @param descricao_inicial_padrao - primeira descrição do combobox
	 */
	function carregarDepartamentos() {
		
		jQuery.ajax({
			url : 'migrar_cargo.php',
			type: 'POST',
			data: jQuery('#frm').serialize() + '&acao=listarDepartamentos_ajax', 
			beforeSend: function() {
				jQuery('#prhdepoid').attr('disabled', 'disabled');
				jQuery('#prhdepoid_final').attr('disabled', 'disabled');
			},
			success: function(data) {
				
				try {
				
					var result = jQuery.parseJSON(data);
					
					str = '<option value="">Todos</option>';
					jQuery.each(result, function(key, item) {
						str += '<option value="'+item.depoid+'">'+item.depdescricao+'</option>';
					});
					
					jQuery('#prhdepoid').html(str);
					jQuery('#prhdepoid_final').html(str);
				
				} catch (e) {
					
					jQuery('#prhdepoid').html('<option value="">Todos</option>');
					jQuery('#prhdepoid_final').html('<option value="">Todos</option>');
					
					if (typeof console != 'undefined' && typeof console.debug != 'undefined') {
						console.log('Ocorreu um erro ao carregar lista de departamentos');
					}
				}
			}, 
			complete: function() {
				jQuery('#prhdepoid').removeAttr('disabled');
				jQuery('#prhdepoid_final').removeAttr('disabled');
				jQuery('#usucargooid').html('<option value="">Todos</option>');
				jQuery('#usucargooid_final').html('<option value="">Todos</option>');
			}
		});
		
	}	
	
	jQuery('#obr_tecoid').change(function(){
		carregarDepartamentos();
	});
	
});