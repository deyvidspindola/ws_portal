<div class="separador"></div>
<div id="resultados_relatorio" class="<?php echo ($this->view->resultados) ? '' : 'invisivel'; ?>">
	<?php echo (trim($_POST['sub_acao'])!='gerarPdf') ? '<div class="bloco_titulo">Acionamentos de Apoio Detalhado</div>' : "";  ?>

	<div class="bloco_conteudo">
		<div class="listagem">
			<table <?php echo (trim($_POST['sub_acao'])=='gerarPdf') ? 'border="1"' : "";  ?> >
				<thead>
					<tr>
						<th class="menor centro">Data Acionamento</th>
						<th class="menor centro">Placa</th>
						<th class="menor centro">Cliente</th>
						<th class="menor centro">Motivo</th>
						<th class="medio centro">Forma Notificação</th>
						<th class="menor centro">Status do Equipamento</th>
						<th class="medio centro">Status</th>
						<th class="menor centro">Recuperado</th>
						<th class="menor centro">Contato</th>
						<th class="medio centro">Cidade</th>
						<th class="medio centro">Usuário</th>
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

                        $mudacor = false;

						if (($cliente != $ocorrencia->ococcliente) || ($equipe != $ocorrencia->ococtelefone_emergencia)) :

                            if (!empty($cliente)) : ?>
                            <tr class="<?php echo $rowClass; ?>">
                                <td colspan="11">
                                    <strong>
                                        Tempo de apoio: <?php  echo $tempoApoio; ?>
                                    </strong>
                                </td>
                            </tr>
                            <?php

                            	$temp = explode(':',$tempoApoio);
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

                    if ($mudacor) $rowClass = ($rowClass == "impar") ? "par" : "impar";
                    ?>
					<tr class="<?php echo $rowClass; ?>">
						<td class="centro"><?php echo $ocorrencia->data_comunicacao; ?></td>
						<td class="esquerda"><?php echo $ocorrencia->ococplaca; ?></td>
						<td class="esquerda"><?php echo $ocorrencia->ococcliente; ?></td>
						<td class="esquerda"><?php echo $ocorrencia->ococmotivo; ?></td>
						<td class="esquerda"><?php echo $ocorrencia->ocococorrencia_forma_notificacao; ?></td>
						<td class="esquerda"><?php echo $ocorrencia->ocococorrencia_motivo_equip_sem_contato; ?></td>
						<td class="esquerda"><?php echo $ocorrencia->ococstatus; ?></td>
						<td class="esquerda">
                            <?php
                                if(!empty($ocorrencia->ococrecuperado)) {
                                    echo ($ocorrencia->ococrecuperado == 't') ? 'Sim' : 'Não';
                                }
                            ?>
                        </td>
						<td class="esquerda"><?php echo $ocorrencia->ococcontato; ?></td>
						<td class="esquerda"><?php echo $ocorrencia->ococcidade; ?></td>
                        <td class="esquerda"><?php echo $ocorrencia->ococusuario;?></td>
					</tr>
                    <?php
                        $tempoApoio = $ocorrencia->tempo_apoio;
                        endForEach;

                        $temp = explode(':',$tempoApoio);
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

                    <tr class="<?php echo $rowClass; ?>">
                       <td colspan="11">
                           <strong>
                               Tempo de apoio: <?php  echo $tempoApoio; ?>
                           </strong>
                       </td>
                   </tr>

				</tbody>
				<tfoot>
					<tr class="impar">
						<td colspan="11" class="esquerda">
							<strong>TEMPO TOTAL DE APOIO <?php echo ($horaFinal . ':' . $minutoFinal . ':' . $segundoFinal); ?></strong>
						</td>
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

