<div id="resultado_contratos_container" style="margin-bottom: 10px; padding: 0; overflow: auto; height: 120px; width: 100%;">
	<table id="lista_contratos" width="100%" class="tableMoldura" style="margin: 0 !important; width: 100% !important;">
		<tbody>
	    	<tr class="tableTituloColunas">
				<td align="center">
					<input type="checkbox" name="marcar_todos" />
				</td>
				<td align="center"><h3>Termo</h3></td>
				<td align="center" width="105px"><h3>Cadastro</h3></td>
				<td align="center"><h3>Ve&iacute;culo</h3></td>
				<td align="center"><h3>Equipamento</h3></td>
				<td align="center"><h3>Classe do Termo</h3></td>
				<td align="center"><h3>Tipo do Termo</h3></td>
				<td align="center"><h3>Status do Termo</h3></td>
			</tr>
	        <?php $linhas = count($resultado);?>
	        <?php if ($linhas > 0 && $resultado != false): ?>
				<?php foreach($resultado as $contrato): ?>
				<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'; ?>
				<tr class="<?php echo $zebra; ?>">
					<td align="center">
						<input class="check_contrato" type="checkbox" id="<?php echo $contrato['contrato']?>" name="marca_contrato[]" value="<?php echo $contrato['contrato']?>" <?php echo in_array($contrato['contrato'], $contratos) ? 'checked="checked"' : ''; ?> />
						<input type="hidden" name="contrato[]" value="<?php echo $contrato['contrato']?>" /> 
					</td>	
					<td align="center">
						<?php echo $contrato['contrato']; ?>
					</td>
					<td align="center">
						<?php echo $contrato['data_cadastro']?>
					</td>
					<td align="center">
						<?php echo $contrato['veiculo']?>
					</td>
					<td align="center">
						<?php echo $contrato['equipamento']?>
					</td>
					<td align="center">
						<?php echo $contrato['classe_contrato']?>
					</td>
					<td align="center">
						<?php echo utf8_encode($contrato['tipo_contrato']); ?>
					</td>
					<td align="center">
						<?php echo utf8_encode($contrato['status'])?>
					</td>
				</tr>
				<?php endforeach;?>        
	        <?php else:?>
	         <tr>
	        	<td align="center">
	        		&nbsp;
	        	</td>
	        </tr>
	        <tr>
	        	<td align="center">
	        		<b>Nenhum resultado encontrado</b>
	        	</td>
	        </tr>
	         <tr>
	        	<td align="center">
	        		&nbsp;
	        	</td>
	        </tr>
	        <?php endif;?>
	    </tbody>
	</table>
</div>
<table width="100%">
	 <tr>
		<td align="center">
			<b>Quantidade de contratos: <?php echo $linhas?>.</b>
		</td>
	</tr>
	<tr>
		<td align="center">
			&nbsp;
		</td>
	</tr>
</table>