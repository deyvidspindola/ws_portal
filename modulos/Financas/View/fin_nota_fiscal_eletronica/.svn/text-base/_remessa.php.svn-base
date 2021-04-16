<?php require_once '_abas.php'; ?>
<div class="bloco_titulo">Dados Pesquisa</div>

<form id="form" name="form" action="fin_nfe_kernel.php?acao=pesquisar" method="post">
	<input type="hidden" name="acao" id="form_acao" value="pesquisar">
	<div class="bloco_conteudo">
		<div class="formulario">

			<?php if($this->acao == 'pesquisar'): ?>
			<?php if(!empty($this->msgInfo)): ?><div class="mensagem info"><?php echo $this->msgInfo; ?></div><?php endif; ?>
			<?php if(!empty($this->msgAlerta)): ?><div class="mensagem alerta"><?php echo $this->msgAlerta; ?></div><?php endif; ?>
			<?php if(!empty($this->msgSucesso)): ?><div class="mensagem sucesso"><?php echo $this->msgSucesso; ?></div><?php endif; ?>
			<?php if(!empty($this->msgErro)): ?><div class="mensagem erro"><?php echo $this->msgErro; ?></div><?php endif; ?>
			<?php endif; ?>

			<div class="campo data">
				<label for="periodo_faturamento_inicial">Período Faturamento</label>
				<input class="campo"  type="text" name="periodo_faturamento_inicial" id="periodo_faturamento_inicial" maxlength="10" value="<?php echo !empty($this->periodoFaturamentoInicial) ? $this->periodoFaturamentoInicial : null; ?>" />
			</div>
			<div style="margin-top: 23px !important;" class="campo label-periodo">a</div>
			<div class="campo data">
				<label>&nbsp;</label>
				<input  class="campo"  type="text" name="periodo_faturamento_final" maxlength="10" value= "<?php echo !empty($this->periodoFaturamentoFinal) ? $this->periodoFaturamentoFinal : null; ?>" />
			</div>

			<div class="clear"></div>

			<div class="campo data">
				<label for="periodo_cancelamento_inicial">Período Cancelamento</label>
				<input class="campo"  type="text" name="periodo_cancelamento_inicial" id="periodo_cancelamento_inicial" maxlength="10" value="<?php echo !empty($this->periodoCancelamentoInicial) ? $this->periodoCancelamentoInicial : null; ?>" />
			</div>
			<div style="margin-top: 23px !important;" class="campo label-periodo">&nbsp;<br>a</div>
			<div class="campo data">
				<label>&nbsp;<br>&nbsp;</label>
				<input  class="campo"  type="text" name="periodo_cancelamento_final" maxlength="10" value= "<?php echo !empty($this->periodoCancelamentoFinal) ? $this->periodoCancelamentoFinal : null; ?>" />
			</div>

			<div class="clear"></div>

			<div class="campo menor">
				<label for="intervalo_notas_inicial">Intervalo de Notas</label>
				<input type="text" name="intervalo_notas_inicial" id="intervalo_notas_inicial" value="<?php echo !empty($this->intervaloNotasInicial) ? $this->intervaloNotasInicial : null; ?>" class="campo" />
			</div>
			<div style="margin-top: 23px !important;" class="campo label-periodo">a</div>
			<div class="campo menor">
				<label>&nbsp;</label>
				<input type="text" name="intervalo_notas_final" value="<?php echo !empty($this->intervaloNotasFinal) ? $this->intervaloNotasFinal : null; ?>" class="campo" />
			</div>

			<div class="campo menor"></div>

