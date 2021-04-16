<?php $contador_revenda = 0; ?>
<?php $contador_varejo = 0; ?>
<?php $contador_vivo = 0; ?>

<!-- REVENDA -->
<?php if(!empty($revenda[$uf])): ?>
<?php foreach ($revenda[$uf] as $i => $array_revenda): ?>

<tr class="tdc">
    <td colspan="10">Revenda</td>
</tr>

<?php foreach ($array_revenda['motivos'] as $motivo_revenda): ?>
<?php $css_class = $contador_revenda % 2 == 0 ? 'tde' : 'tdc'; ?>

<tr class="<?php echo $css_class ?>">
  <td>
      <a href="fin_det_comissao_novo.php?motivo=<?php echo urlencode($motivo_revenda['motivo']);?>&periodo_inicial_busca=<?php echo urlencode($_POST['periodo_inicial_busca'])?>&periodo_final_busca=<?php echo urlencode($_POST['periodo_final_busca'])?>&uf_busca=<?php echo urlencode($uf)?>&representante_busca=<?php echo $_POST['representante_busca']?>&classe_busca=<?php echo $_POST['classe_busca']?>&situacao_busca=<?php echo $_POST['situacao_busca']?>&tipo_contrato_busca=<?php echo $array_revenda['tipo_contrato']?>&instaladoroid=<?php echo $_POST['instalador_busca']?>&representante_terceiro_busca=<?php echo $_POST['representante_terceiro_busca']?>&conmodalidade=V" target="_blank">
      <?php echo $motivo_revenda['motivo'];?></a>
  </td>
  <td style="text-align:right"><?php echo number_format($motivo_revenda['deslocamento'],0,",",".");?></td>
  <td style="text-align:right"><?php echo number_format($motivo_revenda['vl_km'],2,",",".");?></td>
  <td style="text-align:right"><?php echo $motivo_revenda['qtde'];?></td>
  <td style="text-align:right"><?php echo $motivo_revenda['ordem'];?></td>
  <td style="text-align:right"><?php echo number_format($motivo_revenda['valor_servico'],2,",",".");?></td>
  <td style="text-align:right"><?php echo number_format($motivo_revenda['pedagio'],2,",",".");?></td>
  <td style="text-align:right"><?php echo number_format($motivo_revenda['ceo'],2,",",".");?></td>
  <td style="text-align:right"><?php echo number_format($motivo_revenda['total'],2,",",".");?></td>
</tr>
<?php
      //Resultados parciais (Total Por Revenda)
      $pctdeslocamento+=$motivo_revenda['deslocamento'];
      $pctvl_km+=$motivo_revenda['vl_km'];
      $pctqtde+=$motivo_revenda['qtde'];
      $pctordem+=$motivo_revenda['ordem'];
      $pctvalor_servico+=$motivo_revenda['valor_servico'];
      $pctpedagio+=$motivo_revenda['pedagio'];
      $pctceo+=$motivo_revenda['total_ceo'];
      $pcttotal+=$motivo_revenda['total'];

      //Resultados parciais (Total Por Estado)
      $ptdeslocamento+=$motivo_revenda['deslocamento'];
      $ptvl_km+=$motivo_revenda['vl_km'];
      $ptqtde+=$motivo_revenda['qtde'];
      $ptordem+=$motivo_revenda['ordem'];
      $ptvalor_servico+=$motivo_revenda['valor_servico'];
      $ptpedagio+=$motivo_revenda['pedagio'];
      $ptceo+=$motivo_revenda['ceo'];
      $pttotal+=$motivo_revenda['total'];

      //Resultados totais (Total Geral)
      $tdeslocamento+=$motivo_revenda['deslocamento'];
      $tvl_km+=$motivo_revenda['vl_km'];
      $tqtde+=$motivo_revenda['qtde'];
      $tordem+=$motivo_revenda['ordem'];
      $tvalor_servico+=$motivo_revenda['valor_servico'];
      $tpedagio+=$motivo_revenda['pedagio'];
      $tceo+=$motivo_revenda['ceo'];
      $ttotal+=$motivo_revenda['total'];

      $contador_revenda++;

      endforeach;
