<table id="resultado_analitico" class="resultado_pesquisa" border="1">

    <tr>
        <td colspan="20" height="10">
            <h3>Resultados da Pesquisa</h3>
        </td>
    </tr>
    <tr class="tableTituloColunas">    
        <td height="25"><h3>Data / Hora</h3></td>
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

    <?php 
        $numero_ligacoes = count($dados_pesquisa['resultados']); 
        $numero_acessos = 0;            
    ?>

    <?php foreach ($dados_pesquisa['resultados'] as $chave => $atendimentos): ?>

    <?php
        $qtd_linhas_rowspan = count($dados_pesquisa['resultados'][$chave]);
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

        $atendimento_concluido = true;
        $chave_atual = 0;
        $protocolo_atual = 0;
        $status_protocolo_atual = '';
        $chave_anterior = 0;
        $protocolo_anterior = 0;
        $status_protocolo_anterior = '';
        
        $contador_rowspan = 0;

        $tempo_total_atendimentos = $dados_pesquisa['tempo_total_protocolo_minutos'][$chave];
   
        foreach ($atendimentos as $atendimento) {
            
            if($chave_atual != $chave){
                $contador_rowspan = 0;
                $zebra_africana = ($zebra_africana=="#FFF") ? "#F3F3F3" : "#FFF";    
            }
                        
            $chave_atual = $chave;
            $protocolo_atual = $atendimento['protocolo_sascar'];
            $status_protocolo_atual = $atendimento['status_protocolo_sascar'];
            
            $tempo_resolucao = $atendimento['tempo_resolucao'];

            if(trim($atendimento['status_protocolo_sascar']) == "" || $atendimento['status_protocolo_sascar'] == 'Pendente' || $atendimento['status_atendimento'] == 'Pendente') {                    
                $atendimento_concluido = false;
            }
            
            ?>
          <tr class="result" bgcolor="<?php echo $zebra_africana ?>">
           <td>
                <?php echo $atendimento['data_hora']; ?>
            </td>
            <td>
                <?php echo $atendimento['protocolo_sascar']; ?>   
            </td> 
            <td>                                                                     
                <?php echo $atendimento['protocolo_vivo']; ?>   
            </td>
            <td align="center">
                <?php echo $atendimento['status_protocolo_sascar']; ?>   
            </td> 
            <td>
                <?php echo $atendimento['nome_cliente']; ?>   
            </td> 
            <td align="center">
                <?php echo $atendimento['classe_cliente']; ?>   
            </td> 
            <td align="center">
                <?php echo $atendimento['tipo_ligacao']; ?>
            </td>
            <td align="center">
                 <?php
                    if($atendimento_concluido) {                        
                        echo $tempo_resolucao;
                        echo '<br />';
                        echo '<br />';
                        echo "<b>".$tempo_resolucao."h</b>";

                    }else{
                        echo '&nbsp;';
                    }
                ?>    
            </td> 
            <td align="center">
                <?php 
                    echo $atendimento['tempo_atendimento'] ?>
                <b><?php
                        if(trim($tempo_total_atendimentos) != "") {
                            echo '<br />';
                            echo '<br />';
                            echo ($contador_rowspan == ($qtd_linhas_rowspan-1)) ? $tempo_total_atendimentos.'m' : '';
                        }
                    ?></b>
            </td>
            <td>
                <?php echo $atendimento['motivo_nivel1']; ?>
            </td>
            <td>
                <?php echo $atendimento['motivo_nivel2']; ?>
            </td>
            <td>
                <?php echo $atendimento['motivo_nivel3']; ?>
            </td>
            <td align="center">
                <?php echo $atendimento['status_atendimento']; ?>  
            </td>
            <td align="center">
                <?php echo $atendimento['atendente']; ?>   
            </td>
            <td align="center">
                <?php echo $atendimento['placa']; ?>  
            </td>
            <td  align="center" style="border-right: 0;">
                <?php echo $atendimento['tipo_contrato']; ?>
            </td>
            <td>
                <?php echo $atendimento['opcao_selecionada_ura']; ?>  
            </td>
            <td>
                <?php echo $atendimento['retorno_ura']; ?>  
            </td>
            <td>
                <?php echo $atendimento['segmento_cliente']; ?>  
            </td>
            <td>
                <?php echo $atendimento['situação_financeira']; ?>  
            </td>
        </tr> 
    
    <?
            $chave_anterior = $chave_atual;
            $protocolo_anterior = $protocolo_atual;
            $status_protocolo_anterior = $status_protocolo_atual;
            
            
            $contador_rowspan++;

        }
        
    endforeach; ?>  

    <tfoot class="result_foot">
        <tr>
            <td colspan="6">&nbsp;</td>
            <td colspan="1">Total: </td>
            <td colspan="1" align="center">
                <b><?php echo !empty($dados_pesquisa['total_em_horas']) ? $dados_pesquisa['total_em_horas'] : '00:00' ?>h</b>
            </td>
            <td colspan="1" align="center"><b><?php echo !empty($dados_pesquisa['total_em_minutos']) ? $dados_pesquisa['total_em_minutos'] : '00:00'; ?>m</b></td>
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