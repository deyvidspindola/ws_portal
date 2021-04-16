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
	function carregarDepartamentos(seletor_combobox_departamento, descricao_inicial_padrao) {
		
		jQuery.ajax({
			url : 'doc_perfil_rh.php',
			type: 'POST',
			data: jQuery('#form').serialize() + '&acao=listarDepartamentos_ajax', 
			beforeSend: function() {
				jQuery(seletor_combobox_departamento).attr('disabled', 'disabled');
			},
			success: function(data) {
				
				try {				
				
					var result = jQuery.parseJSON(data);
					
					str = '<option value="">'+descricao_inicial_padrao+'</option>';
					jQuery.each(result, function(key, item) {
						str += '<option value="'+item.depoid+'">'+item.depdescricao+'</option>';
					});
					
					jQuery(seletor_combobox_departamento).html(str);
				
				} catch (e) {
					jQuery(seletor_combobox_departamento).html('<option value="">'+descricao_inicial_padrao+'</option>');
					
				}
			}, 
			complete: function() {
				jQuery(seletor_combobox_departamento).removeAttr('disabled');
			}
		});
		
	}	

	/**
	 * carregar lista de centros de custos por empresa 
	 */
	function carregarCentrosCustos() {
		
		jQuery.ajax({
			url : 'doc_perfil_rh.php',
			type: 'POST',
			data: jQuery('#form').serialize() + '&acao=listarCentrosCustos_ajax', 
			beforeSend: function() {
				jQuery('#obr_prhcntoid').attr('disabled', 'disabled');
			},
			success: function(data) {
				
				try {
					
					var result = jQuery.parseJSON(data);
					
					str = '<option value="">Escolha</option>';
					jQuery.each(result, function(key, item) {
						str += '<option value="'+key+'">'+item+'</option>';
					});
					
					jQuery('#obr_prhcntoid').html(str);
				
				} catch (e) {
					
					jQuery('#obr_prhcntoid').html('<option value="">Escolha</option>');
				}
			}, 
			complete: function() {
				jQuery('#obr_prhcntoid').removeAttr('disabled');
			}
		});
		
	}
	
	function resetCentrosCustos() {
		jQuery('#obr_prhcntoid').html('<option value="">Escolha</option>');
	}
	
	/**
	 * ação ao selecionar empresa no formulário de busca
	 */
	jQuery('#obr_prhtecoid_busca').change(function(){
		carregarDepartamentos('#departamento_busca', '--Todos--');		
	});
	
	/**
	 * ação ao selecionar empresa no formulário de cadastro
	 */
	jQuery('#obr_prhtecoid').change(function(){
		carregarDepartamentos('#obr_prhdepoid', '--ESCOLHA--');	
		resetCentrosCustos();
		xajax_show_cargo('');
	});

	/**
	 * ação ao selecionar departamento no formulário de cadastro
	 */
	jQuery('#obr_prhdepoid').change(function(){
		carregarCentrosCustos();
	});
});