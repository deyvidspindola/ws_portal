<?php


if($listaClientesNovo != '' || $listaClientesNovo != null || $listaClientesNovo > 0) {
	$nomeClienteFisico = $listaClientesNovo['ptcnome'];
	$rg = $listaClientesNovo['ptcrg'];
	$orgaoEmissor = $listaClientesNovo['ptcorgaoemissor'];
	$dataEmissao = $listaClientesNovo['ptcdataemissao'];
	$dataNasc = $listaClientesNovo['ptcdatanasc'];
	$nomePai = $listaClientesNovo['ptcnomepai'];
	$nomeMae = $listaClientesNovo['ptcnomemae'];
	$sexo = $listaClientesNovo['ptcsexo'];
	$estadocivil = $listaClientesNovo['ptcivil'];
	
}else if($listaClienteExistente != '' || $listaClienteExistente != null || $listaClienteExistente > 0){
	
	
	$nomeClienteFisico = $retorno['ptranome'];
	$rg = $listaClienteExistente['clino_rg'];
	$orgaoEmissor = $listaClienteExistente['cliemissor_rg'];
	$dataEmissao = $listaClienteExistente['clidt_emissao_rg'];
	$dataNasc = $listaClienteExistente['clidt_nascimento'];
	$nomePai = $listaClienteExistente['clipai'];
	$nomeMae = $listaClienteExistente['climae'];
	$sexo = $listaClienteExistente['clisexo'];
	$estadocivil = $listaClienteExistente['cliestado_civil'];
	
	//$dataNascExp = explode("-",$listaClienteExistente['clidt_nascimento']);
	//$dataNasc = clidt_nascimento;

	$formaPagamentoAtual = $control->retormaFormaPagamentoClienteExistente($listaClienteExistente['clioid']);
}else{
	$nomeClienteFisico = $retorno['ptranome'];
}

	if(strlen($retorno['ptrano_documento']) < 11) {
		$numeroDocumento = "0".$retorno['ptrano_documento'];
	}else{
		$numeroDocumento =  $retorno['ptrano_documento'];
	}

?>
<div class="mensagem alerta" id="msgalertacliente" style="display: none;"></div>
<div class="modulo_titulo">Dados Principais</div>
<div class="bloco_conteudo">
  <table class="tableMoldura">
  
    <tr>
          <td><label for="cliente">Valor Taxa Transferencia*</label></td>
        </tr>
        <tr>
          <td><input type="text" id="taxa" name="taxa"
            value="<?php echo $valorTaxasContratos;?>"  />
 
          </td>

  </tr>
  
  <tr>
          <td><label for="cliente">CPF*</label></td>
        </tr>
        <tr>
          <td><input type="text" id="prtno_documento_editar" name="prtno_documento_editar"
            value="<?php echo $numeroDocumento;?>" readonly="readonly" maxlength="14" />
          <input type="hidden" id="prtipopessoa" name="prtipopessoa"
            value="F"  />  
          </td>

  </tr>

  <tr>
          <td><label for="cliente">Nome*</label></td>
        </tr>
        <tr>
          <td><input type="text" id="prtcontratante" name="prtcontratante"
            value="<?php echo utf8_decode($nomeClienteFisico);?>" size="50" maxlength="100" /></td>

  </tr>

  <tr>
      <td width="18%"><label>RG:</label></td>
      <td width="18%"><label><?php echo "Orgão Emissor";?></label></td>
      <td width="18%"><label><?php echo "Data Emissão"; ?></label></td>
    </tr>
    <tr>
      <td><input type="text" name="prtrg" id="prtrg" size="15"  maxlength="12" value="<?php echo $rg;?>" ></td>
      <td>
