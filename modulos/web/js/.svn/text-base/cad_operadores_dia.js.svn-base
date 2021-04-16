jQuery(document).ready(function() {

    jQuery('#opddt_inivigencia').periodo('#opddt_fimvigencia');
    
    var dados = [];
    var mensagem = [];

    jQuery("#voltar").click(function(event){
        window.location="cad_operadora_dia.php?acao=pesquisar&sessao"; 
    });

    jQuery("#novo").click(function(event){
        window.location="cad_operadora_dia.php?acao=cadastrar"; 
    });
    
    jQuery('.deletar').click(function(event){
        event.preventDefault();
        if (confirm('Tem certeza que deseja excluir o registro?')){
            window.location= jQuery(this).attr('href'); 
            //console.log(jQuery(this).attr('href'));
        }
    });

    jQuery("#botao_pesquisar, #confirmar").click(function(event){
        event.preventDefault();
        jQuery.fn.limpaMensagens();
        dados = [];
        mensagem = [];
        
        var obrigatorio = true;
        var menorPeriodo = true;
        var validarObrigatoriedade = (jQuery(this).attr('id') == 'confirmar');
        
        
          
        var hoje = new Date();  
        dia = hoje.getDate(); 
        mes = hoje.getMonth();  
        ano = hoje.getFullYear();
        hoje = dia+ '/'+ mes + '/' + ano; 
        hoje = hoje.split("/");
        
        hoje = new Date(hoje[2], hoje[1], hoje[0]);
        
        //validar periodo
        if (jQuery.trim(jQuery('#opddt_inivigencia').val()) == '' && validarObrigatoriedade ) {
            obrigatorio = false;
            setErrors('opddt_inivigencia','Campo obrigatório.');
        } else {
            var dataIni = jQuery('#opddt_inivigencia').val().split("/");
            dataIni = new Date(dataIni[2], dataIni[1]-1, dataIni[0]);
            
            
            //se não for a tela de pesquisa, excuta a validação abaixo
            if (jQuery.trim(jQuery('#acao').val()) != 'pesquisar') {
                if (dataIni < hoje) {
                    setErrors('opddt_inivigencia');
                    menorPeriodo = false;
                }
            }
           
        }
        
        //se for preenchido qualque data o outro se torna obrigatório
         if (jQuery.trim(jQuery('#acao').val()) == 'pesquisar') {
           
           if (jQuery.trim(jQuery('#opddt_inivigencia').val()) != '' && jQuery.trim(jQuery('#opddt_fimvigencia').val()) == ''){
               setErrors('opddt_fimvigencia');
               setMsg('Informar início e fim da vigência.');
           }
           
            if (jQuery.trim(jQuery('#opddt_fimvigencia').val()) != '' && jQuery.trim(jQuery('#opddt_inivigencia').val()) == ''){
               setErrors('opddt_inivigencia');
               setMsg('Informar início e fim da vigência.');
           }
                   
         }
        
        
        
        
        if (jQuery.trim(jQuery('#opddt_fimvigencia').val()) == '' && validarObrigatoriedade) {
            obrigatorio = false;
            setErrors('opddt_fimvigencia','Campo obrigatório.');
        } else {
            var dataFim = jQuery('#opddt_fimvigencia').val().split("/");
            dataFim = new Date(dataFim[2], dataFim[1]-1, dataFim[0]);
            
           // if (hoje > dataFim) {
               // menorPeriodo = false;
               // setErrors('opddt_fimvigencia');
            //}
        }
        
        if (jQuery.trim(jQuery("#opdopeoid").val()) == '' && validarObrigatoriedade) {
            obrigatorio = false;
            setErrors('opdopeoid','Campo obrigatório.');
        }
         

        if (!obrigatorio) {
            setMsg('Existem campos obrigatórios não preenchidos.');
        }
        
        if (!menorPeriodo) {
            setMsg('Data início da vigência menor que a data atual');
        }
        
        //verrifico se existe erros
            if (dados.length > 0) {
                    showFormErros(dados);
                    if (mensagem.length > 0) {
                            //verrifico se existe mensagem e mostro
                            jQuery.each(mensagem, function(index, value) {
                                            jQuery('#mensagens').append('<div class="mensagem alerta">' + value.mensagem + '</div>');
                                    });

                            }

            }else{
            jQuery('#form_exemplo').submit();
        }
               
        
    });
    
    function setMsg (string_mensagem) {
            mensagem.push({
                mensagem: string_mensagem
            });
        }
    
    
    function setErrors (id,mensagem) {
            dados.push({
                campo: id,
                mensagem: mensagem	
            });
        }

});

jQuery.fn.limpaMensagens = function() {
            jQuery('.mensagem').not('.info').hideMessage();
            jQuery('.mensagem').not('.info').remove();
            jQuery(".erro").not('.mensagem').removeClass("erro");
            resetFormErros();
        }