<!-- 			<div class="campo maior">
				<label for="situacao_rps">Situação da RPS</label>
				<select name="situacao_rps" id="situacao_rps">
					<option value="">Selecione</option>
					<option value="1" <?php echo !empty($this->situacaoRps) && $this->situacaoRps == "1" ? 'selected' : null; ?>>NF sem erro</option>
					<option value="2" <?php echo !empty($this->situacaoRps) && $this->situacaoRps == "2" ? 'selected' : null; ?>>NF com erro</option>
					<option value="3" <?php echo !empty($this->situacaoRps) && $this->situacaoRps == "3" ? 'selected' : null; ?>>NF gerada</option>
				</select>
			</div> -->

			<div class="clear"></div>

			<div class="campo maior">
				<label for="numero_cpf_cnpj">CPF/CNPJ</label>
				<input type="text" name="numero_cpf_cnpj" id="numero_cpf_cnpj" value="<?php echo !empty($this->numeroCpfCnpj) ? $this->numeroCpfCnpj : null; ?>" class="campo" />
			</div>

			<div class="campo maior">
				<label for="numero_nfe">Número NF-e</label>
				<input type="text" name="numero_nfe" id="numero_nfe" value="<?php echo !empty($this->numeroNfe) ? $this->numeroNfe : null; ?>" class="campo" />
			</div>

			<div class="clear"></div>

			<div class="campo maior">
				<label for="numero_resultados">Resultados (Limit)</label>
				<input type="text" name="numero_resultados" id="numero_resultados" value="<?php echo !empty($this->numeroResultados) ? $this->numeroResultados : null; ?>" class="campo" />
			</div>

			<div class="campo maior">
				<label for="numero_nf">Número NF</label>
				<input type="text" name="numero_nf" id="numero_nf" value="<?php echo !empty($this->numeroNf) ? $this->numeroNf : null; ?>" class="campo" />
			</div>

			<div class="clear"></div>

			<fieldset class="maior">
				<legend>Somente notas não enviadas</legend>
				<input type="radio" name="somente_nao_enviadas" value="1" <?php echo !isset($this->somenteNaoEnviadas) || $this->somenteNaoEnviadas === 1  ? 'checked' : ''; ?> />
				<label for="somente_nao_enviadas_sim">Sim</label>
				<input type="radio" name="somente_nao_enviadas" value="0" <?php echo $this->somenteNaoEnviadas === 0 ? 'checked' : ''; ?> />
				<label for="somente_nao_enviadas_nao">Não</label>
			</fieldset>

			<fieldset class="maior">
				<legend>Layout</legend>
				<input type="radio" name="layout" value="monitoramento" <?php echo $this->layout == 'monitoramento' ? 'checked' : ''; ?> />
				<label for="layout">Monitoramento</label>
				<input type="radio" name="layout" value="unificado" <?php echo !isset($this->layout) || $this->layout === 'unificado'  ? 'checked' : ''; ?> />
				<label for="layout">Unificado</label>
			</fieldset>

			<div class="clear"></div>
		</div>
	</div>
	<div class="bloco_acoes">
		<button type="submit">Pesquisar</button>
	</div>
</form>

