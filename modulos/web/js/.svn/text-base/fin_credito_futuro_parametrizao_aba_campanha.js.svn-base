jQuery(document).ready(function(){
    
    
    
    jQuery("#retornar").click(function(){
        window.location = "./fin_credito_futuro_parametrizacao_campanha.php";
    });
    
    jQuery('#confirmar').click(function(){
          jQuery('#form').submit(); 
       });
    
    jQuery("#retornar").click(function(){
        window.location = "./fin_credito_futuro_parametrizacao_campanha.php?acao=pesquisar";
    });
    
    jQuery('#cfcpdt_inicio_vigencia').periodo('#cfcpdt_fim_vigencia');
    jQuery(".desconto_percentual").maskMoney({symbol:'', thousands:'.', decimal:',', symbolStay: false, precision:2, defaultZero: false});
    jQuery(".desconto_valor").maskMoney({symbol:'', thousands:'.', decimal:',', symbolStay: false,defaultZero: false});
    
    jQuery("#cfcpqtde_parcelas").mask("9?99",{placeholder:''});
    
    jQuery('#pesquisar').click(function(){
        jQuery('#form').submit();
    });
    
    jQuery('#novo').click(function(){
        window.location = "?acao=cadastro";
    });
    
    jQuery('.tipo_desconto').change(function(){
        jQuery('.tipo_desconto_box').each(function(){
           jQuery(this).css('display','none');
           jQuery('.tipo_desconto_box input').attr('value','');
           jQuery('.tipo_desconto_box input').css('tabindex','');
        });
        if (jQuery(this).attr('id') == 'cfcptipo_desconto_1') {
            jQuery('#tipo_desconto_percentual').fadeIn();
            jQuery('#tipo_desconto_percentual input').attr('tabindex','8');
        }else if (jQuery(this).attr('id') == 'cfcptipo_desconto_2') {
            jQuery('#tipo_desconto_valor').fadeIn();
            jQuery('#tipo_desconto_valor input').attr('tabindex','8');
        }
    }); 
    
    jQuery('.tipo_desconto_cadastro').change(function(){
        jQuery('.tipo_desconto_box').each(function(){
           $(this).css('display','none');
           $(this).attr('tabindex','');
        });
        if (jQuery(this).attr('id') == 'cfcptipo_desconto_1') {
            jQuery('#cfcpdescont_percentual').fadeIn();
            jQuery('#cfcpdescont_percentual input').attr('tabindex','7');
        }else if (jQuery(this).attr('id') == 'cfcptipo_desconto_2') {
            jQuery('#cfcpdescont_valor').fadeIn();
            jQuery('#cfcpdescont_valor input').attr('tabindex','7');
        }
    });
    
     jQuery('.forma_aplicacao').change(function(){
           jQuery('#div_cfcpqtde_parcelas').css('display','none');
           jQuery('#div_cfcpqtde_parcelas label').css('display','none');
        if (jQuery(this).attr('id') != 'cfcpaplicacao_1') {
            jQuery('#div_cfcpqtde_parcelas').css('display','block');
            jQuery('#div_cfcpqtde_parcelas label').css('display','block');
        }
    });
    
    if (jQuery('#cfcptipo_desconto_1').is(':checked')){
        jQuery('#cfcpdescont_percentual').fadeIn();
        jQuery('#cfcpdescont_percentual input').attr('tabindex','7');
        
        
        jQuery('#tipo_desconto_percentual').fadeIn();
        jQuery('#tipo_desconto_percentual input').attr('tabindex','8');
    }
    
    if (jQuery('#cfcptipo_desconto_2').is(':checked')){
        jQuery('#cfcpdescont_valor').fadeIn();
        jQuery('#cfcpdescont_valor input').attr('tabindex','7');
        
        jQuery('#tipo_desconto_valor').fadeIn();
        jQuery('#tipo_desconto_valor input').attr('tabindex','8');
    }
    
    if (jQuery('#cfcpaplicacao_2').is(':checked')){
        jQuery('#div_cfcpqtde_parcelas').fadeIn();
        jQuery('#div_cfcpqtde_parcelas input').attr('tabindex','10');
    }
    
    
    corrigeMaskMoney('cfcpdesconto_valor','moeda');
    corrigeMaskMoney('cfcpdesconto_percentual','porcento');
    corrigeMaskMoney('cfcpdesconto_valor_de','moeda');
    corrigeMaskMoney('cfcpdesconto_valor_ate','moeda');
    corrigeMaskMoney('cfcpdesconto_percentual_de','porcento');
    corrigeMaskMoney('cfcpdesconto_percentual_ate','porcento');
    
    
    function corrigeMaskMoney(id,tipo){
        
        jQuery("#"+id).keyup(function() {
            var str= jQuery("#"+id).val();
            var n=str.replace(/\'/g,'');
            n = n.replace(/\"/g,'');
            n = n.replace(/\%/g,'');
            n = n.replace(/[a-zA-Z]/g,'');
            n = n.replace(/\(/g,'');
            n = n.replace(/\)/g,'');
            n = n.replace(/\]/g,'');
            n = n.replace(/\[/g,'');
            n = n.replace(/\}/g,'');
            n = n.replace(/\{/g,'');
            n = n.replace(/\=/g,'');
            n = n.replace(/\-/g,'');
            jQuery("#"+id).attr('value',n);
        });
        
        jQuery("#"+id).on('paste',function() { 
            
            
            setTimeout(function(){
            var str= jQuery.trim(jQuery("#"+id).val());
            var n = str;
            
            if (tipo =='moeda'){
               // var objER  = new RegExp("[0-9]{2}.[0-9]{3},[0-9]{2}");
                var verifica = str.replace(".","");
                verifica = str.replace(",","");
                
                if (IsNumeric(verifica) && verifica.length >= 5) {
                     verifica2 = verifica.split("");
                    str = verifica2[0] + verifica2[1]  + verifica2[2] + ',' + verifica2[3]  + verifica2[4];
                }else if (IsNumeric(verifica) && verifica.length == 4) {
                     verifica2 = verifica.split("");
                    str = verifica2[0] + verifica2[1] + ',' + verifica2[2]  + verifica2[3];
                }else if (IsNumeric(verifica) && verifica.length == 3) {
                     verifica2 = verifica.split("");
                    str = verifica2[0] + ',' + verifica2[1]  + verifica2[2];
                }else if (IsNumeric(verifica) && verifica.length == 2) {
                     verifica2 = verifica.split("");
                     str = '0' + ',' + verifica2[0]  + verifica2[1];
                }else if (IsNumeric(verifica) && verifica.length == 1) {
                     verifica2 = verifica.split("");
                     str = '00,' + '0' + verifica2[0];
                }
                
                if (!IsNumeric(verifica)) {
                    n = '';
                }else{
                    n = str;
                }
                
            } else if (tipo =='porcento') {
                //var objER1  = new RegExp("[0-9]{2}.[0-9]{1}");
                
                if (str.length > 1) {
                    var stringReserva = parseFloat(str);
                }else{
                    var stringReserva = parseFloat(str);
                }
                stringReserva = String(stringReserva);
                
                var verifica = stringReserva.replace(",","");
                
                if (IsNumeric(verifica) && verifica.length >= 5){
                    verifica2 = verifica.split("");
                    str = verifica2[0] + verifica2[1] + verifica2[2] + ',' + verifica2[3] + verifica2[4];
                }else if (IsNumeric(verifica) && verifica.length == 4) {
                    str = verifica2[0] + verifica2[1] + ',' + verifica2[2] + verifica2[3];
                }else if (IsNumeric(verifica) && verifica.length == 3){
                    verifica2 = verifica.split("");
                    str = verifica2[0] + verifica2[1] + ',' + verifica2[2];
                }else if (IsNumeric(verifica) && verifica.length== 2) {
                    verifica2 = verifica.split("");
                    str = verifica2[0] + ',' + verifica2[1];
                }else if (IsNumeric(verifica) && verifica.length== 1) {
                    verifica2 = verifica.split("");
                    str = verifica2[0] + ',0';
                }
                var countString = str.split(',');
                
               if (!IsNumeric(verifica) && (countString.length == 1 || !IsNumeric(countString[0]) || !IsNumeric(countString[1]) )) {
                    n = '';
                }else{
                    n = str;
                }
            }
            
            jQuery("#"+id).attr('value',n);
            },50);

        });
    }
    
    function IsNumeric(input){
         var RE = /^-{0,1}\d*\.{0,1}\d+$/;
        return (RE.test(input));
    }
    
});

jQuery(document).ready(function(){
        
        
        
        if (!existeParametroEmail){
            jQuery('input,select,radio,textarea,button ').attr('disabled','disabled');
            jQuery('img.ui-datepicker-trigger').click(function(){
                jQuery('#ui-datepicker-div').remove();
            })
        }
        
        var dados = [];
        var mensagem = [];
        
        
       jQuery('#bt_pesquisar').click(function(event){
		event.preventDefault();
                jQuery.fn.limpaMensagens();
                var obrigatorios = true;
                var diferenteZero = true;
                var diferenteZeroPorcentagem = true;
                var menorCem = true
                dados = [];
                mensagem = [];
            
            //verificação do periodo de vigencia
            var dataInicio = jQuery.trim(jQuery('#cfcpdt_inicio_vigencia').val());
            var dataFinal = jQuery.trim(jQuery('#cfcpdt_fim_vigencia').val());
            
            if (dataInicio == ""){
                //verifico se é vazio
                setErrors('cfcpdt_inicio_vigencia','Campo obrigatório.');
                obrigatorios = false;

            }
            
             if (dataFinal == ""){
                //verifico se é vazio
                setErrors('cfcpdt_fim_vigencia','Campo obrigatório.');
                obrigatorios = false;

            }
            
            
            //console.log(new Date(dataInicio).getTime());
            //console.log(new Date(dataFinal).getTime());
            
            /*if (dataInicio != "" && dataFinal != '' && (new Date(dataInicio).getTime() > new Date(dataFinal).getTime())) {
                //verifico se a data de inicio é maior que a data final
                setErrors('cfcpdt_inicio_vigencia','');
                setErrors('cfcpdt_fim_vigencia','');
                setMsg('A data inicial não pode ser maior que a data final.');
            }*/
            
            //verificação de tipo de descontos
            
            //verifico se o percentual esta marcado
            if (jQuery('#cfcptipo_desconto_1').is(':checked')){
                
                //verifico se o campo percentual "de" e "ate" estão preenchidos
                var percentualDe = jQuery.trim(jQuery('#cfcpdesconto_percentual_de').val()).replace(".","").replace(",",".");
                var percentualAte = jQuery.trim(jQuery('#cfcpdesconto_percentual_ate').val()).replace(".","").replace(",",".");

                if (percentualDe == "") {
                    setErrors('cfcpdesconto_percentual_de','Campo obrigatório.');
                    obrigatorios = false;
                    
                } else if (percentualDe == 0) {
                     setErrors('cfcpdesconto_percentual_de','');
                     diferenteZeroPorcentagem = false;
                    
                } else if (percentualDe >= 100){
                    setErrors('cfcpdesconto_percentual_de','');
                    menorCem = false;
                }
                
                
                 if (percentualAte == "") {
                    setErrors('cfcpdesconto_percentual_ate','Campo obrigatório.');
                    obrigatorios = false;
                    
                } else if (percentualAte == 0) {
                     setErrors('cfcpdesconto_percentual_ate','');
                     diferenteZeroPorcentagem = false;
                    
                } else if (percentualAte >= 100){
                    setErrors('cfcpdesconto_percentual_ate','');
                    menorCem = false;
                }
                
                if (percentualAte != '' && percentualDe != '' && parseFloat(percentualAte) <= parseFloat(percentualDe)) {
                     setErrors('cfcpdesconto_percentual_ate','');
                     setMsg('A informação "De" não pode ser maior que a informação "Até".');
                }
                
            }
            
            if (jQuery('#cfcptipo_desconto_2').is(':checked')) {
                 //verifico se o valor esta marcado
                 
                //verifico se o campo percentual "de" e "ate" estão preenchidos
                var valorDe = jQuery.trim(jQuery('#cfcpdesconto_valor_de').val()).replace(".","").replace(",",".");
                var ValorAte = jQuery.trim(jQuery('#cfcpdesconto_valor_ate').val()).replace(".","").replace(",",".");
                
                
                if (valorDe == "") {
                    setErrors('cfcpdesconto_valor_de','Campo obrigatório.');
                    obrigatorios = false;
                } else if (valorDe == 0) {
                    setErrors('cfcpdesconto_valor_de','');
                    diferenteZero = false;
                }
                
                if (ValorAte == "") {
                    setErrors('cfcpdesconto_valor_ate','Campo obrigatório.');
                    obrigatorios = false;
                } else if (ValorAte == 0) {
                    setErrors('cfcpdesconto_valor_ate','');
                    diferenteZero = false;
                }
                
                
                if (valorDe !='' && ValorAte != '' && parseFloat(ValorAte) <= parseFloat(valorDe)) {
                    setErrors('cfcpdesconto_valor_ate','');
                    setMsg('A informação "De" não pode ser maior que a informação "Até".');
                }
                
            }
            
            if (!obrigatorios) {
                setMsg('Existem campos obrigatórios não preenchidos.');
            }
            
            if (!diferenteZero) {
                setMsg('O valor de desconto deve ser maior que zero.');
            }
            
            if (!diferenteZeroPorcentagem) {
                setMsg('O percentual de desconto deve ser maior que zero.');
            }
            
            if (!menorCem) {
                setMsg('O percentual de desconto não pode ser igual ou maior que 100%.');
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

            } else {
                    //envia formularios
                    jQuery('#form').submit();
            }
                
            showFormErros(dados);
            
        });
        
        
        function setErrors (id,mensagem) {
            dados.push({
                campo: id,
                mensagem: mensagem	
            });
        }
        
        function verificaExistenciaErro(id){
            jQuery(dados).each(function(index,value){

            });
        }
        
        function setMsg (string_mensagem) {
            mensagem.push({
                mensagem: string_mensagem
            });
        }
        
       
        
    });
    
     jQuery.fn.limpaMensagens = function() {
            jQuery('.mensagem').not('.info').hideMessage();
            jQuery('.mensagem').not('.info').remove();
            jQuery(".erro").not('.mensagem').removeClass("erro");
            resetFormErros();
        }

