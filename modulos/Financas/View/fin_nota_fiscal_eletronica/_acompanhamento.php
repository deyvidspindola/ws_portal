<?php require_once '_abas.php'; ?>



<!--Inicio - ORGMKTOTVS-826 - ERP - Alterar tela de geração de remessa para a Prefeitura de Barueri-->
<?php if(INTEGRACAO_TOTVS_ATIVA == false): ?>

<div class="bloco_titulo">Arquivo de Importação Notas Fiscais Eletrônicas</div>

<form id="form" name="form" action="fin_nfe_kernel.php?acao=acompanhamento" method="post" enctype="multipart/form-data">
	<input type="hidden" name="acao" id="form_acao" value="processarRetornoSucessoArquivoRPS">
	<div class="bloco_conteudo">
		<div class="formulario">

			<?php
				if ($this->action == 'processarRetornoSucessoArquivoRPS') {
					if (!empty($this->mensagemRetornoSucesso)) {
						echo '<div class="mensagem sucesso">'.$this->mensagemRetornoSucesso.'</div>';
					}
					if (!empty($this->mensagemRetornoErro)) {
						echo '<div class="mensagem erro">'.$this->mensagemRetornoErro.'</div>';
					}
					if (!empty($this->mensagemRetornoInformativa)) {
						echo '<div class="mensagem info">'.$this->mensagemRetornoInformativa.'</div>';
					}
				}
			?>

			<div class="campo maior">
				<label for="arq_retorno_sucesso">Arquivo</label>
				<input type="file" name="arq_retorno" id="arq_retorno_sucesso">
			</div>

			<div class="clear"></div>
		</div>
	</div>
	<div class="bloco_acoes">
		<button type="submit">Processar</button>
	</div>
</form>



<div class="separador"></div>

<?php endif; ?>
<!--Fim - ORGMKTOTVS-826 - ERP - Alterar tela de geração de remessa para a Prefeitura de Barueri-->


<div class="bloco_titulo">Arquivo de Importação RPS com Erros</div>

<form id="form" name="form" action="fin_nfe_kernel.php?acao=acompanhamento" method="post" enctype="multipart/form-data">
	<input type="hidden" name="acao" id="form_acao" value="processarRetornoErroArquivoRPS">
	<div class="bloco_conteudo">
		
		<div class="formulario">

		<?php
			if ($this->action == 'processarRetornoErroArquivoRPS') {
				if (!empty($this->mensagemRetornoSucesso)) {

					echo '<div class="mensagem sucesso">'.$this->mensagemRetornoSucesso.'</div>';
				}
				if (!empty($this->mensagemRetornoErro)) {

					echo '<div class="mensagem erro">'.$this->mensagemRetornoErro.'</div>';
				}
				if (!empty($this->mensagemRetornoInformativa)) {

					echo '<div class="mensagem info">'.$this->mensagemRetornoInformativa.'</div>';
				}
			}
		?>

			<div class="campo maior">
				<label for="arq_retorno_erro">Arquivo</label>
				<input type="file" name="arq_retorno_erro" id="arq_retorno_erro">
			</div>

			<div class="clear"></div>
		</div>
	</div>
	<div class="bloco_acoes">
		<button type="submit">Processar</button>
	</div>
</form>


<!--Inicio - ORGMKTOTVS-826 - ERP - Alterar tela de geração de remessa para a Prefeitura de Barueri-->

<input type="hidden" name="INTEGRACAO_TOTVS_ATIVA" id="INTEGRACAO_TOTVS_ATIVA" value="<?php echo (INTEGRACAO_TOTVS_ATIVA); ?>">



<?php if(INTEGRACAO_TOTVS_ATIVA == true): ?>
<div class="hidden" id="processarRetornoErroArquivoRPS">


<div class="separador"></div>

<?php if($this->action == 'processarRetornoErroArquivoRPS'): ?>

<div class="bloco_titulo" >Erros Notas Fiscais</div>
<div class="bloco_conteudo">
	<div class="listagem">

			<table>
				<thead>
					<tr>
						<th style="text-align: center;"></th>
						<th style="text-align: center;">NF</th>
						<th style="text-align: center;">Série</th>
						<th style="text-align: center;">Cod/Cli</th>
						<th style="text-align: center;">Cliente</th>
						<th style="text-align: center;">CPF/CNPJ</th>
						<th style="text-align: center;">Valor</th>
						<th style="text-align: center;">Ocorrência</th>
					</tr>
				</thead>
				<?php if(!empty($this->notas_file)): ?>
				<tbody>
					<?php foreach($this->notas_file as $nf): ?>
					<tr class="par">
						<td align="center">
						</td>
						<td style="text-align: center;"><?php echo $nf['numeroRPS']; ?></td>
						<td style="text-align: center;"><?php echo $nf['serieNfe']; ?></td>
						<td style="text-align: center;"><?php echo $nf['clioid']; ?></td>
						<td><?php echo $nf['nomeTomador']; ?></td>
						<td style="text-align: center;"><?php echo $nf['numeroDocumento']; ?></td>
						<td style="text-align: center;"><?php echo number_format($nf['valorFatura'], 2, ",", "."); ?></td>
						<td style="text-align: left;"><?php echo $nf['ocorrencias']; ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
				<?php endif; ?>

				<tfoot>
					<?php if(!empty($this->notas_file)): ?>

					<tr class="tableRodapeModelo3">
						<td align="center">
						</td>
						<td colspan="10" align="center">
						<?php $acao = 'index'; ?>
							<input type="submit" id="fechar_rps" value="Fechar" class="botao">
					<tr><td colspan="11" style="text-align: center;"><?php echo count($this->notas_file); ?> registro(s) encontrado(s)</td></tr>
					<?php else: ?>
					<tr><td colspan="11" style="text-align: center;">Nenhum resultado encontrado.</td></tr>
					<?php endif; ?>
				</tfoot>
			</table>
	</div>

</div>
<?php endif; ?>
</div>
<?php endif; ?>
<!--Fim - ORGMKTOTVS-826 - ERP - Alterar tela de geração de remessa para a Prefeitura de Barueri-->