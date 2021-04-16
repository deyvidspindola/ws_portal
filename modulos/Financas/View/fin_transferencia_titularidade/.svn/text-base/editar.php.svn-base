<?php require_once '_header.php'; ?> 
<script type="text/javascript" src="modulos/web/js/fin_transferencia_titularida_novo_titular.js" charset="utf-8"></script>
<script type="text/javascript" src="modulos/web/js/fin_transferencia_titularidade.js" charset="utf-8"></script>
<script type="text/javascript" src="modulos/web/js/fin_transferencia_titularidade_gera_termo.js" charset="utf-8"></script>
<style>

.form{
border-radius:2px;
padding:20px 30px;
box-shadow:0 0 15px;
font-size:14px;
font-weight:bold;
width:350px;
margin:20px 250px 0 35px;
float:left;
}

textarea{
width:100%;
height:80px;
margin-top:5px;
border-radius:3px;
padding:5px;
resize:none;
}
#titularidadediv{
opacity:0.92;
position: fixed;
top: 0px;
left: 0px;
height: 100%;
width: 100%;
background: #000;
display: none;
}

.img{
float: right;
margin-top: -35px;
margin-right: -37px;
}

#reprovacao{
width:350px;
heigth:100%;
margin:0px;
background-color:white;
font-family: 'Fauna One', serif;
position: relative;
border: 5px solid rgb(90, 158, 181);
}

#reprovacao{
left: 50%;
top: 50%;
margin-left:-210px;
margin-top:-255px;
}

</style>

<div class="mensagem alerta" id="msgsucesso" style="display: none;"></div>
<!-- Contact Form -->
<div id="titularidadediv">
<form class="form" action="#" id="reprovacao">
<img src="images/fileclose.gif" class="img" id="cancelar"/>
<label>Motivo da Reprovação: <span>*</span></label>
<textarea rows="4" cols="50" maxlength="300" id="textareaReprova">
</textarea>
<input type="hidden" id="idproposta" value="<?php echo $retorno['ptraoid'] ?>" />
<input type="hidden" id="tipoReprovacao" value="" />
<input type="button" id="salvar" value="Salvar"/>
<input type="button" id="cancelar" value="Cancelar"/>
<br/>
</form>
</div>
<?php
 require_once 'lista_contrato_dadoscontato.php'; 