?>
<tr class="ttotal tableTituloColunas">
  <td style="text-align:left"><b>Total Revenda</b></td>
  <td style="text-align:right"><b><?php echo number_format($pctdeslocamento,0,",",".")?></b></td>
  <td style="text-align:right"><b><?php echo number_format($pctvl_km,2,",",".")?></b></td>
  <td style="text-align:right"><b><?php echo $pctqtde?></b></td>
  <td style="text-align:right"><b><?php echo $pctordem?></b></td>
  <td style="text-align:right"><b><?php echo number_format($pctvalor_servico,2,",",".")?></b></td>
  <td style="text-align:right"><b><?php echo number_format($pctpedagio,2,",",".")?></b></td>
  <td style="text-align:right"><b><?php echo number_format($pctceo,2,",",".")?></b></td>
  <td style="text-align:right"><b><?php echo number_format($pcttotal,2,",",".")?></b></td>
</tr>
<?php
      //Resultados parciais (Total Por Revenda) 
      $tceorevenda = $pctceo;
      $ttceorevenda += $tceorevenda;
      $pctdeslocamento  = 0;
      $pctvl_km         = 0;
      $pctqtde          = 0;
      $pctordem         = 0;
      $pctvalor_servico = 0;
      $pctpedagio       = 0;
      $pcttotal         = 0;
      $pctceo           = 0;
      endforeach;
?>
<?php endif; ?>

<!-- Varejo -->
<?php if(!empty($varejo[$uf])): ?>
<?php foreach ($varejo[$uf] as $i => $array_varejo): ?>

<tr class="tdc">
    <td colspan="10">Varejo</td>
</tr>

<?php foreach ($array_varejo['motivos'] as $motivo_varejo): ?>
<?php $css_class = $contador_varejo % 2 == 0 ? 'tde' : 'tdc'; ?>

<tr class="<?php echo $css_class ?>">
  <td>
      <a href="fin_det_comissao_novo.php?motivo=<?php echo urlencode($motivo_varejo['motivo']);?>&periodo_inicial_busca=<?php echo urlencode($_POST['periodo_inicial_busca'])?>&periodo_final_busca=<?php echo urlencode($_POST['periodo_final_busca'])?>&uf_busca=<?php echo urlencode($uf)?>&representante_busca=<?php echo $_POST['representante_busca']?>&classe_busca=<?php echo $_POST['classe_busca']?>&situacao_busca=<?php echo $_POST['situacao_busca']?>&tipo_contrato_busca=<?php echo $array_varejo['tipo_contrato']?>&instaladoroid=<?php echo $_POST['instalador_busca']?>&representante_terceiro_busca=<?php echo $_POST['representante_terceiro_busca']?>" target="_blank">
      <?php echo $motivo_varejo['motivo'];?></a>
  </td>
  <td style="text-align:right"><?php echo number_format($motivo_varejo['deslocamento'],0,",",".");?></td>
  <td style="text-align:right"><?php echo number_format($motivo_varejo['vl_km'],2,",",".");?></td>
  <td style="text-align:right"><?php echo $motivo_varejo['qtde'];?></td>
  <td style="text-align:right"><?php echo $motivo_varejo['ordem'];?></td>
  <td style="text-align:right"><?php echo number_format($motivo_varejo['valor_servico'],2,",",".");?></td>
  <td style="text-align:right"><?php echo number_format($motivo_varejo['pedagio'],2,",",".");?></td>
  <td style="text-align:right"><?php echo number_format($motivo_varejo['ceo'],2,",",".");?></td>
  <td style="text-align:right"><?php echo number_format($motivo_varejo['total'] + $motivo_varejo['ceo'],2,",",".");?></td>
