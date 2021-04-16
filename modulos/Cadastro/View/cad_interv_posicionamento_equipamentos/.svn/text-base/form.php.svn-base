
<?php 
	$this->form($acao); ?>

<!-- JS Custom -->
<script>
	jQuery('#not_id_embdt_alteracao').attr('disabled', true);
	var exibeCabecalhoListas = <?php echo $this->exibeCabecalhoListas ?>;
</script>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>
<script type="text/javascript" src="modulos/web/js/cad_embarcadores.js"></script>
<script>

	// arrays de valores para adicionar e excluir nas combos (listas de opções selecionadas)
	var gerRiscoSelArrAdd      = [<?php echo $this->gerRiscoSelAdd?>];
	var gerRiscoSelArrAddLabel = <?php echo json_encode($this->gerRiscoSelAddLabel)?>;		
	var gerRiscoSelArrRem      = [];

	var transpSelArrAdd      = [<?php echo $this->transpSelAdd?>];
	var transpSelArrAddLabel = <?php echo json_encode($this->transpSelAddLabel)?>;
	var transpSelArrRem      = [];

	<?php if($mensagem != ''): ?>
	exibeAlertaMsg('<?php echo $mensagem?>');
	<?php endif; ?>
</script>

<!--<span id="div_msg" class="msg"><?php echo $mensagem;?></span>-->
<style>
	.botaoAddContainer {
		padding: 15px;
	}
	#not_id_embdt_alteracao {
		background: #CECECE;
		width: 122px;
	}
	.botao {width: 100px;}
	
	#span_id_embuf, #id_lbl_embcep {float: left; line-height: 25px; height: 25px;}
</style>
<?php  
include ("lib/rodape.php");