<table id="resultado_pesquisa" class="tableMoldura">
    <tbody>
        <tr class="tableSubTitulo">
            <td>
                <h2>Resultado da Pesquisa</h2>
            </td>
        </tr>
        <tr>
            <td align="center">
                <table width="100%">
                    <thead>
                        <tr class="tableTituloColunas">                            
                            <td align="center"><h3>Data</h3></td>
                            <td align="center"><h3>Arquivo</h3></td>
                            <td align="center"><h3>Usu&aacute;rio</h3></td>
                            <td align="center"><h3>Status</h3></td>
                            <td align="center"><h3>Tipo Contrato</h3></td>
                            <td align="center"><h3>Origem</h3></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $total = 0;
                            if($dados != null){                                
                                foreach($dados as $row){
                                    $class   = ($total % 2) ? 'class="tde9"' : 'class="tdc9"';
                                    $caminho = $row['caminho'];
                                    
                                    echo '<tr '.$class.'>                                            
                                            <td align="center">'.$row['data'].'</td>
                                            <td><a href="#" onclick="downloadFile(\''.$caminho.'\');">'.$row['arquivo'].'</a></td>
                                            <td>'.$row['usuario'].'</td>
                                            <td>'.$row['status'].'</td>
                                            <td>'.$row['tipo_contrato'].'</td>
                                            <td>'.utf8_encode($row['origem']).'</td>
                                          </tr>';
                                    $total++;
                                }                                
                            } else{
                                echo '<tr class="tdc9">
                                        <td align="center" colspan="6"><h3>N&atilde;o h&aacute; resultado para a pesquisa.</h3></td>
                                      </tr>';
                            }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr class="tableRodapeModelo1">                            
                            <td align="center" colspan="6"><h3><?=$total?> registro(s)</h3></td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </tbody>
</table>