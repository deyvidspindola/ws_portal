/**
 * @author Dyorg Almeida <dyorg.almeida@meta.com.br>
 * @since 17/01/2013 
 */
jQuery(document).ready(function(){

	/**
	 * carregar lista de departamentos por empresa 
	 * @param seletor_combobox_departamento - seletor do combobox departamento a ser populado 
	 * @param descricao_inicial_padrao - primeira descrição do combobox
	 */
	function carregarDepartamentos(seletor_combobox_departamento, descricao_inicial_padrao) {
		
		jQuery.ajax({
			url : 'cad_requisicao_material_novo.php',
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
			url : 'cad_requisicao_material_novo.php',
			type: 'POST',
			data: jQuery('#form').serialize() + '&acao=listarCentrosCustos_ajax', 
			beforeSend: function() {
				jQuery('#centro_custo_requisicao_id').attr('disabled', 'disabled');
			},
			success: function(data) {
				try {
					jQuery('#centro_custo_requisicao_id').empty().html(data);
				
				} catch (e) {
					jQuery('#centro_custo_requisicao_id').html('<option value="">Escolha</option>');
				}
			}, 
			complete: function() {
				jQuery('#centro_custo_requisicao_id').removeAttr('disabled');
			}
		});
		
	}
	
	
	/**
	 * carregar lista de departamentos por empresa 
	 * @param seletor_combobox_departamento - seletor do combobox departamento a ser populado 
	 * @param descricao_inicial_padrao - primeira descrição do combobox
	 */
	function carregarAprovadores() {
		
		seletor_combobox_aprovadores = ''; 
		descricao_inicial_padrao = '';
		
		jQuery.ajax({
			url : 'cad_requisicao_material_novo.php',
			type: 'POST',
			data: jQuery('#form').serialize() + '&acao=listarAprovadores_ajax', 
			beforeSend: function() {
				jQuery('#rmapusuoid_aprovador_g').attr('disabled', 'disabled');
			},
			success: function(data) {
				
				try {				
				
					var result = jQuery.parseJSON(data);
					
					str = '<option value="">--Escolha--</option>';
					jQuery.each(result, function(key, item) {
						str += '<option value="'+item.id+'">'+item.nome+'</option>';
					});
					
					jQuery('#rmapusuoid_aprovador_g').html(str);
				
				} catch (e) {
					jQuery('#rmapusuoid_aprovador_g').html('<option value="">--Escolha--</option>');
					
				}
			}, 
			complete: function() {
				jQuery('#rmapusuoid_aprovador_g').removeAttr('disabled');
			}
		});
		
	}	
	
	function resetCentrosCustos() {
		jQuery('#centro_custo_requisicao_id').html('<option value="">--Escolha--</option>');
	}
	
	function resetAprovadores() {
		jQuery('#rmapusuoid_aprovador_g').html('<option value="">--Escolha--</option>');
	}
	
	/**
	 * ação ao selecionar empresa no formulário de cadastro
	 */
	jQuery('#empresa_requisicao_id').change(function(){
		carregarDepartamentos('#departamento_requisicao_id', '--Escolha--');	
		carregarCentrosCustos();
		resetAprovadores();
	});

	/**
	 * ação ao selecionar departamento no formulário de cadastro
	 */
	jQuery('#departamento_requisicao_id').change(function(){
		carregarCentrosCustos();
		resetAprovadores();
	});
	
	/**
	 * ação ao selecionar centro de custo no formulário de cadastro
	 */
	jQuery('#centro_custo_requisicao_id').change(function(){
		carregarAprovadores();
	});
	
	jQuery('#bt_atualizar').click(function(){
	
		var campos_obrigatorios_preenchidos = true;
		removeAlerta();
		
		$('[obrigatorio="true"]').each(function(i){
			$(this).removeClass('inputError');
			if ($.trim($(this).val()).length == 0) {
				$(this).addClass('inputError');
				campos_obrigatorios_preenchidos = false;
			}
		});		
		
		if (campos_obrigatorios_preenchidos == false) {
			criaAlerta('Existem Campos Obrigatórios a serem preenchidos.');
		} else {
			
			// invoca função xajax para salvar atualizações
			reqmoid = jQuery('#reqmoid').val();
			ck_aprovacao_t = document.getElementById('ck_aprovacao_t').checked;
			reqmdt_direcionamento_almox = jQuery('#reqmdt_direcionamento_almox').val();
			xajax_confirma_edicao(reqmoid,ck_aprovacao_t,reqmdt_direcionamento_almox);		
		}
	}); 

});
    //Visualizar campos se o produto já foi faturado
    function visualizarCamposRmiFaturado(){
        var valor = jQuery("#rmifaturado").val();
        jQuery("#rmicnpj_forn").mask("99.999.999/9999-99");
        
        var numeronf = jQuery("#rminumnf");
        var valornf = jQuery("#rmivlr_nf");
        var fornecedor_cnpj = jQuery("#rmicnpj_forn");
        var fornecedor_nome = jQuery("#rmiforn_nome");
        var responsavel_compra = jQuery("#rmiresp_compra");
        
        
        var linhas = jQuery('tr.rmifaturado');

        if (valor == 'S'){
            linhas.removeClass('invisivel');
            linhas.show();
        } else {
            linhas.addClass('invisivel');
            numeronf.val('');
            valornf.val('0,00');
            fornecedor_cnpj.val('');
            fornecedor_nome.val('');
            responsavel_compra.val('');
        }
    }
    
    
    function validarInclusaoMaterial(){
        
        var linha_mensagem = jQuery("td.msg");
        
        linha_mensagem.hide();
        
        var material = jQuery("#rmimatoid");
        
        var dt_emissao    = jQuery('#rminfdt_emissao');
        var dt_vencimento = jQuery('#rminfdt_vencimento');
        
        var motivo = jQuery("#rmimotivo");
        var local = jQuery("#rmilocal_util");
        
        var finalidade = jQuery("#rmifinitem");
        var faturado = jQuery("#rmifaturado");
        var numeronf = jQuery("#rminumnf");
        var valornf = jQuery("#rmivlr_nf");
        var fornecedor_cnpj = jQuery("#rmicnpj_forn");
        var fornecedor_nome = jQuery("#rmiforn_nome");
        var responsavel_compra = jQuery("#rmiresp_compra");
        
        var msg = '';
        
        var erro = false;
        
        if (material.val().length == 0){
            msg += 'É necessário informar o MATERIAL e o FIM A QUE SE DESTINA.<br>';
            erro = true;
        }
        
        
        if (motivo.val().length < 15){
            msg += 'Motivo da Compra do Produto/Serviço, o campo deve ser preenchido com no mínimo 15 caracteres.<br>';
            erro = true;
        }
        
        if (local.val().length == 0){
            msg += "Preencha o campo Local a ser executado/utilizado o produto/serviço<br>";
            erro = true;
        }
        
        if (finalidade.val().length == 0){
            msg +="Preencha o campo Finalidade do produto/serviço<br>";
            finalidade.focus();
            erro = true;
        }
        
        
        if (faturado.val() == 'S'){
            
            if (numeronf.val().length == 0){
                msg +="Preencha o campo Numero da NF<br>";
                erro = true;
            }
            
            
            if (dt_emissao.val().length == 0){
                msg +='Preencha o campo Data de Emissão da NF<br>';
                erro = true;
            }
            
            if (dt_vencimento.val().length == 0){
                msg +='Preencha o campo Data de Vencimento da NF<br>';
                erro = true;
            }
            
            if (dt_vencimento.val() != '' && dt_emissao.val() == ''){
                msg +='Preencha o campo Data de Emissão da NF<br>';
                erro = true;
            }
            if (dt_vencimento.val() != '' && dt_emissao.val() != ''){
                var diferenca = diferencaEntreDatas(dt_vencimento.val(), dt_emissao.val());

                if (diferenca < 0){
                    msg +='A Data de Vencimento da NF não pode ser menor que a Data de Emissão da NF<br>';
                    dt_vencimento.val('');
                    erro = true;
                }
            }
            
            
            if (valornf.val().length == 0){
                msg +="Preencha o campo Valor da NF<br>";
                erro = true;
            }
            
            if (fornecedor_cnpj.val().length == 0){
                msg +="Preencha o campo CNPJ Fornecedor<br>";
                erro = true;
            }
            
            if (fornecedor_nome.val().length == 0){
                msg +="Preencha o campo Nome do Fornecedor<br>";
                erro = true;
            }
            
            if (responsavel_compra.val().length == 0){
                msg +="Preencha o campo Responsável por realizar a compra<br>";
                erro = true;
            }
            
        }

        if (erro){
            linha_mensagem.html(msg);
            linha_mensagem.show();
            return false;
        } else {
            return true;
        }
    }
    