<?php 
$numResultado = count($resultadoPesquisa['dados']);
?>
<div id="carregando" class="carregando "></div>
<div class="resultado_pesquisa">
	<div class="bloco_titulo">Resultado da Pesquisa</div>
	<?php if($numResultado > 0) {?>
	<div class="bloco_conteudo">
	    <div class="listagem">
	        <table>
	            <thead>
	                <tr>
	                    <th>Data Cadastro</th>
	                    <th>Cliente</th>
	                    <th>Classe</th>
	                    <th>CPF/CNPJ</th>
	                    <th>Pessoa</th>
	                    <th>UF</th>
	                    <th>Município</th>
	                    <th>Gerenciadora</th>
	                    <th>Chat SASGC</th>
	                </tr>
	            </thead>
	            <tbody>
	            	
	            <?php 
    	                
                	$cor = 'par';
	            	foreach($resultadoPesquisa['dados'] as $linha){
						
                        //formata mascara CNPJ e CPF                        
                        if($linha['clitipo'] == 'FÍSICA'){
                            $linha['clicpfcgc'] = str_pad($linha['clicpfcgc'], 11, "0", STR_PAD_LEFT);
                            $linha['clicpfcgc'] = $this->mascaraString('###.###.###-##',$linha['clicpfcgc']);
                        }else{
                            $linha['clicpfcgc'] = str_pad($linha['clicpfcgc'], 14, "0", STR_PAD_LEFT);
                            $linha['clicpfcgc'] = $this->mascaraString('##.###.###/####-##',$linha['clicpfcgc']);
                        }
                        $cor = ($cor=="par") ? "" : "par";?>
						<tr <?php if ($cor != '') { ?> class="<?=$cor?>" <?php } ?>>
							<td><?php echo $linha['clidt_cadastro']; ?></td>
							<td><a href="<?php echo str_replace('/', '', strrchr($_SERVER['SCRIPT_NAME'], '/'));?>?acao=principal&clioid=<?php echo $linha['clioid']; ?>"><?php echo $linha['clinome']; ?></a></td>
							<td><?php echo ($linha['clicldescricao']?$linha['clicldescricao']:'-'); ?></td>
							<td><?php echo $linha['clicpfcgc']; ?></td>
							<td><?php echo ($linha['clitipo']?$linha['clitipo']:'-'); ?></td>
							<td><?php echo ($linha['cliuf']?$linha['cliuf']:'-'); ?></td>
							<td><?php echo ($linha['clicidade']?$linha['clicidade']:'-'); ?></td>
							<td><?php echo ($linha['gernome']?$linha['gernome']:'-'); ?></td>
							<td><?php echo ($linha['clivisualizacao_sasgc'] == 't')?'Sim':'Não'; ?></td>
						</tr>
						<?
						
					}
	            ?>			
	            </tbody>
	        </table>
	    </div>
	</div>
	<div class="bloco_acoes"><p><?php echo $this->getMensagemTotalRegistros($numResultado);?></p></div>
    <?php } else {?>
    <div class="bloco_acoes"><p>Nenhum Resultado Encontrado.</p></div>
    <?php } ?>
</div>
