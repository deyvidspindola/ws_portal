<div id="sintetico" class="relatorio">
    <table width="100%" id="resultado_sintetico" class="tableMoldura resultado_pesquisa" border="1">	
        <?php 		
		if(!empty($options['nome_cliente']) || !empty($dados_pesquisa['nome_usuario'])): 
				if(!empty($options['nome_cliente'])) $cliente = '<h3>Cliente: </h3>'.implode(", ", $dados_pesquisa['clientes']).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				if(!empty($dados_pesquisa['nome_usuario'])) $cliente .= '<h3>Atendente: </h3>'.$dados_pesquisa['nome_usuario'];
		?>
		
        <?php if($dados_pesquisa['total_ligacoes']['total'] > 0): ?>
        <tr class="tableTituloColunas">    
            <td colspan="4"><?php echo $cliente ?></td>
        </tr>    		
        <?php endif; ?>
        
		<?php endif; ?>
		
		
		
        <tr class="tableTituloColunas">    
            <td colspan="3">                    
                <h3>Resumo de ligações no período de <?php echo  $options['dt_ini'];?> a <?php echo  $options['dt_fim'];?></h3>                                    
            </td>
            <td width="10%" colspan="1" align="center"><h3>Total</h3></td>
        </tr>
        <tr class="result">    
            <td colspan="3">
                <h3>Total de Ligações</h3>                    
            </td>
            <td colspan="1" align="center"><?php echo $dados_pesquisa['total_ligacoes']['total'] ?></td>
        </tr>
        <?php foreach($dados_pesquisa['tipo_ligacao'] as $tipo_ligacao): ?>
            <tr class="result">    
                <td colspan="3"><?php echo $tipo_ligacao['label'] ?></td>
                <td colspan="1" align="center"><?php echo $tipo_ligacao['value'] ?></td>
            </tr> 
        <?php endforeach; ?>   		
        
		
		
        <?php if(!empty($dados_pesquisa['classe_equipamento'])): ?>
		
            <tr class="tableTituloColunas">    
                <td colspan="4">                    
                    <h3>Por classe de equipamento no período de <?php echo  $options['dt_ini'];?> a <?php echo  $options['dt_fim'];?></h3>                    
                </td>
            </tr>
            
            <tr class="tableTituloColunas">    
                <td colspan="4"><h3>Classe</h3></td>
            </tr>
            
			<?php foreach($dados_pesquisa['classe_equipamento'] as $classe_equipamento => $registros): ?>
				<tr class="result">    
					<td colspan="3"><?php echo $classe_equipamento ?></td>
					<td colspan="1" align="center"><?php echo count($registros); ?></td>
				</tr> 
			<?php endforeach; ?> 		
			<tr class="tableTituloColunas">    
				<td colspan="3" ><h3>Total Geral</h3></td>
				<td colspan="1" align="center"><?php echo $dados_pesquisa['total_classe_equipamento']['total'] ?></td>
            </tr>
        
        <?php endif; ?>
        
		
        <?php if(!empty($dados_pesquisa['versao_equipamento'])): ?>
		
            <tr class="tableTituloColunas">    
                <td colspan="4">                    
                    <h3>Por versão de equipamento no período de <?php echo  $options['dt_ini'];?> a <?php echo  $options['dt_fim'];?></h3>                    
                </td>
            </tr>
            
            <tr class="tableTituloColunas">    
                <td colspan="4"><h3>Versão</h3></td>
            </tr>
            
			<?php foreach($dados_pesquisa['versao_equipamento'] as $versao_equipamento => $registros): ?>
				<tr class="result">    
                    <td colspan="3"><?php echo $versao_equipamento ?></td>
                    <td colspan="1" align="center"><?php echo count($registros); ?></td>
				</tr> 
			<?php endforeach; ?>
			<tr class="tableTituloColunas">    
				<td colspan="3"><h3>Total Geral</h3></td>
				<td colspan="1" align="center"><?php echo $dados_pesquisa['total_versao_equipamento']['total']?></td>
            </tr>
        
        <?php endif; ?>
        
		<?php if(!empty($dados_pesquisa['motivos'])): ?>
		
		
            <tr class="tableTituloColunas">    
                <td colspan="3">
                    <h3>                        
                        Resumo de Motivos no período de <?php echo  $options['dt_ini'];?> a <?php echo  $options['dt_fim'];?>
                    </h3>                    
                </td>			
				<td colspan="1" align="center"><h3>Total</h3></td>
            </tr>
            <?php $total_motivos = 0; 
			foreach($dados_pesquisa['motivos'] as $motivos): 
					$total_motivos += count($motivos);
			?>
            <?php endforeach; ?>
            
            <tr class="tableTituloColunas">    
				<td colspan="3">
                    <h3>Total de Motivos</h3>                    
                </td>
				<td colspan="1" align="center"><?php echo $total_motivos?></td>
            </tr>            
            
			<?php foreach($dados_pesquisa['motivos'] as $motivos): ?>
				<tr class="result">    
                    <td colspan="3"><?php echo $motivos[0] ?></td>
					<td colspan="1" align="center"><?php echo count($motivos); ?></td>
				</tr> 
			<?php endforeach; ?>
			
        <?php endif; ?>
		
		
		
            <tr class="tableTituloColunas">    
                <td colspan="4">
                    <h3>Relatório Sintético de Protocolos</h3>                    
                </td>
            </tr>
			<?php foreach($dados_pesquisa['resultado_sintetico'] as $resultado): ?>
			<tr class="result">    
                <td colspan="3"><?php echo $resultado['label'] ?></td>
				<td colspan="1" align="center"><?php echo $resultado['value'] ?></td>
            </tr> 
			<?php endforeach; ?>
        
        
        <?php if($options['tipo_relatorio'] == "data_hora"): ?>
            <?php  include _MODULEDIR_ . 'Relatorio/View/rel_atendimento_front_end/data_hora.php'; ?>
        <?php endif; ?>
        
    </table>
</div>