<link type="text/css" rel="stylesheet" href="lib/css/style.css" />

<script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
<script type="text/javascript" src="lib/js/bootstrap.js"></script>
<script type="text/javascript" src="lib/js/jquery-ui-1.10.0.custom.min.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>
<script type="text/javascript" src="lib/funcoes_masc_novo.js"></script>
<script type="text/javascript" src="modulos/web/js/prn_gestao_frota_contrato_vivo.js"></script>

<!-- jQuery UI -->
<link type="text/css" rel="stylesheet" href="lib/css/cupertino/jquery-ui-1.10.0.custom.min.css" /> 

<div class="modulo_titulo">Histórico de O.S</div>
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
                            <th width="70">Quant. <?php echo (int) $ordensServico['total_registros'] ?><span id="qtd_os"></span></th>                            
                            <th>
                                <table>
                                    <tr>
                                        <th>
                                            <span class="float-left"> Pesquisar O.S.</span>
                                            <input type="text" name="numero_os" class="float-left" id="numero_os" maxlength="10"> 
                                            <button id="btn_pesquisar_os" class="float-left" type="button">Pesquisar</button>
                                        </th>
                                        <th>
                                            <span class="float-left"> Pesquisar ID Vivo</span>
                                            <input type="text" name="idvivo_os" class="float-left" id="idvivo_os" maxlength="30" size="30"> 
                                            <button id="btn_pesquisar_os_idvivo" class="float-left" type="button">Pesquisar</button>
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
        
        <div id="tabela_os" class="bloco_conteudo">
            <div class="listagem">
                <table>
                    <thead>
                        <tr>
                            <th>ID VIVO</th>
                            <th>Placa</th>
                            <th>Protocolo Vivo</th>
                            <th>Protocolo Sascar</th>                            
                            <th>N° O.S.</th>
                            <th>Motivo da O.S.</th>
                            <th>Status</th>
                            <th>Última Ação</th>
                            <th>Defeito Alegado</th>
                            <th>Data de Abertura</th>
                            <th>Data de Encerramento</th>
                            <th>Tempo de Conclusão</th>
                            <th>Atendente</th>
                        </tr>
                    </thead> 
                    <tbody id="conteudo_grid_os">   
                        
                        <?php foreach($ordensServico['resultados'] as $os): ?>

                        <tr>
                            <td class="centro"><?php echo $os->idvivo ?></td>                        
                            <td class="direita">
                                <a class="placa_veiculo" href="prn_gestao_frota_contrato_vivo.php?acao=carregarDadosHistoricoContatos&connumero=<?php echo $os->connumero ?>" target="_blank">
                                    <?php echo $os->placa ?>
                                </a>
                            </td>
                            <td class="direita"><?php echo $os->protocolo_vivo ?></td>
                            <td class="direita"><?php echo $os->protocolo_sascar ?></td>
                            <td class="direita">
                                <a class="placa_veiculo" href="prn_gestao_frota_contrato_vivo.php?acao=carregarDadosHistoricoOrdemServico&ordoid=<?php echo $os->ordem_servico ?>" target="_blank">
                                <?php echo $os->ordem_servico ?>
                                </a>
                            </td>
                            <td><?php echo utf8_decode($os->motivo) ?></td>
                            <td><?php echo utf8_decode($os->status) ?></td>
                            <td><?php echo utf8_decode($os->ultima_acao) ?></td>
                            <td><?php echo utf8_decode($os->defeito_alegado) ?></td>                            
                            <td class="centro"><?php echo $os->data_abertura ?></td>
                            <td class="centro"><?php echo $os->data_encerramento ?></td>
                            <td><?php echo $os->tempo_conclusao ?></td>
                            <td><?php echo utf8_decode($os->atendente) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>                
            </div>
        </div>
        
        <div class="separador" id="tabela_os_separador"></div>

        <div id="alerta_grid_os" class="mensagem alerta invisivel"></div>

        <div id="loader_grid_os" class="carregando invisivel"></div>
        
    </form>
</div>