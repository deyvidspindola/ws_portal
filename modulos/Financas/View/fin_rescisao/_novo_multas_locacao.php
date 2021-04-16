<div class="bloco_titulo">Multas sobre o valor de locação + Acessórios </div>

<?php if ($contratosMultasLocacao): ?>
    <div class="bloco_conteudo">  
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th>Contrato</th>
                        <th>% Multa</th>
                        <th>Meses restantes</th>
                        <th>Valor do contrato (locacao)</th>
                        <th>Valor do contrato (acessórios)</th>
                        <th>Valor da multa</th>
                     </tr>
                </thead>

                <tbody>
                    <?php
                  
                    foreach ($contratosMultasLocacao as $contrato): 
                        
                     if (intval($contrato['mesesFaltantes']) == 0 && floatval($contrato['descontoProRataLocacao']) <= 0) {
                          continue;
                      }
                     
                      $style = "";
                      if (intval($contrato['mesesFaltantes']) == 0 && floatval($contrato['descontoProRataLocacao']) > 0) {
                          $style = 'style="display:none"';
                      }
                      ?>
                        <tr <?= $style ?>>
                            <td class="multa-locacao-connumero">
                            <input type="hidden" value="<?= $contrato['connumero']; ?>" class="contrato-multa-locacao" />
                            <input type="hidden" value="<?= $contrato['percentualMulta'] ?>" class="multa-locacao-percentual-multa" />
                            <input type="hidden" value="<?= $contrato['mesesFaltantes'] ?>" class="multa-locacao-meses-faltantes" />
                            <input type="hidden" value="<?= toMoney($contrato['valor_modulo']) ?>" class="multa-locacao-valor-modulo"/>
                            <input type="hidden" value="<?= toMoney($contrato['valor_acessorio']) ?>" class="multa-locacao-valor-acessorio" />
                            <input type="hidden" value="<?= toMoney($contrato['totalMultaLocacaoAcessorios']) ?>" class="multa-locacao-total-multa-locacao-acessorio" />
                            <input type="hidden" value="<?= toMoney($contrato['descontoProRataLocacao']) ?>" class="multa-locacao-desconto-pro-rata" />
                            <?= $contrato['connumero'] ?></td>
                            <td ><?= $contrato['percentualMulta'] ?></td>
                            <td > <?= $contrato['mesesFaltantes'] ?></td>
                            <td ><?= toMoney($contrato['valor_modulo']) ?></td>
                            <td ><?= toMoney($contrato['valor_acessorio']) ?></td>
                            <td ><?= toMoney($contrato['totalMultaLocacaoAcessorios']) ?></td>
                        </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?> 
    <div class="bloco_acoes">
        <p><strong>Nenhum registro encontrado.</strong></p>
    </div>
<?php endif ?> 