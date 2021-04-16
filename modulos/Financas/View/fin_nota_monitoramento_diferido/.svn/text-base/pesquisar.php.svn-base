<div class="bloco_titulo">Resultado da Pesquisa</div>
<div class="bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th style="text-align:center;">Per&iacute;odo</th>
                    <th style="text-align:center;">Nota (N&uacute;mero refer&ecirc;ncia)</th>
                    <th style="text-align:center;">S&eacute;rie</th>
                    <th style="text-align:center;">Monitoramento Diferido</th>
                    <th style="text-align:center;">A&ccedil;&otilde;es</th>
                </tr>
            </thead>
            <tbody>
            <?php
                if(!empty($dados)){
                    $total = count($dados)." registro(s) encontrado(s).";
                    $i = 0;
                    
                    foreach($dados as $row){
                        $class  = !($i % 2) ? "par" : "";
                        $numero = $row['numero'];
                        $diferido = ($row['nflmonitoramento_diferido'] == 't') ? 'Sim' : 'N&atilde;o' ;
                        
                        $body = "<tr class='$class'>
                                <td style='text-align:center;'>".$row['nfldt_emissao']."</td>
                                <td style='text-align:center;'>".$row['nflno_numero']."</td>
                                <td style='text-align:center;'>".$row['nflserie']."</td>
                                <td style='text-align:center;'>".$diferido."</td>
                                <td style='text-align:center;'>";
                            
                        if($row['nflmonitoramento_diferido'] == 't'){                        
                            $body .= "<a class='bt_excluir excluir_listagem' href='javascript:void(0)' data-nfid='".$row['nflno_numero']."-".$row['nflserie']."' title='Excluir'>
                                        <img class='icone' alt='Excluir' src='images/icon_error.png'>
                                    </a>";
                        }
                        
                        $body .= "</td></tr>";
                        
                        echo $body;
                        $i++;
                    }
                } else{
                    $total = 'Nenhum registro encontrado.';
                    
                    echo "<tr class=''>
                                <td style='text-align:center;'>&nbsp;</td>
                                <td style='text-align:center;'>&nbsp;</td>
                                <td style='text-align:center;'>&nbsp;</td>
                                <td style='text-align:center;'>&nbsp;</td>
                                <td style='text-align:center;'>&nbsp;</td>
                              </tr>";
                }
            ?>                
            </tbody>
            <tfoot>							
                <tr>
                    <!-- Total de registros -->
                    <td style="text-align:center;" colspan="5"><?=$total?></td>                
                </tr>					
            </tfoot>
        </table>
    </div>
</div>
<div class="bloco_acoes"></div>