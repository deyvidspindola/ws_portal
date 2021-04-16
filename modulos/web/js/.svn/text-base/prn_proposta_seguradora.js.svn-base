function busca_dados_instalacao()
{
	var nome_instalacao = document.getElementById('psctnome_instalacao').value;
	if(nome_instalacao == '')
	{
		alert('DADOS DE CONTATO\nCONTATO DEVE SER PREENCHIDO!');
		document.getElementById('psctnome_instalacao').focus();
		return false;
	}

	var cpf_instalacao = document.getElementById('psctcpf_instalacao').value;
	if(cpf_instalacao == '')
	{
		alert('DADOS DE CONTATO\nCPF DEVE SER PREENCHIDO!');
		document.getElementById('psctcpf_instalacao').focus();
		return false;
	}	

	var psctddd_residencial_instalacao = document.getElementById('psctddd_residencial_instalacao').value;	
	var psctfone_residencial_instalacao = document.getElementById('psctfone_residencial_instalacao').value;

	var psctddd_comercial_instalacao = document.getElementById('psctddd_comercial_instalacao').value;
	var psctfone_comercial_instalacao = document.getElementById('psctfone_comercial_instalacao').value;

	var psctddd_celular_instalacao = document.getElementById('psctddd_celular_instalacao').value;
	var psctfone_celular_instalacao = document.getElementById('psctfone_celular_instalacao').value;		
	
	if(psctfone_residencial_instalacao == '' && psctfone_comercial_instalacao == '' && psctfone_celular_instalacao == '')
	{
		alert('DADOS DE CONTATO\nALGUM TELEFONE PARA CONTATO DEVE SER PREENCHIDO!');
		return false;		
	}	 
	
	if(psctfone_residencial_instalacao != '')
	{
		if(psctddd_residencial_instalacao == '')
		{
			alert('DADOS DE CONTATO\nDDD RESIDENCIAL DEVE SER PREENCHIDO!');
			document.getElementById('psctddd_residencial_instalacao').focus();
			return false;
		}	

		if(psctddd_residencial_instalacao.length < 2)
		{
			alert('DADOS DE CONTATO\nDDD RESIDENCIAL DEVE TER 2 DIGITOS!');
			document.getElementById('psctddd_residencial_instalacao').focus();
			return false;
		}	

	}	
	
	if(psctfone_comercial_instalacao != '')
	{
		if(psctddd_comercial_instalacao == '')
		{
			alert('DADOS DE CONTATO\nDDD COMERCIAL DEVE SER PREENCHIDO!');
			document.getElementById('psctddd_comercial_instalacao').focus();
			return false;
		}	

		if(psctddd_comercial_instalacao.length < 2)
		{
			alert('DADOS DE CONTATO\nDDD COMERCIAL DEVE TER 2 DIGITOS!');
			document.getElementById('psctddd_comercial_instalacao').focus();
			return false;
		}	

	}	
	
	if(psctfone_celular_instalacao != '')
	{
		if(psctddd_celular_instalacao == '')
		{
			alert('DADOS DE CONTATO\nDDD CELULAR DEVE SER PREENCHIDO!');
			document.getElementById('psctddd_celular_instalacao').focus();
			return false;
		}	

		if(psctddd_celular_instalacao.length < 2)
		{
			alert('DADOS DE CONTATO\nDDD CELULAR DEVE TER 2 DIGITOS!');
			document.getElementById('psctddd_celular_instalacao').focus();
			return false;
		}	

	}	
	ajax_busca_dados_instalacao();	
}

