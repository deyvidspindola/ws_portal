<html>
<head>
	<title>Relatório Conciliação</title>
	<meta http-equiv="Content-Type content=text/html charset=UTF-8"/>
	<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
</head>
<body style="margin:50px">
                 <?php
                    $relatorio = $dadosCsv;
                    $relatorio_estornados = $dadosCsvEstornoEstornados;
                    $relatorio_estorno = $dadosCsvEstorno;
                    $relatorio_nao_estornados = $dadosCsvEstornoErro;
                    $numrelatorio = count($dados);
                    
                    if ($numrelatorio > 0) {
                    ?>
                    <div class="listagem" style="width:100%">
                        <table style="width:100%">
                            <thead>
                                <tr>
                                    <td colspan="13" style="margin:0;padding:0;border:0 none">
                                        <div class="bloco_titulo" style="font-size:16; margin:0;border: 1px solid #FFFFFF;">Relatório Conciliação</div>
                                    </td>
                                </tr>
                                <tr>
                                	<?php foreach ($cabecalho as $chave => $valor) { 
                                	       $indice_1[] =$valor;?>
                                		<th width="<?php echo $valor;?>"><?php echo $chave; ?></th>
                                	<?php } ?>
                                </tr>
                            </thead>
                           
                            <tbody>
                                <?
                                    $cor = 'par';
                                    
                                    if(count($relatorio) > 0){
	                                    foreach ($relatorio as $rel) {
	                                        $cor = ($cor=="par") ? "" : "par";?>
	                                                <tr <?php if ($cor != '') { ?> class="<?=$cor?>" <?php } ?>> 
	                                                	<?php foreach ($rel as $key => $valor_rel) {  ?>
	                                                		<td ><?php echo $valor_rel; ?></td>
	                                                	<?php } ?>
	                                                </tr>
	                                                <?
	                                    }
	                                    
                    				}else{ ?>
                                       <tr><td>Não há dados para exibir.</td></tr>
                                    <?php 
                                    }
                                ?>                                          
                            </tbody>
                        </table>
                        
                         <table style="width:100%">
                            <thead>
                                <tr>
                                    <td colspan="9" style="margin:0;padding:0;border:0 none">
                                        <div class="bloco_titulo" style="font-size:16; margin:0;border: 0px ;">Título(s) de Crédito Estornado(s)</div>
                                    </td>
                                </tr>
                                <tr>
                                	<?php foreach ($cabecalho_estorno_ok as $chave => $valor) { 
                                	       $indice_2[] =$valor;?>
                                		<th width="<?php echo $valor;?>"><?php echo $chave; ?></th>
                                	<?php } ?>
                                </tr>
                            </thead>
                           
                            <tbody>
                                <?
                                    $cor = 'par';
                                    
                                    if(count($relatorio_estornados) > 0){
                                    	foreach($relatorio_estornados as $estornados){
                                    		foreach($estornados as $estornos){
                                    	         $cor = ($cor=="par") ? "" : "par";?>
                                    			  <tr <?php if ($cor != '') { ?> class="<?=$cor?>" <?php } ?>> 
                                    					<?php foreach ($estornos as $key => $valor_estorno) { ?>
                                    					<td><?php echo $valor_estorno; ?></td>
                                    					<?php } ?>
                                    				    </tr>
                                    				 <?
                                    	     }
                                    	}
                                    }else{ ?>
                                    <tr><td>Não há dados para exibir.</td></tr>
                                    <?php 
                                    }
                                ?> 
                            </tbody>
                        </table>
                        
                         <table style="width:100%">
                            <thead>
                                <tr>
                                    <td colspan="11" style="margin:0;padding:0;border:0 none">
                                        <div class="bloco_titulo" style="font-size:16; margin:0;border: 0px ;">Dados de estornos do arquivo: </div>
                                    </td>
                                </tr>
                                <tr>
                                	<?php foreach ($cabecalho_dados_estorno as $chave => $valor) { 
                                	       $indice_21[] =$valor;?>
                                		<th width="<?php echo $valor;?>"><?php echo $chave; ?></th>
                                	<?php } ?>
                                </tr>
                            </thead>
                           
                            <tbody>
                                <?
                                    $cor = 'par';
                                    if(count($relatorio_estorno) > 0){
                                    	foreach($relatorio_estorno as $dados_estorno){
                                    		foreach($dados_estorno as $rel_estornos){
                                    	        $cor = ($cor=="par") ? "" : "par";?>
                                    			  <tr <?php if ($cor != '') { ?> class="<?=$cor?>" <?php } ?>> 
                                    					<?php foreach ($rel_estornos as $key => $valor_estorno) { ?>
                                    					<td><?php echo $valor_estorno; ?></td>
                                    					<?php } ?>
                                    				    </tr>
                                    				 <?
                                    	     }
                                    	}
                                    	     
                                    }else{ ?>
                                    <tr><td>Não há dados para exibir.</td></tr>
                                    <?php 
                                    }
                                ?> 
                            </tbody>
                        </table>
                        
                         <table style="width:100%">
                            <thead>
                                <tr>
                                    <td colspan="8" style="margin:0;padding:0;border:0 none">
                                        <div class="bloco_titulo" style="font-size:16; margin:0;border: 0px ;">O(s) dado(s) de Crédito de Cartão abaixo não foi(ram) estornado(s):</div>
                                    </td>
                                </tr>
                                <tr>
                                	<?php foreach ($cabecalho_erro_estorno as $chave => $valor) { 
                                	       $indice_3[] =$valor;?>
                                		<th width="<?php echo $valor;?>"><?php echo $chave; ?></th>
                                	<?php } ?>
                                </tr>
                            </thead>
                           
                            <tbody>
                                <?
                                    $cor = 'par';
                                    if(count($relatorio_nao_estornados) > 0){
	                                    foreach ($relatorio_nao_estornados as $rel_erro){
	                                        $cor = ($cor=="par") ? "" : "par";?>
                                                <tr <?php if ($cor != '') { ?> class="<?=$cor?>" <?php } ?>> 
                                                	<?php foreach ($rel_erro as $key => $valor) { ?>
                                                		<td><?php echo $valor; ?></td>
                                                	<?php } ?>
                                                </tr>
                                                <?
	                                    }
                                    }else{ ?>
                                    <tr><td>Não há dados para exibir.</td></tr>
                                    <?php 
                                    }
                                ?> 
                            </tbody>
                        </table>
                    </div>
                    <?
                    }
                    ?>
</body>
</html>
	
