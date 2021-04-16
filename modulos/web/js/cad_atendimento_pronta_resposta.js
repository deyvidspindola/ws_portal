jQuery(document).ready(function(){
    
    var cep_search          = new AdressSearch();
    var cep_recup_search    = new AdressSearch();
    var veiculo_search      = new VeiSearch();
    var carreta_search      = new VeiSearch();
    objLoading              = new Loading();    
    
    jQuery('.hour').mask('99:99');
    
    
    jQuery('form#cadastro #recuperado').change(function(){
        if(jQuery(this).val() == "" || ~~jQuery(this).val() == 0) {
            jQuery('#cep_recup').attr('disabled', 'disabled');
            jQuery('#destino_veiculo').attr('disabled', 'disabled');
        } else {
            jQuery('#cep_recup').removeAttr('disabled');
            jQuery('#destino_veiculo').removeAttr('disabled');
        }
    });
    
    jQuery('form#cadastro #recuperado').trigger('change');
    
    jQuery('#pesquisar').click(function(){
	
		removeAlerta();
		jQuery("#dt_ini").removeClass("inputError");
		jQuery("#dt_fim").removeClass("inputError");
	
        var dt_ini = jQuery('#dt_ini').val();
        var dt_fim = jQuery('#dt_fim').val();
      
        if (diferencaEntreDatas(dt_fim, dt_ini) < 0){
			criaAlerta("Data final menor que a data inicial."); 
			jQuery("#dt_fim").addClass("inputError");
			return false;	
		}
	
		if(jQuery.trim(jQuery('#placa').val()).length == 0){
		
			if(jQuery.trim(dt_ini).length == 0 || jQuery.trim(dt_fim).length == 0){
				criaAlerta("Campo Data Acionamento obrigatório."); 
				jQuery("#dt_ini").addClass("inputError");
				jQuery("#dt_fim").addClass("inputError");
				return false;
			}
		}
		
		jQuery.ajax({
			url : 'cad_atendimento_pronta_resposta.php',
			type: 'POST',
			data: jQuery('#form').serialize()+'&acao=pesquisar',
			beforeSend: function(){
				jQuery('#img_loading').html('<img src="images/loading.gif" />');
				jQuery('#resultado').hide();
			},
			success: function(data){
				jQuery('#img_loading').hide();
				jQuery('#resultado').html(data);
				jQuery('#resultado').fadeIn(1000);
				
				jQuery('.result:odd').addClass('tde');
				jQuery('.result:even').addClass('tdc'); 
			}
		})
		
    });
   
    jQuery('#novo').click(function(){
        jQuery('#acao').val('novo');
        jQuery('#form').submit();
    });
            
    cep_search.cep_id         = 'cep';
    cep_search.estado_id      = 'uf';
    cep_search.cidade_id      = 'cidade';
    cep_search.bairro_id      = 'bairro';
    cep_search.logradouro_id  = 'logradouro';
    cep_search.numero_id      = 'end_numero';
    cep_search.zona_id        = 'zona';
    cep_search.search();    
    
    cep_recup_search.cep_id         = 'cep_recup';
    cep_recup_search.estado_id      = 'uf_recup';
    cep_recup_search.cidade_id      = 'cidade_recup';
    cep_recup_search.bairro_id      = 'bairro_recup';
    cep_recup_search.logradouro_id  = 'logradouro_recup';
    cep_recup_search.numero_id      = 'numero_recup';
    cep_recup_search.zona_id        = 'zona_recup';
    cep_recup_search.prefix         = '_recup';
    cep_recup_search.search();    
        
    veiculo_search.plaque_field_id      = 'veiculo_placa';
    veiculo_search.color_field_id       = 'veiculo_cor';
    veiculo_search.year_field_id        = 'veiculo_ano';
    veiculo_search.provider_field_id    = 'veiculo_marca';
    veiculo_search.model_field_id       = 'veiculo_modelo';
    
    jQuery('#veiculo_placa').blur(function(){                
        veiculo_search.search();
    });
    
    if(jQuery('#veiculo_placa').val() != undefined && jQuery('#veiculo_placa').val().length != 0) {
        jQuery('#veiculo_placa').trigger('blur');
    }
    
    jQuery('#veiculo_marca').change(function(){
    	
    	if(jQuery('#veiculo_placa').val().length == 0 || jQuery('#veiculo_marca_inexistente') != undefined){
		 jQuery.ajax({            
	            url: 'cad_atendimento_pronta_resposta.php',
	            type: 'POST',
	            data: {acao: 'getIdMarca', marca: jQuery(this).val()},
	            beforeSend: function() {
	               // jQuery('.veiculo_loader'+properties.loader_prefix).show();
	            },
	            success: function(data) {

	                var result = jQuery.parseJSON(data);  
	                
	                jQuery.ajax({            
	    	            url: 'cad_atendimento_pronta_resposta.php',
	    	            type: 'POST',
	    	            data: {acao: 'getModelosByMarca', id_marca: result.id_marca},
	    	            beforeSend: function() {
	    	                //jQuery('.loader_modelo'+properties.loader_prefix).show();
	    	            	jQuery('.loader_modelo').show();
	    	            },
	    	            success: function(data) {

	    	                var result = jQuery.parseJSON(data);  
	    	               
	    	                if(result.length != 0) {
	    	                	
	    	                	var select = "";
	    	                	
	    	                	select += " <select id='veiculo_modelo' name='veiculo_modelo'>";
	    	                	select += " <option value=''>Escolha</option>";
	    	                    
	    	                    jQuery.each(result, function(i, modelo){
	    	                    	select += '<option value="'+modelo.modelo+'">'+modelo.modelo+'</option>';
	    	                        
	    	                    }); 
	    	                    select += " </select><img class='loader_modelo' alt='' src='modulos/web/images/ajax-loader-circle.gif'>";
	    	                    
	    	                	jQuery('#div_veiculo_modelo').html(select);
	    	                }
	    	                
	    	                jQuery('.loader_modelo').hide();
	    	            }
	    	        });
                    
	            }
	        }); 
    	} else {
    		veiculo_search.getProvider();
    	}
		
    });
    
    carreta_search.plaque_field_id      = 'carreta_placa';
    carreta_search.color_field_id       = 'carreta_cor';
    carreta_search.year_field_id        = 'carreta_ano';
    carreta_search.provider_field_id    = 'carreta_marca';
    carreta_search.model_field_id       = 'carreta_modelo';
    carreta_search.loader_prefix        = '_carreta';
    
    jQuery('#carreta_placa').blur(function(){                
        carreta_search.search();
    });
    
    if(jQuery('#carreta_placa').val() != undefined && jQuery('#carreta_placa').val().length != 0) {                
        jQuery('#carreta_placa').trigger('blur');
    }
    
    jQuery('#carreta_marca').change(function(){
    	
    	if(jQuery('#carreta_placa').val().length == 0 || jQuery('#carreta_placa_inexistente') != undefined){
   		 jQuery.ajax({            
   	            url: 'cad_atendimento_pronta_resposta.php',
   	            type: 'POST',
   	            data: {acao: 'getIdMarca', marca: jQuery(this).val()},
   	            beforeSend: function() {
   	               // jQuery('.veiculo_loader'+properties.loader_prefix).show();
   	            },
   	            success: function(data) {
                    //alert(data);
   	                var result = jQuery.parseJSON(data);  
   	                
   	                jQuery.ajax({            
   	    	            url: 'cad_atendimento_pronta_resposta.php',
   	    	            type: 'POST',
   	    	            data: {acao: 'getModelosByMarca', id_marca: result.id_marca},
   	    	            beforeSend: function() {
   	    	                //jQuery('.loader_modelo'+properties.loader_prefix).show();
   	    	            	jQuery('.loader_modelo_carreta').show();
   	    	            },
   	    	            success: function(data) {
                            //alert(data);
   	    	                var result = jQuery.parseJSON(data);  
   	    	               
   	    	                if(result.length != 0) {
   	    	                	
   	    	                	var select = "";
   	    	                	
   	    	                	select += " <select id='carreta_modelo' name='carreta_modelo'>";
   	    	                	select += " <option value=''>Escolha</option>";
   	    	                    
   	    	                    jQuery.each(result, function(i, modelo){
   	    	                    	select += '<option value="'+modelo.modelo+'">'+modelo.modelo+'</option>';
   	    	                        
   	    	                    }); 
   	    	                    select += " </select><img class='loader_modelo_carreta' alt='' src='modulos/web/images/ajax-loader-circle.gif'>";
   	    	                    
   	    	                	jQuery('#div_carreta_modelo').html(select);
   	    	                }
   	    	                
   	    	                jQuery('.loader_modelo_carreta').hide();
   	    	            }
   	    	        });
   	          
                      // jQuery('#veiculo_marca_hidden').val(result.id_marca);
   	            }
   	        }); 
       	} else {
       	 carreta_search.getProvider(jQuery(this).val(), null);
       	}
        
    });
    
    
    jQuery('#latitude').keypress(function(e){

        var valor = jQuery(this).val();
        
        if(valor.charAt(0) == '-'){
            //tecla 8 = backspace
            if(e.keyCode != 8){
                valor = valor.replace(/[^\d]/g, '');
                valor = '-' + valor;
                valor = valor.replace(/(\d{2})/,"$1.");
            } else {
                if(valor.length-1 <= 3){
                   letra = valor.charAt(valor.length - 1);
                   valor = valor.replace(valor.charAt(valor.length - 1), '');
                }
            }
            jQuery(this).attr('maxlength', '17');
            
        } else {
            //tecla 8 = backspace
            if(e.keyCode != 8){
                valor = valor.replace(/[^\d]/g,""); 
                valor = valor.replace(/^(\d{1,2})/,"$1.");
            } else {
                if(valor.length-1 <= 2){
                    letra = valor.charAt(valor.length - 1);
                    valor = valor.replace(valor.charAt(valor.length - 1), '');
                }
                
            }
            jQuery(this).attr('maxlength', '16');
        }        
        
        jQuery(this).val(valor);
        
    });
    
    jQuery('#latitude').blur(function(){
        jQuery(this).trigger('keypress');
    });


    jQuery('#longitude').keypress(function(e){
        
       var valor = jQuery(this).val();
        
        if(valor.charAt(0) == '-'){
            //tecla 8 = backspace
            if(e.keyCode != 8){
                valor = valor.replace(/[^\d]/g, '');
                valor = '-' + valor;
                valor = valor.replace(/(\d{2})/,"$1.");
            } else {
                if(valor.length-1 <= 3){
                   valor = valor.replace(valor.charAt(valor.length - 1), '');
                }
            }
            jQuery(this).attr('maxlength', '17');
            
        } else {
            //tecla 8 = backspace
            if(e.keyCode != 8){
                valor = valor.replace(/[^\d]/g,""); 
                valor = valor.replace(/^(\d{1,2})/,"$1.");
            } else {
                if(valor.length-1 <= 2){
                    valor = valor.replace(valor.charAt(valor.length - 1), '');
                }
                
            }
            jQuery(this).attr('maxlength', '16');
        }        
        
        jQuery(this).val(valor);
    });
    
   jQuery('#longitude').blur(function(){
        jQuery(this).trigger('keypress');
    });
    
    jQuery('#tipo_arquivo').change(function(){
        upload.enable(jQuery('#preroid').val(), jQuery(this).val());
    });
    
    jQuery('#confirmar').click(function(){
        
        removeAlerta();
        jQuery('.inputError').removeClass("inputError");
        jQuery('#div_msg').html('');
        
        jQuery.ajax({
			url : 'cad_atendimento_pronta_resposta.php',
			type: 'POST',
			data: jQuery('form#cadastro').serialize()+'&acao=salvar',
			beforeSend: function(){
                objLoading.show();
			},
			success: function(data){
				
                var result = jQuery.parseJSON(data);
                
                if(result.error_validate_fields) {
                    jQuery.each(result.error_list, function(i, error){                         
                        jQuery(error.input).addClass("inputError");                        
                    });
                    
                    jQuery('.inputError').eq(0).focus();
                    
                    criaAlerta(result.error_list[0].message);
                    return false;
                }
                                                
                if(result.error) {
                    jQuery('#div_msg').html(result.message);
                    jQuery('#div_msg').show();
                } else {
                    jQuery('#div_msg').html(result.message);
                    jQuery('#div_msg').show();
                    
                    jQuery('#div_msg').parent().effect('highlight', {}, 1000);
                    
                    jQuery('form#cadastro')
                        .append('<input type="hidden" id="preroid" name="preroid" value="'+result.preroid+'">');
                    
                    upload.enable(result.preroid, jQuery('#tipo_arquivo').val());
                        
                    jQuery('.anexos').fadeIn();
                    
                }

			},
            complete: function() {
                objLoading.hide();
            },
            error: function() {
                objLoading.hide();
                criaAlerta('Houver algum erro durante a requisição');
            }
		});
        
    });
    
    jQuery('body').delegate('.remover_anexo', 'click', function(){
        
        var confirm = window.confirm('Tem certeza que deseja remover este anexo?');
        
        if(confirm) {
                    
            var objLoading = new Loading();
            var id_anexo = jQuery(this).attr('id');      
            var tr = jQuery(this).parent().parent();
            
            jQuery.ajax({
                url : 'cad_atendimento_pronta_resposta.php',
                type: 'POST',
                data: {
                    acao: 'excluirAnexo',
                    id_anexo: id_anexo,
                    id_atendimento: jQuery('#preroid').val(),
                    nome_arquivo: jQuery(this).attr('rel')
                },
                beforeSend: function(){
                    objLoading.show();
                },
                success: function(data){
                    
                    var result = jQuery.parseJSON(data);
                    
                    jQuery('#div_msg').html(result.message);
                    
                    if(!result.error) {
                        
                        tr.effect('highlight', {}, 1000);
                        
                        tr.fadeOut(500, function(){
                            
                            tr.remove();
                            
                            
                            jQuery('#anexados tr.result').removeClass('tde');
                            jQuery('#anexados tr.result').removeClass('tdc');
                            
                            // Zebrando a tabela
                            jQuery('#anexados tr.result:odd').addClass('tde');
                            jQuery('#anexados tr.result:even').addClass('tdc');
                        });
                    }                    
                    
                }, 
                complete: function() {
                    objLoading.hide();
                    jQuery('#div_msg').show();
                }
            });
        
        }
        
    });
    
    try {
        
        if(jQuery('#preroid').val() != undefined) {
            upload.enable(jQuery('#preroid').val(), jQuery('#tipo_arquivo').val());
        }
        
    }catch(e){
        
    }
    // Zebrando a tabela
    jQuery('#anexados tr.result:odd').addClass('tde');
    jQuery('#anexados tr.result:even').addClass('tdc');
    
});

