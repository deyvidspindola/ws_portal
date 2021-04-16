<div id="analitico" class="relatorio">    
    <table id="resultado_analitico" class="tableMoldura resultado_pesquisa" border="1">
        <thead>
            <tr class="tableSubTitulo">
                <td colspan="20">            
                    <h2>Resultados da Pesquisa</h2>                
                </td>
            </tr>        
            <tr class="tableTituloColunas">    
                <td><h3>Data / Hora</h3></td>
                <td><h3>Protocolo Sascar</h3></td>
                <td><h3>Protocolo Vivo</h3></td>
                <td><h3>Status Prot. Sascar</h3></td>
                <td><h3>Cliente</h3></td>
                <td><h3>Cliente Classe</h3></td>
                <td><h3>Tipo de Ligação</h3></td>
                <td><h3>Tempo Resol.</h3></td>
                <td><h3>Tempo Atend.</h3></td>
                <td><h3>Motivo Nível 1</h3></td>
                <td><h3>Motivo Nível 2</h3></td>
                <td><h3>Motivo Nível 3</h3></td>
                <td><h3>Status Aten(Mot.)</h3></td>
                <td><h3>Atendente</h3></td>
                <td><h3>Placa</h3></td>
                <td><h3>Tipo Contrato</h3></td>
                <td><h3>Opção Selecionada</h3></td>
                <td><h3>Retorno Ura</h3></td>
                <td><h3>Segmento</h3></td>
                <td><h3>Situação Financeira</h3></td>
            </tr>
        </thead>
        <?php 
            $numero_ligacoes = count($dados_pesquisa['resultados']); 
            $numero_acessos = 0;            
        ?>

        <?php foreach ($dados_pesquisa['resultados'] as $chave => $atendimentos): ?>

        <?php
            $numero_atendimentos += count($atendimentos);

            $data_hora = '';
            $protocolo_sascar = '';
            $protocolo_vivo = '';
            $status_protocolo_sascar = '';
            $nome_cliente = '';
            $classe_cliente = '';
            $tipo_ligacao = '';
            $tempo_atendimento = '';
            $motivo_nivel1 = '';
            $motivo_nivel2 = '';
            $motivo_nivel3 = '';
            $status_atendimento = '';
            $atendente = '';
            $placa = '';
            $tipo_contrato = '';
            $opcao_selecionada_ura = '';
            $retorno_ura = '';
            $segmento = '';
            $situação_financeira = '';
            
            $atendimento_concluido = true;
            $chave_atual = 0;
            $protocolo_atual = 0;
            $status_protocolo_atual = '';
            $chave_anterior = 0;
            $protocolo_anterior = 0;
            $status_protocolo_anterior = '';
			$nome_cliente_atual = '';
			$nome_cliente_anterior = '';
			$espaco_tempo_resolucao = '';
            $count_atendimento = 0;
			
            foreach ($atendimentos as $atendimento) {
                
                $chave_atual = $chave;
                $protocolo_atual = $atendimento['protocolo_sascar'];
                $status_protocolo_atual = $atendimento['status_protocolo_sascar'];
				$nome_cliente_atual = $atendimento['nome_cliente'];
                
                $data_hora                  .= '<div class="acessos">'.$atendimento['data_hora'].'</div>';
                
                if($protocolo_atual != $protocolo_anterior || (!empty($status_protocolo_atual) && $status_protocolo_atual != $status_protocolo_anterior)) {
                    $protocolo_sascar       .= '<div class="acessos no-border">'.$atendimento['protocolo_sascar'].'</div>';
                }
                
                $protocolo_vivo             .= '<div class="acessos">'.$atendimento['protocolo_vivo'].'</div>';
                
                if(!empty($status_protocolo_atual) && ($status_protocolo_atual != $status_protocolo_anterior)) {
                    $status_protocolo_sascar    .= '<div class="acessos no-border">'.$atendimento['status_protocolo_sascar'].'</div>';
                }
                
                if( ($chave_atual != $chave_anterior) || (!empty($nome_cliente_atual) && ($nome_cliente_atual != $nome_cliente_anterior)) ) {
                    $nome_cliente           .= '<div class="acessos no-border">'.$atendimento['nome_cliente'].'</div>';
                }
                                
                if($chave_atual != $chave_anterior) {                
                    $classe_cliente             .= '<div class="acessos no-border">'.$atendimento['classe_cliente'].'</div>';
                }
                
                $tipo_ligacao               .= '<div class="acessos">'.$atendimento['tipo_ligacao'].'</div>';
                $tempo_atendimento          .= '<div class="acessos">'.$atendimento['tempo_atendimento'].'</div>';
                $motivo_nivel1              .= '<div class="acessos">'.$atendimento['motivo_nivel1'].'</div>';
                $motivo_nivel2              .= '<div class="acessos">'.$atendimento['motivo_nivel2'].'</div>';
                $motivo_nivel3              .= '<div class="acessos">'.$atendimento['motivo_nivel3'].'</div>';
                $status_atendimento         .= '<div class="acessos">'.$atendimento['status_atendimento'].'</div>';
                $atendente                  .= '<div class="acessos">'.$atendimento['atendente'].'</div>';
                $placa                      .= '<div class="acessos">'.$atendimento['placa'].'</div>';
                $tipo_contrato              .= '<div class="acessos">'.$atendimento['tipo_contrato'].'</div>';
                $opcao_selecionada_ura      = '<div class="acessos">'.$atendimento['opcao_selecionada_ura'].'</div>';
                $retorno_ura                = '<div class="acessos">'.$atendimento['retorno_ura'].'</div>';
                $segmento_cliente           = '<div class="acessos">'.$atendimento['segmento_cliente'].'</div>';
                $situação_financeira        = '<div class="acessos">'.$atendimento['situacao_financeira'].'</div>';
                                
                $tempo_resolucao 			= $atendimento['tempo_resolucao'];
                                
				if($count_atendimento > 0) {				
					$espaco_tempo_resolucao .= '<div class="acessos no-border">&nbsp;</div>';
				}
                
                if(trim($atendimento['status_protocolo_sascar']) == "" || $atendimento['status_protocolo_sascar'] == 'Pendente' || $atendimento['status_atendimento'] == 'Pendente') {                    
                    $atendimento_concluido = false;
                }
                
                $chave_anterior = $chave_atual;
                $protocolo_anterior = $protocolo_atual;
                $status_protocolo_anterior = $status_protocolo_atual;
				$nome_cliente_anterior = $nome_cliente_atual;
	
				$count_atendimento++;
				
            }
				
            $tempo_total_atendimentos = $dados_pesquisa['tempo_total_protocolo_minutos'][$chave];
            
        ?>
        <tr class="result">
            <td>
                <?php echo $data_hora; ?>
            </td>
            <td>
                <?php echo $protocolo_sascar; ?>   
            </td> 
            <td>                                                                     
                <?php echo $protocolo_vivo; ?>   
            </td>
            <td>                                     
                <?php echo $status_protocolo_sascar; ?>  
            </td>
            <td>
                <?php echo $nome_cliente; ?>
            </td>
            <td>                                                                       
                <?php echo $classe_cliente; ?>
            </td>
            <td>
                <?php echo $tipo_ligacao; ?>
            </td>
            <td  align="center">
                <?php
                    if($atendimento_concluido) {                        
                        echo '<div class="acessos">'.$tempo_resolucao.'</div>';
						echo $espaco_tempo_resolucao;
                        echo '<div class="acessos no-border"><b>'.$tempo_resolucao.'h</b></div>';

                    }else{
                        echo '<div class="acessos"></div>';
                    }
                ?>                                                                

            </td>
            <td>
                <?php echo $tempo_atendimento; ?>
                <b><?php
                        if(trim($tempo_total_atendimentos) != "") {
                            echo '<div class="acessos no-border">'.$tempo_total_atendimentos.'m</div>';
                        }
                    ?></b>
            </td>
            <td>
                <?php echo $motivo_nivel1; ?>
            </td>
            <td>
                <?php echo $motivo_nivel2; ?>
            </td>
            <td>
                <?php echo $motivo_nivel3; ?>
            </td>
            <td>
                <?php echo $status_atendimento; ?>  
            </td>
            <td>
                <?php echo $atendente; ?>   
            </td>
            <td>
                <?php echo $placa; ?>  
            </td>
            <td  style="border-right: 0;">
                <?php echo $tipo_contrato; ?>
            </td>
            <td>
                <?php echo $opcao_selecionada_ura ?>  
            </td>
            <td>
                <?php echo $retorno_ura ?>  
            </td>
            <td>
                <?php echo $segmento_cliente ?>  
            </td>
            <td>
                <?php echo $situação_financeira ?>  
            </td>
        </tr>                                                        
        <?php endforeach; ?>  
        
        <tfoot class="result_foot">
            <tr>
                <td colspan="6">&nbsp;</td>
                <td colspan="1">Total: </td>
                <td colspan="1">
                    <b><?php echo !empty($dados_pesquisa['total_em_horas']) ? $dados_pesquisa['total_em_horas'] : '00:00' ?>h</b>
                </td>
                <td colspan="1"><b><?php echo !empty($dados_pesquisa['total_em_minutos']) ? $dados_pesquisa['total_em_minutos'] : '00:00'; ?>m</b></td>
                <td colspan="11" style="border-right: 0;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="20" style="text-align: center; border-right: 0;">
                    <?php
                    
                        if($numero_atendimentos == 0) {
                            $msg_rodape = 'Nenhum resultado encontrado';                            
                        }else{
                            $msg_rodape = $numero_ligacoes;                                                                
                            $msg_rodape .= ($numero_ligacoes > 1) ? ' Ligações ' : ' Ligação ';
                            $msg_rodape .= $numero_atendimentos;
                            $msg_rodape .= ($numero_atendimentos > 1) ? ' Atendimentos ' : ' Atendimento ';                            
                        }
                    ?>
                    <b><?php echo $msg_rodape ?></b>
                </td>
            </tr> 
        </tfoot>
    </table>       
</div>