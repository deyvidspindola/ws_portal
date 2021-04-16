<div class="separador"></div>
<div id="resultados_relatorio" class="<?php echo ($this->view->resultados) ? '' : 'invisivel'; ?>">
	<div class="bloco_conteudo">
		<div class="listagem" style="height: 750px; overflow-x: scroll">
			<table <?php echo (trim($_POST['sub_acao'])=='gerarPdf') ? 'border="1"' : "";  ?>>
				<thead>
					<tr>
						<th class="menor centro">Placa</th>
						<th class="menor centro">Chassi</th>
						<th class="menor centro">Tipo de Veículo</th>
						<th class="menor centro">Cor</th>
						<th class="medio centro">Ano</th>
						<th class="menor centro">Marca</th>
						<th class="medio centro">Modelo</th>
						<th class="menor centro">Valor FIPE</th>
						<th class="menor centro">Carregado?</th>
						<th class="medio centro">Valor da Carga</th>
						<th class="medio centro">Carga</th>
						<th class="menor centro">Tipo de Carga</th>
						<th class="menor centro">Embarcador</th>
						<th class="menor centro">Seguradora da Carga</th>
						<th class="medio centro">Cliente</th>
						<th class="menor centro">CPF/CNPJ</th>
						<th class="menor centro">Tipo Pessoa</th>
						<th class="menor centro">Tipo Contrato</th>
						<th class="menor centro">Cidade do Cliente</th>
						<th class="menor centro">UF do Cliente</th>
						<th class="menor centro">Classe</th>
						<th class="menor centro">Instalado CARGO TRACCK?</th>
						<th class="menor centro">Serial CT</th>
						<th class="menor centro">Equipamento</th>
						<th class="menor centro">Local de Instalação do Equipamento</th>
						<th class="menor centro">Técnico Instalador</th>
						<th class="menor centro">Motivo da Ocorrência</th>
						<th class="menor centro">Forma da Notificação</th>
						<th class="menor centro">Status</th>
						<th class="menor centro">Forma de Recuperação</th>
						<th class="menor centro">Status do Equipamento</th>
						<th class="menor centro">Lat/Long Última Posição</th>
						<th class="menor centro">Data Comunicação</th>
						<th class="menor centro">Data de Roubo</th>
						<th class="menor centro">Tempo de Aviso</th>
						<th class="menor centro">Data/Hora Recuperado</th>
						<th class="menor centro">Local do Evento</th>
						<th class="menor centro">Bairro do Evento</th>
						<th class="menor centro">Zona do Evento</th>
						<th class="menor centro">Cidade do Evento</th>
						<th class="menor centro">UF do Evento</th>
						<th class="menor centro">Lat/Long do Evento</th>
						<th class="menor centro">Local Recuperado</th>
						<th class="menor centro">Bairro Recuperado</th>
						<th class="menor centro">Zona Recuperado</th>
						<th class="menor centro">Cidade Recuperado</th>
						<th class="menor centro">Estado Recuperado</th>
						<th class="menor centro">Lat/Long Recuperação</th>
						<th class="menor centro">Equipe de Apoio Acionada</th>
						<th class="menor centro">Recuperado pelo Apoio?</th>
						<th class="menor centro">Tempo de Chegada de Apoio</th>
						<th class="menor centro">Nº B.O.</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach( $this->view->dadosRelMacro  as $ocorrencia): ?>
					<?php $rowClass = ($rowClass == "impar") ? "par" : "impar"; ?>
					<tr class="<?php echo $rowClass; ?> <?php echo trim($ocorrencia->condt_exclusao) != '' ? 'excluido' : '' ?>">
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
							<?php echo $ocorrencia->ococveiculo_chassi; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococtipo_veiculo; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococveiculo_cor; ?>
						</td>
						<td class="direita">
							<?php echo $ocorrencia->ococveiculo_ano; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococmarca_veiculo; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococmodelo_veiculo; ?>
						</td>
						<td class="direita">
							 <?php echo $ocorrencia->ococvalor_fipe; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococcarregado; ?>
						</td>
						<td class="direita">
							<?php echo $ocorrencia->ococvalor_carga; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococcarga; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococtipo_carga; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococembarcador; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococseguradora_carga; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococcliente; ?>
						</td>
						<td class="direita">
							<?php echo $ocorrencia->ococcnpj_cpf; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococtipo_pessoa; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococtipo_contrato; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococcidade; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococuf; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococecclasse_termo; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococinstalado_cargo_track; ?>
						</td>
						<td class="direita">
							<?php echo $ocorrencia->ococserial_cargo_track; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococequipamento; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococlocal_instalacao_equipamento; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococtecnico_instalacao; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococmotivo; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ocococorrencia_forma_notificacao; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococstatus; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococforma_recuperacao; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ocococorrencia_motivo_equip_sem_contato; ?>
						</td>
						<td class="direita">
							<?php echo $ocorrencia->ococlatitude_longitude_recuperado; ?>
						</td>
						<td class="centro">
							<?php echo $ocorrencia->data_comunicacao; ?>
						</td>
						<td class="centro">
							<?php echo $ocorrencia->ococdata_roubo; ?>
						</td>
						<td class="centro">
							<?php echo $ocorrencia->ococtempo_aviso; ?>
						</td>
						<td class="centro">
							<?php echo $ocorrencia->ococdata_recuperacao; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococlocal_evento; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococbairro_evento; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococzona_evento; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococcidade_evento; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococuf_evento; ?>
						</td>
						<td class="direita">
							<?php echo $ocorrencia->ococlatitude_longitude_evento; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococlocal_recuperado; ?>
						</td>
						<td class="esquerda">
                            <?php echo $ocorrencia->ococbairro_recuperado; ?>
						</td>
						<td class="esquerda">
							<?php $ocorrencia->ococzona_recuperado; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococcidade_recuperado; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococuf_recuperado; ?>
						</td>
						<td class="direita">
							<?php echo $ocorrencia->ococlatitude_longitude_recuperado; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococequipe_apoio; ?>
						</td>
						<td class="esquerda">
							<?php echo $ocorrencia->ococrecuperado_apoio; ?>
						</td>
						<td class="centro">
							<?php echo $ocorrencia->ococtempo_chegada_apoio; ?>
						</td>
						<td class="direita">
							<?php echo $ocorrencia->ococnumero_bo; ?>
						</td>
					</tr>
                    <?php
                        $totalFipe = $ocorrencia->total_fipe;
                        $mediaAviso = $ocorrencia->media_aviso;
                        $mediaChegada = $ocorrencia->media_chegada;
                    ?>
					<?php endForEach;?>
				</tbody>
				<tfoot>
					<tr>
						<?php if (count( $this->view->dadosRelMacro ) == 1): ?>
							<td colspan="52"> 1 registro encontrado.</td>
						<?php else: ?>
							<td colspan="52"> <?php echo count( $this->view->dadosRelMacro); ?> registros encontrados.</td>
						<?php endIf; ?>
					</tr>
                    <tr>
                        <td colspan="52" class="esquerda">
                            Média de Aviso: <?php echo $mediaAviso; ?> <br>
                            Média de Tempo Chegada Apoio: <?php echo $mediaChegada; ?> <br />
                            Total Veículo FIPE: R$ <?php echo $totalFipe; ?><br />
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