tinyMCE.init({
    // General options
    language : 'pt',
    mode : 'textareas',
    theme : 'advanced',
    editor_selector : 'editor',
    editor_deselector : 'no-editor',
    plugins : 'safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount',

    theme_advanced_buttons1_add : "fontselect,fontsizeselect",
    theme_advanced_buttons2_add_before : "cut,copy,paste,pastetext,pasteword,separator",
    theme_advanced_buttons2_add : "separator,forecolor,backcolor",
    theme_advanced_buttons3 : "tablecontrols,separator",
    theme_advanced_buttons3_add : "fullscreen,preview,code",
    theme_advanced_buttons4_add : "iespell,media,advhr,separator,print,separator,ltr,rtl,separator,fullscreen",
    theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,|,code",

    theme_advanced_toolbar_location : 'top',
    theme_advanced_toolbar_align : 'left',
    theme_advanced_statusbar_location : 'bottom',
    theme_advanced_resizing : false,

    // Example content CSS (should be your site CSS)
    content_css : 'includes/css/base_editor.css',

    // Drop lists for link/image/media/template dialogs
    template_external_list_url : 'lib/tinymce/lists/template_list.js',
    external_link_list_url : 'lists/link_list.js',
    external_image_list_url : 'lists/image_list.js',
    media_external_list_url : 'lists/media_list.js',
    height: 400
});



/**
 * Código da tela
 */
