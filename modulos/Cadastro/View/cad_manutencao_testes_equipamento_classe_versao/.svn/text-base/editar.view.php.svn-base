<?php
/**
 * @author	Emanuel Pires Ferreira
 * @email	epferreira@brq.com
 * @since	11/01/2013
 */
?>
<tr>
    <td align="center">
        <form name="copiar" id="copiar" method="post">
            <input type="hidden" name="epcvoid" id="epcvoid" value="<?=$view['epcvoid']?>" />
            <input type="hidden" name="acao" id="acao" value="copiar" />
        </form>
        <form name="novo_teste" id="novo_teste" method="post" action="">
            <table class="tableMoldura dados_pesquisa">
                <tr class="tableSubTitulo">
                    <td colspan="4"><h2>Cadastro Equipamentos X Classe X Versão</h2></td>
                </tr>
                <tr>
                    <td width="15%"><label for="eproid">Equipamento Projeto: *</label></td>
                    <td>
                        <select name="eproid" id="eproid" style="width: 350px;" onchange="preencheVersao(jQuery(this).val(),0);">
                            <?php
                                foreach($arrEquipamentosProjeto as $eProjeto) {
                                    if($eProjeto['eproid'] == $view['eproid'])
                                        print "<option value='".$eProjeto['eproid']."' $sel>".$eProjeto['eprnome']."</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="eqcoid">Equipamento Classe: *</label></td>
                    <td>
                        <select name="eqcoid" id="eqcoid" style="width: 350px;">
                            <?php
                                foreach($arrEquipamentosClasse as $eClasse) {
                                    if($eClasse['eqcoid'] == $view['eqcoid'])
                                        print "<option value='".$eClasse['eqcoid']."' $sel>".$eClasse['eqcdescricao']."</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="eveoid">Equipamento Versão: *</label></td>
                    <td>
                        <select name="eveoid" id="eveoid" style="width: 350px;">
                            <?php
                                foreach($arrEquipamentosVersao['versoes'] as $eVersao) {
                                    if($eVersao['eveoid'] == $view['eveoid'])
                                        print "<option value='".$eVersao['eveoid']."' $sel>".$eVersao['eveversao']."</option>";
                                }
                            ?>
                        </select>
                        <span id="loadVersao" style="display:none;"><img src="images/progress.gif" alt="" /></span>
                    </td>
                </tr>
                <tr class="tableRodapeModelo1" style="height:23px;">
                    <td align="center" colspan="2">
                        <input type="button" name="bt_voltar" id="bt_voltar" value="Voltar" class="botao" onclick="window.location.href='cad_manutencao_testes_equipamento_classe_versao.php';" style="width:70px;">
                        <a href="javascript:void(0);" class="excluiTeste" epcvoid="<?=$view['epcvoid']?>"><input type="button" name="bt_excluir" id="bt_excluir" value="Excluir" class="botao" style="width:70px;"></a>
                        <input type="button" name="bt_copiar" id="bt_copiar" value="Copiar" class="botao" onclick="jQuery('#copiar').submit();" style="width:70px;">
                    </td>
                </tr>
            </table>
        </form>
    </td>
</tr>
<form id="excluirTesteTelaEditar" method="post">
    <input type="hidden" name="mensagem" id="mensagem" value="" /> 
</form>

<!-- FORMULÁRIO DE CADASTRO DE COMANDOS -->
<tr id="tableNovoComando">
    <td align="center">
        <form name="novo_comando" id="novo_comando" method="post" action="">
            <input type="hidden" id="epcvoid" name="epcvoid" value="<?=$view['epcvoid']?>" />
            <table class="tableMoldura dados_pesquisa">
                <tr class="tableSubTitulo">
                    <td colspan="4"><h2>Novo Comando</h2></td>
                </tr>
                <tr>
                    <td width="15%"><label for="cmdoid">Comando: *</label></td>
                    <td>
                        <select name="cmdoid" id="cmdoid" style="width: 350px;">
                            <option value="">Selecione</option>
                            <?php
                                foreach($view['comandos']['comandos'] as $eComando) {
                                    print "<option value='".$eComando['cmdoid']."'>".$eComando['comando']."</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="eptpoid">Teste: *</label></td>
                    <td>
                        <select name="eptpoid" id="eptpoid" style="width: 350px;">
                            <option value="">Selecione</option>
                            <?php
                                foreach($view['testes']['testes'] as $eTeste) {
                                    print "<option value='".$eTeste['eptpoid']."'>".$eTeste['instrucao']."</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="ecmteptpoid_antecessor">Depende do Teste:</label></td>
                    <td>
                        <select name="ecmteptpoid_antecessor" id="ecmteptpoid_antecessor" style="width: 350px;">
                            <option value="">Selecione</option>
                            <?php
                                foreach($view['dependentes']['dependentes'] as $eTeste) {
                                    print "<option value='".$eTeste['eptpoid']."'>".$eTeste['instrucao']."</option>";
                                }
                            ?>
                        </select>
                        <img align="absmiddle" onclick="mostrarHelpComment(this,'Informa que o teste escolhido no campo \'Teste\' somente poderá ser realiado quando o teste selecionado neste campo for realizado com sucesso.','D' , '');" onmouseout="document.body.style.cursor='default';" onmouseover="document.body.style.cursor='pointer';" src="images/help10.gif">
                    </td>
                </tr>
                
                <tr class="tableRodapeModelo1" style="height:23px;">
                    <td align="center" colspan="4">
                        <input type="button" name="bt_salvar_comando" id="bt_salvar_comando" value="Salvar Novo Comando" class="botao" onclick="jQuery('#novo_comando').submit();" style="width:150px;">
                    </td>
                </tr>
                
            </table>
        </form>
    </td>
</tr>

<!-- LISTA DE COMANDOS CADASTRADOS -->
<tr id="tableListaComandos">
    <td align="center">
        <span id="exibeListaComandosCadastrados">
            <table class="tableMoldura resultado_pesquisa">
                <tr class="tableSubTitulo"><td colspan="4"><h2>Comandos</h2></td></tr>
                <tr class="tableTituloColunas tab_registro">
                    <td width="5%" align="center"><h3>Excluir</h3></td>
                    <td width="35%" align="center"><h3>Comando</h3></td>
                    <td width="30%" align="center"><h3>Teste</h3></td>
                    <td width="30%" align="center"><h3>Depende do Teste</h3></td>
                </tr>
                <?php 
                if(count($view['comandosCadastrados']['comandos']) > 0) {
                    // Monta a listagem de comandos cadastrados
                    foreach($view['comandosCadastrados']['comandos'] as $comando){ ?>
                        <tr class="tr_resultado_ajax_comandos" id="comando<?=$comando['ecmtoid']?>">
                            <td align="center"><a href="javascript:void(0);" ecmtoid="<?=$comando['ecmtoid']?>" class="excluiComando"><img src="images/icones/t2/x.jpg" /></a></td>
                            <td align="center"><?=$comando['comando']?></td>
                            <td align="center"><?=$comando['instrucao']?></td>
                            <td align="center"><?=$comando['antecessor']?></td>
                        </tr>
                    <?php } 
                } else { ?>
                    <tr class="tr_resultado_ajax">
                        <td align="center" colspan="3">Nenhum comando cadastrado</td>
                    </tr>
                <?php } ?>
            </table>
        </span>
        <span id="loadingListaComandosCadastrados"></span>
    </td>
</tr>



<!-- FORMULÁRIO DE CADASTRO DE ALERTAS DE PÂNICO -->
<tr id="tableNovoAlertaPanico">
    <td align="center">
        <form name="novo_alerta" id="novo_alerta" method="post" action="">
            <input type="hidden" id="epcvoid_alerta" name="epcvoid_alerta" value="" />
            <table class="tableMoldura dados_pesquisa">
                <tr class="tableSubTitulo">
                    <td colspan="4"><h2>Novo Alerta/Pânico</h2></td>
                </tr>
                <tr>
                    <td width="100px"><label for="pantoid">Alerta: *</label></td>
                    <td width="500px">
                        <select name="pantoid" id="pantoid" style="width: 350px;">
                            <option value="">Selecione</option>
                            <?php
                                foreach($view['alertas']['alertas'] as $eAlerta) {
                                    print "<option value='".$eAlerta['pantoid']."'>".$eAlerta['panico']."</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="eptpoid_alerta">Teste: *</label></td>
                    <td colspan="3">
                        <select name="eptpoid_alerta" id="eptpoid_alerta" style="width: 350px;">
                            <option value="">Selecione</option>
                            <?php
                                foreach($view['testes']['testes'] as $eTeste) {
                                    print "<option value='".$eTeste['eptpoid']."'>".$eTeste['instrucao']."</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                
                <tr class="tableRodapeModelo1" style="height:23px;">
                    <td align="center" colspan="4">
                        <input type="button" name="bt_salvar_alerta_panico" id="bt_salvar_alerta_panico" value="Salvar Novo Alerta" class="botao" onclick="jQuery('#novo_alerta').submit();" style="width:150px;">
                    </td>
                </tr>
            </table>
        </form>
    </td>
</tr>

<!-- LISTA DE ALERTAS DE PÂNICO CADASTRADOS -->
<tr id="tableListaAlertaPanico">
    <td align="center">
        <span id="exibeListaAlertasCadastrados">
            <table class="tableMoldura resultado_pesquisa">
                <tr class="tableSubTitulo"><td colspan="4"><h2>Alertas/Pânico</h2></td></tr>
                <tr class="tableTituloColunas tab_registro">
                    <td width="5%" align="center"><h3>Excluir</h3></td>
                    <td width="35%" align="center"><h3>Alerta</h3></td>
                    <td width="60%" align="center"><h3>Teste</h3></td>
                </tr>
                <?php 
                if(count($view['alertasCadastrados']['alertas']) > 0) {
                    // Monta a listagem de comandos cadastrados
                    foreach($view['alertasCadastrados']['alertas'] as $alerta){ ?>
                        <tr class="tr_resultado_ajax_alertas" id="alerta<?=$alerta['epntoid']?>">
                            <td align="center"><a href="javascript:void(0);" epntoid="<?=$alerta['epntoid']?>" class="excluiAlerta"><img src="images/icones/t2/x.jpg" /></a></td>
                            <td align="center"><?=$alerta['pantdescricao']?></td>
                            <td align="center"><?=$alerta['eptpinstrucao']?></td>
                        </tr>
                    <?php } 
                } else { ?>
                    <tr class="tr_resultado_ajax">
                        <td align="center" colspan="3">Nenhum alerta cadastrado</td>
                    </tr>
                <?php } ?>
            </table>
        </span>
        <span id="loadingListaAlertasCadastrados"></span>
    </td>
</tr>