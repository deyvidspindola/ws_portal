<div class="separador"></div>
<div class="bloco_titulo">Resultado da Pesquisa</div>
<div class="bloco_conteudo">
	<div class="listagem">
		<table>
		
			<thead>
				<tr>
					<th class="menor centro">Prev.</th>
					<th class="menor centro">Dt. Previsão</th>
					<th class="menor centro">Contrato</th>
					<th class="menor centro">CPF/ CNPJ</th>
					<th class="medio centro">Cliente</th>
					<th class="menor centro">Placa</th>
					<th class="menor centro">Subscription</th>
					<th class="medio centro">Item</th>
					<th class="menor centro">Valor</th>
					<th class="menor centro">Desc.</th>
					<th class="menor centro">Mês/ Ano</th>
					<th class="menor centro">Início Vigência</th>
					<th class="menor centro">Vencimento</th>
					<th class="menor centro">Ciclo</th>
					<th class="menor centro">Proc.</th>
					<th class="menor centro">Status Vivo</th>
				</tr>
			</thead>
			
			<tbody>			
                <?php if (count($this->view->dados) > 0) : ?>
                    <?php $classeLinha = "par"; ?>
                    <?php $totalGeral = 0; ?>
					
                    <?php foreach ($this->view->dados as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                        <tr class="<?php echo $classeLinha; ?>">
							<td class="direita"><?php echo $resultado->pavoid; ?></td>
							<td><?php echo $resultado->dt_previsao; ?></td>
							<td><?php echo $resultado->pavconoid; ?></td>
							<td class="direita"><?php echo $resultado->cli_docto; ?></td>
							<td><?php echo $resultado->clinome; ?></td>
							<td><?php echo $resultado->veiplaca; ?></td>
							<td class="direita"><?php echo $resultado->pavsubscription; ?></td>
							<td><?php echo $resultado->obrobrigacao; ?></td>
							<td class="direita"><?php echo $resultado->pavvl_previsao; ?></td>
							<td class="direita"><?php echo $resultado->pavvl_desconto; ?></td>
							<td class="centro"><?php echo $resultado->referencia; ?></td>
							<td class="centro"><?php echo $resultado->dt_ini_vigencia; ?></td>
							<td class="centro"><?php echo $resultado->dt_vencimento; ?></td>
							<td class="direita"><?php echo $resultado->vppaciclo; ?></td>
							<td><?php echo $resultado->pavflag_processado; ?></td>
							<td><?php echo $resultado->vpesstatus; ?></td>							
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>                
			</tbody>
			
            <tfoot>
                <tr>
                    <td colspan="9" class="direita">
                        Total: <?php echo $this->view->total; ?>
                    </td>
                    <td colspan="7" class="direita">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="16" class="centro">
                        <?php
                        $totalRegistros = count($this->view->dados);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
            
		</table>
	</div>
</div>

<div class="separador"></div>
	<div class="resultado">
		<div class="bloco_titulo resultado">Download</div>
		<div class="bloco_conteudo">
			<div class="conteudo centro">
				<a href="download.php?arquivo=<?php echo $this->view->nomeArquivo ?>" target="_blank">
					<img src="images/icones/t3/caixa2.jpg"><br><?php echo basename($this->view->nomeArquivo) ?>
				</a>
			</div>
		</div>
	</div>
</div>