jQuery(document).ready(function() {
    "use strict";
    var $ = jQuery;

    // Exibe mensagem de alerta
    function alerta(msg) {
		removeAlerta();
		criaAlerta(msg);
	}

    // Destacar campo
    function highlight(elm) {
    	elm.addClass('highlight');
    }

    // Limpa elementos destacados
    function clearHighlight() {
    	jQuery('.highlight').removeClass('highlight');
    }

    // Cria tabela zebrada AUTOMAGICAMENTE, SHOOP DA WHOOP!
	jQuery('#tabela_resultados tr.item:even').addClass('tdc');
    jQuery('#tabela_resultados tr.item:odd').addClass('tde');

    /**
     * Ação de exclusão de registro
     */
    jQuery('.excluir').click(function() {
    	var seeoid = jQuery(this).data('seeoid');


    	var seepadrao = jQuery(this).data('seepadrao');
    	if(seepadrao === 't') {
    		alerta('Este layout é definido como padrão. Se deseja excluir, favor configurar outro layout como padrão.');
    		return;
    	}


    	// Exibe diálogo de confirmação
		var confirmacao = confirm('Deseja realmente excluir o item?');

		if (confirmacao === false) {
			return;
		}

		// Envia post com requisição de exclusão
		var post = {
			seeoid: seeoid
		};

		$.post(ACTION + '?acao=excluir', post, function() {
			window.location.reload();
		});
    });

    /**
     * Ação de edição de registro
     */
    jQuery('#editar_layout_emails').submit(function(e) {
    	var seeseefoid 	 = jQuery('#seeseefoid'),
    		seeseetoid 	 = jQuery('#seeseetoid'),
	        seeobjetivo  = jQuery('#seeobjetivo'),
	        seecabecalho = jQuery('#seecabecalho'),
	        isValid	     = true;
    	var seetipo = jQuery('#seetipo');

    	// Limpa elementos destacados
    	clearHighlight();

    	// Valida preenchimento da funcionalidade
    	if (parseInt(seeseefoid.val()) === 0) {
    		highlight(seeseefoid);
    		isValid = false;
    	}

    	// Valida preenchimento do titulo do e-mail
    	if (parseInt(seeseetoid.val()) === 0) {
    		highlight(seeseetoid);
    		isValid = false;
    	}

    	// Valida objetivo
    	if (seeobjetivo.val().length == 0) {
    		highlight(seeobjetivo);
    		isValid = false;
    	}


    	if (seetipo.val() == 'E'){

        	if (seecabecalho.val().length === 0) {
        		highlight(seecabecalho);
        		isValid = false;
        	}

            var seecorpo = jQuery('#seecorpo_ifr');

        	var currentLength = parseInt(jQuery('#seecorpo_ifr')
	    			.contents()
	    			.find("body")
	    			.html()
	    			.replace(/(<([^>]+)>)/ig,'')
	    			.length);

			if (currentLength === 0) {
				highlight(seecorpo);
				isValid = false;
			}

			if (currentLength > 3000) {
				alerta('O campo "Texto padrão" excedeu o limite máximo de 3000 caracteres.');

				return;
			}
    	} else {


          	var currentLengthSms = parseInt(jQuery('#seecorpoSms').val().length);

    		if (currentLengthSms == 0) {
    			highlight(jQuery('#seecorpoSms'));
    			isValid = false;
    		}

    		if (currentLengthSms > 120) {
    			alerta('O campo "Texto padrão" excedeu o limite máximo de 120 caracteres.');
    			e.preventDefault();
    			return;
    		}

    	}
    	if (!isValid) {
    		alerta('Existem campos obrigatórios não preenchidos.');
    		e.preventDefault();
    		return false;
    	}

        if(jQuery("#seeimagem_anexo").is(":checked") && jQuery("#seeimagem").val()==""){

            alerta('Informe o caminho da imagem anexa.');
    		e.preventDefault();
    		return false;
        }


    });

    /**
     * Ação carrega valores titulo layout
     */

	jQuery('#seeseefoid').change( function(){

		jQuery(".titulo_layout").hide();

		var idTituloEmail = jQuery('#seeseefoid').val();

		if(idTituloEmail != ''){

			var opcaoPesquisa = jQuery('#seeseefoid :selected').text();

			//se for pesquisa satisfação, exibe o texto automático, se não limpa o texto
			if($.trim(opcaoPesquisa) === 'Pesquisa de Satisfacao'){
				jQuery("#msg_texto").show();
				jQuery("#msg_texto").html('<img style="margin: 5px 10px 0 15px" src="images/help10.gif">O endereço para a pesquisa de satisfação deve ser exatamente como apresentado abaixo: <br/><br/> http://www.sascar.com.br/questionariopesquisasatisfacao.jspx?cP=$codControleQuestionario&cC=$codCliente');
			}else {
				jQuery("#msg_texto").html('');
				jQuery("#msg_texto").hide();
			}

			var ajax = true;

			if (jQuery("#loading_titulo").length) {
				jQuery('#loading_titulo').show()
			} else {
				jQuery('#seeseetoid').html('<img src="images/progress4.gif" id="loading_tipo" />');
			}

			var post = {
					seefoid: idTituloEmail
			};

			$.post(ACTION + '?acao=listaTitulosEmail', post, function(r) {

				if (r.length) {

	                var resultado = jQuery.parseJSON(r);

	                if(resultado != ''){

						jQuery("#seeseetoid").html("");
						jQuery("#seeseetoid").append(jQuery('<option>Selecione</option>').attr("value",''));

						jQuery(".titulo_layout").show();

						jQuery.each(resultado,function(i, res) {
							jQuery("#seeseetoid").append(jQuery('<option></option>').attr("value", res.seetoid).text(res.seetdescricao));
						});

						jQuery("#seeseetoid").val(jQuery("#titulo_layout_select").val());

					}else{
						jQuery("#seeseetoid").val("");
						jQuery(".titulo_layout").hide();
					}
					jQuery('#loading_titulo').hide();

	            }
			});
		}
	});



    /**
     * Ação carrega valors sub tipo proposta
     */

	jQuery('#tppoid').change( function(){

		jQuery(".sub_tipo_pro").hide();

		var idTipoProposta = jQuery('#tppoid').val();

		if(idTipoProposta != ''){

			var ajax = true;

			if (jQuery("#loading_tipo").length) {
				jQuery('#loading_tipo').show()
			} else {
				jQuery('#lconftppoid_sub').html('<img src="images/progress4.gif" id="loading_tipo" />');
			}

			var post = {
					tppoid: idTipoProposta
			};

			$.post(ACTION + '?acao=listaSubtipoProposta', post, function(r) {

				if (r.length) {

	                var resultado = jQuery.parseJSON(r);

	                if(resultado != ''){

						jQuery("#lconftppoid_sub").html("");
						jQuery("#lconftppoid_sub").append(jQuery('<option>Todos</option>').attr("value",''));

						jQuery(".sub_tipo_pro").show();

						jQuery.each(resultado,function(i, res) {
							jQuery("#lconftppoid_sub").append(jQuery('<option></option>').attr("value", res.tppoid).text(res.tppdescricao));
						});

						jQuery("#lconftppoid_sub").val(jQuery("#sub_tipo_pro_select").val());

					}else{
						jQuery("#lconftppoid_sub").val("");
						jQuery(".sub_tipo_pro").hide();
					}
					jQuery('#loading_tipo').hide();

	            }
			});
		}
	});

''


    /**
     * Conta caracteres digitados no corpo do texto
     */
    // Atualiza a cada 10ms, Workaround Patterns, GAMMA, E. et al.
    setInterval(updateCounter, 10);

    function updateCounter() {
    	if (!jQuery('#seecorpo_ifr').length) return;

    	var MAX_LENGTH = 3000,

    		currentLength = parseInt(jQuery('#seecorpo_ifr')
					    			.contents()
					    			.find("body")
					    			.html()
					    			.replace(/(<([^>]+)>)/ig,'')
					    			.length);

		// Atualiza contador de palavras
		jQuery('#char-count').text(MAX_LENGTH - currentLength);



    }


    /**
     * Conta caracteres digitados no corpo do texto Sms
     */
    // Atualiza a cada 10ms, Workaround Patterns, GAMMA, E. et al.
    setInterval(updateCamposSms, 100);

    function updateCamposSms(){
    	var MAX_LENGTH = 120;

    	if (jQuery('#seecorpoSms').length == 0)
    		return;

    	updateTextoSms(MAX_LENGTH);
    	updateCounterSms(MAX_LENGTH);

    }

    function updateTextoSms(maximoCaracteres){
    	jQuery('#seecorpoSms').text(jQuery('#seecorpoSms').val().slice(0, maximoCaracteres));
    }

    function updateCounterSms(maximoCaracteres) {
    	var currentLengthSms = parseFloat(jQuery('#seecorpoSms').val().length);

    	// Atualiza contador de palavras
    	jQuery('#countSms').text(maximoCaracteres - currentLengthSms);

    }





    /**
     * Exclui imagem do layout via AJAX
     */
    jQuery('.excluir-imagem a').click(function() {
        var seeoid = jQuery(this).data('seeoid');

        // Exibe diálogo de confirmação
		var confirmacao = confirm('Deseja realmente excluir a imagem?');

		if (confirmacao === false) {
			return;
		}

		// Envia post com requisição de exclusão
		var post = {
			seeoid: seeoid
		};

		$.post(ACTION + '?acao=excluirImagem', post, function(r) {

			if (r.length) {
                alert('A imagem não pôde ser excluída.');
            } else {
                jQuery('.imagem-atual').remove();
            }
		});
    });

    // Carrega dados subtipo proposta
    if(jQuery('#tppoid').val()) {
    	jQuery('#tppoid').change();
    }
    // Carrega dados titulo
    if(jQuery('#seeseefoid').val() > 0) {
    	jQuery('#seeseefoid').change();
	}



    	/** DUM 81396 **/
    if (jQuery('#seetipo').val() != 'S'){
  	  jQuery('#corpoSms').hide();
	  jQuery('#char-countSms').hide();
	  jQuery('#legSms').hide();
	  jQuery('#osSms').hide();
	  jQuery('#placaSms').hide();
	  jQuery('#dtAgendamentoSms').hide();
	  jQuery('#hmSms').hide();
      jQuery("#legendaNumeroContrato").hide();
	  jQuery('#layoutSms').hide();
    }


	 jQuery('#seetipo').change(function(event){
		 alterarLayout(jQuery(this).val());
	 });


});