var upload = {
    
    enable: function(id_atendimento, tipo_arquivo) {
        
        new AjaxUpload('arquivo', {
            action: 'cad_atendimento_pronta_resposta.php',
            type: 'post',
            data: {
                acao: 'uploadAnexo',
                id_atendimento: id_atendimento,
                tipo_arquivo: tipo_arquivo
            },
            name: 'arquivo',
            onSubmit: function(file, ext){
                
                var tipo_arquivo = jQuery('#tipo_arquivo').val();

                if (tipo_arquivo == 'foto' && !(ext && /^(jpg|png|jpeg|gif)$/.test(ext))){                        
                    jQuery('#div_msg').html('Apenas imagens do tipo JPG, PNG ou GIF são permitidos.');
                    jQuery('#div_msg').show();
                    return false;
                }
                
                if (tipo_arquivo == 'documento' && (!ext || /^(jpg|png|jpeg|gif)$/.test(ext))){                        
                    jQuery('#div_msg').html('Apenas documentos são permitidos.');
                    jQuery('#div_msg').show();
                    return false;
                }

                objLoading.show();                            
            },
            onComplete: function(file, response){                    
                
                var result = jQuery.parseJSON(response);

                jQuery('#div_msg').html(result.message);
                jQuery('#div_msg').show();            

                if(!result.error) {

                    var td_excluir = '<td class="center"><b>[</b><img id="'+result.id_arquivo+'" rel="'+result.nome_arquivo+'" class="remover_anexo" align="absmiddle" height="12" width="13" title="Remover" alt="Remover" src="images/del.gif"><b>]</b></td>';
                    var td_preview = '<td class="center"><a href="download.php?arquivo=/var/www/anexos_ocorrencia/'+id_atendimento+'/'+ result.nome_arquivo +'"><img class="preview_anexo" align="absmiddle" height="12" width="13" title="Preview" alt="Preview" src="images/icones/file.gif"></a></td>';                                
                    var td_data_inclusao = '<td>'+ result.data_inclusao +'</td>';
                    var td_tipo = '<td>'+ result.tipo_arquivo +'</td>';
                    var td_arquivo = '<td>'+ result.nome_arquivo +'</td>';
                    var td_usuario = '<td>'+ result.usuario +'</td>';
                    
                    var tr = '<tr class="result">'+ td_excluir+td_preview+td_data_inclusao+td_tipo+td_arquivo+td_usuario+'</tr>';

                    jQuery('#anexados').append(tr);

                    // Zebrando a tabela
                    jQuery('#anexados tr.result:odd').addClass('tde');
                    jQuery('#anexados tr.result:even').addClass('tdc');

                }

                objLoading.hide();
            }
        });
    }
    
}

