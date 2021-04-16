<div align="center">
    <form name="mitc_form" id="mitc_form" class="form" method="post" action="man_insere_texto_contrato.php" enctype="multipart/form-data">
        <ul class="ul_containner">
            <li class="ul_containner_titulo"><h1>Insere Texto no histórico dos contratos</h1></li>
            <ul class="ul_content">
                <li class="li_content_titulo"><h2>Pesquisar</h2></li>
                <li style="width: 50%;padding: 7px;"><p>Contratos (informe somente números e vírgula. Separe os contratos com vírgula):</p></li>
                <li style="width: 50%;">
                <textarea name="contratos" id="contratos" rows="5" style="width: 80%;margin: 5px;float: center;" value="<?=$contratos?>" onkeypress="return Verifica(event);"></textarea>
                </li>
                <li class="li_content_rodape">
                    <input type="submit" id="pesquisar" class="botao" value="Pesquisar" onClick="return ValidaVirgulas();"  style="width:90px; display: block;">
                    <input type="hidden" name="acao" value="P">
                </li>
            </ul>
        </ul>
    </form>
</div>

<script>
function Verifica(event){ 
    var keyDig = event.keyCode;
    // aceita somente numeros, "," e enter
    if (keyDig == 44 || (keyDig >= 48 && keyDig <= 57) || keyDig == 13) {
            return true;
        }
        else {
            return false;
        }
}


function ValidaVirgulas()
{
    var caracter,prox_caracter,ind, enter_code;
    
    for(ind = 0;ind < document.getElementById('contratos').value.length;ind++) {
        caracter = document.getElementById('contratos').value.substring(ind - 1,ind);
        prox_caracter = document.getElementById('contratos').value.substring(ind,ind + 1);
        enter_code = prox_caracter.charCodeAt(0);
		
        if (caracter == "," && prox_caracter == ",") {
            alert('Favor informar valores entre as vírgulas');
	    document.getElementById('contratos').focus();
	    return false;
	}
	
	if (enter_code == 10 && caracter != ",") {
	   alert ("Favor informar vírgula antes do enter");
	    document.getElementById('contratos').focus();
	    return false;
	}
	
    }
    return true;
}
											    

</script>

<?php
if(isset($resultado)){
    if ($resultado == "ok") {
	echo ' <li class="li_content_rodape">Históricos inseridos com sucesso </li>';
    } // ok
    else {
	echo ' <li class="li_content_rodape"><b>Erro</b> ao inserir os históricos </li>';
    }// else
}

?>