function ValidaDataPC(campo)
{
    
    seleciona=campo;
    campo = campo.value;
    len = campo.length;
     
    if(campo!="" && len<10){
        alert("Data inválida! Por favor digite a data correta!");
        seleciona.select();
        return false;
    }
    
    if(campo!="" && len==10 ){
        dia = campo.substring(0,2);
        mes = campo.substring(3,5);
        ano = campo.substring(6,10);
        
        if((mes < 1) || (mes > 12)){
            alert("\nMeses, neste campo, devem estar entre valores 01 e 12");
            seleciona.select();
            return false;
        }
        
        if((mes==1) || (mes==3) || (mes==5) || (mes==7) || (mes==8) || (mes==10) || (mes ==12)){
            if((dia < 01) || (dia > 31)){
                alert("\nO dia, neste campo, deve estar entre 01 e 31");
                seleciona.select();
                return false;
            }
        }
        
        if((mes==4) || (mes==6) || (mes==9) || (mes==11)){
            if((dia < 01) || (dia > 30)){
                alert("\nO dia, neste campo, deve estar entre 01 e 30");
                seleciona.select();
                return false;
            }
        }
        
        if(mes== 2){
            if((ano % 4) == 0){
                if((dia < 1) || (dia > 29)){
                    alert("\nO dia, neste campo, deve estar entre 01 e 29");
                    seleciona.select();
                    return false;
                }
            }else{
                if((dia < 1) || (dia > 28)){
                    alert("\nO dia, neste campo, deve estar entre 01 e 28");
                    seleciona.select();
                    return false;
                }
            }
        }
        
        if(ano.length != 4){
            alert("\nO ano, neste campo, deve possuir 4 dígitos.\nExemplo: 2000");
            seleciona.select();
            return false;
        }
        
        if((ano < 1800) || (ano >=3000)){
            alert("\Ano Inválido.");
            seleciona.select();
            return false;
        }
        
    }
    
    return true;
    
}

function verificaCpfCnpj()
{
	var prpstipo_pessoa = document.getElementById('prpstipo_pessoa').value;
	if(prpstipo_pessoa == 'F')
	{
		document.getElementById('div_cpf').style.display='inline';
		document.getElementById('div_cnpj').style.display='none';
		
		document.getElementById('prpsscnpj_cpf_cnpj').value='';
		
		document.getElementById('prpsscnpj_cpf_cnpj').Name='prpsscnpj_cnpj';	
		document.getElementById('prpsscnpj_cpf_cpf').Name='prpsscnpj_cpf';	
		
		document.getElementById('div_rg').style.display='inline';	
		document.getElementById('div_i_est').style.display='none';
		
		document.getElementById('div_nascimento').style.display='inline';	
		document.getElementById('div_fundacao').style.display='none';
		
		document.getElementById('div_sexo').style.display='inline';	
		
		//alert(document.getElementById('prpsscnpj_cpf_cpf').Name);
		//alert(document.getElementById('prpsscnpj_cpf_cnpj').Name);
	}
	else if(prpstipo_pessoa == 'J')
	{
		document.getElementById('div_cpf').style.display='none';
		document.getElementById('div_cnpj').style.display='inline';
		
		document.getElementById('prpsscnpj_cpf_cpf').value='';			
		document.getElementById('prpsscnpj_cpf_cnpj').Name='prpsscnpj_cpf';		
		document.getElementById('prpsscnpj_cpf_cpf').Name='prpsscnpj_cpf_cpf';		

		document.getElementById('div_rg').style.display='none';
		document.getElementById('div_i_est').style.display='inline';
		
		document.getElementById('div_nascimento').style.display='none';
		document.getElementById('div_fundacao').style.display='inline';		
				
		document.getElementById('div_sexo').style.display='none';				
	}
		
}
 
function validar_prpsinicio_vigencia()
{
	var prpsinicio_vigencia = document.getElementById('prpsinicio_vigencia').value;
	var prpsfim_vigencia = document.getElementById('prpsfim_vigencia').value;	
	
	var inicio = prpsinicio_vigencia.split('/');
	var fim = prpsfim_vigencia.split('/');	

	if(prpsinicio_vigencia != '' && prpsfim_vigencia !='')
	{
	
			if(fim[2]+''+fim[1]+''+fim[0] < inicio[2]+''+inicio[1]+''+inicio[0])
			{
				alert("A Data Final nao poder ser inferior a Data Inicial!");
				document.getElementById('prpsinicio_vigencia').value = '';
				document.getElementById('prpsinicio_vigencia').focus();
				return false;
			}
	}		
}
 
function Mascara_Hora(Hora)
{
	var hora01 = '';
	hora01 = hora01 + Hora;
	if(hora01.length == 2)
	{
		hora01 = hora01 + ':';
		document.getElementById('prpshhora').value = hora01;
	}
	if(hora01.length == 5)
	{
		Verifica_Hora();
	}
}  
 
