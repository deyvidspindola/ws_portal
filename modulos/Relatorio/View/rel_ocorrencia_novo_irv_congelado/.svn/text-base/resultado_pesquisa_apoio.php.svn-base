
<div class="separador"></div>
<div id="resultados_relatorio" class="<?php echo ($this->view->resultados) ? '' : 'invisivel'; ?>">
	<?php echo (trim($_POST['sub_acao'])!='gerarPdf') ? '<div class="bloco_titulo">Acionamentos de Apoio</div>' : "";  ?>

	<div class="bloco_conteudo">
		<div class="listagem">
			<table <?php echo (trim($_POST['sub_acao'])=='gerarPdf') ? 'border="1"' : "";  ?> >
				<thead>
					<tr>
						<th class="menor centro"><br>Data Acionamento</br></th>
						<th class="menor centro"><br>Placa</br></th>
						<th class="menor centro"><br>Cliente</br></th>
						<th class="menor centro"><br>Motivo</br></th>
						<th class="medio centro"><br>Forma Notificação</br></th>
						<th class="menor centro"><br>Status do Equipamento</br></th>
						<th class="medio centro"><br>Status</br></th>
						<th class="menor centro"><br>Recuperado</br></th>
						<th class="menor centro"><br>Contato</br></th>
						<th class="medio centro"><br>Cidade</br></th>
						<th class="medio centro"><br>Usuário</br></th>
					</tr>
				</thead>
				<tbody>
				<?php
					$equipe=""; 
					$cliente=""; 
					$hora = 0;
					$minuto = 0;
					$segundo = 0;


					foreach($dadosView as $ocorrencia):

						//echo "<pre>";var_dump($ocorrencia); exit;

						$mudacor = false;
						$chave = $ocorrencia->ococococdoid;

						if($cliente != $ocorrencia->ococcliente) :

							if($cliente!=""):

								?>
								<tr class="impar">
									<td colspan="11">
										<strong>Tempo de apoio: <?php echo $tempo_apoio; ?></strong>
									</td>
								</tr>
								<?php

								$temp = explode(':',$tempo_apoio);
								$hora += $temp[0];
								$minuto += $temp[1];
								$segundo += $temp[2];	
							endif;

							$cliente=$ocorrencia->ococcliente;
							$mudacor = true;

						endif;
						if($equipe != $ocorrencia->ococtelefone_emergencia):
							$equipe=$ocorrencia->ococtelefone_emergencia;
							$mudacor = true;
					?>
					<tr>
						<td class="agrupamento esquerda" colspan="11">
							<?php echo $equipe; ?>
						</td>
					</tr>
					<?php
						endif;

						if($mudacor) $rowClass = ($rowClass == "impar") ? "par" : "impar";
					?>
					<tr class="<?php echo $rowClass; ?>">
						<td class="centro"><?php echo $ocorrencia->data_comunicacao; ?></td>
						<td class="esquerda"><?php echo $ocorrencia->ococplaca; ?></td>
						<td class="esquerda"><?php echo $ocorrencia->ococcliente; ?></td>
						<td class="esquerda"><?php echo $ocorrencia->ococmotivo; ?></td>
						<td class="esquerda"><?php echo $ocorrencia->ocococorrencia_forma_notificacao; ?></td>
						<td class="esquerda"><?php echo $ocorrencia->ocococorrencia_motivo_equip_sem_contato; ?></td>
						<td class="esquerda"><?php echo $ocorrencia->ococstatus; ?></td>
						<td class="esquerda"><?php
									if(!empty($ocorrencia->ococrecuperado)) {
										echo ($ocorrencia->ococrecuperado == 't') ? 'Sim' : 'Não';
									}
									?></td>
						<td class="esquerda"><?php echo $ocorrencia->ococcontato; ?></td>
						<td class="esquerda"><?php echo $ocorrencia->ococusuario; ?></td>
						<td class="esquerda"><?php echo $ocorrencia->ococcidade; ?></td>
					</tr>
				<?php
                        $tempo_apoio = $ocorrencia->tempo_apoio;
					endForeach;

					$temp = explode(':',$tempo_apoio);
					$hora += $temp[0];
					$minuto += $temp[1];
					$segundo += $temp[2];

					$segundoFinal = ($segundo%60);
					$minutoFinal = floor($segundo/60) + $minuto;
					$minutoFinal = ($minutoFinal%60);
					$horaFinal = floor($minuto/60) + $hora;

					$segundoFinal = (strlen("$segundoFinal") > 1) ? "$segundoFinal" : "0"."$segundoFinal";
					$minutoFinal = (strlen("$minutoFinal") > 1) ? "$minutoFinal" : "0"."$minutoFinal";
					$horaFinal = (strlen("$horaFinal") > 1) ? "$horaFinal" : "0"."$horaFinal";

				?>
					<tr class="impar">
						<td colspan="11">
							<strong>Tempo de apoio: <?php echo $tempo_apoio; ?></strong>
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr class="impar">
						<td colspan="11" class="esquerda">
							<strong>TEMPO TOTAL DE APOIO <?php echo ($horaFinal . ':' . $minutoFinal . ':' . $segundoFinal); ?></strong>
					</td>					
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