function alterarLayout(tipo){

	 if (tipo == 'E') {

		 jQuery("#seecabecalho").show("slow");
		 jQuery("#assunto").show("slow");
		 jQuery("#servidor").show("slow");
		 jQuery("#srvoid").show("slow");
		 jQuery("#importar").show("slow");
		 jQuery("#seeimagem").show("slow");
		 jQuery("#seeimagem_anexo").show("slow");
		 jQuery("#seeimagem_anexo_label").show("slow");
		 jQuery("#seecorpoId").show("slow");
		 jQuery("#char-countId").show("slow");
		 jQuery("#layoutEmails").show("slow");
		 jQuery("#corpoSms").hide("slow");
		 jQuery("#char-countSms").hide("slow");
		 jQuery("#layoutSms").hide("slow");
		 jQuery("#legSms").hide("slow");
		 jQuery("#osSms").hide("slow");
		 jQuery("#placaSms").hide("slow");
		 jQuery("#dtAgendamentoSms").hide("slow");
		 jQuery("#hmSms").hide("slow");
         jQuery("#legendaNumeroContrato").hide("slow");
		 jQuery("#srvoidSms").show("slow");
		 jQuery("#usuarioSms").show("slow");
		 jQuery("#usuarios").show("slow");
		 jQuery("#titFunc").hide("slow");
		 jQuery("#seeremetente").show("slow");
		 jQuery("#remetente").show("slow");


	 } else {
 		 jQuery("#seecabecalho").hide("slow");
		 jQuery("#assunto").hide("slow");
		 jQuery("#servidor").hide("slow");
		 jQuery("#srvoid").hide("slow");
		 jQuery("#importar").hide("slow");
		 jQuery("#seeimagem").hide("slow");
		 jQuery("#seeimagem_anexo").hide("slow");
		 jQuery("#seeimagem_anexo_label").hide("slow");
		 jQuery("#seecorpoId").hide("slow");
		 jQuery("#char-countId").hide("slow");
		 jQuery("#layoutEmails").hide("slow");
		 jQuery("#corpoSms").show("slow");
		 jQuery("#char-countSms").show("slow");
		 jQuery("#legSms").show("slow");
		 jQuery("#osSms").show("slow");
		 jQuery("#placaSms").show("slow");
		 jQuery("#dtAgendamentoSms").show("slow");
		 jQuery("#hmSms").show("slow");
		 jQuery("#layoutSms").show("slow");
		 jQuery("#srvoidSms").hide("slow");
		 jQuery("#usuarioSms").hide("slow");
		 jQuery("#usuarios").hide("slow");
		 jQuery("#titFunc").show("slow");
		 jQuery("#seeremetente").hide("slow");
		 jQuery("#remetente").hide("slow");
		 jQuery("#bt_visualizar").hide("slow");

	 }
}