function Verifica_Hora()
{  
   hrs = (document.getElementById('prpshhora').value.substring(0,2)); 
   min = (document.getElementById('prpshhora').value.substring(3,5)); 
   estado = "";  

   if ((hrs < 00 ) || (hrs > 23) || ( min < 00) ||( min > 59)){  
      estado = "errada";  
   }  
  
   if(document.getElementById('prpshhora').value.substring(0,2) == "") 
   {  
      estado = "errada";  
   }  
   if (estado == "errada") 
   {  
      alert("Hora invalida!");  
      document.getElementById('prpshhora').focus();  
      document.getElementById('prpshhora').value='';
   }  
}    
 
function abre_historico(prpsoid)
{
    janela=window.open ("poupup_proposta_seguradora.php?prpsoid="+prpsoid,"view","status,scrollbars=yes,menubar=no,toolbar=no,resizable=yes,top=50,bottom=50,width=755,height=415");
    janela.moveTo(screen.width/2-300,screen.height/2-200);
}

function retornar()
{
	
	location='prn_proposta_seguradora.php';
	
}

function incluir_contrato_seguradora()
{
	document.form.acao.value='incluir';
	
    document.form.submit();
}

function onlyNumbers(input) {
	var reg = new RegExp("[0-9]");
	var ultimo = input.value.substring(input.value.length-1,input.value.length);
	if (!reg.test(ultimo)) input.value=input.value.substring(0,input.value.length-1);
}

function verificaProposta(prpsproposta,prpstpcoid) {
	var retorno = false;
	//alert('teste');
	jQuery.ajax({
		url: 'prn_proposta_seguradora.php',
		type: 'post',
		data: {prpsproposta_ajax:prpsproposta,prpstpcoid_ajax:prpstpcoid},
		beforeSend: function(){
			jQuery('#loading1').html('<div style="width: 100%; text-align:center;"><img src="images/progress.gif" alt="" /></div>');
		},
		success: function(data) {
			//console.log(data);
			jQuery('#loading1').html('');
			var resultado = jQuery.parseJSON(data);
			if (resultado.existeProposta==1) {
				alert("A proposta já existe, não é possível fazer a inclusão!");
				$("input").attr('disabled','disabled');
				$("textarea").attr('disabled','disabled');
				$("select").attr('disabled','disabled');
				$("#prpsproposta").removeAttr('disabled');
				$("#prpstpcoid").removeAttr('disabled');
				$("#prpsproposta").val('');
				$("#prpsproposta").focus();
				retorno = false;
			}
			else {
				retorno = true;
				$("input").removeAttr('disabled');
				$("textarea").removeAttr('disabled');
				$("select").removeAttr('disabled');
			}
		}
	});
	
	return retorno;
}

function delQuarentena(connumero_quarentena,proposta)
{
	
	if (confirm("Deseja mesmo retirar o contrato de Quarentena?")) {
		jQuery.ajax({
			url: 'prn_proposta_seguradora.php',
			type: 'post',
			data: {connumero_quarentena_del_ajax:connumero_quarentena,proposta_ajax:proposta},
			beforeSend: function(){
				jQuery('#loading2').html('<div style="width: 100%; text-align:center;"><img src="images/progress.gif" alt="" /></div>');
			},
			success: function(data) {
				//console.log(data);
				jQuery('#loading2').html('');
				var resultado = jQuery.parseJSON(data);
				if (resultado.deletou==1) {
					$("#quarentena").html('');
					$("#del_quarentena").hide();
					$("#inc_quarentena").show();
					document.location = "?acao=editar&id="+$('#prpsoid').val();
				}
			}
		});
	}
	
}

