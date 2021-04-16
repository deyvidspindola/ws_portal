<?php require_once '_abas.php'; ?> 

<div class="bloco_titulo">Dados Pesquisa</div>

<form id="form" name="form" action="fin_nfe_kernel.php?acao=ocorrencias" method="post">
	<input type="hidden" name="action" id="form_acao" value="pesquisar">
	<div class="bloco_conteudo">
		<div class="formulario">

			<?php if($this->acao == 'pesquisaOcorrencias'): ?>
			<?php if(!empty($this->msgInfo)): ?><div class="mensagem info"><?php echo $this->msgInfo; ?></div><?php endif; ?>
			<?php if(!empty($this->msgAlerta)): ?><div class="mensagem alerta"><?php echo $this->msgAlerta; ?></div><?php endif; ?>
			<?php if(!empty($this->msgSucesso)): ?><div class="mensagem sucesso"><?php echo $this->msgSucesso; ?></div><?php endif; ?>
			<?php if(!empty($this->msgErro)): ?><div class="mensagem erro"><?php echo $this->msgErro; ?></div><?php endif; ?>
			<?php endif; ?>

			<div class="campo maior">
				<label for="numero_nf">Número NF</label>
				<input type="text" name="numero_nf" id="numero_nf" value="<?php echo !empty($this->numeroNf) ? $this->numeroNf : null; ?>" class="campo" />
			</div>

			<div class="campo data">
				<label for="periodo_faturamento_inicial">Data Faturamento</label>
				<input class="campo"  type="text" name="periodo_faturamento_inicial" id="periodo_faturamento_inicial" maxlength="10" value="<?php echo !empty($this->periodoFaturamentoInicial) ? $this->periodoFaturamentoInicial : null; ?>" />
			</div>
			<div style="margin-top: 23px !important;" class="campo label-periodo">a</div>
			<div class="campo data">
				<label>&nbsp;</label>
				<input  class="campo"  type="text" name="periodo_faturamento_final" maxlength="10" value= "<?php echo !empty($this->periodoFaturamentoFinal) ? $this->periodoFaturamentoFinal : null; ?>" />
			</div>
			<div class="clear"></div>

			<div class="campo maior">
				<label for="cliente_nome">Cliente</label>
				<input type="text" name="cliente_nome" id="cliente_nome" value="<?php echo !empty($this->clienteNome) ? $this->clienteNome : null; ?>" class="campo" />
			</div>
			<div class="clear"></div>

			<div class="campo maior">
				<label for="numero_cpf_cnpj">CPF/CNPJ</label>
				<input type="text" name="numero_cpf_cnpj" id="numero_cpf_cnpj" value="<?php echo !empty($this->numeroCpfCnpj) ? $this->numeroCpfCnpj : null; ?>" class="campo" />
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<div class="bloco_acoes">
		<button type="submit">Pesquisar</button>
	</div>

<div class="separador"></div>
<?php if($this->acao == 'pesquisaOcorrencias'): ?>
<div class="bloco_titulo">Notas Fiscais</div>
<div class="bloco_conteudo">
	<div class="listagem">

			<table>
				<thead>
					<tr>
						<th style="text-align: center;"></th>
						<th style="text-align: center;">NF</th>
						<th style="text-align: center;">Série</th>
						<th style="text-align: center;">Cliente</th>
						<th style="text-align: center;">CPF/CNPJ</th>
						<th style="text-align: center;">Valor</th>
						<th style="text-align: center;">Data Faturamento</th>
						<th style="text-align: center;">Data Transmissão RPS</th>
						<th style="text-align: center;">Data Retorno RPS</th>
						<th style="text-align: center;">Ocorrência</th>
					</tr>
				</thead>
				<?php if(!empty($this->notas)): ?>
				<tbody>
					<?php foreach($this->notas as $nf): ?>
					<tr class="par">
						<td align="center">
							<input type="checkbox" class="checkbox_nf" name="notas_fiscais[]" value="<?php echo $nf['id_nf']; ?>">
						</td>
						<td style="text-align: center;"><?php echo $nf['numero_nf']; ?></td>
						<td style="text-align: center;"><?php echo $nf['serie_nf']; ?></td>
						<td><?php echo $nf['nome_cliente']; ?></td>
						<td style="text-align: center;"><?php echo $nf['cpf_cnpj']; ?></td>
						<td style="text-align: center;"><?php echo number_format($nf['valor_nf'], 2, ",", "."); ?></td>
						<td style="text-align: center;"><?php echo empty($nf['data_faturamento']) ? '-' : date('d/m/Y', strtotime($nf['data_faturamento'])); ?></td>
						<td style="text-align: center;"><?php echo empty($nf['data_transmissao_rps']) ? '-' : date('d/m/Y', strtotime($nf['data_transmissao_rps'])); ?></td>
						<td style="text-align: center;"><?php echo empty($nf['data_retorno_rps']) ? '-' : date('d/m/Y', strtotime($nf['data_retorno_rps'])); ?></td>
						<td style="text-align: left;"><?php echo $nf['ocorrencias']; ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
				<?php endif; ?>

				<tfoot>
					<?php if(!empty($this->notas)): ?>
					<tr class="tableRodapeModelo3">
						<td align="center">
							<input type="checkbox" name="selecionarTodasNFs" id="selecionarTodasNFs">
						</td>
						<td colspan="10" align="center">
							<input type="submit" id="liberar_rps" value="Liberar RPS para reenvio" class="botao" disabled style="width:200px;">
					<tr><td colspan="11" style="text-align: center;"><?php echo count($this->notas); ?> registro(s) encontrado(s)</td></tr>
					<?php else: ?>
					<tr><td colspan="11" style="text-align: center;">Nenhum resultado encontrado.</td></tr>
					<?php endif; ?>
				</tfoot>
			</table>
		</form>

	</div>

</div>
<?php endif; ?>