var AdressSearch = jQuery.Class.create({
    
    cep_id: '',
    estado_id: '',
    cidade_id: '',
    bairro_id: '',
    logradouro_id: '',
    numero_id: '',
    zona_id: '',
    prefix: '',
    
    search: function() {
        
        var cep_id          = this.cep_id;
        var estado_id       = this.estado_id;
        var cidade_id       = this.cidade_id;
        var bairro_id       = this.bairro_id;
        var logradouro_id   = this.logradouro_id;
        var numero_id       = this.numero_id;
        var zona_id         = this.zona_id;
        var prefix          = this.prefix;
        
       /*
        * Busca do endereço a partir do CEP
        * Ao sair do campo CEP, roda um ajax para buscar as informações 
        * do endereço.
        * 
        */
       jQuery('#'+this.cep_id).blur(function(){

           jQuery("input").css("background-color", "#FFFFFF");
           jQuery("select").css("background-color", "#FFFFFF");
           removeAlerta();

           var cep_valido = true;

           if (jQuery(this).val().length == 8){

               jQuery.ajax({
                   url: 'lib/Atom/Helper/helper.php',
                   type: 'post',
                   data: 'acao=buscarEndereco&cep='+jQuery(this).val(),
                   beforeSend: function(){

                       //Loading ao iniciar o processamento                    
                       jQuery('#cep_loader'+prefix).show();

                   },
                   success: function(data){                                              
                       try{            		

                           var resultado = jQuery.parseJSON(data);
                           
                           jQuery('#'+estado_id).html('');
                           jQuery('#'+estado_id).parent().html('<select id="'+estado_id+'" name="'+estado_id+'"></select>');
                           jQuery('#'+estado_id).append(jQuery('<option value="">Escolha</option>'));
                           jQuery.each(resultado.estados, function(i, estado){                               
                               jQuery('#'+estado_id).append(jQuery('<option></option>').attr("value", estado.estoid).text(estado.estuf));
                           }); 
                           jQuery('#'+cidade_id).parent().html('<select id="'+cidade_id+'" name="'+cidade_id+'"><option value="">Escolha</option></select>');
                           jQuery('#'+bairro_id).parent().html('<select id="'+bairro_id+'" name="'+bairro_id+'"><option value="">Escolha</option></select>');
                           jQuery('#'+logradouro_id).val('');
                           jQuery('#'+logradouro_id).removeAttr('readonly');
                           
                           if (resultado.endereco != false){

                               jQuery('#'+estado_id).val(resultado.endereco.uf);
                               jQuery('#'+estado_id).attr('disabled', 'disabled');
                               jQuery('#'+estado_id+'_hidden').remove();
                               jQuery('#'+estado_id).parent().append('<input type="hidden" id="'+estado_id+'_hidden" name="'+estado_id+'_hidden" value="'+resultado.endereco.uf+'">');

                               jQuery('#'+cidade_id).parent().html('<input type="text" id="'+cidade_id+'" name="'+cidade_id+'" size="40" readonly="readonly">');
                               jQuery('#'+cidade_id).val(resultado.endereco.cidade);

                               jQuery('#'+bairro_id).parent().html('<select id="'+bairro_id+'" name="'+bairro_id+'"></select>');                            
                               if(resultado.endereco.bairro_ini && resultado.endereco.bairro_fim){
                                   jQuery('#'+bairro_id).append(jQuery('<option></option>').val('').text('Escolha'));
                               }
                               if(resultado.endereco.bairro_ini){
                                   jQuery('#'+bairro_id).append(jQuery('<option></option>').attr("value", resultado.endereco.bairro_ini).text(resultado.endereco.bairro_ini));
                               }
                               if(resultado.endereco.bairro_fim){
                                   jQuery('#'+bairro_id).append(jQuery('<option></option>').attr("value", resultado.endereco.bairro_fim).text(resultado.endereco.bairro_fim));
                               }

                               jQuery('#'+logradouro_id).val(resultado.endereco.logradouro);
                               jQuery('#'+logradouro_id).attr('readonly', 'readonly');

                           }
                           else{                               
                                cep_valido = false;  
                               
                                jQuery('#'+numero_id).val('');
                                jQuery('#'+zona_id).val('');
                           }

                       }catch(e){                             
                           jQuery("#div_msg").html('Erro ao buscar endereço.');            		
                       }

                       jQuery('.hide'+prefix).fadeIn();
                   },
                   complete: function() {
                       jQuery('#cep_loader'+prefix).hide();
                   }
               });

           }
           else{
               cep_valido = false;
           }
           
           if (!cep_valido){

               jQuery('#'+estado_id).removeAttr('disabled');
               jQuery('#'+estado_id).val('');
               jQuery('#'+estado_id+'_hidden').remove();

               jQuery('#'+cidade_id).parent().html('<select id="'+cidade_id+'" name="'+cidade_id+'"><option value="">Escolha</option></select>');

               jQuery('#'+bairro_id).parent().html('<select id="'+bairro_id+'" name="'+bairro_id+'"><option value="">Escolha</option></select>');

               jQuery('#'+logradouro_id).removeAttr('readonly');
               jQuery('#'+logradouro_id).val('');
           }

       });

       jQuery('#'+this.cep_id).keyup(function(){                
           if(jQuery(this).val().length == 8) {
               jQuery(this).trigger('blur');
           }
       });
       
       if(jQuery('#'+this.cep_id).val() != undefined && jQuery('#'+this.cep_id).val().length == 8) {
           
           var cep_input = this.cep_id;         
           
           jQuery.ajax({
                url : 'cad_atendimento_pronta_resposta.php',
                type: 'POST',
                data: {
                    acao: 'verificaCepExiste',
                    cep: jQuery('#'+this.cep_id).val()                    
                },
                beforeSend: function(){
                    
                },
                success: function(data){                                        
                                                            
                    if(data == 'true') {
                        jQuery('#'+cep_input).trigger('blur');
                    } else {
                        jQuery('.hide').fadeIn();
                    }
                    
                }, 
                complete: function() {
                    
                }
            });
            
       }

       jQuery('body').delegate('#'+this.estado_id, 'change', function(){

           jQuery("input").css("background-color", "#FFFFFF");
           jQuery("select").css("background-color", "#FFFFFF");
           removeAlerta();

           if (jQuery(this).val() != ""){

               jQuery.ajax({
                   async: false,
                   url: 'lib/Atom/Helper/helper.php',
                   type: 'post',
                   data: 'acao=listarCidades&estado='+jQuery(this).val(),
                   beforeSend: function(){

                       //Loading ao iniciar o processamento  
                       jQuery('#cidade_loader'+prefix).show();
                   },
                   success: function(data){
                       try{           		

                           var resultado = jQuery.parseJSON(data);

                           if(resultado.cidades.length > 0){
                               jQuery('#'+cidade_id).parent().html('<select id="'+cidade_id+'" name="'+cidade_id+'">');    
                               jQuery('#'+cidade_id).append(jQuery('<option value="">Escolha</option>'));

                               jQuery.each(resultado.cidades, function(i, cidade){

                                   jQuery('#'+cidade_id).append(jQuery('<option></option>').attr("value", cidade.clcnome).text(cidade.clcnome));
                               });
                           }
                           else{
                               jQuery('#'+cidade_id).parent().html('<input id="'+cidade_id+'" type="text" size="40" name="'+cidade_id+'">');                            
                               jQuery('#'+bairro_id).parent().html('<input id="'+bairro_id+'" type="text" size="40" name="'+bairro_id+'">');
                           }

                       }catch(e){

                           jQuery("#div_msg").html('Erro ao listar cidades.');            		
                       }
                   }
               });

               //Loading ao encerrar o processamento            
               jQuery('#cidade_loader'+prefix).hide();
           }
           else{

                jQuery('#'+cidade_id).html(jQuery('<option value="">Escolha</option>'));
                jQuery('#'+bairro_id).html(jQuery('<option value="">Escolha</option>'));
           }

       });


       jQuery('body').delegate('#'+this.cidade_id, 'change', function(){

           jQuery("input").css("background-color", "#FFFFFF");
           jQuery("select").css("background-color", "#FFFFFF");
           removeAlerta();

           if (jQuery(this).val() != ""){

               jQuery.ajax({
                   async: false,
                   url: 'lib/Atom/Helper/helper.php',
                   type: 'post',
                   data: 'acao=listarBairros&estado='+jQuery("#uf").val()+'&cidade='+jQuery(this).val(),
                   beforeSend: function(){

                       //Loading ao iniciar o processamento                    
                       jQuery('#bairro_loader'+prefix).show();

                   },
                   success: function(data){
                       try{            		

                           var resultado = jQuery.parseJSON(data);

                           if(resultado.bairros.length > 1){

                               jQuery('#'+bairro_id).parent().html('<select id="'+bairro_id+'" name="'+bairro_id+'">');
                               jQuery('#'+bairro_id).append(jQuery('<option value="">Escolha</option>'));

                               jQuery.each(resultado.bairros, function(i, bairro){

                                   jQuery('#'+bairro_id).append(jQuery('<option></option>').attr("value", bairro.cbanome).text(bairro.cbanome));
                               });                        
                           }
                           else{                            
                               jQuery('#'+bairro_id).parent().html('<input id="'+bairro_id+'" type="text" size="40" name="'+bairro_id+'">');
                           }
                       }catch(e){

                           jQuery("#div_msg").html('Erro ao listar cidades.');            		
                       }
                   }
               });

               //Loading ao encerrar o processamento            
               jQuery('#bairro_loader'+prefix).hide();

           }
           else{

                jQuery('#'+bairro_id).html(jQuery('<option value="">Escolha a Cidade</option>'));
           }        
       });    
        
    }
    
    
});