function incQuarentena(connumero_quarentena,proposta,contrato_tipo)
{
	if (confirm("Deseja mesmo colocar este Contrato em Quarentena?")) {
		jQuery.ajax({
			url: 'prn_proposta_seguradora.php',
			type: 'post',
			data: {connumero_quarentena_inc_ajax:connumero_quarentena,proposta_ajax:proposta,contrato_tipo_ajax:contrato_tipo},
			beforeSend: function(){
				jQuery('#loading2_'+connumero_quarentena).html('<div style="width: 100%; text-align:center;"><img src="images/progress.gif" alt="" /></div>');
			},
			success: function(data) {
				//console.log(data);
				jQuery('#loading2_'+connumero_quarentena).html('');
				var resultado = jQuery.parseJSON(data);
				if (resultado.incluiu==1) {
					$("#quarentena_"+connumero_quarentena).html(resultado.data_quarentena);
					$("#del_quarentena_"+connumero_quarentena).show();
					$("#inc_quarentena_"+connumero_quarentena).hide();
				}
			}
		});
	}
}

function verificaCadastro()
{
	
	/* NUMERO DA PROPOSTA*/
	var prpsproposta = document.getElementById('prpsproposta').value;
	if(prpsproposta == '')
	{
		alert('CAMPO OBRIGATORIO\nO CAMPO NUMERO DA PROPOSTA DEVE SER PREENCHIDO!');
		document.getElementById('prpsproposta').focus();
		return false;
	}
	
	/* PLACA DO VEICULO*/
	var veiplaca = document.getElementById('veiplaca').value;
	if(veiplaca == '')
	{
		alert('CAMPO OBRIGATORIO\nO CAMPO PLACA DO VEíCULO DEVE SER PREENCHIDO!');
		document.getElementById('veiplaca').focus();
		return false;
	}
	
	/* CHASSI */
	var prpschassi = document.getElementById('prpschassi').value;
	if(prpschassi == '')
	{
		alert('CAMPO OBRIGATORIO\nO CAMPO CHASSI DO VEíCULO DEVE SER PREENCHIDO!');
		document.getElementById('prpschassi').focus();
		return false;
	}
	
	/* CAMPO STATUS*/
	var prpsprpssoid = document.getElementById('prpsprpssoid').value;
	if(prpsprpssoid == '')
	{
		alert('CAMPO OBRIGATORIO\nO CAMPO STATUS DEVE SER PREENCHIDO!');
		document.getElementById('prpsprpssoid').focus();
		return false;
	}	
	
	/* CAMPO DRM ?*/
	/*
	var prpsproposta = document.getElementById('prpsproposta').value;
	if(prpsproposta == '')
	{
		alert('CAMPO OBRIGATORIO\nO CAMPO NUMERO DA PROPOSTA DEVE SER PREENCHIDO!');
		document.getElementById('prpsproposta').focus();
		return false;
	}	
	*/
	
	/* DADOS DO SEGURADO NOME*/
	var prpssegurado = document.getElementById('prpssegurado').value;
	if(prpssegurado == '')
	{
		alert('CAMPO OBRIGATORIO\nO CAMPO NOME DEVE SER PREENCHIDO!');
		document.getElementById('prpssegurado').focus();
		return false;
	}	
	
	/* TIPO PESSOA*/
	var prpstipo_pessoa = document.getElementById('prpstipo_pessoa').value;
	if(prpstipo_pessoa == '')
	{
		alert('CAMPO OBRIGATORIO\nO CAMPO TIPO DEVE SER PREENCHIDO!');
		document.getElementById('prpstipo_pessoa').focus();
		return false;
	}	
	
	//alert('cpf  '+document.getElementById('prpsscnpj_cpf_cpf').value+' | '+document.getElementById('prpsscnpj_cpf_cpf').Name);
	//alert('cnpj '+document.getElementById('prpsscnpj_cpf_cnpj').value+' | '+document.getElementById('prpsscnpj_cpf_cnpj').Name);	
	
	
	if(prpstipo_pessoa == 'F')
	{
		/*  CPF */
		var prpsscnpj_cpf_cpf = document.getElementById('prpsscnpj_cpf_cpf').value;
		if(prpsscnpj_cpf_cpf == '')
		{
			alert('CAMPO OBRIGATORIO\nO CAMPO CPF DEVE SER PREENCHIDO!');
			document.getElementById('prpsscnpj_cpf_cpf').focus();
			document.getElementById('prpsscnpj_cpf_cnpj').value = '';			
			return false;
		}

        var cpf = prpsscnpj_cpf_cpf;
        exp = /\.|\-/g
        cpf = cpf.toString().replace( exp, "" ); 
        var digitoDigitado = eval(cpf.charAt(9)+cpf.charAt(10));
        var soma1=0, soma2=0;
        var vlr =11;
        
        for(i=0;i<9;i++)
        {
                soma1+=eval(cpf.charAt(i)*(vlr-1));
                soma2+=eval(cpf.charAt(i)*vlr);
                vlr--;
        }  
             
        soma1 = (((soma1*10)%11)==10 ? 0:((soma1*10)%11));
        soma2=(((soma2+(2*soma1))*10)%11);
        
        var digitoGerado=(soma1*10)+soma2;
        if(digitoGerado!=digitoDigitado)  
        {      
            //    alert('CPF Invalido!');
            //    document.getElementById('prpsscnpj_cpf_cpf').focus();
          	//	return false;
        }   		

	}
	
	if(prpstipo_pessoa == 'J')
	{
		/*  CNPJ */
		var prpsscnpj_cpf_cnpj = document.getElementById('prpsscnpj_cpf_cnpj').value;
		
		//alert(prpsscnpj_cpf_cnpj);
		
		if(prpsscnpj_cpf_cnpj == '')
		{
			alert('CAMPO OBRIGATORIO\nO CAMPO CNPJ DEVE SER PREENCHIDO!');
		/*	document.getElementById('prpsscnpj_cpf_cnpj').focus();*/
			document.getElementById('prpsscnpj_cpf_cpf').value = ''			
			return false;
		}	
		
        var cnpj = prpsscnpj_cpf_cnpj;
        var valida = new Array(6,5,4,3,2,9,8,7,6,5,4,3,2);
        var dig1= new Number;
        var dig2= new Number;
        
        exp = /\.|\-|\//g
        cnpj = cnpj.toString().replace( exp, "" ); 
        var digito = new Number(eval(cnpj.charAt(12)+cnpj.charAt(13)));
                
        for(i = 0; i<valida.length; i++)
        {
                dig1 += (i>0? (cnpj.charAt(i-1)*valida[i]):0);  
                dig2 += cnpj.charAt(i)*valida[i];       
        }
        
        dig1 = (((dig1%11)<2)? 0:(11-(dig1%11)));
        dig2 = (((dig2%11)<2)? 0:(11-(dig2%11)));
        
        if(((dig1*10)+dig2) != digito)  
        {
                alert('CNPJ Invalido!');		
                document.getElementById('prpsscnpj_cpf_cnpj').foccpfus();
          		return false;
        }  				
		
	}

	var prpsdt_solicitacao = $('#prpsdt_solicitacao').val();
	var enderecoNumero = $('#prpsnumero').val();
	var prpsddd = $("#prpsddd").val();
	var prpsfone = $("#prpsfone").val();
	var prpscep = $("#prpscep").val();
    
    var prpsddd2 = $("#prpsddd2").val();
	var prpsfone2 = $("#prpsfone2").val();
    
    var prpsddd3 = $("#prpsddd3").val();
	var prpsfone3 = $("#prpsfone3").val();

	if (!(prpsdt_solicitacao.length > 0)) {
		alert('É necessario informar a data de solicitação');
		return false;
	}

	if (isNaN(enderecoNumero) || !(enderecoNumero > 0)) {
		alert('É necessario informar o número do endereço.');
		return false;
	}	
	
	if (isNaN(prpsddd) || !(prpsddd > 0)) {
		alert("Por favor informe um DDD válido.");
		$("#prpsddd").focus();
		return false;
	}

	if (prpsfone == "") {
		alert("Por favor informe o número do telefone.");
		$("#prpsfone").focus();
		return false;
	}
    
    if (isNaN(prpsddd2) || !(prpsddd2 > 0)) {
		alert("Por favor informe um DDD válido.");
		$("#prpsddd2").focus();
		return false;
	}

	if (prpsfone2 == "") {
		alert("Por favor informe o número do telefone 2.");
		$("#prpsfone2").focus();
		return false;
	}

	if (prpscep == "") {
		alert("Por favor preencha o campo CEP.");
		$("#prpscep").focus();
		return false;
	}

	return true;
}

