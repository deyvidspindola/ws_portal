<?php
/**
 * @author	Emanuel Pires Ferreira
 * @email	epferreira@brq.com
 * @since	11/01/2013
 */
 
 if(isset($_POST) && $_POST['mensagem'] != "") {
     print "<script type='text/javascript'>criaAlerta('".$_POST['mensagem']."');</script>";
     unset($_POST['mensagem']);
 }
?>
<tr>
    <td align="center">
        <form name="busca_embarques" id="busca_embarques" method="post" action="">
            <input type="hidden" name="acao" id="acao" value="pesquisar" />
            <table class="tableMoldura dados_pesquisa">
                <tr class="tableSubTitulo">
                    <td colspan="4"><h2>Dados para Pesquisa: </h2></td>
                </tr>
                <tr>
                    <td><label for="eptcfoid">Grupo:</label></td>
                    <td>
                        <select name="eptcfoid" id="eptcfoid">
                            <option value="">Selecione</option>
                            <?php
                                foreach($arrGrupos as $grupo) {
                                    $sel = (isset($_POST['eptcfoid']) && $_POST['eptcfoid'] == $grupo['eptcfoid'])?"selected = 'selected'":"";
                                    print "<option value='".$grupo['eptcfoid']."' $sel>".$grupo['eptcfdescricao']."</option>";
                                }
                            ?>
                        </select>
                    </td>
                    <td><label for="eproid">Projeto Equipamento:</label></td>
                    <td>
                        <select name="eproid" id="eproid" onchange="preencheVersao(jQuery(this).val(),0);">
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
                    <td><label for="cmdcomando">Comando:</label></td>
                    <td>
                        <input type="text" name="cmdcomando" id="cmdcomando" value="<?=(isset($_POST['cmdcomando']) && $_POST['cmdcomando'] != "")?$_POST['cmdcomando']:''?>" style="width: 350px;" />
                    </td>
                    <td><label for="eqcoid">Classe Equipamento:</label></td>
                    <td>
                        <select name="eqcoid" id="eqcoid">
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
                    <td colspan="2"></td>
                    <td><label for="eveoid">Versão Equipamento:</label></td>
                    <td>
                        <select name="eveoid" id="eveoid">
                            <option value="">Selecione</option>
                            <?php
                                foreach($arrEquipamentosVersao['versoes'] as $eVersao) {
                                    $sel = (isset($_POST['eveoid']) && $_POST['eveoid'] == $eVersao['eveoid'])?"selected = 'selected'":"";
                                    print "<option value='".$eVersao['eveoid']."' $sel>".$eVersao['eveversao']."</option>";
                                }
                            ?>
                        </select>
                        <span id="loadVersao" style="display:none;"><img src="images/progress.gif" alt="" /></span>
                    </td>
                </tr>
                <tr class="tableRodapeModelo1" >
                    <td align="center" colspan="4">
                        <input type="button" name="bt_pesquisar" id="bt_pesquisar" value="Pesquisar" class="botao" onclick="jQuery('#busca_embarques').submit();" style="width:90px;">
                        <input type="button" name="bt_cadastrar" id="bt_cadastrar" value="Novo Grupo" class="botao" onclick="exibeCadastro();" style="width:90px;">
                    </td>
                </tr>
            </table>
        </form>
    </td>
</tr>