var VeiSearch = jQuery.Class.create({
      
      /* ID do campo placa do veículo */
      plaque_field_id: '',
      
      /* ID do campo cor do veículo */
      color_field_id: '',
      
      /* ID do campo ano do veículo */
      year_field_id: '',
      
      /* ID do campo marca do veículo */
      provider_field_id: '',
      
      /* ID do campo modelo do veículo */
      model_field_id: '',
      
      /* Prefixo do loader do ajax */
      loader_prefix: '',
      
      init: function() {},
      
      search: function() {
            
        var properties = this;
                    
        jQuery.ajax({            
            url: 'cad_atendimento_pronta_resposta.php',
            type: 'POST',
            data: {acao: 'getDadosVeiculo', placa: jQuery('#' + properties.plaque_field_id).val()},
            beforeSend: function() {
                jQuery('.veiculo_loader'+properties.loader_prefix).show();
            },
            success: function(data) {

                var result = jQuery.parseJSON(data);                      

                if(result.length != 0) {
                    
                    jQuery('#' + properties.color_field_id).val(result.cor);
                    jQuery('#' + properties.year_field_id).val(result.ano);
                    jQuery('#' + properties.provider_field_id).val(result.marca);
                    
                    jQuery('#' + properties.provider_field_id)
                        .parent()
                        .append('<input type="hidden" id="'+properties.provider_field_id+'_hidden" name="'+properties.provider_field_id+'_hidden" value="'+result.id_marca+'">');
                    
                    properties.getProvider(result.modelo);
                    
                    jQuery('#placa_inexistente').remove();                    
                    
                }else {
                    jQuery('#' + properties.provider_field_id)
                        .parent()
                        .append('<input type="hidden" id="'+properties.plaque_field_id+'_inexistente" name="'+properties.plaque_field_id+'_inexistente" value="1">');                        
                }

            },
            complete: function() {
                jQuery('.veiculo_loader'+properties.loader_prefix).hide();
            }
        });          
          
      },
      
      getProvider: function(modelo) {
          
          var properties = this;
          
          jQuery.ajax({            
            url: 'cad_atendimento_pronta_resposta.php',
            type: 'POST',
            data: {acao: 'getModelosByMarca', id_marca: jQuery('#' + properties.provider_field_id+'_hidden').val()},
            beforeSend: function() {
                jQuery('.loader_modelo'+properties.loader_prefix).show();
            },
            success: function(data) {

                var result = jQuery.parseJSON(data);                      
                
                if(result.length != 0) {
                    
                    jQuery.each(result, function(i, modelo){
                        jQuery('#'+properties.model_field_id).append(jQuery('<option></option>').attr("value", modelo.modelo).text(modelo.modelo));
                    }); 
                }
                
                if(modelo != null) {
                    jQuery('#' + properties.model_field_id).val(modelo);
                }

            },
            complete: function() {
                jQuery('.loader_modelo'+properties.loader_prefix).hide();
            }
        });
          
      }
	  
});

jQuery.download = function(url, data, method){
	//url and data options required
	if( url && data ){ 
		//data can be string of parameters or array/object
		data = typeof data == 'string' ? data : jQuery.param(data);		 
		//split params into form inputs
		var inputs = '';
		jQuery.each(data.split('&'), function(){ 
			var pair = this.split('=');
			inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />'; 
		});
		//send request
		jQuery('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>')
		.appendTo('body').submit().remove();
	};
};

jQuery('body').delegate('#gerarPdf', 'click', function(){

	var preroid = jQuery('#preroid').val();
	
	jQuery.download('cad_atendimento_pronta_resposta.php', 'acao=gerarPdf&id='+preroid);
	
})

jQuery('body').delegate('#gerarDoc', 'click', function(){

	jQuery('#acao').val('gerarDoc');
	
	jQuery('#cadastro').submit();
	
	
})