</tr>
<?php
      //Resultados parciais (Total Por Varejo)
      $pctdeslocamento+=$motivo_varejo['deslocamento'];
      $pctvl_km+=$motivo_varejo['vl_km'];
      $pctqtde+=$motivo_varejo['qtde'];
      $pctordem+=$motivo_varejo['ordem'];
      $pctvalor_servico+=$motivo_varejo['valor_servico'];
      $pctpedagio+=$motivo_varejo['pedagio'];
      $pctceo+=$motivo_varejo['ceo'];
      $pcttotal+=$motivo_varejo['total'];

      //Resultados parciais (Total Por Estado)
      $ptdeslocamento+=$motivo_varejo['deslocamento'];
      $ptvl_km+=$motivo_varejo['vl_km'];
      $ptqtde+=$motivo_varejo['qtde'];
      $ptordem+=$motivo_varejo['ordem'];
      $ptvalor_servico+=$motivo_varejo['valor_servico'];
      $ptpedagio+=$motivo_varejo['pedagio'];
      $ptceo+=$motivo_varejo['ceo'];
      $pttotal+=$motivo_varejo['total'];

      //Resultados totais (Total Geral)
      $tdeslocamento+=$motivo_varejo['deslocamento'];
      $tvl_km+=$motivo_varejo['vl_km'];
      $tqtde+=$motivo_varejo['qtde'];
      $tordem+=$motivo_varejo['ordem'];
      $tvalor_servico+=$motivo_varejo['valor_servico'];
      $tpedagio+=$motivo_varejo['pedagio'];
      $tceo+=$motivo_varejo['ceo'];
      $ttotal+=$motivo_varejo['total'];

      $contador_varejo++;

      endforeach;
?>
<tr class="ttotal tableTituloColunas">
  <td style="text-align:left"><b>Total Varejo</b></td>
  <td style="text-align:right"><b><?php echo number_format($pctdeslocamento,0,",",".")?></b></td>
  <td style="text-align:right"><b><?php echo number_format($pctvl_km,2,",",".")?></b></td>
  <td style="text-align:right"><b><?php echo $pctqtde?></b></td>
  <td style="text-align:right"><b><?php echo $pctordem?></b></td>
  <td style="text-align:right"><b><?php echo number_format($pctvalor_servico,2,",",".")?></b></td>
  <td style="text-align:right"><b><?php echo number_format($pctpedagio,2,",",".")?></b></td>
  <td style="text-align:right"><b><?php echo number_format($pctceo,2,",",".")?></b></td>
  <td style="text-align:right"><b><?php echo number_format($pcttotal + $pctceo,2,",",".")?></b></td>
</tr>
<?php
      //Resultados parciais (Total Por Varejo) 
      $tceovarejo = $pctceo;
      $ttceovarejo += $tceovarejo;
      $pctdeslocamento  = 0;
      $pctvl_km         = 0;
      $pctqtde          = 0;
      $pctordem         = 0;
      $pctvalor_servico = 0;
      $pctpedagio       = 0;
      $pcttotal         = 0;
      $pctceo           = 0;
      endforeach;
?>
<?php endif; ?>

<!-- VIVO -->
<?php if(!empty($vivo[$uf])): ?>
<?php foreach ($vivo[$uf] as $i => $array_vivo): ?>

<tr class="tdc">
    <td colspan="10">VIVO</td>
</tr>

<?php foreach ($array_vivo['motivos'] as $motivo_vivo): ?>          
<?php $css_class = $contador_vivo % 2 == 0 ? 'tde' : 'tdc'; ?>