<select
            name="prtrgorgaoemissor" id="prtrgorgaoemissor">

                       <option value="" ><?php echo "Escolha o Orgão Emissor do RG"; ?></value>
                       <option value="CNT" <? if ($orgaoEmissor=="CNT") echo " SELECTED"; ?>><?php echo "CNT - Carteira Nacional de Habilitação";?></value>
                       <option value="MMA" <? if ($orgaoEmissor=="MMA") echo " SELECTED"; ?>><?php echo "MMA - Ministério da Marinha";?></value>
                       <option value="DIC" <? if ($orgaoEmissor=="DIC") echo " SELECTED"; ?>><?php echo "DIC - Diretoria de Identificação Civil";?></value>
                       <option value="POF" <? if ($orgaoEmissor=="POF") echo " SELECTED"; ?>><?php echo "POF - Polícia Federal";?></value>
                       <option value="IFP" <? if ($orgaoEmissor=="IFP") echo " SELECTED"; ?>><?php echo "IFP - Instituto Félix Pacheco";?></value>
                       <option value="POM" <? if ($orgaoEmissor=="POM") echo " SELECTED"; ?>><?php echo "POM - Polícia Militar";?></value>
                       <option value="SES" <? if ($orgaoEmissor=="SES") echo " SELECTED"; ?>>SES - Carteira de Estrangeiro</value>
                       <option value="MAE" <? if ($orgaoEmissor=="MAE") echo " SELECTED"; ?>><?php echo "MAE - Ministério da Aeronáutica";?></value>
                       <option value="SSP" <? if ($orgaoEmissor=="SSP") echo " SELECTED"; ?>><?php echo "SSP - Secretaria de Segurança Pública";?></value>
                        <option value="MEX" <? if ($orgaoEmissor=="MEX") echo " SELECTED"; ?>><?php echo "MEX - Ministério do Exército";?></value>
          </select>
      </td>
      <td>
        <input type="text" name="prpemi_dt" id="prpemi_dt"
            value="<?=$dataEmissao?>" size="10" maxlength="10"
            onkeyup="formatar(this,'@@/@@/@@@@');"
            onblur="revalidar(this,'@@/@@/@@@@','data');"> <img
            src="images/calendar_cal.gif" align="absmiddle" border="0"
            alt="Calendário..."
            onclick="displayCalendar(document.getElementById('prpemi_dt'),'dd/mm/yyyy',this)">
      </td>
    </tr>
      <tr>
          <td><label for="pesq_campo1">Data de Nascimento:*</label></td>
        </tr>
        <tr>
        <td><input type="text" name="prpnas_dt" id="prpnas_dt"
            value="<?=$dataNasc?>" size="10" maxlength="10"
            onkeyup="formatar(this,'@@/@@/@@@@');"
            onblur="revalidar(this,'@@/@@/@@@@','data');"> <img
            src="images/calendar_cal.gif" align="absmiddle" border="0"
            alt="Calendário..."
            onclick="displayCalendar(document.getElementById('prpnas_dt'),'dd/mm/yyyy',this)">
          </td>
        </tr>
   <tr>
          <td><label for="prtnomepai"><?php echo "Filiação/Pai"; ?></label></td>
        </tr>
        <tr>
          <td><input type="text" id="prtfiliacaopai" name="prtfiliacaopai" value="<?php echo utf8_decode($nomePai);?>"
            class="campo" size="50" maxlength="100" /></td>
        </tr>

    <tr>
          <td><label for="prtfiliacaoMae"><?php echo "Filiação/Mãe*"; ?></label></td>
        </tr>
        <tr>
          <td><input type="text" id="prtfiliacaoMae" name="prtfiliacaoMae" value="<?php echo utf8_decode($nomeMae);?>"
            class="campo"  size="50" maxlength="100"/></td>
        </tr>
        <tr>
          <td><label for="prtsexo">Sexo:*</label></td>
        </tr>
        <tr>
          <td>
          <select name="prtsexo" id="prtsexo">
           <option value="">Escolha</option>
           <option value="M" <? if ($sexo == "M") echo " SELECTED"; ?>>Masculino</option>
           <option value="F" <? if ($sexo == "F") echo " SELECTED"; ?>>Feminino</option>
          </select>
          </td>
        </tr>
   
        <tr>
          <td><label for="prtEsdadoCivil">Estado Civil:*</label></td>
        </tr>
        <tr>
          <td>
           <select name="prtestado_civil" id="prtestado_civil">
           <option value="">Escolha</option>
           <option value="C" <? if ($estadocivil == "C") echo " SELECTED"; ?>>Casado</option>
           <option value="S" <? if ($estadocivil == "S") echo " SELECTED"; ?>>Solteiro</option>
           <option value="V" <? if ($estadocivil == "V") echo " SELECTED"; ?>>Viúvo</option>
           <option value="D" <? if ($estadocivil == "D") echo " SELECTED"; ?>>Separado</option>
           </select>
          </td>
        </tr>
  </table>
</div>