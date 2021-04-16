<style>
	.blocos {display: table-cell;}
	.blocos label {
		clear: both;
		display: block;
		padding-top: 10px;
		padding-bottom: 10px;
	}

	.blocos select {
		height: 140px;
		margin: 3px 10px;
	}
	
	#blocoSegoid, #blocoEmbuf  {		
		display: table-cell;	
	}

	#blocoSegoid {width: 370px; }
	#blocoEmbuf {width: 122px;}

	/*td {border: 1px solid red;}*/
</style>

<?php 
	cabecalho();	
?>
<center><span id="div_msg" class="msg"><?php echo $mensagem;?></span></center>
<center>
<form name="form" id="form" method="post" action="">
<input type="hidden" name="acao" id="acao" value="pesquisar">
<table width="98%" class="tableMoldura">
	<tr class="tableTitulo">
    	<td>
    		<h1>Embarcadores por Segmentos, Estados, GRs e Transportadoras</h1>
    	</td>
	</tr>
	<tr>
    	<td align="center"><br>    		
			<table class="tableMoldura" width="98%">
				<tr class="tableSubTitulo">
					<td colspan="3"><h2>Dados para pesquisa</h2></td>
				</tr>
				<tr>
					<td>
						<div id="bloco01" class="blocos">
							<div id="blocoSegoid">
								<?php echo $this->formPesquisaObj->desenhaCampo('segoid[]', array('open_tr' => false,)); ?>
							</div>
							<div id="blocoEmbuf">
								<?php echo $this->formPesquisaObj->desenhaCampo('embuf[]', array('open_tr' => false)); ?>
							</div>
						</div>
						<div id="bloco02" class="blocos">
							<?php echo $this->formPesquisaObj->desenhaCampo('emboid[]', array('open_tr' => false)); ?>
						</div>
						<div style="clear: both;"></div>
						<div id="bloco03" class="blocos">
							<?php echo $this->formPesquisaObj->desenhaCampo('geroid[]', array('open_tr' => false)); ?>
						</div>
						<div id="bloco04" class="blocos">
							<?php echo $this->formPesquisaObj->desenhaCampo('traoid[]', array('open_tr' => false)); ?>
						</div>
					</td>
				</tr>
				<tr class="tableRodapeModelo1">
					<td colspan="3" align="center">
						<?php echo $this->formPesquisaObj->desenhaCampo('pesquisar', array('open_tr' => false)); ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
</center>

<?php
	if($acao == 'pesquisar'): ?>
	<div align="center">
		<?php include _MODULEDIR_.'Relatorio/View/rel_embarcadores_seg/result.php'; ?>
	</div>
<?php endif; ?>
<script type="text/javascript" src="modulos/web/js/rel_embarcadores_seg.js"></script>
<?php 
include ("lib/rodape.php");