if ($retorno ['ptrasfoid_analise_credito'] != 2 && $retorno ['ptrasfoid_analise_divida'] != 2 && ($retorno ['ptrasfoid_analise_credito'] != 3 && $retorno ['ptrasfoid_analise_divida'] != 3 )) {


?>

<div class="bloco_acoes">
<!-- #dialog is the id of a DIV defined in the code below -->
<?php if ($_SESSION['funcao']['analise_credito_transferencia'] == 1) {?>
	<input type="button" id="aprova_credito"
		value="<?php echo 'Aprovar Análise de Crédito';?>" /> <input
		type="button" id="reprova_credito"
		value="<?php echo 'Reprovar Análise de Crédito';?>" /> 
		<?php } if ($_SESSION['funcao']['analise_divida_transferencia'] == 1){ ?>
		<input
		type="button" id="aprova_divida"
		value="<?php echo 'Aprovar transferência de Dívida';?>" /> <input
		type="button" id="reprova_divida"
		value="<?php echo 'Reprovar transferência de Dívida';?>" />
		<?php }?>
				<input
		type="button" id="voltar"
		value="<?php echo utf8_encode('Voltar');?>" />

</div>
<?php
} else if ($retorno ['ptrasfoid_analise_credito'] == 2 && $retorno ['ptrasfoid_analise_divida'] != 2 && $retorno ['ptrasfoid_analise_divida'] != 3) {
	?>
<div class="bloco_acoes">
<?php  if ($_SESSION['funcao']['analise_divida_transferencia'] == 1){?>
	<input type="button" id="aprova_divida"
		value="<?php echo 'Aprovar transferência de Dívida';?>" /> <input
		type="button" id="reprova_divida"
		value="<?php echo 'Reprovar transferência de Dívida';?>" />
<?php } ?>
				<input
		type="button" id="voltar"
		value="<?php echo utf8_encode('Voltar');?>" />
</div>
<?php
} else if ($retorno ['ptrasfoid_analise_credito'] != 2 && $retorno ['ptrasfoid_analise_credito'] != 3 && $retorno ['ptrasfoid_analise_divida'] == 2) {
	?>
<div class="bloco_acoes">
<?php if ($_SESSION['funcao']['analise_credito_transferencia'] == 1) {?>
	<input type="button" id="aprova_credito"
		value="<?php echo 'Aprovar Análise de Crédito';?>" /> <input
		type="button" id="reprova_credito"
		value="<?php echo 'Reprovar Análise de Crédito';?>" />
		<?php } ?>
				<input
		type="button" id="voltar"
		value="<?php echo utf8_encode('Voltar');?>" />
</div>
<?php
} else if ($retorno ['ptrasfoid_analise_credito'] == 3 || $retorno ['ptrasfoid_analise_divida'] == 3) {
	?>
<div class="bloco_acoes">
	<input type="button" id="voltar"
		value="<?php echo 'Voltar';?>"/>
</div>
<?php
}else {
$control = new FinTransferenciaTitularidade();

$estado = $control->retornaEstados();
$pais = $control->retornaPaises();
?>

<form name="frm_novo_titular" id="frm_novo_titular" method="POST"
	action="">
	


	<input type="hidden" name="acao" id="acao" value="" />
	<input type="hidden" name="ptraoid" id="ptraoid" value="<?php echo $retorno['ptraoid'] ?>" />
	<div class="modulo_titulo">Dados do Novo Cliente Titular</div>
	
	<div class="modulo_conteudo">
		     <div id="carregandoAguardeInicio"></div> 
   <?php require_once '_msgPrincipal.php'; ?>

   <?php
	if(strlen($retorno['ptrano_documento']) <= 11) {
		require_once 'novo_cliente_fisico.php';
	 }else {
	
		require_once 'novo_cliente_juridico.php';
	 }
	?>

	<div class="separador"></div>
	<div><?php require_once 'endereco.php'; ?></div>
<div id="carregandoAguardeEndereco"></div> 
	<div class="separador"></div>
	<div><?php require_once 'endereco_cobranca.php'; ?></div>

	<div class="separador"></div>
	<div><?php require_once 'formas_pagamento.php'; ?></div>
	<div id="carregandoAguardeMeio"></div> 
	<div class="separador"></div>
	<div><?php require_once 'pessoas_autorizadas.php'; ?></div>
	
	<div class="separador"></div>
	<div><?php require_once 'contato_emergencia.php'; ?></div>
	<div class="separador"></div>
	<div id="carregandoAguardeAutorizada"></div> 
	<div><?php require_once 'contato_instalacao_assistencia.php';?></div>
	
		<div class="separador"></div>
	<div><?php require_once 'anexar_arquivo.php';?></div>

		<div class="separador"></div>
	<div><?php require_once 'anexar_carta_transferencia.php';?></div>
	</div>
	 <div id="carregandoAguardeFim"></div> 
	<div class="bloco_acoes">
	<?php 

	if(trim($retorno['ptrastatus_conclusao_proposta']) != 'CA' && trim($retorno['ptrastatus_conclusao_proposta']) != 'C' && trim($retorno['ptrastatus_conclusao_proposta']) != 'F') {

	?>
	<input type="button" id="salva_proposta_titularidade"
		value="<?php echo utf8_encode('Salvar');?>" /> <input
		type="button" id="gerar_termo"
		value="<?php  echo utf8_encode('Gerar Termo');?>" /> <input
		type="button" id="cancelar_solicitacao"
		value="<?php echo 'Cancelar Solicitação';?>" /> 
		<?php } ?>
				<input
		type="button" id="voltar"
		value="<?php echo utf8_encode('Voltar');?>" />

	</div>
</form>
<?php } ?>
