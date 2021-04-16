 <div class="separador"></div>
    <div class="bloco_titulo">Funcionalidades do Pacote</div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="esquerda">Funcionalidade<br>(Pacote Monitoramento)</th>                   
                        <th class="esquerda">Tag</th>                   
                        <th class="esquerda">Status</th>                   
                        <th class="esquerda">Aprovada</th>                   
                        <th class="esquerda">Aprovador</th>                   
                        <th class="esquerda"><?php  print utf8_encode('Data Aprovação');?></th>
                    </tr>
                </thead>
                 <?php $classeLinha = "par"; ?>
                    <?php $totalGeral = 0; $i= 0; ?>
					
                    <?php if(!empty($this->view->dados)):
                           foreach ($this->view->dados as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : "";  $i++; ?>
                <tbody>
                    <tr class="<?php echo $classeLinha; ?>">
                        <td><?php echo utf8_encode($resultado['descricao']); ?></td>                 
                        <td><?php echo utf8_encode($resultado['tag']); ?></td>
                        <td><?php echo utf8_encode($resultado['status']); ?></td>
                        <td><img src="modulos/web/images/<?php print $var = $resultado['status']== "Em Aprovação" ?"checkbox_unchecked_icon.png":"checkbox_checked_icon.png"?>" height="15" title="<?php echo utf8_encode($resultado['status']); ?>" width="15" border="0"></td>
                        <td><?php echo utf8_encode($resultado['aprovador']); ?></td>
                        <td><?php print  $resultado['data_aprovacao']!=""? implode("/",array_reverse(explode("-",$resultado['data_aprovacao']))):"";?></td>
                    </tr>
                </tbody>
                <?php endforeach;endif;?>
                
                <tfoot>
                    <tr>
                        <td colspan="6"><?php print $i;?> registros encontrados.</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
