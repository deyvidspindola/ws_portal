<div class="bloco_titulo">Resultado da Pesquisa</div>
<div class="bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th style="text-align:center;">Número</th>
                    <th style="text-align:center;">Data</th>
                    <th style="text-align:center;">Cliente</th>
                    <th style="text-align:center;">Usuário</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Situação</th>
                    <th style="text-align:center;">Valor</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $i = 0;
                foreach($dados as $row){
                    $class  = !($i % 2) ? "par" : "";
                    $numero = $row['numero'];
                    
                    echo "<tr class='$class'>
                            <td><a href='javascript:void(0);' id='num_termo_$numero'>".$numero."</a></td>
                            <td>".$row['data']."</td>
                            <td>".$row['cliente']."</td>
                            <td>".$row['usuario']."</td>
                            <td>".$row['status']."</td>
                            <td>".$row['situacao']."</td>
                            <td align='right'>".number_format($row['valor'], 2, ',', '.')."</td>
                          </tr>";
                    $i++;
                }                
            ?>                
            </tbody>
            <tfoot>							
                <tr>
                    <!-- Total de registros -->
                    <td style="text-align:center;" colspan="7"><?=$total?></td>                
                </tr>					
            </tfoot>
        </table>
    </div>
</div>
<div class="bloco_acoes"></div>