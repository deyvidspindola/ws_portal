<head>
    <!-- CSS -->
    <link type="text/css" rel="stylesheet" href="calendar/calendar.css"/>
    <link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
    <link type="text/css" rel="stylesheet" href="modulos/web/js/lib/jQueryUI/themes/base/jquery.ui.all.css" > 
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
    <input type="hidden" name="acao" id="acao" value="<?php echo $acao; ?>"/>
    <input type="hidden" name="ctfoid" id="ctfoid" value="<?php echo $controle_falha_id; ?>"/>
    <input type="hidden" name="msg_retorno" id="msg_retorno"/>
    <input type="hidden" name="connumero" id="connumero" value="<?php echo $informacoesFalha["numero_contrato"]; ?>"/>
    <input type="hidden" name="status_equipamento" id="status_equipamento" value="<?php echo $statusContrato["equipamento_status_id"]; ?>"/>        
    <input type="hidden" name="item_ordem_servico" id="item_ordem_servico" value="<?php echo $informacoesFalha["item_ordem_servico"]; ?>"/>    
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
            
            <?php if ( $quantidade_equipamento > 0 ):?>
            
                <tr>
                    <td align="center">

                        <table class="tableMoldura">

                            <tr class="tableSubTitulo">
                                <td colspan="7"><h2>Dados Campo</h2></td>
                            </tr>

                            <tr class="tableTituloColunas">
                                <td><h3>Serial</h3></td>
                                <td><h3>Defeito Constatado</h3></td>
                                <td><h3>Causa</h3></td>
                                <td><h3>Ocorrência</h3></td>
                                <td><h3>Solução</h3></td>
                                <td><h3>Componente</h3></td>
                                <td><h3>Motivo</h3></td>
                            </tr>

                            <?php if ( $quantidade_falhas_equipamento > 0 && ( $statusContrato["equipamento_status_id"] == 20 || $statusContrato["equipamento_status_id"] == 24 ) ):?>

                                <tr class="tdc">
                                    <td><?php echo $informacoesFalha["numero_serie"]; ?></td>
                                    <td><?php echo $informacoesFalha["defeito_constatado"]; ?></td>                            
                                    <td><?php echo $informacoesFalha["causa"]; ?></td>
                                    <td><?php echo $informacoesFalha["ocorrencia"]; ?></td>
                                    <td><?php echo $informacoesFalha["solucao"]; ?></td>
                                    <td><?php echo $informacoesFalha["componente"]; ?></td>
                                    <td><?php echo $informacoesFalha["motivo"]; ?></td>
                                </tr>
                            <?php else: ?>
                            
                                <tr class="tableRodapeModelo3">
                                    <td colspan="7" align="center"><b>Nenhum Resultado Encontrado.</b></td>
                                </tr>
                                
                            <?php endif; ?>                        


                        </table>

                    </td>
                </tr>

                <tr>
                    <td align="center">

                        <table class="tableMoldura" id="itemControleFalha">

                            <tr class="tableSubTitulo">
                                <td colspan="6"><h2>Dados Lab.</h2></td>
                            </tr>

                            <tr class="tableTituloColunas">
                                <td nowrap><h3>Serial</h3></td>
                                <td nowrap><h3>Modelo Eqpto.</h3></td>
                                <td nowrap><h3>Data Entrada Lab.</h3></td>
                                <td width="1%" nowrap><h3>Defeito Lab.</h3></td>
                                <td width="1%" nowrap><h3>Ação Lab.</h3></td>
                                <td width="1%" nowrap><h3>Componente Afetado Lab.&nbsp;</h3></td>
                            </tr>

                            <?php if ( $quantidade_falhas_equipamento > 0 && ($statusContrato["equipamento_status_id"] == 20 || $statusContrato["equipamento_status_id"] == 24) ):?>

                                <tr class="tr_item_controle_falha tdc">
                                    <td>
                                        <input type="hidden" name="ctfno_serie" id="ctfno_serie" value="<?php echo $informacoesFalha["numero_serie"]; ?>">
                                        <?php echo $informacoesFalha["numero_serie"]; ?>
                                    </td>
                                    <td>
                                        <input type="hidden" name="ctfeproid" id="ctfeproid" value="<?php echo $informacoesFalha["modelo_equipamento_id"]; ?>">
                                        <input type="hidden" name="eprnome" id="eprnome" value="<?php echo $informacoesFalha["modelo_equipamento_descricao"]; ?>">                                                                           
                                        <?php echo $informacoesFalha["modelo_equipamento_descricao"]; ?>
                                    </td>
                                    <td>
                                        <input type="hidden" name="ctfdt_entrada" id="ctfdt_entrada" value="<?php echo $informacoesFalha["data_entrada_laboratorio"]; ?>">
                                        <?php echo $informacoesFalha["data_entrada_laboratorio"]; ?>
                                    </td>                                    
                                    <td>
                                        <select name="ctfifdoid" id="ctfifdoid">
                                            <option value="">Selecione</option>
                                            <?php if ( count($dadosLaboratorio["defeitosLaboratorio"]) > 0 ):?>

                                                <?php foreach($dadosLaboratorio["defeitosLaboratorio"] as $defeitoLaboratorio): ?>
                                                    
                                                    <?php if ( $defeitoLaboratorio["ifdoid"] == $controle_falhas["defeito_laboratorio"] ): ?>   
                                                        <?php echo "<option value='".$defeitoLaboratorio["ifdoid"]."' selected='selected'>".$defeitoLaboratorio["ifddescricao"]."</option>"; ?>
                                                    <?php else: ?>
                                                        <?php echo "<option value='".$defeitoLaboratorio["ifdoid"]."'>".$defeitoLaboratorio["ifddescricao"]."</option>"; ?>
                                                    <?php endif; ?>
                                            
                                                <?php endforeach; ?>

                                            <?php endif; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="ctfifaoid" id="ctfifaoid">
                                            <option value="">Selecione</option>
                                            <?php if ( count($dadosLaboratorio["acoesLaboratorio"]) > 0 ):?>

                                                <?php foreach($dadosLaboratorio["acoesLaboratorio"] as $acaoLaboratorio): ?>
                                                    
                                                    <?php if ( $acaoLaboratorio["ifaoid"] == $controle_falhas["acao_laboratorio"] ): ?>
                                                        <?php echo "<option value='".$acaoLaboratorio["ifaoid"]."' selected='selected'>".$acaoLaboratorio["ifadescricao"]."</option>"; ?>
                                                    <?php else: ?>
                                                        <?php echo "<option value='".$acaoLaboratorio["ifaoid"]."'>".$acaoLaboratorio["ifadescricao"]."</option>"; ?>
                                                    <?php endif; ?>
                                                        
                                                <?php endforeach; ?>

                                            <?php endif; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="ctfifcoid" id="ctfifcoid">
                                            <option value="">Selecione</option>
                                            <?php if ( count($dadosLaboratorio["componentesAfetados"]) > 0 ):?>

                                                <?php foreach($dadosLaboratorio["componentesAfetados"] as $componenteAfetado): ?>
                                                    
                                                    <?php if ( $componenteAfetado["ifcoid"] == $controle_falhas["componente_afetado_laboratorio"] ): ?>
                                                        <?php echo "<option value='".$componenteAfetado["ifcoid"]."' selected='selected'>".$componenteAfetado["ifcdescricao"]."</option>"; ?>
                                                    <?php else: ?>
                                                        <?php echo "<option value='".$componenteAfetado["ifcoid"]."'>".$componenteAfetado["ifcdescricao"]."</option>"; ?>
                                                    <?php endif; ?>
                                            
                                                <?php endforeach; ?>
                                                
                                            <?php endif; ?>
                                        </select>
                                    </td>
                                </tr>
                                
                                <tr class="tableRodapeModelo1">
                                    <td colspan="6" align="center">
                                        <input type="button" value="Gravar" name="btn_gravar_falhas" id="btn_gravar_falhas" class="botao">
                                    </td>
                                </tr> 
                                
                            <?php else: ?>
                                
                                <tr class="tableRodapeModelo3">
                                    <td colspan="7" align="center"><b>Nenhum Resultado Encontrado.</b></td>
                                </tr>
                                
                            <?php endif; ?>
                            
                        </table>

                    </td>
                </tr>
            
            <?php endif; ?>
            
        </table>
        
    </center>
</form> 