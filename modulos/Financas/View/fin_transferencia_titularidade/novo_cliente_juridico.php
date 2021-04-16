<?php

if(isset($listaClientesNovo) || $listaClientesNovo != '' || $listaClientesNovo != null || $listaClientesNovo > 0) {
	
	$nomeJuridico = $listaClientesNovo['ptcnome'];
	$optsimples = $listaClientesNovo['ptcoptantesimples'];
	$dtfundacao = $listaClientesNovo['ptcdatafundacao'];
	
	if($dtfundacao!= '' && !empty($dtfundacao)){
		$dataFundacao = explode("-",$dtfundacao);
		$dtfundacao = $dataFundacao[2]."/".$dataFundacao[1]."/".$dataFundacao[0];
	}
	
	$estIns = $listaClientesNovo['ptcestadoinscricaoest'];
	$NumInscEs = $listaClientesNovo['ptcinscricaoest'];

}else if(isset($listaClienteExistente) || $listaClienteExistente != '' || $listaClienteExistente != null || $listaClienteExistente > 0){

	$nomeJuridico = $retorno['ptranome'];
	$optsimples = $listaClientesNovo['clireg_simples'];
	$dtfundacao = $listaClientesNovo['clidt_nascimento'];
	/*if($dtfundacao!= '' && !empty($dtfundacao)){
		$dataFundacao = explode("-",$dtfundacao);
		$dtfundacao = $dataFundacao[2]."/".$dataFundacao[1]."/".$dataFundacao[0];
	}*/
	$estIns = $listaClientesNovo['cliuf_inscr'];
	$NumInscEs = $listaClientesNovo['cliinscr'];
}else{
	$nomeJuridico = $retorno['ptranome'];
}

if(strlen($retorno['ptrano_documento']) < 14) {
	$numeroDocumento = "0".$retorno['ptrano_documento'];
}else{
	$numeroDocumento =  $retorno['ptrano_documento'];
}

?>

<div class="mensagem alerta" id="msgalerta2" style="display: none;"></div>
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
          <td><label for="cliente">CNPJ*</label></td>
        </tr>
        <tr>
          <td><input type="text" id="prtno_documento_editar" name="prtno_documento_editar"
            value="<?php echo $numeroDocumento;?>"  readonly="readonly" maxlength="20"  />
            <input type="hidden" id="prtipopessoa" name="prtipopessoa"
            value="J"  /> 
            </td>

  </tr>

  <tr>
          <td><label for="cliente">Optante pelo Simples*</label></td>
        </tr>
        <tr>
          <td>
          <select name="prtoptante_simples" id="prtoptante_simples" >
           <option value="">Escolha</option>
           <option value="S" <? if ($optsimples=="S") echo " SELECTED"; ?>>Sim</option>
           <option value="N" <? if ($optsimples=="N") echo " SELECTED"; ?>>Não</option>
          </select>
          </td>

  </tr>
  
     <tr>
          <td><label for="pesq_campo1"><?php echo "Contratante*"; ?></label></td>
        </tr>
        <tr>
          <td><input type="text" id="prtcontratante" name="prtcontratante"
            value="<?php echo $nomeJuridico; ?>" size="50" maxlength="100" /></td>
        </tr>
        
      <tr>
          <td><label for="pesq_campo1">Data de Fundação*</label></td>
        </tr>
        <tr>
            <td><input type="text" name="prpfund_dt" id="prpfund_dt"
            value="<?=$dtfundacao?>" size="10" maxlength="10"
            onkeyup="formatar(this,'@@/@@/@@@@');"
            onblur="revalidar(this,'@@/@@/@@@@','data');"> <img
            src="images/calendar_cal.gif" align="absmiddle" border="0"
            alt="Calendário..."
            onclick="displayCalendar(document.getElementById('prpfund_dt'),'dd/mm/yyyy',this)">
          </td>
        </tr>
  		 <tr>
          <td><label for="pesq_campo1"><?php echo "Inscrição Estadual*"; ?></label></td>
        </tr>
        <tr>
          <td>
          <select
			name="prtie_estado" id="prtie_estado">
			<option value="">UF</option>
			<?php foreach ($estado as $row) : ?>
			<option value="<?php echo $row['estoid']; ?>" <? if ($estIns==$row['estoid']) echo " SELECTED"; ?>><?php echo $row['estuf'];?></option>
			<?php endforeach;?>
		</select> <input type="text" id="prtie_num" name="prtie_num" value="<?php echo $NumInscEs; ?>"
			size="50"  maxlength="21"/>
          
          </td>
        </tr>

  </table>
</div>
