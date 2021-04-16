<!--lista-->

<script>
  $(function() {
    $( "#dialog" ).dialog({
      autoOpen: false,
      width: 'auto',
      show: {},
      hide: {}
    });
  });
 
  </script>
<meta charset="UTF-8" />
<div id="dialog" title="<?php print utf8_decode("Histórico");?>">
 <link type="text/css" rel="stylesheet" href="modulos/web/css/prn_modificacao_contrato.css" />
  <div class="separador"></div>
    <div class="bloco_titulo"><?php echo utf8_encode('Histórico'); ?></div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="esquerda"><?php echo utf8_encode('Data'); ?></th>                   
                        <th class="esquerda"><?php echo utf8_encode('Usuário'); ?></th>                   
                        <th class="esquerda"><?php echo utf8_encode('Ação'); ?></th>                   
                        <th class="esquerda"><?php echo utf8_encode('Valor Padrão'); ?></th>                   
                        <th class="esquerda"><?php echo utf8_encode('Valor Mínimo'); ?></th>                   
                        <th class="esquerda"><?php echo utf8_encode('Valor Máximo'); ?></th>
                        <th class="esquerda"><?php echo utf8_encode('Desc. Obrigação'); ?></th>
                        <th class="esquerda"><?php echo utf8_encode('Cancelamento'); ?></th>
                        <th class="esquerda"><?php echo utf8_encode('Obr. Única por Cliente'); ?></th>
                        <th class="esquerda"><?php echo utf8_encode('Status'); ?></th>
                    </tr>
                </thead>
                 <?php $classeLinha = "par"; ?>
                    <?php $totalGeral = 0; $i= 0; ?>
					
                    <?php if(!empty($dadosHistorico)):
                           foreach ($dadosHistorico as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : "";  $i++; ?>
                <tbody>
                    <tr class="<?php echo $classeLinha; ?>">
                        <td><?php echo utf8_encode(date("d/m/Y H:i:s", strtotime($resultado['data_cadastro']))); ?></td>                 
                        <td><?php echo utf8_encode($resultado['usuario']); ?></td>
                        <td><?php echo utf8_encode($resultado['acao']); ?></td>
                        <td><?php echo utf8_encode(empty($resultado['valor_padrao']) ? '' : 'R$ ' . number_format($resultado['valor_padrao'], 2, ',', '.')); ?></td>
                        <td><?php echo utf8_encode(empty($resultado['valor_minimo']) ? '' : 'R$ ' . number_format($resultado['valor_minimo'], 2, ',', '.')); ?></td>
                        <td><?php echo utf8_encode(empty($resultado['valor_maximo']) ? '' : 'R$ ' . number_format($resultado['valor_maximo'], 2, ',', '.')); ?></td>
                        <td><?php echo utf8_encode($resultado['descricao']); ?></td>
                        <td><?php echo utf8_encode(empty($resultado['data_cancelamento']) ? '' : date("d/m/Y H:i:s", strtotime($resultado['data_cancelamento']))); ?></td>
                        <td><?php echo utf8_encode($resultado['obr_unica'] === 'f' ? 'Não' : 'Sim'); ?></td>
                        <td><?php echo utf8_encode($resultado['status']); ?></td>
                    </tr>
                </tbody>
        
                <?php endforeach;endif;?>
                
                <tfoot>
                    <tr>
                        <td colspan="10"><?php print $i;?> registros encontrados.</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<!-- fim lista-->