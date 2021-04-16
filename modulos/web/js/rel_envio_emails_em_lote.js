jQuery(document).ready(function(){
    
    /**
	 * Mensagens de retorno
	 */
	MSG_DATAINI_OBRIGATORIA = "Data inicial não informada.";
	MSG_DATAINI_INVALIDA 	= "Data inicial informada não é válida.";
	MSG_DATAFIM_OBRIGATORIA = "Data final não informada.";
	MSG_DATAFIM_INVALIDA 	= "Data final informada não é válida.";
	MSG_DATAFINAL_MENOR		= "A data inicial deve ser menor que a data final.";
    MSG_PERIODO_OBRIGATORIO = "Período não informado.";
    
    jQuery('button[name="pesquisar"]').click(function() {
		pesquisar();
	});
    
    jQuery('#img_dt_ini').click(function() {
		displayCalendar(document.forms[0].dt_ini,'dd/mm/yyyy',this);
	});
	
	jQuery('#img_dt_fim').click(function() {
		displayCalendar(document.forms[0].dt_fim,'dd/mm/yyyy',this);
	});
    
    /**
	 * @tag input(type="text", name="dt_ini")
	 * @Event OnBlur
	 */
	jQuery('#dt_ini').blur(function(){
		
		if (jQuery(this).val() != '' && revalidar(this,'@@/@@/@@@@','data')) {
			jQuery(this).removeClass("inputError");
		}
	});
	
	/**
	 * @tag input(type="text", name="dt_ini")
	 * @Event OnKeypress
	 */
	jQuery('#dt_ini').keypress(function(){
		formatar(this, '@@/@@/@@@@');
	});
	
	/**
	 * @tag input(type="text", name="dt_fim")
	 * @Event OnKeypress
	 */
	jQuery('#dt_fim').keypress(function(){
		formatar(this, '@@/@@/@@@@');
	});
	
	/**
	 * @tag input(type="text", name="dt_fim")
	 * @Event OnFocus
	 */
	jQuery('#dt_ini').focus(function() {
		jQuery('#dt_fim').trigger('blur');
	});
	
	
	/**
	 * @tag input(type="text", name="dt_ini")
	 * @Event OnBlur
	 */
	jQuery('#dt_fim').blur(function(){		
		if (jQuery(this).val() != '' && revalidar(this,'@@/@@/@@@@','data')) {
			jQuery(this).removeClass("inputError");
		}
	});
	
	/**
	 * @tag input(type="text", name="dt_fim")
	 * @Event OnFocus
	 */
	jQuery('#dt_fim').focus(function() {
		jQuery('#dt_ini').trigger('blur');
	});
	
    jQuery('input[name="placa"]').keyup(function(){
		jQuery(this).val((jQuery(this).val()).toUpperCase());
	});
    
    jQuery('#dt_ini').focus();
    
});

function pesquisar() {
			
    var dataFim = jQuery("#dt_fim").val();
    var dataIni = jQuery("#dt_ini").val();
    var post = null;    
    
    jQuery('body input').removeClass('inputError');
    jQuery('body input[type="text"]').css('background', '#FFFFFF');
    
    formata_dt(document.getElementById('dt_ini'));
    formata_dt(document.getElementById('dt_fim'));

    /**
     * Valida período
     */
    if (dataIni.length == 0 && dataFim.length == 0) {

        removeAlerta();
        criaAlerta(MSG_PERIODO_OBRIGATORIO); 

        jQuery("#dt_ini").addClass("inputError");
        jQuery("#dt_fim").addClass("inputError");
        return false;
    } 
    else if (dataIni.length == 0) {

        removeAlerta();
        criaAlerta(MSG_DATAINI_OBRIGATORIA); 
        jQuery("#dt_ini").addClass("inputError");
        return false;
    } 
    else if (dataFim.length == 0) {

        removeAlerta();
        criaAlerta(MSG_DATAFIM_OBRIGATORIA); 
        jQuery("#dt_fim").addClass("inputError");
        return false;
    } 

    if (diferencaEntreDatas(jQuery("#dt_fim").val(), jQuery("#dt_ini").val()) < 0){

        removeAlerta();
        criaAlerta(MSG_DATAFINAL_MENOR); 
        jQuery("#dt_fim").addClass("inputError");
        return false;	
    }

    jQuery('input[name="acao"]').val('pesquisar');
    post = jQuery('form[name="filtro"]').serialize();

    removeAlerta();
    
    jQuery('#motivo_progress').show();
    jQuery.ajax({
          url: 'rel_envio_emails_em_lote.php',
          dataType: 'json',
          type: 'post',
          data: post,
          beforeSend: function() {
              removeAlerta();
              jQuery('#resultado_relatorio_container').html('');
              jQuery('#resultado_relatorio').fadeOut('fast', function() {
                  jQuery('#resultado_progress').fadeIn('slow');
              });
          },
          success: function(data) {
              
              if(data.status != undefined && data.status == "errorlogin") {
                  criaAlerta("A sua sessão expirou, por favor faça login no sistema novamente.");
                  return false;
              }
              
              if (data.erro !== false) {                  
                  criaAlerta(data.retorno);
                  return false;
              }
              
              if (data !== null  && data.erro === false) {
                  if (data.codigo == 0) {
                      jQuery('#resultado_relatorio_container').html('<a href="'+data.retorno+'"><img src="images/icones/t3/caixa2.jpg" width="36px" alt="Baixar relatório" /><br />Relatório Envio de Emails em Lote</a>');
                  }
                  else {
                      jQuery('#resultado_relatorio_container').html('<b>Nenhum resultado encontrado.</b>');
                  }
              }              
              
          },
          complete: function() {
              jQuery('#resultado_progress').fadeOut('fast', function() {
                  jQuery('#resultado_relatorio').fadeIn('slow');
              });
          }
        });
        
        return true;
}