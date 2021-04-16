<?php if(!empty($dados_pesquisa['resultado_data_hora'])): ?>
    <?php foreach($dados_pesquisa['resultado_data_hora'] as $resultado): ?>

    <?php $total_registros_dia = 0; ?>
        
            <tr>
                <td style="border-top: 1px solid #94ADC2;" colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr class="tableTituloColunas">    
                <td colspan="1"><h3>Data - Hora</h3></td>
                <td colspan="3" style="text-align: center;"><h3>Detalhe de Motivos hora a hora do dia <?php echo $resultado['data'] ?></h3></td>
            </tr> 
            <tr class="tableTituloColunas">    
                <td width="10%" style="text-align: left;"><h3><?php echo $resultado['data'] ?></h3></td>
                <td width="70%"><h3>Motivos</h3></td>
                <td width="10%" style="text-align: center;"><h3>Qtde. x Motivo</h3></td>
                <td width="10%" style="text-align: center;"><h3>Total</h3></td>
            </tr>             
        
            <?php for($i = 0; $i < 24; $i++): ?>


            <?php 

                $hora = $i < 10 ? '0'.$i : $i;
                $motivos_hora = isset($resultado['horas'][$hora]) ? $resultado['horas'][$hora] : array();
                $total_registros_hora = 0;

            ?>
        
            <tr class="result">    
                <td width="10%"><?php echo $hora ?>:00 - <?php echo $hora ?>:59</td>
                <td width="70%">
                    <?php foreach($motivos_hora as $motivo): ?>                
                    <div style="white-space: nowrap; display: block; margin: 5px 0;"><?php echo $motivo['label'] ?></div>                    
                    <?php endforeach; ?>
                </td>
                <td width="10%" style="text-align: center;">
                    <?php foreach($motivos_hora as $motivo): ?>                
                    <div style="display: block; margin: 5px 0;"><?php echo count($motivo['registros']) ?></div>                    
                    <?php $total_registros_hora += count($motivo['registros']) ?>
                    <?php endforeach; ?>
                </td>
                <td width="10%" style="text-align: center;">
                    <?php 
                        $total_registros_dia += $total_registros_hora;
                        echo $total_registros_hora;
                    ?>
                </td>
            </tr> 
         
        <?php endfor; ?>
         
        <tr class="tableTituloColunas">
            <td><h3>Total do dia:</h3></td>
            <td colspan="2">&nbsp;</td>
            <td style="text-align: center;"><h3><?php echo $total_registros_dia ?></h3></td>
        </tr>        
   
        <tr>
            <td style="border-bottom: 1px solid #94ADC2;" colspan="4">&nbsp;</td>
        </tr>
        
        <tr class="tableTituloColunas">    
            <td style="text-align: left;"><h3><?php echo $resultado['data'] ?></h3></td>
            <td colspan="2" style="text-align: center;"><h3>Motivos de Atendimento do dia <?php echo $resultado['data'] ?></h3></td>
            <td style="text-align: center;"><h3>Total</h3></td>
        </tr> 
        <tr class="tableTituloColunas">    
            <td colspan="3"><h3>Total de Motivos</h3></td>
            <td style="text-align: center;"><h3><?php echo $total_registros_dia ?></h3></td>
        </tr>             
        

        <?php foreach($resultado['motivos'] as $motivo): ?>
        <tr class="result">
            <td colspan="3" style="text-align: left;"><?php echo $motivo['label'] ?></td>
            <td style="text-align: center;"><?php echo count($motivo['registros']) ?></td>
        </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>    
<?php endif; ?>    