function alterar_contrato_seguradora()
{
	var verificacao = verificaCadastro();
	
	if (!verificacao) {
		return false;
	}

    jQuery('#acao').val('alterar_proposta');
	//document.getElementById('acao').value='alterar_proposta';
    
    jQuery.ajax({
        url: 'prn_proposta_seguradora.php?acao=editar&id='+jQuery('#prpsoid').val(),        
        data: jQuery('#form').serialize()+'&isAjax=true',
        type: 'post',
        beforeSend: function(){
            //
        },
        success: function(data){
            
            var resultado = jQuery.parseJSON(data);
                                    
            if(resultado.error){
                alert(resultado.descricao);
            }else{
                jQuery('#form').submit();
            }
            
        }
    });	
    
    return true;
}

function incluir_contrato_seguradora()
{
	/* tipo de contrato*/
	var prpstpcoid = document.getElementById('prpstpcoid').value;
	if(prpstpcoid == '')
	{
		alert('CAMPO OBRIGATORIO\nO CAMPO TIPO DE CONTRATO DEVE SER PREENCHIDO!');
		document.getElementById('prpstpcoid').focus();
		return false;
	}
	
	var verificacao = verificaCadastro();
	
	if (!verificacao) {
		return false;
	}

    document.form.acao.value='incluir_proposta';
    document.form.submit(); 
    
}