<tr class="<?php echo $css_class ?>">
  <td>
      <a href="fin_det_comissao_novo.php?motivo=<?php echo urlencode($motivo_vivo['motivo']);?>&periodo_inicial_busca=<?php echo urlencode($_POST['periodo_inicial_busca'])?>&periodo_final_busca=<?php echo urlencode($_POST['periodo_final_busca'])?>&uf_busca=<?php echo urlencode($uf)?>&representante_busca=<?php echo $_POST['representante_busca']?>&classe_busca=<?php echo $_POST['classe_busca']?>&situacao_busca=<?php echo $_POST['situacao_busca']?>&tipo_contrato_busca=<?php echo $array_vivo['tipo_contrato']?>&instaladoroid=<?php echo $_POST['instalador_busca']?>&representante_terceiro_busca=<?php echo $_POST['representante_terceiro_busca']?>" target="_blank">
      <?php echo $motivo_vivo['motivo'];?></a>
  </td>
  <td style="text-align:right"><?php echo number_format($motivo_vivo['deslocamento'],0,",",".");?></td>
  <td style="text-align:right"><?php echo number_format($motivo_vivo['vl_km'],2,",",".");?></td>
  <td style="text-align:right"><?php echo $motivo_vivo['qtde'];?></td>
  <td style="text-align:right"><?php echo $motivo_vivo['ordem'];?></td>
  <td style="text-align:right"><?php echo number_format($motivo_vivo['valor_servico'],2,",",".");?></td>
  <td style="text-align:right"><?php echo number_format($motivo_vivo['pedagio'],2,",",".");?></td>
  <td style="text-align:right"><?php echo number_format($motivo_vivo['ceo'],2,",",".");?></td>
  <td style="text-align:right"><?php echo number_format($motivo_vivo['total'] + $motivo_vivo['ceo'],2,",",".");?></td>
</tr>
<?php
      //Resultados parciais (Total Por VIVO)
      $pctdeslocamento+=$motivo_vivo['deslocamento'];
      $pctvl_km+=$motivo_vivo['vl_km'];
      $pctqtde+=$motivo_vivo['qtde'];
      $pctordem+=$motivo_vivo['ordem'];
      $pctvalor_servico+=$motivo_vivo['valor_servico'];
      $pctpedagio+=$motivo_vivo['pedagio'];
      $pctceo+=$motivo_vivo['ceo'];
      $pcttotal+=$motivo_vivo['total'];

      //Resultados parciais (Total Por Estado)
      $ptdeslocamento+=$motivo_vivo['deslocamento'];
      $ptvl_km+=$motivo_vivo['vl_km'];
      $ptqtde+=$motivo_vivo['qtde'];
      $ptordem+=$motivo_vivo['ordem'];
      $ptvalor_servico+=$motivo_vivo['valor_servico'];
      $ptpedagio+=$motivo_vivo['pedagio'];
      $ptceo+=$motivo_vivo['ceo'];
      $pttotal+=$motivo_vivo['total'];

      //Resultados totais (Total Geral)
      $tdeslocamento+=$motivo_vivo['deslocamento'];
      $tvl_km+=$motivo_vivo['vl_km'];
      $tqtde+=$motivo_vivo['qtde'];
      $tordem+=$motivo_vivo['ordem'];
      $tvalor_servico+=$motivo_vivo['valor_servico'];
      $tpedagio+=$motivo_vivo['pedagio'];
      $tceo+=$motivo_vivo['ceo'];
      $ttotal+=$motivo_vivo['total'];
      $contador_vivo++;
      endforeach;
?>
<tr class="ttotal tableTituloColunas">
  <td style="text-align:left"><b>Total VIVO</b></td>
  <td style="text-align:right"><b><?php echo number_format($pctdeslocamento,0,",",".")?></b></td>
  <td style="text-align:right"><b><?php echo number_format($pctvl_km,2,",",".")?></b></td>
  <td style="text-align:right"><b><?php echo $pctqtde?></b></td>
  <td style="text-align:right"><b><?php echo $pctordem?></b></td>
  <td style="text-align:right"><b><?php echo number_format($pctvalor_servico,2,",",".")?></b></td>
  <td style="text-align:right"><b><?php echo number_format($pctpedagio,2,",",".")?></b></td>
  <td style="text-align:right"><b><?php echo number_format($pctceo,2,",",".")?></b></td>
  <td style="text-align:right"><b><?php echo number_format($pcttotal + $pctceo,2,",",".")?></b></td>
</tr>
<?php
      //Resultados parciais (Total Por VIVO)
      
      $tceovivo = $pctceo;
      $ttceovivo += $tceovivo;
      $pctdeslocamento  = 0;
      $pctvl_km         = 0;
      $pctqtde          = 0;
      $pctordem         = 0;
      $pctvalor_servico = 0;
      $pctpedagio       = 0;
      $pcttotal         = 0;
      $pctceo           = 0;
      endforeach;
?>
<?php endif; ?>