<div class="separador"></div>
<?php if($this->acao == 'pesquisar'): ?>
<div class="bloco_titulo">Notas Fiscais</div>
<div class="bloco_conteudo">
	<div class="listagem">

		<form id="form_listagem" name="form_listagem" action="fin_nfe_kernel.php?acao=gerarRPS" method="post">
			<table>
				<?php #if($listContratos && count($listContratos)): ?>
				<thead>
					<tr>
						<th style="text-align: center;"></th>
						<th style="text-align: center;">NF</th>
						<th style="text-align: center;">Série</th>
						<th style="text-align: center;">Cliente</th>
						<th style="text-align: center;">Valor</th>
						<th style="text-align: center;">Data Faturamento</th>
						<th style="text-align: center;">Data Cancelamento</th>
						<th style="text-align: center;">NF-e</th>
						<th style="text-align: center;">Data Transmissão RPS</th>
						<th style="text-align: center;">Data Retorno RPS</th>
						<!-- <th style="text-align: center;">Situação de Envio</th> -->
					</tr>
				</thead>
				<?php if(!empty($this->notas)): ?>
				<tbody>
					<?php foreach($this->notas as $nf): ?>
					<tr class="par">
						

						<td align="center">
						<!--Inicio - ORGMKTOTVS-826 - ERP - Alterar tela de geração de remessa para a Prefeitura de Barueri-->
						<?php if(INTEGRACAO_TOTVS_ATIVA == false): ?>
							<input type="checkbox" class="checkbox_nf" name="notas_fiscais[]" value="<?php echo $nf['id_nf']; ?>">
						<?php else: ?>
							&nbsp;&nbsp;
						<?php endif; ?>
						<!--Fim - ORGMKTOTVS-826 - ERP - Alterar tela de geração de remessa para a Prefeitura de Barueri-->
						</td>


						<td style="text-align: center;"><?php echo $nf['numero_nf']; ?></td>
						<td style="text-align: center;"><?php echo $nf['serie_nf']; ?></td>
						<td><?php echo $nf['nome_cliente']; ?></td>
						<td style="text-align: center;"><?php echo number_format($nf['valor_nf'], 2, ",", "."); ?></td>
						<td style="text-align: center;"><?php echo empty($nf['data_faturamento']) ? '-' : date('d/m/Y', strtotime($nf['data_faturamento'])); ?></td>
						<td style="text-align: center;"><?php echo empty($nf['data_cancelamento']) ? '-' : date('d/m/Y', strtotime($nf['data_cancelamento'])); ?></td>
						<td style="text-align: center;"><?php echo isset($nf['numero_nfe']) ? "<a href=".$nf['link_nfe']." target='blank'>".$nf['numero_nfe']."</a>" : ""; ?></td>
						<td style="text-align: center;"><?php echo empty($nf['data_transmissao_rps']) ? '-' : date('d/m/Y', strtotime($nf['data_transmissao_rps'])); ?></td>
						<td style="text-align: center;"><?php echo empty($nf['data_retorno_rps']) ? '-' : date('d/m/Y', strtotime($nf['data_retorno_rps'])); ?></td>
						<!-- <td></td> -->
					</tr>
					<?php endforeach; ?>
				</tbody>
				<?php endif; ?>

				<tfoot>

					<?php if(!empty($this->notas)): ?>

					<tr class="tableRodapeModelo3">
						

						<td align="center">
							<!--Inicio - ORGMKTOTVS-826 - ERP - Alterar tela de geração de remessa para a Prefeitura de Barueri-->
							<?php if(INTEGRACAO_TOTVS_ATIVA == false): ?>
								<input type="checkbox" name="selecionarTodasNFs" id="selecionarTodasNFs">
								<?php else: ?>
									&nbsp;&nbsp;
								<?php endif; ?>
							<!--Fim - ORGMKTOTVS-826 - ERP - Alterar tela de geração de remessa para a Prefeitura de Barueri-->
							</td>


						<td colspan="9" align="center">


							<!--Inicio - ORGMKTOTVS-826 - ERP - Alterar tela de geração de remessa para a Prefeitura de Barueri-->
							<?php if(INTEGRACAO_TOTVS_ATIVA == false): ?>

								<label for="nfldt_emissao" style="display: inline-block"><b>Data de Emissão: </b></label>
								<input id="nfldt_emissao" name="nfldt_emissao" value="" type="text"  size="10" maxlength="10" value=""  onkeyup="formatar(this,'@@/@@/@@@@');" onblur="revalidar(this,'@@/@@/@@@@','data');"/>
								<img src="images/calendar_cal.gif" border="0" id="icone_data_emissao" align="absmiddle" alt="Calendário">
							<?php endif; ?>
							<!--Fim - ORGMKTOTVS-826 - ERP - Alterar tela de geração de remessa para a Prefeitura de Barueri-->


							&nbsp;&nbsp;(Última data de emissão: <?php echo $this->ultimaDataEmissao; ?>)
						</td>
					</tr>


					<!--Inicio - ORGMKTOTVS-826 - ERP - Alterar tela de geração de remessa para a Prefeitura de Barueri-->
					<?php if(INTEGRACAO_TOTVS_ATIVA == false): ?>

						<tr class="tableRodapeModelo3">
							<td colspan="100%" align="center">
								<input type="submit" id="gerar_rps" value="Gerar RPS" class="botao" style="width:100px;">
							</td>
						</tr>
					<?php endif; ?>
					<!--Fim - ORGMKTOTVS-826 - ERP - Alterar tela de geração de remessa para a Prefeitura de Barueri-->


					<tr><td colspan="10" style="text-align: center;"><?php echo count($this->notas); ?> registro(s) encontrado(s)</td></tr>
					<?php else: ?>
					<tr><td colspan="10" style="text-align: center;">Nenhum resultado encontrado.</td></tr>
					<?php endif; ?>
					<!-- <tr><td colspan="10" style="text-align: center;">0000 com Erros / 0000 Aguardando Envio</td></tr> -->
				</tfoot>
				<?php #else: ?>
				<!-- <thead><tr><th style="text-align: center;">Nenhum contrato encontrado.</th></tr></thead> -->
				<?php #endif ?>
			</table>
		</form>

	</div>

</div>
<?php endif; ?>

<div id="download_wrapper" style="display:none;">
	<div class="separador"></div>
	<div class="bloco_titulo">Download RPS</div>
	<div class="bloco_conteudo">
		<div class="carregando"></div>
		<div class="separador"></div>
		<div class="mensagem"></div>
		<div class="download_arquivo" align="center" style="display: none;">
			<a href="#"><img src="images/icones/t3/caixa2.jpg"><br><br><span class="nome_arquivo"></span></a><br>
		</div>
		<div class="separador"></div>
	</div>
</div>