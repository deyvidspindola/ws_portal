<div class="separador"></div>
<div id="resultados_relatorio" class="<?php echo ($this->view->resultados) ? '' : 'invisivel'; ?>">
	<?php echo (trim($_POST['sub_acao'])!='gerarPdf') ? '<div class="bloco_titulo">Acionamentos de Apoio Analítico</div>' : "";  ?>

<div class="bloco_conteudo">
	<div class="listagem">
			<table <?php echo (trim($_POST['sub_acao'])=='gerarPdf') ? 'border="1"' : "";  ?>>
				<thead>
					<tr>
						<th class="menor centro">Data Comunicação</th>
						<th class="menor centro">Placa</th>
						<th class="menor centro">Classe</th>
						<th class="menor centro">Projeto</th>
						<th class="medio centro">Cliente</th>

						<?php if (isset($this->view->parametros->filtrar_exibir_endereco) && trim($this->view->parametros->filtrar_exibir_endereco)== "1" ) : ?>

						<th class="medio centro">End. Cliente</th>

						<?php endif; ?>

						<th class="menor centro">DDD - Fone</th>
						<th class="medio centro">Seguradora</th>
						<th class="menor centro">Tipo Termo</th>
						<th class="menor centro">Motivo</th>
						<th class="medio centro">Forma Notificação</th>
						<th class="medio centro">Status do Equipamento</th>
						<th class="menor centro">Atendente</th>
						<th class="maior centro">Nº BO</th>
						<th class="menor centro">Tempo Aviso</th>
						<th class="menor centro">Status</th>
						<th class="menor centro">Concluído</th>
						<th class="menor centro">Valor Veiculo</th>
						<th class="menor centro">Tipo de Carga</th>
						<th class="menor centro">Tipo de Proposta</th>
					</tr>
				</thead>
				<tbody>

				<?php foreach($this->view->dados as $ocorrencia): ?>
					<?php $rowClass = ($rowClass == "impar") ? "par" : "impar"; ?>
					<tr class="<?php echo $rowClass; ?>">
						<td class="centro">
							<?php echo $ocorrencia->data_comunicacao; ?>
						</td>
						<td class="esquerda">
                            <?php
                            if($this->isUsuarioComercial()) {
                            	echo "<a href=\"javascript:abre_carta('{$ocorrencia->ocococooid}')\">
                            			{$ocorrencia->ococplaca}
                            		  </a>";
                            } else {
                            	echo $ocorrencia->ococplaca;
                            }
                            ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococecclasse_termo; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococequipamento_projeto; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococcliente; ?>
						</td>

						<?php if (isset($this->view->parametros->filtrar_exibir_endereco) && trim($this->view->parametros->filtrar_exibir_endereco)== "1" ) : ?>

						<td class="esquerda">
							<?php echo trim($ocorrencia->ococcidade) . '-' . trim($ocorrencia->ococuf); ?>
						</td>

						<?php endif; ?>

						<td class="direita">
							<?php echo trim($ocorrencia->ococddd) . '-' . trim($ocorrencia->ococfone); ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococseguradora; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococtipo_contrato; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococmotivo; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ocococorrencia_forma_notificacao; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ocococorrencia_motivo_equip_sem_contato; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococatendente; ?>
						</td>
						<td class="direita">
							<?php echo $ocorrencia->ococnumero_bo; ?>
						</td>
						<td class="centro">
							<?php
								$tempoAviso = explode('-', $ocorrencia->ococtempo_aviso);

								if (intval($tempoAviso['0']) > 0) {
									$tempoAviso = $tempoAviso['0'] . ' dia(s) ' . $tempoAviso['1'];
								} else {
									$tempoAviso = $tempoAviso['1'];
								}

							 ?>
							<?php echo $tempoAviso; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococstatus; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococconcluido; ?>
						</td>
						<td class="direita">
							R$ <?php echo number_format($ocorrencia->ococvalor_veiculo, 2, ',', '.'); ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococtipo_carga; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococsub_tipo_proposta; ?>
						</td>
					</tr>
				<?php endForEach;?>
				</tbody>
				<tfoot>
					<tr>
						<?php if (count($this->view->dados) == 1): ?>
							<td colspan="<?php echo isset($this->view->parametros->filtrar_exibir_endereco) && trim($this->view->parametros->filtrar_exibir_endereco) == '1' ? '20' : '19'  ?>"> 1 registro encontrado.</td>
						<?php else: ?>
							<td colspan="<?php echo isset($this->view->parametros->filtrar_exibir_endereco) && trim($this->view->parametros->filtrar_exibir_endereco) == '1' ? '20' : '19'  ?>"> <?php echo count($this->view->dados); ?> registros encontrados.</td>
						<?php endif; ?>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<?php if (trim($_POST['sub_acao'])!='gerarPdf'): ?>
	<div class="bloco_acoes">
		<button type="button" id="gerar_pdf">Gerar PDF</button>
		<button type="button" id="btn_gerar_xls">Gerar XLS</button>
		<button type="button" onclick="javascript:window.print();">Imprimir</button>
	</div>
	<?php endif; ?>

</div>

<?php if (trim($_POST['sub_acao'])!='gerarPdf'): ?>
   <?php require_once _MODULEDIR_ . "Relatorio/View/rel_ocorrencia_novo_irv_congelado/bloco_csv.php"; ?>
<?php endif; ?>
