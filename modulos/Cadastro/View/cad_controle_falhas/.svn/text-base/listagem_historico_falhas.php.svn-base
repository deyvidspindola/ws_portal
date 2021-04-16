<head>
    <!-- CSS -->
    <link type="text/css" rel="stylesheet" href="calendar/calendar.css"/>
    <link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
    <link type="text/css" rel="stylesheet" href="modulos/web/js/lib/jQueryUI/themes/base/jquery.ui.all.css"> 
    <link type="text/css" rel="stylesheet" href="modulos/web/css/cad_controle_falhas.css">

    <!-- JAVASCRIPT -->
    <script type="text/javascript" src="includes/js/calendar.js"></script>
    <script type="text/javascript" src="includes/js/mascaras.js"></script>
    <script type="text/javascript" src="includes/js/auxiliares.js"></script>
    <script type="text/javascript" src="includes/js/validacoes.js"></script>
    <script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
    <script type="text/javascript" src="modulos/web/js/lib/jQueryUI/js/jquery-ui-1.8.24.custom.min.js"></script>
    <script type="text/javascript" src="modulos/web/js/cad_controle_falhas.js"></script>
</head>

<form name="form" id="form" method="POST" action="cad_controle_falhas.php">
    <input type="hidden" name="acao" id="acao" />

    <center>
        
        <table class="tableMoldura">
            
            <tr class="tableTitulo">
                <td><h1>Controle de Falhas</h1></td>
            </tr>
            
            <tr>
                <td>
                    <span id="msg" class="msg"><? echo $this->msg; ?></span>
                </td>
            </tr>	
            
            <tr>
                <td align="center">
                    
                    <table class="tableMoldura">
                        
                        <tr class="tableSubTitulo">
                            <td colspan="2"><h2>Dados para pesquisa</h2></td>
                        </tr> 
                        
                        <tr>
                            <td width="10%">
                                <label>Serial: *</label>
                            </td>
                            <td width="90%">
                                <input type="text" name="equno_serie" id="equno_serie" maxlength="15" size="15" value="<?php echo $numero_serie;?>"><input type="text" style="display:none" />
                            </td>
                        </tr> 
                        
                        <tr class="tableRodapeModelo1">
                            <td colspan="2" align="center">
                                <input type="button" value="Pesquisar" name="btn_pesquisar_historico_falhas" id="btn_pesquisar_historico_falhas" class="botao">
                                <input type="button" value="Novo" name="btn_inserir_falhas" id="btn_inserir_falhas" class="botao">
                            </td>
                        </tr> 
                        
                    </table>
                    
                </td>
            </tr> 
            
            <?php if ( !empty($this->msg_reincidencia) ):?>
            <tr>
                <td>
                    <span id="msg_reincidencia" class="msg"><? echo $this->msg_reincidencia; ?></span>
                </td>
            </tr> 
            <?php endif; ?>
            
            <?php if ( $quantidade_equipamento > 0 ):?>            
                <tr>
                    <td align="center">                    
                        <table class="tableMoldura">

                            <tr class="tableSubTitulo">
                                <td colspan="7"><h2>Histórico</h2></td>
                            </tr>

                            <tr class="tableTituloColunas">
                                <?php if ($statusContrato["equipamento_status_id"] == 20 || $statusContrato["equipamento_status_id"] == 24 ):?>
                                    <td></td>
                                <?php endif; ?>    
                                <td><h3>Serial</h3></td>
                                <td><h3>Modelo Eqpto.</h3></td>                            
                                <td><h3>Defeito Lab.</h3></td>
                                <td><h3>Ação Lab.</h3></td>
                                <td><h3>Componente Afetado Lab.</h3></td>
                                <td><h3>Data Entrada Lab.</h3></td>
                            </tr>

                            <?php if ( $quantidade_falhas > 0 ):?>

                            <?php foreach($historicoFalhas as $falha): ?>

                                <?php $class = ($class=="tdc") ? "tde" : "tdc"; ?>

                                <tr class="tr_historico_falha <?php echo $class; ?>">
                                    <?php if ($statusContrato["equipamento_status_id"] == 20 || $statusContrato["equipamento_status_id"] == 24 ):?>
                                    <td align="center">                                        
                                        <?php if ($falha["data_entrada_laboratorio"] == $ultima_data_falha["ultima_data_entrada_laboratorio"]) :?>
                                            <input type="checkbox" name="ctfoid[]" class="chk_controle_falhas" value="<?php echo $falha["controle_falhas_id"]; ?>">
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                    <td><?php echo $falha["numero_serie"]; ?></td>
                                    <td><?php echo $falha["modelo_equipamento"]; ?></td>                            
                                    <td><?php echo $falha["defeito_laboratorio"]; ?></td>
                                    <td><?php echo $falha["acao_laboratorio"]; ?></td>
                                    <td><?php echo $falha["componente_afetado_laboratorio"]; ?></td>
                                    <td>
                                        <?php if ($falha["data_entrada_laboratorio"] == $ultima_data_falha["ultima_data_entrada_laboratorio"] && ($statusContrato["equipamento_status_id"] == 20 || $statusContrato["equipamento_status_id"] == 24 ) ) :?>
                                            <a href="cad_controle_falhas.php?acao=carregarEdicaoFallhas&numero_serie=<?php echo $falha["numero_serie"]; ?>&controle_falha_id=<?php echo $falha["controle_falhas_id"]; ?>">
                                            <?php echo $falha["data_entrada_laboratorio"]; ?>
                                            </a>                                        
                                        <?php else: ?> 
                                            <?php echo $falha["data_entrada_laboratorio"]; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                                <tr class="tableRodapeModelo3">
                                    <td colspan="7" align="center">A pesquisa retornou <b><?php echo count($historicoFalhas); ?></b> resultado(s)</td>
                                </tr>

                            <?php else: ?> 

                                <tr class="tableRodapeModelo3">
                                    <td colspan="7" align="center"><b>Nenhum Resultado Encontrado.</b></td>
                                </tr>

                            <?php endif; ?>
                            
                            <?php if ($statusContrato["equipamento_status_id"] == 20 || $statusContrato["equipamento_status_id"] == 24 ):?>
                            <tr class="tableRodapeModelo1">
                                <td colspan="7" align="center">
                                    <input type="button" value="Excluir" name="btn_excluir_historico_falhas" id="btn_excluir_historico_falhas" class="botao">                                
                                </td>
                            </tr> 
                            <?php endif; ?>
                            
                        </table>

                    </td>
                </tr>
            <?php endif; ?>
        </table>
        
    </center>
</form>