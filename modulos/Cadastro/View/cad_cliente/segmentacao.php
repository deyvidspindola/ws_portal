<div class="bloco_titulo">Segmentação</div>
<form action="" name="cad_cliente_segmentacao" id="cad_cliente_segmentacao" method="post">
	<input type="hidden" name="acao" id="acao" value="setSegmentacao" />
	<input type="hidden" name="clioid" id="clioid" value="<?php echo $this->clioid; ?>" />
	<div class="bloco_conteudo">
		<div class="conteudo">
<?php
if ($this->clioid == '') {
	$this->clioid = 0;
}
$tiposSegmentacao = array(
			'collection_score' => array('collectionScore', 'Collection Score'),
			'ramo_de_atuacao' => array('ramoAtuacao', 'Ramo de Atuação'),
			'tamanho_de_mercado' => array('tamanhoMercado', 'Tamanho de Mercado'),
			'tipo_de_atendimento' => array('tipoAtendimento', 'Tipo de Atendimento')
			);
foreach ($tiposSegmentacao as $chave => $valor) {
	?>
<div class="campo maior">
		<label for="<?php echo $valor[0] ?>"><?php echo $valor[1] ?>:</label>
		<select name="clstsgoid[]" id="<?php echo $valor[0] ?>">
			<option value="0">Selecione</option>
			<?php 
			$opcoes = $this->getSegmentacao($this->clioid, $chave);
			foreach ($opcoes as $opcoesChave => $opcoesValor) { ?>
				<option value="<?php echo $opcoesChave?>" <?php echo $opcoesValor[1] ?>><?php echo $opcoesValor[0] ?></option>
			<?php }	?>
		</select>
</div>
<div class="clear"></div>
<?php
	}
?>		
		</div>
	</div>
	<div class="bloco_acoes">
		<button id="buttonConfirmarSegmentacao" name="buttonConfirmarSegmentacao" value="Confirmar" type="submit" class="validacao">Confirmar</button>
		<button type="button" value="Voltar" id="buttonVoltar" name="buttonVoltar" onclick="window.location.href='<?php echo str_replace('/', '', strrchr($_SERVER['SCRIPT_NAME'], '/'));?>'">Voltar</button>
	    <? if($this->retCliente!=""){ ?>
    		<button type="button" value="Voltar" id="buttonVoltar" name="buttonVoltar" onclick="window.location.href='<?=trata_retorno($this->retCliente,$clioid)?>'">Retornar ao Contrato</button>
        <? } ?>
	</div>
</form>
<div class="separador"></div>