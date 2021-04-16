
<?php 
	echo $this->form($acao); ?>
<script>
<?php if($mensagem != ''): ?>
	// exibe mensagem de erro, caso exista
    criarDiv('mess', '<table height=\"32\" width=\"100%\" valign=\"middle\"><tr onclick=\"removeDiv(\'mess\');\"><td class="\msg\" width=\"96%\" heigth=\"100%\">&nbsp;<font color=\"#CD0000\"><?php echo $mensagem;?></font></td><td width=\"4%\"><img width=\"15\" height=\"15\" src=\"images/X.jpg\"></img></td></tr></table>', '100%', '34', '0', '0');
    alinhaDiv('mess');
          
    id_interval = setInterval("alinhaDiv('mess')",500);
                    
    fade(0,'mess',80);
<?php endif; ?>
</script>
<!-- JS Custom -->
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>
<script>

	// arrays de valores para adicionar e excluir nas combos
	var gerRiscoSelArrAdd      = [<?php echo $this->gerRiscoSelAdd?>];
	var gerRiscoSelArrAddLabel = <?php echo json_encode($this->gerRiscoSelAddLabel)?>;		
	var gerRiscoSelArrRem      = [];

	var transpSelArrAdd      = [<?php echo $this->transpSelAdd?>];
	var transpSelArrAddLabel = <?php echo json_encode($this->transpSelAddLabel)?>;
	var transpSelArrRem      = [];



</script>
<script type="text/javascript" src="modulos/web/js/cad_embarcadores.js"></script>
<!--<span id="div_msg" class="msg"><?php echo $mensagem;?></span>-->
<style>
	.botaoAddContainer {
		padding: 15px;
	}
</style>
<?php
include ("lib/rodape.php");