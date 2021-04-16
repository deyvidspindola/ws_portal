<?php
/**
 * @author	Emanuel Pires Ferreira
 * @email	epferreira@brq.com
 * @since	15/02/2013
 */
 
 if(isset($_POST) && $_POST['mensagem'] != "") {
     print "<script type='text/javascript'>criaAlerta('".$_POST['mensagem']."');</script>";
     unset($_POST['mensagem']);
 }
?>
<tr>
    <td align="center">
        <form name="busca_restricoes" id="busca_restricoes" method="post" action="">
            <input type="hidden" name="acao" id="acao" value="pesquisar" />
            <table class="tableMoldura dados_pesquisa">
                <tr class="tableSubTitulo">
                    <td colspan="4"><h2>Dados para Pesquisa: </h2></td>
                </tr>
                <tr>
                    <td><label for="ravtipo_restricao">Tipo de Restrição:</label></td>
                    <td>
                        <select name="ravtipo_restricao" id="ravtipo_restricao">
                            <option value="">Selecione</option>
                            <option value="P" <?=(isset($_POST['ravtipo_restricao']) && $_POST['ravtipo_restricao'] == 'P')?'selected="selected"':''?>>Por Projeto</option>
                            <option value="V" <?=(isset($_POST['ravtipo_restricao']) && $_POST['ravtipo_restricao'] == 'V')?'selected="selected"':''?>>Por Projeto e Versão</option>
                            <option value="C" <?=(isset($_POST['ravtipo_restricao']) && $_POST['ravtipo_restricao'] == 'C')?'selected="selected"':''?>>Por Classe</option>
                            <option value="T" <?=(isset($_POST['ravtipo_restricao']) && $_POST['ravtipo_restricao'] == 'T')?'selected="selected"':''?>>Por Tipo de Contrato</option>
                        </select>
                    </td>
                    <td><label for="eqcoid">Classe:</label></td>
                    <td>
                        <select name="eqcoid" id="eqcoid" style="width:310px;" >
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
                    <td><label for="eproid">Projeto:</label></td>
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
                    <td><label for="prdproduto">Acessório (Produto):</label></td>
                    <td>
                        <input type="text" name="prdproduto" id="prdproduto" value="<?=(isset($_POST['prdproduto']) && $_POST['prdproduto'] != "")?$_POST['prdproduto']:''?>" style="width:310px;" />
                    </td>
                </tr>
                <tr>
                    <td><label for="eveoid">Versão:</label></td>
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
                    <td><label for="prdproduto">Tipo de Contrato:</label></td>
                    <td>
                        <select name="tpcoid" id="tpcoid" style="width:310px;">
                            <option value="">Selecione</option>
                            <?php 
                                foreach($arrEquipamentosTipoContrato as $tpcoid => $tpcdescricao) {
                                    $sel = (isset($_POST['tpcoid']) && $_POST['tpcoid'] != '' && $_POST['tpcoid'] == $tpcoid) ? "selected = 'selected'" : "";
                                    print "<option value='".$tpcoid."' $sel>".$tpcdescricao."</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr class="tableRodapeModelo1" >
                    <td align="center" colspan="4">
                        <input type="button" name="bt_pesquisar" id="bt_pesquisar" value="Pesquisar" class="botao" onclick="jQuery('#busca_restricoes').submit();" style="width:90px;">
                        <input type="button" name="bt_cadastrar" id="bt_cadastrar" value="Nova Restrição" class="botao" onclick="exibeCadastro();" style="width:110px;">
                    </td>
                </tr>
            </table>
        </form>
    </td>
</tr>