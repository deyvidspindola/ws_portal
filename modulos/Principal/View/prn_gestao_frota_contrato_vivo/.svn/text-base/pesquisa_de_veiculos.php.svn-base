<link type="text/css" rel="stylesheet" href="lib/css/style.css" />

<script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
<script type="text/javascript" src="lib/js/bootstrap.js"></script>
<script type="text/javascript" src="lib/js/jquery-ui-1.10.0.custom.min.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>
<script type="text/javascript" src="lib/funcoes_masc_novo.js"></script>
<script type="text/javascript" src="modulos/web/js/prn_gestao_frota_contrato_vivo.js"></script>

<!-- jQuery UI -->
<link type="text/css" rel="stylesheet" href="lib/css/cupertino/jquery-ui-1.10.0.custom.min.css" /> 

<div class="modulo_titulo">Veículos</div>
<div class="modulo_conteudo">
    <form>
        
        <?php if(empty($clioid)): ?>
        <div class="mensagem alerta">É necessário ter pesquisado um cliente previamente.</div>
        <?php endif; ?>
        
        <input type="hidden" name="clioid_hidden" id="clioid_hidden" value="<?php echo $clioid ?>" /> 
        
        <div class="bloco_conteudo">
            <div class="listagem">
                <table>
                    <thead>
                        <tr>                       
                            <th width="70"><span class="float-left">Quant. <?php echo (int)$veiculos['total_ativos'] ?></span> <span class="float-left" id="qtd_veiculos_ativos"></span></th>                            
                            <th>
                                <table>
                                    <tr>
                                        <th>
                                            <span class="float-left"> Pesquisar Placa</span>
                                            <input type="text" name="placa" class="float-left" id="placa" maxlength="10"> 
                                            <button id="btn_pesquisar_veiculos" class="float-left" type="button">Pesquisar</button>
                                        </th>
                                        <th>
                                            <span class="float-left"> Pesquisar ID Vivo</span>
                                            <input type="text" name="idvivo_veiculo" class="float-left" id="idvivo_veiculo" maxlength="30" size="30"> 
                                            <button id="btn_pesquisar_veiculos_idvivo" class="float-left" type="button">Pesquisar</button>
                                        </th>
                                    </tr>
                                </table>
                            </th>
                        </tr>
                    </thead>                    
                </table>                
            </div>
        </div>

        <div class="separador"></div>

        <div id="tabela_veiculos" class="bloco_conteudo">
            <div class="listagem">
                <table>
                    <thead>
                        <tr>
                            <th>ID VIVO</th>
                            <th>Placa</th>
                            <th>Status</th>                            
                            <th>Produto</th>
                            <th>Valor do Serviço</th>
                            <th>Parcela do Equipamento</th>
                            <th>Valor da Parcela</th>
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Tempo de Contrato</th>
                        </tr>
                    </thead> 
                    <tbody id="conteudo_veiculos">
                        <?php foreach($veiculos['resultados'] as $veiculo): ?>

                        <tr>
                            <td class="centro"><?php echo $veiculo->idvivo ?></td>                        
                            <td class="direita">
                                <a class="placa_veiculo" href="prn_gestao_frota_contrato_vivo.php?acao=carregarDadosHistoricoContatos&connumero=<?php echo $veiculo->connumero ?>" target="_blank">
                                    <?php echo $veiculo->placa ?>
                                </a>
                            </td>
                            <td><?php echo $veiculo->status ?></td>
                            <td><?php echo utf8_decode($veiculo->descricao) ?></td>
                            <td class="direita"><?php echo $veiculo->valorServico ?></td>
                            <?php if(!trim($veiculo->parcela) == ""): ?>
                            <td>                            
                                <?php echo str_pad($veiculo->parcela, 2, '0', STR_PAD_LEFT) ?>/<?php echo str_pad($veiculo->totalParcelas, 2, '0', STR_PAD_LEFT) ?>
                            </td>
                            <?php else: ?>
                            <td>&nbsp;</td>
                            <?php endif; ?>                        
                            <td class="direita"><?php echo $veiculo->valorParcela ?></td>
                            <td class="centro"><?php echo $veiculo->dataInicio ?></td>
                            <td class="centro"><?php echo $veiculo->dataFim ?></td>
                            <td><?php echo $veiculo->tempoContrato ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>                
            </div>
        </div>

        <div class="separador" id="tabela_veiculos_separador"></div>

        <div id="alerta_veiculos" class="mensagem alerta invisivel"></div>

        <div id="loader_veiculos" class="carregando invisivel"></div>
    </form>
</div>