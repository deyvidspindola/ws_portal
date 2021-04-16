
jQuery(document).ready(function(){
	
	jQuery("#loading").hide();
	
	var prnMigracaoLote = new PrnMigracaoLote();
	
	prnMigracaoLote.carregarContratosPorArquivo();
	prnMigracaoLote.migrar();
	prnMigracaoLote.toggleChecked();
	
});

function PrnMigracaoLote() {
	
	this.carregarContratosPorArquivo = function() {
		jQuery("#btn_processar").click(function(){
			jQuery("#div_msg").html("");
			var prnMigracaoLote = new PrnMigracaoLote();
			if (prnMigracaoLote.validar(false)) {
				jQuery("#acao").val('pesquisar');
				jQuery("#frm_prn_migracao_lote").submit();
			}
		});
	};
	
	this.migrar = function() {
		
		jQuery("#btn_migrar").click(function(){
			jQuery("#div_msg").html("");
			var prnMigracaoLote = new PrnMigracaoLote();
			if (prnMigracaoLote.validar(true)) {
				jQuery("#acao").val('migrar');
				jQuery("#frm_prn_migracao_lote").submit();
			}
		});
		
		jQuery("#frm_prn_migracao_lote").submit(function(){
			
			removeAlerta();
			
			var prnMigracaoLote = new PrnMigracaoLote();
			if (jQuery("#acao").val() == 'pesquisar') {

				if (!prnMigracaoLote.validar(false)) {
					return false;
				
				}
				
				jQuery("#loading").show();
				return true;
			}

			
			if (!prnMigracaoLote.validar(true)) {
				return false;
			}
			
			if (!confirm("Deseja mesmo Migrar para o tipo de contrato correspondente os contratos selecionados?")) {
				return false;
			}
			
			jQuery("#loading").show();
			return true;
		});
	};
	
	this.validarArquivo = function() {
		
		return true;
	};
	
	this.validar = function(validarcontrato) {
		removeAlerta();
        jQuery(".input_error").removeClass("input_error");
		jQuery("#div_msg").html('');		
		
		if (jQuery("#arquivo").val() == '' && !validarcontrato) {
            jQuery("#arquivo").addClass("input_error");
			criaAlerta('Informe o Arquivo');
			return false;
		}
		
		if (jQuery("#is_ativar").val() == '') {
            jQuery("#is_ativar").addClass("input_error");
			criaAlerta('Informe a Ação.');
			return false;
		}
		
		
		if (jQuery("#is_gera_os").val() == '' && jQuery("#is_ativar").val() != 's')  {
            jQuery("#is_gera_os").addClass("input_error");
			criaAlerta('Informe o Gera O.S');
			return false;
		}
		
		if(validarcontrato){
		
		if (jQuery("#is_email_processamento").is(':checked') && jQuery("#destinatarios").val() == '') {
            jQuery("#destinatarios").addClass("input_error");
			criaAlerta('Informe o Destinatário');
			return false;
		}
		
		var isFormouContrato = false;
		jQuery(".toggle_checkbox").each(function(){
			var isDisabled = jQuery(this).attr('disabled');
			if (isDisabled != 'disabled') {
				isChecked = jQuery(this).is(':checked');
				if (isChecked) {
					isFormouContrato = true;
				}
			}
		});

		if (!isFormouContrato) {
			criaAlerta('Informe o Contrato');
			return false;
		}
		
		}
		
		return true;
	};
	
	
	
	
	this.toggleChecked = function() {
		
		jQuery("#checked_all").click(function(){
			var isCheckedAll = jQuery(this).is(':checked');
			
			jQuery(".toggle_checkbox").each(function(){
				
				var isDisabled = jQuery(this).attr('disabled');
				
				if (isDisabled != 'disabled') {
					jQuery(this).attr('checked', isCheckedAll);
				}
			});
		});
		
		jQuery(".toggle_checkbox").click(function(){
			
			var isCheckedAll = true;
			
			jQuery(".toggle_checkbox").each(function(){
				
				var isDisabled = jQuery(this).attr('disabled');
				
				if (isDisabled != 'disabled') {
					isChecked = jQuery(this).is(':checked');
					
					if (!isChecked) {
						isCheckedAll = false;
					}
				}
			});
			
			jQuery("#checked_all").attr('checked', isCheckedAll);
		});
	};
		
   	jQuery("#is_ativar").change(function(){
	   	jQuery("#resultado_contratos").html("");
	   	jQuery("#acao").val('pesquisar');
	   	jQuery("#contratos").val('0');
    	
	   	if (jQuery(this).val()=='s'){	    		
    		jQuery("#is_gera_os").val('').attr('disabled','disabled'); 
	   	}
	   	else{
		   jQuery("#is_gera_os").removeAttr('disabled');
		   
	   	}	    
	   	return false;
    });
}
