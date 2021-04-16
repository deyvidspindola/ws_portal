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
        <form name="busca_testes" id="busca_testes" method="post" action="">
            <input type="hidden" name="acao" id="acao" value="pesquisar" />
            <table class="tableMoldura dados_pesquisa">
                <tr class="tableSubTitulo">
                    <td colspan="4"><h2>Dados para Pesquisa</h2></td>
                </tr>
                <tr>
                    <td width="15%"><label for="eproid">Equipamento Projeto:</label></td>
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
                    <td><label for="eqcoid">Equipamento Classe:</label></td>
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
                    <td><label for="eveoid">Equipamento Versão:</label></td>
                    <td>
                        <select name="eveoid" id="eveoid" style="width: 350px;">
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
                <tr class="tableRodapeModelo1" style="height:23px;">
                    <td align="center" colspan="2">
                        <input class="botao" type="button" name="pesquisar" id="bt_pesquisar" value="Pesquisar" onclick="jQuery('#busca_testes').submit();" style="width:70px;">
                        <input class="botao" type="button" name="bt_cadastrar" id="bt_cadastrar" value="Novo" onclick="exibeCadastro();" style="width:70px;">
                    </td>
                </tr>
            </table>
        </form>
    </td>
</tr>