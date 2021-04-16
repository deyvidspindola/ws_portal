<?php
/**
 * @author	Emanuel Pires Ferreira
 * @email	epferreira@brq.com
 * @since	24/01/2013
 */
?>
<tr>
    <td align="center">
        <form name="novoGrupo" id="novoGrupo" method="post" action="">
            <input type="hidden" name="eptcfoid" id="eptcfoid" value="<?=$view['eptcfoid']?>" />
            <table class="tableMoldura dados_pesquisa">
                <tr class="tableSubTitulo">
                    <td colspan="4"><h2>Dados Principais</h2></td>
                </tr>
                <tr>
                    <td><label for="eptcfdescricao">Grupo: *</label></td>
                    <td>
                        <input name="eptcfdescricao" id="eptcfdescricao" style="width: 350px;" value="<?=$view['eptcfdescricao']?>"/>
                    </td>
                </tr>
                <tr class="tableRodapeModelo1" style="height:23px;">
                    <td align="center" colspan="4">
                        <input type="button" name="bt_salvar" id="bt_salvar" value="Salvar" class="botao" onclick="jQuery('#novoGrupo').submit();" style="width:70px;">
                        <a href="javascript:void(0);" class="excluiGrupo" eptcfoid="<?=$view['eptcfoid']?>"><input type="button" name="bt_excluir" id="bt_excluir" value="Excluir" class="botao" style="width:70px;"></a>
                        <input type="button" name="bt_voltar" id="bt_voltar" value="Voltar" class="botao" onclick="window.location.href='cad_embarque_configuracoes_portal.php';" style="width:70px;">
                    </td>
                </tr>
            </table>
        </form>
    </td>
</tr>
<form id="excluirGrupoTelaEditar" method="post">
    <input type="hidden" name="mensagem" id="mensagem" value="" /> 
</form>


<!-- FORMULÁRIO DE CADASTRO DE PROJETO, CLASSE, VERSÃO E COMANDO -->
<tr id="tableNovaConfiguracao">
    <td align="center">
        <form name="nova_configuracao" id="nova_configuracao" method="post" action="">
            <input type="hidden" id="eptcfoid_cadastro" name="eptcfoid_cadastro" value="<?=$view['eptcfoid']?>" />
            <table class="tableMoldura dados_pesquisa">
                <tr class="tableSubTitulo">
                    <td colspan="4"><h2>Incluir Projeto e Comando:</h2></td>
                </tr>
                <tr>
                    <td><label for="eproid">Projeto Equipamento: *</label></td>
                    <td>
                        <select name="eproid" id="eproid" style="width: 350px;" onchange="preencheVersao(jQuery(this).val(),0);">
                            <option value="">Selecione</option>
                            <?php
                                foreach($arrEquipamentosProjeto as $eProjeto) {
                                    $sel = (isset($_POST['eproid']) && $_POST['eproid'] == $eProjeto['eproid'])?"selected = 'selected'":"";
                                    print "<option value='".$eProjeto['eproid']."' $sel>".$eProjeto['eprnome']."</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="eqcoid">Classe Equipamento: *</label></td>
                    <td>
                        <select name="eqcoid" id="eqcoid" style="width: 350px;">
                            <option value="">Selecione</option>
                            <?php
                                foreach($arrEquipamentosClasse as $eClasse) {
                                    $sel = (isset($_POST['eqcoid']) && $_POST['eqcoid'] == $eClasse['eqcoid'])?"selected = 'selected'":"";
                                    print "<option value='".$eClasse['eqcoid']."' $sel>".$eClasse['eqcdescricao']."</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="eveoid">Versão Equipamento: *</label></td>
                    <td>
                        <select name="eveoid" id="eveoid" style="width: 350px;" onchange="preencheComandos();">
                            <option value="">Selecione</option>
                            <?php if(!isset($_POST['eproid'])) {
                                foreach($arrEquipamentosVersao['versoes'] as $eVersao) {
                                    $sel = (isset($_POST['eveoid']) && $_POST['eveoid'] == $eVersao['eveoid'])?"selected = 'selected'":"";
                                    print "<option value='".$eVersao['eveoid']."' $sel>".$eVersao['eveversao']."</option>";
                                }
                            }?>
                        </select>
                        <span id="loadVersao" style="display:none;"><img src="images/progress.gif" alt="" /></span>
                    </td>
                </tr>
                <tr>
                    <td><label for="cmdoid">Comando: *</label></td>
                    <td>
                        <select name="cmdoid" id="cmdoid" style="width: 350px;">
                            <option value="">Selecione</option>
                        </select>
                        <span id="loadComandos" style="display:none;"><img src="images/progress.gif" alt="" /></span>
                    </td>
                </tr>
                
                <tr class="tableRodapeModelo1" style="height:23px;">
                    <td align="center" colspan="4">
                        <input type="button" name="bt_salvar_configuracao" id="bt_salvar_configuracao" value="Adicionar" class="botao" onclick="jQuery('#nova_configuracao').submit();" style="width:70px;">
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
                <tr class="tableSubTitulo"><td colspan="5"><h2>Comandos Cadastrados:</h2></td></tr>
                <tr class="tableTituloColunas tab_registro">
                    <td><h3>Projeto Equipamento</h3></td>
                    <td><h3>Classe Equipamento</h3></td>
                    <td><h3>Versão Equipamento</h3></td>
                    <td><h3>Comando</h3></td>
                    <td width="5%" align="center"><h3>Excluir</h3></td>
                </tr>

                <?php 
                if(count($view['comandosCadastrados']['comandos']) > 0) {
                    // Monta a listagem de comandos cadastrados
                    foreach($view['comandosCadastrados']['comandos'] as $comando){ ?>
                        <tr class="tr_resultado_ajax_comandos" id="comando<?=$comando['epccoid']?>">
                            <td align="center"><?=$comando['eprnome']?></td>
                            <td align="center"><?=$comando['eqcdescricao']?></td>
                            <td align="center"><?=$comando['eveversao']?></td>
                            <td align="center"><?=$comando['comando']?></td>
                            <td align="center"><a href="javascript:void(0);" epccoid="<?=$comando['epccoid']?>" class="excluiComando"><img src="images/icones/t2/x.jpg" /></a></td>
                        </tr>
                    <?php } 
                } else { ?>
                    <tr class="tr_resultado_ajax">
                        <td align="center" colspan="5">Nenhum comando cadastrado</td>
                    </tr>
                <?php } ?> 
            </table>
        </span>
    </td>
</tr>