function gerar_contrato()
{
	
	/* NUMERO DA PROPOSTA*/
	var prpsproposta = document.getElementById('prpsproposta').value;
	if(prpsproposta == '')
	{
		alert('CAMPO OBRIGATORIO\nO CAMPO NUMERO DA PROPOSTA DEVE SER PREENCHIDO!');
		document.getElementById('prpsproposta').focus();
		return false;
	}
	
	/* CAMPO STATUS*/
	var prpsprpssoid = document.getElementById('prpsprpssoid').value;
	if(prpsprpssoid == '')
	{
		alert('CAMPO OBRIGATORIO\nO CAMPO STATUS DEVE SER PREENCHIDO!');
		document.getElementById('prpsprpssoid').focus();
		return false;
	}	
	
	/* CAMPO DRM ?*/
	/*
	var prpsproposta = document.getElementById('prpsproposta').value;
	if(prpsproposta == '')
	{
		alert('CAMPO OBRIGATORIO\nO CAMPO NUMERO DA PROPOSTA DEVE SER PREENCHIDO!');
		document.getElementById('prpsproposta').focus();
		return false;
	}	
	*/
	
	/* DADOS DO SEGURADO NOME*/
	var prpssegurado = document.getElementById('prpssegurado').value;
	if(prpssegurado == '')
	{
		alert('CAMPO OBRIGATORIO\nO CAMPO NOME DEVE SER PREENCHIDO!');
		document.getElementById('prpssegurado').focus();
		return false;
	}	
	
	/* TIPO PESSOA*/
	var prpstipo_pessoa = document.getElementById('prpstipo_pessoa').value;
	if(prpstipo_pessoa == '')
	{
		alert('CAMPO OBRIGATORIO\nO CAMPO TIPO DEVE SER PREENCHIDO!');
		document.getElementById('prpstipo_pessoa').focus();
		return false;
	}	
	
	if(prpstipo_pessoa == 'F')
	{
		/*  CPF */
		var prpsscnpj_cpf_cpf = document.getElementById('prpsscnpj_cpf_cpf').value;
		if(prpsscnpj_cpf_cpf == '')
		{
			alert('CAMPO OBRIGATORIO\nO CAMPO CPF DEVE SER PREENCHIDO!');
			document.getElementById('prpsscnpj_cpf_cpf').focus();
			document.getElementById('prpsscnpj_cpf_cnpj').value = '';			
			return false;
		}	

	}
	
	if(prpstipo_pessoa == 'J')
	{
		/*  CNPJ */
		var prpsscnpj_cpf_cnpj = document.getElementById('prpsscnpj_cpf_cnpj').value;
		if(prpsscnpj_cpf_cnpj == '')
		{
			alert('CAMPO OBRIGATORIO\nO CAMPO CNPJ DEVE SER PREENCHIDO!');
			document.getElementById('prpsscnpj_cpf_cnpj').focus();
			document.getElementById('prpsscnpj_cpf_cpf').value = ''			
			return false;
		}	
		
	}
		

	var prpsddd = $("#prpsddd").val();
	var prpsfone = $("#prpsfone").val();
	var prpscep = $("#prpscep").val();
    
    var prpsddd2 = $("#prpsddd2").val();
	var prpsfone2 = $("#prpsfone2").val();
	
	if (isNaN(prpsddd) || !(prpsddd > 0)) {
		alert("Por favor informe um DDD válido.");
		$("#prpsddd").focus();
		return false;
	}

	if (prpsfone == "") {
		alert("Por favor informe o número do telefone.");
		$("#prpsfone").focus();
		return false;
	}
    
    if (isNaN(prpsddd2) || !(prpsddd2 > 0)) {
		alert("Por favor informe um DDD válido.");
		$("#prpsddd2").focus();
		return false;
	}

	if (prpsfone2 == "") {
		alert("Por favor informe o número do telefone 2.");
		$("#prpsfone2").focus();
		return false;
	}

	if (prpscep == "") {
		alert("Por favor preencha o campo CEP.");
		$("#prpscep").focus();
		return false;
	}
	
	document.getElementById('acao').value='gerar_contrato';
	document.getElementById("form").submit();
	
}

