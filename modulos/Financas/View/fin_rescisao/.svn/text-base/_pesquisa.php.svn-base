<? if (isset($resultados)): ?>
<div class="separador"></div>

<div class="bloco_titulo">Resultados da Pesquisa</div>
<div class="bloco_conteudo">   
    <? if ($resultados && count($resultados)): ?>
        <div class="listagem">
            <table class="resultados-pesquisa-rescisao">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>Rescisão</th>	
                        <th>Cidade</th>
                        <th>Cliente</th>
                        <th>Fone</th>
                        <th>Contrato</th>
                        <th>O.S.</th>
                        <th>Retirada</th>
                        <th>Pend.</th>
                        <th>Status(C)</th>
                        <th>Status(R)</th>
                        <th>Decl. Eqpto</th>
                        <th>Veículo</th>
                        <th>Placa</th>
                        <th>Data</th>
                        <th>Respons.</th>
                        <th>Motivo</th>
                    </tr>
                </thead>
                
                <tbody>		
                <?
                    $currentUf = $resultados[0]['uf'];
                    $ufCount   = 0;
                    $currentResmoid = 0;
                ?>
                <? foreach ($resultados as $item): ?>
                    <!-- Cria row de totalizador de UF, se a UF atual for diferente -->
                    <? if ($currentUf != $item['uf']): ?>
                        <tr class="totalizador">
                            <td colspan="20">
                                Total <?= strtoupper($currentUf) ?>: <?= $ufCount ?>
                            </td>
                        </tr>
                        <? 
                            $currentUf = $item['uf'];
                            $ufCount   = 1;
                         ?>
                    <? else: ?>
                        <? $ufCount += 1 ?>
                    <? endif ?>
                    
                    <tr>
                        <td>
                            <? if ($currentResmoid == 0 || $currentResmoid != $item['resmoid']): ?>
                                <input type="checkbox" class="imprimir-segunda-via-check"
                                    data-resmoid="<?= $item['resmoid'] ?>" />
                            <? endif ?>
                        </td>
                        <td>
                            <? if ($currentResmoid == 0 || $currentResmoid != $item['resmoid']): ?>
                                <a href="fin_rescisao.php?acao=visualizar&resmoid=<?= $item['resmoid'] ?>">
                                    <?= $item['resmoid'] ?>
                                </a>
                                <? $currentResmoid = $item['resmoid'] ?>
                            <? endif ?>
                        </td>
                        <td><?= $item['cidade'] ?></td>
                        <td><?= $item['clinome'] ?></td>
                        <td><?= $item['fone'] ?></td>
                        <td><?= $item['connumero'] ?></td>
                        <td><?= $item['ord_serv'] ?></td>
                        <td><?= $item['retirada'] ?></td>
                        <td><?= $item['pendente'] ?></td>
                        <td>
                            <?
                                if ($item['conno_tipo'] == 14)
                                {
                                    echo 'Rec.Eqpto';
                                }
                                elseif (strlen($item['condt_exclusao']))
                                {
                                    echo "Cancelado";
                                }
                                else
                                {
                                    echo "Ativo";
                                }
                            ?>
                        </td>
                        <td>
                            <? 
                                if ($item['rescstatus'] == 'R')
                                {
                                    echo 'Env. p/ Retirada';
                                }
                                elseif ($item['rescstatus'] == 'D')
                                {
                                    echo 'Aguardando Dep.';
                                }
                                elseif ($item['rescstatus'] == 'A')
                                {
                                    echo 'Arquivado';
                                }
                                elseif ($item['rescstatus'] == 'E')
                                {
                                    echo 'Recup Equip Obs.';
                                }
                            ?>
                        </td>
                        <td><?= $item['declaracao'] ?></td>
                        <td>
                            <?= $this->_dao->getNomeVeiculo($item['veioid']) ?>
                        </td>
                        <td><?= $item['veiplaca'] ?></td>
                        <td><?= $item['rescisao'] ?></td>
                        <td>    
                            <?= $this->_dao->getNomeResponsavel($item['resmoid']) ?>
                        </td>
                        <td><?= $item['mrescdescricao'] ?></td>
                    </tr>	
                <? endforeach ?>
                
                    <!-- Último totalizador de estado -->
                    <tr class="totalizador">
                        <td colspan="17">
                            Total <?= strtoupper($currentUf) ?>: <?= $ufCount ?>
                        </td>
                    </tr>
                </tbody>
                
                <tfoot>
                    <tr>
                        <td colspan="17" align="left">
                            <a class="botao imprimir-segunda-via">Imprimir 2ª via</a>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>            
    <? endif ?>        
</div>

<div class="bloco_acoes">
    <? if ($resultados && count($resultados)): ?>
        <p><?= count($resultados) ?> registro(s) encontrado(s)</p>
    <? else: ?>
        <p>Nenhum registro encontrado.</p>
    <? endif ?>
</div>
<? endif ?>


        