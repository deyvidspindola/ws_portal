<?php 
$formaPagamento = $control->tipoPagamento();
$dataVencimento = $control->dataVencimento();
$clioid = $listaClienteExistente['clioid'];
if($listaFormaPagamentoIdProposta['ptfpforcoid'] != '' || $listaFormaPagamentoIdProposta['ptfpforcoid'] != null || $listaFormaPagamentoIdProposta['ptfpforcoid'] > 0) {

	$tipoPagamento = $listaFormaPagamentoIdProposta['ptfpforcoid'];
	$diaVencimento = $listaFormaPagamentoIdProposta['cdvdia'];
	$codigoBanco = $listaFormaPagamentoIdProposta['ptfpbancodigo'];
	$nomeBanco = $listaFormaPagamentoIdProposta['bannome'];
	$bancoAgencia = $listaFormaPagamentoIdProposta['ptfpagencia'];
	$cartaoDebito = $listaFormaPagamentoIdProposta['ptfpnumconta'];
	$cartaoCredito = $listaFormaPagamentoIdProposta['ptfpnumcartaocredito'];
	$dataValidadeCartaoCredito = $listaFormaPagamentoIdProposta['ptfpvalidadecartaocredito'];
	
}else if($formaPagamentoAntiga != '' || $formaPagamentoAntiga != null || $formaPagamentoAntiga > 0){

	
	$tipoPagamento = $formaPagamentoAntiga['forcoid'];
	$diaVencimento = $formaPagamentoAntiga['clidia_vcto'];
	$codigoBanco = $formaPagamentoAntiga['bancodigo'];
	$nomeBanco = $formaPagamentoAntiga['bannome'];
	$bancoAgencia = $formaPagamentoAntiga['clicagencia'];
	$cartaoDebito =  $formaPagamentoAntiga['clicconta'];
	$cartaoCredito = '';
	$dataValidadeCartaoCredito = '';
	
	
}
?>

<div class="mensagem alerta" id="msgalerta2" style="display: none;"></div>
<div class="modulo_titulo">Forma de Pagamento</div>
<div class="bloco_conteudo">

 <table  border="0" cellspacing="1" cellpadding="0" class="tableMoldura">

        <input type="hidden" class="campo" name="clioid" id="clioid" value="<?=$clioid?>">	
              
                    <tr>
                        <td>
                            <label for="cliente">Forma de Pagamento*</label>
                        </td>
                    </tr>
                    <tr> 
                        <td>
                        <?php 
						   if($formaPagamentoAntigaCadastrada['forcdebito_conta'] == 't') {

						?>
                        	
                        	 <input type="hidden" class="campo" name="tipoPagamento" id="tipoPagamento" value="Debito">	
                        <?php 
                        	}else if($formaPagamentoAntigaCadastrada['forccobranca_cartao_credito'] == 't'){

							?>
                        	 <input type="hidden" class="campo" name="tipoPagamento" id="tipoPagamento" value="Credito">	
                        <?php 		
                        	}
                        ?>
                       <input type="hidden" class="campo" name="tipoPagamentoAtual" id="tipoPagamentoAtual" value="">	
                                   <select name="tipo_pagamento" id="tipo_pagamento" >
                                                  		<option value="">Escolha</option>
							<?php foreach ($formaPagamento as $row) : ?>
							<option value="<?php echo $row['codigo'];?>" <? if ($tipoPagamento==$row['codigo']) echo " SELECTED"; ?> ><?php echo $row['descricao'];?></option>
							<?php endforeach;?>
                                                        </select>
                        </td>
                        
                    </tr>
                 <tr><td></td></tr>
                    <tr>
                        <td>
                            <label for="cliente">Data de Vencimento*</label>
                        </td>
                    </tr>
                    <tr> 
                        <td>
                                   <select name="data_vencimento" id="data_vencimento" >
                                                            	<option value="">Escolha</option>
							<?php foreach ($dataVencimento as $row) : ?>
							<option value="<?php echo $row['codigo']; ?>" <? if ($diaVencimento==$row['descricao']) echo " SELECTED"; ?> ><?php echo $row['descricao'];?></option>
							<?php endforeach;?>
                                                        </select>
                        </td>
                        
                    </tr>
    </table>  
    <div id="formasPagamento"></div>
    <div id="cartaoDebito">
     <table  border="0" cellspacing="1" cellpadding="0" class="tableMoldura">
        

                    <tr>
                        <td>
                            <label for="cliente">Banco*</label>
                        </td>
                    </tr>
                    <tr> 
                        <td>
                        <input type="hidden" class="campo" name="idBanco" id="idBanco" value="<?php echo $codigoBanco;?>">
                    	<input type="text" class="campo" name="nomeBanco" id="nomeBanco" value="<?php echo $nomeBanco; ?>">
                        </td>
                        
                    </tr>
                 <tr><td></td></tr>
                    <tr>
                        <td>
                            <label for="cliente">Agencia*</label>
                        </td>
                    </tr>
                    <tr> 
                        <td>
                     <input type="text" class="campo" name="nAgencia" id="nAgencia" size="8" maxlength="5" value="<?php echo $bancoAgencia; ?>">
                        </td>
                        
                    </tr>
                        <tr>
                        <td>
                            <label for="cliente">Conta*</label>
                        </td>
                    </tr>
                    <tr> 
                        <td>
                     <input type="text" class="campo" name="nConta" id="nConta" size="10" maxlength="5" value="<?php echo $cartaoDebito; ?>">
                        </td>
                        
                    </tr>
    </table>
    
    </div>
    
        <div id="cartaoCredito">
     <table  border="0" cellspacing="1" cellpadding="0" class="tableMoldura">
     
           <tr>
                        <td>
                           <label for="cliente">Nº Cartão *</label>
                        </td>
                    </tr>
                    <tr> 
                        <td>
                         <input type="text" class="campo" name="nCartao" id="nCartao" value="<?php echo $cartaoCredito; ?>" maxlength="16">
                        </td>
                        
                    </tr>
                 <tr><td></td></tr>
                    <tr>
                        <td>
                            <label for="cliente">Mês/Ano (mm/aa)*</label>
                        </td>
                    </tr>
                    <tr> 
                        <td>
                     <input type="text" class="campo" name="dataCartao" id="dataCartao" size="10" value="<?php echo $dataValidadeCartaoCredito; ?>" maxlength="5">
                        </td>
                        
                    </tr>
                
    </table>
    
    </div>
</div>
