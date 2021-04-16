tinyMCE.init({
    // General options
    language : 'pt',
    mode : 'textareas',
    theme : 'advanced',
    editor_selector : 'editor',
    editor_deselector : 'no-editor',
    //readonly: 1,
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
    //content_css : 'includes/css/base_editor.css',

    // Drop lists for link/image/media/template dialogs
    template_external_list_url : 'lib/tinymce/lists/template_list.js',
    external_link_list_url : 'lists/link_list.js',
    external_image_list_url : 'lists/image_list.js',
    media_external_list_url : 'lists/media_list.js',
    height: 400,
    width: 750
});


/**
 * Código da tela
 */
jQuery(document).ready(function() {
	
    /**
     * Ação de edição de registro
     */
	jQuery("body").delegate('#bt_enviar', 'click', function(e){

    	var seeoid 	 = jQuery('#seeoid'), 
    		seecorpo = jQuery('#seecorpo'),
    		isValid	 = true;
    	
    	// Limpa elementos destacados
    	removeAlerta();
    	$('.highlight').removeClass('highlight');
    	
    	// Valida preenchimento da funcionalidade
    	if (seeoid.val() == "") {
    		seeoid.addClass('highlight');
    		isValid = false;
    	}
    	
    	if(jQuery('#editavel').val()=='t'){ 	
    		
	    	var currentLength = parseInt(jQuery('#seecorpo_ifr')
						    			.contents()
						    			.find("body")
						    			.html()
						    			.replace(/(<([^>]+)>)/ig,'')
						    			.trim().length)

			if (currentLength < 7) {
				criaAlerta('O campo "Conteúdo" não pode estar vazio.');
	    		isValid = false;
	    	}
	    	
	    	if (currentLength > 3000) {
	    		criaAlerta('O campo "Conteúdo" excedeu o limite máximo de 3000 caracteres.');
	    		return false;
	    	}	    	
    	}
    	
    	if (!isValid) {
    		criaAlerta('Existem campos obrigatórios não preenchidos.');
    		e.preventDefault();
    		return false;
    	}
    	
    	jQuery('#editar_layout_emails').submit();
    	
    });
    
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
    
	jQuery("body").delegate('#seeoid', 'change', function(){		
		var seeoid = jQuery(this).val();
    	removeAlerta();
		jQuery('#seecorpoo_edit').hide();
		jQuery('#seecorpoo_readonly').hide();		
		jQuery("#seecorpo1").removeAttr('onLoad');
		jQuery.fn.pesquisarLayout(true, seeoid, 't');
	});
	
	jQuery.fn.pesquisarLayout(true, jQuery('#seeoid').val(), 'f');
});


/**
 * Pesquisar conteúdo do e-mail
 * @param async - asincrono - true, false.
 * @param seeoid - id do layout a ser carregado
 * @param reset - ('t', 'f') - desconsidera edicao anterior em sessao e carrega o layout original * 
 */
jQuery.fn.pesquisarLayout = function(async, seeoid, reset) {
	
	if(seeoid==""){
		jQuery('#loading').html('<center><img src="images/loading.gif" alt="" /></center>').show();
		jQuery('#bt_enviar').attr('disabled', 'disabled');
		jQuery('#editavel').val('f');
		jQuery('#seecorpoo_edit').hide();	
		jQuery("#seecorpo1").attr('src', 'send_layout_emails.php?acao=emailOcorrenciaHTML&blank=t' );
		jQuery('#seecorpo_ifr').contents().find("body").html("");					
		
		alert('Nenhum layout foi encontrado!');
		jQuery('#loading').html('').hide();
		jQuery('#seecorpoo_readonly').show();
		return;
	}
	
	
	jQuery('#acao').val('emailOcorrenciaHTML');
	jQuery.ajax({
		async: async,
		url: 'send_layout_emails.php',
		type: 'post',
		data: jQuery('#editar_layout_emails').serialize()+'&ajax=true&acao=emailOcorrenciaHTML&reset='+reset,
        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
		beforeSend: function(){
			jQuery('#bt_enviar').attr('disabled', 'disabled');
			removeAlerta();
			jQuery('#loading').html('<center><img src="images/loading.gif" alt="" /></center>').show();
		},
		success: function(data){
			try{	
				var resultado = jQuery.parseJSON(data);
				
				if(resultado.status="success"){
					if(resultado.message.editavel=='t'){
						$('#conteudoLayoutEmail').html(resultado.message.seecorpo);
						$('#seecorpo').val(resultado.message.seecorpo);
						
						jQuery('#editavel').val('f');
						jQuery('#seecorpoo_edit').show();
						jQuery('#seecorpoo_readonly').hide();
						jQuery('#loading').html('').hide();
						if(seeoid!='') jQuery('#bt_enviar').removeAttr('disabled');
					}else{	
						jQuery('#loading').html('<center><img src="images/loading.gif" alt="" /></center>').show();
						jQuery("#seecorpo1").attr('src', 'send_layout_emails.php?acao=emailOcorrenciaHTML&seeoid='+seeoid );
						jQuery('#editavel').val('f');					
						jQuery("#seecorpo1").load(function(){ 
							jQuery('#loading').html('').hide();
							jQuery('#seecorpoo_readonly').show()
							if(seeoid!='') jQuery('#bt_enviar').removeAttr('disabled');
						});
					}

					return true;
				}
				criaAlerta(resultado.message);
				if(resultado.redirect != "") window.location.href = resultado.redirect;
			}catch(e){
				try{	
					//popula o editor
					//console.info(data);
					jQuery('#editavel').val('t');					
				}catch(e){			
					criaAlerta('Erro no processamento dos dados.');
				}
			}			
			jQuery('#loading').fadeOut().html('');				
		},
		erro: function(data) {
			alert('Nenhum layout cadastrado.');
			return false;
		}
	});	
}