function abre_abas(link)
{
		location=link;
}

function incluir_historico()
{
	    
	var prpshpsmtoid = document.getElementById('prpshpsmtoid').value;
	if(prpshpsmtoid == '')
	{
		alert('CAMPO OBRIGATORIO\nEM HISTORICO O CAMPO MOTIVO DEVE SER PREENCHIDO!');
		document.getElementById('prpshpsmtoid').focus();
		return false;
	}
	
	if(prpshpsmtoid == '19')
	{
		var prpshdata = document.getElementById('prpshdata').value;
		if(prpshdata == '')
		{
			alert('CAMPO OBRIGATORIO\nDATA DEVE SER PREENCHIDO!');
			document.getElementById('prpshdata').focus();
			return false;
		}
	
		var prpshhora = document.getElementById('prpshhora').value;
		if(prpshhora == '')
		{
			alert('CAMPO OBRIGATORIO\nHORA DEVE SER PREENCHIDO!');
			document.getElementById('prpshhora').focus();
			return false;
		}
	}
	
	var prpshobservacao = document.getElementById('prpshobservacao').value;
	if(prpshobservacao == '')
	{
		alert('CAMPO OBRIGATORIO\nOBSERVACAO DEVE SER PREENCHIDO!');
		document.getElementById('prpshobservacao').focus();
		return false;
	}
	
	document.getElementById('acao').value='incluir_historico';
	document.getElementById("form").submit();
	
}

function alterar_contrato_seg()
{
	    
	var connumero_original = document.getElementById('connumero_original').value;
	var acao_contrato = document.getElementById('acao_contrato').value;
	var prpsnumero = $('#prpsnumero').val();

	if (prpsnumero == '' || prpsnumero == 0 || isNaN(prpsnumero)) {
		alert('CAMPO OBRIGATORIO\nO Número do endereço deve ser informado!');
		$('#prpsnumero').focus();
		return false;
	}
	
	if(connumero_original == '')
	{
		alert('CAMPO OBRIGATORIO\nO Número do contrato Original deve ser informado!');
		document.getElementById('connumero_original').focus();
		return false;
	}
	
	
	
	if(acao_contrato>0)
	{
		if(acao_contrato == 1)
		{
			var nome_acao_contrato = 'Fazer Transferência de Titularidade do';
		}
		else if(acao_contrato == 2)
		{
			var nome_acao_contrato = 'Migrar para o';	
			
			if($('#tpcoid').val() == '')
			{
				alert('CAMPO OBRIGATORIO\nMigrar para deve ser informado!');
				//document.getElementById('connumero_original').focus();
				return false;
			}			
					
		}
		else if(acao_contrato == 3)
		{
			var nome_acao_contrato = 'Migrar e Transferir a titularidade para o';	
			
			if($('#tpcoid').val() == '')
			{
				alert('CAMPO OBRIGATORIO\nMigrar para deve ser informado!');
				//document.getElementById('connumero_original').focus();
				return false;
			}			
					
		}
	
		
		if(confirm("Deseja mesmo "+nome_acao_contrato+" contrato "+connumero_original+" ?"))
		{
		
				if(acao_contrato==1){
				document.getElementById('acao').value='transfeferncia_titularidade';
			    document.getElementById("form").submit();		
				}else if(acao_contrato==3){
					document.getElementById('acao').value='migracao_transferencia_titularidade';
				    document.getElementById("form").submit();		
				} else {
				document.getElementById('acao').value='migracao_ativo';
			    document.getElementById("form").submit();
				}
		}
				
	} else {
			alert('ATENÇÃO!\nInforme a ação antes de prosseguir!');
			document.getElementById('acao_contrato').focus();
			return false;
	}
	
}

