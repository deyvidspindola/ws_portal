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
    		<input type="hidden" name="acao" id="acao" value="copiar" />
    		<input type="hidden" name="epcvoid" id="epcvoid" value="" />
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
                            <option value="">Selecione</option>
                            <?php
                                foreach($arrEquipamentosProjeto as $eProjeto) {
                                    print "<option value='".$eProjeto['eproid']."'>".$eProjeto['eprnome']."</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="eqcoid">Equipamento Classe: *</label></td>
                    <td>
                        <select name="eqcoid" id="eqcoid" style="width: 350px;">
                            <option value="">Selecione</option>
                            <?php
                                foreach($arrEquipamentosClasse as $eClasse) {
                                    print "<option value='".$eClasse['eqcoid']."'>".$eClasse['eqcdescricao']."</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="eveoid">Equipamento Versão: *</label></td>
                    <td>
                        <select name="eveoid" id="eveoid" style="width: 350px;">
                            <option value="">Selecione</option>
                        </select>
                        <span id="loadVersao" style="display:none;"><img src="images/progress.gif" alt="" /></span>
                    </td>
                </tr>
                <tr class="tableRodapeModelo1" style="height:23px;">
                    <td align="center" colspan="2">
                        <input type="button" name="bt_salvar" id="bt_salvar" value="Salvar" class="botao" onclick="jQuery('#novo_teste').submit();" style="width:70px;">
                        <input type="button" name="bt_voltar" id="bt_voltar" value="Voltar" class="botao" onclick="window.location.href='cad_manutencao_testes_equipamento_classe_versao.php';" style="width:70px;">
                        <a href="javascript:void(0);" class="excluiTeste" epcvoid=""><input type="button" name="bt_excluir" id="bt_excluir" value="Excluir" class="botao" style="display:none;width: 70px;" /></a>
                        <input type="button" name="bt_copiar" id="bt_copiar" value="Copiar" class="botao" onclick="jQuery('#copiar').submit();" style="display:none;width: 70px;">
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
<tr style="display:none;" id="tableNovoComando">
    <td align="center">
        <form name="novo_comando" id="novo_comando" method="post" action="">
            <input type="hidden" id="epcvoid" name="epcvoid" value="" />
            <table class="tableMoldura dados_pesquisa">
                <tr class="tableSubTitulo">
                    <td colspan="4"><h2>Novo Comando</h2></td>
                </tr>
                <tr>
                    <td><label for="cmdoid">Comando: *</label></td>
                    <td colspan="3">
                        <select name="cmdoid" id="cmdoid" style="width: 350px;">
                            <option value="">Selecione</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="eptpoid">Teste: *</label></td>
                    <td colspan="3">
                        <select name="eptpoid" id="eptpoid" style="width: 350px;">
                            <option value="">Selecione</option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <td><label for="ecmteptpoid_antecessor">Depende do Teste:</label></td>
                    <td colspan="3">
                        <select name="ecmteptpoid_antecessor" id="ecmteptpoid_antecessor" style="width: 350px;">
                            <option value="">Selecione</option>
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
<tr id="tableListaComandos" style="display: none;">
    <td align="center">
        <span id="exibeListaComandosCadastrados"></span>
    </td>
</tr>



<!-- FORMULÁRIO DE CADASTRO DE ALERTAS DE PÂNICO -->
<tr style="display:none;" id="tableNovoAlertaPanico">
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
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="eptpoid_alerta">Teste: *</label></td>
                    <td colspan="3">
                        <select name="eptpoid_alerta" id="eptpoid_alerta" style="width: 350px;">
                            <option value="">Selecione</option>
                        </select>
                    </td>
                </tr>
                
                <tr class="tableRodapeModelo1" style="height:23px;">
                    <td align="center" colspan="4">
                        <input type="button" name="bt_salvar_alerta_panico" id="bt_salvar_alerta_panico" value="Salvar Novo Alerta" class="botao" onclick="jQuery('#novo_alerta').submit();" style="width: 150px;">
                    </td>
                </tr>
            </table>
        </form>
    </td>
</tr>

<!-- LISTA DE ALERTAS DE PÂNICO CADASTRADOS -->
<tr id="tableListaAlertaPanico" style="display: none;">
    <td align="center">
        <span id="exibeListaAlertasCadastrados"></span>
    </td>
</tr>