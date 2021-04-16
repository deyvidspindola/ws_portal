<table width="100%" style="border: 1px solid;">
	<tr>
		<td> 
			<b>RELATÓRIO DE ATENDIMENTO SASCAR</b>
		</td>
	</tr>
	<tr>
		<td style="border-bottom: 1px solid #000000; padding: 5px;">     
			<span style="float: left; margin-top: 10px; font-weight: bold;">Aprovado:</span> <?php echo ($this->atendimento['aprovado'] == 't') ? 'SIM' : 'NÃO'; ?>
		</td>
	</tr>
	<tr>
		<td style="border-bottom: 1px solid #000000; padding: 5px;">     
			<span style="float: left; margin-top: 10px; font-weight: bold;">DATA:</span> <?php echo $this->atendimento['data_atendimento'] ?>
		</td>
	</tr>
	<tr>
		<td style="border-bottom: 1px solid #000000; padding: 5px;">      
			<span style="float: left; margin-top: 10px; font-weight: bold;">HORA DO ACIONAMENTO:</span> <?php echo $this->atendimento['hora_acionamento'] ?>
		</td>
	</tr>
	<tr>
		<td style="border-bottom: 1px solid; padding: 5px;">            
			<span style="float: left; margin-top: 10px; font-weight: bold;">HORA CHEGADA LOCAL:</span> <?php echo $this->atendimento['hora_chegada'] ?>
		</td>
	</tr>
	<tr>
		<td style="border-bottom: 1px solid #000000; padding: 5px;">  
			<span style="float: left; margin-top: 10px; font-weight: bold;">HORA ENCERRAMENTO:</span> <?php echo $this->atendimento['hora_encerramento'] ?>
		</td>
	</tr>
	<tr>
		<td style="border-bottom: 0px solid #000000; padding: 5px;">            
			<span style="float: left; margin-top: 10px; font-weight: bold;">LOCAL DO ACIONAMENTO:</span><br />
            CEP: <?php echo utf8_decode($this->atendimento['cep']) ?> <br />
			Endereço: <?php echo utf8_decode($this->atendimento['logradouro']) ?>,  <?php echo $this->atendimento['end_numero'] ?><br />
			Bairro: <?php echo utf8_decode($this->atendimento['bairro']) ?> <br />
			Zona: <?php echo $this->atendimento['zona'] ?> <br />
			Cidade: <?php echo utf8_decode($this->atendimento['cidade']) ?> <br />
			UF: <?php echo $this->atendimento['uf'] ?> <br />
		</td>
	</tr>
	<tr>
		<td  style="border-bottom: 1px solid #000000; padding: 5px;">
			<span style="float: left; margin-top: 10px; font-weight: bold;">LATITUDE:</span> <?php echo $this->atendimento['latitude'] ?>
			<span style="float: left; margin-top: 10px; font-weight: bold; margin-left: 15px;">LONGITUDE:</span> <?php echo $this->atendimento['longitude'] ?>
		</td>
	</tr>
	<tr>
		<td style="border-bottom: 1px solid #000000; padding: 5px;">    
			<span style="float: left; margin-top: 10px; font-weight: bold;">OPERADOR SASCAR:</span> <?php echo $this->atendimento['nome_operador'] ?>
		</td>
	</tr>
	<tr>
		<td  style="border-bottom: 1px solid #000000; padding: 5px;">   
			<span style="float: left; margin-top: 10px; font-weight: bold;">TIPO DE OCORRÊNCIA:</span> <br />
			<table>
				<tr>
					<td>
						<?php echo ($this->atendimento['tipo'] == 0) ? '<b>[X]</b>' : '[&nbsp;&nbsp;]'; ?> Cerca
						
						<?php echo ($this->atendimento['tipo'] == 1) ? '<b>[X]</b>' : '[&nbsp;&nbsp;]'; ?> Roubo
						
						<?php echo ($this->atendimento['tipo'] == 2) ? '<b>[X]</b>' : '[&nbsp;&nbsp;]'; ?> Furto
						
						<?php echo ($this->atendimento['tipo'] == 3) ? '<b>[X]</b>' : '[&nbsp;&nbsp;]'; ?> Suspeita
						
						<?php echo ($this->atendimento['tipo'] == 4) ? '<b>[X]</b>' : '[&nbsp;&nbsp;]'; ?> Sequestro
					</td>
				</tr>
			</table>
			<br />
		</td>
	</tr>
	<tr>
		<td style="border-bottom: 1px solid #000000; padding: 5px;">  
			<br />
			<?php echo ($this->atendimento['recuperado'] == 't') ? '<b>[X]</b>' : '[&nbsp;&nbsp;]'; ?> Recuperado
			<?php echo ($this->atendimento['recuperado'] == 'f') ? '<b>[X]</b>' : '[&nbsp;&nbsp;]'; ?> Não recuperado
			<br />
		</td>
	</tr>
	<tr>
		<td  style="border-bottom: 1px solid #000000; padding: 5px;">    
			<span style="float: left; margin-top: 10px; font-weight: bold;">CLIENTE :</span> <?php echo utf8_decode($this->atendimento['cliente']) ?>
		</td>
	</tr>
	<tr>
		<td  style="border-bottom: 1px solid #000000; padding: 5px;">    
			<span style=" float: left; margin-top: 10px; font-weight: bold;">VEÍCULO:</span> <br />
			<table width="100%">
				<tr>
					<td>            
						Placa: <?php echo $this->atendimento['veiculo_placa'] ?> <br />
						Cor: <?php echo $this->atendimento['veiculo_cor'] ?> <br />
						Ano: <?php echo $this->atendimento['veiculo_ano'] ?> <br />
                        Marca: <?php echo $this->atendimento['veiculo_marca'] ?> <br />
						Modelo: <?php echo $this->atendimento['veiculo_modelo'] ?> <br />
					</td>
				</tr>
			</table>
			<br />
		</td>
	</tr>
	<tr>
		<td  style="border-bottom: 1px solid #000000; padding: 5px;">
			<span style=" float: left; margin-top: 10px; font-weight: bold;">CARRETA:</span> <br />
			<table width="100%">
				<tr>
					<td>            
						Placa: <?php echo $this->atendimento['carreta_placa'] ?> <br />
						Cor: <?php echo $this->atendimento['carreta_cor'] ?> <br />
						Ano: <?php echo $this->atendimento['carreta_ano'] ?> <br />
                        Marca: <?php echo $this->atendimento['carreta_marca'] ?> <br />
						Modelo: <?php echo $this->atendimento['carreta_modelo'] ?> <br />
						Carga: <?php echo utf8_decode($this->atendimento['carreta_carga']) ?> <br />
                    </td>
				</tr>
			</table>
			<br />
		</td>
	</tr>
	<tr>
		<td  style="border-bottom: 1px solid #000000; padding: 5px;">    
			<span style="float: left; margin-top: 10px; font-weight: bold;">AGENTE DE APOIO:</span><br />
			<table width="100%">
				<tr>
					<td>            
						Placa do veículo utilizado nas buscas: <?php echo $this->atendimento['placa_busca'] ?>
					</td>
				</tr>
			</table>
			<br />
		</td>
	</tr>
	<tr>
		<td style="border-bottom: 1px solid #000000; padding: 5px;">    
			<span style="float: left; margin-top: 10px; font-weight: bold;">DESCRIÇÃO DA OCORRÊNCIA:</span><br /><br />
			&nbsp;<?php echo $this->atendimento['descricao'] ?>
		<br />
		</td>
	</tr>
	<tr>
		<td style="border-bottom: 1px solid #000000; padding: 5px;">            
			<span style="float: left; margin-top: 10px; font-weight: bold;">ENDEREÇO DA RECUPERAÇÃO:</span><br />
            CEP: <?php echo utf8_decode($this->atendimento['cep_recup']) ?> <br />
			Endereço: <?php echo utf8_decode($this->atendimento['logradouro_recup']) ?>, <?php echo $this->atendimento['numero_recup'] ?> <br />
			Bairro: <?php echo utf8_decode($this->atendimento['bairro_recup']) ?> <br />
			Zona: <?php echo $this->atendimento['zona_recup'] ?> <br />
			Cidade: <?php echo utf8_decode($this->atendimento['cidade_recup']) ?> <br />
			UF: <?php echo $this->atendimento['uf_recup'] ?> <br />
		</td>
	</tr>
	<tr>
		<td style="border-bottom: 1px solid #000000; padding: 5px;">    
			<span style="float: left; margin-top: 10px; font-weight: bold;">DESTINAÇÃO DO VEÍCULO PÓS RECUPERAÇÃO:</span> <?php echo utf8_decode($this->atendimento['destino_veiculo']) ?>
		</td>
	</tr>
	<tr>
		<td>    
			<span style="float: left; margin-top: 10px; font-weight: bold;">LAUDO FOTOGRÁFICO:</span>
			
			<?php             
            $image_temp_dir = _SITEDIR_ . 'images/temp/';
            $anexos_dir = '/var/www/anexos_ocorrencia';
            $count_docs = 0;
            
            if(!is_dir($image_temp_dir)){
                mkdir($image_temp_dir, 0777);
            }
            
			foreach($this->anexos as $anexo){				
                if($anexo['tipo_arquivo'] == 'Foto') {
                    
                    copy($anexos_dir . '/' . $anexo['lauapreroid'] . '/' . $anexo['nome_arquivo'], $image_temp_dir . '/' . $anexo['nome_arquivo']);
                    
                    echo '<br />';
                    echo '<div style="text-align: center;">';
                    echo '<img width="400" height="400" src="' . _PROTOCOLO_ . _SITEURL_ . 'images/temp/' . $anexo['nome_arquivo'].'" alt="" />';
                    echo '</div>';                    
                    
                    $this->image_temp[] = $image_temp_dir . $anexo['nome_arquivo'];
                    
                }else {
                    echo $count_docs > 0 ? $anexo['nome_arquivo']. ' - ' .$anexo['usuario'].'<br />' : '<br />'.$anexo['nome_arquivo']. ' - ' .$anexo['usuario'].'<br />';
                    $count_docs++;
                }
			}
			?>			
		</td>
	</tr>
</table>  
		