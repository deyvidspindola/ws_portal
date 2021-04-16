<script type="text/javascript" src="modulos/web/js/fin_transferencia_titularida_novo_titular.js" charset="utf-8"></script>
<?php
$id = $result['ptraoid'];
if ($result ['ptrasfoid_analise'] != 2 && $result ['ptrasfoid_status'] != 2) {
	?>
<div class="bloco_acoes">
	<input type="button" id="aprova_credito"
		value="<?php echo 'Aprovar Análise de Crédito';?>" /> <input
		type="button" id="reprova_credito"
		value="<?php echo 'Reprovar Análise de Crédito';?>" /> <input
		type="button" id="aprova_divida"
		value="<?php echo 'Aprovar transferência de Dívida';?>" /> <input
		type="button" id="reprova_divida"
		value="<?php echo 'Reprovar transferência de Dívida';?>" />
		<input
		type="button" id="voltar"
		value="<?php echo utf8_encode('Voltar');?>" />

</div>
<?php
} else if ($result ['ptrasfoid_analise'] == 2 && $result ['ptrasfoid_status'] != 2) {
	?>
<div class="bloco_acoes">
	<input type="button" id="aprova_divida"
		value="<?php echo 'Aprovar transferência de Dívida';?>" /> <input
		type="button" id="reprova_divida"
		value="<?php echo 'Reprovar transferência de Dívida';?>" />
</div>
<?php
} else if ($result ['ptrasfoid_analise'] != 2 && $result ['ptrasfoid_status'] == 2) {
	?>
<div class="bloco_acoes">
	<input type="button" id="aprova_credito"
		value="<?php echo 'Aprovar Análise de Crédito';?>" /> <input
		type="button" id="reprova_credito"
		value="<?php echo 'Reprovar Análise de Crédito';?>" />
</div>
<?php
} else {
 
$control = new FinTransferenciaTitularidade();

$estado = $control->retornaEstados();
$pais = $control->retornaPaises();


?>

<form name="frm_novo_titular" id="frm_novo_titular" method="POST"
	action="">
	<input type="hidden" name="acao" id="acao" value="cadastroNovoTitular" />
	<input type="hidden" name="ptraoid" id="ptraoid" value="<?php echo $result['ptraoid'] ?>" />
	<div class="modulo_titulo">Dados do Novo Cliente Titular</div>
	<div class="modulo_conteudo">
		
        <?php require_once '_msgPrincipal.php'; ?>


    

        <?php
	if($retorno['tipo_pessoa'] == 'F') {
	require_once 'novo_cliente_fisico.php';
	 }else {
	
	require_once 'novo_cliente_juridico.php';
	 }
	?>
      
	

	<div class="separador"></div>
	<div><?php require_once 'endereco.php'; ?></div>

	<div class="separador"></div>
	<div><?php require_once 'endereco_cobranca.php'; ?></div>

	<div class="separador"></div>
	<div><?php require_once 'formas_pagamento.php'; ?></div>

	<div class="separador"></div>
	<div><?php require_once 'pessoas_autorizadas.php'; ?></div>
	
	<div class="separador"></div>
	<div><?php require_once 'contato_emergencia.php'; ?></div>
	
	<div class="separador"></div>
	<div><?php require_once 'contato_instalacao_assistencia.php';?></div>
	
		<div class="separador"></div>
	<div><?php require_once 'anexar_arquivo.php';?></div>
	
	<div class="separador"></div>
	<div><?php require_once 'anexar_carta_transferencia.php';?></div>

	</div>
	
	<div class="bloco_acoes">
	<input type="button" id="aprova_credito"
		value="<?php echo utf8_encode('Salvar');?>" /> <input
		type="button" id="reprova_credito"
		value="<?php  echo utf8_encode('Salvar e Finalizar');?>" /> <input
		type="button" id="aprova_divida"
		value="<?php echo 'Cancelar Solicitação';?>" /> <input
		type="button" id="reprova_divida"
		value="<?php echo utf8_encode('Voltar');?>" />

	</div>
</form>
<?php } ?>
