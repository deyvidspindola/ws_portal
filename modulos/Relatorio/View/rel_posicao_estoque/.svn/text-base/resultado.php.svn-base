    <div class="bloco_titulo">Resultado da Pesquisa</div>
    <div class="bloco_conteudo">
        <div class="listagem" style="overflow-x: scroll;">
            <table>

            <?php if ($relatorio !== null && count($relatorio) != 0){ ?>
			<?php $zebra = 'tde'; ?>
            <thead>
                    <tr>
                        <th class="menor centro"><strong>Dt Posição</strong></th>
                    	<th class="menor centro">Tipo Estoque</th>
                    	<th class="menor centro">Cód Repr</th>
                    	<th class="menor centro">Repr</th>
                    	<th class="menor centro">Cidade</th>
                    	<th class="menor centro">UF</th>
                    	<th class="menor centro">Cód Prod</th>
                    	<th class="menor centro">Prod</th>
                    	<th class="menor centro">Qtd Dispon</th>
                    	<th class="menor centro">Qtd Reserv</th>
                    	<th class="menor centro">Qtd Transi</th>
						<th class="menor centro">Qtd Reserv Transi</th>
                    	<th class="menor centro">Qtd Confer</th>
                    	<th class="menor centro">Qtd Retira</th>
                    	<th class="menor centro">Qtd Instal</th>
                    	<th class="menor centro">Qtd Retorn</th>
                    	<th class="menor centro">Qtd Recall</th>
                    	<th class="menor centro">Qtd Recall Dispon</th>
                    	<th class="menor centro">Qtd Manute Fornec</th>
                    	<th class="menor centro">Qtd Manute Intern</th>
                    	<th class="menor centro">Qtd Aguard Manute</th>
                    	<th class="menor centro">Total</th>
                    	<th class="menor centro">Vlr Unit</th>
                    	<th class="menor centro">Vlr Total</th>
                    	<th class="menor centro">Status Repr</th>
                    </tr>
                </thead>
            <tbody>
			<?php foreach ($relatorio as $row): ?>
			<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'; ?>
			<tr class="<?php echo $zebra;?>">
                <td class="centro"><?php echo $row['data_posicao_estoque'];?></td>
				<td class="esquerda"><?php echo $row['tipo_item'];?></td> 
                <td class="direita"><?php echo $row['repoid'];?></td>
                <td class="esquerda"><?php echo $row['repnome'];?></td> 
                <td class="esquerda"><?php echo utf8_decode($row['cidade']);?></td>
                <td class="esquerda"><?php echo $row['uf'];?></td>
                <td class="direita"><?php echo $row['idprd'];?></td>
                <td class="esquerda"><?php echo $row['prdproduto'];?></td>
				<td class="direita"><?php echo $row['qtd_disponivel'];?></td>
                <td class="direita"><?php echo $row['qtd_reserva'];?></td>
                <td class="direita"><?php echo $row['qtd_transito'];?></td>
				<td class="direita"><?php echo $row['qtd_reserv_transi'];?></td>
                <td class="direita"><?php echo $row['qtd_conferencia_if'];?></td>
                <td class="direita"><?php echo $row['qtd_retirada'];?></td>
                <td class="direita"><?php echo $row['qtd_instalador'];?></td>
                <td class="direita"><?php echo $row['qtd_retornado'];?></td>
                <td class="direita"><?php echo $row['qtd_recall'];?></td>
				<td class="direita"><?php echo $row['qtd_recall_disponivel'];?></td>
                <td class="direita"><?php echo $row['qtd_manutencao_fornecedor'];?></td>
                <td class="direita"><?php echo $row['qtd_manutencao_interna'];?></td>
                <td class="direita"><?php echo $row['qtd_aguardando_manutencao'];?></td>
                <td class="direita"><?php echo $row['total'];?></td>
                <td class="direita"><?php echo number_format($row['custo_medio_produto'],2,",",".");?></td>
                <td class="direita"><?php echo number_format($row['vlr_total'],2,",",".");?></td>
                <td class="esquerda"><?php echo $row['repstatus'];?></td>
			</tr>
			<?php endforeach;?>
		    </tbody>
                <tfoot>
                    <tr><?php if(count($relatorio) == 1){ ?>
                        <td colspan="24"><b><?php echo count($relatorio) ?> registro encontrado</b></td>
                        <?php }else{ ?>
                        <td colspan="24"><b><?php echo count($relatorio) ; ?> registros encontrados</b></td>
                        <?php }?>
                    </tr>
                </tfoot>	
		<?php }else{?> 
			 <?php echo null; ?>
		<?php } ?>
               
            </table>
        </div> 
    </div> 

  
    