function buscaDadosMigracao()
{
	var acao_contrato = $('#acao_contrato').val();
	
	if(acao_contrato == '2' || acao_contrato == '3')
	{
		$('#mostraMigrarPara').css('display','');
	}
	else
	{
		$('#mostraMigrarPara').css('display','none');		
	}
}

function pesquisar()
{
	
	var prpsproposta_busca = document.getElementById('prpsproposta_busca').value;
	var placa_busca = document.getElementById('placa_busca').value;
	var chassi_busca = document.getElementById('chassi_busca').value;
	

    //caso tenha digitado somente a data o outro filtro deve ser o tipo de contrato pelo menos
    var array_tmp = document.form.prpsdt_ultima_acao_inicio_busca.value.split('/');
    var dia_ini = array_tmp[0];
    var mes_ini = array_tmp[1];
    var ano_ini = parseInt(array_tmp[2]);
    array_tmp = document.form.prpsdt_ultima_acao_final_busca.value.split('/');
    var dia_fim = array_tmp[0];
    var mes_fim = array_tmp[1];
    var ano_fim = parseInt(array_tmp[2]);
    
    if(ano_ini+''+mes_ini+''+dia_ini > ano_fim+''+mes_fim+''+dia_fim)
    {
           alert("A Data Final nao poder ser inferior a Data Inicial!");
    	  return false;
    }

	
    var count=0;
    /*
    if(document.form.prpsdt_ultima_acao_inicio_busca.value!="" && document.form.prpsdt_ultima_acao_final_busca.value!="")
    {
        count=1;
    }
    */

    if(document.form.prpsproposta_busca.value!="")
    {
        count=1;
    }
    
    if(document.form.placa_busca.value!="")
    {
        count=1;
    }

    if(document.form.chassi_busca.value!="")
    {
        count=1;
    }

    if(count==1)
    {
        document.form.acao.value='pesquisar';
        document.form.submit();
    }
    else
    if((document.form.prpsdt_ultima_acao_inicio_busca.value!="" && document.form.prpsdt_ultima_acao_final_busca.value!=""))
    {

                
                //diminui tres meses
                if((mes_fim-3)<=0){
                    var ano_tmp=ano_fim-1;
                    var mes_tmp=12+(mes_fim-3);
                }else{
                    var ano_tmp=ano_fim;
                    var mes_tmp=(mes_fim-3);
                }
                
                //se vier valor menor que 10 concatena o 0 antes do numero do mes
                if(mes_tmp<10){
                    mes_tmp='0'+mes_tmp;
                }
                
                var data1=dia_fim+'/'+mes_tmp+'/'+ano_tmp;
                var mes_sobra=date_diff(document.form.prpsdt_ultima_acao_final_busca.value,dia_fim+'/'+mes_tmp+'/'+ano_tmp);
                var diferenca=date_diff(document.form.prpsdt_ultima_acao_final_busca.value,document.form.prpsdt_ultima_acao_inicio_busca.value);
				var diferenca=1;
                
                //limite de 1 meses de pesquisa se não utilizar outros filtros
                if(diferenca>31 || diferenca<0)
                {
                    alert("A data inicial não pode ser anterior a 1 mes!");
                    return;
                }
                else
                {
                    document.form.acao.value='pesquisar';
                    document.form.submit();
                }
       
        
    }else{
        if(count<=2){
            alert("Para realizar a pesquisa preencha algum filtro");
        }
    }
}

function nova_proposta() {
	document.form.acao.value='incluir';
    document.form.submit();
}